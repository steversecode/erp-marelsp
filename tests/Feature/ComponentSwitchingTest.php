<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Product;
use App\Models\ProductSubstitute;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\ManufacturingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentSwitchingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StockLocation $whMain;
    protected StockLocation $whFg;
    protected StockLocation $prodFloor;
    protected StockLocation $vendorSupp;
    protected Product $rawCotton30s;
    protected Product $rawCotton24s;
    protected Product $fgKaos;
    protected Bom $bomKaos;
    protected InventoryService $inventoryService;
    protected ManufacturingService $manufacturingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = app(InventoryService::class);
        $this->manufacturingService = app(ManufacturingService::class);

        $this->user = User::factory()->create();

        $this->whMain = StockLocation::create(['code' => 'WH-MAIN', 'name' => 'Gudang Utama', 'type' => 'internal']);
        $this->whFg = StockLocation::create(['code' => 'WH-FG', 'name' => 'Gudang FG', 'type' => 'internal']);
        $this->prodFloor = StockLocation::create(['code' => 'PROD-FLOOR', 'name' => 'Lantai Produksi', 'type' => 'production']);
        $this->vendorSupp = StockLocation::create(['code' => 'VENDOR-SUPP', 'name' => 'Vendor', 'type' => 'vendor']);

        // Material Asli (Stok 0)
        $this->rawCotton30s = Product::create([
            'code' => 'COT-30S',
            'name' => 'Cotton Combed 30s',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 85000,
        ]);

        // Material Pengganti (Stok 500 kg)
        $this->rawCotton24s = Product::create([
            'code' => 'COT-24S',
            'name' => 'Cotton Combed 24s',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 82000,
        ]);

        // Beri stok pada Cotton 24s
        StockMove::create([
            'move_number' => 'SM-TEST-001',
            'product_id' => $this->rawCotton24s->id,
            'from_location_id' => $this->vendorSupp->id,
            'to_location_id' => $this->whMain->id,
            'qty' => 500,
            'uom' => 'kg',
            'category' => StockMove::CAT_PO_RECEIPT,
            'created_by' => $this->user->id,
        ]);

        // Finished Good
        $this->fgKaos = Product::create([
            'code' => 'FG-KAOS',
            'name' => 'Kaos Polos',
            'type' => 'finished_good',
            'uom' => 'pcs',
            'cost_price' => 35000,
        ]);

        // Aturan Alternatif
        ProductSubstitute::create([
            'primary_product_id' => $this->rawCotton30s->id,
            'substitute_product_id' => $this->rawCotton24s->id,
            'conversion_rate' => 1.05,
            'notes' => 'Toleransi tebal 24s',
        ]);

        // Master BoM
        $this->bomKaos = Bom::create([
            'code' => 'BOM-001',
            'name' => 'BoM Kaos',
            'product_id' => $this->fgKaos->id,
            'quantity' => 100,
            'uom' => 'pcs',
        ]);

        BomItem::create([
            'bom_id' => $this->bomKaos->id,
            'product_id' => $this->rawCotton30s->id,
            'quantity' => 20, // 20 kg per 100 pcs
            'uom' => 'kg',
        ]);
    }

    public function test_creating_mo_copies_bom_components_and_keeps_master_intact(): void
    {
        $mo = $this->manufacturingService->createManufacturingOrder([
            'bom_id' => $this->bomKaos->id,
            'planned_qty' => 100,
            'created_by' => $this->user->id,
        ]);

        $this->assertCount(1, $mo->components);
        $component = $mo->components->first();

        $this->assertEquals($this->rawCotton30s->id, $component->original_product_id);
        $this->assertEquals($this->rawCotton30s->id, $component->actual_product_id);
        $this->assertFalse($component->is_switched);
        $this->assertEquals(20.0, (float) $component->planned_qty);

        // Pastikan Master BoM tetap berisi Cotton 30s
        $bomItem = BomItem::where('bom_id', $this->bomKaos->id)->first();
        $this->assertEquals($this->rawCotton30s->id, $bomItem->product_id);
    }

    public function test_dynamic_component_switching_does_not_mutate_master_bom(): void
    {
        $mo = $this->manufacturingService->createManufacturingOrder([
            'bom_id' => $this->bomKaos->id,
            'planned_qty' => 100,
            'created_by' => $this->user->id,
        ]);

        $component = $mo->components->first();

        // Lakukan Component Switching karena Cotton 30s kosong
        $switchedComponent = $this->manufacturingService->switchComponent(
            $component->id,
            $this->rawCotton24s->id,
            'Stok Cotton 30s habis di supplier',
            null, // gunakan automatic conversion rate (20 * 1.05 = 21 kg)
            $this->user->id
        );

        // Verifikasi pada MO Component
        $this->assertTrue($switchedComponent->is_switched);
        $this->assertEquals($this->rawCotton30s->id, $switchedComponent->original_product_id);
        $this->assertEquals($this->rawCotton24s->id, $switchedComponent->actual_product_id);
        $this->assertEquals('Stok Cotton 30s habis di supplier', $switchedComponent->switch_reason);
        $this->assertEquals(21.0, (float) $switchedComponent->planned_qty);

        // Verifikasi KRUSIAL: Master BoM sama sekali TIDAK BERUBAH
        $this->bomKaos->refresh();
        $originalBomItem = $this->bomKaos->items()->first();
        $this->assertEquals($this->rawCotton30s->id, $originalBomItem->product_id);
        $this->assertEquals(20.0, (float) $originalBomItem->quantity);
    }

    public function test_full_production_lifecycle_with_switching_and_leftover_return(): void
    {
        // 1. Buat MO
        $mo = $this->manufacturingService->createManufacturingOrder([
            'bom_id' => $this->bomKaos->id,
            'planned_qty' => 100,
            'created_by' => $this->user->id,
        ]);

        $component = $mo->components->first();

        // 2. Switch component ke Cotton 24s
        $this->manufacturingService->switchComponent(
            $component->id,
            $this->rawCotton24s->id,
            'Switching to 24s',
            21.0,
            $this->user->id
        );

        // 3. Release ke Lantai Produksi
        $this->manufacturingService->releaseToProduction($mo->id, $this->user->id);

        // Cek stok Cotton 24s di Gudang berkurang 21 kg (500 - 21 = 479)
        $stock24sWarehouse = $this->inventoryService->getCurrentStock($this->rawCotton24s->id, $this->whMain->id);
        $this->assertEquals(479.0, $stock24sWarehouse);

        // 4. Retur Sisa Produksi (misal ada sisa benang 2 kg dikembalikan ke gudang)
        $component->refresh();
        $this->manufacturingService->returnLeftoverMaterial(
            $mo->id,
            $component->id,
            2.0,
            'Sisa potongan rajutan utuh',
            $this->user->id
        );

        // Stok di gudang bertambah kembali 2 kg (479 + 2 = 481)
        $stock24sAfterReturn = $this->inventoryService->getCurrentStock($this->rawCotton24s->id, $this->whMain->id);
        $this->assertEquals(481.0, $stock24sAfterReturn);

        // Net Consumed pada component adalah 21 - 2 = 19 kg
        $component->refresh();
        $this->assertEquals(21.0, (float) $component->issued_qty);
        $this->assertEquals(2.0, (float) $component->returned_qty);
        $this->assertEquals(19.0, $component->net_consumed_qty);

        // 5. Complete MO
        $this->manufacturingService->completeManufacturingOrder($mo->id, 100, $this->user->id);

        $mo->refresh();
        $this->assertEquals('done', $mo->status);
        $this->assertEquals(100.0, (float) $mo->produced_qty);

        // Stok Barang Jadi di WH-FG bertambah 100 pcs
        $stockFg = $this->inventoryService->getCurrentStock($this->fgKaos->id, $this->whFg->id);
        $this->assertEquals(100.0, $stockFg);
    }
}

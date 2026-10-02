<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\ManufacturingOrder;
use App\Models\MoComponent;
use App\Models\Product;
use App\Models\ProductSubstitute;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRouteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StockLocation $whMain;
    protected StockLocation $whFg;
    protected StockLocation $prodFloor;
    protected StockLocation $vendorSupp;
    protected StockLocation $subconDyeing;
    protected StockLocation $virtualSample;
    protected Product $rawCotton;
    protected Product $rawCottonSub;
    protected Product $fgKaos;
    protected Bom $bomKaos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->whMain = StockLocation::create(['code' => 'WH-MAIN', 'name' => 'Gudang Utama', 'type' => 'internal']);
        $this->whFg = StockLocation::create(['code' => 'WH-FG', 'name' => 'Gudang FG', 'type' => 'internal']);
        $this->prodFloor = StockLocation::create(['code' => 'PROD-FLOOR', 'name' => 'Lantai Produksi', 'type' => 'production']);
        $this->vendorSupp = StockLocation::create(['code' => 'VENDOR-SUPP', 'name' => 'Vendor Supplier', 'type' => 'vendor']);
        $this->subconDyeing = StockLocation::create(['code' => 'SUBCON-DYEING', 'name' => 'Vendor Celup', 'type' => 'dyeing_subcon']);
        $this->virtualSample = StockLocation::create(['code' => 'VIRTUAL-SAMPLE', 'name' => 'Divisi Sample', 'type' => 'sample']);

        $this->rawCotton = Product::create([
            'code' => 'RAW-COT-01',
            'name' => 'Cotton Combed 30s',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 85000,
        ]);

        $this->rawCottonSub = Product::create([
            'code' => 'RAW-COT-02',
            'name' => 'Cotton Combed 24s Sub',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 82000,
        ]);

        $this->fgKaos = Product::create([
            'code' => 'FG-KAOS-01',
            'name' => 'Kaos Polos',
            'type' => 'finished_good',
            'uom' => 'pcs',
            'cost_price' => 40000,
        ]);

        $this->bomKaos = Bom::create([
            'code' => 'BOM-01',
            'name' => 'BoM Kaos',
            'product_id' => $this->fgKaos->id,
            'quantity' => 100,
            'uom' => 'pcs',
        ]);

        BomItem::create([
            'bom_id' => $this->bomKaos->id,
            'product_id' => $this->rawCotton->id,
            'quantity' => 20,
            'uom' => 'kg',
        ]);

        ProductSubstitute::create([
            'primary_product_id' => $this->rawCotton->id,
            'substitute_product_id' => $this->rawCottonSub->id,
            'conversion_rate' => 1.05,
        ]);

        // Stock for substitute
        StockMove::create([
            'move_number' => 'SM-INIT-001',
            'product_id' => $this->rawCottonSub->id,
            'from_location_id' => $this->vendorSupp->id,
            'to_location_id' => $this->whMain->id,
            'qty' => 500,
            'uom' => 'kg',
            'category' => StockMove::CAT_PO_RECEIPT,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Sistem Manajemen Gudang');
    }

    public function test_inventory_views_render_successfully(): void
    {
        $this->get(route('inventory.index'))->assertStatus(200)->assertSee('Saldo Stok Fisik');
        $this->get(route('inventory.moves'))->assertStatus(200)->assertSee('Buku Besar Mutasi');
    }

    public function test_manufacturing_workflow_via_http(): void
    {
        // 1. Create MO
        $createResponse = $this->post(route('manufacturing.store'), [
            'bom_id' => $this->bomKaos->id,
            'planned_qty' => 100,
            'notes' => 'Test MO Order',
        ]);

        $createResponse->assertRedirect();
        $mo = ManufacturingOrder::first();
        $this->assertNotNull($mo);

        // 2. View MO detail
        $this->get(route('manufacturing.show', $mo->id))->assertStatus(200)->assertSee($mo->mo_number);

        // 3. Switch component via HTTP
        $component = $mo->components->first();
        $switchResponse = $this->post(route('manufacturing.switch', [$mo->id, $component->id]), [
            'substitute_product_id' => $this->rawCottonSub->id,
            'switch_reason' => 'Stok 30s habis',
            'custom_qty' => 21.0,
        ]);

        $switchResponse->assertRedirect();
        $component->refresh();
        $this->assertTrue($component->is_switched);
        $this->assertEquals($this->rawCottonSub->id, $component->actual_product_id);

        // 4. Release to Production
        $this->post(route('manufacturing.release', $mo->id))->assertRedirect();
        $mo->refresh();
        $this->assertEquals('in_progress', $mo->status);

        // 5. Return leftover
        $this->post(route('manufacturing.return-leftover', $mo->id), [
            'mo_component_id' => $component->id,
            'returned_qty' => 1.5,
            'notes' => 'Sisa rajutan',
        ])->assertRedirect();

        // 6. Complete MO
        $this->post(route('manufacturing.complete', $mo->id), [
            'produced_qty' => 100,
        ])->assertRedirect();

        $mo->refresh();
        $this->assertEquals('done', $mo->status);
    }

    public function test_stock_in_and_stock_out_routes(): void
    {
        // PO Index
        $this->get(route('stock-in.po.index'))->assertStatus(200);

        // Create PO
        $this->post(route('stock-in.po.store'), [
            'supplier_name' => 'PT Supplier Marel',
            'order_date' => '2026-03-01',
            'product_id' => $this->rawCotton->id,
            'ordered_qty' => 300,
            'unit_price' => 85000,
        ])->assertRedirect();

        $po = PurchaseOrder::first();
        $this->assertNotNull($po);

        // Receive PO
        $this->post(route('stock-in.po.receive', $po->id), [
            'item_id' => $po->items->first()->id,
            'qty' => 300,
            'batch_number' => 'LOT-TEST-PO',
        ])->assertRedirect();

        // Leftover Index
        $this->get(route('stock-in.leftover.index'))->assertStatus(200);

        // Sample Issue
        $this->get(route('stock-out.sample.index'))->assertStatus(200);
        $this->post(route('stock-out.sample.store'), [
            'product_id' => $this->rawCottonSub->id,
            'qty' => 5,
            'purpose' => 'Uji lab kekuatan serat benang',
        ])->assertRedirect();

        // Dyeing Issue & Return
        $this->get(route('stock-out.dyeing.index'))->assertStatus(200);
        $this->post(route('stock-out.dyeing.issue'), [
            'product_id' => $this->rawCottonSub->id,
            'qty' => 50,
            'color_target' => 'Navy Blue',
        ])->assertRedirect();

        $this->post(route('stock-out.dyeing.receive'), [
            'product_id' => $this->rawCottonSub->id,
            'qty' => 49,
            'dyeing_order_number' => 'DYE-001',
        ])->assertRedirect();
    }
}

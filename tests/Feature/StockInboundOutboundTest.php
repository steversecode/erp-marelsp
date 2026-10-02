<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PurchaseService;
use App\Services\SampleProductionService;
use App\Services\SubcontractDyeingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInboundOutboundTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StockLocation $whMain;
    protected StockLocation $vendorSupp;
    protected StockLocation $subconDyeing;
    protected StockLocation $virtualSample;
    protected Product $rawCotton;
    protected Product $rawCottonDyed;
    protected InventoryService $inventoryService;
    protected PurchaseService $purchaseService;
    protected SubcontractDyeingService $dyeingService;
    protected SampleProductionService $sampleService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = app(InventoryService::class);
        $this->purchaseService = app(PurchaseService::class);
        $this->dyeingService = app(SubcontractDyeingService::class);
        $this->sampleService = app(SampleProductionService::class);

        $this->user = User::factory()->create();

        $this->whMain = StockLocation::create(['code' => 'WH-MAIN', 'name' => 'Gudang Utama', 'type' => 'internal']);
        $this->vendorSupp = StockLocation::create(['code' => 'VENDOR-SUPP', 'name' => 'Vendor', 'type' => 'vendor']);
        $this->subconDyeing = StockLocation::create(['code' => 'SUBCON-DYEING', 'name' => 'Vendor Celup', 'type' => 'dyeing_subcon']);
        $this->virtualSample = StockLocation::create(['code' => 'VIRTUAL-SAMPLE', 'name' => 'Divisi Sample', 'type' => 'sample']);

        $this->rawCotton = Product::create([
            'code' => 'COT-RAW',
            'name' => 'Cotton Greige',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 80000,
        ]);

        $this->rawCottonDyed = Product::create([
            'code' => 'COT-DYED-BLK',
            'name' => 'Cotton Hitam Reaktif',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 95000,
        ]);
    }

    public function test_purchase_order_goods_receipt_increases_stock(): void
    {
        $po = $this->purchaseService->createPurchaseOrder([
            'supplier_name' => 'PT Mitra Benang Jaya',
            'order_date' => '2026-03-01',
            'items' => [
                [
                    'product_id' => $this->rawCotton->id,
                    'ordered_qty' => 1000,
                    'unit_price' => 80000,
                    'uom' => 'kg',
                ],
            ],
            'created_by' => $this->user->id,
        ]);

        $poItem = $po->items->first();

        // Terima barang 600 kg (Parsial)
        $this->purchaseService->receiveGoods(
            $po->id,
            [
                ['item_id' => $poItem->id, 'qty' => 600, 'batch_number' => 'LOT-MBJ-01'],
            ],
            $this->whMain->id,
            $this->user->id
        );

        $stock = $this->inventoryService->getCurrentStock($this->rawCotton->id, $this->whMain->id);
        $this->assertEquals(600.0, $stock);

        $po->refresh();
        $this->assertEquals('partial_received', $po->status);
    }

    public function test_sample_issue_decreases_stock_and_records_audit(): void
    {
        // Beri saldo awal 100 kg
        StockMove::create([
            'move_number' => 'SM-INIT-SMPL',
            'product_id' => $this->rawCotton->id,
            'from_location_id' => $this->vendorSupp->id,
            'to_location_id' => $this->whMain->id,
            'qty' => 100,
            'uom' => 'kg',
            'category' => StockMove::CAT_PO_RECEIPT,
            'created_by' => $this->user->id,
        ]);

        // Issue 15 kg untuk sample R&D
        $move = $this->sampleService->issueForSample(
            $this->rawCotton->id,
            15,
            'SMPL-REQ-001',
            'Sample swatch kain rajut jersey buyer Uniqlo',
            $this->user->id
        );

        $this->assertEquals(StockMove::CAT_SAMPLE_ISSUE, $move->category);
        $this->assertEquals(85.0, $this->inventoryService->getCurrentStock($this->rawCotton->id, $this->whMain->id));
    }

    public function test_dyeing_subcontract_out_and_in(): void
    {
        // Beri saldo awal 500 kg greige
        StockMove::create([
            'move_number' => 'SM-INIT-DYE',
            'product_id' => $this->rawCotton->id,
            'from_location_id' => $this->vendorSupp->id,
            'to_location_id' => $this->whMain->id,
            'qty' => 500,
            'uom' => 'kg',
            'category' => StockMove::CAT_PO_RECEIPT,
            'created_by' => $this->user->id,
        ]);

        // 1. Kirim 200 kg ke vendor celup
        $outMove = $this->dyeingService->issueForDyeing(
            $this->rawCotton->id,
            200,
            $this->subconDyeing->id,
            'DYE-ORD-001',
            'Jet Black 9000',
            'LOT-RAW-001',
            $this->user->id
        );

        $this->assertEquals(StockMove::CAT_DYEING_OUT, $outMove->category);
        $this->assertEquals(300.0, $this->inventoryService->getCurrentStock($this->rawCotton->id, $this->whMain->id));

        // 2. Terima kembali benang celup jadi 195 kg (setelah susut celup)
        $inMove = $this->dyeingService->receiveFromDyeing(
            $this->rawCottonDyed->id,
            195,
            $this->subconDyeing->id,
            'DYE-ORD-001',
            'LOT-DYED-001',
            $this->user->id
        );

        $this->assertEquals(StockMove::CAT_DYEING_RETURN, $inMove->category);
        $this->assertEquals(195.0, $this->inventoryService->getCurrentStock($this->rawCottonDyed->id, $this->whMain->id));
    }
}

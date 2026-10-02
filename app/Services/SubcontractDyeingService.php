<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubcontractDyeingService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * ALUR KELUAR 3: Pengeluaran Material ke Proses Celup (Dyeing Out)
     */
    public function issueForDyeing(
        int $productId,
        float $qty,
        ?int $vendorLocationId = null,
        ?string $dyeingOrderNumber = null,
        ?string $colorTarget = null,
        ?string $batchNumber = null,
        ?int $userId = null
    ): StockMove {
        return DB::transaction(function () use ($productId, $qty, $vendorLocationId, $dyeingOrderNumber, $colorTarget, $batchNumber, $userId) {
            $sourceLocId = StockLocation::mainLocationId();
            $subconLocId = $vendorLocationId ?? StockLocation::subconLocationId();

            $availableStock = $this->inventoryService->getCurrentStock($productId, $sourceLocId);
            if ($availableStock < $qty) {
                $product = Product::find($productId);
                throw new Exception("Stok {$product->name} tidak mencukupi untuk dikirim celup (Tersedia: {$availableStock}, Diminta: {$qty}).");
            }

            $orderRef = $dyeingOrderNumber ?? 'DYE-' . date('Ym') . '-' . strtoupper(Str::random(4));
            $product = Product::findOrFail($productId);

            return $this->inventoryService->recordStockMove([
                'product_id' => $productId,
                'from_location_id' => $sourceLocId,
                'to_location_id' => $subconLocId,
                'qty' => $qty,
                'uom' => $product->uom,
                'category' => StockMove::CAT_DYEING_OUT,
                'reference_type' => 'DyeingOrder',
                'reference_number' => $orderRef,
                'batch_lot_number' => $batchNumber,
                'notes' => "Pengiriman benang greige ke vendor celup. Target Warna: " . ($colorTarget ?? 'Standar'),
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Penerimaan Kembali Hasil Celup dari Vendor
     */
    public function receiveFromDyeing(
        int $dyeingProductOutputId, // SKU Benang Celup Jadi
        float $qtyReceived,
        ?int $vendorLocationId = null,
        ?string $dyeingOrderNumber = null,
        ?string $batchNumber = null,
        ?int $userId = null
    ): StockMove {
        return DB::transaction(function () use ($dyeingProductOutputId, $qtyReceived, $vendorLocationId, $dyeingOrderNumber, $batchNumber, $userId) {
            $subconLocId = $vendorLocationId ?? StockLocation::subconLocationId();
            $destLocId = StockLocation::mainLocationId();

            $product = Product::findOrFail($dyeingProductOutputId);

            return $this->inventoryService->recordStockMove([
                'product_id' => $dyeingProductOutputId,
                'from_location_id' => $subconLocId,
                'to_location_id' => $destLocId,
                'qty' => $qtyReceived,
                'uom' => $product->uom,
                'category' => StockMove::CAT_DYEING_RETURN,
                'reference_type' => 'DyeingOrder',
                'reference_number' => $dyeingOrderNumber,
                'batch_lot_number' => $batchNumber,
                'notes' => "Penerimaan benang selesai proses celup dari vendor.",
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }
}

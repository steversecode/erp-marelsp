<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleProductionService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * ALUR KELUAR 2: Pengeluaran Material untuk Produksi Sample / R&D
     */
    public function issueForSample(
        int $productId,
        float $qty,
        ?string $sampleReference = null,
        ?string $purpose = null,
        ?int $userId = null
    ): StockMove {
        return DB::transaction(function () use ($productId, $qty, $sampleReference, $purpose, $userId) {
            $sourceLocId = StockLocation::mainLocationId();
            $sampleLocId = StockLocation::sampleLocationId();

            $availableStock = $this->inventoryService->getCurrentStock($productId, $sourceLocId);
            if ($availableStock < $qty) {
                $product = Product::find($productId);
                throw new Exception("Stok {$product->name} tidak cukup untuk sample (Tersedia: {$availableStock}, Diminta: {$qty}).");
            }

            $product = Product::findOrFail($productId);
            $refNumber = $sampleReference ?? 'SMPL-' . date('Ym') . '-' . strtoupper(Str::random(4));

            return $this->inventoryService->recordStockMove([
                'product_id' => $productId,
                'from_location_id' => $sourceLocId,
                'to_location_id' => $sampleLocId,
                'qty' => $qty,
                'uom' => $product->uom,
                'category' => StockMove::CAT_SAMPLE_ISSUE,
                'reference_type' => 'SampleRequest',
                'reference_number' => $refNumber,
                'notes' => "Pengeluaran material untuk keperluan sample/R&D. Keperluan: " . ($purpose ?? 'Uji Coba Produk Baru'),
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }
}

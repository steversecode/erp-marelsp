<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Catat pergerakan stok (Stock Move) ke dalam ledger mutasi.
     */
    public function recordStockMove(array $data): StockMove
    {
        return DB::transaction(function () use ($data) {
            $moveNumber = $data['move_number'] ?? 'SM-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            return StockMove::create([
                'move_number' => $moveNumber,
                'product_id' => $data['product_id'],
                'from_location_id' => $data['from_location_id'],
                'to_location_id' => $data['to_location_id'],
                'qty' => $data['qty'],
                'uom' => $data['uom'] ?? 'kg',
                'category' => $data['category'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'batch_lot_number' => $data['batch_lot_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);
        });
    }

    /**
     * Hitung saldo stok terkini suatu produk berdasarkan buku besar stock_moves.
     * Jika locationId null, maka menghitung total di seluruh lokasi internal perusahaan.
     */
    public function getCurrentStock(int $productId, ?int $locationId = null): float
    {
        $inQuery = StockMove::where('product_id', $productId);
        $outQuery = StockMove::where('product_id', $productId);

        if ($locationId) {
            $inQuery->where('to_location_id', $locationId);
            $outQuery->where('from_location_id', $locationId);
        } else {
            // Default: Lokasi internal
            $internalLocationIds = StockLocation::where('type', 'internal')->pluck('id');
            $inQuery->whereIn('to_location_id', $internalLocationIds);
            $outQuery->whereIn('from_location_id', $internalLocationIds);
        }

        $totalIn = (float) $inQuery->sum('qty');
        $totalOut = (float) $outQuery->sum('qty');

        return max(0, $totalIn - $totalOut);
    }

    /**
     * Cek apakah stok produk mencukupi untuk kebutuhan tertentu.
     */
    public function checkAvailability(int $productId, float $requiredQty, ?int $locationId = null): array
    {
        $availableStock = $this->getCurrentStock($productId, $locationId);
        $isSufficient = $availableStock >= $requiredQty;
        $deficit = $isSufficient ? 0 : ($requiredQty - $availableStock);

        $product = Product::with('substitutes')->find($productId);

        // Cari alternatif material jika stok tidak mencukupi
        $substituteOptions = [];
        if (!$isSufficient && $product) {
            foreach ($product->substitutes as $substitute) {
                $subStock = $this->getCurrentStock($substitute->id, $locationId);
                $conversionRate = (float) ($substitute->pivot->conversion_rate ?? 1.0);
                $adjustedRequired = $requiredQty * $conversionRate;

                $substituteOptions[] = [
                    'id' => $substitute->id,
                    'code' => $substitute->code,
                    'name' => $substitute->name,
                    'uom' => $substitute->uom,
                    'current_stock' => $subStock,
                    'conversion_rate' => $conversionRate,
                    'required_qty' => $adjustedRequired,
                    'is_sufficient' => $subStock >= $adjustedRequired,
                    'notes' => $substitute->pivot->notes,
                ];
            }
        }

        return [
            'product_id' => $productId,
            'required_qty' => $requiredQty,
            'available_stock' => $availableStock,
            'is_sufficient' => $isSufficient,
            'deficit' => $deficit,
            'has_substitutes' => count($substituteOptions) > 0,
            'substitute_options' => $substituteOptions,
        ];
    }

    /**
     * Laporan ringkas status stok seluruh produk.
     */
    public function getInventorySummary()
    {
        $products = Product::where('is_active', true)->get();
        $mainLocation = StockLocation::where('code', 'WH-MAIN')->first();

        return $products->map(function ($prod) use ($mainLocation) {
            $stock = $this->getCurrentStock($prod->id, $mainLocation?->id);
            return [
                'id' => $prod->id,
                'code' => $prod->code,
                'name' => $prod->name,
                'type' => $prod->type,
                'category' => $prod->category,
                'uom' => $prod->uom,
                'current_stock' => $stock,
                'min_stock' => (float) $prod->min_stock,
                'is_low_stock' => $stock <= (float) $prod->min_stock,
                'cost_price' => (float) $prod->cost_price,
                'total_valuation' => $stock * (float) $prod->cost_price,
            ];
        });
    }
}

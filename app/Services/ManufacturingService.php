<?php

namespace App\Services;

use App\Models\Bom;
use App\Models\ManufacturingOrder;
use App\Models\MoComponent;
use App\Models\Product;
use App\Models\ProductSubstitute;
use App\Models\StockLocation;
use App\Models\StockMove;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManufacturingService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Membuat Manufacturing Order (MO) dan menduplikasi komponen dari Master BoM.
     * Master BoM tetap utuh dan aman dari modifikasi.
     */
    public function createManufacturingOrder(array $data): ManufacturingOrder
    {
        return DB::transaction(function () use ($data) {
            $bom = Bom::with('items.product')->findOrFail($data['bom_id']);
            $plannedQty = (float) $data['planned_qty'];
            
            $sourceLocationId = $data['source_location_id'] ?? StockLocation::mainLocationId();
            $destLocationId = $data['destination_location_id'] ?? StockLocation::finishedGoodsLocationId();

            $moNumber = $data['mo_number'] ?? 'MO-' . date('Ym') . '-' . strtoupper(Str::random(4));

            $mo = ManufacturingOrder::create([
                'mo_number' => $moNumber,
                'bom_id' => $bom->id,
                'product_id' => $bom->product_id,
                'planned_qty' => $plannedQty,
                'uom' => $bom->uom,
                'status' => 'draft',
                'start_date' => $data['start_date'] ?? date('Y-m-d'),
                'source_location_id' => $sourceLocationId,
                'destination_location_id' => $destLocationId,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            if (!empty($data['components']) && is_array($data['components'])) {
                foreach ($data['components'] as $comp) {
                    if (empty($comp['product_id'])) continue;
                    $prod = Product::find($comp['product_id']);
                    if (!$prod) continue;
                    MoComponent::create([
                        'manufacturing_order_id' => $mo->id,
                        'original_product_id' => $prod->id,
                        'actual_product_id' => $prod->id,
                        'is_switched' => false,
                        'planned_qty' => (float) ($comp['planned_qty'] ?? $comp['quantity'] ?? 1),
                        'issued_qty' => 0,
                        'returned_qty' => 0,
                        'uom' => $comp['uom'] ?? $prod->uom,
                    ]);
                }
            } else {
                // Duplikasi komponen dari BoM ke MO Component lines
                // Rasio dihitung: (plannedQty / bom.quantity) * bom_item.quantity * (1 + wastage_percent/100)
                $multiplier = $plannedQty / ($bom->quantity > 0 ? $bom->quantity : 1);

                foreach ($bom->items as $item) {
                    $wastageFactor = 1 + (($item->wastage_percent ?? 0) / 100);
                    $calculatedComponentQty = (float) ($item->quantity * $multiplier * $wastageFactor);

                    MoComponent::create([
                        'manufacturing_order_id' => $mo->id,
                        'original_product_id' => $item->product_id,
                        'actual_product_id' => $item->product_id, // Default: sama dengan BoM
                        'is_switched' => false,
                        'planned_qty' => $calculatedComponentQty,
                        'issued_qty' => 0,
                        'returned_qty' => 0,
                        'uom' => $item->uom,
                    ]);
                }
            }

            return $mo->load('components.originalProduct', 'components.actualProduct', 'product', 'bom');
        });
    }

    /**
     * DYNAMIC COMPONENT SWITCHING
     * Mengganti komponen pada MO secara spesifik tanpa menyentuh Master BoM.
     */
    public function switchComponent(
        int $moComponentId,
        int $newProductId,
        string $reason,
        ?float $customQty = null,
        ?int $userId = null
    ): MoComponent {
        return DB::transaction(function () use ($moComponentId, $newProductId, $reason, $customQty, $userId) {
            $component = MoComponent::with('manufacturingOrder')->findOrFail($moComponentId);

            if (in_array($component->manufacturingOrder->status, ['done', 'cancelled'])) {
                throw new Exception('Tidak dapat mengganti komponen pada MO yang sudah selesai atau dibatalkan.');
            }

            $newProduct = Product::findOrFail($newProductId);

            // Hitung konversi Qty jika tidak diisi manual
            $finalQty = $customQty;
            if ($finalQty === null) {
                // Cek apakah ada aturan konversi
                $rule = ProductSubstitute::where('primary_product_id', $component->original_product_id)
                    ->where('substitute_product_id', $newProductId)
                    ->first();

                $conversionRate = $rule ? (float) $rule->conversion_rate : 1.0;
                $finalQty = (float) $component->planned_qty * $conversionRate;
            }

            $component->update([
                'actual_product_id' => $newProductId,
                'is_switched' => ($newProductId !== $component->original_product_id),
                'switch_reason' => $reason,
                'planned_qty' => $finalQty,
                'uom' => $newProduct->uom,
                'switched_by' => $userId ?? auth()->id(),
                'switched_at' => now(),
            ]);

            return $component->fresh(['originalProduct', 'actualProduct', 'switchedBy']);
        });
    }

    /**
     * Mengeluarkan material ke Lantai Produksi (Release to Production)
     * Menggunakan actual_product_id (baik material asli maupun material pengganti hasil switching)
     */
    public function releaseToProduction(int $moId, ?int $userId = null): ManufacturingOrder
    {
        return DB::transaction(function () use ($moId, $userId) {
            $mo = ManufacturingOrder::with('components.actualProduct')->findOrFail($moId);

            if ($mo->status === 'cancelled' || $mo->status === 'done') {
                throw new Exception("MO status {$mo->status} tidak dapat dirilis.");
            }

            $sourceLocId = $mo->source_location_id ?? StockLocation::mainLocationId();
            $prodLocId = StockLocation::productionLocationId();

            foreach ($mo->components as $component) {
                $qtyToIssue = (float) ($component->planned_qty - $component->issued_qty);
                if ($qtyToIssue <= 0) {
                    continue;
                }

                // Cek stok material aktual
                $availableStock = $this->inventoryService->getCurrentStock($component->actual_product_id, $sourceLocId);
                if ($availableStock < $qtyToIssue) {
                    throw new Exception("Stok untuk {$component->actualProduct->name} tidak mencukupi di gudang (Tersedia: {$availableStock}, Dibutuhkan: {$qtyToIssue}). Lakukan component switching atau PO terlebih dahulu.");
                }

                // Catat mutasi Stock Move (Gudang -> Lantai Produksi)
                $this->inventoryService->recordStockMove([
                    'product_id' => $component->actual_product_id,
                    'from_location_id' => $sourceLocId,
                    'to_location_id' => $prodLocId,
                    'qty' => $qtyToIssue,
                    'uom' => $component->uom,
                    'category' => StockMove::CAT_MO_CONSUMPTION,
                    'reference_type' => 'ManufacturingOrder',
                    'reference_id' => $mo->id,
                    'reference_number' => $mo->mo_number,
                    'notes' => "Pengeluaran material untuk {$mo->mo_number}" . ($component->is_switched ? " (Substitusi: {$component->switch_reason})" : ""),
                    'created_by' => $userId ?? auth()->id(),
                ]);

                // Update issued_qty
                $component->update([
                    'issued_qty' => $component->issued_qty + $qtyToIssue,
                ]);
            }

            $mo->update([
                'status' => 'in_progress',
            ]);

            return $mo->fresh(['components.actualProduct', 'components.originalProduct']);
        });
    }

    /**
     * ALUR MASUK: Retur Sisa Produksi (Production Return)
     * Mengembalikan material berlebih dari lantai produksi kembali ke gudang utama.
     */
    public function returnLeftoverMaterial(
        int $moId,
        int $moComponentId,
        float $returnedQty,
        ?string $notes = null,
        ?int $userId = null
    ): MoComponent {
        return DB::transaction(function () use ($moId, $moComponentId, $returnedQty, $notes, $userId) {
            $mo = ManufacturingOrder::findOrFail($moId);
            $component = MoComponent::with('actualProduct')->where('manufacturing_order_id', $moId)->findOrFail($moComponentId);

            if ($returnedQty <= 0) {
                throw new Exception('Jumlah retur harus lebih dari 0.');
            }

            $maxReturnable = (float) ($component->issued_qty - $component->returned_qty);
            if ($returnedQty > $maxReturnable) {
                throw new Exception("Jumlah retur ({$returnedQty}) melebihi sisa material yang dikeluarkan ({$maxReturnable}).");
            }

            $sourceLocId = $mo->source_location_id ?? StockLocation::mainLocationId();
            $prodLocId = StockLocation::productionLocationId();

            // Catat mutasi Stock Move (Lantai Produksi -> Gudang Utama)
            $this->inventoryService->recordStockMove([
                'product_id' => $component->actual_product_id,
                'from_location_id' => $prodLocId,
                'to_location_id' => $sourceLocId,
                'qty' => $returnedQty,
                'uom' => $component->uom,
                'category' => StockMove::CAT_PRODUCTION_RETURN,
                'reference_type' => 'ManufacturingOrder',
                'reference_id' => $mo->id,
                'reference_number' => $mo->mo_number,
                'notes' => "Pengembalian sisa produksi {$mo->mo_number}" . ($notes ? ": {$notes}" : ""),
                'created_by' => $userId ?? auth()->id(),
            ]);

            // Update returned_qty
            $component->update([
                'returned_qty' => $component->returned_qty + $returnedQty,
            ]);

            return $component->fresh(['actualProduct', 'originalProduct']);
        });
    }

    /**
     * Selesaikan MO & Bukukan Barang Jadi ke Gudang
     */
    public function completeManufacturingOrder(int $moId, float $producedQty, ?int $userId = null): ManufacturingOrder
    {
        return DB::transaction(function () use ($moId, $producedQty, $userId) {
            $mo = ManufacturingOrder::with('product')->findOrFail($moId);

            $prodLocId = StockLocation::productionLocationId();
            $destLocId = $mo->destination_location_id ?? StockLocation::finishedGoodsLocationId();

            // Catat penambahan barang jadi di Gudang FG
            $this->inventoryService->recordStockMove([
                'product_id' => $mo->product_id,
                'from_location_id' => $prodLocId,
                'to_location_id' => $destLocId,
                'qty' => $producedQty,
                'uom' => $mo->uom,
                'category' => StockMove::CAT_MO_FINISHED_GOODS,
                'reference_type' => 'ManufacturingOrder',
                'reference_id' => $mo->id,
                'reference_number' => $mo->mo_number,
                'notes' => "Hasil jadi produksi {$mo->mo_number} ({$producedQty} {$mo->uom})",
                'created_by' => $userId ?? auth()->id(),
            ]);

            $mo->update([
                'status' => 'done',
                'produced_qty' => $producedQty,
                'end_date' => date('Y-m-d'),
            ]);

            return $mo->fresh(['product', 'components.actualProduct', 'components.originalProduct']);
        });
    }

    /**
     * Menambahkan baris komponen baru langsung ke Manufacturing Order
     */
    public function addComponentLine(int $moId, array $data): MoComponent
    {
        return DB::transaction(function () use ($moId, $data) {
            $mo = ManufacturingOrder::findOrFail($moId);
            $product = Product::findOrFail($data['product_id']);

            return MoComponent::create([
                'manufacturing_order_id' => $mo->id,
                'original_product_id' => $product->id,
                'actual_product_id' => $product->id,
                'is_switched' => false,
                'planned_qty' => (float) ($data['planned_qty'] ?? $data['quantity'] ?? 1),
                'issued_qty' => 0,
                'returned_qty' => 0,
                'uom' => $data['uom'] ?? $product->uom,
            ]);
        });
    }

    /**
     * Menghapus baris komponen dari Manufacturing Order
     */
    public function removeComponentLine(int $moId, int $componentId): bool
    {
        return DB::transaction(function () use ($moId, $componentId) {
            $comp = MoComponent::where('manufacturing_order_id', $moId)->findOrFail($componentId);
            if ($comp->issued_qty > 0) {
                throw new Exception('Komponen yang sudah dikeluarkan sebagian ke lantai produksi tidak dapat dihapus.');
            }
            return $comp->delete();
        });
    }
}

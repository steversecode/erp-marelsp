<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockLocation;
use App\Models\StockMove;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Buat Purchase Order baru
     */
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $poNumber = $data['po_number'] ?? 'PO-' . date('Ym') . '-' . strtoupper(Str::random(4));
            $supplierLocationId = $data['supplier_location_id'] ?? StockLocation::vendorLocationId();

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_name' => $data['supplier_name'],
                'supplier_location_id' => $supplierLocationId,
                'order_date' => $data['order_date'] ?? date('Y-m-d'),
                'expected_date' => $data['expected_date'] ?? null,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $subtotal = (float) $item['ordered_qty'] * (float) ($item['unit_price'] ?? 0);
                $totalAmount += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'ordered_qty' => $item['ordered_qty'],
                    'received_qty' => 0,
                    'uom' => $item['uom'] ?? 'kg',
                    'unit_price' => $item['unit_price'] ?? 0,
                    'subtotal' => $subtotal,
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);

            return $po->load('items.product', 'supplierLocation');
        });
    }

    /**
     * ALUR MASUK: Penerimaan Barang dari Supplier PO (Goods Receipt)
     */
    public function receiveGoods(
        int $poId,
        array $receivedItems, // [ [ 'item_id' => 1, 'qty' => 500, 'batch_number' => 'LOT-01' ], ... ]
        ?int $destinationLocationId = null,
        ?int $userId = null
    ): PurchaseOrder {
        return DB::transaction(function () use ($poId, $receivedItems, $destinationLocationId, $userId) {
            $po = PurchaseOrder::with('items.product')->findOrFail($poId);

            $supplierLocId = $po->supplier_location_id ?? StockLocation::vendorLocationId();
            $destLocId = $destinationLocationId ?? StockLocation::mainLocationId();

            $allCompleted = true;

            foreach ($receivedItems as $recv) {
                $poItem = $po->items->firstWhere('id', $recv['item_id']);
                if (!$poItem) {
                    continue;
                }

                $qtyReceived = (float) $recv['qty'];
                if ($qtyReceived <= 0) {
                    continue;
                }

                $batch = $recv['batch_number'] ?? null;

                // Catat mutasi Stock Move (Supplier -> Gudang Utama)
                $this->inventoryService->recordStockMove([
                    'product_id' => $poItem->product_id,
                    'from_location_id' => $supplierLocId,
                    'to_location_id' => $destLocId,
                    'qty' => $qtyReceived,
                    'uom' => $poItem->uom,
                    'category' => StockMove::CAT_PO_RECEIPT,
                    'reference_type' => 'PurchaseOrder',
                    'reference_id' => $po->id,
                    'reference_number' => $po->po_number,
                    'batch_lot_number' => $batch,
                    'notes' => "Penerimaan PO {$po->po_number} dari {$po->supplier_name}",
                    'created_by' => $userId ?? auth()->id(),
                ]);

                $newReceivedQty = $poItem->received_qty + $qtyReceived;
                $poItem->update(['received_qty' => $newReceivedQty]);

                if ($newReceivedQty < $poItem->ordered_qty) {
                    $allCompleted = false;
                }
            }

            // Periksa status PO
            $totalOrdered = $po->items->sum('ordered_qty');
            $totalReceived = $po->items->sum('received_qty');

            if ($totalReceived >= $totalOrdered) {
                $po->update(['status' => 'received']);
            } elseif ($totalReceived > 0) {
                $po->update(['status' => 'partial_received']);
            }

            return $po->fresh('items.product');
        });
    }
}

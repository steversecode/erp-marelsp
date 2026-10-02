<?php

namespace App\Http\Controllers;

use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Services\ManufacturingService;
use App\Services\PurchaseService;
use Exception;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    protected PurchaseService $purchaseService;
    protected ManufacturingService $manufacturingService;

    public function __construct(PurchaseService $purchaseService, ManufacturingService $manufacturingService)
    {
        $this->purchaseService = $purchaseService;
        $this->manufacturingService = $manufacturingService;
    }

    /**
     * List Penerimaan PO
     */
    public function poIndex()
    {
        $purchaseOrders = PurchaseOrder::with(['items.product', 'supplierLocation'])->latest()->paginate(15);
        $products = Product::where('is_active', true)->where('type', 'raw_material')->get();

        return view('stock-in.po-receipt', compact('purchaseOrders', 'products'));
    }

    /**
     * Buat PO Baru
     */
    public function storePo(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'ordered_qty' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);

            $this->purchaseService->createPurchaseOrder([
                'supplier_name' => $request->supplier_name,
                'order_date' => $request->order_date,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'ordered_qty' => $request->ordered_qty,
                        'unit_price' => $request->unit_price,
                        'uom' => $product->uom,
                    ],
                ],
                'created_by' => auth()->id() ?? 1,
            ]);

            return back()->with('success', 'Purchase Order berhasil dibuat! Anda sekarang dapat mencatat penerimaan barang.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    /**
     * Terima Barang Masuk dari Supplier PO
     */
    public function receivePo(Request $request, int $poId)
    {
        $request->validate([
            'item_id' => 'required|exists:purchase_order_items,id',
            'qty' => 'required|numeric|min:0.001',
            'batch_number' => 'nullable|string|max:100',
        ]);

        try {
            $this->purchaseService->receiveGoods(
                $poId,
                [
                    [
                        'item_id' => (int) $request->item_id,
                        'qty' => (float) $request->qty,
                        'batch_number' => $request->batch_number,
                    ],
                ],
                null,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Penerimaan barang dari PO berhasil dicatat ke saldo gudang utama!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menerima barang: ' . $e->getMessage());
        }
    }

    /**
     * List Retur Sisa Produksi
     */
    public function leftoverIndex()
    {
        $activeOrders = ManufacturingOrder::with(['product', 'components.actualProduct'])
            ->whereIn('status', ['in_progress', 'done'])
            ->latest()
            ->get();

        $recentReturns = StockMove::with(['product', 'fromLocation', 'toLocation'])
            ->where('category', StockMove::CAT_PRODUCTION_RETURN)
            ->latest()
            ->paginate(15);

        return view('stock-in.leftover-return', compact('activeOrders', 'recentReturns'));
    }

    /**
     * Simpan Retur Sisa Produksi
     */
    public function storeLeftover(Request $request)
    {
        $request->validate([
            'manufacturing_order_id' => 'required|exists:manufacturing_orders,id',
            'mo_component_id' => 'required|exists:mo_components,id',
            'returned_qty' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->manufacturingService->returnLeftoverMaterial(
                (int) $request->manufacturing_order_id,
                (int) $request->mo_component_id,
                (float) $request->returned_qty,
                $request->notes,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Sisa produksi berhasil dikembalikan ke Gudang Utama dan saldo stok bertambah kembali!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal retur sisa produksi: ' . $e->getMessage());
        }
    }
}

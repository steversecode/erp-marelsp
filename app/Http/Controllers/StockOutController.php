<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Services\SampleProductionService;
use App\Services\SubcontractDyeingService;
use Exception;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    protected SampleProductionService $sampleService;
    protected SubcontractDyeingService $dyeingService;

    public function __construct(SampleProductionService $sampleService, SubcontractDyeingService $dyeingService)
    {
        $this->sampleService = $sampleService;
        $this->dyeingService = $dyeingService;
    }

    /**
     * Halaman Pengeluaran Sample
     */
    public function sampleIndex()
    {
        $products = Product::where('is_active', true)->where('type', 'raw_material')->get();
        $sampleMoves = StockMove::with(['product', 'fromLocation', 'toLocation'])
            ->where('category', StockMove::CAT_SAMPLE_ISSUE)
            ->latest()
            ->paginate(15);

        return view('stock-out.sample', compact('products', 'sampleMoves'));
    }

    /**
     * Simpan Pengeluaran Sample
     */
    public function storeSample(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.001',
            'sample_reference' => 'nullable|string|max:100',
            'purpose' => 'required|string|max:255',
        ]);

        try {
            $this->sampleService->issueForSample(
                (int) $request->product_id,
                (float) $request->qty,
                $request->sample_reference,
                $request->purpose,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Pengeluaran material untuk keperluan sample berhasil dibukukan!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengeluarkan material sample: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Proses Celup (Dyeing)
     */
    public function dyeingIndex()
    {
        $rawMaterials = Product::where('is_active', true)->where('type', 'raw_material')->where('category', 'Benang')->get();
        $allProducts = Product::where('is_active', true)->get();
        $dyeingLocations = StockLocation::where('type', 'dyeing_subcon')->get();

        $dyeingMoves = StockMove::with(['product', 'fromLocation', 'toLocation'])
            ->whereIn('category', [StockMove::CAT_DYEING_OUT, StockMove::CAT_DYEING_RETURN])
            ->latest()
            ->paginate(15);

        return view('stock-out.dyeing', compact('rawMaterials', 'allProducts', 'dyeingLocations', 'dyeingMoves'));
    }

    /**
     * Simpan Pengiriman ke Vendor Celup
     */
    public function storeDyeingOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'vendor_location_id' => 'nullable|exists:stock_locations,id',
            'color_target' => 'required|string|max:100',
            'batch_number' => 'nullable|string|max:100',
        ]);

        try {
            $this->dyeingService->issueForDyeing(
                (int) $request->product_id,
                (float) $request->qty,
                $request->vendor_location_id ? (int) $request->vendor_location_id : null,
                null,
                $request->color_target,
                $request->batch_number,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Material greige berhasil dikeluarkan untuk proses celup ke vendor!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengirim ke proses celup: ' . $e->getMessage());
        }
    }

    /**
     * Simpan Penerimaan Kembali dari Vendor Celup
     */
    public function storeDyeingReturn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'vendor_location_id' => 'nullable|exists:stock_locations,id',
            'dyeing_order_number' => 'nullable|string|max:100',
            'batch_number' => 'nullable|string|max:100',
        ]);

        try {
            $this->dyeingService->receiveFromDyeing(
                (int) $request->product_id,
                (float) $request->qty,
                $request->vendor_location_id ? (int) $request->vendor_location_id : null,
                $request->dyeing_order_number,
                $request->batch_number,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Penerimaan hasil benang celup berhasil dicatat ke saldo gudang!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menerima hasil celup: ' . $e->getMessage());
        }
    }
}

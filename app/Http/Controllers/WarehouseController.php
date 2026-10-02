<?php

namespace App\Http\Controllers;

use App\Models\StockLocation;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of Warehouses (Odoo Tree/List View).
     */
    public function index(Request $request)
    {
        $query = Warehouse::with(['lotStock', 'viewLocation', 'creator']);

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhereHas('lotStock', function ($l) use ($q) {
                        $l->where('code', 'like', "%{$q}%")
                          ->orWhere('name', 'like', "%{$q}%");
                    });
            });
        }

        $warehouses = $query->orderBy('id')->paginate(20)->withQueryString();
        $totalWarehouses = Warehouse::count();

        return view('warehouses.index', compact('warehouses', 'totalWarehouses'));
    }

    /**
     * Show the form for creating a new Warehouse (Odoo Form View).
     */
    public function create()
    {
        $otherWarehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->orderBy('code')->get();

        return view('warehouses.create', compact('otherWarehouses', 'locations'));
    }

    /**
     * Store a newly created Warehouse in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:warehouses,code'],
            'address' => ['nullable', 'string', 'max:255'],
            'lot_stock_id' => ['nullable', 'exists:stock_locations,id'],
            'incoming_steps' => ['required', Rule::in(['1_step', '2_steps', '3_steps'])],
            'outgoing_steps' => ['required', Rule::in(['1_step', '2_steps', '3_steps'])],
            'buy_to_resupply' => ['nullable', 'boolean'],
            'manufacture_to_resupply' => ['nullable', 'boolean'],
            'manufacture_steps' => ['nullable', Rule::in(['1_step', '2_steps', '3_steps'])],
            'resupply_from_warehouse_ids' => ['nullable', 'array'],
            'resupply_from_warehouse_ids.*' => ['exists:warehouses,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $warehouse = DB::transaction(function () use ($request, $validated) {
                // If no lot_stock_id chosen, auto create one: CODE/Stock
                $lotStockId = $validated['lot_stock_id'] ?? null;
                if (!$lotStockId) {
                    $stockLoc = StockLocation::firstOrCreate(
                        ['code' => strtoupper($validated['code']) . '/Stock'],
                        [
                            'name' => 'Stock',
                            'type' => 'internal',
                            'address' => $validated['address'] ?? $validated['name'],
                            'is_active' => true,
                        ]
                    );
                    $lotStockId = $stockLoc->id;
                }

                $wh = Warehouse::create([
                    'name' => $validated['name'],
                    'code' => strtoupper($validated['code']),
                    'address' => $validated['address'] ?? null,
                    'lot_stock_id' => $lotStockId,
                    'incoming_steps' => $validated['incoming_steps'],
                    'outgoing_steps' => $validated['outgoing_steps'],
                    'buy_to_resupply' => $request->boolean('buy_to_resupply'),
                    'manufacture_to_resupply' => $request->boolean('manufacture_to_resupply'),
                    'manufacture_steps' => $validated['manufacture_steps'] ?? '2_steps',
                    'resupply_from_warehouse_ids' => $validated['resupply_from_warehouse_ids'] ?? [],
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                // Link the location
                StockLocation::where('id', $lotStockId)->update(['warehouse_id' => $wh->id]);

                return $wh;
            });

            return redirect()->route('configuration.warehouses.show', $warehouse->id)
                ->with('success', "Warehouse [{$warehouse->code}] {$warehouse->name} berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat Warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Display or edit the specified Warehouse (Odoo Master Form View).
     */
    public function show(int $id)
    {
        $warehouse = Warehouse::with(['lotStock', 'viewLocation', 'creator', 'updater'])->findOrFail($id);
        $otherWarehouses = Warehouse::where('id', '!=', $warehouse->id)->where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->orderBy('code')->get();

        return view('warehouses.show', compact('warehouse', 'otherWarehouses', 'locations'));
    }

    public function edit(int $id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified Warehouse in storage.
     */
    public function update(Request $request, int $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('warehouses', 'code')->ignore($warehouse->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'lot_stock_id' => ['nullable', 'exists:stock_locations,id'],
            'incoming_steps' => ['required', Rule::in(['1_step', '2_steps', '3_steps'])],
            'outgoing_steps' => ['required', Rule::in(['1_step', '2_steps', '3_steps'])],
            'buy_to_resupply' => ['nullable', 'boolean'],
            'manufacture_to_resupply' => ['nullable', 'boolean'],
            'manufacture_steps' => ['nullable', Rule::in(['1_step', '2_steps', '3_steps'])],
            'resupply_from_warehouse_ids' => ['nullable', 'array'],
            'resupply_from_warehouse_ids.*' => ['exists:warehouses,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($warehouse, $request, $validated) {
                $warehouse->update([
                    'name' => $validated['name'],
                    'code' => strtoupper($validated['code']),
                    'address' => $validated['address'] ?? null,
                    'lot_stock_id' => $validated['lot_stock_id'] ?? $warehouse->lot_stock_id,
                    'incoming_steps' => $validated['incoming_steps'],
                    'outgoing_steps' => $validated['outgoing_steps'],
                    'buy_to_resupply' => $request->boolean('buy_to_resupply'),
                    'manufacture_to_resupply' => $request->boolean('manufacture_to_resupply'),
                    'manufacture_steps' => $validated['manufacture_steps'] ?? '2_steps',
                    'resupply_from_warehouse_ids' => $validated['resupply_from_warehouse_ids'] ?? [],
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
                    'updated_by' => auth()->id(),
                ]);

                if ($warehouse->lot_stock_id) {
                    StockLocation::where('id', $warehouse->lot_stock_id)->update(['warehouse_id' => $warehouse->id]);
                }
            });

            return redirect()->route('configuration.warehouses.show', $warehouse->id)
                ->with('success', "Warehouse [{$warehouse->code}] {$warehouse->name} berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui Warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Warehouse from storage.
     */
    public function destroy(int $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        try {
            $warehouse->delete();
            return redirect()->route('configuration.warehouses.index')
                ->with('success', "Warehouse [{$warehouse->code}] berhasil dihapus!");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus Warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Bulk destroy selected Warehouses.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada gudang yang dipilih.'], 400);
        }

        try {
            $count = Warehouse::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => "Berhasil menghapus {$count} gudang."]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus gudang: ' . $e->getMessage()], 500);
        }
    }
}

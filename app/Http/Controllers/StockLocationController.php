<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockLocationController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of Stock Locations (Odoo Tree View).
     */
    public function index(Request $request)
    {
        $query = StockLocation::with(['parent', 'warehouse'])
            ->withCount(['incomingStockMoves', 'outgoingStockMoves', 'children']);

        // Quick Search Keyword
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhereHas('parent', function ($pSub) use ($q) {
                        $pSub->where('name', 'like', "%{$q}%")
                            ->orWhere('code', 'like', "%{$q}%");
                    })
                    ->orWhereHas('warehouse', function ($wSub) use ($q) {
                        $wSub->where('name', 'like', "%{$q}%")
                            ->orWhere('code', 'like', "%{$q}%");
                    });
            });
        }

        // Filter: Location Type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter: Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        // Filter: Parent Location
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        // Filter: Is Scrap
        if ($request->filled('is_scrap')) {
            $query->where('is_scrap', $request->boolean('is_scrap'));
        }

        // Filter: Is Return
        if ($request->filled('is_return')) {
            $query->where('is_return', $request->boolean('is_return'));
        }

        // Filter: Active Status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $allLocations = StockLocation::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $locations = $query->orderBy('warehouse_id')->orderBy('type')->orderBy('name')->paginate(20)->withQueryString();

        $totalLocations = StockLocation::count();
        $internalCount = StockLocation::where('type', 'internal')->count();
        $scrapCount = StockLocation::where('is_scrap', true)->count();
        $viewCount = StockLocation::where('type', 'view')->count();

        return view('configuration.locations.index', compact(
            'locations', 
            'allLocations', 
            'warehouses', 
            'totalLocations', 
            'internalCount', 
            'scrapCount', 
            'viewCount'
        ));
    }

    /**
     * Show the form for creating a new Stock Location.
     */
    public function create()
    {
        $parentLocations = StockLocation::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('configuration.locations.create', compact('parentLocations', 'warehouses'));
    }

    /**
     * Store a newly created Stock Location in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:stock_locations,code'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:stock_locations,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'type' => ['required', Rule::in(['internal', 'view', 'vendor', 'customer', 'production', 'sample', 'dyeing_subcon', 'loss', 'transit'])],
            'is_scrap' => ['nullable'],
            'is_return' => ['nullable'],
            'address' => ['nullable', 'string', 'max:500'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_scrap'] = $request->has('is_scrap');
        $validated['is_return'] = $request->has('is_return');
        $validated['is_active'] = $request->has('is_active');

        $location = StockLocation::create($validated);

        return redirect()->route('configuration.locations.show', $location->id)
            ->with('success', "Location [{$location->code}] '{$location->name}' created successfully!");
    }

    /**
     * Display the specified Stock Location (Odoo Form Sheet).
     */
    public function show(int $id)
    {
        $location = StockLocation::with(['parent', 'children', 'warehouse'])
            ->withCount(['incomingStockMoves', 'outgoingStockMoves'])
            ->findOrFail($id);

        // Recent Moves
        $recentMoves = StockMove::with(['product', 'fromLocation', 'toLocation', 'creator'])
            ->where(function ($q) use ($location) {
                $q->where('from_location_id', $location->id)
                    ->orWhere('to_location_id', $location->id);
            })
            ->latest()
            ->limit(10)
            ->get();

        $totalMovesCount = $location->incoming_stock_moves_count + $location->outgoing_stock_moves_count;

        // Current Products in this location
        $allProducts = Product::where('is_active', true)->get();
        $storedProducts = [];
        $totalStockUnits = 0;
        $totalValuation = 0;

        foreach ($allProducts as $product) {
            $stock = $this->inventoryService->getCurrentStock($product->id, $location->id);
            if ($stock > 0) {
                $val = $stock * (float) $product->cost_price;
                $storedProducts[] = [
                    'product' => $product,
                    'on_hand' => $stock,
                    'valuation' => $val,
                ];
                $totalStockUnits += $stock;
                $totalValuation += $val;
            }
        }

        return view('configuration.locations.show', compact(
            'location', 
            'recentMoves', 
            'totalMovesCount', 
            'storedProducts', 
            'totalStockUnits', 
            'totalValuation'
        ));
    }

    /**
     * Show the form for editing the specified Stock Location.
     */
    public function edit(int $id)
    {
        $location = StockLocation::findOrFail($id);
        // Exclude self to prevent circular reference
        $parentLocations = StockLocation::where('id', '!=', $location->id)->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('configuration.locations.edit', compact('location', 'parentLocations', 'warehouses'));
    }

    /**
     * Update the specified Stock Location in storage.
     */
    public function update(Request $request, int $id)
    {
        $location = StockLocation::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('stock_locations', 'code')->ignore($location->id)],
            'barcode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:stock_locations,id', Rule::notIn([$location->id])],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'type' => ['required', Rule::in(['internal', 'view', 'vendor', 'customer', 'production', 'sample', 'dyeing_subcon', 'loss', 'transit'])],
            'is_scrap' => ['nullable'],
            'is_return' => ['nullable'],
            'address' => ['nullable', 'string', 'max:500'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_scrap'] = $request->has('is_scrap');
        $validated['is_return'] = $request->has('is_return');
        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        return redirect()->route('configuration.locations.show', $location->id)
            ->with('success', "Location [{$location->code}] '{$location->name}' updated successfully!");
    }

    /**
     * Remove the specified Stock Location from storage.
     */
    public function destroy(int $id)
    {
        $location = StockLocation::withCount(['incomingStockMoves', 'outgoingStockMoves'])->findOrFail($id);

        if ($location->incoming_stock_moves_count > 0 || $location->outgoing_stock_moves_count > 0) {
            // Archive instead of hard delete to preserve ledger integrity
            $location->update(['is_active' => false]);
            return redirect()->route('configuration.locations.index')
                ->with('success', "Location [{$location->code}] has existing ledger moves and has been archived (set to inactive).");
        }

        $code = $location->code;
        $location->delete();

        return redirect()->route('configuration.locations.index')
            ->with('success', "Location [{$code}] deleted successfully.");
    }

    /**
     * Bulk archive / delete selected Stock Locations.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:stock_locations,id'],
        ]);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $loc = StockLocation::withCount(['incomingStockMoves', 'outgoingStockMoves'])->find($id);
            if ($loc) {
                if ($loc->incoming_stock_moves_count > 0 || $loc->outgoing_stock_moves_count > 0) {
                    $loc->update(['is_active' => false]);
                } else {
                    $loc->delete();
                }
                $count++;
            }
        }

        return redirect()->route('configuration.locations.index')
            ->with('success', "{$count} locations have been processed (deleted/archived).");
    }
}

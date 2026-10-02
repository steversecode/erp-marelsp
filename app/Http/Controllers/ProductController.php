<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $category = $request->query('category');
        $categoryId = $request->query('category_id');
        $tracking = $request->query('tracking');

        $query = Product::with(['productCategory', 'boms', 'substitutes'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('productCategory', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } elseif ($category) {
            $query->where(function ($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('productCategory', function ($cq) use ($category) {
                      $cq->where('name', $category);
                  });
            });
        }

        if ($tracking) {
            $query->where('tracking', $tracking);
        }

        $products = $query->paginate(25)->withQueryString();
        $mainLocation = StockLocation::where('code', 'WH-MAIN')->first();

        $categories = ProductCategory::orderBy('name')->get();

        return view('products.index', compact('products', 'search', 'type', 'category', 'categoryId', 'tracking', 'categories', 'mainLocation'));
    }

    /**
     * Show the form for creating a new product (Odoo style).
     */
    public function create()
    {
        $categories = ProductCategory::with('parent')->orderBy('name')->get();
        $allProducts = Product::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $uoms = \App\Models\Uom::where('is_active', true)->with('category')->orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->with('lotStock')->get();

        return view('products.create', compact('categories', 'allProducts', 'users', 'uoms', 'warehouses'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'type' => ['required', 'string', Rule::in(['goods', 'service', 'combo', 'raw_material', 'work_in_progress', 'finished_good', 'sample'])],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'uom' => ['required', 'string', 'max:20'],
            'uom_po' => ['nullable', 'string', 'max:20'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'invoicing_policy' => ['required', 'string', Rule::in(['ordered', 'delivered'])],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'reorder_qty' => ['nullable', 'numeric', 'min:0'],
            'tracking' => ['required', 'string', Rule::in(['quantity', 'lot', 'serial'])],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'purchase_lead_time_days' => ['nullable', 'integer', 'min:0'],
            'preferred_vendor' => ['nullable', 'string', 'max:255'],
            'vendor_code' => ['nullable', 'string', 'max:50'],
            'purchase_description' => ['nullable', 'string'],
            'costing_method' => ['nullable', 'string', Rule::in(['standard', 'average', 'fifo'])],
            'valuation_method' => ['nullable', 'string', Rule::in(['manual', 'automated'])],
            'description' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // Checkboxes
        $validated['can_be_sold'] = $request->has('can_be_sold');
        $validated['can_be_purchased'] = $request->has('can_be_purchased');
        $validated['can_be_manufactured'] = $request->has('can_be_manufactured');
        $validated['can_be_subcontracted'] = $request->has('can_be_subcontracted');
        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['allow_negative_stock'] = $request->has('allow_negative_stock');
        $validated['route_buy'] = $request->has('route_buy');
        $validated['route_manufacture'] = $request->has('route_manufacture');
        $validated['route_subcontract'] = $request->has('route_subcontract');
        $validated['route_mto'] = $request->has('route_mto');
        $validated['route_resupply_ids'] = $request->input('route_resupply_ids', []);
        $validated['warehouse_id'] = $request->input('warehouse_id');
        $validated['is_active'] = $request->has('is_active');

        // Audit & Responsible
        $validated['responsible_id'] = $validated['responsible_id'] ?? auth()->id();
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        // Sync legacy category text name if category_id is set
        if (!empty($validated['category_id'])) {
            $cat = ProductCategory::find($validated['category_id']);
            if ($cat) {
                $validated['category'] = $cat->name;
            }
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product = Product::create($validated);

        return redirect()->route('products.show', $product->id)
            ->with('success', "Product [{$product->code}] {$product->name} created successfully!");
    }

    /**
     * Display the specified product (Odoo Master Form View with Smart Buttons).
     */
    public function show(int $id)
    {
        $product = Product::with(['productCategory', 'bomItems', 'boms', 'substitutes', 'substitutedFor', 'responsible', 'creator', 'updater', 'warehouse'])->findOrFail($id);
        
        $currentStock = $this->inventoryService->getCurrentStock($product->id);

        $recentMoves = StockMove::with(['fromLocation', 'toLocation', 'creator'])
            ->where('product_id', $product->id)
            ->latest()
            ->limit(10)
            ->get();

        $totalMovesCount = StockMove::where('product_id', $product->id)->count();

        return view('products.show', compact('product', 'currentStock', 'recentMoves', 'totalMovesCount'));
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(int $id)
    {
        $product = Product::with(['productCategory', 'substitutes', 'responsible', 'creator', 'updater', 'warehouse'])->findOrFail($id);
        $categories = ProductCategory::with('parent')->orderBy('name')->get();
        $allProducts = Product::where('id', '!=', $product->id)->where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $uoms = \App\Models\Uom::where('is_active', true)->with('category')->orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->with('lotStock')->get();

        return view('products.edit', compact('product', 'categories', 'allProducts', 'users', 'uoms', 'warehouses'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'type' => ['required', 'string', Rule::in(['goods', 'service', 'combo', 'raw_material', 'work_in_progress', 'finished_good', 'sample'])],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'uom' => ['required', 'string', 'max:20'],
            'uom_po' => ['nullable', 'string', 'max:20'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'invoicing_policy' => ['required', 'string', Rule::in(['ordered', 'delivered'])],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'reorder_qty' => ['nullable', 'numeric', 'min:0'],
            'tracking' => ['required', 'string', Rule::in(['quantity', 'lot', 'serial'])],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'purchase_lead_time_days' => ['nullable', 'integer', 'min:0'],
            'preferred_vendor' => ['nullable', 'string', 'max:255'],
            'vendor_code' => ['nullable', 'string', 'max:50'],
            'purchase_description' => ['nullable', 'string'],
            'costing_method' => ['nullable', 'string', Rule::in(['standard', 'average', 'fifo'])],
            'valuation_method' => ['nullable', 'string', Rule::in(['manual', 'automated'])],
            'description' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // Checkboxes
        $validated['can_be_sold'] = $request->has('can_be_sold');
        $validated['can_be_purchased'] = $request->has('can_be_purchased');
        $validated['can_be_manufactured'] = $request->has('can_be_manufactured');
        $validated['can_be_subcontracted'] = $request->has('can_be_subcontracted');
        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['allow_negative_stock'] = $request->has('allow_negative_stock');
        $validated['route_buy'] = $request->has('route_buy');
        $validated['route_manufacture'] = $request->has('route_manufacture');
        $validated['route_subcontract'] = $request->has('route_subcontract');
        $validated['route_mto'] = $request->has('route_mto');
        $validated['route_resupply_ids'] = $request->input('route_resupply_ids', []);
        $validated['warehouse_id'] = $request->input('warehouse_id');
        $validated['is_active'] = $request->has('is_active');

        // Audit
        $validated['updated_by'] = auth()->id();

        // Sync legacy category text name if category_id is set
        if (!empty($validated['category_id'])) {
            $cat = ProductCategory::find($validated['category_id']);
            if ($cat) {
                $validated['category'] = $cat->name;
            }
        } else {
            $validated['category_id'] = null;
        }

        // Handle Image Upload & Remove
        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('products.show', $product->id)
            ->with('success', "Product [{$product->code}] {$product->name} updated successfully!");
    }

    /**
     * Remove or archive the specified product.
     */
    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);

        return redirect()->route('inventory.index')
            ->with('success', "Product [{$product->code}] {$product->name} has been archived/deleted.");
    }

    /**
     * Bulk archive / delete selected products.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        $count = Product::whereIn('id', $validated['ids'])->update(['is_active' => false]);

        return redirect()->route('inventory.index')
            ->with('success', "{$count} products have been archived/deleted successfully.");
    }

    /**
     * Display Odoo Stock On Hand breakdown per location for this product.
     */
    public function onHand(int $id, Request $request)
    {
        $product = Product::with(['productCategory', 'boms'])->findOrFail($id);
        $locations = StockLocation::orderBy('name')->get();
        $internalLocations = StockLocation::where('type', 'internal')->get();

        $quants = [];
        foreach ($locations as $loc) {
            $stock = $this->inventoryService->getCurrentStock($product->id, $loc->id);
            // If location has stock or if showing all locations is requested
            if ($stock > 0 || $request->has('show_all') || $request->query('location_id') == $loc->id) {
                $quants[] = [
                    'location' => $loc,
                    'on_hand' => $stock,
                    'reserved' => 0.0,
                    'available' => $stock,
                    'valuation' => $stock * (float) $product->cost_price,
                ];
            }
        }

        $totalOnHand = $this->inventoryService->getCurrentStock($product->id);
        $totalValuation = $totalOnHand * (float) $product->cost_price;

        return view('products.on-hand', compact('product', 'quants', 'locations', 'internalLocations', 'totalOnHand', 'totalValuation'));
    }

    /**
     * Update/Adjust product quantity (Odoo Inventory Adjustment / Stock Take).
     */
    public function updateQuantity(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'location_id' => ['required', 'exists:stock_locations,id'],
            'counted_qty' => ['required', 'numeric', 'min:0'],
            'batch_lot_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $location = StockLocation::findOrFail($validated['location_id']);
        $currentQty = $this->inventoryService->getCurrentStock($product->id, $location->id);
        $countedQty = (float) $validated['counted_qty'];
        $diff = $countedQty - $currentQty;

        if (abs($diff) > 0.0001) {
            $virtualLossLocation = StockLocation::where('type', 'loss')->first() 
                ?? StockLocation::firstOrCreate(['code' => 'SCRAP-LOSS'], ['name' => 'Inventory Adjustment / Loss', 'type' => 'loss']);

            if ($diff > 0) {
                // Stock Increase
                $this->inventoryService->recordStockMove([
                    'product_id' => $product->id,
                    'from_location_id' => $virtualLossLocation->id,
                    'to_location_id' => $location->id,
                    'qty' => $diff,
                    'uom' => $product->uom,
                    'category' => 'adjustment',
                    'reference_type' => 'inventory_adjustment',
                    'reference_number' => 'ADJ-' . strtoupper(date('ymd')) . '-' . rand(100, 999),
                    'batch_lot_number' => $validated['batch_lot_number'] ?? null,
                    'notes' => $validated['notes'] ?? 'Odoo Inventory Adjustment / Stock Take',
                ]);
            } else {
                // Stock Decrease
                $this->inventoryService->recordStockMove([
                    'product_id' => $product->id,
                    'from_location_id' => $location->id,
                    'to_location_id' => $virtualLossLocation->id,
                    'qty' => abs($diff),
                    'uom' => $product->uom,
                    'category' => 'adjustment',
                    'reference_type' => 'inventory_adjustment',
                    'reference_number' => 'ADJ-' . strtoupper(date('ymd')) . '-' . rand(100, 999),
                    'batch_lot_number' => $validated['batch_lot_number'] ?? null,
                    'notes' => $validated['notes'] ?? 'Odoo Inventory Adjustment / Stock Take',
                ]);
            }
        }

        return redirect()->route('products.on-hand', $product->id)
            ->with('success', "Quantity for [{$product->code}] {$product->name} in location {$location->name} updated to {$countedQty} {$product->uom}.");
    }

    /**
     * Save Odoo-style inline editable table grid rows (bulk update on hand quantities).
     */
    public function saveOnHandGrid(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.location_id' => ['required', 'exists:stock_locations,id'],
            'lines.*.on_hand' => ['required', 'numeric', 'min:0'],
            'lines.*.lot_number' => ['nullable', 'string', 'max:100'],
        ]);

        $virtualLossLocation = StockLocation::where('type', 'loss')->first() 
            ?? StockLocation::firstOrCreate(['code' => 'SCRAP-LOSS'], ['name' => 'Inventory Adjustment / Loss', 'type' => 'loss']);

        $updatedCount = 0;

        foreach ($validated['lines'] as $line) {
            $locationId = (int) $line['location_id'];
            $newQty = (float) $line['on_hand'];
            $lotNumber = $line['lot_number'] ?? null;

            $currentStock = $this->inventoryService->getCurrentStock($product->id, $locationId);
            $diff = $newQty - $currentStock;

            if (abs($diff) > 0.0001) {
                if ($diff > 0) {
                    // Stock Increase
                    $this->inventoryService->recordStockMove([
                        'product_id' => $product->id,
                        'from_location_id' => $virtualLossLocation->id,
                        'to_location_id' => $locationId,
                        'qty' => $diff,
                        'uom' => $product->uom,
                        'category' => 'adjustment',
                        'reference_type' => 'inventory_adjustment',
                        'reference_number' => 'ADJ-' . strtoupper(date('ymd')) . '-' . rand(100, 999),
                        'batch_lot_number' => $lotNumber,
                        'notes' => 'Odoo Inline On-Hand Quantity Count',
                    ]);
                } else {
                    // Stock Decrease
                    $this->inventoryService->recordStockMove([
                        'product_id' => $product->id,
                        'from_location_id' => $locationId,
                        'to_location_id' => $virtualLossLocation->id,
                        'qty' => abs($diff),
                        'uom' => $product->uom,
                        'category' => 'adjustment',
                        'reference_type' => 'inventory_adjustment',
                        'reference_number' => 'ADJ-' . strtoupper(date('ymd')) . '-' . rand(100, 999),
                        'batch_lot_number' => $lotNumber,
                        'notes' => 'Odoo Inline On-Hand Quantity Count',
                    ]);
                }
                $updatedCount++;
            }
        }

        return redirect()->route('products.on-hand', $product->id)
            ->with('success', "Stock on-hand for [{$product->code}] {$product->name} saved successfully.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $category = $request->query('category');
        $categoryId = $request->query('category_id');

        $query = Product::with(['productCategory'])->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
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

        $products = $query->latest()->paginate(20)->withQueryString();
        $mainLocation = StockLocation::where('code', 'WH-MAIN')->first();

        $items = $products->through(function ($prod) use ($mainLocation) {
            $stock = $this->inventoryService->getCurrentStock($prod->id, $mainLocation?->id);
            return [
                'model' => $prod,
                'current_stock' => $stock,
                'min_stock' => (float) $prod->min_stock,
                'is_low_stock' => $stock <= (float) $prod->min_stock,
                'valuation' => $stock * (float) $prod->cost_price,
            ];
        });

        $categories = ProductCategory::orderBy('name')->get();
        $totalProductsCount = Product::where('is_active', true)->count();

        return view('inventory.index', compact('items', 'search', 'type', 'category', 'categoryId', 'categories', 'totalProductsCount'));
    }

    public function moves(Request $request)
    {
        $category = $request->query('category');
        $productId = $request->query('product_id');

        $query = StockMove::with(['product', 'fromLocation', 'toLocation', 'creator'])->latest();

        if ($category) {
            $query->where('category', $category);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $moves = $query->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('inventory.moves', compact('moves', 'products', 'category', 'productId'));
    }
}

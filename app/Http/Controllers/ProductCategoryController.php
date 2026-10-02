<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories (Odoo Style) with advance search & 15/page pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $parentId = $request->query('parent_id');
        $costingMethod = $request->query('costing_method');
        $valuationMethod = $request->query('valuation_method');

        $query = ProductCategory::with(['parent', 'children'])
            ->withCount('products');

        // Search by keyword in name, code, or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('parent', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Parent
        if ($parentId !== null && $parentId !== '') {
            if ($parentId === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        // Filter by Costing Method
        if ($costingMethod) {
            $query->where('costing_method', $costingMethod);
        }

        // Filter by Valuation Method
        if ($valuationMethod) {
            $query->where('valuation_method', $valuationMethod);
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();
        $parentCategories = ProductCategory::whereNull('parent_id')
            ->orWhereHas('children')
            ->orderBy('name')
            ->get();
        $totalCount = ProductCategory::count();

        return view('configuration.categories', compact(
            'categories', 
            'parentCategories', 
            'search', 
            'parentId', 
            'costingMethod', 
            'valuationMethod',
            'totalCount'
        ));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'costing_method' => ['required', 'string', Rule::in(['standard', 'average', 'fifo'])],
            'valuation_method' => ['required', 'string', Rule::in(['manual', 'automated'])],
            'description' => ['nullable', 'string'],
        ]);

        $category = ProductCategory::create($validated);

        return redirect()->route('configuration.categories')
            ->with('success', "Category '{$category->name}' has been created successfully.");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, int $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:product_categories,id', Rule::notIn([$category->id])],
            'costing_method' => ['required', 'string', Rule::in(['standard', 'average', 'fifo'])],
            'valuation_method' => ['required', 'string', Rule::in(['manual', 'automated'])],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('configuration.categories')
            ->with('success', "Category '{$category->name}' has been updated successfully.");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int $id)
    {
        $category = ProductCategory::findOrFail($id);

        // Disassociate products before deleting
        if (Schema::hasColumn('products', 'category_id')) {
            Product::where('category_id', $category->id)->update(['category_id' => null]);
        }

        // Update child categories to have no parent
        ProductCategory::where('parent_id', $category->id)->update(['parent_id' => null]);

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('configuration.categories')
            ->with('success', "Category '{$categoryName}' deleted successfully.");
    }

    /**
     * Bulk Delete selected categories.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:product_categories,id'],
        ]);

        $ids = $validated['ids'];

        // Disassociate products linked to these categories
        if (Schema::hasColumn('products', 'category_id')) {
            Product::whereIn('category_id', $ids)->update(['category_id' => null]);
        }

        // Unlink children pointing to these parents
        ProductCategory::whereIn('parent_id', $ids)->update(['parent_id' => null]);

        $count = ProductCategory::whereIn('id', $ids)->delete();

        return redirect()->route('configuration.categories')
            ->with('success', "{$count} categories have been deleted successfully.");
    }
}

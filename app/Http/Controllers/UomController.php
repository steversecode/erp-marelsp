<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Uom;
use App\Models\UomCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UomController extends Controller
{
    /**
     * Display a listing of the Units of Measure (UoM Tree / List View).
     */
    public function index(Request $request)
    {
        $query = Uom::with('category')->withCount('products');

        // Quick Search Keyword
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhereHas('category', function ($catSub) use ($q) {
                        $catSub->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Advance Filter: UoM Type
        if ($request->filled('uom_type')) {
            $query->where('uom_type', $request->input('uom_type'));
        }

        // Advance Filter: Active Status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = UomCategory::orderBy('name')->get();
        $uoms = $query->orderBy('category_id')->orderBy('ratio')->paginate(15)->withQueryString();

        $totalCount = Uom::count();
        $categoriesCount = UomCategory::count();

        return view('configuration.uom.index', compact('uoms', 'categories', 'totalCount', 'categoriesCount'));
    }

    /**
     * Show the form for creating a new Unit of Measure.
     */
    public function create()
    {
        $categories = UomCategory::orderBy('name')->get();
        return view('configuration.uom.create', compact('categories'));
    }

    /**
     * Store a newly created Unit of Measure in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:uom_categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:100'],
            'uom_type' => ['required', Rule::in(['reference', 'smaller', 'bigger'])],
            'ratio' => ['required', 'numeric', 'min:0.000001'],
            'rounding' => ['required', 'numeric', 'min:0.000001'],
            'is_active' => ['nullable'],
        ]);

        // Support creating a new category on the fly if requested
        if (empty($validated['category_id']) && !empty($validated['new_category_name'])) {
            $cat = UomCategory::firstOrCreate(['name' => trim($validated['new_category_name'])]);
            $validated['category_id'] = $cat->id;
        }

        if (empty($validated['category_id'])) {
            return back()->withInput()->withErrors(['category_id' => 'Please select or enter a Category.']);
        }

        $validated['is_active'] = $request->has('is_active');

        // If type is reference, ratio is strictly 1.0
        if ($validated['uom_type'] === 'reference') {
            $validated['ratio'] = 1.0;
        }

        $uom = Uom::create($validated);

        return redirect()->route('configuration.uom.show', $uom->id)
            ->with('success', "Unit of Measure '{$uom->name}' created successfully!");
    }

    /**
     * Display the specified Unit of Measure (Odoo Form Sheet).
     */
    public function show(int $id)
    {
        $uom = Uom::with('category')->findOrFail($id);
        
        // Products using this UoM
        $products = Product::where('uom', $uom->name)
            ->orWhere('uom_po', $uom->name)
            ->with('productCategory')
            ->orderBy('name')
            ->limit(20)
            ->get();
            
        $productsCount = Product::where('uom', $uom->name)->orWhere('uom_po', $uom->name)->count();

        // Other units in the same category
        $categoryUnits = Uom::where('category_id', $uom->category_id)
            ->orderBy('ratio')
            ->get();

        return view('configuration.uom.show', compact('uom', 'products', 'productsCount', 'categoryUnits'));
    }

    /**
     * Show the form for editing the specified Unit of Measure.
     */
    public function edit(int $id)
    {
        $uom = Uom::with('category')->findOrFail($id);
        $categories = UomCategory::orderBy('name')->get();

        return view('configuration.uom.edit', compact('uom', 'categories'));
    }

    /**
     * Update the specified Unit of Measure in storage.
     */
    public function update(Request $request, int $id)
    {
        $uom = Uom::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:uom_categories,id'],
            'uom_type' => ['required', Rule::in(['reference', 'smaller', 'bigger'])],
            'ratio' => ['required', 'numeric', 'min:0.000001'],
            'rounding' => ['required', 'numeric', 'min:0.000001'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['uom_type'] === 'reference') {
            $validated['ratio'] = 1.0;
        }

        $uom->update($validated);

        return redirect()->route('configuration.uom.show', $uom->id)
            ->with('success', "Unit of Measure '{$uom->name}' updated successfully!");
    }

    /**
     * Remove the specified Unit of Measure from storage.
     */
    public function destroy(int $id)
    {
        $uom = Uom::findOrFail($id);
        $name = $uom->name;
        $uom->delete();

        return redirect()->route('configuration.uom.index')
            ->with('success', "Unit of Measure '{$name}' deleted successfully.");
    }

    /**
     * Bulk delete selected Units of Measure.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:uoms,id'],
        ]);

        $count = Uom::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('configuration.uom.index')
            ->with('success', "{$count} Units of Measure deleted successfully.");
    }
}

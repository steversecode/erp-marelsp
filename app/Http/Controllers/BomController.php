<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Product;
use App\Models\Uom;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BomController extends Controller
{
    /**
     * Display a listing of Bills of Materials (Odoo Tree/List View).
     */
    public function index(Request $request)
    {
        $query = Bom::with(['product', 'items.product', 'creator', 'updater'])
            ->withCount('items');

        // Search
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhereHas('product', function ($pSub) use ($q) {
                        $pSub->where('name', 'like', "%{$q}%")
                            ->orWhere('code', 'like', "%{$q}%");
                    });
            });
        }

        // Filter: BoM Type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter: Product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        // Filter: Active Status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $boms = $query->latest()->paginate(20)->withQueryString();

        // Statistical KPI metrics
        $totalBoms = Bom::count();
        $normalCount = Bom::where('type', 'normal')->count();
        $kitCount = Bom::where('type', 'phantom')->count();
        $activeCount = Bom::where('is_active', true)->count();

        $allProducts = Product::where('is_active', true)->orderBy('name')->get();

        return view('manufacturing.boms.index', compact(
            'boms',
            'totalBoms',
            'normalCount',
            'kitCount',
            'activeCount',
            'allProducts'
        ));
    }

    /**
     * Show the form for creating a new Bill of Material.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();
        $uoms = Uom::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('manufacturing.boms.create', compact('products', 'rawMaterials', 'uoms', 'users'));
    }

    /**
     * Store a newly created Bill of Material in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'code' => ['nullable', 'string', 'max:50', 'unique:boms,code'],
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['normal', 'phantom'])],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'uom' => ['required', 'string', 'max:20'],
            'ready_to_produce' => ['nullable', 'string', 'max:50'],
            'consumption' => ['nullable', 'string', 'max:50'],
            'produce_delay' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.cones' => ['nullable', 'numeric', 'min:0'],
            'items.*.uom' => ['required', 'string', 'max:20'],
            'items.*.wastage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $bom = DB::transaction(function () use ($request, $validated) {
                $product = Product::findOrFail($validated['product_id']);

                $code = $validated['code'] ?? null;
                if (empty($code)) {
                    $code = 'BOM-' . $product->code . '-' . strtoupper(Str::random(4));
                }

                $name = $validated['name'] ?? null;
                if (empty($name)) {
                    $name = 'BoM ' . $product->name . ' (Basis ' . (float)$validated['quantity'] . ' ' . $validated['uom'] . ')';
                }

                $bom = Bom::create([
                    'code' => $code,
                    'name' => $name,
                    'product_id' => $product->id,
                    'type' => $validated['type'],
                    'quantity' => $validated['quantity'],
                    'uom' => $validated['uom'],
                    'ready_to_produce' => $validated['ready_to_produce'] ?? 'all_available',
                    'consumption' => $validated['consumption'] ?? 'flexible',
                    'produce_delay' => $validated['produce_delay'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                    'is_active' => $request->has('is_active'),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                foreach ($request->input('items', []) as $index => $itemData) {
                    if (empty($itemData['product_id'])) {
                        continue;
                    }

                    BomItem::create([
                        'bom_id' => $bom->id,
                        'sequence' => $index + 1,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'cones' => !empty($itemData['cones']) ? $itemData['cones'] : null,
                        'uom' => $itemData['uom'] ?? 'kg',
                        'wastage_percent' => $itemData['wastage_percent'] ?? 0,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }

                return $bom;
            });

            return redirect()->route('manufacturing.boms.show', $bom->id)
                ->with('success', "Bill of Material [{$bom->code}] {$bom->name} berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat Bill of Material: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Bill of Material (Odoo Master Form View).
     */
    public function show(int $id)
    {
        $bom = Bom::with([
            'product',
            'items.product',
            'creator',
            'updater',
            'manufacturingOrders'
        ])->findOrFail($id);

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();
        $uoms = Uom::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('manufacturing.boms.show', compact('bom', 'products', 'rawMaterials', 'uoms', 'users'));
    }

    /**
     * Show the form for editing the specified Bill of Material.
     */
    public function edit(int $id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified Bill of Material in storage.
     */
    public function update(Request $request, int $id)
    {
        $bom = Bom::findOrFail($id);

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('boms', 'code')->ignore($bom->id)],
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['normal', 'phantom'])],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'uom' => ['required', 'string', 'max:20'],
            'ready_to_produce' => ['nullable', 'string', 'max:50'],
            'consumption' => ['nullable', 'string', 'max:50'],
            'produce_delay' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.cones' => ['nullable', 'numeric', 'min:0'],
            'items.*.uom' => ['required', 'string', 'max:20'],
            'items.*.wastage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($bom, $request, $validated) {
                $code = $validated['code'] ?? $bom->code;
                $name = $validated['name'] ?? null;
                if (empty($name)) {
                    $product = Product::find($validated['product_id']);
                    $name = $bom->name ?? ('BoM ' . ($product ? $product->name : '') . ' (Basis ' . (float)$validated['quantity'] . ' ' . $validated['uom'] . ')');
                }

                $bom->update([
                    'product_id' => $validated['product_id'],
                    'code' => $code,
                    'name' => $name,
                    'type' => $validated['type'],
                    'quantity' => $validated['quantity'],
                    'uom' => $validated['uom'],
                    'ready_to_produce' => $validated['ready_to_produce'] ?? 'all_available',
                    'consumption' => $validated['consumption'] ?? 'flexible',
                    'produce_delay' => $validated['produce_delay'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                    'is_active' => $request->has('is_active'),
                    'updated_by' => auth()->id(),
                ]);

                // Sync Components: delete existing and re-insert
                $bom->items()->delete();

                foreach ($request->input('items', []) as $index => $itemData) {
                    if (empty($itemData['product_id'])) {
                        continue;
                    }

                    BomItem::create([
                        'bom_id' => $bom->id,
                        'sequence' => $index + 1,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'cones' => !empty($itemData['cones']) ? $itemData['cones'] : null,
                        'uom' => $itemData['uom'] ?? 'kg',
                        'wastage_percent' => $itemData['wastage_percent'] ?? 0,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            });

            return redirect()->route('manufacturing.boms.show', $bom->id)
                ->with('success', "Bill of Material [{$bom->code}] {$bom->name} berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui Bill of Material: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Bill of Material from storage.
     */
    public function destroy(int $id)
    {
        $bom = Bom::withCount('manufacturingOrders')->findOrFail($id);

        if ($bom->manufacturing_orders_count > 0) {
            return back()->with('error', "BoM [{$bom->code}] tidak dapat dihapus karena sudah digunakan oleh {$bom->manufacturing_orders_count} Manufacturing Order.");
        }

        $code = $bom->code;
        $bom->items()->delete();
        $bom->delete();

        return redirect()->route('manufacturing.boms.index')
            ->with('success', "Bill of Material [{$code}] berhasil dihapus!");
    }

    /**
     * Bulk Delete Bills of Materials.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:boms,id',
        ]);

        $ids = $request->input('ids');
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $bom = Bom::withCount('manufacturingOrders')->find($id);
            if ($bom) {
                if ($bom->manufacturing_orders_count > 0) {
                    $skippedCount++;
                } else {
                    $bom->items()->delete();
                    $bom->delete();
                    $deletedCount++;
                }
            }
        }

        $msg = "{$deletedCount} Bill of Material berhasil dihapus.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} BoM dilewati karena terhubung dengan Manufacturing Order).";
        }

        return redirect()->route('manufacturing.boms.index')->with('success', $msg);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\ManufacturingOrder;
use App\Models\MoComponent;
use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMove;
use App\Services\InventoryService;
use App\Services\ManufacturingService;
use Exception;
use Illuminate\Http\Request;

class ManufacturingOrderController extends Controller
{
    protected ManufacturingService $manufacturingService;
    protected InventoryService $inventoryService;

    public function __construct(ManufacturingService $manufacturingService, InventoryService $inventoryService)
    {
        $this->manufacturingService = $manufacturingService;
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = ManufacturingOrder::with([
            'product',
            'bom',
            'components.actualProduct',
            'components.originalProduct',
            'sourceLocation',
            'creator'
        ]);

        $search = $request->input('search');
        $filter = $request->input('filter', $request->input('status', 'todo'));
        $groupBy = $request->input('group_by');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mo_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Apply Odoo MRP Filters
        if ($filter && $filter !== 'all') {
            switch ($filter) {
                case 'todo':
                    $query->whereIn('status', ['draft', 'confirmed', 'in_progress']);
                    break;
                case 'unbuilt':
                case 'draft':
                    $query->where('status', 'draft');
                    break;
                case 'confirmed':
                case 'planned':
                    $query->where('status', 'confirmed');
                    break;
                case 'in_progress':
                    $query->where('status', 'in_progress');
                    break;
                case 'to_close':
                    $query->where('status', 'in_progress');
                    break;
                case 'done':
                    $query->where('status', 'done');
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelled');
                    break;
                case 'late':
                    $query->where('start_date', '<', now()->startOfDay())
                          ->whereNotIn('status', ['done', 'cancelled']);
                    break;
                default:
                    if (in_array($filter, ['draft', 'confirmed', 'in_progress', 'done', 'cancelled'])) {
                        $query->where('status', $filter);
                    }
                    break;
            }
        }

        if ($groupBy === 'product') {
            $query->orderBy('product_id');
        } elseif ($groupBy === 'status') {
            $query->orderBy('status');
        } elseif ($groupBy === 'date') {
            $query->orderBy('start_date', 'desc');
        } else {
            $query->latest();
        }

        $orders = $query->paginate(40)->withQueryString();

        // Calculate component readiness for each order
        $orders->getCollection()->transform(function($mo) {
            $sourceLocId = $mo->source_location_id ?? StockLocation::mainLocationId();
            $allAvailable = true;
            foreach ($mo->components as $comp) {
                $stock = $this->inventoryService->getCurrentStock($comp->actual_product_id, $sourceLocId);
                if ($stock < (float)$comp->planned_qty) {
                    $allAvailable = false;
                    break;
                }
            }
            $mo->all_components_available = $allAvailable;
            return $mo;
        });

        // Filter by material readiness if requested
        if ($filter === 'mo_ready') {
            $filtered = $orders->getCollection()->filter(fn($mo) => $mo->all_components_available);
            $orders->setCollection($filtered);
        } elseif ($filter === 'mo_pending') {
            $filtered = $orders->getCollection()->filter(fn($mo) => !$mo->all_components_available);
            $orders->setCollection($filtered);
        }

        return view('manufacturing.index', compact('orders', 'search', 'filter', 'groupBy'));
    }

    public function create()
    {
        $boms = Bom::with(['product', 'items.product.substitutes'])->where('is_active', true)->get();
        $sourceLocations = StockLocation::where('type', 'internal')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();

        // Siapkan info stok real-time untuk setiap komponen BoM
        $bomDetails = $boms->map(function ($bom) {
            $components = $bom->items->map(function ($item) {
                $availability = $this->inventoryService->checkAvailability($item->product_id, (float) $item->quantity);
                return [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_code' => $item->product->code,
                    'product_name' => $item->product->name,
                    'base_qty' => (float) $item->quantity,
                    'wastage_percent' => (float) ($item->wastage_percent ?? 0),
                    'uom' => $item->uom,
                    'available_stock' => $availability['available_stock'],
                    'is_sufficient' => $availability['is_sufficient'],
                    'has_substitutes' => $availability['has_substitutes'],
                    'substitute_options' => $availability['substitute_options'],
                ];
            });

            return [
                'id' => $bom->id,
                'code' => $bom->code,
                'name' => $bom->name,
                'product_name' => $bom->product->name,
                'base_quantity' => (float) $bom->quantity,
                'uom' => $bom->uom,
                'components' => $components,
            ];
        });

        return view('manufacturing.create', compact('boms', 'bomDetails', 'sourceLocations', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bom_id' => 'required|exists:boms,id',
            'planned_qty' => 'required|numeric|min:0.01',
            'start_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'components' => 'nullable|array',
            'components.*.product_id' => 'required_with:components|exists:products,id',
            'components.*.planned_qty' => 'required_with:components|numeric|min:0.0001',
        ]);

        try {
            $mo = $this->manufacturingService->createManufacturingOrder([
                'bom_id' => $request->bom_id,
                'planned_qty' => $request->planned_qty,
                'start_date' => $request->start_date ?? date('Y-m-d'),
                'notes' => $request->notes,
                'components' => $request->input('components'),
                'source_location_id' => $request->input('source_location_id'),
                'created_by' => auth()->id() ?? 1,
            ]);

            return redirect()->route('manufacturing.show', $mo->id)
                ->with('success', "Manufacturing Order {$mo->mo_number} berhasil dibuat! Periksa ketersediaan komponen atau lakukan Component Switching jika stok kosong.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat MO: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $mo = ManufacturingOrder::with([
            'product',
            'bom.items.product',
            'components.originalProduct',
            'components.actualProduct.substitutes',
            'components.switchedBy',
            'sourceLocation',
            'destinationLocation',
            'creator'
        ])->findOrFail($id);

        $sourceLocId = $mo->source_location_id ?? StockLocation::mainLocationId();

        // Check availability of each component's actual product
        $componentStatuses = $mo->components->map(function ($comp) use ($sourceLocId) {
            $stock = $this->inventoryService->getCurrentStock($comp->actual_product_id, $sourceLocId);
            $required = (float) $comp->planned_qty;
            $availability = $this->inventoryService->checkAvailability($comp->original_product_id, $required, $sourceLocId);

            return [
                'component' => $comp,
                'current_stock' => $stock,
                'is_sufficient' => $stock >= $required,
                'substitute_options' => $availability['substitute_options'],
            ];
        });

        $allComponentsAvailable = $componentStatuses->every(fn($c) => $c['is_sufficient']);

        $productMovesCount = StockMove::where('reference_type', 'ManufacturingOrder')
            ->where('reference_id', $mo->id)
            ->count();

        $moMoves = StockMove::with(['product', 'fromLocation', 'toLocation', 'creator'])
            ->where('reference_type', 'ManufacturingOrder')
            ->where('reference_id', $mo->id)
            ->latest()
            ->get();

        $allMosCount = ManufacturingOrder::count();
        $currentMoPosition = ManufacturingOrder::where('id', '<=', $mo->id)->count();

        $allProducts = Product::where('is_active', true)->orderBy('name')->get();

        return view('manufacturing.show', compact(
            'mo',
            'componentStatuses',
            'allComponentsAvailable',
            'productMovesCount',
            'moMoves',
            'allMosCount',
            'currentMoPosition',
            'allProducts'
        ));
    }

    public function edit(int $id)
    {
        $mo = ManufacturingOrder::with([
            'product',
            'bom.items.product',
            'components.originalProduct',
            'components.actualProduct',
            'sourceLocation',
            'destinationLocation',
            'creator'
        ])->findOrFail($id);

        $boms = Bom::with(['product', 'items.product'])->where('is_active', true)->get();
        $sourceLocations = StockLocation::where('type', 'internal')->get();
        $rawMaterials = Product::where('is_active', true)->orderBy('name')->get();

        return view('manufacturing.edit', compact('mo', 'boms', 'sourceLocations', 'rawMaterials'));
    }

    public function update(Request $request, int $id)
    {
        $mo = ManufacturingOrder::findOrFail($id);

        $request->validate([
            'planned_qty' => 'required|numeric|min:0.01',
            'start_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'components' => 'nullable|array',
        ]);

        $mo->update([
            'planned_qty' => $request->planned_qty,
            'start_date' => $request->start_date,
            'notes' => $request->notes,
        ]);

        // Sync components if provided
        if ($request->has('components') && is_array($request->components)) {
            $existingIds = [];
            foreach ($request->components as $compData) {
                if (empty($compData['product_id'])) continue;
                $compId = $compData['id'] ?? null;
                $qty = (float) ($compData['planned_qty'] ?? 1);
                $uom = $compData['uom'] ?? 'kg';

                if ($compId && $existingComp = MoComponent::where('manufacturing_order_id', $mo->id)->find($compId)) {
                    $existingComp->update([
                        'actual_product_id' => $compData['product_id'],
                        'planned_qty' => $qty,
                        'uom' => $uom,
                    ]);
                    $existingIds[] = $existingComp->id;
                } else {
                    $newComp = MoComponent::create([
                        'manufacturing_order_id' => $mo->id,
                        'original_product_id' => $compData['product_id'],
                        'actual_product_id' => $compData['product_id'],
                        'is_switched' => false,
                        'planned_qty' => $qty,
                        'issued_qty' => 0,
                        'returned_qty' => 0,
                        'uom' => $uom,
                    ]);
                    $existingIds[] = $newComp->id;
                }
            }

            // Remove deleted components that haven't been issued yet
            MoComponent::where('manufacturing_order_id', $mo->id)
                ->whereNotIn('id', $existingIds)
                ->where('issued_qty', '<=', 0)
                ->delete();
        }

        return redirect()->route('manufacturing.show', $mo->id)
            ->with('success', "Manufacturing Order {$mo->mo_number} berhasil diperbarui!");
    }

    public function addComponent(Request $request, int $moId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'planned_qty' => 'required|numeric|min:0.0001',
            'uom' => 'nullable|string',
        ]);

        try {
            $this->manufacturingService->addComponentLine($moId, [
                'product_id' => $request->product_id,
                'planned_qty' => $request->planned_qty,
                'uom' => $request->uom,
            ]);
            return back()->with('success', 'Komponen baru berhasil ditambahkan ke MO ini!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menambahkan komponen: ' . $e->getMessage());
        }
    }

    public function destroyComponent(int $moId, int $componentId)
    {
        try {
            $this->manufacturingService->removeComponentLine($moId, $componentId);
            return back()->with('success', 'Baris komponen berhasil dihapus dari MO!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus komponen: ' . $e->getMessage());
        }
    }

    public function switchComponent(Request $request, int $moId, int $componentId)
    {
        $request->validate([
            'substitute_product_id' => 'required|exists:products,id',
            'switch_reason' => 'required|string|max:255',
            'custom_qty' => 'nullable|numeric|min:0.001',
        ]);

        try {
            $this->manufacturingService->switchComponent(
                $componentId,
                (int) $request->substitute_product_id,
                $request->switch_reason,
                $request->filled('custom_qty') ? (float) $request->custom_qty : null,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Component switching berhasil diterapkan pada MO ini tanpa mengubah Master BoM!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengganti komponen: ' . $e->getMessage());
        }
    }

    public function release(int $moId)
    {
        try {
            $this->manufacturingService->releaseToProduction($moId, auth()->id() ?? 1);
            return back()->with('success', 'Material berhasil dikeluarkan dari gudang dan MO dirilis ke Lantai Produksi!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal merilis MO: ' . $e->getMessage());
        }
    }

    public function returnLeftover(Request $request, int $moId)
    {
        $request->validate([
            'mo_component_id' => 'required|exists:mo_components,id',
            'returned_qty' => 'required|numeric|min:0.0001',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->manufacturingService->returnLeftoverMaterial(
                $moId,
                (int) $request->mo_component_id,
                (float) $request->returned_qty,
                $request->notes,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Sisa material berhasil dikembalikan ke Gudang Utama dan tercatat di buku besar mutasi!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal retur sisa produksi: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, int $moId)
    {
        $request->validate([
            'produced_qty' => 'required|numeric|min:0.01',
        ]);

        try {
            $this->manufacturingService->completeManufacturingOrder(
                $moId,
                (float) $request->produced_qty,
                auth()->id() ?? 1
            );

            return back()->with('success', 'Manufacturing Order selesai! Barang jadi telah masuk ke Gudang Finished Goods.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan MO: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMove;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $productsSummary = $this->inventoryService->getInventorySummary();
        $totalValuation = $productsSummary->sum('total_valuation');
        $lowStockCount = $productsSummary->where('is_low_stock', true)->count();
        $totalProducts = $productsSummary->count();

        $activeMoCount = ManufacturingOrder::whereIn('status', ['draft', 'confirmed', 'in_progress'])->count();
        $recentMoves = StockMove::with(['product', 'fromLocation', 'toLocation'])->latest()->take(8)->get();
        $switchedMoCount = ManufacturingOrder::whereHas('components', function ($q) {
            $q->where('is_switched', true);
        })->count();

        return view('dashboard', compact(
            'productsSummary',
            'totalValuation',
            'lowStockCount',
            'totalProducts',
            'activeMoCount',
            'recentMoves',
            'switchedMoCount'
        ));
    }
}

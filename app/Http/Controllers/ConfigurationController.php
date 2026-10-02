<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLocation;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    /**
     * Display Inventory Settings.
     */
    public function settings()
    {
        return view('configuration.settings');
    }

    /**
     * Display Warehouses & Locations list.
     */
    public function locations()
    {
        $locations = StockLocation::withCount(['incomingStockMoves', 'outgoingStockMoves'])->get();
        return view('configuration.locations', compact('locations'));
    }

    /**
     * Display Operation Types.
     */
    public function operationTypes()
    {
        $operationTypes = [
            [
                'name' => 'Receipts (PO Inbound)',
                'code' => 'WH/IN',
                'type' => 'incoming',
                'default_source' => 'Vendor / Suppliers',
                'default_dest' => 'WH-MAIN (Main Warehouse)',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ],
            [
                'name' => 'Internal Transfers',
                'code' => 'WH/INT',
                'type' => 'internal',
                'default_source' => 'WH-MAIN (Main Warehouse)',
                'default_dest' => 'WH-FG / Storage Aisle',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
            ],
            [
                'name' => 'Manufacturing / Consumption',
                'code' => 'WH/MO',
                'type' => 'mrp',
                'default_source' => 'WH-MAIN (Raw Materials)',
                'default_dest' => 'PROD-FLOOR (WIP Production)',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            ],
            [
                'name' => 'Subcontracting (Dyeing)',
                'code' => 'WH/SUBCON',
                'type' => 'subcontracting',
                'default_source' => 'WH-MAIN (Greige Fabric)',
                'default_dest' => 'SUBCON-DYEING (Vendor Celup)',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
            ],
            [
                'name' => 'Delivery Orders / Samples',
                'code' => 'WH/OUT',
                'type' => 'outgoing',
                'default_source' => 'WH-MAIN / WH-FG',
                'default_dest' => 'VIRTUAL-SAMPLE / Customers',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
            ],
        ];

        return view('configuration.operation-types', compact('operationTypes'));
    }

    /**
     * Display Product Categories.
     */
    public function categories()
    {
        $categories = Product::select('category')
            ->whereNotNull('category')
            ->groupBy('category')
            ->selectRaw('count(*) as total_products')
            ->get();

        return view('configuration.categories', compact('categories'));
    }

    /**
     * Display Units of Measure (UoM).
     */
    public function uom()
    {
        $uomList = [
            ['name' => 'kg (Kilogram)', 'category' => 'Weight', 'type' => 'Reference Unit', 'ratio' => '1.000'],
            ['name' => 'gram', 'category' => 'Weight', 'type' => 'Smaller than reference', 'ratio' => '0.001'],
            ['name' => 'cone', 'category' => 'Unit / Piece', 'type' => 'Reference Unit', 'ratio' => '1.000'],
            ['name' => 'roll', 'category' => 'Unit / Piece', 'type' => 'Reference Unit', 'ratio' => '1.000'],
            ['name' => 'pcs (Pieces)', 'category' => 'Unit / Piece', 'type' => 'Reference Unit', 'ratio' => '1.000'],
            ['name' => 'pack', 'category' => 'Unit / Piece', 'type' => 'Bigger than reference', 'ratio' => '3.000'],
            ['name' => 'meter', 'category' => 'Length / Distance', 'type' => 'Reference Unit', 'ratio' => '1.000'],
            ['name' => 'yard', 'category' => 'Length / Distance', 'type' => 'Smaller than reference', 'ratio' => '0.914'],
        ];

        return view('configuration.uom', compact('uomList'));
    }
}

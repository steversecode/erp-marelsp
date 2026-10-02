@extends('layouts.app')

@section('title', "Location: {$location->name} [{$location->code}] - Configuration")

@section('content')
<div class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Form Header) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('configuration.locations.edit', $location->id) }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                <span>Edit</span>
            </a>
            <a href="{{ route('configuration.locations.create') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-semibold flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>

            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('configuration.locations.index') }}" class="hover:text-[#001849] font-semibold">Locations</a>
                    <span>/</span>
                    <span class="text-[#001849] font-bold font-mono">{{ $location->code }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('configuration.locations.destroy', $location->id) }}" onsubmit="return confirm('Delete or archive location [{{ $location->code }}]?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Delete</span>
                </button>
            </form>
            <a href="{{ route('configuration.locations.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#757681] hover:text-[#001849] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to Locations
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Odoo Form Sheet Container -->
    <div class="bg-white rounded-2xl border border-[#e5eeff] shadow-sm overflow-hidden">
        
        <!-- Smart / Stat Buttons Toolbar (Odoo Style) -->
        <div class="border-b border-[#eff4ff] bg-[#f8f9ff]/50 p-2 sm:px-6 flex items-center justify-end gap-2 flex-wrap">
            <!-- Stat Button 1: Stock Items Stored -->
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-[#dce9ff] flex items-center gap-2.5 text-xs shadow-xs">
                <span class="material-symbols-outlined text-[#fb7800] text-[20px]">inventory_2</span>
                <div class="text-left">
                    <span class="text-[10px] text-[#757681] uppercase font-bold block leading-none">Stored Products</span>
                    <span class="font-bold text-[#001849] font-mono text-xs">{{ count($storedProducts) }} SKUs ({{ number_format($totalStockUnits, 2) }})</span>
                </div>
            </div>

            <!-- Stat Button 2: Stock Moves History -->
            <a href="{{ route('inventory.moves', ['location_id' => $location->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-white border border-[#dce9ff] hover:border-[#001849] hover:bg-[#eff4ff] transition-all flex items-center gap-2.5 text-xs shadow-xs group">
                <span class="material-symbols-outlined text-[#0d2c6c] text-[20px]">history</span>
                <div class="text-left">
                    <span class="text-[10px] text-[#757681] uppercase font-bold block leading-none">Stock Moves</span>
                    <span class="font-bold text-[#001849] font-mono text-xs">{{ $totalMovesCount }} Moves</span>
                </div>
            </a>
        </div>

        <div class="p-6 sm:p-8 space-y-8">
            <!-- Header Title -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#eff4ff] pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[11px] uppercase tracking-wider text-[#fb7800] font-bold">Complete Location Path:</span>
                        <span class="text-xs font-bold text-[#001849] font-mono">{{ $location->complete_name }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#001849] flex items-center gap-3 flex-wrap">
                        <span>{{ $location->name }}</span>
                        <span class="font-mono text-base font-bold text-[#0d2c6c] bg-[#eff4ff] px-2.5 py-0.5 rounded-lg border border-[#dce9ff]">
                            [{{ $location->code }}]
                        </span>
                        @if($location->is_active)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Archived</span>
                        @endif
                        @if($location->is_scrap)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Scrap</span>
                        @endif
                        @if($location->is_return)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Return</span>
                        @endif
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#757681]">Type:</span>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                        {{ $location->type_label }}
                    </span>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-[#757681] uppercase tracking-wider">Location Hierarchy &amp; Warehouse</h3>

                    <div class="bg-[#f8f9ff] p-4 rounded-xl border border-[#e5eeff] space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Hierarchical Path</span>
                            <span class="font-bold text-[#001849]">{{ $location->complete_name }}</span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Parent Location</span>
                            <span class="font-bold text-[#001849]">
                                @if($location->parent)
                                    <a href="{{ route('configuration.locations.show', $location->parent_id) }}" class="text-[#0d2c6c] hover:underline">
                                        {{ $location->parent->complete_name }}
                                    </a>
                                @else
                                    Root Top Hierarchy
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Warehouse</span>
                            <span class="font-bold text-[#001849]">
                                @if($location->warehouse)
                                    <a href="{{ route('configuration.warehouses.show', $location->warehouse_id) }}" class="text-[#0d2c6c] hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px] text-[#fb7800]">warehouse</span>
                                        <span>{{ $location->warehouse->name }} ({{ $location->warehouse->code }})</span>
                                    </a>
                                @else
                                    <span class="text-[#757681] italic">Virtual / Generic Location</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Barcode Reference</span>
                            <span class="font-mono font-bold text-[#001849]">{{ $location->barcode ?: '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Location Type</span>
                            <span class="font-bold text-[#001849]">{{ $location->type_label }}</span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Scrap &amp; Return</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold {{ $location->is_scrap ? 'text-rose-700' : 'text-[#757681]' }}">
                                    {{ $location->is_scrap ? 'Scrap: Yes' : 'Scrap: No' }}
                                </span>
                                <span>•</span>
                                <span class="font-bold {{ $location->is_return ? 'text-blue-700' : 'text-[#757681]' }}">
                                    {{ $location->is_return ? 'Return: Yes' : 'Return: No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Address & Valuation -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-[#757681] uppercase tracking-wider">Logistics, Address &amp; Valuation</h3>

                    <div class="bg-[#eff4ff]/50 p-4 rounded-xl border border-[#dce9ff] space-y-3 text-xs">
                        <div>
                            <span class="text-[#757681] block mb-1 font-semibold">Building Zone / Rack / Address</span>
                            <p class="font-medium text-[#001849] leading-relaxed bg-white p-3 rounded-lg border border-[#dce9ff]">
                                {{ $location->address ?: 'No specific building zone provided.' }}
                            </p>
                        </div>

                        @if($location->comment)
                        <div>
                            <span class="text-[#757681] block mb-1 font-semibold">Internal Notes</span>
                            <p class="font-medium text-[#001849] leading-relaxed bg-white p-3 rounded-lg border border-[#dce9ff]">
                                {{ $location->comment }}
                            </p>
                        </div>
                        @endif

                        <div class="pt-2 flex items-center justify-between border-t border-[#dce9ff]">
                            <span class="text-[#757681]">Total Stock Valuation in Location:</span>
                            <span class="font-bold font-mono text-sm text-[#001849]">
                                Rp {{ number_format($totalValuation, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Sub-Locations (if any) -->
            @if($location->children->count() > 0)
            <div class="space-y-3 pt-4 border-t border-[#eff4ff]">
                <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#fb7800] text-[18px]">account_tree</span>
                    <span>Sub-Locations Under This Location ({{ $location->children->count() }})</span>
                </h3>

                <div class="bg-white rounded-xl border border-[#e5eeff] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase border-b border-[#e5eeff]">
                            <tr>
                                <th class="py-2.5 px-4">Code</th>
                                <th class="py-2.5 px-4">Sub-Location Name</th>
                                <th class="py-2.5 px-4">Complete Path</th>
                                <th class="py-2.5 px-4">Type</th>
                                <th class="py-2.5 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eff4ff]">
                            @foreach($location->children as $child)
                            <tr class="hover:bg-[#f8f9ff] transition-colors">
                                <td class="py-2.5 px-4 font-mono font-bold text-[#0d2c6c]">
                                    {{ $child->code }}
                                </td>
                                <td class="py-2.5 px-4 font-semibold text-[#001849]">
                                    {{ $child->name }}
                                </td>
                                <td class="py-2.5 px-4 font-mono text-[11px] text-[#757681]">
                                    {{ $child->complete_name }}
                                </td>
                                <td class="py-2.5 px-4 text-[#757681]">
                                    {{ $child->type_label }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <a href="{{ route('configuration.locations.show', $child->id) }}" class="text-[#0d2c6c] hover:underline font-semibold text-xs">
                                        Open Location →
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Products Stored in this Location -->
            <div class="space-y-3 pt-4 border-t border-[#eff4ff]">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#fb7800] text-[18px]">inventory_2</span>
                        <span>Current Physical Stock in this Location ({{ count($storedProducts) }} SKUs)</span>
                    </h3>
                </div>

                @if(count($storedProducts) > 0)
                <div class="bg-white rounded-xl border border-[#e5eeff] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase border-b border-[#e5eeff]">
                            <tr>
                                <th class="py-2.5 px-4">Code</th>
                                <th class="py-2.5 px-4">Product Name</th>
                                <th class="py-2.5 px-4">Category</th>
                                <th class="py-2.5 px-4 text-right">On Hand Qty</th>
                                <th class="py-2.5 px-4 text-right">Valuation</th>
                                <th class="py-2.5 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eff4ff]">
                            @foreach($storedProducts as $item)
                            <tr class="hover:bg-[#f8f9ff] transition-colors">
                                <td class="py-2.5 px-4 font-mono font-bold text-[#0d2c6c]">
                                    <a href="{{ route('products.show', $item['product']->id) }}" class="hover:underline">
                                        [{{ $item['product']->code }}]
                                    </a>
                                </td>
                                <td class="py-2.5 px-4 font-medium text-[#0b1c30]">
                                    <a href="{{ route('products.show', $item['product']->id) }}" class="hover:underline">
                                        {{ $item['product']->name }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-4 text-[#757681]">
                                    {{ $item['product']->productCategory?->name ?? $item['product']->category ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#001849]">
                                    {{ number_format($item['on_hand'], 2) }} {{ $item['product']->uom }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#001849]">
                                    Rp {{ number_format($item['valuation'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <a href="{{ route('products.on-hand', $item['product']->id) }}" class="text-[#0d2c6c] hover:underline font-semibold text-xs">
                                        Update Qty
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-6 rounded-xl bg-[#f8f9ff] border border-[#e5eeff] text-center text-xs text-[#757681]">
                    No stock currently recorded in this location.
                </div>
                @endif
            </div>

            <!-- Recent Moves in/out of this location -->
            <div class="space-y-3 pt-4 border-t border-[#eff4ff]">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#0d2c6c] text-[18px]">history</span>
                        <span>Recent Stock Moves ({{ $recentMoves->count() }})</span>
                    </h3>
                    <a href="{{ route('inventory.moves', ['location_id' => $location->id]) }}" class="text-xs text-[#0d2c6c] hover:underline font-semibold">
                        View All Moves →
                    </a>
                </div>

                @if($recentMoves->count() > 0)
                <div class="bg-white rounded-xl border border-[#e5eeff] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase border-b border-[#e5eeff]">
                            <tr>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Move Ref</th>
                                <th class="py-2.5 px-4">Product</th>
                                <th class="py-2.5 px-4">From</th>
                                <th class="py-2.5 px-4">To</th>
                                <th class="py-2.5 px-4 text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eff4ff]">
                            @foreach($recentMoves as $move)
                            <tr class="hover:bg-[#f8f9ff] transition-colors">
                                <td class="py-2.5 px-4 text-[#757681] font-mono text-[11px]">
                                    {{ $move->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2.5 px-4 font-mono font-bold text-[#0d2c6c]">
                                    {{ $move->move_number }}
                                </td>
                                <td class="py-2.5 px-4 font-medium text-[#0b1c30]">
                                    {{ $move->product?->name ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-[#757681]">
                                    {{ $move->fromLocation?->name ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-[#757681]">
                                    {{ $move->toLocation?->name ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#001849]">
                                    {{ number_format($move->qty, 2) }} {{ $move->uom }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

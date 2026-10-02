@extends('layouts.app')

@section('title', '[' . $product->code . '] ' . $product->name)

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Top Action & Breadcrumb Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('inventory.index') }}" class="text-[#757681] hover:text-[#001849] font-medium flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Products &amp; SKU</span>
            </a>
            <span class="text-[#757681]">/</span>
            <span class="font-bold text-[#001849] font-mono">{{ $product->code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to List
            </a>
            <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                <span>Edit Product</span>
            </a>
        </div>
    </div>

    <!-- Main Odoo Product Form Container -->
    <div class="bg-white rounded-2xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <!-- Odoo Smart / Stat Buttons Toolbar -->
        <div class="bg-[#f8f9ff] border-b border-[#e5eeff] p-3 flex flex-wrap items-center justify-end gap-2">
            <!-- Stat 1: On Hand Stock (Clickable to On Hand Breakdown & Adjustment) -->
            <a href="{{ route('products.on-hand', $product->id) }}" class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-[#eff4ff] border border-[#dce9ff] hover:border-[#0d2c6c] rounded-lg shadow-2xs transition-all cursor-pointer group" title="View location breakdown & update quantity">
                <div class="w-8 h-8 rounded-lg bg-[#dae1ff] text-[#001849] group-hover:bg-[#001849] group-hover:text-white flex items-center justify-center shrink-0 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">inventory</span>
                </div>
                <div class="text-left">
                    <span class="text-[10px] text-[#757681] font-semibold block uppercase group-hover:text-[#001849]">On Hand</span>
                    <span class="text-xs font-bold font-mono text-[#001849]">{{ number_format($currentStock, 2) }} {{ $product->uom }}</span>
                </div>
            </a>

            <!-- Stat 2: Product Moves Ledger Count -->
            <a href="{{ route('inventory.moves', ['product_id' => $product->id]) }}" class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-[#eff4ff] border border-[#dce9ff] rounded-lg shadow-2xs transition-colors">
                <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                </div>
                <div class="text-left">
                    <span class="text-[10px] text-[#757681] font-semibold block uppercase">Product Moves</span>
                    <span class="text-xs font-bold font-mono text-teal-800">{{ $totalMovesCount }} Moves</span>
                </div>
            </a>

            <!-- Stat 4: Bill of Materials (BoM) if manufactured -->
            @if($product->boms->count() > 0)
                <a href="{{ route('manufacturing.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-[#eff4ff] border border-[#dce9ff] rounded-lg shadow-2xs transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">schema</span>
                    </div>
                    <div class="text-left">
                        <span class="text-[10px] text-[#757681] font-semibold block uppercase">Bill of Materials</span>
                        <span class="text-xs font-bold font-mono text-indigo-800">{{ $product->boms->count() }} BoM Active</span>
                    </div>
                </a>
            @endif
        </div>

        <!-- Product Header Information -->
        <div class="p-6 space-y-6">
            <div class="flex flex-col-reverse md:flex-row md:items-start justify-between gap-6 pb-6 border-b border-[#eff4ff]">
                <!-- Details -->
                <div class="space-y-3 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-[#001849] text-white">
                            {{ $product->code }}
                        </span>
                        @if($product->barcode)
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">barcode</span>
                                {{ $product->barcode }}
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                            {{ $product->type_label }}
                        </span>
                        @if($product->category_name !== '-')
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-[#f8f9ff] text-[#757681] border border-[#e5eeff]">
                                {{ $product->category_name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold font-display text-[#001849]">
                        {{ $product->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-[#757681]">
                        <span class="flex items-center gap-1 {{ $product->can_be_sold ? 'text-emerald-700' : 'text-gray-400 line-through' }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $product->can_be_sold ? 'check_circle' : 'cancel' }}</span>
                            Can be Sold
                        </span>
                        <span class="flex items-center gap-1 {{ $product->can_be_purchased ? 'text-emerald-700' : 'text-gray-400 line-through' }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $product->can_be_purchased ? 'check_circle' : 'cancel' }}</span>
                            Can be Purchased
                        </span>
                        <span class="flex items-center gap-1 {{ $product->can_be_manufactured ? 'text-indigo-700' : 'text-gray-400 line-through' }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $product->can_be_manufactured ? 'check_circle' : 'cancel' }}</span>
                            Can be Manufactured
                        </span>
                        <span class="flex items-center gap-1 {{ $product->can_be_subcontracted ? 'text-purple-700' : 'text-gray-400 line-through' }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $product->can_be_subcontracted ? 'check_circle' : 'cancel' }}</span>
                            Can be Subcontracted
                        </span>
                    </div>
                </div>

                <!-- Product Image & Pricing Badge -->
                <div class="flex items-center md:items-start gap-4 shrink-0">
                    <div class="text-right space-y-1">
                        <span class="text-[10px] uppercase font-bold text-[#757681] block">Cost Price (HPP)</span>
                        <span class="text-xl font-bold font-mono text-[#001849] block">
                            Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            <span class="text-xs font-normal text-[#757681]">/ {{ $product->uom }}</span>
                        </span>
                        @if($product->sale_price > 0)
                            <span class="text-xs font-mono text-[#757681] block">
                                Sales Price: Rp {{ number_format($product->sale_price, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Image Avatar -->
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-[#eff4ff] border border-[#dce9ff] flex items-center justify-center overflow-hidden shadow-2xs shrink-0">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-[#757681]/60">
                                <span class="material-symbols-outlined text-[36px]">image</span>
                                <span class="text-[9px] font-semibold mt-0.5">No Image</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tabbed Detail Display -->
            <div x-data="{ activeTab: 'general' }" class="space-y-6">
                <div class="flex items-center gap-2 border-b border-[#e5eeff] overflow-x-auto pb-px">
                    <button type="button" @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span>General Information</span>
                    </button>
                    <button type="button" @click="activeTab = 'inventory'" :class="activeTab === 'inventory' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">warehouse</span>
                        <span>Inventory</span>
                    </button>
                    <button type="button" @click="activeTab = 'substitutes'" :class="activeTab === 'substitutes' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">shuffle</span>
                        <span>Substitutes ({{ $product->substitutes->count() }})</span>
                    </button>
                    <button type="button" @click="activeTab = 'moves'" :class="activeTab === 'moves' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                        <span>Recent Moves</span>
                    </button>
                </div>

                <!-- Tab 1: General Info -->
                <div x-show="activeTab === 'general'" class="space-y-4 text-xs animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#f8f9ff] p-5 rounded-xl border border-[#e5eeff]">
                        <div class="space-y-3">
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Product Type:</span>
                                <span class="font-bold text-[#001849]">{{ $product->type_label }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Invoicing Policy:</span>
                                <span class="font-bold text-[#001849]">{{ $product->invoicing_policy_label }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Product Category:</span>
                                <span class="font-bold text-[#001849]">{{ $product->category_name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff] items-center">
                                <span class="text-[#757681]">Responsible:</span>
                                <span class="inline-flex items-center gap-1.5 font-bold text-[#001849]">
                                    <span class="material-symbols-outlined text-[15px] text-[#fb7800]">person</span>
                                    <span>{{ $product->responsible?->name ?? 'Unassigned' }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Unit of Measure (UoM):</span>
                                <span class="font-bold font-mono text-[#001849]">{{ $product->uom }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Tracking:</span>
                                <span class="font-bold text-[#0d2c6c]">{{ $product->tracking_label }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Preferred Vendor:</span>
                                <span class="font-medium text-[#001849]">{{ $product->preferred_vendor ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Costing Method:</span>
                                <span class="font-semibold text-[#001849]">{{ strtoupper($product->costing_method ?? 'average') }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#eff4ff]">
                                <span class="text-[#757681]">Negative Stock Allowed:</span>
                                <span class="font-semibold {{ $product->allow_negative_stock ? 'text-emerald-700' : 'text-[#757681]' }}">
                                    {{ $product->allow_negative_stock ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($product->description)
                        <div class="p-4 rounded-xl bg-white border border-[#e5eeff]">
                            <h4 class="font-bold text-[#001849] mb-1">Description:</h4>
                            <p class="text-[#444650] leading-relaxed">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Tab 2: Inventory -->
                <div x-show="activeTab === 'inventory'" class="space-y-4 text-xs animate-in fade-in duration-100" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 rounded-xl bg-[#eff4ff] border border-[#dce9ff] space-y-3">
                            <h4 class="font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-[#fb7800]">alt_route</span>
                                Operations Routes
                            </h4>
                            <ul class="space-y-2">
                                <li class="flex items-center gap-2 {{ $product->route_buy ? 'text-[#001849] font-bold' : 'text-gray-400 line-through' }}">
                                    <span class="material-symbols-outlined text-[16px]">{{ $product->route_buy ? 'check' : 'close' }}</span>
                                    Buy (Purchase Order)
                                </li>
                                <li class="flex items-center gap-2 {{ $product->route_manufacture ? 'text-[#001849] font-bold' : 'text-gray-400 line-through' }}">
                                    <span class="material-symbols-outlined text-[16px]">{{ $product->route_manufacture ? 'check' : 'close' }}</span>
                                    Manufacture (Production Order)
                                </li>
                                <li class="flex items-center gap-2 {{ $product->route_mto ? 'text-[#001849] font-bold' : 'text-gray-400 line-through' }}">
                                    <span class="material-symbols-outlined text-[16px]">{{ $product->route_mto ? 'check' : 'close' }}</span>
                                    Replenish on Order (MTO)
                                </li>
                                <li class="flex items-center gap-2 {{ $product->route_subcontract ? 'text-[#001849] font-bold' : 'text-gray-400 line-through' }}">
                                    <span class="material-symbols-outlined text-[16px]">{{ $product->route_subcontract ? 'check' : 'close' }}</span>
                                    Subcontracting (Dyeing / Finishing)
                                </li>
                                @php
                                    $savedResupply = is_array($product->route_resupply_ids) ? $product->route_resupply_ids : (json_decode($product->route_resupply_ids ?? '[]', true) ?? []);
                                @endphp
                                @if(!empty($savedResupply))
                                    <li class="pt-2 border-t border-[#dce9ff]">
                                        <span class="text-[11px] font-bold text-[#001849] block mb-1">Warehouse Resupply:</span>
                                        <div class="space-y-1 pl-1">
                                            @foreach($savedResupply as $routeCode)
                                                @php
                                                    $parts = explode('_', $routeCode);
                                                    $fromWh = \App\Models\Warehouse::find($parts[0] ?? null);
                                                    $toWh = \App\Models\Warehouse::find($parts[1] ?? null);
                                                @endphp
                                                @if($fromWh && $toWh)
                                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-800">
                                                        <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                                        <span>Supply from {{ $fromWh->name }} to {{ $toWh->name }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-white border border-[#dce9ff] space-y-3">
                            <h4 class="font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-emerald-700">rule</span>
                                Reordering Rules
                            </h4>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-[#f8f9ff] p-2.5 rounded-lg text-center">
                                    <span class="text-[10px] text-[#757681] block">Min Quantity</span>
                                    <span class="font-bold font-mono text-[#001849]">{{ number_format($product->min_stock, 2) }}</span>
                                </div>
                                <div class="bg-[#f8f9ff] p-2.5 rounded-lg text-center">
                                    <span class="text-[10px] text-[#757681] block">Max Quantity</span>
                                    <span class="font-bold font-mono text-[#001849]">{{ number_format($product->max_stock ?? 0, 2) }}</span>
                                </div>
                                <div class="bg-[#f8f9ff] p-2.5 rounded-lg text-center">
                                    <span class="text-[10px] text-[#757681] block">Multiple Qty</span>
                                    <span class="font-bold font-mono text-[#001849]">{{ number_format($product->reorder_qty ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Substitutes -->
                <div x-show="activeTab === 'substitutes'" class="space-y-4 text-xs animate-in fade-in duration-100" style="display: none;">
                    <div class="p-4 rounded-xl bg-[#eff4ff] border border-[#dce9ff]">
                        <p class="font-semibold text-[#001849]">Dynamic BoM Component Switching Rules:</p>
                        <p class="text-[#757681] mt-0.5">If this component is out of stock during a Manufacturing Order (MO), the system automatically suggests the following approved substitutes:</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase">
                                <tr>
                                    <th class="py-2.5 px-4">Substitute Material</th>
                                    <th class="py-2.5 px-4 text-center">Conversion Ratio</th>
                                    <th class="py-2.5 px-4">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#eff4ff]">
                                @forelse($product->substitutes as $sub)
                                <tr>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('products.show', $sub->id) }}" class="font-bold text-[#0d2c6c] hover:underline block">
                                            [{{ $sub->code }}] {{ $sub->name }}
                                        </a>
                                        <span class="text-[10px] text-[#757681]">{{ $sub->category }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-[#001849]">
                                        {{ $sub->pivot->conversion_rate }}x
                                    </td>
                                    <td class="py-3 px-4 text-[#757681]">
                                        {{ $sub->pivot->notes ?? 'Automated substitution rule' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-[#757681]">
                                        No substitute materials registered for this item.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Moves Ledger -->
                <div x-show="activeTab === 'moves'" class="space-y-4 text-xs animate-in fade-in duration-100" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase">
                                <tr>
                                    <th class="py-2.5 px-4">Move Reference</th>
                                    <th class="py-2.5 px-4">Operation Category</th>
                                    <th class="py-2.5 px-4">Source Location</th>
                                    <th class="py-2.5 px-4">Destination Location</th>
                                    <th class="py-2.5 px-4 text-right">Quantity</th>
                                    <th class="py-2.5 px-4">Lot / Batch</th>
                                    <th class="py-2.5 px-4">Date &amp; User</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#eff4ff]">
                                @forelse($recentMoves as $move)
                                <tr>
                                    <td class="py-2.5 px-4 font-mono font-bold text-[#001849] whitespace-nowrap">{{ $move->move_number }}</td>
                                    <td class="py-2.5 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $move->category_badge }}">
                                            {{ $move->category_label }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-[#757681]">{{ $move->fromLocation->name ?? '-' }}</td>
                                    <td class="py-2.5 px-4 text-[#001849] font-medium">{{ $move->toLocation->name ?? '-' }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">{{ number_format($move->qty, 2) }} {{ $move->uom }}</td>
                                    <td class="py-2.5 px-4 font-mono text-[#757681]">{{ $move->batch_lot_number ?? '-' }}</td>
                                    <td class="py-2.5 px-4 text-[#757681] whitespace-nowrap">
                                        {{ $move->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-[#757681]">No stock moves recorded for this product yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Odoo History & Audit Log Section -->
            <div class="pt-6 border-t border-[#eff4ff] bg-[#f8f9ff] -mx-6 -mb-6 sm:-mx-8 sm:-mb-8 p-6 sm:p-8 rounded-b-2xl">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[#fb7800] text-[18px]">history</span>
                    <h4 class="text-xs font-bold text-[#001849] uppercase tracking-wider">History &amp; Audit Log</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Created Info -->
                    <div class="p-4 rounded-xl bg-white border border-[#e5eeff] shadow-2xs flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        </div>
                        <div class="flex-1 min-w-0 text-xs">
                            <span class="text-[10px] uppercase font-bold text-[#757681] block">Created By &amp; When</span>
                            <div class="font-bold text-[#001849] truncate mt-0.5">
                                {{ $product->creator?->name ?? 'System Administrator' }}
                            </div>
                            <div class="text-[11px] text-[#757681] font-mono mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                <span>{{ $product->created_at ? $product->created_at->translatedFormat('l, d F Y - H:i:s') . ' WIB' : '-' }}</span>
                            </div>
                            <span class="text-[10px] text-[#757681]/70 block mt-0.5">({{ $product->created_at ? $product->created_at->diffForHumans() : '-' }})</span>
                        </div>
                    </div>

                    <!-- Updated Info -->
                    <div class="p-4 rounded-xl bg-white border border-[#e5eeff] shadow-2xs flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-200">
                            <span class="material-symbols-outlined text-[18px]">edit_calendar</span>
                        </div>
                        <div class="flex-1 min-w-0 text-xs">
                            <span class="text-[10px] uppercase font-bold text-[#757681] block">Last Updated By &amp; When</span>
                            <div class="font-bold text-[#001849] truncate mt-0.5">
                                {{ $product->updater?->name ?? $product->creator?->name ?? 'System Administrator' }}
                            </div>
                            <div class="text-[11px] text-[#757681] font-mono mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">update</span>
                                <span>{{ $product->updated_at ? $product->updated_at->translatedFormat('l, d F Y - H:i:s') . ' WIB' : '-' }}</span>
                            </div>
                            <span class="text-[10px] text-[#757681]/70 block mt-0.5">({{ $product->updated_at ? $product->updated_at->diffForHumans() : '-' }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

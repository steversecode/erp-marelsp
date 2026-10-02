@extends('layouts.app')

@section('title', "Unit of Measure: {$uom->name} - Configuration")

@section('content')
<div class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Form Header) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('configuration.uom.edit', $uom->id) }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                <span>Edit</span>
            </a>
            <a href="{{ route('configuration.uom.create') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-semibold flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>

            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('configuration.uom.index') }}" class="hover:text-[#001849] font-semibold">Units of Measure</a>
                    <span>/</span>
                    <span class="text-[#001849] font-bold font-mono">{{ $uom->name }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('configuration.uom.destroy', $uom->id) }}" onsubmit="return confirm('Delete Unit of Measure \'{{ $uom->name }}\'?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Delete</span>
                </button>
            </form>
            <a href="{{ route('configuration.uom.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#757681] hover:text-[#001849] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to List
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
        <div class="border-b border-[#eff4ff] bg-[#f8f9ff]/50 p-2 sm:px-6 flex items-center justify-end gap-2">
            <a href="{{ route('inventory.index', ['q' => $uom->name]) }}" class="px-3.5 py-1.5 rounded-xl bg-white border border-[#dce9ff] hover:border-[#001849] hover:bg-[#eff4ff] transition-all flex items-center gap-2.5 text-xs shadow-xs group">
                <span class="material-symbols-outlined text-[#fb7800] text-[20px]">inventory_2</span>
                <div class="text-left">
                    <span class="text-[10px] text-[#757681] uppercase font-bold block leading-none">Products</span>
                    <span class="font-bold text-[#001849] font-mono text-xs">{{ $productsCount }} Items</span>
                </div>
            </a>
        </div>

        <div class="p-6 sm:p-8 space-y-8">
            <!-- Header Title -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#eff4ff] pb-6">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-[#fb7800] font-bold block mb-1">Unit of Measure</span>
                    <h1 class="text-2xl sm:text-3xl font-bold font-mono text-[#001849] flex items-center gap-3">
                        <span>{{ $uom->name }}</span>
                        @if($uom->is_active)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Inactive</span>
                        @endif
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#757681]">Category:</span>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                        {{ $uom->category?->name ?? 'Unassigned' }}
                    </span>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-[#757681] uppercase tracking-wider">Unit Specifications</h3>

                    <div class="bg-[#f8f9ff] p-4 rounded-xl border border-[#e5eeff] space-y-3">
                        <div class="flex items-center justify-between text-xs py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Category</span>
                            <span class="font-bold text-[#001849]">{{ $uom->category?->name ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">UoM Type</span>
                            <div>
                                @if($uom->uom_type === 'reference')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Reference Unit of Measure</span>
                                    </span>
                                @elseif($uom->uom_type === 'bigger')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="material-symbols-outlined text-[14px]">trending_up</span>
                                        <span>Bigger than reference</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="material-symbols-outlined text-[14px]">trending_down</span>
                                        <span>Smaller than reference</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs py-1 border-b border-[#eff4ff]">
                            <span class="text-[#757681]">Rounding Precision</span>
                            <span class="font-bold font-mono text-[#001849]">{{ number_format($uom->rounding, 4) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Ratio & Formula Card -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-[#757681] uppercase tracking-wider">Conversion &amp; Ratio</h3>

                    <div class="bg-[#eff4ff]/60 p-5 rounded-xl border border-[#dce9ff] space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#0d2c6c]">
                            <span class="material-symbols-outlined text-[18px]">calculate</span>
                            <span>Conversion Ratio</span>
                        </div>
                        <div class="text-xl font-bold font-mono text-[#001849]">
                            {{ $uom->ratio_formula }}
                        </div>
                        <p class="text-xs text-[#757681] leading-relaxed">
                            @if($uom->uom_type === 'reference')
                                This is the base reference unit for the <strong>{{ $uom->category?->name }}</strong> category. All other units in this category are calculated relative to this unit.
                            @else
                                Stock calculations, purchases, and sales in this unit will automatically convert based on ratio <span class="font-mono font-bold text-[#001849]">{{ (float)$uom->ratio }}</span>.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Other Units in This Category -->
            <div class="space-y-3 pt-4 border-t border-[#eff4ff]">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#fb7800] text-[18px]">straighten</span>
                        <span>Units in Category: {{ $uom->category?->name }}</span>
                    </h3>
                    <a href="{{ route('configuration.uom.index', ['category_id' => $uom->category_id]) }}" class="text-xs text-[#0d2c6c] hover:underline font-semibold">
                        View All in Category →
                    </a>
                </div>

                <div class="bg-white rounded-xl border border-[#e5eeff] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase border-b border-[#e5eeff]">
                            <tr>
                                <th class="py-2.5 px-4">Unit Name</th>
                                <th class="py-2.5 px-4">Type</th>
                                <th class="py-2.5 px-4 text-right">Ratio Formula</th>
                                <th class="py-2.5 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eff4ff]">
                            @foreach($categoryUnits as $unit)
                            <tr class="hover:bg-[#f8f9ff] transition-colors {{ $unit->id === $uom->id ? 'bg-[#fff8f2] font-bold' : '' }}">
                                <td class="py-2.5 px-4 font-mono">
                                    <a href="{{ route('configuration.uom.show', $unit->id) }}" class="text-[#001849] hover:underline">
                                        {{ $unit->name }} {{ $unit->id === $uom->id ? '(Current)' : '' }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-4">
                                    <span class="text-[11px] text-[#757681]">{{ ucfirst($unit->uom_type) }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono text-[#001849]">
                                    {{ $unit->ratio_formula }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <a href="{{ route('configuration.uom.show', $unit->id) }}" class="text-[#0d2c6c] hover:underline text-xs">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Products Using This Unit -->
            <div class="space-y-3 pt-4 border-t border-[#eff4ff]">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#fb7800] text-[18px]">inventory_2</span>
                        <span>Products Using This Unit ({{ $productsCount }})</span>
                    </h3>
                </div>

                @if($products->count() > 0)
                <div class="bg-white rounded-xl border border-[#e5eeff] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase border-b border-[#e5eeff]">
                            <tr>
                                <th class="py-2.5 px-4">Code</th>
                                <th class="py-2.5 px-4">Product Name</th>
                                <th class="py-2.5 px-4">Category</th>
                                <th class="py-2.5 px-4 text-right">Cost Price</th>
                                <th class="py-2.5 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eff4ff]">
                            @foreach($products as $prod)
                            <tr class="hover:bg-[#f8f9ff] transition-colors">
                                <td class="py-2.5 px-4 font-mono font-bold text-[#0d2c6c]">
                                    <a href="{{ route('products.show', $prod->id) }}" class="hover:underline">
                                        [{{ $prod->code }}]
                                    </a>
                                </td>
                                <td class="py-2.5 px-4 font-medium text-[#0b1c30]">
                                    <a href="{{ route('products.show', $prod->id) }}" class="hover:underline">
                                        {{ $prod->name }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-4 text-[#757681]">
                                    {{ $prod->productCategory?->name ?? $prod->category ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#001849]">
                                    Rp {{ number_format($prod->cost_price, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <a href="{{ route('products.show', $prod->id) }}" class="text-[#0d2c6c] hover:underline font-semibold text-xs">
                                        Open Product
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-6 rounded-xl bg-[#f8f9ff] border border-[#e5eeff] text-center text-xs text-[#757681]">
                    No products currently assigned to this unit of measure.
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Units of Measure - Configuration')

@section('content')
<div x-data="{ 
    selected: [],
    selectAll: false,
    bulkDeleteConfirmOpen: false,
    advanceSearchOpen: {{ request()->anyFilled(['category_id', 'uom_type', 'is_active']) ? 'true' : 'false' }},
    
    toggleAll() {
        if (this.selectAll) {
            this.selected = [{{ $uoms->pluck('id')->implode(',') }}];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        const pageIds = [{{ $uoms->pluck('id')->implode(',') }}];
        this.selectAll = pageIds.length > 0 && pageIds.every(id => this.selected.includes(id));
    }
}" class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('configuration.uom.create') }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>
            
            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('inventory.index') }}" class="hover:text-[#001849] font-semibold">Inventory</a>
                    <span>/</span>
                    <span class="text-[#757681]">Configuration</span>
                    <span>/</span>
                    <span class="text-[#001849] font-bold">Units of Measure</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-[#757681]">
                Total: <strong class="text-[#001849] font-mono">{{ $totalCount }}</strong> UoMs in <strong class="text-[#001849]">{{ $categoriesCount }}</strong> categories
            </span>
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#757681] hover:text-[#001849] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to Inventory
            </a>
        </div>
    </div>

    <!-- Quick Search & Advance Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm space-y-3">
        <form method="GET" action="{{ route('configuration.uom.index') }}" class="space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="material-symbols-outlined text-[#757681] text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search unit name or category (e.g. kg, Pcs, Weight, Length)..." 
                        class="w-full pl-9 pr-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                </div>

                <!-- Category Quick Filter Dropdown -->
                <div class="flex items-center gap-2">
                    <select name="category_id" onchange="this.form.submit()" 
                        class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                        <option value="">All Categories ({{ $categoriesCount }})</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" @click="advanceSearchOpen = !advanceSearchOpen" class="px-3 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer" :class="advanceSearchOpen ? 'bg-[#dce9ff]' : ''">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        <span>Filters</span>
                    </button>

                    @if(request()->anyFilled(['q', 'category_id', 'uom_type', 'is_active']))
                        <a href="{{ route('configuration.uom.index') }}" class="px-3 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Expandable Advance Search Filters -->
            <div x-show="advanceSearchOpen" x-collapse class="pt-3 border-t border-[#eff4ff] grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Type</label>
                    <select name="uom_type" class="w-full px-3 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                        <option value="">All Types</option>
                        <option value="reference" {{ request('uom_type') == 'reference' ? 'selected' : '' }}>Reference Unit</option>
                        <option value="bigger" {{ request('uom_type') == 'bigger' ? 'selected' : '' }}>Bigger than reference</option>
                        <option value="smaller" {{ request('uom_type') == 'smaller' ? 'selected' : '' }}>Smaller than reference</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Status</label>
                    <select name="is_active" class="w-full px-3 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- MAIN UOM TABLE -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 rounded text-[#001849] focus:ring-[#001849] cursor-pointer">
                        </th>
                        <th class="py-3 px-4 min-w-[150px]">Unit of Measure</th>
                        <th class="py-3 px-4 min-w-[140px]">Category</th>
                        <th class="py-3 px-4 min-w-[170px]">Type</th>
                        <th class="py-3 px-4 min-w-[220px]">Ratio &amp; Conversion</th>
                        <th class="py-3 px-4 text-right">Rounding</th>
                        <th class="py-3 px-3 text-center">Products</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($uoms as $uom)
                    <tr class="hover:bg-[#f8f9ff] transition-colors" :class="selected.includes({{ $uom->id }}) ? 'bg-[#fff8f2]' : ''">
                        <td class="py-3 px-3 text-center">
                            <input type="checkbox" :value="{{ $uom->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded text-[#001849] focus:ring-[#001849] cursor-pointer">
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('configuration.uom.show', $uom->id) }}" class="font-mono font-bold text-[#001849] hover:text-[#fb7800] hover:underline text-sm block">
                                {{ $uom->name }}
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                                <span class="material-symbols-outlined text-[13px]">category</span>
                                <span>{{ $uom->category?->name ?? '-' }}</span>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($uom->uom_type === 'reference')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Reference</span>
                                </span>
                            @elseif($uom->uom_type === 'bigger')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="material-symbols-outlined text-[12px]">trending_up</span>
                                    <span>Bigger</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="material-symbols-outlined text-[12px]">trending_down</span>
                                    <span>Smaller</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs font-mono text-[#001849]">
                            {{ $uom->ratio_formula }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-[#757681]">
                            {{ number_format($uom->rounding, 4) }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono font-bold text-[#0d2c6c]">
                            {{ $uom->products_count }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($uom->is_active)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('configuration.uom.show', $uom->id) }}" class="p-1.5 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                </a>
                                <a href="{{ route('configuration.uom.edit', $uom->id) }}" class="p-1.5 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] transition-colors" title="Edit UoM">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                </a>
                                <form method="POST" action="{{ route('configuration.uom.destroy', $uom->id) }}" onsubmit="return confirm('Delete Unit of Measure \'{{ $uom->name }}\'?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors cursor-pointer" title="Delete">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-[#757681]">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-[40px] text-[#757681]/40 mb-2">straighten</span>
                                <h4 class="font-bold text-[#001849] text-sm">No Units of Measure Found</h4>
                                <p class="text-xs text-[#757681] mt-0.5 mb-3">Try adjusting your search criteria or create a new unit.</p>
                                <a href="{{ route('configuration.uom.create') }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs">
                                    + Create Unit of Measure
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar (15 items / page) -->
        @if($uoms->hasPages() || $uoms->total() > 0)
        <div class="p-4 border-t border-[#e5eeff] bg-[#eff4ff]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="text-[#757681]">
                Showing <strong class="text-[#001849]">{{ $uoms->firstItem() ?? 0 }}</strong> to <strong class="text-[#001849]">{{ $uoms->lastItem() ?? 0 }}</strong> of <strong class="text-[#001849]">{{ $uoms->total() }}</strong> units
            </div>
            <div>
                {{ $uoms->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Floating Bulk Actions Toolbar -->
    <div x-show="selected.length > 0" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-[#001849] text-white px-5 py-3 rounded-2xl shadow-2xl border border-[#202e5a] flex items-center gap-4">
        <div class="flex items-center gap-2 text-xs font-semibold">
            <span class="w-6 h-6 rounded-full bg-[#fb7800] text-white text-[11px] font-bold flex items-center justify-center font-mono" x-text="selected.length"></span>
            <span>UoMs selected</span>
        </div>
        <div class="h-4 w-[1px] bg-[#202e5a]"></div>
        <button type="button" @click="bulkDeleteConfirmOpen = true" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">delete</span>
            <span>Delete Selected</span>
        </button>
        <button type="button" @click="selected = []; selectAll = false" class="text-xs text-[#b3c5ff] hover:text-white transition-colors cursor-pointer">
            Deselect
        </button>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div x-show="bulkDeleteConfirmOpen" x-cloak class="fixed inset-0 z-50 bg-[#001849]/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div @click.away="bulkDeleteConfirmOpen = false" class="bg-white rounded-2xl border border-[#e5eeff] shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
            <div class="flex items-center gap-3 text-rose-600">
                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                </div>
                <div>
                    <h3 class="font-bold text-[#001849] text-base">Delete Selected Units</h3>
                    <p class="text-xs text-[#757681]">Are you sure you want to delete <span class="font-bold text-[#001849]" x-text="selected.length"></span> Unit(s)?</p>
                </div>
            </div>

            <p class="text-xs text-[#757681] leading-relaxed">
                This action will delete the selected units of measure. Products linked to these units may need to be updated.
            </p>

            <form method="POST" action="{{ route('configuration.uom.bulk-destroy') }}">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="bulkDeleteConfirmOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold cursor-pointer">
                        Confirm Bulk Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

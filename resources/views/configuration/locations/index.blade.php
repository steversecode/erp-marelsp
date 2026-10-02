@extends('layouts.app')

@section('title', 'Locations - Inventory Configuration')

@section('content')
<div x-data="{ 
    selected: [],
    selectAll: false,
    bulkDeleteConfirmOpen: false,
    advanceSearchOpen: {{ request()->anyFilled(['type', 'warehouse_id', 'parent_id', 'is_scrap', 'is_return', 'is_active']) ? 'true' : 'false' }},
    
    toggleAll() {
        if (this.selectAll) {
            this.selected = [{{ $locations->pluck('id')->implode(',') }}];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        const pageIds = [{{ $locations->pluck('id')->implode(',') }}];
        this.selectAll = pageIds.length > 0 && pageIds.every(id => this.selected.includes(id));
    }
}" class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('configuration.locations.create') }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>
            
            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('inventory.index') }}" class="hover:text-[#001849] font-semibold">Inventory</a>
                    <span>/</span>
                    <span class="text-[#757681]">Configuration</span>
                    <span>/</span>
                    <span class="text-[#001849] font-bold">Locations</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 text-xs text-[#757681]">
                <span class="px-2.5 py-1 rounded-md bg-[#eff4ff] text-[#001849] font-semibold">
                    Total: <strong class="font-mono">{{ $totalLocations }}</strong>
                </span>
                <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 font-semibold">
                    Internal: <strong class="font-mono">{{ $internalCount }}</strong>
                </span>
                @if($scrapCount > 0)
                <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-800 font-semibold">
                    Scrap: <strong class="font-mono">{{ $scrapCount }}</strong>
                </span>
                @endif
            </div>
            <a href="{{ route('configuration.warehouses.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[15px]">warehouse</span>
                <span>Warehouses</span>
            </a>
        </div>
    </div>

    <!-- Quick Search & Advance Search Bar (Odoo Style) -->
    <div class="bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm space-y-3">
        <form method="GET" action="{{ route('configuration.locations.index') }}" class="space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="material-symbols-outlined text-[#757681] text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search location path, code, barcode, warehouse, or building zone..." 
                        class="w-full pl-9 pr-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                </div>

                <!-- Quick Filter Selects -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Warehouse Filter -->
                    <select name="warehouse_id" onchange="this.form.submit()" 
                        class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Type Quick Filter -->
                    <select name="type" onchange="this.form.submit()" 
                        class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                        <option value="">All Location Types</option>
                        <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Internal Location</option>
                        <option value="view" {{ request('type') == 'view' ? 'selected' : '' }}>View (Hierarchy Folder)</option>
                        <option value="vendor" {{ request('type') == 'vendor' ? 'selected' : '' }}>Vendor Location</option>
                        <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer Location</option>
                        <option value="production" {{ request('type') == 'production' ? 'selected' : '' }}>Production (WIP)</option>
                        <option value="loss" {{ request('type') == 'loss' ? 'selected' : '' }}>Inventory Loss / Scrap</option>
                        <option value="transit" {{ request('type') == 'transit' ? 'selected' : '' }}>Transit Location</option>
                        <option value="dyeing_subcon" {{ request('type') == 'dyeing_subcon' ? 'selected' : '' }}>Subcontractor</option>
                        <option value="sample" {{ request('type') == 'sample' ? 'selected' : '' }}>Sample / R&amp;D</option>
                    </select>

                    <button type="button" @click="advanceSearchOpen = !advanceSearchOpen" class="px-3 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer" :class="advanceSearchOpen ? 'bg-[#dce9ff]' : ''">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        <span>Filters</span>
                    </button>

                    @if(request()->anyFilled(['q', 'type', 'warehouse_id', 'parent_id', 'is_scrap', 'is_return', 'is_active']))
                        <a href="{{ route('configuration.locations.index') }}" class="px-3 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Expandable Advance Search Filters -->
            <div x-show="advanceSearchOpen" x-collapse class="pt-3 border-t border-[#eff4ff] grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Parent Location</label>
                    <select name="parent_id" class="w-full px-3 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                        <option value="">All Parents</option>
                        @foreach($allLocations as $pLoc)
                            <option value="{{ $pLoc->id }}" {{ request('parent_id') == $pLoc->id ? 'selected' : '' }}>
                                {{ $pLoc->complete_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Scrap Location</label>
                    <select name="is_scrap" class="w-full px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                        <option value="">All</option>
                        <option value="1" {{ request('is_scrap') === '1' ? 'selected' : '' }}>Scrap Only</option>
                        <option value="0" {{ request('is_scrap') === '0' ? 'selected' : '' }}>Non-Scrap</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Return Location</label>
                    <select name="is_return" class="w-full px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                        <option value="">All</option>
                        <option value="1" {{ request('is_return') === '1' ? 'selected' : '' }}>Return Only</option>
                        <option value="0" {{ request('is_return') === '0' ? 'selected' : '' }}>Non-Return</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#757681] uppercase mb-1">Status</label>
                    <div class="flex gap-2">
                        <select name="is_active" class="flex-1 px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active Only</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs transition-colors">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- MAIN LOCATIONS TABLE (Odoo Hierarchical View) -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 rounded text-[#001849] focus:ring-[#001849] cursor-pointer">
                        </th>
                        <th class="py-3 px-4 min-w-[280px]">Location (Complete Path)</th>
                        <th class="py-3 px-3 min-w-[140px]">Location Type</th>
                        <th class="py-3 px-3 min-w-[160px]">Warehouse</th>
                        <th class="py-3 px-3 min-w-[130px]">Code</th>
                        <th class="py-3 px-3 text-center min-w-[110px]">Properties</th>
                        <th class="py-3 px-3 text-right">In Moves</th>
                        <th class="py-3 px-3 text-right">Out Moves</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($locations as $loc)
                    <tr class="hover:bg-[#f8f9ff] transition-colors {{ $loc->type === 'view' ? 'bg-[#fcfdff]' : '' }}" :class="selected.includes({{ $loc->id }}) ? 'bg-[#fff8f2]' : ''">
                        <td class="py-3 px-3 text-center">
                            <input type="checkbox" :value="{{ $loc->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded text-[#001849] focus:ring-[#001849] cursor-pointer">
                        </td>
                        
                        <!-- Complete Location Name / Hierarchy -->
                        <td class="py-3 px-4">
                            <a href="{{ route('configuration.locations.show', $loc->id) }}" class="hover:text-[#0d2c6c] hover:underline flex items-center gap-2">
                                @if($loc->type === 'internal')
                                    <span class="material-symbols-outlined text-[17px] text-[#fb7800] shrink-0">warehouse</span>
                                @elseif($loc->type === 'view')
                                    <span class="material-symbols-outlined text-[17px] text-slate-500 shrink-0">folder_open</span>
                                @elseif($loc->type === 'production')
                                    <span class="material-symbols-outlined text-[17px] text-indigo-600 shrink-0">precision_manufacturing</span>
                                @elseif($loc->type === 'vendor')
                                    <span class="material-symbols-outlined text-[17px] text-blue-600 shrink-0">local_shipping</span>
                                @elseif($loc->type === 'customer')
                                    <span class="material-symbols-outlined text-[17px] text-cyan-600 shrink-0">person_pin_circle</span>
                                @elseif($loc->type === 'transit')
                                    <span class="material-symbols-outlined text-[17px] text-amber-600 shrink-0">swap_horizontal_circle</span>
                                @elseif($loc->type === 'dyeing_subcon')
                                    <span class="material-symbols-outlined text-[17px] text-purple-600 shrink-0">palette</span>
                                @elseif($loc->type === 'sample')
                                    <span class="material-symbols-outlined text-[17px] text-teal-600 shrink-0">science</span>
                                @elseif($loc->type === 'loss' || $loc->is_scrap)
                                    <span class="material-symbols-outlined text-[17px] text-rose-600 shrink-0">delete_sweep</span>
                                @else
                                    <span class="material-symbols-outlined text-[17px] text-[#757681] shrink-0">place</span>
                                @endif
                                
                                <div class="min-w-0">
                                    <span class="font-bold text-[#001849] block truncate {{ $loc->type === 'view' ? 'text-slate-600' : '' }}">
                                        {{ $loc->complete_name }}
                                    </span>
                                    @if($loc->address)
                                        <span class="text-[10px] text-[#757681] block truncate">{{ $loc->address }}</span>
                                    @endif
                                </div>
                            </a>
                        </td>

                        <!-- Location Type Badge -->
                        <td class="py-3 px-3 whitespace-nowrap">
                            @if($loc->type === 'internal')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Internal Location</span>
                                </span>
                            @elseif($loc->type === 'view')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <span>View</span>
                                </span>
                            @elseif($loc->type === 'production')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <span>Production</span>
                                </span>
                            @elseif($loc->type === 'vendor')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span>Vendor Location</span>
                                </span>
                            @elseif($loc->type === 'customer')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                    <span>Customer Location</span>
                                </span>
                            @elseif($loc->type === 'transit')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span>Transit Location</span>
                                </span>
                            @elseif($loc->type === 'dyeing_subcon')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <span>Subcontractor</span>
                                </span>
                            @elseif($loc->type === 'sample')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                    <span>Sample / R&amp;D</span>
                                </span>
                            @elseif($loc->type === 'loss')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span>Inventory Loss</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-50 text-gray-700 border border-gray-200">
                                    <span>{{ ucfirst($loc->type) }}</span>
                                </span>
                            @endif
                        </td>

                        <!-- Warehouse Column -->
                        <td class="py-3 px-3 whitespace-nowrap">
                            @if($loc->warehouse)
                                <a href="{{ route('configuration.warehouses.show', $loc->warehouse_id) }}" class="font-semibold text-[#0d2c6c] hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-[#fb7800]">warehouse</span>
                                    <span>{{ $loc->warehouse->name }}</span>
                                </a>
                            @else
                                <span class="text-[#757681] italic">-</span>
                            @endif
                        </td>

                        <!-- Code / Ref -->
                        <td class="py-3 px-3 font-mono font-bold text-[#0d2c6c] whitespace-nowrap">
                            <a href="{{ route('configuration.locations.show', $loc->id) }}" class="hover:underline">
                                {{ $loc->code }}
                            </a>
                        </td>

                        <!-- Properties: Scrap & Return -->
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                @if($loc->is_scrap)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-700" title="Scrap Location">Scrap</span>
                                @endif
                                @if($loc->is_return)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700" title="Return Location">Return</span>
                                @endif
                                @if(!$loc->is_scrap && !$loc->is_return)
                                    <span class="text-[#757681]">-</span>
                                @endif
                            </div>
                        </td>

                        <!-- In / Out Moves -->
                        <td class="py-3 px-3 text-right font-mono font-bold text-[#001849]">
                            {{ $loc->incoming_stock_moves_count }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-[#001849]">
                            {{ $loc->outgoing_stock_moves_count }}
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            @if($loc->is_active)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600">Archived</span>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('configuration.locations.show', $loc->id) }}" class="p-1.5 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                </a>
                                <a href="{{ route('configuration.locations.edit', $loc->id) }}" class="p-1.5 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] transition-colors" title="Edit Location">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                </a>
                                <form method="POST" action="{{ route('configuration.locations.destroy', $loc->id) }}" onsubmit="return confirm('Delete or archive location [{{ $loc->code }}]?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors cursor-pointer" title="Delete / Archive">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-[#757681]">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-[40px] text-[#757681]/40 mb-2">location_off</span>
                                <h4 class="font-bold text-[#001849] text-sm">No Locations Found</h4>
                                <p class="text-xs text-[#757681] mt-0.5 mb-3">Create your first location or adjust filters.</p>
                                <a href="{{ route('configuration.locations.create') }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs">
                                    + Create Location
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar (20 items / page) -->
        @if($locations->hasPages() || $locations->total() > 0)
        <div class="p-4 border-t border-[#e5eeff] bg-[#eff4ff]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="text-[#757681]">
                Showing <strong class="text-[#001849]">{{ $locations->firstItem() ?? 0 }}</strong> to <strong class="text-[#001849]">{{ $locations->lastItem() ?? 0 }}</strong> of <strong class="text-[#001849]">{{ $locations->total() }}</strong> locations
            </div>
            <div>
                {{ $locations->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Floating Bulk Actions Toolbar -->
    <div x-show="selected.length > 0" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-[#001849] text-white px-5 py-3 rounded-2xl shadow-2xl border border-[#202e5a] flex items-center gap-4">
        <div class="flex items-center gap-2 text-xs font-semibold">
            <span class="w-6 h-6 rounded-full bg-[#fb7800] text-white text-[11px] font-bold flex items-center justify-center font-mono" x-text="selected.length"></span>
            <span>locations selected</span>
        </div>
        <div class="h-4 w-[1px] bg-[#202e5a]"></div>
        <button type="button" @click="bulkDeleteConfirmOpen = true" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">delete</span>
            <span>Delete / Archive Selected</span>
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
                    <h3 class="font-bold text-[#001849] text-base">Process Selected Locations</h3>
                    <p class="text-xs text-[#757681]">Are you sure you want to delete/archive <span class="font-bold text-[#001849]" x-text="selected.length"></span> Location(s)?</p>
                </div>
            </div>

            <p class="text-xs text-[#757681] leading-relaxed">
                Locations with existing stock ledger moves will be safely archived (set to inactive) to preserve inventory audit history. Unused locations will be removed.
            </p>

            <form method="POST" action="{{ route('configuration.locations.bulk-destroy') }}">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="bulkDeleteConfirmOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold cursor-pointer">
                        Confirm Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

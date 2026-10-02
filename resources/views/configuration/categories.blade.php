@extends('layouts.app')

@section('title', 'Product Categories - Configuration')

@section('content')
<div x-data="{ 
    createModalOpen: false, 
    editModalOpen: false, 
    bulkDeleteConfirmOpen: false,
    advanceSearchOpen: {{ ($parentId || $costingMethod || $valuationMethod) ? 'true' : 'false' }},
    editData: { id: null, name: '', code: '', parent_id: '', costing_method: 'average', valuation_method: 'automated', description: '' },
    selectedIds: [],
    selectAll: false,
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedIds = [{{ $categories->pluck('id')->implode(',') }}];
        } else {
            this.selectedIds = [];
        }
    },
    updateSelectAllState() {
        const pageIds = [{{ $categories->pluck('id')->implode(',') }}];
        this.selectAll = pageIds.length > 0 && pageIds.every(id => this.selectedIds.includes(id));
    }
}" class="space-y-5 animate-in fade-in duration-150 relative">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">settings</span>
                <span>CONFIGURATION / PRODUCTS</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Product Categories (Odoo Standard)
            </h2>
            <p class="text-xs text-[#757681]">
                Master kategori produk (Total: <strong>{{ $totalCount ?? $categories->total() }}</strong> kategori) dengan relasi hierarki, metode costing, dan valuasi persediaan
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="createModalOpen = true" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add_box</span>
                <span>+ New Category</span>
            </button>
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to Inventory
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">error</span>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-4 space-y-0.5 font-normal">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Bar & Advance Search Controls -->
    <div class="bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm space-y-3">
        <form method="GET" action="{{ route('configuration.categories') }}" id="searchFilterForm" class="space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Main Search Input -->
                <div class="relative flex-1">
                    <span class="material-symbols-outlined text-[#757681] text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search category by name, code, description, or parent..." 
                        class="w-full pl-9 pr-4 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="advanceSearchOpen = !advanceSearchOpen" class="px-3.5 py-2 rounded-lg border border-[#dce9ff] text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer" :class="advanceSearchOpen ? 'bg-[#0d2c6c] text-white border-[#0d2c6c]' : 'bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff]'">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        <span>Advance Search</span>
                        @if($parentId || $costingMethod || $valuationMethod)
                            <span class="w-2 h-2 rounded-full bg-[#fb7800]"></span>
                        @endif
                    </button>

                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold transition-colors cursor-pointer">
                        Search
                    </button>

                    @if($search || $parentId || $costingMethod || $valuationMethod)
                        <a href="{{ route('configuration.categories') }}" class="px-3 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold transition-colors" title="Reset all filters">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Advance Search Drawer / Panel -->
            <div x-show="advanceSearchOpen" x-cloak class="pt-3 border-t border-[#eff4ff] grid grid-cols-1 sm:grid-cols-3 gap-3 animate-in fade-in duration-100">
                
                <!-- Searchable Custom Dropdown: Parent Category -->
                <div x-data="{ 
                    open: false, 
                    searchKeyword: '', 
                    selectedVal: '{{ $parentId ?? '' }}',
                    selectedText: '{{ $parentId === 'root' ? '-- Only Root Categories (No Parent) --' : ($parentCategories->firstWhere('id', $parentId)?->name ?? 'All Categories (Root & Sub)') }}',
                    items: [
                        { id: '', name: 'All Categories (Root & Sub)' },
                        { id: 'root', name: '-- Only Root Categories (No Parent) --' },
                        @foreach($parentCategories as $p)
                            { id: '{{ $p->id }}', name: '{{ addslashes($p->complete_name ?? $p->name) }}' },
                        @endforeach
                    ],
                    get filteredItems() {
                        if (!this.searchKeyword) return this.items;
                        return this.items.filter(i => i.name.toLowerCase().includes(this.searchKeyword.toLowerCase()));
                    },
                    choose(item) {
                        this.selectedVal = item.id;
                        this.selectedText = item.name;
                        this.open = false;
                        $refs.hiddenParent.value = item.id;
                    }
                }" class="relative">
                    <label class="text-[11px] font-bold text-[#757681] uppercase block mb-1">Parent Category</label>
                    <input type="hidden" name="parent_id" x-ref="hiddenParent" :value="selectedVal">

                    <button type="button" @click="open = !open" class="w-full px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none flex items-center justify-between text-left cursor-pointer">
                        <span class="truncate" x-text="selectedText"></span>
                        <span class="material-symbols-outlined text-[16px] text-[#757681] shrink-0">arrow_drop_down</span>
                    </button>

                    <!-- Dropdown Panel (Max-height bounded with internal search) -->
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-xl border border-[#e5eeff] shadow-xl z-50 overflow-hidden animate-in fade-in duration-100">
                        <div class="p-2 border-b border-[#eff4ff] bg-[#f8f9ff]">
                            <div class="relative">
                                <span class="material-symbols-outlined text-[#757681] text-[15px] absolute left-2.5 top-1/2 -translate-y-1/2">search</span>
                                <input type="text" x-model="searchKeyword" placeholder="Filter parent..." class="w-full pl-8 pr-2.5 py-1 rounded-md bg-white border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                            </div>
                        </div>

                        <div class="max-h-48 overflow-y-auto divide-y divide-[#eff4ff] text-xs">
                            <template x-for="item in filteredItems" :key="item.id">
                                <button type="button" @click="choose(item)" class="w-full px-3 py-2 text-left hover:bg-[#eff4ff] flex items-center justify-between transition-colors cursor-pointer" :class="selectedVal === item.id ? 'bg-[#eff4ff] font-bold text-[#0d2c6c]' : 'text-[#001849]'">
                                    <span class="truncate" x-text="item.name"></span>
                                    <span x-show="selectedVal === item.id" class="material-symbols-outlined text-[#0d2c6c] text-[15px]">check</span>
                                </button>
                            </template>
                            <div x-show="filteredItems.length === 0" class="p-3 text-center text-[#757681] text-xs">
                                No matching parent category
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Costing Method Filter -->
                <div>
                    <label class="text-[11px] font-bold text-[#757681] uppercase block mb-1">Costing Method</label>
                    <select name="costing_method" class="w-full px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">All Costing Methods</option>
                        <option value="average" {{ $costingMethod === 'average' ? 'selected' : '' }}>Average Cost (AVCO)</option>
                        <option value="fifo" {{ $costingMethod === 'fifo' ? 'selected' : '' }}>First In First Out (FIFO)</option>
                        <option value="standard" {{ $costingMethod === 'standard' ? 'selected' : '' }}>Standard Price</option>
                    </select>
                </div>

                <!-- Valuation Method Filter -->
                <div>
                    <label class="text-[11px] font-bold text-[#757681] uppercase block mb-1">Inventory Valuation</label>
                    <select name="valuation_method" class="w-full px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">All Valuations</option>
                        <option value="automated" {{ $valuationMethod === 'automated' ? 'selected' : '' }}>Automated (Real-time)</option>
                        <option value="manual" {{ $valuationMethod === 'manual' ? 'selected' : '' }}>Manual (Periodic)</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Floating Bulk Actions Bar (Contextual when checkboxes are checked) -->
    <div x-show="selectedIds.length > 0" x-cloak class="sticky top-4 z-40 bg-[#001849] text-white p-3.5 rounded-xl shadow-lg border border-[#0d2c6c] flex flex-wrap items-center justify-between gap-3 animate-in slide-in-from-top-2 duration-150">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-[#fb7800] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                <span x-text="selectedIds.length"></span>
            </div>
            <div class="text-xs">
                <span class="font-bold"><span x-text="selectedIds.length"></span> category(ies) selected</span>
                <span class="text-white/60 text-[11px] hidden sm:inline ml-1">• Ready for bulk operations</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="selectedIds = []; selectAll = false" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors cursor-pointer">
                Deselect All
            </button>
            <button type="button" @click="bulkDeleteConfirmOpen = true" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1 shadow-xs transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                <span>Delete Selected</span>
            </button>
        </div>
    </div>

    <!-- Category Hierarchy Table (Max 15 items per page) -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-3 px-4">Category Name</th>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Parent Category</th>
                        <th class="py-3 px-4 text-center">Linked Products</th>
                        <th class="py-3 px-4 text-center">Costing Method</th>
                        <th class="py-3 px-4 text-center">Valuation</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-[#f8f9ff] transition-colors" :class="selectedIds.includes({{ $cat->id }}) ? 'bg-[#eff4ff]/60' : ''">
                        <td class="py-3 px-3 text-center">
                            <input type="checkbox" :value="{{ $cat->id }}" x-model="selectedIds" @change="updateSelectAllState()" class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4 cursor-pointer">
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-[#001849] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-[#fb7800]">folder</span>
                                <span>{{ $cat->name }}</span>
                            </div>
                            @if($cat->description)
                                <span class="text-[11px] text-[#757681] line-clamp-1 mt-0.5">{{ $cat->description }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono font-semibold text-[#0d2c6c]">
                            {{ $cat->code ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-[#757681]">
                            @if($cat->parent)
                                <a href="{{ route('configuration.categories', ['parent_id' => $cat->parent_id]) }}" class="px-2 py-0.5 rounded text-[11px] bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] font-medium border border-[#dce9ff] transition-colors inline-block" title="Filter by this parent">
                                    {{ $cat->parent->name }}
                                </a>
                            @else
                                <span class="text-[#757681]/60 italic">Root Category</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('inventory.index', ['category_id' => $cat->id]) }}" class="inline-flex items-center gap-1 font-mono font-bold text-[#0d2c6c] hover:underline bg-[#eff4ff] px-2.5 py-0.5 rounded-full border border-[#dce9ff]">
                                <span class="material-symbols-outlined text-[13px]">inventory_2</span>
                                <span>{{ $cat->products_count ?? 0 }} Products</span>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff] uppercase">
                                {{ match($cat->costing_method) { 'fifo' => 'FIFO', 'standard' => 'Standard', default => 'AVCO (Average)' } }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $cat->valuation_method === 'automated' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($cat->valuation_method ?? 'automated') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="
                                    editData = {
                                        id: {{ $cat->id }},
                                        name: '{{ addslashes($cat->name) }}',
                                        code: '{{ addslashes($cat->code ?? '') }}',
                                        parent_id: '{{ $cat->parent_id ?? '' }}',
                                        costing_method: '{{ $cat->costing_method ?? 'average' }}',
                                        valuation_method: '{{ $cat->valuation_method ?? 'automated' }}',
                                        description: '{{ addslashes($cat->description ?? '') }}'
                                    };
                                    editModalOpen = true;
                                " class="p-1.5 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] transition-colors cursor-pointer" title="Edit Category">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                </button>

                                <form action="{{ route('configuration.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'? Linked products will be unassigned.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors cursor-pointer" title="Delete Category">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-[#757681]">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[#757681]/50 text-[36px]">folder_off</span>
                                <span>No product categories match your search criteria.</span>
                                @if($search || $parentId || $costingMethod || $valuationMethod)
                                    <a href="{{ route('configuration.categories') }}" class="text-xs text-[#0d2c6c] font-semibold hover:underline mt-1">
                                        Clear search &amp; filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls (Max 15 per page) -->
        <div class="px-5 py-4 bg-[#eff4ff]/50 border-t border-[#e5eeff] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-[#757681]">
            <div>
                Showing <strong>{{ $categories->firstItem() ?? 0 }}</strong> to <strong>{{ $categories->lastItem() ?? 0 }}</strong> of <strong>{{ $categories->total() }}</strong> categories (15 / page)
            </div>
            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- BULK DELETE CONFIRMATION MODAL -->
    <div x-show="bulkDeleteConfirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in duration-100">
        <div @click.away="bulkDeleteConfirmOpen = false" class="bg-white rounded-2xl border border-[#e5eeff] shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
            <div class="flex items-center gap-3 text-rose-600">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                </div>
                <div>
                    <h3 class="font-display font-bold text-[#001849] text-base">Bulk Delete Categories</h3>
                    <p class="text-xs text-[#757681]">Confirm deleting selected categories</p>
                </div>
            </div>

            <p class="text-xs text-[#444650] leading-relaxed">
                You are about to delete <strong class="text-rose-600" x-text="selectedIds.length"></strong> selected category(ies). Products belonging to these categories will have their category unassigned.
            </p>

            <form action="{{ route('configuration.categories.bulk-destroy') }}" method="POST" class="pt-2 flex items-center justify-end gap-2">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button" @click="bulkDeleteConfirmOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Yes, Delete <span x-text="selectedIds.length"></span> Categories</span>
                </button>
            </form>
        </div>
    </div>

    <!-- CREATE CATEGORY MODAL -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in duration-100">
        <div @click.away="createModalOpen = false" class="bg-white rounded-2xl border border-[#e5eeff] shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5eeff] flex items-center justify-between bg-[#eff4ff]/60">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#fb7800] text-[20px]">create_new_folder</span>
                    <h3 class="font-display font-bold text-[#001849] text-base">Create Product Category</h3>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-[#757681] hover:text-[#001849] p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="{{ route('configuration.categories.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Category Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Raw Materials, Greige Yarn, Finished Goods..." 
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Category Code / Prefix</label>
                        <input type="text" name="code" placeholder="e.g. CAT-RAW-YARN" 
                            class="w-full font-mono uppercase px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Parent Category</label>
                        <select name="parent_id" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                            <option value="">-- None (Top Level) --</option>
                            @foreach($parentCategories as $p)
                                <option value="{{ $p->id }}">{{ $p->complete_name ?? $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Costing Method <span class="text-rose-600">*</span></label>
                        <select name="costing_method" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                            <option value="average" selected>Average Cost (AVCO)</option>
                            <option value="fifo">First In First Out (FIFO)</option>
                            <option value="standard">Standard Price</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Inventory Valuation <span class="text-rose-600">*</span></label>
                        <select name="valuation_method" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                            <option value="automated" selected>Automated (Real-time)</option>
                            <option value="manual">Manual (Periodic)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Description / Notes</label>
                    <textarea name="description" rows="2" placeholder="Optional category description or valuation notes..." 
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-[#e5eeff] flex items-center justify-end gap-2">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold shadow-xs cursor-pointer">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in duration-100">
        <div @click.away="editModalOpen = false" class="bg-white rounded-2xl border border-[#e5eeff] shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5eeff] flex items-center justify-between bg-[#eff4ff]/60">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0d2c6c] text-[20px]">edit_note</span>
                    <h3 class="font-display font-bold text-[#001849] text-base">Edit Product Category</h3>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-[#757681] hover:text-[#001849] p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form :action="'{{ url('configuration/categories') }}/' + editData.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Category Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" x-model="editData.name" required 
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Category Code / Prefix</label>
                        <input type="text" name="code" x-model="editData.code" 
                            class="w-full font-mono uppercase px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Parent Category</label>
                        <select name="parent_id" x-model="editData.parent_id" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                            <option value="">-- None (Top Level) --</option>
                            @foreach($parentCategories as $p)
                                <option value="{{ $p->id }}" x-show="editData.id != {{ $p->id }}">{{ $p->complete_name ?? $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Costing Method <span class="text-rose-600">*</span></label>
                        <select name="costing_method" x-model="editData.costing_method" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                            <option value="average">Average Cost (AVCO)</option>
                            <option value="fifo">First In First Out (FIFO)</option>
                            <option value="standard">Standard Price</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Inventory Valuation <span class="text-rose-600">*</span></label>
                        <select name="valuation_method" x-model="editData.valuation_method" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                            <option value="automated">Automated (Real-time)</option>
                            <option value="manual">Manual (Periodic)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Description / Notes</label>
                    <textarea name="description" x-model="editData.description" rows="2" 
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-[#e5eeff] flex items-center justify-end gap-2">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold shadow-xs cursor-pointer">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

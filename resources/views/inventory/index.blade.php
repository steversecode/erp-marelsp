@extends('layouts.app')

@section('title', 'Products & SKU Master Data')

@section('content')
<div x-data="{ 
    bulkDeleteConfirmOpen: false,
    selectedIds: [],
    selectAll: false,
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedIds = [{{ $items->pluck('model.id')->implode(',') }}];
        } else {
            this.selectedIds = [];
        }
    },
    updateSelectAllState() {
        const pageIds = [{{ $items->pluck('model.id')->implode(',') }}];
        this.selectAll = pageIds.length > 0 && pageIds.every(id => this.selectedIds.includes(id));
    }
}" class="space-y-5 animate-in fade-in duration-150 relative">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>PRODUCT MASTER CATALOG</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Products &amp; SKU (Odoo WMS)
            </h2>
            <p class="text-xs text-[#757681]">
                Master data produk (Total: <strong>{{ $totalProductsCount ?? $items->total() }}</strong> item aktif), stok on-hand, safety buffer, dan valuasi persediaan
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('products.create') }}" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add_box</span>
                <span>+ New Product</span>
            </a>
            <a href="{{ route('stock-in.po.index') }}" class="px-4 py-2 rounded-lg bg-[#fb7800] hover:bg-[#994700] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>+ Purchase (PO)</span>
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
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

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined text-[#757681] text-[18px] absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by SKU, barcode, product name, or category..." 
                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
            </div>

            <select name="type" class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                <option value="">All Product Types</option>
                <option value="goods" {{ $type === 'goods' ? 'selected' : '' }}>Goods</option>
                <option value="service" {{ $type === 'service' ? 'selected' : '' }}>Service</option>
                <option value="combo" {{ $type === 'combo' ? 'selected' : '' }}>Combo</option>
            </select>

            <!-- Searchable Category Filter -->
            <div x-data="{ 
                open: false, 
                searchKeyword: '', 
                selectedVal: '{{ $categoryId ?? '' }}',
                selectedText: '{{ $categories->firstWhere('id', $categoryId)?->complete_name ?? $categories->firstWhere('id', $categoryId)?->name ?? 'All Categories' }}',
                items: [
                    { id: '', name: 'All Categories' },
                    @foreach($categories as $c)
                        { id: '{{ $c->id }}', name: '{{ addslashes($c->complete_name ?? $c->name) }}' },
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
                    $refs.hiddenCatInput.value = item.id;
                }
            }" class="relative min-w-[170px]">
                <input type="hidden" name="category_id" x-ref="hiddenCatInput" :value="selectedVal">
                <button type="button" @click="open = !open" class="w-full px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none flex items-center justify-between text-left cursor-pointer">
                    <span class="truncate" x-text="selectedText"></span>
                    <span class="material-symbols-outlined text-[16px] text-[#757681] shrink-0">arrow_drop_down</span>
                </button>

                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-64 bg-white rounded-xl border border-[#e5eeff] shadow-xl z-50 overflow-hidden animate-in fade-in duration-100">
                    <div class="p-2 border-b border-[#eff4ff] bg-[#f8f9ff]">
                        <div class="relative">
                            <span class="material-symbols-outlined text-[#757681] text-[15px] absolute left-2 top-1/2 -translate-y-1/2">search</span>
                            <input type="text" x-model="searchKeyword" placeholder="Filter categories..." class="w-full pl-7 pr-2 py-1 rounded-md bg-white border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                        </div>
                    </div>
                    <div class="max-h-52 overflow-y-auto divide-y divide-[#eff4ff] text-xs">
                        <template x-for="item in filteredItems" :key="item.id">
                            <button type="button" @click="choose(item)" class="w-full px-3 py-2 text-left hover:bg-[#eff4ff] flex items-center justify-between transition-colors cursor-pointer" :class="selectedVal === item.id ? 'bg-[#eff4ff] font-bold text-[#0d2c6c]' : 'text-[#001849]'">
                                <span class="truncate" x-text="item.name"></span>
                                <span x-show="selectedVal === item.id" class="material-symbols-outlined text-[#0d2c6c] text-[15px]">check</span>
                            </button>
                        </template>
                        <div x-show="filteredItems.length === 0" class="p-3 text-center text-[#757681] text-xs">
                            No matching category
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-4 py-2 rounded-lg bg-[#001849] text-white text-xs font-semibold hover:bg-[#0d2c6c] transition-colors cursor-pointer">
                Filter
            </button>
            @if($search || $type || !empty($categoryId) || !empty($category))
                <a href="{{ route('inventory.index') }}" class="px-3 py-2 rounded-lg bg-[#eff4ff] text-[#757681] text-xs font-semibold hover:bg-[#dce9ff] transition-colors" title="Reset filters">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('inventory.moves') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] text-xs font-semibold hover:bg-[#dce9ff] flex items-center gap-1.5 shrink-0 transition-colors">
            <span class="material-symbols-outlined text-[16px]">bar_chart</span>
            <span>Stock Moves Ledger</span>
        </a>
    </div>

    <!-- Floating Bulk Actions Bar (Contextual when product checkboxes are checked) -->
    <div x-show="selectedIds.length > 0" x-cloak style="display: none;" class="sticky top-4 z-40 bg-[#001849] text-white p-3.5 rounded-xl shadow-lg border border-[#0d2c6c] flex flex-wrap items-center justify-between gap-3 animate-in slide-in-from-top-2 duration-150">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-[#fb7800] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                <span x-text="selectedIds.length"></span>
            </div>
            <div class="text-xs">
                <span class="font-bold"><span x-text="selectedIds.length"></span> product(s) selected</span>
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

    <!-- Clean Single-Line SKU Table (Max 20 items per page) -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-3 px-3 w-12 text-center whitespace-nowrap">Image</th>
                        <th class="py-3 px-4 whitespace-nowrap">Internal Ref</th>
                        <th class="py-3 px-4 whitespace-nowrap">Product Name</th>
                        <th class="py-3 px-3 whitespace-nowrap">Type</th>
                        <th class="py-3 px-3 whitespace-nowrap">Category</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">On Hand</th>
                        <th class="py-3 px-3 text-right whitespace-nowrap">Min Stock</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Cost</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Valuation</th>
                        <th class="py-3 px-3 text-center whitespace-nowrap">Status</th>
                        <th class="py-3 px-3 text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($items as $item)
                    <tr class="hover:bg-[#f8f9ff] transition-colors" :class="selectedIds.includes({{ $item['model']->id }}) ? 'bg-[#eff4ff]/60' : ''">
                        <!-- Checkbox -->
                        <td class="py-2.5 px-3 text-center">
                            <input type="checkbox" :value="{{ $item['model']->id }}" x-model="selectedIds" @change="updateSelectAllState()" class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4 cursor-pointer">
                        </td>

                        <!-- Thumbnail Image -->
                        <td class="py-2.5 px-3 text-center">
                            <div class="w-8 h-8 rounded-lg bg-[#eff4ff] border border-[#dce9ff] flex items-center justify-center overflow-hidden mx-auto shadow-2xs">
                                @if($item['model']->image_url)
                                    <img src="{{ $item['model']->image_url }}" alt="{{ $item['model']->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-[16px] text-[#757681]/40">image</span>
                                @endif
                            </div>
                        </td>

                        <!-- Internal Reference (Single Line) -->
                        <td class="py-2.5 px-4 font-mono font-bold whitespace-nowrap text-[#0d2c6c]">
                            <a href="{{ route('products.show', $item['model']->id) }}" class="hover:underline">
                                {{ $item['model']->code }}
                            </a>
                        </td>

                        <!-- Product Name (Single Line) -->
                        <td class="py-2.5 px-4 font-medium text-[#0b1c30] whitespace-nowrap">
                            <a href="{{ route('products.show', $item['model']->id) }}" class="hover:text-[#0d2c6c] hover:underline" title="{{ $item['model']->description ?? $item['model']->name }}">
                                {{ $item['model']->name }}
                            </a>
                        </td>

                        <!-- Product Type (Single Line Badge) -->
                        <td class="py-2.5 px-3 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                                {{ $item['model']->type_label }}
                            </span>
                        </td>

                        <!-- Category (Single Line) -->
                        <td class="py-2.5 px-3 whitespace-nowrap text-[#757681]">
                            {{ $item['model']->category_name }}
                        </td>

                        <!-- On Hand (Single Line - Clickable to Location Breakdown) -->
                        <td class="py-2.5 px-4 text-right font-bold text-xs tabular-nums whitespace-nowrap">
                            <a href="{{ route('products.on-hand', $item['model']->id) }}" class="text-[#0d2c6c] hover:underline hover:text-[#fb7800] transition-colors" title="View location breakdown & update quantity">
                                {{ number_format($item['current_stock'], 2) }} <span class="text-[11px] font-normal text-[#757681]">{{ $item['model']->uom }}</span>
                            </a>
                        </td>

                        <!-- Min Stock Buffer (Single Line) -->
                        <td class="py-2.5 px-3 text-right text-[#757681] tabular-nums whitespace-nowrap text-xs">
                            {{ number_format($item['min_stock'], 2) }} {{ $item['model']->uom }}
                        </td>

                        <!-- Cost Price (Single Line) -->
                        <td class="py-2.5 px-4 text-right font-mono text-xs text-[#0b1c30] whitespace-nowrap">
                            Rp {{ number_format($item['model']->cost_price, 0, ',', '.') }}
                        </td>

                        <!-- Total Valuation (Single Line) -->
                        <td class="py-2.5 px-4 text-right font-bold font-mono text-xs text-[#001849] whitespace-nowrap">
                            Rp {{ number_format($item['valuation'], 0, ',', '.') }}
                        </td>

                        <!-- Status Badge (Single Line) -->
                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            @if($item['is_low_stock'])
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ffdad6] text-[#ba1a1a]">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Available
                                </span>
                            @endif
                        </td>

                        <!-- Quick Actions (Single Line) -->
                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('products.show', $item['model']->id) }}" class="p-1 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                </a>
                                <a href="{{ route('products.edit', $item['model']->id) }}" class="p-1 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#001849] transition-colors" title="Edit Product">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                </a>
                                <form action="{{ route('products.destroy', $item['model']->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive/delete product [{{ $item['model']->code }}] {{ $item['model']->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors cursor-pointer" title="Archive / Delete">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-10 text-center text-[#757681]">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[#757681]/50 text-[36px]">inventory_2</span>
                                <span>No product records match the specified criteria.</span>
                                @if($search || $type || !empty($categoryId) || !empty($category))
                                    <a href="{{ route('inventory.index') }}" class="text-xs text-[#0d2c6c] font-semibold hover:underline mt-1">
                                        Clear filters &amp; search
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls (Max 20 per page) -->
        <div class="px-5 py-4 bg-[#eff4ff]/50 border-t border-[#e5eeff] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-[#757681]">
            <div>
                Showing <strong>{{ $items->firstItem() ?? 0 }}</strong> to <strong>{{ $items->lastItem() ?? 0 }}</strong> of <strong>{{ $items->total() }}</strong> products (20 / page)
            </div>
            <div>
                {{ $items->links() }}
            </div>
        </div>
    </div>

    <!-- BULK DELETE CONFIRMATION MODAL -->
    <div x-show="bulkDeleteConfirmOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in duration-100">
        <div @click.away="bulkDeleteConfirmOpen = false" class="bg-white rounded-2xl border border-[#e5eeff] shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
            <div class="flex items-center gap-3 text-rose-600">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                </div>
                <div>
                    <h3 class="font-display font-bold text-[#001849] text-base">Bulk Archive / Delete Products</h3>
                    <p class="text-xs text-[#757681]">Confirm archiving selected products</p>
                </div>
            </div>

            <p class="text-xs text-[#444650] leading-relaxed">
                You are about to archive/delete <strong class="text-rose-600" x-text="selectedIds.length"></strong> selected product(s). They will be hidden from active inventory stock and catalog.
            </p>

            <form action="{{ route('products.bulk-destroy') }}" method="POST" class="pt-2 flex items-center justify-end gap-2">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button" @click="bulkDeleteConfirmOpen = false" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Yes, Archive <span x-text="selectedIds.length"></span> Products</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

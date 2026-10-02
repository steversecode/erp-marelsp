@extends('layouts.app')

@section('title', 'New Product')

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
            <span class="font-bold text-[#001849]">New Product</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                Discard
            </a>
            <button type="submit" form="productForm" class="px-5 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm cursor-pointer transition-colors">
                <span class="material-symbols-outlined text-[16px]">save</span>
                <span>Save</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="productForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Main Product Card (Odoo Master Form Style) -->
        <div class="bg-white rounded-2xl border border-[#e5eeff] shadow-sm p-6 space-y-6">
            <!-- Header: Title, Image Box, and Transaction Checkboxes -->
            <div class="space-y-4 pb-6 border-b border-[#eff4ff]">
                <div class="flex flex-col-reverse md:flex-row md:items-start justify-between gap-6">
                    <!-- Left: Product Name & Flags -->
                    <div class="flex-1 space-y-3">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-[#757681] block mb-1">
                                Product Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Cotton Combed 30s Greige Yarn, Jet Black T-Shirt L..." 
                                class="w-full text-lg sm:text-xl font-display font-bold px-4 py-2.5 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-[#001849] focus:ring-2 focus:ring-[#0d2c6c] focus:bg-white outline-none transition-all placeholder:text-[#757681]/40 placeholder:font-normal">
                        </div>

                        <!-- Odoo Transaction Flags -->
                        <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-1 text-xs font-medium text-[#0b1c30]">
                            <label class="flex items-center gap-2 cursor-pointer hover:text-[#0d2c6c]">
                                <input type="checkbox" name="can_be_sold" value="1" {{ old('can_be_sold', true) ? 'checked' : '' }} class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4">
                                <span>Can be Sold</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer hover:text-[#0d2c6c]">
                                <input type="checkbox" name="can_be_purchased" value="1" {{ old('can_be_purchased', true) ? 'checked' : '' }} class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4">
                                <span>Can be Purchased</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer hover:text-[#0d2c6c]">
                                <input type="checkbox" name="can_be_manufactured" value="1" {{ old('can_be_manufactured', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4">
                                <span>Can be Manufactured</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer hover:text-[#0d2c6c]">
                                <input type="checkbox" name="can_be_subcontracted" value="1" {{ old('can_be_subcontracted', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c] focus:ring-[#0d2c6c] w-4 h-4">
                                <span>Can be Subcontracted</span>
                            </label>
                        </div>
                    </div>

                    <!-- Right: Product Image Upload Box (Odoo Avatar Style) & Active Switch -->
                    <div class="flex flex-col items-center sm:items-end gap-3 shrink-0">
                        <div class="flex items-center gap-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                <span class="ml-2 text-xs font-semibold text-[#0b1c30]">Active</span>
                            </label>
                        </div>

                        <!-- Image Uploader Widget -->
                        <div class="relative group w-28 h-28 sm:w-32 sm:h-32 rounded-2xl border-2 border-dashed border-[#c5c6d2] hover:border-[#0d2c6c] bg-[#eff4ff] flex flex-col items-center justify-center overflow-hidden cursor-pointer transition-all shadow-2xs">
                            <img id="imagePreview" src="#" alt="Product Preview" class="hidden w-full h-full object-cover">
                            
                            <div id="imagePlaceholder" class="flex flex-col items-center justify-center text-center p-2 text-[#757681] group-hover:text-[#001849]">
                                <span class="material-symbols-outlined text-[32px]">photo_camera</span>
                                <span class="text-[10px] font-semibold mt-1">Upload Image</span>
                                <span class="text-[9px] text-[#757681]/70">PNG, JPG, WEBP</span>
                            </div>

                            <input type="file" name="image" id="imageInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewProductImage(event)">
                            
                            <button type="button" id="removeImageBtn" onclick="clearProductImage(event)" class="hidden absolute top-1.5 right-1.5 p-1 rounded-full bg-black/60 text-white hover:bg-rose-600 transition-colors shadow" title="Remove image">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Odoo-Style Tabs Navigation -->
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
                    <button type="button" @click="activeTab = 'purchase'" :class="activeTab === 'purchase' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                        <span>Purchase</span>
                    </button>
                    <button type="button" @click="activeTab = 'accounting'" :class="activeTab === 'accounting' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-[#757681] hover:text-[#001849] font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                        <span>Accounting</span>
                    </button>
                </div>

                <!-- TAB 1: GENERAL INFORMATION -->
                <div x-show="activeTab === 'general'" class="space-y-5 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Product Type <span class="text-rose-600">*</span></label>
                                <select name="type" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                    <option value="goods" {{ old('type', 'goods') == 'goods' ? 'selected' : '' }}>Goods</option>
                                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="combo" {{ old('type') == 'combo' ? 'selected' : '' }}>Combo</option>
                                </select>
                                <span class="text-[10px] text-[#757681] mt-0.5 block">Goods are physical, storable products tracked in inventory.</span>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Invoicing Policy <span class="text-rose-600">*</span></label>
                                <select name="invoicing_policy" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                    <option value="ordered" {{ old('invoicing_policy', 'ordered') == 'ordered' ? 'selected' : '' }}>Ordered quantities</option>
                                    <option value="delivered" {{ old('invoicing_policy') == 'delivered' ? 'selected' : '' }}>Delivered quantities</option>
                                </select>
                                <span class="text-[10px] text-[#757681] mt-0.5 block">Determines whether invoicing is based on ordered or delivered quantities.</span>
                            </div>

                            <!-- Searchable Product Category Combobox -->
                            <div x-data="{ 
                                open: false, 
                                searchKeyword: '', 
                                selectedVal: '{{ old('category_id') }}',
                                selectedText: '{{ $categories->firstWhere('id', old('category_id'))?->complete_name ?? $categories->firstWhere('id', old('category_id'))?->name ?? '-- Select Category --' }}',
                                items: [
                                    { id: '', name: '-- None / Unassigned --' },
                                    @foreach($categories as $cat)
                                        { id: '{{ $cat->id }}', name: '{{ addslashes($cat->complete_name ?? $cat->name) }}' },
                                    @endforeach
                                ],
                                get filteredItems() {
                                    if (!this.searchKeyword) return this.items;
                                    return this.items.filter(i => i.name.toLowerCase().includes(this.searchKeyword.toLowerCase()));
                                },
                                choose(item) {
                                    this.selectedVal = item.id;
                                    this.selectedText = item.id ? item.name : '-- Select Category --';
                                    this.open = false;
                                    $refs.hiddenCategoryInput.value = item.id;
                                }
                            }" class="relative">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-semibold text-[#0b1c30]">Product Category</label>
                                    <a href="{{ route('configuration.categories') }}" target="_blank" class="text-[11px] text-[#0d2c6c] hover:underline flex items-center gap-1 font-medium">
                                        <span class="material-symbols-outlined text-[13px]">add_circle</span>
                                        <span>Manage / Add Category</span>
                                    </a>
                                </div>

                                <input type="hidden" name="category_id" x-ref="hiddenCategoryInput" :value="selectedVal">

                                <button type="button" @click="open = !open" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-medium text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none flex items-center justify-between text-left cursor-pointer">
                                    <span class="truncate" x-text="selectedText"></span>
                                    <span class="material-symbols-outlined text-[16px] text-[#757681] shrink-0">arrow_drop_down</span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-xl border border-[#e5eeff] shadow-xl z-50 overflow-hidden animate-in fade-in duration-100">
                                    <div class="p-2 border-b border-[#eff4ff] bg-[#f8f9ff]">
                                        <div class="relative">
                                            <span class="material-symbols-outlined text-[#757681] text-[15px] absolute left-2.5 top-1/2 -translate-y-1/2">search</span>
                                            <input type="text" x-model="searchKeyword" placeholder="Search category (e.g. Yarn, Baju, Kain)..." class="w-full pl-8 pr-2.5 py-1.5 rounded-md bg-white border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
                                        </div>
                                    </div>

                                    <div class="max-h-56 overflow-y-auto divide-y divide-[#eff4ff] text-xs">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <button type="button" @click="choose(item)" class="w-full px-3.5 py-2 text-left hover:bg-[#eff4ff] flex items-center justify-between transition-colors cursor-pointer" :class="selectedVal == item.id ? 'bg-[#eff4ff] font-bold text-[#0d2c6c]' : 'text-[#001849]'">
                                                <span class="truncate" x-text="item.name"></span>
                                                <span x-show="selectedVal == item.id" class="material-symbols-outlined text-[#0d2c6c] text-[15px]">check</span>
                                            </button>
                                        </template>
                                        <div x-show="filteredItems.length === 0" class="p-3 text-center text-[#757681] text-xs">
                                            No matching categories found
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Responsible User -->
                            <div>
                                <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Responsible</label>
                                <select name="responsible_id" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('responsible_id', auth()->id()) == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-[10px] text-[#757681] mt-0.5 block">User responsible for product management and updates.</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Internal Reference (SKU) <span class="text-rose-600">*</span></label>
                                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="RAW-BNG-COT30S" 
                                        class="w-full font-mono uppercase px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Barcode (EAN-13 / QR)</label>
                                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="8991234567890" 
                                        class="w-full font-mono px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Unit of Measure (UoM) Search Combobox -->
                                <div class="relative" id="uomComboboxMain" 
                                    x-data="{
                                        open: false,
                                        search: '{{ old('uom', 'kg') }}',
                                        uoms: @js($uoms ?? []),
                                        get filtered() {
                                            if (!this.search || this.search.trim() === '') return this.uoms.slice(0, 8);
                                            const q = this.search.toLowerCase();
                                            return this.uoms.filter(u => 
                                                (u.name && u.name.toLowerCase().includes(q)) || 
                                                (u.category && u.category.name && u.category.name.toLowerCase().includes(q))
                                            ).slice(0, 8);
                                        },
                                        select(u) {
                                            this.search = u.name;
                                            this.open = false;
                                            document.getElementById('uomInputMain').value = u.name;
                                        }
                                    }"
                                    @click.outside="open = false"
                                    :class="open ? 'z-30' : 'z-10'">
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Unit of Measure (UoM) <span class="text-rose-600">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="uom" id="uomInputMain" x-model="search" @focus="open = true" @input="open = true" required placeholder="kg, cone, pcs, meter" 
                                            class="w-full px-3.5 py-2 pr-8 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                        <button type="button" @click="open = !open" class="material-symbols-outlined text-[18px] text-[#757681] absolute right-2.5 top-1/2 -translate-y-1/2 hover:text-[#001849] cursor-pointer">arrow_drop_down</button>
                                    </div>

                                    <!-- Dropdown Menu (Odoo Image 3 Style) -->
                                    <div x-show="open" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-lg border border-slate-200 shadow-xl z-50 overflow-hidden text-xs">
                                        <div class="max-h-52 overflow-y-auto divide-y divide-slate-100">
                                            <template x-for="u in filtered" :key="u.id">
                                                <div @click="select(u)" class="px-3.5 py-2 hover:bg-slate-100/80 cursor-pointer flex items-center justify-between text-slate-800 transition-colors">
                                                    <span class="font-medium" x-text="u.name"></span>
                                                    <span class="text-[11px] text-slate-400 font-mono" x-text="u.category ? u.category.name : ''"></span>
                                                </div>
                                            </template>
                                            <div x-show="filtered.length === 0" class="p-3 text-center text-slate-400 italic">
                                                Tidak ada UoM cocok.
                                            </div>
                                        </div>
                                        <div class="p-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                            <button type="button" @click.prevent="open = false; openUomSearchModal('main')" class="text-xs font-medium text-sky-700 hover:text-sky-900 hover:underline flex items-center gap-1 cursor-pointer">
                                                <span>Search More...</span>
                                            </button>
                                            <span class="text-[11px] text-slate-400 italic pr-1">Start typing...</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchase UoM Search Combobox -->
                                <div class="relative" id="uomComboboxPo" 
                                    x-data="{
                                        open: false,
                                        search: '{{ old('uom_po', 'kg') }}',
                                        uoms: @js($uoms ?? []),
                                        get filtered() {
                                            if (!this.search || this.search.trim() === '') return this.uoms.slice(0, 8);
                                            const q = this.search.toLowerCase();
                                            return this.uoms.filter(u => 
                                                (u.name && u.name.toLowerCase().includes(q)) || 
                                                (u.category && u.category.name && u.category.name.toLowerCase().includes(q))
                                            ).slice(0, 8);
                                        },
                                        select(u) {
                                            this.search = u.name;
                                            this.open = false;
                                            document.getElementById('uomInputPo').value = u.name;
                                        }
                                    }"
                                    @click.outside="open = false"
                                    :class="open ? 'z-30' : 'z-10'">
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Purchase UoM</label>
                                    <div class="relative">
                                        <input type="text" name="uom_po" id="uomInputPo" x-model="search" @focus="open = true" @input="open = true" placeholder="kg, ball, roll, pack" 
                                            class="w-full px-3.5 py-2 pr-8 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                        <button type="button" @click="open = !open" class="material-symbols-outlined text-[18px] text-[#757681] absolute right-2.5 top-1/2 -translate-y-1/2 hover:text-[#001849] cursor-pointer">arrow_drop_down</button>
                                    </div>

                                    <!-- Dropdown Menu (Odoo Image 3 Style) -->
                                    <div x-show="open" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-lg border border-slate-200 shadow-xl z-50 overflow-hidden text-xs">
                                        <div class="max-h-52 overflow-y-auto divide-y divide-slate-100">
                                            <template x-for="u in filtered" :key="u.id">
                                                <div @click="select(u)" class="px-3.5 py-2 hover:bg-slate-100/80 cursor-pointer flex items-center justify-between text-slate-800 transition-colors">
                                                    <span class="font-medium" x-text="u.name"></span>
                                                    <span class="text-[11px] text-slate-400 font-mono" x-text="u.category ? u.category.name : ''"></span>
                                                </div>
                                            </template>
                                            <div x-show="filtered.length === 0" class="p-3 text-center text-slate-400 italic">
                                                Tidak ada UoM cocok.
                                            </div>
                                        </div>
                                        <div class="p-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                            <button type="button" @click.prevent="open = false; openUomSearchModal('po')" class="text-xs font-medium text-sky-700 hover:text-sky-900 hover:underline flex items-center gap-1 cursor-pointer">
                                                <span>Search More...</span>
                                            </button>
                                            <span class="text-[11px] text-slate-400 italic pr-1">Start typing...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Sales Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#757681]">Rp</span>
                                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', 0) }}" placeholder="0" 
                                            class="w-full pl-9 pr-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono font-bold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Cost (HPP)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#757681]">Rp</span>
                                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', 0) }}" placeholder="0" 
                                            class="w-full pl-9 pr-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono font-bold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Internal Description</label>
                                <textarea name="description" rows="3" placeholder="Yarn specification, knit composition, grammage, count, or intended use..." 
                                    class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: INVENTORY -->
                <div x-show="activeTab === 'inventory'" x-cloak class="space-y-6 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left: Routes & Traceability (3 Odoo Tracking Options) -->
                        <div class="space-y-4">
                            <!-- Odoo Dynamic Warehouse Routes -->
                            <div class="p-4 rounded-xl bg-[#eff4ff] border border-[#dce9ff] space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-[#fb7800]">alt_route</span>
                                        Routes
                                    </span>
                                    <a href="{{ route('configuration.warehouses.index') }}" target="_blank" class="text-[11px] text-[#0d2c6c] hover:underline flex items-center gap-0.5 font-medium">
                                        <span class="material-symbols-outlined text-[13px]">warehouse</span>
                                        <span>Warehouse Routes</span>
                                    </a>
                                </div>
                                <div class="space-y-2 text-xs text-[#0b1c30]">
                                    <!-- Buy -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="route_buy" value="1" {{ old('route_buy', true) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span><strong>Buy</strong> — Procure from supplier via Purchase Order</span>
                                    </label>

                                    <!-- Manufacture -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="route_manufacture" value="1" {{ old('route_manufacture', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span><strong>Manufacture</strong> — Produced in-house via Manufacturing Order</span>
                                    </label>

                                    <!-- Replenish on Order (MTO) -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="route_mto" value="1" {{ old('route_mto', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span><strong>Replenish on Order (MTO)</strong> — Trigger PO/MO on demand</span>
                                    </label>

                                    <!-- Subcontracting -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="route_subcontract" value="1" {{ old('route_subcontract', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span><strong>Subcontracting</strong> — Outsourced to dyeing / finishing vendor</span>
                                    </label>

                                    <!-- Warehouse Resupply Routes (Online Shop, Marelika, etc.) -->
                                    @foreach($warehouses ?? [] as $wh)
                                        @foreach($wh->resupply_warehouses as $parentWh)
                                            <label class="flex items-center gap-2 cursor-pointer pt-1 border-t border-[#dce9ff]/60">
                                                <input type="checkbox" name="route_resupply_ids[]" value="{{ $parentWh->id }}_{{ $wh->id }}" 
                                                    {{ is_array(old('route_resupply_ids')) && in_array($parentWh->id . '_' . $wh->id, old('route_resupply_ids')) ? 'checked' : '' }} 
                                                    class="rounded text-[#0d2c6c]">
                                                <span><strong>Supply from {{ $parentWh->name }} to {{ $wh->name }}</strong> (Internal Transfer)</span>
                                            </label>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>

                            <!-- Odoo Traceability Section with 3 Options -->
                            <div class="p-4 rounded-xl bg-[#eff4ff] border border-[#dce9ff] space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#001849]">
                                        <input type="checkbox" name="track_inventory" value="1" {{ old('track_inventory', true) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span>Track Inventory</span>
                                    </label>
                                    <span class="text-[11px] text-[#757681]">Traceability</span>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Tracking Method</label>
                                    <select name="tracking" class="w-full px-3.5 py-2 rounded-lg bg-white border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                        <option value="serial" {{ old('tracking') == 'serial' ? 'selected' : '' }}>By Unique Serial Number</option>
                                        <option value="lot" {{ old('tracking') == 'lot' ? 'selected' : '' }}>By Lots</option>
                                        <option value="quantity" {{ old('tracking', 'quantity') == 'quantity' ? 'selected' : '' }}>By Quantity</option>
                                    </select>
                                </div>

                                <div class="pt-1">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-[#0b1c30]">
                                        <input type="checkbox" name="allow_negative_stock" value="1" {{ old('allow_negative_stock', false) ? 'checked' : '' }} class="rounded text-[#0d2c6c]">
                                        <span>Allow Negative Stock</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Reordering Rules & Logistics -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-white border border-[#dce9ff] shadow-xs space-y-3">
                                <span class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-700">rule</span>
                                    Reordering Rules
                                </span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[11px] font-semibold text-[#757681] block mb-1">Min Quantity</label>
                                        <input type="number" step="0.001" name="min_stock" value="{{ old('min_stock', 0) }}" 
                                            class="w-full px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold text-[#001849] outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-semibold text-[#757681] block mb-1">Max Quantity</label>
                                        <input type="number" step="0.001" name="max_stock" value="{{ old('max_stock', 0) }}" 
                                            class="w-full px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-semibold text-[#757681] block mb-1">Multiple Quantity</label>
                                        <input type="number" step="0.001" name="reorder_qty" value="{{ old('reorder_qty', 0) }}" 
                                            class="w-full px-2.5 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Weight (kg)</label>
                                    <input type="number" step="0.001" name="weight" value="{{ old('weight') }}" placeholder="0.000" 
                                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Volume (m³)</label>
                                    <input type="number" step="0.0001" name="volume" value="{{ old('volume') }}" placeholder="0.0000" 
                                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Vendor Lead Time (Days)</label>
                                    <input type="number" name="purchase_lead_time_days" value="{{ old('purchase_lead_time_days', 0) }}" 
                                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Manufacturing Lead Time (Days)</label>
                                    <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 0) }}" 
                                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: PURCHASE -->
                <div x-show="activeTab === 'purchase'" x-cloak class="space-y-4 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Preferred Vendor</label>
                            <input type="text" name="preferred_vendor" value="{{ old('preferred_vendor') }}" placeholder="e.g. PT Indorama Synthetics Tbk / CV Bintang Tex..." 
                                class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Vendor Product Code</label>
                            <input type="text" name="vendor_code" value="{{ old('vendor_code') }}" placeholder="SUPP-COT-30S" 
                                class="w-full font-mono px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Purchase Description</label>
                        <textarea name="purchase_description" rows="3" placeholder="Terms and specifications to be printed on purchase orders..." 
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">{{ old('purchase_description') }}</textarea>
                    </div>
                </div>

                <!-- TAB 4: ACCOUNTING -->
                <div x-show="activeTab === 'accounting'" x-cloak class="space-y-4 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Costing Method</label>
                            <select name="costing_method" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                <option value="average" {{ old('costing_method', 'average') == 'average' ? 'selected' : '' }}>Average Cost (AVCO)</option>
                                <option value="fifo" {{ old('costing_method') == 'fifo' ? 'selected' : '' }}>First In First Out (FIFO)</option>
                                <option value="standard" {{ old('costing_method') == 'standard' ? 'selected' : '' }}>Standard Price</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Inventory Valuation</label>
                            <select name="valuation_method" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                                <option value="automated" {{ old('valuation_method', 'automated') == 'automated' ? 'selected' : '' }}>Automated (Real-time)</option>
                                <option value="manual" {{ old('valuation_method') == 'manual' ? 'selected' : '' }}>Manual (Periodic)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-[#0b1c30] block mb-1">Internal Notes</label>
                        <textarea name="internal_notes" rows="3" placeholder="Accounting, audit, or warehouse internal notes..." 
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#0b1c30] focus:ring-1 focus:ring-[#0d2c6c] outline-none">{{ old('internal_notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Odoo Search: Unit of Measure Modal (Gambar 4 Style) -->
<div id="uomSearchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-4xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-100">
        <!-- Modal Header -->
        <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-semibold text-sm text-slate-800">
                Search: Unit of Measure
            </h3>
            <div class="flex items-center gap-1">
                <button type="button" onclick="closeUomSearchModal()" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-md transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        <!-- Search Bar & Counter (Gambar 4) -->
        <div class="px-4 py-2.5 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <div class="flex items-center bg-white border border-slate-300 rounded px-2.5 py-1 focus-within:border-slate-500 shadow-xs">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                    <input type="text" id="modalUomSearchInput" onkeyup="filterModalUoms()" placeholder="Search Unit of Measure..." class="w-full text-xs text-slate-800 outline-none bg-transparent">
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span id="modalUomRecordCount" class="font-mono">1-{{ count($uoms ?? []) }} / {{ count($uoms ?? []) }}</span>
                <div class="flex items-center border border-slate-200 rounded bg-white overflow-hidden shadow-2xs">
                    <button type="button" class="p-1 hover:bg-slate-100 text-slate-500 disabled:opacity-40"><span class="material-symbols-outlined text-[14px]">chevron_left</span></button>
                    <button type="button" class="p-1 hover:bg-slate-100 text-slate-500 disabled:opacity-40"><span class="material-symbols-outlined text-[14px]">chevron_right</span></button>
                </div>
            </div>
        </div>

        <!-- UoMs Table -->
        <div class="p-0 overflow-y-auto flex-1">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200 sticky top-0 z-10 uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center"><span class="material-symbols-outlined text-[16px] text-slate-400">star</span></th>
                        <th class="py-2.5 px-3 font-semibold">Unit of Measure</th>
                        <th class="py-2.5 px-3 font-semibold">Category</th>
                        <th class="py-2.5 px-3 text-center font-semibold">Type</th>
                        <th class="py-2.5 px-3 text-right font-semibold">Ratio</th>
                        <th class="py-2.5 px-3 text-right font-semibold">Rounding</th>
                    </tr>
                </thead>
                <tbody id="modalUomsTbody" class="divide-y divide-slate-100">
                    @foreach($uoms ?? [] as $u)
                    <tr class="modal-uom-row hover:bg-slate-50/80 cursor-pointer transition-colors text-slate-700" onclick="selectModalUom('{{ $u->name }}')">
                        <td class="py-2.5 px-3 text-center text-slate-300 hover:text-amber-500">
                            <span class="material-symbols-outlined text-[16px]">star_border</span>
                        </td>
                        <td class="py-2.5 px-3 font-semibold text-slate-900 whitespace-nowrap">
                            {{ $u->name }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">
                            {{ $u->category?->name ?? 'General' }}
                        </td>
                        <td class="py-2.5 px-3 text-center text-slate-500 whitespace-nowrap">
                            {{ ucfirst($u->uom_type) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-600 whitespace-nowrap">
                            {{ (float)$u->ratio }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-600 whitespace-nowrap">
                            {{ (float)$u->rounding }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('configuration.uom.create') }}" target="_blank" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>New UoM</span>
            </a>
            <button type="button" onclick="closeUomSearchModal()" class="px-4 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium text-xs rounded shadow-xs">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentUomTarget = 'main'; // 'main' or 'po'

    function openUomSearchModal(target) {
        currentUomTarget = target;
        const modal = document.getElementById('uomSearchModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.getElementById('modalUomSearchInput').value = '';
        filterModalUoms();
        document.getElementById('modalUomSearchInput').focus();
    }

    function closeUomSearchModal() {
        const modal = document.getElementById('uomSearchModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    function selectModalUom(uomName) {
        if (currentUomTarget === 'main') {
            const combobox = document.getElementById('uomComboboxMain');
            if (combobox && combobox._x_dataStack) {
                combobox._x_dataStack[0].select({ name: uomName });
            } else {
                document.getElementById('uomInputMain').value = uomName;
            }
        } else {
            const combobox = document.getElementById('uomComboboxPo');
            if (combobox && combobox._x_dataStack) {
                combobox._x_dataStack[0].select({ name: uomName });
            } else {
                document.getElementById('uomInputPo').value = uomName;
            }
        }
        closeUomSearchModal();
    }

    function filterModalUoms() {
        const query = document.getElementById('modalUomSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.modal-uom-row');
        let count = 0;
        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            if (text.includes(query)) {
                r.style.display = '';
                count++;
            } else {
                r.style.display = 'none';
            }
        });
        const total = rows.length;
        document.getElementById('modalUomRecordCount').textContent = `1-${count} / ${total}`;
    }

    function previewProductImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('imagePlaceholder');
                const removeBtn = document.getElementById('removeImageBtn');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                removeBtn.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function clearProductImage(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');
        const removeBtn = document.getElementById('removeImageBtn');

        input.value = '';
        preview.src = '#';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endpush

@extends('layouts.app')

@section('title', '[' . $bom->code . '] ' . $bom->name . ' - Bills of Materials')

@section('content')
<form method="POST" action="{{ route('manufacturing.boms.update', $bom->id) }}" id="bomForm" class="space-y-4 animate-in fade-in duration-150">
    @csrf
    @method('PUT')

    <!-- Odoo Top Action Bar & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-xl border border-[#e5eeff] shadow-xs">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('manufacturing.boms.create') }}" class="px-3 py-1.5 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>New</span>
            </a>
            <div class="h-5 w-px bg-[#e5eeff]"></div>
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('manufacturing.boms.index') }}" class="text-[#0d2c6c] hover:underline font-medium">Bills of Materials</a>
                <span class="text-[#757681]">/</span>
                <span class="font-bold text-[#001849] font-mono">{{ $bom->code }}</span>
            </div>
            <div class="flex items-center gap-1.5 ml-2">
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white text-xs font-semibold shadow-xs flex items-center gap-1 transition-all">
                    <span class="material-symbols-outlined text-[15px]">save</span>
                    <span>Save</span>
                </button>
                <a href="{{ route('manufacturing.boms.index') }}" class="px-3 py-1.5 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold">
                    Discard
                </a>
            </div>
        </div>

        <!-- Smart Stat Buttons (Odoo Standard) -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="openPerformanceModal()" class="px-3 py-1.5 rounded-lg border border-[#e5eeff] bg-white hover:bg-[#f8f9ff] text-[#0b1c30] text-xs font-medium flex items-center gap-2 shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[17px] text-[#0d2c6c]">schedule</span>
                <div class="text-left leading-tight">
                    <div class="text-[10px] text-[#757681]">Operations</div>
                    <div class="font-bold text-[11px]">Performance</div>
                </div>
            </button>
            <button type="button" onclick="openOverviewModal()" class="px-3 py-1.5 rounded-lg border border-[#e5eeff] bg-white hover:bg-[#f8f9ff] text-[#0b1c30] text-xs font-medium flex items-center gap-2 shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[17px] text-[#fb7800]">account_tree</span>
                <div class="text-left leading-tight">
                    <div class="text-[10px] text-[#757681]">Structure</div>
                    <div class="font-bold text-[11px]">BoM Overview</div>
                </div>
            </button>
        </div>
    </div>

    <!-- Main Container Grid: Form on Left + Chatter on Right -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
        <!-- Main Form Sheet (2 Cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl border border-[#e5eeff] shadow-xs p-5 space-y-6">
                <!-- Header Fields Grid (Odoo MRP Header) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <!-- Left Column: Product & Quantity -->
                    <div class="space-y-4">
                        <!-- Product Selection with Odoo Search Dropdown + Search More Modal -->
                        <div class="relative" id="mainProductSearchContainer" 
                            x-data="{
                                open: false,
                                search: '{{ addslashes('[' . ($bom->product->code ?? '') . '] ' . ($bom->product->name ?? '')) }}',
                                selectedId: '{{ $bom->product_id }}',
                                products: @js($products),
                                get filtered() {
                                    if (!this.search || this.search.trim() === '') return this.products.slice(0, 8);
                                    const q = this.search.toLowerCase();
                                    return this.products.filter(p => 
                                        (p.name && p.name.toLowerCase().includes(q)) || 
                                        (p.code && p.code.toLowerCase().includes(q))
                                    ).slice(0, 8);
                                },
                                selectProduct(p) {
                                    this.selectedId = p.id;
                                    this.search = `[${p.code}] ${p.name}`;
                                    this.open = false;
                                    document.getElementById('product_id_hidden').value = p.id;
                                    if (p.uom) {
                                        document.getElementById('uom').value = p.uom;
                                    }
                                }
                            }"
                            @click.outside="open = false"
                            :class="open ? 'z-30' : 'z-10'">
                            <label class="block text-xs font-bold text-[#ba1a1a] mb-1">
                                Product <span class="text-[#ba1a1a]">*</span>
                            </label>

                            <input type="hidden" name="product_id" id="product_id_hidden" :value="selectedId" required>

                            <div class="relative">
                                <input type="text" 
                                    x-model="search" 
                                    @focus="open = true" 
                                    @input="open = true" 
                                    placeholder="Ketik untuk mencari produk output..." 
                                    required
                                    class="w-full text-xs font-medium bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg py-2 pl-3 pr-8 text-[#0b1c30] focus:ring-1 focus:ring-[#fb7800] focus:border-[#fb7800] outline-none">
                                <button type="button" @click="open = !open" class="material-symbols-outlined text-[18px] text-[#757681] absolute right-2.5 top-1/2 -translate-y-1/2 hover:text-[#001849] cursor-pointer">arrow_drop_down</button>
                            </div>

                            <!-- Odoo Style Autocomplete Dropdown (Gambar 3) -->
                            <div x-show="open" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-lg border border-slate-200 shadow-xl z-50 overflow-hidden text-xs">
                                <div class="max-h-56 overflow-y-auto divide-y divide-slate-100">
                                    <template x-for="p in filtered" :key="p.id">
                                        <div @click="selectProduct(p)" class="px-3.5 py-2 hover:bg-slate-100/80 cursor-pointer flex items-center justify-between text-slate-800 transition-colors">
                                            <span class="truncate font-medium" x-text="`[${p.code}] ${p.name}`"></span>
                                        </div>
                                    </template>
                                    <div x-show="filtered.length === 0" class="p-3 text-center text-slate-400 italic">
                                        Tidak ada produk cocok.
                                    </div>
                                </div>

                                <!-- Odoo Search More... Action (Gambar 3) -->
                                <div class="p-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                    <button type="button" @click.prevent="open = false; openProductSearchModal('main')" class="text-xs font-medium text-sky-700 hover:text-sky-900 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>Search More...</span>
                                    </button>
                                    <span class="text-[11px] text-slate-400 italic pr-2">Start typing...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Variant -->
                        <div>
                            <label class="block text-xs font-semibold text-[#757681] mb-1">
                                Product Variant <span class="text-[#7e96dc] font-normal cursor-help" title="Varian spesifik jika menggunakan multi-atribut">(?)</span>
                            </label>
                            <input type="text" name="product_variant_info" value="Semua Varian Produk (Default)" class="w-full text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-2 text-[#757681]" readonly>
                        </div>

                        <!-- Quantity & UoM -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                Quantity <span class="text-[#7e96dc] font-normal cursor-help" title="Basis kuantitas produksi untuk BoM ini">(?)</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.00001" min="0.00001" name="quantity" id="quantity" value="{{ old('quantity', (float)$bom->quantity) }}" required class="w-32 text-xs font-mono font-bold bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-2 text-[#0b1c30] focus:ring-1 focus:ring-[#fb7800]">
                                <select name="uom" id="uom" required class="flex-1 text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-2 text-[#0b1c30]">
                                    @foreach($uoms as $u)
                                        <option value="{{ $u->name }}" {{ old('uom', $bom->uom) == $u->name ? 'selected' : '' }}>{{ $u->name }} ({{ $u->category?->name ?? 'General' }})</option>
                                    @endforeach
                                    <option value="pcs" {{ old('uom', $bom->uom) == 'pcs' ? 'selected' : '' }}>pcs</option>
                                    <option value="kg" {{ old('uom', $bom->uom) == 'kg' ? 'selected' : '' }}>kg</option>
                                    <option value="yard" {{ old('uom', $bom->uom) == 'yard' ? 'selected' : '' }}>yard</option>
                                    <option value="meter" {{ old('uom', $bom->uom) == 'meter' ? 'selected' : '' }}>meter</option>
                                    <option value="cone" {{ old('uom', $bom->uom) == 'cone' ? 'selected' : '' }}>cone</option>
                                    <option value="lusin" {{ old('uom', $bom->uom) == 'lusin' ? 'selected' : '' }}>lusin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Reference & BoM Type -->
                    <div class="space-y-4">
                        <!-- Reference (Code) -->
                        <div>
                            <label for="code" class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                Reference
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $bom->code) }}" required class="w-full text-xs font-mono font-bold bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-2 text-[#0b1c30] focus:ring-1 focus:ring-[#fb7800]">
                        </div>

                        <!-- BoM Name / Formula Title -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                Formula Description
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $bom->name) }}" required class="w-full text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-2 text-[#0b1c30] focus:ring-1 focus:ring-[#fb7800]">
                        </div>

                        <!-- BoM Type (Radio) -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0b1c30] mb-2">
                                BoM Type
                            </label>
                            <div class="space-y-2 bg-[#f8f9ff] p-3 rounded-lg border border-[#e5eeff]">
                                <label class="flex items-center gap-2 text-xs font-medium text-[#0b1c30] cursor-pointer">
                                    <input type="radio" name="type" value="normal" {{ old('type', $bom->type) === 'normal' ? 'checked' : '' }} class="text-[#0d2c6c] focus:ring-[#0d2c6c]">
                                    <span>Manufacture this product</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-[#0b1c30] cursor-pointer">
                                    <input type="radio" name="type" value="phantom" {{ old('type', $bom->type) === 'phantom' ? 'checked' : '' }} class="text-[#0d2c6c] focus:ring-[#0d2c6c]">
                                    <span>Kit</span>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="1">
                    </div>
                </div>

                <!-- Odoo Notebook / Tabs -->
                <div x-data="{ activeTab: 'components' }" class="pt-4 border-t border-[#e5eeff]">
                    <!-- Tab Headers -->
                    <div class="flex items-center gap-6 border-b border-[#e5eeff] pb-px text-xs font-semibold">
                        <button type="button" @click="activeTab = 'components'" :class="activeTab === 'components' ? 'text-[#0d2c6c] border-b-2 border-[#0d2c6c] pb-2 font-bold' : 'text-[#757681] hover:text-[#001849] pb-2'" class="transition-all">
                            Components
                        </button>
                        <button type="button" @click="activeTab = 'operations'" :class="activeTab === 'operations' ? 'text-[#0d2c6c] border-b-2 border-[#0d2c6c] pb-2 font-bold' : 'text-[#757681] hover:text-[#001849] pb-2'" class="transition-all">
                            Operations
                        </button>
                        <button type="button" @click="activeTab = 'byproducts'" :class="activeTab === 'byproducts' ? 'text-[#0d2c6c] border-b-2 border-[#0d2c6c] pb-2 font-bold' : 'text-[#757681] hover:text-[#001849] pb-2'" class="transition-all">
                            By-products
                        </button>
                        <button type="button" @click="activeTab = 'miscellaneous'" :class="activeTab === 'miscellaneous' ? 'text-[#0d2c6c] border-b-2 border-[#0d2c6c] pb-2 font-bold' : 'text-[#757681] hover:text-[#001849] pb-2'" class="transition-all">
                            Miscellaneous
                        </button>
                    </div>

                    @php
                        $bomInitialItems = $bom->items->map(function($it) {
                            return [
                                'uid' => 'item_' . $it->id,
                                'product_id' => $it->product_id,
                                'displayText' => $it->product ? '[' . $it->product->code . '] ' . $it->product->name : '',
                                'cones' => $it->cones,
                                'quantity' => number_format((float)$it->quantity, 4, '.', ''),
                                'uom' => $it->uom ?? $it->product?->uom ?? 'kg',
                                'wastage_percent' => number_format((float)($it->wastage_percent ?? 0), 1, '.', ''),
                                'notes' => $it->notes ?? '',
                                'open' => false,
                            ];
                        })->values();
                    @endphp

                    <!-- TAB 1: Components Table (Odoo Editable Grid) -->
                    <div x-show="activeTab === 'components'" class="pt-4 space-y-3" id="componentsManagerContainer" x-data="bomComponentsManager(@js($bomInitialItems))">
                        <div class="border border-[#e5eeff] rounded-lg min-h-[220px] bg-white">
                            <table class="w-full text-left text-xs border-collapse" id="componentsTable">
                                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold border-b border-[#e5eeff]">
                                    <tr>
                                        <th class="py-2.5 px-3 w-8 text-center">#</th>
                                        <th class="py-2.5 px-3 min-w-[260px]">Component (Raw Material) <span class="text-[#ba1a1a]">*</span></th>
                                        <th class="py-2.5 px-3 w-24 text-right">Cones</th>
                                        <th class="py-2.5 px-3 w-28 text-right">Quantity <span class="text-[#ba1a1a]">*</span></th>
                                        <th class="py-2.5 px-3 w-28">Unit of Measure</th>
                                        <th class="py-2.5 px-3 w-24 text-right">Wastage (%)</th>
                                        <th class="py-2.5 px-3 min-w-[150px]">Notes</th>
                                        <th class="py-2.5 px-2 w-10 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#eff4ff]">
                                    <template x-for="(item, index) in items" :key="item.uid">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-2 px-3 text-center text-[#757681] font-mono text-[11px]" x-text="index + 1"></td>
                                            <td class="py-2 px-3">
                                                <div class="relative" @click.outside="item.open = false" :class="item.open ? 'z-30' : 'z-10'">
                                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id" required>
                                                    <div class="relative">
                                                        <input type="text" 
                                                            x-model="item.displayText" 
                                                            @focus="item.open = true" 
                                                            @input="item.open = true; item.product_id = ''" 
                                                            placeholder="Ketik untuk mencari komponen..." 
                                                            required 
                                                            class="w-full text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg py-1.5 pl-2.5 pr-7 text-[#0b1c30] focus:ring-1 focus:ring-[#fb7800] outline-none">
                                                        <button type="button" @click="item.open = !item.open" class="material-symbols-outlined text-[16px] text-[#757681] absolute right-2 top-1/2 -translate-y-1/2 hover:text-[#001849] cursor-pointer">arrow_drop_down</button>
                                                    </div>

                                                    <!-- Dropdown List with Search More (Gambar 3) -->
                                                    <div x-show="item.open" x-cloak class="absolute left-0 mt-1 w-full bg-white rounded-lg border border-slate-200 shadow-xl z-50 overflow-hidden text-xs">
                                                        <div class="max-h-48 overflow-y-auto divide-y divide-slate-100">
                                                            <template x-for="p in getFilteredProducts(item.displayText)" :key="p.id">
                                                                <div @click="selectComponent(item, p)" class="px-3 py-1.5 hover:bg-slate-100/80 cursor-pointer flex items-center justify-between text-slate-800 transition-colors">
                                                                    <span class="truncate font-medium" x-text="'[' + p.code + '] ' + p.name"></span>
                                                                </div>
                                                            </template>
                                                            <div x-show="getFilteredProducts(item.displayText).length === 0" class="p-2.5 text-center text-slate-400 italic">
                                                                Tidak ada komponen cocok.
                                                            </div>
                                                        </div>
                                                        <div class="p-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                                            <button type="button" @click.prevent="item.open = false; openProductSearchModal(index)" class="text-xs font-medium text-sky-700 hover:text-sky-900 hover:underline flex items-center gap-1 cursor-pointer">
                                                                <span>Search More...</span>
                                                            </button>
                                                            <span class="text-[11px] text-slate-400 italic pr-1">Start typing...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                <input type="number" step="0.01" min="0" :name="`items[${index}][cones]`" x-model="item.cones" placeholder="0" class="w-full text-xs font-mono text-right bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-1.5 text-[#0b1c30]">
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                <input type="number" step="0.0001" min="0.0001" :name="`items[${index}][quantity]`" x-model="item.quantity" required class="w-full text-xs font-mono font-bold text-right bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-1.5 text-[#0b1c30]">
                                            </td>
                                            <td class="py-2 px-3">
                                                <input type="text" :name="`items[${index}][uom]`" x-model="item.uom" required class="w-full text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-1.5 text-[#0b1c30]">
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                <input type="number" step="0.1" min="0" max="100" :name="`items[${index}][wastage_percent]`" x-model="item.wastage_percent" class="w-full text-xs font-mono text-right bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-1.5 text-[#0b1c30]">
                                            </td>
                                            <td class="py-2 px-3">
                                                <input type="text" :name="`items[${index}][notes]`" x-model="item.notes" placeholder="Catatan spek..." class="w-full text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg p-1.5 text-[#0b1c30]">
                                            </td>
                                            <td class="py-2 px-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="p-1 rounded text-[#ba1a1a] hover:bg-[#ffebee] transition-colors" title="Hapus Baris">
                                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Add a Line / Catalog Controls -->
                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-3">
                                <button type="button" @click="addItem()" class="text-xs font-semibold text-[#0d2c6c] hover:text-[#001849] hover:underline flex items-center gap-1 py-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    <span>Add a line</span>
                                </button>
                                <span class="text-[#c5c6d2]">|</span>
                                <button type="button" @click="openProductSearchModal('component_catalog')" class="text-xs font-semibold text-[#0d2c6c] hover:text-[#001849] hover:underline flex items-center gap-1 py-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">grid_view</span>
                                    <span>Catalog</span>
                                </button>
                            </div>

                            <div class="text-xs text-[#757681] font-mono">
                                Total Est. Component Cost: <strong class="text-[#001849]">Rp {{ number_format($bom->total_cost, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Operations -->
                    <div x-show="activeTab === 'operations'" class="pt-4 space-y-3">
                        <div class="bg-[#f8f9ff] p-4 rounded-lg border border-[#e5eeff] space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#001849]">Rute &amp; Work Centers Manufaktur</span>
                                <span class="text-[10px] bg-[#eff4ff] text-[#0d2c6c] font-semibold px-2 py-0.5 rounded">Routings</span>
                            </div>
                            <div class="space-y-2 text-[#757681]">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-[#0d2c6c] text-white flex items-center justify-center font-bold text-[10px]">1</span>
                                    <span><strong>Proses Rajut / Knitting:</strong> Kapasitas mesin rajut bundar (Circular Knitting Machine).</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-[#0d2c6c] text-white flex items-center justify-center font-bold text-[10px]">2</span>
                                    <span><strong>Proses Dyeing / Pencelupan:</strong> Vendor Celup Subcon CV Bintang Tex.</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-[#0d2c6c] text-white flex items-center justify-center font-bold text-[10px]">3</span>
                                    <span><strong>Proses Jahit / Sewing &amp; Finishing:</strong> Lantai Produksi Garment Gedung Utama.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: By-products -->
                    <div x-show="activeTab === 'byproducts'" class="pt-4 space-y-3">
                        <div class="p-6 text-center text-[#757681] bg-[#f8f9ff] rounded-lg border border-[#e5eeff] text-xs">
                            <span class="material-symbols-outlined text-3xl text-[#c5c6d2] block mb-1">recycling</span>
                            <span class="font-medium">Tidak ada by-product tambahan untuk BoM ini.</span>
                            <p class="text-[10px] text-[#757681] mt-1">Sisa potongan kain / waste akan secara otomatis dicatat ke lokasi virtual <code>SCRAP-LOSS</code> saat MO selesai.</p>
                        </div>
                    </div>

                    <!-- TAB 4: Miscellaneous -->
                    <div x-show="activeTab === 'miscellaneous'" class="pt-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="space-y-3 bg-[#f8f9ff] p-3.5 rounded-lg border border-[#e5eeff]">
                                <label class="block font-bold text-[#001849]">Manufacturing Readiness</label>
                                <label class="flex items-center gap-2 font-medium text-[#0b1c30]">
                                    <input type="radio" name="ready_to_produce" value="all_available" {{ old('ready_to_produce', $bom->ready_to_produce) === 'all_available' ? 'checked' : '' }} class="text-[#0d2c6c]">
                                    <span>When all components are available</span>
                                </label>
                                <label class="flex items-center gap-2 font-medium text-[#0b1c30]">
                                    <input type="radio" name="ready_to_produce" value="asap" {{ old('ready_to_produce', $bom->ready_to_produce) === 'asap' ? 'checked' : '' }} class="text-[#0d2c6c]">
                                    <span>When components for 1st operation are available</span>
                                </label>
                            </div>

                            <div class="space-y-3 bg-[#f8f9ff] p-3.5 rounded-lg border border-[#e5eeff]">
                                <label class="block font-bold text-[#001849]">Flexible Consumption</label>
                                <select name="consumption" class="w-full text-xs bg-white border border-[#c5c6d2] rounded-lg p-2 text-[#0b1c30]">
                                    <option value="flexible" {{ old('consumption', $bom->consumption) === 'flexible' ? 'selected' : '' }}>Allowed (Bebas sesuai kebutuhan riil)</option>
                                    <option value="warning" {{ old('consumption', $bom->consumption) === 'warning' ? 'selected' : '' }}>Allowed with warning (Peringatan selisih BoM)</option>
                                    <option value="strict" {{ old('consumption', $bom->consumption) === 'strict' ? 'selected' : '' }}>Blocked (Ketat sesuai BoM)</option>
                                </select>
                                <div class="pt-2">
                                    <label class="block font-bold text-[#001849] mb-1">Manufacturing Lead Time (Days)</label>
                                    <input type="number" name="produce_delay" value="{{ old('produce_delay', $bom->produce_delay) }}" min="0" class="w-24 text-xs font-mono bg-white border border-[#c5c6d2] rounded-lg p-1.5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Odoo Chatter / History Log -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-[#e5eeff] shadow-xs p-4 space-y-4">
                <!-- Chatter Action Header -->
                <div class="flex items-center justify-between pb-3 border-b border-[#e5eeff]">
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-2.5 py-1 bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-[11px] font-bold rounded-lg flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">mail</span>
                            <span>Send message</span>
                        </button>
                        <button type="button" class="px-2.5 py-1 bg-[#f8f9ff] text-[#757681] hover:bg-[#eff4ff] text-[11px] font-bold rounded-lg flex items-center gap-1 border border-[#e5eeff]">
                            <span class="material-symbols-outlined text-[14px]">edit_note</span>
                            <span>Log note</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5 text-[#757681]">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span class="text-[10px] font-semibold">1</span>
                    </div>
                </div>

                <!-- Linked Manufacturing Orders -->
                @if($bom->manufacturingOrders->count() > 0)
                <div class="bg-[#eff4ff] p-3 rounded-lg border border-[#dce9ff] space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#0d2c6c] block">
                        Digunakan oleh {{ $bom->manufacturingOrders->count() }} MO
                    </span>
                    <div class="flex flex-wrap gap-1">
                        @foreach($bom->manufacturingOrders->take(5) as $mo)
                            <a href="{{ route('manufacturing.show', $mo->id) }}" class="px-2 py-0.5 bg-white text-[#0d2c6c] text-[10px] font-mono font-bold rounded border border-[#c5c6d2] hover:bg-[#dce9ff]">
                                {{ $mo->mo_number }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Activity Feed / History Log -->
                <div class="space-y-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#7e96dc]">
                        Audit Trail &amp; History
                    </div>

                    <!-- Creator Note -->
                    <div class="flex items-start gap-2.5 text-xs">
                        <div class="w-7 h-7 rounded-full bg-[#0d2c6c] text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                            {{ substr($bom->creator->name ?? 'Admin', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-[#001849] truncate">{{ $bom->creator->name ?? 'Bpk. Hendra Pratama' }}</span>
                                <span class="text-[10px] text-[#757681]">{{ $bom->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="text-[11px] text-[#757681] mt-0.5">
                                Created Bill of Materials [{{ $bom->code }}]
                            </div>
                        </div>
                    </div>

                    <!-- Updater Note (If updated) -->
                    @if($bom->updated_at != $bom->created_at)
                    <div class="flex items-start gap-2.5 text-xs pt-2 border-t border-[#eff4ff]">
                        <div class="w-7 h-7 rounded-full bg-[#fb7800] text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                            {{ substr($bom->updater->name ?? ($bom->creator->name ?? 'Admin'), 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-[#001849] truncate">{{ $bom->updater->name ?? ($bom->creator->name ?? 'Admin') }}</span>
                                <span class="text-[10px] text-[#757681]">{{ $bom->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="text-[11px] text-[#757681] mt-0.5">
                                Updated BoM components &amp; formula
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Odoo Search More Modal for Products & Components (Gambar 4) -->
<div id="productSearchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-4xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-100">
        <!-- Modal Header -->
        <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-semibold text-sm text-slate-800" id="searchModalTitle">
                Search: Product
            </h3>
            <div class="flex items-center gap-1">
                <button type="button" onclick="closeProductSearchModal()" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-md transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        <!-- Search Bar & Pagination Counter (Gambar 4) -->
        <div class="px-4 py-2.5 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <div class="flex items-center bg-white border border-slate-300 rounded px-2.5 py-1 focus-within:border-slate-500 shadow-xs">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                    <input type="text" id="modalProductSearchInput" onkeyup="filterModalProducts()" placeholder="Search..." class="w-full text-xs text-slate-800 outline-none bg-transparent">
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span id="modalRecordCount" class="font-mono">1-{{ count($rawMaterials) }} / {{ count($rawMaterials) }}</span>
                <div class="flex items-center border border-slate-200 rounded bg-white overflow-hidden shadow-2xs">
                    <button type="button" class="p-1 hover:bg-slate-100 text-slate-500 disabled:opacity-40"><span class="material-symbols-outlined text-[14px]">chevron_left</span></button>
                    <button type="button" class="p-1 hover:bg-slate-100 text-slate-500 disabled:opacity-40"><span class="material-symbols-outlined text-[14px]">chevron_right</span></button>
                </div>
            </div>
        </div>

        <!-- Odoo Products Table (Gambar 4) -->
        <div class="p-0 overflow-y-auto flex-1">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200 sticky top-0 z-10 uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center"><span class="material-symbols-outlined text-[16px] text-slate-400">star</span></th>
                        <th class="py-2.5 px-3 font-semibold">Internal Reference</th>
                        <th class="py-2.5 px-3 font-semibold">Name</th>
                        <th class="py-2.5 px-3 font-semibold">Product Type</th>
                        <th class="py-2.5 px-3 text-right font-semibold">Sales Price</th>
                        <th class="py-2.5 px-3 text-right font-semibold">Cost</th>
                        <th class="py-2.5 px-3 text-center font-semibold">Unit</th>
                    </tr>
                </thead>
                <tbody id="modalProductsTbody" class="divide-y divide-slate-100">
                    @foreach($rawMaterials as $mat)
                    <tr class="modal-product-row hover:bg-slate-50/80 cursor-pointer transition-colors text-slate-700" onclick="selectModalProduct('{{ $mat->id }}', '{{ addslashes($mat->name) }}', '{{ $mat->code }}', '{{ $mat->uom }}')">
                        <td class="py-2.5 px-3 text-center text-slate-300 hover:text-amber-500">
                            <span class="material-symbols-outlined text-[16px]">star_border</span>
                        </td>
                        <td class="py-2.5 px-3 font-mono font-semibold text-slate-900 whitespace-nowrap">
                            {{ $mat->code }}
                        </td>
                        <td class="py-2.5 px-3 font-medium text-slate-900 whitespace-nowrap">
                            {{ $mat->name }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">
                            {{ ucfirst(str_replace('_', ' ', $mat->type)) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-600 whitespace-nowrap">
                            {{ number_format((float)$mat->sale_price, 2, '.', '') }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-600 whitespace-nowrap">
                            {{ number_format((float)$mat->cost_price, 2, '.', '') }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-medium text-slate-500 whitespace-nowrap">
                            <span class="px-1.5 py-0.5 bg-slate-100 rounded text-[11px] font-mono">{{ $mat->uom }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('products.create') }}" target="_blank" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>New</span>
            </a>
            <button type="button" onclick="closeProductSearchModal()" class="px-4 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium text-xs rounded shadow-xs">
                Close
            </button>
        </div>
    </div>
</div>

<!-- BoM Overview Modal -->
<div id="overviewModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-3xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-100">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-[#001849] flex items-center gap-2">
                <span class="material-symbols-outlined text-[19px] text-[#fb7800]">account_tree</span>
                <span>BoM Structure &amp; Cost Overview: [{{ $bom->code }}] {{ $bom->product->name ?? $bom->name }}</span>
            </h3>
            <button type="button" onclick="closeOverviewModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-200">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div class="p-5 overflow-y-auto flex-1 space-y-4 text-xs">
            <div class="flex items-center justify-between bg-slate-50 p-3 rounded-lg border border-slate-200">
                <div>
                    <span class="text-[11px] text-[#757681]">Output Produksi (Basis):</span>
                    <div class="font-bold text-sm text-[#001849]">{{ (float)$bom->quantity }} {{ $bom->uom }}</div>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-[#757681]">Estimasi Total Biaya Komponen:</span>
                    <div class="font-bold text-sm text-[#fb7800] font-mono">Rp {{ number_format($bom->total_cost, 0, ',', '.') }}</div>
                </div>
            </div>

            <table class="w-full text-left border-collapse border border-slate-200 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 text-[11px] font-semibold text-[#757681]">
                    <tr>
                        <th class="p-2.5">Component</th>
                        <th class="p-2.5 text-right">Qty</th>
                        <th class="p-2.5 text-center">UoM</th>
                        <th class="p-2.5 text-right">Wastage</th>
                        <th class="p-2.5 text-right">Unit Cost</th>
                        <th class="p-2.5 text-right">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($bom->items as $item)
                    @php
                        $cost = (float)($item->product->cost_price ?? 0);
                        $wastageFactor = 1 + (($item->wastage_percent ?? 0) / 100);
                        $itemTotal = (float)$item->quantity * $cost * $wastageFactor;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 font-bold text-[#0b1c30]">
                            [{{ $item->product->code ?? '-' }}] {{ $item->product->name ?? 'Komponen' }}
                        </td>
                        <td class="p-2.5 text-right font-mono font-semibold">{{ (float)$item->quantity }}</td>
                        <td class="p-2.5 text-center font-medium text-[#757681]">{{ $item->uom }}</td>
                        <td class="p-2.5 text-right font-mono">{{ (float)$item->wastage_percent }}%</td>
                        <td class="p-2.5 text-right font-mono">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right font-mono font-bold text-[#001849]">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[11px] text-[#757681] italic">* Estimasi biaya didasarkan pada harga pokok (Cost Price) masing-masing bahan baku.</span>
            <button type="button" onclick="closeOverviewModal()" class="px-4 py-1.5 bg-[#0d2c6c] text-white font-semibold text-xs rounded-lg hover:bg-[#001849]">
                Tutup Overview
            </button>
        </div>
    </div>
</div>

<!-- Operations Performance Modal -->
<div id="performanceModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-100">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-[#001849] flex items-center gap-2">
                <span class="material-symbols-outlined text-[19px] text-[#0d2c6c]">schedule</span>
                <span>Operations Performance &amp; Routing</span>
            </h3>
            <button type="button" onclick="closePerformanceModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-200">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div class="p-5 space-y-3 text-xs">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 space-y-2">
                <div class="flex items-center justify-between font-bold text-[#001849]">
                    <span>Standard Lead Time Produksi</span>
                    <span>{{ $bom->produce_delay }} Hari</span>
                </div>
                <div class="flex items-center justify-between text-[#757681]">
                    <span>Manufacturing Readiness:</span>
                    <span class="font-semibold text-[#001849]">{{ $bom->ready_to_produce === 'all_available' ? 'All Components Available' : 'ASAP' }}</span>
                </div>
                <div class="flex items-center justify-between text-[#757681]">
                    <span>Flexible Consumption:</span>
                    <span class="font-semibold text-[#001849]">{{ ucfirst($bom->consumption) }}</span>
                </div>
            </div>
        </div>
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 text-right">
            <button type="button" onclick="closePerformanceModal()" class="px-4 py-1.5 bg-slate-200 text-[#0d2c6c] font-semibold text-xs rounded-lg hover:bg-slate-300">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.bomRawMaterials = @json($rawMaterials);
    let currentSearchTarget = null; // 'main', 'component_catalog', or component item index (integer)

    function bomComponentsManager(initialData = []) {
        return {
            items: (initialData && initialData.length > 0) ? initialData.map(i => ({...i, open: false})) : [
                {
                    uid: 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                    product_id: '',
                    displayText: '',
                    cones: '',
                    quantity: '1.0000',
                    uom: 'kg',
                    wastage_percent: '0',
                    notes: '',
                    open: false
                }
            ],
            getFilteredProducts(search) {
                if (!search || search.trim() === '') return (window.bomRawMaterials || []).slice(0, 8);
                const q = search.toLowerCase();
                return (window.bomRawMaterials || []).filter(p => 
                    (p.name && p.name.toLowerCase().includes(q)) || 
                    (p.code && p.code.toLowerCase().includes(q))
                ).slice(0, 8);
            },
            selectComponent(item, p) {
                item.product_id = p.id;
                item.displayText = `[${p.code}] ${p.name}`;
                item.uom = p.uom || 'kg';
                item.open = false;
            },
            addItem(productId = '', quantity = '1.0000', cones = '', uom = 'kg', wastage = '0', notes = '', code = '', name = '') {
                let p = (window.bomRawMaterials || []).find(m => m.id == productId);
                let display = '';
                if (p) {
                    display = `[${p.code}] ${p.name}`;
                    uom = p.uom || uom;
                } else if (code && name) {
                    display = `[${code}] ${name}`;
                }
                this.items.push({
                    uid: 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                    product_id: productId,
                    displayText: display,
                    cones: cones,
                    quantity: quantity,
                    uom: uom,
                    wastage_percent: wastage,
                    notes: notes,
                    open: false
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                } else {
                    this.items = [{
                        uid: 'row_' + Date.now(),
                        product_id: '',
                        displayText: '',
                        cones: '',
                        quantity: '1.0000',
                        uom: 'kg',
                        wastage_percent: '0',
                        notes: '',
                        open: false
                    }];
                }
            }
        };
    }

    function openProductSearchModal(target) {
        currentSearchTarget = target;
        const modal = document.getElementById('productSearchModal');
        const title = document.getElementById('searchModalTitle');
        if (target === 'main') {
            title.textContent = 'Search: Product';
        } else {
            title.textContent = 'Search: Component';
        }
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.getElementById('modalProductSearchInput').value = '';
        filterModalProducts();
        document.getElementById('modalProductSearchInput').focus();
    }

    function closeProductSearchModal() {
        const modal = document.getElementById('productSearchModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        currentSearchTarget = null;
    }

    function selectModalProduct(id, name, code, uom) {
        if (currentSearchTarget === 'main') {
            const mainContainer = document.querySelector('#mainProductSearchContainer');
            if (mainContainer && mainContainer._x_dataStack) {
                const alpineData = mainContainer._x_dataStack[0];
                alpineData.selectProduct({ id: id, name: name, code: code, uom: uom });
            } else {
                document.getElementById('product_id_hidden').value = id;
            }
        } else if (currentSearchTarget === 'component_catalog') {
            const compContainer = document.querySelector('#componentsManagerContainer');
            if (compContainer && compContainer._x_dataStack) {
                compContainer._x_dataStack[0].addItem(id, '1.0000', '', uom, '0', '', code, name);
            }
        } else if (typeof currentSearchTarget === 'number') {
            const compContainer = document.querySelector('#componentsManagerContainer');
            if (compContainer && compContainer._x_dataStack) {
                const alpineData = compContainer._x_dataStack[0];
                if (alpineData.items[currentSearchTarget]) {
                    const item = alpineData.items[currentSearchTarget];
                    alpineData.selectComponent(item, { id: id, code: code, name: name, uom: uom });
                }
            }
        }
        closeProductSearchModal();
    }

    function filterModalProducts() {
        const query = document.getElementById('modalProductSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.modal-product-row');
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
        document.getElementById('modalRecordCount').textContent = `1-${count} / ${(window.bomRawMaterials || []).length}`;
    }

    function openOverviewModal() {
        const m = document.getElementById('overviewModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }

    function closeOverviewModal() {
        const m = document.getElementById('overviewModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }

    function openPerformanceModal() {
        const m = document.getElementById('performanceModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }

    function closePerformanceModal() {
        const m = document.getElementById('performanceModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }
</script>
@endpush
@endsection

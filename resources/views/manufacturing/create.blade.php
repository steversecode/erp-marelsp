@extends('layouts.app')

@section('title', 'New Manufacturing Order')

@section('content')
<div class="space-y-4 animate-in fade-in duration-150 text-slate-800" x-data="moCreateForm(@js($bomDetails), @js($rawMaterials))">
    <form method="POST" action="{{ route('manufacturing.store') }}" id="moCreateForm">
        @csrf

        <!-- 1. Odoo Top Control Panel / Action & Breadcrumb Bar -->
        <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xs">
            <!-- Left: Save / Discard + Breadcrumbs -->
            <div class="flex items-center gap-2.5">
                <button type="submit" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs transition-colors flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">check</span>
                    <span>Save</span>
                </button>
                <a href="{{ route('manufacturing.index') }}" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs transition-colors">
                    Discard
                </a>

                <div class="flex items-center gap-2 text-xs pl-2 border-l border-slate-200">
                    <a href="{{ route('manufacturing.index') }}" class="text-sky-700 hover:text-sky-900 font-medium hover:underline">
                        Manufacturing Orders
                    </a>
                    <span class="text-slate-400">/</span>
                    <span class="font-bold text-slate-900 font-mono">New</span>
                </div>
            </div>

            <!-- Right: Status Pipeline / Chevron Breadcrumbs -->
            <div class="flex items-center text-xs font-semibold select-none">
                <!-- Step 1: Draft (Active) -->
                <div class="px-3.5 py-1 bg-[#017e84] text-white font-bold rounded-l border border-r-0 border-slate-300 flex items-center gap-1 shadow-xs">
                    <span>Draft</span>
                </div>
                <!-- Step 2: Confirmed -->
                <div class="px-3.5 py-1 bg-slate-100 text-slate-400 border-y border-slate-300 flex items-center gap-1">
                    <span>Confirmed</span>
                </div>
                <!-- Step 3: In Progress -->
                <div class="px-3.5 py-1 bg-slate-100 text-slate-400 border-y border-slate-300 flex items-center gap-1">
                    <span>In Progress</span>
                </div>
                <!-- Step 4: Done -->
                <div class="px-3.5 py-1 bg-slate-100 text-slate-400 rounded-r border border-l-0 border-slate-300 flex items-center gap-1">
                    <span>Done</span>
                </div>
            </div>
        </div>

        <!-- 2. Main Sheet Form (Odoo Card) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 space-y-6 mt-4">
            <!-- Header Title -->
            <div class="flex items-center gap-2">
                <button type="button" class="text-slate-300 hover:text-amber-400 transition-colors cursor-pointer" title="Favorite">
                    <span class="material-symbols-outlined text-[24px]">star_border</span>
                </button>
                <h1 class="text-2xl font-bold font-mono text-slate-400">
                    New
                </h1>
            </div>

            <!-- Main Info Grid (2 Columns Odoo Style) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-xs">
                <!-- Left Column -->
                <div class="space-y-3.5">
                    <!-- Bill of Material -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <label for="bom_id" class="font-semibold text-slate-700 w-32 shrink-0 flex items-center gap-0.5">
                            <span>Bill of Material</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex-1 min-w-0">
                            <select name="bom_id" id="bom_id" x-model="selectedBomId" @change="onBomChange()" required class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-1.5 focus:border-[#017e84] outline-none">
                                <option value="">-- Select Bill of Material --</option>
                                @foreach($boms as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->name }} ({{ $b->product->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Product (Auto resolved from BoM) -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-32 shrink-0">Product</span>
                        <div class="flex-1 min-w-0 text-left">
                            <span class="font-bold text-slate-900 block truncate" x-text="currentProduct ? currentProduct : 'Auto-filled from BoM'"></span>
                        </div>
                    </div>

                    <!-- Quantity To Produce -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <label for="planned_qty" class="font-semibold text-slate-700 w-32 shrink-0 flex items-center gap-0.5">
                            <span>Quantity</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex-1 flex items-center gap-2">
                            <input type="number" step="0.0001" min="0.0001" name="planned_qty" id="planned_qty" x-model="plannedQty" @input="recalculateComponents()" required class="w-28 text-xs font-mono font-bold text-slate-900 bg-white border border-slate-300 rounded p-1.5 focus:border-[#017e84] outline-none">
                            <span class="text-slate-600 font-medium" x-text="currentUom">Pairs</span>
                            <span class="text-slate-500 text-[11px] font-semibold bg-slate-100 px-1.5 py-0.5 rounded">To Produce</span>
                            <span class="material-symbols-outlined text-rose-600 text-[16px]">show_chart</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-3.5">
                    <!-- Start Date -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <label for="start_date" class="font-semibold text-slate-700 w-36 shrink-0 flex items-center gap-0.5">
                            <span>Start Date</span>
                            <span class="text-slate-400 cursor-help" title="Planned production start">?</span>
                        </label>
                        <div class="flex-1">
                            <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-1.5 focus:border-[#017e84] outline-none">
                        </div>
                    </div>

                    <!-- Scheduled End Date (Preview) -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-36 shrink-0 flex items-center gap-0.5">
                            <span>Scheduled End</span>
                            <span class="text-slate-400 cursor-help" title="Estimated completion">?</span>
                        </span>
                        <div class="flex-1 text-slate-700 font-mono">
                            {{ now()->addDays(7)->format('d F Y 17:00:00') }}
                        </div>
                    </div>

                    <!-- Responsible -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-36 shrink-0">Responsible</span>
                        <div class="flex-1 text-slate-900 font-medium">
                            {{ auth()->user()->name ?? 'Admin User' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notebook / Tabs (Odoo Style) -->
            <div x-data="{ tab: 'components' }" class="pt-4">
                <!-- Tab Navigation -->
                <div class="flex items-center gap-6 border-b border-slate-200 text-xs font-semibold pb-px">
                    <button type="button" @click="tab = 'components'" :class="tab === 'components' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                        Components
                    </button>
                    <button type="button" @click="tab = 'workorders'" :class="tab === 'workorders' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                        Work Orders
                    </button>
                    <button type="button" @click="tab = 'miscellaneous'" :class="tab === 'miscellaneous' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                        Miscellaneous
                    </button>
                </div>

                <!-- TAB 1: COMPONENTS -->
                <div x-show="tab === 'components'" class="pt-3 space-y-2">
                    <div class="border border-slate-200 rounded-lg overflow-hidden min-h-[160px]">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3 min-w-[240px]">Product</th>
                                    <th class="py-2.5 px-3">From</th>
                                    <th class="py-2.5 px-3 text-right w-28">To Consume</th>
                                    <th class="py-2.5 px-3 text-right">Stok On Hand</th>
                                    <th class="py-2.5 px-2 w-20">UoM</th>
                                    <th class="py-2.5 px-3 text-center w-24">Status</th>
                                    <th class="py-2.5 px-2 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(c, index) in computedComponents" :key="c.uid || index">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Product Selection / Display -->
                                        <td class="py-2 px-3">
                                            <input type="hidden" :name="`components[${index}][product_id]`" :value="c.product_id">
                                            <div x-show="!c.isCustom">
                                                <div class="font-medium text-slate-900" x-text="c.product_name"></div>
                                                <div class="text-[10px] text-slate-500 font-mono" x-text="c.product_code"></div>
                                            </div>
                                            <div x-show="c.isCustom">
                                                <select x-model="c.product_id" @change="onCustomProductChange(c)" class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                                    <option value="">-- Select Component --</option>
                                                    <template x-for="p in rawMaterials" :key="p.id">
                                                        <option :value="p.id" x-text="`[${p.code}] ${p.name}`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </td>

                                        <!-- From Location -->
                                        <td class="py-2 px-3 text-slate-600 font-mono text-[11px]">
                                            WH/Stock
                                        </td>

                                        <!-- To Consume (Editable Planned Qty) -->
                                        <td class="py-2 px-3 text-right">
                                            <input type="number" step="0.0001" min="0.0001" :name="`components[${index}][planned_qty]`" x-model="c.calculated_qty" class="w-24 text-xs font-mono font-bold text-right text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                        </td>

                                        <!-- Stok On Hand -->
                                        <td class="py-2 px-3 text-right font-mono text-slate-600" x-text="parseFloat(c.available_stock || 0).toFixed(2)"></td>

                                        <!-- UoM -->
                                        <td class="py-2 px-2 text-slate-600">
                                            <input type="text" :name="`components[${index}][uom]`" x-model="c.uom" class="w-16 text-xs text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                        </td>

                                        <!-- Availability Status -->
                                        <td class="py-2 px-3 text-center">
                                            <span x-show="parseFloat(c.available_stock || 0) >= parseFloat(c.calculated_qty || 0)" class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">
                                                Available
                                            </span>
                                            <span x-show="parseFloat(c.available_stock || 0) < parseFloat(c.calculated_qty || 0)" class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded">
                                                Not Available
                                            </span>
                                        </td>

                                        <!-- Remove Line -->
                                        <td class="py-2 px-2 text-center">
                                            <button type="button" @click="removeLine(index)" class="p-1 rounded text-rose-600 hover:bg-rose-50 cursor-pointer" title="Delete line">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="computedComponents.length === 0">
                                    <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                        Pilih Master BoM di atas atau klik <strong>Add a line</strong> untuk menambahkan komponen secara manual.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add a line button (Gambar Odoo) -->
                    <div class="pt-1">
                        <button type="button" @click="addLine()" class="text-xs font-semibold text-[#017e84] hover:text-[#01656a] hover:underline flex items-center gap-1 py-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Add a line</span>
                        </button>
                    </div>
                </div>

                <!-- TAB 2: WORK ORDERS -->
                <div x-show="tab === 'workorders'" class="pt-3">
                    <div class="border border-slate-200 rounded-lg p-4 bg-slate-50 text-xs text-slate-600">
                        <p>Work Center dan tahapan operasi akan di-assign otomatis berdasarkan routing BoM terpilih.</p>
                    </div>
                </div>

                <!-- TAB 3: MISCELLANEOUS -->
                <div x-show="tab === 'miscellaneous'" class="pt-3">
                    <div class="border border-slate-200 rounded-lg p-4 space-y-4 text-xs bg-slate-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Lokasi Asal Material (Source Location)</label>
                                <select name="source_location_id" class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-2 focus:border-[#017e84] outline-none">
                                    @foreach($sourceLocations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->complete_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Catatan Produksi</label>
                                <textarea name="notes" rows="2" placeholder="Catatan khusus untuk lantai produksi..." class="w-full text-xs text-slate-800 bg-white border border-slate-300 rounded p-2 focus:border-[#017e84] outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function moCreateForm(bomDetails, rawMaterials) {
        return {
            boms: bomDetails || [],
            rawMaterials: rawMaterials || [],
            selectedBomId: '',
            currentProduct: '',
            currentUom: 'Pairs',
            plannedQty: 100,
            computedComponents: [],
            onBomChange() {
                const bom = this.boms.find(b => b.id == this.selectedBomId);
                if (bom) {
                    this.currentProduct = bom.product_name;
                    this.currentUom = bom.uom;
                    this.plannedQty = bom.base_quantity || 100;
                    this.recalculateComponents();
                } else {
                    this.currentProduct = '';
                    this.computedComponents = [];
                }
            },
            recalculateComponents() {
                const bom = this.boms.find(b => b.id == this.selectedBomId);
                if (!bom) {
                    return;
                }
                const multiplier = parseFloat(this.plannedQty || 0) / (parseFloat(bom.base_quantity) || 1);
                
                // Keep any manually added custom components
                const customItems = this.computedComponents.filter(c => c.isCustom);

                const bomItems = bom.components.map((c, idx) => {
                    const wastage = 1 + (parseFloat(c.wastage_percent || 0) / 100);
                    const calculated = parseFloat(c.base_qty) * multiplier * wastage;
                    return {
                        ...c,
                        uid: 'bom_' + c.product_id + '_' + idx,
                        isCustom: false,
                        calculated_qty: calculated
                    };
                });

                this.computedComponents = [...bomItems, ...customItems];
            },
            addLine() {
                this.computedComponents.push({
                    uid: 'custom_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
                    product_id: '',
                    product_name: '',
                    product_code: '',
                    calculated_qty: 1.0000,
                    available_stock: 0,
                    uom: 'kg',
                    isCustom: true
                });
            },
            removeLine(index) {
                this.computedComponents.splice(index, 1);
            },
            onCustomProductChange(comp) {
                const p = this.rawMaterials.find(m => m.id == comp.product_id);
                if (p) {
                    comp.product_name = p.name;
                    comp.product_code = p.code;
                    comp.uom = p.uom || 'kg';
                    comp.available_stock = p.current_stock || 0;
                }
            }
        };
    }
</script>
@endpush

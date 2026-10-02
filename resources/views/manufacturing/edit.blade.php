@extends('layouts.app')

@section('title', 'Edit Manufacturing Order - ' . $mo->mo_number)

@section('content')
@php
    $initialComponents = $mo->components->map(function($c) {
        return [
            'id' => $c->id,
            'uid' => 'comp_' . $c->id,
            'product_id' => $c->actual_product_id,
            'product_name' => $c->actualProduct->name,
            'product_code' => $c->actualProduct->code,
            'planned_qty' => (float)$c->planned_qty,
            'issued_qty' => (float)$c->issued_qty,
            'uom' => $c->uom,
            'isCustom' => false,
        ];
    })->values();
@endphp

<div class="space-y-4 animate-in fade-in duration-150 text-slate-800" x-data="moEditForm(@js($initialComponents), @js($rawMaterials))">
    <form method="POST" action="{{ route('manufacturing.update', $mo->id) }}">
        @csrf
        @method('PUT')

        <!-- 1. Odoo Top Control Panel / Action & Breadcrumb Bar -->
        <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xs">
            <!-- Left: Save / Discard + Breadcrumbs -->
            <div class="flex items-center gap-2.5">
                <button type="submit" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs transition-colors flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">check</span>
                    <span>Save</span>
                </button>
                <a href="{{ route('manufacturing.show', $mo->id) }}" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs transition-colors">
                    Discard
                </a>

                <div class="flex items-center gap-2 text-xs pl-2 border-l border-slate-200">
                    <a href="{{ route('manufacturing.index') }}" class="text-sky-700 hover:text-sky-900 font-medium hover:underline">
                        Manufacturing Orders
                    </a>
                    <span class="text-slate-400">/</span>
                    <a href="{{ route('manufacturing.show', $mo->id) }}" class="text-sky-700 hover:text-sky-900 font-medium hover:underline font-mono">
                        {{ $mo->mo_number }}
                    </a>
                    <span class="text-slate-400">/</span>
                    <span class="font-bold text-slate-900">Edit</span>
                </div>
            </div>

            <!-- Right: Status Pipeline / Chevron Breadcrumbs -->
            <div class="flex items-center text-xs font-semibold select-none">
                <div class="px-3 py-1 {{ $mo->status === 'draft' ? 'bg-[#017e84] text-white font-bold' : 'bg-slate-100 text-slate-500' }} rounded-l border border-r-0 border-slate-300 flex items-center gap-1">
                    <span>Draft</span>
                </div>
                <div class="px-3 py-1 {{ $mo->status === 'confirmed' ? 'bg-[#017e84] text-white font-bold' : 'bg-slate-100 text-slate-500' }} border-y border-slate-300 flex items-center gap-1">
                    <span>Confirmed</span>
                </div>
                <div class="px-3 py-1 {{ $mo->status === 'in_progress' ? 'bg-[#017e84] text-white font-bold' : 'bg-slate-100 text-slate-500' }} border-y border-slate-300 flex items-center gap-1">
                    <span>In Progress</span>
                </div>
                <div class="px-3 py-1 {{ $mo->status === 'done' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-500' }} rounded-r border border-l-0 border-slate-300 flex items-center gap-1">
                    <span>Done</span>
                </div>
            </div>
        </div>

        <!-- 2. Main Sheet Form (Odoo Card) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 space-y-6 mt-4">
            <!-- Header Title -->
            <div class="flex items-center gap-2">
                <button type="button" class="text-amber-400 hover:text-amber-500 transition-colors cursor-pointer" title="Favorite">
                    <span class="material-symbols-outlined text-[24px]">star_border</span>
                </button>
                <h1 class="text-2xl font-bold font-mono text-slate-900">
                    {{ $mo->mo_number }}
                </h1>
            </div>

            <!-- Main Info Grid (2 Columns Odoo Style) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-xs">
                <!-- Left Column -->
                <div class="space-y-3.5">
                    <!-- Product -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-32 shrink-0">Product</span>
                        <div class="flex-1 min-w-0 text-left">
                            <span class="font-bold text-slate-900 block truncate">
                                [{{ $mo->product->code }}] {{ $mo->product->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Quantity To Produce -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <label for="planned_qty" class="font-semibold text-slate-700 w-32 shrink-0 flex items-center gap-0.5">
                            <span>Quantity</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex-1 flex items-center gap-2">
                            <input type="number" step="0.0001" min="0.0001" name="planned_qty" id="planned_qty" value="{{ old('planned_qty', $mo->planned_qty) }}" required class="w-28 text-xs font-mono font-bold text-slate-900 bg-white border border-slate-300 rounded p-1.5 focus:border-[#017e84] outline-none">
                            <span class="text-slate-600 font-medium">{{ $mo->uom }}</span>
                            <span class="text-slate-500 text-[11px] font-semibold bg-slate-100 px-1.5 py-0.5 rounded">To Produce</span>
                            <span class="material-symbols-outlined text-rose-600 text-[16px]">show_chart</span>
                        </div>
                    </div>

                    <!-- Bill of Material -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-32 shrink-0 flex items-center gap-0.5">
                            <span>Bill of Material</span>
                            <span class="text-slate-400 cursor-help" title="BoM reference">?</span>
                        </span>
                        <div class="flex-1 min-w-0 text-left">
                            <span class="text-slate-800 font-medium">
                                {{ $mo->bom->name }}
                            </span>
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
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $mo->start_date ? $mo->start_date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-1.5 focus:border-[#017e84] outline-none">
                        </div>
                    </div>

                    <!-- Scheduled End Date -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-36 shrink-0 flex items-center gap-0.5">
                            <span>Scheduled End</span>
                            <span class="text-slate-400 cursor-help" title="Estimated completion">?</span>
                        </span>
                        <div class="flex-1 text-slate-700 font-mono">
                            {{ $mo->start_date ? $mo->start_date->addDays(7)->format('d F Y 17:00:00') : '-' }}
                        </div>
                    </div>

                    <!-- Responsible -->
                    <div class="flex items-baseline justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-semibold text-slate-700 w-36 shrink-0">Responsible</span>
                        <div class="flex-1 text-slate-900 font-medium">
                            {{ $mo->creator->name ?? 'Admin User' }}
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
                    <button type="button" @click="tab = 'miscellaneous'" :class="tab === 'miscellaneous' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                        Miscellaneous
                    </button>
                </div>

                <!-- TAB 1: COMPONENTS -->
                <div x-show="tab === 'components'" class="pt-3 space-y-2">
                    <div class="border border-slate-200 rounded-lg overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3 min-w-[240px]">Product</th>
                                    <th class="py-2.5 px-3">From</th>
                                    <th class="py-2.5 px-3 text-right w-28">To Consume</th>
                                    <th class="py-2.5 px-2 w-20">UoM</th>
                                    <th class="py-2.5 px-2 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(comp, index) in components" :key="comp.uid || index">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Product Column -->
                                        <td class="py-2 px-3">
                                            <input type="hidden" :name="`components[${index}][id]`" :value="comp.id || ''">
                                            <input type="hidden" :name="`components[${index}][product_id]`" :value="comp.product_id">
                                            <div x-show="!comp.isCustom">
                                                <div class="font-medium text-slate-900" x-text="comp.product_name"></div>
                                                <div class="text-[10px] text-slate-500 font-mono" x-text="comp.product_code"></div>
                                            </div>
                                            <div x-show="comp.isCustom">
                                                <select x-model="comp.product_id" @change="onCustomProductChange(comp)" class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                                    <option value="">-- Select Component --</option>
                                                    <template x-for="p in rawMaterials" :key="p.id">
                                                        <option :value="p.id" x-text="`[${p.code}] ${p.name}`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </td>

                                        <!-- From Location -->
                                        <td class="py-2 px-3 text-slate-600 font-mono text-[11px]">
                                            {{ $mo->sourceLocation->name ?? 'WH/Stock' }}
                                        </td>

                                        <!-- To Consume (Planned Qty) -->
                                        <td class="py-2 px-3 text-right">
                                            <input type="number" step="0.0001" min="0.0001" :name="`components[${index}][planned_qty]`" x-model="comp.planned_qty" class="w-24 text-xs font-mono font-bold text-right text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                        </td>

                                        <!-- UoM -->
                                        <td class="py-2 px-2 text-slate-600">
                                            <input type="text" :name="`components[${index}][uom]`" x-model="comp.uom" class="w-16 text-xs text-slate-800 bg-white border border-slate-300 rounded p-1 focus:border-[#017e84] outline-none">
                                        </td>

                                        <!-- Delete Line (Only if not issued) -->
                                        <td class="py-2 px-2 text-center">
                                            <button type="button" x-show="!comp.issued_qty || comp.issued_qty <= 0" @click="removeLine(index)" class="p-1 rounded text-rose-600 hover:bg-rose-50 cursor-pointer" title="Delete line">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add a line button -->
                    <div class="pt-1">
                        <button type="button" @click="addLine()" class="text-xs font-semibold text-[#017e84] hover:text-[#01656a] hover:underline flex items-center gap-1 py-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Add a line</span>
                        </button>
                    </div>
                </div>

                <!-- TAB 2: MISCELLANEOUS -->
                <div x-show="tab === 'miscellaneous'" class="pt-3">
                    <div class="border border-slate-200 rounded-lg p-4 space-y-4 text-xs bg-slate-50">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Catatan Produksi</label>
                            <textarea name="notes" rows="2" placeholder="Catatan khusus untuk lantai produksi..." class="w-full text-xs text-slate-800 bg-white border border-slate-300 rounded p-2 focus:border-[#017e84] outline-none">{{ old('notes', $mo->notes) }}</textarea>
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
    function moEditForm(initialComponents, rawMaterials) {
        return {
            components: initialComponents || [],
            rawMaterials: rawMaterials || [],
            addLine() {
                this.components.push({
                    id: null,
                    uid: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
                    product_id: '',
                    product_name: '',
                    product_code: '',
                    planned_qty: 1.0000,
                    issued_qty: 0,
                    uom: 'kg',
                    isCustom: true
                });
            },
            removeLine(index) {
                this.components.splice(index, 1);
            },
            onCustomProductChange(comp) {
                const p = this.rawMaterials.find(m => m.id == comp.product_id);
                if (p) {
                    comp.product_name = p.name;
                    comp.product_code = p.code;
                    comp.uom = p.uom || 'kg';
                }
            }
        };
    }
</script>
@endpush

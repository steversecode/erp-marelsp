@extends('layouts.app')

@section('title', 'Manufacturing Orders - ' . $mo->mo_number)

@section('content')
<div class="space-y-4 animate-in fade-in duration-150 text-slate-800">
    <!-- 1. Odoo Top Control Panel / Breadcrumb & Smart Buttons Header -->
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xs">
        <!-- Left: New Button + Breadcrumb Navigation -->
        <div class="flex items-center gap-3">
            <a href="{{ route('manufacturing.create') }}" class="px-3 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>New</span>
            </a>

            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('manufacturing.index') }}" class="text-sky-700 hover:text-sky-900 font-medium hover:underline">
                    Manufacturing Orders
                </a>
                <span class="text-slate-400">/</span>
                <div class="flex items-center gap-1 font-bold text-slate-900 font-mono">
                    <span>{{ $mo->mo_number }}</span>
                    <button type="button" class="text-slate-400 hover:text-slate-600 p-0.5 rounded cursor-pointer" title="Settings">
                        <span class="material-symbols-outlined text-[15px]">settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Odoo Smart Buttons & Pager -->
        <div class="flex flex-wrap items-center gap-1.5">
            <!-- Smart Button 1: Product Moves -->
            <a href="{{ route('inventory.moves', ['reference_type' => 'ManufacturingOrder', 'reference_id' => $mo->id]) }}" class="px-3 py-1 bg-white hover:bg-slate-50 border border-slate-200 rounded text-xs font-medium text-slate-700 flex items-center gap-1.5 shadow-2xs transition-colors">
                <span class="material-symbols-outlined text-slate-500 text-[16px]">swap_horiz</span>
                <span>Product Moves</span>
                @if($productMovesCount > 0)
                    <span class="px-1.5 py-0.2 bg-teal-100 text-teal-800 text-[10px] font-bold rounded-full font-mono">{{ $productMovesCount }}</span>
                @endif
            </a>

            <!-- Smart Button 2: Overview -->
            <button type="button" onclick="openOverviewModal()" class="px-3 py-1 bg-white hover:bg-slate-50 border border-slate-200 rounded text-xs font-medium text-slate-700 flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-slate-500 text-[16px]">view_quilt</span>
                <span>Overview</span>
            </button>

            <!-- Smart Button 3: Timeline -->
            <button type="button" onclick="openTimelineModal()" class="px-3 py-1 bg-white hover:bg-slate-50 border border-slate-200 rounded text-xs font-medium text-slate-700 flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-slate-500 text-[16px]">calendar_month</span>
                <span>Timeline</span>
            </button>

            <!-- Pager -->
            <div class="flex items-center gap-1 pl-2 border-l border-slate-200 text-xs text-slate-500 font-mono">
                <span>{{ $currentMoPosition }} / {{ max(1, $allMosCount) }}</span>
                <div class="flex items-center border border-slate-200 rounded overflow-hidden bg-white ml-1">
                    <a href="{{ route('manufacturing.index') }}" class="p-1 hover:bg-slate-100 text-slate-600 transition-colors" title="Previous"><span class="material-symbols-outlined text-[14px]">chevron_left</span></a>
                    <a href="{{ route('manufacturing.index') }}" class="p-1 hover:bg-slate-100 text-slate-600 transition-colors" title="Next"><span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container: 2/3 Sheet Form + 1/3 Chatter -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- LEFT 8 COLS: Odoo Sheet Form -->
        <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <!-- Action & Status Ribbon Bar -->
            <div class="px-5 py-2.5 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-1.5">
                    @if($mo->status === 'draft')
                        <form method="POST" action="{{ route('manufacturing.release', $mo->id) }}" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Keluarkan material dan rilis MO ini ke lantai produksi?')"
                                class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1 cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-[15px]">play_arrow</span>
                                <span>Produce All</span>
                            </button>
                        </form>
                    @elseif($mo->status === 'in_progress')
                        <button type="button" onclick="openCompleteModal()"
                            class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-[15px]">check</span>
                            <span>Produce All</span>
                        </button>
                        <button type="button" onclick="openLeftoverModal()"
                            class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs flex items-center gap-1 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-[15px]">replay</span>
                            <span>Retur Sisa</span>
                        </button>
                    @elseif($mo->status === 'done')
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">verified</span>
                            <span>Done</span>
                        </span>
                    @endif

                    <button type="button" onclick="alert('Action: Unplan')" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs transition-colors cursor-pointer">
                        Unplan
                    </button>
                    <button type="button" onclick="alert('Action: Cancel')" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs transition-colors cursor-pointer">
                        Cancel
                    </button>
                </div>

                <!-- Status Pipeline / Chevron Breadcrumbs -->
                <div class="flex items-center text-xs font-semibold select-none">
                    <!-- Step 1: Draft -->
                    <div class="px-3 py-1 {{ $mo->status === 'draft' ? 'bg-[#017e84] text-white font-bold' : 'bg-slate-100 text-slate-500' }} rounded-l border border-r-0 border-slate-300 flex items-center gap-1">
                        <span>Draft</span>
                    </div>
                    <!-- Step 2: Confirmed -->
                    <div class="px-3 py-1 {{ $mo->status === 'confirmed' ? 'bg-[#017e84] text-white font-bold' : 'bg-slate-100 text-slate-500' }} border-y border-slate-300 flex items-center gap-1">
                        <span>Confirmed</span>
                    </div>
                    <!-- Step 3: In Progress -->
                    <div class="px-3 py-1 {{ $mo->status === 'in_progress' ? 'bg-[#017e84] text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-500' }} border-y border-slate-300 flex items-center gap-1">
                        <span>In Progress</span>
                    </div>
                    <!-- Step 4: Done -->
                    <div class="px-3 py-1 {{ $mo->status === 'done' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 text-slate-500' }} rounded-r border border-l-0 border-slate-300 flex items-center gap-1">
                        <span>Done</span>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-6 space-y-6">
                <!-- Title Header & Star -->
                <div class="flex items-center gap-2">
                    <button type="button" class="text-amber-400 hover:text-amber-500 transition-colors cursor-pointer" title="Favorite">
                        <span class="material-symbols-outlined text-[24px]">star_border</span>
                    </button>
                    <h1 class="text-2xl font-bold font-mono text-slate-900">
                        {{ $mo->mo_number }}
                    </h1>
                </div>

                <!-- Main Info Grid (2 Columns Odoo Style) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3.5 text-xs">
                    <!-- Left Column -->
                    <div class="space-y-3">
                        <!-- Product -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-32 shrink-0">Product</span>
                            <div class="flex-1 min-w-0 text-left">
                                <a href="{{ route('products.show', $mo->product_id) }}" class="text-sky-700 hover:text-sky-900 font-bold hover:underline truncate block" title="{{ $mo->product->name }}">
                                    [{{ $mo->product->code }}] {{ $mo->product->name }}
                                </a>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-32 shrink-0">Quantity</span>
                            <div class="flex-1 flex items-center gap-2 font-mono">
                                <span class="font-bold text-slate-900">{{ number_format($mo->produced_qty, 2) }}</span>
                                <span class="text-slate-400">/</span>
                                <span class="font-bold text-slate-900">{{ number_format($mo->planned_qty, 4) }}</span>
                                <span class="text-slate-600 font-sans font-medium">{{ $mo->uom }}</span>
                                <span class="text-slate-500 font-sans text-[11px] font-semibold bg-slate-100 px-1.5 py-0.5 rounded">To Produce</span>
                                <span class="material-symbols-outlined text-rose-600 text-[16px]">show_chart</span>
                            </div>
                        </div>

                        <!-- Bill of Material -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-32 shrink-0 flex items-center gap-0.5">
                                <span>Bill of Material</span>
                                <span class="text-slate-400 cursor-help" title="BoM reference">?</span>
                            </span>
                            <div class="flex-1 min-w-0 text-left">
                                <a href="{{ route('manufacturing.boms.show', $mo->bom_id) }}" class="text-sky-700 hover:text-sky-900 font-medium hover:underline truncate block">
                                    {{ $mo->bom->name }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">
                        <!-- Start Date -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-36 shrink-0 flex items-center gap-0.5">
                                <span>Start Date</span>
                                <span class="text-slate-400 cursor-help" title="Production start date">?</span>
                            </span>
                            <div class="flex-1 text-rose-700 font-medium">
                                {{ $mo->start_date ? $mo->start_date->format('d F Y H:i:s') : now()->format('d F Y 08:00:00') }}
                            </div>
                        </div>

                        <!-- Scheduled End -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-36 shrink-0 flex items-center gap-0.5">
                                <span>Scheduled End</span>
                                <span class="text-slate-400 cursor-help" title="Expected finish date">?</span>
                            </span>
                            <div class="flex-1 text-slate-700">
                                {{ $mo->start_date ? $mo->start_date->addDays(7)->format('d F Y 17:00:00') : '-' }}
                            </div>
                        </div>

                        <!-- Component Status -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-36 shrink-0 flex items-center gap-0.5">
                                <span>Component Status</span>
                                <span class="text-slate-400 cursor-help" title="Stock availability">?</span>
                            </span>
                            <div class="flex-1 font-semibold {{ $allComponentsAvailable ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $allComponentsAvailable ? 'Available' : 'Not Available' }}
                            </div>
                        </div>

                        <!-- Responsible -->
                        <div class="flex items-baseline justify-between border-b border-slate-100 pb-2">
                            <span class="font-semibold text-slate-600 w-36 shrink-0">Responsible</span>
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
                        <button type="button" @click="tab = 'workorders'" :class="tab === 'workorders' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                            Work Orders
                        </button>
                        <button type="button" @click="tab = 'miscellaneous'" :class="tab === 'miscellaneous' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                            Miscellaneous
                        </button>
                        <button type="button" @click="tab = 'stockrequests'" :class="tab === 'stockrequests' ? 'text-[#017e84] border-b-2 border-[#017e84] pb-2 font-bold' : 'text-slate-500 hover:text-slate-800 pb-2'" class="transition-all cursor-pointer">
                            Stock Requests
                        </button>
                    </div>

                    <!-- TAB 1: COMPONENTS -->
                    <div x-show="tab === 'components'" class="pt-3">
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Product</th>
                                        <th class="py-2.5 px-3">From</th>
                                        <th class="py-2.5 px-3 text-right">To Consume</th>
                                        <th class="py-2.5 px-3 text-right font-bold text-slate-800">Quantity</th>
                                        <th class="py-2.5 px-2">UoM</th>
                                        <th class="py-2.5 px-2 text-center w-8">C...</th>
                                        <th class="py-2.5 px-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($componentStatuses as $item)
                                    @php
                                        $comp = $item['component'];
                                        $currentStock = $item['current_stock'];
                                        $isSufficient = $item['is_sufficient'];
                                        $substitutes = $item['substitute_options'];
                                        $consumedQty = max(0, $comp->issued_qty - $comp->returned_qty);
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors {{ $comp->is_switched ? 'bg-amber-50/50' : '' }}">
                                        <!-- Product Column -->
                                        <td class="py-2.5 px-3">
                                            <div class="font-medium text-slate-900">
                                                {{ $comp->actualProduct->name }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-mono">
                                                {{ $comp->actualProduct->code }}
                                            </div>
                                            @if($comp->is_switched)
                                                <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[9px] font-bold rounded">
                                                    Substituted: {{ $comp->originalProduct->code }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- From Location -->
                                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap font-mono text-[11px]">
                                            {{ $mo->sourceLocation->name ?? 'WH/Stock' }}
                                        </td>

                                        <!-- To Consume (Planned Qty) -->
                                        <td class="py-2.5 px-3 text-right font-mono text-slate-700 tabular-nums">
                                            {{ number_format($comp->planned_qty, 4) }}
                                        </td>

                                        <!-- Quantity (Actual Consumed in Green Font) -->
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700 tabular-nums">
                                            {{ number_format($consumedQty > 0 ? $consumedQty : $comp->planned_qty, 4) }}
                                        </td>

                                        <!-- UoM -->
                                        <td class="py-2.5 px-2 text-slate-600">
                                            {{ $comp->uom }}
                                        </td>

                                        <!-- Consumed Status / Checkbox -->
                                        <td class="py-2.5 px-2 text-center">
                                            @if($comp->issued_qty > 0)
                                                <span class="material-symbols-outlined text-emerald-600 text-[18px]" title="Consumed">check_box</span>
                                            @else
                                                <span class="material-symbols-outlined text-slate-300 text-[18px]" title="Pending">check_box_outline_blank</span>
                                            @endif
                                        </td>

                                        <!-- Actions / Substitution & Delete -->
                                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($mo->status !== 'done')
                                                    <button type="button" 
                                                        onclick="openSwitchModal({{ $comp->id }}, '{{ addslashes($comp->originalProduct->name) }}', {{ $comp->actual_product_id }}, {{ $comp->planned_qty }}, '{{ $comp->uom }}', @js($substitutes))"
                                                        class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-[11px] font-medium rounded shadow-2xs transition-colors cursor-pointer"
                                                        title="Switch / Ganti Komponen">
                                                        <span class="material-symbols-outlined text-[13px] align-middle">shuffle</span>
                                                        <span>Switch</span>
                                                    </button>
                                                    @if($comp->issued_qty <= 0)
                                                    <form method="POST" action="{{ route('manufacturing.destroy-component', [$mo->id, $comp->id]) }}" onsubmit="return confirm('Hapus baris komponen ini dari MO?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors cursor-pointer" title="Delete Line">
                                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                                        </button>
                                                    </form>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400 text-[11px] italic">Locked</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Add a line button -->
                        @if($mo->status !== 'done')
                        <div class="pt-2">
                            <button type="button" onclick="openAddComponentModal()" class="text-xs font-semibold text-[#017e84] hover:text-[#01656a] hover:underline flex items-center gap-1 py-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Add a line</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- TAB 2: WORK ORDERS -->
                    <div x-show="tab === 'workorders'" class="pt-3">
                        <div class="border border-slate-200 rounded-lg p-4 bg-slate-50 text-xs text-slate-600">
                            <div class="flex items-center gap-2 font-bold text-slate-800 mb-2">
                                <span class="material-symbols-outlined text-[18px] text-[#017e84]">engineering</span>
                                <span>Routing &amp; Work Centers</span>
                            </div>
                            <p>Proses perakitan/rajut dijalankan pada Lantai Produksi Utama (Work Center 01).</p>
                        </div>
                    </div>

                    <!-- TAB 3: MISCELLANEOUS -->
                    <div x-show="tab === 'miscellaneous'" class="pt-3">
                        <div class="border border-slate-200 rounded-lg p-4 space-y-3 text-xs bg-slate-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="font-bold text-slate-700 block">Lokasi Asal (Source Location)</span>
                                    <span class="text-slate-600 font-mono">{{ $mo->sourceLocation->complete_name ?? 'WH/Stock' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-700 block">Lokasi Tujuan (Finished Goods)</span>
                                    <span class="text-slate-600 font-mono">{{ $mo->destinationLocation->complete_name ?? 'WH/Stock' }}</span>
                                </div>
                            </div>
                            @if($mo->notes)
                            <div class="pt-2 border-t border-slate-200">
                                <span class="font-bold text-slate-700 block">Catatan Tambahan:</span>
                                <p class="text-slate-600 mt-0.5">{{ $mo->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- TAB 4: STOCK REQUESTS -->
                    <div x-show="tab === 'stockrequests'" class="pt-3">
                        <div class="border border-slate-200 rounded-lg p-4 bg-slate-50 text-xs text-slate-600">
                            <p>Permintaan material gudang otomatis dialokasikan dari stok on-hand yang tersedia.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT 4 COLS: Odoo Chatter Activity Stream -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Chatter Control Toolbar -->
            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs space-y-3">
                <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-100 pb-2.5">
                    <button type="button" class="px-2.5 py-1 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-2xs flex items-center gap-1 cursor-pointer transition-colors">
                        <span class="material-symbols-outlined text-[14px]">mail</span>
                        <span>Send message</span>
                    </button>
                    <button type="button" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs flex items-center gap-1 cursor-pointer transition-colors">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                        <span>Log note</span>
                    </button>
                    <button type="button" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-medium rounded shadow-2xs flex items-center gap-1 cursor-pointer transition-colors">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        <span>Activities</span>
                    </button>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                        <span class="material-symbols-outlined text-[16px] text-slate-400">visibility</span>
                        <span class="material-symbols-outlined text-[16px] text-slate-400">attach_file</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">person</span>
                        <span class="font-bold text-slate-700">1</span>
                        <span class="text-[11px] text-slate-400">Follower</span>
                    </div>
                </div>
            </div>

            <!-- Chatter Timeline Audit Feed -->
            <div class="space-y-3">
                <!-- Date Marker -->
                <div class="text-center">
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded-full uppercase tracking-wider">
                        {{ $mo->created_at ? $mo->created_at->format('M d, Y') : 'Aug 29, 2025' }}
                    </span>
                </div>

                <!-- Activity Card 1: State Transition -->
                @if($mo->status !== 'draft')
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-sky-700 text-white flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr($mo->creator->name ?? 'Admin', 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900">{{ $mo->creator->name ?? 'Admin User' }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $mo->updated_at ? $mo->updated_at->format('M d, Y, h:i A') : 'Aug 29, 2025, 11:34 AM' }}</span>
                    </div>
                    <div class="text-xs text-slate-700 space-y-1 pl-8">
                        <p class="font-semibold text-slate-900">MO Progress</p>
                        <ul class="list-disc ml-4 text-[11px] space-y-0.5 text-slate-600">
                            <li>Waiting &rarr; <span class="font-semibold text-emerald-700">Ready</span> <span class="text-slate-400 italic">(MO Readiness)</span></li>
                            <li>Confirmed &rarr; <span class="font-semibold text-sky-700">{{ ucfirst($mo->status) }}</span> <span class="text-slate-400 italic">(State)</span></li>
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Activity Card 2: Creation -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-purple-700 text-white flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr($mo->creator->name ?? 'Kukuh', 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900">{{ $mo->creator->name ?? 'kukuh' }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $mo->created_at ? $mo->created_at->format('M d, Y, h:i A') : 'Aug 15, 2025, 10:36 AM' }}</span>
                    </div>
                    <div class="text-xs text-slate-700 pl-8">
                        <p class="font-medium text-slate-800">Manufacturing Order created</p>
                    </div>
                </div>

                <!-- Activity Card 3: Stock Moves Log (if any) -->
                @if(isset($moMoves) && $moMoves->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-teal-700">swap_horiz</span>
                        <span class="text-xs font-bold text-slate-900">Stock Moves Ledger ({{ $moMoves->count() }})</span>
                    </div>
                    <div class="divide-y divide-slate-100 text-[11px]">
                        @foreach($moMoves->take(4) as $mv)
                        <div class="py-1.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $mv->product->name }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $mv->fromLocation->name }} &rarr; {{ $mv->toLocation->name }}</span>
                            </div>
                            <span class="font-mono font-bold text-slate-800">{{ number_format($mv->qty, 2) }} {{ $mv->uom }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- MODAL 1: COMPONENT SWITCHING -->
<div id="switchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-100">
        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[18px]">shuffle</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Dynamic Component Switching</h3>
                    <p class="text-[11px] text-slate-500">Ganti material pada MO ini tanpa merusak Master BoM</p>
                </div>
            </div>
            <button type="button" onclick="closeSwitchModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="switchForm" method="POST" action="" class="space-y-3.5 text-xs">
            @csrf

            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-slate-500">Material Standar BoM:</span>
                <span id="modalOriginalName" class="font-bold text-slate-900 block mt-0.5">-</span>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Pilih Material Pengganti (Alternatif) <span class="text-rose-500">*</span></label>
                <select name="substitute_product_id" id="substituteSelect" required class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-[#017e84] outline-none">
                    <option value="">-- Pilih Material Pengganti --</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }} - {{ $p->uom }})</option>
                    @endforeach
                </select>
                <div id="substituteRecommendation" class="hidden text-[11px] text-amber-900 bg-amber-50 p-2.5 rounded-lg border border-amber-200 mt-1"></div>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Kuantitas Kebutuhan Disesuaikan</label>
                <div class="relative">
                    <input type="number" step="0.0001" name="custom_qty" id="customQtyInput" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold font-mono focus:ring-1 focus:ring-[#017e84] outline-none">
                    <span id="modalUomBadge" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-semibold">kg</span>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Alasan Penggantian (Audit Note) <span class="text-rose-500">*</span></label>
                <input type="text" name="switch_reason" required placeholder="Contoh: Stok benang kosong, diganti alternatif atas izin PPIC"
                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs focus:ring-1 focus:ring-[#017e84] outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeSwitchModal()" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#017e84] hover:bg-[#01656a] text-white font-bold shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">check</span>
                    <span>Terapkan Substitusi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: RETUR SISA PRODUKSI -->
<div id="leftoverModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">Retur Sisa Produksi</h3>
            <button type="button" onclick="closeLeftoverModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('manufacturing.return-leftover', $mo->id) }}" class="space-y-3 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Pilih Komponen Material</label>
                <select name="mo_component_id" required class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-[#017e84] outline-none">
                    @foreach($mo->components as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->actualProduct->name }} (Dikeluarkan: {{ number_format($c->issued_qty - $c->returned_qty, 2) }} {{ $c->uom }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Jumlah Sisa yang Dikembalikan ke Gudang</label>
                <input type="number" step="0.0001" min="0.0001" name="returned_qty" required placeholder="0.00"
                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold font-mono focus:ring-1 focus:ring-[#017e84] outline-none">
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Catatan / Kondisi Fisik</label>
                <input type="text" name="notes" placeholder="Contoh: Sisa potongan benang utuh pada cone"
                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs focus:ring-1 focus:ring-[#017e84] outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeLeftoverModal()" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#017e84] hover:bg-[#01656a] text-white font-bold cursor-pointer shadow-xs">
                    Konfirmasi Retur Sisa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: COMPLETE MO (PRODUCE ALL) -->
<div id="completeModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">Produce All / Selesaikan Manufacturing Order</h3>
            <button type="button" onclick="closeCompleteModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('manufacturing.complete', $mo->id) }}" class="space-y-3 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Jumlah Riil Hasil Jadi (Finished Goods)</label>
                <div class="relative">
                    <input type="number" step="1" min="1" name="produced_qty" value="{{ $mo->planned_qty }}" required
                        class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold font-mono focus:ring-1 focus:ring-[#017e84] outline-none">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-semibold">{{ $mo->uom }}</span>
                </div>
                <p class="text-[11px] text-slate-500">Barang jadi akan langsung dibukukan ke saldo Gudang FG.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeCompleteModal()" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#017e84] hover:bg-[#01656a] text-white font-bold cursor-pointer shadow-xs">
                    Konfirmasi Selesai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: OVERVIEW -->
<div id="overviewModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">MO Cost &amp; Material Overview</h3>
            <button type="button" onclick="closeOverviewModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="space-y-3 text-xs">
            <div class="flex justify-between py-1 border-b border-slate-100">
                <span class="text-slate-600">Product Output:</span>
                <span class="font-bold text-slate-900">{{ $mo->product->name }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
                <span class="text-slate-600">Total Planned:</span>
                <span class="font-bold text-slate-900">{{ number_format($mo->planned_qty, 2) }} {{ $mo->uom }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
                <span class="text-slate-600">Total Produced:</span>
                <span class="font-bold text-emerald-700">{{ number_format($mo->produced_qty, 2) }} {{ $mo->uom }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-slate-600">Component Count:</span>
                <span class="font-bold text-slate-900">{{ $mo->components->count() }} Items</span>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 5: TIMELINE -->
<div id="timelineModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">Manufacturing Timeline</h3>
            <button type="button" onclick="closeTimelineModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="space-y-3 text-xs">
            <div class="flex items-center gap-3 p-2 bg-slate-50 rounded">
                <span class="material-symbols-outlined text-sky-700 text-[18px]">calendar_today</span>
                <div>
                    <span class="font-bold text-slate-800 block">Start Date</span>
                    <span class="text-slate-600">{{ $mo->start_date ? $mo->start_date->format('d F Y') : '-' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 p-2 bg-slate-50 rounded">
                <span class="material-symbols-outlined text-emerald-700 text-[18px]">event_available</span>
                <div>
                    <span class="font-bold text-slate-800 block">Scheduled End Date</span>
                    <span class="text-slate-600">{{ $mo->start_date ? $mo->start_date->addDays(7)->format('d F Y') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>
<!-- MODAL 6: ADD COMPONENT LINE -->
<div id="addComponentModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">Add a Line (Tambah Komponen Baru)</h3>
            <button type="button" onclick="closeAddComponentModal()" class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('manufacturing.add-component', $mo->id) }}" class="space-y-3.5 text-xs">
            @csrf

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Pilih Produk / Komponen <span class="text-rose-500">*</span></label>
                <select name="product_id" id="addComponentSelect" onchange="onAddComponentProductChange()" required class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-[#017e84] outline-none">
                    <option value="">-- Pilih Komponen --</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}" data-uom="{{ $p->uom }}">{{ $p->name }} ({{ $p->code }} - {{ $p->uom }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-800">Kuantitas Kebutuhan (To Consume) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" step="0.0001" min="0.0001" name="planned_qty" value="1.0000" required class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold font-mono focus:ring-1 focus:ring-[#017e84] outline-none">
                    <span id="addComponentUom" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-semibold">kg</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddComponentModal()" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#017e84] hover:bg-[#01656a] text-white font-bold cursor-pointer shadow-xs flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">add</span>
                    <span>Tambahkan Baris</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const switchModal = document.getElementById('switchModal');
    const switchForm = document.getElementById('switchForm');
    const modalOriginalName = document.getElementById('modalOriginalName');
    const substituteSelect = document.getElementById('substituteSelect');
    const substituteRecommendation = document.getElementById('substituteRecommendation');
    const customQtyInput = document.getElementById('customQtyInput');
    const modalUomBadge = document.getElementById('modalUomBadge');

    function openSwitchModal(componentId, originalName, actualProductId, plannedQty, uom, substitutes) {
        switchForm.action = `{{ url('/manufacturing/' . $mo->id . '/switch') }}/${componentId}`;
        modalOriginalName.textContent = originalName;
        substituteSelect.value = actualProductId;
        customQtyInput.value = plannedQty;
        modalUomBadge.textContent = uom;

        if (substitutes && substitutes.length > 0) {
            let html = `<strong>Rekomendasi Alternatif Terdaftar:</strong><ul class="list-disc ml-4 mt-1 space-y-0.5">`;
            substitutes.forEach(s => {
                html += `<li>${s.name} (Stok: ${s.current_stock.toFixed(2)} ${s.uom}, Rasio: ${s.conversion_rate})</li>`;
            });
            html += `</ul>`;
            substituteRecommendation.innerHTML = html;
            substituteRecommendation.classList.remove('hidden');
            substituteRecommendation.style.display = 'block';
        } else {
            substituteRecommendation.classList.add('hidden');
            substituteRecommendation.style.display = 'none';
        }

        switchModal.classList.remove('hidden');
        switchModal.style.display = 'flex';
    }

    function closeSwitchModal() {
        switchModal.classList.add('hidden');
        switchModal.style.display = 'none';
    }

    function openAddComponentModal() {
        const m = document.getElementById('addComponentModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }

    function closeAddComponentModal() {
        const m = document.getElementById('addComponentModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }

    function onAddComponentProductChange() {
        const select = document.getElementById('addComponentSelect');
        const selectedOption = select.options[select.selectedIndex];
        const uom = selectedOption ? selectedOption.getAttribute('data-uom') : 'kg';
        document.getElementById('addComponentUom').textContent = uom || 'kg';
    }

    function openLeftoverModal() {
        const m = document.getElementById('leftoverModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }
    function closeLeftoverModal() {
        const m = document.getElementById('leftoverModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }

    function openCompleteModal() {
        const m = document.getElementById('completeModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }
    function closeCompleteModal() {
        const m = document.getElementById('completeModal');
        m.classList.add('hidden');
        m.style.display = 'none';
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

    function openTimelineModal() {
        const m = document.getElementById('timelineModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }
    function closeTimelineModal() {
        const m = document.getElementById('timelineModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }
</script>
@endpush

@extends('layouts.app')

@section('title', $warehouse->name . ' - Warehouse')

@section('content')
<div class="space-y-4" x-data="{ showRoutesModal: false }">
    <form action="{{ route('configuration.warehouses.update', $warehouse->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Top Action Bar (Odoo Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#e5eeff] shadow-xs">
            <div class="flex items-center gap-2">
                <a href="{{ route('configuration.warehouses.create') }}" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1.5 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>New</span>
                </a>
                <button type="submit" class="px-4 py-1.5 bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold rounded shadow-xs flex items-center gap-1.5 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save</span>
                </button>
                <a href="{{ route('configuration.warehouses.index') }}" class="px-3.5 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded shadow-xs transition-colors">
                    Discard
                </a>
            </div>

            <!-- Smart Button & Breadcrumb (Odoo Screenshot 2 Style) -->
            <div class="flex items-center gap-4">
                <!-- Smart Button: Routes -->
                <button type="button" @click="showRoutesModal = true" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 shadow-2xs flex items-center gap-2 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[18px] text-sky-700">sync_alt</span>
                    <span>Routes ({{ count($warehouse->generated_routes) }})</span>
                </button>

                <div class="text-xs text-slate-500 font-medium">
                    <a href="{{ route('configuration.warehouses.index') }}" class="text-sky-700 hover:underline">Warehouses</a>
                    <span class="mx-1 text-slate-300">/</span>
                    <span class="text-slate-800 font-semibold">{{ $warehouse->name }}</span>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><span class="material-symbols-outlined text-[16px]">close</span></button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">error</span>
                    <span>Harap periksa form:</span>
                </div>
                <ul class="list-disc list-inside pl-4 text-[11px] space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Card (Matching Odoo Screenshot 2) -->
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-xs p-6 space-y-6">
            <!-- Header: Title & Names -->
            <div class="space-y-4">
                <div>
                    <span class="text-xs font-semibold text-slate-500 block mb-1">Warehouse</span>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" required placeholder="e.g. PT Marel Sukses Pratama"
                        class="w-full text-2xl font-bold text-slate-900 border-b border-slate-300 hover:border-slate-500 focus:border-[#001849] outline-none pb-1 transition-colors bg-transparent">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1 mb-1">
                            <span>Short Name</span>
                            <span class="text-slate-400 font-normal cursor-help" title="Kode singkat warehouse (e.g. WH, wh-on, WH2M)">(?)</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $warehouse->code) }}" required placeholder="e.g. WH"
                            class="w-full max-w-xs font-mono uppercase px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:ring-1 focus:ring-[#001849] outline-none">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700 block mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address', $warehouse->address) }}" placeholder="e.g. PT Marel Sukses Pratama / Dua Marelika Gemilang"
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-[#001849] outline-none">
                    </div>
                </div>
            </div>

            <!-- Tabs: Warehouse Configuration & Technical Information -->
            <div x-data="{ activeTab: 'config' }" class="space-y-6 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
                    <button type="button" @click="activeTab = 'config'" :class="activeTab === 'config' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-slate-500 hover:text-slate-900 font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 transition-all cursor-pointer">
                        Warehouse Configuration
                    </button>
                    <button type="button" @click="activeTab = 'tech'" :class="activeTab === 'tech' ? 'border-[#001849] text-[#001849] font-bold bg-[#eff4ff]/60' : 'border-transparent text-slate-500 hover:text-slate-900 font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg border-b-2 transition-all cursor-pointer">
                        Technical Information
                    </button>
                </div>

                <!-- TAB 1: Warehouse Configuration (Matching Screenshot 2) -->
                <div x-show="activeTab === 'config'" class="space-y-6 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Left Column: SHIPMENTS -->
                        <div class="space-y-6">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-sky-700">local_shipping</span>
                                <span>Shipments</span>
                            </h3>

                            <!-- Incoming Shipments -->
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-slate-800 flex items-center gap-1">
                                    <span>Incoming Shipments</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Langkah alur penerimaan barang masuk ke gudang">(?)</span>
                                </label>
                                <div class="space-y-2 pl-1 text-xs text-slate-700">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="incoming_steps" value="1_step" {{ old('incoming_steps', $warehouse->incoming_steps) === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Receive and Store (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="incoming_steps" value="2_steps" {{ old('incoming_steps', $warehouse->incoming_steps) === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Receive then Store (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="incoming_steps" value="3_steps" {{ old('incoming_steps', $warehouse->incoming_steps) === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Receive, Quality Control, then Store (3 steps)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Outgoing Shipments -->
                            <div class="space-y-2 pt-2">
                                <label class="text-xs font-semibold text-slate-800 flex items-center gap-1">
                                    <span>Outgoing Shipments</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Langkah alur pengiriman barang keluar dari gudang">(?)</span>
                                </label>
                                <div class="space-y-2 pl-1 text-xs text-slate-700">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="outgoing_steps" value="1_step" {{ old('outgoing_steps', $warehouse->outgoing_steps) === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Deliver (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="outgoing_steps" value="2_steps" {{ old('outgoing_steps', $warehouse->outgoing_steps) === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick then Deliver (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="outgoing_steps" value="3_steps" {{ old('outgoing_steps', $warehouse->outgoing_steps) === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick, Pack, then Deliver (3 steps)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: RESUPPLY -->
                        <div class="space-y-6">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-amber-600">sync_alt</span>
                                <span>Resupply</span>
                            </h3>

                            <!-- Buy to Resupply -->
                            <div class="space-y-3">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800">
                                    <input type="checkbox" name="buy_to_resupply" value="1" {{ old('buy_to_resupply', $warehouse->buy_to_resupply) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
                                    <span>Buy to Resupply</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Mengizinkan pengadaan bahan dari supplier langsung ke gudang ini">(?)</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800">
                                    <input type="checkbox" name="manufacture_to_resupply" value="1" {{ old('manufacture_to_resupply', $warehouse->manufacture_to_resupply) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
                                    <span>Manufacture to Resupply</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Mengizinkan proses produksi / manufaktur di gudang ini">(?)</span>
                                </label>
                            </div>

                            <!-- Manufacture Steps -->
                            <div class="space-y-2 pt-1">
                                <label class="text-xs font-semibold text-slate-800 flex items-center gap-1">
                                    <span>Manufacture</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Langkah alur manufaktur di gudang ini">(?)</span>
                                </label>
                                <div class="space-y-2 pl-1 text-xs text-slate-700">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="manufacture_steps" value="1_step" {{ old('manufacture_steps', $warehouse->manufacture_steps) === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Manufacture (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="manufacture_steps" value="2_steps" {{ old('manufacture_steps', $warehouse->manufacture_steps) === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick components then manufacture (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="manufacture_steps" value="3_steps" {{ old('manufacture_steps', $warehouse->manufacture_steps) === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick components, manufacture, then store products (3 steps)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Resupply From (Other Warehouses) -->
                            <div class="space-y-2 pt-2">
                                <label class="text-xs font-semibold text-slate-800 flex items-center gap-1">
                                    <span>Resupply From</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Gudang sumber yang menyuplai produk jadi ke gudang ini">(?)</span>
                                </label>
                                <div class="space-y-2 pl-1 text-xs text-slate-700">
                                    @forelse($otherWarehouses as $ow)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="resupply_from_warehouse_ids[]" value="{{ $ow->id }}" 
                                                {{ (is_array(old('resupply_from_warehouse_ids', $warehouse->resupply_from_warehouse_ids)) && in_array($ow->id, old('resupply_from_warehouse_ids', $warehouse->resupply_from_warehouse_ids ?? []))) ? 'checked' : '' }} 
                                                class="rounded text-[#001849] focus:ring-0">
                                            <span>{{ $ow->name }} <span class="text-slate-400 font-mono text-[10px]">[{{ $ow->code }}]</span></span>
                                        </label>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">Belum ada warehouse lain untuk dijadikan sumber suplai.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Technical Information -->
                <div x-show="activeTab === 'tech'" x-cloak class="space-y-5 animate-in fade-in duration-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-slate-800 block mb-1">Location Stock (Internal Location)</label>
                            <select name="lot_stock_id" class="w-full px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-mono text-slate-900 focus:bg-white focus:ring-1 focus:ring-[#001849] outline-none">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('lot_stock_id', $warehouse->lot_stock_id) == $loc->id ? 'selected' : '' }}>{{ $loc->code }} ({{ $loc->name }})</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-400 mt-1 block">Lokasi penyimpanan utama untuk semua barang yang ada di gudang ini.</span>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-800 block mb-1">Status</label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
                                <span>Active</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Odoo Routes Modal (Generated by Warehouse) -->
    <div x-show="showRoutesModal" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-100" @click.outside="showRoutesModal = false">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-sky-700">sync_alt</span>
                    <h3 class="font-bold text-sm text-slate-900">
                        Generated Routes for {{ $warehouse->name }}
                    </h3>
                </div>
                <button type="button" @click="showRoutesModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="p-5 overflow-y-auto space-y-3 divide-y divide-slate-100">
                @foreach($warehouse->generated_routes as $route)
                    <div class="pt-3 first:pt-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#001849]">{{ $route['name'] }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono bg-sky-50 text-sky-800 border border-sky-200">
                                {{ strtoupper($route['type']) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $route['description'] }}</p>
                        <div class="text-[11px] text-slate-400 flex items-center gap-1 font-mono">
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                            <span>Rules: {{ $route['step'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 text-right">
                <button type="button" @click="showRoutesModal = false" class="px-4 py-1.5 bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold rounded shadow-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

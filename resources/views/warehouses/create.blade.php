@extends('layouts.app')

@section('title', 'New Warehouse')

@section('content')
<div class="space-y-4">
    <form action="{{ route('configuration.warehouses.store') }}" method="POST">
        @csrf

        <!-- Top Action Bar (Odoo Style) -->
        <div class="flex items-center justify-between bg-white p-3.5 rounded-xl border border-[#e5eeff] shadow-xs">
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-1.5 bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold rounded shadow-xs flex items-center gap-1.5 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save</span>
                </button>
                <a href="{{ route('configuration.warehouses.index') }}" class="px-4 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded shadow-xs transition-colors">
                    Discard
                </a>
            </div>

            <div class="text-xs text-slate-500 font-medium">
                <a href="{{ route('configuration.warehouses.index') }}" class="text-sky-700 hover:underline">Warehouses</a>
                <span class="mx-1 text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">New</span>
            </div>
        </div>

        <!-- Alert Error Messages -->
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
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. PT Marel Sukses Pratama / Central Hub"
                        class="w-full text-2xl font-bold text-slate-900 border-b border-slate-300 hover:border-slate-500 focus:border-[#001849] outline-none pb-1 transition-colors bg-transparent placeholder:text-slate-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1 mb-1">
                            <span>Short Name</span>
                            <span class="text-slate-400 font-normal cursor-help" title="Kode singkat warehouse (e.g. WH, wh-on, WH2M)">(?)</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. WH"
                            class="w-full max-w-xs font-mono uppercase px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:ring-1 focus:ring-[#001849] outline-none">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700 block mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. PT Marel Sukses Pratama / Dua Marelika Gemilang"
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
                                        <input type="radio" name="incoming_steps" value="1_step" {{ old('incoming_steps', '1_step') === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Receive and Store (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="incoming_steps" value="2_steps" {{ old('incoming_steps') === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Receive then Store (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="incoming_steps" value="3_steps" {{ old('incoming_steps') === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
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
                                        <input type="radio" name="outgoing_steps" value="1_step" {{ old('outgoing_steps', '1_step') === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Deliver (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="outgoing_steps" value="2_steps" {{ old('outgoing_steps') === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick then Deliver (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="outgoing_steps" value="3_steps" {{ old('outgoing_steps') === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
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
                                    <input type="checkbox" name="buy_to_resupply" value="1" {{ old('buy_to_resupply', true) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
                                    <span>Buy to Resupply</span>
                                    <span class="text-slate-400 font-normal cursor-help" title="Mengizinkan pengadaan bahan dari supplier langsung ke gudang ini">(?)</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800">
                                    <input type="checkbox" name="manufacture_to_resupply" value="1" {{ old('manufacture_to_resupply', false) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
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
                                        <input type="radio" name="manufacture_steps" value="1_step" {{ old('manufacture_steps') === '1_step' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Manufacture (1 step)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="manufacture_steps" value="2_steps" {{ old('manufacture_steps', '2_steps') === '2_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
                                        <span>Pick components then manufacture (2 steps)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="manufacture_steps" value="3_steps" {{ old('manufacture_steps') === '3_steps' ? 'checked' : '' }} class="text-[#001849] focus:ring-0">
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
                                            <input type="checkbox" name="resupply_from_warehouse_ids[]" value="{{ $ow->id }}" {{ is_array(old('resupply_from_warehouse_ids')) && in_array($ow->id, old('resupply_from_warehouse_ids')) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
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
                                <option value="">-- Otomatis Buat Baru ([CODE]/Stock) --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('lot_stock_id') == $loc->id ? 'selected' : '' }}>{{ $loc->code }} ({{ $loc->name }})</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-400 mt-1 block">Lokasi penyimpanan utama untuk semua barang yang ada di gudang ini.</span>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-800 block mb-1">Status</label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded text-[#001849] focus:ring-0">
                                <span>Active</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Settings - Configuration')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">tune</span>
                <span>CONFIGURATION / GENERAL</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Inventory Settings
            </h2>
            <p class="text-xs text-[#757681]">
                Configure warehouse operations, traceability policies, and automated replenishment
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold">
                ← Back to Inventory
            </a>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Operations & Multi-Warehouse Settings -->
        <div class="bg-white rounded-xl border border-[#e5eeff] p-5 shadow-sm space-y-4">
            <h3 class="font-display font-bold text-sm text-[#001849] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#fb7800]">warehouse</span>
                <span>Warehouse Management</span>
            </h3>
            <div class="space-y-3 text-xs text-[#444650]">
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#eff4ff]">
                    <div>
                        <p class="font-bold text-[#001849]">Multi-Step Routes</p>
                        <p class="text-[11px] text-[#757681]">Use custom routes (Buy, Manufacture, Subcontract)</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Enabled</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#eff4ff]">
                    <div>
                        <p class="font-bold text-[#001849]">Storage Locations</p>
                        <p class="text-[11px] text-[#757681]">Track product location in warehouses &amp; zones</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Enabled</span>
                </div>
            </div>
        </div>

        <!-- Traceability Settings -->
        <div class="bg-white rounded-xl border border-[#e5eeff] p-5 shadow-sm space-y-4">
            <h3 class="font-display font-bold text-sm text-[#001849] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#0d2c6c]">qr_code_2</span>
                <span>Traceability</span>
            </h3>
            <div class="space-y-3 text-xs text-[#444650]">
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#eff4ff]">
                    <div>
                        <p class="font-bold text-[#001849]">Lots &amp; Serial Numbers</p>
                        <p class="text-[11px] text-[#757681]">Track yarn lots, fabric rolls, and batches</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Enabled</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#eff4ff]">
                    <div>
                        <p class="font-bold text-[#001849]">Valuation Method</p>
                        <p class="text-[11px] text-[#757681]">Perpetual automated double-entry inventory ledger</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">AVCO / Real-time</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

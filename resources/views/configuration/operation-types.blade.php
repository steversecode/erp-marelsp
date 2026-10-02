@extends('layouts.app')

@section('title', 'Operation Types - Configuration')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">settings</span>
                <span>CONFIGURATION / WAREHOUSE MANAGEMENT</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Operation Types (Odoo Standard)
            </h2>
            <p class="text-xs text-[#757681]">
                Standard logistics operation sequences: Receipts, Internal Transfers, Manufacturing, and Deliveries
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold">
                ← Back to Inventory
            </a>
        </div>
    </div>

    <!-- Operation Types Cards Grid (Odoo Style) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($operationTypes as $op)
        <div class="bg-white rounded-xl border border-[#e5eeff] p-5 shadow-sm space-y-4 hover:border-[#0d2c6c] transition-colors">
            <div class="flex items-start justify-between gap-2">
                <div class="space-y-1">
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-[#001849] text-white">
                        {{ $op['code'] }}
                    </span>
                    <h3 class="font-display font-bold text-sm text-[#001849] mt-1">
                        {{ $op['name'] }}
                    </h3>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $op['badge'] }}">
                    {{ ucfirst($op['type']) }}
                </span>
            </div>

            <div class="space-y-2 text-xs text-[#444650] pt-2 border-t border-[#eff4ff]">
                <div class="flex items-center justify-between">
                    <span class="text-[#757681]">Default Source:</span>
                    <span class="font-medium text-[#0b1c30]">{{ $op['default_source'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#757681]">Default Destination:</span>
                    <span class="font-medium text-[#001849]">{{ $op['default_dest'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Warehouses')

@section('content')
<div class="space-y-4">
    <!-- Top Action Bar (Odoo Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#e5eeff] shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('configuration.warehouses.create') }}" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>

            <div class="flex items-center gap-2">
                <h1 class="text-sm font-bold text-[#001849] flex items-center gap-1.5">
                    <span>Warehouses</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400">settings</span>
                </h1>
            </div>
        </div>

        <!-- Search Bar & Pagination Counter (Odoo Style) -->
        <div class="flex items-center gap-3">
            <form action="{{ route('configuration.warehouses.index') }}" method="GET" class="relative">
                <div class="flex items-center bg-white border border-slate-300 rounded px-2.5 py-1 focus-within:border-slate-500 shadow-2xs">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search warehouse, code, or address..." class="w-48 sm:w-64 text-xs text-slate-800 outline-none bg-transparent">
                    @if(request('q'))
                        <a href="{{ route('configuration.warehouses.index') }}" class="material-symbols-outlined text-slate-400 hover:text-slate-700 text-[16px] ml-1">close</a>
                    @endif
                </div>
            </form>

            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="font-mono">{{ $warehouses->firstItem() ?? 0 }}-{{ $warehouses->lastItem() ?? 0 }} / {{ $warehouses->total() }}</span>
                <div class="flex items-center border border-slate-200 rounded bg-white overflow-hidden shadow-2xs">
                    @if ($warehouses->onFirstPage())
                        <span class="p-1 text-slate-300 cursor-not-allowed"><span class="material-symbols-outlined text-[14px]">chevron_left</span></span>
                    @else
                        <a href="{{ $warehouses->previousPageUrl() }}" class="p-1 hover:bg-slate-100 text-slate-600"><span class="material-symbols-outlined text-[14px]">chevron_left</span></a>
                    @endif

                    @if ($warehouses->hasMorePages())
                        <a href="{{ $warehouses->nextPageUrl() }}" class="p-1 hover:bg-slate-100 text-slate-600"><span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
                    @else
                        <span class="p-1 text-slate-300 cursor-not-allowed"><span class="material-symbols-outlined text-[14px]">chevron_right</span></span>
                    @endif
                </div>
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

    @if(session('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><span class="material-symbols-outlined text-[16px]">close</span></button>
        </div>
    @endif

    <!-- Warehouses Table (Matching Odoo Screenshot 1) -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-xs overflow-hidden" x-data="{
        selected: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selected = [{{ $warehouses->pluck('id')->implode(',') }}];
            } else {
                this.selected = [];
            }
        },
        async bulkDelete() {
            if (this.selected.length === 0) return;
            if (!confirm(`Hapus ${this.selected.length} warehouse terpilih?`)) return;
            
            try {
                const res = await fetch('{{ route('configuration.warehouses.bulk-destroy') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: this.selected })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus');
                }
            } catch (err) {
                alert('Terjadi kesalahan.');
            }
        }
    }">
        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" x-cloak style="display: none;" class="p-2.5 bg-[#eff4ff] border-b border-[#dce9ff] flex items-center justify-between animate-in fade-in duration-100">
            <div class="text-xs text-[#001849] font-medium flex items-center gap-2">
                <span class="font-bold" x-text="selected.length"></span> warehouse dipilih
            </div>
            <button type="button" @click="bulkDelete()" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded shadow-xs flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">delete</span>
                <span>Delete Selected</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded text-[#017e84] focus:ring-0 cursor-pointer">
                        </th>
                        <th class="py-2.5 px-3 w-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[15px]">drag_indicator</span>
                        </th>
                        <th class="py-2.5 px-3 font-semibold text-slate-800 min-w-[220px]">Warehouse</th>
                        <th class="py-2.5 px-3 font-semibold text-slate-800 min-w-[160px]">Location Stock</th>
                        <th class="py-2.5 px-3 font-semibold text-slate-800">Address</th>
                        <th class="py-2.5 px-3 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($warehouses as $wh)
                    <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer group" onclick="if(!event.target.closest('input')) window.location.href='{{ route('configuration.warehouses.show', $wh->id) }}'">
                        <td class="py-2.5 px-3 text-center" onclick="event.stopPropagation()">
                            <input type="checkbox" :value="{{ $wh->id }}" x-model="selected" class="rounded text-[#017e84] focus:ring-0 cursor-pointer">
                        </td>
                        <td class="py-2.5 px-3 text-center text-slate-300 group-hover:text-slate-500">
                            <span class="material-symbols-outlined text-[16px]">drag_indicator</span>
                        </td>
                        <td class="py-2.5 px-3 font-semibold text-slate-900 flex items-center gap-2">
                            <span>{{ $wh->name }}</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-mono">[{{ $wh->code }}]</span>
                            @if($wh->manufacture_to_resupply)
                                <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-semibold">Manufacturing Hub</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 font-mono font-medium text-slate-800">
                            {{ $wh->lotStock?->code ?? $wh->code . '/Stock' }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-600">
                            {{ $wh->address ?? '-' }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if($wh->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">Archived</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[36px] mb-1">warehouse</span>
                            <p class="text-xs">Belum ada warehouse yang terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Retur Sisa Produksi')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">replay</span>
                <span>INBOUND SISA MATERIAL</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Pengembalian Sisa Produksi (MO Return)
            </h2>
            <p class="text-xs text-[#757681]">
                Pencatatan material berlebih/sisa yang dikembalikan dari Lantai Produksi (WIP) ke Gudang Utama
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold self-start">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Quick Return Form Card -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm p-6">
        <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-[#fb7800] text-[18px]">replay</span>
            <span>Form Pengembalian Sisa Material dari MO</span>
        </h3>

        <form method="POST" action="{{ route('stock-in.leftover.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            @csrf

            <!-- Select MO -->
            <div class="space-y-1 md:col-span-2">
                <label class="block font-bold text-[#001849]">Pilih Manufacturing Order</label>
                <select name="manufacturing_order_id" id="moSelect" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    <option value="">-- Pilih Manufacturing Order Aktif --</option>
                    @foreach($activeOrders as $order)
                        <option value="{{ $order->id }}" data-components="{{ json_encode($order->components) }}">
                            {{ $order->mo_number }} - {{ $order->product->name }} (Status: {{ ucfirst($order->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Select Component Material -->
            <div class="space-y-1 md:col-span-2">
                <label class="block font-bold text-[#001849]">Material Komponen yang Dikembalikan</label>
                <select name="mo_component_id" id="compSelect" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    <option value="">-- Pilih MO Terlebih Dahulu --</option>
                </select>
            </div>

            <!-- Qty Return -->
            <div class="space-y-1">
                <label class="block font-bold text-[#001849]">Jumlah Qty Sisa</label>
                <input type="number" step="0.0001" min="0.0001" name="returned_qty" required placeholder="0.00"
                    class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
            </div>

            <!-- Notes -->
            <div class="space-y-1 md:col-span-2">
                <label class="block font-bold text-[#001849]">Catatan / Kondisi Fisik Sisa</label>
                <input type="text" name="notes" placeholder="Contoh: Sisa 2 cone benang jahit belum terpakai"
                    class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    <span>Simpan Retur Sisa</span>
                </button>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e5eeff]">
            <h3 class="text-sm font-bold text-[#001849]">Riwayat Pengembalian Sisa Produksi</h3>
            <p class="text-xs text-[#757681]">Mutasi material dari Lantai Produksi kembali ke Gudang Utama</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-2.5 px-4">No. Mutasi &amp; Waktu</th>
                        <th class="py-2.5 px-4">Referensi MO</th>
                        <th class="py-2.5 px-4">Material Dikembalikan</th>
                        <th class="py-2.5 px-4 text-right">Jumlah Sisa</th>
                        <th class="py-2.5 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($recentReturns as $ret)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-[#001849]">
                            {{ $ret->move_number }}
                            <div class="text-[10px] text-[#757681] font-sans mt-0.5">{{ $ret->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-[#0d2c6c]">
                            {{ $ret->reference_number ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-[#0b1c30] block">{{ $ret->product->name }}</span>
                            <span class="text-[10px] text-[#757681] font-mono block">SKU: {{ $ret->product->code }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-sm text-teal-800 tabular-nums">
                            +{{ number_format($ret->qty, 2) }} {{ $ret->uom }}
                        </td>
                        <td class="py-3 px-4 text-[#757681]">
                            {{ $ret->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-[#757681]">
                            Belum ada riwayat pengembalian sisa produksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const moSelect = document.getElementById('moSelect');
    const compSelect = document.getElementById('compSelect');

    moSelect.addEventListener('change', function() {
        const selectedOption = moSelect.options[moSelect.selectedIndex];
        compSelect.innerHTML = '<option value="">-- Pilih Komponen Material --</option>';

        if (!selectedOption.value) return;

        const components = JSON.parse(selectedOption.getAttribute('data-components') || '[]');
        components.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${c.actual_product ? c.actual_product.name : 'Material ID #' + c.actual_product_id} (Dikeluarkan: ${parseFloat(c.issued_qty || 0).toFixed(2)} ${c.uom})`;
            compSelect.appendChild(opt);
        });
    });
</script>
@endpush

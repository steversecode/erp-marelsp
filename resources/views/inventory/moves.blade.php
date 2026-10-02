@extends('layouts.app')

@section('title', 'Buku Besar Mutasi Stok')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                <span>AUDIT TRAIL LOGISTIK</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Buku Besar Mutasi (Stock Ledger)
            </h2>
            <p class="text-xs text-[#757681]">
                Catatan mutasi pergerakan fisik gudang, transfer antar cabang, dan verifikasi opname secara double-entry
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold">
                ← Kembali ke Dashboard
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-semibold flex items-center gap-1.5 shadow">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar Ledger</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <form method="GET" action="{{ route('inventory.moves') }}" class="flex flex-wrap items-center gap-3">
            <select name="category" class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium">
                <option value="">Semua Kategori Mutasi</option>
                <option value="po_receipt" {{ $category === 'po_receipt' ? 'selected' : '' }}>Penerimaan Supplier (PO)</option>
                <option value="production_return" {{ $category === 'production_return' ? 'selected' : '' }}>Retur Sisa Produksi</option>
                <option value="mo_consumption" {{ $category === 'mo_consumption' ? 'selected' : '' }}>Konsumsi Produksi (MO)</option>
                <option value="mo_finished_goods" {{ $category === 'mo_finished_goods' ? 'selected' : '' }}>Output Barang Jadi (MO)</option>
                <option value="sample_issue" {{ $category === 'sample_issue' ? 'selected' : '' }}>Pengeluaran Sample</option>
                <option value="dyeing_out" {{ $category === 'dyeing_out' ? 'selected' : '' }}>Kirim Proses Celup</option>
                <option value="dyeing_return" {{ $category === 'dyeing_return' ? 'selected' : '' }}>Kembali Dari Celup</option>
            </select>

            <select name="product_id" class="px-3 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none font-medium max-w-xs">
                <option value="">Semua Produk / Material</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ (string)$productId === (string)$prod->id ? 'selected' : '' }}>
                        {{ $prod->name }} ({{ $prod->code }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-lg bg-[#001849] text-white text-xs font-semibold hover:bg-[#0d2c6c] transition-colors">
                Terapkan Filter
            </button>
            @if($category || $productId)
                <a href="{{ route('inventory.moves') }}" class="px-3 py-2 rounded-lg bg-[#eff4ff] text-[#757681] text-xs font-semibold hover:bg-[#dce9ff]">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                        <th class="py-2.5 px-4">No. Mutasi / Waktu</th>
                        <th class="py-2.5 px-4">Kategori &amp; Referensi</th>
                        <th class="py-2.5 px-4">Produk / Material</th>
                        <th class="py-2.5 px-4">Lokasi (Asal &rarr; Tujuan)</th>
                        <th class="py-2.5 px-4 text-right">Kuantitas</th>
                        <th class="py-2.5 px-4">Lot / Batch</th>
                        <th class="py-2.5 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($moves as $move)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-[#001849] whitespace-nowrap">
                            {{ $move->move_number }}
                            <div class="text-[10px] text-[#757681] font-sans mt-0.5">{{ $move->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if(in_array($move->category, ['po_receipt', 'production_return', 'dyeing_return', 'mo_finished_goods']))
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full font-semibold border border-emerald-200">
                                    <span class="material-symbols-outlined text-[13px]">arrow_downward</span>
                                    {{ $move->category_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] text-[#994700] bg-[#ffdbc8]/60 px-2 py-0.5 rounded-full font-semibold border border-[#ffb68b]">
                                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span>
                                    {{ $move->category_label }}
                                </span>
                            @endif
                            @if($move->reference_number)
                                <div class="text-[10px] font-mono text-[#757681] mt-1">Ref: {{ $move->reference_number }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 min-w-[180px]">
                            <span class="font-bold text-[#0b1c30] block">{{ $move->product->name }}</span>
                            <span class="text-[10px] text-[#757681] font-mono block">SKU: {{ $move->product->code }}</span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-medium text-[#0b1c30]">{{ $move->fromLocation->name }}</span>
                            <span class="text-[#757681]"> &rarr; </span>
                            <span class="font-semibold text-[#0d2c6c]">{{ $move->toLocation->name }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-[#0b1c30] tabular-nums whitespace-nowrap">
                            {{ number_format($move->qty, 2) }} <span class="text-xs font-normal text-[#757681]">{{ $move->uom }}</span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($move->batch_lot_number)
                                <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                                    {{ $move->batch_lot_number }}
                                </span>
                            @else
                                <span class="text-[#757681]">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-[#757681] text-[11px] max-w-xs truncate">
                            {{ $move->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[#757681]">
                            Tidak ada catatan mutasi stok yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($moves->hasPages())
        <div class="px-4 py-3 border-t border-[#eff4ff] bg-[#eff4ff]/30">
            {{ $moves->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

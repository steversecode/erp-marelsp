@extends('layouts.app')

@section('title', 'Proses Celup (Dyeing)')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">palette</span>
                <span>SUBCONTRACT DYEING</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Proses Celup &amp; Subkontrak (Dyeing)
            </h2>
            <p class="text-xs text-[#757681]">
                Pencatatan pengiriman benang greige ke vendor celup dan penerimaan kembali benang warna jadi
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold self-start">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Card 1: Form Kirim ke Vendor Celup (Dyeing Out) -->
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-[#dce9ff] text-[#0d2c6c] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider">1. Kirim Benang ke Vendor Celup</h3>
                    <p class="text-[11px] text-[#757681]">Pengeluaran benang mentah (greige) ke vendor subkontrak</p>
                </div>
            </div>

            <form method="POST" action="{{ route('stock-out.dyeing.issue') }}" class="space-y-3 pt-2 text-xs">
                @csrf

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Pilih Benang Greige Mentah</label>
                    <select name="product_id" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">-- Pilih Benang Mentah --</option>
                        @foreach($rawMaterials as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Jumlah Kirim (kg)</label>
                        <input type="number" step="0.01" min="0.01" name="qty" required placeholder="Contoh: 200"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Target Warna Celup</label>
                        <input type="text" name="color_target" required placeholder="Contoh: Hitam Reaktif Jet Black"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Vendor Celup Tujuan</label>
                        <select name="vendor_location_id" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                            @foreach($dyeingLocations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Nomor Lot Benang</label>
                        <input type="text" name="batch_number" placeholder="LOT-COT30-DYE"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Kirim ke Vendor Celup</span>
                </button>
            </form>
        </div>

        <!-- Card 2: Form Terima Hasil Celup (Dyeing In) -->
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">arrow_downward</span>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider">2. Terima Kembali Benang Selesai Celup</h3>
                    <p class="text-[11px] text-[#757681]">Penerimaan kembali benang warna jadi dari vendor ke gudang</p>
                </div>
            </div>

            <form method="POST" action="{{ route('stock-out.dyeing.receive') }}" class="space-y-3 pt-2 text-xs">
                @csrf

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Pilih SKU Benang Celup Selesai</label>
                    <select name="product_id" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">-- Pilih Produk Benang Jadi --</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Jumlah Diterima (kg)</label>
                        <input type="number" step="0.01" min="0.01" name="qty" required placeholder="Contoh: 195"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Nomor Surat Jalan / Order</label>
                        <input type="text" name="dyeing_order_number" placeholder="DYE-2026-001"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Dari Vendor</label>
                        <select name="vendor_location_id" class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                            @foreach($dyeingLocations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Nomor Lot Hasil Celup</label>
                        <input type="text" name="batch_number" placeholder="LOT-DYED-BLK01"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Simpan Penerimaan Hasil Celup</span>
                </button>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e5eeff]">
            <h3 class="text-sm font-bold text-[#001849]">Riwayat Mutasi Proses Celup</h3>
            <p class="text-xs text-[#757681]">Audit pergerakan barang ke dan dari Vendor Celup Subkontrak</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-2.5 px-4">No. Mutasi &amp; Waktu</th>
                        <th class="py-2.5 px-4">Kategori</th>
                        <th class="py-2.5 px-4">Material</th>
                        <th class="py-2.5 px-4">Lokasi (Asal &rarr; Tujuan)</th>
                        <th class="py-2.5 px-4 text-right">Kuantitas</th>
                        <th class="py-2.5 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($dyeingMoves as $m)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-[#001849]">
                            {{ $m->move_number }}
                            <div class="text-[10px] text-[#757681] font-sans mt-0.5">{{ $m->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @if($m->category === 'dyeing_return')
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full font-semibold border border-emerald-200">
                                    <span class="material-symbols-outlined text-[13px]">arrow_downward</span>
                                    Kembali Dari Celup
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] text-[#994700] bg-[#ffdbc8]/60 px-2 py-0.5 rounded-full font-semibold border border-[#ffb68b]">
                                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span>
                                    Kirim Proses Celup
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-[#0b1c30] block">{{ $m->product->name }}</span>
                            <span class="text-[10px] text-[#757681] font-mono block">SKU: {{ $m->product->code }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-medium text-[#0b1c30]">{{ $m->fromLocation->name }}</span>
                            <span class="text-[#757681]"> &rarr; </span>
                            <span class="font-semibold text-[#0d2c6c]">{{ $m->toLocation->name }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-sm text-[#0b1c30] tabular-nums">
                            {{ number_format($m->qty, 2) }} <span class="text-xs font-normal text-[#757681]">{{ $m->uom }}</span>
                        </td>
                        <td class="py-3 px-4 text-[#757681]">
                            {{ $m->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-[#757681]">
                            Belum ada riwayat mutasi proses celup.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

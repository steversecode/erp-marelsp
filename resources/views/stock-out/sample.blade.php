@extends('layouts.app')

@section('title', 'Pengeluaran Sample')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">science</span>
                <span>OUTBOUND R&amp;D / SAMPLE</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Pengeluaran Material Sample &amp; Uji Coba
            </h2>
            <p class="text-xs text-[#757681]">
                Pencatatan material/benang yang dikeluarkan untuk uji coba, pembuatan swatch, atau R&amp;D
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold self-start">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Form Pengeluaran Sample -->
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-[#fb7800] text-[18px]">science</span>
                <span>Form Pengeluaran Sample</span>
            </h3>

            <form method="POST" action="{{ route('stock-out.sample.store') }}" class="space-y-3 text-xs">
                @csrf

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Material yang Dikeluarkan</label>
                    <select name="product_id" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">-- Pilih Material --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Jumlah Qty Sample</label>
                    <input type="number" step="0.0001" min="0.0001" name="qty" required placeholder="Contoh: 2.5"
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">No. Referensi Sample / Tiket</label>
                    <input type="text" name="sample_reference" placeholder="SMPL-2026-001"
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Keperluan / Tujuan Sample</label>
                    <textarea name="purpose" rows="2" required placeholder="Contoh: Pembuatan mockup swatch kaos warna hitam untuk buyer Uniqlo"
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none"></textarea>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 rounded-lg bg-[#fb7800] hover:bg-[#994700] text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Keluarkan Material Sample</span>
                </button>
            </form>
        </div>

        <!-- Info Card -->
        <div class="lg:col-span-2 bg-[#001849] rounded-xl p-6 text-white shadow-sm flex flex-col justify-between border border-[#202e5a]">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#0d2c6c] flex items-center justify-center text-[#fb7800]">
                    <span class="material-symbols-outlined text-[24px]">science</span>
                </div>
                <h3 class="text-base font-bold font-display">Pengeluaran Sample Tanpa Mengganggu HPP Produksi</h3>
                <p class="text-xs text-[#b3c5ff] leading-relaxed">
                    Pengeluaran material untuk keperluan sample/R&amp;D tidak memerlukan pembuatan Manufacturing Order massal. Mutasi dipindahkan langsung ke lokasi <strong>VIRTUAL-SAMPLE</strong> sehingga biaya bahan dialokasikan langsung ke akun Beban Sampel/R&amp;D.
                </p>
            </div>
            <div class="p-3 rounded-xl bg-[#081844] border border-[#202e5a] text-xs text-[#dae1ff] flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-400 text-[18px]">check_circle</span>
                <span>Stok fisik di Gudang Utama berkurang secara otomatis dan tercatat rapi pada buku besar mutasi.</span>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e5eeff]">
            <h3 class="text-sm font-bold text-[#001849]">Riwayat Pengeluaran Sample</h3>
            <p class="text-xs text-[#757681]">Daftar material yang telah dikeluarkan untuk kebutuhan R&amp;D</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-2.5 px-4">No. Mutasi &amp; Waktu</th>
                        <th class="py-2.5 px-4">No. Ref Sample</th>
                        <th class="py-2.5 px-4">Material</th>
                        <th class="py-2.5 px-4 text-right">Jumlah Keluar</th>
                        <th class="py-2.5 px-4">Keperluan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($sampleMoves as $m)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-[#001849]">
                            {{ $m->move_number }}
                            <div class="text-[10px] text-[#757681] font-sans mt-0.5">{{ $m->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-[#994700]">
                            {{ $m->reference_number ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-[#0b1c30] block">{{ $m->product->name }}</span>
                            <span class="text-[10px] text-[#757681] font-mono block">SKU: {{ $m->product->code }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-sm text-[#994700] tabular-nums">
                            -{{ number_format($m->qty, 2) }} <span class="text-xs font-normal text-[#757681]">{{ $m->uom }}</span>
                        </td>
                        <td class="py-3 px-4 text-[#757681]">
                            {{ $m->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-[#757681]">
                            Belum ada riwayat pengeluaran sample.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Penerimaan Pembelian (PO)')

@section('content')
<div class="space-y-5 animate-in fade-in duration-150">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-xl border border-[#e5eeff] shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#fb7800] font-bold">
                <span class="material-symbols-outlined text-[16px]">move_to_inbox</span>
                <span>INBOUND LOGISTIK</span>
            </div>
            <h2 class="text-xl font-bold text-[#001849] font-display mt-0.5">
                Penerimaan Pembelian (Purchase Order)
            </h2>
            <p class="text-xs text-[#757681]">
                Pencatatan barang/benang masuk dari hasil pembelian ke supplier langsung ke Gudang Utama
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg bg-[#eff4ff] text-[#0d2c6c] hover:bg-[#dce9ff] text-xs font-semibold self-start">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Top Action Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Form Buat PO Cepat -->
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-[#fb7800] text-[18px]">add_shopping_cart</span>
                <span>Buat Purchase Order Baru</span>
            </h3>

            <form method="POST" action="{{ route('stock-in.po.store') }}" class="space-y-3 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Nama Supplier</label>
                    <input type="text" name="supplier_name" required placeholder="PT Mitra Benang Jaya"
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Tanggal Order</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-[#001849]">Material Dipesan</label>
                    <select name="product_id" required class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                        <option value="">-- Pilih Material --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Jumlah Qty</label>
                        <input type="number" step="0.01" min="0.01" name="ordered_qty" required placeholder="500"
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-[#001849]">Harga Satuan (Rp)</label>
                        <input type="number" step="1" min="0" name="unit_price" value="85000" required
                            class="w-full px-3.5 py-2 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 rounded-lg bg-[#fb7800] hover:bg-[#994700] text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Simpan Purchase Order</span>
                </button>
            </form>
        </div>

        <!-- Info Card -->
        <div class="lg:col-span-2 bg-[#001849] rounded-xl p-6 text-white shadow-sm flex flex-col justify-between border border-[#202e5a]">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#0d2c6c] flex items-center justify-center text-[#fb7800]">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                </div>
                <h3 class="text-base font-bold font-display">Penerimaan Barang Masuk (Goods Receipt)</h3>
                <p class="text-xs text-[#b3c5ff] leading-relaxed">
                    Setiap konfirmasi penerimaan barang dari supplier akan otomatis membukukan transaksi <strong>Stock Move (Kategori: po_receipt)</strong> dari lokasi Supplier ke Gudang Utama (WH-MAIN). Saldo stok langsung ter-update di buku besar.
                </p>
            </div>
            <div class="p-3 rounded-xl bg-[#081844] border border-[#202e5a] text-xs text-[#dae1ff] flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-400 text-[18px]">verified</span>
                <span>Tercatat dengan nomor Lot/Batch untuk penelusuran kualitas benang.</span>
            </div>
        </div>
    </div>

    <!-- PO List & Receipt Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-[#e5eeff] flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#001849]">Daftar Purchase Order &amp; Penerimaan</h3>
                <p class="text-xs text-[#757681]">Input kuantitas barang yang tiba secara penuh atau bertahap (parsial)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-2.5 px-4">No. PO &amp; Tanggal</th>
                        <th class="py-2.5 px-4">Supplier</th>
                        <th class="py-2.5 px-4">Material Dipesan</th>
                        <th class="py-2.5 px-4 text-right">Dipesan</th>
                        <th class="py-2.5 px-4 text-right">Telah Diterima</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4 text-right">Aksi Penerimaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($purchaseOrders as $po)
                        @foreach($po->items as $item)
                        @php
                            $remaining = $item->ordered_qty - $item->received_qty;
                        @endphp
                        <tr class="hover:bg-[#f8f9ff] transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#001849] whitespace-nowrap">
                                {{ $po->po_number }}
                                <div class="text-[10px] text-[#757681] font-sans mt-0.5">{{ $po->order_date ? $po->order_date->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold text-[#0b1c30]">
                                {{ $po->supplier_name }}
                            </td>
                            <td class="py-3 px-4 min-w-[180px]">
                                <span class="font-bold text-[#0b1c30] block">{{ $item->product->name }}</span>
                                <span class="text-[10px] text-[#757681] font-mono block">SKU: {{ $item->product->code }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-sm text-[#0b1c30] tabular-nums whitespace-nowrap">
                                {{ number_format($item->ordered_qty, 2) }} {{ $item->uom }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-sm text-emerald-800 tabular-nums whitespace-nowrap">
                                {{ number_format($item->received_qty, 2) }} {{ $item->uom }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($po->status === 'received')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai Diterima</span>
                                @elseif($po->status === 'partial_received')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ffdbc8] text-[#994700]">Parsial</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">Menunggu Barang</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if($remaining > 0)
                                    <button type="button" 
                                        onclick="openReceiveModal({{ $po->id }}, {{ $item->id }}, '{{ addslashes($item->product->name) }}', {{ $remaining }}, '{{ $item->uom }}')"
                                        class="px-3 py-1.5 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white font-bold text-xs inline-flex items-center gap-1 transition-colors shadow">
                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                        <span>Terima Barang</span>
                                    </button>
                                @else
                                    <span class="text-[11px] text-emerald-700 font-bold flex items-center justify-end gap-1">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        <span>Komplit</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[#757681]">
                            Belum ada dokumen Purchase Order.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL RECEIVE PO -->
<div id="receiveModal" class="fixed inset-0 bg-[#001849]/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl border border-[#c5c6d2] shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#eff4ff]">
            <h3 class="font-display font-bold text-base text-[#001849]">Penerimaan Barang Masuk (Goods Receipt)</h3>
            <button onclick="closeReceiveModal()" class="p-1 rounded-lg text-[#757681] hover:text-[#0b1c30]">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="receiveForm" method="POST" action="" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="item_id" id="modalItemId">

            <div class="p-3 rounded-xl bg-[#eff4ff] border border-[#dce9ff]">
                <span class="text-[#757681]">Material:</span>
                <span id="modalProductName" class="font-bold text-[#001849] block mt-0.5">-</span>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#001849]">Jumlah Qty yang Diterima Saat Ini</label>
                <div class="relative">
                    <input type="number" step="0.0001" min="0.0001" name="qty" id="modalQtyInput" required
                        class="w-full px-3.5 py-2.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs font-bold focus:ring-1 focus:ring-[#0d2c6c] outline-none">
                    <span id="modalUom" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#757681] font-semibold">kg</span>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#001849]">Nomor Lot / Batch Benang Supplier</label>
                <input type="text" name="batch_number" placeholder="Contoh: LOT-COT30-202603A"
                    class="w-full px-3.5 py-2.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs focus:ring-1 focus:ring-[#0d2c6c] outline-none">
            </div>

            <div class="pt-3 border-t border-[#eff4ff] flex items-center justify-end gap-2">
                <button type="button" onclick="closeReceiveModal()" class="px-4 py-2 rounded-lg bg-[#eff4ff] text-[#757681] font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white text-xs font-bold shadow">
                    Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openReceiveModal(poId, itemId, prodName, remainingQty, uom) {
        const form = document.getElementById('receiveForm');
        form.action = `{{ url('/stock-in/po') }}/${poId}/receive`;
        document.getElementById('modalItemId').value = itemId;
        document.getElementById('modalProductName').textContent = prodName;
        document.getElementById('modalQtyInput').value = remainingQty;
        document.getElementById('modalUom').textContent = uom;
        const m = document.getElementById('receiveModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }

    function closeReceiveModal() {
        const m = document.getElementById('receiveModal');
        m.classList.add('hidden');
        m.style.display = 'none';
    }
</script>
@endpush

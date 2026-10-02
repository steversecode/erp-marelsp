@extends('layouts.app')

@section('title', 'Bills of Materials (BoM)')

@section('content')
<div class="space-y-4 animate-in fade-in duration-150">
    <!-- Top Control Bar (Odoo Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 sm:p-4 rounded-xl border border-[#e5eeff] shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('manufacturing.boms.create') }}" class="px-3.5 py-1.5 rounded-lg bg-[#0d2c6c] hover:bg-[#001849] text-white text-xs font-semibold flex items-center gap-1.5 shadow-xs transition-all">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>New</span>
            </a>
            <div class="h-5 w-px bg-[#e5eeff]"></div>
            <div>
                <h1 class="text-base font-bold text-[#001849] font-display">Bills of Materials</h1>
                <p class="text-[11px] text-[#757681]">Master formula &amp; struktur komponen produk manufaktur</p>
            </div>
        </div>

        <!-- KPI Badges -->
        <div class="flex items-center gap-2 overflow-x-auto text-[11px]">
            <span class="px-2.5 py-1 bg-[#eff4ff] text-[#0d2c6c] rounded-lg font-semibold border border-[#dce9ff]">
                Total: <strong>{{ $totalBoms }}</strong>
            </span>
            <span class="px-2.5 py-1 bg-[#e8f5e9] text-[#2e7d32] rounded-lg font-semibold border border-[#c8e6c9]">
                Manufacture: <strong>{{ $normalCount }}</strong>
            </span>
            <span class="px-2.5 py-1 bg-[#fff3e0] text-[#e65100] rounded-lg font-semibold border border-[#ffe0b2]">
                Kit: <strong>{{ $kitCount }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar with Bulk Actions -->
    <div class="bg-white p-3 rounded-xl border border-[#e5eeff] shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Search Form -->
        <form method="GET" action="{{ route('manufacturing.boms.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757681] text-[18px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode referensi, nama BoM, atau nama produk..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg focus:outline-hidden focus:border-[#fb7800] focus:ring-1 focus:ring-[#fb7800]">
            </div>

            <!-- Filter Type -->
            <select name="type" onchange="this.form.submit()" class="text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg px-2.5 py-1.5 text-[#0b1c30]">
                <option value="">Semua Tipe BoM</option>
                <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Manufacture this product</option>
                <option value="phantom" {{ request('type') == 'phantom' ? 'selected' : '' }}>Kit</option>
            </select>

            <!-- Filter Product -->
            <select name="product_id" onchange="this.form.submit()" class="text-xs bg-[#f8f9ff] border border-[#c5c6d2] rounded-lg px-2.5 py-1.5 text-[#0b1c30] max-w-[200px]">
                <option value="">Semua Produk Output</option>
                @foreach($allProducts as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>[{{ $p->code }}] {{ $p->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-1.5 bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] font-semibold text-xs rounded-lg transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['q', 'type', 'product_id', 'is_active']))
                <a href="{{ route('manufacturing.boms.index') }}" class="px-2.5 py-1.5 text-xs text-[#ba1a1a] hover:underline font-medium">
                    Reset
                </a>
            @endif
        </form>

        <!-- Bulk Action Container (Odoo style) -->
        <div id="bulkActionContainer" class="hidden items-center gap-2">
            <span class="text-xs font-semibold text-[#001849]">
                <span id="selectedCount">0</span> BoM terpilih
            </span>
            <form id="bulkDeleteForm" method="POST" action="{{ route('manufacturing.boms.bulk-destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus BoM yang dipilih?');">
                @csrf
                <div id="bulkInputContainer"></div>
                <button type="submit" class="px-3 py-1.5 bg-[#ba1a1a] hover:bg-[#93000a] text-white font-semibold text-xs rounded-lg flex items-center gap-1 shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-[15px]">delete</span>
                    <span>Hapus Massal</span>
                </button>
            </form>
        </div>
    </div>

    <!-- BoM Tree / List View Table -->
    <div class="bg-white rounded-xl border border-[#e5eeff] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-semibold uppercase tracking-wider border-b border-[#e5eeff]">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">
                            <input type="checkbox" id="selectAll" class="rounded border-[#c5c6d2] text-[#fb7800] focus:ring-[#fb7800] cursor-pointer">
                        </th>
                        <th class="py-2.5 px-4">Reference (Code)</th>
                        <th class="py-2.5 px-4">Product Output</th>
                        <th class="py-2.5 px-4 text-center">BoM Type</th>
                        <th class="py-2.5 px-4 text-right">Quantity</th>
                        <th class="py-2.5 px-4 text-center">Unit of Measure</th>
                        <th class="py-2.5 px-4 text-center">Components</th>
                        <th class="py-2.5 px-4 text-right">Est. Cost</th>
                        <th class="py-2.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eff4ff]">
                    @forelse($boms as $b)
                    <tr class="hover:bg-[#f8f9ff] transition-colors group">
                        <td class="py-2.5 px-3 text-center">
                            <input type="checkbox" name="selected_boms[]" value="{{ $b->id }}" class="bom-checkbox rounded border-[#c5c6d2] text-[#fb7800] focus:ring-[#fb7800] cursor-pointer">
                        </td>
                        <td class="py-2.5 px-4 font-mono font-bold text-[#001849] whitespace-nowrap">
                            <a href="{{ route('manufacturing.boms.show', $b->id) }}" class="text-[#0d2c6c] hover:underline flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] text-[#fb7800]">account_tree</span>
                                <span>{{ $b->code }}</span>
                            </a>
                            <div class="text-[10px] text-[#757681] font-sans font-normal truncate max-w-[200px]">{{ $b->name }}</div>
                        </td>
                        <td class="py-2.5 px-4">
                            @if($b->product)
                                <a href="{{ route('products.show', $b->product_id) }}" class="font-bold text-[#0b1c30] hover:text-[#fb7800]">
                                    {{ $b->product->name }}
                                </a>
                                <div class="text-[10px] text-[#757681] font-mono">SKU: {{ $b->product->code }}</div>
                            @else
                                <span class="text-[#ba1a1a] italic">- Produk Terhapus -</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            @if($b->type === 'phantom')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#fff3e0] text-[#e65100] border border-[#ffe0b2]">
                                    Kit
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e8f5e9] text-[#2e7d32] border border-[#c8e6c9]">
                                    Manufacture
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono font-bold text-[#001849]">
                            {{ number_format((float)$b->quantity, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-4 text-center font-medium text-[#757681]">
                            <span class="px-2 py-0.5 bg-[#eff4ff] text-[#0d2c6c] rounded font-semibold text-[11px]">
                                {{ $b->uom }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#eff4ff] text-[#0d2c6c]">
                                <span class="material-symbols-outlined text-[13px]">view_list</span>
                                <span>{{ $b->items_count }} items</span>
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono font-semibold text-[#001849]">
                            Rp {{ number_format($b->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('manufacturing.boms.show', $b->id) }}" class="p-1.5 rounded-lg text-[#0d2c6c] hover:bg-[#eff4ff] transition-colors" title="Lihat / Edit Form BoM">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                </a>
                                <form method="POST" action="{{ route('manufacturing.boms.destroy', $b->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus BoM [{{ $b->code }}]?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-[#ba1a1a] hover:bg-[#ffebee] transition-colors" title="Hapus BoM">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center text-[#757681]">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-[#c5c6d2]">account_tree</span>
                                <span class="font-medium">Belum ada data Bill of Materials (BoM).</span>
                                <a href="{{ route('manufacturing.boms.create') }}" class="mt-2 px-4 py-2 bg-[#0d2c6c] text-white rounded-lg text-xs font-semibold hover:bg-[#001849]">
                                    + Buat BoM Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($boms->hasPages())
        <div class="p-3 border-t border-[#e5eeff] bg-[#f8f9ff] flex items-center justify-between">
            <div class="text-[11px] text-[#757681]">
                Menampilkan {{ $boms->firstItem() }} - {{ $boms->lastItem() }} dari {{ $boms->total() }} BoM
            </div>
            <div>
                {{ $boms->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.bom-checkbox');
        const bulkActionContainer = document.getElementById('bulkActionContainer');
        const selectedCount = document.getElementById('selectedCount');
        const bulkInputContainer = document.getElementById('bulkInputContainer');

        function updateBulkState() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            selectedCount.textContent = selected.length;
            
            if (selected.length > 0) {
                bulkActionContainer.classList.remove('hidden');
                bulkActionContainer.classList.add('flex');
            } else {
                bulkActionContainer.classList.add('hidden');
                bulkActionContainer.classList.remove('flex');
            }

            bulkInputContainer.innerHTML = '';
            selected.forEach(cb => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'ids[]';
                hiddenInput.value = cb.value;
                bulkInputContainer.appendChild(hiddenInput);
            });
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkState();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                selectAll.checked = Array.from(checkboxes).every(c => c.checked);
                updateBulkState();
            });
        });
    });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', "On Hand: [{$product->code}] {$product->name}")

@section('content')
<div x-data="{ 
    isEditing: false,
    rows: [
        @foreach($quants as $q)
        {
            id: {{ $q['location']->id }},
            location_id: '{{ $q['location']->id }}',
            location_name: '{{ addslashes($q['location']->name) }}',
            location_code: '{{ addslashes($q['location']->code) }}',
            location_type: '{{ addslashes($q['location']->type) }}',
            lot_number: '',
            on_hand: {{ (float)$q['on_hand'] }},
            initial_on_hand: {{ (float)$q['on_hand'] }},
            reserved: {{ (float)$q['reserved'] }},
            available: {{ (float)$q['available'] }},
            unit: '{{ addslashes($product->uom) }}',
            valuation: {{ (float)$q['valuation'] }},
            is_new: false,
            is_row_editing: false
        },
        @endforeach
    ],
    locations: [
        @foreach($locations as $loc)
        {
            id: '{{ $loc->id }}',
            name: '{{ addslashes($loc->name) }}',
            code: '{{ addslashes($loc->code) }}',
            type: '{{ addslashes($loc->type) }}'
        },
        @endforeach
    ],
    searchFilter: '',
    defaultLocationId: '{{ $locations->firstWhere('type', 'internal')?->id ?? $locations->first()?->id }}',
    
    get filteredRows() {
        if (!this.searchFilter) return this.rows;
        const q = this.searchFilter.toLowerCase();
        return this.rows.filter(r => 
            (r.location_name && r.location_name.toLowerCase().includes(q)) ||
            (r.location_code && r.location_code.toLowerCase().includes(q)) ||
            (r.lot_number && r.lot_number.toLowerCase().includes(q))
        );
    },

    addNewRow() {
        this.isEditing = true;
        const defaultLoc = this.locations.find(l => l.id == this.defaultLocationId) || this.locations[0];
        this.rows.push({
            id: 'new_' + Date.now(),
            location_id: defaultLoc ? defaultLoc.id : '',
            location_name: defaultLoc ? defaultLoc.name : '',
            location_code: defaultLoc ? defaultLoc.code : '',
            location_type: defaultLoc ? defaultLoc.type : '',
            lot_number: '',
            on_hand: 0.00,
            initial_on_hand: 0.00,
            reserved: 0.00,
            available: 0.00,
            unit: '{{ addslashes($product->uom) }}',
            valuation: 0,
            is_new: true,
            is_row_editing: true
        });
    },

    removeRow(index) {
        this.rows.splice(index, 1);
        if (this.rows.every(r => !r.is_new && !r.is_row_editing)) {
            this.isEditing = false;
        }
    },

    editRow(row) {
        this.isEditing = true;
        row.is_row_editing = true;
    },

    updateLocation(row, locId) {
        row.location_id = locId;
        const loc = this.locations.find(l => l.id == locId);
        if (loc) {
            row.location_name = loc.name;
            row.location_code = loc.code;
            row.location_type = loc.type;
        }
    },

    discard() {
        if (confirm('Discard all unsaved quantity changes?')) {
            window.location.reload();
        }
    }
}" class="space-y-4 animate-in fade-in duration-150 relative">

    <!-- Top Action Toolbar (Odoo 17/18 Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        
        <!-- Left: Odoo Save / Discard OR New Button & Breadcrumb -->
        <div class="flex items-center gap-3">
            <!-- Mode 1: When Editing or New Row Added -> Show "Save" & "Discard" Buttons -->
            <div x-show="isEditing" class="flex items-center gap-2">
                <button type="button" @click="$refs.gridForm.submit()" class="px-5 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save</span>
                </button>
                <button type="button" @click="discard()" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] text-xs font-semibold transition-all cursor-pointer">
                    <span>Discard</span>
                </button>
            </div>

            <!-- Mode 2: Normal View Mode -> Show "New" Button -->
            <div x-show="!isEditing" class="flex items-center gap-2">
                <button type="button" @click="addNewRow()" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add_box</span>
                    <span>New</span>
                </button>
            </div>

            <!-- Breadcrumb Navigation -->
            <div class="border-l border-[#e5eeff] pl-3 py-1 text-xs">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('inventory.index') }}" class="hover:text-[#0d2c6c] hover:underline font-semibold">Products</a>
                    <span>/</span>
                    <a href="{{ route('products.show', $product->id) }}" class="text-[#0d2c6c] hover:underline font-bold">
                        [{{ $product->code }}] {{ $product->name }}
                    </a>
                </div>
                <div class="text-[11px] text-[#fb7800] font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">settings_suggest</span>
                    <span>Update Quantity (Stock Opname)</span>
                </div>
            </div>
        </div>

        <!-- Right Controls -->
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.moves', ['product_id' => $product->id]) }}" class="px-3 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-semibold flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">history</span>
                <span>History Moves</span>
            </a>
            <a href="{{ route('products.show', $product->id) }}" class="px-3 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold transition-colors">
                ← Back to Product
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">error</span>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-4 space-y-0.5 font-normal">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Odoo Filter & Search Tags Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#e5eeff] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2 flex-1">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                <span>Internal Locations</span>
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]">
                <span>On Hand &gt; 0</span>
            </span>

            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <span class="material-symbols-outlined text-[#757681] text-[16px] absolute left-2.5 top-1/2 -translate-y-1/2">search</span>
                <input type="text" x-model="searchFilter" placeholder="Search location or lot/serial..." 
                    class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-xs outline-none focus:ring-1 focus:ring-[#0d2c6c]">
            </div>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <div class="text-right">
                <span class="text-[#757681]">Total Stock:</span>
                <span class="font-bold font-mono text-[#001849] ml-1">{{ number_format($totalOnHand, 2) }} {{ $product->uom }}</span>
            </div>
            <div class="text-right border-l border-[#e5eeff] pl-3">
                <span class="text-[#757681]">Valuation:</span>
                <span class="font-bold font-mono text-[#001849] ml-1">Rp {{ number_format($totalValuation, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- MAIN EDITABLE TABLE (Odoo editable='bottom' Tree View) -->
    <form x-ref="gridForm" action="{{ route('products.on-hand.save-grid', $product->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl border border-[#e5eeff] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#eff4ff] text-[#757681] text-[11px] font-bold uppercase tracking-wider border-b border-[#e5eeff]">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">#</th>
                            <th class="py-3 px-4 min-w-[200px]">Location</th>
                            <th class="py-3 px-4 min-w-[220px]">Product</th>
                            <th class="py-3 px-4 min-w-[150px]">Package / Lot Serial</th>
                            <th class="py-3 px-4 text-right min-w-[130px]">On Hand Qty</th>
                            <th class="py-3 px-4 text-right">Reserved</th>
                            <th class="py-3 px-3 text-center">Unit</th>
                            <th class="py-3 px-4 text-right">Valuation</th>
                            <th class="py-3 px-4 text-center w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eff4ff]">
                        <template x-for="(row, index) in filteredRows" :key="row.id">
                            <tr class="hover:bg-[#f8f9ff] transition-colors" :class="(row.is_new || row.is_row_editing) ? 'bg-[#fff8f2]' : ''">
                                
                                <!-- Index / Checkbox -->
                                <td class="py-2.5 px-3 text-center text-[#757681] font-mono text-[11px]">
                                    <span x-text="index + 1"></span>
                                </td>

                                <!-- Location (Editable Dropdown or Static Display) -->
                                <td class="py-2 px-4">
                                    <template x-if="row.is_new || row.is_row_editing">
                                        <div>
                                            <select :name="'lines[' + index + '][location_id]'" x-model="row.location_id" @change="updateLocation(row, $event.target.value)" required 
                                                class="w-full px-2.5 py-1.5 rounded-lg bg-white border-2 border-[#001849] text-xs font-bold text-[#001849] focus:ring-2 focus:ring-[#0d2c6c] outline-none shadow-xs">
                                                <template x-for="loc in locations" :key="loc.id">
                                                    <option :value="loc.id" x-text="loc.name + ' (' + loc.code + ')'" :selected="row.location_id == loc.id"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="!row.is_new && !row.is_row_editing">
                                        <div @dblclick="editRow(row)" class="cursor-pointer group flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-[#001849] flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[16px] text-[#fb7800]">warehouse</span>
                                                    <span x-text="row.location_name"></span>
                                                </div>
                                                <span class="text-[10px] font-mono text-[#757681]" x-text="row.location_code + ' (' + row.location_type + ')'"></span>
                                            </div>
                                            <span class="material-symbols-outlined text-[14px] text-[#757681]/40 opacity-0 group-hover:opacity-100 transition-opacity">edit</span>
                                        </div>
                                    </template>
                                </td>

                                <!-- Product (Static Display with Code) -->
                                <td class="py-2.5 px-4 font-medium text-[#0b1c30]">
                                    <span class="font-mono font-bold text-[#0d2c6c]">[{{ $product->code }}]</span>
                                    <span>{{ $product->name }}</span>
                                </td>

                                <!-- Lot / Package Serial Number (Editable Input) -->
                                <td class="py-2 px-4">
                                    <template x-if="row.is_new || row.is_row_editing">
                                        <input type="text" :name="'lines[' + index + '][lot_number]'" x-model="row.lot_number" placeholder="Optional Lot / Batch #" 
                                            class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-[#dce9ff] text-xs font-mono text-[#001849] focus:border-[#001849] outline-none">
                                    </template>
                                    <template x-if="!row.is_new && !row.is_row_editing">
                                        <div @dblclick="editRow(row)" class="cursor-pointer">
                                            <span x-show="row.lot_number" class="px-2 py-0.5 rounded text-[11px] font-mono bg-[#eff4ff] text-[#0d2c6c] border border-[#dce9ff]" x-text="row.lot_number"></span>
                                            <span x-show="!row.lot_number" class="text-[#757681]/60 italic text-[11px]">-</span>
                                        </div>
                                    </template>
                                </td>

                                <!-- On Hand Quantity (Directly Editable Number Input!) -->
                                <td class="py-2 px-4 text-right">
                                    <template x-if="row.is_new || row.is_row_editing">
                                        <div class="relative">
                                            <input type="number" step="0.001" min="0" :name="'lines[' + index + '][on_hand]'" x-model.number="row.on_hand" required 
                                                class="w-full pl-2 pr-2 py-1.5 rounded-lg bg-white border-2 border-[#001849] text-sm font-bold font-mono text-right text-[#001849] focus:ring-2 focus:ring-[#0d2c6c] outline-none shadow-xs">
                                        </div>
                                    </template>
                                    <template x-if="!row.is_new && !row.is_row_editing">
                                        <div @dblclick="editRow(row)" class="cursor-pointer group flex items-center justify-end gap-1.5">
                                            <span class="font-bold text-sm font-mono text-[#001849] tabular-nums" x-text="Number(row.on_hand).toFixed(2)"></span>
                                            <span class="material-symbols-outlined text-[14px] text-[#757681]/40 opacity-0 group-hover:opacity-100 transition-opacity">edit</span>
                                        </div>
                                    </template>
                                </td>

                                <!-- Reserved Quantity -->
                                <td class="py-2.5 px-4 text-right text-[#757681] font-mono tabular-nums">
                                    <span x-text="Number(row.reserved || 0).toFixed(2)"></span>
                                </td>

                                <!-- Unit of Measure -->
                                <td class="py-2.5 px-3 text-center font-mono text-[#757681]">
                                    {{ $product->uom }}
                                </td>

                                <!-- Valuation -->
                                <td class="py-2.5 px-4 text-right font-bold font-mono text-[#001849]">
                                    <span x-text="'Rp ' + (row.on_hand * {{ (float)$product->cost_price }}).toLocaleString('id-ID')"></span>
                                </td>

                                <!-- Actions -->
                                <td class="py-2 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <template x-if="row.is_new">
                                            <button type="button" @click="removeRow(index)" class="p-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors cursor-pointer" title="Remove line">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        </template>
                                        <template x-if="!row.is_new && !row.is_row_editing">
                                            <button type="button" @click="editRow(row)" class="p-1 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] transition-colors cursor-pointer" title="Edit row">
                                                <span class="material-symbols-outlined text-[15px]">edit</span>
                                            </button>
                                        </template>
                                        <template x-if="!row.is_new && row.is_row_editing">
                                            <button type="button" @click="row.is_row_editing = false" class="p-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors cursor-pointer" title="Done editing row">
                                                <span class="material-symbols-outlined text-[15px]">check</span>
                                            </button>
                                        </template>
                                        <a :href="'{{ url('inventory/moves') }}?product_id={{ $product->id }}&location_id=' + row.location_id" class="p-1 rounded-md bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] transition-colors" title="View History Moves">
                                            <span class="material-symbols-outlined text-[15px]">history</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State when 0 rows -->
                        <tr x-show="filteredRows.length === 0">
                            <td colspan="9" class="py-12 text-center text-[#757681]">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-[36px] text-[#757681]/40 mb-2">inventory_2</span>
                                    <h4 class="font-bold text-[#001849] text-sm">No Stock Records</h4>
                                    <p class="text-xs text-[#757681] mt-0.5 mb-3">Click <strong>+ Add a line / New</strong> to record initial physical stock.</p>
                                    <button type="button" @click="addNewRow()" class="px-4 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                        + Add a line (Set Initial Stock)
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Bottom Action (+ Add a line / New) -->
            <div class="p-3 bg-[#eff4ff]/40 border-t border-[#e5eeff] flex items-center justify-between">
                <button type="button" @click="addNewRow()" class="px-3 py-1.5 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-bold flex items-center gap-1 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Add a line</span>
                </button>

                <div x-show="isEditing" class="flex items-center gap-2">
                    <span class="text-[11px] text-[#fb7800] font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        <span>Unsaved changes in table</span>
                    </span>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

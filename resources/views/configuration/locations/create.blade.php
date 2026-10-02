@extends('layouts.app')

@section('title', 'New Location - Configuration')

@section('content')
<div x-data="{ 
    locationType: 'internal',
    isScrap: false,
    isReturn: false,
    locationName: '{{ old('name') }}',
    parentName: '',
    updateParent(el) {
        const selected = el.options[el.selectedIndex];
        this.parentName = selected && selected.value ? selected.getAttribute('data-name') : '';
    }
}" class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Form Header) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <button type="button" @click="$refs.locationForm.submit()" class="px-5 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">save</span>
                <span>Save</span>
            </button>
            <a href="{{ route('configuration.locations.index') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] text-xs font-semibold transition-all">
                <span>Discard</span>
            </a>

            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('configuration.locations.index') }}" class="hover:text-[#001849] font-semibold">Locations</a>
                    <span>/</span>
                    <span class="text-[#001849] font-bold">New Location</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('configuration.locations.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#757681] hover:text-[#001849] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to Locations
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1 shadow-xs">
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

    <!-- Odoo Form Sheet Container -->
    <form x-ref="locationForm" method="POST" action="{{ route('configuration.locations.store') }}" class="bg-white rounded-2xl border border-[#e5eeff] shadow-sm p-6 sm:p-8 space-y-8">
        @csrf

        <!-- Title Section: Location Name & Live Path Preview -->
        <div class="space-y-2 border-b border-[#eff4ff] pb-6">
            <div class="flex items-center justify-between">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#757681]">
                    Location Name <span class="text-rose-500">*</span>
                </label>
                <div class="text-xs text-[#757681] font-mono flex items-center gap-1">
                    <span class="text-[11px] uppercase font-bold text-[#fb7800]">Path Preview:</span>
                    <span x-text="parentName ? parentName + '/' + (locationName || '...') : (locationName || '...')" class="font-bold text-[#001849]"></span>
                </div>
            </div>
            <input type="text" name="name" id="name" x-model="locationName" value="{{ old('name') }}" placeholder="e.g. Stock, Gedung Bahan Baku Utama, Rak Benang A1, Production Floor..." required 
                class="w-full text-xl sm:text-2xl font-bold text-[#001849] placeholder:text-[#757681]/40 border-b-2 border-[#001849] focus:outline-none pb-1 bg-transparent">
        </div>

        <!-- Form Grid Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left Column: Parent, Type, Warehouse, Code -->
            <div class="space-y-5">
                <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5 border-b border-[#eff4ff] pb-2">
                    <span class="material-symbols-outlined text-[17px] text-[#fb7800]">account_tree</span>
                    Location Structure
                </h3>

                <!-- Parent Location -->
                <div class="space-y-1.5">
                    <label for="parent_id" class="block text-xs font-bold text-[#001849]">
                        Parent Location
                    </label>
                    <select name="parent_id" id="parent_id" @change="updateParent($event.target)" class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                        <option value="" data-name="">None (Top Level Root Hierarchy)</option>
                        @foreach($parentLocations as $pLoc)
                            <option value="{{ $pLoc->id }}" data-name="{{ $pLoc->complete_name }}" {{ old('parent_id') == $pLoc->id ? 'selected' : '' }}>
                                {{ $pLoc->complete_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#757681]">Creates hierarchical structure (e.g. Physical Locations &gt; WH &gt; Stock &gt; Shelf 1).</p>
                </div>

                <!-- Location Type -->
                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-bold text-[#001849]">
                        Location Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" id="type" x-model="locationType" required class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                        <option value="internal">Internal Location (Physical stock storage)</option>
                        <option value="view">View (Virtual parent / folder location)</option>
                        <option value="vendor">Vendor Location (Supplier source for incoming goods)</option>
                        <option value="customer">Customer Location (Customer destination for sales/deliveries)</option>
                        <option value="production">Production (Virtual manufacturing &amp; WIP floor)</option>
                        <option value="loss">Inventory Loss (Stock adjustments, discrepancies, scrap)</option>
                        <option value="transit">Transit Location (Inter-warehouse transfer storage)</option>
                        <option value="dyeing_subcon">Subcontractor (Vendor Celup / Finishing)</option>
                        <option value="sample">Sample / R&amp;D Division</option>
                    </select>
                </div>

                <!-- Warehouse Association -->
                <div class="space-y-1.5">
                    <label for="warehouse_id" class="block text-xs font-bold text-[#001849]">
                        Warehouse
                    </label>
                    <select name="warehouse_id" id="warehouse_id" class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                        <option value="">None (Virtual / Partner / Generic Location)</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#757681]">Associate this internal location with one of company warehouses.</p>
                </div>

                <!-- Location Code & Barcode -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="code" class="block text-xs font-bold text-[#001849]">
                            Location Short Code <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="e.g. WH-STOCK-A1" required 
                            class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono font-bold text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="barcode" class="block text-xs font-bold text-[#001849]">
                            Barcode Reference
                        </label>
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="e.g. LOC-00123" 
                            class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                    </div>
                </div>
            </div>

            <!-- Right Column: Properties, Address, Notes -->
            <div class="space-y-5">
                <h3 class="text-xs font-bold text-[#001849] uppercase tracking-wider flex items-center gap-1.5 border-b border-[#eff4ff] pb-2">
                    <span class="material-symbols-outlined text-[17px] text-[#fb7800]">tune</span>
                    Logistics &amp; Properties
                </h3>

                <!-- Properties Checkboxes Card -->
                <div class="p-4 rounded-xl bg-[#f8f9ff] border border-[#e5eeff] space-y-3">
                    <!-- Is Scrap -->
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_scrap" value="1" x-model="isScrap" {{ old('is_scrap') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded text-[#001849] focus:ring-[#001849]">
                        <div>
                            <span class="text-xs font-bold text-[#001849] block">Is a Scrap Location?</span>
                            <span class="text-[11px] text-[#757681] block">Allow this location to be chosen for damaged goods, defect items, or waste production.</span>
                        </div>
                    </label>

                    <!-- Is Return -->
                    <label class="flex items-start gap-2.5 cursor-pointer pt-2 border-t border-[#eff4ff]">
                        <input type="checkbox" name="is_return" value="1" x-model="isReturn" {{ old('is_return') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded text-[#001849] focus:ring-[#001849]">
                        <div>
                            <span class="text-xs font-bold text-[#001849] block">Is a Return Location?</span>
                            <span class="text-[11px] text-[#757681] block">Allow this location to store customer returns, leftover goods, or production scrap.</span>
                        </div>
                    </label>

                    <!-- Active -->
                    <label class="flex items-start gap-2.5 cursor-pointer pt-2 border-t border-[#eff4ff]">
                        <input type="checkbox" name="is_active" value="1" checked class="mt-0.5 w-4 h-4 rounded text-[#001849] focus:ring-[#001849]">
                        <div>
                            <span class="text-xs font-bold text-[#001849] block">Active</span>
                            <span class="text-[11px] text-[#757681] block">Inactive / archived locations are hidden from everyday inventory transfers.</span>
                        </div>
                    </label>
                </div>

                <!-- Address / Building Zone -->
                <div class="space-y-1.5">
                    <label for="address" class="block text-xs font-bold text-[#001849]">
                        Building Zone / Rack / Address
                    </label>
                    <textarea name="address" id="address" rows="2" placeholder="e.g. Gedung A Lantai 1, Rak Lorong 3, Kawasan Industri Rancaekek..." 
                        class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] focus:bg-white focus:border-[#001849] outline-none">{{ old('address') }}</textarea>
                </div>

                <!-- Internal Notes / Comment -->
                <div class="space-y-1.5">
                    <label for="comment" class="block text-xs font-bold text-[#001849]">
                        Internal Description &amp; Notes
                    </label>
                    <textarea name="comment" id="comment" rows="2" placeholder="Additional logistics notes for warehouse operators..." 
                        class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs text-[#001849] focus:bg-white focus:border-[#001849] outline-none">{{ old('comment') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-[#eff4ff] flex items-center justify-end gap-2">
            <a href="{{ route('configuration.locations.index') }}" class="px-4 py-2 rounded-xl bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                Save Location
            </button>
        </div>
    </form>
</div>
@endsection

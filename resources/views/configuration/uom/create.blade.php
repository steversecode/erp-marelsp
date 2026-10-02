@extends('layouts.app')

@section('title', 'New Unit of Measure - Configuration')

@section('content')
<div x-data="{ 
    uomType: 'reference',
    ratio: 1.0,
    rounding: 0.01,
    categoryId: '{{ $categories->first()?->id }}',
    addNewCategory: false,
    newCategoryName: ''
}" class="space-y-4 animate-in fade-in duration-150">

    <!-- Top Action & Navigation Header (Odoo Form View) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-[#e5eeff] shadow-sm">
        <div class="flex items-center gap-3">
            <button type="button" @click="$refs.uomForm.submit()" class="px-5 py-2 rounded-lg bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">save</span>
                <span>Save</span>
            </button>
            <a href="{{ route('configuration.uom.index') }}" class="px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] hover:text-[#001849] text-xs font-semibold transition-all">
                <span>Discard</span>
            </a>

            <div class="border-l border-[#e5eeff] pl-3 py-1">
                <div class="flex items-center gap-1.5 text-xs text-[#757681]">
                    <a href="{{ route('configuration.uom.index') }}" class="hover:text-[#001849] font-semibold">Units of Measure</a>
                    <span>/</span>
                    <span class="text-[#001849] font-bold">New</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('configuration.uom.index') }}" class="px-3.5 py-1.5 rounded-lg bg-[#eff4ff] text-[#757681] hover:text-[#001849] hover:bg-[#dce9ff] text-xs font-semibold transition-colors">
                ← Back to List
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
    <form x-ref="uomForm" method="POST" action="{{ route('configuration.uom.store') }}" class="bg-white rounded-2xl border border-[#e5eeff] shadow-sm p-6 sm:p-8 space-y-8">
        @csrf

        <!-- Title Section: Unit of Measure Name -->
        <div class="space-y-2 border-b border-[#eff4ff] pb-6">
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#757681]">
                Unit of Measure Name <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. kg, Pcs, Dozens, Meter, Roll..." required 
                class="w-full sm:max-w-md text-xl sm:text-2xl font-bold font-mono text-[#001849] placeholder:text-[#757681]/40 border-b-2 border-[#001849] focus:outline-none pb-1 bg-transparent">
        </div>

        <!-- Form Grid Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Left Column: Category & Type -->
            <div class="space-y-6">
                <!-- Category -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-[#001849]">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" @click="addNewCategory = !addNewCategory" class="text-[11px] text-[#fb7800] hover:underline font-semibold flex items-center gap-0.5">
                            <span x-text="addNewCategory ? '← Choose Existing' : '+ New Category'"></span>
                        </button>
                    </div>

                    <div x-show="!addNewCategory">
                        <select name="category_id" x-model="categoryId" class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-semibold text-[#001849] focus:bg-white focus:border-[#001849] outline-none transition-colors">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="addNewCategory" class="space-y-1">
                        <input type="text" name="new_category_name" x-model="newCategoryName" placeholder="Enter new category name (e.g. Area, Density)..." 
                            class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#fb7800] text-xs font-semibold text-[#001849] outline-none">
                        <p class="text-[10px] text-[#757681]">Category will be created automatically.</p>
                    </div>
                </div>

                <!-- Type (Radio Options) -->
                <div class="space-y-2.5">
                    <label class="block text-xs font-bold text-[#001849]">
                        Type <span class="text-rose-500">*</span>
                    </label>
                    
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-[#e5eeff] hover:bg-[#eff4ff]/50 cursor-pointer transition-colors" :class="uomType === 'reference' ? 'border-[#001849] bg-[#eff4ff]' : ''">
                            <input type="radio" name="uom_type" value="reference" x-model="uomType" @change="ratio = 1.0" class="mt-0.5 text-[#001849] focus:ring-[#001849]">
                            <div>
                                <span class="text-xs font-bold text-[#001849] block">Reference Unit of Measure for this category</span>
                                <span class="text-[11px] text-[#757681]">The standard baseline unit (e.g. 1 kg, 1 Unit, 1 meter). Ratio is automatically 1.0.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-[#e5eeff] hover:bg-[#eff4ff]/50 cursor-pointer transition-colors" :class="uomType === 'bigger' ? 'border-[#001849] bg-[#eff4ff]' : ''">
                            <input type="radio" name="uom_type" value="bigger" x-model="uomType" class="mt-0.5 text-[#001849] focus:ring-[#001849]">
                            <div>
                                <span class="text-xs font-bold text-[#001849] block">Bigger than the reference Unit of Measure</span>
                                <span class="text-[11px] text-[#757681]">Multiples of base unit (e.g. 1 Dozen = 12 Units, 1 Ton = 1000 kg).</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-[#e5eeff] hover:bg-[#eff4ff]/50 cursor-pointer transition-colors" :class="uomType === 'smaller' ? 'border-[#001849] bg-[#eff4ff]' : ''">
                            <input type="radio" name="uom_type" value="smaller" x-model="uomType" class="mt-0.5 text-[#001849] focus:ring-[#001849]">
                            <div>
                                <span class="text-xs font-bold text-[#001849] block">Smaller than the reference Unit of Measure</span>
                                <span class="text-[11px] text-[#757681]">Fractional subunits (e.g. 1 g = 0.001 kg, 1 cm = 0.01 m).</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Ratio & Rounding & Status -->
            <div class="space-y-6">
                <!-- Ratio / Factor -->
                <div class="space-y-1.5" x-show="uomType !== 'reference'">
                    <label for="ratio" class="block text-xs font-bold text-[#001849]">
                        <span x-show="uomType === 'bigger'">Ratio (Quantity in Reference Unit)</span>
                        <span x-show="uomType === 'smaller'">Ratio (Fraction of Reference Unit)</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.000001" min="0.000001" name="ratio" id="ratio" x-model="ratio" required 
                        class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono font-bold text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                    
                    <div class="p-3 rounded-lg bg-[#eff4ff] border border-[#dce9ff] text-[11px] text-[#0d2c6c]">
                        <span class="font-bold">Conversion Formula:</span>
                        <template x-if="uomType === 'bigger'">
                            <p class="mt-0.5">1 [This Unit] = <span class="font-mono font-bold" x-text="ratio"></span> [Base Reference Unit]</p>
                        </template>
                        <template x-if="uomType === 'smaller'">
                            <p class="mt-0.5">1 [This Unit] = <span class="font-mono font-bold" x-text="ratio"></span> [Base Reference Unit]</p>
                        </template>
                    </div>
                </div>

                <!-- Hidden ratio for reference type -->
                <input type="hidden" name="ratio" value="1.0" x-show="uomType === 'reference'" :disabled="uomType !== 'reference'">

                <!-- Rounding Precision -->
                <div class="space-y-1.5">
                    <label for="rounding" class="block text-xs font-bold text-[#001849]">
                        Rounding Precision <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.000001" min="0.000001" name="rounding" id="rounding" x-model="rounding" required 
                        class="w-full px-3.5 py-2 rounded-xl bg-[#eff4ff] border border-[#dce9ff] text-xs font-mono text-[#001849] focus:bg-white focus:border-[#001849] outline-none">
                    <p class="text-[10px] text-[#757681]">Rounding factor applied in inventory balances and procurement (e.g. 0.01 or 0.001).</p>
                </div>

                <!-- Active Status -->
                <div class="pt-2">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-[#001849] focus:ring-[#001849]">
                        <span class="text-xs font-bold text-[#001849]">Active</span>
                    </label>
                    <p class="text-[10px] text-[#757681] ml-6.5">If unchecked, this unit will be archived and hidden from selection dropdowns.</p>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-[#eff4ff] flex items-center justify-end gap-2">
            <a href="{{ route('configuration.uom.index') }}" class="px-4 py-2 rounded-xl bg-[#eff4ff] hover:bg-[#dce9ff] text-[#757681] text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-[#001849] hover:bg-[#0d2c6c] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                Save Unit of Measure
            </button>
        </div>
    </form>
</div>
@endsection

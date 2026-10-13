<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $stats = $this->getStats();
        @endphp

        {{-- Top Summary Metric Cards (Square Layout) --}}
        <div style="display: flex; flex-wrap: wrap; gap: 1.25rem;" class="flex flex-wrap gap-5">
            {{-- Total Valuation Card --}}
            <div style="flex: 0 0 270px; width: 270px; min-height: 210px; display: flex; flex-direction: column; justify-content: space-between;" class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6" />
                    </div>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                        Valuation
                    </span>
                </div>

                <div class="my-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-valuation') }}
                    </p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400">
                        {{ $stats['currency_code'] }} {{ number_format($stats['total_valuation'], 2, ',', '.') }}
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-2 text-xs text-gray-500 dark:border-white/5 dark:text-gray-400">
                    Nilai persediaan di seluruh lokasi internal
                </div>
            </div>

            {{-- Total Units Card --}}
            <div style="flex: 0 0 270px; width: 270px; min-height: 210px; display: flex; flex-direction: column; justify-content: space-between;" class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <x-filament::icon icon="heroicon-o-cube" class="h-6 w-6" />
                    </div>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                        On-Hand
                    </span>
                </div>

                <div class="my-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-units') }}
                    </p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400">
                        {{ number_format($stats['total_units'], 2, ',', '.') }}
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-2 text-xs text-gray-500 dark:border-white/5 dark:text-gray-400">
                    Total kuantitas fisik on-hand yang tersedia
                </div>
            </div>

            {{-- Total SKUs Card --}}
            <div style="flex: 0 0 270px; width: 270px; min-height: 210px; display: flex; flex-direction: column; justify-content: space-between;" class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                        <x-filament::icon icon="heroicon-o-tag" class="h-6 w-6" />
                    </div>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                        Products
                    </span>
                </div>

                <div class="my-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-products') }}
                    </p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-indigo-600 dark:text-indigo-400">
                        {{ number_format($stats['total_products'], 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">SKU</span>
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-2 text-xs text-gray-500 dark:border-white/5 dark:text-gray-400">
                    Jumlah produk unik dengan stok aktif
                </div>
            </div>
        </div>

        {{-- Filament Interactive Table --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $stats = $this->getStats();
        @endphp

        {{-- Top Summary Metric Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            {{-- Total Valuation Card --}}
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-valuation') }}
                        </p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                            {{ $stats['currency_code'] }} {{ number_format($stats['total_valuation'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Nilai akumulasi persediaan barang di seluruh lokasi internal
                </div>
            </div>

            {{-- Total Units Card --}}
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-units') }}
                        </p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">
                            {{ number_format($stats['total_units'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <x-filament::icon icon="heroicon-o-cube" class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Total kuantitas fisik on-hand yang tersedia
                </div>
            </div>

            {{-- Total SKUs Card --}}
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-products') }}
                        </p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">
                            {{ number_format($stats['total_products'], 0, ',', '.') }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">SKU</span>
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                        <x-filament::icon icon="heroicon-o-tag" class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
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

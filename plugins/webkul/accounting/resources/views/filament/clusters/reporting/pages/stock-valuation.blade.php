<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $stats = $this->getStats();
        @endphp

        {{-- Scoped Luxury KPI Styles --}}
        <style>
            .stock-kpi-container {
                display: flex;
                flex-wrap: wrap;
                gap: 1.25rem;
                margin-bottom: 1.5rem;
            }
            .stock-kpi-card {
                flex: 1 1 240px;
                max-width: 320px;
                min-width: 240px;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                padding: 1.25rem 1.25rem 1rem 1.25rem;
                display: flex;
                flex-direction: column;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                box-sizing: border-box;
            }
            .stock-kpi-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
                border-color: #cbd5e1;
            }
            .dark .stock-kpi-card {
                background: #111827;
                border-color: rgba(255, 255, 255, 0.08);
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.25);
            }
            .dark .stock-kpi-card:hover {
                border-color: rgba(255, 255, 255, 0.16);
            }
            .kpi-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
            }
            .kpi-icon-pill {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .kpi-chip {
                font-size: 0.7rem;
                font-weight: 600;
                padding: 3px 9px;
                border-radius: 9999px;
                letter-spacing: 0.02em;
            }
            .kpi-title {
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #64748b;
                margin: 0 0 0.25rem 0;
            }
            .dark .kpi-title {
                color: #94a3b8;
            }
            .kpi-number {
                font-size: 1.6rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                line-height: 1.2;
                margin: 0;
            }
            .kpi-desc {
                margin-top: 1rem;
                padding-top: 0.65rem;
                border-top: 1px solid #f1f5f9;
                font-size: 0.72rem;
                color: #64748b;
                display: flex;
                align-items: center;
                gap: 6px;
                line-height: 1.35;
            }
            .dark .kpi-desc {
                border-top-color: rgba(255, 255, 255, 0.06);
                color: #94a3b8;
            }
            .kpi-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                flex-shrink: 0;
            }
        </style>

        {{-- Top Summary Metric Cards (Neat Square Executive Layout) --}}
        <div class="stock-kpi-container">
            {{-- Card 1: Total Valuation --}}
            <div class="stock-kpi-card">
                <div class="kpi-head">
                    <div class="kpi-icon-pill" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669;">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                    </div>
                    <span class="kpi-chip" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                        Valuasi
                    </span>
                </div>

                <div>
                    <p class="kpi-title">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-valuation') }}
                    </p>
                    <p class="kpi-number" style="color: #059669;">
                        {{ $stats['currency_code'] }} {{ number_format($stats['total_valuation'], 2, ',', '.') }}
                    </p>
                </div>

                <div class="kpi-desc">
                    <span class="kpi-dot" style="background: #10b981;"></span>
                    <span>Nilai seluruh persediaan gudang internal</span>
                </div>
            </div>

            {{-- Card 2: Total Units --}}
            <div class="stock-kpi-card">
                <div class="kpi-head">
                    <div class="kpi-icon-pill" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb;">
                        <x-filament::icon icon="heroicon-o-cube" class="h-5 w-5" />
                    </div>
                    <span class="kpi-chip" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;">
                        On-Hand
                    </span>
                </div>

                <div>
                    <p class="kpi-title">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-units') }}
                    </p>
                    <p class="kpi-number" style="color: #2563eb;">
                        {{ number_format($stats['total_units'], 2, ',', '.') }}
                    </p>
                </div>

                <div class="kpi-desc">
                    <span class="kpi-dot" style="background: #3b82f6;"></span>
                    <span>Total kuantitas fisik yang tersedia</span>
                </div>
            </div>

            {{-- Card 3: Total Products --}}
            <div class="stock-kpi-card">
                <div class="kpi-head">
                    <div class="kpi-icon-pill" style="background: #faf5ff; border: 1px solid #e9d5ff; color: #7c3aed;">
                        <x-filament::icon icon="heroicon-o-tag" class="h-5 w-5" />
                    </div>
                    <span class="kpi-chip" style="background: #fdf4ff; color: #86198f; border: 1px solid #f5d0fe;">
                        Produk
                    </span>
                </div>

                <div>
                    <p class="kpi-title">
                        {{ __('accounting::filament/clusters/reporting.pages.stock-valuation.metrics.total-products') }}
                    </p>
                    <p class="kpi-number" style="color: #7c3aed;">
                        {{ number_format($stats['total_products'], 0, ',', '.') }} <span style="font-size: 0.95rem; font-weight: 600; color: #64748b;">SKU</span>
                    </p>
                </div>

                <div class="kpi-desc">
                    <span class="kpi-dot" style="background: #8b5cf6;"></span>
                    <span>Jumlah variasi produk aktif tersimpan</span>
                </div>
            </div>
        </div>

        {{-- Filament Interactive Table --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>

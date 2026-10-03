@php
    $bgImage = function_exists('setting') && setting('auth_login_image') 
        ? \Illuminate\Support\Facades\Storage::url(setting('auth_login_image')) 
        : asset('images/auth-bg.jpg');
@endphp

<div class="msp-auth-wrapper">
    <!-- Left Column: Industrial / Factory Visual (Desktop Only) -->
    <div class="msp-auth-left">
        <!-- Background Image -->
        <img 
            alt="Fasilitas Manufaktur PT Marel Sukses Pratama"
            class="msp-auth-left-bg"
            src="{{ $bgImage }}" 
        />
        
        <!-- Elegant Multi-layer Gradient Overlay -->
        <div class="msp-auth-left-overlay"></div>

        <!-- Top Left Badge -->
        <div style="position: relative; z-index: 10; padding: 2.5rem 3rem;">
            <div class="msp-badge">
                <span style="position: relative; display: flex; width: 0.5rem; height: 0.5rem;">
                    <span style="position: absolute; width: 100%; height: 100%; border-radius: 9999px; background: #60a5fa; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                    <span style="position: relative; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #3b82f6;"></span>
                </span>
                <span>Enterprise ERP System</span>
            </div>
        </div>

        <!-- Bottom Narrative & Metrics -->
        <div style="position: relative; z-index: 10; padding: 2.5rem 3rem;">
            <h2 class="msp-hero-title">
                Presisi, Efisiensi & <br>
                <span class="msp-hero-gradient">Otomasi Manufaktur Modern</span>
            </h2>
            <p class="msp-hero-desc">
                Platform operasional terintegrasi untuk manajemen rantai pasok, kontrol inventaris, dan alur produksi cerdas PT Marel Sukses Pratama.
            </p>

            <!-- Operational Feature Badges -->
            <div class="msp-metrics-grid">
                <div class="msp-metric-card">
                    <div class="msp-metric-val">100%</div>
                    <div class="msp-metric-label">Traceability</div>
                </div>
                <div class="msp-metric-card">
                    <div class="msp-metric-val" style="color: #38bdf8;">Real-Time</div>
                    <div class="msp-metric-label">Monitoring</div>
                </div>
                <div class="msp-metric-card">
                    <div class="msp-metric-val" style="color: #34d399;">High Precision</div>
                    <div class="msp-metric-label">Inventory & BOM</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Login Form -->
    <div class="msp-auth-right">
        <!-- Header -->
        <header class="msp-right-header">
            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                <img 
                    src="{{ asset('images/logo-marel.webp') }}" 
                    alt="Logo PT Marel Sukses Pratama"
                    class="msp-brand-logo"
                />
                <div style="display: flex; flex-direction: column; min-width: 0;">
                    <span class="msp-brand-title">PT MAREL SUKSES PRATAMA</span>
                    <span class="msp-brand-sub">Enterprise Resource Planning</span>
                </div>
            </div>

            <a 
                href="mailto:it@erpmsp.com" 
                class="msp-help-link"
            >
                <svg style="width: 0.875rem; height: 0.875rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Bantuan</span>
            </a>
        </header>

        <!-- Form Body Container -->
        <div class="msp-form-center">
            <!-- Title & Subtitle -->
            <div style="text-align: left;">
                <h1 class="msp-form-heading">
                    {{ $this->getHeading() }}
                </h1>
                <p class="msp-form-subheading">
                    {{ $this->getSubheading() ?? 'Masukkan kredensial akun Anda untuk mengakses dashboard operasional.' }}
                </p>
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE) }}

            <!-- Filament Livewire Form -->
            <form
                id="form"
                wire:submit="authenticate"
                x-data="{ isProcessing: false }"
                x-on:submit="if (isProcessing) $event.preventDefault()"
                x-on:form-processing-started="isProcessing = true"
                x-on:form-processing-finished="isProcessing = false"
                style="display: flex; flex-direction: column; gap: 1rem;"
            >
                {{ $this->form }}

                <div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="msp-submit-btn"
                    >
                        <svg wire:loading wire:target="authenticate" style="animation: spin 1s linear infinite; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="authenticate">Masuk ke Sistem</span>
                        <span wire:loading wire:target="authenticate">Memproses...</span>
                    </button>
                </div>
            </form>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER) }}
        </div>

        <!-- Footer -->
        <footer class="msp-right-footer">
            <p class="msp-footer-text">
                © {{ date('Y') }} PT Marel Sukses Pratama • All rights reserved.
            </p>
        </footer>
    </div>
</div>

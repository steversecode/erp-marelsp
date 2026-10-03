@php
    $bgImage = function_exists('setting') && setting('auth_login_image') 
        ? \Illuminate\Support\Facades\Storage::url(setting('auth_login_image')) 
        : asset('images/auth-bg.jpg');
@endphp

<div class="flex min-h-screen w-full bg-slate-50 dark:bg-[#080b11] font-sans antialiased selection:bg-blue-600 selection:text-white">
    <!-- Left Column: Industrial / Factory Visual (Desktop Only) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900 flex-col justify-between">
        <!-- Background Image -->
        <img 
            alt="Fasilitas Manufaktur PT Marel Sukses Pratama"
            class="absolute inset-0 h-full w-full object-cover object-center scale-100 hover:scale-105 transition-transform duration-1000 ease-out pointer-events-none"
            src="{{ $bgImage }}" 
        />
        
        <!-- Elegant Multi-layer Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-slate-950/30"></div>
        <div class="absolute inset-0 bg-blue-950/20 mix-blend-multiply"></div>

        <!-- Top Left Badge -->
        <div class="relative z-10 p-8 xl:p-12">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-xl">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                <span class="text-xs font-semibold tracking-wider uppercase">Enterprise ERP System</span>
            </div>
        </div>

        <!-- Bottom Narrative & Metrics -->
        <div class="relative z-10 p-8 xl:p-12 flex flex-col gap-6 text-white">
            <div class="space-y-3 max-w-xl">
                <h2 class="text-3xl xl:text-4xl font-extrabold tracking-tight leading-tight text-white drop-shadow-sm">
                    Presisi, Efisiensi & <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-300">
                        Otomasi Manufaktur Modern
                    </span>
                </h2>
                <p class="text-sm xl:text-base text-slate-300 font-normal leading-relaxed drop-shadow">
                    Platform operasional terintegrasi untuk manajemen rantai pasok, kontrol inventaris, dan alur produksi cerdas PT Marel Sukses Pratama.
                </p>
            </div>

            <!-- Operational Feature Badges -->
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-white/15 max-w-lg">
                <div class="p-3 rounded-xl bg-white/5 backdrop-blur-md border border-white/10">
                    <div class="text-base xl:text-lg font-bold text-white">100%</div>
                    <div class="text-[11px] text-slate-300 font-medium">Traceability</div>
                </div>
                <div class="p-3 rounded-xl bg-white/5 backdrop-blur-md border border-white/10">
                    <div class="text-base xl:text-lg font-bold text-sky-400">Real-Time</div>
                    <div class="text-[11px] text-slate-300 font-medium">Monitoring</div>
                </div>
                <div class="p-3 rounded-xl bg-white/5 backdrop-blur-md border border-white/10">
                    <div class="text-base xl:text-lg font-bold text-emerald-400">High Precision</div>
                    <div class="text-[11px] text-slate-300 font-medium">Inventory & BOM</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col min-h-screen justify-between bg-white dark:bg-[#0c0f17] transition-colors duration-300 overflow-y-auto">
        <!-- Header -->
        <header class="flex items-center justify-between px-6 sm:px-10 xl:px-14 py-6 shrink-0">
            <div class="flex items-center gap-3">
                <img 
                    src="{{ asset('images/logo-marel.webp') }}" 
                    alt="Logo PT Marel Sukses Pratama"
                    class="h-8 sm:h-9 w-auto object-contain"
                />
                <div class="flex flex-col">
                    <span class="text-xs sm:text-sm font-bold tracking-tight text-gray-900 dark:text-white leading-none">
                        PT MAREL SUKSES PRATAMA
                    </span>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">
                        Enterprise Resource Planning
                    </span>
                </div>
            </div>

            <a 
                href="mailto:it@erpmsp.com" 
                class="text-xs font-semibold text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 hover:border-blue-500/40"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Bantuan</span>
            </a>
        </header>

        <!-- Form Body Container -->
        <div class="flex-1 flex flex-col justify-center px-6 sm:px-12 md:px-16 xl:px-20 py-8">
            <div class="max-w-[420px] w-full mx-auto flex flex-col gap-7">
                <!-- Title & Welcome Subtitle -->
                <div class="text-left">
                    <h1 class="text-3xl sm:text-[34px] font-extrabold tracking-tight text-gray-950 dark:text-white leading-tight">
                        {{ $this->getHeading() }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-normal leading-relaxed">
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
                    class="flex flex-col gap-5"
                >
                    {{ $this->form }}

                    <div class="pt-1">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full h-12 flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl font-bold uppercase tracking-wider text-xs shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 active:scale-[0.98] transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg wire:loading wire:target="authenticate" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="authenticate">Masuk ke Sistem</span>
                            <span wire:loading wire:target="authenticate">Memproses...</span>
                        </button>
                    </div>
                </form>

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER) }}
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-6 px-6 sm:px-10 xl:px-14 text-center shrink-0 border-t border-gray-100 dark:border-white/5">
            <p class="text-[11px] text-gray-400 dark:text-zinc-500 font-medium tracking-wide">
                © {{ date('Y') }} PT Marel Sukses Pratama • All rights reserved.
            </p>
        </footer>
    </div>
</div>

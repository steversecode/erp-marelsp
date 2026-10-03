<div style="background-color: #F8FAFC; color: #0B1C30; min-height: 100vh; min-height: 100dvh; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow-x: hidden; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    <!-- Background ambient radial glow -->
    <div style="position: fixed; inset: 0; pointer-events: none; background: radial-gradient(circle at 50% 40%, rgba(10,37,64,0.04), transparent 65%);"></div>

    <!-- Main Centered Sign-In Content -->
    <main style="position: relative; z-index: 10; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; width: 100%; box-sizing: border-box;">
        <div style="display: flex; flex-direction: column; width: 100%; align-items: center; justify-content: center; position: relative;">
            <!-- Colored background blurs -->
            <div style="position: absolute; top: -6rem; width: 20rem; height: 20rem; background-color: rgba(0, 112, 242, 0.05); border-radius: 9999px; filter: blur(48px); pointer-events: none;"></div>
            <div style="position: absolute; bottom: -5rem; width: 18rem; height: 18rem; background-color: rgba(252, 119, 40, 0.05); border-radius: 9999px; filter: blur(48px); pointer-events: none;"></div>

            <!-- Authentication Card (1:1 with marel-erp-portal/src/components/LoginScreen.tsx) -->
            <div style="width: 100%; max-width: 490px; background-color: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 16px 36px -12px rgba(10,37,64,0.1); border: 1px solid #E2E8F0; padding: 1.5rem 1.5rem; position: relative; z-index: 10; box-sizing: border-box;">
                <!-- Logo and Headings -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <div style="height: 2.75rem; padding: 0.25rem 0.875rem; border-radius: 0.5rem; background-color: #EFF4FF; border: 1px solid #DCE9FF; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                        <img 
                            src="{{ asset('images/logo-marel.webp') }}" 
                            alt="PT Marel Sukses Pratama Logo"
                            style="height: 1.875rem; width: auto; object-fit: contain;"
                        />
                    </div>
                    <h1 style="font-size: 1.375rem; font-weight: 600; color: #0F172A; letter-spacing: -0.02em; margin: 0; line-height: 1.2;">
                        {{ $this->getHeading() }}
                    </h1>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.25rem 0 0 0; font-weight: 400; letter-spacing: -0.01em;">
                        {{ $this->getSubheading() ?? 'PT Marel Sukses Pratama • Enterprise Resource Planning' }}
                    </p>
                </div>

                <!-- Filament Livewire Form -->
                <form
                    id="form"
                    wire:submit="authenticate"
                    x-data="{ isProcessing: false }"
                    x-on:submit="if (isProcessing) $event.preventDefault()"
                    x-on:form-processing-started="isProcessing = true"
                    x-on:form-processing-finished="isProcessing = false"
                    style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.875rem;"
                >
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE) }}

                    {{ $this->form }}

                    <div style="padding-top: 0.25rem;">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="marel-btn-primary"
                        >
                            <svg wire:loading wire:target="authenticate" style="animation: spin 1s linear infinite; height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="authenticate">Sign in</span>
                            <span wire:loading wire:target="authenticate">Mengautentikasi...</span>
                            <svg wire:loading.remove wire:target="authenticate" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER) }}
                </form>

                <!-- Sub-Card Status Strip (Inside Card) -->
                <div style="margin-top: 1.25rem; padding: 0.625rem 1.5rem; background-color: rgba(239, 244, 255, 0.75); margin-left: -1.5rem; margin-right: -1.5rem; margin-bottom: -1.5rem; border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #64748B;">
                    <div style="display: flex; align-items: center; gap: 0.375rem; color: #334155; font-weight: 500;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #0070F2;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        <span>Portal Aman Enterprise</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.375rem;">
                        <span style="position: relative; display: flex; width: 0.5rem; height: 0.5rem;">
                            <span style="position: absolute; width: 100%; height: 100%; border-radius: 9999px; background: #10B981; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                            <span style="position: relative; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #10B981;"></span>
                        </span>
                        <span style="font-weight: 600; color: #0F172A;">Sistem Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Under-Card Security Badges -->
            <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.875rem; font-size: 0.75rem; color: #64748B;">
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <svg style="width: 0.875rem; height: 0.875rem; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Enkripsi TLS 1.3 Terverifikasi</span>
                </div>
                <div style="width: 1px; height: 0.75rem; background-color: #CBD5E1;"></div>
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <svg style="width: 0.875rem; height: 0.875rem; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Pusat Data Jakarta (JKT-01)</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Global Page Footer -->
    <footer style="position: relative; z-index: 10; width: 100%; padding: 0.75rem 1.5rem; padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); border-top: 1px solid #E2E8F0; background-color: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px);">
        <div style="max-width: 80rem; margin: 0 auto; display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; font-size: 0.75rem; color: #64748B;">
            <div>
                © {{ date('Y') }} PT Marel Sukses Pratama. All rights reserved. Enterprise Resource Planning Core.
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem;">
                    <svg style="width: 0.875rem; height: 0.875rem; color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Portal Aman Enterprise</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #10B981;"></span>
                    <span>Sistem Aktif v4.18</span>
                </div>
            </div>
        </div>
    </footer>
</div>

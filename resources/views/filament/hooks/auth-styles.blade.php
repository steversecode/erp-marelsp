@if (! auth()->check() || request()->routeIs('filament.*.auth.*'))
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

<style>
    /* ==========================================================================
       MAREL ERP PORTAL THEME (EXACT 1:1 SLICING FROM marel-erp-portal LoginScreen.tsx)
       PT Marel Sukses Pratama
       STRICTLY SCOPED TO AUTH PAGES ONLY (.fi-simple-layout)
       ========================================================================== */

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    [x-cloak] {
        display: none !important;
    }

    /* Outer Viewport Reset - Strictly scoped to Auth Simple Layout */
    html:has(.fi-simple-layout),
    body.fi-body:has(.fi-simple-layout),
    body:has(.fi-simple-layout) {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100vh !important;
        max-height: 100dvh !important;
        background-color: #F8FAFC !important;
        color: #0B1C30 !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        -webkit-font-smoothing: antialiased;
        overflow: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    html:has(.fi-simple-layout)::-webkit-scrollbar,
    body:has(.fi-simple-layout)::-webkit-scrollbar,
    .fi-simple-layout::-webkit-scrollbar,
    .fi-simple-layout *::-webkit-scrollbar {
        width: 0px !important;
        height: 0px !important;
        display: none !important;
    }

    .fi-simple-layout ::selection {
        background-color: #0070F2 !important;
        color: #FFFFFF !important;
    }

    /* Filament Simple Layout Overrides */
    .fi-simple-layout {
        position: relative !important;
        height: 100% !important;
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100vh !important;
        max-height: 100dvh !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background: #F8FAFC !important;
        overflow: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    .fi-simple-layout::before {
        display: none !important;
    }

    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 10 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    .fi-simple-main {
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-radius: 0 !important;
        animation: none !important;
        overflow: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    .fi-simple-layout .fi-simple-header,
    .fi-simple-layout .fi-simple-footer {
        display: none !important;
    }

    /* Material Symbols Outlined */
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined' !important;
        font-weight: normal;
        font-style: normal;
        font-size: 20px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-smoothing: antialiased;
        user-select: none;
    }

    /* Input & Interactive Styles matching LoginScreen.tsx exactly */
    .msp-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        border-radius: 0.5rem;
        border: 1px solid #E2E8F0;
        background-color: #FFFFFF;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        transition: all 0.15s ease-in-out;
    }

    .msp-input-wrap:hover {
        border-color: #CBD5E1;
    }

    .msp-input-wrap:focus-within {
        border-color: #0070F2;
        box-shadow: 0 0 0 2px rgba(0, 112, 242, 0.15);
    }

    .msp-input-icon {
        position: absolute;
        left: 0.75rem;
        display: flex;
        align-items: center;
        pointer-events: none;
        color: #64748B;
    }

    .msp-input-field {
        width: 100%;
        height: 2.5rem;
        padding-left: 2.5rem;
        padding-right: 0.75rem;
        background: transparent;
        border-radius: 0.5rem;
        border: none;
        outline: none;
        font-size: 0.8125rem;
        color: #0F172A;
        transition: color 0.15s ease-in-out;
    }

    @media (min-width: 640px) {
        .msp-input-field {
            font-size: 0.875rem;
        }
    }

    .msp-input-field::placeholder {
        color: #94A3B8;
    }

    .msp-input-password {
        padding-right: 2.5rem !important;
    }

    /* Custom Checkbox */
    .msp-checkbox-box {
        width: 0.875rem;
        height: 0.875rem;
        border-radius: 0.25rem;
        border: 1px solid #CBD5E1;
        background-color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        transition: background-color 0.15s, border-color 0.15s;
    }

    @media (min-width: 640px) {
        .msp-checkbox-box {
            width: 1rem;
            height: 1rem;
        }
    }

    input:checked + .msp-checkbox-box {
        background-color: #0A2540;
        border-color: #0A2540;
    }

    input:checked + .msp-checkbox-box .msp-check-icon {
        opacity: 1;
    }

    /* Submit Button (1:1 with LoginScreen.tsx) */
    .msp-btn-submit {
        width: 100%;
        height: 2.5rem;
        margin-top: 0.125rem;
        border-radius: 0.5rem;
        background-color: #0A2540;
        color: #FFFFFF !important;
        font-weight: 600;
        font-size: 0.8125rem;
        letter-spacing: -0.01em;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @media (min-width: 640px) {
        .msp-btn-submit {
            height: 2.625rem;
            font-size: 0.875rem;
        }
    }

    .msp-btn-submit:hover:not(:disabled) {
        background-color: #002643;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .msp-btn-submit:active:not(:disabled) {
        transform: scale(0.99);
    }

    .msp-btn-submit:disabled {
        opacity: 0.8;
        cursor: not-allowed;
    }

    .msp-btn-submit:hover .msp-arrow-icon {
        transform: translateX(0.125rem);
    }

    .msp-btn-content {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Modal Center Positioning */
    .msp-modal-overlay {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        z-index: 99999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1rem !important;
        background-color: rgba(10, 37, 64, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        box-sizing: border-box !important;
    }

    .msp-modal-overlay[style*="display: none"] {
        display: none !important;
    }

    .msp-modal-card {
        margin: auto !important;
        width: 100% !important;
        max-width: 28rem !important;
        background-color: #FFFFFF !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid #E2E8F0 !important;
        padding: 1.5rem !important;
        position: relative !important;
        box-sizing: border-box !important;
    }
</style>
@endif

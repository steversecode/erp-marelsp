<style>
    /* ==========================================================================
       MAREL ERP PORTAL THEME (1:1 SLICING FROM LOGINSCREEN.TSX)
       PT Marel Sukses Pratama
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

    /* Outer Viewport */
    html, body.fi-body {
        margin: 0 !important;
        padding: 0 !important;
        background-color: #F8FAFC !important;
        color: #0F172A !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        -webkit-font-smoothing: antialiased;
        overflow-x: hidden !important;
    }

    .fi-simple-layout {
        position: relative !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background: #F8FAFC !important;
        overflow: hidden !important;
    }

    .fi-simple-layout::before {
        display: none !important;
    }

    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 10 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fi-simple-main {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-radius: 0 !important;
        animation: none !important;
    }

    .fi-simple-header,
    .fi-simple-footer {
        display: none !important;
    }

    /* Card Form Inputs Styling (1:1 with LoginScreen.tsx) */
    .fi-fo-field-wrp-label label,
    .fi-fo-field-wrp-label span {
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        color: #0F172A !important;
        letter-spacing: -0.01em !important;
        display: inline-block !important;
        margin-bottom: 0.25rem !important;
    }

    .fi-input-wrp {
        background-color: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-input-wrp:hover {
        border-color: #CBD5E1 !important;
    }

    .fi-input-wrp:focus-within {
        border-color: #0070F2 !important;
        box-shadow: 0 0 0 2px rgba(0, 112, 242, 0.15) !important;
    }

    .fi-input-wrp input {
        color: #0F172A !important;
        background: transparent !important;
        font-size: 0.8125rem !important;
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }

    @media (max-width: 640px) {
        .fi-input-wrp input {
            font-size: 16px !important; /* Prevent mobile zoom */
        }
    }

    .fi-input-wrp input::placeholder {
        color: #94A3B8 !important;
    }

    /* Checkbox & Remember Me */
    .fi-fo-checkbox label {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        cursor: pointer !important;
    }

    .fi-checkbox-input {
        width: 1rem !important;
        height: 1rem !important;
        border-radius: 0.25rem !important;
        border: 1px solid #CBD5E1 !important;
        background-color: #FFFFFF !important;
        transition: all 0.15s ease !important;
    }

    .fi-checkbox-input:checked {
        background-color: #0A2540 !important;
        border-color: #0A2540 !important;
    }

    .fi-fo-checkbox label span {
        font-size: 0.75rem !important;
        color: #0F172A !important;
        font-weight: 400 !important;
    }

    /* Forgot Password Link */
    .fi-link,
    a.fi-link {
        color: #0070F2 !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
    }

    .fi-link:hover,
    a.fi-link:hover {
        text-decoration: underline !important;
    }

    /* Submit Button (1:1 #0A2540) */
    .marel-btn-primary {
        width: 100%;
        height: 2.5rem;
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
        transition: all 0.15s ease-in-out;
    }

    @media (min-width: 640px) {
        .marel-btn-primary {
            height: 2.625rem;
            font-size: 0.875rem;
        }
    }

    .marel-btn-primary:hover {
        background-color: #002643;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .marel-btn-primary:active {
        transform: scale(0.99);
    }
</style>

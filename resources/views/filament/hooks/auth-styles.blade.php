<style>
    /* ==========================================================================
       SPLIT-SCREEN 2-COLUMN AUTH STYLING (FILAMENT ADMIN PANEL)
       PT Marel Sukses Pratama
       ========================================================================== */

    /* Reset Filament Simple Wrapper for 100% Full-bleed Split Screen */
    body.fi-body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden !important;
    }

    .fi-simple-layout {
        position: relative !important;
        min-height: 100vh !important;
        width: 100% !important;
        max-width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        background: transparent !important;
        overflow: hidden !important;
    }

    .fi-simple-layout::before {
        display: none !important;
    }

    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
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

    /* Form Labels */
    .fi-fo-field-wrp-label label {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        letter-spacing: -0.01em !important;
    }

    .dark .fi-fo-field-wrp-label label {
        color: #e2e8f0 !important;
    }

    /* Form Input Fields */
    .fi-input-wrp {
        border-radius: 0.75rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* Light mode inputs */
    html:not(.dark) .fi-input-wrp {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    }

    html:not(.dark) .fi-input-wrp:hover {
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
    }

    html:not(.dark) .fi-input-wrp:focus-within {
        background-color: #ffffff !important;
        border-color: #0070F2 !important;
        box-shadow: 0 0 0 3px rgba(0, 112, 242, 0.15) !important;
    }

    html:not(.dark) .fi-input-wrp input {
        color: #0f172a !important;
        font-size: 0.875rem !important;
    }

    html:not(.dark) .fi-input-wrp input::placeholder {
        color: #94a3b8 !important;
    }

    /* Dark mode inputs */
    .dark .fi-input-wrp {
        background-color: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
    }

    .dark .fi-input-wrp:hover {
        border-color: rgba(255, 255, 255, 0.2) !important;
        background-color: rgba(255, 255, 255, 0.06) !important;
    }

    .dark .fi-input-wrp:focus-within {
        border-color: #0070F2 !important;
        background-color: rgba(0, 112, 242, 0.05) !important;
        box-shadow: 0 0 0 3px rgba(0, 112, 242, 0.25) !important;
    }

    .dark .fi-input-wrp input {
        color: #f8fafc !important;
        font-size: 0.875rem !important;
    }

    .dark .fi-input-wrp input::placeholder {
        color: #64748b !important;
    }

    /* Checkbox & Text */
    .fi-checkbox-input {
        border-radius: 0.375rem !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.15s ease !important;
    }

    .dark .fi-checkbox-input {
        border-color: rgba(255, 255, 255, 0.2) !important;
        background-color: rgba(255, 255, 255, 0.04) !important;
    }

    .fi-checkbox-input:checked {
        background-color: #0070F2 !important;
        border-color: #0070F2 !important;
        box-shadow: 0 0 10px rgba(0, 112, 242, 0.3) !important;
    }

    .fi-fo-checkbox label span {
        font-size: 0.8125rem !important;
        color: #64748b !important;
    }

    .dark .fi-fo-checkbox label span {
        color: #94a3b8 !important;
    }

    /* Forgot Password & Links */
    .fi-link,
    a.fi-link {
        color: #0070F2 !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: color 0.15s ease !important;
    }

    .dark .fi-link,
    .dark a.fi-link {
        color: #60a5fa !important;
    }

    .fi-link:hover,
    a.fi-link:hover {
        color: #0056BD !important;
        text-decoration: underline !important;
    }

    .dark .fi-link:hover,
    .dark a.fi-link:hover {
        color: #93c5fd !important;
    }

    /* Submit Button */
    form button[type="submit"],
    .fi-btn-color-primary {
        background: linear-gradient(135deg, #0070F2 0%, #0056BD 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 0.75rem !important;
        height: 2.875rem !important;
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        letter-spacing: 0.025em !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px -2px rgba(0, 112, 242, 0.4) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    form button[type="submit"]:hover,
    .fi-btn-color-primary:hover {
        background: linear-gradient(135deg, #0d7cfd 0%, #0060d4 100%) !important;
        box-shadow: 0 6px 20px -2px rgba(0, 112, 242, 0.55) !important;
        transform: translateY(-1px) !important;
    }

    form button[type="submit"]:active,
    .fi-btn-color-primary:active {
        transform: translateY(0px) !important;
        box-shadow: 0 2px 8px -2px rgba(0, 112, 242, 0.35) !important;
    }
</style>

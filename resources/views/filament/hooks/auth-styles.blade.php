<style>
    /* ==========================================================================
       MODERN MINIMALIST AUTH STYLING (FILAMENT ADMIN PANEL)
       PT Marel Sukses Pratama
       ========================================================================== */

    /* Viewport / Outer Layout */
    .fi-simple-layout {
        position: relative !important;
        min-height: 100vh !important;
        background-color: #07090e !important;
        background-image: 
            radial-gradient(circle 800px at 50% 42%, rgba(0, 112, 242, 0.13), transparent 70%),
            radial-gradient(circle 600px at 85% 15%, rgba(99, 102, 241, 0.08), transparent 60%),
            radial-gradient(circle 600px at 15% 85%, rgba(2, 132, 199, 0.06), transparent 70%),
            radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px) !important;
        background-size: 100% 100%, 100% 100%, 100% 100%, 28px 28px !important;
        background-position: 0 0, 0 0, 0 0, -1px -1px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
    }

    /* Ambient animated subtle backdrop glow */
    .fi-simple-layout::before {
        content: '';
        position: absolute;
        width: 600px;
        height: 600px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, rgba(0, 112, 242, 0.1) 0%, rgba(99, 102, 241, 0.04) 50%, transparent 70%);
        pointer-events: none;
        z-index: 0;
        filter: blur(40px);
        animation: fiAmbientPulse 8s ease-in-out infinite alternate;
    }

    @keyframes fiAmbientPulse {
        0% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.7; }
        100% { transform: translate(-50%, -50%) scale(1.08); opacity: 1; }
    }

    /* Main Container */
    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 10 !important;
        width: 100% !important;
        max-width: 27rem !important; /* ~432px */
        margin: 0 auto !important;
    }

    /* Glassmorphism Card */
    .fi-simple-main {
        background: rgba(14, 18, 28, 0.75) !important;
        backdrop-filter: blur(28px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(28px) saturate(190%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-radius: 1.25rem !important; /* 20px */
        box-shadow: 
            0 25px 60px -15px rgba(0, 0, 0, 0.75),
            0 0 45px -10px rgba(0, 112, 242, 0.16),
            inset 0 1px 1px 0 rgba(255, 255, 255, 0.08) !important;
        padding: 2.25rem 2rem !important;
        animation: fiCardEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }

    @keyframes fiCardEntrance {
        from {
            opacity: 0;
            transform: translateY(14px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Brand Logo & Header */
    .fi-simple-header {
        margin-bottom: 1.75rem !important;
        text-align: center !important;
    }

    .fi-simple-header .fi-logo {
        height: 2.75rem !important;
        width: auto !important;
        margin: 0 auto !important;
        padding: 0.35rem 0.85rem !important;
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.09) !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 4px 16px -2px rgba(0, 112, 242, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.1) !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease !important;
    }

    .fi-simple-header .fi-logo:hover {
        transform: translateY(-1px) scale(1.02) !important;
        box-shadow: 0 6px 20px -2px rgba(0, 112, 242, 0.35) !important;
    }

    .fi-simple-header-heading {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        letter-spacing: -0.025em !important;
        color: #ffffff !important;
        margin-top: 1.125rem !important;
        line-height: 1.2 !important;
    }

    .fi-simple-header-subheading {
        font-size: 0.8125rem !important;
        color: #94a3b8 !important;
        margin-top: 0.375rem !important;
        font-weight: 400 !important;
        line-height: 1.4 !important;
    }

    /* Form Labels */
    .fi-simple-main .fi-fo-field-wrp-label label {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: #cbd5e1 !important;
        letter-spacing: 0.01em !important;
    }

    /* Form Input Fields */
    .fi-simple-main .fi-input-wrp {
        background-color: rgba(255, 255, 255, 0.035) !important;
        border: 1px solid rgba(255, 255, 255, 0.09) !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.25) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .fi-simple-main .fi-input-wrp:hover {
        border-color: rgba(255, 255, 255, 0.18) !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .fi-simple-main .fi-input-wrp:focus-within {
        border-color: #0070F2 !important;
        background-color: rgba(0, 112, 242, 0.04) !important;
        box-shadow: 0 0 0 3px rgba(0, 112, 242, 0.22), 0 1px 2px 0 rgba(0, 0, 0, 0.1) !important;
    }

    .fi-simple-main .fi-input-wrp input {
        color: #f8fafc !important;
        font-size: 0.875rem !important;
        padding-top: 0.625rem !important;
        padding-bottom: 0.625rem !important;
    }

    .fi-simple-main .fi-input-wrp input::placeholder {
        color: #64748b !important;
    }

    /* Checkbox & Text */
    .fi-simple-main .fi-checkbox-input {
        border-radius: 0.375rem !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        background-color: rgba(255, 255, 255, 0.04) !important;
        transition: all 0.15s ease !important;
    }

    .fi-simple-main .fi-checkbox-input:checked {
        background-color: #0070F2 !important;
        border-color: #0070F2 !important;
        box-shadow: 0 0 10px rgba(0, 112, 242, 0.4) !important;
    }

    .fi-simple-main .fi-fo-checkbox label span {
        font-size: 0.8125rem !important;
        color: #94a3b8 !important;
    }

    /* Forgot Password & Links */
    .fi-simple-main .fi-link,
    .fi-simple-main a {
        color: #60a5fa !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: color 0.15s ease !important;
    }

    .fi-simple-main .fi-link:hover,
    .fi-simple-main a:hover {
        color: #93c5fd !important;
        text-decoration: underline !important;
    }

    /* Submit Button */
    .fi-simple-main form button[type="submit"],
    .fi-simple-main .fi-btn-color-primary {
        background: linear-gradient(135deg, #0070F2 0%, #0056BD 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 0.75rem !important;
        padding-top: 0.6875rem !important;
        padding-bottom: 0.6875rem !important;
        font-weight: 600 !important;
        font-size: 0.9375rem !important;
        letter-spacing: 0.01em !important;
        color: #ffffff !important;
        box-shadow: 0 4px 16px -2px rgba(0, 112, 242, 0.45) !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .fi-simple-main form button[type="submit"]:hover,
    .fi-simple-main .fi-btn-color-primary:hover {
        background: linear-gradient(135deg, #0d7cfd 0%, #0060d4 100%) !important;
        box-shadow: 0 8px 24px -2px rgba(0, 112, 242, 0.6) !important;
        transform: translateY(-1px) !important;
    }

    .fi-simple-main form button[type="submit"]:active,
    .fi-simple-main .fi-btn-color-primary:active {
        transform: translateY(0px) !important;
        box-shadow: 0 2px 10px -2px rgba(0, 112, 242, 0.4) !important;
    }

    /* Light Mode Fallbacks */
    html:not(.dark) .fi-simple-layout {
        background-color: #f1f5f9 !important;
        background-image: 
            radial-gradient(circle 800px at 50% 40%, rgba(0, 112, 242, 0.08), transparent 70%),
            radial-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 1px) !important;
    }

    html:not(.dark) .fi-simple-layout::before {
        opacity: 0.3 !important;
    }

    html:not(.dark) .fi-simple-main {
        background: rgba(255, 255, 255, 0.9) !important;
        border: 1px solid rgba(0, 0, 0, 0.07) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 20px 45px -15px rgba(0, 0, 0, 0.08), 0 0 30px -10px rgba(0, 112, 242, 0.08) !important;
    }

    html:not(.dark) .fi-simple-header-heading {
        color: #0f172a !important;
    }

    html:not(.dark) .fi-simple-header-subheading {
        color: #64748b !important;
    }

    html:not(.dark) .fi-simple-main .fi-input-wrp {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }

    html:not(.dark) .fi-simple-main .fi-input-wrp input {
        color: #0f172a !important;
    }
</style>

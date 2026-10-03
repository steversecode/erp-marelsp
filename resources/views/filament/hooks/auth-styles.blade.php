<style>
    /* ==========================================================================
       SPLIT-SCREEN AUTH STYLING & FULL RESPONSIVE FIXES
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

    /* Reset Filament Simple Wrapper for 100% Full-bleed Split Screen */
    html, body.fi-body {
        margin: 0 !important;
        padding: 0 !important;
        background-color: #0b0f19 !important;
        -webkit-text-size-adjust: 100%;
    }

    .fi-simple-layout {
        position: relative !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        background: transparent !important;
        overflow-x: hidden !important;
        overflow-y: visible !important;
    }

    .fi-simple-layout::before {
        display: none !important;
    }

    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .fi-simple-main {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
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

    /* Master Container */
    .msp-auth-wrapper {
        display: flex;
        min-height: 100vh;
        min-height: 100dvh;
        width: 100%;
        overflow-x: hidden;
    }

    /* Left Column (Desktop Only Visual) */
    .msp-auth-left {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 50%;
        min-height: 100vh;
        background-color: #090d16;
        overflow: hidden;
    }

    @media (max-width: 1023px) {
        .msp-auth-left {
            display: none !important;
        }
    }

    .msp-auth-left-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        pointer-events: none;
    }

    .msp-auth-left-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(9, 13, 22, 0.96) 0%, rgba(9, 13, 22, 0.5) 50%, rgba(9, 13, 22, 0.35) 100%);
        pointer-events: none;
    }

    .msp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    }

    .msp-hero-title {
        font-size: 2.125rem;
        font-weight: 800;
        line-height: 1.25;
        color: #ffffff;
        letter-spacing: -0.025em;
        margin-bottom: 0.625rem;
    }

    .msp-hero-gradient {
        background: linear-gradient(90deg, #60a5fa 0%, #38bdf8 50%, #818cf8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .msp-hero-desc {
        font-size: 0.875rem;
        line-height: 1.6;
        color: #cbd5e1;
        max-width: 32rem;
    }

    .msp-metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        max-width: 32rem;
    }

    .msp-metric-card {
        padding: 0.75rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .msp-metric-val {
        font-size: 1.125rem;
        font-weight: 700;
        color: #ffffff;
    }

    .msp-metric-label {
        font-size: 0.6875rem;
        color: #cbd5e1;
        font-weight: 500;
        margin-top: 0.125rem;
    }

    /* Right Column (Sign In Form) */
    .msp-auth-right {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
        min-height: 100dvh;
        width: 50%;
        overflow-y: visible;
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    @media (max-width: 1023px) {
        .msp-auth-right {
            width: 100% !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            overflow: visible !important;
        }
    }

    /* Theme adaptive background and colors for Right Column */
    html.dark .msp-auth-right,
    .dark .msp-auth-right {
        background-color: #0b0f19 !important;
        color: #f8fafc !important;
    }

    html:not(.dark) .msp-auth-right {
        background-color: #ffffff !important;
        color: #0f172a !important;
    }

    /* Header in Right Column */
    .msp-right-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 2.5rem;
        flex-shrink: 0;
        gap: 1rem;
    }

    @media (max-width: 640px) {
        .msp-right-header {
            padding: 1rem 1.25rem !important;
            gap: 0.5rem !important;
        }
    }

    .msp-brand-logo {
        height: 2.25rem;
        width: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    @media (max-width: 640px) {
        .msp-brand-logo {
            height: 1.875rem !important;
        }
    }

    .msp-brand-title {
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        text-transform: uppercase;
        line-height: 1.2;
    }

    @media (max-width: 640px) {
        .msp-brand-title {
            font-size: 0.75rem !important;
            letter-spacing: 0.01em !important;
        }
    }

    html.dark .msp-brand-title, .dark .msp-brand-title { color: #ffffff !important; }
    html:not(.dark) .msp-brand-title { color: #0f172a !important; }

    .msp-brand-sub {
        font-size: 0.6875rem;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 0.125rem;
    }

    @media (max-width: 480px) {
        .msp-brand-sub {
            display: none !important;
        }
    }

    .msp-help-link {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        text-decoration: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    @media (max-width: 640px) {
        .msp-help-link {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.6875rem !important;
            gap: 0.25rem !important;
        }
    }

    .msp-help-link:hover {
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.3);
    }

    /* Form Container */
    .msp-form-center {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2rem 2.5rem;
        max-width: 440px;
        width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }

    @media (max-width: 640px) {
        .msp-form-center {
            padding: 1.25rem 1.25rem 1.75rem 1.25rem !important;
            max-width: 100% !important;
        }
    }

    .msp-form-heading {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 640px) {
        .msp-form-heading {
            font-size: 1.625rem !important; /* 26px */
            margin-bottom: 0.375rem !important;
        }
    }

    html.dark .msp-form-heading, .dark .msp-form-heading { color: #ffffff !important; }
    html:not(.dark) .msp-form-heading { color: #0f172a !important; }

    .msp-form-subheading {
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 640px) {
        .msp-form-subheading {
            font-size: 0.8125rem !important; /* 13px */
            margin-bottom: 1.125rem !important;
            line-height: 1.4 !important;
        }
    }

    html.dark .msp-form-subheading, .dark .msp-form-subheading { color: #94a3b8 !important; }
    html:not(.dark) .msp-form-subheading { color: #64748b !important; }

    /* Form Field Labels */
    .fi-fo-field-wrp-label label,
    .fi-fo-field-wrp-label span {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.01em !important;
        display: inline-block !important;
        margin-bottom: 0.25rem !important;
    }

    html.dark .fi-fo-field-wrp-label label,
    .dark .fi-fo-field-wrp-label label,
    html.dark .fi-fo-field-wrp-label span,
    .dark .fi-fo-field-wrp-label span {
        color: #e2e8f0 !important;
    }

    html:not(.dark) .fi-fo-field-wrp-label label,
    html:not(.dark) .fi-fo-field-wrp-label span {
        color: #1e293b !important;
    }

    /* Form Inputs */
    .fi-input-wrp {
        border-radius: 0.75rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* Prevent mobile browser zoom by keeping font-size at least 16px on mobile */
    .fi-input-wrp input {
        font-size: 16px !important;
        padding-top: 0.6875rem !important;
        padding-bottom: 0.6875rem !important;
        box-sizing: border-box !important;
    }

    @media (min-width: 641px) {
        .fi-input-wrp input {
            font-size: 0.875rem !important;
        }
    }

    /* Light mode input */
    html:not(.dark) .fi-input-wrp {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    html:not(.dark) .fi-input-wrp:hover {
        border-color: #94a3b8 !important;
        background-color: #ffffff !important;
    }

    html:not(.dark) .fi-input-wrp:focus-within {
        background-color: #ffffff !important;
        border-color: #0070F2 !important;
        box-shadow: 0 0 0 3px rgba(0, 112, 242, 0.18) !important;
    }

    html:not(.dark) .fi-input-wrp input {
        color: #0f172a !important;
        background: transparent !important;
    }

    html:not(.dark) .fi-input-wrp input::placeholder {
        color: #94a3b8 !important;
    }

    /* Dark mode input */
    html.dark .fi-input-wrp,
    .dark .fi-input-wrp {
        background-color: #131926 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .fi-input-wrp:hover,
    .dark .fi-input-wrp:hover {
        border-color: rgba(255, 255, 255, 0.25) !important;
        background-color: #182030 !important;
    }

    html.dark .fi-input-wrp:focus-within,
    .dark .fi-input-wrp:focus-within {
        border-color: #0070F2 !important;
        background-color: #151d2d !important;
        box-shadow: 0 0 0 3px rgba(0, 112, 242, 0.3) !important;
    }

    html.dark .fi-input-wrp input,
    .dark .fi-input-wrp input {
        color: #ffffff !important;
        background: transparent !important;
    }

    html.dark .fi-input-wrp input::placeholder,
    .dark .fi-input-wrp input::placeholder {
        color: #64748b !important;
    }

    /* Checkbox & Remember Me */
    .fi-fo-checkbox label {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        cursor: pointer !important;
    }

    .fi-checkbox-input {
        border-radius: 0.375rem !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.15s ease !important;
    }

    html.dark .fi-checkbox-input,
    .dark .fi-checkbox-input {
        border-color: rgba(255, 255, 255, 0.2) !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .fi-checkbox-input:checked {
        background-color: #0070F2 !important;
        border-color: #0070F2 !important;
        box-shadow: 0 0 10px rgba(0, 112, 242, 0.3) !important;
    }

    .fi-fo-checkbox label span {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
    }

    html.dark .fi-fo-checkbox label span,
    .dark .fi-fo-checkbox label span {
        color: #cbd5e1 !important;
    }

    html:not(.dark) .fi-fo-checkbox label span {
        color: #475569 !important;
    }

    /* Forgot Password Link */
    .fi-link,
    a.fi-link {
        color: #0070F2 !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: color 0.15s ease !important;
    }

    html.dark .fi-link,
    .dark .fi-link,
    html.dark a.fi-link,
    .dark a.fi-link {
        color: #60a5fa !important;
    }

    .fi-link:hover,
    a.fi-link:hover {
        text-decoration: underline !important;
    }

    /* Submit Button */
    .msp-submit-btn {
        width: 100%;
        height: 3.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        background: linear-gradient(135deg, #0070F2 0%, #0056BD 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.8125rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-radius: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 16px -2px rgba(0, 112, 242, 0.45);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        margin-top: 0.625rem;
    }

    @media (max-width: 640px) {
        .msp-submit-btn {
            height: 3rem !important;
            font-size: 0.8125rem !important;
        }
    }

    .msp-submit-btn:hover {
        background: linear-gradient(135deg, #0d7cfd 0%, #0060d4 100%);
        box-shadow: 0 6px 22px -2px rgba(0, 112, 242, 0.65);
        transform: translateY(-1px);
    }

    .msp-submit-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px -2px rgba(0, 112, 242, 0.4);
    }

    /* Footer */
    .msp-right-footer {
        padding: 1.25rem 2rem;
        padding-bottom: max(1.25rem, env(safe-area-inset-bottom));
        text-align: center;
        flex-shrink: 0;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    @media (max-width: 640px) {
        .msp-right-footer {
            padding: 1rem 1.25rem !important;
            padding-bottom: max(1rem, env(safe-area-inset-bottom)) !important;
        }
    }

    html:not(.dark) .msp-right-footer {
        border-top: 1px solid #f1f5f9;
    }

    .msp-footer-text {
        font-size: 0.6875rem;
        color: #94a3b8;
        font-weight: 500;
        margin: 0;
    }
</style>

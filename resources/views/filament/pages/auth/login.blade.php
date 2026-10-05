<div
    x-data="{
        showForgotModal: false,
        showPassword: false,
        forgotStep: 'email',
        forgotEmail: 'nama@erpmsp.com',
        forgotOtp: ['', '', '', '', '', ''],
        newPassword: '',
        confirmPassword: '',
        forgotLoading: false,
        forgotErrorMessage: '',
        resendTimer: 58,
        timerInterval: null,

        openForgotPassword() {
            this.showForgotModal = true;
            this.forgotStep = 'email';
            this.forgotErrorMessage = '';
            this.forgotEmail = @this.get('data.email') || 'nama@erpmsp.com';
        },

        handleSendOtp() {
            if (!this.forgotEmail.trim().endsWith('@erpmsp.com') && !this.forgotEmail.trim().includes('@')) {
                this.forgotErrorMessage = 'Harap gunakan alamat email korporat resmi @erpmsp.com';
                return;
            }
            this.forgotErrorMessage = '';
            this.forgotLoading = true;
            setTimeout(() => {
                this.forgotLoading = false;
                this.forgotStep = 'otp';
                this.forgotOtp = ['4', '8', '2', '9', '1', '0'];
                this.startTimer();
            }, 800);
        },

        startTimer() {
            this.resendTimer = 58;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if (this.resendTimer > 0) {
                    this.resendTimer--;
                } else {
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },

        resendOtp() {
            alert('Kode verifikasi baru telah dikirimkan ke email Anda.');
            this.startTimer();
        },

        onOtpInput(event, idx) {
            const val = event.target.value.slice(-1);
            this.forgotOtp[idx] = val;
            if (val && idx < 5) {
                const container = event.target.closest('.otp-container');
                if (container) {
                    const inputs = container.querySelectorAll('input');
                    if (inputs[idx + 1]) inputs[idx + 1].focus();
                }
            }
        },

        handleVerifyOtp() {
            this.forgotLoading = true;
            setTimeout(() => {
                this.forgotLoading = false;
                this.forgotStep = 'newPassword';
            }, 700);
        },

        handleResetPassword() {
            if (this.newPassword.length < 8) {
                this.forgotErrorMessage = 'Kata sandi minimal 8 karakter dengan kombinasi angka & simbol.';
                return;
            }
            if (this.newPassword !== this.confirmPassword) {
                this.forgotErrorMessage = 'Konfirmasi kata sandi tidak cocok.';
                return;
            }
            this.forgotErrorMessage = '';
            this.forgotLoading = true;
            setTimeout(() => {
                this.forgotLoading = false;
                this.forgotStep = 'done';
            }, 900);
        },

        onSuccessReturn() {
            this.showForgotModal = false;
            @this.set('data.email', this.forgotEmail);
            @this.set('data.password', this.newPassword);
        }
    }"
    style="background-color: #F8FAFC; color: #0B1C30; height: 100vh; height: 100dvh; max-height: 100vh; max-height: 100dvh; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; font-family: 'Inter', system-ui, -apple-system, sans-serif;"
>
    <!-- Background ambient radial glow -->
    <div style="position: fixed; inset: 0; pointer-events: none; background: radial-gradient(circle at 50% 40%, rgba(10,37,64,0.04), transparent 65%);"></div>

    <!-- Main Centered Sign-In Content -->
    <main style="position: relative; z-index: 10; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem 1rem; width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="display: flex; flex-direction: column; width: 100%; align-items: center; justify-content: center; position: relative;">
            <!-- Colored background blurs -->
            <div style="position: absolute; top: -6rem; width: 20rem; height: 20rem; background-color: rgba(0, 112, 242, 0.05); border-radius: 9999px; filter: blur(48px); pointer-events: none;"></div>
            <div style="position: absolute; bottom: -5rem; width: 18rem; height: 18rem; background-color: rgba(252, 119, 40, 0.05); border-radius: 9999px; filter: blur(48px); pointer-events: none;"></div>

            <!-- Authentication Card (1:1 with LoginScreen.tsx) -->
            <div style="width: 100%; max-width: 500px; background-color: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 16px 36px -12px rgba(10,37,64,0.1); border: 1px solid #E2E8F0; padding: 1.375rem 1.75rem; position: relative; z-index: 10; box-sizing: border-box; transition: all 0.3s ease;">
                <!-- Logo and Headings -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <div style="height: 2.75rem; padding: 0.25rem 0.875rem; border-radius: 0.5rem; background-color: #EFF4FF; border: 1px solid #DCE9FF; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.03);">
                        <img 
                            src="{{ asset('images/logo-marel.webp') }}" 
                            alt="PT Marel Sukses Pratama Logo"
                            style="height: 1.875rem; width: auto; object-fit: contain;"
                            onerror="this.onerror=null; this.src='https://lh3.googleusercontent.com/aida/AEtjO1VRrMjqijYkL39fK5iQw2dqj4XUt94GoYje-y4mUaU70uFGVgOnF5NG5YDOfRWL3ccJBI-N48IFiy5L6fekr6CCAlHlyhj1syzXZejIBrKnLr4pTLKmidzaadiiKDWNTurPL9ycE5b_EcP0nQmeg01Ir1gEzAcAXMOvAkv3moxpwzkiRKx2yVWqVuEAbwkKbkMXE516Y7eA_Ot7LYmQAlX01IpnWSa6BNnPvbvG4fw0qWQcfjilhYGGJGG8';"
                        />
                    </div>
                    <h1 style="font-size: 1.375rem; font-weight: 600; color: #0F172A; letter-spacing: -0.02em; margin: 0; line-height: 1.2;">
                        Sign in
                    </h1>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.25rem 0 0 0; font-weight: 400; letter-spacing: -0.01em;">
                        PT Marel Sukses Pratama • Enterprise Resource Planning
                    </p>
                </div>

                <!-- Validation Error Message Alert -->
                @if ($errors->any())
                    <div style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; background-color: #FEF2F2; border: 1px solid #FECACA; font-size: 0.75rem; color: #B91C1C; display: flex; align-items: center; gap: 0.5rem;">
                        <span class="material-symbols-outlined" style="font-size: 16px; color: #DC2626;">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form
                    id="form"
                    wire:submit="authenticate"
                    style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;"
                >
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE) }}

                    <!-- Email Field -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="emailInput" style="font-size: 0.8125rem; font-weight: 500; color: #0F172A; display: flex; align-items: center;">
                                <span>Email address</span>
                                <span style="color: #DC2626; margin-left: 0.125rem;">*</span>
                            </label>
                            <span style="font-size: 10px; color: #64748B; font-weight: 500; background-color: #F1F5F9; padding: 0.125rem 0.5rem; border-radius: 0.25rem;">
                                SSO Domain
                            </span>
                        </div>
                        <div class="msp-input-wrap">
                            <div class="msp-input-icon">
                                <span class="material-symbols-outlined" style="font-size: 18px;">mail</span>
                            </div>
                            <input
                                id="emailInput"
                                name="email"
                                type="email"
                                wire:model="data.email"
                                required
                                autocomplete="username"
                                autofocus
                                placeholder="nama@erpmsp.com"
                                class="msp-input-field"
                            />
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="passwordInput" style="font-size: 0.8125rem; font-weight: 500; color: #0F172A; display: flex; align-items: center;">
                                <span>Password</span>
                                <span style="color: #DC2626; margin-left: 0.125rem;">*</span>
                            </label>
                            <button
                                type="button"
                                @click="openForgotPassword()"
                                style="font-size: 0.75rem; font-weight: 600; color: #0070F2; background: none; border: none; padding: 0; cursor: pointer; text-decoration: none;"
                                onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'"
                            >
                                Forgot password?
                            </button>
                        </div>
                        <div class="msp-input-wrap">
                            <div class="msp-input-icon">
                                <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
                            </div>
                            <input
                                id="passwordInput"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                wire:model="data.password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="msp-input-field msp-input-password"
                            />
                            <button
                                type="button"
                                aria-label="Toggle kata sandi"
                                title="Toggle kata sandi"
                                @click="showPassword = !showPassword"
                                style="position: absolute; right: 0.625rem; padding: 0.25rem; border-radius: 0.375rem; color: #64748B; background: transparent; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;"
                                onmouseover="this.style.backgroundColor='#EFF4FF'; this.style.color='#0F172A';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#64748B';"
                            >
                                <template x-if="showPassword">
                                    <svg style="width: 1.125rem; height: 1.125rem; color: #0070F2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </template>
                                <template x-if="!showPassword">
                                    <svg style="width: 1.125rem; height: 1.125rem; color: #64748B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Session Duration -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.125rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
                            <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                                <input
                                    type="checkbox"
                                    id="rememberMe"
                                    wire:model="data.remember"
                                    style="position: absolute; opacity: 0; width: 0; height: 0;"
                                />
                                <div class="msp-checkbox-box">
                                    <span class="material-symbols-outlined msp-check-icon" style="color: #FFFFFF; font-size: 11px; opacity: 0; font-weight: 700; transition: opacity 0.15s ease;">check</span>
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; color: #0F172A;">
                                Remember me
                            </span>
                        </label>
                        <span style="font-size: 10px; font-weight: 500; color: #002643; background-color: #EFF4FF; padding: 0.125rem 0.5rem; border-radius: 9999px; border: 1px solid #DCE9FF;">
                            Sesi 30 Hari
                        </span>
                    </div>

                    <!-- Sign In Button -->
                    <div style="padding-top: 0.125rem;">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="msp-btn-submit"
                        >
                            <div wire:loading.remove wire:target="authenticate" class="msp-btn-content">
                                <span>Sign in</span>
                                <span class="material-symbols-outlined msp-arrow-icon" style="font-size: 17px; transition: transform 0.15s ease;">
                                    arrow_forward
                                </span>
                            </div>
                            <div wire:loading.flex wire:target="authenticate" class="msp-btn-content" style="display: none;">
                                <svg style="animation: spin 1s linear infinite; height: 1rem; width: 1rem; color: #FFFFFF;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengautentikasi...</span>
                            </div>
                        </button>
                    </div>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER) }}
                </form>

                <!-- Sub-Card Status Strip (Inside Card) -->
                <div style="margin-top: 1.125rem; padding: 0.625rem 1.75rem; background-color: rgba(239, 244, 255, 0.7); margin-left: -1.75rem; margin-right: -1.75rem; margin-bottom: -1.375rem; border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #64748B;">
                    <div style="display: flex; align-items: center; gap: 0.375rem; color: #334155; font-weight: 500;">
                        <span class="material-symbols-outlined" style="font-size: 14px; color: #0070F2;">
                            verified_user
                        </span>
                        <span>Portal Aman Enterprise</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="position: relative; display: flex; width: 0.5rem; height: 0.5rem;">
                            <span style="position: absolute; width: 100%; height: 100%; border-radius: 9999px; background: #10B981; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                            <span style="position: relative; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: #10B981;"></span>
                        </span>
                        <span style="font-weight: 600; color: #0F172A;">Sistem Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Under-Card Security Badges -->
            <div style="margin-top: 0.875rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 0.875rem; font-size: 0.75rem; color: #64748B;">
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <span class="material-symbols-outlined" style="font-size: 15px; color: #475569;">
                        security
                    </span>
                    <span>Enkripsi TLS 1.3 Terverifikasi</span>
                </div>
                <div style="width: 1px; height: 0.75rem; background-color: #CBD5E1;"></div>
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <span class="material-symbols-outlined" style="font-size: 15px; color: #475569;">
                        corporate_fare
                    </span>
                    <span>Pusat Data Jakarta (JKT-01)</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Global Page Footer -->
    <footer style="position: relative; z-index: 10; width: 100%; padding: 0.625rem 1.5rem; border-top: 1px solid #E2E8F0; background-color: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px);">
        <div style="max-width: 80rem; margin: 0 auto; display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; font-size: 0.75rem; color: #64748B;">
            <div>
                © {{ date('Y') }} PT Marel Sukses Pratama. All rights reserved. Enterprise Resource Planning Core.
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem;">
                    <span class="material-symbols-outlined" style="font-size: 14px; color: #10B981;">
                        lock
                    </span>
                    <span>Portal Aman Enterprise</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #10B981;"></span>
                    <span>Sistem Aktif v4.18</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Forgot Password Modal (1:1 with ForgotPasswordModal.tsx) -->
    <div
        x-show="showForgotModal"
        x-cloak
        style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; background-color: rgba(10, 37, 64, 0.6); backdrop-filter: blur(4px);"
        @keydown.escape.window="showForgotModal = false"
    >
        <div
            @click.outside="showForgotModal = false"
            style="width: 100%; max-width: 28rem; background-color: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #E2E8F0; padding: 1.5rem; position: relative; box-sizing: border-box;"
        >
            <!-- Close Button -->
            <button
                type="button"
                @click="showForgotModal = false"
                style="position: absolute; top: 1rem; right: 1rem; color: #64748B; background: transparent; border: none; padding: 0.25rem; border-radius: 0.375rem; cursor: pointer; transition: color 0.15s ease;"
                onmouseover="this.style.color='#0F172A'"
                onmouseout="this.style.color='#64748B'"
            >
                <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
            </button>

            <!-- Header with Marel identity -->
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background-color: #EFF4FF; border: 1px solid #DCE9FF; display: flex; align-items: center; justify-content: center; color: #0A2540;">
                    <span class="material-symbols-outlined" style="font-size: 22px;">lock_reset</span>
                </div>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 600; color: #0F172A; margin: 0; line-height: 1.3;">
                        Pemulihan Kata Sandi SSO
                    </h2>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.125rem 0 0 0;">
                        PT Marel Sukses Pratama Security
                    </p>
                </div>
            </div>

            <!-- Error Banner -->
            <template x-if="forgotErrorMessage">
                <div style="margin-bottom: 1rem; padding: 0.625rem; border-radius: 0.5rem; background-color: #FEF2F2; border: 1px solid #FECACA; font-size: 0.75rem; color: #B91C1C; display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 16px; color: #DC2626;">error</span>
                    <span x-text="forgotErrorMessage"></span>
                </div>
            </template>

            <!-- Step 1: Input Corporate Email -->
            <div x-show="forgotStep === 'email'">
                <form @submit.prevent="handleSendOtp()" style="display: flex; flex-direction: column; gap: 1rem;">
                    <p style="font-size: 0.75rem; color: #475569; line-height: 1.5; margin: 0;">
                        Masukkan alamat email korporat Anda yang terdaftar pada direktori SSO Active Directory
                        PT Marel Sukses Pratama untuk menerima kode verifikasi 6-digit.
                    </p>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                            Email Korporat MSP
                        </label>
                        <div class="msp-input-wrap">
                            <span class="material-symbols-outlined msp-input-icon" style="font-size: 18px;">mail</span>
                            <input
                                type="email"
                                required
                                x-model="forgotEmail"
                                placeholder="nama@erpmsp.com"
                                class="msp-input-field"
                            />
                        </div>
                    </div>

                    <div style="padding-top: 0.5rem; display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                        <button
                            type="button"
                            @click="showForgotModal = false"
                            style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #475569; background: transparent; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.backgroundColor='#F1F5F9'"
                            onmouseout="this.style.backgroundColor='transparent'"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="forgotLoading"
                            class="msp-btn-submit"
                            style="width: auto; height: auto; padding: 0.5rem 1rem; margin: 0; display: inline-flex;"
                        >
                            <span x-text="forgotLoading ? 'Mengirim Kode...' : 'Kirim Kode Verifikasi'"></span>
                            <span class="material-symbols-outlined" style="font-size: 15px;">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Input 6-Digit OTP -->
            <div x-show="forgotStep === 'otp'">
                <form @submit.prevent="handleVerifyOtp()" style="display: flex; flex-direction: column; gap: 1rem;">
                    <p style="font-size: 0.75rem; color: #475569; line-height: 1.5; margin: 0;">
                        Kode otentikasi telah dikirim ke <strong style="color: #0F172A;" x-text="forgotEmail"></strong>.
                        Berlaku selama 10 menit.
                    </p>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #0F172A; margin-bottom: 0.5rem; text-align: center;">
                            Kode Verifikasi 6-Digit
                        </label>
                        <div class="otp-container" style="display: flex; gap: 0.5rem; justify-content: center;">
                            <template x-for="(digit, idx) in forgotOtp" :key="idx">
                                <input
                                    type="text"
                                    maxlength="1"
                                    :value="digit"
                                    @input="onOtpInput($event, idx)"
                                    style="width: 2.5rem; height: 3rem; text-align: center; font-family: monospace; font-size: 1.125rem; font-weight: 700; border: 1px solid #CBD5E1; border-radius: 0.5rem; outline: none; color: #0F172A;"
                                    onfocus="this.style.borderColor='#0070F2'; this.style.boxShadow='0 0 0 2px rgba(0,112,242,0.2)';"
                                    onblur="this.style.borderColor='#CBD5E1'; this.style.boxShadow='none';"
                                />
                            </template>
                        </div>
                    </div>

                    <div style="text-align: center; font-size: 11px; color: #64748B;">
                        Tidak menerima email?{' '}
                        <button
                            type="button"
                            @click="resendOtp()"
                            style="color: #0070F2; font-weight: 600; background: none; border: none; cursor: pointer; text-decoration: underline;"
                        >
                            Kirim ulang kode (<span x-text="resendTimer"></span>s)
                        </button>
                    </div>

                    <div style="padding-top: 0.5rem; display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                        <button
                            type="button"
                            @click="forgotStep = 'email'"
                            style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #475569; background: transparent; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.backgroundColor='#F1F5F9'"
                            onmouseout="this.style.backgroundColor='transparent'"
                        >
                            Kembali
                        </button>
                        <button
                            type="submit"
                            :disabled="forgotLoading"
                            class="msp-btn-submit"
                            style="width: auto; height: auto; padding: 0.5rem 1rem; margin: 0; display: inline-flex;"
                        >
                            <span x-text="forgotLoading ? 'Memvalidasi...' : 'Verifikasi & Lanjut'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Set New Password -->
            <div x-show="forgotStep === 'newPassword'">
                <form @submit.prevent="handleResetPassword()" style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <p style="font-size: 0.75rem; color: #475569; margin: 0;">
                        Buat kata sandi baru untuk akun SSO Anda.
                    </p>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
                            Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            required
                            x-model="newPassword"
                            placeholder="Minimal 8 karakter"
                            style="width: 100%; height: 2.5rem; padding: 0 0.75rem; font-size: 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; outline: none; box-sizing: border-box; color: #0F172A;"
                            onfocus="this.style.borderColor='#0070F2';"
                            onblur="this.style.borderColor='#CBD5E1';"
                        />
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            required
                            x-model="confirmPassword"
                            placeholder="Konfirmasi kata sandi"
                            style="width: 100%; height: 2.5rem; padding: 0 0.75rem; font-size: 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; outline: none; box-sizing: border-box; color: #0F172A;"
                            onfocus="this.style.borderColor='#0070F2';"
                            onblur="this.style.borderColor='#CBD5E1';"
                        />
                    </div>

                    <div style="padding: 0.625rem; border-radius: 0.5rem; background-color: #F8FAFC; border: 1px solid #E2E8F0; font-size: 11px; color: #64748B; display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span class="material-symbols-outlined" style="font-size: 13px; color: #059669;">check</span>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span class="material-symbols-outlined" style="font-size: 13px; color: #059669;">check</span>
                            <span>Memuat huruf besar, kecil, dan angka</span>
                        </div>
                    </div>

                    <div style="padding-top: 0.5rem; display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                        <button
                            type="button"
                            @click="showForgotModal = false"
                            style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #475569; background: transparent; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.backgroundColor='#F1F5F9'"
                            onmouseout="this.style.backgroundColor='transparent'"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="forgotLoading"
                            class="msp-btn-submit"
                            style="width: auto; height: auto; padding: 0.5rem 1rem; margin: 0; display: inline-flex;"
                        >
                            <span x-text="forgotLoading ? 'Menyimpan...' : 'Simpan Kata Sandi'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Done -->
            <div x-show="forgotStep === 'done'">
                <div style="text-align: center; padding: 1rem 0; display: flex; flex-direction: column; gap: 0.75rem; align-items: center;">
                    <div style="width: 3rem; height: 3rem; border-radius: 9999px; background-color: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="font-size: 28px;">check_circle</span>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #0F172A; margin: 0;">
                        Kata Sandi Berhasil Diperbarui
                    </h3>
                    <p style="font-size: 0.75rem; color: #64748B; max-width: 20rem; margin: 0; line-height: 1.4;">
                        Kredensial SSO Anda telah disinkronkan ke seluruh sistem ERP PT Marel Sukses Pratama.
                    </p>
                    <div style="padding-top: 0.75rem; width: 100%;">
                        <button
                            type="button"
                            @click="onSuccessReturn()"
                            class="msp-btn-submit"
                        >
                            Masuk Sekarang dengan Kata Sandi Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

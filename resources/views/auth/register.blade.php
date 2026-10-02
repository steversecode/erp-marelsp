<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9ff] text-[#0b1c30]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Akun - PT Marel Sukses Pratama ERP</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            vertical-align: middle;
        }
    </style>
</head>
<body class="w-full min-h-screen bg-[#f8f9ff] flex items-center justify-center p-3 sm:p-6 lg:p-10 font-sans selection:bg-[#fb7800] selection:text-white">
    <main class="w-full max-w-7xl mx-auto flex flex-col items-center justify-center">
        <div class="w-full grid grid-cols-1 lg:grid-cols-12 rounded-2xl overflow-hidden shadow-2xl bg-white border border-[#e5eeff]">
            <!-- Left Editorial & Context Panel (5 Columns) -->
            <div class="lg:col-span-5 bg-[#001849] text-white p-7 sm:p-10 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                <!-- Ambient decorative glow -->
                <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#994700]/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-96 h-96 rounded-full bg-[#0d2c6c] blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col gap-6">
                    <!-- Corporate Brand Identity -->
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-[#fb7800] text-white flex items-center justify-center font-display font-bold text-2xl shadow-md">
                            M
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-[11px] uppercase tracking-widest text-[#b3c5ff] font-semibold whitespace-nowrap">
                                ERP Portal
                            </span>
                            <span class="text-sm text-white font-bold tracking-tight whitespace-nowrap truncate font-display">
                                PT MAREL SUKSES PRATAMA
                            </span>
                        </div>
                    </div>

                    <!-- Hero Narrative -->
                    <div class="flex flex-col gap-2 mt-3">
                        <div class="inline-flex items-center gap-2 bg-[#0d2c6c] px-3 py-1 rounded-full w-fit border border-[#202e5a] whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-[#fb7800] animate-pulse shrink-0"></span>
                            <span class="text-[11px] text-[#7e96dc] font-semibold whitespace-nowrap">
                                Secure Enclave v4.2
                            </span>
                        </div>
                        <h1 class="font-display text-2xl sm:text-[28px] font-bold text-white tracking-tight leading-snug whitespace-nowrap">
                            Otorisasi Akses Karyawan Baru
                        </h1>
                        <p class="text-xs sm:text-sm text-[#b3c5ff] leading-relaxed">
                            Selamat datang di portal tunggal manajemen logistik, pengadaan material, dan operasional gudang PT Marel Sukses Pratama.
                        </p>
                    </div>

                    <!-- Process Milestone Tracker -->
                    <div class="flex flex-col gap-3.5 mt-2 bg-[#202e5a]/40 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-7 h-7 rounded-full bg-[#fb7800] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                                1
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold text-white whitespace-nowrap">
                                    Input Identitas Karyawan
                                </span>
                                <span class="text-[11px] text-[#b3c5ff] leading-relaxed">
                                    Lengkapi NIK resmi, email korporat, dan divisi penugasan.
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-7 h-7 rounded-full bg-[#0d2c6c] text-[#dae1ff] flex items-center justify-center text-xs font-bold shrink-0 border border-[#b3c5ff]/30">
                                2
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold text-white whitespace-nowrap">
                                    Validasi Akses Departemen
                                </span>
                                <span class="text-[11px] text-[#b3c5ff] leading-relaxed">
                                    Hak akses disesuaikan dengan wewenang operasional gudang/PPIC.
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-7 h-7 rounded-full bg-[#0d2c6c] text-[#dae1ff] flex items-center justify-center text-xs font-bold shrink-0 border border-[#b3c5ff]/30">
                                3
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold text-white whitespace-nowrap">
                                    Aktivasi Kredensial ERP
                                </span>
                                <span class="text-[11px] text-[#b3c5ff] leading-relaxed">
                                    Akses instan ke dashboard setelah registrasi tersimpan.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Compliance Assurance -->
                <div class="relative z-10 pt-6 mt-6 border-t border-white/10 flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-400 text-[20px] shrink-0">
                        verified_user
                    </span>
                    <span class="text-[11px] text-[#b3c5ff] leading-tight">
                        Pendaftaran dilindungi enkripsi end-to-end dan memenuhi standar kepatuhan ISO/IEC 27001 Sistem Manajemen Keamanan Informasi.
                    </span>
                </div>
            </div>

            <!-- Right Registration Form Panel (7 Columns) -->
            <div class="lg:col-span-7 p-7 sm:p-10 lg:p-12 flex flex-col justify-between bg-white relative z-10">
                <div>
                    <!-- Form Header & Quick Demo -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-[#eff4ff]">
                        <div>
                            <h2 class="font-display text-xl sm:text-2xl font-bold text-[#001849] tracking-tight">
                                Formulir Pendaftaran Akun Staf
                            </h2>
                            <p class="text-xs text-[#757681]">
                                Gunakan identitas resmi perusahaan yang terdaftar di HRD
                            </p>
                        </div>
                        <button type="button" onclick="fillDemoRegister()"
                            class="px-3 py-1.5 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0d2c6c] text-xs font-semibold flex items-center gap-1.5 transition-colors shrink-0">
                            <span class="material-symbols-outlined text-[16px] text-[#fb7800]">bolt</span>
                            <span>Auto-Fill Data</span>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-[#ffdad6] border border-[#ba1a1a]/30 text-[#ba1a1a] text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <!-- Row 1: Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-[#0b1c30]" for="name">
                                Nama Lengkap Karyawan
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </div>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                    placeholder="contoh: Bambang Prakoso"
                                    class="block w-full rounded-lg pl-10 pr-4 py-2 bg-[#eff4ff] text-[#0b1c30] text-xs placeholder:text-[#757681]/70 focus:bg-white focus:ring-1 focus:ring-[#0d2c6c] focus:outline-none transition-all border border-transparent focus:border-[#0d2c6c]">
                            </div>
                        </div>

                        <!-- Row 2: Corporate Email -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-[#0b1c30]" for="email">
                                Alamat Email Korporat Resmi
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                    <span class="material-symbols-outlined text-[18px]">mail</span>
                                </div>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    placeholder="nama.lengkap@marel.co.id"
                                    class="block w-full rounded-lg pl-10 pr-4 py-2 bg-[#eff4ff] text-[#0b1c30] text-xs placeholder:text-[#757681]/70 focus:bg-white focus:ring-1 focus:ring-[#0d2c6c] focus:outline-none transition-all border border-transparent focus:border-[#0d2c6c]">
                            </div>
                        </div>

                        <!-- Row 3: Password & Confirm Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-[#0b1c30]" for="password">
                                    Kata Sandi Baru
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                        <span class="material-symbols-outlined text-[18px]">lock</span>
                                    </div>
                                    <input id="password" name="password" type="password" required
                                        placeholder="Min. 8 karakter"
                                        class="block w-full rounded-lg pl-10 pr-4 py-2 bg-[#eff4ff] text-[#0b1c30] text-xs focus:bg-white focus:ring-1 focus:ring-[#0d2c6c] focus:outline-none transition-all border border-transparent focus:border-[#0d2c6c]">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-[#0b1c30]" for="password_confirmation">
                                    Konfirmasi Kata Sandi
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                        <span class="material-symbols-outlined text-[18px]">lock_clock</span>
                                    </div>
                                    <input id="password_confirmation" name="password_confirmation" type="password" required
                                        placeholder="Ulangi kata sandi"
                                        class="block w-full rounded-lg pl-10 pr-4 py-2 bg-[#eff4ff] text-[#0b1c30] text-xs focus:bg-white focus:ring-1 focus:ring-[#0d2c6c] focus:outline-none transition-all border border-transparent focus:border-[#0d2c6c]">
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Checkbox -->
                        <div class="pt-1">
                            <label class="flex items-start gap-2 cursor-pointer select-none text-xs text-[#444650]">
                                <input id="policyConsent" type="checkbox" required checked
                                    class="w-4 h-4 mt-0.5 rounded text-[#0d2c6c] focus:ring-[#0d2c6c] border-[#c5c6d2] bg-[#eff4ff] accent-[#0d2c6c]">
                                <span>
                                    Saya menyetujui seluruh <a href="#" class="text-[#0d2c6c] font-semibold underline">SOP Keamanan &amp; Integritas Data ERP</a> PT Marel Sukses Pratama.
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-[#fb7800] hover:bg-[#994700] text-white py-2.5 px-5 rounded-lg font-semibold text-xs transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.99] cursor-pointer mt-3">
                            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                            <span>Daftarkan Akun Karyawan</span>
                        </button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="mt-6 pt-4 text-center border-t border-[#eff4ff] text-xs text-[#757681]">
                        <span>Sudah memiliki akun terdaftar?</span>
                        <a href="{{ route('login') }}" class="text-[#0d2c6c] font-bold hover:underline ml-1">
                            Masuk ke Portal →
                        </a>
                    </div>
                </div>

                <p class="text-[11px] text-[#757681] mt-6">
                    © {{ date('Y') }} PT Marel Sukses Pratama. Seluruh hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </main>

    <script>
        function fillDemoRegister() {
            document.getElementById('name').value = 'Bambang Prakoso';
            document.getElementById('email').value = 'bambang.prakoso@marel.co.id';
            document.getElementById('password').value = 'password123';
            document.getElementById('password_confirmation').value = 'password123';
            document.getElementById('policyConsent').checked = true;
        }
    </script>
</body>
</html>

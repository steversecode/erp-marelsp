<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9ff] text-[#0b1c30]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - PT Marel Sukses Pratama ERP Portal</title>

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
    <main class="w-full max-w-7xl mx-auto flex items-center justify-center">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 w-full rounded-2xl overflow-hidden shadow-2xl bg-white border border-[#e5eeff]">
            <!-- Left Column: Login Form -->
            <div class="lg:col-span-6 xl:col-span-5 p-7 sm:p-10 lg:p-12 flex flex-col justify-between bg-white relative z-10">
                <div>
                    <!-- Company Logo -->
                    <div class="mb-8">
                        <div class="inline-flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-[#fb7800] text-white flex items-center justify-center font-display font-bold text-2xl shadow-md">
                                M
                            </div>
                            <div class="flex flex-col text-left">
                                <span class="font-display font-bold text-[#001849] tracking-tight leading-tight text-lg">
                                    PT MAREL
                                </span>
                                <span class="text-[10px] uppercase font-bold text-[#fb7800] tracking-wider leading-none">
                                    SUKSES PRATAMA
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Header Titles -->
                    <div class="space-y-1 mb-6">
                        <h1 class="font-display text-2xl sm:text-[28px] font-bold text-[#001849] tracking-tight whitespace-nowrap">
                            Masuk ke Portal ERP Marel
                        </h1>
                        <p class="text-sm text-[#444650] whitespace-nowrap">
                            Sistem Manajemen Terpadu &amp; Inventaris
                        </p>
                    </div>

                    <!-- Quick Demo Helper Bar -->
                    <div class="mb-5 p-2.5 rounded-lg bg-[#eff4ff] border border-[#dce9ff] flex items-center justify-between gap-2 text-xs">
                        <span class="text-[#0d2c6c] font-medium flex items-center gap-1 whitespace-nowrap">
                            <span class="material-symbols-outlined text-[16px] text-[#fb7800] shrink-0">key</span>
                            Akun Demo:
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="fillDemo('admin@marel.co.id', 'password123')"
                                class="px-2.5 py-1 rounded bg-white text-[#0d2c6c] hover:bg-[#dae1ff] border border-[#c5c6d2] font-semibold text-[11px] whitespace-nowrap transition-colors cursor-pointer">
                                Admin Logistik
                            </button>
                            <button type="button" onclick="fillDemo('staff@marel.co.id', 'password123')"
                                class="px-2.5 py-1 rounded bg-white text-[#0d2c6c] hover:bg-[#dae1ff] border border-[#c5c6d2] font-semibold text-[11px] whitespace-nowrap transition-colors cursor-pointer">
                                Staff Gudang
                            </button>
                        </div>
                    </div>

                    <!-- Flash / Error Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-[#ffdad6] border border-[#ba1a1a]/30 text-[#ba1a1a] text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email / Identifier -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-[#0b1c30]" for="email">
                                Email atau NIK Karyawan
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                </div>
                                <input id="email" name="email" type="email" value="{{ old('email', 'admin@marel.co.id') }}" required autofocus
                                    placeholder="contoh: admin@marel.co.id"
                                    class="block w-full rounded-lg pl-10 pr-4 py-2.5 bg-[#eff4ff] text-[#0b1c30] text-sm placeholder:text-[#757681]/70 focus:bg-white focus:ring-2 focus:ring-[#0d2c6c] focus:outline-none transition-all duration-150 border border-transparent focus:border-[#0d2c6c]">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-[#0b1c30]" for="password">
                                Kata Sandi
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input id="password" name="password" type="password" required value="password123"
                                    placeholder="Masukkan kata sandi akun"
                                    class="block w-full rounded-lg pl-10 pr-11 py-2.5 bg-[#eff4ff] text-[#0b1c30] text-sm placeholder:text-[#757681]/70 focus:bg-white focus:ring-2 focus:ring-[#0d2c6c] focus:outline-none transition-all duration-150 border border-transparent focus:border-[#0d2c6c]">
                                <button type="button" aria-label="Toggle password visibility" onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#757681] hover:text-[#0b1c30] transition-colors cursor-pointer">
                                    <span id="passToggleIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-1 text-sm">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input id="remember_me" name="remember" type="checkbox" checked
                                    class="w-4 h-4 rounded text-[#0d2c6c] focus:ring-[#0d2c6c] border-[#c5c6d2] bg-[#eff4ff] cursor-pointer accent-[#0d2c6c]">
                                <span class="text-xs text-[#444650]">
                                    Ingat Saya di perangkat ini
                                </span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-[#fb7800] hover:text-[#994700] font-semibold transition-colors">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <!-- Primary CTA Button -->
                        <button id="submitBtn" type="submit"
                            class="w-full flex items-center justify-center gap-2.5 bg-[#0d2c6c] hover:bg-[#001849] text-white py-3 px-5 rounded-lg font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.99] cursor-pointer mt-2">
                            <span>Masuk ke Sistem</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </form>

                    <!-- Registration Link -->
                    <div class="mt-7 pt-5 bg-[#f8f9ff] rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left border border-[#e5eeff]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#dce9ff] flex items-center justify-center text-[#0d2c6c]">
                                <span class="material-symbols-outlined text-[18px]">person_add</span>
                            </div>
                            <span class="text-xs text-[#444650]">Belum punya akun admin?</span>
                        </div>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center text-xs text-[#fb7800] hover:text-[#994700] font-semibold group transition-colors">
                            <span>Ajukan Pendaftaran</span>
                            <span class="material-symbols-outlined text-[16px] ml-1 group-hover:translate-x-0.5 transition-transform">
                                chevron_right
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Security & Legal Footer -->
                <div class="mt-8 pt-5 space-y-2 border-t border-[#e5eeff]">
                    <div class="flex items-center gap-2 text-[#444650]">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">
                            verified_user
                        </span>
                        <span class="text-[11px] text-[#757681] leading-tight">
                            Sesi terenkripsi SSL 256-bit kelas perbankan industri. Dilindungi oleh proteksi sesi multi-layer.
                        </span>
                    </div>
                    <p class="text-[11px] text-[#757681]">
                        © {{ date('Y') }} PT Marel Sukses Pratama. Seluruh hak cipta dilindungi undang-undang.
                    </p>
                </div>
            </div>

            <!-- Right Column: Visual Industrial Metric Banner -->
            <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-[#001849] relative flex-col justify-between p-10 xl:p-12 overflow-hidden text-white">
                <!-- Ambient Modern Patterns -->
                <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#0d2c6c]/40 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-20 w-80 h-80 rounded-full bg-[#fb7800]/20 blur-3xl pointer-events-none"></div>

                <!-- Top Badge in Right Column -->
                <div class="relative z-10 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#0d2c6c]/70 backdrop-blur-md border border-[#202e5a]">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] text-[#dae1ff] uppercase tracking-wider font-semibold">
                            Live Core Engine v4.8
                        </span>
                    </div>
                    <span class="text-[11px] text-[#b3c5ff]/80 font-mono">
                        Cluster: JKT-MSP-PROD-01
                    </span>
                </div>

                <!-- Central Content -->
                <div class="relative z-10 my-auto py-8 space-y-6">
                    <div class="space-y-3">
                        <span class="text-xs uppercase tracking-widest font-bold text-[#fb7800] bg-[#fb7800]/10 px-3 py-1 rounded-full border border-[#fb7800]/20 inline-block">
                            Sistem Logistik &amp; Manufaktur Terpadu
                        </span>
                        <h2 class="font-display text-3xl xl:text-4xl font-bold leading-tight tracking-tight text-white">
                            Solusi Rantai Pasok &amp; ERP Tekstil Presisi
                        </h2>
                        <p class="text-sm text-[#b3c5ff] leading-relaxed max-w-lg">
                            Kelola mutasi material, pemantauan inventaris multi-gudang, dynamic BoM component switching, dan penerbitan PO otomatis dalam satu ekosistem terintegrasi.
                        </p>
                    </div>

                    <!-- Highlight Badges -->
                    <div class="grid grid-cols-2 gap-3.5 pt-2">
                        <div class="p-3.5 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#fb7800]/20 flex items-center justify-center text-[#fb7800] shrink-0">
                                <span class="material-symbols-outlined text-[20px]">sync_alt</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">Buku Besar Mutasi</p>
                                <p class="text-[11px] text-[#b3c5ff] truncate">Sinkronisasi Real-time</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#dae1ff]/20 flex items-center justify-center text-[#dae1ff] shrink-0">
                                <span class="material-symbols-outlined text-[20px]">shuffle</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">Dynamic BoM Switch</p>
                                <p class="text-[11px] text-[#b3c5ff] truncate">Fleksibilitas Produksi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Status -->
                <div class="relative z-10 flex items-center justify-between text-xs text-[#b3c5ff]/80 pt-4 border-t border-white/10">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Server Status: Operasional 100%
                    </span>
                    <span>Protokol ISO 9001:2015</span>
                </div>
            </div>
        </div>
    </main>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }

        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const passIcon = document.getElementById('passToggleIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                passIcon.textContent = 'visibility_off';
            } else {
                passInput.type = 'password';
                passIcon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>

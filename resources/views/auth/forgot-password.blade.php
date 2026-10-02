<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9ff] text-[#0b1c30]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemulihan Kata Sandi - PT Marel Sukses Pratama ERP</title>

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
</head>
<body class="w-full min-h-screen bg-[#f8f9ff] flex items-center justify-center p-4 font-sans selection:bg-[#fb7800] selection:text-white">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-2xl border border-[#e5eeff] space-y-6">
        <div class="text-center">
            <div class="inline-flex items-center gap-2.5 mb-4">
                <div class="w-10 h-10 rounded-xl bg-[#fb7800] text-white flex items-center justify-center font-display font-bold text-xl shadow-md">
                    M
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-display font-bold text-[#001849] tracking-tight leading-tight text-base">
                        PT MAREL
                    </span>
                    <span class="text-[10px] uppercase font-bold text-[#fb7800] tracking-wider leading-none">
                        SUKSES PRATAMA
                    </span>
                </div>
            </div>
            <h1 class="font-display text-xl font-bold text-[#001849]">
                Pemulihan Kata Sandi
            </h1>
            <p class="text-xs text-[#757681] mt-1 leading-relaxed">
                Masukkan alamat email resmi Anda dan kami akan mengirimkan tautan reset kata sandi ke kotak masuk Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded-lg bg-[#ffdad6] border border-[#ba1a1a]/30 text-[#ba1a1a] text-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4 text-xs">
            @csrf
            <div class="space-y-1.5">
                <label class="block font-semibold text-[#0b1c30]" for="email">
                    Alamat Email Terdaftar
                </label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#757681]">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                    </div>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        placeholder="contoh: admin@marel.co.id"
                        class="block w-full rounded-lg pl-10 pr-4 py-2.5 bg-[#eff4ff] text-[#0b1c30] text-xs placeholder:text-[#757681]/70 focus:bg-white focus:ring-1 focus:ring-[#0d2c6c] focus:outline-none transition-all border border-transparent focus:border-[#0d2c6c]">
                </div>
            </div>

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-[#0d2c6c] hover:bg-[#001849] text-white py-2.5 px-4 rounded-lg font-semibold transition-all shadow-md">
                <span class="material-symbols-outlined text-[16px]">send</span>
                <span>Kirim Tautan Reset Kata Sandi</span>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-[#eff4ff] text-xs">
            <a href="{{ route('login') }}" class="text-[#0d2c6c] font-bold hover:underline flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Halaman Masuk</span>
            </a>
        </div>
    </div>
</body>
</html>

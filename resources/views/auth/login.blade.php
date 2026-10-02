<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · IMS | Connecti Jelajah Priangan</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}?v={{ file_exists(public_path('img/logo.png')) ? filemtime(public_path('img/logo.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v={{ file_exists(public_path('img/logo.png')) ? filemtime(public_path('img/logo.png')) : time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Space Grotesk', system-ui, sans-serif; }

        /* Latar belakang bentuk organik gradasi */
        .bg-canvas {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(circle at 12% 18%, rgba(6, 182, 212, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 88% 82%, rgba(37, 99, 235, 0.14) 0%, transparent 45%);
        }

        /* Kurva dan ombak SVG dinamis */
        .wave-curve-1 {
            border-radius: 42% 58% 68% 32% / 36% 45% 55% 64%;
            animation: waveMorph 12s ease-in-out infinite alternate;
        }
        .wave-curve-2 {
            border-radius: 65% 35% 42% 58% / 50% 60% 40% 50%;
            animation: waveMorph 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes waveMorph {
            0%   { border-radius: 42% 58% 68% 32% / 36% 45% 55% 64%; }
            50%  { border-radius: 55% 45% 38% 62% / 48% 35% 65% 52%; }
            100% { border-radius: 68% 32% 52% 48% / 60% 55% 45% 40%; }
        }

        /* Animasi halus muncul */
        @keyframes modalEnter {
            from { opacity: 0; transform: scale(0.97) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal {
            animation: modalEnter 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.7s linear infinite; }
    </style>
</head>
<body class="bg-canvas min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">

    <!-- Bentuk kurva dekoratif di luar kartu (khas ciri visual Connecti biru-cyan) -->
    <div class="pointer-events-none fixed -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-cyan-400/25 via-blue-600/20 to-transparent rounded-full blur-2xl"></div>
    <div class="pointer-events-none fixed -bottom-24 -right-20 w-[28rem] h-[28rem] bg-gradient-to-tl from-blue-700/20 via-cyan-500/15 to-transparent rounded-full blur-3xl"></div>

    <!-- Bentuk organik aksen di pojok kartu seperti pada mockup referensi -->
    <div class="pointer-events-none absolute -top-6 -left-6 w-48 h-48 sm:w-64 sm:h-64 bg-gradient-to-br from-[#06b6d4] to-[#1e3a5f] rounded-3xl -rotate-12 opacity-80 filter blur-sm"></div>
    <div class="pointer-events-none absolute -bottom-8 -left-8 w-44 h-44 sm:w-56 sm:h-56 bg-gradient-to-tr from-[#1d4ed8] via-[#0284c7] to-[#0f172a] rounded-3xl rotate-12 opacity-70 filter blur-sm"></div>

    <!-- ================= KARTU MODAL LOGIN ================= -->
    <div class="animate-modal relative z-10 w-full max-w-4xl bg-white rounded-[32px] shadow-[0_25px_60px_-15px_rgba(15,38,71,0.22)] border border-slate-100/80 overflow-hidden flex flex-col md:flex-row min-h-[540px]">

        <!-- ============ BAGIAN KIRI: ARTWORK KURVA FLUID & BADGE LOGO ============ -->
        <div class="relative md:w-[50%] lg:w-[52%] overflow-hidden flex items-center justify-center p-8 sm:p-10 lg:p-12 min-h-[300px] md:min-h-auto"
             style="background: linear-gradient(135deg, #0a192f 0%, #0f2b52 35%, #1e40af 70%, #0891b2 100%);">

            <!-- Lapisan bentuk kurva cairan / wave organik berlapis -->
            <div class="absolute -top-16 -right-16 w-80 h-80 bg-gradient-to-br from-cyan-400/35 to-blue-500/20 wave-curve-1 pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-16 w-88 h-88 bg-gradient-to-tr from-blue-900/60 via-indigo-600/30 to-cyan-300/25 wave-curve-2 pointer-events-none"></div>
            <div class="absolute inset-0 bg-radial-gradient from-transparent via-blue-950/20 to-black/30 pointer-events-none"></div>

            <!-- Lingkaran Lengkungan Besar (Fluid Arc seperti pada referensi) -->
            <div class="absolute -left-16 top-1/2 -translate-y-1/2 w-[340px] h-[340px] md:w-[420px] md:h-[420px] rounded-full border-[28px] md:border-[36px] border-cyan-400/15 pointer-events-none"></div>
            <div class="absolute -left-20 top-1/2 -translate-y-1/2 w-[380px] h-[380px] md:w-[480px] md:h-[480px] rounded-full border-[2px] border-dashed border-cyan-300/20 pointer-events-none"></div>

            <!-- LINGKARAN PUTIH / BADGE TENGAH (Sesuai Desain Referensi) -->
            <div class="relative z-10 w-64 h-64 sm:w-72 sm:h-72 rounded-full bg-white shadow-[0_15px_45px_rgba(0,0,0,0.25)] p-6 sm:p-7 flex flex-col items-center justify-center text-center border-4 border-white/80 transition-transform duration-300 hover:scale-[1.02]">
                
                <!-- Logo Fast Connect / Connecti -->
                <div class="mb-3 flex items-center justify-center">
                    <img src="{{ asset('img/logo.png') }}"
                         alt="Logo Connecti Jelajah Priangan"
                         class="h-14 sm:h-16 w-auto max-w-[210px] object-contain filter drop-shadow-sm">
                </div>

                <!-- Judul & Tagline Perusahaan -->
                <div class="px-2">
                    <h3 class="font-display font-extrabold text-sm sm:text-[15px] text-slate-800 tracking-tight leading-tight">
                        Connecti Jelajah Priangan
                    </h3>
                    <p class="text-[11px] font-semibold text-cyan-600 uppercase tracking-widest mt-1">
                        Integrated Management System
                    </p>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-2 max-w-[200px] mx-auto">
                        Kelola jaringan, pendaftaran, & pemantauan pelanggan dalam satu dasbor terpadu.
                    </p>
                </div>
            </div>
        </div>

        <!-- ============ BAGIAN KANAN: FORM MASUK (STYLE REFERENSI) ============ -->
        <div class="md:w-[50%] lg:w-[48%] bg-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between items-center text-center">

            <!-- Container Konten Tengah -->
            <div class="w-full max-w-xs mx-auto my-auto">

                <!-- Avatar Template Kosong (Silhouette) sesuai instruksi -->
                <div class="mb-5 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-slate-100 border-2 border-slate-200/80 flex items-center justify-center text-slate-400 overflow-hidden shadow-inner mx-auto transition-transform hover:scale-105">
                        <svg class="w-full h-full text-slate-400 pt-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h2 class="font-display font-bold text-xl text-slate-800 tracking-tight mt-2.5">
                        Masuk Akun
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Masukkan kredensial pengguna Anda
                    </p>
                </div>

                <!-- Pesan Error Validasi -->
                @if ($errors->any())
                    <div class="mb-4 flex items-start gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs text-rose-700 text-left">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 flex-shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm" class="space-y-3.5">
                    @csrf

                    <!-- Input Username (Pill Shaped dengan Icon Kanan sesuai mockup) -->
                    <div class="relative text-left">
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" autofocus
                               placeholder="Username"
                               class="w-full rounded-full border border-slate-200 bg-slate-50/60 py-3 pl-5 pr-11 text-xs text-slate-800 outline-none transition-all duration-200 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </div>
                    </div>

                    <!-- Input Password (Pill Shaped dengan Toggle Icon Lock/Eye) -->
                    <div class="relative text-left">
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               placeholder="Password"
                               class="w-full rounded-full border border-slate-200 bg-slate-50/60 py-3 pl-5 pr-11 text-xs text-slate-800 outline-none transition-all duration-200 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <button type="button" onclick="togglePass()" aria-label="Lihat password"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors">
                            <i id="eyeIcon" class="fa-solid fa-lock text-sm"></i>
                        </button>
                    </div>

                    <!-- Ingat Saya -->
                    <div class="flex items-center justify-between px-2 pt-0.5 text-xs text-slate-500">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember" type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Login (Pill Shaped Gradasi Khas Ciri CJP/IMS) -->
                    <div class="pt-2">
                        <button type="submit" id="submitBtn"
                                class="w-full rounded-full bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-700 hover:via-indigo-700 hover:to-cyan-600 py-3 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 disabled:opacity-80 disabled:cursor-not-allowed">
                            <span id="btnLabel" class="flex items-center justify-center gap-2">
                                LOGIN
                            </span>
                            <span id="btnSpinner" class="hidden items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-notch spin"></i> Memverifikasi…
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Bantuan Lupa Password / Kontak Admin -->
                <div class="mt-4 text-xs text-slate-400">
                    <p>Kendala akses? <span class="text-blue-600 font-semibold cursor-pointer hover:underline">Hubungi Administrator</span></p>
                </div>
            </div>

            <!-- Footer Hak Cipta di Bawah Kartu -->
            <div class="w-full pt-6 mt-4 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-center">
                <span>&copy; 2026 Integrated Management System</span>
            </div>
        </div>

    </div>

    <!-- Script Fungsionalitas Login -->
    <script>
        // Toggle lihat / sembunyikan password
        function togglePass() {
            var p = document.getElementById('password');
            var i = document.getElementById('eyeIcon');
            if (p.type === 'password') {
                p.type = 'text';
                i.className = 'fa-regular fa-eye text-sm';
            } else {
                p.type = 'password';
                i.className = 'fa-solid fa-lock text-sm';
            }
        }

        // Simpan username di localStorage jika opsi "Ingat saya" aktif
        (function () {
            var u = document.getElementById('username');
            var r = document.getElementById('remember');
            try {
                var saved = localStorage.getItem('ims_remember_user');
                if (saved) {
                    u.value = saved;
                    r.checked = true;
                    document.getElementById('password').focus();
                }
            } catch (e) {}

            document.getElementById('loginForm').addEventListener('submit', function () {
                try {
                    if (r.checked) localStorage.setItem('ims_remember_user', u.value.trim());
                    else localStorage.removeItem('ims_remember_user');
                } catch (e) {}

                // Status Loading tombol submit
                var btn = document.getElementById('submitBtn');
                btn.disabled = true;
                document.getElementById('btnLabel').classList.add('hidden');
                document.getElementById('btnSpinner').classList.remove('hidden');
                document.getElementById('btnSpinner').classList.add('flex');
            });
        })();
    </script>
</body>
</html>
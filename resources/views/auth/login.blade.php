<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · Connecti Jelajah Priangan</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Space Grotesk', system-ui, sans-serif; }

        /* grid titik ambient (bukan blob) */
        .dot-grid { background-image: radial-gradient(circle at 1px 1px, rgba(148,163,184,0.18) 1px, transparent 0); background-size: 26px 26px; }

        /* graf jaringan */
        @keyframes nodePulse { 0%,100% { transform: scale(1); opacity: .55; } 50% { transform: scale(1.5); opacity: 1; } }
        @keyframes dashFlow { to { stroke-dashoffset: -24; } }
        @keyframes haloPulse { 0%,100% { opacity: .25; transform: scale(1); } 50% { opacity: .5; transform: scale(1.15); } }
        .net-node { transform-box: fill-box; transform-origin: center; animation: nodePulse 3.4s ease-in-out infinite; }
        .net-line { stroke-dasharray: 4 8; animation: dashFlow 1.6s linear infinite; }
        .net-halo { transform-box: fill-box; transform-origin: center; animation: haloPulse 4s ease-in-out infinite; }

        /* entrance */
        @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .rise { opacity: 0; animation: rise .7s cubic-bezier(.16,1,.3,1) forwards; }
        .d1 { animation-delay: .05s; } .d2 { animation-delay: .15s; } .d3 { animation-delay: .25s; }
        .d4 { animation-delay: .35s; } .d5 { animation-delay: .45s; } .d6 { animation-delay: .55s; }
        .fade { opacity: 0; animation: fadeIn 1s ease forwards; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin .7s linear infinite; }
        @media (prefers-reduced-motion: reduce) {
            .rise,.fade { opacity: 1 !important; animation: none !important; }
            .net-node,.net-line,.net-halo { animation: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="min-h-screen flex">

        <!-- ============ PANEL KIRI: identitas + visual jaringan ============ -->
        <aside class="relative hidden lg:flex lg:w-[46%] xl:w-[50%] flex-col justify-between overflow-hidden bg-slate-950 text-white p-12 xl:p-16">
            <!-- glow terkontrol + grid -->
            <div class="pointer-events-none absolute -top-24 -left-24 w-[28rem] h-[28rem] rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 w-[24rem] h-[24rem] rounded-full bg-blue-600/10 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 dot-grid opacity-60"></div>

            <!-- graf jaringan (SVG) -->
            <svg class="pointer-events-none absolute inset-0 w-full h-full opacity-70 fade" viewBox="0 0 480 600" preserveAspectRatio="xMidYMid slice" fill="none">
                <g stroke="rgba(56,189,248,0.35)" stroke-width="1.4">
                    <line class="net-line" x1="90"  y1="140" x2="240" y2="90"/>
                    <line class="net-line" x1="240" y1="90"  x2="390" y2="170" style="animation-delay:.3s"/>
                    <line class="net-line" x1="90"  y1="140" x2="150" y2="300" style="animation-delay:.6s"/>
                    <line class="net-line" x1="240" y1="90"  x2="300" y2="320" style="animation-delay:.2s"/>
                    <line class="net-line" x1="390" y1="170" x2="300" y2="320" style="animation-delay:.5s"/>
                    <line class="net-line" x1="150" y1="300" x2="300" y2="320" style="animation-delay:.8s"/>
                    <line class="net-line" x1="150" y1="300" x2="110" y2="470" style="animation-delay:.4s"/>
                    <line class="net-line" x1="300" y1="320" x2="360" y2="480" style="animation-delay:.7s"/>
                    <line class="net-line" x1="110" y1="470" x2="360" y2="480" style="animation-delay:.9s"/>
                </g>
                <g>
                    <circle class="net-halo" cx="240" cy="90"  r="22" fill="rgba(34,211,238,0.18)"/>
                    <circle class="net-halo" cx="300" cy="320" r="26" fill="rgba(59,130,246,0.16)" style="animation-delay:1s"/>
                </g>
                <g fill="#22d3ee">
                    <circle class="net-node" cx="90"  cy="140" r="4"/>
                    <circle class="net-node" cx="240" cy="90"  r="6" style="animation-delay:.4s"/>
                    <circle class="net-node" cx="390" cy="170" r="4" style="animation-delay:.8s"/>
                    <circle class="net-node" cx="150" cy="300" r="5" style="animation-delay:1.2s"/>
                    <circle class="net-node" cx="300" cy="320" r="7" fill="#3b82f6" style="animation-delay:.6s"/>
                    <circle class="net-node" cx="110" cy="470" r="4" style="animation-delay:1s"/>
                    <circle class="net-node" cx="360" cy="480" r="5" style="animation-delay:1.4s"/>
                </g>
            </svg>

            <!-- konten -->
            <div class="relative z-10">
                <div class="flex items-center gap-3.5 rise d1">
                    <div class="h-12 w-12 flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('img/logo.png') }}" alt="Connecti Jelajah Priangan Logo" class="h-full w-full object-contain">
                    </div>
                    <div class="leading-tight">
                        <p class="font-display font-extrabold tracking-wide text-base text-white">CONNECTI JELAJAH PRIANGAN</p>
                        <p class="font-display font-medium text-cyan-300 text-[11px] tracking-wider uppercase">Internet Cepat, Koneksi Tanpa Batas</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 my-auto py-16">
                <p class="rise d2 text-cyan-300/90 text-xs font-semibold uppercase tracking-[0.25em] mb-4">Integrated Management System</p>
                <h1 class="rise d3 font-display font-bold text-4xl xl:text-5xl leading-[1.08] text-white">
                    Kelola jaringan<br>
                    <span class="text-slate-400">dalam satu dasbor.</span>
                </h1>
                <p class="rise d4 mt-5 text-slate-400 text-[15px] leading-relaxed max-w-md">
                    Pendaftaran, tiket, perubahan layanan, hingga pemantauan pelanggan, terhimpun dan terhubung secara langsung.
                </p>

                <ul class="rise d5 mt-9 space-y-4 max-w-md">
                    @foreach ([
                        ['fa-tower-broadcast', 'Pemantauan realtime', 'Status layanan & antrian tiket terkini.'],
                        ['fa-route',           'Alur kerja terpadu', 'Registrasi hingga terminasi dalam satu alur.'],
                        ['fa-shield-halved',   'Akses bertingkat',   'Hanya pengguna terdaftar yang dapat masuk.'],
                    ] as $f)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 w-9 h-9 rounded-lg bg-white/5 ring-1 ring-white/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $f[0] }} text-cyan-300 text-sm"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-100">{{ $f[1] }}</p>
                                <p class="text-[13px] text-slate-400 leading-snug">{{ $f[2] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative z-10 rise d6 text-xs text-slate-500 flex items-center justify-between">
                <span>&copy; 2021 Integrated Management System</span>
                <span class="font-mono">v3.0.1</span>
            </div>
        </aside>

        <!-- ============ PANEL KANAN: form masuk ============ -->
        <main class="flex-1 flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-sm">

                <!-- logo mobile -->
                <div class="lg:hidden flex items-center gap-3 mb-10 rise d1">
                    <div class="h-10 w-10 flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('img/logo.png') }}" alt="Connecti Jelajah Priangan Logo" class="h-full w-full object-contain">
                    </div>
                    <div class="leading-tight">
                        <span class="font-display font-extrabold tracking-wide text-slate-900 block text-sm">CONNECTI JELAJAH PRIANGAN</span>
                        <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block">Internet Cepat, Koneksi Tanpa Batas</span>
                    </div>
                </div>

                <div class="rise d2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-600 mb-2">Selamat datang kembali</p>
                    <h2 class="font-display font-bold text-3xl text-slate-900">Masuk ke akun Anda</h2>
                    <p class="mt-2 text-sm text-slate-500">Gunakan kredensial yang diberikan administrator.</p>
                </div>

                <!-- pesan error -->
                @if ($errors->any())
                    <div class="rise d3 mt-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" id="loginForm" class="mt-8 space-y-5">
                    @csrf

                    <!-- username -->
                    <div class="rise d3">
                        <label for="username" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">Username</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 transition-colors peer-focus:text-blue-600">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" autofocus
                                class="peer w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-800 outline-none transition-all duration-200 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>
                    </div>

                    <!-- password -->
                    <div class="rise d4">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 transition-colors peer-focus:text-blue-600">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                class="peer w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-11 text-sm text-slate-800 outline-none transition-all duration-200 placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <button type="button" onclick="togglePass()" aria-label="Lihat password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                                <i id="eyeIcon" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ingat saya -->
                    <div class="rise d5 flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-600">Ingat username saya</span>
                        </label>
                    </div>

                    <!-- submit -->
                    <button type="submit" id="submitBtn"
                        class="rise d5 group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition-all duration-200 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-80 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/25 to-transparent skew-x-12 transition-transform duration-700 group-hover:translate-x-full"></span>
                        <span id="btnLabel" class="relative flex items-center justify-center gap-2">
                            Masuk <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1"></i>
                        </span>
                        <span id="btnSpinner" class="relative hidden items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch spin"></i> Memverifikasi…
                        </span>
                    </button>
                </form>

                <p class="rise d6 mt-8 text-center text-xs text-slate-400">
                    Belum punya akses? Hubungi administrator sistem.
                </p>
            </div>
        </main>
    </div>

    <script>
        // lihat / sembunyikan password
        function togglePass() {
            var p = document.getElementById('password');
            var i = document.getElementById('eyeIcon');
            if (p.type === 'password') { p.type = 'text';  i.className = 'fa-regular fa-eye-slash'; }
            else                       { p.type = 'password'; i.className = 'fa-regular fa-eye'; }
        }

        // "ingat username" via localStorage (hanya username, bukan password)
        (function () {
            var u = document.getElementById('username');
            var r = document.getElementById('remember');
            try {
                var saved = localStorage.getItem('ims_remember_user');
                if (saved) { u.value = saved; r.checked = true; document.getElementById('password').focus(); }
            } catch (e) {}
            document.getElementById('loginForm').addEventListener('submit', function () {
                try {
                    if (r.checked) localStorage.setItem('ims_remember_user', u.value.trim());
                    else localStorage.removeItem('ims_remember_user');
                } catch (e) {}
                // keadaan loading
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
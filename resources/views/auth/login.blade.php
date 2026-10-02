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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            letter-spacing: -0.01em;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
            position: relative;
            /* Latar belakang netral dengan sentuhan gradasi biru lembut */
            background-color: #eef2f7;
            background-image:
                radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.08) 0%, transparent 50%);
        }

        .font-outfit { font-family: 'Outfit', system-ui, sans-serif; }

        /* ============ BENTUK DEKORATIF LATAR BELAKANG ============ */
        .bg-deco {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }
        .bg-deco-1 {
            top: -120px; left: -120px;
            width: 420px; height: 420px;
            background: linear-gradient(135deg, #0e4d92 0%, #0891b2 100%);
            border-radius: 40% 60% 65% 35% / 40% 50% 50% 60%;
            opacity: 0.18;
            animation: floatDeco 14s ease-in-out infinite alternate;
        }
        .bg-deco-2 {
            bottom: -100px; right: -100px;
            width: 360px; height: 360px;
            background: linear-gradient(135deg, #1e40af 0%, #06b6d4 100%);
            border-radius: 55% 45% 40% 60% / 50% 60% 40% 50%;
            opacity: 0.15;
            animation: floatDeco 12s ease-in-out infinite alternate-reverse;
        }
        .bg-deco-3 {
            bottom: -60px; left: 10%;
            width: 280px; height: 280px;
            background: linear-gradient(135deg, #0284c7 0%, #1e3a5f 100%);
            border-radius: 60% 40% 50% 50% / 45% 55% 45% 55%;
            opacity: 0.12;
            animation: floatDeco 16s ease-in-out infinite alternate;
        }

        @keyframes floatDeco {
            0%   { transform: translate(0, 0) rotate(0deg); }
            50%  { transform: translate(15px, -10px) rotate(3deg); }
            100% { transform: translate(-10px, 15px) rotate(-3deg); }
        }

        /* ============ KARTU LOGIN UTAMA ============ */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 960px;
            min-height: 560px;
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 30px 80px -20px rgba(10, 25, 47, 0.22), 0 0 0 1px rgba(148, 163, 184, 0.08);
            display: flex;
            overflow: hidden;
            animation: cardEnter 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: scale(0.96) translateY(16px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ============ PANEL KIRI: CURVED ARTWORK ============ */
        .panel-left {
            position: relative;
            width: 52%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Background gelap panel kiri */
        .panel-left-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, #0a192f 0%, #0f2b52 30%, #1e40af 65%, #0891b2 100%);
            z-index: 1;
        }

        /* Kurva besar mengambang — inspirasi dari desain referensi (gambar 2) */
        .curve-main {
            position: absolute;
            z-index: 2;
            width: 650px;
            height: 650px;
            border-radius: 50%;
            right: -200px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(160deg, #06b6d4 0%, #1e40af 40%, #0a192f 100%);
            animation: curvePulse 10s ease-in-out infinite alternate;
        }
        .curve-inner {
            position: absolute;
            z-index: 3;
            width: 430px;
            height: 430px;
            border-radius: 50%;
            right: -80px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(160deg, #0891b2 0%, #1e3a5f 60%, #0f2b52 100%);
            animation: curvePulse 8s ease-in-out infinite alternate-reverse;
        }

        @keyframes curvePulse {
            0%   { transform: translateY(-50%) scale(1); }
            100% { transform: translateY(-50%) scale(1.03); }
        }

        /* Lingkaran putih di tengah panel kiri — berisi logo & info */
        .badge-circle {
            position: relative;
            z-index: 10;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 28px;
            transition: transform 0.3s ease;
        }
        .badge-circle:hover { transform: scale(1.03); }

        .badge-logo-wrap {
            display: inline-flex;
            align-items: center;
            padding: 8px 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2647 50%, #162d4a 100%);
            box-shadow: 0 4px 18px rgba(15, 38, 71, 0.28);
            margin-bottom: 14px;
            transition: transform 0.25s ease;
        }
        .badge-logo-wrap:hover { transform: scale(1.05); }
        .badge-logo-wrap img { height: 40px; width: auto; max-width: 180px; object-fit: contain; }

        .badge-title {
            font-family: 'Outfit', system-ui, sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: #0f172a;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }
        .badge-subtitle {
            font-family: 'Outfit', system-ui, sans-serif;
            font-weight: 700;
            font-size: 10.5px;
            color: #0891b2;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            margin-top: 4px;
        }
        .badge-desc {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 10px;
            max-width: 200px;
        }

        /* Elemen dekoratif: lingkaran outline tipis */
        .deco-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px dashed rgba(6, 182, 212, 0.15);
            pointer-events: none;
            z-index: 4;
        }
        .deco-ring-1 {
            width: 340px; height: 340px;
            left: -60px; top: 50%;
            transform: translateY(-50%);
            animation: ringRotate 30s linear infinite;
        }
        .deco-ring-2 {
            width: 500px; height: 500px;
            left: -130px; top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 1px;
            border-color: rgba(6, 182, 212, 0.08);
            animation: ringRotate 50s linear infinite reverse;
        }

        @keyframes ringRotate {
            from { transform: translateY(-50%) rotate(0deg); }
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Partikel dot kecil floating */
        .floating-dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(6, 182, 212, 0.4);
            z-index: 5;
            animation: dotFloat 6s ease-in-out infinite alternate;
        }
        .floating-dot:nth-child(1) { top: 15%; left: 20%; animation-delay: 0s; }
        .floating-dot:nth-child(2) { top: 75%; left: 15%; animation-delay: 1.5s; width: 4px; height: 4px; background: rgba(30, 64, 175, 0.3); }
        .floating-dot:nth-child(3) { top: 25%; left: 65%; animation-delay: 3s; width: 5px; height: 5px; background: rgba(6, 182, 212, 0.3); }
        .floating-dot:nth-child(4) { top: 80%; left: 60%; animation-delay: 2s; width: 3px; height: 3px; }

        @keyframes dotFloat {
            0%   { transform: translate(0, 0); opacity: 0.4; }
            50%  { transform: translate(8px, -12px); opacity: 0.8; }
            100% { transform: translate(-5px, 6px); opacity: 0.3; }
        }

        /* ============ PANEL KANAN: FORM LOGIN ============ */
        .panel-right {
            width: 48%;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .form-section {
            width: 100%;
            max-width: 300px;
            margin: auto;
        }

        /* Avatar lingkaran */
        .avatar-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2.5px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            position: relative;
            background: #f8fafc;
            overflow: hidden;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .avatar-ring:hover {
            border-color: #0891b2;
            box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
        }
        .avatar-ring svg { width: 100%; height: 100%; color: #94a3b8; padding-top: 8px; }

        .welcome-title {
            font-family: 'Outfit', system-ui, sans-serif;
            font-weight: 800;
            font-size: 26px;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-top: 4px;
        }
        .welcome-sub {
            font-size: 12.5px;
            font-weight: 500;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Error box */
        .error-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 16px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 12px;
            text-align: left;
            margin-bottom: 16px;
        }
        .error-box i { margin-top: 2px; color: #ef4444; flex-shrink: 0; }

        /* Input fields */
        .input-group {
            position: relative;
            margin-bottom: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 14px 44px 14px 20px;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            background: #f8fafc;
            font-size: 13.5px;
            font-weight: 500;
            color: #1e293b;
            outline: none;
            transition: all 0.25s ease;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .input-group input::placeholder { color: #94a3b8; font-weight: 400; }
        .input-group input:focus {
            background: #ffffff;
            border-color: #0891b2;
            box-shadow: 0 0 0 4px rgba(8, 145, 178, 0.1);
        }
        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s ease;
        }
        .input-group input:focus ~ .input-icon { color: #0891b2; }
        .input-icon-btn {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s ease;
        }
        .input-icon-btn:hover { color: #0891b2; }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 4px;
            margin-bottom: 18px;
        }
        .remember-row input[type="checkbox"] {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            accent-color: #0891b2;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        /* Tombol Login */
        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #0e4d92 0%, #1e40af 40%, #0891b2 100%);
            color: #ffffff;
            font-family: 'Outfit', system-ui, sans-serif;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px -4px rgba(14, 77, 146, 0.35);
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:hover {
            box-shadow: 0 12px 32px -4px rgba(14, 77, 146, 0.45);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0) scale(0.99); }
        .btn-login:disabled {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none;
        }

        /* Help links */
        .help-text {
            margin-top: 18px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        .help-text a {
            color: #0891b2;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .help-text a:hover { color: #0e4d92; text-decoration: underline; }

        /* Footer */
        .login-footer {
            width: 100%;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
        }

        /* Spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.7s linear infinite; }

        /* ============ RESPONSIF ============ */
        @media (max-width: 768px) {
            body { padding: 12px; align-items: flex-start; padding-top: 40px; }

            .login-card {
                flex-direction: column;
                max-width: 420px;
                min-height: auto;
                border-radius: 24px;
            }
            .panel-left {
                width: 100%;
                height: 280px;
                min-height: 260px;
            }
            .curve-main {
                width: 500px; height: 500px;
                right: -160px;
            }
            .curve-inner {
                width: 330px; height: 330px;
                right: -60px;
            }
            .badge-circle {
                width: 200px; height: 200px;
                padding: 20px;
            }
            .badge-logo-wrap { padding: 6px 14px; margin-bottom: 10px; }
            .badge-logo-wrap img { height: 28px; }
            .badge-title { font-size: 13px; }
            .badge-subtitle { font-size: 9px; }
            .badge-desc { display: none; }
            .deco-ring-1 { width: 250px; height: 250px; left: -40px; }
            .deco-ring-2 { width: 380px; height: 380px; left: -90px; }

            .panel-right {
                width: 100%;
                padding: 32px 28px;
            }
            .welcome-title { font-size: 22px; }
        }

        @media (max-width: 480px) {
            .panel-left { height: 240px; min-height: 220px; }
            .badge-circle { width: 170px; height: 170px; padding: 16px; }
            .badge-logo-wrap img { height: 24px; }
            .badge-title { font-size: 12px; }
            .panel-right { padding: 24px 20px; }
        }
    </style>
</head>
<body>

    <!-- Bentuk dekoratif di background halaman -->
    <div class="bg-deco bg-deco-1"></div>
    <div class="bg-deco bg-deco-2"></div>
    <div class="bg-deco bg-deco-3"></div>

    <!-- ================= KARTU MODAL LOGIN ================= -->
    <div class="login-card">

        <!-- ============ PANEL KIRI: ARTWORK KURVA BESAR ============ -->
        <div class="panel-left">
            <!-- Background gradasi biru -->
            <div class="panel-left-bg"></div>

            <!-- Kurva besar (seperti di gambar 2) -->
            <div class="curve-main"></div>
            <div class="curve-inner"></div>

            <!-- Lingkaran dekoratif -->
            <div class="deco-ring deco-ring-1"></div>
            <div class="deco-ring deco-ring-2"></div>

            <!-- Partikel floating -->
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>

            <!-- BADGE LINGKARAN PUTIH: Logo & Info -->
            <div class="badge-circle">
                <div class="badge-logo-wrap">
                    <img src="{{ asset('img/logo-white.png') }}" alt="Logo Fast Connect">
                </div>
                <h3 class="badge-title">Connecti Jelajah Priangan</h3>
                <p class="badge-subtitle">Integrated Management System</p>
                <p class="badge-desc">Kelola jaringan, pendaftaran, & pemantauan pelanggan dalam satu dasbor terpadu.</p>
            </div>
        </div>

        <!-- ============ PANEL KANAN: FORM MASUK ============ -->
        <div class="panel-right">
            <div class="form-section">
                <!-- Avatar & Judul -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <div class="avatar-ring">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h2 class="welcome-title">Selamat Datang</h2>
                    <p class="welcome-sub">Masuk untuk mengakses dasbor IMS</p>
                </div>

                <!-- Pesan Error Validasi -->
                @if ($errors->any())
                    <div class="error-box">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf

                    <!-- Input Username -->
                    <div class="input-group">
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" autofocus
                               placeholder="Username">
                        <i class="fa-regular fa-user input-icon"></i>
                    </div>

                    <!-- Input Password -->
                    <div class="input-group">
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               placeholder="Password">
                        <button type="button" onclick="togglePass()" aria-label="Lihat password" class="input-icon-btn">
                            <i id="eyeIcon" class="fa-solid fa-lock"></i>
                        </button>
                    </div>

                    <!-- Ingat Saya -->
                    <div class="remember-row">
                        <input id="remember" type="checkbox">
                        <label for="remember">Ingat saya</label>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" id="submitBtn" class="btn-login">
                        <span id="btnLabel">LOGIN</span>
                        <span id="btnSpinner" style="display:none; align-items:center; justify-content:center; gap:8px;">
                            <i class="fa-solid fa-circle-notch spin"></i> Memverifikasi…
                        </span>
                    </button>
                </form>

                <!-- Bantuan -->
                <div class="help-text">
                    <p>Kendala akses? <a href="#">Hubungi Administrator</a></p>
                </div>
            </div>

            <!-- Footer Hak Cipta -->
            <div class="login-footer">
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
                i.className = 'fa-regular fa-eye';
            } else {
                p.type = 'password';
                i.className = 'fa-solid fa-lock';
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
                document.getElementById('btnLabel').style.display = 'none';
                document.getElementById('btnSpinner').style.display = 'flex';
            });
        })();
    </script>
</body>
</html>
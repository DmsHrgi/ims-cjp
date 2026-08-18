<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | Connecti Jelajah Priangan</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        html {
            zoom: 90%;
            -moz-transform: scale(0.9);
            -moz-transform-origin: 0 0;
        }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
        }

        /* Hide datalist dropdown indicator arrow ONLY for inputs with list attribute */
        input[list]::-webkit-calendar-picker-indicator,
        input[list]::-webkit-list-button {
            display: none !important;
            -webkit-appearance: none !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Ensure input[type="date"] calendar picker is visible and clickable */
        input[type="date"]::-webkit-calendar-picker-indicator {
            display: block !important;
            opacity: 1 !important;
            width: auto !important;
            height: auto !important;
            cursor: pointer;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Sidebar */
        #sidebar {
            width: 240px;
            transition: width 0.25s cubic-bezier(.4,0,.2,1), transform 0.25s cubic-bezier(.4,0,.2,1);
            will-change: width;
            overflow: hidden;
        }
        #sidebar.collapsed {
            width: 0;
        }

        /* Sidebar nav item active bar */
        .nav-item-active::before {
            content: '';
            position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: #3b82f6;
        }

        /* Smooth dropdown */
        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease;
        }
        .dropdown-menu.open {
            max-height: 300px;
        }

        /* Page fade-in */
        main { animation: fadeIn 0.2s ease both; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Input focus ring */
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        /* Auto uppercase preview for text inputs and textareas (except sharelock & permintaan_khusus) */
        input[type="text"]:not(.no-uppercase):not([name="sharelock"]):not([name="lon_lat"]), 
        input[type="search"]:not(.no-uppercase), 
        textarea:not(.no-uppercase):not([name="permintaan_khusus"]) {
            text-transform: uppercase;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#3b82f6', dark: '#1d4ed8', light: '#eff6ff' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f5f6fa] text-gray-800 antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- ═══════════ SIDEBAR ═══════════ -->
        <aside id="sidebar" class="flex flex-col bg-[#111827] text-white flex-shrink-0">
            <div class="w-[240px] flex flex-col h-full flex-shrink-0">
                @include('partials.sidebar')
            </div>
        </aside>

        <!-- ═══════════ MAIN WRAPPER ═══════════ -->
        <div class="flex flex-col flex-1 overflow-hidden min-w-0">

            <!-- Navbar -->
            @include('partials.navbar')

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('partials.footer')

        </div>
    </div>

    <!-- ═══════════ SCRIPTS ═══════════ -->
    <script>
        /* ── Sidebar Toggle & Modal Center Offset ── */
        function updateModalOffset() {
            const sb = document.getElementById('sidebar');
            const isCollapsed = sb && sb.classList.contains('collapsed');
            const isDesktop = window.innerWidth >= 768;
            document.querySelectorAll('.modal-center-wrapper').forEach(function(el) {
                if (isDesktop && sb && !isCollapsed) {
                    el.style.paddingLeft = '240px';
                    el.style.paddingRight = '0px';
                } else {
                    el.style.paddingLeft = '0px';
                    el.style.paddingRight = '0px';
                }
            });
        }

        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            if (!sb) return;
            sb.classList.toggle('collapsed');
            localStorage.setItem('sb', sb.classList.contains('collapsed') ? '1' : '0');
            updateModalOffset();
        }
        window.toggleSidebar = toggleSidebar;

        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('sb') === '1') {
                const sb = document.getElementById('sidebar');
                if (sb) sb.classList.add('collapsed');
            }
            updateModalOffset();
            window.addEventListener('resize', updateModalOffset);

            /* ── Dropdown menus ── */
            document.querySelectorAll('.dropdown-toggle').forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const menu = this.parentElement.querySelector('.dropdown-menu');
                    const icon = this.querySelector('.dd-chevron');
                    if (!menu) return;
                    const isOpen = menu.classList.contains('open');

                    // close others
                    document.querySelectorAll('.dropdown-menu.open').forEach(function (m) {
                        m.classList.remove('open');
                    });
                    document.querySelectorAll('.dd-chevron').forEach(function (c) {
                        c.style.transform = 'rotate(0deg)';
                    });

                    if (!isOpen) {
                        menu.classList.add('open');
                        if (icon) icon.style.transform = 'rotate(180deg)';
                    }
                });
            });

            // auto-open active dropdown
            const activeInDropdown = document.querySelector('.dropdown-menu .dd-active');
            if (activeInDropdown) {
                const menu = activeInDropdown.closest('.dropdown-menu');
                const icon = menu?.previousElementSibling?.querySelector('.dd-chevron');
                if (menu) {
                    menu.classList.add('open');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            }

            /* ── Auto Uppercase for inputs & textareas ── */
            document.addEventListener('input', function (e) {
                const el = e.target;
                if (!el) return;
                if (el.tagName === 'TEXTAREA' || (el.tagName === 'INPUT' && (el.type === 'text' || el.type === 'search'))) {
                    const name = (el.name || '').toLowerCase();
                    const isExcluded = el.classList.contains('no-uppercase') || name === 'sharelock' || name === 'permintaan_khusus' || name === 'lon_lat';
                    if (!isExcluded && !el.readOnly && !el.disabled) {
                        const start = el.selectionStart;
                        const end = el.selectionEnd;
                        const oldVal = el.value;
                        const newVal = oldVal.toUpperCase();
                        if (oldVal !== newVal) {
                            el.value = newVal;
                            if (start !== null && end !== null) {
                                el.setSelectionRange(start, end);
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>
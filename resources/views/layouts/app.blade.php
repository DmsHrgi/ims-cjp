<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) && $title !== 'Dashboard' ? $title . ' - IMS | Connecti Jelajah Priangan' : 'IMS | Connecti Jelajah Priangan' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}?v={{ file_exists(public_path('img/logo.png')) ? filemtime(public_path('img/logo.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v={{ file_exists(public_path('img/logo.png')) ? filemtime(public_path('img/logo.png')) : time() }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Immediate script to prevent flash of expanded sidebar on page load -->
    <script>
        if (localStorage.getItem('sb') === '1') {
            document.documentElement.classList.add('sb-collapsed');
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        html {
            font-size: 90%;
            height: 100%;
        }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            min-height: 100%;
            margin: 0;
            padding: 0;
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
            width: 250px;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 40;
        }
        #sidebar.collapsed,
        html.sb-collapsed #sidebar {
            width: 76px;
        }
        #sidebar.collapsed,
        html.sb-collapsed #sidebar,
        #sidebar.collapsed #sidebar-nav,
        html.sb-collapsed #sidebar-nav {
            overflow: visible !important;
        }

        /* Collapsed behavior */
        #sidebar.collapsed .sidebar-hide-collapsed,
        html.sb-collapsed #sidebar .sidebar-hide-collapsed {
            display: none !important;
        }
        #sidebar .sidebar-show-collapsed {
            display: none !important;
        }
        #sidebar.collapsed .sidebar-show-collapsed,
        html.sb-collapsed #sidebar .sidebar-show-collapsed {
            display: flex !important;
        }

        /* Item layout when collapsed */
        #sidebar.collapsed .sidebar-nav-item,
        html.sb-collapsed #sidebar .sidebar-nav-item {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: 46px !important;
            height: 46px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            border-radius: 14px !important;
            position: relative;
        }
        #sidebar.collapsed .sidebar-nav-item .icon-wrapper,
        html.sb-collapsed #sidebar .sidebar-nav-item .icon-wrapper {
            width: 100% !important;
            height: 100% !important;
            background: transparent !important;
            border-radius: 14px !important;
        }

        /* Active highlight for collapsed & expanded */
        .sidebar-active-item {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.4) !important;
        }

        /* Floating Tooltips and Flyouts (hidden by default in expanded mode) */
        .sidebar-tooltip, .sidebar-flyout {
            display: none !important;
        }

        /* Floating Tooltips when collapsed */
        #sidebar.collapsed .sidebar-tooltip {
            display: block !important;
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%) scale(0.9);
            background: #1e293b;
            color: #f8fafc;
            padding: 7px 13px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.12);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease, transform 0.15s ease;
            z-index: 99999;
        }
        #sidebar.collapsed .sidebar-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
        }
        #sidebar.collapsed .group:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateY(-50%) scale(1);
            pointer-events: auto;
        }
        #sidebar.collapsed .group:hover .sidebar-flyout ~ .sidebar-tooltip,
        #sidebar.collapsed .group.flyout-open .sidebar-tooltip {
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* Collapsed Flyout Submenu for Permintaan / Billing */
        #sidebar.collapsed .sidebar-flyout {
            position: absolute;
            left: calc(100% + 12px);
            top: 0;
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            padding: 8px;
            min-width: 190px;
            display: none !important;
            z-index: 99999;
            pointer-events: auto;
        }
        #sidebar.collapsed .sidebar-flyout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -18px;
            width: 18px;
            height: 100%;
        }
        #sidebar.collapsed .group:hover .sidebar-flyout,
        #sidebar.collapsed .group.flyout-open .sidebar-flyout {
            display: block !important;
            animation: flyoutFadeIn 0.15s ease-out;
        }
        @keyframes flyoutFadeIn {
            from { opacity: 0; transform: translateX(-6px); }
            to { opacity: 1; transform: translateX(0); }
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

        /* Auto uppercase preview for text inputs and textareas (except sharelock, lon_lat, hostname, snmp_community, telnet, etc) */
        input[type="text"]:not(.no-uppercase):not([name="sharelock"]):not([name="lon_lat"]):not([name="hostname"]):not([name="snmp_community"]):not([name="telnet_username"]):not([name="telnet_password"]), 
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
            <script>
                if (localStorage.getItem('sb') === '1') {
                    document.getElementById('sidebar').classList.add('collapsed');
                }
            </script>
            @include('partials.sidebar')
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
                if (isDesktop && sb) {
                    el.style.paddingLeft = isCollapsed ? '76px' : '250px';
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
            const isCol = sb.classList.contains('collapsed');
            if (isCol) {
                document.documentElement.classList.add('sb-collapsed');
            } else {
                document.documentElement.classList.remove('sb-collapsed');
            }
            localStorage.setItem('sb', isCol ? '1' : '0');
            updateModalOffset();
        }
        window.toggleSidebar = toggleSidebar;

        document.addEventListener('DOMContentLoaded', function () {
            const sb = document.getElementById('sidebar');
            if (localStorage.getItem('sb') === '1') {
                if (sb) sb.classList.add('collapsed');
                document.documentElement.classList.add('sb-collapsed');
            } else {
                if (sb) sb.classList.remove('collapsed');
                document.documentElement.classList.remove('sb-collapsed');
            }
            updateModalOffset();
            window.addEventListener('resize', updateModalOffset);

            /* ── Dropdown menus (Expanded accordion + Collapsed flyout) ── */
            document.querySelectorAll('.dropdown-toggle').forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const sb = document.getElementById('sidebar');
                    const isCollapsed = sb && sb.classList.contains('collapsed');
                    const parentGroup = this.closest('.group');

                    if (isCollapsed) {
                        // Toggle flyout on click in collapsed mode
                        const isOpen = parentGroup && parentGroup.classList.contains('flyout-open');
                        document.querySelectorAll('.group.flyout-open').forEach(function (g) {
                            g.classList.remove('flyout-open');
                        });
                        if (!isOpen && parentGroup) {
                            parentGroup.classList.add('flyout-open');
                        }
                    } else {
                        // Toggle accordion in expanded mode
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
                    }
                });
            });

            // Close flyouts when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.group')) {
                    document.querySelectorAll('.group.flyout-open').forEach(function (g) {
                        g.classList.remove('flyout-open');
                    });
                }
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
                    const isExcluded = el.classList.contains('no-uppercase') 
                        || name === 'sharelock' 
                        || name === 'permintaan_khusus' 
                        || name === 'lon_lat'
                        || name === 'hostname'
                        || name === 'snmp_community'
                        || name === 'telnet_username'
                        || name === 'telnet_password';
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
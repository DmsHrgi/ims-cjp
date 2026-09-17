@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $isPermintaan = str_starts_with($currentRoute, 'permintaan');

    $u = session('user', []);
    $userLevel = strtoupper($u['level'] ?? '');
    $kodeLevel = $u['kode_level'] ?? '';
    $levelNum  = $u['level_num'] ?? null;

    // Cek role Admin, NOC & Finance
    $isAdmin   = ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');
    $isNoc     = !$isAdmin && ($userLevel === 'NOC' || $kodeLevel === 'lv68132');
    $isFinance = !$isAdmin && ($userLevel === 'FINANCE' || $kodeLevel === 'lv33501' || $levelNum == 6 || str_contains($userLevel, 'FINANCE') || str_contains($userLevel, 'KEUANGAN') || str_contains($userLevel, 'KASIR'));

    $allNavItems = [
        ['route' => 'dashboard',   'icon' => 'fa-gauge-high',    'label' => 'Dashboard'],
        ['route' => 'tiket',       'icon' => 'fa-ticket',         'label' => 'Tiket'],
        ['route' => 'pendaftaran', 'icon' => 'fa-user-plus',      'label' => 'Registrasi'],
        ['route' => 'pelanggan',   'icon' => 'fa-users',          'label' => 'Pelanggan'],
    ];

    if ($isAdmin) {
        $allNavItems[] = ['route' => 'olt.index',   'icon' => 'fa-server',    'label' => 'OLT Device'];
        $allNavItems[] = ['route' => 'users.index', 'icon' => 'fa-user-gear', 'label' => 'Manajemen User'];
    }

    $navItems = array_values(array_filter($allNavItems, function($item) use ($isNoc) {
        if ($isNoc && $item['route'] === 'pendaftaran') {
            return false;
        }
        return true;
    }));

    $namaKaryawan = $u['nama_karyawan'] ?? ($u['username'] ?? 'User');
    $initials = strtoupper(substr($namaKaryawan, 0, 1));
    $roleLabel = $u['level'] ?? 'Staff';
@endphp

<div class="flex flex-col h-full w-full">
    {{-- ────────────────────────────────
         BRAND / LOGO
    ──────────────────────────────── --}}
    {{-- Expanded Logo --}}
    <div class="flex-shrink-0 px-4 pt-5 pb-4 sidebar-hide-collapsed">
        <div class="relative rounded-2xl overflow-hidden"
             style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2647 50%, #162d4a 100%); box-shadow: 0 4px 24px rgba(59,130,246,0.18), inset 0 1px 0 rgba(255,255,255,0.07);">
            <div class="absolute inset-0 opacity-20"
                 style="background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%); pointer-events:none;"></div>
            <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #60a5fa, transparent 70%);"></div>
            <div class="absolute -bottom-3 -left-3 w-14 h-14 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #3b82f6, transparent 70%);"></div>

            <div class="relative flex items-center justify-center px-4 py-2.5">
                <img src="{{ asset('img/logo-white.png') }}"
                     alt="Logo Connecti Jelajah"
                     class="h-11 w-auto max-w-full object-contain filter drop-shadow-md transition-transform duration-200 hover:scale-105">
            </div>
        </div>
        <div class="mt-4 h-px" style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.08), transparent);"></div>
    </div>

    {{-- Collapsed Mini Logo --}}
    <div class="flex-shrink-0 pt-5 pb-3 justify-center items-center sidebar-show-collapsed">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center relative overflow-hidden transition-all duration-200 hover:scale-105"
             style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2647 50%, #162d4a 100%); box-shadow: 0 4px 16px rgba(59,130,246,0.25), inset 0 1px 0 rgba(255,255,255,0.15);">
            <img src="{{ asset('img/logo-icon-white.png') }}"
                 alt="Logo Icon"
                 class="h-7 w-7 object-contain filter drop-shadow-sm"
                 onerror="this.onerror=null; this.src='{{ asset('img/logo-icon.png') }}';">
        </div>
    </div>

    {{-- ────────────────────────────────
         NAVIGATION
    ──────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-3 space-y-1" id="sidebar-nav">

        {{-- Section label --}}
        <div class="sidebar-hide-collapsed px-2 pt-2 mb-2">
            <p class="text-[9px] font-bold tracking-[0.15em] uppercase text-gray-500 select-none">Menu</p>
        </div>

        {{-- Regular items --}}
        @foreach ($navItems as $item)
            @php
                $active = $currentRoute === $item['route'] 
                    || ($item['route'] === 'olt.index' && str_starts_with($currentRoute, 'olt.'))
                    || ($item['route'] === 'users.index' && str_starts_with($currentRoute, 'users.'));
            @endphp
            <div class="relative group flex justify-center">
                <a href="{{ route($item['route']) }}"
                   class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150 w-full
                          {{ $active
                             ? 'sidebar-active-item'
                             : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="icon-wrapper w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                                 {{ $active ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                        <i class="fa-solid {{ $item['icon'] }} text-[13px] {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    </span>
                    <span class="truncate sidebar-hide-collapsed">{{ $item['label'] }}</span>
                </a>

                {{-- Tooltip for collapsed mode --}}
                <div class="sidebar-tooltip">
                    {{ $item['label'] }}
                </div>
            </div>
        @endforeach

        {{-- Permintaan dropdown (Hanya untuk NOC) --}}
        @if ($isNoc)
        <div class="relative group flex flex-col items-center">
            <button class="dropdown-toggle sidebar-nav-item w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150
                           {{ $isPermintaan ? 'sidebar-active-item' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <span class="icon-wrapper w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                                 {{ $isPermintaan ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                        <i class="fa-solid fa-clipboard-list text-[13px] {{ $isPermintaan ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    </span>
                    <span class="sidebar-hide-collapsed">Permintaan</span>
                </span>
                <i class="fa-solid fa-chevron-down dd-chevron text-[9px] transition-transform duration-200 sidebar-hide-collapsed {{ $isPermintaan ? 'text-white/70' : 'text-gray-500' }}"></i>
            </button>

            {{-- Expanded accordion submenu --}}
            <ul class="dropdown-menu w-full mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5 sidebar-hide-collapsed {{ $isPermintaan ? 'open' : '' }}">
                @php
                    $subItems = [
                        ['route' => 'permintaan.up-downgrade', 'label' => 'UP / Downgrade'],
                        ['route' => 'permintaan.terminasi',    'label' => 'Terminasi'],
                        ['route' => 'permintaan.suspend',      'label' => 'Suspend'],
                    ];
                @endphp
                @foreach ($subItems as $sub)
                    @php $subActive = $currentRoute === $sub['route']; @endphp
                    <li>
                        <a href="{{ route($sub['route']) }}"
                           class="{{ $subActive ? 'dd-active text-blue-300 bg-white/5 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }} flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $subActive ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                            {{ $sub['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Collapsed Flyout Menu --}}
            <div class="sidebar-flyout">
                <div class="px-2.5 py-1.5 border-b border-white/10 mb-1 text-[11px] font-bold text-gray-300 uppercase tracking-wider">
                    Permintaan
                </div>
                <div class="space-y-0.5">
                    @foreach ($subItems as $sub)
                        @php $subActive = $currentRoute === $sub['route']; @endphp
                        <a href="{{ route($sub['route']) }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all
                                  {{ $subActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $subActive ? 'bg-white' : 'bg-blue-400' }}"></span>
                            {{ $sub['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Billing dropdown (Hanya untuk Finance) --}}
        @if ($isFinance)
        @php
            $isBilling = str_starts_with($currentRoute, 'billing');
            $billingItems = [
                ['route' => 'billing.registrasi', 'label' => 'Invoice Registrasi'],
                ['route' => 'billing.layanan',    'label' => 'Invoice Layanan'],
            ];
        @endphp
        <div class="relative group flex flex-col items-center">
            <button class="dropdown-toggle sidebar-nav-item w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150
                           {{ $isBilling ? 'sidebar-active-item' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <span class="icon-wrapper w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                                 {{ $isBilling ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                        <i class="fa-solid fa-coins text-[13px] {{ $isBilling ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    </span>
                    <span class="sidebar-hide-collapsed">Billing</span>
                </span>
                <i class="fa-solid fa-chevron-down dd-chevron text-[9px] transition-transform duration-200 sidebar-hide-collapsed {{ $isBilling ? 'text-white/70' : 'text-gray-500' }}"></i>
            </button>

            {{-- Expanded accordion submenu --}}
            <ul class="dropdown-menu w-full mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5 sidebar-hide-collapsed {{ $isBilling ? 'open' : '' }}">
                @foreach ($billingItems as $bItem)
                    @php $bActive = $currentRoute === $bItem['route']; @endphp
                    <li>
                        <a href="{{ route($bItem['route']) }}"
                           class="{{ $bActive ? 'dd-active text-blue-300 bg-white/5 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }} flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $bActive ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                            {{ $bItem['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Collapsed Flyout Menu --}}
            <div class="sidebar-flyout">
                <div class="px-2.5 py-1.5 border-b border-white/10 mb-1 text-[11px] font-bold text-gray-300 uppercase tracking-wider">
                    Billing
                </div>
                <div class="space-y-0.5">
                    @foreach ($billingItems as $bItem)
                        @php $bActive = $currentRoute === $bItem['route']; @endphp
                        <a href="{{ route($bItem['route']) }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all
                                  {{ $bActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $bActive ? 'bg-white' : 'bg-blue-400' }}"></span>
                            {{ $bItem['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </nav>

    {{-- ────────────────────────────────
         USER PROFILE + LOGOUT
    ──────────────────────────────── --}}
    <div class="px-3 pt-3 pb-4 border-t border-white/10 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="relative group flex justify-center">
                <button type="submit"
                        class="sidebar-nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all duration-150 cursor-pointer">
                    <span class="icon-wrapper w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/5 group-hover:bg-red-500/15 transition-all">
                        <i class="fa-solid fa-right-from-bracket text-[13px]"></i>
                    </span>
                    <span class="sidebar-hide-collapsed">Keluar</span>
                </button>

                {{-- Tooltip for collapsed mode --}}
                <div class="sidebar-tooltip text-red-300">
                    Keluar
                </div>
            </div>
        </form>
    </div>
</div>
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

    $navItems = array_values(array_filter($allNavItems, function($item) use ($isNoc, $isFinance) {
        if ($isNoc && $item['route'] === 'pendaftaran') {
            return false;
        }
        if ($isFinance && $item['route'] === 'pendaftaran') {
            return false;
        }
        return true;
    }));

    $namaKaryawan = $u['nama_karyawan'] ?? ($u['username'] ?? 'User');
    $initials = strtoupper(substr($namaKaryawan, 0, 1));
    $roleLabel = $u['level'] ?? 'Staff';
@endphp

{{-- ────────────────────────────────
     BRAND / LOGO
──────────────────────────────── --}}
<div class="flex-shrink-0 px-4 pt-5 pb-4">
    {{-- Logo card with gradient glow --}}
    <div class="relative rounded-2xl overflow-hidden"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2647 50%, #162d4a 100%); box-shadow: 0 4px 24px rgba(59,130,246,0.18), inset 0 1px 0 rgba(255,255,255,0.07);">

        {{-- Shine overlay --}}
        <div class="absolute inset-0 opacity-20"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%); pointer-events:none;"></div>

        {{-- Decorative blobs --}}
        <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
             style="background: radial-gradient(circle, #60a5fa, transparent 70%);"></div>
        <div class="absolute -bottom-3 -left-3 w-14 h-14 rounded-full opacity-10"
             style="background: radial-gradient(circle, #3b82f6, transparent 70%);"></div>

        {{-- Content --}}
        <div class="relative flex items-center gap-3 px-3.5 py-3">
            {{-- Logo bubble --}}
            <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                 style="background: rgba(255,255,255,0.97); box-shadow: 0 2px 12px rgba(0,0,0,0.25);">
                <img src="{{ asset('img/logo.png') }}"
                     alt="Logo Connecti Jelajah"
                     class="w-9 h-9 object-contain">
            </div>

            {{-- Brand text --}}
            <div class="leading-tight min-w-0">
                <p class="text-white font-extrabold text-[12.5px] tracking-wide truncate drop-shadow-sm">
                    CONNECTI JELAJAH
                </p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    <p class="text-cyan-300 text-[9.5px] font-semibold tracking-widest uppercase">
                        Koneksi Cepat
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Subtle separator --}}
    <div class="mt-4 h-px" style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.08), transparent);"></div>
</div>


{{-- ────────────────────────────────
     NAVIGATION
──────────────────────────────── --}}
<nav class="flex-1 overflow-y-auto px-3 pb-3 space-y-0" id="sidebar-nav">

    {{-- Section label --}}
    <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-500 select-none">Menu</p>

    {{-- Regular items --}}
    @foreach ($navItems as $item)
        @php $active = $currentRoute === $item['route']; @endphp
        <a href="{{ route($item['route']) }}"
           class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150 mb-0.5
                  {{ $active
                     ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25'
                     : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                         {{ $active ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                <i class="fa-solid {{ $item['icon'] }} text-[12px] {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
            </span>
            <span class="truncate">{{ $item['label'] }}</span>
        </a>
    @endforeach

    {{-- Permintaan dropdown (Hanya untuk NOC) --}}
    @if ($isNoc)
    <div class="mb-0.5">
        <button class="dropdown-toggle w-full group relative flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150
                       {{ $isPermintaan ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <span class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                             {{ $isPermintaan ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                    <i class="fa-solid fa-clipboard-list text-[12px] {{ $isPermintaan ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                </span>
                Permintaan
            </span>
            <i class="fa-solid fa-chevron-down dd-chevron text-[9px] transition-transform duration-200 {{ $isPermintaan ? 'text-white/70' : 'text-gray-500' }}"></i>
        </button>

        <ul class="dropdown-menu mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
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
                       class="dd-{{ $subActive ? 'active ' : '' }}flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all
                              {{ $subActive ? 'text-blue-300 bg-white/5' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $subActive ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                        {{ $sub['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Billing dropdown (Hanya untuk Finance) --}}
    @if ($isFinance)
    @php
        $isBilling = str_starts_with($currentRoute, 'billing');
    @endphp
    <div class="mb-0.5">
        <button class="dropdown-toggle w-full group relative flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150
                       {{ $isBilling ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <span class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all
                             {{ $isBilling ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }}">
                    <i class="fa-solid fa-coins text-[12px] {{ $isBilling ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                </span>
                Billing
            </span>
            <i class="fa-solid fa-chevron-down dd-chevron text-[9px] transition-transform duration-200 {{ $isBilling ? 'text-white/70' : 'text-gray-500' }}"></i>
        </button>

        <ul class="dropdown-menu mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5 {{ $isBilling ? 'open' : '' }}">
            @php
                $billingItems = [
                    ['route' => 'billing.registrasi', 'label' => 'Invoice Registrasi'],
                    ['route' => 'billing.layanan',    'label' => 'Invoice Layanan'],
                ];
            @endphp
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
    </div>
    @endif

</nav>

{{-- ────────────────────────────────
     USER PROFILE + LOGOUT
──────────────────────────────── --}}
<div class="px-3 pt-3 pb-4 border-t border-white/10 flex-shrink-0">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all duration-150 group">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/5 group-hover:bg-red-500/10 transition-all">
                <i class="fa-solid fa-right-from-bracket text-[12px]"></i>
            </span>
            Keluar
        </button>
    </form>
</div>
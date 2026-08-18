@php
    $u        = session('user', []);
    $uname    = $u['username'] ?? '';
    $rawNama  = $u['nama_karyawan'] ?? $u['nama'] ?? '';

    // Jika $rawNama kosong, mengandung '@', atau sama dengan username/email, cari nama_karyawan di DB
    if (empty($rawNama) || str_contains($rawNama, '@') || $rawNama === $uname) {
        if ($uname) {
            $userDb = \Illuminate\Support\Facades\DB::table('view_pengguna')
                ->where('username', $uname)
                ->first();
            if ($userDb && !empty($userDb->nama_karyawan)) {
                $rawNama = $userDb->nama_karyawan;
            }
        }
    }

    $nama    = strtoupper($rawNama ?: ($uname ?: 'PENGGUNA'));
    $level   = $u['level'] ?? null;
    $parts   = preg_split('/\s+/', trim($nama));
    $inisial = strtoupper(
        mb_substr($parts[0] ?? '', 0, 1) .
        mb_substr($parts[1] ?? $parts[0] ?? '', 0, 1)
    );
@endphp

<header class="h-14 bg-white border-b border-gray-200/70 flex items-center justify-between px-4 flex-shrink-0 z-30">

    {{-- Left: Hamburger --}}
    <button type="button" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-all cursor-pointer">
        <i class="fa-solid fa-bars text-base"></i>
    </button>

    {{-- Right: Clock + Profile --}}
    <div class="flex items-center gap-3">

        {{-- Realtime clock --}}
        <div class="hidden md:flex items-center gap-2 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-lg px-3 py-1.5">
            <i class="fa-regular fa-clock text-gray-400"></i>
            <span id="realtime-clock" class="font-medium tabular-nums">-</span>
        </div>

        {{-- Divider --}}
        <div class="hidden md:block h-5 w-px bg-gray-200"></div>

        {{-- Profile dropdown --}}
        <div class="relative" id="profileContainer">
            <button id="profileToggle" type="button"
                    class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition-all group focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                    {{ $inisial }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-semibold text-gray-700 leading-tight">{{ $nama }}</p>
                    @if($level)
                        <p class="text-[10px] text-gray-400 leading-tight">{{ $level }}</p>
                    @endif
                </div>
                <i id="profileChevron" class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 hidden md:block"></i>
            </button>

            {{-- Dropdown menu --}}
            <div id="profileMenu"
                 class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl border border-gray-100 shadow-lg shadow-gray-200/60 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/60">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ $inisial }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $nama }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ ($level ? $level.' · ' : '').'@'.$uname }}</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="submit" id="logoutBtn"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>

<script>
    /* ── Realtime Clock ── */
    (function() {
        var el = document.getElementById('realtime-clock');
        var days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        function tick() {
            if (!el) return;
            var d = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
            var H = String(d.getHours()).padStart(2,'0');
            var M = String(d.getMinutes()).padStart(2,'0');
            var S = String(d.getSeconds()).padStart(2,'0');
            el.textContent = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' · ' + H + ':' + M + ':' + S;
        }
        tick();
        setInterval(tick, 1000);
    })();

    /* ── Profile Dropdown ── */
    (function() {
        var toggle  = document.getElementById('profileToggle');
        var menu    = document.getElementById('profileMenu');
        var chevron = document.getElementById('profileChevron');
        var wrap    = document.getElementById('profileContainer');

        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', open);
            if (chevron) chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
        });

        document.addEventListener('click', function(e) {
            if (!wrap.contains(e.target)) {
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
    })();
</script>
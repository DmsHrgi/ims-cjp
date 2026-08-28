@extends('layouts.app')

@section('content')
    @php
        /** @var array $sections */
        /** @var \Illuminate\Support\Collection $wilayahList */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $customers */

        $u = session('user', []);
        $userLevel = strtoupper($u['level'] ?? '');
        $kodeLevel = $u['kode_level'] ?? '';
        $levelNum  = $u['level_num'] ?? null;
        $isAdmin   = ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');
        $isNoc     = !$isAdmin && ($userLevel === 'NOC' || $kodeLevel === 'lv68132');
        $isTeknik  = !$isAdmin && ($userLevel === 'TEKNIK' || $kodeLevel === 'lv9812' || $levelNum == 4 || str_contains($userLevel, 'TEKNIK'));
        $isFinance = !$isAdmin && ($userLevel === 'FINANCE' || $kodeLevel === 'lv33501' || $levelNum == 6 || str_contains($userLevel, 'FINANCE') || str_contains($userLevel, 'KEUANGAN') || str_contains($userLevel, 'KASIR'));
        
        $tones = [
            'aktif'     => [
                'badge' => 'bg-cyan-500',   
                'ring' => 'hover:ring-cyan-300 ring-cyan-400',   
                'num' => 'group-hover:text-cyan-600',   
                'bar' => 'bg-cyan-500',
                'active_card' => 'bg-cyan-50/70 border-cyan-400 ring-2 ring-cyan-400 shadow-md scale-[1.02]',
                'active_num' => 'text-cyan-700',
                'active_badge' => 'bg-cyan-600',
            ],
            'terminasi' => [
                'badge' => 'bg-rose-500',   
                'ring' => 'hover:ring-rose-300 ring-rose-400',   
                'num' => 'group-hover:text-rose-600',   
                'bar' => 'bg-rose-500',
                'active_card' => 'bg-rose-50/70 border-rose-400 ring-2 ring-rose-400 shadow-md scale-[1.02]',
                'active_num' => 'text-rose-700',
                'active_badge' => 'bg-rose-600',
            ],
            'suspend'   => [
                'badge' => 'bg-amber-500',  
                'ring' => 'hover:ring-amber-300 ring-amber-400',  
                'num' => 'group-hover:text-amber-600',  
                'bar' => 'bg-amber-500',
                'active_card' => 'bg-amber-50/70 border-amber-400 ring-2 ring-amber-400 shadow-md scale-[1.02]',
                'active_num' => 'text-amber-700',
                'active_badge' => 'bg-amber-600',
            ],
            'gagal'     => [
                'badge' => 'bg-teal-500',   
                'ring' => 'hover:ring-teal-300 ring-teal-400',   
                'num' => 'group-hover:text-teal-600',   
                'bar' => 'bg-teal-500',
                'active_card' => 'bg-teal-50/70 border-teal-400 ring-2 ring-teal-400 shadow-md scale-[1.02]',
                'active_num' => 'text-teal-700',
                'active_badge' => 'bg-teal-600',
            ],
        ];
    @endphp

    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); }
        .reveal { transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1); }
        .reveal.is-visible { opacity: 1; transform: none; }
        .pel-card { flex: 0 0 100%; }
        @media (min-width: 640px)  { .pel-card { flex: 0 0 calc((100% - 0.75rem) / 2); } }
        @media (min-width: 1024px) { .pel-card { flex: 0 0 calc((100% - 3.75rem) / 6); } }
        @media (prefers-reduced-motion: reduce) { .reveal { opacity:1 !important; transform:none !important; transition:none; } }
    </style>
    <script>document.documentElement.classList.add('js');</script>

    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-gray-600 font-medium">Pelanggan</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Data Pelanggan</h1>
                <p class="text-xs text-gray-500">Kelola dan pantau seluruh data pelanggan aktif, suspend, dan terminasi</p>
            </div>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
            <div>
                <p class="font-semibold">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5"></i>
            <div>
                <p class="font-semibold">Terjadi kesalahan:</p>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Status Selection Tabs (Aktif, Suspend, Terminasi, Semua) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @php
            $currentSec = request('section', request('status', ''));
        @endphp

        <!-- Tab 1: Semua Pelanggan -->
        @php $isAll = empty($currentSec) || $currentSec === 'semua'; @endphp
        <a href="{{ route('pelanggan', array_merge(request()->except('page', 'section', 'status'), ['section' => 'semua'])) }}"
           class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAll ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-blue-300 hover:shadow-sm' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAll ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:scale-105' }} transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $isAll ? 'text-blue-100' : 'text-gray-500' }}">Semua Pelanggan</p>
                    <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['semua'] ?? 0) }}</p>
                </div>
            </div>
            @if($isAll)
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
            @endif
        </a>

        <!-- Tab 2: Pelanggan Aktif -->
        @php $isAktif = $currentSec === 'aktif'; @endphp
        <a href="{{ route('pelanggan', array_merge(request()->except('page', 'section', 'status'), ['section' => 'aktif'])) }}"
           class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAktif ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-emerald-300 hover:shadow-sm' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAktif ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 group-hover:scale-105' }} transition-transform">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $isAktif ? 'text-emerald-100' : 'text-gray-500' }}">Pelanggan Aktif</p>
                    <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['aktif'] ?? 0) }}</p>
                </div>
            </div>
            @if($isAktif)
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
            @endif
        </a>

        <!-- Tab 3: Pelanggan Suspend -->
        @php $isSuspend = $currentSec === 'suspend'; @endphp
        <a href="{{ route('pelanggan', array_merge(request()->except('page', 'section', 'status'), ['section' => 'suspend'])) }}"
           class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isSuspend ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-amber-300 hover:shadow-sm' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isSuspend ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 group-hover:scale-105' }} transition-transform">
                    <i class="fa-solid fa-circle-pause"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $isSuspend ? 'text-amber-100' : 'text-gray-500' }}">Pelanggan Suspend</p>
                    <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['suspend'] ?? 0) }}</p>
                </div>
            </div>
            @if($isSuspend)
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
            @endif
        </a>

        <!-- Tab 4: Pelanggan Terminasi -->
        @php $isTerminasi = $currentSec === 'terminasi'; @endphp
        <a href="{{ route('pelanggan', array_merge(request()->except('page', 'section', 'status'), ['section' => 'terminasi'])) }}"
           class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isTerminasi ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-rose-300 hover:shadow-sm' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isTerminasi ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-600 group-hover:scale-105' }} transition-transform">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $isTerminasi ? 'text-rose-100' : 'text-gray-500' }}">Pelanggan Terminasi</p>
                    <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['terminasi'] ?? 0) }}</p>
                </div>
            </div>
            @if($isTerminasi)
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
            @endif
        </a>
    </div>

    <div>
    <div>
        <!-- Filter Bar Top (Matching Exact Layout) -->
        <form method="GET" action="{{ route('pelanggan') }}" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs mb-8 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                <!-- Row 1: Status Pelanggan, Search Nama, Wilayah, Alamat, Reset & Export -->
                <div class="lg:col-span-3">
                    <select name="section" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="" {{ empty(request('section')) || request('section') === 'semua' ? 'selected' : '' }}>SEMUA STATUS PELANGGAN</option>
                        <option value="aktif" {{ request('section') === 'aktif' ? 'selected' : '' }}>PELANGGAN AKTIF</option>
                        <option value="suspend" {{ request('section') === 'suspend' ? 'selected' : '' }}>PELANGGAN SUSPEND</option>
                        <option value="terminasi" {{ request('section') === 'terminasi' ? 'selected' : '' }}>PELANGGAN TERMINASI</option>
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="SEMUA NAMA / NOMOR LAYANAN" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none placeholder-gray-400">
                </div>

                <div class="lg:col-span-2">
                    <select name="wilayah" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="">SEMUA WILAYAH</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ strtoupper($w) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <input type="text" name="alamat" value="{{ request('alamat') }}" placeholder="SEMUA ALAMAT" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none placeholder-gray-400">
                </div>

                <div class="lg:col-span-2 flex items-center justify-end gap-2">
                    <a href="{{ route('pelanggan') }}" class="bg-rose-400 hover:bg-rose-500 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 flex-1 justify-center">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                    <a href="{{ route('pendaftaran.export', request()->query()) }}" class="bg-amber-400 hover:bg-amber-500 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 flex-1 justify-center">
                        <i class="fa-solid fa-file-excel text-[11px]"></i> Export
                    </a>
                </div>
            </div>

            <!-- Row 2: Bulan, Tahun, Media Akses, Group Layanan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <select name="bulan" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="">SEMUA BULAN AKTIF</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="tahun" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="">SEMUA TAHUN</option>
                        @foreach(range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="media_akses" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="">SEMUA MEDIA AKSES</option>
                        @foreach($mediaAksesList ?? [] as $ma)
                            <option value="{{ $ma }}" {{ request('media_akses') == $ma ? 'selected' : '' }}>{{ strtoupper($ma) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="group_layanan" onchange="this.form.submit()" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-pointer">
                        <option value="">-- Semua Group Layanan --</option>
                        @foreach($groupLayananList ?? [] as $gl)
                            <option value="{{ $gl }}" {{ request('group_layanan') == $gl ? 'selected' : '' }}>{{ strtoupper($gl) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Tabel Data Pelanggan (Desain Sesuai Mockup) -->
        <div id="tabel-pelanggan" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">
            <!-- Controls: Entries & Search -->
            <div class="px-6 py-4 border-b border-gray-100 bg-white flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('pelanggan') }}#tabel-pelanggan" class="flex items-center gap-2 text-xs text-gray-600">
                    <span>Show</span>
                    <select name="entries" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-800 text-xs rounded-lg px-2.5 py-1 focus:border-blue-500 outline-none cursor-pointer">
                        <option value="10" {{ request('entries', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>entries</span>
                </form>

                <form method="GET" action="{{ route('pelanggan') }}#tabel-pelanggan" class="w-full md:w-auto flex items-center gap-2">
                    <span class="text-xs text-gray-500">Search:</span>
                    <input type="text" name="search" value="{{ request('search') }}" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1 text-xs text-gray-700 focus:border-blue-500 outline-none">
                </form>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-gray-700 font-bold uppercase tracking-wider border-b border-gray-100 text-[11px]">
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">Lokasi Pemasangan</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Tanggal SO</th>
                            <th class="py-3.5 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                        @forelse($customers as $c)
                            <tr class="hover:bg-blue-50/20 transition-colors">
                                <!-- Col 1: Pelanggan -->
                                <td class="py-4 px-6 align-top space-y-1">
                                    <a href="{{ route('pelanggan.detail', $c->nomor_internet) }}" class="font-bold text-blue-600 hover:underline block text-sm">
                                        {{ $c->nomor_internet }}
                                    </a>
                                    <a href="{{ route('pelanggan.detail', $c->nomor_internet) }}" class="font-bold text-gray-900 hover:text-blue-600 hover:underline uppercase block">
                                        {{ $c->nama_display }}
                                    </a>
                                    <a href="{{ route('pelanggan.detail', $c->nomor_internet) }}" class="text-[11px] font-semibold text-blue-600 hover:underline block">
                                        {{ $c->paket }}
                                    </a>
                                    {{-- Group Layanan (Hide Dulu) --}}
                                    {{-- <div class="text-[11px] text-gray-400 mt-1">
                                        Group Layanan : <span class="font-semibold text-gray-700 uppercase">{{ $c->group_layanan ?: 'MEDIANET' }}</span>
                                    </div> --}}
                                </td>

                                <!-- Col 2: Lokasi Pemasangan -->
                                <td class="py-4 px-6 align-top space-y-1">
                                    <div class="font-bold text-gray-700 uppercase text-[11px]">
                                        {{ $c->jenis_bangunan ?: 'RUMAH-PRIBADI' }}
                                    </div>
                                    <div class="text-xs text-gray-600 max-w-sm leading-relaxed uppercase">
                                        {{ $c->alamat_pasang ?: $c->alamat_p ?: '-' }}
                                    </div>
                                    <div class="font-bold text-gray-800 text-[11px] mt-1">
                                        {{ $c->media_akses ?: 'MediaNet FTTH' }}
                                    </div>
                                </td>

                                <!-- Col 3: Status -->
                                <td class="py-4 px-6 align-top space-y-1.5">
                                    <div>
                                        @if(($c->is_suspend ?? '0') == '1' || ($c->section ?? '') === 'suspend')
                                            <span class="inline-block bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                <i class="fa-solid fa-pause-circle text-[9px] mr-0.5"></i> Suspend
                                            </span>
                                        @elseif(($c->is_termin ?? '0') == '1' || ($c->section ?? '') === 'terminasi')
                                            <span class="inline-block bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                Terminasi
                                            </span>
                                        @elseif(($c->section ?? '') === 'gagal')
                                            <span class="inline-block bg-gray-100 text-gray-800 border border-gray-300 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                Gagal Pasang
                                            </span>
                                        @else
                                            <span class="inline-block bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-400 underline">
                                        Updated {{ \Carbon\Carbon::parse($c->date_update ?: $c->date_create)->format('d M Y') }}
                                    </div>
                                    <div class="font-semibold text-gray-800 text-xs">
                                        Rp {{ number_format((float) ($c->harga_bandwith ?? $c->harga_paket ?? 0), 2, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Col 4: Tanggal SO -->
                                <td class="py-4 px-6 align-top space-y-1">
                                    <div class="text-xs text-gray-700 font-semibold">
                                        {{ \Carbon\Carbon::parse($c->date_create)->format('d M Y H:i') }} WIB
                                    </div>
                                    <div class="text-xs font-bold text-gray-800 uppercase">
                                        {{ strtoupper($c->user_create ?: 'NUNU NUGRAHA') }}
                                    </div>
                                    <div class="text-xs text-gray-500 uppercase">
                                        SALES : {{ strtoupper($c->nama_sales ?: '-') }}
                                    </div>
                                </td>

                                    <!-- Col 5: Aksi -->
                                    <td class="py-4 px-6 align-top">
                                        <div class="flex flex-col gap-1.5 text-xs whitespace-nowrap">
                                            @if($isAdmin || $isNoc || $isTeknik)
                                                {{-- Role NOC, ADMIN, TEKNIK: Hanya Edit dan Hapus --}}
                                                <a href="{{ route('pendaftaran.edit', $c->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors font-medium">
                                                    <i class="fa-solid fa-pen-to-square text-emerald-500 text-[11px]"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <button type="button" onclick="openModalHapus('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-trash-can text-rose-500 text-[11px]"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            @elseif($isFinance)
                                                {{-- Role Finance: Edit & Aksi Permintaan --}}
                                                <a href="{{ route('pendaftaran.edit', $c->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors font-medium">
                                                    <i class="fa-solid fa-pen-to-square text-emerald-500 text-[11px]"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <button type="button" onclick="openModalTerminasi('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-file-contract text-rose-500 text-[11px]"></i>
                                                    <span>Req. Terminasi</span>
                                                </button>
                                                <button type="button" onclick="openModalUpDowngrade('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-purple-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-arrows-up-down text-purple-500 text-[11px]"></i>
                                                    <span>Req. Up/Downgrade</span>
                                                </button>
                                                <button type="button" onclick="openModalSuspend('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-amber-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-circle-pause text-amber-500 text-[11px]"></i>
                                                    <span>Req. Suspend</span>
                                                </button>
                                                <button type="button" onclick="openModalAdjust('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-cyan-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-sliders text-cyan-500 text-[11px]"></i>
                                                    <span>Adjust</span>
                                                </button>
                                            @else
                                                {{-- Default role lainnya: Edit dan Hapus --}}
                                                <a href="{{ route('pendaftaran.edit', $c->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors font-medium">
                                                    <i class="fa-solid fa-pen-to-square text-emerald-500 text-[11px]"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <button type="button" onclick="openModalHapus('{{ $c->nomor_internet }}', '{{ addslashes($c->nama_display) }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors font-medium text-left cursor-pointer">
                                                    <i class="fa-solid fa-trash-can text-rose-500 text-[11px]"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                                <i class="fa-solid fa-users-slash text-xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-700">Tidak ada data pelanggan ditemukan</p>
                                            <p class="text-xs text-gray-400 mt-1">Gunakan tab pilihan di atas atau ubah filter pencarian untuk menemukan data pelanggan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. MODAL REQUEST TERMINASI LAYANAN -->
    <!-- ========================================================================= -->
    <div id="modal-terminasi" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    Form Request Terminasi Layanan <span id="term-title-name" class="text-blue-600 uppercase"></span>
                </h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 text-lg p-1 rounded-lg transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="form-terminasi" onsubmit="submitFormTerminasi(event)">
                @csrf
                <input type="hidden" id="term-nomor-internet" name="nomor_internet">

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/30">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Layanan Saat Ini
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs flex items-center justify-center text-center min-h-[100px]">
                                <span id="term-current-pack" class="text-xl font-extrabold text-slate-800 tracking-tight">Memuat...</span>
                            </div>
                        </div>

                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Riwayat Pending Tagihan
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-sky-50/70 text-slate-700 font-bold border-b border-gray-100">
                                            <th class="py-2.5 px-3">Periode <i class="fa-solid fa-sort text-[10px] text-gray-400"></i></th>
                                            <th class="py-2.5 px-3">Jumlah</th>
                                            <th class="py-2.5 px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="term-pending-tbody" class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-5">
                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Informasi Terminasi
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl p-3.5 shadow-xs space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-700">
                                    Alasan Terminasi <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="term-note" name="note_termin" rows="3" required placeholder="alasan user melakukan terminasi..." class="w-full bg-white border border-gray-200 rounded-lg p-2.5 text-xs text-gray-700 focus:border-blue-500 outline-none placeholder-gray-400 resize-none"></textarea>
                            </div>
                        </div>

                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Perangkat On Site
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-sky-50/70 text-slate-700 font-bold border-b border-gray-100">
                                            <th class="py-2.5 px-3">Perangkat <i class="fa-solid fa-sort text-[10px] text-gray-400"></i></th>
                                            <th class="py-2.5 px-3">Jumlah</th>
                                            <th class="py-2.5 px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="term-devices-tbody" class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAllModals()" class="bg-[#00c5c8] hover:bg-[#00b0b3] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-[11px]"></i> Batal
                    </button>
                    <button type="submit" id="btn-submit-term" class="bg-[#0070f3] hover:bg-[#005bb5] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MODAL REQUEST UP/DOWNGRADE LAYANAN -->
    <!-- ========================================================================= -->
    <div id="modal-updowngrade" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    Form Request Up/Downgrade <span id="updown-title-name" class="text-blue-600 uppercase"></span>
                </h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 text-lg p-1 rounded-lg transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="form-updowngrade" onsubmit="submitFormUpDowngrade(event)">
                @csrf
                <input type="hidden" id="updown-nomor-internet" name="nomor_internet">

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/30">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Layanan saat ini
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs flex items-center justify-center text-center min-h-[100px]">
                                <span id="updown-current-pack" class="text-xl font-extrabold text-slate-800 tracking-tight">Memuat...</span>
                            </div>
                        </div>

                        <div>
                            <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-2">
                                Riwayat Perubahan Layanan
                            </div>
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-sky-50/70 text-slate-700 font-bold border-b border-gray-100">
                                            <th class="py-2.5 px-3">Old <i class="fa-solid fa-sort text-[10px] text-gray-400"></i></th>
                                            <th class="py-2.5 px-3">New</th>
                                            <th class="py-2.5 px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="updown-history-tbody" class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                                        <tr><td colspan="3" class="py-4 text-center text-gray-400">No data available in table</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700 mb-1">
                            Request Ubah Layanan
                        </div>

                        <!-- Cyan Notification Alert -->
                        <div class="bg-[#00c5c8] text-white text-xs p-3.5 rounded-xl font-medium shadow-xs leading-relaxed">
                            Info! Setiap Perubahan Layanan Akan Efektif pada sistem setiap tanggal pertama awal bulan.
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs space-y-3.5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                    Jenis Bangunan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="updown-jenis-bangunan" readonly class="w-full bg-gray-100/70 border border-gray-200 text-gray-700 py-2 px-3 text-xs font-semibold uppercase rounded-lg outline-none cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                    Kategori Layanan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="updown-kategori-input" name="kode_kategori" autocomplete="off" required placeholder="KETIK KATEGORI LAYANAN (MISAL: BROADBAND, DEDICATED)" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-medium rounded-lg outline-none uppercase placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                    Kapasitas Layanan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="updown-paket-input" name="kode_bandwith_baru" autocomplete="off" required placeholder="KETIK KAPASITAS LAYANAN / BANDWIDTH (MISAL: 100 MBPS)" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-medium rounded-lg outline-none placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                    Harga Layanan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="updown-harga-input" name="harga_paket" autocomplete="off" required placeholder="KETIK HARGA LAYANAN (CONTOH: 500.000)" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2 px-3 text-xs font-semibold rounded-lg outline-none placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAllModals()" class="bg-[#00c5c8] hover:bg-[#00b0b3] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-[11px]"></i> Batal
                    </button>
                    <button type="submit" id="btn-submit-updown" class="bg-[#0070f3] hover:bg-[#005bb5] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. MODAL REQUEST SUSPEND LAYANAN -->
    <!-- ========================================================================= -->
    <div id="modal-suspend" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    Form Request Suspend Layanan <span id="susp-title-name" class="text-blue-600 uppercase"></span>
                </h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 text-lg p-1 rounded-lg transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="form-suspend" onsubmit="submitFormSuspend(event)">
                @csrf
                <input type="hidden" id="susp-nomor-internet" name="nomor_internet">

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/30">
                    <!-- Left Column: Riwayat Pembayaran -->
                    <div class="space-y-3">
                        <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700">
                            Riwayat Pembayaran
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-sky-50/70 text-slate-700 font-bold border-b border-gray-100">
                                        <th class="py-2.5 px-3">Bulan <i class="fa-solid fa-sort text-[10px] text-gray-400"></i></th>
                                        <th class="py-2.5 px-3">Biaya</th>
                                        <th class="py-2.5 px-3">Status Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="susp-payment-tbody" class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                                    <tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Column: Request Suspend -->
                    <div class="space-y-3">
                        <div class="border-l-3 border-blue-500 pl-2 text-xs font-bold text-gray-700">
                            Request Suspend
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs space-y-2">
                            <label class="block text-[11px] font-bold text-gray-700">
                                note suspend <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="susp-note" name="note_suspend" rows="5" required placeholder="Catatan layanan disuspend" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 text-xs text-gray-700 focus:border-blue-500 outline-none placeholder-gray-400 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAllModals()" class="bg-[#00c5c8] hover:bg-[#00b0b3] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-[11px]"></i> Batal
                    </button>
                    <button type="submit" id="btn-submit-susp" class="bg-[#0070f3] hover:bg-[#005bb5] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. MODAL PENYESUAIAN DATA (ADJUST) -->
    <!-- ========================================================================= -->
    <div id="modal-adjust" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    Form Penyesuaian Data <span id="adj-title-name" class="text-blue-600 uppercase"></span>
                </h3>
                <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 text-lg p-1 rounded-lg transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="form-adjust" onsubmit="submitFormAdjust(event)">
                @csrf
                <input type="hidden" id="adj-nomor-internet" name="nomor_internet">

                <div class="p-6 space-y-6 bg-slate-50/20">
                    <!-- Row 1: Tagihan Bulanan, Range Tagihan, PPN, Status PPN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
                        <!-- Col 1: Tagihan Bulanan -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                TAGIHAN BULANAN <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="adj-tagihan" name="tagihan_bulanan" placeholder="185000" class="w-full bg-white border border-gray-200 rounded-lg py-2 px-3 text-xs font-bold text-gray-800 focus:border-blue-500 outline-none">
                        </div>

                        <!-- Col 2: Range Tagihan -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                RANGE TAGIHAN <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-lg p-1.5 text-xs text-gray-700">
                                <span class="font-medium text-gray-500">Per</span>
                                <input type="number" id="adj-range" name="range_tagihan" value="1" min="1" max="12" class="w-10 text-center font-bold text-gray-800 border-b border-gray-300 focus:border-blue-500 outline-none py-0.5">
                                <span class="font-medium text-gray-500">Bulan</span>
                            </div>
                        </div>

                        <!-- Col 3: PPN -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                PPN <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center justify-center gap-1.5 bg-white border border-gray-200 rounded-lg p-1.5 text-xs text-gray-700">
                                <input type="number" id="adj-ppn" name="ppn" value="0" min="0" max="100" class="w-12 text-center font-bold text-gray-800 border-b border-gray-300 focus:border-blue-500 outline-none py-0.5">
                                <span class="font-bold text-gray-500">%</span>
                            </div>
                        </div>

                        <!-- Col 4: Status PPN -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                STATUS PPN ? <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-3 pt-1 text-xs">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ppn" value="1" id="adj-ppn-aktif" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">Aktif</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="status_ppn" value="0" id="adj-ppn-nonaktif" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">Tidak Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Suspend By Payment, Denda, Periode Terminasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-start pt-2 border-t border-gray-100">
                        <!-- Col 1: Suspend By Payment -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                SUSPEND BY PAYMENT ? <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 pt-1 text-xs">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="is_suspend" value="1" id="adj-susp-ya" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">YA</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="is_suspend" value="0" id="adj-susp-tidak" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">TIDAK</span>
                                </label>
                            </div>
                        </div>

                        <!-- Col 2: Denda -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                DENDA ? <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 pt-1 text-xs">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="is_denda" value="1" id="adj-denda-ya" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">YA</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="is_denda" value="0" id="adj-denda-tidak" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-700 font-medium">TIDAK</span>
                                </label>
                            </div>
                        </div>

                        <!-- Col 3: Periode Terminasi -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-700">
                                PERIODE TERMINASI <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-lg p-1.5 text-xs text-gray-700">
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[11px] font-bold">term</span>
                                <input type="text" id="adj-terminasi" name="periode_terminasi" placeholder="0" class="w-14 text-center font-bold text-gray-800 border-b border-gray-300 focus:border-blue-500 outline-none py-0.5">
                                <span class="font-medium text-gray-500 uppercase">BULAN</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAllModals()" class="bg-[#00c5c8] hover:bg-[#00b0b3] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-[11px]"></i> Batal
                    </button>
                    <button type="submit" id="btn-submit-adj" class="bg-[#0070f3] hover:bg-[#005bb5] text-white px-5 py-2 rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL KONFIRMASI HAPUS PELANGGAN -->
    <!-- ========================================================================= -->
    <div id="modal-hapus" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-rose-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-1">Apakah Anda yakin ingin menghapus data pelanggan ini?</p>
                <p id="hapus-nomor-internet" class="text-sm font-semibold text-rose-600 mb-6"></p>

                <form id="form-hapus-pelanggan" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" onclick="closeAllModals()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-md shadow-rose-200/50 flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- JAVASCRIPT HANDLERS FOR THE MODALS -->
    <!-- ========================================================================= -->
    <script>
        let cachedPaketList = [];

        function closeAllModals() {
            document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
        }

        // Close on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAllModals();
        });

        // Close on clicking backdrop
        document.querySelectorAll('[id^="modal-"]').forEach(m => {
            m.addEventListener('click', function(e) {
                if (e.target === this) closeAllModals();
            });
        });

        // -------------------------------------------------------------
        // 0. MODAL HAPUS
        // -------------------------------------------------------------
        function openModalHapus(nomorInternet, namaDisplay = '') {
            closeAllModals();
            const form = document.getElementById('form-hapus-pelanggan');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet);
            document.getElementById('hapus-nomor-internet').textContent = nomorInternet + (namaDisplay ? ' - ' + namaDisplay : '');
            document.getElementById('modal-hapus').classList.remove('hidden');
        }

        function konfirmasiHapus(nomorInternet, namaDisplay = '') {
            openModalHapus(nomorInternet, namaDisplay);
        }

        // -------------------------------------------------------------
        // 1. MODAL TERMINASI
        // -------------------------------------------------------------
        function openModalTerminasi(nomorInternet, namaDisplay) {
            closeAllModals();
            const modal = document.getElementById('modal-terminasi');
            document.getElementById('term-title-name').textContent = namaDisplay;
            document.getElementById('term-nomor-internet').value = nomorInternet;
            document.getElementById('term-current-pack').textContent = 'Memuat...';
            document.getElementById('term-note').value = '';
            document.getElementById('term-pending-tbody').innerHTML = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>';
            document.getElementById('term-devices-tbody').innerHTML = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>';
            
            modal.classList.remove('hidden');

            fetch(`/pelanggan/${nomorInternet}/modal-data`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('term-current-pack').textContent = d.current_pack;
                        
                        // Render pending bills
                        let pendingHtml = '';
                        if (d.pending_bills && d.pending_bills.length > 0) {
                            d.pending_bills.forEach(b => {
                                pendingHtml += `
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-semibold">${b.periode}</td>
                                        <td class="py-2.5 px-3 font-bold text-gray-800">${b.jumlah}</td>
                                        <td class="py-2.5 px-3"><span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[10px] font-bold">${b.status}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            pendingHtml = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada pending tagihan</td></tr>';
                        }
                        document.getElementById('term-pending-tbody').innerHTML = pendingHtml;

                        // Render devices
                        let deviceHtml = '';
                        if (d.devices && d.devices.length > 0) {
                            d.devices.forEach(dev => {
                                deviceHtml += `
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-bold">
                                            ${dev.nama}
                                            <div class="text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded inline-block mt-0.5">${dev.sub}</div>
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold text-gray-800">${dev.jumlah}</td>
                                        <td class="py-2.5 px-3"><span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded text-[10px] font-bold">${dev.status}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            deviceHtml = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada perangkat</td></tr>';
                        }
                        document.getElementById('term-devices-tbody').innerHTML = deviceHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('term-current-pack').textContent = 'Gagal memuat';
                });
        }

        function submitFormTerminasi(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-term');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[11px]"></i> Menyimpan...';

            const form = document.getElementById('form-terminasi');
            const data = new FormData(form);

            fetch("{{ route('pelanggan.request-terminasi') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                alert('Terjadi kesalahan jaringan atau server.');
            });
        }

        // -------------------------------------------------------------
        // 2. MODAL UP/DOWNGRADE
        // -------------------------------------------------------------
        let globalUpdownPakets = [];
        let globalUpdownLayanans = [];

        function renderUpdownPaketDatalist(selectedKategori = '') {
            const listPaketEl = document.getElementById('listUpdownPaket');
            if (!listPaketEl) return;
            listPaketEl.innerHTML = '';

            const filtered = selectedKategori 
                ? globalUpdownPakets.filter(p => !p.nama_kategori_bandwith || p.nama_kategori_bandwith.toLowerCase().includes(selectedKategori.toLowerCase()) || (p.kode_kategori_bandwith && p.kode_kategori_bandwith.toLowerCase().includes(selectedKategori.toLowerCase())))
                : globalUpdownPakets;

            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = `${p.nominal_bandwith} Mbps - ${p.nama_kategori_bandwith || ''}`;
                listPaketEl.appendChild(opt);
            });
        }

        function openModalUpDowngrade(nomorInternet, namaDisplay) {
            closeAllModals();
            const modal = document.getElementById('modal-updowngrade');
            document.getElementById('updown-title-name').textContent = namaDisplay;
            document.getElementById('updown-nomor-internet').value = nomorInternet;
            document.getElementById('updown-current-pack').textContent = 'Memuat...';
            document.getElementById('updown-jenis-bangunan').value = '';
            document.getElementById('updown-history-tbody').innerHTML = '<tr><td colspan="3" class="py-4 text-center text-gray-400">No data available in table</td></tr>';

            document.getElementById('updown-kategori-input').value = '';
            document.getElementById('updown-paket-input').value = '';
            document.getElementById('updown-harga-input').value = '';

            modal.classList.remove('hidden');

            fetch(`/pelanggan/${nomorInternet}/modal-data`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('updown-current-pack').textContent = d.current_pack;
                        document.getElementById('updown-jenis-bangunan').value = d.jenis_bangunan || 'RUMAH-PRIBADI';

                        // Populate riwayat ubah layanan
                        let histHtml = '';
                        if (d.riwayat_ubah && d.riwayat_ubah.length > 0) {
                            d.riwayat_ubah.forEach(h => {
                                histHtml += `
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-semibold">${h.old}</td>
                                        <td class="py-2.5 px-3 font-bold text-gray-800">${h.new}</td>
                                        <td class="py-2.5 px-3"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold">${h.status}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            histHtml = '<tr><td colspan="3" class="py-4 text-center text-gray-400">No data available in table</td></tr>';
                        }
                        document.getElementById('updown-history-tbody').innerHTML = histHtml;

                        // Simpan master paket & layanan
                        globalUpdownPakets = d.paket_list || [];
                        globalUpdownLayanans = d.layanan_list || [];

                        // Populate datalist kategori
                        const listKatEl = document.getElementById('listUpdownKategori');
                        if (listKatEl && globalUpdownLayanans.length > 0) {
                            listKatEl.innerHTML = '';
                            globalUpdownLayanans.forEach(k => {
                                const opt = document.createElement('option');
                                opt.value = k.nama_kategori_bandwith;
                                listKatEl.appendChild(opt);
                            });
                        }

                        renderUpdownPaketDatalist();
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('updown-current-pack').textContent = 'Gagal memuat';
                });
        }

        function submitFormUpDowngrade(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-updown');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[11px]"></i> Menyimpan...';

            const form = document.getElementById('form-updowngrade');
            const data = new FormData(form);

            fetch("{{ route('pelanggan.request-up-downgrade') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                alert('Terjadi kesalahan jaringan atau server.');
            });
        }

        // -------------------------------------------------------------
        // 3. MODAL SUSPEND
        // -------------------------------------------------------------
        function openModalSuspend(nomorInternet, namaDisplay) {
            closeAllModals();
            const modal = document.getElementById('modal-suspend');
            document.getElementById('susp-title-name').textContent = namaDisplay;
            document.getElementById('susp-nomor-internet').value = nomorInternet;
            document.getElementById('susp-note').value = '';
            document.getElementById('susp-payment-tbody').innerHTML = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Memuat data...</td></tr>';

            modal.classList.remove('hidden');

            fetch(`/pelanggan/${nomorInternet}/modal-data`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        let payHtml = '';
                        if (d.riwayat_bayar && d.riwayat_bayar.length > 0) {
                            d.riwayat_bayar.forEach(p => {
                                payHtml += `
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-semibold">${p.bulan}</td>
                                        <td class="py-2.5 px-3 font-bold text-gray-800">${p.biaya}</td>
                                        <td class="py-2.5 px-3"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold">${p.status}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            payHtml = '<tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada riwayat pembayaran</td></tr>';
                        }
                        document.getElementById('susp-payment-tbody').innerHTML = payHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }

        function submitFormSuspend(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-susp');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[11px]"></i> Menyimpan...';

            const form = document.getElementById('form-suspend');
            const data = new FormData(form);

            fetch("{{ route('pelanggan.request-suspend') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                alert('Terjadi kesalahan jaringan atau server.');
            });
        }

        // -------------------------------------------------------------
        // 4. MODAL ADJUST (PENYESUAIAN DATA)
        // -------------------------------------------------------------
        function openModalAdjust(nomorInternet, namaDisplay) {
            closeAllModals();
            const modal = document.getElementById('modal-adjust');
            document.getElementById('adj-title-name').textContent = namaDisplay;
            document.getElementById('adj-nomor-internet').value = nomorInternet;

            modal.classList.remove('hidden');

            fetch(`/pelanggan/${nomorInternet}/modal-data`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const adj = res.data.adjust;
                        document.getElementById('adj-tagihan').value = adj.tagihan_bulanan || 185000;
                        document.getElementById('adj-range').value = adj.range_tagihan || 1;
                        document.getElementById('adj-ppn').value = adj.ppn || 0;
                        
                        if (adj.status_ppn === '1') {
                            document.getElementById('adj-ppn-aktif').checked = true;
                        } else {
                            document.getElementById('adj-ppn-nonaktif').checked = true;
                        }

                        if (adj.is_suspend === '1') {
                            document.getElementById('adj-susp-ya').checked = true;
                        } else {
                            document.getElementById('adj-susp-tidak').checked = true;
                        }

                        if (adj.is_denda === '1') {
                            document.getElementById('adj-denda-ya').checked = true;
                        } else {
                            document.getElementById('adj-denda-tidak').checked = true;
                        }

                        document.getElementById('adj-terminasi').value = adj.periode_terminasi || '0';
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }

        function submitFormAdjust(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-adj');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[11px]"></i> Menyimpan...';

            const form = document.getElementById('form-adjust');
            const data = new FormData(form);

            fetch("{{ route('pelanggan.adjust') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-[11px]"></i> Update';
                alert('Terjadi kesalahan jaringan atau server.');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Up/Downgrade form interactive listeners
            const katInput = document.getElementById('updown-kategori-input');
            const pktInput = document.getElementById('updown-paket-input');
            const hrgInput = document.getElementById('updown-harga-input');

            if (katInput) {
                katInput.addEventListener('input', function() {
                    renderUpdownPaketDatalist(this.value);
                });
            }

            if (pktInput) {
                pktInput.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (!val) return;

                    // Match paket
                    const matched = globalUpdownPakets.find(p => {
                        const label1 = `${p.nominal_bandwith} Mbps - ${p.nama_kategori_bandwith || ''}`;
                        const label2 = `${p.nominal_bandwith} Mbps`;
                        return label1.toLowerCase() === val.toLowerCase() 
                            || label2.toLowerCase() === val.toLowerCase()
                            || (p.kode_bandwith && p.kode_bandwith.toLowerCase() === val.toLowerCase())
                            || (p.nominal_bandwith && val.startsWith(p.nominal_bandwith));
                    });

                    if (matched) {
                        if (katInput && !katInput.value && matched.nama_kategori_bandwith) {
                            katInput.value = matched.nama_kategori_bandwith;
                        }
                        if (hrgInput && matched.harga_bandwith) {
                            hrgInput.value = 'Rp ' + parseInt(matched.harga_bandwith).toLocaleString('id-ID');
                        }
                    }
                });
            }

            if (hrgInput) {
                function formatRibuanPelanggan(val) {
                    const num = (val || '').toString().replace(/\D/g, '');
                    if (!num) return '';
                    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                hrgInput.addEventListener('input', function() {
                    const startPos = this.selectionStart;
                    const prevLen = this.value.length;
                    this.value = formatRibuanPelanggan(this.value);
                    const newLen = this.value.length;
                    const newPos = Math.max(0, startPos + (newLen - prevLen));
                    this.setSelectionRange(newPos, newPos);
                });

                if (hrgInput.value) {
                    hrgInput.value = formatRibuanPelanggan(hrgInput.value);
                }
            }

            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('is-visible'); }); return; }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); } });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
            els.forEach(function (e) { io.observe(e); });
        });
    </script>
@endsection
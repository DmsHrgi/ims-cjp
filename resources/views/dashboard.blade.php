@extends('layouts.app')

@section('content')
    @php
        /** @var array $chart */
        /** @var int $maxChart */
        /** @var array $c1 */
        /** @var array $c2 */
        /** @var array $c3 */
        /** @var array $c4 */

        $cards = [
            ['title' => 'Pendaftaran Baru', 'stat' => $c1, 'icon' => 'fa-user-plus',  'color' => 'blue',   'bg' => 'bg-blue-500',   'soft' => 'bg-blue-50 text-blue-600'],
            ['title' => 'Tiket Gangguan',   'stat' => $c2, 'icon' => 'fa-headset',    'color' => 'rose',   'bg' => 'bg-rose-500',   'soft' => 'bg-rose-50 text-rose-600'],
            ['title' => 'Suspend',          'stat' => $c3, 'icon' => 'fa-pause',      'color' => 'amber',  'bg' => 'bg-amber-500',  'soft' => 'bg-amber-50 text-amber-600'],
            ['title' => 'Terminasi',        'stat' => $c4, 'icon' => 'fa-user-xmark', 'color' => 'violet', 'bg' => 'bg-violet-500', 'soft' => 'bg-violet-50 text-violet-600'],
        ];
    @endphp

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-sm text-gray-400 mt-0.5">Selamat datang kembali 👋 Berikut ringkasan bulan ini.</p>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach ($cards as $c)
            @php $s = $c['stat']; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow duration-200">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ $c['title'] }}</p>
                    <p class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ number_format($s['now'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-xs font-medium">
                        @if ($s['now'] == 0 && $s['prev'] == 0)
                            <span class="text-gray-400">Belum ada data</span>
                        @else
                            <span class="flex items-center gap-1 {{ $s['dir'] === 'up' ? 'text-emerald-600' : ($s['dir'] === 'down' ? 'text-red-500' : 'text-gray-400') }}">
                                <i class="fa-solid {{ $s['dir'] === 'up' ? 'fa-arrow-up' : ($s['dir'] === 'down' ? 'fa-arrow-down' : 'fa-minus') }}"></i>
                                {{ abs($s['trend']) }}% vs bulan lalu
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-11 h-11 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fa-solid {{ $c['icon'] }} text-white text-base"></i>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Pendaftaran 7 Bulan Terakhir</h2>
                <p class="text-xs text-gray-400 mt-0.5">Jumlah registrasi baru per bulan</p>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">trx_batchjob_register</span>
        </div>

        <div class="h-52 flex items-end gap-2">
            @foreach ($chart as $bar)
                @php $h = $maxChart > 0 ? (int) round($bar['count'] / $maxChart * 100) : 0; @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group">
                    <span class="text-[10px] font-semibold text-gray-400 mb-1 tabular-nums group-hover:text-blue-500 transition-colors">{{ $bar['count'] }}</span>
                    @if ($h > 0)
                        <div title="{{ $bar['label'] }}: {{ $bar['count'] }}"
                             class="w-full max-w-[36px] rounded-t-md bg-blue-500 opacity-80 group-hover:opacity-100 group-hover:bg-blue-600 transition-all duration-200"
                             style="height: {{ max($h, 4) }}%"></div>
                    @else
                        <div class="w-full max-w-[36px] h-1 rounded-t-md bg-gray-100"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex gap-2 mt-3 border-t border-gray-100 pt-3">
            @foreach ($chart as $bar)
                <div class="flex-1 text-center text-[10px] font-semibold text-gray-400 uppercase tracking-wide">{{ $bar['label'] }}</div>
            @endforeach
        </div>
    </div>

    {{-- =========================================================
         SEKSI VISUALISASI PERUSAHAAN & ID PELANGGAN
         ========================================================= --}}
    <div id="company-visualization-section" class="space-y-6">
        {{-- Section Header --}}
        <div class="pb-2 border-b border-gray-200/70">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-xs">
                    <i class="fa-solid fa-building-user text-base"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        Distribusi Perusahaan & ID Pelanggan
                        <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200/60 px-2.5 py-0.5 rounded-full">
                            {{ $totalPerusahaan }} Perusahaan
                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemetaan relasi nama perusahaan terhadap seluruh ID Pelanggan (Nomor Internet / Layanan) yang terdaftar.</p>
                </div>
            </div>
        </div>

        {{-- Visual Distribution Bar (Top Companies) --}}
        @if ($topCompanies->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-chart-simple text-indigo-500"></i>
                            Top Perusahaan dengan ID Pelanggan Terbanyak
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Perusahaan yang mengelola titik sambungan dan ID layanan terbanyak</p>
                    </div>
                    <span class="text-[11px] text-gray-500 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-lg self-start sm:self-auto">
                        Top {{ $topCompanies->count() }} Perusahaan
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($topCompanies as $idx => $top)
                        @php
                            $pct = $maxTopCount > 0 ? round(($top['total_pelanggan'] / $maxTopCount) * 100) : 0;
                            $corpInitials = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $top['nama_perusahaan']), 0, 2)) ?: 'PT';
                            $colorPalettes = [
                                ['bg' => 'bg-indigo-500', 'soft' => 'bg-indigo-50 text-indigo-700 border-indigo-100'],
                                ['bg' => 'bg-blue-500',   'soft' => 'bg-blue-50 text-blue-700 border-blue-100'],
                                ['bg' => 'bg-cyan-500',   'soft' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                                ['bg' => 'bg-teal-500',   'soft' => 'bg-teal-50 text-teal-700 border-teal-100'],
                                ['bg' => 'bg-violet-500', 'soft' => 'bg-violet-50 text-violet-700 border-violet-100'],
                                ['bg' => 'bg-emerald-500','soft' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                            ];
                            $palette = $colorPalettes[$idx % count($colorPalettes)];
                        @endphp
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-3.5 hover:bg-white hover:shadow-md hover:border-gray-200 transition-all duration-200">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-lg {{ $palette['soft'] }} border flex items-center justify-center font-bold text-[11px] flex-shrink-0">
                                        {{ $corpInitials }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-800 truncate" title="{{ $top['nama_perusahaan'] }}">
                                            {{ $top['nama_perusahaan'] }}
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-mono truncate">
                                            ID: {{ $top['id_perusahaan'] }}
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-xs font-extrabold {{ $palette['soft'] }} border flex-shrink-0 tabular-nums">
                                    {{ $top['total_pelanggan'] }} ID
                                </span>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="w-full bg-gray-200/80 rounded-full h-2 overflow-hidden mb-2">
                                <div class="{{ $palette['bg'] }} h-2 rounded-full transition-all duration-500" style="width: {{ max($pct, 6) }}%"></div>
                            </div>

                            {{-- Mini stats --}}
                            <div class="flex items-center justify-between text-[10px] text-gray-500 pt-1 border-t border-gray-200/50">
                                <span class="flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $top['count_aktif'] }} Aktif
                                </span>
                                @if ($top['count_suspend'] > 0)
                                    <span class="flex items-center gap-1 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $top['count_suspend'] }} Suspend
                                    </span>
                                @endif
                                @if ($top['count_terminasi'] > 0)
                                    <span class="flex items-center gap-1 text-rose-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $top['count_terminasi'] }} Terminasi
                                    </span>
                                @endif
                                <button type="button" onclick="focusCompanyCard('{{ addslashes($top['key']) }}')" class="text-indigo-600 font-medium hover:underline text-[10px] flex items-center gap-0.5">
                                    Lihat <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between gap-3">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text"
                           id="companySearchInput"
                           onkeyup="filterCompanyVisualization()"
                           placeholder="Cari nama perusahaan, ID perusahaan, ID Pelanggan (001-...), atau kota..."
                           class="w-full pl-9 pr-8 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <button type="button"
                            id="clearCompanySearchBtn"
                            onclick="clearCompanySearch()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-300 hover:text-gray-500 hidden">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-[11px] text-gray-400">
                <span id="companyCountDisplay">Menampilkan <strong>{{ $totalPerusahaan }}</strong> perusahaan</span>
                <span class="flex items-center gap-1">
                    <i class="fa-regular fa-lightbulb text-amber-500"></i>
                    Klik pada kartu perusahaan untuk memperluas / melihat detail ID pelanggan
                </span>
            </div>
        </div>

        {{-- Company & Customer IDs Matrix / Cards Grid --}}
        <div id="companyCardsContainer" class="space-y-4">
            @forelse ($companyList as $corp)
                @php
                    $initials = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $corp['nama_perusahaan']), 0, 2)) ?: 'PT';
                    $hasMulti = $corp['total_pelanggan'] >= 2;
                    $hasAktif = $corp['count_aktif'] > 0;
                    $hasSuspend = $corp['count_suspend'] > 0;
                    $hasTerminasi = $corp['count_terminasi'] > 0;
                @endphp
                <div class="company-card bg-white rounded-2xl border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden"
                     data-company-key="{{ $corp['key'] }}"
                     data-company-name="{{ strtolower($corp['nama_perusahaan']) }}"
                     data-company-id="{{ strtolower($corp['id_perusahaan']) }}"
                     data-has-multisite="{{ $hasMulti ? '1' : '0' }}"
                     data-has-aktif="{{ $hasAktif ? '1' : '0' }}"
                     data-has-suspend="{{ $hasSuspend ? '1' : '0' }}"
                     data-has-terminasi="{{ $hasTerminasi ? '1' : '0' }}"
                     data-search-text="{{ strtolower($corp['nama_perusahaan'] . ' ' . $corp['id_perusahaan'] . ' ' . implode(' ', $corp['cities']) . ' ' . implode(' ', array_column($corp['pelanggan_list'], 'nomor_internet')) . ' ' . implode(' ', array_column($corp['pelanggan_list'], 'nama_pelanggan'))) }}">
                    
                    {{-- Card Header --}}
                    <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 cursor-pointer select-none bg-gradient-to-r from-white via-white to-gray-50/50 hover:to-indigo-50/30 transition-colors"
                         onclick="toggleCompanyCard('{{ $corp['key'] }}')">
                        
                        <div class="flex items-start gap-3.5 min-w-0">
                            {{-- Avatar Initials --}}
                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-extrabold text-sm flex-shrink-0 shadow-xs">
                                {{ $initials }}
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-gray-800 hover:text-indigo-600 transition-colors">
                                        {{ $corp['nama_perusahaan'] }}
                                    </h3>
                                    <span class="inline-flex items-center gap-1 font-mono text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md border border-gray-200">
                                        <i class="fa-solid fa-id-badge text-[9px] text-gray-400"></i>
                                        {{ $corp['id_perusahaan'] }}
                                    </span>
                                </div>

                                {{-- Locations pill --}}
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400">
                                    @if (!empty($corp['cities']))
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i>
                                            {{ implode(', ', array_slice($corp['cities'], 0, 3)) }}{{ count($corp['cities']) > 3 ? ' +' . (count($corp['cities']) - 3) . ' kota' : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Right Status Badges & Accordion Trigger --}}
                        <div class="flex items-center justify-between md:justify-end gap-3 flex-shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100">
                            {{-- Service counts pill --}}
                            <div class="flex items-center gap-1.5">
                                @if ($corp['count_aktif'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200" title="{{ $corp['count_aktif'] }} ID Pelanggan Aktif">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $corp['count_aktif'] }} Aktif
                                    </span>
                                @endif
                                @if ($corp['count_suspend'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="{{ $corp['count_suspend'] }} ID Pelanggan Suspend">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $corp['count_suspend'] }} Suspend
                                    </span>
                                @endif
                                @if ($corp['count_terminasi'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200" title="{{ $corp['count_terminasi'] }} ID Pelanggan Terminasi">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $corp['count_terminasi'] }} Terminasi
                                    </span>
                                @endif
                                @if ($corp['count_proses'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200" title="{{ $corp['count_proses'] }} ID Pelanggan Dalam Proses">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        {{ $corp['count_proses'] }} Proses
                                    </span>
                                @endif
                            </div>

                            {{-- Total Badge & Chevron --}}
                            <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $corp['total_pelanggan'] }} ID Pelanggan
                                </span>
                                <div id="chevron-{{ $corp['key'] }}" class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center text-xs transition-transform duration-200">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Body: ID Pelanggan List Table / Grid --}}
                    <div id="body-{{ $corp['key'] }}" class="company-card-body border-t border-gray-100 bg-gray-50/40 p-4 sm:p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-indigo-500"></i>
                                Daftar ID Pelanggan & Layanan Terkait ({{ count($corp['pelanggan_list']) }})
                            </span>
                            <span class="text-[11px] text-gray-400">Klik tombol salin untuk menyalin ID Pelanggan</span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-2xs">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                                        <th class="py-2.5 px-3.5">ID Pelanggan (Nomor Internet)</th>
                                        <th class="py-2.5 px-3.5">Nama Site / Lokasi</th>
                                        <th class="py-2.5 px-3.5">Kota / Lokasi</th>
                                        <th class="py-2.5 px-3.5 text-center">Status</th>
                                        <th class="py-2.5 px-3.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($corp['pelanggan_list'] as $pel)
                                        <tr class="hover:bg-indigo-50/20 transition-colors">
                                            {{-- ID Pelanggan with Copy Button --}}
                                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-mono font-bold text-gray-800 bg-gray-100/90 text-indigo-900 px-2 py-0.5 rounded border border-gray-200 text-xs">
                                                        {{ $pel['nomor_internet'] }}
                                                    </span>
                                                    @if ($pel['nomor_internet'] !== '-')
                                                        <button type="button"
                                                                onclick="copyToClipboard('{{ $pel['nomor_internet'] }}', this)"
                                                                title="Salin ID Pelanggan"
                                                                class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                                            <i class="fa-regular fa-copy text-[11px]"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-gray-400 block mt-0.5">Tgl: {{ $pel['date_create'] }}</span>
                                            </td>

                                            {{-- Site / Customer Name --}}
                                            <td class="py-2.5 px-3.5">
                                                <div class="font-semibold text-gray-800">
                                                    {{ $pel['nama_pelanggan'] }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 truncate max-w-xs" title="{{ $pel['alamat'] }}">
                                                    {{ $pel['alamat'] }}
                                                </div>
                                            </td>

                                            {{-- City --}}
                                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1 text-gray-600 font-medium">
                                                    <i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i>
                                                    {{ $pel['kota'] }}
                                                </span>
                                            </td>

                                            {{-- Status --}}
                                            <td class="py-2.5 px-3.5 whitespace-nowrap text-center">
                                                @if ($pel['status_sec'] === 'aktif')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($pel['status_sec'] === 'suspend')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Suspend
                                                    </span>
                                                @elseif ($pel['status_sec'] === 'terminasi')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        Terminasi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200" title="{{ $pel['desc_registrasi'] }}">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                        {{ $pel['status_label'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Action Button --}}
                                            <td class="py-2.5 px-3.5 whitespace-nowrap text-right">
                                                @if ($pel['nomor_internet'] !== '-')
                                                    <a href="{{ route('pelanggan.detail', $pel['nomor_internet']) }}"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-800 transition">
                                                        <span>Detail</span>
                                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 mx-auto mb-3">
                        <i class="fa-solid fa-building-circle-xmark text-lg"></i>
                    </div>
                    <h4 class="text-sm font-bold text-gray-700">Belum Ada Data Perusahaan</h4>
                    <p class="text-xs text-gray-400 mt-1">Data perusahaan dan ID pelanggan akan otomatis tampil setelah ada data pendaftaran registrasi.</p>
                </div>
            @endforelse
        </div>

        {{-- No Results from Search --}}
        <div id="companyNoResults" class="hidden bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg mx-auto mb-3">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-800">Tidak ada perusahaan yang cocok</h4>
            <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci pencarian atau filter status yang berbeda.</p>
            <button type="button" onclick="clearCompanySearch()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition">
                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Pencarian
            </button>
        </div>
    </div>

    {{-- Toast Notification for Copy --}}
    <div id="copyToast" class="fixed bottom-5 right-5 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2.5 text-xs font-medium border border-gray-800">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span id="copyToastText">ID Pelanggan berhasil disalin!</span>
        </div>
    </div>

    {{-- JavaScript for Company Visualization Interactivity --}}
    <script>
        function toggleCompanyCard(key) {
            const body = document.getElementById('body-' + key);
            const chevron = document.getElementById('chevron-' + key);
            if (!body) return;

            if (body.classList.contains('hidden')) {
                body.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

        function toggleAllCompanyCards(open) {
            const cards = document.querySelectorAll('.company-card');
            cards.forEach(card => {
                const key = card.getAttribute('data-company-key');
                const body = document.getElementById('body-' + key);
                const chevron = document.getElementById('chevron-' + key);
                if (!body) return;

                if (open) {
                    body.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                } else {
                    body.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            });
        }

        function focusCompanyCard(key) {
            const card = document.querySelector(`.company-card[data-company-key="${key}"]`);
            if (card) {
                const body = document.getElementById('body-' + key);
                const chevron = document.getElementById('chevron-' + key);
                if (body) {
                    body.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                }
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('ring-2', 'ring-indigo-500', 'shadow-lg');
                setTimeout(() => {
                    card.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg');
                }, 2000);
            }
        }

        function filterCompanyVisualization() {
            const input = document.getElementById('companySearchInput');
            const clearBtn = document.getElementById('clearCompanySearchBtn');
            const query = input ? input.value.trim().toLowerCase() : '';
            const cards = document.querySelectorAll('.company-card');
            const noResults = document.getElementById('companyNoResults');
            const countDisplay = document.getElementById('companyCountDisplay');

            if (clearBtn) {
                clearBtn.classList.toggle('hidden', query.length === 0);
            }

            let visibleCount = 0;

            cards.forEach(card => {
                const searchText = card.getAttribute('data-search-text') || '';

                // Check text query
                const matchesQuery = query === '' || searchText.includes(query);

                if (matchesQuery) {
                    card.classList.remove('hidden');
                    visibleCount++;
                    // If searching specifically, auto-expand matching card
                    if (query.length > 0) {
                        const key = card.getAttribute('data-company-key');
                        const body = document.getElementById('body-' + key);
                        const chevron = document.getElementById('chevron-' + key);
                        if (body) {
                            body.classList.remove('hidden');
                            if (chevron) chevron.classList.add('rotate-180');
                        }
                    }
                } else {
                    card.classList.add('hidden');
                }
            });

            if (noResults) {
                noResults.classList.toggle('hidden', visibleCount > 0);
            }

            if (countDisplay) {
                countDisplay.innerHTML = `Menampilkan <strong>${visibleCount}</strong> dari ${cards.length} perusahaan`;
            }
        }

        function clearCompanySearch() {
            const input = document.getElementById('companySearchInput');
            if (input) {
                input.value = '';
            }
            filterCompanyVisualization();
        }

        function copyToClipboard(text, btnElement) {
            if (!text || text === '-') return;

            navigator.clipboard.writeText(text).then(() => {
                // Toast
                const toast = document.getElementById('copyToast');
                const toastText = document.getElementById('copyToastText');
                if (toast && toastText) {
                    toastText.textContent = `ID Pelanggan "${text}" berhasil disalin!`;
                    toast.classList.remove('translate-y-20', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');

                    setTimeout(() => {
                        toast.classList.remove('translate-y-0', 'opacity-100');
                        toast.classList.add('translate-y-20', 'opacity-0');
                    }, 2500);
                }

                // Button visual feedback
                if (btnElement) {
                    const originalHtml = btnElement.innerHTML;
                    btnElement.innerHTML = '<i class="fa-solid fa-check text-emerald-500 text-[11px]"></i>';
                    setTimeout(() => {
                        btnElement.innerHTML = originalHtml;
                    }, 1500);
                }
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        // Initialize: Set all accordion bodies open or closed by default
        document.addEventListener('DOMContentLoaded', () => {
            // Keep top 3 open by default, collapse remaining for optimal performance and clean view
            const cards = document.querySelectorAll('.company-card');
            cards.forEach((card, index) => {
                const key = card.getAttribute('data-company-key');
                const body = document.getElementById('body-' + key);
                const chevron = document.getElementById('chevron-' + key);
                if (body && chevron) {
                    if (index < 3) {
                        body.classList.remove('hidden');
                        chevron.classList.add('rotate-180');
                    } else {
                        body.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                }
            });
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen pb-12">
        <!-- Background Grid Pattern -->
        <div class="pointer-events-none absolute inset-0 -z-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.04) 1px, transparent 0); background-size: 24px 24px;"></div>

        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-gray-600 font-medium">Broadband</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                            <i class="fa-solid fa-wifi text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Data Pelanggan Broadband</h1>
                            <p class="text-xs text-gray-500">Pemantauan data pelanggan broadband ritel seluruh wilayah (Read-Only)</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Semua Role (View-Only)
                    </span>
                    <a href="{{ route('broadband.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-white text-gray-600 hover:text-blue-600 border border-gray-200 hover:border-blue-300 shadow-2xs transition-all">
                        <i class="fa-solid fa-rotate text-[11px]"></i>
                        Refresh
                    </a>
                </div>
            </div>
        </div>

        @if (!$tableExists)
            <!-- Banner Jika Tabel Belum Ada -->
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-800 shadow-xs">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600 text-lg">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-base text-amber-900 mb-1">Tabel `trx_broadband` Belum Dibuat di Database</h3>
                        <p class="text-xs text-amber-700 mb-3 leading-relaxed">
                            Sistem telah menyiapkan struktur tabel untuk menu Broadband. Silakan klik tombol di bawah ini untuk membuat tabel otomatis, atau jalankan file SQL <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono font-bold">create_trx_broadband.sql</code> di database MySQL Anda.
                        </p>
                        <div class="flex items-center gap-3">
                            <a href="{{ url('/fix-database-schema') }}" target="_blank" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                Buat Tabel Otomatis Sekarang
                            </a>
                            <span class="text-xs text-amber-600">Setelah dibuat, silakan import data SQL Anda ke tabel <strong class="font-mono">trx_broadband</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab Ringkasan Status -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
            @php $currentStatus = request('status', 'semua'); @endphp

            <!-- Tab 1: Semua Pelanggan -->
            @php $isAll = empty($currentStatus) || $currentStatus === 'semua'; @endphp
            <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'semua'])) }}"
               class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAll ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-blue-300 hover:shadow-xs' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAll ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:scale-105' }} transition-transform">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider {{ $isAll ? 'text-blue-100' : 'text-gray-500' }}">Semua</p>
                        <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['semua'] ?? 0) }}</p>
                    </div>
                </div>
                @if($isAll)
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
                @endif
            </a>

            <!-- Tab 2: Aktif -->
            @php $isAktif = $currentStatus === 'aktif'; @endphp
            <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'aktif'])) }}"
               class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAktif ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-emerald-300 hover:shadow-xs' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAktif ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 group-hover:scale-105' }} transition-transform">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider {{ $isAktif ? 'text-emerald-100' : 'text-gray-500' }}">Aktif</p>
                        <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['aktif'] ?? 0) }}</p>
                    </div>
                </div>
                @if($isAktif)
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
                @endif
            </a>

            <!-- Tab 3: Suspend -->
            @php $isSuspend = $currentStatus === 'suspend'; @endphp
            <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'suspend'])) }}"
               class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isSuspend ? 'bg-amber-600 text-white border-amber-600 shadow-md shadow-amber-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-amber-300 hover:shadow-xs' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isSuspend ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 group-hover:scale-105' }} transition-transform">
                        <i class="fa-solid fa-pause"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider {{ $isSuspend ? 'text-amber-100' : 'text-gray-500' }}">Suspend</p>
                        <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['suspend'] ?? 0) }}</p>
                    </div>
                </div>
                @if($isSuspend)
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
                @endif
            </a>

            <!-- Tab 4: Terminasi -->
            @php $isTermin = $currentStatus === 'terminasi'; @endphp
            <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'terminasi'])) }}"
               class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isTermin ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-rose-300 hover:shadow-xs' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isTermin ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-600 group-hover:scale-105' }} transition-transform">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider {{ $isTermin ? 'text-rose-100' : 'text-gray-500' }}">Terminasi</p>
                        <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['terminasi'] ?? 0) }}</p>
                    </div>
                </div>
                @if($isTermin)
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
                @endif
            </a>

            <!-- Tab 5: Sudah Onboarding / Login -->
            @php $isOnboard = $currentStatus === 'onboarding'; @endphp
            <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'onboarding'])) }}"
               class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isOnboard ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-indigo-300 hover:shadow-xs' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isOnboard ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-600 group-hover:scale-105' }} transition-transform">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider {{ $isOnboard ? 'text-indigo-100' : 'text-gray-500' }}">Onboard/Login</p>
                        <p class="text-lg font-bold leading-none mt-0.5">{{ number_format($statusCounts['is_login'] ?? 0) }}</p>
                    </div>
                </div>
                @if($isOnboard)
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md font-bold text-white uppercase tracking-wider">Dipilih</span>
                @endif
            </a>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-4 mb-6">
            <form method="GET" action="{{ route('broadband.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <input type="hidden" name="status" value="{{ request('status', 'semua') }}">

                <!-- Search Keyword -->
                <div class="lg:col-span-2 relative">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cari Pelanggan</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="No. Internet, Nama, NIK, Alamat, OLT..."
                               class="w-full pl-9 pr-3 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-700 placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <!-- Filter POP -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Filter POP</label>
                    <select name="pop" class="w-full px-3 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        <option value="">Semua POP</option>
                        @foreach ($popList as $popItem)
                            <option value="{{ $popItem }}" {{ request('pop') == $popItem ? 'selected' : '' }}>
                                {{ $popItem }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Bandwidth -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Bandwidth</label>
                    <select name="bandwith" class="w-full px-3 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        <option value="">Semua Paket</option>
                        @foreach ($bandwidthList as $bwItem)
                            <option value="{{ $bwItem }}" {{ request('bandwith') == $bwItem ? 'selected' : '' }}>
                                {{ $bwItem }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Group Layanan -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Group Layanan</label>
                    <select name="group" class="w-full px-3 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        <option value="">Semua Group</option>
                        @foreach ($groupList as $grp)
                            <option value="{{ $grp }}" {{ request('group') == $grp ? 'selected' : '' }}>
                                {{ $grp }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-filter text-[10px]"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->hasAny(['q', 'pop', 'bandwith', 'group', 'mitra']))
                        <a href="{{ route('broadband.index', ['status' => request('status', 'semua')]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 py-2 px-3 rounded-xl text-xs flex items-center justify-center transition-all" title="Reset Filter">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Daftar Pelanggan Broadband</span>
                    @if($customers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            Total: {{ number_format($customers->total()) }}
                        </span>
                    @endif
                </div>

                <!-- Per Page Selector -->
                <form method="GET" action="{{ route('broadband.index') }}" class="flex items-center gap-2">
                    @foreach (request()->except('per_page', 'page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <label class="text-[11px] text-gray-500 font-medium">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg text-xs px-2 py-1 text-gray-700 focus:outline-none focus:border-blue-500">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">No. Internet</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Paket & Bandwidth</th>
                            <th class="py-3 px-4">POP / OLT</th>
                            <th class="py-3 px-4">Alamat Pasang</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @if ($tableExists && $customers->count() > 0)
                            @foreach ($customers as $c)
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <!-- No Internet -->
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-hashtag text-[10px] text-gray-400"></i>
                                            <span>{{ $c->nomor_internet }}</span>
                                        </div>
                                        @if(!empty($c->date_create))
                                            <span class="block text-[10px] font-sans font-normal text-gray-400 mt-0.5">
                                                {{ substr($c->date_create, 0, 10) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Pelanggan & NIK -->
                                    <td class="py-3.5 px-4 min-w-[180px]">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-xs flex-shrink-0 font-bold group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                                                {{ strtoupper(substr($c->nama_pelanggan ?: 'P', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-gray-900 truncate" title="{{ $c->nama_pelanggan }}">
                                                    {{ $c->nama_pelanggan ?: '-' }}
                                                </p>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    @if(!empty($c->nik_penduduk))
                                                        <span class="text-[10px] text-gray-500 font-mono" title="NIK Penduduk">
                                                            <i class="fa-regular fa-id-card text-[9px] mr-0.5 text-gray-400"></i>{{ $c->nik_penduduk }}
                                                        </span>
                                                    @endif
                                                    @if($c->is_login == 1)
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Sudah pernah login/onboarding aplikasi">
                                                            <i class="fa-solid fa-mobile-screen text-[8px] mr-1"></i>Onboard
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-medium bg-gray-100 text-gray-500" title="Belum pernah login/onboarding">
                                                            Belum Onboard
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Paket & Bandwidth -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span class="font-bold text-gray-800">{{ $c->paket_label }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 flex items-center gap-2 mt-0.5">
                                            @if(!empty($c->media_akses))
                                                <span>{{ $c->media_akses }}</span>
                                            @endif
                                            @if(!empty($c->group_layanan))
                                                <span class="bg-gray-100 px-1 rounded">{{ $c->group_layanan }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- POP / OLT -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-semibold text-gray-800">
                                            <i class="fa-solid fa-tower-broadcast text-[10px] text-gray-400 mr-1"></i>
                                            {{ $c->kode_pop ?: '-' }}
                                        </p>
                                        @if(!empty($c->olt) || !empty($c->index_olt))
                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                OLT: {{ $c->olt ?: '-' }} {{ $c->index_olt ? '('.$c->index_olt.')' : '' }}
                                            </p>
                                        @endif
                                    </td>

                                    <!-- Alamat Pasang -->
                                    <td class="py-3.5 px-4 max-w-[260px]">
                                        <p class="text-gray-700 line-clamp-2" title="{{ $c->alamat_lengkap }}">
                                            {{ $c->alamat_lengkap }}
                                        </p>
                                        @if(!empty($c->maps_url))
                                            <a href="{{ $c->maps_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-blue-600 hover:text-blue-800 font-medium mt-0.5">
                                                <i class="fa-solid fa-location-dot text-[9px] text-rose-500"></i>
                                                <span>Buka Maps</span>
                                            </a>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $c->status_class }}">
                                            <i class="fa-solid {{ $c->status_icon }} text-[9px]"></i>
                                            <span>{{ $c->status_text }}</span>
                                        </span>
                                    </td>

                                    <!-- Aksi (Lihat Profil) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Tombol Quick View Modal -->
                                            <button type="button" 
                                                    onclick="openBroadbandModal('{{ $c->nomor_internet }}')"
                                                    class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                                                    title="Lihat Profil Cepat">
                                                <i class="fa-solid fa-eye text-[11px]"></i>
                                                <span>Profil</span>
                                            </button>

                                            <!-- Link Ke Halaman Lengkap -->
                                            <a href="{{ route('broadband.detail', $c->nomor_internet) }}"
                                               class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition-all hover:text-blue-600"
                                               title="Buka Halaman Penuh">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="max-w-md mx-auto flex flex-col items-center">
                                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl mb-3 shadow-xs">
                                            <i class="fa-solid fa-wifi"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-800 mb-1">Belum Ada Data Pelanggan Broadband</h3>
                                        <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                                            @if(!$tableExists)
                                                Tabel database <code class="bg-gray-100 px-1 py-0.5 rounded font-mono">trx_broadband</code> belum dibuat.
                                            @else
                                                Tabel <code class="bg-gray-100 px-1 py-0.5 rounded font-mono">trx_broadband</code> sudah siap di database. Masukkan / import data SQL dump Anda langsung ke tabel ini dan data akan otomatis tampil di sini.
                                            @endif
                                        </p>
                                        @if(!$tableExists)
                                            <a href="{{ url('/fix-database-schema') }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-xl transition-all shadow-sm">
                                                <i class="fa-solid fa-database mr-1"></i> Buat Tabel trx_broadband
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($tableExists && $customers instanceof \Illuminate\Pagination\LengthAwarePaginator && $customers->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-white">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP QUICK VIEW PROFIL BROADBAND (Interactive Slide/Modal)        -->
    <!-- ========================================================================= -->
    <div id="broadbandDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeBroadbandModal()"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-100">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 px-6 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-lg shadow-inner">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider" id="m_status_badge">
                                    Broadband
                                </span>
                                <span class="text-white/80 font-mono text-xs font-semibold" id="m_nomor_internet">
                                    -
                                </span>
                            </div>
                            <h2 class="text-base font-bold text-white mt-0.5" id="m_nama_pelanggan">
                                Profil Pelanggan Broadband
                            </h2>
                        </div>
                    </div>
                    <button type="button" onclick="closeBroadbandModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto" id="m_modal_content">
                    <!-- Loading State -->
                    <div id="m_loading" class="py-12 flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-500 mb-2"></i>
                        <span class="text-xs">Memuat data profil...</span>
                    </div>

                    <!-- Content State -->
                    <div id="m_data_container" class="hidden space-y-5">
                        <!-- Seksi 1: Data Identitas Pelanggan -->
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-id-card text-blue-500"></i>
                                Informasi Identitas & Pelanggan
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Nama Pelanggan</span>
                                    <span class="font-bold text-gray-800 text-sm" id="m_val_nama">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">NIK Penduduk</span>
                                    <span class="font-mono text-gray-700 font-semibold" id="m_val_nik">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Onboarding App</span>
                                    <span id="m_val_onboarding" class="font-semibold">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Registrasi</span>
                                    <span class="font-semibold text-gray-700" id="m_val_status_reg">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tanggal Dibuat</span>
                                    <span class="text-gray-700" id="m_val_tgl_buat">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">User Create</span>
                                    <span class="text-gray-700" id="m_val_user_create">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Seksi 2: Layanan, Bandwidth & Jaringan -->
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-network-wired text-indigo-500"></i>
                                Paket & Infrastruktur Jaringan
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Paket / Bandwidth</span>
                                    <span class="font-bold text-blue-700" id="m_val_paket">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Media Akses</span>
                                    <span class="text-gray-800 font-semibold" id="m_val_media">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">POP</span>
                                    <span class="text-gray-800 font-semibold" id="m_val_pop">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Group Layanan</span>
                                    <span class="text-gray-800 font-semibold" id="m_val_group">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">OLT</span>
                                    <span class="text-gray-800" id="m_val_olt">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Index OLT</span>
                                    <span class="text-gray-800" id="m_val_index_olt">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">ONT US</span>
                                    <span class="text-gray-800 font-mono" id="m_val_ont_us">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">ONT PS</span>
                                    <span class="text-gray-800 font-mono" id="m_val_ont_ps">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Seksi 3: Alamat Pemasangan -->
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                Alamat & Lokasi Pemasangan
                            </h3>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Alamat Lengkap</span>
                                    <p class="font-medium text-gray-800" id="m_val_alamat">-</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-100">
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Jenis Bangunan</span>
                                        <span class="text-gray-800" id="m_val_jenis_bangunan">-</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Koordinat (Lon / Lat)</span>
                                        <span class="font-mono text-gray-700 text-[11px]" id="m_val_lon_lat">-</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tautan Maps</span>
                                        <div id="m_val_maps_link">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seksi 4: Billing & Administrasi -->
                        <div class="bg-gray-50/70 border border-gray-100 rounded-xl p-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-invoice-dollar text-emerald-500"></i>
                                Billing & Keuangan
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Harga Paket</span>
                                    <span class="font-bold text-emerald-700" id="m_val_harga">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">PPN</span>
                                    <span class="text-gray-800" id="m_val_ppn">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Potongan</span>
                                    <span class="text-gray-800" id="m_val_potongan">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Periode Billing</span>
                                    <span class="text-gray-800 font-semibold" id="m_val_periode">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Last Billing</span>
                                    <span class="text-gray-800 font-mono" id="m_val_last_billing">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Sales</span>
                                    <span class="text-gray-800 font-semibold" id="m_val_sales">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Mitra</span>
                                    <span class="text-gray-800" id="m_val_mitra">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Catatan Request</span>
                                    <span class="text-gray-800 truncate block" id="m_val_note">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-3.5 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[11px] text-gray-400">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Mode Read-Only (Hanya Lihat)
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeBroadbandModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-semibold transition-all">
                            Tutup
                        </button>
                        <a href="#" id="m_btn_full_page" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shadow-sm">
                            <span>Buka Halaman Lengkap</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Interaksi Modal -->
    <script>
        function openBroadbandModal(nomorInternet) {
            const modal = document.getElementById('broadbandDetailModal');
            const loading = document.getElementById('m_loading');
            const container = document.getElementById('m_data_container');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            container.classList.add('hidden');

            document.getElementById('m_nomor_internet').innerText = '#' + nomorInternet;
            document.getElementById('m_nama_pelanggan').innerText = 'Memuat data...';

            fetch(`/broadband/${encodeURIComponent(nomorInternet)}/modal`)
                .then(res => {
                    if (!res.ok) throw new Error('Data tidak ditemukan');
                    return res.json();
                })
                .then(res => {
                    const d = res.data;
                    loading.classList.add('hidden');
                    container.classList.remove('hidden');

                    document.getElementById('m_nomor_internet').innerText = '#' + (d.nomor_internet || '-');
                    document.getElementById('m_nama_pelanggan').innerText = d.nama_pelanggan || 'Pelanggan Broadband';
                    document.getElementById('m_status_badge').innerText = d.status_text || 'Aktif';

                    // Seksi 1
                    document.getElementById('m_val_nama').innerText = d.nama_pelanggan || '-';
                    document.getElementById('m_val_nik').innerText = d.nik_penduduk || '-';
                    document.getElementById('m_val_onboarding').innerHTML = d.is_login == 1 
                        ? '<span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i>Sudah Onboarding / Login</span>'
                        : '<span class="text-gray-400"><i class="fa-regular fa-circle mr-1"></i>Belum Pernah Login</span>';
                    document.getElementById('m_val_status_reg').innerText = d.desc_registrasi || d.status_reg || '-';
                    document.getElementById('m_val_tgl_buat').innerText = d.date_create ? d.date_create.substring(0, 19) : '-';
                    document.getElementById('m_val_user_create').innerText = d.user_create || '-';

                    // Seksi 2
                    document.getElementById('m_val_paket').innerText = d.paket_label || '-';
                    document.getElementById('m_val_media').innerText = d.media_akses || '-';
                    document.getElementById('m_val_pop').innerText = (d.nama_pop ? d.nama_pop + ' (' + d.kode_pop + ')' : d.kode_pop) || '-';
                    document.getElementById('m_val_group').innerText = d.group_layanan || '-';
                    document.getElementById('m_val_olt').innerText = d.olt || '-';
                    document.getElementById('m_val_index_olt').innerText = d.index_olt || '-';
                    document.getElementById('m_val_ont_us').innerText = d.ont_us || '-';
                    document.getElementById('m_val_ont_ps').innerText = d.ont_ps || '-';

                    // Seksi 3
                    document.getElementById('m_val_alamat').innerText = d.alamat_lengkap || '-';
                    document.getElementById('m_val_jenis_bangunan').innerText = d.jenis_bangunan || '-';
                    document.getElementById('m_val_lon_lat').innerText = d.lon_lat || '-';
                    document.getElementById('m_val_maps_link').innerHTML = d.maps_url 
                        ? `<a href="${d.maps_url}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center gap-1 font-semibold"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka Google Maps</a>`
                        : '-';

                    // Seksi 4
                    document.getElementById('m_val_harga').innerText = d.harga_formatted || '-';
                    document.getElementById('m_val_ppn').innerText = (d.ppn ? d.ppn + '%' : '') + (d.ppn_nom_formatted !== '-' ? ' (' + d.ppn_nom_formatted + ')' : '');
                    document.getElementById('m_val_potongan').innerText = d.potongan_formatted + (d.potongan_note ? ' - ' + d.potongan_note : '');
                    document.getElementById('m_val_periode').innerText = (d.periode_billing ? d.periode_billing + ' Bulan' : '-');
                    document.getElementById('m_val_last_billing').innerText = (d.last_month_billing ? d.last_month_billing + '/' + d.last_year_billing : '-');
                    document.getElementById('m_val_sales').innerText = d.nama_sales || '-';
                    document.getElementById('m_val_mitra').innerText = d.mitra || '-';
                    document.getElementById('m_val_note').innerText = d.note_request || '-';

                    // Tombol Halaman Lengkap
                    document.getElementById('m_btn_full_page').href = `/broadband/${encodeURIComponent(d.nomor_internet)}`;
                })
                .catch(err => {
                    loading.innerHTML = `<div class="text-rose-500"><i class="fa-solid fa-circle-exclamation text-xl mb-1"></i><p>${err.message}</p></div>`;
                });
        }

        function closeBroadbandModal() {
            document.getElementById('broadbandDetailModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBroadbandModal();
            }
        });
    </script>
@endsection

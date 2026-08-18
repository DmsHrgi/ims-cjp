@extends('layouts.app')

@section('content')
    @php /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */ @endphp
    @php /** @var \Illuminate\Support\Collection $bangunan */ @endphp
    @php /** @var \Illuminate\Support\Collection $kategori */ @endphp
    @php /** @var \Illuminate\Support\Collection $groupLayanan */ @endphp
    @php /** @var \Illuminate\Support\Collection $sales */ @endphp
    @php /** @var \Illuminate\Support\Collection $provinsi */ @endphp

    <style>
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
    </style>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <nav class="text-sm text-gray-500">
            <a href="{{ route('pendaftaran') }}" class="hover:text-blue-600 transition-colors">IMS</a>
            <span class="mx-2 text-gray-300">></span>
            <span class="text-gray-700 font-medium">Registration</span>
        </nav>
        @if(!($isNoc ?? false))
        <button onclick="openModal()" class="mt-3 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <i class="fa-solid fa-user-plus"></i> Registrasi Baru
        </button>
        @endif
    </div>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
            <div>
                <p class="font-semibold">Registrasi Berhasil!</p>
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

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('pendaftaran') }}" id="filterForm" class="flex flex-wrap items-center gap-4">
            @if(request('entries'))
                <input type="hidden" name="entries" value="{{ request('entries') }}">
            @endif
            <div class="relative">
                <select name="layanan" onchange="this.form.submit()" class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer min-w-[160px]">
                    <option value="">SEMUA LAYANAN</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->kode_kategori_bandwith }}" {{ request('layanan') == $k->kode_kategori_bandwith ? 'selected' : '' }}>{{ strtoupper($k->nama_kategori_bandwith) }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex-1 min-w-[150px] relative">
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="NAMA / NO INTERNET" class="w-full bg-transparent border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 px-3 text-sm uppercase tracking-wide outline-none transition-colors placeholder-gray-400">
            </div>
            <div class="flex-1 min-w-[150px] relative">
                <input type="text" name="alamat" value="{{ request('alamat') }}" placeholder="ALAMAT / LOKASI" class="w-full bg-transparent border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 px-3 text-sm uppercase tracking-wide outline-none transition-colors placeholder-gray-400">
            </div>
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer min-w-[160px]">
                    <option value="">SEMUA STATUS</option>
                    @foreach($statusList as $st)
                        <option value="{{ $st->status_reg }}" {{ request('status') == $st->status_reg ? 'selected' : '' }}>{{ strtoupper($st->desc_registrasi ?: $st->status_reg) }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="relative">
                <select name="wilayah" onchange="this.form.submit()" class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer min-w-[160px]">
                    <option value="">SEMUA WILAYAH</option>
                    @foreach($wilayahList as $w)
                        <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ strtoupper($w) }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>Cari
                </button>
                <a href="{{ route('pendaftaran') }}" class="bg-pink-400 hover:bg-pink-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-pink-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <i class="fa-solid fa-rotate"></i>Reset
                </a>
                <a href="{{ route('pendaftaran.export', request()->query()) }}" class="bg-amber-400 hover:bg-amber-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-amber-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <i class="fa-solid fa-file-export"></i>Export
                </a>
            </div>
        </form>
    </div>

    <!-- DataTable Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select onchange="changeEntries(this.value)" class="bg-white border border-gray-200 rounded px-2 py-1 text-sm outline-none focus:border-blue-400">
                    <option value="10" {{ request('entries', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>entries</span>
            </div>
            @if (isset($rows) && $rows->total()) @include('partials.pagination', ['rows' => $rows]) @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200">
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Pelanggan</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-48">Nama Perusahaan</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Lokasi Pemasangan</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-48">Status</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-44">Tanggal SO</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows ?? [] as $r)
                        <tr class="group odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="relative py-4 px-4 align-top">
                                <span class="absolute left-0 top-0 h-full w-1 bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></span>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block font-bold text-blue-600 text-sm hover:underline">{{ $r->nomor_internet }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-sm font-semibold text-gray-800 underline decoration-gray-300 hover:decoration-blue-500 hover:text-blue-700 mt-1">{{ $r->nama_pelanggan ?: '-' }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs text-blue-600 hover:underline mt-1">{{ $r->nama_kategori_bandwith }} {{ $r->nominal_bandwith }} Mbps</a>
                            </td>
                            <td class="py-4 px-4 align-top"><p class="text-sm font-semibold text-gray-700">{{ $r->nama_perusahaan ?: ($r->nama_pelanggan ?: '-') }}</p></td>
                            <td class="py-4 px-4 align-top">
                                <p class="text-sm font-bold text-gray-800">{{ $r->jenis_bangunan ?: '-' }}</p>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $r->alamat_p ?: ($r->alamat_pasang ?: '-') }}</p>
                            </td>
                              <td class="py-4 px-4 align-top">
                                  <div class="space-y-1.5">
                                       @php
                                           $isReportDone = !empty($r->instalasi_date_finish) || (!empty($r->instalasi_team) && !empty($r->instalasi_note_finish)) || $r->status_reg == '15' || str_contains(strtoupper($r->desc_registrasi ?? ''), 'SELESAI INSTALASI');
                                           $isAktivasiScheduled = !empty($r->aktivasi_date_start);
                                           $isAktivasiDone = !empty($r->aktivasi_date_finish) || $r->status_reg == '16' || str_contains(strtoupper($r->desc_registrasi ?? ''), 'SELESAI AKTIVASI');
                                           
                                           $stepAktivasiScheduled = $isAktivasiScheduled && !$isAktivasiDone;
                                           $step5_instalasiDone = $isReportDone && !$isAktivasiScheduled && !$isAktivasiDone;
                                           $step1_dataInput = empty($r->survey_date_start) && empty($r->survey_date_finish) && empty($r->instalasi_date_start) && !$step5_instalasiDone && !$isAktivasiScheduled && !$isAktivasiDone;
                                           $step2_surveyScheduled = !empty($r->survey_date_start) && empty($r->survey_date_finish) && empty($r->instalasi_date_start) && !$step5_instalasiDone && !$isAktivasiScheduled && !$isAktivasiDone;
                                           $step3_surveyDone = !empty($r->survey_date_finish) && empty($r->instalasi_date_start) && !$step5_instalasiDone && !$isAktivasiScheduled && !$isAktivasiDone;
                                           $step4_instalasiScheduled = !empty($r->instalasi_date_start) && empty($r->instalasi_date_finish) && !$step5_instalasiDone && !$isAktivasiScheduled && !$isAktivasiDone;
                                       @endphp

                                       @if($step1_dataInput)
                                           <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-medium px-3 py-1 rounded-full">Data Input</span>
                                       @elseif($step2_surveyScheduled)
                                           <div class="space-y-1">
                                               <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-md leading-tight">
                                                   Jadwal Survey Terbit<br>
                                                   <span class="text-[11px] font-normal">{{ \Carbon\Carbon::parse($r->survey_date_start)->format('d M Y') }} {{ $r->survey_time ?? '' }}WIB</span>
                                               </span>
                                               <div>
                                                   <span class="inline-block bg-rose-50 border border-rose-100 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">POSTING SURVEY</span>
                                               </div>
                                           </div>
                                       @elseif($step3_surveyDone)
                                           <div class="space-y-1">
                                               <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-md">Selesai Survey</span>
                                               @if(!empty($r->survey_note_finish) && strtoupper(trim($r->survey_note_finish)) !== 'TESTING')
                                                   <div>
                                                       <span class="inline-block bg-rose-50 border border-rose-100 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">{{ strtoupper($r->survey_note_finish) }}</span>
                                                   </div>
                                               @endif
                                           </div>
                                       @elseif($step4_instalasiScheduled)
                                           <div class="space-y-1">
                                               <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-md leading-tight">
                                                   Jadwal Instalasi Terbit<br>
                                                   <span class="text-[11px] font-normal">{{ \Carbon\Carbon::parse($r->instalasi_date_start)->format('d M Y') }} {{ $r->instalasi_time ?? '' }}WIB</span>
                                               </span>
                                               <div>
                                                   <span class="inline-block bg-rose-50 border border-rose-100 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">POSTING INSTALASI</span>
                                               </div>
                                           </div>
                                       @elseif($stepAktivasiScheduled)
                                           <div class="space-y-1.5">
                                               <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-md leading-tight">
                                                   Jadwal Aktivasi Terbit<br>
                                                   <span class="text-[11px] font-normal">{{ \Carbon\Carbon::parse($r->aktivasi_date_start)->format('d M Y') }} {{ $r->aktivasi_time ?? '' }}WIB</span>
                                               </span>
                                               <div>
                                                   <span class="inline-block bg-rose-50 border border-rose-100 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">POSTING AKTIVASI</span>
                                               </div>
                                               <p class="text-[11px] text-gray-500 underline decoration-gray-300">
                                                   Updated {{ \Carbon\Carbon::parse($r->date_update ?: $r->date_create)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                               </p>
                                           </div>
                                       @elseif($isAktivasiDone)
                                           <div class="space-y-1.5">
                                               <span class="inline-block bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-md">Selesai Aktivasi</span>
                                               <p class="text-[11px] text-gray-500 underline decoration-gray-300">
                                                   Updated {{ \Carbon\Carbon::parse($r->date_update ?: $r->date_create)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                               </p>
                                           </div>
                                       @elseif($step5_instalasiDone)
                                           <div class="space-y-1.5">
                                               <span class="inline-block bg-indigo-50 border border-indigo-100/80 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-md">Selesai Instalasi</span>
                                               <p class="text-[11px] text-gray-500 underline decoration-gray-300">
                                                   Updated {{ \Carbon\Carbon::parse($r->date_update ?: $r->date_create)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                               </p>
                                           </div>
                                       @else
                                           @if($r->desc_registrasi)
                                               @php
                                                   $statusBg = 'bg-blue-100 text-blue-700';
                                                   if (str_contains(strtoupper($r->desc_registrasi), 'AKTIVASI')) {
                                                       $statusBg = 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300';
                                                   } elseif (str_contains(strtoupper($r->desc_registrasi), 'SURVEY')) {
                                                       $statusBg = 'bg-amber-100 text-amber-800 font-bold border border-amber-300';
                                                   }
                                               @endphp
                                               <span class="inline-block {{ $statusBg }} text-xs font-semibold px-2.5 py-1 rounded-lg">{{ $r->desc_registrasi }}</span>
                                           @endif
                                       @endif

                                       @if(!$step5_instalasiDone && !$stepAktivasiScheduled && !$isAktivasiDone)
                                           <p class="text-[11px] text-gray-500 underline decoration-gray-300 mt-2">
                                               Updated {{ \Carbon\Carbon::parse($r->date_update ?: $r->date_create)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                           </p>
                                       @endif
                                   </div>
                              </td>
                              <td class="py-4 px-4 align-top">
                                  <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($r->date_create)->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                                  <p class="text-sm font-semibold text-gray-800 mt-1">
                                      @php
                                          $uName = $r->user_create;
                                          if (empty($uName) || str_contains($uName, '@')) {
                                              $userDb = \Illuminate\Support\Facades\DB::table('view_pengguna')->where('username', $uName)->first();
                                              if ($userDb && !empty($userDb->nama_karyawan)) {
                                                  $uName = $userDb->nama_karyawan;
                                              }
                                          }
                                      @endphp
                                      {{ strtoupper($uName ?: 'SYSTEM') }}
                                  </p>
                                  <p class="text-xs text-gray-500 mt-0.5 font-medium">SALES : {{ strtoupper($r->nama_sales ?: '-') }}</p>
                              </td>
                             <td class="py-4 px-4 align-top">
                                 <div class="flex flex-col gap-2">
                                      @php
                                          $u = session('user', []);
                                          $userLevel = strtoupper($u['level'] ?? '');
                                          $kodeLevel = $u['kode_level'] ?? '';
                                          $levelNum  = $u['level_num'] ?? null;
                                          $isAdminUser = ($isAdmin ?? false) || ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');
                                          $isFinanceUser = ($isFinance ?? false) || ($userLevel === 'FINANCE' || $kodeLevel === 'lv33501' || $levelNum == 6 || str_contains($userLevel, 'FINANCE') || str_contains($userLevel, 'KEUANGAN') || str_contains($userLevel, 'KASIR'));
                                      @endphp
                                      @if($isAdminUser)
                                          {{-- Role Admin: Hanya Edit dan Hapus --}}
                                          <div class="flex flex-col items-start gap-1.5 text-xs font-medium whitespace-nowrap">
                                              <a href="{{ route('pendaftaran.edit', $r->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-pen-to-square text-emerald-500"></i>
                                                  <span>Edit</span>
                                              </a>
                                              <button type="button" onclick="konfirmasiHapus('{{ $r->nomor_internet }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-trash-can text-rose-500"></i>
                                                  <span>Hapus</span>
                                              </button>
                                          </div>
                                      @elseif($isFinanceUser)
                                          {{-- Role Finance: Hanya Batal Pasang, Edit, Hapus --}}
                                          <div class="flex flex-col items-start gap-1.5 text-xs font-medium whitespace-nowrap">
                                              <form method="POST" action="{{ route('pendaftaran.batal-pasang', $r->nomor_internet) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')" class="inline">
                                                  @csrf
                                                  @method('PUT')
                                                  <button type="submit" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-xmark text-slate-400"></i>
                                                      <span>Batal Pasang</span>
                                                  </button>
                                              </form>
                                              <a href="{{ route('pendaftaran.edit', $r->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-pen-to-square text-emerald-500"></i>
                                                  <span>Edit</span>
                                              </a>
                                              <button type="button" onclick="konfirmasiHapus('{{ $r->nomor_internet }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-trash-can text-rose-500"></i>
                                                  <span>Hapus</span>
                                              </button>
                                          </div>
                                      @elseif($isNoc ?? false)
                                         {{-- Role NOC Aksi --}}
                                         <div class="flex flex-col items-start gap-1.5 text-xs font-medium whitespace-nowrap">
                                             <form method="POST" action="{{ route('pendaftaran.batal-pasang', $r->nomor_internet) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')" class="inline">
                                                 @csrf
                                                 @method('PUT')
                                                 <button type="submit" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                     <i class="fa-solid fa-xmark text-slate-400"></i>
                                                     <span>Batal Pasang</span>
                                                 </button>
                                             </form>

                                             {{-- 2. NOC Workflow --}}
                                             @if(empty($r->aktivasi_date_start))
                                                 @if($isReportDone)
                                                     @php
                                                         $rItems = $installedItems->get($r->nomor_internet, collect());
                                                     @endphp
                                                     <button type="button" onclick="openAktivasiModal(
                                                             '{{ $r->nomor_internet }}',
                                                             '{{ addslashes($r->nama_pelanggan) }}',
                                                             '{{ $r->aktivasi_date_start ?? '' }}',
                                                             '{{ $r->aktivasi_time ?? '' }}',
                                                             '{{ addslashes($r->aktivasi_team ?? '') }}',
                                                             '{{ $r->kode_pop ?? '' }}',
                                                             '{{ addslashes($r->media_akses ?? '') }}',
                                                             '{{ addslashes($r->index_olt ?? '') }}',
                                                             '{{ addslashes($r->aktivasi_note ?? '') }}',
                                                             {{ json_encode($rItems) }}
                                                         )" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                         <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                         <span>Jadwal Aktivasi</span>
                                                     </button>
                                                 @else
                                                     <span class="inline-flex items-center gap-1 text-[11px] text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200/80 whitespace-nowrap" title="Jadwal Aktivasi terbuka setelah proses Teknik / Report Instalasi Selesai">
                                                         <i class="fa-solid fa-clock text-[10px]"></i>
                                                         <span>Menunggu Report Instalasi</span>
                                                     </span>
                                                 @endif
                                             @elseif(!empty($r->aktivasi_date_start) && empty($r->aktivasi_date_finish))
                                                 @php
                                                     $rItems = $installedItems->get($r->nomor_internet, collect());
                                                 @endphp
                                                 <button type="button" onclick="openReportAktivasiModal(
                                                         '{{ $r->nomor_internet }}',
                                                         '{{ addslashes($r->nama_pelanggan) }}',
                                                         '{{ $r->aktivasi_date_finish ?? '' }}',
                                                         '{{ addslashes($r->aktivasi_note_finish ?? '') }}',
                                                         '{{ addslashes($r->aktivasi_team ?? '') }}',
                                                         '{{ $r->kode_pop ?? '' }}',
                                                         '{{ addslashes($r->media_akses ?? '') }}',
                                                         '{{ addslashes($r->index_olt ?? '') }}',
                                                         {{ json_encode($rItems) }}
                                                     )" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                     <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                     <span>Report Aktivasi</span>
                                                 </button>
                                             @else
                                                 {{-- Selesai Aktivasi: Tombol Report Aktivasi HILANG --}}
                                             @endif

                                             {{-- 3. Edit --}}
                                             <a href="{{ route('pendaftaran.edit', $r->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors whitespace-nowrap">
                                                 <i class="fa-solid fa-pen-to-square text-emerald-500"></i>
                                                 <span>Edit</span>
                                             </a>
                                         </div>
                                     @else
                                          {{-- Role Teknik / General Admin Aksi --}}
                                          <div class="flex flex-col items-start gap-1.5 text-xs font-medium whitespace-nowrap">
                                              {{-- 1. Batal Pasang --}}
                                              <form method="POST" action="{{ route('pendaftaran.batal-pasang', $r->nomor_internet) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')" class="inline">
                                                  @csrf
                                                  @method('PUT')
                                                  <button type="submit" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-xmark text-slate-400"></i>
                                                      <span>Batal Pasang</span>
                                                  </button>
                                              </form>

                                              {{-- 2. Workflow Actions --}}
                                              @if($step1_dataInput)
                                                  <button type="button" onclick="openSurveyModal(
                                                          '{{ $r->nomor_internet }}',
                                                          '{{ addslashes($r->nama_pelanggan) }}',
                                                          '{{ $r->survey_date_start ?? '' }}',
                                                          '{{ $r->survey_time ?? '' }}',
                                                          '{{ addslashes($r->survey_note ?? '') }}',
                                                          '{{ addslashes($r->survey_team ?? '') }}'
                                                      )" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                      <span>Jadwal Survey</span>
                                                  </button>
                                              @elseif($step2_surveyScheduled)
                                                  <button type="button" onclick="openReportSurveyModal(
                                                          '{{ $r->nomor_internet }}',
                                                          '{{ addslashes($r->nama_pelanggan) }}',
                                                          '{{ $r->survey_date_finish ?? '' }}',
                                                          '{{ addslashes($r->survey_note_finish ?? '') }}',
                                                          '{{ addslashes($r->survey_team ?? '') }}'
                                                      )" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                      <span>Report Survey</span>
                                                  </button>
                                              @elseif($step3_surveyDone)
                                                  @php
                                                      $rItems = $installedItems->get($r->nomor_internet, collect());
                                                  @endphp
                                                  <button type="button" onclick="openFormInstalasiModal(
                                                          '{{ $r->nomor_internet }}',
                                                          '{{ addslashes($r->nama_pelanggan) }}',
                                                          '{{ addslashes($r->survey_note_finish ?: $r->note_request ?: '') }}',
                                                          '{{ $r->instalasi_date_start ?? '' }}',
                                                          '{{ $r->instalasi_time ?? '' }}',
                                                          '{{ addslashes($r->instalasi_note ?? '') }}',
                                                          '{{ addslashes($r->instalasi_team ?? '') }}',
                                                          {{ json_encode($rItems) }}
                                                      )" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                      <span>Jadwal Instalasi</span>
                                                  </button>
                                              @elseif($step4_instalasiScheduled)
                                                  <a href="{{ route('pendaftaran.report-instalasi', $r->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                                                      <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                                                      <span>Report Instalasi</span>
                                                  </a>
                                              @elseif($step5_instalasiDone)
                                                  {{-- Process Selesai: Tombol Report Instalasi HILANG --}}
                                              @endif

                                              {{-- 3. Edit --}}
                                              <a href="{{ route('pendaftaran.edit', $r->nomor_internet) }}" class="flex items-center gap-1.5 text-gray-700 hover:text-emerald-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-pen-to-square text-emerald-500"></i>
                                                  <span>Edit</span>
                                              </a>

                                              {{-- 4. Hapus --}}
                                              <button type="button" onclick="konfirmasiHapus('{{ $r->nomor_internet }}')" class="flex items-center gap-1.5 text-gray-700 hover:text-rose-600 transition-colors whitespace-nowrap">
                                                  <i class="fa-solid fa-trash-can text-rose-500"></i>
                                                  <span>Hapus</span>
                                              </button>
                                          </div>
                                      @endif
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200/60 flex items-center justify-center mb-4 shadow-inner">
                                        <i class="fa-solid fa-box-open text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Belum ada pendaftaran</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Registrasi Baru" untuk menambahkan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (isset($rows) && $rows->total())
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 gap-3">
                <div class="text-sm text-gray-500">Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} entries</div>
                @include('partials.pagination', ['rows' => $rows])
            </div>
        @endif
    </div>

    <!-- Custom Scrollbar Styles -->
    <style>
        .custom-modal-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-modal-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 99px;
        }
        .custom-modal-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @if(!($isNoc ?? false))
    <!-- ============================================ -->
    <!-- MODAL FORM REGISTRATION -->
    <!-- ============================================ -->
    <!-- ============================================ -->
    <!-- MODAL FORM REGISTRATION -->
    <!-- ============================================ -->
    <div id="modalRegistrasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>

        <div class="modal-center-wrapper flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6 relative z-10 transition-all duration-300">
            <div class="relative bg-slate-50/95 rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] max-h-[800px] flex flex-col overflow-hidden border border-slate-200/80 mx-auto my-auto transform transition-all">

                <!-- Modal Header (Fixed Top) -->
                <div class="shrink-0 flex items-center justify-between px-6 py-3.5 bg-white/95 backdrop-blur-md border-b border-slate-200/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-bold text-lg shadow-xs">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Form Pendaftaran Baru</h3>
                            <p class="text-xs text-slate-500">Lengkapi formulir pendaftaran layanan pelanggan di bawah ini</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="resetFormRegistrasi()" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Kosongkan seluruh isian formulir">
                            <i class="fa-solid fa-rotate-left text-xs text-rose-500"></i>
                            <span>Reset Form</span>
                        </button>
                        <button type="button" onclick="closeModal()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick Section Navigation Bar (Sub-header) -->
                <div class="shrink-0 px-4 sm:px-6 py-2 bg-slate-100/90 border-b border-slate-200/80 flex items-center justify-start sm:justify-center overflow-x-auto no-scrollbar gap-2 sm:gap-3 text-xs font-semibold text-slate-600 backdrop-blur-xs">
                    <button type="button" onclick="scrollToSection('sec-pelanggan')" class="px-3 py-1.5 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-2xs transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-id-card text-blue-500"></i> 1. Informasi Pelanggan
                    </button>
                    <span class="text-slate-300 hidden sm:inline">•</span>
                    <button type="button" onclick="scrollToSection('sec-alamat-perusahaan')" class="px-3 py-1.5 rounded-lg hover:bg-white hover:text-emerald-600 hover:shadow-2xs transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-building text-emerald-500"></i> 2. Alamat Perusahaan
                    </button>
                    <span class="text-slate-300 hidden sm:inline">•</span>
                    <button type="button" onclick="scrollToSection('sec-pasang')" class="px-3 py-1.5 rounded-lg hover:bg-white hover:text-indigo-600 hover:shadow-2xs transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-location-dot text-indigo-500"></i> 3. Lokasi Pemasangan
                    </button>
                    <span class="text-slate-300 hidden sm:inline">•</span>
                    <button type="button" onclick="scrollToSection('sec-paket')" class="px-3 py-1.5 rounded-lg hover:bg-white hover:text-green-600 hover:shadow-2xs transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-wifi text-green-500"></i> 4. Kapasitas Layanan
                    </button>
                    <span class="text-slate-300 hidden sm:inline">•</span>
                    <button type="button" onclick="scrollToSection('sec-sales')" class="px-3 py-1.5 rounded-lg hover:bg-white hover:text-amber-600 hover:shadow-2xs transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fa-solid fa-user-gear text-amber-500"></i> 5. Penugasan Sales
                    </button>
                </div>

                <!-- Form Content Body -->
                <form method="POST" action="{{ route('pendaftaran.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden" id="formRegistrasi">
                    @csrf

                    <!-- Scrollable Form Container -->
                    <div class="flex-1 min-h-0 overflow-y-auto custom-modal-scroll p-4 sm:p-6 space-y-6 scroll-smooth">

                    <!-- ============================================ -->
                    <!-- SECTION 1: INFORMASI PELANGGAN -->
                    <!-- ============================================ -->
                    <div id="sec-pelanggan" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">1. Informasi Pelanggan</h4>
                        </div>

                        <!-- Row 1: Nama Perusahaan (1st), ID Perusahaan (2nd), No Telp, Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- 1. Nama Perusahaan (Pertama) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-700">Nama Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                    <span id="autoFillAlert" class="hidden text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs truncate max-w-[170px]">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Data Terisi
                                    </span>
                                </div>
                                <input type="text" name="nama_perusahaan" id="inputNamaPerusahaan" list="listExistingCompanyNames" required maxlength="255" placeholder="Ketik / Pilih Nama Perusahaan" value="{{ old('nama_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                                <datalist id="listExistingCompanyNames">
                                    @foreach($existingCompanies ?? [] as $comp)
                                        @php
                                            $cName = $comp->nama_perusahaan ?? $comp->nama_pelanggan ?? '';
                                        @endphp
                                        @if($cName)
                                            <option value="{{ $cName }}">{{ $cName }} (ID: {{ $comp->id_perusahaan }})</option>
                                        @endif
                                    @endforeach
                                </datalist>
                                <p class="text-[10px] text-slate-400 mt-1">Ketik nama perusahaan baru atau pilih yang sudah ada.</p>
                            </div>

                            <!-- 2. ID Perusahaan (Kedua - Readonly Auto Generated / Auto Match) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-700">ID Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                    <div class="flex items-center gap-1">
                                        <span class="text-[10px] text-blue-700 font-semibold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-lock text-[9px] text-blue-500"></i> Auto ID
                                        </span>
                                    </div>
                                </div>
                                <div class="relative flex items-center">
                                    <input type="text" name="id_perusahaan" id="inputIdPerusahaan" readonly required maxlength="100" placeholder="isp-001-{{ date('Y') }}" value="{{ old('id_perusahaan', $autoIdPerusahaan ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-mono py-2.5 pl-3.5 pr-10 text-sm rounded-xl outline-none select-all" title="ID Perusahaan otomatis di-generate atau disesuaikan dengan nama perusahaan">
                                    <button type="button" id="btnRefreshAutoId" onclick="refreshAutoIdPerusahaan()" title="Generate Ulang ID Baru" class="absolute right-2 text-slate-400 hover:text-blue-600 transition-colors p-1.5">
                                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info text-blue-500"></i> Otomatis terisi / generate saat mengisi Nama Perusahaan.
                                </p>
                            </div>

                            <!-- 3. No Telp Perusahaan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No Telp. Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="no_telp_perusahaan" required maxlength="30" placeholder="08xxxxxxxxxx" value="{{ old('no_telp_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>

                            <!-- 4. Email Perusahaan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="email" name="email_perusahaan" required maxlength="150" placeholder="email@perusahaan.com" value="{{ old('email_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <!-- Row 2: PIC Teknis -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="nama_pic_teknis" required maxlength="200" placeholder="Nama PIC Teknis" value="{{ old('nama_pic_teknis') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No Telp. PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="no_telp_pic_teknis" required maxlength="30" placeholder="08xxxxxxxxxx" value="{{ old('no_telp_pic_teknis') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                                <input type="email" name="email_pic_teknis" required maxlength="150" placeholder="teknis@perusahaan.com" value="{{ old('email_pic_teknis') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <!-- Row 3: PIC Keuangan -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="nama_pic_keuangan" required maxlength="200" placeholder="Nama PIC Keuangan" value="{{ old('nama_pic_keuangan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No Telp. PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="no_telp_pic_keuangan" required maxlength="30" placeholder="08xxxxxxxxxx" value="{{ old('no_telp_pic_keuangan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="email" name="email_pic_keuangan" required maxlength="150" placeholder="keuangan@perusahaan.com" value="{{ old('email_pic_keuangan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <!-- Row 4: Jenis Perusahaan & Tanggal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="jenis_perusahaan" required maxlength="100" placeholder="Contoh: PT, CV, Yayasan, dll" value="{{ old('jenis_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal <span class="text-rose-500 font-bold">*</span></label>
                                <input type="date" name="tanggal_registrasi" required value="{{ old('tanggal_registrasi', date('Y-m-d')) }}" onclick="this.showPicker && this.showPicker()" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- SECTION 2: ALAMAT PERUSAHAAN & DETAIL -->
                    <!-- ============================================ -->
                    <div id="sec-alamat-perusahaan" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200/60 text-emerald-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">2. Alamat Perusahaan & Detail Perusahaan</h4>
                        </div>

                        <!-- Cascading Dropdown Wilayah Perusahaan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Provinsi Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="provinsi_ktp" id="provKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinsi as $p)
                                            <option value="{{ $p->kode_wilayah_provinsi }}" {{ old('provinsi_ktp') == $p->kode_wilayah_provinsi ? 'selected' : '' }}>{{ $p->nama_provinsi }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kota/Kabupaten <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kota_ktp" id="kotaKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kecamatan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kecamatan_ktp" id="kecKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelurahan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kelurahan_ktp" id="kelKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- RT, RW, Jenis Bangunan, No Blok Bangunan -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">RT <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="rt_ktp" id="rtKtp" required placeholder="000" value="{{ old('rt_ktp') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">RW <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="rw_ktp" id="rwKtp" required placeholder="000" value="{{ old('rw_ktp') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Bangunan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="jenis_bangunan_perusahaan" id="jenisBangunanPerusahaan" list="listBangunan" required placeholder="Contoh: RUMAH, RUKO, GEDUNG" value="{{ old('jenis_bangunan_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No / Blok Bangunan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="nomor_bangunan_perusahaan" id="noBangunanPerusahaan" required placeholder="Contoh: LT2/15, BLOK C/22, No. 41" value="{{ old('nomor_bangunan_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <!-- Detail Alamat Perusahaan -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Alamat Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                            <textarea name="alamat_ktp" id="alamatKtp" required rows="2" placeholder="JALAN, NO. RUMAH, KOMPLEK, DLL" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400">{{ old('alamat_ktp') }}</textarea>
                        </div>

                        <!-- Titik Koordinat & Link Sharelock Perusahaan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Titik Koordinat Perusahaan (Lat, Long)</label>
                                <input type="text" name="lon_lat_perusahaan" id="lonLatPerusahaan" placeholder="-6.12345, 106.78910" value="{{ old('lon_lat_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Link Sharelock Lokasi Perusahaan</label>
                                <input type="text" name="sharelock_perusahaan" id="sharelockPerusahaan" placeholder="https://maps.google.com/..." value="{{ old('sharelock_perusahaan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                            </div>
                        </div>

                        <!-- Upload Foto PO & Foto Bangunan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto PO <span class="text-rose-500 font-bold">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center transition-all cursor-pointer bg-slate-50/50 hover:bg-blue-50/30 group" onclick="this.querySelector('input').click()">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 group-hover:scale-110 flex items-center justify-center mx-auto mb-2 transition-all shadow-xs">
                                        <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600">Klik untuk mengunggah foto PO</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Format JPG, PNG, WEBP (Maks: 5MB)</p>
                                    <p class="text-xs font-bold text-blue-600 mt-2 file-name truncate px-2"></p>
                                    <input type="file" name="foto_po" accept="image/*" class="hidden" onchange="previewFile(this, 'previewPo')">
                                </div>
                                <img id="previewPo" class="mt-3 max-h-36 rounded-xl border border-slate-200 shadow-xs hidden object-cover mx-auto">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Bangunan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center transition-all cursor-pointer bg-slate-50/50 hover:bg-blue-50/30 group" onclick="this.querySelector('input').click()">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 group-hover:scale-110 flex items-center justify-center mx-auto mb-2 transition-all shadow-xs">
                                        <i class="fa-solid fa-house-chimney-window text-lg"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600">Klik untuk mengunggah foto Bangunan</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Format JPG, PNG, WEBP (Maks: 5MB)</p>
                                    <p class="text-xs font-bold text-blue-600 mt-2 file-name truncate px-2"></p>
                                    <input type="file" name="foto_bangunan" accept="image/*" class="hidden" onchange="previewFile(this, 'previewBangunan')">
                                </div>
                                <img id="previewBangunan" class="mt-3 max-h-36 rounded-xl border border-slate-200 shadow-xs hidden object-cover mx-auto">
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- SECTION 3: ALAMAT & LOKASI PEMASANGAN -->
                    <!-- ============================================ -->
                    <div id="sec-pasang" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200/60 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">3. Alamat & Lokasi Pemasangan</h4>
                            </div>
                        </div>

                        <!-- Cascading Dropdown Wilayah Pemasangan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Provinsi Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="provinsi_pasang" id="provPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinsi as $p)
                                            <option value="{{ $p->kode_wilayah_provinsi }}" {{ old('provinsi_pasang') == $p->kode_wilayah_provinsi ? 'selected' : '' }}>{{ $p->nama_provinsi }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kota/Kabupaten Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kota_pasang" id="kotaPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kecamatan Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kecamatan_pasang" id="kecPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelurahan Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <div class="relative">
                                    <select name="kelurahan_pasang" id="kelPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- RT, RW, Jenis Bangunan, No Blok Bangunan -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">RT Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="rt_pasang" id="rtPasang" required placeholder="000" value="{{ old('rt_pasang') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">RW Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="rw_pasang" id="rwPasang" required placeholder="000" value="{{ old('rw_pasang') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Bangunan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="jenis_bangunan" id="jenisBangunanPasang" list="listBangunan" required placeholder="Contoh: RUMAH, RUKO, GEDUNG" value="{{ old('jenis_bangunan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No / Blok Bangunan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="nomor_bangunan" id="noBangunanPasang" required placeholder="Contoh: LT2/15, BLOK C/22, No. 41" value="{{ old('nomor_bangunan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <!-- Detail Alamat Pemasangan -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Alamat Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                            <textarea name="alamat_pasang" id="alamatPasang" required rows="2" placeholder="JALAN, NO. RUMAH, PATOKAN LOKASI" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2 px-3 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400">{{ old('alamat_pasang') }}</textarea>
                        </div>

                        <!-- Titik Koordinat, Sharelock, Permintaan Khusus -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Titik Koordinat (Lat, Long)</label>
                                <input type="text" name="lon_lat" id="lonLatPasang" placeholder="-6.12345, 106.78910" value="{{ old('lon_lat') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Link Sharelock Lokasi</label>
                                <input type="text" name="sharelock" id="sharelockPasang" placeholder="https://maps.google.com/..." value="{{ old('sharelock') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Permintaan Khusus Pelanggan</label>
                                <textarea name="permintaan_khusus" id="permintaanKhusus" rows="2" placeholder="Catatan khusus teknisi/pemasangan" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2 px-3 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400 no-uppercase">{{ old('permintaan_khusus') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- SECTION 4: PEMILIHAN KAPASITAS LAYANAN -->
                    <!-- ============================================ -->
                    <div id="sec-paket" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-green-50 border border-green-200/60 text-green-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-wifi"></i>
                            </div>
                            <h4 class="text-xs font-bold text-green-700 uppercase tracking-wider">4. Kapasitas Layanan</h4>
                        </div>

                        <!-- Kategori Layanan & Kapasitas Layanan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Layanan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="kode_kategori" id="inputKategori" list="listKategori" required placeholder="Ketik kategori layanan (misal: BROADBAND, DEDICATED)" value="{{ old('kode_kategori') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                                <datalist id="listKategori">
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->nama_kategori_bandwith }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kapasitas Layanan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="kode_bandwith" id="inputPaket" list="listPaket" required placeholder="Ketik kapasitas layanan / bandwidth (misal: 100 Mbps)" value="{{ old('kode_bandwith') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                                <datalist id="listPaket">
                                    @foreach ($paketList ?? [] as $p)
                                        <option value="{{ $p->nominal_bandwith }} Mbps - {{ $p->nama_kategori_bandwith }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- Harga Layanan (Manual Ketik) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Harga Layanan <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="harga_paket" id="hargaPaket" required placeholder="Ketik harga layanan (contoh: 500000 atau Rp 500.000)" value="{{ old('harga_paket') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none font-semibold transition-all placeholder-slate-400">
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- SECTION 5: INFORMASI PENUGASAN SALES & SISTEM -->
                    <!-- ============================================ -->
                    <div id="sec-sales" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200/60 text-amber-600 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">5. Informasi Penugasan Sales & Sistem</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Pelanggan</label>
                                <div class="relative">
                                    <input type="text" value="Auto generate saat disimpan" readonly class="w-full bg-slate-100/80 border border-slate-200 text-slate-500 py-2.5 px-3.5 text-sm rounded-xl outline-none cursor-not-allowed italic font-medium">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 text-xs">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Sales <span class="text-rose-500 font-bold">*</span></label>
                                <input type="text" name="nama_sales" id="inputSales" list="listSales" required placeholder="Ketik nama sales" value="{{ old('nama_sales') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                                <datalist id="listSales">
                                    @foreach ($sales as $s)
                                        <option value="{{ $s->nama_karyawan }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>
                    </div> <!-- End of Scrollable Form Container -->

                    <!-- Modal Footer (Fixed Bottom Inside Form) -->
                    <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white/95 backdrop-blur-md border-t border-slate-200/80 rounded-b-2xl">
                        <div class="text-xs text-slate-400 hidden sm:block">
                            <span class="text-rose-500 font-bold">*</span> Menandakan kolom wajib diisi
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-800 text-sm font-semibold transition-all duration-200">
                                <i class="fa-solid fa-xmark mr-1.5"></i>Batal
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-lg shadow-blue-500/25 transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>Simpan Registration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- ============================================ -->
    <!-- MODAL KONFIRMASI HAPUS -->
    <!-- ============================================ -->
    <div id="modalHapus" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeHapusModal()"></div>
        <div class="flex min-h-screen w-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-3xl text-red-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 mb-1">Apakah Anda yakin ingin menghapus data pendaftaran ini?</p>
                    <p id="hapusNomorInternet" class="text-sm font-semibold text-red-600 mb-6"></p>

                    <form id="formHapus" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" onclick="closeHapusModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 transition-all duration-200 hover:-translate-y-0.5"><i class="fa-solid fa-xmark"></i>Batal</button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-red-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-trash-can"></i>Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif {{-- end !$isNoc --}}

    @if($isNoc ?? false)
    <!-- ============================================ -->
    <!-- MODAL FORM AKTIVASI (NOC) -->
    <!-- ============================================ -->
    <div id="modalAktivasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeAktivasiModal()"></div>
        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] max-h-[800px] flex flex-col overflow-hidden border border-slate-200 my-auto transform transition-all">

                {{-- Header (Fixed Top) --}}
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-800">
                        Form Aktivasi An/<span id="aktivasiNamaHeader" class="text-blue-600"></span>
                    </h3>
                    <button type="button" onclick="closeAktivasiModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Body & Form (Flex-1 scrollable) --}}
                <form id="formAktivasi" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex-1 overflow-y-auto p-6 custom-modal-scroll">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                            <!-- LEFT COLUMN -->
                            <div class="space-y-4">
                                <!-- 1. Jadwal Aktivasi* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jadwal Aktivasi<span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="aktivasi_date_start" id="aktivasiDate" required
                                           placeholder="Tanggal Instalasi"
                                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                </div>

                                <!-- 2. Waktu Aktivasi* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Waktu Aktivasi<span class="text-red-500">*</span>
                                    </label>
                                    <select name="aktivasi_time" id="aktivasiTime" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                        <option value="">Select a State</option>
                                        <option value="08:00 - 10:00 WIB">08:00 - 10:00 WIB</option>
                                        <option value="10:00 - 12:00 WIB">10:00 - 12:00 WIB</option>
                                        <option value="13:00 - 15:00 WIB">13:00 - 15:00 WIB</option>
                                        <option value="15:00 - 17:00 WIB">15:00 - 17:00 WIB</option>
                                        <option value="FULL DAY">FULL DAY</option>
                                    </select>
                                </div>

                                <!-- 3. Team Aktivasi -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Team Aktivasi
                                    </label>
                                    <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-2.5 border border-slate-300 rounded-lg bg-slate-50/50 text-xs">
                                        @foreach($teamAktivasiList ?? [] as $tm)
                                            <label class="flex items-center gap-2 cursor-pointer hover:text-cyan-600 select-none">
                                                <input type="checkbox" name="aktivasi_team[]" value="{{ $tm->nama_karyawan }}" class="team-checkbox rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                                                <span class="truncate font-semibold uppercase text-slate-700 text-[11px]">{{ $tm->nama_karyawan }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 4. POP/ODN* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        POP/ODN<span class="text-red-500">*</span>
                                    </label>
                                    <select name="kode_pop" id="aktivasiPop" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                        <option value="">Select a State</option>
                                        @foreach($popList ?? [] as $pop)
                                            <option value="{{ $pop->kode_pop }}">{{ strtoupper($pop->nama_pop) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 5. Media Akses* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Media Akses<span class="text-red-500">*</span>
                                    </label>
                                    <select name="media_akses" id="aktivasiMediaAkses" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                        <option value="">Select a State</option>
                                        @foreach($mediaAksesList ?? [] as $ma)
                                            <option value="{{ $ma->nama_media_akses }}">{{ strtoupper($ma->nama_media_akses) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 6. Index OLT* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Index OLT<span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="index_olt" id="aktivasiIndexOlt" placeholder=""
                                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                </div>

                                <!-- 7. Catatan Proses Aktivasi* -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Catatan Proses Aktivasi<span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="aktivasi_note" id="aktivasiNote" rows="3" placeholder="Informasi pendukung proses aktivasi."
                                              class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN -->
                            <div class="md:border-l md:pl-6 border-slate-200 space-y-4">
                                <h4 class="text-xs font-semibold text-slate-700 border-b border-slate-100 pb-2">
                                    Perangkat/ Peralatan Yang Digunakan
                                </h4>

                                <!-- Input Row for Adding Perangkat -->
                                <div class="grid grid-cols-12 gap-2 items-end">
                                    <div class="col-span-6">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="inputKodeBarang" class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 focus:border-cyan-400 outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @foreach($barangList ?? [] as $b)
                                                <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}" data-satuan="{{ $b->satuan ?: 'UNIT' }}">{{ $b->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1 text-center">Jumlah</label>
                                        <input type="number" id="inputJumlahBarang" value="1" min="1" class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 focus:border-cyan-400 outline-none text-center">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1 text-center">action</label>
                                        <button type="button" onclick="addAktivasiBarang()" class="w-full bg-cyan-400 hover:bg-cyan-500 text-white font-semibold text-xs py-1.5 px-3 rounded-lg shadow-xs transition-colors">
                                            Add
                                        </button>
                                    </div>
                                </div>

                                <!-- Table of Added Perangkat -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden mt-3">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-2 px-3 text-left">Barang</th>
                                                <th class="py-2 px-3 text-center">Jumlah</th>
                                                <th class="py-2 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableAktivasiBarang" class="divide-y divide-slate-100">
                                            <!-- Rows added dynamically via JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenBarangContainer"></div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer (Fixed Bottom) --}}
                    <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button type="button" onclick="closeAktivasiModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i> Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endif {{-- end $isNoc --}}

    <!-- ============================================ -->
    <!-- MODAL FORM SURVEY (ROLE TEKNIK) -->
    <!-- ============================================ -->
    <div id="modalSurvey" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeSurveyModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="surveyModalTitle">Form Survey An/</h3>
                    <button type="button" onclick="closeSurveyModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formSurvey" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Tanggal Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Survey<span class="text-rose-500">*</span></label>
                                <input type="date" name="survey_date_start" id="surveyDateStart" required class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all">
                            </div>

                            <!-- Catatan Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Survey<span class="text-rose-500">*</span></label>
                                <textarea name="survey_note" id="surveyNote" rows="3" required placeholder="masukan catatan untuk teknisi lapangan saat proses instalasi" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400"></textarea>
                            </div>

                            <!-- Foto Mapping -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Mapping<span class="text-rose-500">*</span></label>
                                <div class="relative border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-5 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingInput').click()">
                                    <input type="file" name="foto_mapping" id="fotoMappingInput" accept="image/*" class="hidden" onchange="previewFotoMapping(this)">
                                    <div class="flex flex-col items-center justify-center space-y-1.5">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium" id="fotoMappingText">Drag and drop a file here or click</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Waktu Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Waktu Survey<span class="text-rose-500">*</span></label>
                                <select name="survey_time" id="surveyTime" required class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all">
                                    <option value="" disabled selected>Pilih waktu survey</option>
                                    <option value="08:00 - 10:00">08:00 - 10:00</option>
                                    <option value="10:00 - 12:00">10:00 - 12:00</option>
                                    <option value="13:00 - 15:00">13:00 - 15:00</option>
                                    <option value="15:00 - 17:00">15:00 - 17:00</option>
                                    <option value="17:00 - 19:00">17:00 - 19:00</option>
                                </select>
                            </div>

                            <!-- Team Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Survey</label>
                                <div class="grid grid-cols-2 gap-2 max-h-[210px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                    @foreach($teamAktivasiList as $tm)
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                            <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="survey-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeSurveyModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL REPORT SURVEY (ROLE TEKNIK - PROSES 2) -->
    <!-- ============================================ -->
    <div id="modalReportSurvey" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeReportSurveyModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="reportSurveyModalTitle">Report Survey An/</h3>
                    <button type="button" onclick="closeReportSurveyModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formReportSurvey" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Checkbox Jadwal Ulang Survey -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-slate-700">Jadwal Ulang Survey ?</label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_reschedule" id="checkRescheduleSurvey" onchange="toggleRescheduleSurvey(this)" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-xs font-medium text-slate-600">Ya, Jadwal Ulang</span>
                                </label>
                            </div>

                            <!-- Form Reschedule (hidden by default) -->
                            <div id="sectionRescheduleSurvey" class="hidden space-y-3 p-3 bg-amber-50/50 border border-amber-200/80 rounded-xl">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Reschedule<span class="text-rose-500">*</span></label>
                                    <input type="date" name="reschedule_date" id="rescheduleDate" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Reschedule</label>
                                    <select name="reschedule_time" id="rescheduleTime" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                        <option value="" disabled selected>Pilih waktu survey</option>
                                        <option value="08:00 - 10:00">08:00 - 10:00</option>
                                        <option value="10:00 - 12:00">10:00 - 12:00</option>
                                        <option value="13:00 - 15:00">13:00 - 15:00</option>
                                        <option value="15:00 - 17:00">15:00 - 17:00</option>
                                        <option value="17:00 - 19:00">17:00 - 19:00</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Reschedule</label>
                                    <input type="text" name="reschedule_note" id="rescheduleNote" placeholder="ALASAN JADWAL ULANG" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                                </div>
                            </div>

                            <!-- Form Selesai Survey -->
                            <div id="sectionSelesaiSurvey" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai Survey<span class="text-rose-500">*</span></label>
                                        <input type="date" name="survey_date_finish" id="surveyDateFinish" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Selesai Survey<span class="text-rose-500">*</span></label>
                                        <input type="text" name="survey_note_finish" id="surveyNoteFinish" required placeholder="CATATAN SELESAI" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                                    </div>
                                </div>

                                <!-- Bisa Dilakukan Pemasangan -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bisa Dilakukan Pemasangan<span class="text-rose-500">*</span></label>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="bisa_pasang" value="1" checked class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                            <span class="text-xs font-semibold text-slate-700">YA</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="bisa_pasang" value="0" class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                            <span class="text-xs font-semibold text-slate-700">Tidak</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Update Foto Mapping -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Foto Mapping<span class="text-rose-500">*</span></label>
                                    <div class="relative border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-5 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingUpdateInput').click()">
                                        <input type="file" name="foto_mapping" id="fotoMappingUpdateInput" accept="image/*" class="hidden" onchange="previewFotoMappingUpdate(this)">
                                        <div class="flex flex-col items-center justify-center space-y-1.5">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium" id="fotoMappingUpdateText">Drag and drop a file here or click</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Team Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Survey</label>
                                <div class="grid grid-cols-2 gap-2 max-h-[170px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                    @foreach($teamAktivasiList as $tm)
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                            <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="report-survey-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="border-t border-slate-100 pt-3 space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="reportSurveySelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @foreach($barangList as $b)
                                                <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}">{{ $b->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="reportSurveyQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addReportSurveyBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[130px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableReportSurveyBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyReportSurveyBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenReportSurveyBarangContainer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeReportSurveyModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL FORM INSTALASI (ROLE TEKNIK - PROSES 3) -->
    <!-- ============================================ -->
    <div id="modalFormInstalasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeFormInstalasiModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="formInstalasiModalTitle">Form Instalasi An/</h3>
                    <button type="button" onclick="closeFormInstalasiModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formInstalasiTeknik" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- PERMINTAAN DARI PELANGGAN -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Permintaan Dari Pelanggan</label>
                                <div class="bg-amber-400 text-slate-900 font-bold p-3.5 rounded-xl text-sm uppercase shadow-xs border border-amber-500/30" id="instalasiNoteRequest">
                                    TESTING
                                </div>
                            </div>

                            <!-- Tanggal & Waktu Instalasi -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Instalasi<span class="text-rose-500">*</span></label>
                                    <input type="date" name="instalasi_date_start" id="instalasiDateStart" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Instalasi<span class="text-rose-500">*</span></label>
                                    <select name="instalasi_time" id="instalasiTime" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                        <option value="" disabled selected>Select a State</option>
                                        <option value="08:00 - 10:00">08:00 - 10:00</option>
                                        <option value="10:00 - 12:00">10:00 - 12:00</option>
                                        <option value="13:00 - 15:00">13:00 - 15:00</option>
                                        <option value="15:00 - 17:00">15:00 - 17:00</option>
                                        <option value="17:00 - 19:00">17:00 - 19:00</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Team Instalasi -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Instalasi</label>
                                <div class="grid grid-cols-2 gap-2 max-h-[170px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                    @foreach($teamAktivasiList as $tm)
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                            <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="instalasi-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                            <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Catatan Pemasangan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Pemasangan<span class="text-rose-500">*</span></label>
                                <textarea name="instalasi_note" id="instalasiNote" rows="3" required placeholder="masukan catatan untuk teknisi lapangan saat proses instalasi." class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-xl outline-none placeholder-slate-400"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="instalasiSelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @foreach($barangList as $b)
                                                <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}">{{ $b->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="instalasiQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addInstalasiBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[140px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableInstalasiBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyInstalasiBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenInstalasiBarangContainer"></div>
                            </div>

                            <!-- Update Foto Mapping -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Foto Mapping<span class="text-rose-500">*</span></label>
                                <div class="relative border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-5 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingInstalasiInput').click()">
                                    <input type="file" name="foto_mapping" id="fotoMappingInstalasiInput" accept="image/*" class="hidden" onchange="previewFotoMappingInstalasi(this)">
                                    <div class="flex flex-col items-center justify-center space-y-1.5">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium" id="fotoMappingInstalasiText">Drag and drop a file here or click</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeFormInstalasiModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL REPORT AKTIVASI (ROLE NOC)             -->
    <!-- ============================================ -->
    <div id="modalReportAktivasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeReportAktivasiModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="reportAktivasiModalTitle">Report aktivasi An/</h3>
                    <button type="button" onclick="closeReportAktivasiModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formReportAktivasi" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Checkbox Jadwal Ulang Aktivasi -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-rose-500">Jadwal Ulang Aktivasi ?</label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_reschedule" id="checkRescheduleAktivasi" onchange="toggleRescheduleAktivasi(this)" class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <span class="text-xs font-medium text-slate-600">Ya, Jadwal Ulang</span>
                                </label>
                            </div>

                            <!-- Form Reschedule Aktivasi (hidden by default) -->
                            <div id="sectionRescheduleAktivasi" class="hidden space-y-3 p-3 bg-amber-50/50 border border-amber-200/80 rounded-xl">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Reschedule<span class="text-rose-500">*</span></label>
                                    <input type="date" name="reschedule_date" id="rescheduleAktivasiDate" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Reschedule</label>
                                    <select name="reschedule_time" id="rescheduleAktivasiTime" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                        <option value="" disabled selected>Pilih waktu aktivasi</option>
                                        <option value="08:00 - 10:00">08:00 - 10:00</option>
                                        <option value="10:00 - 12:00">10:00 - 12:00</option>
                                        <option value="13:00 - 15:00">13:00 - 15:00</option>
                                        <option value="15:00 - 17:00">15:00 - 17:00</option>
                                        <option value="17:00 - 19:00">17:00 - 19:00</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Reschedule</label>
                                    <input type="text" name="reschedule_note" id="rescheduleAktivasiNote" placeholder="ALASAN JADWAL ULANG" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                                </div>
                            </div>

                            <!-- Form Selesai Aktivasi -->
                            <div id="sectionSelesaiAktivasi" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai Aktivasi<span class="text-rose-500">*</span></label>
                                        <input type="date" name="aktivasi_date_finish" id="reportAktivasiDateFinish" required placeholder="Tanggal aktivasi" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Setelah aktivasi<span class="text-rose-500">*</span></label>
                                        <input type="text" name="aktivasi_note_finish" id="reportAktivasiNoteFinish" required placeholder="catatan aktivasi" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                </div>

                                <!-- Team Aktivasi -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Aktivasi</label>
                                    <div class="grid grid-cols-2 gap-2 max-h-[120px] overflow-y-auto custom-modal-scroll p-2.5 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                        @foreach($teamAktivasiList as $tm)
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="report-aktivasi-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- POP / ODN, Media Akses, Index OLT -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">POP/ODN<span class="text-rose-500">*</span></label>
                                        <select name="kode_pop" id="reportAktivasiPop" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                            <option value="">Lastmile Goesar</option>
                                            @foreach($popList as $p)
                                                <option value="{{ $p->kode_pop }}">{{ $p->nama_pop }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Media Akses<span class="text-rose-500">*</span></label>
                                        <select name="media_akses" id="reportAktivasiMediaAkses" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                            <option value="">MEDIANET</option>
                                            @foreach($mediaAksesList as $m)
                                                <option value="{{ $m->nama_media_akses }}">{{ $m->nama_media_akses }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Index OLT<span class="text-rose-500">*</span></label>
                                    <input type="text" name="index_olt" id="reportAktivasiIndexOlt" placeholder="TESTING" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="reportAktivasiSelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @foreach($barangList as $b)
                                                <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}">{{ $b->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="reportAktivasiQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addReportAktivasiBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[160px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableReportAktivasiBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyReportAktivasiBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenReportAktivasiBarangContainer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeReportAktivasiModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL KONFIRMASI AUTO-FILL PERUSAHAAN -->
    <!-- ============================================ -->
    <div id="modalConfirmAutoFillCompany" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-building-user text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Konfirmasi Auto-Fill</h3>
                </div>
                <button type="button" onclick="closeConfirmAutoFillModal()" class="w-8 h-8 rounded-full hover:bg-slate-200/60 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 text-xs text-slate-600 space-y-4">
                <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl space-y-2">
                    <p class="font-semibold text-blue-900 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-blue-600"></i> Data Perusahaan Ditemukan!
                    </p>
                    <div class="space-y-1.5 pt-1 text-slate-700 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">ID Perusahaan:</span>
                            <span id="confirmCompanyId" class="font-bold text-slate-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nama Perusahaan:</span>
                            <span id="confirmCompanyName" class="font-bold text-slate-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">No Telp:</span>
                            <span id="confirmCompanyPhone" class="font-semibold text-slate-700"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span id="confirmCompanyEmail" class="font-semibold text-slate-700"></span>
                        </div>
                    </div>
                </div>

                <p class="leading-relaxed text-slate-600">
                    Apakah Anda ingin menggunakan data ini untuk mengisi otomatis <strong>Informasi Pelanggan (Form 1)</strong> dan <strong>Alamat Perusahaan (Form 2)</strong>?
                </p>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                <button type="button" onclick="cancelAutoFillCompany()" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors">
                    Isi Manual / Batal
                </button>
                <button type="button" onclick="applyAutoFillCompany()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-sm shadow-blue-200">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Ya, Isi Otomatis
                </button>
            </div>
        </div>
    </div>

    <script>
        function openSurveyModal(nomorInternet, namaPelanggan, surveyDate, surveyTime, surveyNote, surveyTeamStr) {
            var form = document.getElementById('formSurvey');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/jadwal-survey';

            document.getElementById('surveyModalTitle').textContent = 'Form Survey An/' + (namaPelanggan || '');
            document.getElementById('surveyDateStart').value = surveyDate || new Date().toISOString().split('T')[0];
            
            var timeSelect = document.getElementById('surveyTime');
            if (timeSelect) {
                timeSelect.value = surveyTime || '';
            }

            document.getElementById('surveyNote').value = surveyNote || '';
            document.getElementById('fotoMappingText').textContent = 'Drag and drop a file here or click';

            var selectedTeams = surveyTeamStr ? surveyTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.survey-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            document.getElementById('modalSurvey').classList.remove('hidden');
        }

        function closeSurveyModal() {
            document.getElementById('modalSurvey').classList.add('hidden');
        }

        function previewFotoMapping(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fotoMappingText').textContent = '📄 ' + input.files[0].name;
            }
        }

        // ── Report Survey Functions (Proses 2) ──
        let globalReportSurveyItems = [];

        function openReportSurveyModal(nomorInternet, namaPelanggan, surveyDateFinish, surveyNoteFinish, surveyTeamStr) {
            var form = document.getElementById('formReportSurvey');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/report-survey';

            document.getElementById('reportSurveyModalTitle').textContent = 'Report Survey An/' + (namaPelanggan || '');
            document.getElementById('surveyDateFinish').value = surveyDateFinish || new Date().toISOString().split('T')[0];
            document.getElementById('surveyNoteFinish').value = surveyNoteFinish || '';
            document.getElementById('fotoMappingUpdateText').textContent = 'Drag and drop a file here or click';
            
            var rescheduleCb = document.getElementById('checkRescheduleSurvey');
            rescheduleCb.checked = false;
            toggleRescheduleSurvey(rescheduleCb);

            var selectedTeams = surveyTeamStr ? surveyTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.report-survey-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalReportSurveyItems = [];
            renderReportSurveyBarangTable();

            document.getElementById('modalReportSurvey').classList.remove('hidden');
        }

        function closeReportSurveyModal() {
            document.getElementById('modalReportSurvey').classList.add('hidden');
        }

        function toggleRescheduleSurvey(cb) {
            var resSec = document.getElementById('sectionRescheduleSurvey');
            var finSec = document.getElementById('sectionSelesaiSurvey');
            if (cb.checked) {
                resSec.classList.remove('hidden');
                finSec.classList.add('hidden');
                document.getElementById('surveyDateFinish').required = false;
                document.getElementById('surveyNoteFinish').required = false;
                document.getElementById('rescheduleDate').required = true;
            } else {
                resSec.classList.add('hidden');
                finSec.classList.remove('hidden');
                document.getElementById('surveyDateFinish').required = true;
                document.getElementById('surveyNoteFinish').required = true;
                document.getElementById('rescheduleDate').required = false;
            }
        }

        function previewFotoMappingUpdate(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fotoMappingUpdateText').textContent = '📄 ' + input.files[0].name;
            }
        }

        function addReportSurveyBarang() {
            var sel = document.getElementById('reportSurveySelectBarang');
            var qtyInput = document.getElementById('reportSurveyQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalReportSurveyItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalReportSurveyItems[existingIndex].jumlah += jumlah;
            } else {
                globalReportSurveyItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderReportSurveyBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeReportSurveyBarang(index) {
            globalReportSurveyItems.splice(index, 1);
            renderReportSurveyBarangTable();
        }

        function renderReportSurveyBarangTable() {
            var tbody = document.getElementById('tableReportSurveyBarang');
            var hiddenContainer = document.getElementById('hiddenReportSurveyBarangContainer');
            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (globalReportSurveyItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyReportSurveyBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalReportSurveyItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 border-b border-slate-100';
                tr.innerHTML = '<td class="py-1.5 px-3 font-semibold text-slate-800">' + item.nama_barang + '</td>' +
                               '<td class="py-1.5 px-3 text-center text-slate-700 font-bold">' + item.jumlah + '</td>' +
                               '<td class="py-1.5 px-3 text-center"><button type="button" onclick="removeReportSurveyBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-bold text-xs"><i class="fa-solid fa-trash-can"></i></button></td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'items[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJml = document.createElement('input');
                inputJml.type = 'hidden';
                inputJml.name = 'items[' + idx + '][jumlah]';
                inputJml.value = item.jumlah;

                hiddenContainer.appendChild(inputKode);
                hiddenContainer.appendChild(inputJml);
            });
        }

        // ── Form Instalasi Functions (Proses 3) ──
        let globalInstalasiItems = [];

        function openFormInstalasiModal(nomorInternet, namaPelanggan, noteRequest, dateStart, timeVal, noteVal, teamStr, existingItems) {
            var form = document.getElementById('formInstalasiTeknik');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/jadwal-instalasi';

            document.getElementById('formInstalasiModalTitle').textContent = 'Form Instalasi An/' + (namaPelanggan || '');
            document.getElementById('instalasiNoteRequest').textContent = noteRequest || 'TESTING';
            document.getElementById('instalasiDateStart').value = dateStart || new Date().toISOString().split('T')[0];
            
            var timeSelect = document.getElementById('instalasiTime');
            if (timeSelect) {
                timeSelect.value = timeVal || '';
            }

            document.getElementById('instalasiNote').value = noteVal || 'masukan catatan untuk teknisi lapangan saat proses instalasi.';
            document.getElementById('fotoMappingInstalasiText').textContent = 'Drag and drop a file here or click';

            var selectedTeams = teamStr ? teamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.instalasi-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalInstalasiItems = [];
            if (Array.isArray(existingItems) && existingItems.length > 0) {
                existingItems.forEach(function(it) {
                    globalInstalasiItems.push({
                        kode_barang: it.kode_barang,
                        nama_barang: it.nama_barang,
                        jumlah: it.jumlah_barang || it.jumlah || 1
                    });
                });
            }

            renderInstalasiBarangTable();
            document.getElementById('modalFormInstalasi').classList.remove('hidden');
        }

        function closeFormInstalasiModal() {
            document.getElementById('modalFormInstalasi').classList.add('hidden');
        }

        function previewFotoMappingInstalasi(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fotoMappingInstalasiText').textContent = '📄 ' + input.files[0].name;
            }
        }

        function addInstalasiBarang() {
            var sel = document.getElementById('instalasiSelectBarang');
            var qtyInput = document.getElementById('instalasiQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalInstalasiItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalInstalasiItems[existingIndex].jumlah += jumlah;
            } else {
                globalInstalasiItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderInstalasiBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeInstalasiBarang(index) {
            globalInstalasiItems.splice(index, 1);
            renderInstalasiBarangTable();
        }

        function renderInstalasiBarangTable() {
            var tbody = document.getElementById('tableInstalasiBarang');
            var hiddenContainer = document.getElementById('hiddenInstalasiBarangContainer');
            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (globalInstalasiItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyInstalasiBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalInstalasiItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 border-b border-slate-100';
                tr.innerHTML = '<td class="py-1.5 px-3 font-semibold text-slate-800">' + item.nama_barang + '</td>' +
                               '<td class="py-1.5 px-3 text-center text-slate-700 font-bold">' + item.jumlah + '</td>' +
                               '<td class="py-1.5 px-3 text-center"><button type="button" onclick="removeInstalasiBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-bold text-xs"><i class="fa-solid fa-trash-can"></i></button></td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'items[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJml = document.createElement('input');
                inputJml.type = 'hidden';
                inputJml.name = 'items[' + idx + '][jumlah]';
                inputJml.value = item.jumlah;

                hiddenContainer.appendChild(inputKode);
                hiddenContainer.appendChild(inputJml);
            });
        }

        // ── Report Aktivasi Functions (Role NOC) ──
        let globalReportAktivasiItems = [];

        function openReportAktivasiModal(nomorInternet, namaPelanggan, dateFinish, noteFinish, teamStr, kodePop, mediaAkses, indexOlt, existingItems) {
            var form = document.getElementById('formReportAktivasi');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/report-aktivasi';

            document.getElementById('reportAktivasiModalTitle').textContent = 'Report aktivasi An/' + (namaPelanggan || '');
            document.getElementById('reportAktivasiDateFinish').value = dateFinish || new Date().toISOString().split('T')[0];
            document.getElementById('reportAktivasiNoteFinish').value = noteFinish || '';
            document.getElementById('reportAktivasiPop').value = kodePop || '';
            document.getElementById('reportAktivasiMediaAkses').value = mediaAkses || '';
            document.getElementById('reportAktivasiIndexOlt').value = indexOlt || 'TESTING';

            var rescheduleCb = document.getElementById('checkRescheduleAktivasi');
            rescheduleCb.checked = false;
            toggleRescheduleAktivasi(rescheduleCb);

            var selectedTeams = teamStr ? teamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.report-aktivasi-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalReportAktivasiItems = [];
            if (Array.isArray(existingItems) && existingItems.length > 0) {
                existingItems.forEach(function(it) {
                    globalReportAktivasiItems.push({
                        kode_barang: it.kode_barang,
                        nama_barang: it.nama_barang,
                        jumlah: it.jumlah_barang || it.jumlah || 1
                    });
                });
            }

            renderReportAktivasiBarangTable();
            document.getElementById('modalReportAktivasi').classList.remove('hidden');
        }

        function closeReportAktivasiModal() {
            document.getElementById('modalReportAktivasi').classList.add('hidden');
        }

        function toggleRescheduleAktivasi(cb) {
            var resSec = document.getElementById('sectionRescheduleAktivasi');
            var finSec = document.getElementById('sectionSelesaiAktivasi');
            if (cb.checked) {
                resSec.classList.remove('hidden');
                finSec.classList.add('hidden');
                document.getElementById('reportAktivasiDateFinish').required = false;
                document.getElementById('reportAktivasiNoteFinish').required = false;
                document.getElementById('rescheduleAktivasiDate').required = true;
            } else {
                resSec.classList.add('hidden');
                finSec.classList.remove('hidden');
                document.getElementById('reportAktivasiDateFinish').required = true;
                document.getElementById('reportAktivasiNoteFinish').required = true;
                document.getElementById('rescheduleAktivasiDate').required = false;
            }
        }

        function addReportAktivasiBarang() {
            var sel = document.getElementById('reportAktivasiSelectBarang');
            var qtyInput = document.getElementById('reportAktivasiQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalReportAktivasiItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalReportAktivasiItems[existingIndex].jumlah += jumlah;
            } else {
                globalReportAktivasiItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderReportAktivasiBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeReportAktivasiBarang(index) {
            globalReportAktivasiItems.splice(index, 1);
            renderReportAktivasiBarangTable();
        }

        function renderReportAktivasiBarangTable() {
            var tbody = document.getElementById('tableReportAktivasiBarang');
            var hiddenContainer = document.getElementById('hiddenReportAktivasiBarangContainer');
            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (globalReportAktivasiItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyReportAktivasiBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalReportAktivasiItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 border-b border-slate-100';
                tr.innerHTML = '<td class="py-1.5 px-3 font-semibold text-slate-800">' + item.nama_barang + '</td>' +
                               '<td class="py-1.5 px-3 text-center text-slate-700 font-bold">' + item.jumlah + '</td>' +
                               '<td class="py-1.5 px-3 text-center"><button type="button" onclick="removeReportAktivasiBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-bold text-xs"><i class="fa-solid fa-trash-can"></i></button></td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'items[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJml = document.createElement('input');
                inputJml.type = 'hidden';
                inputJml.name = 'items[' + idx + '][jumlah]';
                inputJml.value = item.jumlah;

                hiddenContainer.appendChild(inputKode);
                hiddenContainer.appendChild(inputJml);
            });
        }

        @if($isNoc ?? false)
        // ── NOC: Modal Jadwal Aktivasi ──
        let globalAktivasiItems = [];

        function openAktivasiModal(nomor, nama, dateVal, timeVal, teamVal, popVal, mediaAksesVal, indexOltVal, noteVal, existingItemsJson) {
            const form = document.getElementById('formAktivasi');
            form.action = '/pendaftaran/' + encodeURIComponent(nomor) + '/jadwal-aktivasi';

            document.getElementById('aktivasiNamaHeader').textContent = nama || '-';
            document.getElementById('aktivasiDate').value = dateVal || '';

            // Waktu aktivasi
            const timeSelect = document.getElementById('aktivasiTime');
            if (timeSelect) {
                timeSelect.value = timeVal || '';
            }

            // Team checkboxes
            const selectedTeams = teamVal ? teamVal.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.team-checkbox').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            // POP
            const popSelect = document.getElementById('aktivasiPop');
            if (popSelect) popSelect.value = popVal || '';

            // Media Akses
            const mediaSelect = document.getElementById('aktivasiMediaAkses');
            if (mediaSelect) mediaSelect.value = mediaAksesVal || '';

            // Index OLT
            const indexOltInput = document.getElementById('aktivasiIndexOlt');
            if (indexOltInput) indexOltInput.value = indexOltVal || '';

            // Note
            const noteTextarea = document.getElementById('aktivasiNote');
            if (noteTextarea) noteTextarea.value = noteVal || '';

            // Existing items
            globalAktivasiItems = [];
            if (existingItemsJson) {
                try {
                    const parsed = typeof existingItemsJson === 'string' ? JSON.parse(existingItemsJson) : existingItemsJson;
                    if (Array.isArray(parsed)) {
                        globalAktivasiItems = parsed.map(function(item) {
                            return {
                                kode_barang: item.kode_barang,
                                nama_barang: item.nama_barang,
                                jumlah: item.jumlah_barang || item.jumlah || 1,
                                satuan: item.satuan || 'UNIT'
                            };
                        });
                    }
                } catch (e) {
                    console.error(e);
                }
            }
            renderAktivasiBarangTable();

            document.getElementById('modalAktivasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAktivasiModal() {
            document.getElementById('modalAktivasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function addAktivasiBarang() {
            const select = document.getElementById('inputKodeBarang');
            const jumlahInput = document.getElementById('inputJumlahBarang');

            if (!select || !select.value) {
                alert('Pilih perangkat terlebih dahulu.');
                return;
            }

            const kodeBarang = select.value;
            const opt = select.options[select.selectedIndex];
            const namaBarang = opt.getAttribute('data-nama') || opt.text;
            const satuan = opt.getAttribute('data-satuan') || 'UNIT';
            const jumlah = parseInt(jumlahInput.value) || 1;

            const existingIndex = globalAktivasiItems.findIndex(function(i) { return i.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalAktivasiItems[existingIndex].jumlah += jumlah;
            } else {
                globalAktivasiItems.push({
                    kode_barang: kodeBarang,
                    nama_barang: namaBarang,
                    jumlah: jumlah,
                    satuan: satuan
                });
            }

            select.value = '';
            jumlahInput.value = 1;
            renderAktivasiBarangTable();
        }

        function removeAktivasiBarang(index) {
            globalAktivasiItems.splice(index, 1);
            renderAktivasiBarangTable();
        }

        function renderAktivasiBarangTable() {
            const tbody = document.getElementById('tableAktivasiBarang');
            const hiddenContainer = document.getElementById('hiddenBarangContainer');
            if (!tbody || !hiddenContainer) return;

            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (globalAktivasiItems.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="py-4 text-center text-slate-400 italic">Belum ada perangkat ditambahkan</td></tr>';
                return;
            }

            globalAktivasiItems.forEach(function(item, idx) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 border-b border-slate-100';
                tr.innerHTML = `
                    <td class="py-2.5 px-3 font-semibold text-slate-700">${item.nama_barang}</td>
                    <td class="py-2.5 px-3 text-center text-slate-600 font-bold">${item.jumlah} ${item.satuan}</td>
                    <td class="py-2.5 px-3 text-center">
                        <button type="button" onclick="removeAktivasiBarang(${idx})" class="text-rose-500 hover:text-rose-700 text-sm">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);

                hiddenContainer.innerHTML += `
                    <input type="hidden" name="items[${idx}][kode_barang]" value="${item.kode_barang}">
                    <input type="hidden" name="items[${idx}][jumlah]" value="${item.jumlah}">
                `;
            });
        }

        // Stub functions so ESC handler doesn't break
        function openModal() {}
        function closeModal() {}
        function closeHapusModal() {}
        function konfirmasiHapus() {}
        @else
        // ── Non-NOC: Modal Registration ──
        function openModal() {
            document.getElementById('modalRegistrasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (typeof updateModalOffset === 'function') updateModalOffset();
            const inputId = document.getElementById('inputIdPerusahaan');
            if (inputId && !inputId.value.trim() && typeof window.refreshAutoIdPerusahaan === 'function') {
                window.refreshAutoIdPerusahaan();
            }
        }
        
        function closeModal() {
            document.getElementById('modalRegistrasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Quick scroll to form section
        function scrollToSection(sectionId) {
            const form = document.getElementById('formRegistrasi');
            const target = document.getElementById(sectionId);
            if (form && target) {
                const topPos = target.offsetTop - form.offsetTop - 12;
                form.scrollTo({ top: topPos, behavior: 'smooth' });
            }
        }



        // Modal Konfirmasi Hapus
        function konfirmasiHapus(nomorInternet) {
            const form = document.getElementById('formHapus');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet);
            document.getElementById('hapusNomorInternet').textContent = nomorInternet;
            document.getElementById('modalHapus').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeHapusModal() {
            document.getElementById('modalHapus').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Stub so ESC handler doesn't break
        function openAktivasiModal() {}
        function closeAktivasiModal() {}
        @endif

        // Preview file upload
        function previewFile(input, previewId) {
            const fileName = input.files[0]?.name;
            input.parentElement.querySelector('.file-name').textContent = fileName || '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Cascading Dropdown Wilayah
        function setupCascading(prefix) {
            const prov = document.getElementById('prov' + prefix);
            const kota = document.getElementById('kota' + prefix);
            const kec = document.getElementById('kec' + prefix);
            const kel = document.getElementById('kel' + prefix);

            if (!prov || !kota || !kec || !kel) return;

            prov.addEventListener('change', function() {
                kota.innerHTML = '<option value="">Memuat...</option>';
                kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
                kel.innerHTML = '<option value="">Pilih Kelurahan</option>';
                if (!this.value) { kota.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>'; return; }
                fetch('{{ route("api.kota") }}?provinsi=' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        kota.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        data.forEach(k => kota.innerHTML += `<option value="${k.kode_wilayah_kota}">${k.nama_kota}</option>`);
                    });
            });

            kota.addEventListener('change', function() {
                kec.innerHTML = '<option value="">Memuat...</option>';
                kel.innerHTML = '<option value="">Pilih Kelurahan</option>';
                if (!this.value) { kec.innerHTML = '<option value="">Pilih Kecamatan</option>'; return; }
                fetch('{{ route("api.kecamatan") }}?kota=' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        data.forEach(k => kec.innerHTML += `<option value="${k.kode_wilayah_kecamatan}">${k.nama_kecamatan}</option>`);
                    });
            });

            kec.addEventListener('change', function() {
                kel.innerHTML = '<option value="">Memuat...</option>';
                if (!this.value) { kel.innerHTML = '<option value="">Pilih Kelurahan</option>'; return; }
                fetch('{{ route("api.kelurahan") }}?kecamatan=' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        kel.innerHTML = '<option value="">Pilih Kelurahan</option>';
                        data.forEach(k => kel.innerHTML += `<option value="${k.kode_wilayah_kelurahan}">${k.nama_kelurahan}</option>`);
                    });
            });
        }

        setupCascading('Ktp');
        setupCascading('Pasang');

        // Auto fill harga default saat Kapasitas Layanan dipilih, tapi tetep bisa diketik manual oleh user
        const selectPaket = document.getElementById('selectPaket');
        if (selectPaket) {
            selectPaket.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const harga = selectedOpt ? selectedOpt.getAttribute('data-harga') : null;
                const hargaInput = document.getElementById('hargaPaket');
                if (hargaInput && harga) {
                    hargaInput.value = 'Rp ' + parseInt(harga).toLocaleString('id-ID');
                }
            });
        }

        // ============================================
        // CHECKBOX "SAMA DENGAN ALAMAT PERUSAHAAN" - AUTO FILL
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            // Checkbox Corporate -> Toggle Nama PIC
            const checkboxCorporate = document.getElementById('checkboxCorporate');
            const inputPic = document.getElementById('inputPic');
            
            function updatePicState() {
                if (!checkboxCorporate || !inputPic) return;
                if (checkboxCorporate.checked) {
                    inputPic.disabled = false;
                    inputPic.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    inputPic.classList.add('bg-white');
                } else {
                    inputPic.disabled = true;
                    inputPic.value = '';
                    inputPic.classList.remove('bg-white');
                    inputPic.classList.add('bg-gray-100', 'cursor-not-allowed');
                }
            }

            if (checkboxCorporate) {
                updatePicState();
                checkboxCorporate.addEventListener('change', updatePicState);
            }

            const checkbox = document.getElementById('checkboxSamaKTP');
            
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    
                    if (isChecked) {
                        // Copy Provinsi
                        const provKtp = document.getElementById('provKtp');
                        const provPasang = document.getElementById('provPasang');
                        if (provKtp && provPasang && provKtp.value) {
                            provPasang.value = provKtp.value;
                            provPasang.dispatchEvent(new Event('change'));
                        }
                        
                        // Wait for API to load, then copy Kota
                        setTimeout(() => {
                            const kotaKtp = document.getElementById('kotaKtp');
                            const kotaPasang = document.getElementById('kotaPasang');
                            if (kotaKtp && kotaPasang && kotaKtp.value) {
                                kotaPasang.value = kotaKtp.value;
                                kotaPasang.dispatchEvent(new Event('change'));
                            }
                            
                            // Wait for API to load, then copy Kecamatan
                            setTimeout(() => {
                                const kecKtp = document.getElementById('kecKtp');
                                const kecPasang = document.getElementById('kecPasang');
                                if (kecKtp && kecPasang && kecKtp.value) {
                                    kecPasang.value = kecKtp.value;
                                    kecPasang.dispatchEvent(new Event('change'));
                                }
                                
                                // Wait for API to load, then copy Kelurahan
                                setTimeout(() => {
                                    const kelKtp = document.getElementById('kelKtp');
                                    const kelPasang = document.getElementById('kelPasang');
                                    if (kelKtp && kelPasang && kelKtp.value) {
                                        kelPasang.value = kelKtp.value;
                                    }
                                }, 500);
                            }, 500);
                        }, 500);
                        
                        // Copy RT, RW, Alamat (langsung)
                        const rtKtp = document.getElementById('rtKtp');
                        const rtPasang = document.getElementById('rtPasang');
                        if (rtKtp && rtPasang) rtPasang.value = rtKtp.value;
                        
                        const rwKtp = document.getElementById('rwKtp');
                        const rwPasang = document.getElementById('rwPasang');
                        if (rwKtp && rwPasang) rwPasang.value = rwKtp.value;
                        
                        const noBangunanPerusahaan = document.getElementById('noBangunanPerusahaan');
                        const noBangunanPasang = document.getElementById('noBangunanPasang');
                        if (noBangunanPerusahaan && noBangunanPasang) noBangunanPasang.value = noBangunanPerusahaan.value;

                        const alamatKtp = document.getElementById('alamatKtp');
                        const alamatPasang = document.getElementById('alamatPasang');
                        if (alamatKtp && alamatPasang) alamatPasang.value = alamatKtp.value;

                        const lonLatCorp = document.getElementById('lonLatPerusahaan');
                        const lonLatPasang = document.getElementById('lonLatPasang');
                        if (lonLatCorp && lonLatPasang) lonLatPasang.value = lonLatCorp.value;

                        const sharelockCorp = document.getElementById('sharelockPerusahaan');
                        const sharelockPasang = document.getElementById('sharelockPasang');
                        if (sharelockCorp && sharelockPasang) sharelockPasang.value = sharelockCorp.value;
                    } else {
                        // Clear semua field Pemasangan
                        const provPasang = document.getElementById('provPasang');
                        if (provPasang) {
                            provPasang.value = '';
                            provPasang.dispatchEvent(new Event('change'));
                        }
                        
                        const kotaPasang = document.getElementById('kotaPasang');
                        if (kotaPasang) kotaPasang.value = '';
                        
                        const kecPasang = document.getElementById('kecPasang');
                        if (kecPasang) kecPasang.value = '';
                        
                        const kelPasang = document.getElementById('kelPasang');
                        if (kelPasang) kelPasang.value = '';
                        
                        const rtPasang = document.getElementById('rtPasang');
                        if (rtPasang) rtPasang.value = '';
                        
                        const rwPasang = document.getElementById('rwPasang');
                        if (rwPasang) rwPasang.value = '';
                        
                        const noBangunanPasang = document.getElementById('noBangunanPasang');
                        if (noBangunanPasang) noBangunanPasang.value = '';

                        const alamatPasang = document.getElementById('alamatPasang');
                        if (alamatPasang) alamatPasang.value = '';

                        const lonLatPasang = document.getElementById('lonLatPasang');
                        if (lonLatPasang) lonLatPasang.value = '';

                        const sharelockPasang = document.getElementById('sharelockPasang');
                        if (sharelockPasang) sharelockPasang.value = '';
                    }
                });
            }

            // ============================================
            // HELPER: CASCADING DROPDOWN WILAYAH (ASYNC)
            // ============================================
            async function populateCascadingWilayah(prefix, provVal, kotaVal, kecVal, kelVal) {
                const provSelect = document.getElementById('prov' + prefix);
                const kotaSelect = document.getElementById('kota' + prefix);
                const kecSelect  = document.getElementById('kec' + prefix);
                const kelSelect  = document.getElementById('kel' + prefix);

                if (!provSelect || !provVal) return;

                provSelect.value = provVal;

                try {
                    // 1. Fetch Kota
                    const rKota = await fetch('/api/kota?provinsi=' + encodeURIComponent(provVal));
                    const kotas = await rKota.json();
                    if (kotaSelect) {
                        kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        kotas.forEach(k => {
                            const sel = (k.kode_wilayah_kota == kotaVal) ? 'selected' : '';
                            kotaSelect.innerHTML += `<option value="${k.kode_wilayah_kota}" ${sel}>${k.nama_kota}</option>`;
                        });
                        if (kotaVal) kotaSelect.value = kotaVal;
                    }

                    // 2. Fetch Kecamatan
                    if (kotaVal) {
                        const rKec = await fetch('/api/kecamatan?kota=' + encodeURIComponent(kotaVal));
                        const kecs = await rKec.json();
                        if (kecSelect) {
                            kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                            kecs.forEach(kc => {
                                const sel = (kc.kode_wilayah_kecamatan == kecVal) ? 'selected' : '';
                                kecSelect.innerHTML += `<option value="${kc.kode_wilayah_kecamatan}" ${sel}>${kc.nama_kecamatan}</option>`;
                            });
                            if (kecVal) kecSelect.value = kecVal;
                        }
                    }

                    // 3. Fetch Kelurahan
                    if (kecVal) {
                        const rKel = await fetch('/api/kelurahan?kecamatan=' + encodeURIComponent(kecVal));
                        const kels = await rKel.json();
                        if (kelSelect) {
                            kelSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
                            kels.forEach(kl => {
                                const sel = (kl.kode_wilayah_kelurahan == kelVal) ? 'selected' : '';
                                kelSelect.innerHTML += `<option value="${kl.kode_wilayah_kelurahan}" ${sel}>${kl.nama_kelurahan}</option>`;
                            });
                            if (kelVal) kelSelect.value = kelVal;
                        }
                    }
                } catch (err) {
                    console.error('Error loading cascading wilayah for ' + prefix + ':', err);
                }
            }

            // ============================================
            // AUTO-FILL IDENTITAS & ALAMAT PERUSAHAAN (FORM 1 - 3)
            // WITH USER CONFIRMATION MODAL
            // ============================================
            const inputId = document.getElementById('inputIdPerusahaan');
            const alertBadge = document.getElementById('autoFillAlert');
            const btnReopen = document.getElementById('btnReopenAutoFill');

            window.pendingCompanyData = null;

            window.openConfirmAutoFillModal = function() {
                if (!window.pendingCompanyData) return;
                const modal = document.getElementById('modalConfirmAutoFillCompany');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            };

            window.closeConfirmAutoFillModal = function() {
                const modal = document.getElementById('modalConfirmAutoFillCompany');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            };

            window.cancelAutoFillCompany = function() {
                window.closeConfirmAutoFillModal();
                if (btnReopen && window.pendingCompanyData) {
                    btnReopen.classList.remove('hidden');
                }
            };

            // ============================================
            // RESET SELURUH FORM PENDAFTARAN
            // ============================================
            window.resetFormRegistrasi = function() {
                const form = document.getElementById('formRegistrasi');
                if (!form) return;

                // 1. Reset nilai input dan textarea
                const inputs = form.querySelectorAll('input:not([type="hidden"]):not([name="_token"]):not([name="_method"]), textarea');
                inputs.forEach(el => {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else if (el.name !== 'tanggal_registrasi') {
                        el.value = '';
                    }
                });

                // 2. Reset semua select dropdown ke opsi pertama
                const selects = form.querySelectorAll('select');
                selects.forEach(sel => {
                    sel.selectedIndex = 0;
                });

                // Reset cascading wilayah KTP
                const kotaKtp = document.getElementById('kotaKtp');
                if (kotaKtp) kotaKtp.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                const kecKtp = document.getElementById('kecKtp');
                if (kecKtp) kecKtp.innerHTML = '<option value="">Pilih Kecamatan</option>';
                const kelKtp = document.getElementById('kelKtp');
                if (kelKtp) kelKtp.innerHTML = '<option value="">Pilih Kelurahan</option>';

                // Reset cascading wilayah Pemasangan
                const kotaPasang = document.getElementById('kotaPasang');
                if (kotaPasang) kotaPasang.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                const kecPasang = document.getElementById('kecPasang');
                if (kecPasang) kecPasang.innerHTML = '<option value="">Pilih Kecamatan</option>';
                const kelPasang = document.getElementById('kelPasang');
                if (kelPasang) kelPasang.innerHTML = '<option value="">Pilih Kelurahan</option>';

                // 3. Reset image previews & file inputs
                const previewPo = document.getElementById('previewPo');
                if (previewPo) {
                    previewPo.src = '';
                    previewPo.classList.add('hidden');
                }
                const previewBangunan = document.getElementById('previewBangunan');
                if (previewBangunan) {
                    previewBangunan.src = '';
                    previewBangunan.classList.add('hidden');
                }
                const poText = document.getElementById('poText');
                if (poText) poText.textContent = 'Drag and drop a file here or click';
                const fotoText = document.getElementById('fotoText');
                if (fotoText) fotoText.textContent = 'Drag and drop a file here or click';

                // 4. Reset Corporate Checkbox & PIC synchronization
                const checkboxCorporate = document.getElementById('checkboxCorporate');
                if (checkboxCorporate) {
                    checkboxCorporate.checked = false;
                    checkboxCorporate.dispatchEvent(new Event('change'));
                }

                // 5. Reset Tanggal Registrasi ke Hari Ini
                const tglInput = form.querySelector('[name="tanggal_registrasi"]');
                if (tglInput) {
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    tglInput.value = `${yyyy}-${mm}-${dd}`;
                }

                // 6. Reset state data auto-fill
                window.pendingCompanyData = null;
                if (alertBadge) alertBadge.classList.add('hidden');
                if (btnReopen) btnReopen.classList.add('hidden');

                // 7. Generate fresh new ID Perusahaan
                if (typeof window.refreshAutoIdPerusahaan === 'function') {
                    window.refreshAutoIdPerusahaan();
                }

                // Scroll kembali ke Section 1 paling atas
                const scrollContainer = form.querySelector('.overflow-y-auto');
                if (scrollContainer) {
                    scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                }
            };

            // ============================================
            // HELPER REFRESH AUTO-ID PERUSAHAAN (isp-nomor-tahun)
            // ============================================
            window.refreshAutoIdPerusahaan = function(callback) {
                const tglInput = document.querySelector('#formRegistrasi [name="tanggal_registrasi"]');
                const year = tglInput && tglInput.value ? tglInput.value.substring(0, 4) : new Date().getFullYear();
                
                const btnRefresh = document.getElementById('btnRefreshAutoId');
                if (btnRefresh) btnRefresh.querySelector('i')?.classList.add('animate-spin');
                
                fetch('/api/generate-id-perusahaan?year=' + encodeURIComponent(year))
                    .then(r => r.json())
                    .then(res => {
                        if (res.success && res.id_perusahaan) {
                            if (inputId) inputId.value = res.id_perusahaan;
                            if (alertBadge) alertBadge.classList.add('hidden');
                            if (btnReopen) btnReopen.classList.add('hidden');
                            window.pendingCompanyData = null;
                            if (typeof callback === 'function') callback(res.id_perusahaan);
                        }
                    })
                    .catch(e => console.error('Error generating ID:', e))
                    .finally(() => {
                        if (btnRefresh) btnRefresh.querySelector('i')?.classList.remove('animate-spin');
                    });
            };

            window.applyAutoFillCompany = function(dataToApply) {
                const d = dataToApply || window.pendingCompanyData;
                if (!d) {
                    window.closeConfirmAutoFillModal();
                    return;
                }

                // Set ID Perusahaan
                if (inputId && d.id_perusahaan) {
                    inputId.value = d.id_perusahaan;
                }

                // 1. Section 1: Informasi Pelanggan
                const fieldsSec1 = {
                    'nama_perusahaan': d.nama_perusahaan,
                    'no_telp_perusahaan': d.no_telp_perusahaan,
                    'email_perusahaan': d.email_perusahaan,
                    'nama_pic_teknis': d.nama_pic_teknis,
                    'no_telp_pic_teknis': d.no_telp_pic_teknis,
                    'email_pic_teknis': d.email_pic_teknis,
                    'nama_pic_keuangan': d.nama_pic_keuangan,
                    'no_telp_pic_keuangan': d.no_telp_pic_keuangan,
                    'email_pic_keuangan': d.email_pic_keuangan,
                    'jenis_perusahaan': d.jenis_perusahaan
                };

                for (const [name, value] of Object.entries(fieldsSec1)) {
                    if (value !== undefined && value !== null && value !== '') {
                        const el = document.querySelector(`#formRegistrasi [name="${name}"]`);
                        if (el) el.value = value;
                    }
                }

                // 2. Section 2: Alamat Perusahaan & Detail Perusahaan
                const fieldsSec2 = {
                    'rt_ktp': d.rt_ktp,
                    'rw_ktp': d.rw_ktp,
                    'nomor_bangunan_perusahaan': d.nomor_bangunan_perusahaan,
                    'alamat_ktp': d.alamat_ktp
                };

                for (const [name, value] of Object.entries(fieldsSec2)) {
                    if (value !== undefined && value !== null && value !== '') {
                        const el = document.querySelector(`#formRegistrasi [name="${name}"]`);
                        if (el) el.value = value;
                    }
                }

                const noBangunanCorp = document.getElementById('noBangunanPerusahaan') || document.querySelector('#formRegistrasi [name="nomor_bangunan_perusahaan"]');
                if (noBangunanCorp && d.nomor_bangunan_perusahaan) {
                    noBangunanCorp.value = d.nomor_bangunan_perusahaan;
                }

                const lonLatPerusahaan = document.getElementById('lonLatPerusahaan') || document.querySelector('#formRegistrasi [name="lon_lat_perusahaan"]');
                if (lonLatPerusahaan && d.lon_lat_perusahaan) {
                    lonLatPerusahaan.value = d.lon_lat_perusahaan;
                }
                
                const sharelockPerusahaan = document.getElementById('sharelockPerusahaan') || document.querySelector('#formRegistrasi [name="sharelock_perusahaan"]');
                if (sharelockPerusahaan && d.sharelock_perusahaan) {
                    sharelockPerusahaan.value = d.sharelock_perusahaan;
                }

                const jenisBangunanCorp = document.getElementById('jenisBangunanPerusahaan') || document.querySelector('#formRegistrasi [name="jenis_bangunan_perusahaan"]');
                if (jenisBangunanCorp && (d.jenis_bangunan || d.jenis_bangunan_perusahaan)) {
                    jenisBangunanCorp.value = d.jenis_bangunan || d.jenis_bangunan_perusahaan;
                }

                // Cascade Alamat Perusahaan (Section 2)
                if (d.provinsi_ktp) {
                    populateCascadingWilayah('Ktp', d.provinsi_ktp, d.kota_ktp, d.kecamatan_ktp, d.kelurahan_ktp);
                }

                // UI update: Sembunyikan button pemicu & Tampilkan notifikasi badge
                if (btnReopen) btnReopen.classList.add('hidden');
                if (alertBadge) {
                    alertBadge.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500"></i> Form 1-2 Otomatis Terisi (${d.id_perusahaan})`;
                    alertBadge.classList.remove('hidden');
                }

                window.closeConfirmAutoFillModal();
            };

            const inputNama = document.getElementById('inputNamaPerusahaan') || document.querySelector('#formRegistrasi [name="nama_perusahaan"]');

            // Handler ketika nama perusahaan ditulis / dipilih
            if (inputNama) {
                let nameSearchTimeout = null;
                let lastSearchedName = '';

                function handleNamaPerusahaanChange() {
                    let val = inputNama.value.trim();
                    if (!val) return;

                    // Bersihkan jika ada format "Nama (ID: isp-xxx)"
                    if (val.includes(' (ID: ')) {
                        val = val.split(' (ID: ')[0].trim();
                        inputNama.value = val;
                    }

                    if (val === lastSearchedName) return;
                    lastSearchedName = val;

                    fetch('/api/perusahaan-detail?id_perusahaan=' + encodeURIComponent(val))
                        .then(res => res.json())
                        .then(res => {
                            if (res.found && res.data) {
                                // Nama perusahaan sudah ada -> otomatis pakai ID yang sudah terdaftar & isi Form 1 & 2
                                window.pendingCompanyData = res.data;
                                window.applyAutoFillCompany(res.data);
                            } else {
                                // Nama perusahaan belum ada (baru) -> generate ID perusahaan otomatis jika belum ada
                                window.pendingCompanyData = null;
                                if (alertBadge) alertBadge.classList.add('hidden');
                                if (btnReopen) btnReopen.classList.add('hidden');

                                const curId = inputId ? inputId.value.trim() : '';
                                const isDefaultAutoId = /^isp-\d+-\d{4}$/i.test(curId);
                                if (!curId || !isDefaultAutoId) {
                                    window.refreshAutoIdPerusahaan();
                                }
                            }
                        })
                        .catch(err => console.error('Error lookup nama perusahaan:', err));
                }

                inputNama.addEventListener('change', handleNamaPerusahaanChange);
                inputNama.addEventListener('blur', handleNamaPerusahaanChange);
                inputNama.addEventListener('input', function() {
                    clearTimeout(nameSearchTimeout);
                    const cur = this.value.trim();
                    if (cur.length >= 3) {
                        nameSearchTimeout = setTimeout(handleNamaPerusahaanChange, 400);
                    }
                });
            }

            if (inputId) {
                let lastFetchedVal = '';
                let searchTimeout = null;

                function checkAutoFillCompany() {
                    let val = inputId.value.trim();
                    if (!val) return;

                    // If user selected "isp-001-2026 - PT Nama", extract clean ID
                    let cleanQuery = val;
                    if (val.includes(' - ')) {
                        cleanQuery = val.split(' - ')[0].trim();
                    }

                    if (cleanQuery === lastFetchedVal) return;

                    fetch('/api/perusahaan-detail?id_perusahaan=' + encodeURIComponent(cleanQuery))
                        .then(res => res.json())
                        .then(res => {
                            if (res.found && res.data) {
                                lastFetchedVal = cleanQuery;
                                inputId.value = res.data.id_perusahaan || cleanQuery;
                                window.pendingCompanyData = res.data;
                                window.applyAutoFillCompany(res.data);
                            } else {
                                window.pendingCompanyData = null;
                                if (alertBadge) alertBadge.classList.add('hidden');
                                if (btnReopen) btnReopen.classList.add('hidden');
                            }
                        })
                        .catch(err => console.error('Error fetching company detail:', err));
                }

                inputId.addEventListener('change', checkAutoFillCompany);
                inputId.addEventListener('blur', checkAutoFillCompany);
                inputId.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const curVal = this.value.trim();
                    if (curVal.includes(' - ') || curVal.length >= 3) {
                        searchTimeout = setTimeout(checkAutoFillCompany, 300);
                    } else {
                        lastFetchedVal = '';
                        window.pendingCompanyData = null;
                        if (alertBadge) alertBadge.classList.add('hidden');
                        if (btnReopen) btnReopen.classList.add('hidden');
                    }
                });

                // Sinkronisasi tahun jika user mengubah tanggal registrasi
                const tglInput = document.querySelector('#formRegistrasi [name="tanggal_registrasi"]');
                if (tglInput) {
                    tglInput.addEventListener('change', function() {
                        const curId = inputId.value.trim();
                        const match = curId.match(/^isp-(\d+)-(\d{4})$/i);
                        if (match && this.value) {
                            const newYear = this.value.substring(0, 4);
                            if (newYear && newYear !== match[2]) {
                                window.refreshAutoIdPerusahaan();
                            }
                        }
                    });
                }
            }
        });

        // Change Entries per page
        function changeEntries(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('entries', val);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Tutup modal dengan ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeHapusModal();
                if (typeof closeAktivasiModal === 'function') closeAktivasiModal();
                if (typeof window.closeConfirmAutoFillModal === 'function') window.closeConfirmAutoFillModal();
            }
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-gray-600 font-medium">Broadband</span>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-gray-800">Data Layanan Broadband</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="fa-solid fa-database text-[10px]"></i> Database ims_v3
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">
                    <i class="fa-solid fa-lock text-[9px]"></i> Read-Only
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-0.5">Integrasi data pendaftaran & koneksi internet dari tabel <code class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded font-mono text-[11px]">trx_batchjob_register</code></p>
        </div>
    </div>
</div>

{{-- Alert Notifikasi Database Error / Connection Notice --}}
@if(!$dbConnected)
    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl flex items-start gap-3.5 shadow-sm">
        <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-1 text-xs">
            <h4 class="font-bold text-sm text-amber-900 mb-1">Koneksi Database ims_v3 Belum Terhubung</h4>
            <p class="text-amber-700 leading-relaxed mb-2">
                Aplikasi belum dapat terhubung ke database kedua (<strong>ims_v3</strong>). Silakan pastikan variabel koneksi di file <code class="font-mono bg-amber-100/70 px-1 rounded">.env</code> telah diisi dengan benar:
            </p>
            <div class="bg-white/80 p-2.5 rounded-lg font-mono text-[11px] text-gray-700 border border-amber-200 space-y-0.5">
                <div>DB_V3_HOST=127.0.0.1 (atau IP server database)</div>
                <div>DB_V3_PORT=3306</div>
                <div>DB_V3_DATABASE=ims_v3</div>
                <div>DB_V3_USERNAME=username_anda</div>
                <div>DB_V3_PASSWORD=password_anda</div>
            </div>
            @if($connectionError)
                <p class="mt-2 text-[11px] text-amber-600 font-mono">Pesan Error Teknis: {{ $connectionError }}</p>
            @endif
        </div>
    </div>
@endif

@if (session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
        <div>
            <p class="font-semibold text-sm">Berhasil!</p>
            <p class="text-xs">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5"></i>
        <div>
            <p class="font-semibold text-sm">Pemberitahuan:</p>
            <ul class="text-xs list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Status Filter Cards (Semua, Aktif, Suspend) --}}
@php
    $currentStatus = strtolower((string) request('status', ''));
    $isAll = empty($currentStatus) || $currentStatus === 'semua';
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
    <!-- Semua Pelanggan -->
    <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'semua'])) }}"
       class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAll ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-blue-300 hover:shadow-sm' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAll ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:scale-105' }} transition-transform">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div>
                <p class="text-xs font-medium {{ $isAll ? 'text-blue-100' : 'text-gray-500' }}">Semua Data</p>
                <h4 class="text-lg font-bold leading-tight">{{ number_format($statusCounts['semua'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
        <i class="fa-solid fa-chevron-right text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
    </a>

    <!-- Aktif -->
    @php $isAktif = $currentStatus === 'aktif'; @endphp
    <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'aktif'])) }}"
       class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isAktif ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-emerald-300 hover:shadow-sm' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isAktif ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 group-hover:scale-105' }} transition-transform">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium {{ $isAktif ? 'text-emerald-100' : 'text-gray-500' }}">Aktif</p>
                <h4 class="text-lg font-bold leading-tight">{{ number_format($statusCounts['aktif'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
        <i class="fa-solid fa-chevron-right text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
    </a>

    <!-- Suspend -->
    @php $isSuspend = $currentStatus === 'suspend'; @endphp
    <a href="{{ route('broadband.index', array_merge(request()->except('page', 'status'), ['status' => 'suspend'])) }}"
       class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 group {{ $isSuspend ? 'bg-amber-600 text-white border-amber-600 shadow-md shadow-amber-500/20 scale-[1.01]' : 'bg-white text-gray-700 border-gray-100 hover:border-amber-300 hover:shadow-sm' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isSuspend ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 group-hover:scale-105' }} transition-transform">
                <i class="fa-solid fa-pause"></i>
            </div>
            <div>
                <p class="text-xs font-medium {{ $isSuspend ? 'text-amber-100' : 'text-gray-500' }}">Suspend</p>
                <h4 class="text-lg font-bold leading-tight">{{ number_format($statusCounts['suspend'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
        <i class="fa-solid fa-chevron-right text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
    </a>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('broadband.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        {{-- Preserve Status --}}
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        {{-- Pencarian --}}
        <div class="lg:col-span-2">
            <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Pencarian</label>
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari No. Internet, Pelanggan, NIK, Alamat, ONT..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
        </div>

        {{-- Filter POP --}}
        <div>
            <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1.5">POP</label>
            <select name="pop" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="ALL">Semua POP</option>
                @foreach ($popOptions as $pop)
                    <option value="{{ $pop }}" {{ request('pop') == $pop ? 'selected' : '' }}>{{ $pop }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Group Layanan --}}
        <div>
            <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Group Layanan</label>
            <select name="group_layanan" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="ALL">Semua Layanan</option>
                @foreach ($groupLayananOptions as $gl)
                    <option value="{{ $gl }}" {{ request('group_layanan') == $gl ? 'selected' : '' }}>{{ $gl }}</option>
                @endforeach
            </select>
        </div>

        {{-- Action Buttons & Entries --}}
        <div class="flex items-center gap-2">
            <select name="entries" class="px-2.5 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm shadow-blue-500/20">
                <i class="fa-solid fa-filter text-[11px]"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'pop', 'group_layanan', 'status', 'entries']))
                <a href="{{ route('broadband.index') }}" class="px-3 py-2 text-xs text-gray-500 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table Card --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-4">No. Internet & Tgl</th>
                    <th class="py-3 px-4">Pelanggan</th>
                    <th class="py-3 px-4">Paket / Bandwidth</th>
                    <th class="py-3 px-4">POP & Lokasi</th>
                    <th class="py-3 px-4">Perangkat & Media</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse ($broadbands as $row)
                    @php
                        $isTerminated = ($row->is_termin ?? '') == '1';
                        $isSuspended  = ($row->is_suspend ?? '') == '1';
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        {{-- No Internet & Tgl --}}
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="font-mono font-bold text-blue-600 text-[13px]">{{ $row->nomor_internet }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                <i class="fa-regular fa-calendar text-[10px] mr-1"></i>
                                {{ !empty($row->date_create) ? \Carbon\Carbon::parse($row->date_create)->format('d M Y') : '-' }}
                            </div>
                        </td>

                        {{-- Pelanggan --}}
                        <td class="py-3 px-4">
                            <div class="font-semibold text-gray-900 text-xs">{{ $row->nama_pelanggan ?: ($row->nama_penduduk ?? '-') }}</div>
                            <div class="text-[11px] text-gray-400 font-mono mt-0.5">
                                <i class="fa-regular fa-id-card text-[10px] mr-1"></i>NIK: {{ $row->nik_penduduk ?: '-' }}
                            </div>
                            @if(!empty($row->telp_pelanggan))
                                <div class="text-[11px] text-gray-500">
                                    <i class="fa-solid fa-phone text-[9px] mr-1 text-gray-400"></i>{{ $row->telp_pelanggan }}
                                </div>
                            @endif
                        </td>

                        {{-- Paket / Bandwidth --}}
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-800">
                                {{ $row->nominal_bandwith ?? $row->kode_bandwith ?? '-' }}
                            </div>
                            @if(!empty($row->nama_kategori_bandwith))
                                <div class="text-[10px] text-blue-600 font-medium mt-0.5">{{ $row->nama_kategori_bandwith }}</div>
                            @endif
                            @if(!empty($row->group_layanan))
                                <div class="text-[10px] text-gray-400 mt-0.5">Group: {{ $row->group_layanan }}</div>
                            @endif
                        </td>

                        {{-- POP & Lokasi --}}
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                                {{ $row->nama_pop ?? $row->kode_pop ?? '-' }}
                            </div>
                            <div class="text-[11px] text-gray-500 mt-0.5 line-clamp-1 max-w-[220px]" title="{{ $row->alamat_pasang }}">
                                {{ $row->alamat_pasang ?: '-' }}
                            </div>
                            @if(!empty($row->nama_kelurahan) || !empty($row->nama_kecamatan) || !empty($row->nama_kota))
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ implode(', ', array_filter([$row->nama_kelurahan ?? '', $row->nama_kecamatan ?? '', $row->nama_kota ?? ''])) }}
                                </div>
                            @endif
                        </td>

                        {{-- Perangkat & Media --}}
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="text-[11px] text-gray-700">
                                <span class="text-gray-400">ONT:</span>
                                <span class="font-mono font-medium">{{ $row->ont_us ?: '-' }}</span>
                            </div>
                            @if(!empty($row->olt))
                                <div class="text-[10px] text-gray-500">
                                    <span class="text-gray-400">OLT:</span> {{ $row->olt }}
                                </div>
                            @endif
                            @if(!empty($row->media_akses))
                                <div class="text-[10px] text-gray-400">{{ $row->media_akses }}</div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($isTerminated)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Terminasi
                                </span>
                            @elseif($isSuspended)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Suspend
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                        onclick="showQuickModal('{{ $row->nomor_internet }}')"
                                        class="px-2.5 py-1.5 text-xs text-gray-600 hover:text-blue-600 bg-gray-100 hover:bg-blue-50 rounded-lg transition-colors flex items-center gap-1"
                                        title="Lihat Cepat">
                                    <i class="fa-regular fa-eye"></i>
                                    <span class="hidden sm:inline">Preview</span>
                                </button>
                                <a href="{{ route('broadband.show', $row->nomor_internet) }}"
                                   class="px-2.5 py-1.5 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-1 shadow-sm"
                                   title="Lihat Detail Profil">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    <span class="hidden sm:inline">Detail</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 text-xl mb-3">
                                    <i class="fa-solid fa-wifi"></i>
                                </div>
                                <h4 class="font-semibold text-gray-700 text-sm mb-1">Belum Ada Data Broadband</h4>
                                <p class="text-xs text-gray-400 max-w-sm">
                                    @if(!$dbConnected)
                                        Koneksi ke database <code class="font-mono text-gray-600">ims_v3</code> belum aktif. Silakan atur konfigurasi .env terlebih dahulu.
                                    @else
                                        Tidak ditemukan data dengan kriteria pencarian atau filter yang Anda masukkan.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if ($broadbands->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $broadbands->links() }}
        </div>
    @endif
</div>

{{-- Quick View Modal (Ajax) --}}
<div id="quickModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeQuickModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-wifi"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800" id="modal-title">Preview Data Layanan Broadband</h3>
                        <p class="text-[11px] text-gray-400 font-mono" id="modal-no-internet">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-6 space-y-4" id="modal-body">
                <div class="flex items-center justify-center py-8 text-gray-400">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mr-2 text-blue-500"></i>
                    <span class="text-xs">Memuat data dari database ims_v3...</span>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between">
                <span class="text-[11px] text-gray-400">Mode: Read-only</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeQuickModal()" class="px-3.5 py-1.5 text-xs text-gray-600 hover:text-gray-800 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <a id="modal-detail-link" href="#" class="px-4 py-1.5 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors flex items-center gap-1.5 shadow-sm shadow-blue-500/20">
                        <span>Halaman Detail</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showQuickModal(noInternet) {
    const modal = document.getElementById('quickModal');
    const titleNo = document.getElementById('modal-no-internet');
    const body = document.getElementById('modal-body');
    const detailLink = document.getElementById('modal-detail-link');

    titleNo.innerText = noInternet;
    detailLink.href = "{{ url('/broadband') }}/" + encodeURIComponent(noInternet);
    body.innerHTML = `
        <div class="flex items-center justify-center py-8 text-gray-400">
            <i class="fa-solid fa-circle-notch fa-spin text-2xl mr-2 text-blue-500"></i>
            <span class="text-xs">Memuat data dari database ims_v3...</span>
        </div>
    `;

    modal.classList.remove('hidden');

    fetch("{{ url('/broadband') }}/" + encodeURIComponent(noInternet) + "/modal")
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                const d = res.data;
                const isTermin = (d.is_termin == '1');
                const isSuspend = (d.is_suspend == '1');
                let badgeStatus = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif</span>';
                if(isTermin) {
                    badgeStatus = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Terminasi</span>';
                } else if(isSuspend) {
                    badgeStatus = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Suspend</span>';
                }

                body.innerHTML = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <!-- Pelanggan -->
                        <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-2">Informasi Pelanggan</p>
                            <div class="space-y-1.5">
                                <div><span class="text-gray-400">Nama:</span> <strong class="text-gray-800">${d.nama_pelanggan || d.nama_penduduk || '-'}</strong></div>
                                <div><span class="text-gray-400">NIK:</span> <span class="font-mono text-gray-700">${d.nik_penduduk || '-'}</span></div>
                                <div><span class="text-gray-400">Telepon:</span> <span class="text-gray-700">${d.telp_pelanggan || '-'}</span></div>
                                <div><span class="text-gray-400">Status:</span> ${badgeStatus}</div>
                            </div>
                        </div>

                        <!-- Layanan -->
                        <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-2">Paket & Bandwidth</p>
                            <div class="space-y-1.5">
                                <div><span class="text-gray-400">Bandwidth:</span> <strong class="text-blue-600">${d.nominal_bandwith || d.kode_bandwith || '-'}</strong></div>
                                <div><span class="text-gray-400">Kategori:</span> <span class="text-gray-700">${d.nama_kategori_bandwith || '-'}</span></div>
                                <div><span class="text-gray-400">Group Layanan:</span> <span class="text-gray-700">${d.group_layanan || '-'}</span></div>
                                <div><span class="text-gray-400">Media Akses:</span> <span class="text-gray-700">${d.media_akses || '-'}</span></div>
                            </div>
                        </div>

                        <!-- Perangkat & Jaringan -->
                        <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-2">Perangkat & Jaringan</p>
                            <div class="space-y-1.5">
                                <div><span class="text-gray-400">POP:</span> <strong class="text-gray-800">${d.nama_pop || d.kode_pop || '-'}</strong></div>
                                <div><span class="text-gray-400">ONT User / PS:</span> <span class="font-mono text-gray-700">${d.ont_us || '-'} / ${d.ont_ps || '-'}</span></div>
                                <div><span class="text-gray-400">OLT / Index:</span> <span class="text-gray-700">${d.olt || '-'} (${d.index_olt || '-'})</span></div>
                                <div><span class="text-gray-400">Sales:</span> <span class="text-gray-700">${d.nama_sales || '-'}</span></div>
                            </div>
                        </div>

                        <!-- Alamat Pemasangan -->
                        <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-2">Alamat Pasang</p>
                            <div class="space-y-1.5 text-gray-700">
                                <p class="leading-relaxed">${d.alamat_pasang || '-'}</p>
                                <div class="text-[11px] text-gray-500">
                                    RT ${d.rt_pasang || '-'}/RW ${d.rw_pasang || '-'} No. ${d.nomor_bangunan || '-'}
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    ${[d.nama_kelurahan, d.nama_kecamatan, d.nama_kota].filter(Boolean).join(', ') || ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                body.innerHTML = `
                    <div class="p-4 text-center text-rose-600 text-xs">
                        <i class="fa-solid fa-triangle-exclamation text-xl mb-1"></i>
                        <p>${res.message || 'Gagal memuat data.'}</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            body.innerHTML = `
                <div class="p-4 text-center text-rose-600 text-xs">
                    <i class="fa-solid fa-triangle-exclamation text-xl mb-1"></i>
                    <p>Terjadi kesalahan: ${err.message}</p>
                </div>
            `;
        });
}

function closeQuickModal() {
    document.getElementById('quickModal').classList.add('hidden');
}
</script>
@endsection

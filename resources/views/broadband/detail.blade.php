@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <a href="{{ route('broadband.index') }}" class="hover:text-blue-500 transition-colors">Broadband</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-gray-600 font-medium">{{ $item->nomor_internet }}</span>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-gray-800">Detail Pelanggan Broadband</h1>
                @php
                    $isTerminated = ($item->is_termin ?? '') == '1';
                    $isSuspended  = ($item->is_suspend ?? '') == '1';
                @endphp
                @if($isTerminated)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Terminasi
                    </span>
                @elseif($isSuspended)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Suspend
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">
                    <i class="fa-solid fa-lock text-[9px]"></i> Read-Only
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-0.5">Sumber Data: Database <strong class="text-gray-700">ims_v3</strong> &bull; Tabel <code class="font-mono text-gray-600 bg-gray-100 px-1 py-0.5 rounded text-[11px]">trx_batchjob_register</code></p>
        </div>
        <div>
            <a href="{{ route('broadband.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

{{-- Top Profile Header Bar --}}
<div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 rounded-2xl p-6 text-white shadow-lg shadow-blue-900/10 mb-6 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none"></div>
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-2xl flex-shrink-0 text-blue-200">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-blue-200 uppercase tracking-wider">No. Internet</p>
                <h2 class="text-2xl font-mono font-bold tracking-tight text-white">{{ $item->nomor_internet }}</h2>
                <p class="text-sm text-blue-100 font-medium mt-0.5">
                    {{ $item->nama_pelanggan ?: ($item->nama_penduduk ?? 'Pelanggan IMS') }}
                    @if(!empty($item->nik_penduduk))
                        <span class="text-xs opacity-70 ml-2">&bull; NIK: {{ $item->nik_penduduk }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10 text-xs">
                <span class="text-blue-200 block text-[10px] uppercase font-semibold">Bandwidth</span>
                <span class="font-bold text-white text-sm">{{ $item->nominal_bandwith ?? $item->kode_bandwith ?? '-' }}</span>
            </div>
            <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10 text-xs">
                <span class="text-blue-200 block text-[10px] uppercase font-semibold">POP Wilayah</span>
                <span class="font-bold text-white text-sm">{{ $item->nama_pop ?? $item->kode_pop ?? '-' }}</span>
            </div>
            <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10 text-xs">
                <span class="text-blue-200 block text-[10px] uppercase font-semibold">Tgl Registrasi</span>
                <span class="font-bold text-white text-sm">{{ !empty($item->date_create) ? \Carbon\Carbon::parse($item->date_create)->format('d M Y') : '-' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri (2 Kolom): Detail Pelanggan, Layanan, Alamat, Perangkat --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- 1. Informasi Pelanggan & Kontak --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-user-circle text-blue-600"></i>
                    Informasi Pelanggan
                </div>
                <span class="text-[11px] text-gray-400">Master Data</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Nama Pelanggan</label>
                    <p class="font-semibold text-gray-800 text-sm">{{ $item->nama_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">NIK KTP</label>
                    <p class="font-mono font-semibold text-gray-800">{{ $item->nik_penduduk ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">No. Telepon / WhatsApp</label>
                    <p class="text-gray-800 font-medium">{{ $item->telp_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Email</label>
                    <p class="text-gray-800">{{ $item->email_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Mitra</label>
                    <p class="text-gray-800">{{ $item->mitra ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Status Onboarding App (is_login)</label>
                    @if(($item->is_login ?? 0) == 1)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-check text-[10px]"></i> Sudah Pernah Login
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600">
                            Belum Pernah Login
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. Paket Layanan & Bandwidth --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    Layanan & Bandwidth
                </div>
                <span class="text-[11px] text-gray-400">Paket Langganan</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Kode Bandwidth</label>
                    <p class="font-mono font-medium text-gray-800">{{ $item->kode_bandwith ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Kapasitas Bandwidth</label>
                    <p class="font-bold text-blue-600 text-sm">{{ $item->nominal_bandwith ?? $item->kode_bandwith ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Kategori Bandwidth</label>
                    <p class="text-gray-800 font-medium">{{ $item->nama_kategori_bandwith ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Harga Bandwidth Master</label>
                    <p class="font-mono text-gray-800">
                        {{ !empty($item->harga_bandwith) ? 'Rp ' . number_format($item->harga_bandwith, 0, ',', '.') : '-' }}
                    </p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Group Layanan</label>
                    <p class="text-gray-800 font-medium">{{ $item->group_layanan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Media Akses</label>
                    <p class="text-gray-800">{{ $item->media_akses ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- 3. Alamat Pemasangan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    Lokasi & Alamat Pemasangan
                </div>
                <span class="text-[11px] text-gray-400">Instalasi</span>
            </div>
            <div class="p-5 space-y-4 text-xs">
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Alamat Lengkap</label>
                    <p class="text-gray-800 font-medium leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">
                        {{ $item->alamat_pasang ?? '-' }}
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">RT / RW</label>
                        <p class="text-gray-800 font-mono">RT {{ $item->rt_pasang ?? '-' }} / RW {{ $item->rw_pasang ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">No. Bangunan</label>
                        <p class="text-gray-800 font-mono">{{ $item->nomor_bangunan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Jenis Bangunan</label>
                        <p class="text-gray-800">{{ $item->jenis_bangunan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Kode Kelurahan</label>
                        <p class="font-mono text-gray-800">{{ $item->kode_wilayah_kelurahan_pasang ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Kelurahan</label>
                        <p class="text-gray-800 font-medium">{{ $item->nama_kelurahan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Kecamatan</label>
                        <p class="text-gray-800 font-medium">{{ $item->nama_kecamatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Kota / Kabupaten</label>
                        <p class="text-gray-800 font-medium">{{ $item->nama_kota ?? '-' }}</p>
                    </div>
                </div>
                @if(!empty($item->lon_lat) || !empty($item->loc_maps))
                    <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center gap-4">
                        @if(!empty($item->lon_lat))
                            <div>
                                <span class="text-gray-400">Koordinat:</span>
                                <span class="font-mono text-gray-800 ml-1">{{ $item->lon_lat }}</span>
                            </div>
                        @endif
                        @if(!empty($item->loc_maps))
                            <a href="{{ $item->loc_maps }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fa-solid fa-map-location-dot"></i> Buka Google Maps
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- 4. Perangkat & Jaringan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-server text-indigo-600"></i>
                    Perangkat & Infrastruktur
                </div>
                <span class="text-[11px] text-gray-400">Network Info</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Point of Presence (POP)</label>
                    <p class="font-bold text-gray-800">{{ $item->nama_pop ?? $item->kode_pop ?? '-' }}</p>
                    <span class="font-mono text-[11px] text-gray-400">{{ $item->kode_pop }}</span>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Nama Sales</label>
                    <p class="text-gray-800 font-medium">{{ $item->nama_sales ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">ONT User (ont_us)</label>
                    <p class="font-mono text-gray-800 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">{{ $item->ont_us ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">ONT Password (ont_ps)</label>
                    <p class="font-mono text-gray-800 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">{{ $item->ont_ps ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Perangkat OLT</label>
                    <p class="font-medium text-gray-800">{{ $item->olt ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Index OLT</label>
                    <p class="font-mono text-gray-800">{{ $item->index_olt ?? '-' }}</p>
                </div>
                @if(!empty($item->note_request))
                    <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                        <label class="block text-gray-400 font-medium mb-1">Catatan Permintaan (note_request)</label>
                        <p class="text-gray-700 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $item->note_request }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Kanan (1 Kolom): Penagihan, Billing & Status System --}}
    <div class="space-y-6">

        {{-- Informasi Penagihan & Billing --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                    Billing & Pajak
                </div>
                <span class="text-[11px] text-gray-400">Finance</span>
            </div>
            <div class="p-5 space-y-3.5 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Periode Billing</span>
                    <span class="font-semibold text-gray-800">{{ $item->periode_billing ?? '-' }} Bulan</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Billing Terakhir</span>
                    <span class="font-semibold text-gray-800">{{ $item->last_month_billing ?? '-' }} / {{ $item->last_year_billing ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Status PPN</span>
                    <span class="font-medium text-gray-800">{{ $item->ppn ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Nominal PPN</span>
                    <span class="font-mono text-gray-800">{{ $item->ppn_nom ? 'Rp ' . number_format((float)$item->ppn_nom, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Potongan Diskon</span>
                    <span class="font-mono text-gray-800">{{ $item->potongan ? 'Rp ' . number_format((float)$item->potongan, 0, ',', '.') : '-' }}</span>
                </div>
                @if(!empty($item->potongan_note))
                    <div class="text-[11px] text-gray-500 bg-gray-50 p-2 rounded-lg">
                        <strong>Catatan Potongan:</strong> {{ $item->potongan_note }}
                    </div>
                @endif
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Dikenakan Denda</span>
                    <span class="font-medium text-gray-800">{{ ($item->is_denda ?? '') == '1' ? 'Ya' : 'Tidak' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500">Prorate</span>
                    <span class="font-medium text-gray-800">{{ ($item->prorate ?? '') == '1' ? 'Ya' : 'Tidak' }}</span>
                </div>
            </div>
        </div>

        {{-- Status Teknis & Suspend --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-sliders text-cyan-600"></i>
                    Status Teknis
                </div>
                <span class="text-[11px] text-gray-400">Controls</span>
            </div>
            <div class="p-5 space-y-3.5 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Status Registrasi</span>
                    <span class="font-semibold text-gray-800">{{ $item->desc_registrasi ?? $item->status_reg ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Status Suspend</span>
                    <span class="font-medium {{ ($item->is_suspend ?? '') == '1' ? 'text-amber-600 font-bold' : 'text-gray-800' }}">
                        {{ ($item->is_suspend ?? '') == '1' ? 'Suspend' : 'Normal' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Jumlah Suspend</span>
                    <span class="font-mono text-gray-800">{{ $item->count_suspend ?? 0 }} kali</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Status Terminasi</span>
                    <span class="font-medium {{ ($item->is_termin ?? '') == '1' ? 'text-rose-600 font-bold' : 'text-gray-800' }}">
                        {{ ($item->is_termin ?? '') == '1' ? 'Terminated' : 'Aktif' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500">Terkunci (islock)</span>
                    <span class="font-medium text-gray-800">{{ ($item->islock ?? '') == '1' ? 'Terkunci' : 'Tidak' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500">Terselubung (hide)</span>
                    <span class="font-medium text-gray-800">{{ ($item->hide ?? '') == '1' ? 'Ya' : 'Tidak' }}</span>
                </div>
            </div>
        </div>

        {{-- Metadata / Audit Trail --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-800 font-bold text-sm">
                    <i class="fa-solid fa-clock-rotate-left text-gray-500"></i>
                    Audit Trail
                </div>
                <span class="text-[11px] text-gray-400">Log</span>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block mb-0.5">Dibuat Pada</span>
                    <p class="text-gray-800 font-medium">
                        {{ !empty($item->date_create) ? \Carbon\Carbon::parse($item->date_create)->format('d M Y - H:i:s') : '-' }}
                    </p>
                    <span class="text-[11px] text-gray-400">Oleh: {{ $item->user_create ?? 'System' }}</span>
                </div>
                <div class="pt-2 border-t border-gray-100">
                    <span class="text-gray-400 block mb-0.5">Terakhir Diperbarui</span>
                    <p class="text-gray-800 font-medium">
                        {{ !empty($item->date_update) ? \Carbon\Carbon::parse($item->date_update)->format('d M Y - H:i:s') : '-' }}
                    </p>
                    <span class="text-[11px] text-gray-400">Oleh: {{ $item->user_update ?? 'System' }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

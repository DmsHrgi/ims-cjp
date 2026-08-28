@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen pb-12">
        <!-- Background Grid Pattern -->
        <div class="pointer-events-none absolute inset-0 -z-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.04) 1px, transparent 0); background-size: 24px 24px;"></div>

@php
    $u = session('user', []);
    $userLevel = strtoupper($u['level'] ?? '');
    $kodeLevel = $u['kode_level'] ?? '';
    $isAdmin = ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');
@endphp

        <!-- Header Navigasi & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">IMS</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('pelanggan') }}" class="hover:text-blue-600 transition-colors">Pelanggan</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-800 font-semibold">Profil Pelanggan</span>
            </nav>

            <div class="flex items-center gap-2.5 self-start sm:self-auto">

                <a href="javascript:history.back()" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-xl text-sm shadow-sm transition-all duration-200 hover:shadow active:scale-95">
                    <i class="fa-solid fa-arrow-left text-gray-500"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Main Layout: 2 Panel Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ========================================================= -->
            <!-- PANEL KIRI: Informasi Detail Pelanggan (col-span-4)       -->
            <!-- ========================================================= -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Kartu Header Profil -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-cyan-500"></div>

                    <div class="flex items-start gap-4 mb-5 pt-2">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold flex-shrink-0 shadow-sm">
                            <i class="fa-solid fa-building flex-shrink-0"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-md tracking-wide">
                                    {{ $customer->nomor_internet }}
                                </span>
                                @if(($customer->is_suspend ?? '0') == '1')
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">
                                        <i class="fa-solid fa-pause-circle text-[9px] mr-0.5"></i> Suspend
                                    </span>
                                @elseif($customer->desc_registrasi)
                                    <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">
                                        {{ $customer->desc_registrasi }}
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-lg font-bold text-gray-900 truncate mt-1">
                                {{ $customer->nama_perusahaan ?: $customer->nama_pelanggan ?: $customer->nama_penduduk ?: 'Tanpa Nama' }}
                            </h1>
                            <p class="text-xs font-medium text-blue-600 truncate mt-0.5">
                                <i class="fa-solid fa-wifi text-[10px] mr-1"></i>
                                {{ ($customer->paket ?? null) ?: ((($customer->nama_kategori_bandwith ?? '') . ' ' . ($customer->nominal_bandwith ?? '')) . ' Mbps') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 text-xs">
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">PIC Sales</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->nama_sales ?: $customer->pic ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Group Layanan</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->group_layanan ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Informasi Pelanggan (Enterprise / Corporate) -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-building-user text-blue-500"></i>
                        <span>Informasi Pelanggan</span>
                    </h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-50">
                            <span class="text-xs text-gray-500 font-medium">Nama Perusahaan</span>
                            <span class="text-xs font-bold text-gray-800 uppercase text-right">
                                {{ $customer->nama_perusahaan ?: $customer->nama_pelanggan ?: '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-50">
                            <span class="text-xs text-gray-500 font-medium">ID Perusahaan</span>
                            <span class="text-xs font-semibold text-gray-800">
                                {{ $customer->id_perusahaan ?: '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-50">
                            <span class="text-xs text-gray-500 font-medium">Jenis Perusahaan</span>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $customer->jenis_perusahaan ?: '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-50">
                            <span class="text-xs text-gray-500 font-medium">Tanggal Registrasi</span>
                            <span class="text-xs font-semibold text-gray-800">
                                {{ $customer->tanggal_registrasi ? \Carbon\Carbon::parse($customer->tanggal_registrasi)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-50">
                            <span class="text-xs text-gray-500 font-medium">No. Telp Perusahaan</span>
                            @if(!empty($customer->no_telp_perusahaan))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->no_telp_perusahaan) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                    <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                    {{ $customer->no_telp_perusahaan }}
                                </a>
                            @else
                                <span class="text-xs font-semibold text-gray-800">-</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-medium">Email Perusahaan</span>
                            <span class="text-xs font-semibold text-gray-800 truncate max-w-[180px]" title="{{ $customer->email_perusahaan }}">
                                {{ $customer->email_perusahaan ?: '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kartu PIC Teknis & PIC Keuangan -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-users text-indigo-500"></i>
                        <span>PIC Teknis & Keuangan</span>
                    </h2>

                    <div class="space-y-4">
                        <!-- Section PIC Teknis -->
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 pb-1.5">
                                <i class="fa-solid fa-headset text-blue-600 text-xs"></i>
                                <span class="text-xs font-bold text-gray-700">PIC Teknis</span>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama:</span>
                                    <span class="font-bold text-gray-800 uppercase">{{ $customer->nama_pic_teknis ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">No. HP:</span>
                                    @if(!empty($customer->no_telp_pic_teknis))
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->no_telp_pic_teknis) }}" target="_blank" class="font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                            <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                            {{ $customer->no_telp_pic_teknis }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-gray-800">-</span>
                                    @endif
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-semibold text-gray-800 truncate max-w-[170px]" title="{{ $customer->email_pic_teknis }}">{{ $customer->email_pic_teknis ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Section PIC Keuangan -->
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 pb-1.5">
                                <i class="fa-solid fa-calculator text-emerald-600 text-xs"></i>
                                <span class="text-xs font-bold text-gray-700">PIC Keuangan</span>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama:</span>
                                    <span class="font-bold text-gray-800 uppercase">{{ $customer->nama_pic_keuangan ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">No. HP:</span>
                                    @if(!empty($customer->no_telp_pic_keuangan))
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->no_telp_pic_keuangan) }}" target="_blank" class="font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                            <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                            {{ $customer->no_telp_pic_keuangan }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-gray-800">-</span>
                                    @endif
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-semibold text-gray-800 truncate max-w-[170px]" title="{{ $customer->email_pic_keuangan }}">{{ $customer->email_pic_keuangan ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Data Alamat -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                        <span>Data Alamat</span>
                    </h2>

                    <div class="space-y-4 text-xs">
                        <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100 space-y-1">
                            <span class="inline-block bg-gray-200 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded mb-1 uppercase">ALAMAT PERUSAHAAN</span>
                            <p class="text-gray-800 leading-relaxed font-semibold">
                                {{ $customer->alamat_ktp ?: '-' }}
                            </p>
                            @if(!empty($customer->rt_ktp) || !empty($customer->rw_ktp) || !empty($customer->nomor_bangunan_perusahaan))
                                <p class="text-[11px] text-gray-500 font-medium">
                                    RT {{ $customer->rt_ktp ?: '-' }} / RW {{ $customer->rw_ktp ?: '-' }}
                                    @if(!empty($customer->nomor_bangunan_perusahaan))
                                        • No/Blok: {{ $customer->nomor_bangunan_perusahaan }}
                                    @endif
                                </p>
                            @endif
                            @if(!empty($customer->nama_kelurahan_corp) || !empty($customer->nama_kota_corp))
                                <p class="text-[11px] text-gray-500 font-medium">
                                    {{ implode(', ', array_filter([$customer->nama_kelurahan_corp ?? null, $customer->nama_kecamatan_corp ?? null, $customer->nama_kota_corp ?? null, $customer->nama_provinsi_corp ?? null])) }}
                                </p>
                            @endif
                        </div>

                        <div class="bg-blue-50/50 p-3.5 rounded-xl border border-blue-100/60 space-y-1">
                            <span class="inline-block bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded mb-1 uppercase">ALAMAT PEMASANGAN</span>
                            <p class="text-gray-800 leading-relaxed font-semibold">
                                {{ $customer->alamat_pasang ?: '-' }}
                            </p>
                            @if(!empty($customer->rt_pasang) || !empty($customer->rw_pasang) || !empty($customer->nomor_bangunan))
                                <p class="text-[11px] text-gray-600 font-medium">
                                    RT {{ $customer->rt_pasang ?: '-' }} / RW {{ $customer->rw_pasang ?: '-' }}
                                    @if(!empty($customer->nomor_bangunan))
                                        • No/Blok: {{ $customer->nomor_bangunan }}
                                    @endif
                                </p>
                            @endif
                            @if(!empty($customer->nama_kelurahan_pasang) || !empty($customer->nama_kota_pasang))
                                <p class="text-[11px] text-gray-600 font-medium">
                                    {{ implode(', ', array_filter([$customer->nama_kelurahan_pasang ?? null, $customer->nama_kecamatan_pasang ?? null, $customer->nama_kota_corp ?? $customer->nama_kota_pasang ?? null, $customer->nama_provinsi_pasang ?? null])) }}
                                </p>
                            @endif
                            @if($customer->jenis_bangunan)
                                <p class="text-[11px] text-blue-600 mt-1 font-semibold">
                                    <i class="fa-solid fa-building text-[10px] mr-1"></i> Jenis Bangunan: {{ $customer->jenis_bangunan }}
                                </p>
                            @endif
                            @if(!empty($customer->note_request))
                                <div class="mt-2 pt-2 border-t border-blue-100/80 text-[11px] text-amber-700 font-medium bg-amber-50/60 p-2 rounded-lg">
                                    <i class="fa-solid fa-note-sticky text-amber-500 mr-1"></i> Catatan Khusus: {{ $customer->note_request }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kartu Pemilihan Kapasitas Layanan -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-wifi text-emerald-500"></i>
                        <span>Pemilihan Kapasitas Layanan</span>
                    </h2>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Kategori Layanan</span>
                            <span class="font-bold text-gray-800">{{ $customer->nama_kategori_bandwith ?: $customer->kode_kategori_bandwith ?: '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Group Layanan</span>
                            <span class="font-bold text-gray-800">{{ $customer->group_layanan ?: '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Kapasitas Layanan</span>
                            <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ ($customer->paket ?? null) ?: (($customer->nominal_bandwith ?? '-') . ' Mbps') }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Harga Layanan</span>
                            <span class="font-bold text-emerald-600 text-sm">
                                @if(!empty($customer->harga_paket))
                                    Rp {{ number_format((float) preg_replace('/[^0-9]/', '', $customer->harga_paket), 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-medium">PIC Sales</span>
                            <span class="font-bold text-gray-800 uppercase">{{ $customer->nama_sales ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Lokasi Geografis -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 overflow-hidden">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-emerald-500"></i>
                            <span>Lokasi Geografis</span>
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <!-- 1. Lokasi Geografis Perusahaan -->
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 space-y-2.5">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-1.5">
                                <span class="text-[11px] font-bold text-slate-700 uppercase flex items-center gap-1.5">
                                    <i class="fa-solid fa-building text-emerald-600"></i> Lokasi Geografis Perusahaan
                                </span>
                                @php
                                    $coordCorp = $customer->lon_lat_perusahaan ?? null;
                                    $rawMapCorp = $customer->sharelock_perusahaan ?: ($coordCorp ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($coordCorp)) : null);
                                    $mapsCorpUrl = null;
                                    if (!empty($rawMapCorp)) {
                                        $mapCorpTrim = trim($rawMapCorp);
                                        if (str_starts_with($mapCorpTrim, 'http://') || str_starts_with($mapCorpTrim, 'https://')) {
                                            $mapsCorpUrl = $mapCorpTrim;
                                        } elseif (str_starts_with($mapCorpTrim, 'maps.app.goo.gl') || str_starts_with($mapCorpTrim, 'goo.gl') || str_starts_with($mapCorpTrim, 'maps.google.com') || str_starts_with($mapCorpTrim, 'www.google.com') || str_starts_with($mapCorpTrim, 'google.com/maps')) {
                                            $mapsCorpUrl = 'https://' . $mapCorpTrim;
                                        } else {
                                            $mapsCorpUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapCorpTrim);
                                        }
                                    }
                                @endphp
                                @if(!empty($coordCorp))
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ addslashes($coordCorp) }}'); alert('Koordinat perusahaan berhasil disalin!')" class="text-blue-600 hover:text-blue-700 text-[10px] font-semibold flex items-center gap-1 transition-colors">
                                        <i class="fa-regular fa-copy"></i> Salin
                                    </button>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-medium uppercase block mb-0.5">Titik Koordinat:</span>
                                <p class="font-mono text-xs font-semibold text-gray-800 break-all leading-relaxed select-all">
                                    {{ $coordCorp ?: '-' }}
                                </p>
                            </div>
                            @if(!empty($mapsCorpUrl))
                                <a href="{{ $mapsCorpUrl }}" target="_blank" rel="noopener noreferrer" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3 rounded-xl text-[11px] flex items-center justify-center gap-2 shadow-xs transition-all duration-200">
                                    <i class="fa-solid fa-map-pin text-xs"></i>
                                    <span class="truncate">Buka Peta Lokasi Perusahaan</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] shrink-0"></i>
                                </a>
                            @else
                                <div class="text-[10px] text-gray-400 italic">Maps lokasi perusahaan tidak tersedia</div>
                            @endif
                        </div>

                        <!-- 2. Lokasi Geografis Pemasangan -->
                        <div class="bg-indigo-50/40 p-3.5 rounded-xl border border-indigo-100/60 space-y-2.5">
                            <div class="flex items-center justify-between border-b border-indigo-100 pb-1.5">
                                <span class="text-[11px] font-bold text-indigo-900 uppercase flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-crosshairs text-indigo-600"></i> Lokasi Geografis Pemasangan
                                </span>
                                @php
                                    $coordPasang = $customer->lon_lat_pasang ?: $customer->lon_lat;
                                    $rawMapPasang = $customer->sharelock_pasang ?: $customer->loc_maps ?: ($coordPasang ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($coordPasang)) : null);
                                    $mapsPasangUrl = null;
                                    if (!empty($rawMapPasang)) {
                                        $mapPasangTrim = trim($rawMapPasang);
                                        if (str_starts_with($mapPasangTrim, 'http://') || str_starts_with($mapPasangTrim, 'https://')) {
                                            $mapsPasangUrl = $mapPasangTrim;
                                        } elseif (str_starts_with($mapPasangTrim, 'maps.app.goo.gl') || str_starts_with($mapPasangTrim, 'goo.gl') || str_starts_with($mapPasangTrim, 'maps.google.com') || str_starts_with($mapPasangTrim, 'www.google.com') || str_starts_with($mapPasangTrim, 'google.com/maps')) {
                                            $mapsPasangUrl = 'https://' . $mapPasangTrim;
                                        } else {
                                            $mapsPasangUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapPasangTrim);
                                        }
                                    }
                                @endphp
                                @if(!empty($coordPasang))
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ addslashes($coordPasang) }}'); alert('Koordinat pemasangan berhasil disalin!')" class="text-indigo-600 hover:text-indigo-700 text-[10px] font-semibold flex items-center gap-1 transition-colors">
                                        <i class="fa-regular fa-copy"></i> Salin
                                    </button>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] text-indigo-400 font-medium uppercase block mb-0.5">Titik Koordinat:</span>
                                <p class="font-mono text-xs font-semibold text-gray-800 break-all leading-relaxed select-all">
                                    {{ $coordPasang ?: '-' }}
                                </p>
                            </div>
                            @if(!empty($mapsPasangUrl))
                                <a href="{{ $mapsPasangUrl }}" target="_blank" rel="noopener noreferrer" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-3 rounded-xl text-[11px] flex items-center justify-center gap-2 shadow-xs transition-all duration-200">
                                    <i class="fa-solid fa-map-pin text-xs"></i>
                                    <span class="truncate">Buka Peta Lokasi Pemasangan</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] shrink-0"></i>
                                </a>
                            @else
                                <div class="text-[10px] text-gray-400 italic">Maps lokasi pemasangan tidak tersedia</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================= -->
            <!-- PANEL KANAN: Tab Menu Administratif & Riwayat (col-span-8) -->
            <!-- ========================================================= -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full">

                    <!-- Tab Header Bar -->
                    <div class="border-b border-gray-100 bg-gray-50/60 px-4 pt-3 overflow-x-auto scrollbar-none">
                        <div class="flex items-center gap-1 min-w-max" id="tabHeaderContainer">
                            <button onclick="switchTab('log')" id="tab-btn-log" class="tab-btn active px-4 py-3 text-xs font-bold rounded-t-xl border-b-2 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left"></i> Log
                            </button>
                            <button onclick="switchTab('arsip')" id="tab-btn-arsip" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-folder-open"></i> Arsip
                            </button>
                            <button onclick="switchTab('layanan')" id="tab-btn-layanan" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-network-wired"></i> Layanan
                                @if(isset($ubahLayanan) && count($ubahLayanan)) <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold">{{ count($ubahLayanan) }}</span> @endif
                            </button>
                            <button onclick="switchTab('suspend')" id="tab-btn-suspend" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-pause"></i> Suspend
                                @if(count($suspends)) <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold">{{ count($suspends) }}</span> @endif
                            </button>
                            <button onclick="switchTab('tagihan')" id="tab-btn-tagihan" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-receipt"></i> Tagihan
                                @if(count($billings)) <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold">{{ count($billings) }}</span> @endif
                            </button>
                            <button onclick="switchTab('pengaduan')" id="tab-btn-pengaduan" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-headset"></i> Pengaduan
                                @if(count($pengaduan)) <span class="bg-rose-100 text-rose-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold">{{ count($pengaduan) }}</span> @endif
                            </button>
                            <button onclick="switchTab('perangkat')" id="tab-btn-perangkat" class="tab-btn px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 rounded-t-xl border-b-2 border-transparent transition-all flex items-center gap-2">
                                <i class="fa-solid fa-microchip"></i> Perangkat dsb.
                            </button>
                        </div>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6 flex-1 bg-white">

                        <!-- TAB 1: LOG (Default Active) -->
                        <div id="tab-content-log" class="tab-content space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">Riwayat Order & Aktivitas</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Struktur log proses pemasangan dan pembaruan status pelanggan</p>
                                </div>
                                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-semibold">
                                    Total: {{ count($logs) }} Log
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th class="py-3.5 px-4 w-44">Status Order</th>
                                            <th class="py-3.5 px-4">Keterangan</th>
                                            <th class="py-3.5 px-4 w-36">Tanggal Update</th>
                                            <th class="py-3.5 px-4 w-32">User Update</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @forelse($logs as $l)
                                            <tr class="hover:bg-blue-50/30 transition-colors">
                                                <td class="py-3.5 px-4 align-top">
                                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-2xs {{ $l->badge ?? 'bg-blue-100 text-blue-800' }}">
                                                        {{ $l->status }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 align-top text-gray-700 leading-relaxed">
                                                    {{ $l->keterangan }}
                                                </td>
                                                <td class="py-3.5 px-4 align-top text-gray-500 whitespace-nowrap">
                                                    {{ $l->tanggal ? \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y H:i') : '-' }}
                                                </td>
                                                <td class="py-3.5 px-4 align-top font-semibold text-gray-800 uppercase">
                                                    {{ $l->user }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-gray-400 italic">
                                                    Belum ada riwayat aktivitas tercatat
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: ARSIP (Dokumen / Foto / Map) -->
                        @php
                            $formatPhotoUrl = function($path) {
                                if (empty($path)) return null;
                                $trimmed = trim($path);
                                if (str_starts_with($trimmed, '<p>') || str_starts_with($trimmed, '<')) return null;
                                if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) return $trimmed;

                                $cleanPath = ltrim($trimmed, '/');
                                $cleanRelative = preg_replace('/^storage\//', '', $cleanPath);

                                // 1. Direct public asset check
                                if (file_exists(public_path($cleanPath))) {
                                    return asset($cleanPath);
                                }
                                if (file_exists(public_path('storage/' . $cleanRelative))) {
                                    return asset('storage/' . $cleanRelative);
                                }

                                // 2. Local storage check
                                $candidates = [
                                    storage_path('app/public/' . $cleanRelative),
                                    storage_path('app/public/foto_po/' . $cleanRelative),
                                    storage_path('app/public/foto_bangunan/' . $cleanRelative),
                                    storage_path('app/public/foto_ktp/' . $cleanRelative),
                                    storage_path('app/public/foto_rumah/' . $cleanRelative),
                                    storage_path('app/public/foto_peta/' . $cleanRelative),
                                    storage_path('app/' . $cleanRelative),
                                ];

                                foreach ($candidates as $cand) {
                                    if (file_exists($cand) && is_file($cand)) {
                                        return url('media-berkas/' . $cleanRelative);
                                    }
                                }

                                // 3. Fallback: stream through media-berkas route
                                if (!empty($cleanRelative)) {
                                    return url('media-berkas/' . $cleanRelative);
                                }

                                return null;
                            };

                            $ktpUrl = $formatPhotoUrl($customer->foto_po ?? $customer->foto_ktp ?? null);
                            $rumahUrl = $formatPhotoUrl($customer->foto_bangunan ?? $customer->foto_rumah ?? null);
                            $petaUrl = $formatPhotoUrl($customer->foto_peta ?? null);
                        @endphp
                        <div id="tab-content-arsip" class="tab-content hidden space-y-4">
                            <div class="pb-3 border-b border-gray-100">
                                <h3 class="text-sm font-bold text-gray-800">Berkas & Dokumen Pelanggan</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Arsip foto identitas, lokasi rumah, dan peta lokasi pemasangan</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <!-- Foto PO -->
                                <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 flex flex-col justify-between space-y-3">
                                    <div>
                                        <span class="text-xs font-bold text-gray-700 block">Foto PO</span>
                                        <p class="text-[11px] text-gray-400">Berkas PO resmi pendaftaran</p>
                                    </div>
                                    @if($ktpUrl)
                                        <div class="group relative rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-200 cursor-pointer" onclick="openPhotoModal('{{ $ktpUrl }}', 'Foto PO - {{ addslashes($customer->nama_pelanggan ?: $customer->nama_penduduk ?: '') }}')">
                                            <img src="{{ $ktpUrl }}" alt="Foto PO" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null; this.src=''; this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex'">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                            </div>
                                        </div>
                                        <div class="hidden h-28 rounded-lg border border-dashed border-gray-300 flex-col items-center justify-center text-gray-400 text-xs bg-white text-center p-2">
                                            <i class="fa-solid fa-file-excel text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto PO Tidak Ditemukan</span>
                                        </div>
                                    @else
                                        <div class="h-28 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 text-xs bg-white">
                                            <i class="fa-solid fa-file-lines text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto PO Kosong</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Foto Rumah -->
                                <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 flex flex-col justify-between space-y-3">
                                    <div>
                                        <span class="text-xs font-bold text-gray-700 block">Foto Rumah / Lokasi</span>
                                        <p class="text-[11px] text-gray-400">Fisik bangunan pemasangan</p>
                                    </div>
                                    @if($rumahUrl)
                                        <div class="group relative rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-200 cursor-pointer" onclick="openPhotoModal('{{ $rumahUrl }}', 'Foto Rumah - {{ addslashes($customer->nama_pelanggan ?: $customer->nama_penduduk ?: '') }}')">
                                            <img src="{{ $rumahUrl }}" alt="Foto Rumah" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null; this.src=''; this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex'">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                            </div>
                                        </div>
                                        <div class="hidden h-28 rounded-lg border border-dashed border-gray-300 flex-col items-center justify-center text-gray-400 text-xs bg-white text-center p-2">
                                            <i class="fa-solid fa-file-excel text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto Rumah Tidak Ditemukan</span>
                                        </div>
                                    @else
                                        <div class="h-28 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 text-xs bg-white">
                                            <i class="fa-solid fa-house text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto Rumah Kosong</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Foto Peta / Embed Map -->
                                <div class="bg-gray-50 rounded-xl border border-gray-100 p-3 flex flex-col justify-between space-y-3">
                                    <div>
                                        <span class="text-xs font-bold text-gray-700 block">Peta Lokasi / Pemasangan</span>
                                        <p class="text-[11px] text-gray-400">Titik lokasi & peta jaringan</p>
                                    </div>
                                    @if($petaUrl)
                                        <div class="group relative rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-200 cursor-pointer" onclick="openPhotoModal('{{ $petaUrl }}', 'Dokumen Peta - {{ addslashes($customer->nama_pelanggan ?: $customer->nama_penduduk ?: '') }}')">
                                            <img src="{{ $petaUrl }}" alt="Foto Peta" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" onerror="this.onerror=null; this.src=''; this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex'">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                            </div>
                                        </div>
                                        <div class="hidden h-28 rounded-lg border border-dashed border-gray-300 flex-col items-center justify-center text-gray-400 text-xs bg-white text-center p-2">
                                            <i class="fa-solid fa-map text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto Peta Tidak Ditemukan</span>
                                        </div>
                                    @elseif(!empty($customer->lon_lat) || !empty($customer->loc_maps))
                                        @php
                                            $mapTarget = !empty($customer->lon_lat) ? $customer->lon_lat : $customer->loc_maps;
                                            $mapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode(trim($mapTarget)) . '&t=&z=15&ie=UTF8&iwloc=&output=embed';
                                        @endphp
                                        <div class="rounded-lg overflow-hidden border border-emerald-200 aspect-video bg-slate-100 relative shadow-2xs">
                                            <iframe width="100%" height="100%" frameborder="0" style="border:0;" src="{{ $mapEmbedUrl }}" allowfullscreen loading="lazy"></iframe>
                                        </div>
                                    @else
                                        <div class="h-28 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 text-xs bg-white">
                                            <i class="fa-solid fa-map-location-dot text-2xl mb-1 text-gray-300"></i>
                                            <span>Foto Peta Kosong</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Seksi Master Dokumen & Scan Dokumen -->
                            <div class="pt-5 mt-4 border-t border-slate-100 space-y-6">
                                <!-- 1. Master Dokumen -->
                                <div class="space-y-3">
                                    <h3 class="text-sm font-bold text-slate-800">Master Dokumen</h3>
                                    <div class="flex flex-wrap gap-4">
                                        <!-- langganan.pdf (Form Berlangganan) -->
                                        <div class="w-36 h-36 bg-white rounded-xl border border-slate-200/90 hover:border-blue-400 p-3.5 flex flex-col justify-between items-center relative shadow-xs hover:shadow-md transition-all duration-200 group">
                                            <a href="{{ route('pelanggan.pdf', $customer->nomor_internet) }}" target="_blank" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-blue-600 transition-colors p-1" title="Download langganan.pdf">
                                                <i class="fa-solid fa-download text-xs"></i>
                                            </a>
                                            <div class="my-auto pt-2">
                                                <i class="fa-solid fa-file-lines text-blue-600 text-4xl group-hover:scale-105 transition-transform duration-200"></i>
                                            </div>
                                            <a href="{{ route('pelanggan.pdf', $customer->nomor_internet) }}" target="_blank" class="text-xs font-medium text-slate-500 group-hover:text-blue-600 text-center truncate w-full" title="langganan.pdf">
                                                langganan.pdf
                                            </a>
                                        </div>

                                        @if(!empty($customer->survey_date_start) || !empty($customer->survey_date_finish) || !empty($customer->survey_team) || !empty($customer->doc_survey) || !empty($customer->foto_peta))
                                        <!-- survey.pdf (Surat Tugas Survey) -->
                                        <div class="w-36 h-36 bg-white rounded-xl border border-slate-200/90 hover:border-blue-400 p-3.5 flex flex-col justify-between items-center relative shadow-xs hover:shadow-md transition-all duration-200 group">
                                            <a href="{{ route('pelanggan.pdf-survey', $customer->nomor_internet) }}" target="_blank" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-blue-600 transition-colors p-1" title="Download survey.pdf">
                                                <i class="fa-solid fa-download text-xs"></i>
                                            </a>
                                            <div class="my-auto pt-2">
                                                <i class="fa-solid fa-file-lines text-blue-600 text-4xl group-hover:scale-105 transition-transform duration-200"></i>
                                            </div>
                                            <a href="{{ route('pelanggan.pdf-survey', $customer->nomor_internet) }}" target="_blank" class="text-xs font-medium text-slate-500 group-hover:text-blue-600 text-center truncate w-full" title="survey.pdf">
                                                survey.pdf
                                            </a>
                                        </div>
                                        @endif

                                        @if(!empty($customer->instalasi_date_start) || !empty($customer->instalasi_date_finish) || !empty($customer->instalasi_team) || !empty($customer->doc_instalasi))
                                        <!-- instalasi.pdf (Surat Tugas Instalasi) -->
                                        <div class="w-36 h-36 bg-white rounded-xl border border-slate-200/90 hover:border-blue-400 p-3.5 flex flex-col justify-between items-center relative shadow-xs hover:shadow-md transition-all duration-200 group">
                                            <a href="{{ route('pelanggan.pdf-instalasi', $customer->nomor_internet) }}" target="_blank" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-blue-600 transition-colors p-1" title="Download instalasi.pdf">
                                                <i class="fa-solid fa-download text-xs"></i>
                                            </a>
                                            <div class="my-auto pt-2">
                                                <i class="fa-solid fa-file-lines text-blue-600 text-4xl group-hover:scale-105 transition-transform duration-200"></i>
                                            </div>
                                            <a href="{{ route('pelanggan.pdf-instalasi', $customer->nomor_internet) }}" target="_blank" class="text-xs font-medium text-slate-500 group-hover:text-blue-600 text-center truncate w-full" title="instalasi.pdf">
                                                instalasi.pdf
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- 2. Scan Dokumen (Presisi Sesuai Mockup Gambar: Berlangganan, Survey, Instalasi, Aktivasi) -->
                                <div class="space-y-3 pt-2">
                                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Scan Dokumen</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        @php
                                            $scanDocCards = [
                                                ['key' => 'berlangganan', 'title' => 'Berlangganan', 'file' => $customer->scan_dokumen ?? null],
                                                ['key' => 'survey', 'title' => 'Survey', 'file' => $customer->scan_dokumen_survey ?? null],
                                                ['key' => 'instalasi', 'title' => 'Instalasi', 'file' => $customer->scan_dokumen_instalasi ?? null],
                                                ['key' => 'aktivasi', 'title' => 'Aktivasi', 'file' => $customer->scan_dokumen_aktivasi ?? null],
                                            ];
                                        @endphp

                                        @foreach($scanDocCards as $docCard)
                                            <div class="w-full h-36 bg-white rounded-xl border border-slate-200/90 hover:border-blue-400 p-3.5 flex flex-col justify-between items-center relative shadow-xs hover:shadow-md transition-all duration-200 group cursor-pointer" onclick="openScanModal('{{ $docCard['key'] }}')">
                                                @if(!empty($docCard['file']))
                                                    <a href="{{ asset($docCard['file']) }}" target="_blank" onclick="event.stopPropagation()" class="absolute top-2.5 right-2.5 text-blue-500 hover:text-blue-700 transition-colors p-1" title="Download Scan Dokumen {{ $docCard['title'] }}">
                                                        <i class="fa-solid fa-download text-xs"></i>
                                                    </a>
                                                @else
                                                    <button type="button" onclick="openScanModal('{{ $docCard['key'] }}'); event.stopPropagation();" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-blue-600 transition-colors p-1" title="Upload Scan Dokumen {{ $docCard['title'] }}">
                                                        <i class="fa-solid fa-download text-xs"></i>
                                                    </button>
                                                @endif

                                                <div class="my-auto pt-2">
                                                    <i class="fa-solid fa-file-lines text-blue-600 text-4xl group-hover:scale-105 transition-transform duration-200"></i>
                                                </div>

                                                <div class="text-center w-full">
                                                    <p class="text-[12px] font-bold text-slate-800 leading-tight">{{ $docCard['title'] }}</p>
                                                    <p class="text-[11px] font-medium text-blue-600 leading-tight mt-0.5">Cetak / Unduh PDF</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: LAYANAN -->
                        <div id="tab-content-layanan" class="tab-content hidden space-y-5">
                            <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">Spesifikasi Layanan & Jaringan</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Rincian teknis konektivitas, bandwidth, dan perangkat POP</p>
                                </div>
                                <span class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-semibold border border-blue-100">
                                    {{ $customer->nama_kategori_bandwith ?: 'Layanan Aktif' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2.5">
                                    <h4 class="font-bold text-gray-800 border-b border-slate-200 pb-2 uppercase tracking-wider text-[11px] text-blue-600">Paket & Biaya</h4>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Kategori Bandwidth:</span>
                                        <span class="font-semibold text-gray-800">{{ $customer->nama_kategori_bandwith ?: '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Nominal Bandwidth:</span>
                                        <span class="font-bold text-blue-600">{{ $customer->nominal_bandwith ?: '0' }} Mbps</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Biaya Registrasi:</span>
                                        <span class="font-semibold text-gray-800">Rp {{ number_format((float) ($customer->biaya_reg ?? 0), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-500">Harga Bulanan:</span>
                                        <span class="font-semibold text-gray-800">Rp {{ number_format((float) ($customer->harga_bandwith ?? 0), 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2.5">
                                    <h4 class="font-bold text-gray-800 border-b border-slate-200 pb-2 uppercase tracking-wider text-[11px] text-indigo-600">Infrastruktur & Akses</h4>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Point of Presence (POP):</span>
                                        <span class="font-semibold text-gray-800">{{ $customer->nama_pop ?: $customer->kode_pop ?: '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Media Akses:</span>
                                        <span class="font-semibold text-gray-800">{{ $customer->media_akses ?: '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 border-b border-slate-100">
                                        <span class="text-gray-500">Index OLT:</span>
                                        <span class="font-mono font-semibold text-gray-800">{{ $customer->index_olt ?: '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-500">ONT US / PS:</span>
                                        <span class="font-mono font-semibold text-gray-800">{{ $customer->ont_us ?: '-' }} / {{ $customer->ont_ps ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Log / Riwayat Perubahan Layanan -->
                            <div class="pt-4 border-t border-slate-100 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800">Riwayat & Log Perubahan Layanan (Up/Downgrade)</h4>
                                        <p class="text-xs text-gray-400 mt-0.5">Daftar transaksi upgrade/downgrade paket layanan pelanggan</p>
                                    </div>
                                    <span class="text-xs bg-slate-100 text-slate-700 px-3 py-1 rounded-full font-semibold border border-slate-200">
                                        Total: {{ isset($ubahLayanan) ? count($ubahLayanan) : 0 }} Log
                                    </span>
                                </div>

                                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50/80 text-slate-700 font-bold tracking-wider border-b border-slate-100">
                                            <tr>
                                                <th class="py-3 px-4">Tanggal</th>
                                                <th class="py-3 px-4">Layanan Lama (Old)</th>
                                                <th class="py-3 px-4">Layanan Baru (New)</th>
                                                <th class="py-3 px-4">Status</th>
                                                <th class="py-3 px-4">Catatan / Keterangan</th>
                                                <th class="py-3 px-4">Petugas</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-medium">
                                            @forelse($ubahLayanan ?? [] as $u)
                                                @php
                                                    $stBg = 'bg-blue-100 text-blue-700';
                                                    if ($u->status_ubah_layanan == '11') $stBg = 'bg-cyan-100 text-cyan-700';
                                                    if ($u->status_ubah_layanan == '12') $stBg = 'bg-amber-100 text-amber-700';
                                                    if ($u->status_ubah_layanan == '13') $stBg = 'bg-emerald-100 text-emerald-700';
                                                    if ($u->status_ubah_layanan == '14') $stBg = 'bg-rose-100 text-rose-700';

                                                    $tgl = $u->date_request ? \Carbon\Carbon::parse($u->date_request)->format('d/m/Y') : ($u->date_create ? \Carbon\Carbon::parse($u->date_create)->format('d/m/Y H:i') : '-');
                                                    $tglSchedule = $u->date_schedule ? \Carbon\Carbon::parse($u->date_schedule)->format('d/m/Y') : null;
                                                @endphp
                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                    <td class="py-3 px-4 whitespace-nowrap">
                                                        <span class="font-semibold text-slate-800">{{ $tgl }}</span>
                                                        @if($tglSchedule)
                                                            <div class="text-[10px] text-amber-600 font-semibold mt-0.5">Jadwal: {{ $tglSchedule }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="font-bold text-slate-700 uppercase">{{ $u->nama_kategori_bandwith_lama ?: '-' }}</div>
                                                        <div class="text-[11px] text-slate-500">{{ $u->nominal_bandwith_lama ? $u->nominal_bandwith_lama . ' Mbps' : ($u->kode_bandwith_lama ?: '-') }}</div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="font-bold text-blue-700 uppercase">{{ $u->nama_kategori_bandwith_baru ?: '-' }}</div>
                                                        <div class="text-[11px] text-blue-600 font-semibold">{{ $u->nominal_bandwith_baru ? $u->nominal_bandwith_baru . ' Mbps' : ($u->kode_bandwith_baru ?: '-') }}</div>
                                                    </td>
                                                    <td class="py-3 px-4 whitespace-nowrap">
                                                        <span class="inline-block {{ $stBg }} text-[10px] font-bold px-2 py-0.5 rounded-full">
                                                            (KD{{ $u->status_ubah_layanan }}) {{ $u->desc_ubah_layanan ?: 'Request' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3 px-4 text-slate-600">
                                                        {{ $u->note_schedule ?: ($u->note_cancel ?: ($u->note_request ?: ($u->note_closing ?: '-'))) }}
                                                    </td>
                                                    <td class="py-3 px-4 text-slate-700 font-medium whitespace-nowrap">
                                                        {{ $u->user_update ?: ($u->user_create ?: 'System') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat / log perubahan layanan untuk pelanggan ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: SUSPEND -->
                        <div id="tab-content-suspend" class="tab-content hidden space-y-4">
                            <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">Riwayat Penangguhan (Suspend)</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Catatan penghentian sementara dan pembatalan suspend pelanggan</p>
                                </div>
                                <span class="text-xs bg-amber-50 text-amber-700 px-3 py-1 rounded-full font-semibold border border-amber-100">
                                    Total: {{ count($suspends) }} Log Suspend
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 text-slate-700 font-bold tracking-wider border-b border-slate-100">
                                        <tr>
                                            <th class="py-3.5 px-4">Tanggal Pengajuan</th>
                                            <th class="py-3.5 px-4">Periode Suspend</th>
                                            <th class="py-3.5 px-4">Alasan Suspend</th>
                                            <th class="py-3.5 px-4">Catatan / Detail Cancel</th>
                                            <th class="py-3.5 px-4">Status</th>
                                            <th class="py-3.5 px-4">Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-medium">
                                        @forelse($suspends as $s)
                                            @php
                                                $stBg = 'bg-amber-100 text-amber-700';
                                                if ($s->status_suspend == '11') $stBg = 'bg-amber-100 text-amber-700';
                                                if ($s->status_suspend == '12') $stBg = 'bg-blue-100 text-blue-700';
                                                if ($s->status_suspend == '13') $stBg = 'bg-emerald-100 text-emerald-700';
                                                if ($s->status_suspend == '14') $stBg = 'bg-rose-100 text-rose-700';

                                                $tglCreate = $s->date_create ? \Carbon\Carbon::parse($s->date_create)->format('d/m/Y H:i') : '-';
                                                $startStr = $s->suspend_start ? \Carbon\Carbon::parse($s->suspend_start)->format('d/m/Y') : '-';
                                                $endStr = $s->suspend_end ? \Carbon\Carbon::parse($s->suspend_end)->format('d/m/Y') : 'Sampai Sekarang';
                                            @endphp
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-800">{{ $tglCreate }}</td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <span class="text-slate-700 font-medium">Mulai: <strong>{{ $startStr }}</strong></span>
                                                    @if($s->suspend_end)
                                                        <div class="text-[11px] text-slate-500">Selesai: {{ $endStr }}</div>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $s->desc_suspend ?: '-' }}</td>
                                                <td class="py-3.5 px-4 text-slate-600">{{ $s->desc_suspend_cancel ?: '-' }}</td>
                                                <td class="py-3.5 px-4 whitespace-nowrap">
                                                    <span class="inline-block {{ $stBg }} text-[10px] font-bold px-2 py-0.5 rounded-full">
                                                        (KD{{ $s->status_suspend }}) {{ $s->desc_status_suspend ?: 'Suspend' }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-slate-700 font-medium whitespace-nowrap">
                                                    {{ $s->user_update ?: ($s->user_create ?: 'System') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat / log suspend untuk pelanggan ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 5: TAGIHAN -->
                        <div id="tab-content-tagihan" class="tab-content hidden space-y-4">
                            <div class="pb-3 border-b border-gray-100">
                                <h3 class="text-sm font-bold text-gray-800">Riwayat Tagihan & Billing</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Daftar invoice dan status pembayaran pelanggan</p>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th class="py-3 px-4">Nomor Billing</th>
                                            <th class="py-3 px-4">Periode</th>
                                            <th class="py-3 px-4">Nominal</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @forelse($billings as $b)
                                            <tr>
                                                <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $b->kode_billing_layanan ?? '-' }}</td>
                                                <td class="py-3 px-4 text-gray-700">{{ $b->periode_tagihan ?? (($b->bulan_tagihan ?? '-') . '/' . ($b->tahun_tagihan ?? '-')) }}</td>
                                                <td class="py-3 px-4 font-bold text-gray-800">Rp {{ number_format((float) ($b->total_layanan ?? $b->nominal ?? 0), 0, ',', '.') }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ in_array(($b->status_bill_lay ?? $b->status_bayar ?? ''), ['LUNAS', '01', 'PAID']) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                        {{ $b->status_bill_lay ?? $b->status_bayar ?? 'BELUM LUNAS' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-gray-500">{{ !empty($b->date_create) ? \Carbon\Carbon::parse($b->date_create)->translatedFormat('d M Y') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">No data available in table</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 6: PENGADUAN -->
                        <div id="tab-content-pengaduan" class="tab-content hidden space-y-4">
                            <div class="pb-3 border-b border-gray-100">
                                <h3 class="text-sm font-bold text-gray-800">Tiket Pengaduan & Gangguan</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Riwayat laporan kendala teknis layanan</p>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-rose-50 text-rose-900 font-bold uppercase tracking-wider border-b border-rose-100">
                                        <tr>
                                            <th class="py-3 px-4">Kode Tiket</th>
                                            <th class="py-3 px-4">Keluhan</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Tanggal Tiket</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @forelse($pengaduan as $p)
                                            <tr>
                                                <td class="py-3 px-4 font-mono font-bold text-rose-600">{{ $p->tiket ?? $p->kode_tiket_gangguan ?? '-' }}</td>
                                                <td class="py-3 px-4 text-gray-700">{{ $p->keluhan ?? $p->indikasi ?? $p->note_gangguan ?? '-' }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                                        {{ $p->status ?? $p->status_tiket ?? 'Proses' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-gray-500">{{ !empty($p->date_create) ? \Carbon\Carbon::parse($p->date_create)->translatedFormat('d M Y H:i') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">No data available in table</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 7: PERANGKAT DSB -->
                        <div id="tab-content-perangkat" class="tab-content hidden space-y-5">
                            <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">Perangkat & Kredensial Akses</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Rincian hardware (ONU/ONT, STB, kabel) dan akun PPPoE pelanggan</p>
                                </div>
                            </div>

                            <!-- Card ID PPPoE (Sesuai Desain & Gambar) -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2 border-l-4 border-l-blue-600">
                                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">ID PPOE</h4>
                                <div class="space-y-1.5 text-xs font-semibold text-slate-700 pt-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400 font-normal">— Username :</span>
                                        <span class="font-mono font-bold text-blue-600 select-all">{{ $customer->nomor_internet }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400 font-normal">— Password :</span>
                                        <span class="font-mono font-bold text-slate-800 select-all">{{ $customer->pppoe_password ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th class="py-3 px-4">Nama Perangkat</th>
                                            <th class="py-3 px-4">Brand / Tipe</th>
                                            <th class="py-3 px-4">Serial Number</th>
                                            <th class="py-3 px-4">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @forelse($perangkat as $pr)
                                            <tr>
                                                <td class="py-3 px-4 font-bold text-gray-800">{{ $pr->nama_barang ?? '-' }}</td>
                                                <td class="py-3 px-4 text-gray-600">{{ $pr->tipe_barang ?? '-' }}</td>
                                                <td class="py-3 px-4 font-mono text-gray-700">{{ $pr->note_instalasi_barang ?? '-' }}</td>
                                                <td class="py-3 px-4 font-bold text-blue-600">{{ $pr->jumlah_barang ?? 1 }} Unit</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">No data available in table</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Preview Foto / Lightbox -->
    <div id="photoPreviewModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-300">
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                <h3 id="photoModalTitle" class="text-sm font-bold text-gray-800">Preview Foto</h3>
                <div class="flex items-center gap-2">
                    <a id="photoModalDownload" href="#" target="_blank" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka Tab Baru
                    </a>
                    <button onclick="closePhotoModal()" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <!-- Modal Body Image -->
            <div class="flex-1 overflow-auto p-4 bg-slate-900 flex items-center justify-center min-h-[300px]">
                <img id="photoModalImg" src="" alt="Preview" class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-md transition-transform duration-200">
            </div>
        </div>
    </div>

    <!-- Script Tab Switching & Photo Modal -->
    <script>
        function switchTab(tabKey) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(el) {
                el.classList.add('hidden');
            });

            // Reset all tab button styles
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.classList.remove('active', 'text-blue-600', 'border-blue-600', 'bg-white', 'shadow-2xs');
                btn.classList.add('text-gray-500', 'border-transparent');
            });

            // Show selected tab content
            const targetContent = document.getElementById('tab-content-' + tabKey);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }

            // Highlight selected tab button
            const targetBtn = document.getElementById('tab-btn-' + tabKey);
            if (targetBtn) {
                targetBtn.classList.remove('text-gray-500', 'border-transparent');
                targetBtn.classList.add('active', 'text-blue-600', 'border-blue-600', 'bg-white', 'shadow-2xs');
            }
        }

        function openPhotoModal(url, title) {
            const modal = document.getElementById('photoPreviewModal');
            const img = document.getElementById('photoModalImg');
            const modalTitle = document.getElementById('photoModalTitle');
            const downloadBtn = document.getElementById('photoModalDownload');

            if (img && modal) {
                img.src = url;
                if (modalTitle) modalTitle.textContent = title || 'Preview Foto';
                if (downloadBtn) downloadBtn.href = url;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoPreviewModal');
            const img = document.getElementById('photoModalImg');
            if (modal) {
                modal.classList.add('hidden');
                if (img) img.src = '';
                document.body.style.overflow = '';
            }
        }

        function openScanModal() {
            const modal = document.getElementById('modalScanDokumen');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeScanModal() {
            const modal = document.getElementById('modalScanDokumen');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            switchTab('log');

            const photoModal = document.getElementById('photoPreviewModal');
            if (photoModal) {
                photoModal.addEventListener('click', function(e) {
                    if (e.target === this) closePhotoModal();
                });
            }

            const scanModal = document.getElementById('modalScanDokumen');
            if (scanModal) {
                scanModal.addEventListener('click', function(e) {
                    if (e.target === this) closeScanModal();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePhotoModal();
                    closeScanModal();
                }
            });
        });

        const scanDocData = {
            berlangganan: {
                title: 'Scan Dokumen Berlangganan',
                desc: 'Master formulir berlangganan bertanda tangan',
                url: @json(!empty($customer->scan_dokumen) ? asset($customer->scan_dokumen) : null),
            },
            survey: {
                title: 'Scan Dokumen Survey',
                desc: 'Dokumen berita acara survey bertanda tangan',
                url: @json(!empty($customer->scan_dokumen_survey) ? asset($customer->scan_dokumen_survey) : null),
            },
            instalasi: {
                title: 'Scan Dokumen Instalasi',
                desc: 'Berita acara instalasi & pemasangan bertanda tangan',
                url: @json(!empty($customer->scan_dokumen_instalasi) ? asset($customer->scan_dokumen_instalasi) : null),
            },
            aktivasi: {
                title: 'Scan Dokumen Aktivasi',
                desc: 'Berita acara aktivasi & serah terima bertanda tangan',
                url: @json(!empty($customer->scan_dokumen_aktivasi) ? asset($customer->scan_dokumen_aktivasi) : null),
            }
        };

        function openScanModal(tipe = 'berlangganan') {
            const data = scanDocData[tipe] || scanDocData.berlangganan;
            const titleEl = document.getElementById('scanModalTitle');
            const descEl = document.getElementById('scanModalDesc');
            const tipeInput = document.getElementById('scanModalTipeInput');
            const deleteTipeInput = document.getElementById('scanModalDeleteTipeInput');
            const uploadedBox = document.getElementById('scanModalUploadedBox');
            const labelUpload = document.getElementById('scanModalUploadLabel');
            const viewBtn = document.getElementById('scanModalViewBtn');
            const downloadBtn = document.getElementById('scanModalDownloadBtn');

            if (titleEl) titleEl.textContent = data.title;
            if (descEl) descEl.textContent = data.desc;
            if (tipeInput) tipeInput.value = tipe;
            if (deleteTipeInput) deleteTipeInput.value = tipe;

            if (data.url) {
                if (uploadedBox) uploadedBox.classList.remove('hidden');
                if (viewBtn) viewBtn.href = data.url;
                if (downloadBtn) downloadBtn.href = data.url;
                if (labelUpload) labelUpload.textContent = 'Ganti File Scan Dokumen:';
            } else {
                if (uploadedBox) uploadedBox.classList.add('hidden');
                if (labelUpload) labelUpload.textContent = 'Pilih File Scan Dokumen (PDF, JPG, JPEG, PNG max 10MB):';
            }

            const modal = document.getElementById('modalScanDokumen');
            if (modal) modal.classList.remove('hidden');
        }

        function closeScanModal() {
            const modal = document.getElementById('modalScanDokumen');
            if (modal) modal.classList.add('hidden');
        }
    </script>

    <!-- Modal Scan Dokumen Upload & Management -->
    <div id="modalScanDokumen" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl max-w-md w-full p-6 space-y-5 relative animate-in fade-in zoom-in-95 duration-200">
            <button type="button" onclick="closeScanModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h3 id="scanModalTitle" class="text-sm font-bold text-gray-800">Scan Dokumen Berlangganan</h3>
                    <p id="scanModalDesc" class="text-xs text-gray-400">Master dokumen bertanda tangan</p>
                </div>
            </div>

            <!-- Uploaded file info & actions box -->
            <div id="scanModalUploadedBox" class="bg-emerald-50/60 rounded-xl border border-emerald-200 p-4 space-y-3 hidden">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Dokumen Sudah Diunggah
                    </span>
                    <a id="scanModalViewBtn" href="#" target="_blank" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <a id="scanModalDownloadBtn" href="#" download class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 px-3 rounded-lg text-center flex items-center justify-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-download"></i> Unduh File
                    </a>
                    <form id="scanModalDeleteForm" method="POST" action="{{ route('pelanggan.delete-scan', $customer->nomor_internet) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file scan dokumen ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tipe_dokumen" id="scanModalDeleteTipeInput" value="berlangganan">
                        <button type="submit" class="bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-semibold py-2 px-3 rounded-lg flex items-center gap-1 transition-colors" title="Hapus Scan Dokumen">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>

            <!-- Upload form -->
            <form method="POST" action="{{ route('pelanggan.upload-scan', $customer->nomor_internet) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="tipe_dokumen" id="scanModalTipeInput" value="berlangganan">
                <div class="space-y-1.5">
                    <label id="scanModalUploadLabel" class="block text-xs font-semibold text-gray-700">
                        Pilih File Scan Dokumen (PDF, JPG, JPEG, PNG max 10MB):
                    </label>
                    <input type="file" name="scan_dokumen" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-50 rounded-xl border border-gray-200 p-1.5">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeScanModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition-colors">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Now
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

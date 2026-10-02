@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen pb-12">
        <!-- Background Grid Pattern -->
        <div class="pointer-events-none absolute inset-0 -z-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.04) 1px, transparent 0); background-size: 24px 24px;"></div>

        <!-- Header Navigasi & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <nav class="flex items-center gap-2 text-xs text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">IMS</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('broadband.index') }}" class="hover:text-blue-600 transition-colors">Broadband</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-800 font-semibold">Profil Pelanggan</span>
            </nav>

            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    <i class="fa-solid fa-eye text-gray-400 text-[11px]"></i>
                    Mode Read-Only
                </span>
                <a href="{{ route('broadband.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-xl text-xs shadow-2xs transition-all">
                    <i class="fa-solid fa-arrow-left text-gray-500"></i>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- Main Layout: 2 Panel Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ========================================================= -->
            <!-- PANEL KIRI: Header & Identitas Pelanggan (col-span-4)     -->
            <!-- ========================================================= -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Kartu Header Profil -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-cyan-500"></div>

                    <div class="flex items-start gap-4 mb-5 pt-2">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold flex-shrink-0 shadow-2xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-md tracking-wide font-mono">
                                    {{ $customer->nomor_internet }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase border {{ $customer->status_class }}">
                                    <i class="fa-solid {{ $customer->status_icon }} text-[9px]"></i>
                                    {{ $customer->status_text }}
                                </span>
                            </div>
                            <h1 class="text-lg font-bold text-gray-900 truncate mt-1.5" title="{{ $customer->nama_pelanggan }}">
                                {{ $customer->nama_pelanggan ?: 'Pelanggan Broadband' }}
                            </h1>
                            <p class="text-xs font-semibold text-blue-600 truncate mt-0.5">
                                <i class="fa-solid fa-wifi text-[10px] mr-1"></i>
                                {{ $customer->paket_label }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 text-xs">
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Sales</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->nama_sales ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Group Layanan</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->group_layanan ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Mitra</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->mitra ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Media Akses</span>
                            <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $customer->media_akses ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Identitas Pelanggan & Onboarding App -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6 space-y-4">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-gray-100">
                        <i class="fa-solid fa-address-card text-blue-500"></i>
                        Informasi Kependudukan & Akun
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">NIK Penduduk</span>
                            <p class="font-mono text-sm font-bold text-gray-800 mt-0.5">{{ $customer->nik_penduduk ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Onboarding / Login Aplikasi</span>
                            <div class="mt-1">
                                @if($customer->is_login == 1)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                        Sudah Onboarding / Login Aplikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <i class="fa-regular fa-circle text-gray-400"></i>
                                        Belum Pernah Login / Onboarding
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($mPelanggan)
                            <div class="pt-2 border-t border-gray-100 space-y-2">
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Data Master Penduduk</span>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">No. HP Pelanggan</span>
                                    <span class="text-gray-800 font-semibold">{{ $mPelanggan->nomor_hp ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Email Pelanggan</span>
                                    <span class="text-gray-800">{{ $mPelanggan->email ?: '-' }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Registrasi</span>
                            <span class="text-gray-800 font-semibold">{{ $customer->desc_registrasi ?: $customer->status_reg ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Metadata Sistem -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6 space-y-3 text-xs">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
                        Riwayat Sistem
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">User Create</span>
                            <span class="text-gray-800 font-medium">{{ $customer->user_create ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tanggal Create</span>
                            <span class="text-gray-800 font-medium">{{ $customer->date_create ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">User Update</span>
                            <span class="text-gray-800 font-medium">{{ $customer->user_update ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tanggal Update</span>
                            <span class="text-gray-800 font-medium">{{ $customer->date_update ?: '-' }}</span>
                        </div>
                    </div>
                    @if(!empty($customer->note_request))
                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Catatan / Note Request</span>
                            <p class="text-gray-700 mt-1 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $customer->note_request }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- PANEL KANAN: Detail Teknis, Alamat & Keuangan (col-span-8)-->
            <!-- ========================================================= -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Seksi Paket & Infrastruktur Jaringan -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">Layanan Broadband & Infrastruktur</h3>
                                <p class="text-[11px] text-gray-400">Parameter teknis jaringan dan konektivitas OLT/POP</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                        <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Paket Bandwidth</span>
                            <span class="font-bold text-blue-700 text-sm block mt-0.5">{{ $customer->paket_label }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $customer->kode_bandwith ?: '-' }}</span>
                        </div>

                        <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Point of Presence (POP)</span>
                            <span class="font-bold text-gray-800 text-sm block mt-0.5">{{ $customer->kode_pop ?: '-' }}</span>
                            <span class="text-[10px] text-gray-500">{{ $customer->nama_pop ?: '-' }}</span>
                        </div>

                        <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Perangkat OLT</span>
                            <span class="font-bold text-gray-800 text-sm block mt-0.5">{{ $customer->olt ?: '-' }}</span>
                            <span class="text-[10px] text-gray-500">Index: {{ $customer->index_olt ?: '-' }}</span>
                        </div>

                        <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Modem ONT (US / PS)</span>
                            <span class="font-mono font-bold text-gray-800 block mt-0.5">US: {{ $customer->ont_us ?: '-' }}</span>
                            <span class="font-mono text-gray-600 block text-[10px]">PS: {{ $customer->ont_ps ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Seksi Alamat Pemasangan -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">Lokasi & Alamat Pemasangan</h3>
                                <p class="text-[11px] text-gray-400">Detail alamat terpasang, wilayah administrasi dan titik koordinat</p>
                            </div>
                        </div>

                        @if(!empty($customer->maps_url))
                            <a href="{{ $customer->maps_url }}" target="_blank" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <span>Buka Google Maps</span>
                            </a>
                        @endif
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Alamat Lengkap</span>
                            <p class="font-semibold text-gray-800 mt-1 leading-relaxed">{{ $customer->alamat_lengkap }}</p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Jenis Bangunan</span>
                                <span class="font-semibold text-gray-800 block mt-0.5">{{ $customer->jenis_bangunan ?: '-' }}</span>
                            </div>

                            <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">No. Bangunan</span>
                                <span class="font-semibold text-gray-800 block mt-0.5">{{ $customer->nomor_bangunan ?: '-' }}</span>
                            </div>

                            <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">RT / RW</span>
                                <span class="font-semibold text-gray-800 block mt-0.5">
                                    RT {{ $customer->rt_pasang ?: '00' }} / RW {{ $customer->rw_pasang ?: '00' }}
                                </span>
                            </div>

                            <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Kode Wilayah</span>
                                <span class="font-mono text-gray-700 block mt-0.5">{{ $customer->kode_wilayah_kelurahan_pasang ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Koordinat GPS (Lon / Lat)</span>
                                <span class="font-mono text-gray-700 block mt-0.5">{{ $customer->lon_lat ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tautan Lokasi Maps</span>
                                <span class="text-blue-600 truncate block mt-0.5">
                                    @if(!empty($customer->maps_url))
                                        <a href="{{ $customer->maps_url }}" target="_blank" class="hover:underline flex items-center gap-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                            {{ $customer->maps_url }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Seksi Billing & Status Finansial -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">Informasi Billing & Tagihan</h3>
                                <p class="text-[11px] text-gray-400">Rincian biaya paket, PPN, potongan dan periode tagihan</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 text-xs">
                        <div class="bg-emerald-50/50 p-3.5 rounded-xl border border-emerald-100">
                            <span class="text-emerald-700 block text-[10px] uppercase font-bold">Harga Paket</span>
                            <span class="font-bold text-emerald-800 text-base block mt-0.5">{{ $customer->harga_formatted }}</span>
                        </div>

                        <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">PPN</span>
                            <span class="font-bold text-gray-800 text-sm block mt-0.5">
                                {{ $customer->ppn ? $customer->ppn . '%' : '-' }}
                            </span>
                            @if($customer->ppn_nom_formatted !== '-')
                                <span class="text-[10px] text-gray-500 font-mono">{{ $customer->ppn_nom_formatted }}</span>
                            @endif
                        </div>

                        <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Potongan Biaya</span>
                            <span class="font-bold text-gray-800 text-sm block mt-0.5">{{ $customer->potongan_formatted }}</span>
                            @if(!empty($customer->potongan_note))
                                <span class="text-[10px] text-gray-500 truncate block">{{ $customer->potongan_note }}</span>
                            @endif
                        </div>

                        <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Periode Billing</span>
                            <span class="font-bold text-gray-800 text-sm block mt-0.5">
                                {{ $customer->periode_billing ? $customer->periode_billing . ' Bulan' : '-' }}
                            </span>
                            <span class="text-[10px] text-gray-500">Notif: {{ $customer->jns_notif ?: '-' }}</span>
                        </div>

                        <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Billing Terakhir</span>
                            <span class="font-mono font-bold text-gray-800 block mt-0.5">
                                {{ $customer->last_month_billing ? $customer->last_month_billing . ' / ' . $customer->last_year_billing : '-' }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Suspend</span>
                            <span class="font-bold block mt-0.5 {{ ($customer->is_suspend ?? '0') === '1' ? 'text-amber-600' : 'text-gray-700' }}">
                                {{ ($customer->is_suspend ?? '0') === '1' ? 'Ya (Suspend)' : 'Tidak' }}
                                @if(!empty($customer->count_suspend))
                                    <span class="text-[10px] text-gray-500">({{ $customer->count_suspend }}x)</span>
                                @endif
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Status Terminasi</span>
                            <span class="font-bold block mt-0.5 {{ ($customer->is_termin ?? '0') === '1' ? 'text-rose-600' : 'text-gray-700' }}">
                                {{ ($customer->is_termin ?? '0') === '1' ? 'Ya (Terminasi)' : 'Tidak' }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Denda / Prorate / Lock</span>
                            <span class="text-gray-700 block mt-0.5 font-medium">
                                Denda: {{ $customer->is_denda ?: '0' }} | Lock: {{ $customer->islock ?: '0' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

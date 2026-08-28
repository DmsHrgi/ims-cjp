@extends('layouts.app')

@section('content')
    @php /** @var object $data */ @endphp
    @php /** @var \Illuminate\Support\Collection $bangunan */ @endphp
    @php /** @var \Illuminate\Support\Collection $kategori */ @endphp
    @php /** @var \Illuminate\Support\Collection $groupLayanan */ @endphp
    @php /** @var \Illuminate\Support\Collection $sales */ @endphp
    @php /** @var \Illuminate\Support\Collection $provinsi */ @endphp

    @php
        $reg = \Illuminate\Support\Facades\DB::table('trx_batchjob_register')->where('nomor_internet', $data->nomor_internet)->first();
        $targetId = $data->id_perusahaan ?? $data->nik_penduduk ?? ($reg ? ($reg->id_perusahaan ?? $reg->nik_penduduk) : null);
        $pelanggan = $targetId ? \Illuminate\Support\Facades\DB::table('m_pelanggan')->where('id_perusahaan', $targetId)->first() : null;

        // Seksi 1: Informasi Pelanggan
        $valNamaPerusahaan = $pelanggan->nama_perusahaan ?? $reg->nama_pelanggan ?? $data->nama_perusahaan ?? $data->nama_pelanggan ?? $data->nama_penduduk ?? '';
        $valNoTelpPerusahaan = $pelanggan->no_telp_perusahaan ?? $data->no_telp_perusahaan ?? $data->nomor_hp ?? '';
        $valEmailPerusahaan = $pelanggan->email_perusahaan ?? $data->email_perusahaan ?? $data->email ?? '';
        $valIdPerusahaan = $pelanggan->id_perusahaan ?? $data->id_perusahaan ?? $data->nik_penduduk ?? '';

        $valNamaPicTeknis = $pelanggan->nama_pic_teknis ?? $data->nama_pic_teknis ?? $data->pic ?? '';
        $valNoTelpPicTeknis = $pelanggan->no_telp_pic_teknis ?? $data->no_telp_pic_teknis ?? $data->nomor_hp_2 ?? $data->nomor_hp ?? '';
        $valEmailPicTeknis = $pelanggan->email_pic_teknis ?? $data->email_pic_teknis ?? $data->email ?? '';

        $valNamaPicKeuangan = $pelanggan->nama_pic_keuangan ?? $data->nama_pic_keuangan ?? $data->pic ?? '';
        $valNoTelpPicKeuangan = $pelanggan->no_telp_pic_keuangan ?? $data->no_telp_pic_keuangan ?? $data->nomor_hp ?? '';
        $valEmailPicKeuangan = $pelanggan->email_pic_keuangan ?? $data->email_pic_keuangan ?? $data->email ?? '';

        $valJenisPerusahaan = !empty($pelanggan->jenis_perusahaan) ? $pelanggan->jenis_perusahaan : (!empty($reg->jenis_perusahaan) ? $reg->jenis_perusahaan : ($data->jenis_perusahaan ?? ''));
        $valTanggalRegistrasi = $pelanggan->tanggal_registrasi ?? $data->tanggal_registrasi ?? (isset($data->date_create) ? substr($data->date_create, 0, 10) : date('Y-m-d'));

        // Seksi 2: Alamat Perusahaan
        $valRtKtp = $reg->rt_perusahaan ?? $pelanggan->rt_ktp ?? $data->rt_perusahaan ?? $data->rt_ktp ?? $data->rt_pasang ?? '';
        $valRwKtp = $reg->rw_perusahaan ?? $pelanggan->rw_ktp ?? $data->rw_perusahaan ?? $data->rw_ktp ?? $data->rw_pasang ?? '';
        $valNoBangunanCorp = $reg->nomor_bangunan_perusahaan ?? $pelanggan->nomor_bangunan_perusahaan ?? $data->nomor_bangunan_perusahaan ?? $data->nomor_bangunan ?? '';
        $valAlamatKtp = $reg->detail_alamat_perusahaan ?? $pelanggan->alamat_ktp ?? $data->detail_alamat_perusahaan ?? $data->alamat_perusahaan ?? $data->alamat_ktp ?? $data->alamat_pasang ?? '';
        $valLonLatCorp = $reg->lon_lat_perusahaan ?? $pelanggan->lon_lat_perusahaan ?? $data->lon_lat_perusahaan ?? '';
        $valSharelockCorp = $reg->sharelock_perusahaan ?? $pelanggan->sharelock_perusahaan ?? $data->sharelock_perusahaan ?? '';
        if (empty($valSharelockCorp) && !empty($valLonLatCorp)) {
            $valSharelockCorp = 'https://maps.google.com/?q=' . urlencode(trim($valLonLatCorp));
        }

        $kodeKelCorp = $reg->kode_wilayah_kelurahan_perusahaan ?? $pelanggan->kode_wilayah_kelurahan_ktp ?? $data->kode_wilayah_kelurahan_perusahaan ?? $data->kode_wilayah_kelurahan_ktp ?? $data->kode_wilayah_kelurahan_pasang ?? '';
        $wilKtp = $kodeKelCorp ? \Illuminate\Support\Facades\DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelCorp)->first() : null;
        $provKtpVal = $wilKtp->kode_wilayah_provinsi ?? '';
        $kotaKtpVal = $wilKtp->kode_wilayah_kota ?? '';
        $kecKtpVal = $wilKtp->kode_wilayah_kecamatan ?? '';
        $kelKtpVal = $kodeKelCorp;

        // Seksi 3: Alamat Pemasangan
        $valRtPasang = $reg->rt_pasang ?? $data->rt_pasang ?? '';
        $valRwPasang = $reg->rw_pasang ?? $data->rw_pasang ?? '';
        $valNoBangunanPasang = $reg->nomor_bangunan ?? $data->nomor_bangunan ?? '';
        $valAlamatPasang = $reg->alamat_pasang ?? $data->alamat_pasang ?? '';

        $kodeKelPasang = $reg->kode_wilayah_kelurahan_pasang ?? $data->kode_wilayah_kelurahan_pasang ?? $kodeKelCorp;
        $wilPasang = $kodeKelPasang ? \Illuminate\Support\Facades\DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelPasang)->first() : null;
        $provPasangVal = $wilPasang->kode_wilayah_provinsi ?? '';
        $kotaPasangVal = $wilPasang->kode_wilayah_kota ?? '';
        $kecPasangVal = $wilPasang->kode_wilayah_kecamatan ?? '';
        $kelPasangVal = $kodeKelPasang;

        $valLonLat = $reg->lon_lat ?? $data->lon_lat ?? $valLonLatCorp;
        $valSharelock = $reg->loc_maps ?? $data->loc_maps ?? $valSharelockCorp;
        $valPermintaanKhusus = $reg->note_request ?? $data->note_request ?? '';

        // Seksi 4: Pemilihan Kapasitas Layanan
        $valJenisBangunanCorp = $pelanggan->jenis_bangunan ?? $data->jenis_bangunan ?? '';
        $valJenisBangunanPasang = $reg->jenis_bangunan ?? $data->jenis_bangunan ?? '';
        $valJenisBangunan = $valJenisBangunanPasang;
        $valKategoriLayanan = $data->nama_kategori_bandwith ?? $data->kode_kategori_bandwith ?? '';
        $valGroupLayanan = $reg->group_layanan ?? $data->group_layanan ?? '';
        $valPaketLayanan = $data->nominal_bandwith ? ($data->nominal_bandwith . ' Mbps') : ($reg->kode_bandwith ?? $data->kode_bandwith ?? '');
        $valHargaPaket = $data->harga_bandwith ?? $reg->total_registrasi ?? '';

        // Seksi 5: Informasi Penugasan Sales & Sistem
        $valNamaSales = $reg->nama_sales ?? $data->nama_sales ?? '';

        // PPPoE Credentials
        $valPppoeUsername = $reg->pppoe_username ?? ($pelanggan->pppoe_username ?? ($data->pppoe_username ?? $data->nomor_internet));
        $valPppoePassword = $reg->pppoe_password ?? ($pelanggan->pppoe_password ?? ($data->pppoe_password ?? ''));
        if (empty($valPppoePassword)) {
            $valPppoePassword = (string) (100000 + (abs(crc32('pppoe_' . $data->nomor_internet)) % 900000));
        }
    @endphp

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

    @php
        $backUrl = session('pendaftaran_last_url', route('pendaftaran'));
        $isFromPelanggan = str_contains($backUrl, '/pelanggan');
        $parentName = $isFromPelanggan ? 'Pelanggan' : 'Pendaftaran';
    @endphp

    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <a href="{{ $backUrl }}" class="hover:text-blue-500 transition-colors">{{ $parentName }}</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-slate-600 font-medium">Ubah Data Registrasi</span>
        </div>
        <h1 class="text-xl font-bold text-slate-800">Ubah Data Registrasi Perusahaan</h1>
    </div>

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-400 mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="text-sm font-semibold">Terjadi kesalahan:</p>
                <ul class="text-xs list-disc list-inside mt-1 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-building text-blue-600"></i>
                    Edit Form Registrasi Perusahaan
                    <span class="text-blue-600 font-semibold">- {{ $data->nomor_internet }}</span>
                </h2>
            </div>
            <a href="{{ session('pendaftaran_last_url', route('pendaftaran')) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('pendaftaran.update', $data->nomor_internet) }}" enctype="multipart/form-data" class="p-6 space-y-6" id="formEdit">
            @csrf
            @method('PUT')

            <!-- ============================================ -->
            <!-- SECTION 1: INFORMASI PELANGGAN (ENTERPRISE) -->
            <!-- ============================================ -->
            <div id="sec-edit-informasi-pelanggan" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">1. Informasi Pelanggan (Enterprise / Corporate)</h4>
                </div>

                <!-- Baris 1: ID Perusahaan, Nama Perusahaan, No Telp Perusahaan, Email Perusahaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">ID Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="id_perusahaan" required maxlength="100" placeholder="Ketik ID Perusahaan" value="{{ old('id_perusahaan', $valIdPerusahaan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Perusahaan / Corporate <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="nama_perusahaan" required maxlength="200" placeholder="NAMA PERUSAHAAN (MISAL: PT. MAJU BERSAMA)" value="{{ old('nama_perusahaan', $valNamaPerusahaan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Telepon Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="no_telp_perusahaan" required placeholder="021-12345678 / 08123456789" value="{{ old('no_telp_perusahaan', $valNoTelpPerusahaan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="email" name="email_perusahaan" required placeholder="info@perusahaan.com" value="{{ old('email_perusahaan', $valEmailPerusahaan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                </div>

                <!-- Baris 3: PIC Teknis & PIC Keuangan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                    <!-- Column PIC Teknis -->
                    <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/60 space-y-3">
                        <div class="flex items-center gap-2 border-b border-slate-200/60 pb-2">
                            <i class="fa-solid fa-headset text-blue-600 text-xs"></i>
                            <span class="text-xs font-bold text-slate-700">PIC Teknis</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="nama_pic_teknis" required placeholder="Nama PIC Teknis" value="{{ old('nama_pic_teknis', $valNamaPicTeknis) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">No. HP PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="no_telp_pic_teknis" required placeholder="08123456789" value="{{ old('no_telp_pic_teknis', $valNoTelpPicTeknis) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email PIC Teknis <span class="text-rose-500 font-bold">*</span></label>
                            <input type="email" name="email_pic_teknis" required placeholder="teknis@perusahaan.com" value="{{ old('email_pic_teknis', $valEmailPicTeknis) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                        </div>
                    </div>

                    <!-- Column PIC Keuangan -->
                    <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/60 space-y-3">
                        <div class="flex items-center gap-2 border-b border-slate-200/60 pb-2">
                            <i class="fa-solid fa-calculator text-emerald-600 text-xs"></i>
                            <span class="text-xs font-bold text-slate-700">PIC Keuangan</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="nama_pic_keuangan" required placeholder="Nama PIC Keuangan" value="{{ old('nama_pic_keuangan', $valNamaPicKeuangan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">No. HP PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="no_telp_pic_keuangan" required placeholder="08123456789" value="{{ old('no_telp_pic_keuangan', $valNoTelpPicKeuangan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email PIC Keuangan <span class="text-rose-500 font-bold">*</span></label>
                            <input type="email" name="email_pic_keuangan" required placeholder="keuangan@perusahaan.com" value="{{ old('email_pic_keuangan', $valEmailPicKeuangan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                        </div>
                    </div>
                </div>

                <!-- Baris 4: Jenis Perusahaan & Tanggal Registrasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="jenis_perusahaan" required maxlength="100" placeholder="Contoh: PT, CV, Yayasan, dll" value="{{ old('jenis_perusahaan', $valJenisPerusahaan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Registrasi <span class="text-rose-500 font-bold">*</span></label>
                        <input type="date" name="tanggal_registrasi" required value="{{ old('tanggal_registrasi', $valTanggalRegistrasi) }}" onclick="this.showPicker && this.showPicker()" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 2: ALAMAT PERUSAHAAN & DETAIL -->
            <!-- ============================================ -->
            <div id="sec-edit-alamat-perusahaan" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
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
                            <select name="provinsi_ktp" id="editProvKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinsi as $p)
                                    <option value="{{ $p->kode_wilayah_provinsi }}" {{ old('provinsi_ktp', $provKtpVal) == $p->kode_wilayah_provinsi ? 'selected' : '' }}>{{ $p->nama_provinsi }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kota/Kabupaten Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kota_ktp" id="editKotaKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kota/Kabupaten</option>
                                @foreach ($kotaKtpList as $k)
                                    <option value="{{ $k->kode_wilayah_kota }}" {{ old('kota_ktp', $kotaKtpVal) == $k->kode_wilayah_kota ? 'selected' : '' }}>{{ $k->nama_kota }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kecamatan Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kecamatan_ktp" id="editKecKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecKtpList as $k)
                                    <option value="{{ $k->kode_wilayah_kecamatan }}" {{ old('kecamatan_ktp', $kecKtpVal) == $k->kode_wilayah_kecamatan ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelurahan Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kelurahan_ktp" id="editKelKtp" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kelurahan</option>
                                @foreach ($kelKtpList as $k)
                                    <option value="{{ $k->kode_wilayah_kelurahan }}" {{ old('kelurahan_ktp', $kelKtpVal) == $k->kode_wilayah_kelurahan ? 'selected' : '' }}>{{ $k->nama_kelurahan }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                </div>

                <!-- RT, RW, Jenis Bangunan, No Blok Bangunan -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">RT Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="rt_ktp" id="editRtKtp" required placeholder="000" value="{{ old('rt_ktp', $valRtKtp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">RW Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="rw_ktp" id="editRwKtp" required placeholder="000" value="{{ old('rw_ktp', $valRwKtp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Bangunan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="jenis_bangunan_perusahaan" id="editJenisBangunanPerusahaan" list="listEditBangunan" required placeholder="Contoh: RUMAH, RUKO, GEDUNG" value="{{ old('jenis_bangunan_perusahaan', $valJenisBangunanCorp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No / Blok Bangunan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="nomor_bangunan_perusahaan" id="editNoBangunanPerusahaan" required placeholder="Contoh: LT2/15, BLOK C/22, No. 41" value="{{ old('nomor_bangunan_perusahaan', $valNoBangunanCorp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                </div>

                <!-- Detail Alamat Perusahaan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Alamat Perusahaan <span class="text-rose-500 font-bold">*</span></label>
                    <textarea name="alamat_ktp" id="editAlamatKtp" required rows="2" placeholder="JALAN, NO. RUMAH, KOMPLEK, DLL" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400">{{ old('alamat_ktp', $valAlamatKtp) }}</textarea>
                </div>

                <!-- Titik Koordinat & Link Sharelock Perusahaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Titik Koordinat Perusahaan (Lat, Long)</label>
                        <input type="text" name="lon_lat_perusahaan" id="editLonLatPerusahaan" placeholder="-6.12345, 106.78910" value="{{ old('lon_lat_perusahaan', $valLonLatCorp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Link Sharelock Lokasi Perusahaan</label>
                        <input type="text" name="sharelock_perusahaan" id="editSharelockPerusahaan" placeholder="https://maps.google.com/..." value="{{ old('sharelock_perusahaan', $valSharelockCorp) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                </div>

                <!-- Upload Foto PO & Foto Bangunan -->
                @php
                    $getPhotoUrl = function($path) {
                        if (empty($path)) return null;
                        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
                        $cleanPath = ltrim($path, '/');
                        $cleanRelative = preg_replace('/^storage\//', '', $cleanPath);

                        if (file_exists(public_path($cleanPath))) {
                            return asset($cleanPath);
                        }
                        if (file_exists(public_path('storage/' . $cleanRelative))) {
                            return asset('storage/' . $cleanRelative);
                        }
                        if (file_exists(storage_path('app/public/' . $cleanRelative))) {
                            return url('media-berkas/' . $cleanRelative);
                        }
                        return url('media-berkas/' . $cleanRelative);
                    };
                    $editPoUrl = $getPhotoUrl($data->foto_po ?? $data->foto_ktp ?? null);
                    $editBangunanUrl = $getPhotoUrl($data->foto_bangunan ?? $data->foto_rumah ?? null);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto PO</label>
                        @if($editPoUrl)
                            <div class="mb-2">
                                <img src="{{ $editPoUrl }}" class="max-h-32 rounded-xl border border-slate-200 shadow-xs">
                                <p class="text-xs text-slate-400 mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center transition-all cursor-pointer bg-slate-50/50 hover:bg-blue-50/30 group" onclick="this.querySelector('input').click()">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 group-hover:scale-110 flex items-center justify-center mx-auto mb-2 transition-all shadow-xs">
                                <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Klik untuk mengunggah foto PO baru</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Format JPG, PNG, WEBP (Maks: 5MB)</p>
                            <p class="text-xs font-bold text-blue-600 mt-2 file-name truncate px-2"></p>
                            <input type="file" name="foto_po" accept="image/*" class="hidden" onchange="previewFile(this, 'editPreviewPo')">
                        </div>
                        <img id="editPreviewPo" class="mt-2 max-h-32 rounded-xl border border-slate-200 shadow-xs hidden object-cover mx-auto">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Bangunan</label>
                        @if($editBangunanUrl)
                            <div class="mb-2">
                                <img src="{{ $editBangunanUrl }}" class="max-h-32 rounded-xl border border-slate-200 shadow-xs">
                                <p class="text-xs text-slate-400 mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center transition-all cursor-pointer bg-slate-50/50 hover:bg-blue-50/30 group" onclick="this.querySelector('input').click()">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 group-hover:scale-110 flex items-center justify-center mx-auto mb-2 transition-all shadow-xs">
                                <i class="fa-solid fa-house-chimney-window text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Klik untuk mengunggah foto Bangunan baru</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Format JPG, PNG, WEBP (Maks: 5MB)</p>
                            <p class="text-xs font-bold text-blue-600 mt-2 file-name truncate px-2"></p>
                            <input type="file" name="foto_bangunan" accept="image/*" class="hidden" onchange="previewFile(this, 'editPreviewBangunan')">
                        </div>
                        <img id="editPreviewBangunan" class="mt-2 max-h-32 rounded-xl border border-slate-200 shadow-xs hidden object-cover mx-auto">
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 3: ALAMAT & LOKASI PEMASANGAN -->
            <!-- ============================================ -->
            <div id="sec-edit-pasang" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
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
                            <select name="provinsi_pasang" id="editProvPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinsi as $p)
                                    <option value="{{ $p->kode_wilayah_provinsi }}" {{ old('provinsi_pasang', $provPasangVal) == $p->kode_wilayah_provinsi ? 'selected' : '' }}>{{ $p->nama_provinsi }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kota/Kabupaten Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kota_pasang" id="editKotaPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kota/Kabupaten</option>
                                @foreach ($kotaPasangList as $k)
                                    <option value="{{ $k->kode_wilayah_kota }}" {{ old('kota_pasang', $kotaPasangVal) == $k->kode_wilayah_kota ? 'selected' : '' }}>{{ $k->nama_kota }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kecamatan Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kecamatan_pasang" id="editKecPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecPasangList as $k)
                                    <option value="{{ $k->kode_wilayah_kecamatan }}" {{ old('kecamatan_pasang', $kecPasangVal) == $k->kode_wilayah_kecamatan ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelurahan Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kelurahan_pasang" id="editKelPasang" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer">
                                <option value="">Pilih Kelurahan</option>
                                @foreach ($kelPasangList as $k)
                                    <option value="{{ $k->kode_wilayah_kelurahan }}" {{ old('kelurahan_pasang', $kelPasangVal) == $k->kode_wilayah_kelurahan ? 'selected' : '' }}>{{ $k->nama_kelurahan }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                </div>

                <!-- RT, RW, Jenis Bangunan, No Blok Bangunan -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">RT Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="rt_pasang" id="editRtPasang" required placeholder="000" value="{{ old('rt_pasang', $valRtPasang) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">RW Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="rw_pasang" id="editRwPasang" required placeholder="000" value="{{ old('rw_pasang', $valRwPasang) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Bangunan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="jenis_bangunan" id="editJenisBangunanPasang" list="listEditBangunan" required placeholder="Contoh: RUMAH, RUKO, GEDUNG" value="{{ old('jenis_bangunan', $valJenisBangunanPasang) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No / Blok Bangunan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="nomor_bangunan" id="editNoBangunanPasang" required placeholder="Contoh: LT2/15, BLOK C/22, No. 41" value="{{ old('nomor_bangunan', $valNoBangunanPasang) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                </div>

                <!-- Detail Alamat Pemasangan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detail Alamat Pemasangan <span class="text-rose-500 font-bold">*</span></label>
                    <textarea name="alamat_pasang" id="editAlamatPasang" required rows="2" placeholder="JALAN, NO. RUMAH, PATOKAN LOKASI" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2 px-3 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400">{{ old('alamat_pasang', $valAlamatPasang) }}</textarea>
                </div>

                <!-- Titik Koordinat, Sharelock, Permintaan Khusus -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Titik Koordinat (Lat, Long)</label>
                        <input type="text" name="lon_lat" id="editLonLatPasang" placeholder="-6.12345, 106.78910" value="{{ old('lon_lat', $valLonLat) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Link Sharelock Lokasi</label>
                        <input type="text" name="sharelock" id="editSharelockPasang" placeholder="https://maps.google.com/..." value="{{ old('sharelock', $valSharelock) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Permintaan Khusus Pelanggan</label>
                        <textarea name="permintaan_khusus" id="editPermintaanKhusus" rows="2" placeholder="Catatan khusus teknisi/pemasangan" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2 px-3 text-sm rounded-xl outline-none transition-all resize-none placeholder-slate-400 no-uppercase">{{ old('permintaan_khusus', $valPermintaanKhusus) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 4: PEMILIHAN KAPASITAS LAYANAN (MANUAL) -->
            <!-- ============================================ -->
            <div id="sec-edit-paket" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
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
                        <div class="relative">
                            <select name="kode_kategori" id="editInputKategori" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-9 text-sm rounded-xl outline-none transition-all cursor-pointer font-medium">
                                <option value="">-- PILIH KATEGORI LAYANAN --</option>
                                <option value="LOCALLOOP" {{ old('kode_kategori', $valKategoriLayanan) == 'LOCALLOOP' ? 'selected' : '' }}>LOCALLOOP</option>
                                <option value="METRO E" {{ old('kode_kategori', $valKategoriLayanan) == 'METRO E' ? 'selected' : '' }}>METRO E</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kapasitas Layanan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="kode_bandwith" id="editInputPaket" autocomplete="off" required placeholder="Ketik kapasitas layanan / bandwidth (misal: 100 Mbps)" value="{{ old('kode_bandwith', $valPaketLayanan) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                </div>

                <!-- Harga Layanan (Manual Ketik dengan format titik otomatis) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Harga Layanan <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="harga_paket" id="editHargaPaket" autocomplete="off" required placeholder="Ketik harga layanan (contoh: 500.000)" value="{{ old('harga_paket', $valHargaPaket) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none font-semibold transition-all placeholder-slate-400">
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECTION 5: INFORMASI PENUGASAN SALES & SISTEM -->
            <!-- ============================================ -->
            <div id="sec-edit-sales" class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-7 h-7 rounded-lg bg-purple-50 border border-purple-200/60 text-purple-600 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h4 class="text-xs font-bold text-purple-700 uppercase tracking-wider">5. Informasi Penugasan Sales & Sistem</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Pelanggan (Sistem)</label>
                        <input type="text" value="{{ $data->nomor_internet }}" readonly class="w-full bg-slate-100 border border-slate-200 text-slate-600 py-2.5 px-3.5 text-sm rounded-xl outline-none cursor-not-allowed font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Sales <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="nama_sales" id="editNamaSales" list="listEditSales" required placeholder="Ketik nama sales penanggung jawab" value="{{ old('nama_sales', $valNamaSales) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none transition-all placeholder-slate-400">
                        <datalist id="listEditSales">
                            @foreach ($sales as $s)
                                <option value="{{ $s->nama_karyawan }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- Akun PPPoE Pelanggan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">PPPoE Username</label>
                        <input type="text" name="pppoe_username" id="editPppoeUsername" placeholder="Username PPPoE" value="{{ old('pppoe_username', $valPppoeUsername) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none font-mono font-semibold transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700">PPPoE Password (6 Digit Angka)</label>
                            <button type="button" onclick="generateEditPppoePassword()" class="text-[11px] text-blue-600 hover:text-blue-700 font-bold hover:underline">
                                <i class="fa-solid fa-arrows-rotate"></i> Acak 6 Digit
                            </button>
                        </div>
                        <input type="text" name="pppoe_password" id="editPppoePassword" placeholder="6 digit angka (contoh: 123456)" value="{{ old('pppoe_password', $valPppoePassword) }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-sm rounded-xl outline-none font-mono font-semibold transition-all placeholder-slate-400">
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ session('pendaftaran_last_url', route('pendaftaran')) }}"
                   class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                    <i class="fa-solid fa-xmark text-xs"></i> Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-200 hover:-translate-y-0.5">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Preview file upload
        function previewFile(input, previewId) {
            const fileName = input.files[0]?.name;
            const parentDiv = input.parentElement;
            if (parentDiv) {
                const nameEl = parentDiv.querySelector('.file-name');
                if (nameEl) nameEl.textContent = fileName || '';
            }
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Cascading Dropdown Wilayah (untuk edit page)
        function setupEditCascading(prefix) {
            const prov = document.getElementById('editProv' + prefix);
            const kota = document.getElementById('editKota' + prefix);
            const kec = document.getElementById('editKec' + prefix);
            const kel = document.getElementById('editKel' + prefix);

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

        setupEditCascading('Ktp');
        setupEditCascading('Pasang');

        // ============================================
        // CHECKBOX "SAMA DENGAN ALAMAT PERUSAHAAN" - AUTO FILL (EDIT PAGE)
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const editCheckbox = document.getElementById('editCheckboxSamaKTP');
            if (editCheckbox) {
                editCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        const provKtp = document.getElementById('editProvKtp');
                        const provPasang = document.getElementById('editProvPasang');
                        if (provKtp && provPasang && provKtp.value) {
                            provPasang.value = provKtp.value;
                            provPasang.dispatchEvent(new Event('change'));
                        }
                        
                        setTimeout(() => {
                            const kotaKtp = document.getElementById('editKotaKtp');
                            const kotaPasang = document.getElementById('editKotaPasang');
                            if (kotaKtp && kotaPasang && kotaKtp.value) {
                                kotaPasang.value = kotaKtp.value;
                                kotaPasang.dispatchEvent(new Event('change'));
                            }
                            
                            setTimeout(() => {
                                const kecKtp = document.getElementById('editKecKtp');
                                const kecPasang = document.getElementById('editKecPasang');
                                if (kecKtp && kecPasang && kecKtp.value) {
                                    kecPasang.value = kecKtp.value;
                                    kecPasang.dispatchEvent(new Event('change'));
                                }
                                
                                setTimeout(() => {
                                    const kelKtp = document.getElementById('editKelKtp');
                                    const kelPasang = document.getElementById('editKelPasang');
                                    if (kelKtp && kelPasang && kelKtp.value) {
                                        kelPasang.value = kelKtp.value;
                                    }
                                }, 500);
                            }, 500);
                        }, 500);
                        
                        const rtKtp = document.getElementById('editRtKtp');
                        const rtPasang = document.getElementById('editRtPasang');
                        if (rtKtp && rtPasang) rtPasang.value = rtKtp.value;
                        
                        const rwKtp = document.getElementById('editRwKtp');
                        const rwPasang = document.getElementById('editRwPasang');
                        if (rwKtp && rwPasang) rwPasang.value = rwKtp.value;
                        
                        const noBangunanCorp = document.getElementById('editNoBangunanPerusahaan');
                        const noBangunanPasang = document.getElementById('editNoBangunanPasang');
                        if (noBangunanCorp && noBangunanPasang) noBangunanPasang.value = noBangunanCorp.value;
                        
                        const alamatKtp = document.getElementById('editAlamatKtp');
                        const alamatPasang = document.getElementById('editAlamatPasang');
                        if (alamatKtp && alamatPasang) alamatPasang.value = alamatKtp.value;

                        const lonLatCorp = document.getElementById('editLonLatPerusahaan');
                        const lonLatPasang = document.getElementById('editLonLatPasang');
                        if (lonLatCorp && lonLatPasang) lonLatPasang.value = lonLatCorp.value;

                        const sharelockCorp = document.getElementById('editSharelockPerusahaan');
                        const sharelockPasang = document.getElementById('editSharelockPasang');
                        if (sharelockCorp && sharelockPasang) sharelockPasang.value = sharelockCorp.value;
                    } else {
                        const provPasang = document.getElementById('editProvPasang');
                        if (provPasang) {
                            provPasang.value = '';
                            provPasang.dispatchEvent(new Event('change'));
                        }
                        
                        const kotaPasang = document.getElementById('editKotaPasang');
                        if (kotaPasang) kotaPasang.value = '';
                        
                        const kecPasang = document.getElementById('editKecPasang');
                        if (kecPasang) kecPasang.value = '';
                        
                        const kelPasang = document.getElementById('editKelPasang');
                        if (kelPasang) kelPasang.value = '';
                        
                        const rtPasang = document.getElementById('editRtPasang');
                        if (rtPasang) rtPasang.value = '';
                        
                        const rwPasang = document.getElementById('editRwPasang');
                        if (rwPasang) rwPasang.value = '';
                        
                        const noBangunanPasang = document.getElementById('editNoBangunanPasang');
                        if (noBangunanPasang) noBangunanPasang.value = '';

                        const alamatPasang = document.getElementById('editAlamatPasang');
                        if (alamatPasang) alamatPasang.value = '';

                        const lonLatPasang = document.getElementById('editLonLatPasang');
                        if (lonLatPasang) lonLatPasang.value = '';

                        const sharelockPasang = document.getElementById('editSharelockPasang');
                        if (sharelockPasang) sharelockPasang.value = '';
                    }
                });
            }
        });

        // Format otomatis titik setiap 3 angka pada Harga Layanan
        function formatRibuanString(val) {
            const num = (val || '').toString().replace(/\D/g, '');
            if (!num) return '';
            return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        const editHargaInput = document.getElementById('editHargaPaket');
        if (editHargaInput) {
            editHargaInput.addEventListener('input', function() {
                const startPos = this.selectionStart;
                const prevLen = this.value.length;
                this.value = formatRibuanString(this.value);
                const newLen = this.value.length;
                const newPos = Math.max(0, startPos + (newLen - prevLen));
                this.setSelectionRange(newPos, newPos);
            });

            if (editHargaInput.value) {
                editHargaInput.value = formatRibuanString(editHargaInput.value);
            }
        }

        function generateEditPppoePassword() {
            const random6 = Math.floor(100000 + Math.random() * 900000);
            const pwdInput = document.getElementById('editPppoePassword');
            if (pwdInput) {
                pwdInput.value = random6;
            }
        }
    </script>
@endsection

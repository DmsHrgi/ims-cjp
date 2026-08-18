<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Berlangganan - {{ $customer->nomor_internet ?? '' }}</title>
    <style>
        @page {
            margin: 12px 18px 15px 18px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5px;
            line-height: 1.25;
            color: #000000;
            background-color: #ffffff;
        }

        /* Top Kop Header Table */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: top;
        }
        .company-header-title {
            font-size: 12.5px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
        }
        .company-header-text {
            font-size: 8.5px;
            color: #111111;
            line-height: 1.25;
        }
        .logo-img {
            max-height: 56px;
            height: 56px;
            width: auto;
            margin-right: 12px;
            float: left;
        }
        
        /* Blue divider line below Kop */
        .kop-divider {
            width: 100%;
            height: 2.5px;
            background-color: #0070c0;
            margin-bottom: 6px;
        }

        /* Form Main Title */
        .form-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: #000000;
        }

        /* Outer Container & Borders */
        .outer-box {
            width: 100%;
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        /* Section Bar Header */
        .section-bar {
            background-color: #0070c0;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 3px 6px;
            letter-spacing: 0.3px;
        }

        /* Tables Inside Sections */
        .table-section {
            width: 100%;
            border-collapse: collapse;
        }
        .table-section td {
            padding: 1.8px 4px;
            vertical-align: top;
            font-size: 8px;
        }

        .label-cell {
            white-space: nowrap;
        }
        .colon-cell {
            width: 8px;
            text-align: center;
        }
        .val-cell {
            font-weight: normal;
        }

        /* Checkbox styling */
        .chk-box {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1px solid #000000;
            text-align: center;
            line-height: 8px;
            font-size: 7.5px;
            font-weight: bold;
            margin-right: 2px;
            vertical-align: middle;
        }

        /* Statement Note */
        .statement-text {
            font-size: 8px;
            margin-top: 6px;
            margin-bottom: 8px;
            line-height: 1.25;
            padding: 0 4px;
        }

        /* Signature Area */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            font-size: 8.5px;
        }
        .signature-space {
            height: 45px;
            vertical-align: middle;
            text-align: center;
        }

        /* Footer Requirements */
        .footer-req {
            font-size: 8px;
            margin-top: 4px;
            padding-left: 4px;
        }
        .footer-req ul {
            margin: 2px 0 0 12px;
            padding: 0;
            list-style-type: disc;
        }
        .footer-req li {
            margin-bottom: 1px;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('img/logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $nomorInternet = $customer->nomor_internet ?? '-';

        // Dates
        $tglRegRaw = $customer->tanggal_registrasi ?? $customer->date_create ?? null;
        $tglRegistrasi = $tglRegRaw ? \Carbon\Carbon::parse($tglRegRaw)->translatedFormat('d F Y') : '-';

        $tglInstalasiRaw = $customer->instalasi_date_finish ?? $customer->instalasi_date_start ?? $tglRegRaw;
        $tglInstalasi = $tglInstalasiRaw ? \Carbon\Carbon::parse($tglInstalasiRaw)->translatedFormat('d F Y') : '-';

        // Customer Identity
        $namaLengkap = $customer->nama_perusahaan ?? $customer->nama_pelanggan ?? $customer->nama_penduduk ?? '-';
        $namaPemohon = $customer->nama_pic_teknis ?? $customer->nama_pic_keuangan ?? $customer->pic ?? $namaLengkap;
        $idKtpPaspor = $customer->id_perusahaan ?? $customer->nik_penduduk ?? '-';

        $rawNamaPerusahaan = trim($customer->nama_perusahaan ?? $customer->nama_pelanggan ?? '-');
        if ($rawNamaPerusahaan !== '-' && $rawNamaPerusahaan !== '') {
            $namaPerusahaan = str_starts_with(strtoupper($rawNamaPerusahaan), 'PT') ? $rawNamaPerusahaan : ('PT. ' . $rawNamaPerusahaan);
        } else {
            $namaPerusahaan = 'PT. -';
        }
        $jabatan = 'Direktur';

        // Contact
        $noTelp = $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? '-';
        $noHp = $customer->no_telp_pic_teknis ?? $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? '-';
        $noTelpPerusahaan = $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? '-';
        $noFax = '-';
        $npwp = $customer->npwp ?? '';
        $email = $customer->email_perusahaan ?? $customer->email ?? '-';
        $emailAlt = '-';

        // PIC Teknis & Keuangan
        $picTeknisNama = $customer->nama_pic_teknis ?? 'BAGUS';
        $picTeknisHp = $customer->no_telp_pic_teknis ?? '0896-5695-2045';
        $picTeknisEmail = $customer->email_pic_teknis ?? 'bagus@ptmsn.co.id';

        $picKeuanganNama = $customer->nama_pic_keuangan ?? 'IDA MAYASARI';
        $picKeuanganHp = $customer->no_telp_pic_keuangan ?? '0897-1030-700';
        $picKeuanganEmail = $customer->email_pic_keuangan ?? 'ida@ptmsn.co.id ; info@ptmsn.co.id';

        // Formatting Alamat Lengkap KTP / Perusahaan
        $alamatKtpLengkap = trim($customer->alamat_ktp ?? '');
        if (!empty($customer->rt_ktp) || !empty($customer->rw_ktp)) {
            $alamatKtpLengkap .= ', RT.' . ($customer->rt_ktp ?? '-') . '/RW.' . ($customer->rw_ktp ?? '-');
        }
        if (!empty($customer->nomor_bangunan_perusahaan)) {
            $alamatKtpLengkap .= ', NO. ' . $customer->nomor_bangunan_perusahaan;
        }
        $wilCorp = array_filter([
            !empty($customer->nama_kelurahan_corp) ? 'Kel. ' . $customer->nama_kelurahan_corp : null,
            !empty($customer->nama_kecamatan_corp) ? 'Kec. ' . $customer->nama_kecamatan_corp : null,
            !empty($customer->nama_kota_corp) ? 'Kota ' . $customer->nama_kota_corp : null,
            $customer->nama_provinsi_corp ?? null
        ]);
        if (count($wilCorp)) {
            $alamatKtpLengkap .= ', ' . implode(', ', $wilCorp);
        }

        // Formatting Alamat Lengkap Pemasangan
        $alamatPasangLengkap = trim($customer->alamat_pasang ?? '');
        if (!empty($customer->rt_pasang) || !empty($customer->rw_pasang)) {
            $alamatPasangLengkap .= ', RT.' . ($customer->rt_pasang ?? '-') . '/RW.' . ($customer->rw_pasang ?? '-');
        }
        if (!empty($customer->nomor_bangunan)) {
            $alamatPasangLengkap .= ', NO. ' . $customer->nomor_bangunan;
        }
        $wilPasang = array_filter([
            !empty($customer->nama_kelurahan_pasang) ? 'Kel. ' . $customer->nama_kelurahan_pasang : null,
            !empty($customer->nama_kecamatan_pasang) ? 'Kec. ' . $customer->nama_kecamatan_pasang : null,
            !empty($customer->nama_kota_pasang) ? 'Kota ' . $customer->nama_kota_pasang : null,
            $customer->nama_provinsi_pasang ?? null
        ]);
        if (count($wilPasang)) {
            $alamatPasangLengkap .= ', ' . implode(', ', $wilPasang);
        }

        $namaLokasiPasang = $customer->jenis_bangunan ?? $namaPerusahaan ?? '-';

        // Company Type
        $jenisPerusahaanRaw = strtoupper($customer->jenis_perusahaan ?? 'SWASTA');
        $isPem = str_contains($jenisPerusahaanRaw, 'PEMERINTAH');
        $isBumn = str_contains($jenisPerusahaanRaw, 'BUMN');
        $isSwasta = !$isPem && !$isBumn && (str_contains($jenisPerusahaanRaw, 'SWASTA') || str_contains($jenisPerusahaanRaw, 'PT') || str_contains($jenisPerusahaanRaw, 'CV'));
        $isPersonal = str_contains($jenisPerusahaanRaw, 'PERSONAL') || str_contains($jenisPerusahaanRaw, 'PERORANGAN');
        $isLainLain = !$isPem && !$isBumn && !$isSwasta && !$isPersonal;

        // Product & Financials
        $nominalBandwith = $customer->nominal_bandwith ?? 1024;
        $subTotal = floatval(preg_replace('/[^0-9.]/', '', (string)($customer->harga_paket ?? 3500000)));
        if ($subTotal <= 0) {
            $subTotal = 3500000;
        }
        $ppn11 = round($subTotal * 0.11);
        $totalRaw = $subTotal + $ppn11;
    @endphp

    <!-- Top Kop Header Table -->
    <table class="kop-table">
        <tr>
            <td style="width: 100%;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
                <div class="company-header-title">PT. CONNECTI JELAJAH PRIANGAN</div>
                <div class="company-header-text">
                    Jl. Reog No.18, Turangga, Kec. Lengkong<br>
                    Kota Bandung, Jawa Barat 40264
                </div>
            </td>
        </tr>
    </table>

    <!-- Blue Horizontal Divider Line -->
    <div class="kop-divider"></div>

    <!-- Form Main Title -->
    <div class="form-title">FORM BERLANGGANAN (FB)</div>

    <!-- Outer Box Container -->
    <div class="outer-box">
        <!-- 1. DATA PELANGGAN / CUSTOMER DATA HEADER BAR -->
        <div class="section-bar">Data Pelanggan / Customer Data</div>

        <!-- Content Grid: 2 Columns -->
        <table class="table-section" style="border-bottom: 1px solid #000;">
            <tr>
                <!-- LEFT COLUMN -->
                <td style="width: 52%; border-right: 1px solid #000; padding-right: 6px;">
                    <table class="table-section">
                        <tr>
                            <td class="label-cell" style="width: 140px;">Tanggal ( Date )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $tglRegistrasi }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Atas nama pribadi</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nama ( Name )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alamat ( Address )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Kode Pos ( Zip Code )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">No. Telp. ( Phone Number )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $noTelp }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" colspan="3" style="font-weight: bold; padding-top: 3px;">Mewakili Instansi / Sekolah / Perusahaan</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nama ( Name )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Jabatan ( Job Title )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">No. HP ( Mobile Phone )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nama Perusahaan / Institusi</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="font-weight: bold; text-transform: uppercase;">{{ $namaPerusahaan }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" colspan="3" style="font-size: 7.5px; font-style: italic; color: #333; margin-top: -2px;">( Company / Institution Name )</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alamat Perusahaan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $alamatKtpLengkap ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">No. Telp ( Phone Number )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $noTelpPerusahaan }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">No. Fax ( Fax Number )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $noFax }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">NPWP Perusahaan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $npwp }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" colspan="3" style="font-size: 7.5px; font-style: italic; color: #333; margin-top: -2px;">( Tax Registration Number )</td>
                        </tr>
                        <tr>
                            <td class="label-cell" colspan="3" style="font-weight: bold; padding-top: 3px;">Lokasi Pemasangan</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nama Lokasi Pemasangan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $namaLokasiPasang }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alamat Lokasi Pemasangan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $alamatPasangLengkap ?: '-' }}</td>
                        </tr>
                    </table>
                </td>

                <!-- RIGHT COLUMN -->
                <td style="width: 48%; padding-left: 6px;">
                    <table class="table-section">
                        <tr>
                            <td class="label-cell" style="width: 120px;">No. FB</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="font-weight: bold;">{{ $nomorInternet }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">No. Identitas ( ID Number )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $idKtpPaspor }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alamat E-mail ( E-mail Add. )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $email }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Alternative E-mail Add.</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $emailAlt }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">PIC Teknis</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td class="label-cell">PIC Keuangan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight: bold; padding-top: 3px;">PIC Teknis</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Nama</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="font-weight: bold; text-transform: uppercase;">{{ $picTeknisNama }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Telp/ No. HP</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $picTeknisHp }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">E-mail</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="color: #0070c0;">{{ $picTeknisEmail }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight: bold; padding-top: 3px;">PIC Keuangan</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Nama</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="font-weight: bold; text-transform: uppercase;">{{ $picKeuanganNama }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Telp/ No. Hp</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $picKeuanganHp }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">E-mail</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell" style="color: #0070c0;">{{ $picKeuanganEmail }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight: bold; padding-top: 4px;">Jenis Perusahaan</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Pemerintahan</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $isPem ? 'v' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">BUMN</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $isBumn ? 'v' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Swasta</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $isSwasta ? 'v' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Personal</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $isPersonal ? 'v' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">Lain-lain ( Other )</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell">{{ $isLainLain ? ($customer->jenis_perusahaan ?? 'Kost an') : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell" style="padding-left: 8px;">PIC On Site</td>
                            <td class="colon-cell">:</td>
                            <td class="val-cell"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 2. KONTRAK / CONTACT HEADER BAR -->
        <div class="section-bar">Kontrak / Contact</div>
        <table class="table-section" style="border-bottom: 1px solid #000;">
            <tr>
                <td class="label-cell" style="width: 140px;">Lama Kontrak</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" style="width: 250px;">1 Tahun</td>
                <td class="label-cell" style="width: 90px;">Tanggal Mulai</td>
                <td class="colon-cell">:</td>
                <td class="val-cell">{{ $tglInstalasi }}</td>
            </tr>
        </table>

        <!-- 3. PEMBAYARAN / BILLING HEADER BAR -->
        <div class="section-bar">Pembayaran / Billing</div>
        <table class="table-section" style="border-bottom: 1px solid #000;">
            <tr>
                <td class="label-cell" style="width: 140px;">Tunai</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" colspan="4"></td>
            </tr>
            <tr>
                <td class="label-cell">Transfer</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" colspan="4"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="4">
                    <table class="table-section" style="width: 100%;">
                        <tr>
                            <td style="width: 130px; font-weight: bold;">Rek Bank Mandiri</td>
                            <td style="width: 170px;">No. 1310055566642</td>
                            <td>a.n. CONNECTI JELAJAH PRIANGAN</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 4. RINCIAN BIAYA / TERM OF PAYMENT HEADER BAR -->
        <div class="section-bar">Rincian Biaya / Term of Payment</div>
        <table class="table-section">
            <tr>
                <td class="label-cell" style="width: 140px; font-weight: bold;">Jenis Produk / Term of Product</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" colspan="3">
                    <span style="font-weight: bold; text-decoration: underline;">LocalLoop {{ $nominalBandwith }} Mbps</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>Personal ... Mbps</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>SOHO Up to .... Mbps</span>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Biaya Registrasi</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" colspan="3"></td>
            </tr>
            <tr>
                <td class="label-cell">Sewa Perangkat</td>
                <td class="colon-cell">:</td>
                <td class="val-cell" colspan="3"></td>
            </tr>
            <tr>
                <td class="label-cell" style="vertical-align: top;">Biaya Bulanan / Monthly Fee</td>
                <td class="colon-cell" style="vertical-align: top;">:</td>
                <td class="val-cell" colspan="3">
                    <table class="table-section" style="width: 250px;">
                        <tr>
                            <td style="width: 90px; font-weight: bold;">Sub Total</td>
                            <td style="width: 15px;">Rp.</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($subTotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">PPN 11 %</td>
                            <td>Rp.</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($ppn11, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; border-top: 1px solid #000;">TOTAL</td>
                            <td style="font-weight: bold; border-top: 1px solid #000;">Rp.</td>
                            <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;">{{ number_format($totalRaw, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- STATEMENT TEXT -->
    <div class="statement-text">
        Dengan ini kami menyatakan bahwa informasi yang kami berikan adalah benar.<br>
        Kami setuju untuk mengikuti Kontrak Berlangganan Internet menjadi satu kesatuan dengan formulir Berlangganan ini. Kami setuju untuk mematuhi Aturan Teknis berlangganan akses internet.
    </div>

    <!-- SIGNATURE AREA -->
    <table class="signature-table">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">
                <div style="font-weight: bold; text-transform: uppercase;">{{ $namaPerusahaan }}</div>
                <div class="signature-space" style="height: 50px;"></div>
                <div style="border-bottom: 1px dashed #000; width: 85%; margin: 0 auto 3px auto;"></div>
                <div style="font-size: 8px;">( Nama Terang & Tanda Tangan )</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER REQUIREMENTS -->
    <div class="footer-req">
        <div style="font-weight: bold;">Kelengkapan Dokumen / Documen Requirement :</div>
        <ul>
            <li>Fotocopy KTP / Paspor ( Copy of ID / Pasport )</li>
            <li>Fotocopy NPWP ( Copy of Tax Registration Number )</li>
            <li>Dilengkapi materai dan cap Perusahaan ( Enclose with Company stamp )</li>
        </ul>
    </div>

</body>
</html>

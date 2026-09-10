<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Layanan - {{ $billing->kode_billing_layanan ?? 'INVOICE' }}</title>
    <style>
        @page {
            margin: 18px 24px 18px 24px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            line-height: 1.3;
            color: #111827;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Top Header / Kop Surat */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-left-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0284c7 100%);
            background-color: #0f172a;
            height: 52px;
            border-radius: 4px;
            padding: 0 14px;
            color: #ffffff;
        }
        .header-company-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.8px;
            color: #ffffff;
            margin: 0;
        }
        .header-company-tagline {
            font-size: 8.5px;
            color: #38bdf8;
            font-weight: bold;
            letter-spacing: 1.5px;
            margin-top: 1px;
            text-transform: uppercase;
        }
        .header-logo-container {
            text-align: right;
            padding-left: 15px;
        }
        .header-logo-img {
            max-height: 52px;
            width: auto;
        }

        /* Title */
        .title-container {
            text-align: center;
            margin-top: 4px;
            margin-bottom: 12px;
        }
        .invoice-main-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #0f172a;
            display: inline-block;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1px;
            margin: 0;
        }
        .invoice-sub-title {
            font-size: 8px;
            font-style: italic;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 1px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* Info Section (Left: Pelanggan, Right: Tagihan) */
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 10px;
        }
        .info-box {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: top;
            border-radius: 2px;
            min-height: 85px;
        }
        .info-box-left {
            width: 48%;
        }
        .info-box-right {
            width: 52%;
        }
        .customer-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000000;
        }
        .customer-address {
            font-size: 8.5px;
            line-height: 1.35;
            color: #1f2937;
            text-transform: uppercase;
        }
        .company-bold-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .meta-list-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-list-table td {
            font-size: 8.5px;
            padding: 1.5px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 110px;
            color: #111827;
        }
        .meta-colon {
            width: 10px;
            text-align: center;
        }
        .meta-val {
            font-weight: 600;
            color: #000000;
        }

        /* Main Item Table */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 8px;
        }
        .item-table th {
            background-color: #000000;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #000000;
        }
        .item-table th.text-center {
            text-align: center;
        }
        .item-table th.text-right {
            text-align: right;
        }
        .item-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
            font-size: 9px;
            vertical-align: middle;
        }
        .item-row {
            height: 26px;
        }
        .terbilang-cell {
            padding: 6px 8px;
            font-size: 8.5px;
            vertical-align: top;
            background-color: #ffffff;
        }
        .terbilang-text {
            font-weight: bold;
            color: #0f172a;
        }
        .calc-label-cell {
            font-weight: bold;
            text-align: right;
            font-size: 8.5px;
            background-color: #ffffff;
            white-space: nowrap;
        }
        .calc-val-cell {
            text-align: right;
            font-weight: 600;
            font-size: 9px;
            background-color: #ffffff;
            white-space: nowrap;
        }
        .total-highlight {
            background-color: #00b4d8 !important;
            color: #000000 !important;
            font-size: 9.5px !important;
            font-weight: bold !important;
            padding: 6px !important;
        }

        /* Authorization / Payment Section (3 Columns) */
        .auth-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 12px;
        }
        .auth-table th {
            background-color: #000000;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            padding: 4px 6px;
            border: 1px solid #000000;
            width: 33.33%;
        }
        .auth-table td {
            border: 1px solid #000000;
            padding: 8px 6px;
            vertical-align: middle;
            text-align: center;
            font-size: 8.5px;
            height: 68px;
        }
        .pay-link {
            color: #0284c7;
            font-weight: bold;
            text-decoration: underline;
            font-size: 9px;
        }
        .sign-line {
            display: inline-block;
            width: 75%;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sign-sub {
            margin-top: 3px;
            font-size: 8px;
            color: #374151;
        }

        /* Perforated / Cut-out Line */
        .perforated-container {
            width: 100%;
            text-align: center;
            margin: 6px 0 10px 0;
            border-bottom: 1px dashed #6b7280;
            position: relative;
            line-height: 0.1em;
        }
        .perforated-text {
            background: #ffffff;
            padding: 0 8px;
            color: #9ca3af;
            font-size: 7.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Slip Pembayaran (Bottom Half) */
        .slip-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .slip-header-company {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            text-align: left;
        }
        .slip-header-title {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            text-align: right;
            letter-spacing: 0.5px;
        }
        .slip-box-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 8px;
        }
        .slip-box-table td {
            border: 1px solid #000000;
            padding: 4px 8px;
            font-size: 8.5px;
            width: 50%;
        }

        /* Slip Signatures */
        .slip-sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .slip-sign-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5px;
        }
        .slip-sign-space {
            height: 38px;
        }

        /* Catatan */
        .notes-container {
            font-size: 7.5px;
            line-height: 1.4;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .notes-container ul {
            margin: 0;
            padding-left: 14px;
        }
        .notes-container li {
            margin-bottom: 1.5px;
        }

        /* Bottom Footer Bar */
        .footer-bar-table {
            width: 100%;
            background-color: #0f766e;
            background: linear-gradient(135deg, #0e7490 0%, #0f766e 100%);
            color: #ffffff;
            border-collapse: collapse;
            border-radius: 4px;
            padding: 6px 8px;
        }
        .footer-bar-table td {
            color: #ffffff;
            font-size: 7.5px;
            line-height: 1.25;
            vertical-align: middle;
            padding: 4px 6px;
        }
        .footer-heading {
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
            color: #a5f3fc;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('img/logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // Nama Pelanggan
        $namaPelanggan = strtoupper($billing->nama_pelanggan ?: ($billing->nama_penduduk ?: 'PELANGGAN'));
        
        // Alamat Pasang Lengkap
        $alamatParts = array_filter([
            $billing->alamat_pasang ?: ($billing->alamat_p ?: ($billing->alamat_ktp ?: null)),
            !empty($billing->nama_kelurahan_pasang) ? 'KEL. ' . strtoupper($billing->nama_kelurahan_pasang) : null,
            !empty($billing->nama_kecamatan_pasang) ? 'KEC. ' . strtoupper($billing->nama_kecamatan_pasang) : null,
            !empty($billing->nama_kota_pasang) ? strtoupper($billing->nama_kota_pasang) : null,
            !empty($billing->nama_provinsi_pasang) ? strtoupper($billing->nama_provinsi_pasang) : null,
        ]);
        $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : ($billing->alamat_pasang ?: '-');

        // Nominal & Perhitungan
        $nominalTotal = (float)($billing->total_layanan ?? 0);
        $nominalPotongan = (float)($billing->potongan ?? 0);
        $subtotal = $nominalTotal + $nominalPotongan;

        $nominalPPN = 0;
        if (!empty($billing->ppn) && (float)$billing->ppn > 0) {
            $nominalPPN = round($subtotal * (float)$billing->ppn);
        }

        // Format Rupiah
        $totalFormatted = 'Rp ' . number_format($nominalTotal, 2, ',', '.');
        $subtotalFormatted = 'Rp ' . number_format($subtotal, 2, ',', '.');
        $potonganFormatted = $nominalPotongan > 0 ? 'Rp ' . number_format($nominalPotongan, 2, ',', '.') : '0';
        $ppnFormatted = $nominalPPN > 0 ? 'Rp ' . number_format($nominalPPN, 2, ',', '.') : 'Rp 0,00 (include)';

        // Layanan Text
        $bw = $billing->nominal_bandwith ? $billing->nominal_bandwith . ' Mbps' : '';
        $kategoriBw = strtoupper($billing->nama_kategori_bandwith ?: 'INTERNET BROADBAND');
        $layananText = "LAYANAN " . $kategoriBw . ($bw ? " " . $bw : "");

        // Periode & Jatuh Tempo
        $periodeText = $billing->periode_tagihan ?: ($billing->bulan_tagihan . ' ' . $billing->tahun_tagihan);
        if ($billing->expiry) {
            $jatuhTempoText = \Carbon\Carbon::parse($billing->expiry)->translatedFormat('d F Y');
        } else {
            $jatuhTempoText = 'Tanggal 20 Setiap Bulan';
        }

        // PIC Keuangan
        $picKeuangan = $financeName ?? 'AMELIA AGUSTINA PUTRI';
    @endphp

    <!-- 1. Header / Kop Surat -->
    <table class="header-table">
        <tr>
            <td class="header-left-bar">
                <div class="header-company-name">CONNECTI JELAJAH PRIANGAN</div>
                <div class="header-company-tagline">Koneksi Cepat &bull; Internet Service Provider</div>
            </td>
            <td class="header-logo-container" style="width: 170px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo CJP">
                @else
                    <div style="font-weight: bold; font-size: 13px; color: #0284c7;">CONNECTI JELAJAH</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- 2. Document Title -->
    <div class="title-container">
        <div class="invoice-main-title">INVOICE</div>
        <div class="invoice-sub-title">TAGIHAN</div>
    </div>

    <!-- 3. Customer & Company Info Boxes -->
    <table class="info-table">
        <tr>
            <td class="info-box info-box-left">
                <div class="customer-name">{{ $namaPelanggan }}</div>
                <div class="customer-address">{{ $alamatLengkap }}</div>
            </td>
            <td class="info-box info-box-right">
                <div class="company-bold-title">PT CONNECTI JELAJAH PRIANGAN</div>
                <table class="meta-list-table">
                    <tr>
                        <td class="meta-label">No tagihan</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $billing->kode_billing_layanan }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Nomor Pelanggan</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $billing->nomor_internet }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Periode Pemakaian</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $periodeText }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jatuh Tempo</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $jatuhTempoText }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. Main Item Table -->
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 55%;">Layanan</th>
                <th style="width: 15%;" class="text-center">Qty</th>
                <th style="width: 25%;" class="text-right">Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="item-row">
                <td class="text-center">1</td>
                <td><strong>{{ $layananText }}</strong></td>
                <td class="text-center">1</td>
                <td class="text-right">{{ $subtotalFormatted }}</td>
            </tr>
            <tr>
                <td colspan="2" rowspan="3" class="terbilang-cell">
                    <span style="font-weight: normal; color: #4b5563;">Terbilang : </span>
                    <span class="terbilang-text">{{ $terbilangText }}</span>
                </td>
                <td class="calc-label-cell">POTONGAN</td>
                <td class="calc-val-cell">{{ $potonganFormatted }}</td>
            </tr>
            <tr>
                <td class="calc-label-cell">PPN</td>
                <td class="calc-val-cell">{{ $ppnFormatted }}</td>
            </tr>
            <tr>
                <td class="calc-label-cell total-highlight">TAGIHAN BULAN INI</td>
                <td class="calc-val-cell total-highlight">{{ $totalFormatted }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 5. Authorization / Payment Section (3 Columns) -->
    <table class="auth-table">
        <thead>
            <tr>
                <th>Pembayaran</th>
                <th>Mengetahui</th>
                <th>Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-weight: bold; margin-bottom: 4px; font-size: 8px; color: #374151;">LINK PEMBAYARAN :</div>
                    @if(!empty($paymentUrl))
                        <a href="{{ $paymentUrl }}" target="_blank" class="pay-link">Klik Disini</a>
                    @else
                        <a href="https://cjp.net.id/payment/{{ $billing->nomor_internet }}" target="_blank" class="pay-link">Klik Disini</a>
                    @endif
                    @if(!empty($billing->nama_bank) || !empty($billing->no_rekening))
                        <div style="font-size: 7.5px; color: #64748b; margin-top: 4px;">
                            {{ $billing->nama_bank ?: 'Bank' }} &bull; {{ $billing->no_rekening }}
                        </div>
                    @endif
                </td>
                <td>
                    <div style="height: 34px;"></div>
                    <span class="sign-line">{{ $picKeuangan }}</span>
                    <div class="sign-sub">Keuangan</div>
                </td>
                <td>
                    <div style="height: 34px;"></div>
                    <span class="sign-line">{{ $namaPelanggan }}</span>
                    <div class="sign-sub">Pelanggan</div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 6. Perforated / Cut-out Line -->
    <div class="perforated-container">
        <span class="perforated-text">&ndash;&ndash; Gunting / Potong Disini Untuk Bukti Pembayaran &ndash;&ndash;</span>
    </div>

    <!-- 7. Slip Pembayaran (Bottom Half) -->
    <table class="slip-header-table">
        <tr>
            <td class="slip-header-company">PT. CONNECTI JELAJAH PRIANGAN</td>
            <td class="slip-header-title">SLIP PEMBAYARAN</td>
        </tr>
    </table>

    <table class="slip-box-table">
        <tr>
            <td>
                <strong>Nomor Tagihan :</strong> {{ $billing->kode_billing_layanan }}
            </td>
            <td>
                <strong>Periode Tagihan :</strong> {{ $periodeText }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Nomor Pelanggan :</strong> {{ $billing->nomor_internet }}
            </td>
            <td>
                <strong>Jatuh Tempo :</strong> {{ $jatuhTempoText }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Nama Pelanggan :</strong> {{ $namaPelanggan }}
            </td>
            <td>
                <strong>Jumlah Tagihan :</strong> <strong>{{ $totalFormatted }}</strong>
            </td>
        </tr>
    </table>

    <!-- Dual Signatures in Slip -->
    <table class="slip-sign-table">
        <tr>
            <td>Petugas</td>
            <td>Pelanggan</td>
        </tr>
        <tr>
            <td class="slip-sign-space"></td>
            <td class="slip-sign-space"></td>
        </tr>
        <tr>
            <td><strong>TTD / Nama</strong></td>
            <td><strong>{{ $namaPelanggan }}</strong></td>
        </tr>
    </table>

    <!-- Catatan & Ketentuan -->
    <div class="notes-container">
        <div class="notes-title">catatan :</div>
        <ul>
            <li>Apabila pelanggan belum melakukan pembayaran sampai dengan jatuh tempo (Maksimal Tanggal 20 setiap bulan), maka akan dilakukan pemutusan koneksi sementara terhitung mulai pukul 24.00 pada tanggal akhir periode sebelumnya.</li>
            <li>Untuk pelanggan yang melakukan pembayaran melalui <strong>Transfer Bank</strong>, mohon memberikan konfirmasi via Whatsapp ke nomor <strong>0895-0841-6636</strong> dengan mencantumkan bukti pembayaran.</li>
        </ul>
    </div>

    <!-- 8. Bottom Footer Bar -->
    <table class="footer-bar-table">
        <tr>
            <td style="width: 32%;">
                <div class="footer-heading">OFFICE</div>
                Jl. Reog No. 18, Turangga, Kec. Lengkong<br>
                Kota Bandung, Jawa Barat 40264
            </td>
            <td style="width: 25%;">
                <div class="footer-heading">OPERATIONAL</div>
                Bandung &amp; Sekitarnya<br>
                Jawa Barat, Indonesia
            </td>
            <td style="width: 23%;">
                <div class="footer-heading">CONTACT</div>
                022-3050-0111<br>
                0895-0841-6636 (WA)
            </td>
            <td style="width: 20%; text-align: right;">
                <div class="footer-heading" style="text-align: right;">ONLINE</div>
                cjp.net.id<br>
                info@cjp.net.id
            </td>
        </tr>
    </table>

</body>
</html>

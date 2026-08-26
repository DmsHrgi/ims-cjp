<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Survey - {{ $customer->nomor_internet ?? '' }}</title>
    <style>
        @page {
            margin: 45px 55px 45px 55px;
            size: a4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #000000;
            background-color: #ffffff;
        }
        .text-center {
            text-align: center;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .nomor-surat {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 35px;
        }
        .content-p {
            text-align: justify;
            margin-bottom: 16px;
            line-height: 1.6;
        }
        .table-data {
            width: 100%;
            margin: 15px 0 20px 20px;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 13px;
        }
        .label-col {
            width: 160px;
            font-weight: normal;
        }
        .colon-col {
            width: 25px;
            text-align: center;
        }
        .val-col {
            font-weight: normal;
        }
        .closing-p {
            text-align: justify;
            margin-top: 20px;
            margin-bottom: 16px;
            line-height: 1.6;
        }
        .regards {
            margin-top: 15px;
            margin-bottom: 30px;
        }
        .table-signature {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table-signature td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 13px;
        }
        .space-sign {
            height: 90px;
        }
        .sign-name {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="title">SURAT TUGAS SURVEY</div>
        <div class="nomor-surat">Nomor : &nbsp;&nbsp;&nbsp;&nbsp;/CJP-NOC/{{ $surveyMonth ?? date('m') }}/{{ $surveyYear ?? date('Y') }}</div>
    </div>

    <div class="content-p">
        Dengan surat ini pada tanggal, <strong>{{ $surveyDateFormatted }}</strong> PT MediaSolusi Network menugaskan tim teknisi, <strong>{{ strtoupper($surveyTeam) }}</strong>. Untuk melakukan survei lokasi pemasangan jaringan internet ke pada pelanggan baru dengan rincian sebagai berikut :
    </div>

    <table class="table-data">
        <tr>
            <td class="label-col">Nama Pelanggan</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ strtoupper($customerName) }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ strtoupper($installationAddress) }}</td>
        </tr>
        <tr>
            <td class="label-col">PIC</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $picName ?: '' }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Telepone</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $phoneNumber ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Layanan</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $serviceName }}</td>
        </tr>
        <tr>
            <td class="label-col">Detail Pekerjaan</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $jobDetails ?: '-' }}</td>
        </tr>
    </table>

    <div class="closing-p">
        Demikian surat tugas ini dibuat dan dapat dipertanggung jawabkan dan dapat digunakan sebagaimana mestinya, terimakasih .
    </div>

    <div class="regards">
        Hormat Kami PT Media Solusi Network, Bandung.
    </div>

    <table class="table-signature">
        <tr>
            <td>Penanggung Jawab</td>
            <td>Pelanggan</td>
        </tr>
        <tr>
            <td class="space-sign"></td>
            <td class="space-sign"></td>
        </tr>
        <tr>
            <td class="sign-name">{{ strtoupper($personInCharge) }}</td>
            <td class="sign-name">{{ strtoupper($customerName) }}</td>
        </tr>
    </table>

</body>
</html>

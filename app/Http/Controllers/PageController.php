<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /* =========================================================
     *  DASHBOARD  (4 metrik bulan-ini + grafik 7 bulan, semua nyata)
     * =======================================================*/
    public function dashboard()
    {
        $namaBulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];

        // Grafik: jumlah pendaftaran per bulan, 7 bulan terakhir (data nyata; bulan kosong = 0)
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->copy()->subMonthsNoOverflow($i);
            $chart[] = [
                'label' => $namaBulan[$d->month] . " '" . substr((string) $d->year, 2),
                'count' => $this->monthCount('trx_batchjob_register', 'date_create', $d->year, $d->month),
            ];
        }
        $maxChart = max(array_column($chart, 'count')) ?: 1;

        // Kartu metrik: hitungan bulan ini vs bulan lalu (trend dihitung, bukan ditebak)
        $c1 = $this->cardStat('trx_batchjob_register', 'date_create'); // Pendaftaran Baru
        $c2 = $this->cardStat('trx_tiket_gangguan',    'date_create'); // Tiket Gangguan
        $c3 = $this->cardStat('trx_suspend',           'date_create'); // Suspend
        $c4 = $this->cardStat('trx_terminasi',         'date_create'); // Terminasi

        return view('dashboard', compact('chart', 'maxChart', 'c1', 'c2', 'c3', 'c4'));
    }

    /* =========================================================
     *  TIKET  (angka "X Tiket" per kartu, dari tabel masing-masing)
     * =======================================================*/
    public function tiket()
    {
        $counts = [
            'gangguan'  => DB::table('trx_tiket_gangguan')->count(),
            'password'  => 0, // CATATAN: skema ims TIDAK punya tabel tiket ganti-password -> selalu 0 (bukan dummy)
            'coverage'  => DB::table('trx_coverage_area')->count(),
            'terminasi' => DB::table('trx_terminasi')->count(),
            'suspend'   => DB::table('trx_suspend')->count(),
            'pasang'    => DB::table('trx_batchjob_register')->count(),
            'ubah'      => DB::table('trx_ubah_layanan')->count(),
        ];
        return view('tiket', compact('counts'));
    }

    /* =========================================================
     *  DETAIL (di-wire penuh di paruh 2B; method sudah siap sekarang)
     * =======================================================*/
    public function gangguan()
    {
        $rows = DB::table('trx_tiket_gangguan as t')
            ->leftJoin('view_batchjob as b', 'b.nomor_internet', '=', 't.nomor_internet')
            ->select('t.*', 'b.nama_penduduk', 'b.nama_pelanggan', 'b.jenis_kelamin',
                'b.nama_kategori_bandwith', 'b.nominal_bandwith', 'b.alamat_p')
            ->orderByDesc('t.date_create')->paginate(10);

        $rows->getCollection()->transform(fn($r) => $this->decorate($r));
        return view('tiket.gangguan-layanan', compact('rows'));
    }

    public function gantiPassword()
    {
        // Skema ims tidak menyediakan tabel tiket ganti-password -> halaman selalu kosong (jujur, bukan dummy)
        $rows = collect();
        return view('tiket.ganti-password', compact('rows'));
    }

    public function coverage()
    {
        $rows = DB::table('trx_coverage_area')->orderByDesc('date_create')->paginate(10);
        return view('tiket.coverage-area', compact('rows'));
    }

    public function pendaftaran()
    {
        $rows = DB::table('view_batchjob')->orderByDesc('date_create')->paginate(10);
        $rows->getCollection()->transform(fn($r) => $this->decorate($r));
        return view('pendaftaran.pemasangan-baru', compact('rows'));
    }

    /* =========================================================
     *  PELANGGAN  (papan 4 seksi x kategori bandwith, data-driven)
     * =======================================================*/
    public function pelanggan(Request $request)
    {
        // 1. Master kategori
        $categories = DB::table('m_bandwith_kategori')
            ->orderBy('kode_kategori_bandwith')
            ->pluck('nama_kategori_bandwith');

        // 2. Daftar Wilayah & Master List
        $wilayahList = DB::table('view_batchjob')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->whereNotNull('nama_kota_pasang')
            ->where('nama_kota_pasang', '!=', '')
            ->distinct()
            ->orderBy('nama_kota_pasang')
            ->pluck('nama_kota_pasang');

        $mediaAksesList = DB::table('m_media_akses')
            ->where(function ($q) { $q->where('hide', '0')->orWhereNull('hide'); })
            ->orderBy('nama_media_akses')
            ->pluck('nama_media_akses');

        $groupFromReg = DB::table('trx_batchjob_register')
            ->select('group_layanan')
            ->distinct()
            ->whereNotNull('group_layanan')
            ->where('group_layanan', '!=', '')
            ->pluck('group_layanan')
            ->toArray();

        $groupFromMitra = DB::table('m_mitra')
            ->select('nama_mitra as group_layanan')
            ->distinct()
            ->whereNotNull('nama_mitra')
            ->where('nama_mitra', '!=', '')
            ->pluck('group_layanan')
            ->toArray();

        $groupLayananList = collect(array_merge($groupFromReg, $groupFromMitra))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // 3. Data untuk hitungan Kartu (semua / per wilayah)
        $cardsQuery = DB::table('view_batchjob')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            });

        if ($request->filled('wilayah')) {
            $cardsQuery->where('nama_kota_pasang', $request->wilayah);
        }

        $cardRows = $cardsQuery
            ->select('nama_kategori_bandwith', 'is_termin', 'is_suspend', 'status_reg', 'desc_registrasi', 'aktivasi_date_finish')
            ->get();

        $defs = [
            ['key' => 'aktif',     'title' => 'Aktif'],
            ['key' => 'terminasi', 'title' => 'Terminasi'],
            ['key' => 'suspend',   'title' => 'Suspend'],
            ['key' => 'gagal',     'title' => 'Gagal Pasang'],
        ];

        $reqSection = $request->input('section');
        $reqKategori = $request->input('kategori');

        $sections = [];
        foreach ($defs as $d) {
            $cards = [];
            $idx = [];
            foreach ($categories as $cat) {
                $idx[$cat] = count($cards);
                $isActive = ($reqSection === $d['key'] && $reqKategori === $cat);
                $cards[] = [
                    'label' => $cat,
                    'total' => 0,
                    'active' => $isActive,
                ];
            }
            foreach ($cardRows as $r) {
                if ($this->sectionOf($r) === $d['key'] && $r->nama_kategori_bandwith !== null && isset($idx[$r->nama_kategori_bandwith])) {
                    $cards[$idx[$r->nama_kategori_bandwith]]['total']++;
                }
            }
            $sections[] = [
                'key' => $d['key'],
                'title' => $d['title'],
                'cards' => $cards,
                'active' => ($reqSection === $d['key'] && (empty($reqKategori) || $reqKategori === 'ALL')),
            ];
        }

        // 4. Data untuk Tabel Pelanggan
        $tableQuery = DB::table('view_batchjob')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            });

        if ($request->filled('wilayah')) {
            $tableQuery->where('nama_kota_pasang', $request->wilayah);
        }

        if ($request->filled('kategori') && $request->kategori !== 'ALL') {
            $tableQuery->where('nama_kategori_bandwith', $request->kategori);
        }

        if ($request->filled('search')) {
            $searchStr = trim($request->search);
            $tableQuery->where(function ($q) use ($searchStr) {
                $q->where('nama_pelanggan', 'like', '%' . $searchStr . '%')
                  ->orWhere('nama_penduduk', 'like', '%' . $searchStr . '%')
                  ->orWhere('nomor_internet', 'like', '%' . $searchStr . '%')
                  ->orWhere('alamat_pasang', 'like', '%' . $searchStr . '%')
                  ->orWhere('alamat_p', 'like', '%' . $searchStr . '%')
                  ->orWhere('nama_kota_pasang', 'like', '%' . $searchStr . '%');
            });
        }

        $allTableRows = $tableQuery->orderByDesc('date_create')->get();

        if ($request->filled('section')) {
            $sec = $request->section;
            if ($sec === 'aktif') {
                $allTableRows = $allTableRows->filter(function ($r) {
                    return in_array($this->sectionOf($r), ['aktif', 'suspend'], true);
                });
            } else {
                $allTableRows = $allTableRows->filter(function ($r) use ($sec) {
                    return $this->sectionOf($r) === $sec;
                });
            }
        } else {
            // Default saat membuka menu Pelanggan: Tampilkan Pelanggan Aktif & Suspend (Pelanggan yang sudah selesai aktivasi)
            $allTableRows = $allTableRows->filter(function ($r) {
                return in_array($this->sectionOf($r), ['aktif', 'suspend'], true);
            });
        }

        $allTableRows->transform(function ($r) {
            $r = $this->decorate($r);
            $r->section = $this->sectionOf($r);
            return $r;
        });

        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = (int) ($request->entries ?? 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $itemsForCurrentPage = $allTableRows->slice(($page - 1) * $perPage, $perPage)->values();
        $customers = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $allTableRows->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('pelanggan', compact('sections', 'wilayahList', 'mediaAksesList', 'groupLayananList', 'customers', 'categories'));
    }

    public function pelangganDetail($nomorInternet)
    {
        $customer = DB::table('view_batchjob')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        if (!$customer) {
            return redirect()->route('pelanggan')->withErrors(['error' => 'Pelanggan tidak ditemukan.']);
        }

        $customer = $this->decorate($customer);

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        $targetId = $customer->id_perusahaan ?? $customer->nik_penduduk ?? ($reg ? ($reg->id_perusahaan ?? $reg->nik_penduduk) : null);
        $pelanggan = $targetId ? DB::table('m_pelanggan')->where('id_perusahaan', $targetId)->first() : null;

        $customer->scan_dokumen = $reg->scan_dokumen ?? $customer->scan_dokumen ?? null;
        $customer->scan_dokumen_survey = $reg->scan_dokumen_survey ?? $customer->scan_dokumen_survey ?? null;
        $customer->scan_dokumen_instalasi = $reg->scan_dokumen_instalasi ?? $customer->scan_dokumen_instalasi ?? null;
        $customer->scan_dokumen_aktivasi = $reg->scan_dokumen_aktivasi ?? $customer->scan_dokumen_aktivasi ?? null;

        // Seksi 1: Informasi Pelanggan
        $customer->nama_perusahaan = $pelanggan->nama_perusahaan ?? $reg->nama_pelanggan ?? $customer->nama_perusahaan ?? $customer->nama_pelanggan ?? $customer->nama_penduduk ?? null;
        $customer->no_telp_perusahaan = $pelanggan->no_telp_perusahaan ?? $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? null;
        $customer->email_perusahaan = $pelanggan->email_perusahaan ?? $customer->email_perusahaan ?? $customer->email ?? null;
        $customer->id_perusahaan = $pelanggan->id_perusahaan ?? $reg->id_perusahaan ?? $customer->id_perusahaan ?? $customer->nik_penduduk ?? null;
        $customer->jenis_perusahaan = $pelanggan->jenis_perusahaan ?? $customer->jenis_perusahaan ?? 'PT';
        $customer->tanggal_registrasi = $pelanggan->tanggal_registrasi ?? $customer->tanggal_registrasi ?? (isset($customer->date_create) ? substr($customer->date_create, 0, 10) : null);

        // PIC Teknis & PIC Keuangan
        $customer->nama_pic_teknis = $pelanggan->nama_pic_teknis ?? $customer->nama_pic_teknis ?? $customer->pic ?? null;
        $customer->no_telp_pic_teknis = $pelanggan->no_telp_pic_teknis ?? $customer->no_telp_pic_teknis ?? $customer->nomor_hp_2 ?? $customer->nomor_hp ?? null;
        $customer->email_pic_teknis = $pelanggan->email_pic_teknis ?? $customer->email_pic_teknis ?? $customer->email ?? null;

        $customer->nama_pic_keuangan = $pelanggan->nama_pic_keuangan ?? $customer->nama_pic_keuangan ?? $customer->pic ?? null;
        $customer->no_telp_pic_keuangan = $pelanggan->no_telp_pic_keuangan ?? $customer->no_telp_pic_keuangan ?? $customer->nomor_hp ?? null;
        $customer->email_pic_keuangan = $pelanggan->email_pic_keuangan ?? $customer->email_pic_keuangan ?? $customer->email ?? null;

        // Seksi 2: Alamat Perusahaan
        $customer->rt_ktp = $reg->rt_perusahaan ?? $pelanggan->rt_ktp ?? $customer->rt_perusahaan ?? $customer->rt_ktp ?? $customer->rt_pasang ?? null;
        $customer->rw_ktp = $reg->rw_perusahaan ?? $pelanggan->rw_ktp ?? $customer->rw_perusahaan ?? $customer->rw_ktp ?? $customer->rw_pasang ?? null;
        $customer->nomor_bangunan_perusahaan = $reg->nomor_bangunan_perusahaan ?? $customer->nomor_bangunan_perusahaan ?? $customer->nomor_bangunan ?? null;
        $customer->alamat_ktp = $reg->detail_alamat_perusahaan ?? $pelanggan->alamat_ktp ?? $customer->detail_alamat_perusahaan ?? $customer->alamat_perusahaan ?? $customer->alamat_ktp ?? $customer->alamat_k ?? null;
        $customer->lon_lat_perusahaan = $reg->lon_lat_perusahaan ?? $pelanggan->lon_lat_perusahaan ?? $customer->lon_lat_perusahaan ?? null;
        $customer->sharelock_perusahaan = $reg->sharelock_perusahaan ?? $pelanggan->sharelock_perusahaan ?? $customer->sharelock_perusahaan ?? null;
        if (empty($customer->sharelock_perusahaan) && !empty($customer->lon_lat_perusahaan)) {
            $customer->sharelock_perusahaan = 'https://www.google.com/maps?q=' . urlencode(trim($customer->lon_lat_perusahaan));
        }

        $kodeKelCorp = $reg->kode_wilayah_kelurahan_perusahaan ?? $pelanggan->kode_wilayah_kelurahan_ktp ?? $customer->kode_wilayah_kelurahan_perusahaan ?? $customer->kode_wilayah_kelurahan_ktp ?? $customer->kode_wilayah_kelurahan_pasang ?? null;
        if ($kodeKelCorp) {
            $wilCorp = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelCorp)->first();
            if ($wilCorp) {
                $customer->nama_kelurahan_corp = $wilCorp->nama_kelurahan;
                $customer->nama_kecamatan_corp = $wilCorp->nama_kecamatan;
                $customer->nama_kota_corp = $wilCorp->nama_kota;
                $customer->nama_provinsi_corp = $wilCorp->nama_provinsi;
            }
        }

        // Seksi 3: Alamat Pemasangan
        $customer->rt_pasang = $reg->rt_pasang ?? $customer->rt_pasang ?? null;
        $customer->rw_pasang = $reg->rw_pasang ?? $customer->rw_pasang ?? null;
        $customer->nomor_bangunan = $reg->nomor_bangunan ?? $customer->nomor_bangunan ?? null;
        $customer->alamat_pasang = $reg->alamat_pasang ?? $customer->alamat_pasang ?? $customer->alamat_p ?? null;
        $customer->jenis_bangunan = $reg->jenis_bangunan ?? $customer->jenis_bangunan ?? null;
        $customer->note_request = $reg->note_request ?? $customer->note_request ?? null;
        $customer->lon_lat_pasang = $reg->lon_lat ?? $customer->lon_lat ?? null;
        $customer->sharelock_pasang = $reg->loc_maps ?? $customer->loc_maps ?? null;
        if (empty($customer->sharelock_pasang) && !empty($customer->lon_lat_pasang)) {
            $customer->sharelock_pasang = 'https://www.google.com/maps?q=' . urlencode(trim($customer->lon_lat_pasang));
        }

        $kodeKelPasang = $reg->kode_wilayah_kelurahan_pasang ?? $customer->kode_wilayah_kelurahan_pasang ?? $kodeKelCorp;
        if ($kodeKelPasang) {
            $wilPasang = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelPasang)->first();
            if ($wilPasang) {
                $customer->nama_kelurahan_pasang = $wilPasang->nama_kelurahan;
                $customer->nama_kecamatan_pasang = $wilPasang->nama_kecamatan;
                $customer->nama_kota_pasang = $wilPasang->nama_kota;
                $customer->nama_provinsi_pasang = $wilPasang->nama_provinsi;
            }
        }

        // Seksi 4 & 5: Paket, Penugasan & Financial
        $customer->group_layanan = $reg->group_layanan ?? $customer->group_layanan ?? null;
        $customer->nama_sales = $reg->nama_sales ?? $customer->nama_sales ?? null;
        $customer->lon_lat = $customer->lon_lat_pasang;
        $customer->loc_maps = $customer->sharelock_pasang;

        $billReg = DB::table('trx_billing_registrasi')->where('nomor_internet', $nomorInternet)->first();
        $customer->harga_paket = $billReg->total_reg ?? $customer->harga_bandwith ?? $reg->total_registrasi ?? null;
        $customer->biaya_reg = $billReg->biaya_registrasi ?? $reg->biaya_reg ?? $customer->biaya_reg ?? null;

        // Foto Berkas & Data Survey
        $instData = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
        $customer->foto_po = $reg->foto_po ?? $instData->foto_ktp ?? $customer->foto_po ?? $customer->foto_ktp ?? null;
        $customer->foto_bangunan = $reg->foto_bangunan ?? $instData->foto_rumah ?? $customer->foto_bangunan ?? $customer->foto_rumah ?? null;
        $customer->foto_peta = $instData->foto_peta ?? $customer->foto_peta ?? null;
        if ($instData) {
            $customer->survey_date_start = $instData->survey_date_start ?? $customer->survey_date_start ?? null;
            $customer->survey_date_finish = $instData->survey_date_finish ?? $customer->survey_date_finish ?? null;
            $customer->survey_time = $instData->survey_time ?? $customer->survey_time ?? null;
            $customer->survey_team = $instData->survey_team ?? $customer->survey_team ?? null;
            $customer->survey_note = $instData->survey_note ?? $customer->survey_note ?? null;
        }

        // --- DATA LOG / RIWAYAT AKTIVITAS ---
        $logs = collect();

        // 1. Logs dari trx_batchjob_register_log (Termasuk Registrasi, Aktivasi, Survey, Instalasi, Edit Data, Adjust Data)
        $dbLogs = DB::table('trx_batchjob_register_log as l')
            ->leftJoin('m_status_registrasi as s', 's.status_reg', '=', 'l.status_reg')
            ->select('l.*', 's.desc_registrasi')
            ->where('l.nomor_internet', $nomorInternet)
            ->orderByDesc('l.date_create')
            ->get();

        foreach ($dbLogs as $dl) {
            $note = $dl->note_schedule ?: 'Log aktivitas registrasi';
            $statusText = $dl->desc_registrasi ?? ('Pembaruan Status (' . $dl->status_reg . ')');
            $badgeColor = 'bg-blue-100 text-blue-800 border border-blue-200';

            if (str_contains(strtoupper((string) $note), 'EDIT') || str_contains(strtoupper((string) $note), 'UPDATE')) {
                $statusText = 'Edit Data Pelanggan';
                $badgeColor = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
            } elseif (str_contains(strtoupper((string) $note), 'ADJUST') || str_contains(strtoupper((string) $note), 'PENYESUAIAN')) {
                $statusText = 'Adjust Data';
                $badgeColor = 'bg-cyan-100 text-cyan-800 border border-cyan-200';
            }

            $logs->push((object)[
                'status'     => $statusText,
                'keterangan' => $note,
                'tanggal'    => $dl->date_schedule ?: $dl->date_create,
                'user'       => $dl->user_create ?: 'System',
                'badge'      => $badgeColor
            ]);
        }

        // 2. Logs dari Terminasi (trx_terminasi & trx_terminasi_log)
        $termLogs = DB::table('trx_terminasi_log as tl')
            ->join('trx_terminasi as t', 't.kode_trx_terminasi', '=', 'tl.kode_trx_terminasi')
            ->leftJoin('m_status_terminasi as st', 'st.status_terminasi', '=', 'tl.status_terminasi')
            ->select('tl.*', 'st.desc_terminasi', 't.note_termin')
            ->where('t.nomor_internet', $nomorInternet)
            ->orderByDesc('tl.date_create')
            ->get();

        foreach ($termLogs as $tl) {
            $logs->push((object)[
                'status'     => 'Terminasi: ' . ($tl->desc_terminasi ?? ('KD' . $tl->status_terminasi)),
                'keterangan' => $tl->note_schedule ?: ($tl->note_termin ?: 'Aktivitas permintaan terminasi'),
                'tanggal'    => $tl->date_schedule ?: $tl->date_create,
                'user'       => $tl->user_create ?: 'System',
                'badge'      => 'bg-rose-100 text-rose-800 border border-rose-200'
            ]);
        }

        // 3. Logs dari Up/Downgrade (trx_ubah_layanan & trx_ubah_layanan_log)
        $ubahLogs = DB::table('trx_ubah_layanan_log as ul')
            ->join('trx_ubah_layanan as u', 'u.kode_trx_ubah_layanan', '=', 'ul.kode_trx_ubah_layanan')
            ->leftJoin('m_status_ubahlayanan as su', 'su.status_ubah_layanan', '=', 'ul.status_ubah_layanan')
            ->select('ul.*', 'su.desc_ubah_layanan')
            ->where('u.nomor_internet', $nomorInternet)
            ->orderByDesc('ul.date_create')
            ->get();

        foreach ($ubahLogs as $ul) {
            $logs->push((object)[
                'status'     => 'Up/Downgrade: ' . ($ul->desc_ubah_layanan ?? ('KD' . $ul->status_ubah_layanan)),
                'keterangan' => $ul->note_ubah_layanan ?: 'Aktivitas permintaan ubah layanan',
                'tanggal'    => $ul->date_create,
                'user'       => $ul->user_create ?: 'System',
                'badge'      => 'bg-purple-100 text-purple-800 border border-purple-200'
            ]);
        }

        // 4. Logs dari Suspend (trx_suspend & trx_suspend_log)
        $suspLogs = DB::table('trx_suspend_log as sl')
            ->join('trx_suspend as s', 's.kode_suspend', '=', 'sl.kode_suspend')
            ->leftJoin('m_status_suspend as ss', 'ss.status_suspend', '=', 'sl.status_suspend')
            ->select('sl.*', 'ss.desc_status_suspend', 's.desc_suspend', 's.desc_suspend_cancel')
            ->where('s.nomor_internet', $nomorInternet)
            ->orderByDesc('sl.date_create')
            ->get();

        foreach ($suspLogs as $sl) {
            $desc = ($sl->status_suspend == '14') ? ($sl->desc_suspend_cancel ?: 'Permintaan suspend dibatalkan') : ($sl->desc_suspend ?: 'Aktivitas permintaan suspend');
            $logs->push((object)[
                'status'     => 'Suspend: ' . ($sl->desc_status_suspend ?? ('KD' . $sl->status_suspend)),
                'keterangan' => $desc,
                'tanggal'    => $sl->date_create,
                'user'       => $sl->user_create ?: 'System',
                'badge'      => 'bg-amber-100 text-amber-800 border border-amber-200'
            ]);
        }

        // 5. Log Aktivasi
        if (!empty($customer->aktivasi_date_start) || !empty($customer->aktivasi_date_finish)) {
            $logs->push((object)[
                'status'     => !empty($customer->aktivasi_date_finish) ? 'Aktivasi Selesai' : 'Proses Aktivasi',
                'keterangan' => trim(($customer->aktivasi_team ? 'Tim: ' . $customer->aktivasi_team . '. ' : '') . ($customer->aktivasi_note ?: $customer->aktivasi_note_finish ?: 'Proses aktivasi layanan')),
                'tanggal'    => $customer->aktivasi_date_finish ?: $customer->aktivasi_date_start,
                'user'       => $customer->user_update ?: $customer->user_create ?: 'System',
                'badge'      => 'bg-emerald-100 text-emerald-800 border border-emerald-200'
            ]);
        }

        // 6. Log Instalasi
        if (!empty($customer->instalasi_date_start) || !empty($customer->instalasi_date_finish)) {
            $logs->push((object)[
                'status'     => !empty($customer->instalasi_date_finish) ? 'Selesai Instalasi' : 'Jadwal Instalasi Terbit',
                'keterangan' => trim(($customer->instalasi_team ? 'Tim: ' . $customer->instalasi_team . '. ' : '') . ($customer->instalasi_note ?: $customer->instalasi_note_finish ?: 'Pemasangan perangkat & jaringan')),
                'tanggal'    => $customer->instalasi_date_finish ?: $customer->instalasi_date_start,
                'user'       => $customer->user_update ?: $customer->user_create ?: 'System',
                'badge'      => 'bg-indigo-100 text-indigo-800 border border-indigo-200'
            ]);
        }

        // 7. Log Survey
        if (!empty($customer->survey_date_start) || !empty($customer->survey_date_finish)) {
            $logs->push((object)[
                'status'     => !empty($customer->survey_date_finish) ? 'Selesai Survey' : 'Jadwal Survey Terbit',
                'keterangan' => trim(($customer->survey_team ? 'Tim: ' . $customer->survey_team . '. ' : '') . ($customer->survey_note ?: $customer->survey_note_finish ?: 'Pemeriksaan lokasi dan jalur FO')),
                'tanggal'    => $customer->survey_date_finish ?: $customer->survey_date_start,
                'user'       => $customer->user_create ?: 'System',
                'badge'      => 'bg-amber-100 text-amber-800 border border-amber-200'
            ]);
        }

        // 8. Log Registrasi Awal
        $logs->push((object)[
            'status'     => 'Pendaftaran Registrasi (' . ($customer->desc_registrasi ?? 'Baru') . ')',
            'keterangan' => $customer->note_request ?: 'Registrasi pendaftaran baru dibuat',
            'tanggal'    => $customer->date_create,
            'user'       => $customer->user_create ?: 'System',
            'badge'      => 'bg-cyan-100 text-cyan-800 border border-cyan-200'
        ]);

        $logs = $logs->sortByDesc('tanggal')->values();

        // Data Tab Terkait
        $ubahLayanan = DB::table('trx_ubah_layanan as u')
            ->leftJoin('m_status_ubahlayanan as ms', 'ms.status_ubah_layanan', '=', 'u.status_ubah_layanan')
            ->leftJoin('m_bandwith as bl', 'bl.kode_bandwith', '=', 'u.kode_bandwith_lama')
            ->leftJoin('m_bandwith_kategori as kl', 'kl.kode_kategori_bandwith', '=', 'bl.kode_kategori_bandwith')
            ->leftJoin('m_bandwith as bb', 'bb.kode_bandwith', '=', 'u.kode_bandwith_baru')
            ->leftJoin('m_bandwith_kategori as kb', 'kb.kode_kategori_bandwith', '=', 'bb.kode_kategori_bandwith')
            ->select(
                'u.*',
                'ms.desc_ubah_layanan',
                'kl.nama_kategori_bandwith as nama_kategori_bandwith_lama',
                'bl.nominal_bandwith as nominal_bandwith_lama',
                'bl.harga_bandwith as harga_bandwith_lama',
                'kb.nama_kategori_bandwith as nama_kategori_bandwith_baru',
                'bb.nominal_bandwith as nominal_bandwith_baru',
                'bb.harga_bandwith as harga_bandwith_baru'
            )
            ->where('u.nomor_internet', $nomorInternet)
            ->where(function ($q) {
                $q->where('u.hide', '0')->orWhereNull('u.hide');
            })
            ->orderByDesc('u.date_create')
            ->get();

        $suspends = DB::table('trx_suspend as s')
            ->leftJoin('m_status_suspend as ms', 'ms.status_suspend', '=', 's.status_suspend')
            ->select('s.*', 'ms.desc_status_suspend')
            ->where('s.nomor_internet', $nomorInternet)
            ->orderByDesc('s.date_create')
            ->get();

        $billings = DB::table('trx_billing_layanan')
            ->where('nomor_internet', $nomorInternet)
            ->orderByDesc('date_create')
            ->get();

        $pengaduan = DB::table('trx_tiket_gangguan')
            ->where('nomor_internet', $nomorInternet)
            ->orderByDesc('date_create')
            ->get();

        $perangkat = DB::table('trx_instalasi_barang as ib')
            ->leftJoin('m_barang as b', 'b.kode_barang', '=', 'ib.kode_barang')
            ->select('ib.*', 'b.nama_barang', 'b.tipe_barang')
            ->where('ib.nomor_internet', $nomorInternet)
            ->get();

        return view('pelanggan.detail', compact('customer', 'logs', 'ubahLayanan', 'suspends', 'billings', 'pengaduan', 'perangkat'));
    }

    public function downloadPelangganPdf($nomorInternet)
    {
        if (!session()->has('user')) {
            abort(401, 'Silakan login terlebih dahulu untuk mengunduh dokumen.');
        }

        $customer = DB::table('view_batchjob')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        if (!$customer) {
            return redirect()->route('pelanggan')->withErrors(['error' => 'Pelanggan tidak ditemukan.']);
        }

        $customer = $this->decorate($customer);

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        $targetId = $customer->id_perusahaan ?? $customer->nik_penduduk ?? ($reg ? ($reg->id_perusahaan ?? $reg->nik_penduduk) : null);
        $pelanggan = $targetId ? DB::table('m_pelanggan')->where('id_perusahaan', $targetId)->first() : null;

        $customer->scan_dokumen = $reg->scan_dokumen ?? $customer->scan_dokumen ?? null;
        $customer->scan_dokumen_survey = $reg->scan_dokumen_survey ?? $customer->scan_dokumen_survey ?? null;
        $customer->scan_dokumen_instalasi = $reg->scan_dokumen_instalasi ?? $customer->scan_dokumen_instalasi ?? null;
        $customer->scan_dokumen_aktivasi = $reg->scan_dokumen_aktivasi ?? $customer->scan_dokumen_aktivasi ?? null;

        // Seksi 1: Informasi Pelanggan
        $customer->nama_perusahaan = $pelanggan->nama_perusahaan ?? $reg->nama_pelanggan ?? $customer->nama_perusahaan ?? $customer->nama_pelanggan ?? $customer->nama_penduduk ?? null;
        $customer->no_telp_perusahaan = $pelanggan->no_telp_perusahaan ?? $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? null;
        $customer->email_perusahaan = $pelanggan->email_perusahaan ?? $customer->email_perusahaan ?? $customer->email ?? null;
        $customer->id_perusahaan = $pelanggan->id_perusahaan ?? $reg->id_perusahaan ?? $customer->id_perusahaan ?? $customer->nik_penduduk ?? null;
        $customer->jenis_perusahaan = $pelanggan->jenis_perusahaan ?? $customer->jenis_perusahaan ?? 'PT';
        $customer->tanggal_registrasi = $pelanggan->tanggal_registrasi ?? $customer->tanggal_registrasi ?? (isset($customer->date_create) ? substr($customer->date_create, 0, 10) : null);

        // PIC Teknis & PIC Keuangan
        $customer->nama_pic_teknis = $pelanggan->nama_pic_teknis ?? $customer->nama_pic_teknis ?? $customer->pic ?? null;
        $customer->no_telp_pic_teknis = $pelanggan->no_telp_pic_teknis ?? $customer->no_telp_pic_teknis ?? $customer->nomor_hp_2 ?? $customer->nomor_hp ?? null;
        $customer->email_pic_teknis = $pelanggan->email_pic_teknis ?? $customer->email_pic_teknis ?? $customer->email ?? null;

        $customer->nama_pic_keuangan = $pelanggan->nama_pic_keuangan ?? $customer->nama_pic_keuangan ?? $customer->pic ?? null;
        $customer->no_telp_pic_keuangan = $pelanggan->no_telp_pic_keuangan ?? $customer->no_telp_pic_keuangan ?? $customer->nomor_hp ?? null;
        $customer->email_pic_keuangan = $pelanggan->email_pic_keuangan ?? $customer->email_pic_keuangan ?? $customer->email ?? null;

        // Seksi 2: Alamat Perusahaan
        $customer->rt_ktp = $reg->rt_perusahaan ?? $pelanggan->rt_ktp ?? $customer->rt_perusahaan ?? $customer->rt_ktp ?? $customer->rt_pasang ?? null;
        $customer->rw_ktp = $reg->rw_perusahaan ?? $pelanggan->rw_ktp ?? $customer->rw_perusahaan ?? $customer->rw_ktp ?? $customer->rw_pasang ?? null;
        $customer->nomor_bangunan_perusahaan = $reg->nomor_bangunan_perusahaan ?? $customer->nomor_bangunan_perusahaan ?? $customer->nomor_bangunan ?? null;
        $customer->alamat_ktp = $reg->detail_alamat_perusahaan ?? $pelanggan->alamat_ktp ?? $customer->detail_alamat_perusahaan ?? $customer->alamat_perusahaan ?? $customer->alamat_ktp ?? $customer->alamat_k ?? null;
        $customer->lon_lat_perusahaan = $reg->lon_lat_perusahaan ?? $pelanggan->lon_lat_perusahaan ?? $customer->lon_lat_perusahaan ?? null;
        $customer->sharelock_perusahaan = $reg->sharelock_perusahaan ?? $pelanggan->sharelock_perusahaan ?? $customer->sharelock_perusahaan ?? null;
        if (empty($customer->sharelock_perusahaan) && !empty($customer->lon_lat_perusahaan)) {
            $customer->sharelock_perusahaan = 'https://www.google.com/maps?q=' . urlencode(trim($customer->lon_lat_perusahaan));
        }

        $kodeKelCorp = $reg->kode_wilayah_kelurahan_perusahaan ?? $pelanggan->kode_wilayah_kelurahan_ktp ?? $customer->kode_wilayah_kelurahan_perusahaan ?? $customer->kode_wilayah_kelurahan_ktp ?? $customer->kode_wilayah_kelurahan_pasang ?? null;
        if ($kodeKelCorp) {
            $wilCorp = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelCorp)->first();
            if ($wilCorp) {
                $customer->nama_kelurahan_corp = $wilCorp->nama_kelurahan;
                $customer->nama_kecamatan_corp = $wilCorp->nama_kecamatan;
                $customer->nama_kota_corp = $wilCorp->nama_kota;
                $customer->nama_provinsi_corp = $wilCorp->nama_provinsi;
            }
        }

        // Seksi 3: Alamat Pemasangan
        $customer->rt_pasang = $reg->rt_pasang ?? $customer->rt_pasang ?? null;
        $customer->rw_pasang = $reg->rw_pasang ?? $customer->rw_pasang ?? null;
        $customer->nomor_bangunan = $reg->nomor_bangunan ?? $customer->nomor_bangunan ?? null;
        $customer->alamat_pasang = $reg->alamat_pasang ?? $customer->alamat_pasang ?? $customer->alamat_p ?? null;
        $customer->jenis_bangunan = $reg->jenis_bangunan ?? $customer->jenis_bangunan ?? null;
        $customer->note_request = $reg->note_request ?? $customer->note_request ?? null;
        $customer->lon_lat_pasang = $reg->lon_lat ?? $customer->lon_lat ?? null;
        $customer->sharelock_pasang = $reg->loc_maps ?? $customer->loc_maps ?? null;
        if (empty($customer->sharelock_pasang) && !empty($customer->lon_lat_pasang)) {
            $customer->sharelock_pasang = 'https://www.google.com/maps?q=' . urlencode(trim($customer->lon_lat_pasang));
        }

        $kodeKelPasang = $reg->kode_wilayah_kelurahan_pasang ?? $customer->kode_wilayah_kelurahan_pasang ?? $kodeKelCorp;
        if ($kodeKelPasang) {
            $wilPasang = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelPasang)->first();
            if ($wilPasang) {
                $customer->nama_kelurahan_pasang = $wilPasang->nama_kelurahan;
                $customer->nama_kecamatan_pasang = $wilPasang->nama_kecamatan;
                $customer->nama_kota_pasang = $wilPasang->nama_kota;
                $customer->nama_provinsi_pasang = $wilPasang->nama_provinsi;
            }
        }

        // Seksi 4 & 5: Paket, Penugasan & Financial
        $customer->group_layanan = $reg->group_layanan ?? $customer->group_layanan ?? null;
        $customer->nama_sales = $reg->nama_sales ?? $customer->nama_sales ?? null;
        $customer->lon_lat = $customer->lon_lat_pasang;
        $customer->loc_maps = $customer->sharelock_pasang;

        $billReg = DB::table('trx_billing_registrasi')->where('nomor_internet', $nomorInternet)->first();
        $customer->harga_paket = $billReg->total_reg ?? $customer->harga_bandwith ?? $reg->total_registrasi ?? null;
        $customer->biaya_reg = $billReg->biaya_registrasi ?? $reg->biaya_reg ?? $customer->biaya_reg ?? null;

        // --- DATA LOG / RIWAYAT AKTIVITAS ---
        $logs = collect();

        $dbLogs = DB::table('trx_batchjob_register_log as l')
            ->leftJoin('m_status_registrasi as s', 's.status_reg', '=', 'l.status_reg')
            ->select('l.*', 's.desc_registrasi')
            ->where('l.nomor_internet', $nomorInternet)
            ->orderByDesc('l.date_create')
            ->get();

        foreach ($dbLogs as $dl) {
            $logs->push((object)[
                'status' => $dl->desc_registrasi ?? 'Pembaruan Status (' . $dl->status_reg . ')',
                'keterangan' => $dl->note_schedule ?: 'Log aktivitas registrasi',
                'tanggal' => $dl->date_schedule ?: $dl->date_create,
                'user' => $dl->user_create ?: 'System',
                'badge' => 'bg-blue-100 text-blue-800'
            ]);
        }

        if (!empty($customer->aktivasi_date_start) || !empty($customer->aktivasi_date_finish)) {
            $logs->push((object)[
                'status' => !empty($customer->aktivasi_date_finish) ? 'Aktivasi Selesai' : 'Proses Aktivasi',
                'keterangan' => trim(($customer->aktivasi_team ? 'Tim: ' . $customer->aktivasi_team . '. ' : '') . ($customer->aktivasi_note ?: $customer->aktivasi_note_finish ?: 'Proses aktivasi layanan')),
                'tanggal' => $customer->aktivasi_date_finish ?: $customer->aktivasi_date_start,
                'user' => $customer->user_update ?: $customer->user_create ?: 'System',
                'badge' => 'bg-emerald-100 text-emerald-800'
            ]);
        }

        if (!empty($customer->instalasi_date_start) || !empty($customer->instalasi_date_finish)) {
            $logs->push((object)[
                'status' => !empty($customer->instalasi_date_finish) ? 'Selesai Instalasi' : 'Jadwal Instalasi Terbit',
                'keterangan' => trim(($customer->instalasi_team ? 'Tim: ' . $customer->instalasi_team . '. ' : '') . ($customer->instalasi_note ?: $customer->instalasi_note_finish ?: 'Pemasangan perangkat & jaringan')),
                'tanggal' => $customer->instalasi_date_finish ?: $customer->instalasi_date_start,
                'user' => $customer->user_update ?: $customer->user_create ?: 'System',
                'badge' => 'bg-indigo-100 text-indigo-800'
            ]);
        }

        if (!empty($customer->survey_date_start) || !empty($customer->survey_date_finish)) {
            $logs->push((object)[
                'status' => !empty($customer->survey_date_finish) ? 'Selesai Survey' : 'Jadwal Survey Terbit',
                'keterangan' => trim(($customer->survey_team ? 'Tim: ' . $customer->survey_team . '. ' : '') . ($customer->survey_note ?: $customer->survey_note_finish ?: 'Pemeriksaan lokasi dan jalur FO')),
                'tanggal' => $customer->survey_date_finish ?: $customer->survey_date_start,
                'user' => $customer->user_create ?: 'System',
                'badge' => 'bg-amber-100 text-amber-800'
            ]);
        }

        $logs->push((object)[
            'status' => 'Pendaftaran Registrasi (' . ($customer->desc_registrasi ?? 'Baru') . ')',
            'keterangan' => $customer->note_request ?: 'Registrasi pendaftaran baru dibuat',
            'tanggal' => $customer->date_create,
            'user' => $customer->user_create ?: 'System',
            'badge' => 'bg-cyan-100 text-cyan-800'
        ]);

        $logs = $logs->sortByDesc('tanggal')->values();

        $suspends = DB::table('trx_suspend as s')
            ->leftJoin('m_status_suspend as ms', 'ms.status_suspend', '=', 's.status_suspend')
            ->select('s.*', 'ms.desc_status_suspend')
            ->where('s.nomor_internet', $nomorInternet)
            ->orderByDesc('s.date_create')
            ->get();

        $billings = DB::table('trx_billing_layanan')
            ->where('nomor_internet', $nomorInternet)
            ->orderByDesc('date_create')
            ->get();

        $pengaduan = DB::table('trx_tiket_gangguan')
            ->where('nomor_internet', $nomorInternet)
            ->orderByDesc('date_create')
            ->get();

        $perangkat = DB::table('trx_instalasi_barang as ib')
            ->leftJoin('m_barang as b', 'b.kode_barang', '=', 'ib.kode_barang')
            ->select('ib.*', 'b.nama_barang', 'b.tipe_barang')
            ->where('ib.nomor_internet', $nomorInternet)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelanggan-detail', compact('customer', 'logs', 'suspends', 'billings', 'pengaduan', 'perangkat'));
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Profil_Pelanggan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nomorInternet) . '.pdf';
        return $pdf->download($fileName);
    }

    public function downloadSurveyPdf($nomorInternet)
    {
        if (!session()->has('user')) {
            abort(401, 'Silakan login terlebih dahulu untuk mengunduh dokumen.');
        }

        $customer = DB::table('view_batchjob')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        if (!$customer) {
            return redirect()->route('pelanggan')->withErrors(['error' => 'Pelanggan tidak ditemukan.']);
        }

        $customer = $this->decorate($customer);

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        $targetId = $customer->id_perusahaan ?? $customer->nik_penduduk ?? ($reg ? ($reg->id_perusahaan ?? $reg->nik_penduduk) : null);
        $pelanggan = $targetId ? DB::table('m_pelanggan')->where('id_perusahaan', $targetId)->first() : null;
        $instData = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();

        // 1. Tanggal Survey
        $surveyDate = $instData->survey_date_start ?? $customer->survey_date_start ?? $customer->date_create ?? now();
        $surveyDateObj = \Carbon\Carbon::parse($surveyDate);
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $surveyDateFormatted = $surveyDateObj->format('d') . ' ' . ($bulanIndo[(int)$surveyDateObj->format('m')] ?? $surveyDateObj->format('F')) . ' ' . $surveyDateObj->format('Y');
        $surveyMonth = $surveyDateObj->format('m');
        $surveyYear = $surveyDateObj->format('Y');

        // Tim teknisi survey
        $surveyTeam = $instData->survey_team ?? $customer->survey_team ?? 'TIM TEKNISI';

        // 2. Data Pelanggan
        $customerName = $pelanggan->nama_perusahaan ?? $reg->nama_pelanggan ?? $customer->nama_pelanggan ?? $customer->nama_penduduk ?? 'TEST';

        // Alamat Pasang Lengkap
        $kodeKel = $reg->kode_wilayah_kelurahan_pasang ?? $customer->kode_wilayah_kelurahan_pasang ?? null;
        $namaKel = $customer->nama_kelurahan_pasang ?? null;
        $namaKec = $customer->nama_kecamatan_pasang ?? null;
        $namaKot = $customer->nama_kota_pasang ?? null;
        $namaPro = $customer->nama_provinsi_pasang ?? null;

        if ($kodeKel && (empty($namaKel) || empty($namaKec))) {
            $wil = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKel)->first();
            if ($wil) {
                $namaKel = $wil->nama_kelurahan;
                $namaKec = $wil->nama_kecamatan;
                $namaKot = $wil->nama_kota;
                $namaPro = $wil->nama_provinsi;
            }
        }

        $alamatParts = array_filter([
            $reg->alamat_pasang ?? $customer->alamat_pasang ?? $customer->alamat_p ?? null,
            !empty($reg->nomor_bangunan ?? $customer->nomor_bangunan) ? 'NO. ' . ($reg->nomor_bangunan ?? $customer->nomor_bangunan) : null,
            (!empty($reg->rt_pasang ?? $customer->rt_pasang) || !empty($reg->rw_pasang ?? $customer->rw_pasang)) ? 'RT' . ($reg->rt_pasang ?? $customer->rt_pasang ?: '00') . '/RW' . ($reg->rw_pasang ?? $customer->rw_pasang ?: '00') : null,
            !empty($namaKel) ? 'KEL. ' . $namaKel : null,
            !empty($namaKec) ? 'KEC. ' . $namaKec : null,
            !empty($namaKot) ? $namaKot : null,
            !empty($namaPro) ? $namaPro : null,
        ]);
        $installationAddress = !empty($alamatParts) ? implode(', ', $alamatParts) : ($customer->alamat_pasang ?: ($customer->alamat_p ?: '-'));

        // PIC, Telepon, Layanan, Detail Pekerjaan
        $picName = $pelanggan->nama_pic_teknis ?? $customer->nama_pic_teknis ?? $customer->pic ?? $reg->pic ?? '';
        $phoneNumber = $pelanggan->no_telp_perusahaan ?? $customer->no_telp_perusahaan ?? $customer->nomor_hp ?? $reg->nomor_hp ?? '-';

        $serviceBandwidth = trim(($customer->nama_kategori_bandwith ?? '') . ($customer->nominal_bandwith ? ', ' . $customer->nominal_bandwith . ' Mbps' : ''));
        $serviceName = !empty($serviceBandwidth) ? $serviceBandwidth : ($customer->paket ?? '-');

        $jobDetails = $instData->survey_note ?? $customer->survey_note ?? $reg->note_request ?? '-';

        // Penanggung Jawab
        $personInCharge = 'IPIN ARIPIN';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat-tugas-survey', compact(
            'customer',
            'surveyDateFormatted',
            'surveyMonth',
            'surveyYear',
            'surveyTeam',
            'customerName',
            'installationAddress',
            'picName',
            'phoneNumber',
            'serviceName',
            'jobDetails',
            'personInCharge'
        ));
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Surat_Tugas_Survey_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nomorInternet) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function uploadScanDokumen(Request $request, $nomorInternet)
    {
        $request->validate([
            'scan_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tipe_dokumen' => 'nullable|string|in:berlangganan,survey,instalasi,aktivasi',
        ], [
            'scan_dokumen.required' => 'File scan dokumen wajib dipilih.',
            'scan_dokumen.mimes' => 'Format file yang diperbolehkan hanya PDF, JPG, JPEG, atau PNG.',
            'scan_dokumen.max' => 'Ukuran file tidak boleh melebihi 10MB.',
        ]);

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        if (!$reg) {
            return redirect()->back()->withErrors(['scan_dokumen' => 'Data pendaftaran pelanggan tidak ditemukan.']);
        }

        $tipe = $request->input('tipe_dokumen', 'berlangganan');
        $columnMap = [
            'berlangganan' => 'scan_dokumen',
            'survey' => 'scan_dokumen_survey',
            'instalasi' => 'scan_dokumen_instalasi',
            'aktivasi' => 'scan_dokumen_aktivasi',
        ];
        $targetColumn = $columnMap[$tipe] ?? 'scan_dokumen';

        $file = $request->file('scan_dokumen');
        $fileName = 'scan_' . $tipe . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nomorInternet) . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $destinationPath = public_path('storage/scan_dokumen');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $fileName);
        $savedPath = 'storage/scan_dokumen/' . $fileName;

        // Hapus file lama jika ada
        if (!empty($reg->{$targetColumn})) {
            $oldFile = public_path($reg->{$targetColumn});
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        DB::table('trx_batchjob_register')
            ->where('nomor_internet', $nomorInternet)
            ->update([
                $targetColumn => $savedPath,
                'user_update' => session('user.username', 'SYSTEM'),
                'date_update' => now(),
            ]);

        $tipeLabel = ucfirst($tipe);
        return redirect()->back()->with('success', "Scan dokumen {$tipeLabel} bertanda tangan berhasil diunggah!");
    }

    public function deleteScanDokumen(Request $request, $nomorInternet)
    {
        $tipe = $request->input('tipe_dokumen', 'berlangganan');
        $columnMap = [
            'berlangganan' => 'scan_dokumen',
            'survey' => 'scan_dokumen_survey',
            'instalasi' => 'scan_dokumen_instalasi',
            'aktivasi' => 'scan_dokumen_aktivasi',
        ];
        $targetColumn = $columnMap[$tipe] ?? 'scan_dokumen';

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        if ($reg && !empty($reg->{$targetColumn})) {
            $filePath = public_path($reg->{$targetColumn});
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    $targetColumn => null,
                    'user_update' => session('user.username', 'SYSTEM'),
                    'date_update' => now(),
                ]);
        }

        $tipeLabel = ucfirst($tipe);
        return redirect()->back()->with('success', "File scan dokumen {$tipeLabel} berhasil dihapus.");
    }

    /* ---------------------- helper ---------------------- */

    private function monthCount(string $table, string $col, int $y, int $m): int
    {
        return DB::table($table)->whereYear($col, $y)->whereMonth($col, $m)->count();
    }

    /** now vs prev month + arah trend (dihitung, bukan dummy) */
    private function cardStat(string $table, string $col): array
    {
        $now  = $this->monthCount($table, $col, now()->year, now()->month);
        $prev = $this->monthCount($table, $col, now()->copy()->subMonth()->year, now()->copy()->subMonth()->month);
        $trend = $prev > 0 ? (int) round(($now - $prev) / $prev * 100) : ($now > 0 ? 100 : 0);
        $dir = $now > $prev ? 'up' : ($now < $prev ? 'down' : 'flat');
        return ['now' => $now, 'prev' => $prev, 'trend' => $trend, 'dir' => $dir];
    }

    /** Tambah nama_display & paket ke baris view_batchjob / join gangguan */
    private function decorate($r)
    {
        $jk = $r->jenis_kelamin == 1 ? '(L)' : ($r->jenis_kelamin == 2 ? '(P)' : '');
        $nama = $r->nama_penduduk ?? $r->nama_pelanggan ?? null;
        $r->nama_display = trim(($nama ?? '') . ' ' . $jk);
        $r->paket = trim(preg_replace('/\s+/', ' ', ($r->nama_kategori_bandwith ?? '') . ' ' . ($r->nominal_bandwith ?? '') . ' Mbps'));
        return $r;
    }

    /**
     * Klasifikasi baris ke seksi papan Pelanggan.
     * ASUMSI (koreksi di sini bila konvensi Anda beda): flag aktif = 1/Y/01/true/aktif;
     * prioritas suspend > terminasi > (desc_registrasi mengandung "GAGAL") > aktif.
     */
    private function sectionOf($r): string
    {
        if ($this->flagOn($r->is_suspend ?? null)) return 'suspend';
        if ($this->flagOn($r->is_termin ?? null))  return 'terminasi';
        if (str_contains(strtoupper((string) ($r->desc_registrasi ?? '')), 'GAGAL')) return 'gagal';

        // Syarat Pelanggan Aktif: Wajib SUDAH SELESAI PROSES AKTIVASI di NOC
        $isAktivasiDone = !empty($r->aktivasi_date_finish) || (string)($r->status_reg ?? '') === '16' || str_contains(strtoupper((string)($r->desc_registrasi ?? '')), 'SELESAI AKTIVASI');
        if ($isAktivasiDone) {
            return 'aktif';
        }
        return 'pending';
    }

    private function flagOn($v): bool
    {
        return in_array(strtolower(trim((string) $v)), ['1', 'y', 'ya', '01', 'true', 'aktif', 'yes'], true);
    }

    /**
     * API: Ambil semua data komprehensif untuk 4 Modal Pelanggan (Terminasi, Up/Downgrade, Suspend, Adjust)
     */
    public function getPelangganModalData($nomorInternet)
    {
        $row = DB::table('view_batchjob')->where('nomor_internet', $nomorInternet)->first();
        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();

        if (!$row && !$reg) {
            return response()->json(['success' => false, 'message' => 'Data pelanggan tidak ditemukan.'], 404);
        }

        $namaPelanggan = $row->nama_penduduk ?? $row->nama_pelanggan ?? $reg->nama_pelanggan ?? 'Pelanggan';
        $currentPack = trim(($row->nama_kategori_bandwith ?? '') . ' ' . ($row->nominal_bandwith ?? '') . ' Mbps');
        if (empty(trim($currentPack)) || $currentPack === 'Mbps') {
            $bw = DB::table('m_bandwith as b')
                ->leftJoin('m_bandwith_kategori as k', 'k.kode_kategori_bandwith', '=', 'b.kode_kategori_bandwith')
                ->where('b.kode_bandwith', $reg->kode_bandwith ?? '')
                ->first();
            $currentPack = $bw ? trim(($bw->nama_kategori_bandwith ?? '') . ' ' . ($bw->nominal_bandwith ?? '') . ' Mbps') : 'UP TO NEW 15 Mbps';
        }

        // 1. Riwayat Pending Tagihan (Terminasi)
        $pendingBills = DB::table('trx_billing_layanan as b')
            ->leftJoin('m_status_bill_lay as s', 's.status_bill_lay', '=', 'b.status_bill_lay')
            ->where('b.nomor_internet', $nomorInternet)
            ->where('b.status_bill_lay', '!=', '15')
            ->select('b.bulan_tagihan', 'b.tahun_tagihan', 'b.total_layanan', 's.desc_bill_lay', 'b.status_bill_lay')
            ->orderByDesc('b.tahun_tagihan')
            ->orderByDesc('b.bulan_tagihan')
            ->get()
            ->map(function ($b) {
                return [
                    'periode' => sprintf('%02d', (int)$b->bulan_tagihan) . '/' . ($b->tahun_tagihan ?? date('Y')),
                    'jumlah'  => 'Rp ' . number_format((float) ($b->total_layanan ?? 0), 2, ',', '.'),
                    'status'  => $b->desc_bill_lay ?: ($b->status_bill_lay == '13' ? 'Publish Billing' : 'Unpaid'),
                ];
            });

        // 2. Perangkat On Site (Terminasi)
        $devices = DB::table('trx_instalasi_barang as ib')
            ->leftJoin('m_barang as b', 'b.kode_barang', '=', 'ib.kode_barang')
            ->where('ib.nomor_internet', $nomorInternet)
            ->select('b.nama_barang', 'b.tipe_barang', 'ib.jumlah_barang', 'ib.kode_barang')
            ->get()
            ->map(function ($d) {
                return [
                    'nama'   => $d->nama_barang ?: 'ONU',
                    'sub'    => $d->kode_barang . ($d->tipe_barang ? ', ' . $d->tipe_barang : ''),
                    'jumlah' => ($d->jumlah_barang ?: 1) . ' UNIT',
                    'status' => 'Aktif',
                ];
            });

        if ($devices->isEmpty()) {
            $devices = collect([
                [
                    'nama'   => 'ONU',
                    'sub'    => ($row->ont_us ?? 'BR013') . ', ' . ($row->ont_ps ?? 'ZTE F660'),
                    'jumlah' => '1 UNIT',
                    'status' => 'Aktif',
                ]
            ]);
        }

        // 3. Riwayat Perubahan Layanan (Up/Downgrade)
        $riwayatUbah = DB::table('view_ubah_layanan')
            ->where('nomor_internet', $nomorInternet)
            ->orderByDesc('date_create')
            ->get()
            ->map(function ($u) {
                return [
                    'old'    => trim(($u->nama_kategori_bandwith_lama ?? '') . ' ' . ($u->nominal_bandwith_lama ?? '') . ' Mbps'),
                    'new'    => trim(($u->nama_kategori_bandwith_baru ?? '') . ' ' . ($u->nominal_bandwith_baru ?? '') . ' Mbps'),
                    'status' => $u->desc_ubah_layanan ?: ($u->status_ubah_layanan ?? 'Proses'),
                ];
            });

        // 4. Riwayat Pembayaran (Suspend)
        $riwayatBayar = DB::table('trx_billing_layanan as b')
            ->leftJoin('m_status_bill_lay as s', 's.status_bill_lay', '=', 'b.status_bill_lay')
            ->where('b.nomor_internet', $nomorInternet)
            ->orderByDesc('b.tahun_tagihan')
            ->orderByDesc('b.bulan_tagihan')
            ->get()
            ->map(function ($b) {
                return [
                    'bulan'  => sprintf('%02d', (int)$b->bulan_tagihan) . '/' . ($b->tahun_tagihan ?? date('Y')),
                    'biaya'  => 'Rp ' . number_format((float) ($b->total_layanan ?? 0), 2, ',', '.'),
                    'status' => $b->desc_bill_lay ?: ($b->status_bill_lay == '15' ? 'Paid' : 'Publish Billing'),
                ];
            });

        // Master lists for Up/Downgrade dropdowns
        $layananList = DB::table('m_bandwith_kategori')
            ->where('hide', '0')
            ->orderBy('nama_kategori_bandwith')
            ->get(['kode_kategori_bandwith', 'nama_kategori_bandwith']);

        $paketList = DB::table('m_bandwith as b')
            ->leftJoin('m_bandwith_kategori as k', 'k.kode_kategori_bandwith', '=', 'b.kode_kategori_bandwith')
            ->where('b.hide', '0')
            ->where('b.disable', '0')
            ->orderBy('b.nominal_bandwith')
            ->get([
                'b.kode_bandwith', 
                'b.kode_kategori_bandwith', 
                'k.nama_kategori_bandwith',
                'b.nominal_bandwith', 
                'b.harga_bandwith'
            ]);

        // Data Penyesuaian (Adjust)
        $tagihanBulanan = (float)($row->harga_bandwith ?? $row->harga_paket ?? 185000);
        $periodeBilling = $reg->periode_billing ?? 1;
        $ppnVal = (int)($reg->ppn ?? 0);
        $statusPpn = ($ppnVal > 0) ? '1' : '0';
        $isSuspend = ($reg->is_suspend ?? '0') == '1' ? '1' : '0';
        $isDenda = ($reg->is_denda ?? '0') == '1' ? '1' : '0';
        $periodeTerminasi = $reg->is_termin ?? '0';

        return response()->json([
            'success' => true,
            'data'    => [
                'nomor_internet'       => $nomorInternet,
                'nama_pelanggan'       => strtoupper($namaPelanggan),
                'current_pack'         => $currentPack,
                'jenis_bangunan'       => strtoupper($row->jenis_bangunan ?? $reg->jenis_bangunan ?? 'RUMAH-PRIBADI'),
                'pending_bills'        => $pendingBills,
                'devices'              => $devices,
                'riwayat_ubah'         => $riwayatUbah,
                'riwayat_bayar'        => $riwayatBayar,
                'layanan_list'         => $layananList,
                'paket_list'           => $paketList,
                'adjust'               => [
                    'tagihan_bulanan'   => $tagihanBulanan,
                    'range_tagihan'     => $periodeBilling,
                    'ppn'               => $ppnVal,
                    'status_ppn'        => $statusPpn,
                    'is_suspend'        => $isSuspend,
                    'is_denda'          => $isDenda,
                    'periode_terminasi' => $periodeTerminasi,
                ]
            ]
        ]);
    }

    /**
     * Submit Request Terminasi dari Modal Pelanggan
     */
    public function postRequestTerminasi(Request $request)
    {
        $validated = $request->validate([
            'nomor_internet' => 'required|string',
            'note_termin'    => 'required|string',
        ], [
            'note_termin.required' => 'Alasan terminasi wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $nomorInternet = $validated['nomor_internet'];
            $currentUser = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
            $kodeTrx = 'TERM-' . $nomorInternet . '-' . date('ymdHis');

            // Pastikan master status terminasi 11 ada
            DB::table('m_status_terminasi')->updateOrInsert(
                ['status_terminasi' => '11'],
                ['desc_terminasi' => 'Req. Terminasi', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            // Insert ke trx_terminasi
            DB::table('trx_terminasi')->insert([
                'kode_trx_terminasi' => $kodeTrx,
                'nomor_internet'     => $nomorInternet,
                'note_termin'        => $validated['note_termin'],
                'status_terminasi'   => '11', // Req. Terminasi
                'date_create'        => now(),
                'user_create'        => substr($currentUser, 0, 50),
                'date_update'        => now(),
                'user_update'        => substr($currentUser, 0, 50),
                'hide'               => '0',
            ]);

            // Insert ke trx_terminasi_log
            DB::table('trx_terminasi_log')->insert([
                'kode_log_terminasi' => 'L-TERM-' . $kodeTrx . '-' . date('ymdHis'),
                'kode_trx_terminasi' => $kodeTrx,
                'note_schedule'      => 'Req. Terminasi: ' . $validated['note_termin'],
                'status_terminasi'   => '11',
                'user_create'        => substr($currentUser, 0, 50),
                'date_create'        => now(),
                'hide'               => '0',
            ]);

            // Update status flag di register
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'is_termin'   => '1',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request terminasi untuk {$nomorInternet} berhasil diajukan."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses request terminasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit Request Up/Downgrade dari Modal Pelanggan
     */
    public function postRequestUpDowngrade(Request $request)
    {
        $validated = $request->validate([
            'nomor_internet'     => 'required|string',
            'kode_kategori'      => 'nullable|string|max:100',
            'kode_bandwith_baru' => 'required|string|max:100',
            'harga_paket'        => 'nullable|string|max:100',
            'nama_layanan_baru'  => 'nullable|string|max:100',
        ], [
            'kode_bandwith_baru.required' => 'Kapasitas layanan wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $nomorInternet = $validated['nomor_internet'];
            $currentUser = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
            $kodeTrx = 'UBAH-' . $nomorInternet . '-' . date('ymdHis');

            // Ambil paket lama
            $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
            $kodeBandwithLama = $reg->kode_bandwith ?? null;

            // Handle parsing harga paket
            $rawHarga = $request->input('harga_paket');
            $parsedHarga = !empty($rawHarga) ? preg_replace('/[^0-9]/', '', $rawHarga) : null;

            // Cari jika input teks cocok dengan kode_bandwith di m_bandwith
            $bandwithBaruInput = trim(substr($validated['kode_bandwith_baru'], 0, 50));
            $kategoriInput = trim($request->input('kode_kategori', ''));

            // Match m_bandwith
            $bwMatch = DB::table('m_bandwith')->where('kode_bandwith', $bandwithBaruInput)->first();
            if (!$bwMatch) {
                // Check if matches nominal_bandwith
                $nominalDigits = preg_replace('/[^0-9]/', '', $bandwithBaruInput);
                $bwMatch = DB::table('m_bandwith')
                    ->where('nominal_bandwith', $bandwithBaruInput)
                    ->orWhere('nominal_bandwith', $nominalDigits)
                    ->first();
            }

            if ($bwMatch) {
                $finalKodeBaru = $bwMatch->kode_bandwith;
            } else {
                // Buat atau sesuaikan kode_bandwith jika custom
                $kategoriDefault = DB::table('m_bandwith_kategori')
                    ->where('nama_kategori_bandwith', $kategoriInput)
                    ->value('kode_kategori_bandwith') ?? 'KB09212';

                $nominalDigits = preg_replace('/[^0-9]/', '', $bandwithBaruInput);
                $nominalStr = !empty($nominalDigits) ? substr($nominalDigits, 0, 5) : '10';

                $newKodeBw = 'CUST-' . strtoupper(Str::slug(substr($bandwithBaruInput, 0, 15), ''));
                if (strlen($newKodeBw) > 50) $newKodeBw = substr($newKodeBw, 0, 50);

                $checkBw = DB::table('m_bandwith')->where('kode_bandwith', $newKodeBw)->first();
                if (!$checkBw) {
                    DB::table('m_bandwith')->insert([
                        'kode_bandwith'          => $newKodeBw,
                        'nominal_bandwith'       => $nominalStr,
                        'harga_bandwith'         => substr((string)($parsedHarga ?: '300000'), 0, 15),
                        'kode_kategori_bandwith' => $kategoriDefault,
                        'user_create'            => substr($currentUser, 0, 20),
                        'date_create'            => now(),
                        'hide'                   => '0'
                    ]);
                }
                $finalKodeBaru = $newKodeBw;
            }

            // Pastikan master status ubahlayanan 11 ada
            DB::table('m_status_ubahlayanan')->updateOrInsert(
                ['status_ubah_layanan' => '11'],
                ['desc_ubah_layanan' => 'Req. Ubah Layanan', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            // Insert ke trx_ubah_layanan
            DB::table('trx_ubah_layanan')->insert([
                'kode_trx_ubah_layanan' => $kodeTrx,
                'nomor_internet'        => $nomorInternet,
                'kode_bandwith_lama'    => $kodeBandwithLama,
                'kode_bandwith_baru'    => $finalKodeBaru,
                'status_ubah_layanan'   => '11', // Req. Ubah Layanan
                'date_request'          => now()->toDateString(),
                'date_create'           => now(),
                'user_create'           => substr($currentUser, 0, 50),
                'date_update'           => now(),
                'user_update'           => substr($currentUser, 0, 50),
                'hide'                  => '0',
            ]);

            $layananBaruStr = $request->input('kode_kategori') ?: $request->input('nama_layanan_baru');
            $hargaStr = $parsedHarga ? ' (Rp ' . number_format((float)$parsedHarga, 0, ',', '.') . ')' : '';
            $noteText = 'Request Ubah Layanan' . ($layananBaruStr ? ' [' . $layananBaruStr . ']' : '') . ': ' . $validated['kode_bandwith_baru'] . $hargaStr;

            // Insert ke trx_ubah_layanan_log
            DB::table('trx_ubah_layanan_log')->insert([
                'kode_ubah_layanan_log' => 'L-UBAH-' . $kodeTrx . '-' . date('ymdHis'),
                'kode_trx_ubah_layanan' => $kodeTrx,
                'status_ubah_layanan'   => '11',
                'note_ubah_layanan'     => substr($noteText, 0, 255),
                'user_create'           => substr($currentUser, 0, 50),
                'date_create'           => now(),
                'hide'                  => '0',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request Up/Downgrade untuk {$nomorInternet} berhasil diajukan."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses request up/downgrade: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit Request Suspend dari Modal Pelanggan
     */
    public function postRequestSuspend(Request $request)
    {
        $validated = $request->validate([
            'nomor_internet' => 'required|string',
            'note_suspend'   => 'required|string',
        ], [
            'note_suspend.required' => 'Catatan suspend wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $nomorInternet = $validated['nomor_internet'];
            $currentUser = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
            $kodeTrx = 'SUSP-' . $nomorInternet . '-' . date('ymdHis');

            // Pastikan master status suspend 11 ada
            DB::table('m_status_suspend')->updateOrInsert(
                ['status_suspend' => '11'],
                ['desc_status_suspend' => 'Req. Suspend', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            // Insert ke trx_suspend
            DB::table('trx_suspend')->insert([
                'kode_suspend'   => $kodeTrx,
                'nomor_internet' => $nomorInternet,
                'desc_suspend'   => $validated['note_suspend'],
                'status_suspend' => '11', // Req. Suspend
                'date_create'    => now(),
                'user_create'    => substr($currentUser, 0, 50),
                'date_update'    => now(),
                'user_update'    => substr($currentUser, 0, 50),
                'hide'           => '0',
            ]);

            // Insert ke trx_suspend_log
            DB::table('trx_suspend_log')->insert([
                'kode_suspend_log' => 'L-SUSP-' . $kodeTrx . '-' . date('ymdHis'),
                'kode_suspend'     => $kodeTrx,
                'status_suspend'   => '11',
                'user_create'      => substr($currentUser, 0, 50),
                'date_create'      => now(),
                'hide'             => '0',
            ]);

            // Update flag di register
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'is_suspend'  => '1',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request suspend untuk {$nomorInternet} berhasil diajukan."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses request suspend: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit Penyesuaian Data (Adjust) dari Modal Pelanggan
     */
    public function postAdjustData(Request $request)
    {
        $validated = $request->validate([
            'nomor_internet'    => 'required|string',
            'tagihan_bulanan'   => 'nullable|numeric',
            'range_tagihan'     => 'nullable|integer',
            'ppn'               => 'nullable|numeric',
            'status_ppn'        => 'nullable|in:0,1',
            'is_suspend'        => 'nullable|in:0,1',
            'is_denda'          => 'nullable|in:0,1',
            'periode_terminasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $nomorInternet = $validated['nomor_internet'];
            $currentUser = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

            $ppnFinal = ($request->input('status_ppn') === '1') ? ($request->input('ppn', 11) ?: 11) : '0';

            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'periode_billing' => $request->input('range_tagihan', 1) ?: 1,
                    'ppn'             => (string)$ppnFinal,
                    'is_suspend'      => $request->input('is_suspend', '0'),
                    'is_denda'        => $request->input('is_denda', '0'),
                    'is_termin'       => $request->input('periode_terminasi', '0'),
                    'user_update'     => $currentUser,
                    'date_update'     => now(),
                ]);

            // Catat log adjust di trx_batchjob_register_log
            $regNow = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
            $noteAdjust = "Penyesuaian Data (Adjust): Billing {$request->input('range_tagihan', 1)} bln, PPN {$ppnFinal}%, Suspend: " . ($request->input('is_suspend') == '1' ? 'Ya' : 'Tidak') . ", Denda: " . ($request->input('is_denda') == '1' ? 'Ya' : 'Tidak') . ", Terminasi: " . ($request->input('periode_terminasi') == '1' ? 'Ya' : 'Tidak');

            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-ADJ-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => $regNow->status_reg ?? '16',
                'note_schedule'              => $noteAdjust,
                'user_create'                => substr($currentUser, 0, 50),
                'date_create'                => now(),
                'hide'                       => '0',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Penyesuaian data untuk {$nomorInternet} berhasil disimpan."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan penyesuaian: ' . $e->getMessage()], 500);
        }
    }
}
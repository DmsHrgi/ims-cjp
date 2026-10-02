<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Broadband;

class BroadbandController extends Controller
{
    /**
     * Pastikan tabel trx_broadband ada.
     */
    private function checkTableExists()
    {
        return Schema::hasTable('trx_broadband');
    }

    /**
     * Tampilkan halaman daftar data Broadband (Read-only untuk semua role).
     */
    public function index(Request $request)
    {
        $tableExists = $this->checkTableExists();

        if (!$tableExists) {
            return view('broadband.index', [
                'tableExists' => false,
                'customers' => collect(),
                'statusCounts' => [
                    'semua' => 0,
                    'aktif' => 0,
                    'suspend' => 0,
                    'terminasi' => 0,
                    'is_login' => 0,
                ],
                'popList' => collect(),
                'bandwidthList' => collect(),
                'groupList' => collect(),
                'mitraList' => collect(),
                'perPage' => 15,
            ]);
        }

        // Query dasar
        $baseQuery = DB::table('trx_broadband as tb')
            ->leftJoin('m_status_registrasi as sr', 'tb.status_reg', '=', 'sr.status_reg')
            ->leftJoin('m_pop as po', 'tb.kode_pop', '=', 'po.kode_pop')
            ->leftJoin('m_bandwith as bw', 'tb.kode_bandwith', '=', 'bw.kode_bandwith')
            ->leftJoin('m_bandwith_kategori as bk', 'bw.kode_kategori_bandwith', '=', 'bk.kode_kategori_bandwith')
            ->leftJoin('m_wilayah as w', 'tb.kode_wilayah_kelurahan_pasang', '=', 'w.kode_wilayah_kelurahan');

        // Filter hide jika ada kolom hide
        $hideQuery = clone $baseQuery;
        $hideQuery->where(function ($q) {
            $q->where('tb.hide', '0')
              ->orWhereNull('tb.hide')
              ->orWhere('tb.hide', '');
        });

        // Hitung total status untuk tab ringkasan
        $statusCounts = [
            'semua' => (clone $hideQuery)->count(),
            'aktif' => (clone $hideQuery)
                ->where(function ($q) {
                    $q->whereNull('tb.is_termin')->orWhere('tb.is_termin', '!=', '1');
                })
                ->where(function ($q) {
                    $q->whereNull('tb.is_suspend')->orWhere('tb.is_suspend', '!=', '1');
                })
                ->count(),
            'suspend' => (clone $hideQuery)->where('tb.is_suspend', '1')->count(),
            'terminasi' => (clone $hideQuery)->where('tb.is_termin', '1')->count(),
            'is_login' => (clone $hideQuery)->where('tb.is_login', 1)->count(),
        ];

        // Mulai pembentukan query filter
        $query = clone $hideQuery;

        // 1. Filter Status Tab
        $currentStatus = $request->input('status', 'semua');
        if ($currentStatus === 'aktif') {
            $query->where(function ($q) {
                $q->whereNull('tb.is_termin')->orWhere('tb.is_termin', '!=', '1');
            })->where(function ($q) {
                $q->whereNull('tb.is_suspend')->orWhere('tb.is_suspend', '!=', '1');
            });
        } elseif ($currentStatus === 'suspend') {
            $query->where('tb.is_suspend', '1');
        } elseif ($currentStatus === 'terminasi') {
            $query->where('tb.is_termin', '1');
        } elseif ($currentStatus === 'onboarding') {
            $query->where('tb.is_login', 1);
        } elseif ($currentStatus === 'belum_login') {
            $query->where(function ($q) {
                $q->where('tb.is_login', 0)->orWhereNull('tb.is_login');
            });
        }

        // 2. Filter Pencarian Keyword (Nomor Internet, Nama, NIK, Alamat, OLT, Sales)
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($sub) use ($keyword) {
                $sub->where('tb.nomor_internet', 'LIKE', "%{$keyword}%")
                    ->orWhere('tb.nama_pelanggan', 'LIKE', "%{$keyword}%")
                    ->orWhere('tb.nik_penduduk', 'LIKE', "%{$keyword}%")
                    ->orWhere('tb.alamat_pasang', 'LIKE', "%{$keyword}%")
                    ->orWhere('tb.olt', 'LIKE', "%{$keyword}%")
                    ->orWhere('tb.nama_sales', 'LIKE', "%{$keyword}%");
            });
        }

        // 3. Filter POP
        if ($request->filled('pop')) {
            $query->where('tb.kode_pop', $request->input('pop'));
        }

        // 4. Filter Bandwidth
        if ($request->filled('bandwith')) {
            $query->where('tb.kode_bandwith', $request->input('bandwith'));
        }

        // 5. Filter Group Layanan
        if ($request->filled('group')) {
            $query->where('tb.group_layanan', $request->input('group'));
        }

        // 6. Filter Mitra
        if ($request->filled('mitra')) {
            $query->where('tb.mitra', $request->input('mitra'));
        }

        // Urutan
        $sortField = $request->input('sort', 'date_create');
        $sortDir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['nomor_internet', 'nama_pelanggan', 'date_create', 'kode_pop', 'kode_bandwith', 'status_reg'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy('tb.' . $sortField, $sortDir);
        } else {
            $query->orderBy('tb.nomor_internet', 'desc');
        }

        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $selectColumns = [
            'tb.nomor_internet',
            'tb.nik_penduduk',
            'tb.nama_pelanggan',
            'tb.rt_pasang',
            'tb.rw_pasang',
            'tb.nomor_bangunan',
            'tb.alamat_pasang',
            'tb.kode_wilayah_kelurahan_pasang',
            'tb.jenis_bangunan',
            'tb.lon_lat',
            'tb.loc_maps',
            'tb.note_request',
            'tb.kode_bandwith',
            'tb.kode_pop',
            'tb.ont_us',
            'tb.ont_ps',
            'tb.status_reg',
            'tb.media_akses',
            'tb.ppn',
            'tb.ppn_nom',
            'tb.potongan',
            'tb.potongan_note',
            'tb.last_month_billing',
            'tb.last_year_billing',
            'tb.periode_billing',
            'tb.jns_notif',
            'tb.is_termin',
            'tb.is_suspend',
            'tb.count_suspend',
            'tb.is_denda',
            'tb.islock',
            'tb.prorate',
            'tb.date_create',
            'tb.user_create',
            'tb.date_update',
            'tb.user_update',
            'tb.hide',
            'tb.mitra',
            'tb.group_layanan',
            'tb.nama_sales',
            'tb.olt',
            'tb.index_olt',
            'tb.is_login',
            'sr.desc_registrasi',
            'po.nama_pop',
            'bw.nominal_bandwith',
            'bw.harga_bandwith',
            'bk.nama_kategori_bandwith',
            'w.nama_kelurahan as nama_kelurahan_pasang',
            'w.nama_kecamatan as nama_kecamatan_pasang',
            'w.nama_kota as nama_kota_pasang',
            'w.nama_provinsi as nama_provinsi_pasang',
        ];

        $customers = $query->select($selectColumns)->paginate($perPage)->withQueryString();

        // Transform collection to add formatted fields
        $customers->getCollection()->transform(function ($item) {
            return $this->decorateCustomer($item);
        });

        // Daftar dropdown filter
        $popList = DB::table('trx_broadband')
            ->select('kode_pop')
            ->distinct()
            ->whereNotNull('kode_pop')
            ->where('kode_pop', '!=', '')
            ->orderBy('kode_pop')
            ->pluck('kode_pop');

        $bandwidthList = DB::table('trx_broadband')
            ->select('kode_bandwith')
            ->distinct()
            ->whereNotNull('kode_bandwith')
            ->where('kode_bandwith', '!=', '')
            ->orderBy('kode_bandwith')
            ->pluck('kode_bandwith');

        $groupList = DB::table('trx_broadband')
            ->select('group_layanan')
            ->distinct()
            ->whereNotNull('group_layanan')
            ->where('group_layanan', '!=', '')
            ->orderBy('group_layanan')
            ->pluck('group_layanan');

        $mitraList = DB::table('trx_broadband')
            ->select('mitra')
            ->distinct()
            ->whereNotNull('mitra')
            ->where('mitra', '!=', '')
            ->orderBy('mitra')
            ->pluck('mitra');

        return view('broadband.index', [
            'tableExists' => true,
            'customers' => $customers,
            'statusCounts' => $statusCounts,
            'currentStatus' => $currentStatus,
            'popList' => $popList,
            'bandwidthList' => $bandwidthList,
            'groupList' => $groupList,
            'mitraList' => $mitraList,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Tampilkan halaman Profil Pengguna Broadband (Detail Read-only).
     */
    public function detail($nomor_internet)
    {
        if (!$this->checkTableExists()) {
            return redirect()->route('broadband.index')->withErrors(['error' => 'Tabel trx_broadband belum tersedia di database.']);
        }

        $customer = DB::table('trx_broadband as tb')
            ->leftJoin('m_status_registrasi as sr', 'tb.status_reg', '=', 'sr.status_reg')
            ->leftJoin('m_pop as po', 'tb.kode_pop', '=', 'po.kode_pop')
            ->leftJoin('m_bandwith as bw', 'tb.kode_bandwith', '=', 'bw.kode_bandwith')
            ->leftJoin('m_bandwith_kategori as bk', 'bw.kode_kategori_bandwith', '=', 'bk.kode_kategori_bandwith')
            ->leftJoin('m_wilayah as w', 'tb.kode_wilayah_kelurahan_pasang', '=', 'w.kode_wilayah_kelurahan')
            ->where('tb.nomor_internet', $nomor_internet)
            ->select([
                'tb.*',
                'sr.desc_registrasi',
                'po.nama_pop',
                'bw.nominal_bandwith',
                'bw.harga_bandwith',
                'bk.nama_kategori_bandwith',
                'w.nama_kelurahan as nama_kelurahan_pasang',
                'w.nama_kecamatan as nama_kecamatan_pasang',
                'w.nama_kota as nama_kota_pasang',
                'w.nama_provinsi as nama_provinsi_pasang',
            ])
            ->first();

        if (!$customer) {
            return redirect()->route('broadband.index')->withErrors(['error' => "Pelanggan Broadband dengan Nomor Internet {$nomor_internet} tidak ditemukan."]);
        }

        $customer = $this->decorateCustomer($customer);

        // Cari detail pelanggan tambahan dari m_pelanggan jika ada (NIK penduduk)
        $mPelanggan = null;
        if (!empty($customer->nik_penduduk) && Schema::hasTable('m_pelanggan')) {
            $mPelanggan = DB::table('m_pelanggan')
                ->where('nik_penduduk', $customer->nik_penduduk)
                ->first();
        }

        return view('broadband.detail', [
            'customer' => $customer,
            'mPelanggan' => $mPelanggan,
        ]);
    }

    /**
     * API JSON untuk modal popup profil cepat di menu broadband.
     */
    public function modalDetail($nomor_internet)
    {
        if (!$this->checkTableExists()) {
            return response()->json(['status' => 'error', 'message' => 'Tabel belum ada'], 404);
        }

        $customer = DB::table('trx_broadband as tb')
            ->leftJoin('m_status_registrasi as sr', 'tb.status_reg', '=', 'sr.status_reg')
            ->leftJoin('m_pop as po', 'tb.kode_pop', '=', 'po.kode_pop')
            ->leftJoin('m_bandwith as bw', 'tb.kode_bandwith', '=', 'bw.kode_bandwith')
            ->leftJoin('m_bandwith_kategori as bk', 'bw.kode_kategori_bandwith', '=', 'bk.kode_kategori_bandwith')
            ->leftJoin('m_wilayah as w', 'tb.kode_wilayah_kelurahan_pasang', '=', 'w.kode_wilayah_kelurahan')
            ->where('tb.nomor_internet', $nomor_internet)
            ->select([
                'tb.*',
                'sr.desc_registrasi',
                'po.nama_pop',
                'bw.nominal_bandwith',
                'bw.harga_bandwith',
                'bk.nama_kategori_bandwith',
                'w.nama_kelurahan as nama_kelurahan_pasang',
                'w.nama_kecamatan as nama_kecamatan_pasang',
                'w.nama_kota as nama_kota_pasang',
                'w.nama_provinsi as nama_provinsi_pasang',
            ])
            ->first();

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $customer = $this->decorateCustomer($customer);

        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    /**
     * Format dan perkaya atribut data broadband untuk kemudahan display.
     */
    private function decorateCustomer($item)
    {
        // 1. Alamat Lengkap Pasang
        $parts = [];
        if (!empty($item->alamat_pasang)) {
            $parts[] = $item->alamat_pasang;
        }
        if (!empty($item->nomor_bangunan)) {
            $parts[] = 'NO. ' . $item->nomor_bangunan;
        }
        if (!empty($item->rt_pasang) || !empty($item->rw_pasang)) {
            $parts[] = 'RT' . ($item->rt_pasang ?: '00') . '/RW' . ($item->rw_pasang ?: '00');
        }
        if (!empty($item->nama_kelurahan_pasang)) {
            $parts[] = 'KEL. ' . $item->nama_kelurahan_pasang;
        }
        if (!empty($item->nama_kecamatan_pasang)) {
            $parts[] = 'KEC. ' . $item->nama_kecamatan_pasang;
        }
        if (!empty($item->nama_kota_pasang)) {
            $parts[] = $item->nama_kota_pasang;
        }
        if (!empty($item->nama_provinsi_pasang)) {
            $parts[] = $item->nama_provinsi_pasang;
        }

        $item->alamat_lengkap = !empty($parts) ? implode(', ', $parts) : ($item->alamat_pasang ?: '-');

        // 2. Status Label & Warna Badge
        if (($item->is_termin ?? '0') === '1') {
            $item->status_badge = 'terminasi';
            $item->status_text = 'Terminasi';
            $item->status_class = 'bg-rose-100 text-rose-800 border-rose-200';
            $item->status_icon = 'fa-ban';
        } elseif (($item->is_suspend ?? '0') === '1') {
            $item->status_badge = 'suspend';
            $item->status_text = 'Suspend';
            $item->status_class = 'bg-amber-100 text-amber-800 border-amber-200';
            $item->status_icon = 'fa-pause-circle';
        } else {
            $item->status_badge = 'aktif';
            $item->status_text = !empty($item->desc_registrasi) ? $item->desc_registrasi : 'Aktif';
            $item->status_class = 'bg-emerald-100 text-emerald-800 border-emerald-200';
            $item->status_icon = 'fa-check-circle';
        }

        // 3. Nama Paket / Bandwidth
        if (!empty($item->nama_kategori_bandwith) && !empty($item->nominal_bandwith)) {
            $item->paket_label = $item->nama_kategori_bandwith . ' ' . $item->nominal_bandwith . ' Mbps';
        } elseif (!empty($item->nominal_bandwith)) {
            $item->paket_label = $item->nominal_bandwith . ' Mbps';
        } else {
            $item->paket_label = $item->kode_bandwith ?: '-';
        }

        // 4. URL Maps / Sharelock
        if (!empty($item->loc_maps)) {
            $item->maps_url = $item->loc_maps;
        } elseif (!empty($item->lon_lat)) {
            $item->maps_url = 'https://www.google.com/maps?q=' . urlencode(trim($item->lon_lat));
        } else {
            $item->maps_url = null;
        }

        // 5. Format Harga / Billing
        $item->harga_formatted = !empty($item->harga_bandwith) ? 'Rp ' . number_format((float)$item->harga_bandwith, 0, ',', '.') : '-';
        $item->ppn_nom_formatted = !empty($item->ppn_nom) ? 'Rp ' . number_format((float)$item->ppn_nom, 0, ',', '.') : '-';
        $item->potongan_formatted = !empty($item->potongan) ? 'Rp ' . number_format((float)$item->potongan, 0, ',', '.') : '-';

        return $item;
    }
}

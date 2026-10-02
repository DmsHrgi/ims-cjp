<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BroadbandController extends Controller
{
    /**
     * Menampilkan daftar data Broadband dari database ke-2 (ims_v3)
     */
    public function index(Request $request)
    {
        $dbConnected = true;
        $connectionError = null;

        $statusCounts = [
            'semua'     => 0,
            'aktif'     => 0,
            'suspend'   => 0,
            'terminasi' => 0,
        ];

        $popOptions = collect();
        $groupLayananOptions = collect();
        $broadbands = new LengthAwarePaginator([], 0, 10);

        try {
            // Test ping koneksi ims_v3
            $v3 = DB::connection('ims_v3');
            $v3->getPdo();

            // Cek apakah tabel trx_batchjob_register ada
            $tableExists = true;
            try {
                $v3->table('trx_batchjob_register')->limit(1)->first();
            } catch (\Throwable $e) {
                $tableExists = false;
                $connectionError = "Tabel 'trx_batchjob_register' tidak ditemukan di database ims_v3: " . $e->getMessage();
            }

            if ($tableExists) {
                // 1. Ambil list dropdown filter dari data yang ada
                try {
                    $popOptions = $v3->table('trx_batchjob_register')
                        ->whereNotNull('kode_pop')
                        ->where('kode_pop', '!=', '')
                        ->distinct()
                        ->pluck('kode_pop')
                        ->sort()
                        ->values();

                    $groupLayananOptions = $v3->table('trx_batchjob_register')
                        ->whereNotNull('group_layanan')
                        ->where('group_layanan', '!=', '')
                        ->distinct()
                        ->pluck('group_layanan')
                        ->sort()
                        ->values();
                } catch (\Throwable $e) {
                    // Abaikan jika gagal ambil dropdown
                }

                // 2. Hitung ringkasan status count
                try {
                    $baseCountQuery = $v3->table('trx_batchjob_register')
                        ->where(function ($q) {
                            $q->where('hide', '0')->orWhereNull('hide');
                        });

                    $statusCounts['semua'] = (clone $baseCountQuery)->count();
                    $statusCounts['aktif'] = (clone $baseCountQuery)
                        ->where(function($q) {
                            $q->whereNull('is_termin')->orWhere('is_termin', '!=', '1');
                        })
                        ->where(function($q) {
                            $q->whereNull('is_suspend')->orWhere('is_suspend', '!=', '1');
                        })
                        ->count();

                    $statusCounts['suspend'] = (clone $baseCountQuery)
                        ->where('is_suspend', '1')
                        ->count();

                    $statusCounts['terminasi'] = (clone $baseCountQuery)
                        ->where('is_termin', '1')
                        ->count();
                } catch (\Throwable $e) {
                    // Abaikan error count
                }

                // 3. Bangun query data utama
                // Kita coba select dengan left join ke master tables jika ada
                $hasMasterTables = true;
                $query = $v3->table('trx_batchjob_register as t');

                try {
                    // Cek ketersediaan tabel master m_pop, m_bandwith, m_status_registrasi, m_wilayah, m_pelanggan
                    $query->leftJoin('m_pop as p', 'p.kode_pop', '=', 't.kode_pop')
                          ->leftJoin('m_bandwith as b', 'b.kode_bandwith', '=', 't.kode_bandwith')
                          ->leftJoin('m_bandwith_kategori as bk', 'bk.kode_kategori_bandwith', '=', 'b.kode_kategori_bandwith')
                          ->leftJoin('m_status_registrasi as sr', 'sr.status_reg', '=', 't.status_reg')
                          ->leftJoin('m_pelanggan as mp', 'mp.nik_penduduk', '=', 't.nik_penduduk')
                          ->leftJoin('m_wilayah as w', 'w.kode_wilayah_kelurahan', '=', 't.kode_wilayah_kelurahan_pasang')
                          ->select(
                              't.*',
                              'p.nama_pop',
                              'b.nominal_bandwith',
                              'b.harga_bandwith',
                              'bk.nama_kategori_bandwith',
                              'sr.desc_registrasi',
                              'mp.nama_penduduk',
                              'mp.telp_pelanggan',
                              'mp.email_pelanggan',
                              'w.nama_kelurahan',
                              'w.nama_kecamatan',
                              'w.nama_kota'
                          );
                    // Cek test query cepat
                    (clone $query)->limit(1)->first();
                } catch (\Throwable $e) {
                    // Fallback jika tidak ada tabel master di ims_v3
                    $hasMasterTables = false;
                    $query = $v3->table('trx_batchjob_register as t')->select('t.*');
                }

                // Filter hide
                $query->where(function ($q) {
                    $q->where('t.hide', '0')->orWhereNull('t.hide');
                });

                // Filter Status
                $statusFilter = strtolower(trim((string) $request->input('status', '')));
                if ($statusFilter === 'aktif') {
                    $query->where(function($q) {
                        $q->whereNull('t.is_termin')->orWhere('t.is_termin', '!=', '1');
                    })->where(function($q) {
                        $q->whereNull('t.is_suspend')->orWhere('t.is_suspend', '!=', '1');
                    });
                } elseif ($statusFilter === 'suspend') {
                    $query->where('t.is_suspend', '1');
                } elseif ($statusFilter === 'terminasi') {
                    $query->where('t.is_termin', '1');
                }

                // Filter POP
                if ($request->filled('pop') && $request->pop !== 'ALL') {
                    $query->where('t.kode_pop', $request->pop);
                }

                // Filter Group Layanan
                if ($request->filled('group_layanan') && $request->group_layanan !== 'ALL') {
                    $query->where('t.group_layanan', $request->group_layanan);
                }

                // Filter Search
                if ($request->filled('search')) {
                    $search = trim($request->search);
                    $query->where(function ($q) use ($search, $hasMasterTables) {
                        $q->where('t.nomor_internet', 'LIKE', "%{$search}%")
                          ->orWhere('t.nama_pelanggan', 'LIKE', "%{$search}%")
                          ->orWhere('t.nik_penduduk', 'LIKE', "%{$search}%")
                          ->orWhere('t.alamat_pasang', 'LIKE', "%{$search}%")
                          ->orWhere('t.ont_us', 'LIKE', "%{$search}%")
                          ->orWhere('t.kode_bandwith', 'LIKE', "%{$search}%")
                          ->orWhere('t.nama_sales', 'LIKE', "%{$search}%")
                          ->orWhere('t.olt', 'LIKE', "%{$search}%");

                        if ($hasMasterTables) {
                            $q->orWhere('p.nama_pop', 'LIKE', "%{$search}%")
                              ->orWhere('mp.nama_penduduk', 'LIKE', "%{$search}%")
                              ->orWhere('mp.telp_pelanggan', 'LIKE', "%{$search}%")
                              ->orWhere('w.nama_kelurahan', 'LIKE', "%{$search}%")
                              ->orWhere('w.nama_kecamatan', 'LIKE', "%{$search}%")
                              ->orWhere('w.nama_kota', 'LIKE', "%{$search}%");
                        }
                    });
                }

                // Pagination
                $perPage = (int) ($request->entries ?? 10);
                if (!in_array($perPage, [10, 25, 50, 100], true)) {
                    $perPage = 10;
                }

                // Sorting
                $query->orderByDesc('t.date_create');

                $broadbands = $query->paginate($perPage)->withQueryString();
            }

        } catch (\Throwable $e) {
            $dbConnected = false;
            $connectionError = $e->getMessage();
        }

        return view('broadband.index', compact(
            'broadbands',
            'statusCounts',
            'popOptions',
            'groupLayananOptions',
            'dbConnected',
            'connectionError'
        ));
    }

    /**
     * Tampilan detail lengkap profil data pelanggan Broadband (Read-only)
     */
    public function show($nomor_internet)
    {
        try {
            $v3 = DB::connection('ims_v3');
            $v3->getPdo();

            $query = $v3->table('trx_batchjob_register as t')
                ->where('t.nomor_internet', $nomor_internet);

            try {
                $item = $query->leftJoin('m_pop as p', 'p.kode_pop', '=', 't.kode_pop')
                    ->leftJoin('m_bandwith as b', 'b.kode_bandwith', '=', 't.kode_bandwith')
                    ->leftJoin('m_bandwith_kategori as bk', 'bk.kode_kategori_bandwith', '=', 'b.kode_kategori_bandwith')
                    ->leftJoin('m_status_registrasi as sr', 'sr.status_reg', '=', 't.status_reg')
                    ->leftJoin('m_pelanggan as mp', 'mp.nik_penduduk', '=', 't.nik_penduduk')
                    ->leftJoin('m_wilayah as w', 'w.kode_wilayah_kelurahan', '=', 't.kode_wilayah_kelurahan_pasang')
                    ->select(
                        't.*',
                        'p.nama_pop',
                        'b.nominal_bandwith',
                        'b.harga_bandwith',
                        'bk.nama_kategori_bandwith',
                        'sr.desc_registrasi',
                        'mp.nama_penduduk',
                        'mp.telp_pelanggan',
                        'mp.email_pelanggan',
                        'mp.alamat_p as alamat_ktp',
                        'w.nama_kelurahan',
                        'w.nama_kecamatan',
                        'w.nama_kota'
                    )
                    ->first();
            } catch (\Throwable $e) {
                // Fallback query tanpa join
                $item = $v3->table('trx_batchjob_register')
                    ->where('nomor_internet', $nomor_internet)
                    ->first();
            }

            if (!$item) {
                return redirect()->route('broadband.index')->withErrors(['error' => 'Data Broadband dengan Nomor Internet ' . $nomor_internet . ' tidak ditemukan di database ims_v3.']);
            }

            return view('broadband.detail', compact('item'));

        } catch (\Throwable $e) {
            return redirect()->route('broadband.index')->withErrors(['error' => 'Koneksi database ims_v3 gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick View Modal via AJAX
     */
    public function modal($nomor_internet)
    {
        try {
            $v3 = DB::connection('ims_v3');
            $query = $v3->table('trx_batchjob_register as t')
                ->where('t.nomor_internet', $nomor_internet);

            try {
                $item = $query->leftJoin('m_pop as p', 'p.kode_pop', '=', 't.kode_pop')
                    ->leftJoin('m_bandwith as b', 'b.kode_bandwith', '=', 't.kode_bandwith')
                    ->leftJoin('m_bandwith_kategori as bk', 'bk.kode_kategori_bandwith', '=', 'b.kode_kategori_bandwith')
                    ->leftJoin('m_status_registrasi as sr', 'sr.status_reg', '=', 't.status_reg')
                    ->leftJoin('m_pelanggan as mp', 'mp.nik_penduduk', '=', 't.nik_penduduk')
                    ->leftJoin('m_wilayah as w', 'w.kode_wilayah_kelurahan', '=', 't.kode_wilayah_kelurahan_pasang')
                    ->select(
                        't.*',
                        'p.nama_pop',
                        'b.nominal_bandwith',
                        'b.harga_bandwith',
                        'bk.nama_kategori_bandwith',
                        'sr.desc_registrasi',
                        'mp.nama_penduduk',
                        'mp.telp_pelanggan',
                        'mp.email_pelanggan',
                        'w.nama_kelurahan',
                        'w.nama_kecamatan',
                        'w.nama_kota'
                    )
                    ->first();
            } catch (\Throwable $e) {
                $item = $v3->table('trx_batchjob_register')
                    ->where('nomor_internet', $nomor_internet)
                    ->first();
            }

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $item,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

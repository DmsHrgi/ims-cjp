<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PendaftaranController extends Controller
{
    public function create(Request $request)
    {
        $bangunan = DB::table('m_jns_bangunan')->where('hide', '0')->orderBy('jenis_bangunan')->get();
        $kategori = DB::table('m_bandwith_kategori')->where('hide', '0')->orderBy('nama_kategori_bandwith')->get();
        $groupFromReg = DB::table('trx_batchjob_register')->select('group_layanan')->distinct()->whereNotNull('group_layanan')->where('group_layanan', '!=', '')->pluck('group_layanan')->toArray();
        $groupFromMitra = DB::table('m_mitra')->pluck('nama_mitra')->toArray();
        $groupLayanan = collect(array_merge($groupFromReg, $groupFromMitra))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        
        $sales = DB::table('tb_m_karyawan')
            ->whereIn('status_aktif', ['1', '01'])
            ->whereNotNull('nama_karyawan')
            ->where('nama_karyawan', '!=', '')
            ->orderBy('nama_karyawan')
            ->get(['kode_karyawan', 'nama_karyawan']);

        if ($sales->isEmpty()) {
            $sales = DB::table('view_pengguna')
                ->whereIn('status_aktif', ['1', '01'])
                ->whereNotNull('nama_karyawan')
                ->where('nama_karyawan', '!=', '')
                ->orderBy('nama_karyawan')
                ->get(['kode_karyawan', 'nama_karyawan']);
        }

        $provinsi = DB::table('m_wilayah')->select('kode_wilayah_provinsi', 'nama_provinsi')->distinct()->orderBy('nama_provinsi')->get();

        $statusList = DB::table('view_batchjob')
            ->select('status_reg', 'desc_registrasi')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->where(function ($q) {
                $q->whereNull('aktivasi_date_finish')
                  ->orWhere('aktivasi_date_finish', '');
            })
            ->where(function ($q) {
                $q->whereNull('status_reg')
                  ->orWhere('status_reg', '!=', '16');
            })
            ->where(function ($q) {
                $q->whereNull('desc_registrasi')
                  ->orWhere('desc_registrasi', 'not like', '%SELESAI AKTIVASI%');
            })
            ->whereNotNull('status_reg')
            ->distinct()
            ->get();

        $wilayahList = DB::table('view_batchjob')
            ->select('nama_kota_pasang')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->where(function ($q) {
                $q->whereNull('aktivasi_date_finish')
                  ->orWhere('aktivasi_date_finish', '');
            })
            ->where(function ($q) {
                $q->whereNull('status_reg')
                  ->orWhere('status_reg', '!=', '16');
            })
            ->where(function ($q) {
                $q->whereNull('desc_registrasi')
                  ->orWhere('desc_registrasi', 'not like', '%SELESAI AKTIVASI%');
            })
            ->whereNotNull('nama_kota_pasang')
            ->where('nama_kota_pasang', '!=', '')
            ->distinct()
            ->orderBy('nama_kota_pasang')
            ->pluck('nama_kota_pasang');

        $query = DB::table('view_batchjob')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->where(function ($q) {
                $q->whereNull('aktivasi_date_finish')
                  ->orWhere('aktivasi_date_finish', '');
            })
            ->where(function ($q) {
                $q->whereNull('status_reg')
                  ->orWhere('status_reg', '!=', '16');
            })
            ->where(function ($q) {
                $q->whereNull('desc_registrasi')
                  ->orWhere('desc_registrasi', 'not like', '%SELESAI AKTIVASI%');
            });

        if ($request->filled('layanan')) {
            $query->where('kode_kategori_bandwith', $request->layanan);
        }

        if ($request->filled('nama')) {
            $namaStr = trim($request->nama);
            $query->where(function ($q) use ($namaStr) {
                $q->where('nama_pelanggan', 'like', '%' . $namaStr . '%')
                  ->orWhere('nama_penduduk', 'like', '%' . $namaStr . '%')
                  ->orWhere('nomor_internet', 'like', '%' . $namaStr . '%');
            });
        }

        if ($request->filled('alamat')) {
            $alamatStr = trim($request->alamat);
            $query->where(function ($q) use ($alamatStr) {
                $q->where('alamat_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_p', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_k', 'like', '%' . $alamatStr . '%')
                  ->orWhere('jenis_bangunan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nomor_bangunan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rt_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rw_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kelurahan_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kecamatan_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kota_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_provinsi_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rt_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rw_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kelurahan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kecamatan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kota', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_provinsi', 'like', '%' . $alamatStr . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status_reg', $request->status);
        }

        if ($request->filled('wilayah')) {
            $query->where('nama_kota_pasang', $request->wilayah);
        }

        if ($request->has('reset')) {
            session()->forget(['pendaftaran_page', 'pendaftaran_query', 'pendaftaran_last_url']);
        }

        // Jika page tidak disertakan dalam query URL tapi tersimpan di session, arahkan ke page terakhir yang diklik
        if (!$request->has('page') && !$request->has('reset') && session()->has('pendaftaran_page') && (int) session('pendaftaran_page') > 1) {
            return redirect()->route('pendaftaran', array_merge($request->query(), ['page' => session('pendaftaran_page')]));
        }

        $perPage = (int) $request->input('entries', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $rows = $query->orderByDesc('date_create')
            ->paginate($perPage)
            ->withQueryString();

        // Jika halaman yang diminta melebihi total halaman (misal setelah filter/hapus data), arahkan ke halaman terakhir yang tersedia
        if ($rows->lastPage() > 0 && $rows->currentPage() > $rows->lastPage()) {
            session(['pendaftaran_page' => $rows->lastPage()]);
            return redirect()->route('pendaftaran', array_merge($request->query(), ['page' => $rows->lastPage()]));
        }

        session([
            'pendaftaran_page' => $rows->currentPage(),
            'pendaftaran_query' => $request->query(),
            'pendaftaran_last_url' => $request->fullUrl(),
        ]);

        $rows->getCollection()->transform(function ($r) {
            if (empty($r->alamat_p)) {
                $parts = array_filter([
                    $r->alamat_pasang ?? null,
                    !empty($r->nomor_bangunan) ? 'NO. ' . $r->nomor_bangunan : null,
                    (!empty($r->rt_pasang) || !empty($r->rw_pasang)) ? 'RT' . ($r->rt_pasang ?: '00') . '/RW' . ($r->rw_pasang ?: '00') : null,
                    !empty($r->nama_kelurahan_pasang) ? 'KEL. ' . $r->nama_kelurahan_pasang : null,
                    !empty($r->nama_kecamatan_pasang) ? 'KEC. ' . $r->nama_kecamatan_pasang : null,
                    $r->nama_kota_pasang ?? null,
                    $r->nama_provinsi_pasang ?? null,
                ]);
                $r->alamat_p = !empty($parts) ? implode(', ', $parts) : ($r->alamat_pasang ?: null);
            }
            return $r;
        });

        // Deteksi level Admin & NOC untuk conditional rendering
        $u = session('user', []);
        $userLevel = strtoupper($u['level'] ?? '');
        $kodeLevel = $u['kode_level'] ?? '';
        $isAdmin = ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');
        $isNoc = !$isAdmin && ($userLevel === 'NOC' || $kodeLevel === 'lv68132');
        $isFinance = !$isAdmin && ($userLevel === 'FINANCE' || $kodeLevel === 'lv33501' || ($u['level_num'] ?? null) == 6 || str_contains($userLevel, 'FINANCE') || str_contains($userLevel, 'KEUANGAN'));

        // Daftar teknisi lapangan khusus untuk Team Survey & Instalasi
        $targetTeknisNames = [
            'Abdul Ghani',
            'Dede',
            'Dika',
            'Dodi Sodikin',
            'Cristian',
            'Iyan sofian',
            'Fadil',
            'M Ryan Septiadi',
            'Sandi',
            'Dudi',
            'Dandi',
            'Reza Apriant',
        ];

        $existingTeknis = DB::table('tb_m_karyawan')
            ->whereIn('status_aktif', ['1', '01'])
            ->where(function ($q) use ($targetTeknisNames) {
                foreach ($targetTeknisNames as $name) {
                    $q->orWhere('nama_karyawan', 'LIKE', '%' . $name . '%');
                }
            })
            ->get(['kode_karyawan', 'nama_karyawan']);

        $teamTeknisList = collect($targetTeknisNames)->map(function ($targetName) use ($existingTeknis) {
            $found = $existingTeknis->first(function ($item) use ($targetName) {
                return strcasecmp(trim($item->nama_karyawan), trim($targetName)) === 0
                    || stripos($item->nama_karyawan, $targetName) !== false;
            });

            return (object)[
                'kode_karyawan' => $found ? $found->kode_karyawan : 'KRY-' . strtoupper(Str::slug($targetName)),
                'nama_karyawan' => $found ? $found->nama_karyawan : $targetName,
            ];
        });

        // Master data tim NOC untuk Jadwal & Report Aktivasi
        $targetNocNames = [
            'KELVIN SULTAN ASHARI',
            'HARRY SETIONO',
            'RICKY SAHARA PUTRA',
            'MUHAMAD RAFI RAMDHANI',
            'RIDWAN',
            'RASHIF',
        ];

        $existingNocKaryawan = collect();
        try {
            $existingNocKaryawan = DB::table('tb_m_karyawan')
                ->where(function ($q) use ($targetNocNames) {
                    foreach ($targetNocNames as $name) {
                        $q->orWhere('nama_karyawan', 'LIKE', '%' . $name . '%');
                    }
                })
                ->get(['kode_karyawan', 'nama_karyawan']);
        } catch (\Exception $e) {}

        $teamAktivasiList = collect($targetNocNames)->map(function ($targetName) use ($existingNocKaryawan) {
            $found = $existingNocKaryawan->first(function ($item) use ($targetName) {
                return strcasecmp(trim($item->nama_karyawan), trim($targetName)) === 0
                    || stripos($item->nama_karyawan, $targetName) !== false;
            });

            return (object)[
                'kode_karyawan' => $found ? $found->kode_karyawan : 'KRY-' . strtoupper(Str::slug($targetName)),
                'nama_karyawan' => $targetName,
            ];
        });

        $popList = DB::table('m_pop')
            ->where(function ($q) { $q->where('hide', '0')->orWhereNull('hide'); })
            ->orderBy('nama_pop')
            ->get();

        $mediaAksesList = DB::table('m_media_akses')
            ->where(function ($q) { $q->where('hide', '0')->orWhereNull('hide'); })
            ->orderBy('nama_media_akses')
            ->get();

        $barangList = DB::table('m_barang as b')
            ->leftJoin('m_jns_barang as jb', 'jb.kode_jns_barang', '=', 'b.kode_jns_barang')
            ->where(function ($q) { $q->where('b.hide', '0')->orWhereNull('b.hide'); })
            ->select('b.kode_barang', 'b.nama_barang', 'b.tipe_barang', 'jb.satuan')
            ->orderBy('b.nama_barang')
            ->get()
            ->map(function ($item) {
                $nama = strtoupper(trim($item->nama_barang ?? ''));
                $tipe = strtoupper(trim($item->tipe_barang ?? ''));
                $kode = trim($item->kode_barang ?? '');
                if ($kode === 'BR003' || $nama === 'HUAWEI') {
                    $item->nama_barang = 'ONU HUAWEI';
                } elseif ($kode === 'BR013' || ($nama === 'ZTE' && str_contains($tipe, 'F660'))) {
                    $item->nama_barang = 'ONU ZTE F660';
                } elseif ($kode === 'BR011' || ($nama === 'ZTE' && str_contains($tipe, 'F609 V3'))) {
                    $item->nama_barang = 'ONU ZTE F609 V3';
                } elseif ($kode === 'BR004' || $nama === 'ZTE') {
                    $item->nama_barang = 'ONU ZTE';
                }
                return $item;
            });

        $installedItems = DB::table('trx_instalasi_barang as ib')
            ->leftJoin('m_barang as b', 'b.kode_barang', '=', 'ib.kode_barang')
            ->leftJoin('m_jns_barang as jb', 'jb.kode_jns_barang', '=', 'b.kode_jns_barang')
            ->where(function ($q) { $q->where('ib.hide', '0')->orWhereNull('ib.hide'); })
            ->select('ib.nomor_internet', 'ib.kode_barang', 'ib.jumlah_barang', 'b.nama_barang', 'b.tipe_barang', 'jb.satuan')
            ->get()
            ->map(function ($item) {
                $nama = strtoupper(trim($item->nama_barang ?? ''));
                $tipe = strtoupper(trim($item->tipe_barang ?? ''));
                $kode = trim($item->kode_barang ?? '');
                if ($kode === 'BR003' || $nama === 'HUAWEI') {
                    $item->nama_barang = 'ONU HUAWEI';
                } elseif ($kode === 'BR013' || ($nama === 'ZTE' && str_contains($tipe, 'F660'))) {
                    $item->nama_barang = 'ONU ZTE F660';
                } elseif ($kode === 'BR011' || ($nama === 'ZTE' && str_contains($tipe, 'F609 V3'))) {
                    $item->nama_barang = 'ONU ZTE F609 V3';
                } elseif ($kode === 'BR004' || $nama === 'ZTE') {
                    $item->nama_barang = 'ONU ZTE';
                }
                return $item;
            })
            ->groupBy('nomor_internet');

        $paketList = DB::table('m_bandwith as b')
            ->join('m_bandwith_kategori as k', 'b.kode_kategori_bandwith', '=', 'k.kode_kategori_bandwith')
            ->where(function ($q) { $q->where('b.hide', '0')->orWhereNull('b.hide'); })
            ->select('b.kode_bandwith', 'b.nominal_bandwith', 'b.harga_bandwith', 'b.kode_kategori_bandwith', 'k.nama_kategori_bandwith')
            ->orderBy('k.nama_kategori_bandwith')
            ->orderBy('b.nominal_bandwith')
            ->get();

        $compFromPelanggan = collect();
        try {
            $compFromPelanggan = DB::table('m_pelanggan')
                ->whereNotNull('id_perusahaan')
                ->where('id_perusahaan', '!=', '')
                ->select('id_perusahaan', 'nama_perusahaan')
                ->get();
        } catch (\Exception $e) {}

        $compFromTrx = collect();
        try {
            $compFromTrx = DB::table('trx_batchjob_register')
                ->whereNotNull('id_perusahaan')
                ->where('id_perusahaan', '!=', '')
                ->select('id_perusahaan', 'nama_pelanggan as nama_perusahaan')
                ->get();
        } catch (\Exception $e) {}

        $existingCompanies = $compFromPelanggan->concat($compFromTrx)
            ->filter(fn($c) => !empty($c->id_perusahaan) && !empty($c->nama_perusahaan))
            ->unique(fn($c) => strtolower(trim($c->id_perusahaan)))
            ->sortBy(fn($c) => strtolower($c->nama_perusahaan))
            ->values();

        $autoIdPerusahaan = self::generateIdPerusahaan();

        return view('pendaftaran.pemasangan-baru', compact(
            'bangunan', 'kategori', 'groupLayanan', 'sales', 'provinsi', 'rows', 'statusList', 'wilayahList', 'isAdmin', 'isNoc', 'isFinance', 'teamAktivasiList', 'teamTeknisList', 'popList', 'mediaAksesList', 'barangList', 'installedItems', 'paketList', 'existingCompanies', 'autoIdPerusahaan'
        ));
    }

    public function export(Request $request)
    {
        $query = DB::table('view_batchjob')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->where(function ($q) {
                $q->whereNull('aktivasi_date_finish')
                  ->orWhere('aktivasi_date_finish', '');
            })
            ->where(function ($q) {
                $q->whereNull('status_reg')
                  ->orWhere('status_reg', '!=', '16');
            })
            ->where(function ($q) {
                $q->whereNull('desc_registrasi')
                  ->orWhere('desc_registrasi', 'not like', '%SELESAI AKTIVASI%');
            });

        if ($request->filled('layanan')) {
            $query->where('kode_kategori_bandwith', $request->layanan);
        }

        if ($request->filled('nama')) {
            $namaStr = trim($request->nama);
            $query->where(function ($q) use ($namaStr) {
                $q->where('nama_pelanggan', 'like', '%' . $namaStr . '%')
                  ->orWhere('nama_penduduk', 'like', '%' . $namaStr . '%')
                  ->orWhere('nomor_internet', 'like', '%' . $namaStr . '%');
            });
        }

        if ($request->filled('alamat')) {
            $alamatStr = trim($request->alamat);
            $query->where(function ($q) use ($alamatStr) {
                $q->where('alamat_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_p', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('alamat_k', 'like', '%' . $alamatStr . '%')
                  ->orWhere('jenis_bangunan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nomor_bangunan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rt_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rw_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kelurahan_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kecamatan_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kota_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_provinsi_pasang', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rt_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('rw_ktp', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kelurahan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kecamatan', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_kota', 'like', '%' . $alamatStr . '%')
                  ->orWhere('nama_provinsi', 'like', '%' . $alamatStr . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status_reg', $request->status);
        }

        if ($request->filled('wilayah')) {
            $query->where('nama_kota_pasang', $request->wilayah);
        }

        $data = $query->orderByDesc('date_create')->get();

        $filename = 'Pendaftaran_Pelanggan_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            fputcsv($file, [
                'No. Internet',
                'Nama Pelanggan',
                'ID Perusahaan',
                'No. HP',
                'Email',
                'Layanan / Kapasitas',
                'Group Layanan',
                'Jenis Bangunan',
                'Alamat Pemasangan',
                'Kota Pemasangan',
                'Status Registrasi',
                'Nama Sales',
                'User Create',
                'Tanggal Create'
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->nomor_internet ?? '-',
                    $row->nama_pelanggan ?? '-',
                    "'" . ($row->id_perusahaan ?? $row->nik_penduduk ?? '-'),
                    "'" . ($row->nomor_hp ?? '-'),
                    $row->email ?? '-',
                    trim(($row->nama_kategori_bandwith ?? '') . ' ' . ($row->nominal_bandwith ?? '') . ' Mbps'),
                    $row->group_layanan ?? '-',
                    $row->jenis_bangunan ?? '-',
                    $row->alamat_p ?? $row->alamat_pasang ?? '-',
                    $row->nama_kota_pasang ?? '-',
                    $row->desc_registrasi ?? '-',
                    $row->nama_sales ?? '-',
                    $row->user_create ?? '-',
                    $row->date_create ? \Carbon\Carbon::parse($row->date_create)->format('d/m/Y H:i') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Section 1: Informasi Pelanggan
            'nama_perusahaan' => 'required|string|max:255',
            'no_telp_perusahaan' => 'required|string|max:30',
            'email_perusahaan' => 'required|email|max:150',
            'id_perusahaan' => 'required|string|max:100',
            'nama_pic_teknis' => 'required|string|max:200',
            'no_telp_pic_teknis' => 'required|string|max:30',
            'email_pic_teknis' => 'required|email|max:150',
            'nama_pic_keuangan' => 'required|string|max:200',
            'no_telp_pic_keuangan' => 'required|string|max:30',
            'email_pic_keuangan' => 'required|email|max:150',
            'jenis_perusahaan' => 'required|string|max:100',
            'tanggal_registrasi' => 'required|date',

            // Section 2: Alamat Perusahaan & Detail
            'provinsi_ktp' => 'required|string',
            'kota_ktp' => 'required|string',
            'kecamatan_ktp' => 'required|string',
            'kelurahan_ktp' => 'required|string',
            'rt_ktp' => 'required|string|max:5',
            'rw_ktp' => 'required|string|max:5',
            'nomor_bangunan_perusahaan' => 'required|string|max:50',
            'jenis_bangunan_perusahaan' => 'nullable|string|max:100',
            'alamat_ktp' => 'required|string',
            'lon_lat_perusahaan' => 'nullable|string|max:100',
            'sharelock_perusahaan' => 'nullable|string|max:500',
            'foto_po' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_bangunan' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Section 3: Alamat & Lokasi Pemasangan
            'provinsi_pasang' => 'required|string',
            'kota_pasang' => 'required|string',
            'kecamatan_pasang' => 'required|string',
            'kelurahan_pasang' => 'required|string',
            'rt_pasang' => 'required|string|max:5',
            'rw_pasang' => 'required|string|max:5',
            'nomor_bangunan' => 'required|string|max:50',
            'alamat_pasang' => 'required|string',
            'lon_lat' => 'nullable|string|max:100',
            'sharelock' => 'nullable|string|max:500',
            'permintaan_khusus' => 'nullable|string',

            // Section 4: Pemilihan Kapasitas Layanan
            'jenis_bangunan' => 'required|string|max:100',
            'kode_kategori' => 'required|string|max:100',
            'group_layanan' => 'nullable|string|max:100',
            'kode_bandwith' => 'required|string|max:100',
            'harga_paket' => 'required|string|max:100',

            // Section 5: Informasi Penugasan Sales & Sistem
            'nama_sales' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Ambil NAMA kategori bandwith
            $bandwithInfo = DB::table('m_bandwith')
                ->join('m_bandwith_kategori', 'm_bandwith.kode_kategori_bandwith', '=', 'm_bandwith_kategori.kode_kategori_bandwith')
                ->where('m_bandwith.kode_bandwith', $validated['kode_bandwith'])
                ->select('m_bandwith_kategori.nama_kategori_bandwith', 'm_bandwith_kategori.kode_kategori_bandwith')
                ->first();

            // Prioritaskan group_layanan yang diisi user
            $groupLayanan = !empty($validated['group_layanan']) 
                ? $validated['group_layanan'] 
                : ($validated['kode_kategori'] ?? ($bandwithInfo ? $bandwithInfo->nama_kategori_bandwith : null));

            // Ambil NAMA jenis bangunan pemasangan (Section 3)
            $bangunanInfo = DB::table('m_jns_bangunan')
                ->where('kode_bangunan', $validated['jenis_bangunan'])
                ->orWhere('jenis_bangunan', $validated['jenis_bangunan'])
                ->first();

            $namaJenisBangunan = $bangunanInfo ? $bangunanInfo->jenis_bangunan : $validated['jenis_bangunan'];

            // Ambil NAMA jenis bangunan perusahaan (Section 2)
            $jenisCorpInput = $validated['jenis_bangunan_perusahaan'] ?? $request->input('jenis_bangunan_perusahaan');
            $namaJenisBangunanCorp = null;
            if (!empty($jenisCorpInput)) {
                $bangunanCorpInfo = DB::table('m_jns_bangunan')
                    ->where('kode_bangunan', $jenisCorpInput)
                    ->orWhere('jenis_bangunan', $jenisCorpInput)
                    ->first();
                $namaJenisBangunanCorp = $bangunanCorpInfo ? $bangunanCorpInfo->jenis_bangunan : $jenisCorpInput;
            }

            // Upload foto PO
            $fotoPoPath = null;
            if ($request->hasFile('foto_po')) {
                $fotoPoPath = $request->file('foto_po')->store('foto_po', 'public');
            }

            // Upload foto Bangunan
            $fotoBangunanPath = null;
            if ($request->hasFile('foto_bangunan')) {
                $fotoBangunanPath = $request->file('foto_bangunan')->store('foto_bangunan', 'public');
            }

            // ID / Identifier Pelanggan Perusahaan
            $rawIdPerusahaan = trim($validated['id_perusahaan'] ?? '');
            if (strpos($rawIdPerusahaan, ' - ') !== false) {
                $parts = explode(' - ', $rawIdPerusahaan, 2);
                $rawIdPerusahaan = trim($parts[0]);
            }

            $idPerusahaan = !empty($rawIdPerusahaan) 
                ? $rawIdPerusahaan 
                : self::generateIdPerusahaan(isset($validated['tanggal_registrasi']) ? date('Y', strtotime($validated['tanggal_registrasi'])) : null);

            // Simpan / update ke m_pelanggan
            $pelangganData = [
                'id_perusahaan' => $idPerusahaan,
                'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),
                'no_telp_perusahaan' => substr($validated['no_telp_perusahaan'], 0, 30),
                'email_perusahaan' => substr($validated['email_perusahaan'], 0, 150),
                'nama_pic_teknis' => !empty($validated['nama_pic_teknis']) ? substr($validated['nama_pic_teknis'], 0, 200) : null,
                'no_telp_pic_teknis' => substr($validated['no_telp_pic_teknis'], 0, 30),
                'email_pic_teknis' => substr($validated['email_pic_teknis'], 0, 150),
                'nama_pic_keuangan' => !empty($validated['nama_pic_keuangan']) ? substr($validated['nama_pic_keuangan'], 0, 200) : null,
                'no_telp_pic_keuangan' => substr($validated['no_telp_pic_keuangan'], 0, 30),
                'email_pic_keuangan' => substr($validated['email_pic_keuangan'], 0, 150),
                'jenis_perusahaan' => substr($validated['jenis_perusahaan'], 0, 100),
                'tanggal_registrasi' => $validated['tanggal_registrasi'],
                'jenis_bangunan' => substr($namaJenisBangunanCorp ?: $namaJenisBangunan, 0, 100),
                'nomor_bangunan_perusahaan' => !empty($validated['nomor_bangunan_perusahaan']) ? substr($validated['nomor_bangunan_perusahaan'], 0, 50) : null,
                'lon_lat_perusahaan' => !empty($validated['lon_lat_perusahaan']) ? substr($validated['lon_lat_perusahaan'], 0, 100) : null,
                'sharelock_perusahaan' => !empty($validated['sharelock_perusahaan']) ? substr($validated['sharelock_perusahaan'], 0, 500) : null,
                
                // Compatibility mapping untuk legacy columns
                'nama_penduduk' => strtoupper($validated['nama_perusahaan']),
                'email' => substr($validated['email_perusahaan'], 0, 100),
                'nomor_hp' => substr($validated['no_telp_perusahaan'], 0, 20),
                'nomor_hp_2' => substr($validated['no_telp_pic_teknis'], 0, 20),
                'pic' => $validated['nama_pic_teknis'] ?? $validated['nama_pic_keuangan'] ?? null,
                'kode_wilayah_kelurahan_ktp' => substr($validated['kelurahan_ktp'], 0, 20),
                'rt_ktp' => substr($validated['rt_ktp'], 0, 3),
                'rw_ktp' => substr($validated['rw_ktp'], 0, 3),
                'alamat_ktp' => $validated['alamat_ktp'],
                'hide' => '0',
            ];

            $pelangganEksis = DB::table('m_pelanggan')->where('id_perusahaan', $idPerusahaan)->first();
            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            if ($pelangganEksis) {
                DB::table('m_pelanggan')
                    ->where('id_perusahaan', $idPerusahaan)
                    ->update(array_merge($pelangganData, [
                        'user_update' => substr($currentUser, 0, 20),
                        'date_update' => now(),
                    ]));
            } else {
                DB::table('m_pelanggan')->insert(array_merge($pelangganData, [
                    'user_create' => substr($currentUser, 0, 20),
                    'date_create' => now(),
                ]));
            }

            // Ambil status registrasi awal yang valid dari m_status_registrasi
            $initialStatus = DB::table('m_status_registrasi')
                ->where(function($q) {
                    $q->where('hide', '0')->orWhereNull('hide');
                })
                ->orderBy('status_reg')
                ->value('status_reg') ?? '11';

            // Generate nomor internet / nomor pelanggan otomatis:
            // Format [Tahun 2-digit YY][Bulan MM][Tgl Registrasi 2-digit DD][No Antrian Registrasi (reset antrian perbulan)]
            // Contoh 2026-08-18 -> 26 + 08 + 18 + 001 = 260818001
            $tglRegInput = $request->input('tanggal_registrasi');
            $tglObj = $tglRegInput ? \Carbon\Carbon::parse($tglRegInput) : now();
            $year2Digits = $tglObj->format('y'); // e.g. 26 (2 digit tahun)
            $year4Digits = $tglObj->format('Y'); // e.g. 2026
            $monthStr = $tglObj->format('m');    // e.g. 08
            $dayStr = $tglObj->format('d');      // e.g. 18

            $prefixMonth2 = $year2Digits . $monthStr; // e.g. 2608
            $prefixMonth4 = $year4Digits . $monthStr; // e.g. 202608

            $existingNos = DB::table('trx_batchjob_register')
                ->where(function($q) use ($prefixMonth2, $prefixMonth4) {
                    $q->where('nomor_internet', 'LIKE', $prefixMonth2 . '%')
                      ->orWhere('nomor_internet', 'LIKE', $prefixMonth4 . '%');
                })
                ->pluck('nomor_internet');

            $maxSeq = 0;
            foreach ($existingNos as $noStr) {
                // Check format 2-digit: yy(2) + mm(2) + dd(2) = 6 digit date prefix + Sequence (e.g. 260818001 -> 9 digits)
                if (str_starts_with($noStr, $prefixMonth2) && strlen($noStr) >= 7) {
                    $seqPart = substr($noStr, 6);
                    if (is_numeric($seqPart)) {
                        $seq = (int) $seqPart;
                        if ($seq > $maxSeq) {
                            $maxSeq = $seq;
                        }
                    }
                } elseif (str_starts_with($noStr, $prefixMonth4) && strlen($noStr) >= 9) {
                    // Check legacy 4-digit: YYYY(4) + MM(2) + DD(2) = 8 digit date prefix + Sequence
                    $seqPart = substr($noStr, 8);
                    if (is_numeric($seqPart)) {
                        $seq = (int) $seqPart;
                        if ($seq > $maxSeq) {
                            $maxSeq = $seq;
                        }
                    }
                }
            }

            $nextSeq = $maxSeq + 1;
            $seqFormatted = sprintf('%03d', $nextSeq);
            $nomorInternet = $year2Digits . $monthStr . $dayStr . $seqFormatted;

            // Pastikan nomor unik dan tidak duplikat
            while (DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->exists()) {
                $nextSeq++;
                $seqFormatted = sprintf('%03d', $nextSeq);
                $nomorInternet = $year2Digits . $monthStr . $dayStr . $seqFormatted;
            }

            // Handle parsing harga paket manual
            $rawHarga = $request->input('harga_paket');
            $parsedHarga = !empty($rawHarga) ? preg_replace('/[^0-9]/', '', $rawHarga) : null;
            $bwData = DB::table('m_bandwith')->where('kode_bandwith', $validated['kode_bandwith'])->first();
            $totalReg = (!empty($parsedHarga) && is_numeric($parsedHarga)) ? $parsedHarga : ($bwData->harga_bandwith ?? '300000');

            // Handle ketersediaan kode_bandwith di m_bandwith (mencegah Foreign Key error jika user mengetik custom text)
            if (!$bwData) {
                $existingBwByName = DB::table('m_bandwith')
                    ->where('nominal_bandwith', $validated['kode_bandwith'])
                    ->orWhere('kode_bandwith', 'like', '%' . $validated['kode_bandwith'] . '%')
                    ->first();

                if ($existingBwByName) {
                    $kodeBandwith = $existingBwByName->kode_bandwith;
                } else {
                    $firstKat = DB::table('m_bandwith_kategori')->first();
                    $kategoriDefault = $firstKat ? $firstKat->kode_kategori_bandwith : 'KB09212';
                    $newKodeBw = 'CUST-' . strtoupper(Str::slug(substr($validated['kode_bandwith'], 0, 15), ''));
                    if (strlen($newKodeBw) > 50) $newKodeBw = substr($newKodeBw, 0, 50);

                    $nominalDigits = preg_replace('/[^0-9]/', '', $validated['kode_bandwith']);
                    $nominalStr = !empty($nominalDigits) ? substr($nominalDigits, 0, 5) : '10';

                    $checkBw = DB::table('m_bandwith')->where('kode_bandwith', $newKodeBw)->first();
                    if (!$checkBw) {
                        DB::table('m_bandwith')->insert([
                            'kode_bandwith' => $newKodeBw,
                            'nominal_bandwith' => $nominalStr,
                            'harga_bandwith' => substr((string)$totalReg, 0, 15),
                            'kode_kategori_bandwith' => $kategoriDefault,
                            'user_create' => substr($currentUser, 0, 20),
                            'date_create' => now(),
                            'hide' => '0'
                        ]);
                    }
                    $kodeBandwith = $newKodeBw;
                }
            } else {
                $kodeBandwith = $validated['kode_bandwith'];
            }

            // Generate random PPPoE Password untuk pelanggan baru
            $pppoePassword = Str::lower(Str::random(8));

            // Data insert ke trx_batchjob_register
            $regInsertData = [
                'nomor_internet' => $nomorInternet,
                'id_perusahaan' => $idPerusahaan,
                'nama_pelanggan' => strtoupper($validated['nama_perusahaan']),
                'rt_pasang' => substr($validated['rt_pasang'], 0, 3),
                'rw_pasang' => substr($validated['rw_pasang'], 0, 3),
                'nomor_bangunan' => !empty($validated['nomor_bangunan']) ? substr($validated['nomor_bangunan'], 0, 10) : null,
                'alamat_pasang' => $validated['alamat_pasang'],
                'kode_wilayah_kelurahan_pasang' => $validated['kelurahan_pasang'],
                'jenis_bangunan' => substr($namaJenisBangunan, 0, 50),
                'lon_lat' => !empty($validated['lon_lat']) ? substr($validated['lon_lat'], 0, 100) : null,
                'loc_maps' => !empty($validated['sharelock']) ? substr($validated['sharelock'], 0, 500) : null,
                'note_request' => !empty($validated['permintaan_khusus']) ? substr($validated['permintaan_khusus'], 0, 50) : null,
                'kode_bandwith' => $kodeBandwith,
                'status_reg' => $initialStatus,
                'group_layanan' => substr($groupLayanan, 0, 50),
                'nama_sales' => substr($validated['nama_sales'], 0, 50),
                'foto_po' => $fotoPoPath ? 'storage/' . $fotoPoPath : null,
                'foto_bangunan' => $fotoBangunanPath ? 'storage/' . $fotoBangunanPath : null,
                'detail_alamat_perusahaan' => $validated['alamat_ktp'],
                'nomor_bangunan_perusahaan' => !empty($validated['nomor_bangunan_perusahaan']) ? substr($validated['nomor_bangunan_perusahaan'], 0, 50) : null,
                'rt_perusahaan' => substr($validated['rt_ktp'], 0, 5),
                'rw_perusahaan' => substr($validated['rw_ktp'], 0, 5),
                'kode_wilayah_kelurahan_perusahaan' => $validated['kelurahan_ktp'],
                'lon_lat_perusahaan' => !empty($validated['lon_lat_perusahaan']) ? substr($validated['lon_lat_perusahaan'], 0, 100) : null,
                'sharelock_perusahaan' => !empty($validated['sharelock_perusahaan']) ? substr($validated['sharelock_perusahaan'], 0, 500) : null,
                'user_create' => substr($currentUser, 0, 20),
                'date_create' => now(),
                'hide' => '0',
            ];

            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('trx_batchjob_register', 'pppoe_password')) {
                    $regInsertData['pppoe_password'] = $pppoePassword;
                }
            } catch (\Throwable $e) {}

            DB::table('trx_batchjob_register')->insert($regInsertData);

            // Insert ke trx_instalasi
            DB::table('trx_instalasi')->insert([
                'kode_instalasi' => 'INST-' . $nomorInternet,
                'nomor_internet' => $nomorInternet,
                'foto_ktp'       => $fotoPoPath ? 'storage/' . $fotoPoPath : null,
                'foto_rumah'     => $fotoBangunanPath ? 'storage/' . $fotoBangunanPath : null,
                'user_create'    => substr($currentUser, 0, 20),
                'date_create'    => now(),
                'hide'           => '0',
            ]);

            // Insert ke trx_billing_registrasi (Invoice User Finance)
            DB::table('trx_billing_registrasi')->insert([
                'kode_billing_registrasi' => 'REG-' . $nomorInternet,
                'nomor_internet'          => $nomorInternet,
                'kode_bandwith'           => $kodeBandwith,
                'nominal_bandwith'        => substr($bwData->nominal_bandwith ?? preg_replace('/[^0-9]/', '', $validated['kode_bandwith']), 0, 5) ?: '10',
                'potongan'                => '0',
                'desc_potongan'           => '-',
                'ppn'                     => '0.11',
                'tax'                     => '2',
                'voucher'                 => '-',
                'total_reg'               => $totalReg,
                'notif_mail'              => '1',
                'notif_wa'                => '1',
                'status_bill_reg'         => '11',
                'payment_type'            => '1',
                'user_create'             => substr($currentUser, 0, 20),
                'hide'                    => '0',
            ]);

            DB::commit();

            session(['pendaftaran_page' => 1]);

            return redirect()->route('pendaftaran')
                ->with('success', "Registrasi perusahaan berhasil! Nomor Internet: {$nomorInternet} | PPPoE Username: {$nomorInternet} | Password: {$pppoePassword}");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($fotoPoPath) Storage::disk('public')->delete($fotoPoPath);
            if ($fotoBangunanPath) Storage::disk('public')->delete($fotoBangunanPath);

            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan pendaftaran')]);
        }
    }

    public function getKota(Request $request)
    {
        $provinsi = $request->query('provinsi') ?? $request->query('provinsi_id');
        $kota = DB::table('m_wilayah')
            ->select('kode_wilayah_kota', 'nama_kota')
            ->where('kode_wilayah_provinsi', $provinsi)
            ->distinct()
            ->orderBy('nama_kota')
            ->get();
        return response()->json($kota);
    }

    public function getKecamatan(Request $request)
    {
        $kota = $request->query('kota') ?? $request->query('kota_id');
        $kec = DB::table('m_wilayah')
            ->select('kode_wilayah_kecamatan', 'nama_kecamatan')
            ->where('kode_wilayah_kota', $kota)
            ->distinct()
            ->orderBy('nama_kecamatan')
            ->get();
        return response()->json($kec);
    }

    public function getKelurahan(Request $request)
    {
        $kec = $request->query('kecamatan') ?? $request->query('kecamatan_id');
        $kel = DB::table('m_wilayah')
            ->select('kode_wilayah_kelurahan', 'nama_kelurahan')
            ->where('kode_wilayah_kecamatan', $kec)
            ->orderBy('nama_kelurahan')
            ->get();
        return response()->json($kel);
    }

    public function getPaket(Request $request)
    {
        $kategori = $request->query('kategori');
        $paket = DB::table('m_bandwith')
            ->where('kode_kategori_bandwith', $kategori)
            ->where('hide', '0')
            ->where('disable', '0')
            ->orderBy('nominal_bandwith')
            ->get(['kode_bandwith', 'nominal_bandwith', 'harga_bandwith']);
        return response()->json($paket);
    }

    /**
     * API: Daftar kategori layanan yang diizinkan berdasarkan jenis bangunan.
     * Menggunakan tabel pivot m_bangunan_layanan.
     */
    public function getLayananByBangunan(Request $request)
    {
        $kodeBangunan = $request->query('bangunan');

        $layanan = DB::table('m_bangunan_layanan as bl')
            ->join('m_bandwith_kategori as k', 'k.kode_kategori_bandwith', '=', 'bl.kode_kategori_bandwith')
            ->where('bl.kode_bangunan', $kodeBangunan)
            ->where('k.hide', '0')
            ->orderBy('k.nama_kategori_bandwith')
            ->get(['k.kode_kategori_bandwith', 'k.nama_kategori_bandwith']);

        return response()->json($layanan);
    }

    // ============================================
    // EDIT - Halaman form edit data registrasi
    // ============================================
    public function edit($nomorInternet)
    {
        $referer = request()->headers->get('referer');
        if ($referer && (str_contains($referer, '/pelanggan') || str_contains($referer, '/pendaftaran'))) {
            session(['pendaftaran_last_url' => $referer]);
        }

        $data = DB::table('view_batchjob')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        if (!$data) {
            return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
        }

        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        $targetId = $data->id_perusahaan ?? $data->nik_penduduk ?? ($reg ? ($reg->id_perusahaan ?? $reg->nik_penduduk) : null);
        $pelanggan = $targetId ? DB::table('m_pelanggan')->where('id_perusahaan', $targetId)->first() : null;

        // Load master data
        $bangunan = DB::table('m_jns_bangunan')->where('hide', '0')->orderBy('jenis_bangunan')->get();
        $kategori = DB::table('m_bandwith_kategori')->where('hide', '0')->orderBy('nama_kategori_bandwith')->get();
        $groupLayanan = DB::table('trx_batchjob_register')->select('group_layanan')->distinct()->whereNotNull('group_layanan')->where('group_layanan', '!=', '')->orderBy('group_layanan')->pluck('group_layanan');
        
        $sales = DB::table('tb_m_karyawan')
            ->whereIn('status_aktif', ['1', '01'])
            ->whereNotNull('nama_karyawan')
            ->where('nama_karyawan', '!=', '')
            ->orderBy('nama_karyawan')
            ->get(['kode_karyawan', 'nama_karyawan']);

        if ($sales->isEmpty()) {
            $sales = DB::table('view_pengguna')
                ->whereIn('status_aktif', ['1', '01'])
                ->whereNotNull('nama_karyawan')
                ->where('nama_karyawan', '!=', '')
                ->orderBy('nama_karyawan')
                ->get(['kode_karyawan', 'nama_karyawan']);
        }
        $provinsi = DB::table('m_wilayah')->select('kode_wilayah_provinsi', 'nama_provinsi')->distinct()->orderBy('nama_provinsi')->get();

        // Ambil paket berdasarkan kategori yang sudah dipilih
        $paketList = DB::table('m_bandwith')
            ->where('kode_kategori_bandwith', $data->kode_kategori_bandwith)
            ->where('hide', '0')
            ->where('disable', '0')
            ->orderBy('nominal_bandwith')
            ->get(['kode_bandwith', 'nominal_bandwith', 'harga_bandwith']);

        // Ambil data wilayah Perusahaan cascading
        $kodeKelKtp = $reg->kode_wilayah_kelurahan_perusahaan 
            ?? ($pelanggan->kode_wilayah_kelurahan_ktp ?? null) 
            ?? $data->kode_wilayah_kelurahan_perusahaan 
            ?? $data->kode_wilayah_kelurahan_ktp 
            ?? $data->kode_wilayah_kelurahan_pasang;

        $kotaKtpList = collect();
        $kecKtpList = collect();
        $kelKtpList = collect();
        if ($kodeKelKtp) {
            $wilKtp = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelKtp)->first();
            if ($wilKtp) {
                $kotaKtpList = DB::table('m_wilayah')->select('kode_wilayah_kota', 'nama_kota')->where('kode_wilayah_provinsi', $wilKtp->kode_wilayah_provinsi)->distinct()->orderBy('nama_kota')->get();
                $kecKtpList = DB::table('m_wilayah')->select('kode_wilayah_kecamatan', 'nama_kecamatan')->where('kode_wilayah_kota', $wilKtp->kode_wilayah_kota)->distinct()->orderBy('nama_kecamatan')->get();
                $kelKtpList = DB::table('m_wilayah')->select('kode_wilayah_kelurahan', 'nama_kelurahan')->where('kode_wilayah_kecamatan', $wilKtp->kode_wilayah_kecamatan)->orderBy('nama_kelurahan')->get();
            }
        }

        // Ambil data wilayah pemasangan cascading
        $kodeKelPasang = $reg->kode_wilayah_kelurahan_pasang 
            ?? $data->kode_wilayah_kelurahan_pasang 
            ?? $kodeKelKtp;

        $kotaPasangList = collect();
        $kecPasangList = collect();
        $kelPasangList = collect();
        if ($kodeKelPasang) {
            $wilPasang = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelPasang)->first();
            if ($wilPasang) {
                $kotaPasangList = DB::table('m_wilayah')->select('kode_wilayah_kota', 'nama_kota')->where('kode_wilayah_provinsi', $wilPasang->kode_wilayah_provinsi)->distinct()->orderBy('nama_kota')->get();
                $kecPasangList = DB::table('m_wilayah')->select('kode_wilayah_kecamatan', 'nama_kecamatan')->where('kode_wilayah_kota', $wilPasang->kode_wilayah_kota)->distinct()->orderBy('nama_kecamatan')->get();
                $kelPasangList = DB::table('m_wilayah')->select('kode_wilayah_kelurahan', 'nama_kelurahan')->where('kode_wilayah_kecamatan', $wilPasang->kode_wilayah_kecamatan)->orderBy('nama_kelurahan')->get();
            }
        }

        return view('pendaftaran.edit', compact(
            'data', 'bangunan', 'kategori', 'groupLayanan', 'sales', 'provinsi',
            'paketList', 'kotaKtpList', 'kecKtpList', 'kelKtpList',
            'kotaPasangList', 'kecPasangList', 'kelPasangList'
        ));
    }

    // ============================================
    // UPDATE - Simpan perubahan data registrasi
    // ============================================
    public function update(Request $request, $nomorInternet)
    {
        $validated = $request->validate([
            // Section 1: Informasi Pelanggan (Enterprise / Corporate)
            'nama_perusahaan' => 'required|string|max:200',
            'no_telp_perusahaan' => 'required|string|max:50',
            'email_perusahaan' => 'required|email|max:100',
            'id_perusahaan' => 'required|string|max:100',
            'nama_pic_teknis' => 'required|string|max:200',
            'no_telp_pic_teknis' => 'required|string|max:50',
            'email_pic_teknis' => 'required|email|max:100',
            'nama_pic_keuangan' => 'required|string|max:200',
            'no_telp_pic_keuangan' => 'required|string|max:50',
            'email_pic_keuangan' => 'required|email|max:100',
            'jenis_perusahaan' => 'required|string|max:50',
            'tanggal_registrasi' => 'required|date',

            // Section 2: Alamat Perusahaan
            'provinsi_ktp' => 'required|string',
            'kota_ktp' => 'required|string',
            'kecamatan_ktp' => 'required|string',
            'kelurahan_ktp' => 'required|string',
            'rt_ktp' => 'required|string|max:10',
            'rw_ktp' => 'required|string|max:10',
            'nomor_bangunan_perusahaan' => 'required|string|max:50',
            'jenis_bangunan_perusahaan' => 'nullable|string|max:100',
            'alamat_ktp' => 'required|string',
            'lon_lat_perusahaan' => 'nullable|string|max:100',
            'sharelock_perusahaan' => 'nullable|string|max:500',
            'foto_po' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_bangunan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Section 3: Alamat & Lokasi Pemasangan
            'provinsi_pasang' => 'required|string',
            'kota_pasang' => 'required|string',
            'kecamatan_pasang' => 'required|string',
            'kelurahan_pasang' => 'required|string',
            'rt_pasang' => 'required|string|max:10',
            'rw_pasang' => 'required|string|max:10',
            'nomor_bangunan' => 'required|string|max:50',
            'alamat_pasang' => 'required|string',
            'lon_lat' => 'nullable|string|max:100',
            'sharelock' => 'nullable|string|max:500',
            'permintaan_khusus' => 'nullable|string',

            // Section 4: Pemilihan Kapasitas Layanan
            'jenis_bangunan' => 'required|string|max:100',
            'kode_kategori' => 'required|string|max:100',
            'group_layanan' => 'nullable|string|max:100',
            'kode_bandwith' => 'required|string|max:100',
            'harga_paket' => 'required|string|max:100',

            // Section 5: Informasi Penugasan Sales & Sistem
            'nama_sales' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            // Ambil NAMA jenis bangunan pemasangan (Section 3)
            $bangunanInfo = DB::table('m_jns_bangunan')
                ->where('kode_bangunan', $validated['jenis_bangunan'])
                ->orWhere('jenis_bangunan', $validated['jenis_bangunan'])
                ->first();
            $namaJenisBangunan = $bangunanInfo ? $bangunanInfo->jenis_bangunan : $validated['jenis_bangunan'];

            // Ambil NAMA jenis bangunan perusahaan (Section 2)
            $jenisCorpInput = $validated['jenis_bangunan_perusahaan'] ?? $request->input('jenis_bangunan_perusahaan');
            $namaJenisBangunanCorp = null;
            if (!empty($jenisCorpInput)) {
                $bangunanCorpInfo = DB::table('m_jns_bangunan')
                    ->where('kode_bangunan', $jenisCorpInput)
                    ->orWhere('jenis_bangunan', $jenisCorpInput)
                    ->first();
                $namaJenisBangunanCorp = $bangunanCorpInfo ? $bangunanCorpInfo->jenis_bangunan : $jenisCorpInput;
            }

            // Group Layanan
            $groupLayanan = !empty($validated['group_layanan']) 
                ? $validated['group_layanan'] 
                : $validated['kode_kategori'];

            // Handle parsing harga paket manual
            $rawHarga = $request->input('harga_paket');
            $parsedHarga = !empty($rawHarga) ? preg_replace('/[^0-9]/', '', $rawHarga) : null;
            $bwData = DB::table('m_bandwith')->where('kode_bandwith', $validated['kode_bandwith'])->first();
            $totalReg = (!empty($parsedHarga) && is_numeric($parsedHarga)) ? $parsedHarga : ($bwData->harga_bandwith ?? '300000');

            // Handle kode_bandwith
            $kodeBandwith = $validated['kode_bandwith'];
            if (!$bwData) {
                $existingBwByName = DB::table('m_bandwith')
                    ->where('nominal_bandwith', $validated['kode_bandwith'])
                    ->orWhere('kode_bandwith', 'like', '%' . $validated['kode_bandwith'] . '%')
                    ->first();

                if ($existingBwByName) {
                    $kodeBandwith = $existingBwByName->kode_bandwith;
                } else {
                    $kategoriDefault = DB::table('m_bandwith_kategori')->value('kode_kategori_bandwith') ?? 'KB09212';
                    $newKodeBw = 'CUST-' . strtoupper(Str::slug(substr($validated['kode_bandwith'], 0, 15), ''));
                    if (strlen($newKodeBw) > 50) $newKodeBw = substr($newKodeBw, 0, 50);

                    $nominalDigits = preg_replace('/[^0-9]/', '', $validated['kode_bandwith']);
                    $nominalStr = !empty($nominalDigits) ? substr($nominalDigits, 0, 5) : '10';

                    $checkBw = DB::table('m_bandwith')->where('kode_bandwith', $newKodeBw)->first();
                    if (!$checkBw) {
                        DB::table('m_bandwith')->insert([
                            'kode_bandwith' => $newKodeBw,
                            'nominal_bandwith' => $nominalStr,
                            'harga_bandwith' => $totalReg,
                            'kode_kategori_bandwith' => $kategoriDefault,
                            'date_create' => now(),
                            'user_create' => 'SYSTEM',
                            'hide' => '0',
                            'disable' => '0'
                        ]);
                    }
                    $kodeBandwith = $newKodeBw;
                }
            }

            // Upload foto PO
            $fotoPoUpdate = [];
            if ($request->hasFile('foto_po')) {
                $oldPo = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->value('foto_po');
                if ($oldPo) $this->removePhysicalFile($oldPo);

                $fotoPoPath = $request->file('foto_po')->store('foto_po', 'public');
                $fotoPoUpdate['foto_po'] = 'storage/' . $fotoPoPath;
            }

            // Upload foto Bangunan
            $fotoBangunanUpdate = [];
            if ($request->hasFile('foto_bangunan')) {
                $oldBang = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->value('foto_bangunan');
                if ($oldBang) $this->removePhysicalFile($oldBang);

                $fotoBangunanPath = $request->file('foto_bangunan')->store('foto_bangunan', 'public');
                $fotoBangunanUpdate['foto_bangunan'] = 'storage/' . $fotoBangunanPath;
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');
            $targetId = $customer->id_perusahaan ?? $customer->nik_penduduk;

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Update m_pelanggan
            DB::table('m_pelanggan')
                ->where('id_perusahaan', $targetId)
                ->orWhere('id_perusahaan', $validated['id_perusahaan'])
                ->update([
                    'id_perusahaan' => $validated['id_perusahaan'],
                    'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),
                    'no_telp_perusahaan' => $validated['no_telp_perusahaan'],
                    'email_perusahaan' => $validated['email_perusahaan'],
                    'nama_pic_teknis' => $validated['nama_pic_teknis'] ?? null,
                    'no_telp_pic_teknis' => $validated['no_telp_pic_teknis'],
                    'email_pic_teknis' => $validated['email_pic_teknis'],
                    'nama_pic_keuangan' => $validated['nama_pic_keuangan'] ?? null,
                    'no_telp_pic_keuangan' => $validated['no_telp_pic_keuangan'],
                    'email_pic_keuangan' => $validated['email_pic_keuangan'],
                    'jenis_perusahaan' => $validated['jenis_perusahaan'],
                    'tanggal_registrasi' => $validated['tanggal_registrasi'],
                    'jenis_bangunan' => $namaJenisBangunanCorp ?: $namaJenisBangunan,
                    'nomor_bangunan_perusahaan' => $validated['nomor_bangunan_perusahaan'] ?? null,
                    'lon_lat_perusahaan' => $validated['lon_lat_perusahaan'] ?? null,
                    'sharelock_perusahaan' => $validated['sharelock_perusahaan'] ?? null,
                    'nama_penduduk' => strtoupper($validated['nama_perusahaan']),
                    'email' => $validated['email_perusahaan'],
                    'nomor_hp' => $validated['no_telp_perusahaan'],
                    'nomor_hp_2' => $validated['no_telp_pic_teknis'],
                    'pic' => $validated['nama_pic_teknis'] ?? $validated['nama_pic_keuangan'] ?? null,
                    'kode_wilayah_kelurahan_ktp' => $validated['kelurahan_ktp'],
                    'rt_ktp' => $validated['rt_ktp'],
                    'rw_ktp' => $validated['rw_ktp'],
                    'alamat_ktp' => $validated['alamat_ktp'],
                    'date_update' => now(),
                    'user_update' => $currentUser,
                ]);

            // Update trx_batchjob_register
            $batchjobUpdate = [
                'id_perusahaan' => $validated['id_perusahaan'],
                'nama_pelanggan' => strtoupper($validated['nama_perusahaan']),
                'rt_pasang' => $validated['rt_pasang'],
                'rw_pasang' => $validated['rw_pasang'],
                'nomor_bangunan' => $validated['nomor_bangunan'],
                'alamat_pasang' => $validated['alamat_pasang'],
                'kode_wilayah_kelurahan_pasang' => $validated['kelurahan_pasang'],
                'jenis_bangunan' => $namaJenisBangunan,
                'lon_lat' => $validated['lon_lat'] ?? null,
                'loc_maps' => $validated['sharelock'] ?? null,
                'note_request' => $validated['permintaan_khusus'] ?? null,
                'kode_bandwith' => $kodeBandwith,
                'group_layanan' => $groupLayanan,
                'nama_sales' => $validated['nama_sales'],
                'detail_alamat_perusahaan' => $validated['alamat_ktp'],
                'nomor_bangunan_perusahaan' => $validated['nomor_bangunan_perusahaan'] ?? null,
                'rt_perusahaan' => $validated['rt_ktp'],
                'rw_perusahaan' => $validated['rw_ktp'],
                'kode_wilayah_kelurahan_perusahaan' => $validated['kelurahan_ktp'],
                'lon_lat_perusahaan' => $validated['lon_lat_perusahaan'] ?? null,
                'sharelock_perusahaan' => $validated['sharelock_perusahaan'] ?? null,
                'date_update' => now(),
                'user_update' => substr($currentUser, 0, 20),
            ];

            if (isset($fotoPoUpdate['foto_po'])) {
                $batchjobUpdate['foto_po'] = $fotoPoUpdate['foto_po'];
            }
            if (isset($fotoBangunanUpdate['foto_bangunan'])) {
                $batchjobUpdate['foto_bangunan'] = $fotoBangunanUpdate['foto_bangunan'];
            }

            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update($batchjobUpdate);

            // Update foto di trx_instalasi jika ada
            if (!empty($fotoPoUpdate) || !empty($fotoBangunanUpdate)) {
                $fotoInstalasiUpdate = [];
                if (isset($fotoPoUpdate['foto_po'])) {
                    $fotoInstalasiUpdate['foto_ktp'] = $fotoPoUpdate['foto_po'];
                }
                if (isset($fotoBangunanUpdate['foto_bangunan'])) {
                    $fotoInstalasiUpdate['foto_rumah'] = $fotoBangunanUpdate['foto_bangunan'];
                }
                if (!empty($fotoInstalasiUpdate)) {
                    DB::table('trx_instalasi')
                        ->where('nomor_internet', $nomorInternet)
                        ->update(array_merge($fotoInstalasiUpdate, [
                            'date_update' => now(),
                            'user_update' => substr($currentUser, 0, 20),
                        ]));
                }
            }

            // Update trx_billing_registrasi jika ada
            DB::table('trx_billing_registrasi')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'kode_bandwith' => $kodeBandwith,
                    'total_reg' => $totalReg,
                    'user_update' => substr($currentUser, 0, 20),
                ]);

            // Catat log update / edit data pelanggan
            $regNow = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-EDIT-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => $regNow->status_reg ?? '16',
                'note_schedule'              => 'Edit/Update Data Pelanggan (' . strtoupper($validated['nama_perusahaan'] ?? $nomorInternet) . ') oleh ' . $currentUser,
                'user_create'                => substr($currentUser, 0, 50),
                'date_create'                => now(),
                'hide'                       => '0',
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            return $this->redirectBackToPendaftaran("Data registrasi perusahaan {$nomorInternet} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'memperbarui data')]);
        }
    }

    // ============================================
    // BATAL PASANG - Ubah status menjadi batal
    // ============================================
    public function batalPasang(Request $request, $nomorInternet)
    {
        $validated = $request->validate([
            'kategori_batal' => 'required|string|in:TIDAK TERJANGKAU JARINGAN,PERMINTAAN DARI USER',
            'alasan_batal' => 'required|string|max:500',
        ], [
            'kategori_batal.required' => 'Silakan pilih kategori alasan pembatalan.',
            'alasan_batal.required' => 'Alasan batal pasang wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $alasanLengkap = $validated['kategori_batal'] . ' - ' . $validated['alasan_batal'];

            // Ambil status registrasi batal/gagal yang valid dari m_status_registrasi
            $statusBatal = DB::table('m_status_registrasi')
                ->where(function ($q) {
                    $q->where('desc_registrasi', 'like', '%batal%')
                      ->orWhere('desc_registrasi', 'like', '%gagal%')
                      ->orWhere('desc_registrasi', 'like', '%tidak valid%')
                      ->orWhere('desc_registrasi', 'like', '%belum valid%');
                })
                ->value('status_reg') ?? '11.1';

            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'status_reg' => $statusBatal,
                    'note_request' => $alasanLengkap,
                    'date_update' => now(),
                    'user_update' => session('user.username') ?? 'system',
                ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Pendaftaran {$nomorInternet} telah dibatalkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => self::formatDbErrorMessage($e, 'membatalkan pendaftaran')]);
        }
    }

    // ============================================
    // HAPUS - Hapus data pendaftaran secara permanen dari database
    // ============================================
    public function destroy($nomorInternet)
    {
        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Hapus seluruh file fisik (foto PO, foto bangunan, scan dokumen, foto peta, dll.) dari storage server
            $this->deletePhysicalFilesForRegister($nomorInternet);

            $reg = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if ($reg) {
                // List of all related tables to clean up
                $relatedTables = [
                    'trx_batchjob_register_log',
                    'trx_instalasi_barang',
                    'trx_instalasi_team',
                    'trx_instalasi',
                    'trx_billing_registrasi_detail',
                    'trx_billing_registrasi_log',
                    'trx_billing_registrasi',
                    'trx_billing_layanan_detail',
                    'trx_billing_layanan_log',
                    'trx_billing_layanan',
                    'trx_suspend_log',
                    'trx_suspend',
                    'trx_terminasi_log',
                    'trx_terminasi',
                    'trx_tiket_gangguan',
                    'trx_ubah_layanan_log',
                    'trx_ubah_layanan',
                    'notif_wa',
                    'bot_chat',
                    'bot_chat_medianet',
                ];

                foreach ($relatedTables as $table) {
                    if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'nomor_internet')) {
                            DB::table($table)->where('nomor_internet', $nomorInternet)->delete();
                        } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'kode_billing_registrasi')) {
                            $billRegIds = DB::table('trx_billing_registrasi')->where('nomor_internet', $nomorInternet)->pluck('kode_billing_registrasi');
                            if (!empty($billRegIds)) {
                                DB::table($table)->whereIn('kode_billing_registrasi', $billRegIds)->delete();
                            }
                        } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'kode_billing_layanan')) {
                            $billLayIds = DB::table('trx_billing_layanan')->where('nomor_internet', $nomorInternet)->pluck('kode_billing_layanan');
                            if (!empty($billLayIds)) {
                                DB::table($table)->whereIn('kode_billing_layanan', $billLayIds)->delete();
                            }
                        }
                    }
                }

                // Delete record from trx_batchjob_register
                DB::table('trx_batchjob_register')
                    ->where('nomor_internet', $nomorInternet)
                    ->delete();

                // Delete from m_pelanggan if no other registration exists for this NIK / id_perusahaan
                if (!empty($reg->nik_penduduk)) {
                    $otherCount = DB::table('trx_batchjob_register')
                        ->where('nik_penduduk', $reg->nik_penduduk)
                        ->count();
                    if ($otherCount === 0) {
                        DB::table('m_pelanggan')
                            ->where('nik_penduduk', $reg->nik_penduduk)
                            ->delete();
                    }
                } elseif (!empty($reg->id_perusahaan)) {
                    $otherCount = DB::table('trx_batchjob_register')
                        ->where('id_perusahaan', $reg->id_perusahaan)
                        ->count();
                    if ($otherCount === 0) {
                        DB::table('m_pelanggan')
                            ->where('id_perusahaan', $reg->id_perusahaan)
                            ->delete();
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

            return $this->redirectBackToPendaftaran("Data pendaftaran {$nomorInternet} beserta file foto/dokumen fisiknya berhasil dihapus.");

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::rollBack();
            return back()->withErrors(['error' => self::formatDbErrorMessage($e, 'menghapus data pendaftaran')]);
        }
    }

    /**
     * Hapus seluruh file foto & dokumen fisik dari storage disk server
     */
    private function deletePhysicalFilesForRegister($nomorInternet)
    {
        $reg = DB::table('trx_batchjob_register')->where('nomor_internet', $nomorInternet)->first();
        $instalasi = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
        
        $pelanggan = null;
        if ($reg && !empty($reg->nik_penduduk)) {
            $otherCount = DB::table('trx_batchjob_register')
                ->where('nik_penduduk', $reg->nik_penduduk)
                ->where('nomor_internet', '!=', $nomorInternet)
                ->count();
            if ($otherCount === 0) {
                $pelanggan = DB::table('m_pelanggan')->where('nik_penduduk', $reg->nik_penduduk)->first();
            }
        } elseif ($reg && !empty($reg->id_perusahaan)) {
            $otherCount = DB::table('trx_batchjob_register')
                ->where('id_perusahaan', $reg->id_perusahaan)
                ->where('nomor_internet', '!=', $nomorInternet)
                ->count();
            if ($otherCount === 0) {
                $pelanggan = DB::table('m_pelanggan')->where('id_perusahaan', $reg->id_perusahaan)->first();
            }
        }

        $filePaths = [];

        // 1. Ambil dari trx_batchjob_register
        if ($reg) {
            foreach (['foto_po', 'foto_bangunan', 'scan_dokumen', 'foto_ktp', 'foto_rumah'] as $col) {
                if (!empty($reg->$col)) {
                    $filePaths[] = $reg->$col;
                }
            }
        }

        // 2. Ambil dari trx_instalasi
        if ($instalasi) {
            foreach ([
                'foto_po', 'foto_bangunan', 'foto_peta', 'doc_instalasi', 'foto_odp',
                'foto_redaman', 'foto_penarikan', 'foto_penyambungan', 'foto_sn_modem',
                'foto_redaman_modem', 'foto_speedtest', 'foto_lokasi_modem', 'foto_rumah', 'foto_rumah_depan'
            ] as $col) {
                if (!empty($instalasi->$col)) {
                    $filePaths[] = $instalasi->$col;
                }
            }
        }

        // 3. Ambil dari m_pelanggan
        if ($pelanggan) {
            foreach (['foto_ktp', 'foto_rumah', 'foto_po', 'foto_bangunan'] as $col) {
                if (!empty($pelanggan->$col)) {
                    $filePaths[] = $pelanggan->$col;
                }
            }
        }

        // 4. Ambil dari bukti bayar billing
        $billRegs = DB::table('trx_billing_registrasi')->where('nomor_internet', $nomorInternet)->get();
        foreach ($billRegs as $br) {
            if (!empty($br->bukti_bayar)) $filePaths[] = $br->bukti_bayar;
            if (!empty($br->file_bukti)) $filePaths[] = $br->file_bukti;
        }

        $billLays = DB::table('trx_billing_layanan')->where('nomor_internet', $nomorInternet)->get();
        foreach ($billLays as $bl) {
            if (!empty($bl->bukti_bayar)) $filePaths[] = $bl->bukti_bayar;
            if (!empty($bl->file_bukti)) $filePaths[] = $bl->file_bukti;
        }

        // 5. Hapus semua file secara permanen
        foreach (array_unique($filePaths) as $path) {
            $this->removePhysicalFile($path);
        }
    }

    /**
     * Helper menghapus file fisik di public_path & storage_path
     */
    private function removePhysicalFile(?string $path)
    {
        if (empty($path)) return;

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'storage/')) {
            $relPath = substr($cleanPath, 8);
            if (Storage::disk('public')->exists($relPath)) {
                Storage::disk('public')->delete($relPath);
            }
            $absStorage = storage_path('app/public/' . $relPath);
            if (file_exists($absStorage) && is_file($absStorage)) {
                @unlink($absStorage);
            }
        }

        $absPublic = public_path($cleanPath);
        if (file_exists($absPublic) && is_file($absPublic)) {
            @unlink($absPublic);
        }
    }

    // ============================================
    // REPORT INSTALASI - Form & Update Report Instalasi
    // ============================================
    public function reportInstalasi($nomorInternet)
    {
        $customer = DB::table('view_batchjob')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        if (!$customer) {
            return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
        }

        // Ambil data instalasi existing
        $instalasi = DB::table('trx_instalasi')
            ->where('nomor_internet', $nomorInternet)
            ->first();

        // Ambil daftar teknisi lapangan khusus untuk team instalasi
        $targetTeknisNames = [
            'Abdul Ghani',
            'Dede',
            'Dika',
            'Dodi Sodikin',
            'Cristian',
            'Iyan sofian',
            'Fadil',
            'M Ryan Septiadi',
            'Sandi',
            'Dudi',
            'Dandi',
            'Reza Apriant',
        ];

        $existingTeknis = DB::table('tb_m_karyawan')
            ->whereIn('status_aktif', ['1', '01'])
            ->where(function ($q) use ($targetTeknisNames) {
                foreach ($targetTeknisNames as $name) {
                    $q->orWhere('nama_karyawan', 'LIKE', '%' . $name . '%');
                }
            })
            ->get(['kode_karyawan', 'nama_karyawan']);

        $teamList = collect($targetTeknisNames)->map(function ($targetName) use ($existingTeknis) {
            $found = $existingTeknis->first(function ($item) use ($targetName) {
                return strcasecmp(trim($item->nama_karyawan), trim($targetName)) === 0
                    || stripos($item->nama_karyawan, $targetName) !== false;
            });

            return (object)[
                'kode_karyawan' => $found ? $found->kode_karyawan : 'KRY-' . strtoupper(Str::slug($targetName)),
                'nama_karyawan' => $found ? $found->nama_karyawan : $targetName,
            ];
        });

        // Team yang terpilih sebelumnya
        $selectedTeams = [];
        if ($instalasi && !empty($instalasi->instalasi_team)) {
            $selectedTeams = array_map('trim', explode(',', $instalasi->instalasi_team));
        } else {
            $dbTeams = DB::table('trx_instalasi_team')
                ->where('nomor_internet', $nomorInternet)
                ->pluck('nama_karyawan')
                ->toArray();
            if (!empty($dbTeams)) {
                $selectedTeams = array_map('trim', $dbTeams);
            }
        }

        // Ambil daftar barang + jenis barang (satuan)
        $barangList = DB::table('m_barang as b')
            ->leftJoin('m_jns_barang as jb', 'jb.kode_jns_barang', '=', 'b.kode_jns_barang')
            ->where(function ($q) {
                $q->where('b.hide', '0')->orWhereNull('b.hide');
            })
            ->select('b.kode_barang', 'b.nama_barang', 'b.tipe_barang', 'jb.satuan', 'jb.nama_jns_barang')
            ->orderBy('b.nama_barang')
            ->get()
            ->map(function ($item) {
                $nama = strtoupper(trim($item->nama_barang ?? ''));
                $tipe = strtoupper(trim($item->tipe_barang ?? ''));
                $kode = trim($item->kode_barang ?? '');
                if ($kode === 'BR003' || $nama === 'HUAWEI') {
                    $item->nama_barang = 'ONU HUAWEI';
                } elseif ($kode === 'BR013' || ($nama === 'ZTE' && str_contains($tipe, 'F660'))) {
                    $item->nama_barang = 'ONU ZTE F660';
                } elseif ($kode === 'BR011' || ($nama === 'ZTE' && str_contains($tipe, 'F609 V3'))) {
                    $item->nama_barang = 'ONU ZTE F609 V3';
                } elseif ($kode === 'BR004' || $nama === 'ZTE') {
                    $item->nama_barang = 'ONU ZTE';
                }
                return $item;
            });

        // Ambil barang yang sudah terpasang
        $installedBarang = DB::table('trx_instalasi_barang as ib')
            ->leftJoin('m_barang as b', 'b.kode_barang', '=', 'ib.kode_barang')
            ->leftJoin('m_jns_barang as jb', 'jb.kode_jns_barang', '=', 'b.kode_jns_barang')
            ->where('ib.nomor_internet', $nomorInternet)
            ->where(function ($q) {
                $q->where('ib.hide', '0')->orWhereNull('ib.hide');
            })
            ->select('ib.*', 'b.nama_barang', 'b.tipe_barang', 'jb.satuan')
            ->get()
            ->map(function ($item) {
                $nama = strtoupper(trim($item->nama_barang ?? ''));
                $tipe = strtoupper(trim($item->tipe_barang ?? ''));
                $kode = trim($item->kode_barang ?? '');
                if ($kode === 'BR003' || $nama === 'HUAWEI') {
                    $item->nama_barang = 'ONU HUAWEI';
                } elseif ($kode === 'BR013' || ($nama === 'ZTE' && str_contains($tipe, 'F660'))) {
                    $item->nama_barang = 'ONU ZTE F660';
                } elseif ($kode === 'BR011' || ($nama === 'ZTE' && str_contains($tipe, 'F609 V3'))) {
                    $item->nama_barang = 'ONU ZTE F609 V3';
                } elseif ($kode === 'BR004' || $nama === 'ZTE') {
                    $item->nama_barang = 'ONU ZTE';
                }
                return $item;
            });

        return view('pendaftaran.report-instalasi', compact(
            'customer', 'instalasi', 'teamList', 'selectedTeams', 'barangList', 'installedBarang'
        ));
    }

    public function updateReportInstalasi(Request $request, $nomorInternet)
    {
        $request->validate([
            'instalasi_date_finish' => 'nullable|date',
            'instalasi_note_finish' => 'nullable|string|max:1000',
            'teams' => 'nullable|array',
            'foto_mapping' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'items' => 'nullable|array',
            'items.*.kode_barang' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            // Upload foto mapping jika ada
            $fotoPath = null;
            if ($request->hasFile('foto_mapping')) {
                $oldPeta = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->value('foto_peta');
                if ($oldPeta) $this->removePhysicalFile($oldPeta);

                $fotoPath = 'storage/' . $request->file('foto_mapping')->store('foto_peta', 'public');
            }

            // Susun string team
            $teamsArr = $request->input('teams', []);
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            // Handle Reschedule
            $isReschedule = $request->has('is_reschedule');
            $rescheduleDate = $request->input('reschedule_date');

            // Cek / update / insert trx_instalasi
            $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();

            $instalasiData = [
                'instalasi_date_start' => $isReschedule && $rescheduleDate ? $rescheduleDate : ($request->instalasi_date_finish ?: now()->toDateString()),
                'instalasi_date_finish' => $request->instalasi_date_finish,
                'instalasi_note_finish' => $request->instalasi_note_finish,
                'instalasi_team' => $teamString,
                'user_update' => session('user.username') ?? 'system',
                'date_update' => now(),
            ];

            if ($fotoPath) {
                $instalasiData['foto_peta'] = $fotoPath;
                $instalasiData['doc_instalasi'] = $fotoPath;
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            if ($instalasiEksis) {
                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($instalasiData);
            } else {
                DB::table('trx_instalasi')->insert(array_merge($instalasiData, [
                    'kode_instalasi' => 'INST-' . now()->format('ymdHis'),
                    'nomor_internet' => $nomorInternet,
                    'user_create' => $currentUser,
                    'date_create' => now(),
                    'hide' => '0',
                ]));
            }

            // Sync trx_instalasi_team (kat_team = 11)
            DB::table('trx_instalasi_team')
                ->where('nomor_internet', $nomorInternet)
                ->where('kat_team', '11')
                ->delete();

            if (!empty($teamsArr)) {
                $uniqueTeams = array_unique($teamsArr);
                $karyawanData = DB::table('tb_m_karyawan')
                    ->whereIn('nama_karyawan', $uniqueTeams)
                    ->get()
                    ->keyBy('nama_karyawan');

                foreach ($uniqueTeams as $tmName) {
                    $kr = $karyawanData[$tmName] ?? null;
                    $kodeTeam = $nomorInternet . '-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                    DB::table('trx_instalasi_team')->updateOrInsert(
                        ['kode_instalasi_team' => $kodeTeam],
                        [
                            'nomor_internet' => $nomorInternet,
                            'kat_team' => '11',
                            'kode_karyawan' => $kr ? $kr->kode_karyawan : '',
                            'nama_karyawan' => $tmName,
                            'user_create' => $currentUser,
                            'date_create' => now(),
                            'date_update' => now(),
                            'user_update' => $currentUser,
                            'hide' => '0',
                        ]
                    );
                }
            }

            // Sync trx_instalasi_barang
            DB::table('trx_instalasi_barang')
                ->where('nomor_internet', $nomorInternet)
                ->delete();

            $items = $request->input('items', []);
            $processedItems = [];
            foreach ($items as $it) {
                if (!empty($it['kode_barang']) && !empty($it['jumlah'])) {
                    $kBarang = $it['kode_barang'];
                    $jml = (int) $it['jumlah'];
                    if (isset($processedItems[$kBarang])) {
                        $processedItems[$kBarang] += $jml;
                    } else {
                        $processedItems[$kBarang] = $jml;
                    }
                }
            }

            foreach ($processedItems as $kBarang => $jml) {
                $kodeInstBarang = $nomorInternet . '-' . $kBarang;
                DB::table('trx_instalasi_barang')->updateOrInsert(
                    ['kode_inst_barang' => $kodeInstBarang],
                    [
                        'nomor_internet' => $nomorInternet,
                        'kode_barang' => $kBarang,
                        'jumlah_barang' => $jml,
                        'status_instalasi_barang' => '11',
                        'note_instalasi_barang' => $request->instalasi_note_finish ?? 'Report Instalasi',
                        'user_create' => $currentUser,
                        'date_create' => now(),
                        'date_update' => now(),
                        'user_update' => $currentUser,
                        'hide' => '0',
                    ]
                );
            }

            // Catat log registrasi
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-' . now()->format('ymdHis'),
                'nomor_internet' => $nomorInternet,
                'status_reg' => $customer->status_reg ?? '12',
                'date_schedule' => $request->instalasi_date_finish ?: now()->toDateString(),
                'note_schedule' => 'Report Instalasi updated: ' . ($request->instalasi_note_finish ?: 'Instalasi selesai'),
                'user_create' => $currentUser,
                'date_create' => now(),
                'hide' => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Report Instalasi untuk {$nomorInternet} berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan Report Instalasi')]);
        }
    }

    // ============================================
    // JADWAL AKTIVASI - NOC mengatur jadwal aktivasi
    // ============================================
    public function jadwalAktivasi(Request $request, $nomorInternet)
    {
        // Hanya NOC yang boleh akses
        $u = session('user', []);
        $isNoc = (strtoupper($u['level'] ?? '') === 'NOC' || ($u['kode_level'] ?? '') === 'lv68132');
        if (!$isNoc) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk mengatur jadwal aktivasi.']);
        }

        $validated = $request->validate([
            'aktivasi_date_start' => 'required|date',
            'aktivasi_time' => 'nullable|string|max:50',
            'aktivasi_team' => 'nullable|array',
            'kode_pop' => 'nullable|string|max:50',
            'media_akses' => 'nullable|string|max:50',
            'index_olt' => 'nullable|string|max:50',
            'aktivasi_note' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.kode_barang' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
        ], [
            'aktivasi_date_start.required' => 'Jadwal Aktivasi wajib diisi.',
            'aktivasi_date_start.date' => 'Format tanggal aktivasi tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            // Validasi: Aktivasi NOC hanya bisa diproses jika seluruh proses teknik (Report Instalasi) telah selesai
            $instalasiCheck = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
            $isReportDone = !empty($instalasiCheck->instalasi_date_finish) 
                || (!empty($instalasiCheck->instalasi_team) && !empty($instalasiCheck->instalasi_note_finish))
                || $customer->status_reg == '15'
                || str_contains(strtoupper($customer->desc_registrasi ?? ''), 'SELESAI INSTALASI');

            if (!$isReportDone) {
                return back()->withErrors(['error' => 'Jadwal Aktivasi hanya dapat dilakukan setelah seluruh proses di teknik (Report Instalasi) selesai.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Susun string team
            $teamsArr = $validated['aktivasi_team'] ?? [];
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            // Pastikan status registrasi '14' (Jadwal Aktivasi Terbit) ada di master
            DB::table('m_status_registrasi')->updateOrInsert(
                ['status_reg' => '14'],
                [
                    'desc_registrasi' => 'Jadwal Aktivasi Terbit',
                    'date_create' => now(),
                    'user_create' => 'SYSTEM',
                    'hide' => '0'
                ]
            );

            // Validasi & pastikan master m_pop terisi jika ada
            $kodePop = !empty($validated['kode_pop']) ? $validated['kode_pop'] : null;
            if ($kodePop) {
                DB::table('m_pop')->updateOrInsert(
                    ['kode_pop' => $kodePop],
                    [
                        'nama_pop'    => $kodePop,
                        'date_create' => now(),
                        'user_create' => 'SYSTEM',
                        'hide'        => '0'
                    ]
                );
            }

            // Update status_reg = '14' dan data teknis di trx_batchjob_register
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'status_reg' => '14',
                    'kode_pop' => $kodePop,
                    'media_akses' => !empty($validated['media_akses']) ? $validated['media_akses'] : null,
                    'index_olt' => !empty($validated['index_olt']) ? $validated['index_olt'] : null,
                    'user_update' => substr($currentUser, 0, 20),
                    'date_update' => now(),
                ]);

            // Update atau insert trx_instalasi
            $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();

            $instalasiData = [
                'aktivasi_date_start' => $validated['aktivasi_date_start'],
                'aktivasi_time' => $validated['aktivasi_time'] ?? null,
                'aktivasi_team' => $teamString,
                'aktivasi_note' => $validated['aktivasi_note'] ?? null,
                'user_update' => substr($currentUser, 0, 20),
                'date_update' => now(),
            ];

            if ($instalasiEksis) {
                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($instalasiData);
            } else {
                DB::table('trx_instalasi')->insert(array_merge($instalasiData, [
                    'kode_instalasi' => 'INST-' . $nomorInternet,
                    'nomor_internet' => $nomorInternet,
                    'user_create' => substr($currentUser, 0, 20),
                    'date_create' => now(),
                    'hide' => '0',
                ]));
            }

            // Sync trx_instalasi_team
            DB::table('trx_instalasi_team')
                ->where('nomor_internet', $nomorInternet)
                ->where('kat_team', '11')
                ->delete();

            if (!empty($teamsArr)) {
                DB::table('m_kat_team')->updateOrInsert(
                    ['kat_team' => '11'],
                    ['desc_kat_team' => 'Team Aktivasi NOC', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                $uniqueTeams = array_unique($teamsArr);
                $karyawanData = DB::table('tb_m_karyawan')
                    ->whereIn('nama_karyawan', $uniqueTeams)
                    ->get()
                    ->keyBy('nama_karyawan');

                foreach ($uniqueTeams as $tmName) {
                    $kr = $karyawanData[$tmName] ?? null;
                    $kodeTeam = $nomorInternet . '-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                    DB::table('trx_instalasi_team')->updateOrInsert(
                        ['kode_instalasi_team' => $kodeTeam],
                        [
                            'nomor_internet' => $nomorInternet,
                            'kat_team' => '11',
                            'kode_karyawan' => $kr ? $kr->kode_karyawan : null,
                            'nama_karyawan' => $tmName,
                            'user_create' => substr($currentUser, 0, 20),
                            'date_create' => now(),
                            'date_update' => now(),
                            'user_update' => substr($currentUser, 0, 20),
                            'hide' => '0',
                        ]
                    );
                }
            }

            // Sync trx_instalasi_barang (Perangkat/Peralatan)
            DB::table('trx_instalasi_barang')
                ->where('nomor_internet', $nomorInternet)
                ->delete();

            DB::table('m_status_instalasi_barang')->updateOrInsert(
                ['status_instalasi_barang' => '11'],
                ['desc_status_instalasi_barang' => 'Terpasang', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            $items = $request->input('items', []);
            $processedItems = [];
            foreach ($items as $it) {
                if (!empty($it['kode_barang']) && !empty($it['jumlah'])) {
                    $kBarang = $it['kode_barang'];
                    $jml = (int) $it['jumlah'];
                    if (isset($processedItems[$kBarang])) {
                        $processedItems[$kBarang] += $jml;
                    } else {
                        $processedItems[$kBarang] = $jml;
                    }
                }
            }

            foreach ($processedItems as $kBarang => $jml) {
                $bExists = DB::table('m_barang')->where('kode_barang', $kBarang)->exists();
                if (!$bExists) continue;

                $kodeInstBarang = $nomorInternet . '-' . $kBarang;
                DB::table('trx_instalasi_barang')->updateOrInsert(
                    ['kode_inst_barang' => $kodeInstBarang],
                    [
                        'nomor_internet' => $nomorInternet,
                        'kode_barang' => $kBarang,
                        'jumlah_barang' => $jml,
                        'status_instalasi_barang' => '11',
                        'note_instalasi_barang' => $validated['aktivasi_note'] ?? 'Aktivasi NOC',
                        'user_create' => substr($currentUser, 0, 20),
                        'date_create' => now(),
                        'date_update' => now(),
                        'user_update' => substr($currentUser, 0, 20),
                        'hide' => '0',
                    ]
                );
            }

            // Catat log registrasi
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-AKT-' . now()->format('ymdHis'),
                'nomor_internet' => $nomorInternet,
                'status_reg' => '14',
                'date_schedule' => $validated['aktivasi_date_start'],
                'note_schedule' => 'Jadwal Aktivasi: ' . $validated['aktivasi_date_start']
                    . ($validated['aktivasi_time'] ? ' ' . $validated['aktivasi_time'] : '')
                    . ($teamString ? ' | Tim: ' . $teamString : '')
                    . (!empty($validated['aktivasi_note']) ? ' | ' . $validated['aktivasi_note'] : ''),
                'user_create' => substr($currentUser, 0, 20),
                'date_create' => now(),
                'hide' => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Jadwal Aktivasi untuk {$customer->nama_pelanggan} ({$nomorInternet}) berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan jadwal aktivasi')]);
        }
    }

    public function getBarangSatuan(Request $request)
    {
        $kodeBarang = $request->query('kode_barang');
        $barang = DB::table('m_barang as b')
            ->leftJoin('m_jns_barang as jb', 'jb.kode_jns_barang', '=', 'b.kode_jns_barang')
            ->where('b.kode_barang', $kodeBarang)
            ->select('b.kode_barang', 'b.nama_barang', 'b.tipe_barang', 'jb.satuan')
            ->first();

        return response()->json($barang);
    }

    public function jadwalSurvey(Request $request, $nomorInternet)
    {
        $validated = $request->validate([
            'survey_date_start' => 'required|date',
            'survey_time'       => 'required|string',
            'survey_note'       => 'required|string',
            'foto_mapping'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'teams'             => 'nullable|array',
        ], [
            'survey_date_start.required' => 'Tanggal Survey wajib diisi.',
            'survey_time.required'       => 'Waktu Survey wajib dipilih.',
            'survey_note.required'       => 'Catatan Survey wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            // Upload foto mapping jika ada
            $fotoPath = null;
            if ($request->hasFile('foto_mapping')) {
                $oldPeta = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->value('foto_peta');
                if ($oldPeta) $this->removePhysicalFile($oldPeta);

                $fotoPath = 'storage/' . $request->file('foto_mapping')->store('foto_peta', 'public');
            }

            $teamsArr = $request->input('teams', []);
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            // Update status_reg = '13' (Jadwal Survey Terbit)
            DB::table('m_status_registrasi')->updateOrInsert(
                ['status_reg' => '13'],
                [
                    'desc_registrasi' => 'Jadwal Survey Terbit',
                    'date_create'     => now(),
                    'user_create'     => 'SYSTEM',
                    'hide'            => '0'
                ]
            );

            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'status_reg'  => '13',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            // Update / insert trx_instalasi
            $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();

            $surveyData = [
                'survey_date_start' => $validated['survey_date_start'],
                'survey_time'       => $validated['survey_time'],
                'survey_note'       => $validated['survey_note'],
                'survey_team'       => $teamString,
                'user_update'       => $currentUser,
                'date_update'       => now(),
            ];

            if ($fotoPath) {
                $surveyData['doc_survey'] = $fotoPath;
                $surveyData['foto_peta']   = $fotoPath;
            }

            if ($instalasiEksis) {
                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($surveyData);
            } else {
                DB::table('trx_instalasi')->insert(array_merge($surveyData, [
                    'kode_instalasi' => 'INST-' . now()->format('ymdHis'),
                    'nomor_internet' => $nomorInternet,
                    'user_create'    => $currentUser,
                    'date_create'    => now(),
                    'hide'           => '0',
                ]));
            }

            // Sync team survey (kat_team = '10')
            if (!empty($teamsArr)) {
                DB::table('m_kat_team')->updateOrInsert(
                    ['kat_team' => '10'],
                    ['desc_kat_team' => 'Team Survey', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                $uniqueTeams = array_unique($teamsArr);
                $karyawanData = DB::table('tb_m_karyawan')
                    ->whereIn('nama_karyawan', $uniqueTeams)
                    ->get()
                    ->keyBy('nama_karyawan');

                foreach ($uniqueTeams as $tmName) {
                    $kr = $karyawanData[$tmName] ?? null;
                    $kodeTeam = $nomorInternet . '-SURVEY-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                    DB::table('trx_instalasi_team')->updateOrInsert(
                        ['kode_instalasi_team' => $kodeTeam],
                        [
                            'nomor_internet' => $nomorInternet,
                            'kat_team'       => '10',
                            'kode_karyawan'  => $kr ? $kr->kode_karyawan : '',
                            'nama_karyawan'  => $tmName,
                            'user_create'    => $currentUser,
                            'date_create'    => now(),
                            'date_update'    => now(),
                            'user_update'    => $currentUser,
                            'hide'           => '0',
                        ]
                    );
                }
            }

            // Catat Log
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-SRV-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => '13',
                'date_schedule'             => $validated['survey_date_start'],
                'note_schedule'             => 'Jadwal Survey: ' . $validated['survey_date_start'] . ' ' . $validated['survey_time'] . ' | ' . $validated['survey_note'],
                'user_create'               => $currentUser,
                'date_create'               => now(),
                'hide'                       => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Jadwal Survey untuk {$customer->nama_pelanggan} ({$nomorInternet}) berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan Jadwal Survey')]);
        }
    }

    public function updateReportSurvey(Request $request, $nomorInternet)
    {
        $isReschedule = $request->boolean('is_reschedule');

        if ($isReschedule) {
            $validated = $request->validate([
                'reschedule_date' => 'required|date',
                'reschedule_time' => 'nullable|string',
                'reschedule_note' => 'nullable|string',
                'teams'           => 'nullable|array',
            ], [
                'reschedule_date.required' => 'Tanggal reschedule wajib diisi.',
            ]);
        } else {
            $validated = $request->validate([
                'survey_date_finish' => 'required|date',
                'survey_note_finish' => 'required|string',
                'bisa_pasang'         => 'required|in:0,1',
                'foto_mapping'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'teams'              => 'nullable|array',
                'items'              => 'nullable|array',
            ], [
                'survey_date_finish.required' => 'Tanggal selesai survey wajib diisi.',
                'survey_note_finish.required' => 'Catatan selesai survey wajib diisi.',
                'bisa_pasang.required'         => 'Pilihan bisa dilakukan pemasangan wajib dipilih.',
            ]);
        }

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');
            $teamsArr = $request->input('teams', []);
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            if ($isReschedule) {
                // Reschedule Survey
                DB::table('m_status_registrasi')->updateOrInsert(
                    ['status_reg' => '13.1'],
                    ['desc_registrasi' => 'Reschedule Survey', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                DB::table('trx_batchjob_register')
                    ->where('nomor_internet', $nomorInternet)
                    ->update([
                        'status_reg'  => '13.1',
                        'user_update' => $currentUser,
                        'date_update' => now(),
                    ]);

                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update([
                        'survey_date_start' => $validated['reschedule_date'],
                        'survey_time'       => $validated['reschedule_time'] ?? null,
                        'survey_note'       => $validated['reschedule_note'] ?? null,
                        'survey_team'       => $teamString,
                        'user_update'       => $currentUser,
                        'date_update'       => now(),
                    ]);

                $logMsg = 'Reschedule Survey: ' . $validated['reschedule_date'] . ' ' . ($validated['reschedule_time'] ?? '');
                $statusRegLog = '13.1';

            } else {
                // Upload foto mapping jika ada
                $fotoPath = null;
                if ($request->hasFile('foto_mapping')) {
                    $oldPeta = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->value('foto_peta');
                    if ($oldPeta) $this->removePhysicalFile($oldPeta);

                    $fotoPath = 'storage/' . $request->file('foto_mapping')->store('foto_peta', 'public');
                }

                $bisaPasang = (int) $validated['bisa_pasang'];
                $newStatusReg = $bisaPasang === 1 ? '13.2' : '17';

                $descReg = $bisaPasang === 1 ? 'Survey Done' : 'Batal Pasang';
                DB::table('m_status_registrasi')->updateOrInsert(
                    ['status_reg' => $newStatusReg],
                    ['desc_registrasi' => $descReg, 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                DB::table('trx_batchjob_register')
                    ->where('nomor_internet', $nomorInternet)
                    ->update([
                        'status_reg'  => $newStatusReg,
                        'user_update' => $currentUser,
                        'date_update' => now(),
                    ]);

                $surveyData = [
                    'survey_date_finish' => $validated['survey_date_finish'],
                    'survey_note_finish' => $validated['survey_note_finish'],
                    'survey_team'        => $teamString,
                    'user_update'        => $currentUser,
                    'date_update'        => now(),
                ];

                if ($fotoPath) {
                    $surveyData['doc_survey'] = $fotoPath;
                    $surveyData['foto_peta']   = $fotoPath;
                }

                $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
                if ($instalasiEksis) {
                    DB::table('trx_instalasi')
                        ->where('nomor_internet', $nomorInternet)
                        ->update($surveyData);
                } else {
                    DB::table('trx_instalasi')->insert(array_merge($surveyData, [
                        'kode_instalasi' => 'INST-' . now()->format('ymdHis'),
                        'nomor_internet' => $nomorInternet,
                        'user_create'    => $currentUser,
                        'date_create'    => now(),
                        'hide'           => '0',
                    ]));
                }

                // Sync barang yang digunakan
                DB::table('m_status_instalasi_barang')->updateOrInsert(
                    ['status_instalasi_barang' => '11'],
                    ['desc_status_instalasi_barang' => 'Terpasang', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                DB::table('trx_instalasi_barang')
                    ->where('nomor_internet', $nomorInternet)
                    ->delete();

                $items = $request->input('items', []);
                foreach ($items as $it) {
                    if (!empty($it['kode_barang']) && !empty($it['jumlah'])) {
                        $kBarang = $it['kode_barang'];
                        $jml = (int) $it['jumlah'];
                        $kodeInstBarang = $nomorInternet . '-SRV-' . $kBarang;
                        DB::table('trx_instalasi_barang')->updateOrInsert(
                            ['kode_inst_barang' => $kodeInstBarang],
                            [
                                'nomor_internet'          => $nomorInternet,
                                'kode_barang'             => $kBarang,
                                'jumlah_barang'           => $jml,
                                'status_instalasi_barang' => '11',
                                'note_instalasi_barang'   => 'Report Survey',
                                'user_create'             => $currentUser,
                                'date_create'             => now(),
                                'date_update'             => now(),
                                'user_update'             => $currentUser,
                                'hide'                    => '0',
                            ]
                        );
                    }
                }

                $logMsg = 'Report Survey Completed: ' . $validated['survey_note_finish'] . ($bisaPasang === 1 ? ' (Bisa Pasang)' : ' (Tidak Bisa Pasang)');
                $statusRegLog = $newStatusReg;
            }

            // Sync team survey (kat_team = '10')
            if (!empty($teamsArr)) {
                DB::table('m_kat_team')->updateOrInsert(
                    ['kat_team' => '10'],
                    ['desc_kat_team' => 'Team Survey', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                DB::table('trx_instalasi_team')
                    ->where('nomor_internet', $nomorInternet)
                    ->where('kat_team', '10')
                    ->delete();

                $uniqueTeams = array_unique($teamsArr);
                $karyawanData = DB::table('tb_m_karyawan')
                    ->whereIn('nama_karyawan', $uniqueTeams)
                    ->get()
                    ->keyBy('nama_karyawan');

                foreach ($uniqueTeams as $tmName) {
                    $kr = $karyawanData[$tmName] ?? null;
                    $kodeTeam = $nomorInternet . '-SURVEY-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                    DB::table('trx_instalasi_team')->updateOrInsert(
                        ['kode_instalasi_team' => $kodeTeam],
                        [
                            'nomor_internet' => $nomorInternet,
                            'kat_team'       => '10',
                            'kode_karyawan'  => $kr ? $kr->kode_karyawan : '',
                            'nama_karyawan'  => $tmName,
                            'user_create'    => $currentUser,
                            'date_create'    => now(),
                            'date_update'    => now(),
                            'user_update'    => $currentUser,
                            'hide'           => '0',
                        ]
                    );
                }
            }

            // Log
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-RSRV-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => $statusRegLog,
                'date_schedule'             => now()->toDateString(),
                'note_schedule'             => $logMsg,
                'user_create'               => $currentUser,
                'date_create'               => now(),
                'hide'                       => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Report Survey untuk {$customer->nama_pelanggan} ({$nomorInternet}) berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan Report Survey')]);
        }
    }

    public function jadwalInstalasi(Request $request, $nomorInternet)
    {
        $validated = $request->validate([
            'instalasi_date_start' => 'required|date',
            'instalasi_time'       => 'required|string',
            'instalasi_note'       => 'required|string',
            'foto_mapping'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'teams'                => 'nullable|array',
            'items'                => 'nullable|array',
        ], [
            'instalasi_date_start.required' => 'Tanggal Instalasi wajib diisi.',
            'instalasi_time.required'       => 'Waktu Instalasi wajib dipilih.',
            'instalasi_note.required'       => 'Catatan Pemasangan wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            // Upload foto mapping jika ada
            $fotoPath = null;
            if ($request->hasFile('foto_mapping')) {
                $oldPeta = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->value('foto_peta');
                if ($oldPeta) $this->removePhysicalFile($oldPeta);

                $fotoPath = 'storage/' . $request->file('foto_mapping')->store('foto_peta', 'public');
            }

            $teamsArr = $request->input('teams', []);
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            // Status '14' = Jadwal Instalasi Terbit
            DB::table('m_status_registrasi')->updateOrInsert(
                ['status_reg' => '14'],
                ['desc_registrasi' => 'Jadwal Instalasi Terbit', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->update([
                    'status_reg'  => '14',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            $instalasiData = [
                'instalasi_date_start' => $validated['instalasi_date_start'],
                'instalasi_time'       => $validated['instalasi_time'],
                'instalasi_note'       => $validated['instalasi_note'],
                'instalasi_team'       => $teamString,
                'user_update'          => $currentUser,
                'date_update'          => now(),
            ];

            if ($fotoPath) {
                $instalasiData['doc_instalasi'] = $fotoPath;
                $instalasiData['foto_peta']      = $fotoPath;
            }

            $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
            if ($instalasiEksis) {
                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($instalasiData);
            } else {
                DB::table('trx_instalasi')->insert(array_merge($instalasiData, [
                    'kode_instalasi' => 'INST-' . now()->format('ymdHis'),
                    'nomor_internet' => $nomorInternet,
                    'user_create'    => $currentUser,
                    'date_create'    => now(),
                    'hide'           => '0',
                ]));
            }

            // Sync team instalasi (kat_team = '11')
            if (!empty($teamsArr)) {
                DB::table('m_kat_team')->updateOrInsert(
                    ['kat_team' => '11'],
                    ['desc_kat_team' => 'Team Instalasi', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                DB::table('trx_instalasi_team')
                    ->where('nomor_internet', $nomorInternet)
                    ->where('kat_team', '11')
                    ->delete();

                $uniqueTeams = array_unique($teamsArr);
                $karyawanData = DB::table('tb_m_karyawan')
                    ->whereIn('nama_karyawan', $uniqueTeams)
                    ->get()
                    ->keyBy('nama_karyawan');

                foreach ($uniqueTeams as $tmName) {
                    $kr = $karyawanData[$tmName] ?? null;
                    $kodeTeam = $nomorInternet . '-INST-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                    DB::table('trx_instalasi_team')->updateOrInsert(
                        ['kode_instalasi_team' => $kodeTeam],
                        [
                            'nomor_internet' => $nomorInternet,
                            'kat_team'       => '11',
                            'kode_karyawan'  => $kr ? $kr->kode_karyawan : '',
                            'nama_karyawan'  => $tmName,
                            'user_create'    => $currentUser,
                            'date_create'    => now(),
                            'date_update'    => now(),
                            'user_update'    => $currentUser,
                            'hide'           => '0',
                        ]
                    );
                }
            }

            // Sync barang
            DB::table('m_status_instalasi_barang')->updateOrInsert(
                ['status_instalasi_barang' => '11'],
                ['desc_status_instalasi_barang' => 'Terpasang', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
            );

            DB::table('trx_instalasi_barang')
                ->where('nomor_internet', $nomorInternet)
                ->delete();

            $items = $request->input('items', []);
            foreach ($items as $it) {
                if (!empty($it['kode_barang']) && !empty($it['jumlah'])) {
                    $kBarang = $it['kode_barang'];
                    $jml = (int) $it['jumlah'];
                    $kodeInstBarang = $nomorInternet . '-INST-' . $kBarang;
                    DB::table('trx_instalasi_barang')->updateOrInsert(
                        ['kode_inst_barang' => $kodeInstBarang],
                        [
                            'nomor_internet'          => $nomorInternet,
                            'kode_barang'             => $kBarang,
                            'jumlah_barang'           => $jml,
                            'status_instalasi_barang' => '11',
                            'note_instalasi_barang'   => 'Jadwal Instalasi',
                            'user_create'             => $currentUser,
                            'date_create'             => now(),
                            'date_update'             => now(),
                            'user_update'             => $currentUser,
                            'hide'                    => '0',
                        ]
                    );
                }
            }

            // Log
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-INST-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => '14',
                'date_schedule'             => $validated['instalasi_date_start'],
                'note_schedule'             => 'Jadwal Instalasi: ' . $validated['instalasi_date_start'] . ' ' . $validated['instalasi_time'] . ' | ' . $validated['instalasi_note'],
                'user_create'               => $currentUser,
                'date_create'               => now(),
                'hide'                       => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Jadwal Instalasi untuk {$customer->nama_pelanggan} ({$nomorInternet}) berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan Jadwal Instalasi')]);
        }
    }

    public function updateReportAktivasi(Request $request, $nomorInternet)
    {
        $isReschedule = $request->filled('is_reschedule') && in_array((string) $request->input('is_reschedule'), ['1', 'true', 'on'], true);

        if ($isReschedule) {
            $validated = $request->validate([
                'reschedule_date' => 'required|date',
                'reschedule_time' => 'nullable|string',
                'reschedule_note' => 'nullable|string',
                'teams'           => 'nullable|array',
            ], [
                'reschedule_date.required' => 'Tanggal reschedule wajib diisi.',
            ]);
        } else {
            $validated = $request->validate([
                'aktivasi_date_finish' => 'required|date',
                'aktivasi_note_finish' => 'required|string',
                'kode_pop'             => 'nullable|string',
                'media_akses'          => 'nullable|string',
                'index_olt'            => 'nullable|string',
                'teams'                => 'nullable|array',
                'items'                => 'nullable|array',
            ], [
                'aktivasi_date_finish.required' => 'Tanggal selesai aktivasi wajib diisi.',
                'aktivasi_note_finish.required' => 'Catatan selesai aktivasi wajib diisi.',
            ]);
        }

        try {
            DB::beginTransaction();

            $customer = DB::table('trx_batchjob_register')
                ->where('nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return $this->redirectBackToPendaftaran('Data pendaftaran tidak ditemukan.', 'error');
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');
            $teamsArr = $request->input('teams', []);
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            if ($isReschedule) {
                $statusRegLog = '14.1';
                $logMsg = 'Reschedule Aktivasi: ' . $validated['reschedule_date'] . ' ' . ($validated['reschedule_time'] ?? '');

                $resData = [
                    'aktivasi_date_start' => $validated['reschedule_date'],
                    'aktivasi_time'       => $validated['reschedule_time'] ?? null,
                    'aktivasi_note'       => $validated['reschedule_note'] ?? null,
                    'user_update'         => $currentUser,
                    'date_update'         => now(),
                ];
                if (!empty($teamString)) {
                    $resData['aktivasi_team'] = $teamString;
                }

                DB::table('trx_instalasi')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($resData);

            } else {
                $newStatusReg = '16'; // 16 = Selesai Aktivasi / Aktivasi Done
                $descReg = 'Selesai Aktivasi';

                DB::table('m_status_registrasi')->updateOrInsert(
                    ['status_reg' => $newStatusReg],
                    ['desc_registrasi' => $descReg, 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                );

                $regUpdate = [
                    'status_reg'  => $newStatusReg,
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ];
                if (array_key_exists('kode_pop', $validated) && !empty($validated['kode_pop'])) {
                    $regUpdate['kode_pop'] = $validated['kode_pop'];
                    DB::table('m_pop')->updateOrInsert(
                        ['kode_pop' => $validated['kode_pop']],
                        [
                            'nama_pop'    => $validated['kode_pop'],
                            'date_create' => now(),
                            'user_create' => 'SYSTEM',
                            'hide'        => '0'
                        ]
                    );
                }
                if (array_key_exists('media_akses', $validated) && !empty($validated['media_akses'])) {
                    $regUpdate['media_akses'] = $validated['media_akses'];
                }
                if (array_key_exists('index_olt', $validated) && !empty($validated['index_olt'])) {
                    $regUpdate['index_olt'] = $validated['index_olt'];
                }

                DB::table('trx_batchjob_register')
                    ->where('nomor_internet', $nomorInternet)
                    ->update($regUpdate);

                $aktData = [
                    'aktivasi_date_finish' => $validated['aktivasi_date_finish'],
                    'aktivasi_note_finish' => $validated['aktivasi_note_finish'],
                    'user_update'          => $currentUser,
                    'date_update'          => now(),
                ];
                if (!empty($teamString)) {
                    $aktData['aktivasi_team'] = $teamString;
                }

                $instalasiEksis = DB::table('trx_instalasi')->where('nomor_internet', $nomorInternet)->first();
                if ($instalasiEksis) {
                    DB::table('trx_instalasi')
                        ->where('nomor_internet', $nomorInternet)
                        ->update($aktData);
                } else {
                    DB::table('trx_instalasi')->insert(array_merge($aktData, [
                        'kode_instalasi' => 'INST-' . now()->format('ymdHis'),
                        'nomor_internet' => $nomorInternet,
                        'user_create'    => $currentUser,
                        'date_create'    => now(),
                        'hide'           => '0',
                    ]));
                }

                // Sync team (kat_team = '22' -> Aktivasi Team)
                if (!empty($teamsArr)) {
                    DB::table('m_kat_team')->updateOrInsert(
                        ['kat_team' => '22'],
                        ['desc_kat_team' => 'Team Aktivasi', 'date_create' => now(), 'user_create' => 'SYSTEM', 'hide' => '0']
                    );

                    DB::table('trx_instalasi_team')
                        ->where('nomor_internet', $nomorInternet)
                        ->where('kat_team', '22')
                        ->delete();

                    $uniqueTeams = array_unique($teamsArr);
                    $karyawanData = DB::table('tb_m_karyawan')
                        ->whereIn('nama_karyawan', $uniqueTeams)
                        ->get()
                        ->keyBy('nama_karyawan');

                    foreach ($uniqueTeams as $tmName) {
                        $kr = $karyawanData[$tmName] ?? null;
                        $kodeTeam = $nomorInternet . '-AKT-' . ($kr ? $kr->kode_karyawan : Str::random(6));
                        DB::table('trx_instalasi_team')->updateOrInsert(
                            ['kode_instalasi_team' => $kodeTeam],
                            [
                                'nomor_internet' => $nomorInternet,
                                'kat_team'       => '22',
                                'kode_karyawan'  => $kr ? $kr->kode_karyawan : '',
                                'nama_karyawan'  => $tmName,
                                'user_create'    => $currentUser,
                                'date_create'    => now(),
                                'date_update'    => now(),
                                'user_update'    => $currentUser,
                                'hide'           => '0',
                            ]
                        );
                    }
                }

                // Sync barang
                $items = $request->input('items', []);
                if (!empty($items)) {
                    DB::table('trx_instalasi_barang')
                        ->where('nomor_internet', $nomorInternet)
                        ->delete();

                    foreach ($items as $it) {
                        if (!empty($it['kode_barang']) && !empty($it['jumlah'])) {
                            $kBarang = $it['kode_barang'];
                            $jml = (int) $it['jumlah'];
                            $kodeInstBarang = $nomorInternet . '-AKT-' . $kBarang;
                            DB::table('trx_instalasi_barang')->updateOrInsert(
                                ['kode_inst_barang' => $kodeInstBarang],
                                [
                                    'nomor_internet'          => $nomorInternet,
                                    'kode_barang'             => $kBarang,
                                    'jumlah_barang'           => $jml,
                                    'status_instalasi_barang' => '11',
                                    'note_instalasi_barang'   => 'Report Aktivasi',
                                    'user_create'             => $currentUser,
                                    'date_create'             => now(),
                                    'date_update'             => now(),
                                    'user_update'             => $currentUser,
                                    'hide'                    => '0',
                                ]
                            );
                        }
                    }
                }

                $logMsg = 'Report Aktivasi Selesai: ' . $validated['aktivasi_date_finish'] . ' | ' . $validated['aktivasi_note_finish'];
                $statusRegLog = '16';
            }

            // Log
            DB::table('trx_batchjob_register_log')->insert([
                'kode_batchjob_register_log' => 'L-' . $nomorInternet . '-RAKT-' . now()->format('ymdHis'),
                'nomor_internet'             => $nomorInternet,
                'status_reg'                 => $statusRegLog,
                'date_schedule'             => now()->toDateString(),
                'note_schedule'             => $logMsg,
                'user_create'               => $currentUser,
                'date_create'               => now(),
                'hide'                       => '0',
            ]);

            DB::commit();

            return $this->redirectBackToPendaftaran("Report Aktivasi untuk {$customer->nama_pelanggan} ({$nomorInternet}) berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => self::formatDbErrorMessage($e, 'menyimpan Report Aktivasi')]);
        }
    }

    // ============================================
    // HELPER & API GENERATE ID PERUSAHAAN (isp-nomorurutregistrasi-tahun)
    // ============================================
    public static function generateIdPerusahaan($year = null)
    {
        $year = $year ?: date('Y');
        if (strlen((string)$year) === 2) {
            $year = '20' . $year;
        }

        $pelangganIds = DB::table('m_pelanggan')
            ->whereNotNull('id_perusahaan')
            ->where(function ($q) use ($year) {
                $q->where('id_perusahaan', 'LIKE', "isp-%-{$year}")
                  ->orWhere('id_perusahaan', 'LIKE', "ISP-%-{$year}");
            })
            ->pluck('id_perusahaan');

        $trxIds = DB::table('trx_batchjob_register')
            ->whereNotNull('id_perusahaan')
            ->where(function ($q) use ($year) {
                $q->where('id_perusahaan', 'LIKE', "isp-%-{$year}")
                  ->orWhere('id_perusahaan', 'LIKE', "ISP-%-{$year}");
            })
            ->pluck('id_perusahaan');

        $allIds = $pelangganIds->concat($trxIds)->unique();

        $maxSeq = 0;
        foreach ($allIds as $idStr) {
            if (preg_match('/^isp-(\d+)-' . $year . '$/i', trim($idStr), $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $seqFormatted = sprintf('%03d', $nextSeq);
        $newId = "isp-{$seqFormatted}-{$year}";

        while (
            DB::table('m_pelanggan')->where('id_perusahaan', $newId)->exists() ||
            DB::table('trx_batchjob_register')->where('id_perusahaan', $newId)->exists()
        ) {
            $nextSeq++;
            $seqFormatted = sprintf('%03d', $nextSeq);
            $newId = "isp-{$seqFormatted}-{$year}";
        }

        return $newId;
    }

    public function generateIdPerusahaanApi(Request $request)
    {
        $year = $request->query('year') ?? date('Y');
        $id = self::generateIdPerusahaan($year);
        return response()->json([
            'success' => true,
            'id_perusahaan' => $id,
        ]);
    }

    // ============================================
    // API GET DETAIL PERUSAHAAN BY ID PERUSAHAAN (AUTO-FILL)
    // ============================================
    public function getPerusahaanDetail(Request $request)
    {
        $rawQuery = trim($request->query('id_perusahaan') ?? $request->query('q') ?? '');
        if (empty($rawQuery)) {
            return response()->json(['found' => false]);
        }

        $idOnly = $rawQuery;
        $nameOnly = $rawQuery;
        if (strpos($rawQuery, ' - ') !== false) {
            $parts = explode(' - ', $rawQuery, 2);
            $idOnly = trim($parts[0]);
            $nameOnly = trim($parts[1] ?? '');
        }

        $idOnlyLower = strtolower($idOnly);
        $rawQueryLower = strtolower($rawQuery);
        $nameOnlyLower = strtolower($nameOnly);

        // 1. Cari di m_pelanggan
        $p = DB::table('m_pelanggan')
            ->where(function ($q) use ($idOnlyLower, $rawQueryLower, $nameOnlyLower) {
                $q->whereRaw('LOWER(TRIM(nama_perusahaan)) = ?', [$rawQueryLower])
                  ->orWhereRaw('LOWER(TRIM(nama_perusahaan)) = ?', [$nameOnlyLower])
                  ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$rawQueryLower])
                  ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$idOnlyLower]);
            })
            ->first();

        // 2. Cari di trx_batchjob_register yang paling baru
        $trx = DB::table('trx_batchjob_register')
            ->where(function ($q) use ($idOnlyLower, $rawQueryLower, $nameOnlyLower) {
                $q->whereRaw('LOWER(TRIM(nama_pelanggan)) = ?', [$rawQueryLower])
                  ->orWhereRaw('LOWER(TRIM(nama_pelanggan)) = ?', [$nameOnlyLower])
                  ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$rawQueryLower])
                  ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$idOnlyLower]);
            })
            ->orderByDesc('date_create')
            ->first();

        // 3. Fallback view_batchjob
        if (!$p && !$trx) {
            $trx = DB::table('view_batchjob')
                ->where(function ($q) use ($idOnlyLower, $rawQueryLower, $nameOnlyLower) {
                    $q->whereRaw('LOWER(TRIM(nama_pelanggan)) = ?', [$rawQueryLower])
                      ->orWhereRaw('LOWER(TRIM(nama_pelanggan)) = ?', [$nameOnlyLower])
                      ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$rawQueryLower])
                      ->orWhereRaw('LOWER(TRIM(id_perusahaan)) = ?', [$idOnlyLower]);
                })
                ->orderByDesc('date_create')
                ->first();
        }

        if (!$p && !$trx) {
            return response()->json(['found' => false]);
        }

        // Resolusi wilayah Perusahaan (Section 2)
        $kodeKelCorp = $p->kode_wilayah_kelurahan_ktp 
            ?? $p->kode_wilayah_kelurahan_perusahaan 
            ?? ($trx->kode_wilayah_kelurahan_perusahaan ?? null)
            ?? ($trx->kode_wilayah_kelurahan_ktp ?? null);

        $provCorp = null; $kotaCorp = null; $kecCorp = null; $kelCorp = $kodeKelCorp;
        if ($kodeKelCorp) {
            $wilCorp = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelCorp)->first();
            if ($wilCorp) {
                $provCorp = $wilCorp->kode_wilayah_provinsi;
                $kotaCorp = $wilCorp->kode_wilayah_kota;
                $kecCorp  = $wilCorp->kode_wilayah_kecamatan;
            }
        }

        // Resolusi wilayah Pemasangan (Section 3)
        $kodeKelPasang = $trx->kode_wilayah_kelurahan_pasang 
            ?? $p->kode_wilayah_kelurahan_pasang 
            ?? $kodeKelCorp;

        $provPasang = null; $kotaPasang = null; $kecPasang = null; $kelPasang = $kodeKelPasang;
        if ($kodeKelPasang) {
            $wilPasang = DB::table('m_wilayah')->where('kode_wilayah_kelurahan', $kodeKelPasang)->first();
            if ($wilPasang) {
                $provPasang = $wilPasang->kode_wilayah_provinsi;
                $kotaPasang = $wilPasang->kode_wilayah_kota;
                $kecPasang  = $wilPasang->kode_wilayah_kecamatan;
            }
        }

        $lonLatCorp = $p->lon_lat_perusahaan ?? $trx->lon_lat_perusahaan ?? $p->lon_lat ?? $trx->lon_lat ?? '';
        $sharelockCorp = $p->sharelock_perusahaan ?? $trx->sharelock_perusahaan ?? $p->sharelock ?? $trx->loc_maps ?? '';
        if (empty($sharelockCorp) && !empty($lonLatCorp)) {
            $sharelockCorp = 'https://www.google.com/maps?q=' . urlencode(trim($lonLatCorp));
        }

        $nomorBangunanCorp = $p->nomor_bangunan_perusahaan ?? $trx->nomor_bangunan_perusahaan ?? $p->nomor_bangunan ?? $trx->nomor_bangunan ?? '';
        $jenisBangunanCorp = $p->jenis_bangunan ?? $trx->jenis_bangunan ?? '';

        $lonLatPasang = $trx->lon_lat ?? $p->lon_lat ?? $lonLatCorp;
        $sharelockPasang = $trx->loc_maps ?? $trx->sharelock ?? $p->sharelock ?? $sharelockCorp;

        $resolvedIdPerusahaan = $p->id_perusahaan ?? $trx->id_perusahaan ?? $idOnly;

        return response()->json([
            'found' => true,
            'data'  => [
                // Section 1: Informasi Pelanggan
                'id_perusahaan'             => $resolvedIdPerusahaan,
                'nama_perusahaan'           => $p->nama_perusahaan ?? $trx->nama_pelanggan ?? $p->nama_penduduk ?? '',
                'no_telp_perusahaan'       => $p->no_telp_perusahaan ?? $p->nomor_hp ?? '',
                'email_perusahaan'         => $p->email_perusahaan ?? $p->email ?? '',
                'nama_pic_teknis'          => $p->nama_pic_teknis ?? '',
                'no_telp_pic_teknis'       => $p->no_telp_pic_teknis ?? $p->nomor_hp_2 ?? '',
                'email_pic_teknis'         => $p->email_pic_teknis ?? '',
                'nama_pic_keuangan'        => $p->nama_pic_keuangan ?? '',
                'no_telp_pic_keuangan'     => $p->no_telp_pic_keuangan ?? '',
                'email_pic_keuangan'       => $p->email_pic_keuangan ?? '',
                'jenis_perusahaan'         => $p->jenis_perusahaan ?? '',

                // Section 2: Alamat Perusahaan & Detail Perusahaan
                'provinsi_ktp'              => $provCorp,
                'kota_ktp'                  => $kotaCorp,
                'kecamatan_ktp'             => $kecCorp,
                'kelurahan_ktp'             => $kelCorp,
                'rt_ktp'                    => $p->rt_ktp ?? $trx->rt_perusahaan ?? $p->rt_perusahaan ?? '',
                'rw_ktp'                    => $p->rw_ktp ?? $trx->rw_perusahaan ?? $p->rw_perusahaan ?? '',
                'nomor_bangunan_perusahaan' => $nomorBangunanCorp,
                'alamat_ktp'                => $p->alamat_ktp ?? $trx->detail_alamat_perusahaan ?? $p->detail_alamat_perusahaan ?? $p->alamat_perusahaan ?? '',
                'lon_lat_perusahaan'        => $lonLatCorp,
                'sharelock_perusahaan'      => $sharelockCorp,
                'jenis_bangunan'            => $jenisBangunanCorp,

                // Section 3: Alamat & Lokasi Pemasangan
                'provinsi_pasang'           => $provPasang,
                'kota_pasang'               => $kotaPasang,
                'kecamatan_pasang'          => $kecPasang,
                'kelurahan_pasang'          => $kelPasang,
                'rt_pasang'                 => $trx->rt_pasang ?? $trx->rt_perusahaan ?? $p->rt_ktp ?? '',
                'rw_pasang'                 => $trx->rw_pasang ?? $trx->rw_perusahaan ?? $p->rw_ktp ?? '',
                'nomor_bangunan'            => $trx->nomor_bangunan ?? $nomorBangunanCorp,
                'alamat_pasang'             => $trx->alamat_pasang ?? $trx->detail_alamat_perusahaan ?? $p->alamat_ktp ?? '',
                'lon_lat'                   => $lonLatPasang,
                'sharelock'                 => $sharelockPasang,
                'permintaan_khusus'         => $trx->note_request ?? '',
            ]
        ]);
    }

    /**
     * Format database exceptions and system errors into clean, user-friendly Indonesian messages
     */
    public static function formatDbErrorMessage(\Throwable $e, string $action = 'menyimpan data'): string
    {
        $msg = $e->getMessage();

        // 1. Data too long for column (SQLSTATE 22001 / Error 1406)
        if (preg_match("/Data too long for column '([^']+)'/i", $msg, $m)) {
            $col = $m[1];
            $fieldLabels = [
                'nomor_bangunan' => 'Nomor Bangunan / No. Rumah',
                'nomor_bangunan_perusahaan' => 'Nomor Bangunan Perusahaan',
                'rt_pasang' => 'RT Lokasi Pemasangan',
                'rw_pasang' => 'RW Lokasi Pemasangan',
                'rt_perusahaan' => 'RT Perusahaan',
                'rw_perusahaan' => 'RW Perusahaan',
                'rt_ktp' => 'RT Perusahaan/KTP',
                'rw_ktp' => 'RW Perusahaan/KTP',
                'nama_pelanggan' => 'Nama Perusahaan / Pelanggan',
                'nama_perusahaan' => 'Nama Perusahaan',
                'alamat_pasang' => 'Alamat Pemasangan',
                'alamat_ktp' => 'Alamat Perusahaan',
                'note_request' => 'Permintaan Khusus / Catatan',
                'nama_sales' => 'Nama Sales',
                'group_layanan' => 'Group Layanan',
                'kode_bandwith' => 'Paket Layanan',
                'kode_wilayah_kelurahan_pasang' => 'Kelurahan Pemasangan',
                'kode_wilayah_kelurahan_perusahaan' => 'Kelurahan Perusahaan',
                'user_create' => 'Nama Pengguna / Admin',
                'user_update' => 'Nama Pengguna / Admin',
                'no_telp_perusahaan' => 'No. Telepon Perusahaan',
                'no_telp_pic_teknis' => 'No. Telepon PIC Teknis',
                'no_telp_pic_keuangan' => 'No. Telepon PIC Keuangan',
                'email_perusahaan' => 'Email Perusahaan',
                'email_pic_teknis' => 'Email PIC Teknis',
                'email_pic_keuangan' => 'Email PIC Keuangan',
            ];
            $fieldName = $fieldLabels[$col] ?? ucwords(str_replace('_', ' ', $col));
            return "Gagal {$action}: Jumlah karakter pada kolom '{$fieldName}' terlalu panjang. Silakan periksa dan perpendek isian data tersebut lalu coba ulangi.";
        }

        // 2. Duplicate entry (SQLSTATE 23000 / Error 1062)
        if (preg_match("/Duplicate entry '([^']+)' for key '([^']+)'/i", $msg, $m)) {
            $val = $m[1];
            $key = $m[2];
            if (str_contains($key, 'nomor_internet')) {
                return "Gagal {$action}: Nomor Internet '{$val}' sudah terdaftar dalam sistem. Silakan ulangi dengan nomor lain.";
            }
            if (str_contains($key, 'id_perusahaan')) {
                return "Gagal {$action}: ID Perusahaan '{$val}' sudah digunakan. Silakan gunakan ID lain.";
            }
            return "Gagal {$action}: Data '{$val}' sudah terdaftar di dalam sistem (duplikat).";
        }

        // 3. Cannot be null / Not null constraint (SQLSTATE 23000 / Error 1048)
        if (preg_match("/Column '([^']+)' cannot be null/i", $msg, $m)) {
            $col = $m[1];
            $fieldName = ucwords(str_replace('_', ' ', $col));
            return "Gagal {$action}: Kolom '{$fieldName}' wajib diisi dan tidak boleh kosong.";
        }

        // 4. Foreign key constraint fails (SQLSTATE 23000 / Error 1452)
        if (str_contains($msg, 'foreign key constraint fails') || str_contains($msg, 'a foreign key constraint')) {
            $detail = '';
            if (preg_match('/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)`/i', $msg, $matches)) {
                $detail = " (Kolom '{$matches[2]}' tidak cocok dengan tabel '{$matches[3]}')";
            }
            return "Gagal {$action}: Data referensi yang dipilih tidak valid atau belum terdaftar di database{$detail}.";
        }

        // 5. Out of range value
        if (str_contains($msg, 'Out of range value')) {
            return "Gagal {$action}: Nilai angka yang dimasukkan melebihi batas kapasitas yang diizinkan sistem.";
        }

        // 6. Generic SQL/Database error
        if (str_contains($msg, 'SQLSTATE') || str_contains($msg, 'Connection: mysql')) {
            $cleanMsg = $msg;
            if (preg_match('/SQLSTATE\[[A-Z0-9]+\]:\s*([^\(]+)/i', $msg, $matches)) {
                $cleanMsg = trim($matches[1]);
            }
            return "Gagal {$action}: " . $cleanMsg;
        }

        return "Gagal {$action}: " . $msg;
    }

    /**
     * Redirect kembali ke halaman pendaftaran terakhir (mempertahankan page & filter)
     */
    private function redirectBackToPendaftaran(?string $message = null, string $type = 'success')
    {
        $url = session('pendaftaran_last_url') ?: (session()->has('pendaftaran_page') ? route('pendaftaran', ['page' => session('pendaftaran_page')]) : route('pendaftaran'));
        $redirect = redirect()->to($url);
        if ($message) {
            if ($type === 'error') {
                $redirect->withErrors(['error' => $message]);
            } else {
                $redirect->with($type, $message);
            }
        }
        return $redirect;
    }
}
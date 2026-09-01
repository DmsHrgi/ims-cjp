<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermintaanController extends Controller
{
    /* =========================================================
     *  UP / DOWNGRADE  (view_ubah_layanan + m_status_ubahlayanan)
     * =======================================================*/
    public function upDowngrade(Request $request)
    {
        // Master filters dropdown
        $masterLayanan = DB::table('m_bandwith_kategori')->where('hide', '0')->orderBy('nama_kategori_bandwith')->get();
        $masterWilayah = DB::table('m_wilayah')->select('nama_kota')->distinct()->whereNotNull('nama_kota')->where('nama_kota', '!=', '')->orderBy('nama_kota')->get();
        $masterStatus  = DB::table('m_status_ubahlayanan')->orderBy('status_ubah_layanan')->get();

        // Kartu status: semua status master + hitungan transaksi
        $cards = DB::table('m_status_ubahlayanan as ms')
            ->leftJoin('trx_ubah_layanan as t', 't.status_ubah_layanan', '=', 'ms.status_ubah_layanan')
            ->select('ms.status_ubah_layanan as code', 'ms.desc_ubah_layanan as desc',
                DB::raw('COUNT(t.kode_trx_ubah_layanan) as total'))
            ->groupBy('ms.status_ubah_layanan', 'ms.desc_ubah_layanan')
            ->orderBy('ms.status_ubah_layanan')
            ->get()
            ->map(fn($c) => (object) [
                'code'  => (string) $c->code,
                'label' => '(KD' . $c->code . ') ' . $c->desc,
                'total' => (int) $c->total,
            ]);

        // Build query
        $query = DB::table('view_ubah_layanan as v')
            ->leftJoin('view_batchjob as b', 'b.nomor_internet', '=', 'v.nomor_internet')
            ->select('v.*', 'b.alamat_p', 'b.nama_penduduk', 'b.jenis_kelamin', 'b.jenis_bangunan');

        $this->applyUpDowngradeFilters($query, $request);

        $perPage = (int) $request->get('per_page', 10);
        $rows = $query->orderByDesc('v.date_create')->paginate($perPage)->withQueryString();

        $rows->getCollection()->transform(function ($r) {
            $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
            $r->nama_display = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
            $r->old_pack = trim(preg_replace('/\s+/', ' ', ($r->nama_kategori_bandwith_lama ?? '') . ' ' . ($r->nominal_bandwith_lama ?? '') . ' Mbps'));
            $r->new_pack = trim(preg_replace('/\s+/', ' ', ($r->nama_kategori_bandwith_baru ?? '') . ' ' . ($r->nominal_bandwith_baru ?? '') . ' Mbps'));
            return $r;
        });

        return view('permintaan.up-downgrade', [
            'cards'         => $cards,
            'rows'          => $rows,
            'module'        => 'ubah',
            'masterLayanan' => $masterLayanan,
            'masterWilayah' => $masterWilayah,
            'masterStatus'  => $masterStatus,
        ]);
    }

    public function updateScheduleUpDowngrade(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'date_schedule' => 'required|date',
            'note_schedule' => 'nullable|string|max:500',
        ], [
            'date_schedule.required' => 'Schedule Update (tanggal) wajib diisi.',
            'date_schedule.date'     => 'Format tanggal schedule tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_ubah_layanan')->where('kode_trx_ubah_layanan', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi ubah layanan tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::table('trx_ubah_layanan')
                ->where('kode_trx_ubah_layanan', $kodeTrx)
                ->update([
                    'status_ubah_layanan' => '12', // On Schedule
                    'date_schedule'       => $validated['date_schedule'],
                    'note_schedule'       => $validated['note_schedule'] ?? null,
                    'user_update'         => $currentUser,
                    'date_update'         => now(),
                ]);

            DB::table('trx_ubah_layanan_log')->insert([
                'kode_ubah_layanan_log' => 'L-UBAH-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_trx_ubah_layanan' => $kodeTrx,
                'status_ubah_layanan'   => '12',
                'note_ubah_layanan'     => 'Schedule Update: ' . $validated['date_schedule'] . ($validated['note_schedule'] ? ' | ' . $validated['note_schedule'] : ''),
                'user_create'           => $currentUser,
                'date_create'           => now(),
                'hide'                  => '0',
            ]);

            DB::commit();

            return redirect()->route('permintaan.up-downgrade')
                ->with('success', "Jadwal ubah layanan untuk {$trx->nomor_internet} berhasil diperbarui (On Schedule).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'memperbarui jadwal ubah layanan')]);
        }
    }

    public function updateCancelUpDowngrade(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'date_cancel' => 'required|date',
            'note_cancel' => 'nullable|string|max:500',
        ], [
            'date_cancel.required' => 'date Update (tanggal cancel) wajib diisi.',
            'date_cancel.date'     => 'Format tanggal cancel tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_ubah_layanan')->where('kode_trx_ubah_layanan', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi ubah layanan tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::table('trx_ubah_layanan')
                ->where('kode_trx_ubah_layanan', $kodeTrx)
                ->update([
                    'status_ubah_layanan' => '14', // Canceled
                    'date_cancel'         => $validated['date_cancel'],
                    'note_cancel'         => $validated['note_cancel'] ?? null,
                    'user_update'         => $currentUser,
                    'date_update'         => now(),
                ]);

            DB::table('trx_ubah_layanan_log')->insert([
                'kode_ubah_layanan_log' => 'L-UBAH-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_trx_ubah_layanan' => $kodeTrx,
                'status_ubah_layanan'   => '14',
                'note_ubah_layanan'     => 'Canceled Update: ' . $validated['date_cancel'] . ($validated['note_cancel'] ? ' | ' . $validated['note_cancel'] : ''),
                'user_create'           => $currentUser,
                'date_create'           => now(),
                'hide'                  => '0',
            ]);

            DB::commit();

            return redirect()->route('permintaan.up-downgrade')
                ->with('success', "Permintaan ubah layanan untuk {$trx->nomor_internet} berhasil dibatalkan (Canceled).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'membatalkan permintaan ubah layanan')]);
        }
    }

    public function exportUpDowngrade(Request $request)
    {
        $query = DB::table('view_ubah_layanan as v')
            ->leftJoin('view_batchjob as b', 'b.nomor_internet', '=', 'v.nomor_internet')
            ->select('v.*', 'b.alamat_p', 'b.nama_penduduk', 'b.jenis_kelamin');

        $this->applyUpDowngradeFilters($query, $request);
        $rows = $query->orderByDesc('v.date_create')->get();

        $filename = 'export_up_downgrade_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, [
                'NO INTERNET', 'NAMA PELANGGAN', 'GROUB LAYANAN', 'PAKET LAMA', 'PAKET BARU', 'STATUS', 'TANGGAL PENGAJUAN', 'USER CREATE'
            ]);

            foreach ($rows as $r) {
                $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
                $namaDisplay = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
                $oldPack = trim(($r->nama_kategori_bandwith_lama ?? '') . ' ' . ($r->nominal_bandwith_lama ?? '') . ' Mbps');
                $newPack = trim(($r->nama_kategori_bandwith_baru ?? '') . ' ' . ($r->nominal_bandwith_baru ?? '') . ' Mbps');

                fputcsv($handle, [
                    $r->nomor_internet ?? '',
                    strtoupper($namaDisplay),
                    strtoupper($r->group_layanan ?? ''),
                    strtoupper($oldPack),
                    strtoupper($newPack),
                    strtoupper($r->desc_ubah_layanan ?? $r->status_ubah_layanan ?? ''),
                    $r->date_create ? Carbon::parse($r->date_create)->format('d-m-Y H:i') : '',
                    strtoupper($r->user_create ?? ''),
                ]);
            }
            fclose($handle);
        }, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    private function applyUpDowngradeFilters($query, Request $request)
    {
        if ($nama = $request->get('nama')) {
            $query->where(function ($q) use ($nama) {
                $q->where('v.nomor_internet', 'LIKE', "%{$nama}%")
                  ->orWhere('v.nama_pelanggan', 'LIKE', "%{$nama}%")
                  ->orWhere('b.nama_penduduk', 'LIKE', "%{$nama}%");
            });
        }
        if ($layanan = $request->get('layanan')) {
            $query->where(function($q) use ($layanan) {
                $q->where('v.nama_kategori_bandwith_lama', $layanan)
                  ->orWhere('v.nama_kategori_bandwith_baru', $layanan);
            });
        }
        if ($wilayah = $request->get('wilayah')) {
            $query->where(function ($q) use ($wilayah) {
                $q->where('b.nama_kota', 'LIKE', "%{$wilayah}%")
                  ->orWhere('b.nama_kecamatan', 'LIKE', "%{$wilayah}%")
                  ->orWhere('b.nama_kelurahan', 'LIKE', "%{$wilayah}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('v.status_ubah_layanan', $status);
        }
    }

    /* =========================================================
     *  TERMINASI  (view_terminasi + m_status_terminasi)
     * =======================================================*/
    public function terminasi(Request $request)
    {
        // Pastikan 8 master status terminasi ada di database
        $statusEntries = [
            ['status_terminasi' => '11',   'desc_terminasi' => 'Req. Terminasi'],
            ['status_terminasi' => '12',   'desc_terminasi' => 'Collecting'],
            ['status_terminasi' => '12.1', 'desc_terminasi' => 'Reschedule Collecting'],
            ['status_terminasi' => '13',   'desc_terminasi' => 'Collect Perangkat Done'],
            ['status_terminasi' => '14',   'desc_terminasi' => 'Terminasi'],
            ['status_terminasi' => '15',   'desc_terminasi' => 'Pending Terminasi'],
            ['status_terminasi' => '16',   'desc_terminasi' => 'Cancel Terminasi'],
            ['status_terminasi' => '17',   'desc_terminasi' => 'Req. Cancel Terminasi'],
        ];
        foreach ($statusEntries as $se) {
            DB::table('m_status_terminasi')->updateOrInsert(
                ['status_terminasi' => $se['status_terminasi']],
                [
                    'desc_terminasi' => $se['desc_terminasi'],
                    'date_create'    => now(),
                    'user_create'    => 'SYSTEM',
                    'hide'           => '0'
                ]
            );
        }

        $masterLayanan = DB::table('m_bandwith_kategori')->where('hide', '0')->orderBy('nama_kategori_bandwith')->get();
        $masterWilayah = DB::table('m_wilayah')->select('nama_kota')->distinct()->whereNotNull('nama_kota')->where('nama_kota', '!=', '')->orderBy('nama_kota')->get();
        $masterStatus  = DB::table('m_status_terminasi')->orderBy('status_terminasi')->get();

        $teamList = DB::table('tb_m_karyawan')
            ->whereIn('status_aktif', ['1', '01'])
            ->whereNotNull('nama_karyawan')
            ->where('nama_karyawan', '!=', '')
            ->orderBy('nama_karyawan')
            ->get(['kode_karyawan', 'nama_karyawan']);

        $cards = DB::table('m_status_terminasi as ms')
            ->leftJoin('trx_terminasi as t', 't.status_terminasi', '=', 'ms.status_terminasi')
            ->select('ms.status_terminasi as code', 'ms.desc_terminasi as desc',
                DB::raw('COUNT(t.kode_trx_terminasi) as total'))
            ->groupBy('ms.status_terminasi', 'ms.desc_terminasi')
            ->orderBy('ms.status_terminasi')
            ->get()
            ->map(fn($c) => (object) [
                'code'  => (string) $c->code,
                'label' => '(KD' . $c->code . ') ' . trim($c->desc),
                'total' => (int) $c->total,
            ]);

        $query = DB::table('view_terminasi');
        $this->applyTerminasiFilters($query, $request);

        $perPage = (int) $request->get('per_page', 10);
        $rows = $query->orderByDesc('date_create')->paginate($perPage)->withQueryString();

        $rows->getCollection()->transform(function ($r) {
            $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
            $r->nama_display = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
            $r->paket = trim(preg_replace('/\s+/', ' ', ($r->nama_kategori_bandwith ?? '') . ' ' . ($r->nominal_bandwith ?? '') . ' Mbps'));
            $r->collect_perangkat_label = $this->doneLabel($r->collect_perangkat ?? null);
            $r->collect_payment_label   = $this->doneLabel($r->collect_payment ?? null);
            return $r;
        });

        return view('permintaan.terminasi', [
            'cards'         => $cards,
            'rows'          => $rows,
            'module'        => 'terminasi',
            'masterLayanan' => $masterLayanan,
            'masterWilayah' => $masterWilayah,
            'masterStatus'  => $masterStatus,
            'teamList'      => $teamList,
        ]);
    }

    public function updateScheduleCollectTerminasi(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'date_collect_start' => 'required|date',
            'time_collect_start' => 'nullable|string|max:50',
            'team_collect'       => 'nullable|array',
            'note_collect_start' => 'nullable|string|max:500',
        ], [
            'date_collect_start.required' => 'Date Schedule wajib diisi.',
            'date_collect_start.date'     => 'Format Date Schedule tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_terminasi')->where('kode_trx_terminasi', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi terminasi tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            $teamsArr = $validated['team_collect'] ?? [];
            $teamString = !empty($teamsArr) ? implode(',', $teamsArr) : null;

            DB::table('trx_terminasi')
                ->where('kode_trx_terminasi', $kodeTrx)
                ->update([
                    'status_terminasi'   => '12', // Collecting
                    'date_collect_start' => $validated['date_collect_start'],
                    'time_collect_start' => $validated['time_collect_start'] ?? null,
                    'team_collect'       => $teamString,
                    'note_collect_start' => $validated['note_collect_start'] ?? null,
                    'user_update'        => substr($currentUser, 0, 15),
                    'date_update'        => now(),
                ]);

            DB::table('trx_terminasi_log')->insert([
                'kode_log_terminasi'  => 'L-TERM-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_trx_terminasi'  => $kodeTrx,
                'date_schedule'       => $validated['date_collect_start'],
                'time_schedule'       => $validated['time_collect_start'] ?? null,
                'note_schedule'       => 'Schedule Collect: ' . $validated['date_collect_start']
                    . ($validated['time_collect_start'] ? ' ' . $validated['time_collect_start'] : '')
                    . ($teamString ? ' | Tim: ' . $teamString : '')
                    . (!empty($validated['note_collect_start']) ? ' | ' . $validated['note_collect_start'] : ''),
                'status_terminasi'    => '12',
                'user_create'         => substr($currentUser, 0, 15),
                'date_create'         => now(),
                'hide'                => '0',
            ]);

            // Update status flag is_termin = 1 di register setelah dikonfirmasi/dijadwalkan oleh NOC
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $trx->nomor_internet)
                ->update([
                    'is_termin'   => '1',
                    'user_update' => substr($currentUser, 0, 15),
                    'date_update' => now(),
                ]);

            DB::commit();

            return redirect()->route('permintaan.terminasi')
                ->with('success', "Jadwal schedule collect untuk {$trx->nomor_internet} berhasil disimpan (Collecting).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'menyimpan jadwal schedule collect')]);
        }
    }

    public function updateCancelTerminasi(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'note_termin_cancel' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_terminasi')->where('kode_trx_terminasi', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi terminasi tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::table('trx_terminasi')
                ->where('kode_trx_terminasi', $kodeTrx)
                ->update([
                    'status_terminasi'   => '16', // Cancel Terminasi
                    'note_termin_cancel' => $validated['note_termin_cancel'] ?? null,
                    'user_update'        => $currentUser,
                    'date_update'        => now(),
                ]);

            DB::table('trx_terminasi_log')->insert([
                'kode_log_terminasi'  => 'L-TERM-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_trx_terminasi'  => $kodeTrx,
                'note_schedule'       => 'Cancel Terminasi: ' . ($validated['note_termin_cancel'] ?? 'Dibatalkan'),
                'status_terminasi'    => '16',
                'user_create'         => $currentUser,
                'date_create'         => now(),
                'hide'                => '0',
            ]);

            // Kembalikan status flag is_termin di register agar pelanggan aktif kembali
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $trx->nomor_internet)
                ->update([
                    'is_termin'   => '0',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            DB::commit();

            return redirect()->route('permintaan.terminasi')
                ->with('success', "Permintaan terminasi untuk {$trx->nomor_internet} berhasil dibatalkan (Cancel Terminasi).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'membatalkan permintaan terminasi')]);
        }
    }

    public function exportTerminasi(Request $request)
    {
        $query = DB::table('view_terminasi');
        $this->applyTerminasiFilters($query, $request);
        $rows = $query->orderByDesc('date_create')->get();

        $filename = 'export_terminasi_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'NO INTERNET', 'NAMA PELANGGAN', 'LAYANAN', 'ALASAN CABUT', 'STATUS', 'TANGGAL PENGAJUAN', 'USER CREATE'
            ]);

            foreach ($rows as $r) {
                $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
                $namaDisplay = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
                $paket = trim(($r->nama_kategori_bandwith ?? '') . ' ' . ($r->nominal_bandwith ?? '') . ' Mbps');

                fputcsv($handle, [
                    $r->nomor_internet ?? '',
                    strtoupper($namaDisplay),
                    strtoupper($paket),
                    strtoupper($r->alasan_cabut ?? ''),
                    strtoupper($r->desc_terminasi ?? $r->status_terminasi ?? ''),
                    $r->date_create ? Carbon::parse($r->date_create)->format('d-m-Y H:i') : '',
                    strtoupper($r->user_create ?? ''),
                ]);
            }
            fclose($handle);
        }, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    private function applyTerminasiFilters($query, Request $request)
    {
        if ($nama = $request->get('nama')) {
            $query->where(function ($q) use ($nama) {
                $q->where('nomor_internet', 'LIKE', "%{$nama}%")
                  ->orWhere('nama_pelanggan', 'LIKE', "%{$nama}%")
                  ->orWhere('nama_penduduk', 'LIKE', "%{$nama}%");
            });
        }
        if ($layanan = $request->get('layanan')) {
            $query->where('nama_kategori_bandwith', $layanan);
        }
        if ($wilayah = $request->get('wilayah')) {
            $query->where(function ($q) use ($wilayah) {
                $q->where('nama_kota', 'LIKE', "%{$wilayah}%")
                  ->orWhere('nama_kecamatan', 'LIKE', "%{$wilayah}%")
                  ->orWhere('nama_kelurahan', 'LIKE', "%{$wilayah}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status_terminasi', $status);
        }
        if ($bulan = $request->get('bulan')) {
            $query->whereMonth('date_create', $bulan);
        }
        if ($tahun = $request->get('tahun')) {
            $query->whereYear('date_create', $tahun);
        }
    }

    /* =========================================================
     *  SUSPEND  (view_suspend + m_status_suspend)
     * =======================================================*/
    public function suspend(Request $request)
    {
        $masterLayanan = DB::table('m_bandwith_kategori')->where('hide', '0')->orderBy('nama_kategori_bandwith')->get();
        $masterWilayah = DB::table('m_wilayah')->select('nama_kota')->distinct()->whereNotNull('nama_kota')->where('nama_kota', '!=', '')->orderBy('nama_kota')->get();
        $masterStatus  = DB::table('m_status_suspend')->orderBy('status_suspend')->get();

        $cards = DB::table('m_status_suspend as ms')
            ->leftJoin('trx_suspend as t', 't.status_suspend', '=', 'ms.status_suspend')
            ->whereIn('ms.status_suspend', ['11', '12', '13'])
            ->select('ms.status_suspend as code', 'ms.desc_status_suspend as desc',
                DB::raw('COUNT(t.kode_suspend) as total'))
            ->groupBy('ms.status_suspend', 'ms.desc_status_suspend')
            ->orderBy('ms.status_suspend')
            ->get()
            ->map(fn($c) => (object) [
                'code'  => (string) $c->code,
                'label' => trim($c->desc),
                'total' => (int) $c->total,
            ]);

        $query = DB::table('view_suspend');
        $this->applySuspendFilters($query, $request);

        $perPage = (int) $request->get('per_page', 10);
        $rows = $query->orderByDesc('date_create')->paginate($perPage)->withQueryString();

        $rows->getCollection()->transform(function ($r) {
            $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
            $r->nama_display = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
            $r->paket = trim(preg_replace('/\s+/', ' ', ($r->nama_kategori_bandwith ?? '') . ' ' . ($r->nominal_bandwith ?? '') . ' Mbps'));
            $r->durasi = $r->suspend_start
                ? Carbon::parse($r->suspend_start)->diff(now())->format('%y tahun %m bulan %d hari')
                : '0 tahun 0 bulan 0 hari';
            return $r;
        });

        return view('permintaan.suspend', [
            'cards'         => $cards,
            'rows'          => $rows,
            'module'        => 'suspend',
            'masterLayanan' => $masterLayanan,
            'masterWilayah' => $masterWilayah,
            'masterStatus'  => $masterStatus,
        ]);
    }

    public function updateApproveSuspend(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'date_suspend_start' => 'required|date',
            'wa_notif'           => 'nullable|string',
        ], [
            'date_suspend_start.required' => 'Start Suspend wajib diisi.',
            'date_suspend_start.date'     => 'Format tanggal suspend tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_suspend')->where('kode_suspend', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi suspend tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::table('trx_suspend')
                ->where('kode_suspend', $kodeTrx)
                ->update([
                    'status_suspend' => '12', // Suspend
                    'suspend_start'  => $validated['date_suspend_start'],
                    'user_update'    => substr($currentUser, 0, 15),
                    'date_update'    => now(),
                ]);

            DB::table('trx_suspend_log')->insert([
                'kode_suspend_log' => 'L-SUSP-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_suspend'     => $kodeTrx,
                'status_suspend'   => '12',
                'user_create'      => substr($currentUser, 0, 15),
                'date_create'      => now(),
                'hide'             => '0',
            ]);

            // Update status flag is_suspend = 1 di register setelah di-approve oleh NOC
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $trx->nomor_internet)
                ->update([
                    'is_suspend'  => '1',
                    'user_update' => substr($currentUser, 0, 15),
                    'date_update' => now(),
                ]);

            DB::commit();

            return redirect()->route('permintaan.suspend')
                ->with('success', "Permintaan suspend untuk {$trx->nomor_internet} berhasil disetujui (Suspend).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'menyetujui suspend')]);
        }
    }

    public function updateCancelSuspend(Request $request, $kodeTrx)
    {
        $validated = $request->validate([
            'note_suspend_cancel' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $trx = DB::table('trx_suspend')->where('kode_suspend', $kodeTrx)->first();
            if (!$trx) {
                return back()->withErrors(['error' => 'Data transaksi suspend tidak ditemukan.']);
            }

            $currentUser = strtoupper(session('user.nama_karyawan') ?? session('user.nama') ?? session('user.username') ?? 'SYSTEM');

            DB::table('trx_suspend')
                ->where('kode_suspend', $kodeTrx)
                ->update([
                    'status_suspend'      => '14', // Cancel Suspend
                    'desc_suspend_cancel' => $validated['note_suspend_cancel'] ?? null,
                    'user_update'         => $currentUser,
                    'date_update'         => now(),
                ]);

            DB::table('trx_suspend_log')->insert([
                'kode_suspend_log' => 'L-SUSP-' . $kodeTrx . '-' . now()->format('ymdHis'),
                'kode_suspend'     => $kodeTrx,
                'status_suspend'   => '14',
                'user_create'      => $currentUser,
                'date_create'      => now(),
                'hide'             => '0',
            ]);

            // Kembalikan status flag is_suspend di register agar pelanggan aktif kembali
            DB::table('trx_batchjob_register')
                ->where('nomor_internet', $trx->nomor_internet)
                ->update([
                    'is_suspend'  => '0',
                    'user_update' => $currentUser,
                    'date_update' => now(),
                ]);

            DB::commit();

            return redirect()->route('permintaan.suspend')
                ->with('success', "Permintaan suspend untuk {$trx->nomor_internet} berhasil dibatalkan (Cancel Suspend).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => PendaftaranController::formatDbErrorMessage($e, 'membatalkan suspend')]);
        }
    }

    public function exportSuspend(Request $request)
    {
        $query = DB::table('view_suspend');
        $this->applySuspendFilters($query, $request);
        $rows = $query->orderByDesc('date_create')->get();

        $filename = 'export_suspend_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'NO INTERNET', 'NAMA PELANGGAN', 'LAYANAN', 'ALASAN SUSPEND', 'STATUS', 'TANGGAL SUSPEND', 'DURASI', 'USER CREATE'
            ]);

            foreach ($rows as $r) {
                $jk = $r->jenis_kelamin == 1 ? 'L' : ($r->jenis_kelamin == 2 ? 'P' : null);
                $namaDisplay = trim(($r->nama_penduduk ?? $r->nama_pelanggan) . ($jk ? " ($jk)" : ''));
                $paket = trim(($r->nama_kategori_bandwith ?? '') . ' ' . ($r->nominal_bandwith ?? '') . ' Mbps');
                $durasi = $r->suspend_start
                    ? Carbon::parse($r->suspend_start)->diff(now())->format('%y thn %m bln %d hr')
                    : '-';

                fputcsv($handle, [
                    $r->nomor_internet ?? '',
                    strtoupper($namaDisplay),
                    strtoupper($paket),
                    strtoupper($r->desc_suspend ?? $r->note_suspend ?? ''),
                    strtoupper($r->desc_status_suspend ?? $r->status_suspend ?? ''),
                    $r->suspend_start ? Carbon::parse($r->suspend_start)->format('d-m-Y') : '',
                    $durasi,
                    strtoupper($r->user_create ?? ''),
                ]);
            }
            fclose($handle);
        }, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    private function applySuspendFilters($query, Request $request)
    {
        if ($nama = $request->get('nama')) {
            $query->where(function ($q) use ($nama) {
                $q->where('nomor_internet', 'LIKE', "%{$nama}%")
                  ->orWhere('nama_pelanggan', 'LIKE', "%{$nama}%")
                  ->orWhere('nama_penduduk', 'LIKE', "%{$nama}%");
            });
        }
        if ($layanan = $request->get('layanan')) {
            $query->where('nama_kategori_bandwith', $layanan);
        }
        if ($wilayah = $request->get('wilayah')) {
            $query->where(function ($q) use ($wilayah) {
                $q->where('nama_kota', 'LIKE', "%{$wilayah}%")
                  ->orWhere('nama_kecamatan', 'LIKE', "%{$wilayah}%")
                  ->orWhere('nama_kelurahan', 'LIKE', "%{$wilayah}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status_suspend', $status);
        }
    }

    /** null / '' / '0' => Undone, selain itu => Done */
    private function doneLabel($v): string
    {
        return ($v !== null && $v !== '' && $v !== '0') ? 'Done' : 'Undone';
    }
}
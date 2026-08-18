<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    private function authorizeFinance()
    {
        $u = session('user', []);
        $userLevel = strtoupper($u['level'] ?? '');
        $kodeLevel = $u['kode_level'] ?? '';
        $levelNum  = $u['level_num'] ?? null;
        $isFinance = ($userLevel === 'FINANCE' || $kodeLevel === 'lv33501' || $levelNum == 6);

        if (!$isFinance) {
            abort(403, 'Akses menu Billing hanya diizinkan untuk role Finance.');
        }
    }

    /**
     * Halaman Invoice Registrasi
     */
    public function registrasi(Request $request)
    {
        $this->authorizeFinance();

        $query = DB::table('view_billing_reg')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            });

        // Filter: Layanan
        if ($request->filled('layanan')) {
            $query->where('nama_kategori_bandwith', $request->layanan)
                  ->orWhere('kode_kategori_bandwith', $request->layanan);
        }

        // Filter: Nama / Nomor Pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nomor_internet', 'like', "%{$search}%")
                  ->orWhere('kode_billing_registrasi', 'like', "%{$search}%");
            });
        }

        // Filter: Status Bayar
        if ($request->filled('status_bayar')) {
            if ($request->status_bayar !== 'all') {
                $query->where('status_bill_reg', $request->status_bayar);
            }
        } else {
            // Default tampilkan yang Draft / Belum diPublish (11)
            $query->where('status_bill_reg', '11');
        }

        // Filter: Metode Bayar
        if ($request->filled('metode_bayar')) {
            $query->where('payment_type', $request->metode_bayar);
        }

        // Filter: Bank / Rekening
        if ($request->filled('bank')) {
            $query->where('no_rekening', $request->bank);
        }

        // Filter: Wilayah
        if ($request->filled('wilayah')) {
            $query->where('alamat_p', 'like', "%{$request->wilayah}%");
        }

        $query->orderBy('kode_billing_registrasi', 'desc');

        $rows = $query->paginate(10)->withQueryString();

        // Filter Master Data Dropdowns
        $layananList     = DB::table('m_bandwith_kategori')->pluck('nama_kategori_bandwith')->filter()->unique();
        $statusBayarList = DB::table('m_status_bill_reg')->get();
        $metodeBayarList = DB::table('m_payment_type')->where('hide', '0')->orWhereNull('hide')->get();
        $bankList        = DB::table('m_bank')->get();
        $wilayahList     = DB::table('m_wilayah')->select('nama_kota')->distinct()->whereNotNull('nama_kota')->where('nama_kota', '!=', '')->orderBy('nama_kota')->pluck('nama_kota');

        return view('billing.registrasi', compact(
            'rows',
            'layananList',
            'statusBayarList',
            'metodeBayarList',
            'bankList',
            'wilayahList'
        ));
    }

    /**
     * Publish Invoice Registrasi -> Pindah ke Invoice Layanan
     */
    public function publishRegistrasi(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');

        $billReg = DB::table('view_billing_reg')
            ->where('kode_billing_registrasi', $kode_billing)
            ->first();

        if (!$billReg) {
            return back()->with('error', 'Data billing registrasi tidak ditemukan.');
        }

        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
        $now = now();
        $expiry = $now->copy()->endOfMonth()->setTime(23, 59, 0);

        // 1. Update trx_billing_registrasi
        DB::table('trx_billing_registrasi')
            ->where('kode_billing_registrasi', $kode_billing)
            ->update([
                'status_bill_reg' => '12', // Publish Billing
                'payment_publish' => $now,
                'expiry'          => $expiry,
                'date_update'     => $now,
                'user_update'     => $uName,
            ]);

        // 2. Buat atau update record di trx_billing_layanan
        $bulanTagihan   = $now->format('m');
        $tahunTagihan   = $now->format('Y');
        $periodeTagihan = $now->translatedFormat('M Y');
        $slugNama       = strtoupper(Str::slug($billReg->nama_pelanggan ?: ($billReg->nama_penduduk ?: 'PELANGGAN')));
        $invoiceFile    = $slugNama . '-(PERIODE-' . strtoupper($now->format('M-Y')) . ')-' . rand(1000, 9999) . '.pdf';

        $totalLayanan = $billReg->harga_bandwith ?: ($billReg->total_reg ?: '200000');

        $existingLay = DB::table('trx_billing_layanan')
            ->where('nomor_internet', $billReg->nomor_internet)
            ->where('bulan_tagihan', $bulanTagihan)
            ->where('tahun_tagihan', $tahunTagihan)
            ->first();

        if ($existingLay) {
            DB::table('trx_billing_layanan')
                ->where('kode_billing_layanan', $existingLay->kode_billing_layanan)
                ->update([
                    'status_bill_lay' => '13', // Publish Billing
                    'payment_publish' => $now,
                    'expiry'          => $expiry,
                    'payment_type'    => $billReg->payment_type ?: ($existingLay->payment_type ?: '1'),
                    'no_rekening'     => $billReg->no_rekening ?: $existingLay->no_rekening,
                    'total_layanan'   => $totalLayanan,
                    'date_update'     => $now,
                    'user_update'     => $uName,
                    'hide'            => '0',
                ]);
        } else {
            $kodeBillingLayanan = 'INV/' . $billReg->nomor_internet . '/' . $bulanTagihan . '/' . $tahunTagihan;

            DB::table('trx_billing_layanan')->insert([
                'kode_billing_layanan' => $kodeBillingLayanan,
                'nomor_internet'       => $billReg->nomor_internet,
                'kode_bandwith'        => $billReg->kode_bandwith,
                'nominal_bandwith'     => $billReg->nominal_bandwith,
                'bulan_tagihan'        => $bulanTagihan,
                'tahun_tagihan'        => $tahunTagihan,
                'periode_tagihan'      => $periodeTagihan,
                'potongan'             => '0',
                'desc_potongan'        => '-',
                'ppn'                  => '0.11',
                'tax'                  => '2',
                'voucher'              => '-',
                'total_layanan'        => $totalLayanan,
                'notif_mail'           => '1',
                'notif_wa'             => '1',
                'status_bill_lay'      => '13', // Publish Billing
                'denda'                => '0',
                'invoice_file'         => $invoiceFile,
                'payment_type'         => $billReg->payment_type ?: '1',
                'no_rekening'          => $billReg->no_rekening ?: 'CASH',
                'payment_publish'      => $now,
                'expiry'               => $expiry,
                'date_create'          => $now,
                'user_create'          => $uName,
                'hide'                 => '0',
            ]);
        }

        return redirect()->route('billing.layanan')->with('success', "Invoice registrasi {$billReg->nomor_internet} ({$billReg->nama_pelanggan}) berhasil di-publish dan dipindahkan ke Invoice Layanan.");
    }

    /**
     * Hapus Invoice Registrasi
     */
    public function destroyRegistrasi(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

        DB::table('trx_billing_registrasi')
            ->where('kode_billing_registrasi', $kode_billing)
            ->update([
                'hide'        => '1',
                'date_update' => now(),
                'user_update' => $uName,
            ]);

        return back()->with('success', "Invoice registrasi {$kode_billing} berhasil dihapus.");
    }

    /**
     * Halaman Invoice Layanan
     */
    public function layanan(Request $request)
    {
        $this->authorizeFinance();

        $query = DB::table('view_billing_layanan')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            });

        // Filter: Bulan Tagihan
        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', sprintf("%02d", $request->bulan));
        }

        // Filter: Tahun Tagihan
        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        // Filter: Layanan
        if ($request->filled('layanan')) {
            $query->where('nama_kategori_bandwith', $request->layanan);
        }

        // Filter: Status User
        if ($request->filled('status_user')) {
            $query->where('status_reg', $request->status_user);
        }

        // Filter: Search Nama / Nomor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nomor_internet', 'like', "%{$search}%")
                  ->orWhere('kode_billing_layanan', 'like', "%{$search}%");
            });
        }

        // Filter: Wilayah
        if ($request->filled('wilayah')) {
            $query->where('nama_kota_pasang', $request->wilayah);
        }

        // Filter: Metode Bayar
        if ($request->filled('metode_bayar')) {
            $query->where('payment_type', $request->metode_bayar);
        }

        // Filter: Bank / Rekening
        if ($request->filled('bank')) {
            $query->where('no_rekening', $request->bank);
        }

        // Filter: Status Bayar
        if ($request->filled('status_bayar')) {
            $query->where('status_bill_lay', $request->status_bayar);
        }

        // Ringkasan Statistik
        $allBilling = DB::table('view_billing_layanan')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->get();

        $statAutoPublish = [
            'count'  => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'auto publish') || str_contains(strtolower($r->desc_bill_lay ?? ''), 'generating') || $r->status_bill_lay == '12')->count(),
            'amount' => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'auto publish') || str_contains(strtolower($r->desc_bill_lay ?? ''), 'generating') || $r->status_bill_lay == '12')->sum(fn($r) => (float)($r->total_layanan ?? 0)),
        ];

        $statPublish = [
            'count'  => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'publish billing') || $r->status_bill_lay == '13')->count(),
            'amount' => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'publish billing') || $r->status_bill_lay == '13')->sum(fn($r) => (float)($r->total_layanan ?? 0)),
        ];

        $statWaiting = [
            'count'  => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'waiting') || $r->status_bill_lay == '14')->count(),
            'amount' => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'waiting') || $r->status_bill_lay == '14')->sum(fn($r) => (float)($r->total_layanan ?? 0)),
        ];

        $statPaid = [
            'count'  => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'paid') || $r->status_bill_lay == '15')->count(),
            'amount' => $allBilling->filter(fn($r) => str_contains(strtolower($r->desc_bill_lay ?? ''), 'paid') || $r->status_bill_lay == '15')->sum(fn($r) => (float)($r->total_layanan ?? 0)),
        ];

        $query->orderBy('kode_billing_layanan', 'desc');

        $rows = $query->paginate(10)->withQueryString();

        // Dropdowns data
        $layananList     = DB::table('m_bandwith_kategori')->pluck('nama_kategori_bandwith')->filter()->unique();
        $statusBayarList = DB::table('m_status_bill_lay')->get();
        $metodeBayarList = DB::table('m_payment_type')->where('hide', '0')->orWhereNull('hide')->get();
        $bankList        = DB::table('m_bank')->get();
        $wilayahList     = DB::table('view_billing_layanan')->whereNotNull('nama_kota_pasang')->pluck('nama_kota_pasang')->unique();
        $bulanList       = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $tahunList = ['2023', '2024', '2025', '2026'];

        return view('billing.layanan', compact(
            'rows',
            'statAutoPublish',
            'statPublish',
            'statWaiting',
            'statPaid',
            'layananList',
            'statusBayarList',
            'metodeBayarList',
            'bankList',
            'wilayahList',
            'bulanList',
            'tahunList'
        ));
    }

    /**
     * Publish Invoice Layanan
     */
    public function publishLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
        $now = now();
        $expiry = $now->copy()->endOfMonth()->setTime(23, 59, 0);

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update([
                'status_bill_lay' => '13', // Publish Billing
                'payment_publish' => $now,
                'expiry'          => $expiry,
                'date_update'     => $now,
                'user_update'     => $uName,
            ]);

        return back()->with('success', "Invoice {$kode_billing} berhasil di-publish.");
    }

    /**
     * Renew Payment Link (Khusus Midtrans)
     */
    public function renewLinkLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
        $now = now();
        $expiry = $now->copy()->addDays(3)->setTime(23, 59, 0);

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update([
                'status_bill_lay' => '13', // Publish Billing / Active
                'payment_publish' => $now,
                'expiry'          => $expiry,
                'date_update'     => $now,
                'user_update'     => $uName,
            ]);

        return back()->with('success', "Link payment invoice {$kode_billing} berhasil diperbarui (Renew Link).");
    }

    /**
     * Accept Pembayaran (Manual Transfer & Cash To Collector)
     */
    public function acceptLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);
        $now = now();

        $bill = DB::table('trx_billing_layanan')->where('kode_billing_layanan', $kode_billing)->first();
        if (!$bill) {
            return back()->with('error', 'Data invoice tidak ditemukan.');
        }

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update([
                'status_bill_lay' => '15', // Paid
                'payment_paid'    => $now,
                'amount_paid'     => $bill->total_layanan,
                'date_update'     => $now,
                'user_update'     => $uName,
            ]);

        return back()->with('success', "Pembayaran invoice {$kode_billing} berhasil di-accept (Status: Paid).");
    }

    /**
     * Rollback Pembayaran (Kembalikan dari Paid ke Publish Billing)
     */
    public function rollbackLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update([
                'status_bill_lay' => '13', // Revert to Publish Billing
                'payment_paid'    => null,
                'amount_paid'     => null,
                'date_update'     => now(),
                'user_update'     => $uName,
            ]);

        return back()->with('success', "Invoice {$kode_billing} berhasil di-rollback ke status Publish Billing.");
    }

    /**
     * Penyesuaian / Adjust Nilai Invoice Layanan
     */
    public function adjustLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');

        $validated = $request->validate([
            'total_layanan'  => 'required|string',
            'potongan'       => 'nullable|string',
            'desc_potongan'  => 'nullable|string|max:100',
            'note_adjusment' => 'nullable|string|max:150',
        ]);

        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

        $parsedTotal = preg_replace('/[^0-9]/', '', $validated['total_layanan']);
        $parsedPotongan = !empty($validated['potongan']) ? preg_replace('/[^0-9]/', '', $validated['potongan']) : '0';

        $updateData = [
            'total_layanan'  => $parsedTotal,
            'potongan'       => $parsedPotongan,
            'desc_potongan'  => $request->input('desc_potongan', '-'),
            'note_adjusment' => $request->input('note_adjusment'),
            'date_update'    => now(),
            'user_update'    => $uName,
        ];

        // Jika status Paid, update amount_paid juga
        $bill = DB::table('trx_billing_layanan')->where('kode_billing_layanan', $kode_billing)->first();
        if ($bill && $bill->status_bill_lay == '15') {
            $updateData['amount_paid'] = $parsedTotal;
        }

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update($updateData);

        return back()->with('success', "Penyesuaian (Adjust) invoice {$kode_billing} berhasil disimpan.");
    }

    /**
     * Hapus Invoice Layanan
     */
    public function destroyLayanan(Request $request, $kode_billing = null)
    {
        $this->authorizeFinance();
        $kode_billing = $kode_billing ?: $request->input('kode_billing');
        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

        DB::table('trx_billing_layanan')
            ->where('kode_billing_layanan', $kode_billing)
            ->update([
                'hide'        => '1',
                'date_update' => now(),
                'user_update' => $uName,
            ]);

        return back()->with('success', "Invoice {$kode_billing} berhasil dihapus.");
    }

    /**
     * Update Payment Type Modal Handler
     */
    public function updatePaymentType(Request $request)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'billing_type' => 'required|in:layanan,registrasi',
            'kode_billing' => 'required|string',
            'payment_type' => 'required|string',
            'no_rekening'  => 'nullable|string',
        ]);

        $table  = ($validated['billing_type'] === 'registrasi') ? 'trx_billing_registrasi' : 'trx_billing_layanan';
        $keyCol = ($validated['billing_type'] === 'registrasi') ? 'kode_billing_registrasi' : 'kode_billing_layanan';

        $uName = substr(session('user.username') ?? session('user.nama_karyawan') ?? 'ADMIN', 0, 20);

        $updateData = [
            'payment_type' => $validated['payment_type'],
            'date_update'  => now()->format('Y-m-d H:i:s'),
            'user_update'  => $uName,
        ];

        if ($request->has('no_rekening')) {
            $updateData['no_rekening'] = $validated['no_rekening'];
        }

        $affected = DB::table($table)
            ->where($keyCol, $validated['kode_billing'])
            ->update($updateData);

        if ($affected === 0) {
            $exists = DB::table($table)->where($keyCol, $validated['kode_billing'])->exists();
            if (!$exists) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Kode billing ' . $validated['kode_billing'] . ' tidak ditemukan.'], 442);
                }
                return back()->with('error', 'Kode billing ' . $validated['kode_billing'] . ' tidak ditemukan.');
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Method berhasil diperbarui.'
            ]);
        }

        return back()->with('success', 'Payment Method berhasil diperbarui.');
    }
}

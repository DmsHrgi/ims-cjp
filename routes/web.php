<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OltController;

// --- AUTENTIKASI (terbuka) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/payment-info/{kode_billing?}', [BillingController::class, 'paymentInfo'])->name('payment.info')->where('kode_billing', '.*');

// Route khusus untuk perbaikan skema database & pembersihan cache hosting via browser
Route::get('/clear-cache', function () {
    $results = [];
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $results[] = 'Optimize Clear: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results[] = 'Optimize Clear error: ' . $e->getMessage();
    }
    return response()->json([
        'status' => 'success',
        'message' => 'Cache aplikasi berhasil dibersihkan!',
        'details' => $results
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::get('/fix-database-schema', function () {
    $results = [];

    // 1. Bersihkan cache
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $results[] = 'Optimize Clear: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results[] = 'Optimize Clear info: ' . $e->getMessage();
    }

    // 2. Jalankan artisan migrate jika ada file migration baru
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $results[] = 'Artisan migrate: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results[] = 'Artisan migrate: ' . $e->getMessage();
    }

    // 3. Eksekusi ALTER TABLE langsung untuk memastikan perubahan kolom berhasil
    $queries = [
        "ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `rt_pasang` VARCHAR(10) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `rw_pasang` VARCHAR(10) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan_perusahaan` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `note_request` TEXT NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `nama_sales` VARCHAR(100) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `group_layanan` VARCHAR(100) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `user_create` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` MODIFY `user_update` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `m_pelanggan` MODIFY `nomor_bangunan_perusahaan` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `m_pelanggan` MODIFY `rt_ktp` VARCHAR(10) NULL DEFAULT NULL",
        "ALTER TABLE `m_pelanggan` MODIFY `rw_ktp` VARCHAR(10) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` ADD `scan_dokumen_survey` VARCHAR(255) NULL DEFAULT NULL AFTER `scan_dokumen`",
        "ALTER TABLE `trx_batchjob_register` ADD `scan_dokumen_instalasi` VARCHAR(255) NULL DEFAULT NULL AFTER `scan_dokumen_survey`",
        "ALTER TABLE `trx_batchjob_register` ADD `scan_dokumen_aktivasi` VARCHAR(255) NULL DEFAULT NULL AFTER `scan_dokumen_instalasi`",
        "ALTER TABLE `trx_batchjob_register` ADD `pppoe_username` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `trx_batchjob_register` ADD `pppoe_password` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `m_pelanggan` ADD `pppoe_username` VARCHAR(50) NULL DEFAULT NULL",
        "ALTER TABLE `m_pelanggan` ADD `pppoe_password` VARCHAR(50) NULL DEFAULT NULL",
        "CREATE TABLE IF NOT EXISTS `m_olt` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `hostname` VARCHAR(100) NULL,
            `ip_address` VARCHAR(50) NOT NULL,
            `vendor` VARCHAR(100) NOT NULL,
            `model` VARCHAR(100) NULL,
            `status` VARCHAR(20) NOT NULL DEFAULT 'Up',
            `snmp_port` INT NOT NULL DEFAULT 161,
            `snmp_version` VARCHAR(20) NOT NULL DEFAULT 'v2c',
            `snmp_community` VARCHAR(100) NOT NULL DEFAULT 'public',
            `location` VARCHAR(255) NULL,
            `description` TEXT NULL,
            `user_create` VARCHAR(50) NULL,
            `user_update` VARCHAR(50) NULL,
            `created_at` TIMESTAMP NULL DEFAULT NULL,
            `updated_at` TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "ALTER TABLE `m_olt` ADD `name` VARCHAR(100) NOT NULL DEFAULT ''",
        "ALTER TABLE `m_olt` ADD `hostname` VARCHAR(100) NULL",
        "ALTER TABLE `m_olt` ADD `ip_address` VARCHAR(50) NOT NULL DEFAULT ''",
        "ALTER TABLE `m_olt` ADD `vendor` VARCHAR(100) NOT NULL DEFAULT ''",
        "ALTER TABLE `m_olt` ADD `model` VARCHAR(100) NULL",
        "ALTER TABLE `m_olt` ADD `status` VARCHAR(20) NOT NULL DEFAULT 'Up'",
        "ALTER TABLE `m_olt` ADD `snmp_port` INT NOT NULL DEFAULT 161",
        "ALTER TABLE `m_olt` ADD `snmp_version` VARCHAR(20) NOT NULL DEFAULT 'v2c'",
        "ALTER TABLE `m_olt` ADD `snmp_community` VARCHAR(100) NOT NULL DEFAULT 'public'",
        "ALTER TABLE `m_olt` ADD `location` VARCHAR(255) NULL",
        "ALTER TABLE `m_olt` ADD `description` TEXT NULL",
        "ALTER TABLE `m_olt` ADD `user_create` VARCHAR(50) NULL",
        "ALTER TABLE `m_olt` ADD `user_update` VARCHAR(50) NULL",
        "ALTER TABLE `m_olt` ADD `created_at` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `m_olt` ADD `updated_at` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `m_olt` MODIFY `kode_olt` VARCHAR(50) NULL DEFAULT NULL",
    ];

    foreach ($queries as $q) {
        try {
            \Illuminate\Support\Facades\DB::statement($q);
            $results[] = 'SUCCESS: ' . $q;
        } catch (\Throwable $e) {
            $results[] = 'INFO/SKIP: ' . $e->getMessage();
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Perbaikan database & clear cache berhasil dijalankan!',
        'details' => $results
    ], 200, [], JSON_PRETTY_PRINT);
});

// Route pengujian status POP dari database
Route::get('/test-pop-status', function () {
    $results = [];
    try {
        // 1. Pastikan tabel m_pop dapat diakses
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('m_pop');
        if (!$tableExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tabel m_pop tidak ditemukan di database.',
            ], 500, [], JSON_PRETTY_PRINT);
        }

        // 2. Pastikan POP default ada di m_pop jika belum ada
        $defaultPops = [
            ['kode_pop' => 'POP MSN', 'nama_pop' => 'POP MSN'],
            ['kode_pop' => 'POP Babakan Tarogong', 'nama_pop' => 'POP Babakan Tarogong'],
            ['kode_pop' => 'POP Bojong Sayang', 'nama_pop' => 'POP Bojong Sayang'],
        ];

        foreach ($defaultPops as $dp) {
            \Illuminate\Support\Facades\DB::table('m_pop')->updateOrInsert(
                ['kode_pop' => $dp['kode_pop']],
                [
                    'nama_pop'    => $dp['nama_pop'],
                    'hide'        => '0',
                    'date_create' => now(),
                    'user_create' => 'SYSTEM_INIT'
                ]
            );
        }

        // 3. Ambil data POP yang aktif
        $popList = \Illuminate\Support\Facades\DB::table('m_pop')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->orderBy('nama_pop')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data POP berhasil diambil dari database (tabel m_pop)!',
            'source' => 'DATABASE (table: m_pop)',
            'total_pop_aktif' => $popList->count(),
            'daftar_pop' => $popList,
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data POP: ' . $e->getMessage(),
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// --- APLIKASI (wajib login) ---
Route::middleware(\App\Http\Middleware\EnsureAuthenticated::class)->group(function () {

    // Dashboard
    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // Tiket
    Route::get('/tiket', [PageController::class, 'tiket'])->name('tiket');
    Route::get('/tiket/gangguan-layanan', [PageController::class, 'gangguan'])->name('tiket.gangguan-layanan');
    Route::get('/tiket/ganti-password', [PageController::class, 'gantiPassword'])->name('tiket.ganti-password');
    Route::get('/tiket/coverage-area', [PageController::class, 'coverage'])->name('tiket.coverage-area');

    // Pendaftaran
    Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran');
    Route::get('/pendaftaran/export', [PendaftaranController::class, 'export'])->name('pendaftaran.export');
    Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/{nomor_internet}/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
    Route::put('/pendaftaran/{nomor_internet}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    Route::put('/pendaftaran/{nomor_internet}/batal-pasang', [PendaftaranController::class, 'batalPasang'])->name('pendaftaran.batal-pasang');
    Route::get('/pendaftaran/{nomor_internet}/report-instalasi', [PendaftaranController::class, 'reportInstalasi'])->name('pendaftaran.report-instalasi');
    Route::put('/pendaftaran/{nomor_internet}/report-instalasi', [PendaftaranController::class, 'updateReportInstalasi'])->name('pendaftaran.update-report-instalasi');
    Route::delete('/pendaftaran/{nomor_internet}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    Route::put('/pendaftaran/{nomor_internet}/jadwal-survey', [PendaftaranController::class, 'jadwalSurvey'])->name('pendaftaran.jadwal-survey');
    Route::put('/pendaftaran/{nomor_internet}/report-survey', [PendaftaranController::class, 'updateReportSurvey'])->name('pendaftaran.update-report-survey');
    Route::put('/pendaftaran/{nomor_internet}/jadwal-instalasi', [PendaftaranController::class, 'jadwalInstalasi'])->name('pendaftaran.jadwal-instalasi');
    Route::put('/pendaftaran/{nomor_internet}/jadwal-aktivasi', [PendaftaranController::class, 'jadwalAktivasi'])->name('pendaftaran.jadwal-aktivasi');
    Route::put('/pendaftaran/{nomor_internet}/report-aktivasi', [PendaftaranController::class, 'updateReportAktivasi'])->name('pendaftaran.update-report-aktivasi');

    // API cascading dropdown wilayah & paket (WAJIB ADA)
    Route::get('/api/kota', [PendaftaranController::class, 'getKota'])->name('api.kota');
    Route::get('/api/kecamatan', [PendaftaranController::class, 'getKecamatan'])->name('api.kecamatan');
    Route::get('/api/kelurahan', [PendaftaranController::class, 'getKelurahan'])->name('api.kelurahan');
    Route::get('/api/paket', [PendaftaranController::class, 'getPaket'])->name('api.paket');
    Route::get('/api/layanan-bangunan', [PendaftaranController::class, 'getLayananByBangunan'])->name('api.layanan-bangunan');
    Route::get('/api/barang-satuan', [PendaftaranController::class, 'getBarangSatuan'])->name('api.barang-satuan');
    Route::get('/api/perusahaan-detail', [PendaftaranController::class, 'getPerusahaanDetail'])->name('api.perusahaan-detail');
    Route::get('/api/generate-id-perusahaan', [PendaftaranController::class, 'generateIdPerusahaanApi'])->name('api.generate-id-perusahaan');

    // Permintaan
    Route::get('/permintaan/up-downgrade', [PermintaanController::class, 'upDowngrade'])->name('permintaan.up-downgrade');
    Route::get('/permintaan/up-downgrade/export', [PermintaanController::class, 'exportUpDowngrade'])->name('permintaan.up-downgrade.export');
    Route::put('/permintaan/up-downgrade/{kode_trx}/schedule', [PermintaanController::class, 'updateScheduleUpDowngrade'])->name('permintaan.up-downgrade.schedule');
    Route::put('/permintaan/up-downgrade/{kode_trx}/cancel', [PermintaanController::class, 'updateCancelUpDowngrade'])->name('permintaan.up-downgrade.cancel');
    Route::get('/permintaan/terminasi', [PermintaanController::class, 'terminasi'])->name('permintaan.terminasi');
    Route::get('/permintaan/terminasi/export', [PermintaanController::class, 'exportTerminasi'])->name('permintaan.terminasi.export');
    Route::put('/permintaan/terminasi/{kode_trx}/schedule-collect', [PermintaanController::class, 'updateScheduleCollectTerminasi'])->name('permintaan.terminasi.schedule-collect');
    Route::put('/permintaan/terminasi/{kode_trx}/cancel', [PermintaanController::class, 'updateCancelTerminasi'])->name('permintaan.terminasi.cancel');
    Route::get('/permintaan/suspend', [PermintaanController::class, 'suspend'])->name('permintaan.suspend');
    Route::get('/permintaan/suspend/export', [PermintaanController::class, 'exportSuspend'])->name('permintaan.suspend.export');
    Route::put('/permintaan/suspend/{kode_trx}/approve', [PermintaanController::class, 'updateApproveSuspend'])->name('permintaan.suspend.approve');
    Route::put('/permintaan/suspend/{kode_trx}/cancel', [PermintaanController::class, 'updateCancelSuspend'])->name('permintaan.suspend.cancel');

    // Pelanggan
    Route::get('/pelanggan', [PageController::class, 'pelanggan'])->name('pelanggan');
    Route::get('/pelanggan/{nomor_internet}/modal-data', [PageController::class, 'getPelangganModalData'])->name('pelanggan.modal-data');
    Route::post('/pelanggan/request-terminasi', [PageController::class, 'postRequestTerminasi'])->name('pelanggan.request-terminasi');
    Route::post('/pelanggan/request-up-downgrade', [PageController::class, 'postRequestUpDowngrade'])->name('pelanggan.request-up-downgrade');
    Route::post('/pelanggan/request-suspend', [PageController::class, 'postRequestSuspend'])->name('pelanggan.request-suspend');
    Route::post('/pelanggan/adjust', [PageController::class, 'postAdjustData'])->name('pelanggan.adjust');
    Route::get('/pelanggan/{nomor_internet}', [PageController::class, 'pelangganDetail'])->name('pelanggan.detail');
    Route::post('/pelanggan/{nomor_internet}/update-pppoe', [PageController::class, 'updatePppoe'])->name('pelanggan.update-pppoe');
    Route::put('/pelanggan/{nomor_internet}/update-infrastruktur', [PageController::class, 'updateInfrastruktur'])->name('pelanggan.update-infrastruktur');
    Route::get('/pelanggan/{nomor_internet}/pdf', [PageController::class, 'downloadPelangganPdf'])->name('pelanggan.pdf');
    Route::get('/pelanggan/{nomor_internet}/pdf-survey', [PageController::class, 'downloadSurveyPdf'])->name('pelanggan.pdf-survey');
    Route::get('/pelanggan/{nomor_internet}/pdf-instalasi', [PageController::class, 'downloadInstalasiPdf'])->name('pelanggan.pdf-instalasi');
    Route::post('/pelanggan/{nomor_internet}/upload-scan', [PageController::class, 'uploadScanDokumen'])->name('pelanggan.upload-scan');
    Route::delete('/pelanggan/{nomor_internet}/delete-scan', [PageController::class, 'deleteScanDokumen'])->name('pelanggan.delete-scan');

    // Billing
    Route::get('/billing/registrasi', [BillingController::class, 'registrasi'])->name('billing.registrasi');
    Route::post('/billing/registrasi/publish', [BillingController::class, 'publishRegistrasi'])->name('billing.registrasi.publish');
    Route::delete('/billing/registrasi/destroy', [BillingController::class, 'destroyRegistrasi'])->name('billing.registrasi.destroy');
    Route::post('/billing/registrasi/{kode_billing}/publish', [BillingController::class, 'publishRegistrasi'])->where('kode_billing', '.*');
    Route::delete('/billing/registrasi/{kode_billing}', [BillingController::class, 'destroyRegistrasi'])->where('kode_billing', '.*');

    Route::get('/billing/layanan', [BillingController::class, 'layanan'])->name('billing.layanan');
    Route::get('/billing/layanan/{kode_billing}/pdf', [BillingController::class, 'downloadPdf'])->name('billing.layanan.pdf')->where('kode_billing', '.*');
    Route::post('/billing/layanan/publish', [BillingController::class, 'publishLayanan'])->name('billing.layanan.publish');
    Route::post('/billing/layanan/renew-link', [BillingController::class, 'renewLinkLayanan'])->name('billing.layanan.renew-link');
    Route::post('/billing/layanan/accept', [BillingController::class, 'acceptLayanan'])->name('billing.layanan.accept');
    Route::post('/billing/layanan/rollback', [BillingController::class, 'rollbackLayanan'])->name('billing.layanan.rollback');
    Route::post('/billing/layanan/adjust', [BillingController::class, 'adjustLayanan'])->name('billing.layanan.adjust');
    Route::delete('/billing/layanan/destroy', [BillingController::class, 'destroyLayanan'])->name('billing.layanan.destroy');

    Route::post('/billing/layanan/{kode_billing}/publish', [BillingController::class, 'publishLayanan'])->where('kode_billing', '.*');
    Route::post('/billing/layanan/{kode_billing}/renew-link', [BillingController::class, 'renewLinkLayanan'])->where('kode_billing', '.*');
    Route::post('/billing/layanan/{kode_billing}/accept', [BillingController::class, 'acceptLayanan'])->where('kode_billing', '.*');
    Route::post('/billing/layanan/{kode_billing}/rollback', [BillingController::class, 'rollbackLayanan'])->where('kode_billing', '.*');
    Route::post('/billing/layanan/{kode_billing}/adjust', [BillingController::class, 'adjustLayanan'])->where('kode_billing', '.*');
    Route::delete('/billing/layanan/{kode_billing}', [BillingController::class, 'destroyLayanan'])->where('kode_billing', '.*');
    Route::post('/billing/update-payment-type', [BillingController::class, 'updatePaymentType'])->name('billing.update-payment-type');

    // Manajemen User (Role Admin)
    Route::get('/manajemen-user', [UserController::class, 'index'])->name('users.index');
    Route::post('/manajemen-user', [UserController::class, 'store'])->name('users.store');
    Route::put('/manajemen-user/{kode_pengguna}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/manajemen-user/{kode_pengguna}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/manajemen-user/{kode_pengguna}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // OLT Device (Role Admin)
    Route::get('/olt', [OltController::class, 'index'])->name('olt.index');
    Route::post('/olt', [OltController::class, 'store'])->name('olt.store');
    Route::put('/olt/{id}', [OltController::class, 'update'])->name('olt.update');
    Route::delete('/olt/{id}', [OltController::class, 'destroy'])->name('olt.destroy');
    Route::get('/olt/{id}/test-connection', [OltController::class, 'testConnection'])->name('olt.test-connection');
});

// Route penayangan berkas media (foto PO, foto bangunan, dokumen) tanpa ketergantungan symlink hosting
Route::get('/media-berkas/{path}', function ($path) {
    $cleanPath = ltrim(preg_replace('/^storage\//', '', $path), '/');
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        storage_path('app/public/foto_po/' . $cleanPath),
        storage_path('app/public/foto_bangunan/' . $cleanPath),
        storage_path('app/public/foto_ktp/' . $cleanPath),
        storage_path('app/public/foto_rumah/' . $cleanPath),
        storage_path('app/public/foto_peta/' . $cleanPath),
        storage_path('app/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        public_path($cleanPath),
    ];
    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            $mime = @mime_content_type($filePath) ?: 'image/jpeg';
            return response()->file($filePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }
    abort(404);
})->where('path', '.*')->name('media.file');

// Fallback route untuk berkas storage
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim(preg_replace('/^storage\//', '', $path), '/');
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        storage_path('app/public/foto_po/' . $cleanPath),
        storage_path('app/public/foto_bangunan/' . $cleanPath),
        storage_path('app/public/foto_ktp/' . $cleanPath),
        storage_path('app/public/foto_rumah/' . $cleanPath),
        storage_path('app/public/foto_peta/' . $cleanPath),
        storage_path('app/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        public_path($cleanPath),
    ];
    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            $mime = @mime_content_type($filePath) ?: 'image/jpeg';
            return response()->file($filePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }
    abort(404);
})->where('path', '.*')->name('storage.fallback');
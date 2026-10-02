<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan kolom-kolom penting di trx_batchjob_register ada
        if (Schema::hasTable('trx_batchjob_register')) {
            $columns = [
                'id_perusahaan'                     => "VARCHAR(100) NULL DEFAULT NULL AFTER `nomor_internet`",
                'nik_penduduk'                      => "VARCHAR(50) NULL DEFAULT NULL AFTER `id_perusahaan`",
                'tipe_pelanggan'                    => "VARCHAR(50) NULL DEFAULT NULL",
                'foto_po'                           => "VARCHAR(255) NULL DEFAULT NULL",
                'foto_bangunan'                     => "VARCHAR(255) NULL DEFAULT NULL",
                'scan_dokumen'                      => "VARCHAR(255) NULL DEFAULT NULL",
                'scan_dokumen_survey'               => "VARCHAR(255) NULL DEFAULT NULL",
                'scan_dokumen_instalasi'            => "VARCHAR(255) NULL DEFAULT NULL",
                'scan_dokumen_aktivasi'             => "VARCHAR(255) NULL DEFAULT NULL",
                'detail_alamat_perusahaan'          => "TEXT NULL DEFAULT NULL",
                'nomor_bangunan_perusahaan'         => "VARCHAR(50) NULL DEFAULT NULL",
                'rt_perusahaan'                     => "VARCHAR(10) NULL DEFAULT NULL",
                'rw_perusahaan'                     => "VARCHAR(10) NULL DEFAULT NULL",
                'kode_wilayah_kelurahan_perusahaan' => "VARCHAR(50) NULL DEFAULT NULL",
                'lon_lat_perusahaan'                => "TEXT NULL DEFAULT NULL",
                'sharelock_perusahaan'              => "TEXT NULL DEFAULT NULL",
                'pppoe_username'                    => "VARCHAR(50) NULL DEFAULT NULL",
                'pppoe_password'                    => "VARCHAR(50) NULL DEFAULT NULL",
            ];

            foreach ($columns as $col => $def) {
                if (!Schema::hasColumn('trx_batchjob_register', $col)) {
                    try {
                        DB::statement("ALTER TABLE `trx_batchjob_register` ADD `{$col}` {$def}");
                    } catch (\Throwable $e) {}
                }
            }

            // Sinkronkan id_perusahaan dan nik_penduduk jika salah satunya kosong
            try {
                DB::statement("UPDATE `trx_batchjob_register` SET `id_perusahaan` = `nik_penduduk` WHERE (`id_perusahaan` IS NULL OR `id_perusahaan` = '') AND (`nik_penduduk` IS NOT NULL AND `nik_penduduk` != '')");
            } catch (\Throwable $e) {}
            try {
                DB::statement("UPDATE `trx_batchjob_register` SET `nik_penduduk` = `id_perusahaan` WHERE (`nik_penduduk` IS NULL OR `nik_penduduk` = '') AND (`id_perusahaan` IS NOT NULL AND `id_perusahaan` != '')");
            } catch (\Throwable $e) {}
        }

        // 2. Buat / Perbaiki VIEW view_batchjob
        try {
            DB::statement("DROP VIEW IF EXISTS `view_batchjob`");
            DB::statement("CREATE VIEW `view_batchjob` AS 
                SELECT 
                    `br`.`nomor_internet` AS `nomor_internet`, 
                    `br`.`status_reg` AS `status_reg`, 
                    `sr`.`desc_registrasi` AS `desc_registrasi`, 
                    COALESCE(`br`.`id_perusahaan`, `br`.`nik_penduduk`) AS `nik_penduduk`, 
                    COALESCE(`br`.`id_perusahaan`, `br`.`nik_penduduk`) AS `id_perusahaan`, 
                    `br`.`nama_pelanggan` AS `nama_pelanggan`, 
                    `p`.`nama_perusahaan` AS `nama_perusahaan`, 
                    `p`.`no_telp_perusahaan` AS `no_telp_perusahaan`, 
                    `p`.`email_perusahaan` AS `email_perusahaan`, 
                    `p`.`nama_pic_teknis` AS `nama_pic_teknis`, 
                    `p`.`no_telp_pic_teknis` AS `no_telp_pic_teknis`, 
                    `p`.`email_pic_teknis` AS `email_pic_teknis`, 
                    `p`.`nama_pic_keuangan` AS `nama_pic_keuangan`, 
                    `p`.`no_telp_pic_keuangan` AS `no_telp_pic_keuangan`, 
                    `p`.`email_pic_keuangan` AS `email_pic_keuangan`, 
                    `p`.`jenis_perusahaan` AS `jenis_perusahaan`, 
                    `p`.`tanggal_registrasi` AS `tanggal_registrasi`, 
                    `p`.`nama_penduduk` AS `nama_penduduk`, 
                    `p`.`nomor_hp` AS `nomor_hp`, 
                    `p`.`nomor_hp_2` AS `nomor_hp_2`, 
                    `p`.`email` AS `email`, 
                    `p`.`jenis_kelamin` AS `jenis_kelamin`, 
                    `p`.`tanggal_lahir` AS `tanggal_lahir`, 
                    `p`.`pic` AS `pic`, 
                    `p`.`rt_ktp` AS `rt_ktp`, 
                    `p`.`rw_ktp` AS `rw_ktp`, 
                    `p`.`alamat_ktp` AS `alamat_ktp`, 
                    `p`.`alamat_ktp` AS `alamat_perusahaan`, 
                    concat(COALESCE(`p`.`alamat_ktp`,''),', RT',COALESCE(`p`.`rt_ktp`,'00'),'/RW',COALESCE(`p`.`rw_ktp`,'00'),', KEL. ',COALESCE(`p`.`nama_kelurahan`,''),', KEC. ',COALESCE(`p`.`nama_kecamatan`,''),', ',COALESCE(`p`.`nama_kota`,''),', ',COALESCE(`p`.`nama_provinsi`,'')) AS `alamat_k`, 
                    concat(COALESCE(`p`.`alamat_ktp`,''),', RT',COALESCE(`p`.`rt_ktp`,'00'),'/RW',COALESCE(`p`.`rw_ktp`,'00'),', KEL. ',COALESCE(`p`.`nama_kelurahan`,''),', KEC. ',COALESCE(`p`.`nama_kecamatan`,''),', ',COALESCE(`p`.`nama_kota`,''),', ',COALESCE(`p`.`nama_provinsi`,'')) AS `alamat_perusahaan_lengkap`, 
                    `p`.`kode_wilayah_kelurahan_ktp` AS `kode_wilayah_kelurahan_ktp`, 
                    `p`.`nama_kelurahan` AS `nama_kelurahan`, 
                    `p`.`nama_kecamatan` AS `nama_kecamatan`, 
                    `p`.`nama_kota` AS `nama_kota`, 
                    `p`.`nama_provinsi` AS `nama_provinsi`, 
                    `b`.`kode_kategori_bandwith` AS `kode_kategori_bandwith`, 
                    `b`.`nama_kategori_bandwith` AS `nama_kategori_bandwith`, 
                    `b`.`alias_nama_kategori` AS `alias_nama_kategori`, 
                    `b`.`biaya_reg` AS `biaya_reg`, 
                    `b`.`kode_bandwith` AS `kode_bandwith`, 
                    `b`.`nominal_bandwith` AS `nominal_bandwith`, 
                    `b`.`harga_bandwith` AS `harga_bandwith`, 
                    `br`.`jenis_bangunan` AS `jenis_bangunan`, 
                    `br`.`rt_pasang` AS `rt_pasang`, 
                    `br`.`rw_pasang` AS `rw_pasang`, 
                    `br`.`alamat_pasang` AS `alamat_pasang`, 
                    concat(COALESCE(`br`.`alamat_pasang`,''),' NO. ',COALESCE(`br`.`nomor_bangunan`,''),', RT',COALESCE(`br`.`rt_pasang`,'00'),'/RW',COALESCE(`br`.`rw_pasang`,'00'),', KEL. ',COALESCE(`w`.`nama_kelurahan`,''),', KEC. ',COALESCE(`w`.`nama_kecamatan`,''),', ',COALESCE(`w`.`nama_kota`,''),', ',COALESCE(`w`.`nama_provinsi`,'')) AS `alamat_p`, 
                    `br`.`kode_wilayah_kelurahan_pasang` AS `kode_wilayah_kelurahan_pasang`, 
                    `br`.`loc_maps` AS `loc_maps`, 
                    `br`.`nomor_bangunan` AS `nomor_bangunan`, 
                    `br`.`lon_lat` AS `lon_lat`, 
                    `w`.`nama_kelurahan` AS `nama_kelurahan_pasang`, 
                    `w`.`nama_kecamatan` AS `nama_kecamatan_pasang`, 
                    `w`.`nama_kota` AS `nama_kota_pasang`, 
                    `w`.`kode_wilayah_kota` AS `kode_wilayah_kota_pasang`, 
                    `w`.`nama_provinsi` AS `nama_provinsi_pasang`, 
                    `br`.`note_request` AS `note_request`, 
                    COALESCE(`br`.`tipe_pelanggan`, `p`.`tipe_pelanggan`) AS `tipe_pelanggan`,
                    `br`.`ppn` AS `ppn`, 
                    `br`.`ppn_nom` AS `ppn_nom`, 
                    `br`.`potongan` AS `potongan`, 
                    `br`.`potongan_note` AS `potongan_note`, 
                    `br`.`last_month_billing` AS `last_month_billing`, 
                    `br`.`last_year_billing` AS `last_year_billing`, 
                    `br`.`periode_billing` AS `periode_billing`, 
                    `br`.`jns_notif` AS `jns_notif`, 
                    `br`.`is_denda` AS `is_denda`, 
                    `br`.`is_suspend` AS `is_suspend`, 
                    `br`.`count_suspend` AS `count_suspend`, 
                    `br`.`is_termin` AS `is_termin`, 
                    `i`.`kode_instalasi` AS `kode_instalasi`, 
                    `i`.`verifikasi_date` AS `verifikasi_date`, 
                    `i`.`verifikasi_note` AS `verifikasi_note`, 
                    `i`.`survey_date_start` AS `survey_date_start`, 
                    `i`.`survey_time` AS `survey_time`, 
                    `i`.`survey_team` AS `survey_team`, 
                    `i`.`survey_note` AS `survey_note`, 
                    `i`.`survey_date_finish` AS `survey_date_finish`, 
                    `i`.`survey_note_finish` AS `survey_note_finish`, 
                    `i`.`doc_survey` AS `doc_survey`, 
                    `i`.`instalasi_date_start` AS `instalasi_date_start`, 
                    `i`.`instalasi_time` AS `instalasi_time`, 
                    `i`.`instalasi_team` AS `instalasi_team`, 
                    `i`.`instalasi_note` AS `instalasi_note`, 
                    `i`.`instalasi_date_finish` AS `instalasi_date_finish`, 
                    `i`.`instalasi_note_finish` AS `instalasi_note_finish`, 
                    `i`.`doc_instalasi` AS `doc_instalasi`, 
                    `i`.`aktivasi_date_start` AS `aktivasi_date_start`, 
                    `i`.`aktivasi_time` AS `aktivasi_time`, 
                    `i`.`aktivasi_team` AS `aktivasi_team`, 
                    `i`.`aktivasi_note` AS `aktivasi_note`, 
                    `i`.`aktivasi_date_finish` AS `aktivasi_date_finish`, 
                    `i`.`aktivasi_note_finish` AS `aktivasi_note_finish`, 
                    `i`.`doc_aktivasi` AS `doc_aktivasi`, 
                    `i`.`doc_berlangganan` AS `doc_berlangganan`, 
                    `i`.`foto_rumah` AS `foto_rumah`, 
                    `i`.`foto_ktp` AS `foto_ktp`, 
                    `i`.`foto_peta` AS `foto_peta`, 
                    `br`.`kode_pop` AS `kode_pop`, 
                    `po`.`nama_pop` AS `nama_pop`, 
                    `po`.`desc_pop` AS `desc_pop`, 
                    `br`.`ont_us` AS `ont_us`, 
                    `br`.`ont_ps` AS `ont_ps`, 
                    `br`.`media_akses` AS `media_akses`, 
                    `br`.`index_olt` AS `index_olt`, 
                    `br`.`foto_po` AS `foto_po`, 
                    `br`.`foto_bangunan` AS `foto_bangunan`, 
                    `br`.`detail_alamat_perusahaan` AS `detail_alamat_perusahaan`, 
                    `br`.`nomor_bangunan_perusahaan` AS `nomor_bangunan_perusahaan`, 
                    `br`.`rt_perusahaan` AS `rt_perusahaan`, 
                    `br`.`rw_perusahaan` AS `rw_perusahaan`, 
                    `br`.`kode_wilayah_kelurahan_perusahaan` AS `kode_wilayah_kelurahan_perusahaan`, 
                    `br`.`lon_lat_perusahaan` AS `lon_lat_perusahaan`, 
                    `br`.`sharelock_perusahaan` AS `sharelock_perusahaan`, 
                    `br`.`group_layanan` AS `group_layanan`, 
                    `br`.`nama_sales` AS `nama_sales`, 
                    `br`.`islock` AS `islock`, 
                    `br`.`prorate` AS `prorate`, 
                    `br`.`hide` AS `hide`, 
                    `br`.`user_create` AS `user_create`, 
                    `br`.`date_create` AS `date_create`, 
                    `br`.`date_update` AS `date_update`, 
                    `br`.`user_update` AS `user_update`, 
                    `h`.`desc_hide` AS `desc_hide` 
                FROM (((((((`trx_batchjob_register` `br` 
                    LEFT JOIN `m_status_registrasi` `sr` ON (`br`.`status_reg` = `sr`.`status_reg`)) 
                    LEFT JOIN `trx_instalasi` `i` ON (`br`.`nomor_internet` = `i`.`nomor_internet`)) 
                    LEFT JOIN `view_bandwith` `b` ON (`br`.`kode_bandwith` = `b`.`kode_bandwith`)) 
                    LEFT JOIN `m_wilayah` `w` ON (`br`.`kode_wilayah_kelurahan_pasang` = `w`.`kode_wilayah_kelurahan`)) 
                    LEFT JOIN `view_pelanggan` `p` ON (COALESCE(`br`.`id_perusahaan`, `br`.`nik_penduduk`) = COALESCE(`p`.`id_perusahaan`, `p`.`nik_penduduk`))) 
                    LEFT JOIN `m_status_hide` `h` ON (`br`.`hide` = `h`.`hide`)) 
                    LEFT JOIN `m_pop` `po` ON (`po`.`kode_pop` = `br`.`kode_pop`))");
        } catch (\Throwable $e) {}

        // 3. Buat / Perbaiki VIEW view_aktif_kota
        try {
            DB::statement("DROP VIEW IF EXISTS `view_aktif_kota`");
            DB::statement("CREATE VIEW `view_aktif_kota` AS 
                SELECT `view_batchjob`.`nama_kota_pasang` AS `nama_kota`, `view_batchjob`.`kode_wilayah_kota_pasang` AS `kode_kota` 
                FROM `view_batchjob` 
                GROUP BY `view_batchjob`.`nama_kota_pasang`, `view_batchjob`.`kode_wilayah_kota_pasang`");
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};

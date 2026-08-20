<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter columns on trx_batchjob_register
        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rt_pasang` VARCHAR(10) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rw_pasang` VARCHAR(10) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan_perusahaan` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `note_request` TEXT NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nama_sales` VARCHAR(100) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `group_layanan` VARCHAR(100) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `user_create` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `user_update` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        // Alter columns on m_pelanggan if exists
        try {
            DB::statement("ALTER TABLE `m_pelanggan` MODIFY `nomor_bangunan_perusahaan` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rt_ktp` VARCHAR(10) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rw_ktp` VARCHAR(10) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        // Alter columns on trx_batchjob_register_old if exists
        try {
            DB::statement("ALTER TABLE `trx_batchjob_register_old` MODIFY `nomor_bangunan` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan` VARCHAR(10) NULL DEFAULT NULL");
        } catch (\Throwable $e) {}
    }
};

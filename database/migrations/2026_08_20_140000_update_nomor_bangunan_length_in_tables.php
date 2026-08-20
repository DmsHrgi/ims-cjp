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
        // Alter trx_batchjob_register
        if (Schema::hasTable('trx_batchjob_register')) {
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan` VARCHAR(50) NULL");
            } catch (\Exception $e) {}
            
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rt_pasang` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rw_pasang` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rt_perusahaan` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rw_perusahaan` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `note_request` TEXT NULL");
            } catch (\Exception $e) {}
        }

        // Alter m_pelanggan
        if (Schema::hasTable('m_pelanggan')) {
            try {
                DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rt_ktp` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rw_ktp` VARCHAR(10) NULL");
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trx_batchjob_register')) {
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `nomor_bangunan` VARCHAR(10) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rt_pasang` VARCHAR(3) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rw_pasang` VARCHAR(3) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rt_perusahaan` VARCHAR(5) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `rw_perusahaan` VARCHAR(5) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `note_request` VARCHAR(50) NULL");
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('m_pelanggan')) {
            try {
                DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rt_ktp` VARCHAR(3) NULL");
            } catch (\Exception $e) {}

            try {
                DB::statement("ALTER TABLE `m_pelanggan` MODIFY `rw_ktp` VARCHAR(3) NULL");
            } catch (\Exception $e) {}
        }
    }
};

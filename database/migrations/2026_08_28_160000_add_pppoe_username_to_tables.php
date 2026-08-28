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
        if (Schema::hasTable('trx_batchjob_register')) {
            if (!Schema::hasColumn('trx_batchjob_register', 'pppoe_username')) {
                try {
                    DB::statement("ALTER TABLE `trx_batchjob_register` ADD `pppoe_username` VARCHAR(50) NULL DEFAULT NULL AFTER `note_request`");
                } catch (\Throwable $e) {}
            }
            if (!Schema::hasColumn('trx_batchjob_register', 'pppoe_password')) {
                try {
                    DB::statement("ALTER TABLE `trx_batchjob_register` ADD `pppoe_password` VARCHAR(50) NULL DEFAULT NULL AFTER `pppoe_username`");
                } catch (\Throwable $e) {}
            }
        }

        if (Schema::hasTable('m_pelanggan')) {
            if (!Schema::hasColumn('m_pelanggan', 'pppoe_username')) {
                try {
                    DB::statement("ALTER TABLE `m_pelanggan` ADD `pppoe_username` VARCHAR(50) NULL DEFAULT NULL");
                } catch (\Throwable $e) {}
            }
            if (!Schema::hasColumn('m_pelanggan', 'pppoe_password')) {
                try {
                    DB::statement("ALTER TABLE `m_pelanggan` ADD `pppoe_password` VARCHAR(50) NULL DEFAULT NULL");
                } catch (\Throwable $e) {}
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trx_batchjob_register')) {
            if (Schema::hasColumn('trx_batchjob_register', 'pppoe_username')) {
                try {
                    DB::statement("ALTER TABLE `trx_batchjob_register` DROP COLUMN `pppoe_username`");
                } catch (\Throwable $e) {}
            }
        }
        if (Schema::hasTable('m_pelanggan')) {
            if (Schema::hasColumn('m_pelanggan', 'pppoe_username')) {
                try {
                    DB::statement("ALTER TABLE `m_pelanggan` DROP COLUMN `pppoe_username`");
                } catch (\Throwable $e) {}
            }
        }
    }
};

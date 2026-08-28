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
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `ont_us` VARCHAR(100) NULL DEFAULT NULL");
            } catch (\Throwable $e) {}
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `olt` VARCHAR(100) NULL DEFAULT NULL");
            } catch (\Throwable $e) {}
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `index_olt` VARCHAR(100) NULL DEFAULT NULL");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trx_batchjob_register')) {
            try {
                DB::statement("ALTER TABLE `trx_batchjob_register` MODIFY `ont_us` VARCHAR(10) NULL DEFAULT NULL");
            } catch (\Throwable $e) {}
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('trx_batchjob_register')) {
            Schema::table('trx_batchjob_register', function (Blueprint $table) {
                if (!Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_survey')) {
                    $table->string('scan_dokumen_survey', 255)->nullable()->after('scan_dokumen');
                }
                if (!Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_instalasi')) {
                    $table->string('scan_dokumen_instalasi', 255)->nullable()->after('scan_dokumen_survey');
                }
                if (!Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_aktivasi')) {
                    $table->string('scan_dokumen_aktivasi', 255)->nullable()->after('scan_dokumen_instalasi');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trx_batchjob_register')) {
            Schema::table('trx_batchjob_register', function (Blueprint $table) {
                if (Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_aktivasi')) {
                    $table->dropColumn('scan_dokumen_aktivasi');
                }
                if (Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_instalasi')) {
                    $table->dropColumn('scan_dokumen_instalasi');
                }
                if (Schema::hasColumn('trx_batchjob_register', 'scan_dokumen_survey')) {
                    $table->dropColumn('scan_dokumen_survey');
                }
            });
        }
    }
};

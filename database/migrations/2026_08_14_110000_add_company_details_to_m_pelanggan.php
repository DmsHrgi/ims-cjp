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
        Schema::table('m_pelanggan', function (Blueprint $table) {
            if (!Schema::hasColumn('m_pelanggan', 'jenis_bangunan')) {
                $table->string('jenis_bangunan', 100)->nullable();
            }
            if (!Schema::hasColumn('m_pelanggan', 'nomor_bangunan_perusahaan')) {
                $table->string('nomor_bangunan_perusahaan', 50)->nullable();
            }
            if (!Schema::hasColumn('m_pelanggan', 'lon_lat_perusahaan')) {
                $table->string('lon_lat_perusahaan', 100)->nullable();
            }
            if (!Schema::hasColumn('m_pelanggan', 'sharelock_perusahaan')) {
                $table->string('sharelock_perusahaan', 500)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_pelanggan', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('m_pelanggan', 'jenis_bangunan')) $cols[] = 'jenis_bangunan';
            if (Schema::hasColumn('m_pelanggan', 'nomor_bangunan_perusahaan')) $cols[] = 'nomor_bangunan_perusahaan';
            if (Schema::hasColumn('m_pelanggan', 'lon_lat_perusahaan')) $cols[] = 'lon_lat_perusahaan';
            if (Schema::hasColumn('m_pelanggan', 'sharelock_perusahaan')) $cols[] = 'sharelock_perusahaan';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

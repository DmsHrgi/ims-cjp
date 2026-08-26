<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('m_barang')) {
            // BR003: HUAWEI -> ONU HUAWEI
            DB::table('m_barang')
                ->where('kode_barang', 'BR003')
                ->orWhere(function ($q) {
                    $q->where('nama_barang', 'HUAWEI');
                })
                ->update(['nama_barang' => 'ONU HUAWEI']);

            // BR013: ZTE (F660) -> ONU ZTE F660
            DB::table('m_barang')
                ->where('kode_barang', 'BR013')
                ->orWhere(function ($q) {
                    $q->where('nama_barang', 'ZTE')->where('tipe_barang', 'F660');
                })
                ->update(['nama_barang' => 'ONU ZTE F660']);

            // BR011: ZTE (F609 V3) -> ONU ZTE F609 V3
            DB::table('m_barang')
                ->where('kode_barang', 'BR011')
                ->orWhere(function ($q) {
                    $q->where('nama_barang', 'ZTE')->where('tipe_barang', 'F609 V3');
                })
                ->update(['nama_barang' => 'ONU ZTE F609 V3']);

            // BR004: ZTE (F609 V1 / lainnya) -> ONU ZTE
            DB::table('m_barang')
                ->where('kode_barang', 'BR004')
                ->orWhere(function ($q) {
                    $q->where('nama_barang', 'ZTE');
                })
                ->update(['nama_barang' => 'ONU ZTE']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('m_barang')) {
            DB::table('m_barang')->where('kode_barang', 'BR003')->update(['nama_barang' => 'HUAWEI']);
            DB::table('m_barang')->where('kode_barang', 'BR013')->update(['nama_barang' => 'ZTE']);
            DB::table('m_barang')->where('kode_barang', 'BR011')->update(['nama_barang' => 'ZTE']);
            DB::table('m_barang')->where('kode_barang', 'BR004')->update(['nama_barang' => 'ZTE']);
        }
    }
};

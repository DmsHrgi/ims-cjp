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
        // 1. Update trx_batchjob_register
        if (Schema::hasTable('trx_batchjob_register')) {
            if (Schema::hasColumn('trx_batchjob_register', 'group_layanan')) {
                DB::table('trx_batchjob_register')
                    ->where('group_layanan', 'like', '%UP TO NEW%')
                    ->orWhere('group_layanan', 'like', '%up to new%')
                    ->update(['group_layanan' => 'LOCALLOOP']);
            }

            if (Schema::hasColumn('trx_batchjob_register', 'nama_kategori_bandwith')) {
                DB::table('trx_batchjob_register')
                    ->where('nama_kategori_bandwith', 'like', '%UP TO NEW%')
                    ->orWhere('nama_kategori_bandwith', 'like', '%up to new%')
                    ->update(['nama_kategori_bandwith' => 'LOCALLOOP']);
            }

            if (Schema::hasColumn('trx_batchjob_register', 'kategori')) {
                DB::table('trx_batchjob_register')
                    ->where('kategori', 'like', '%UP TO NEW%')
                    ->orWhere('kategori', 'like', '%up to new%')
                    ->update(['kategori' => 'LOCALLOOP']);
            }
        }

        // 2. Update m_bandwith_kategori
        if (Schema::hasTable('m_bandwith_kategori')) {
            if (Schema::hasColumn('m_bandwith_kategori', 'nama_kategori_bandwith')) {
                DB::table('m_bandwith_kategori')
                    ->where('nama_kategori_bandwith', 'like', '%UP TO NEW%')
                    ->orWhere('nama_kategori_bandwith', 'like', '%up to new%')
                    ->update(['nama_kategori_bandwith' => 'LOCALLOOP']);
            }
        }

        // 3. Update m_bandwith
        if (Schema::hasTable('m_bandwith')) {
            if (Schema::hasColumn('m_bandwith', 'nama_kategori_bandwith')) {
                DB::table('m_bandwith')
                    ->where('nama_kategori_bandwith', 'like', '%UP TO NEW%')
                    ->orWhere('nama_kategori_bandwith', 'like', '%up to new%')
                    ->update(['nama_kategori_bandwith' => 'LOCALLOOP']);
            }
        }

        // 4. Update m_pelanggan
        if (Schema::hasTable('m_pelanggan')) {
            if (Schema::hasColumn('m_pelanggan', 'group_layanan')) {
                DB::table('m_pelanggan')
                    ->where('group_layanan', 'like', '%UP TO NEW%')
                    ->orWhere('group_layanan', 'like', '%up to new%')
                    ->update(['group_layanan' => 'LOCALLOOP']);
            }
        }

        // 5. Update trx_pelanggan_layanan
        if (Schema::hasTable('trx_pelanggan_layanan')) {
            if (Schema::hasColumn('trx_pelanggan_layanan', 'group_layanan')) {
                DB::table('trx_pelanggan_layanan')
                    ->where('group_layanan', 'like', '%UP TO NEW%')
                    ->orWhere('group_layanan', 'like', '%up to new%')
                    ->update(['group_layanan' => 'LOCALLOOP']);
            }
            if (Schema::hasColumn('trx_pelanggan_layanan', 'nama_kategori_bandwith')) {
                DB::table('trx_pelanggan_layanan')
                    ->where('nama_kategori_bandwith', 'like', '%UP TO NEW%')
                    ->orWhere('nama_kategori_bandwith', 'like', '%up to new%')
                    ->update(['nama_kategori_bandwith' => 'LOCALLOOP']);
            }
        }

        // 6. Update trx_histori_layanan_pelanggan
        if (Schema::hasTable('trx_histori_layanan_pelanggan')) {
            if (Schema::hasColumn('trx_histori_layanan_pelanggan', 'old_layanan')) {
                DB::statement("UPDATE trx_histori_layanan_pelanggan SET old_layanan = REPLACE(old_layanan, 'UP TO NEW', 'LOCALLOOP') WHERE old_layanan LIKE '%UP TO NEW%'");
            }
            if (Schema::hasColumn('trx_histori_layanan_pelanggan', 'new_layanan')) {
                DB::statement("UPDATE trx_histori_layanan_pelanggan SET new_layanan = REPLACE(new_layanan, 'UP TO NEW', 'LOCALLOOP') WHERE new_layanan LIKE '%UP TO NEW%'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback as UP TO NEW is permanently deprecated in favor of LOCALLOOP
    }
};

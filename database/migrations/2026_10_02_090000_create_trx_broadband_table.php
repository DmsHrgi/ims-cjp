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
        if (!Schema::hasTable('trx_broadband')) {
            Schema::create('trx_broadband', function (Blueprint $table) {
                $table->string('nomor_internet', 50)->primary();
                $table->string('nik_penduduk', 50)->nullable()->index();
                $table->string('nama_pelanggan', 200)->nullable()->index();
                $table->string('rt_pasang', 10)->nullable();
                $table->string('rw_pasang', 10)->nullable();
                $table->string('nomor_bangunan', 50)->nullable();
                $table->text('alamat_pasang')->nullable();
                $table->string('kode_wilayah_kelurahan_pasang', 50)->nullable()->index();
                $table->string('jenis_bangunan', 50)->nullable();
                $table->text('lon_lat')->nullable();
                $table->text('loc_maps')->nullable();
                $table->text('note_request')->nullable();
                $table->string('kode_bandwith', 50)->nullable()->index();
                $table->string('kode_pop', 50)->nullable()->index();
                $table->string('ont_us', 50)->nullable();
                $table->string('ont_ps', 50)->nullable();
                $table->string('status_reg', 10)->nullable()->index();
                $table->string('media_akses', 100)->nullable();
                $table->string('ppn', 10)->nullable();
                $table->string('ppn_nom', 20)->nullable();
                $table->string('potongan', 20)->nullable();
                $table->string('potongan_note', 100)->nullable();
                $table->string('last_month_billing', 5)->nullable()->index();
                $table->string('last_year_billing', 5)->nullable()->index();
                $table->integer('periode_billing')->nullable();
                $table->string('jns_notif', 10)->nullable();
                $table->string('is_termin', 10)->nullable()->index();
                $table->string('is_suspend', 10)->nullable()->index();
                $table->string('count_suspend', 10)->nullable();
                $table->string('is_denda', 10)->nullable();
                $table->string('islock', 5)->nullable();
                $table->string('prorate', 5)->nullable();
                $table->dateTime('date_create')->nullable();
                $table->string('user_create', 50)->nullable();
                $table->dateTime('date_update')->nullable();
                $table->string('user_update', 50)->nullable();
                $table->string('hide', 5)->nullable()->index();
                $table->string('mitra', 50)->nullable();
                $table->string('group_layanan', 50)->nullable()->index();
                $table->string('nama_sales', 50)->nullable();
                $table->string('olt', 100)->nullable();
                $table->string('index_olt', 100)->nullable();
                $table->tinyInteger('is_login')->default(0)->comment('0 = Belum pernah login/onboarding, 1 = Sudah pernah login');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_broadband');
    }
};

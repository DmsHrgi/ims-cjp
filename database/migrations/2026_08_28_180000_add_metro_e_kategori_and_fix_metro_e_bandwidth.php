<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('m_bandwith_kategori')) {
            $metroKat = DB::table('m_bandwith_kategori')
                ->where('nama_kategori_bandwith', 'METRO E')
                ->orWhere('kode_kategori_bandwith', 'KB_METROE')
                ->first();

            if (!$metroKat) {
                DB::table('m_bandwith_kategori')->insert([
                    'kode_kategori_bandwith' => 'KB_METROE',
                    'nama_kategori_bandwith' => 'METRO E',
                    'alias_nama_kategori'    => 'METRO E',
                    'biaya_reg'              => '100000',
                    'ppn_reg'                => 2,
                    'ppn_reg_nom'            => '0.11',
                    'ppn_bill'               => 1,
                    'ppn_bill_nom'           => '0.11',
                    'disable'                => 0,
                    'hide'                   => '0',
                    'date_create'            => now(),
                    'user_create'            => 'SYSTEM',
                ]);
                $metroKatKode = 'KB_METROE';
            } else {
                $metroKatKode = $metroKat->kode_kategori_bandwith;
            }

            // Perbaiki data register / pelanggan yang group_layanan METRO E tapi bandwidth kategorinya LOCALLOOP / lainnya
            if (Schema::hasTable('trx_batchjob_register') && Schema::hasTable('m_bandwith')) {
                $registers = DB::table('trx_batchjob_register')
                    ->where(function ($q) {
                        $q->where('group_layanan', 'METRO E')
                          ->orWhere('group_layanan', 'like', '%METRO E%');
                    })
                    ->get();

                foreach ($registers as $reg) {
                    $bw = DB::table('m_bandwith')->where('kode_bandwith', $reg->kode_bandwith)->first();
                    $nominal = $bw->nominal_bandwith ?? preg_replace('/[^0-9]/', '', (string)$reg->kode_bandwith) ?? '10';
                    if (empty($nominal)) $nominal = '10';

                    $metroBwKode = 'CUST-METROE-' . $nominal . 'M';

                    $checkMetroBw = DB::table('m_bandwith')->where('kode_bandwith', $metroBwKode)->first();
                    if (!$checkMetroBw) {
                        DB::table('m_bandwith')->insert([
                            'kode_bandwith'          => $metroBwKode,
                            'nominal_bandwith'       => $nominal,
                            'harga_bandwith'         => $bw->harga_bandwith ?? '300000',
                            'kode_kategori_bandwith' => $metroKatKode,
                            'user_create'            => 'SYSTEM',
                            'date_create'            => now(),
                            'hide'                   => '0',
                            'disable'                => '0'
                        ]);
                    } elseif ($checkMetroBw->kode_kategori_bandwith !== $metroKatKode) {
                        DB::table('m_bandwith')
                            ->where('kode_bandwith', $metroBwKode)
                            ->update(['kode_kategori_bandwith' => $metroKatKode]);
                    }

                    // Update register
                    DB::table('trx_batchjob_register')
                        ->where('nomor_internet', $reg->nomor_internet)
                        ->update(['kode_bandwith' => $metroBwKode]);

                    if (Schema::hasTable('tbl_customer_all')) {
                        DB::table('tbl_customer_all')
                            ->where('nomor_internet', $reg->nomor_internet)
                            ->update(['kode_bandwith' => $metroBwKode]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Status Hide
        DB::table('m_status_hide')->upsert(
            [['hide' => '0', 'desc_hide' => 'Tampil'], ['hide' => '1', 'desc_hide' => 'Sembunyi']],
            ['hide'],
            ['desc_hide']
        );

        // 2. Status Registrasi
        DB::table('m_status_registrasi')->upsert(
            [
                ['status_reg' => '01', 'desc_registrasi' => 'Baru', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['status_reg' => '02', 'desc_registrasi' => 'Survey', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['status_reg' => '03', 'desc_registrasi' => 'Instalasi', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['status_reg' => '04', 'desc_registrasi' => 'Aktif', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['status_reg' => '05', 'desc_registrasi' => 'Gagal Pasang', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
            ],
            ['status_reg'],
            ['desc_registrasi', 'hide']
        );

        // 3. Jenis Bangunan
        DB::table('m_jns_bangunan')->upsert(
            [
                ['kode_bangunan' => 'BG01', 'jenis_bangunan' => 'RUMAH-PRIBADI', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bangunan' => 'BG02', 'jenis_bangunan' => 'RUMAH-KANTOR', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bangunan' => 'BG03', 'jenis_bangunan' => 'RUKO', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bangunan' => 'BG04', 'jenis_bangunan' => 'KANTOR', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bangunan' => 'BG05', 'jenis_bangunan' => 'APARTEMEN', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
            ],
            ['kode_bangunan'],
            ['jenis_bangunan', 'hide']
        );

        // 4. Kategori Bandwith
        DB::table('m_bandwith_kategori')->upsert(
            [
                ['kode_kategori_bandwith' => 'MED', 'nama_kategori_bandwith' => 'MEDIANET', 'alias_nama_kategori' => 'Medianet', 'biaya_reg' => '500000', 'ppn_reg' => 11, 'ppn_reg_nom' => '55000', 'ppn_bill' => 11, 'ppn_bill_nom' => '55000', 'disable' => 0, 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_kategori_bandwith' => 'LST', 'nama_kategori_bandwith' => 'LASTMILE', 'alias_nama_kategori' => 'Lastmile', 'biaya_reg' => '1000000', 'ppn_reg' => 11, 'ppn_reg_nom' => '11000', 'ppn_bill' => 11, 'ppn_bill_nom' => '11000', 'disable' => 0, 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_kategori_bandwith' => 'COR', 'nama_kategori_bandwith' => 'CORPORATE', 'alias_nama_kategori' => 'Corporate', 'biaya_reg' => '2000000', 'ppn_reg' => 11, 'ppn_reg_nom' => '22000', 'ppn_bill' => 11, 'ppn_bill_nom' => '22000', 'disable' => 0, 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
            ],
            ['kode_kategori_bandwith'],
            ['nama_kategori_bandwith', 'alias_nama_kategori', 'biaya_reg', 'hide']
        );

        // 5. Paket Bandwith
        DB::table('m_bandwith')->upsert(
            [
                ['kode_bandwith' => 'MED-10', 'kode_kategori_bandwith' => 'MED', 'nominal_bandwith' => '10', 'harga_bandwith' => '250000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'MED-15', 'kode_kategori_bandwith' => 'MED', 'nominal_bandwith' => '15', 'harga_bandwith' => '300000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'MED-20', 'kode_kategori_bandwith' => 'MED', 'nominal_bandwith' => '20', 'harga_bandwith' => '350000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'MED-25', 'kode_kategori_bandwith' => 'MED', 'nominal_bandwith' => '25', 'harga_bandwith' => '400000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'LST-50', 'kode_kategori_bandwith' => 'LST', 'nominal_bandwith' => '50', 'harga_bandwith' => '750000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'LST-100', 'kode_kategori_bandwith' => 'LST', 'nominal_bandwith' => '100', 'harga_bandwith' => '1200000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'COR-100', 'kode_kategori_bandwith' => 'COR', 'nominal_bandwith' => '100', 'harga_bandwith' => '5000000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_bandwith' => 'COR-200', 'kode_kategori_bandwith' => 'COR', 'nominal_bandwith' => '200', 'harga_bandwith' => '8000000', 'disable' => '0', 'hide' => '0', 'date_create' => $now, 'user_create' => 'system'],
            ],
            ['kode_bandwith'],
            ['nominal_bandwith', 'harga_bandwith', 'disable', 'hide']
        );

        // 6. Wilayah Indonesia
        DB::table('m_wilayah')->upsert([
            [
                'kode_wilayah' => '3273010001',
                'kode_wilayah_kelurahan' => '3273010001',
                'kode_kelurahan' => '0001',
                'nama_kelurahan' => 'DAGO',
                'kode_wilayah_kecamatan' => '327301',
                'kode_kecamatan' => '01',
                'nama_kecamatan' => 'COBLONG',
                'kode_wilayah_kota' => '3273',
                'kode_kota' => '73',
                'nama_kota' => 'KOTA BANDUNG',
                'kode_wilayah_provinsi' => '32',
                'kode_provinsi' => '32',
                'nama_provinsi' => 'JAWA BARAT',
            ],
            [
                'kode_wilayah' => '3273020001',
                'kode_wilayah_kelurahan' => '3273020001',
                'kode_kelurahan' => '0001',
                'nama_kelurahan' => 'CICENDO',
                'kode_wilayah_kecamatan' => '327302',
                'kode_kecamatan' => '02',
                'nama_kecamatan' => 'CICENDO',
                'kode_wilayah_kota' => '3273',
                'kode_kota' => '73',
                'nama_kota' => 'KOTA BANDUNG',
                'kode_wilayah_provinsi' => '32',
                'kode_provinsi' => '32',
                'nama_provinsi' => 'JAWA BARAT',
            ],
            [
                'kode_wilayah' => '3273030001',
                'kode_wilayah_kelurahan' => '3273030001',
                'kode_kelurahan' => '0001',
                'nama_kelurahan' => 'SUMMER BANDUNG',
                'kode_wilayah_kecamatan' => '327303',
                'kode_kecamatan' => '03',
                'nama_kecamatan' => 'SUMMER BANDUNG',
                'kode_wilayah_kota' => '3273',
                'kode_kota' => '73',
                'nama_kota' => 'KOTA BANDUNG',
                'kode_wilayah_provinsi' => '32',
                'kode_provinsi' => '32',
                'nama_provinsi' => 'JAWA BARAT',
            ],
        ], 'kode_wilayah');

        // 7. Karyawan
        DB::table('tb_m_karyawan')->upsert(
            [
                ['kode_karyawan' => 'K001', 'nama_karyawan' => 'NUNU NUGRAHA', 'cuti' => 0, 'uid' => 'system', 'tinggi' => '170', 'berat' => '65', 'status_kontrak' => 1, 'kendaraan' => '-', 'sim' => '-', 'status_rumah' => '-', 'kantor' => 'PUSAT', 'status_aktif' => '01', 'date_create' => $now, 'user_create' => 'system'],
                ['kode_karyawan' => 'K002', 'nama_karyawan' => 'BUDI SANTOSO', 'cuti' => 0, 'uid' => 'system', 'tinggi' => '175', 'berat' => '70', 'status_kontrak' => 1, 'kendaraan' => '-', 'sim' => '-', 'status_rumah' => '-', 'kantor' => 'PUSAT', 'status_aktif' => '01', 'date_create' => $now, 'user_create' => 'system'],
            ],
            ['kode_karyawan'],
            ['nama_karyawan', 'status_aktif']
        );

        $this->command->info('✅ Semua data master berhasil diisi!');
    }
}
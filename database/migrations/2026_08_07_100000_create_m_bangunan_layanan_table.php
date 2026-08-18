<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel mapping jenis bangunan → kategori layanan
        DB::statement("
            CREATE TABLE IF NOT EXISTS `m_bangunan_layanan` (
                `kode_bangunan` VARCHAR(10) NOT NULL,
                `kode_kategori_bandwith` VARCHAR(20) NOT NULL,
                PRIMARY KEY (`kode_bangunan`, `kode_kategori_bandwith`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // Seed data mapping
        $mappings = [
            // APARTEMEN → SOHO
            ['BN005', 'KB1682'],
            // GEDUNG → SOHO, LASTMILE
            ['BN006', 'KB1682'],
            ['BN006', 'KB58163'],
            // KOS-KOSAN → SOHO, BROADBAND NEW
            ['BN001', 'KB1682'],
            ['BN001', 'KB69771'],
            // OUTDOR/EVENT → LASTMILE, BANDWITDH ON DEMAND
            ['BN007', 'KB58163'],
            ['BN007', 'KBBOD1'],
            // RUKO → SOHO
            ['BN004', 'KB1682'],
            // RUMAH-KANTOR → SOHO, CORPORATE, LASTMILE
            ['BN003', 'KB1682'],
            ['BN003', 'KB22285'],
            ['BN003', 'KB58163'],
            // RUMAH-PRIBADI → SOHO, BROADBAND NEW, BROADBAND FREE
            ['BN002', 'KB1682'],
            ['BN002', 'KB69771'],
            ['BN002', 'KBFRE02'],
        ];

        foreach ($mappings as [$bangunan, $kategori]) {
            DB::table('m_bangunan_layanan')->insertOrIgnore([
                'kode_bangunan' => $bangunan,
                'kode_kategori_bandwith' => $kategori,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('m_bangunan_layanan');
    }
};

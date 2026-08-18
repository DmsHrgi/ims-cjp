<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        
        // Ensure level Admin exists in tb_m_level_pengguna
        if (!DB::table('tb_m_level_pengguna')->where('kode_level', 'lv00001')->exists()) {
            DB::table('tb_m_level_pengguna')->insert([
                'kode_level'  => 'lv00001',
                'level'       => 0,
                'nama_level'  => 'Admin',
                'desc_level'  => 'Administrator Sistem',
                'date_create' => $now,
                'user_create' => 'system',
                'date_update' => $now,
                'user_update' => 'system',
            ]);
        }

        // ============================================
        // AKUN UTAMA: ADMIN
        // ============================================
        $this->createUser(
            kodeKaryawan: 'KR_ADMIN',
            kodePengguna: 'pg_admin',
            username: 'admin',
            password: '123456',
            namaLengkap: 'ADMINISTRATOR',
            level: 'lv00001',
            jabatan: 'Administrator'
        );

        // ============================================
        // AKUN 1: NUNU NUGRAHA
        // ============================================
        $this->createUser(
            kodeKaryawan: 'K001',
            kodePengguna: 'U001',
            username: 'nunu',
            password: 'nunu123',
            namaLengkap: 'NUNU NUGRAHA',
            level: 'L001',
            jabatan: 'Administrator Sistem'
        );

        // ============================================
        // AKUN 2: BUDI SANTOSO
        // ============================================
        $this->createUser(
            kodeKaryawan: 'K002',
            kodePengguna: 'U002',
            username: 'budi',
            password: 'budi123',
            namaLengkap: 'BUDI SANTOSO',
            level: 'L002',
            jabatan: 'Manager Operasional'
        );

        // ============================================
        // AKUN 3: SITI AMINAH
        // ============================================
        $this->createUser(
            kodeKaryawan: 'K003',
            kodePengguna: 'U003',
            username: 'siti',
            password: 'siti123',
            namaLengkap: 'SITI AMINAH',
            level: 'L003',
            jabatan: 'Staff Customer Service'
        );
    }

    private function createUser(
        string $kodeKaryawan,
        string $kodePengguna,
        string $username,
        string $password,
        string $namaLengkap,
        string $level,
        string $jabatan
    ): void {
        $hash = Hash::make($password);
        $now = now();

        // 1) Insert/Update Karyawan (hanya kolom yang ada di tb_m_karyawan)
        if (!DB::table('tb_m_karyawan')->where('kode_karyawan', $kodeKaryawan)->exists()) {
            DB::table('tb_m_karyawan')->insert([
                'kode_karyawan' => $kodeKaryawan,
                'nama_karyawan' => $namaLengkap,
                'kode_jabatan' => $jabatan,
                'cuti' => 0,
                'uid' => 'system',
                'tinggi' => '170',
                'berat' => '65',
                'status_kontrak' => 1,
                'kendaraan' => '-',
                'sim' => '-',
                'status_rumah' => '-',
                'kantor' => 'PUSAT',
                'status_aktif' => '01',
                'date_create' => $now,
                'user_create' => 'system',
            ]);
        } else {
            DB::table('tb_m_karyawan')->where('kode_karyawan', $kodeKaryawan)
                ->update([
                    'nama_karyawan' => $namaLengkap,
                    'kode_jabatan' => $jabatan,
                ]);
        }

        // 2) Insert/Update Pengguna
        if (DB::table('tb_pengguna')->where('username', $username)->exists()) {
            DB::table('tb_pengguna')->where('username', $username)->update([
                'password' => $hash,
                'kode_karyawan' => $kodeKaryawan,
                'kode_level' => $level,
                'status_aktif' => '01',
                'date_update' => $now,
                'user_update' => 'system',
            ]);
        } else {
            DB::table('tb_pengguna')->insert([
                'kode_pengguna' => $kodePengguna,
                'kode_karyawan' => $kodeKaryawan,
                'kode_level' => $level,
                'username' => $username,
                'password' => $hash,
                'status_aktif' => '01',
                'as_sales' => 0,
                'date_create' => $now,
                'user_create' => 'system',
                'date_update' => $now,
                'user_update' => 'system',
            ]);
        }

        $this->command->info("✅ Akun {$username} ({$namaLengkap}) berhasil dibuat!");
    }
}
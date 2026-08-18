<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Sudah login? langsung ke dasbor (perilaku "guest")
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('view_pengguna')
            ->where('username', $data['username'])
            ->first();

        if (! $user) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        // Verifikasi password: mendukung Hash (Bcrypt), MD5 (legacy DB), dan Teks biasa
        $passwordMatch = false;
        if (str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$2a$')) {
            $passwordMatch = Hash::check($data['password'], $user->password);
        } else {
            $passwordMatch = (md5($data['password']) === strtolower($user->password))
                || (md5($data['password']) === $user->password)
                || ($data['password'] === $user->password);
        }

        // Kredensial salah -> pesan umum
        if (! $passwordMatch) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        if (! in_array((string)$user->status_aktif, ['1', '01'], true)) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Akun ini tidak aktif. Hubungi administrator.']);
        }

        // Simpan profil & level lengkap ke session (termasuk lv9812 Teknik)
        $request->session()->put('user', [
            'kode_pengguna' => $user->kode_pengguna,
            'kode_karyawan' => $user->kode_karyawan,
            'username'      => $user->username,
            'nama_karyawan' => strtoupper($user->nama_karyawan ?: $user->username),
            'nama'          => strtoupper($user->nama_karyawan ?: $user->username),
            'foto'          => $user->foto,
            'level'         => $user->nama_level ?: 'Teknik',
            'kode_level'    => $user->kode_level,
            'level_num'     => $user->level,
        ]);

        // Jejak login terakhir
        DB::table('tb_pengguna')
            ->where('kode_pengguna', $user->kode_pengguna)
            ->update([
                'last_ip'   => $request->ip(),
                'las_login' => now()->format('Y-m-d H:i:s'),
            ]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
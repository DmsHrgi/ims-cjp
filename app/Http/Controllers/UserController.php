<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Pastikan hanya role Admin yang dapat mengakses controller ini.
     */
    private function authorizeAdmin()
    {
        $u = session('user', []);
        $userLevel = strtoupper($u['level'] ?? '');
        $kodeLevel = $u['kode_level'] ?? '';
        $isAdmin = ($userLevel === 'ADMIN' || $kodeLevel === 'lv00001' || ($u['username'] ?? '') === 'admin');

        if (!$isAdmin) {
            abort(403, 'Akses Ditolak. Halaman ini hanya dapat diakses oleh Administrator.');
        }
    }

    /**
     * Tampilkan halaman daftar manajemen pengguna.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $search = trim($request->input('q', ''));
        $filterRole = $request->input('role', '');
        $filterStatus = $request->input('status', '');
        $entries = (int) $request->input('entries', 10);
        if (!in_array($entries, [10, 25, 50, 100], true)) {
            $entries = 10;
        }

        // Query view_pengguna
        $query = DB::table('view_pengguna');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nama_jabatan', 'like', "%{$search}%")
                  ->orWhere('nama_level', 'like', "%{$search}%")
                  ->orWhere('kode_pengguna', 'like', "%{$search}%");
            });
        }

        if ($filterRole !== '') {
            $query->where('kode_level', $filterRole);
        }

        if ($filterStatus !== '') {
            if ($filterStatus === '1') {
                $query->whereIn('status_aktif', ['1', '01']);
            } else {
                $query->whereNotIn('status_aktif', ['1', '01']);
            }
        }

        $users = $query->orderBy('nama_karyawan', 'asc')
                       ->paginate($entries)
                       ->appends($request->query());

        // Master level/role
        $roles = DB::table('tb_m_level_pengguna')
            ->orderBy('level', 'asc')
            ->get();

        // Statistik ringkas
        $totalUsers = DB::table('tb_pengguna')->count();
        $activeUsers = DB::table('tb_pengguna')->whereIn('status_aktif', ['1', '01'])->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $totalRoles = $roles->count();

        return view('users.index', compact(
            'users',
            'roles',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalRoles',
            'search',
            'filterRole',
            'filterStatus',
            'entries'
        ));
    }

    /**
     * Simpan user baru beserta rolenya ke database.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:100',
            'username'      => 'required|string|max:100|unique:tb_pengguna,username',
            'password'      => 'required|string|min:6|confirmed',
            'kode_level'    => 'required|string|exists:tb_m_level_pengguna,kode_level',
            'kode_jabatan'  => 'nullable|string|max:50',
            'status_aktif'  => 'required|in:1,0',
        ], [
            'nama_karyawan.required' => 'Nama lengkap / karyawan wajib diisi.',
            'username.required'      => 'Username wajib diisi.',
            'username.unique'        => 'Username sudah digunakan oleh akun lain.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal harus 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'kode_level.required'    => 'Role / Level pengguna wajib dipilih.',
            'kode_level.exists'      => 'Role yang dipilih tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $currentUser = session('user.username', 'admin');
            $now = now();

            // 1. Generate kode_karyawan unik
            do {
                $kodeKaryawan = 'KR' . mt_rand(10000, 99999);
            } while (DB::table('tb_m_karyawan')->where('kode_karyawan', $kodeKaryawan)->exists());

            // 2. Generate kode_pengguna unik
            do {
                $kodePengguna = 'pg' . mt_rand(10000, 99999);
            } while (DB::table('tb_pengguna')->where('kode_pengguna', $kodePengguna)->exists());

            // 3. Simpan ke tb_m_karyawan
            DB::table('tb_m_karyawan')->insert([
                'kode_karyawan'  => $kodeKaryawan,
                'nama_karyawan'  => strtoupper($validated['nama_karyawan']),
                'kode_jabatan'   => $validated['kode_jabatan'] ?? 'Staff',
                'cuti'           => 0,
                'uid'            => 'system',
                'tinggi'         => '170',
                'berat'          => '65',
                'status_kontrak' => 1,
                'kendaraan'      => '-',
                'sim'            => '-',
                'status_rumah'   => '-',
                'kantor'         => 'PUSAT',
                'status_aktif'   => $validated['status_aktif'] == '1' ? '01' : '02',
                'date_create'    => $now,
                'user_create'    => substr($currentUser, 0, 20),
            ]);

            // Cek apakah level sales/marketing
            $levelData = DB::table('tb_m_level_pengguna')->where('kode_level', $validated['kode_level'])->first();
            $asSales = 0;
            if ($levelData && (str_contains(strtolower($levelData->nama_level ?? ''), 'sales') || str_contains(strtolower($levelData->nama_level ?? ''), 'salses') || str_contains(strtolower($levelData->nama_level ?? ''), 'mitra'))) {
                $asSales = 1;
            }

            // 4. Simpan ke tb_pengguna
            DB::table('tb_pengguna')->insert([
                'kode_pengguna' => $kodePengguna,
                'kode_karyawan' => $kodeKaryawan,
                'kode_level'    => $validated['kode_level'],
                'username'      => trim($validated['username']),
                'password'      => Hash::make($validated['password']),
                'status_aktif'  => $validated['status_aktif'] == '1' ? '1' : '0',
                'as_sales'      => $asSales,
                'date_create'   => $now,
                'user_create'   => substr($currentUser, 0, 20),
                'date_update'   => $now,
                'user_update'   => substr($currentUser, 0, 20),
            ]);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "Pengguna baru '{$validated['username']}' ({$validated['nama_karyawan']}) berhasil ditambahkan.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Gagal menambahkan pengguna: ' . $e->getMessage()]);
        }
    }

    /**
     * Perbarui data user dan role.
     */
    public function update(Request $request, $kode_pengguna)
    {
        $this->authorizeAdmin();

        $user = DB::table('tb_pengguna')->where('kode_pengguna', $kode_pengguna)->first();
        if (!$user) {
            return redirect()->route('users.index')->withErrors(['error' => 'Data pengguna tidak ditemukan.']);
        }

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:100',
            'username'      => [
                'required',
                'string',
                'max:100',
                Rule::unique('tb_pengguna', 'username')->ignore($kode_pengguna, 'kode_pengguna')
            ],
            'password'      => 'nullable|string|min:6|confirmed',
            'kode_level'    => 'required|string|exists:tb_m_level_pengguna,kode_level',
            'kode_jabatan'  => 'nullable|string|max:50',
            'status_aktif'  => 'required|in:1,0',
        ], [
            'nama_karyawan.required' => 'Nama lengkap / karyawan wajib diisi.',
            'username.required'      => 'Username wajib diisi.',
            'username.unique'        => 'Username sudah digunakan oleh akun lain.',
            'password.min'           => 'Password minimal harus 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password baru tidak cocok.',
            'kode_level.required'    => 'Role / Level pengguna wajib dipilih.',
            'kode_level.exists'      => 'Role yang dipilih tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $currentUser = session('user.username', 'admin');
            $now = now();

            // 1. Update tb_m_karyawan jika ada
            if ($user->kode_karyawan) {
                DB::table('tb_m_karyawan')
                    ->where('kode_karyawan', $user->kode_karyawan)
                    ->update([
                        'nama_karyawan' => strtoupper($validated['nama_karyawan']),
                        'kode_jabatan'  => $validated['kode_jabatan'] ?? 'Staff',
                        'status_aktif'  => $validated['status_aktif'] == '1' ? '01' : '02',
                    ]);
            } else {
                // Buatkan kode_karyawan jika sebelumnya null
                do {
                    $newKodeKaryawan = 'KR' . mt_rand(10000, 99999);
                } while (DB::table('tb_m_karyawan')->where('kode_karyawan', $newKodeKaryawan)->exists());

                DB::table('tb_m_karyawan')->insert([
                    'kode_karyawan'  => $newKodeKaryawan,
                    'nama_karyawan'  => strtoupper($validated['nama_karyawan']),
                    'kode_jabatan'   => $validated['kode_jabatan'] ?? 'Staff',
                    'cuti'           => 0,
                    'uid'            => 'system',
                    'tinggi'         => '170',
                    'berat'          => '65',
                    'status_kontrak' => 1,
                    'kendaraan'      => '-',
                    'sim'            => '-',
                    'status_rumah'   => '-',
                    'kantor'         => 'PUSAT',
                    'status_aktif'   => $validated['status_aktif'] == '1' ? '01' : '02',
                    'date_create'    => $now,
                    'user_create'    => substr($currentUser, 0, 20),
                ]);

                DB::table('tb_pengguna')
                    ->where('kode_pengguna', $kode_pengguna)
                    ->update(['kode_karyawan' => $newKodeKaryawan]);
            }

            // Cek sales level
            $levelData = DB::table('tb_m_level_pengguna')->where('kode_level', $validated['kode_level'])->first();
            $asSales = 0;
            if ($levelData && (str_contains(strtolower($levelData->nama_level ?? ''), 'sales') || str_contains(strtolower($levelData->nama_level ?? ''), 'salses') || str_contains(strtolower($levelData->nama_level ?? ''), 'mitra'))) {
                $asSales = 1;
            }

            $userUpdateData = [
                'username'     => trim($validated['username']),
                'kode_level'   => $validated['kode_level'],
                'status_aktif' => $validated['status_aktif'] == '1' ? '1' : '0',
                'as_sales'     => $asSales,
                'date_update'  => $now,
                'user_update'  => substr($currentUser, 0, 20),
            ];

            // Jika password diisi, update password
            if (!empty($validated['password'])) {
                $userUpdateData['password'] = Hash::make($validated['password']);
            }

            DB::table('tb_pengguna')
                ->where('kode_pengguna', $kode_pengguna)
                ->update($userUpdateData);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "Data pengguna '{$validated['username']}' berhasil diperbarui.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Gagal memperbarui data pengguna: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy($kode_pengguna)
    {
        $this->authorizeAdmin();

        $currentLoggedIn = session('user.kode_pengguna');
        if ($currentLoggedIn === $kode_pengguna) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan saat ini.']);
        }

        $user = DB::table('tb_pengguna')->where('kode_pengguna', $kode_pengguna)->first();
        if (!$user) {
            return redirect()->route('users.index')->withErrors(['error' => 'Data pengguna tidak ditemukan.']);
        }

        if ($user->username === 'admin') {
            return back()->withErrors(['error' => 'Akun Superadmin utama tidak dapat dihapus.']);
        }

        try {
            DB::beginTransaction();

            $username = $user->username;
            DB::table('tb_pengguna')->where('kode_pengguna', $kode_pengguna)->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "Pengguna '{$username}' berhasil dihapus dari sistem.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus pengguna: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle status aktif/nonaktif akun pengguna secara cepat.
     */
    public function toggleStatus($kode_pengguna)
    {
        $this->authorizeAdmin();

        $currentLoggedIn = session('user.kode_pengguna');
        if ($currentLoggedIn === $kode_pengguna) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.']);
        }

        $user = DB::table('tb_pengguna')->where('kode_pengguna', $kode_pengguna)->first();
        if (!$user) {
            return redirect()->route('users.index')->withErrors(['error' => 'Data pengguna tidak ditemukan.']);
        }

        $isAktif = in_array((string)$user->status_aktif, ['1', '01'], true);
        $newStatus = $isAktif ? '0' : '1';
        $statusLabel = $isAktif ? 'dinonaktifkan' : 'diaktifkan';

        DB::table('tb_pengguna')
            ->where('kode_pengguna', $kode_pengguna)
            ->update([
                'status_aktif' => $newStatus,
                'date_update'  => now(),
                'user_update'  => substr(session('user.username', 'admin'), 0, 20),
            ]);

        return redirect()->route('users.index')
            ->with('success', "Akun '{$user->username}' berhasil {$statusLabel}.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OltDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OltController extends Controller
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
     * Pastikan tabel m_olt siap digunakan.
     */
    private function ensureTableExists()
    {
        try {
            if (!Schema::hasTable('m_olt')) {
                Schema::create('m_olt', function ($table) {
                    $table->id();
                    $table->string('name', 100);
                    $table->string('hostname', 100)->nullable();
                    $table->string('ip_address', 50);
                    $table->string('vendor', 100);
                    $table->string('model', 100)->nullable();
                    $table->string('status', 20)->default('Up');
                    $table->integer('snmp_port')->default(161);
                    $table->string('snmp_version', 20)->default('v2c');
                    $table->string('snmp_community', 100)->default('public');
                    $table->string('location', 255)->nullable();
                    $table->text('description')->nullable();
                    $table->string('user_create', 50)->nullable();
                    $table->string('user_update', 50)->nullable();
                    $table->timestamps();
                });
            } else {
                // Pastikan seluruh kolom yang dibutuhkan ada jika tabel sudah pernah dibuat sebelumnya
                Schema::table('m_olt', function ($table) {
                    if (!Schema::hasColumn('m_olt', 'name')) {
                        $table->string('name', 100)->default('');
                    }
                    if (!Schema::hasColumn('m_olt', 'hostname')) {
                        $table->string('hostname', 100)->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'ip_address')) {
                        $table->string('ip_address', 50)->default('');
                    }
                    if (!Schema::hasColumn('m_olt', 'vendor')) {
                        $table->string('vendor', 100)->default('');
                    }
                    if (!Schema::hasColumn('m_olt', 'model')) {
                        $table->string('model', 100)->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'status')) {
                        $table->string('status', 20)->default('Up');
                    }
                    if (!Schema::hasColumn('m_olt', 'snmp_port')) {
                        $table->integer('snmp_port')->default(161);
                    }
                    if (!Schema::hasColumn('m_olt', 'snmp_version')) {
                        $table->string('snmp_version', 20)->default('v2c');
                    }
                    if (!Schema::hasColumn('m_olt', 'snmp_community')) {
                        $table->string('snmp_community', 100)->default('public');
                    }
                    if (!Schema::hasColumn('m_olt', 'location')) {
                        $table->string('location', 255)->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'description')) {
                        $table->text('description')->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'user_create')) {
                        $table->string('user_create', 50)->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'user_update')) {
                        $table->string('user_update', 50)->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!Schema::hasColumn('m_olt', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }

            // Jika tabel kosong, masukkan sample data
            if (DB::table('m_olt')->count() === 0) {
                DB::table('m_olt')->insert([
                    'name' => 'OLT_BAGONG',
                    'hostname' => 'aplikasi',
                    'ip_address' => '172.168.12.102',
                    'vendor' => 'ZTE',
                    'model' => 'C320',
                    'status' => 'Up',
                    'snmp_port' => 161,
                    'snmp_version' => 'v2c',
                    'snmp_community' => 'K4yu4gung',
                    'location' => 'Data Center Bagong / Rack 01',
                    'description' => 'Primary distribution OLT device',
                    'user_create' => 'SYSTEM',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Ignored if handled
        }
    }

    /**
     * Tampilkan halaman daftar perangkat OLT.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $search = trim($request->input('q', ''));
        $filterVendor = trim($request->input('vendor', ''));
        $filterStatus = trim($request->input('status', ''));
        $viewMode = $request->input('view', 'card'); // 'card' atau 'table'

        $query = DB::table('m_olt');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('hostname', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($filterVendor !== '') {
            $query->where('vendor', $filterVendor);
        }

        if ($filterStatus !== '') {
            $query->where('status', $filterStatus);
        }

        $oltDevices = $query->orderBy('id', 'desc')->paginate(12)->appends($request->query());

        // Master vendor list untuk filter
        $vendorList = DB::table('m_olt')
            ->select('vendor')
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->distinct()
            ->pluck('vendor');

        // Statistik ringkas
        $totalDevices = DB::table('m_olt')->count();
        $totalUp = DB::table('m_olt')->where('status', 'Up')->count();
        $totalDown = DB::table('m_olt')->where('status', 'Down')->count();
        $totalVendors = $vendorList->count();

        return view('olt.index', compact(
            'oltDevices',
            'vendorList',
            'totalDevices',
            'totalUp',
            'totalDown',
            'totalVendors',
            'search',
            'filterVendor',
            'filterStatus',
            'viewMode'
        ));
    }

    /**
     * Simpan data OLT baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'hostname'       => 'nullable|string|max:100',
            'ip_address'     => 'required|string|max:50',
            'vendor'         => 'required|string|max:100',
            'model'          => 'nullable|string|max:100',
            'status'         => 'nullable|string|in:Up,Down',
            'snmp_port'      => 'required|integer|min:1|max:65535',
            'snmp_version'   => 'required|string|in:v1,v2c,v3',
            'snmp_community' => 'required|string|max:100',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:500',
        ], [
            'name.required'           => 'Nama OLT (Name) wajib diisi.',
            'ip_address.required'     => 'IP Address wajib diisi.',
            'vendor.required'         => 'Vendor perangkat OLT wajib diisi.',
            'snmp_port.required'      => 'SNMP Port wajib diisi.',
            'snmp_version.required'   => 'SNMP Version wajib dipilih.',
            'snmp_community.required' => 'SNMP Community wajib diisi.',
        ]);

        $u = session('user', []);
        $username = $u['username'] ?? 'Admin';

        DB::table('m_olt')->insert([
            'name'           => $validated['name'],
            'hostname'       => $validated['hostname'] ?? null,
            'ip_address'     => $validated['ip_address'],
            'vendor'         => $validated['vendor'],
            'model'          => $validated['model'] ?? null,
            'status'         => $validated['status'] ?? 'Up',
            'snmp_port'      => (int) $validated['snmp_port'],
            'snmp_version'   => $validated['snmp_version'],
            'snmp_community' => $validated['snmp_community'],
            'location'       => $validated['location'] ?? null,
            'description'    => $validated['description'] ?? null,
            'user_create'    => $username,
            'user_update'    => $username,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('olt.index')->with('success', 'Perangkat OLT baru berhasil ditambahkan!');
    }

    /**
     * Update data OLT yang ada.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'hostname'       => 'nullable|string|max:100',
            'ip_address'     => 'required|string|max:50',
            'vendor'         => 'required|string|max:100',
            'model'          => 'nullable|string|max:100',
            'status'         => 'nullable|string|in:Up,Down',
            'snmp_port'      => 'required|integer|min:1|max:65535',
            'snmp_version'   => 'required|string|in:v1,v2c,v3',
            'snmp_community' => 'required|string|max:100',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:500',
        ]);

        $u = session('user', []);
        $username = $u['username'] ?? 'Admin';

        $affected = DB::table('m_olt')->where('id', $id)->update([
            'name'           => $validated['name'],
            'hostname'       => $validated['hostname'] ?? null,
            'ip_address'     => $validated['ip_address'],
            'vendor'         => $validated['vendor'],
            'model'          => $validated['model'] ?? null,
            'status'         => $validated['status'] ?? 'Up',
            'snmp_port'      => (int) $validated['snmp_port'],
            'snmp_version'   => $validated['snmp_version'],
            'snmp_community' => $validated['snmp_community'],
            'location'       => $validated['location'] ?? null,
            'description'    => $validated['description'] ?? null,
            'user_update'    => $username,
            'updated_at'     => now(),
        ]);

        return redirect()->route('olt.index')->with('success', 'Data perangkat OLT berhasil diperbarui!');
    }

    /**
     * Hapus perangkat OLT.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        DB::table('m_olt')->where('id', $id)->delete();

        return redirect()->route('olt.index')->with('success', 'Perangkat OLT berhasil dihapus.');
    }

    /**
     * Test koneksi / ping status OLT.
     */
    public function testConnection(Request $request, $id)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $olt = DB::table('m_olt')->where('id', $id)->first();
        if (!$olt) {
            return response()->json(['status' => 'error', 'message' => 'Perangkat OLT tidak ditemukan.'], 404);
        }

        $ip = $olt->ip_address;
        $port = (int) ($olt->snmp_port ?: 161);
        $isReachable = false;
        $responseTimeMs = null;

        // Cek soket port / ping cepat
        $startTime = microtime(true);
        $socket = @fsockopen($ip, $port, $errno, $errstr, 1.5);
        if ($socket) {
            $isReachable = true;
            fclose($socket);
            $responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);
        } else {
            // Cek port ICMP / port 80 / fallback check
            $socket80 = @fsockopen($ip, 80, $errno2, $errstr2, 1.0);
            if ($socket80) {
                $isReachable = true;
                fclose($socket80);
                $responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);
            }
        }

        // Update status di database jika ingin auto sync
        $newStatus = $isReachable ? 'Up' : 'Down';
        DB::table('m_olt')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'reachable' => $isReachable,
            'device_status' => $newStatus,
            'ip' => $ip,
            'port' => $port,
            'latency_ms' => $responseTimeMs,
            'message' => $isReachable 
                ? "Koneksi ke {$olt->name} ({$ip}) BERHASIL (Latensi: {$responseTimeMs} ms)." 
                : "Tidak dapat terhubung ke {$olt->name} ({$ip}:{$port}).",
        ]);
    }
}

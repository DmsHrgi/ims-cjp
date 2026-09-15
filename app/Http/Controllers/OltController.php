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
     * Pastikan tabel m_olt siap digunakan dan memiliki seluruh kolom yang diperlukan.
     */
    private function ensureTableExists()
    {
        try {
            if (!Schema::hasTable('m_olt')) {
                Schema::create('m_olt', function ($table) {
                    $table->id();
                    $table->string('kode_olt', 50)->nullable()->unique();
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
                // Modifikasi kolom lama jika perlu
                try {
                    if (Schema::hasColumn('m_olt', 'kode_olt')) {
                        DB::statement("ALTER TABLE `m_olt` MODIFY `kode_olt` VARCHAR(50) NULL DEFAULT NULL");
                    }
                } catch (\Throwable $e) {}

                // Tambahkan kolom baru jika belum ada
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
                $sampleData = [
                    'name'           => 'OLT_BAGONG',
                    'hostname'       => 'aplikasi',
                    'ip_address'     => '172.168.12.102',
                    'vendor'         => 'ZTE',
                    'model'          => 'C320',
                    'status'         => 'Up',
                    'snmp_port'      => 161,
                    'snmp_version'   => 'v2c',
                    'snmp_community' => 'K4yu4gung',
                    'location'       => 'Data Center Bagong / Rack 01',
                    'description'    => 'Primary distribution OLT device',
                    'user_create'    => 'SYSTEM',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                if (Schema::hasColumn('m_olt', 'kode_olt')) {
                    $sampleData['kode_olt'] = 'OLT00001';
                }
                if (Schema::hasColumn('m_olt', 'name_olt')) {
                    $sampleData['name_olt'] = 'OLT_BAGONG';
                }

                DB::table('m_olt')->insert($sampleData);
            }
        } catch (\Throwable $e) {
            // Ignored if handled
        }
    }

    /**
     * Generate Kode OLT unik jika kolom kode_olt tersedia di tabel m_olt.
     */
    private function generateKodeOlt()
    {
        try {
            if (!Schema::hasColumn('m_olt', 'kode_olt')) {
                return null;
            }

            $lastRecord = DB::table('m_olt')
                ->where('kode_olt', 'like', 'OLT%')
                ->orderBy('kode_olt', 'desc')
                ->value('kode_olt');

            $nextNumber = 1;
            if ($lastRecord && preg_match('/OLT(\d+)/i', $lastRecord, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            } else {
                $total = DB::table('m_olt')->count();
                $nextNumber = $total + 1;
            }

            return 'OLT' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        } catch (\Throwable $e) {
            return 'OLT' . rand(10000, 99999);
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

        $hasIdCol = Schema::hasColumn('m_olt', 'id');
        $hasKodeCol = Schema::hasColumn('m_olt', 'kode_olt');
        $hasNameCol = Schema::hasColumn('m_olt', 'name');
        $hasNameOltCol = Schema::hasColumn('m_olt', 'name_olt');

        $query = DB::table('m_olt');

        if ($search !== '') {
            $query->where(function ($q) use ($search, $hasNameCol, $hasNameOltCol, $hasKodeCol) {
                if ($hasNameCol) $q->orWhere('name', 'like', "%{$search}%");
                if ($hasNameOltCol) $q->orWhere('name_olt', 'like', "%{$search}%");
                if ($hasKodeCol) $q->orWhere('kode_olt', 'like', "%{$search}%");
                if (Schema::hasColumn('m_olt', 'hostname')) $q->orWhere('hostname', 'like', "%{$search}%");
                if (Schema::hasColumn('m_olt', 'ip_address')) $q->orWhere('ip_address', 'like', "%{$search}%");
                if (Schema::hasColumn('m_olt', 'vendor')) $q->orWhere('vendor', 'like', "%{$search}%");
                if (Schema::hasColumn('m_olt', 'model')) $q->orWhere('model', 'like', "%{$search}%");
                if (Schema::hasColumn('m_olt', 'location')) $q->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($filterVendor !== '' && Schema::hasColumn('m_olt', 'vendor')) {
            $query->where('vendor', $filterVendor);
        }

        if ($filterStatus !== '' && Schema::hasColumn('m_olt', 'status')) {
            $query->where('status', $filterStatus);
        }

        $orderCol = $hasIdCol ? 'id' : ($hasKodeCol ? 'kode_olt' : (Schema::hasColumn('m_olt', 'created_at') ? 'created_at' : 'name'));
        $rawDevices = $query->orderBy($orderCol, 'desc')->paginate(12)->appends($request->query());

        // Standardize object properties so view always has id, name, etc.
        $oltDevices = $rawDevices->through(function ($item) {
            if (!isset($item->id) || empty($item->id)) {
                $item->id = $item->kode_olt ?? rand(1, 9999);
            }
            if (empty($item->name)) {
                $item->name = $item->name_olt ?? ($item->kode_olt ?? 'OLT Device');
            }
            if (!isset($item->hostname)) $item->hostname = null;
            if (!isset($item->ip_address)) $item->ip_address = '-';
            if (!isset($item->vendor)) $item->vendor = 'Unknown';
            if (!isset($item->model)) $item->model = null;
            if (!isset($item->status)) $item->status = 'Up';
            if (!isset($item->snmp_port)) $item->snmp_port = 161;
            if (!isset($item->snmp_version)) $item->snmp_version = 'v2c';
            if (!isset($item->snmp_community)) $item->snmp_community = 'public';
            if (!isset($item->location)) $item->location = null;
            if (!isset($item->description)) $item->description = $item->note_olt ?? null;
            return $item;
        });

        // Master vendor list untuk filter
        $vendorList = collect([]);
        if (Schema::hasColumn('m_olt', 'vendor')) {
            $vendorList = DB::table('m_olt')
                ->select('vendor')
                ->whereNotNull('vendor')
                ->where('vendor', '!=', '')
                ->distinct()
                ->pluck('vendor');
        }

        // Statistik ringkas
        $totalDevices = DB::table('m_olt')->count();
        $totalUp = Schema::hasColumn('m_olt', 'status') ? DB::table('m_olt')->where('status', 'Up')->count() : $totalDevices;
        $totalDown = Schema::hasColumn('m_olt', 'status') ? DB::table('m_olt')->where('status', 'Down')->count() : 0;
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

        // Bangun data insert yang aman sesuai kolom yang tersedia
        $dataToInsert = [];

        if (Schema::hasColumn('m_olt', 'kode_olt')) {
            $dataToInsert['kode_olt'] = $this->generateKodeOlt();
        }
        if (Schema::hasColumn('m_olt', 'name')) {
            $dataToInsert['name'] = $validated['name'];
        }
        if (Schema::hasColumn('m_olt', 'name_olt')) {
            $dataToInsert['name_olt'] = $validated['name'];
        }
        if (Schema::hasColumn('m_olt', 'hostname')) {
            $dataToInsert['hostname'] = $validated['hostname'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'ip_address')) {
            $dataToInsert['ip_address'] = $validated['ip_address'];
        }
        if (Schema::hasColumn('m_olt', 'vendor')) {
            $dataToInsert['vendor'] = $validated['vendor'];
        }
        if (Schema::hasColumn('m_olt', 'model')) {
            $dataToInsert['model'] = $validated['model'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'status')) {
            $dataToInsert['status'] = $validated['status'] ?? 'Up';
        }
        if (Schema::hasColumn('m_olt', 'snmp_port')) {
            $dataToInsert['snmp_port'] = (int) $validated['snmp_port'];
        }
        if (Schema::hasColumn('m_olt', 'snmp_version')) {
            $dataToInsert['snmp_version'] = $validated['snmp_version'];
        }
        if (Schema::hasColumn('m_olt', 'snmp_community')) {
            $dataToInsert['snmp_community'] = $validated['snmp_community'];
        }
        if (Schema::hasColumn('m_olt', 'location')) {
            $dataToInsert['location'] = $validated['location'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'description')) {
            $dataToInsert['description'] = $validated['description'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'note_olt')) {
            $dataToInsert['note_olt'] = $validated['description'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'user_create')) {
            $dataToInsert['user_create'] = $username;
        }
        if (Schema::hasColumn('m_olt', 'user_update')) {
            $dataToInsert['user_update'] = $username;
        }
        if (Schema::hasColumn('m_olt', 'created_at')) {
            $dataToInsert['created_at'] = now();
        }
        if (Schema::hasColumn('m_olt', 'updated_at')) {
            $dataToInsert['updated_at'] = now();
        }

        DB::table('m_olt')->insert($dataToInsert);

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

        $dataToUpdate = [];

        if (Schema::hasColumn('m_olt', 'name')) {
            $dataToUpdate['name'] = $validated['name'];
        }
        if (Schema::hasColumn('m_olt', 'name_olt')) {
            $dataToUpdate['name_olt'] = $validated['name'];
        }
        if (Schema::hasColumn('m_olt', 'hostname')) {
            $dataToUpdate['hostname'] = $validated['hostname'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'ip_address')) {
            $dataToUpdate['ip_address'] = $validated['ip_address'];
        }
        if (Schema::hasColumn('m_olt', 'vendor')) {
            $dataToUpdate['vendor'] = $validated['vendor'];
        }
        if (Schema::hasColumn('m_olt', 'model')) {
            $dataToUpdate['model'] = $validated['model'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'status')) {
            $dataToUpdate['status'] = $validated['status'] ?? 'Up';
        }
        if (Schema::hasColumn('m_olt', 'snmp_port')) {
            $dataToUpdate['snmp_port'] = (int) $validated['snmp_port'];
        }
        if (Schema::hasColumn('m_olt', 'snmp_version')) {
            $dataToUpdate['snmp_version'] = $validated['snmp_version'];
        }
        if (Schema::hasColumn('m_olt', 'snmp_community')) {
            $dataToUpdate['snmp_community'] = $validated['snmp_community'];
        }
        if (Schema::hasColumn('m_olt', 'location')) {
            $dataToUpdate['location'] = $validated['location'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'description')) {
            $dataToUpdate['description'] = $validated['description'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'note_olt')) {
            $dataToUpdate['note_olt'] = $validated['description'] ?? null;
        }
        if (Schema::hasColumn('m_olt', 'user_update')) {
            $dataToUpdate['user_update'] = $username;
        }
        if (Schema::hasColumn('m_olt', 'updated_at')) {
            $dataToUpdate['updated_at'] = now();
        }

        $query = DB::table('m_olt');
        if (Schema::hasColumn('m_olt', 'id') && is_numeric($id)) {
            $query->where('id', $id);
        } elseif (Schema::hasColumn('m_olt', 'kode_olt')) {
            $query->where('kode_olt', $id);
        } else {
            $query->where('name', $id);
        }

        $query->update($dataToUpdate);

        return redirect()->route('olt.index')->with('success', 'Data perangkat OLT berhasil diperbarui!');
    }

    /**
     * Hapus perangkat OLT.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $query = DB::table('m_olt');
        if (Schema::hasColumn('m_olt', 'id') && is_numeric($id)) {
            $query->where('id', $id);
        } elseif (Schema::hasColumn('m_olt', 'kode_olt')) {
            $query->where('kode_olt', $id);
        } else {
            $query->where('name', $id);
        }

        $query->delete();

        return redirect()->route('olt.index')->with('success', 'Perangkat OLT berhasil dihapus.');
    }

    /**
     * Test koneksi / ping status OLT.
     */
    public function testConnection(Request $request, $id)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $query = DB::table('m_olt');
        if (Schema::hasColumn('m_olt', 'id') && is_numeric($id)) {
            $query->where('id', $id);
        } elseif (Schema::hasColumn('m_olt', 'kode_olt')) {
            $query->where('kode_olt', $id);
        } else {
            $query->where('name', $id);
        }

        $olt = $query->first();
        if (!$olt) {
            return response()->json(['status' => 'error', 'message' => 'Perangkat OLT tidak ditemukan.'], 404);
        }

        $ip = $olt->ip_address ?? '127.0.0.1';
        $port = (int) ($olt->snmp_port ?? 161);
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
            // Cek port ICMP / port 80 fallback check
            $socket80 = @fsockopen($ip, 80, $errno2, $errstr2, 1.0);
            if ($socket80) {
                $isReachable = true;
                fclose($socket80);
                $responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);
            }
        }

        // Update status di database jika ingin auto sync
        $newStatus = $isReachable ? 'Up' : 'Down';
        if (Schema::hasColumn('m_olt', 'status')) {
            $upQuery = DB::table('m_olt');
            if (Schema::hasColumn('m_olt', 'id') && is_numeric($id)) {
                $upQuery->where('id', $id);
            } elseif (Schema::hasColumn('m_olt', 'kode_olt')) {
                $upQuery->where('kode_olt', $id);
            }
            $upQuery->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);
        }

        $deviceName = $olt->name ?? ($olt->name_olt ?? 'OLT Device');

        return response()->json([
            'status' => 'success',
            'reachable' => $isReachable,
            'device_status' => $newStatus,
            'ip' => $ip,
            'port' => $port,
            'latency_ms' => $responseTimeMs,
            'message' => $isReachable 
                ? "Koneksi ke {$deviceName} ({$ip}) BERHASIL (Latensi: {$responseTimeMs} ms)." 
                : "Tidak dapat terhubung ke {$deviceName} ({$ip}:{$port}).",
        ]);
    }
}

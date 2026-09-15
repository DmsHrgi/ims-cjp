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
     * Tidak menyisipkan data dummy/palsu.
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
                    $table->string('status', 20)->default('Down');
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
                        $table->string('status', 20)->default('Down');
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
        } catch (\Throwable $e) {
            // Ignored if handled
        }
    }

    /**
     * Uji konektivitas aktual perangkat OLT secara nyata (SNMP Query, UDP Probe, ICMP Ping, dan Port Probe).
     */
    public function pingDevice($ip, $port = 161, $community = 'public', $version = 'v2c', $timeoutSec = 1.0)
    {
        $ip = trim((string) $ip);
        if (empty($ip) || $ip === '-') {
            return ['status' => 'Down', 'latency_ms' => null, 'method' => 'none', 'message' => 'IP Address belum diatur'];
        }

        $port = (int) ($port ?: 161);
        $community = (string) ($community ?: 'public');
        $version = (string) ($version ?: 'v2c');

        $startTime = microtime(true);
        $latency = null;

        // 1. Uji SNMP jika modul PHP SNMP terpasang
        if (function_exists('snmp2_get') && in_array(strtolower($version), ['v2c', 'v2', '2c'])) {
            try {
                // sysUpTime OID: .1.3.6.1.2.1.1.3.0
                $snmpResult = @snmp2_get($ip . ':' . $port, $community, '.1.3.6.1.2.1.1.3.0', (int) ($timeoutSec * 1000000), 1);
                if ($snmpResult !== false) {
                    $latency = round((microtime(true) - $startTime) * 1000, 1);
                    return ['status' => 'Up', 'latency_ms' => $latency, 'method' => 'snmp_v2c', 'message' => 'Respon SNMP v2c Terverifikasi'];
                }
            } catch (\Throwable $e) {}
        } elseif (function_exists('snmpget') && strtolower($version) === 'v1') {
            try {
                $snmpResult = @snmpget($ip . ':' . $port, $community, '.1.3.6.1.2.1.1.3.0', (int) ($timeoutSec * 1000000), 1);
                if ($snmpResult !== false) {
                    $latency = round((microtime(true) - $startTime) * 1000, 1);
                    return ['status' => 'Up', 'latency_ms' => $latency, 'method' => 'snmp_v1', 'message' => 'Respon SNMP v1 Terverifikasi'];
                }
            } catch (\Throwable $e) {}
        }

        // 2. Uji SNMP UDP Probe langsung via Socket
        try {
            $sock = @fsockopen("udp://$ip", $port, $errno, $errstr, $timeoutSec);
            if ($sock) {
                stream_set_timeout($sock, 1);
                $commLen = chr(strlen($community));
                // SNMP packet request
                $packet = "\x30" . chr(29 + strlen($community)) . "\x02\x01\x01\x04" . $commLen . $community . "\xa0\x18\x02\x04\x12\x34\x56\x78\x02\x01\x00\x02\x01\x00\x30\x0a\x30\x08\x06\x04\x2b\x06\x01\x02\x05\x00";
                @fwrite($sock, $packet);
                $resp = @fread($sock, 512);
                fclose($sock);
                if (!empty($resp)) {
                    $latency = round((microtime(true) - $startTime) * 1000, 1);
                    return ['status' => 'Up', 'latency_ms' => $latency, 'method' => 'snmp_udp', 'message' => 'Respon UDP SNMP Aktif'];
                }
            }
        } catch (\Throwable $e) {}

        // 3. Uji ICMP Ping (OS Ping)
        try {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $pingCmd = $isWindows 
                ? "ping -n 1 -w 1000 " . escapeshellarg($ip)
                : "ping -c 1 -W 1 " . escapeshellarg($ip);
            
            $output = [];
            $resultCode = 1;
            @exec($pingCmd, $output, $resultCode);

            if ($resultCode === 0) {
                $latency = round((microtime(true) - $startTime) * 1000, 1);
                return ['status' => 'Up', 'latency_ms' => $latency, 'method' => 'icmp_ping', 'message' => 'ICMP Ping Terhubung'];
            }
        } catch (\Throwable $e) {}

        // 4. Uji Port Management OLT (Telnet 23, HTTP 80, SSH 22, HTTPS 443, port 8080)
        $commonPorts = [23, 80, 22, 443, 8080];
        foreach ($commonPorts as $p) {
            $sock = @fsockopen($ip, $p, $errno, $errstr, 0.4);
            if ($sock) {
                fclose($sock);
                $latency = round((microtime(true) - $startTime) * 1000, 1);
                return ['status' => 'Up', 'latency_ms' => $latency, 'method' => "tcp_port_{$p}", 'message' => "Port {$p} Aktif"];
            }
        }

        return ['status' => 'Down', 'latency_ms' => null, 'method' => 'timeout', 'message' => 'Perangkat Offline / Tidak Merespons'];
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
            if (!isset($item->status)) $item->status = 'Down';
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

        // Statistik ringkas berdasarkan kondisi riil database
        $totalDevices = DB::table('m_olt')->count();
        $totalUp = Schema::hasColumn('m_olt', 'status') ? DB::table('m_olt')->where('status', 'Up')->count() : 0;
        $totalDown = Schema::hasColumn('m_olt', 'status') ? DB::table('m_olt')->where('status', 'Down')->count() : $totalDevices;
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
     * Simpan data OLT baru ke database dengan pengecekan status riil saat disimpan.
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

        // Cek status konektivitas riil ke perangkat OLT saat disimpan
        $pingResult = $this->pingDevice(
            $validated['ip_address'],
            (int) $validated['snmp_port'],
            $validated['snmp_community'],
            $validated['snmp_version']
        );
        $realStatus = $pingResult['status']; // 'Up' jika terhubung, 'Down' jika offline

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
            $dataToInsert['status'] = $realStatus;
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

        $statusMsg = $realStatus === 'Up' 
            ? "Status: ONLINE ({$pingResult['latency_ms']} ms via {$pingResult['method']})" 
            : "Status: OFFLINE (Belum merespons jaringan)";

        return redirect()->route('olt.index')->with('success', "Perangkat OLT berhasil disimpan! {$statusMsg}");
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

        // Cek status konektivitas riil ke perangkat OLT saat di-update
        $pingResult = $this->pingDevice(
            $validated['ip_address'],
            (int) $validated['snmp_port'],
            $validated['snmp_community'],
            $validated['snmp_version']
        );
        $realStatus = $pingResult['status'];

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
            $dataToUpdate['status'] = $realStatus;
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

        return redirect()->route('olt.index')->with('success', 'Data perangkat OLT berhasil diperbarui! Status terkini: ' . $realStatus);
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
     * Test koneksi / ping status OLT tunggal secara real-time.
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
        $community = $olt->snmp_community ?? 'public';
        $version = $olt->snmp_version ?? 'v2c';

        $pingResult = $this->pingDevice($ip, $port, $community, $version);
        $newStatus = $pingResult['status'];

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
            'status'        => 'success',
            'reachable'     => ($newStatus === 'Up'),
            'device_status' => $newStatus,
            'ip'            => $ip,
            'port'          => $port,
            'latency_ms'    => $pingResult['latency_ms'],
            'method'        => $pingResult['method'],
            'message'       => $newStatus === 'Up' 
                ? "Koneksi ke {$deviceName} ({$ip}) BERHASIL (Latensi: {$pingResult['latency_ms']} ms via {$pingResult['method']})." 
                : "Tidak dapat terhubung ke {$deviceName} ({$ip}:{$port}).",
        ]);
    }

    /**
     * Sinkronisasi status riil seluruh perangkat OLT sekaligus secara paralel/cepat.
     */
    public function syncAllStatus(Request $request)
    {
        $this->authorizeAdmin();
        $this->ensureTableExists();

        $devices = DB::table('m_olt')->get();
        $results = [];

        foreach ($devices as $d) {
            $id = $d->id ?? ($d->kode_olt ?? null);
            $ip = $d->ip_address ?? '';
            $port = (int) ($d->snmp_port ?? 161);
            $comm = $d->snmp_community ?? 'public';
            $ver = $d->snmp_version ?? 'v2c';

            $ping = $this->pingDevice($ip, $port, $comm, $ver, 0.5);

            if (Schema::hasColumn('m_olt', 'status') && $id) {
                $q = DB::table('m_olt');
                if (isset($d->id)) {
                    $q->where('id', $d->id);
                } else {
                    $q->where('kode_olt', $d->kode_olt);
                }
                $q->update([
                    'status' => $ping['status'],
                    'updated_at' => now(),
                ]);
            }

            $results[] = [
                'id' => $id,
                'name' => $d->name ?? ($d->name_olt ?? 'OLT'),
                'ip' => $ip,
                'status' => $ping['status'],
                'latency_ms' => $ping['latency_ms'],
                'method' => $ping['method'],
            ];
        }

        return response()->json([
            'status' => 'success',
            'total' => count($results),
            'devices' => $results,
        ]);
    }
}

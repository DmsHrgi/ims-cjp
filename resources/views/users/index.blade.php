@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ── Breadcrumb & Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Pengaturan</span>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600 font-semibold">Manajemen User</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                    <i class="fa-solid fa-user-gear"></i>
                </span>
                Manajemen User & Hak Akses
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data pengguna, peran akses (role), dan status akun sistem.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200 hover:-translate-y-0.5 cursor-pointer">
                <i class="fa-solid fa-user-plus text-xs"></i>
                Tambah User Baru
            </button>
        </div>
    </div>

    {{-- ── Alert Notifikasi ── --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between gap-3 shadow-xs animate-in fade-in duration-200">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-xs shadow-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-xs font-semibold">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 shadow-xs animate-in fade-in duration-200">
            <div class="w-7 h-7 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0 text-xs mt-0.5 shadow-xs">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-rose-900 mb-0.5">Terjadi kesalahan:</p>
                <ul class="text-xs list-disc list-inside space-y-0.5 text-rose-700 font-medium">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Pengguna --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-blue-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Total Pengguna</p>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($totalUsers, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-slate-400 font-medium">Seluruh akun terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg border border-blue-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
        </div>

        {{-- Pengguna Aktif --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-emerald-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Pengguna Aktif</p>
                <h3 class="text-2xl font-black text-emerald-600 tracking-tight">{{ number_format($activeUsers, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-600/80 font-medium">Dapat login ke sistem</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
        </div>

        {{-- Pengguna Nonaktif --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-rose-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Pengguna Nonaktif</p>
                <h3 class="text-2xl font-black text-rose-500 tracking-tight">{{ number_format($inactiveUsers, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-rose-500/80 font-medium">Akses login terkunci</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg border border-rose-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
        </div>

        {{-- Total Roles / Levels --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-purple-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Level & Role</p>
                <h3 class="text-2xl font-black text-purple-600 tracking-tight">{{ $totalRoles }}</h3>
                <p class="text-[10px] text-purple-600/80 font-medium">Hak akses dikonfigurasi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg border border-purple-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-violet-500"></div>
        </div>
    </div>

    {{-- ── Main Table Card ── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        {{-- Toolbar Filter & Pencarian --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center justify-between gap-3" id="filterUserForm">
                <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-1 min-w-[200px] max-w-md">
                        <input type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Cari username, nama, role, atau jabatan..."
                               class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2 pl-9 pr-3 text-xs rounded-xl outline-none transition-all placeholder-slate-400">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        @if($search)
                            <a href="{{ route('users.index', array_merge(request()->except('q', 'page'))) }}"
                               class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                            </a>
                        @endif
                    </div>

                    {{-- Filter Role --}}
                    <div class="relative min-w-[150px]">
                        <select name="role"
                                onchange="document.getElementById('filterUserForm').submit()"
                                class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-700 py-2 pl-3 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                            <option value="">Semua Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->kode_level }}" {{ $filterRole === $r->kode_level ? 'selected' : '' }}>
                                    {{ $r->nama_level }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>

                    {{-- Filter Status --}}
                    <div class="relative min-w-[130px]">
                        <select name="status"
                                onchange="document.getElementById('filterUserForm').submit()"
                                class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-700 py-2 pl-3 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                            <option value="">Semua Status</option>
                            <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>

                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-semibold transition-all">
                        Filter
                    </button>

                    @if($search || $filterRole || $filterStatus)
                        <a href="{{ route('users.index') }}" class="text-xs text-rose-600 hover:underline font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset
                        </a>
                    @endif
                </div>

                {{-- Entries Selector --}}
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span>Tampilkan</span>
                    <select name="entries" onchange="document.getElementById('filterUserForm').submit()" class="bg-white border border-slate-200 rounded-lg py-1 px-2 text-xs outline-none cursor-pointer">
                        <option value="10" {{ $entries == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $entries == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $entries == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $entries == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>baris</span>
                </div>
            </form>
        </div>

        {{-- Tabel Data Pengguna --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Username</th>
                        <th class="py-3.5 px-4">Role / Level</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Aktivitas Terakhir</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $user)
                        @php
                            $nama = $user->nama_karyawan ?: $user->username;
                            $inisial = strtoupper(substr(trim($nama), 0, 1));
                            $isAktif = in_array((string)$user->status_aktif, ['1', '01'], true);
                            $levelName = strtoupper($user->nama_level ?: 'STAFF');
                            $isCurrentAuth = (session('user.kode_pengguna') === $user->kode_pengguna);

                            // Warna badge role
                            $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                            if (str_contains($levelName, 'ADMIN')) {
                                $badgeColor = 'bg-rose-50 text-rose-700 border-rose-200';
                            } elseif (str_contains($levelName, 'NOC')) {
                                $badgeColor = 'bg-sky-50 text-sky-700 border-sky-200';
                            } elseif (str_contains($levelName, 'FINANCE') || str_contains($levelName, 'KEUANGAN')) {
                                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (str_contains($levelName, 'TEKNIK')) {
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (str_contains($levelName, 'SALES') || str_contains($levelName, 'SALSES') || str_contains($levelName, 'MITRA')) {
                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (str_contains($levelName, 'DIREKTUR')) {
                                $badgeColor = 'bg-purple-50 text-purple-700 border-purple-200';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $isCurrentAuth ? 'bg-blue-50/30' : '' }}">
                            {{-- Pengguna (Avatar + Nama + Jabatan) --}}
                            <td class="py-3.5 px-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-xs shadow-xs border border-white/20">
                                        {{ $inisial }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 truncate flex items-center gap-1.5">
                                            <span>{{ $nama }}</span>
                                            @if($isCurrentAuth)
                                                <span class="bg-blue-100 text-blue-700 text-[9px] font-extrabold px-1.5 py-0.2 rounded-md">Akun Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                                            <span class="font-medium text-slate-500">{{ $user->nama_jabatan ?: 'Staff' }}</span>
                                            @if($user->kode_karyawan)
                                                <span>•</span>
                                                <span class="font-mono text-[10px]">{{ $user->kode_karyawan }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Username --}}
                            <td class="py-3.5 px-4 align-middle">
                                <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md text-[11px]">
                                    {{ $user->username }}
                                </span>
                            </td>

                            {{-- Role / Level --}}
                            <td class="py-3.5 px-4 align-middle">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeColor }}">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    {{ $user->nama_level ?: 'User' }}
                                </span>
                            </td>

                            {{-- Status Aktif --}}
                            <td class="py-3.5 px-4 align-middle">
                                @if($isAktif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aktivitas Terakhir --}}
                            <td class="py-3.5 px-4 align-middle">
                                @if($user->las_login)
                                    <div class="text-[11px] text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($user->las_login)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        IP: {{ $user->last_ip ?: '-' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Belum pernah login</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Edit Button --}}
                                    <button type="button"
                                            onclick='openEditModal(@json($user))'
                                            title="Edit Data Pengguna"
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    @if(!$isCurrentAuth)
                                        <form method="POST" action="{{ route('users.toggle-status', $user->kode_pengguna) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin {{ $isAktif ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $user->username }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="{{ $isAktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                    class="w-7 h-7 rounded-lg {{ $isAktif ? 'bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white border-amber-200' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border-emerald-200' }} border flex items-center justify-center transition-colors cursor-pointer">
                                                <i class="fa-solid {{ $isAktif ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete Button --}}
                                    @if(!$isCurrentAuth && $user->username !== 'admin')
                                        <button type="button"
                                                onclick="openDeleteModal('{{ $user->kode_pengguna }}', '{{ addslashes($user->username) }}', '{{ addslashes($nama) }}')"
                                                title="Hapus Pengguna"
                                                class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 flex items-center justify-center transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-lg">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-600">Tidak ada data pengguna yang ditemukan.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci pencarian atau reset filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($users->hasPages() || $users->total() > 0)
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-slate-700">{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $users->total() }}</span> total pengguna
                </div>
                @if (isset($users) && $users->total())
                    @include('partials.pagination', ['rows' => $users])
                @endif
            </div>
        @endif
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL TAMBAH USER BARU --}}
{{-- ============================================ --}}
<div id="modalCreateUser" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeCreateModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-xs font-bold shadow-xs">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Tambah Pengguna Baru</h3>
                </div>
                <button type="button" onclick="closeCreateModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:bg-slate-200/60 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-4" id="formCreateUser">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap / Karyawan <span class="text-rose-500 font-bold">*</span></label>
                    <input type="text" name="nama_karyawan" required maxlength="100" placeholder="Contoh: AHMAD FAUZI" value="{{ old('nama_karyawan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400 uppercase">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username / Login ID <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="username" required maxlength="100" placeholder="ahmad@ptmsn.co.id / ahmad" value="{{ old('username') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none font-mono transition-all placeholder-slate-400 no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role / Hak Akses <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kode_level" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->kode_level }}" {{ old('kode_level') === $r->kode_level ? 'selected' : '' }}>
                                        {{ $r->nama_level }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jabatan (Opsional)</label>
                        <input type="text" name="kode_jabatan" maxlength="50" placeholder="Contoh: Staff NOC, Supervisor" value="{{ old('kode_jabatan') }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Akun <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="status_aktif" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                                <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif (Terkunci)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password <span class="text-rose-500 font-bold">*</span></label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Password <span class="text-rose-500 font-bold">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Ulangi password" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400 no-uppercase">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL EDIT USER --}}
{{-- ============================================ --}}
<div id="modalEditUser" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeEditModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-xs font-bold shadow-xs">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Edit Data Pengguna</h3>
                        <p class="text-[10px] text-slate-400 font-mono" id="editKodePenggunaDisplay"></p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:bg-slate-200/60 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form method="POST" action="" class="p-6 space-y-4" id="formEditUser">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap / Karyawan <span class="text-rose-500 font-bold">*</span></label>
                    <input type="text" name="nama_karyawan" id="editNamaKaryawan" required maxlength="100" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all uppercase">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username / Login ID <span class="text-rose-500 font-bold">*</span></label>
                        <input type="text" name="username" id="editUsername" required maxlength="100" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none font-mono transition-all no-uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role / Hak Akses <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="kode_level" id="editKodeLevel" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->kode_level }}">{{ $r->nama_level }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jabatan</label>
                        <input type="text" name="kode_jabatan" id="editKodeJabatan" maxlength="50" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Akun <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <select name="status_aktif" id="editStatusAktif" required class="w-full appearance-none bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-slate-800 py-2.5 pl-3.5 pr-8 text-xs rounded-xl outline-none transition-all cursor-pointer font-medium">
                                <option value="1">Aktif (Dapat Login)</option>
                                <option value="0">Nonaktif (Terkunci)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ubah Password (Opsional) --}}
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 space-y-3">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                        <i class="fa-solid fa-key text-blue-600 text-[11px]"></i>
                        <span>Ubah Password (Kosongkan jika tidak diubah)</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Password Baru</label>
                            <input type="password" name="password" minlength="6" placeholder="Minimal 6 karakter" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none no-uppercase">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ulangi Password Baru</label>
                            <input type="password" name="password_confirmation" minlength="6" placeholder="Ulangi password" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none no-uppercase">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL HAPUS USER --}}
{{-- ============================================ --}}
<div id="modalDeleteUser" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150">
            
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-3.5 text-xl shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Hapus Akun Pengguna?</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun <span class="font-bold text-slate-800" id="deleteUsernameDisplay"></span> (<span id="deleteNamaDisplay" class="font-medium"></span>)? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" action="" id="formDeleteUser" class="flex items-center justify-center gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors w-1/2">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-500/20 transition-all w-1/2 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalCreateUser').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeCreateModal() {
        document.getElementById('modalCreateUser').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openEditModal(user) {
        const form = document.getElementById('formEditUser');
        form.action = "{{ url('/manajemen-user') }}/" + user.kode_pengguna;

        document.getElementById('editKodePenggunaDisplay').textContent = user.kode_pengguna + ' • ' + (user.kode_karyawan || '');
        document.getElementById('editNamaKaryawan').value = user.nama_karyawan || '';
        document.getElementById('editUsername').value = user.username || '';
        document.getElementById('editKodeLevel').value = user.kode_level || '';
        document.getElementById('editKodeJabatan').value = user.nama_jabatan || '';
        
        const isAktif = (user.status_aktif === '1' || user.status_aktif === '01');
        document.getElementById('editStatusAktif').value = isAktif ? '1' : '0';

        document.getElementById('modalEditUser').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditUser').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openDeleteModal(kodePengguna, username, nama) {
        const form = document.getElementById('formDeleteUser');
        form.action = "{{ url('/manajemen-user') }}/" + kodePengguna;

        document.getElementById('deleteUsernameDisplay').textContent = username;
        document.getElementById('deleteNamaDisplay').textContent = nama;

        document.getElementById('modalDeleteUser').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteModal() {
        document.getElementById('modalDeleteUser').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
</script>
@endsection

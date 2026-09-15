@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ── Breadcrumb & Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-slate-500">Master Data</span>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-blue-600 font-semibold">OLT Device</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                    <i class="fa-solid fa-server"></i>
                </span>
                OLT Device Management
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola dan pantau perangkat Optical Line Terminal (OLT) beserta konfigurasi SNMP.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200 hover:-translate-y-0.5 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah OLT Baru
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
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs cursor-pointer">
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
                <p class="text-xs font-bold text-rose-900 mb-0.5">Terjadi kesalahan validasi:</p>
                <ul class="text-xs list-disc list-inside space-y-0.5 text-rose-700 font-medium">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total OLT --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-blue-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Total OLT Device</p>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($totalDevices, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-slate-400 font-medium">Perangkat terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg border border-blue-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-server"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
        </div>

        {{-- OLT Status Up --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-emerald-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Status Online (Up)</p>
                <h3 class="text-2xl font-black text-emerald-600 tracking-tight">{{ number_format($totalUp, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-600/80 font-medium">Perangkat aktif normal</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-circle-nodes"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
        </div>

        {{-- OLT Status Down --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-rose-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Status Offline (Down)</p>
                <h3 class="text-2xl font-black text-rose-500 tracking-tight">{{ number_format($totalDown, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-rose-500/80 font-medium">Perangkat tidak merespons</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg border border-rose-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
        </div>

        {{-- Total Vendor --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-purple-300 transition-colors">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500">Total Vendor</p>
                <h3 class="text-2xl font-black text-purple-600 tracking-tight">{{ $totalVendors }}</h3>
                <p class="text-[10px] text-purple-600/80 font-medium">Brand (ZTE, Huawei, dll)</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg border border-purple-100 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-violet-500"></div>
        </div>
    </div>

    {{-- ── Filter & Search Bar ── --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('olt.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            {{-- Pencarian --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Cari nama OLT, IP address, hostname, vendor, model..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            {{-- Filter Vendor --}}
            <div class="w-full md:w-48">
                <select name="vendor"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white text-slate-700">
                    <option value="">Semua Vendor</option>
                    @foreach ($vendorList as $v)
                        <option value="{{ $v }}" {{ $filterVendor === $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="w-full md:w-40">
                <select name="status"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white text-slate-700">
                    <option value="">Semua Status</option>
                    <option value="Up" {{ $filterStatus === 'Up' ? 'selected' : '' }}>Up (Online)</option>
                    <option value="Down" {{ $filterStatus === 'Down' ? 'selected' : '' }}>Down (Offline)</option>
                </select>
            </div>

            {{-- Submit & Reset --}}
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit"
                        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition-all shadow-xs cursor-pointer flex-1 md:flex-initial">
                    Filter
                </button>
                @if ($search !== '' || $filterVendor !== '' || $filterStatus !== '')
                    <a href="{{ route('olt.index') }}"
                       class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-all shadow-xs text-center">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── OLT Device Cards / List (Sesuai Gambar 1) ── --}}
    @if ($oltDevices->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mx-auto text-2xl mb-3 shadow-xs">
                <i class="fa-solid fa-server"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Belum Ada Perangkat OLT</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                {{ $search !== '' || $filterVendor !== '' || $filterStatus !== '' ? 'Tidak ada perangkat OLT yang sesuai dengan filter pencarian.' : 'Mulai daftarkan perangkat OLT pertama untuk memantau status jaringan dan konfigurasi SNMP.' }}
            </p>
            <div class="mt-4">
                <button type="button"
                        onclick="openCreateModal()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah OLT Baru
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach ($oltDevices as $device)
                @php
                    $isUp = strtolower($device->status ?? 'up') === 'up';
                    $deviceJson = json_encode($device);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden relative group">
                    
                    {{-- Card Header Banner --}}
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ $isUp ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/60' : 'bg-rose-50 text-rose-600 border border-rose-200/60' }} flex items-center justify-center text-sm shadow-xs font-bold">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                                    {{ $device->name }}
                                    @if ($device->model)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-200/70 text-slate-600">
                                            {{ $device->model }}
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5">
                                    <span>Vendor: <strong class="text-slate-600">{{ $device->vendor }}</strong></span>
                                    @if ($device->hostname)
                                        <span>•</span>
                                        <span>Host: <strong class="text-slate-600">{{ $device->hostname }}</strong></span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons & Status Badge --}}
                        <div class="flex items-center gap-2">
                            {{-- Test Connection Button --}}
                            <button type="button"
                                    onclick="testConnection({{ $device->id }}, this)"
                                    title="Test Koneksi SNMP / Ping"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center text-xs transition-all shadow-xs cursor-pointer">
                                <i class="fa-solid fa-network-wired"></i>
                            </button>

                            {{-- Edit Button --}}
                            <button type="button"
                                    onclick='openEditModal(@json($device))'
                                    title="Edit Perangkat OLT"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 flex items-center justify-center text-xs transition-all shadow-xs cursor-pointer">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            {{-- Delete Button --}}
                            <button type="button"
                                    onclick="confirmDelete({{ $device->id }}, '{{ addslashes($device->name) }}')"
                                    title="Hapus Perangkat OLT"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center text-xs transition-all shadow-xs cursor-pointer">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Card Body (2 Columns Layout persis Gambar 1) --}}
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Left Column: Device Information (Gambar 1) --}}
                        <div class="space-y-3">
                            <h4 class="text-sm font-bold text-slate-800 tracking-tight pb-1 border-b border-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500 text-xs"></i>
                                Device Information
                            </h4>

                            <div class="space-y-2.5 text-xs">
                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">NAME</span>
                                    <span class="font-bold text-slate-800">{{ $device->name }}</span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">HOSTNAME</span>
                                    <span class="font-semibold text-slate-700 font-mono">{{ $device->hostname ?: '-' }}</span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">IP ADDRESS</span>
                                    <span class="font-bold font-mono text-rose-500 tracking-wide bg-rose-50/50 px-2 py-0.5 rounded-md border border-rose-100">
                                        {{ $device->ip_address }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">VENDOR</span>
                                    <span class="font-semibold text-slate-800">{{ $device->vendor }}</span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">MODEL</span>
                                    <span class="font-semibold text-slate-800">{{ $device->model ?: '-' }}</span>
                                </div>

                                <div class="flex items-center justify-between py-1">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">STATUS</span>
                                    <div>
                                        @if ($isUp)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                Up
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                Down
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: SNMP Configuration (Gambar 1) --}}
                        <div class="space-y-3">
                            <h4 class="text-sm font-bold text-slate-800 tracking-tight pb-1 border-b border-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-sliders text-indigo-500 text-xs"></i>
                                SNMP Configuration
                            </h4>

                            <div class="space-y-2.5 text-xs">
                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">SNMP PORT</span>
                                    <span class="font-bold font-mono text-slate-800">{{ $device->snmp_port }}</span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">SNMP VERSION</span>
                                    <span class="font-semibold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-mono text-[11px] border border-indigo-100">
                                        {{ $device->snmp_version }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                    <span class="text-[11px] font-bold text-slate-500 tracking-wider uppercase">COMMUNITY</span>
                                    <span class="font-bold font-mono text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">
                                        {{ $device->snmp_community }}
                                    </span>
                                </div>
                            </div>

                            {{-- Location Section (Gambar 1) --}}
                            <div class="pt-3 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-700 tracking-tight mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[11px]"></i>
                                    Location
                                </h4>
                                <p class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                                    {{ $device->location ?: 'Lokasi belum ditentukan' }}
                                </p>
                            </div>
                        </div>

                    </div>

                    {{-- Card Footer Note --}}
                    @if ($device->description)
                        <div class="px-6 py-2.5 bg-slate-50/50 border-t border-slate-100 text-[11px] text-slate-500 flex items-center gap-2">
                            <i class="fa-regular fa-comment-dots text-slate-400"></i>
                            <span class="truncate">{{ $device->description }}</span>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $oltDevices->links() }}
        </div>
    @endif

</div>

{{-- ──────────────────────────────────────────────────────────
     MODAL TAMBAH & EDIT OLT (SESUAI GAMBAR 2)
────────────────────────────────────────────────────────── --}}
<div id="oltModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeOltModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div id="modalIconBox" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-sm shadow-xs font-bold">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-sm font-extrabold text-slate-800 tracking-tight">Tambah Perangkat OLT</h3>
                        <p id="modalSubtitle" class="text-[11px] text-slate-400">Masukkan informasi perangkat dan konfigurasi SNMP</p>
                    </div>
                </div>
                <button type="button" onclick="closeOltModal()" class="w-8 h-8 rounded-xl bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Modal Form --}}
            <form id="oltForm" method="POST" action="{{ route('olt.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

                    {{-- ── SECTION 1: Device Information (Gambar 2) ── --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 tracking-tight flex items-center gap-2 text-[13px]">
                            <i class="fa-solid fa-circle-info text-blue-600 text-xs"></i>
                            Device Information
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Name * --}}
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">
                                    Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       required
                                       placeholder="OLT Jakarta 01"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                            </div>

                            {{-- Hostname --}}
                            <div>
                                <label for="hostname" class="block text-xs font-bold text-slate-700 mb-1">
                                    Hostname
                                </label>
                                <input type="text"
                                       id="hostname"
                                       name="hostname"
                                       placeholder="olt-jkt-01"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono">
                            </div>

                            {{-- IP Address * --}}
                            <div>
                                <label for="ip_address" class="block text-xs font-bold text-slate-700 mb-1">
                                    IP Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="ip_address"
                                       name="ip_address"
                                       required
                                       placeholder="192.168.1.1"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono">
                            </div>

                            {{-- Vendor * --}}
                            <div>
                                <label for="vendor" class="block text-xs font-bold text-slate-700 mb-1">
                                    Vendor <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="vendor"
                                       name="vendor"
                                       list="vendorSuggestions"
                                       required
                                       placeholder="ZTE, Huawei, Fiberhome..."
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                                <datalist id="vendorSuggestions">
                                    <option value="ZTE">
                                    <option value="Huawei">
                                    <option value="Fiberhome">
                                    <option value="VSOL">
                                    <option value="BDCOM">
                                    <option value="Nokia">
                                    <option value="Cisco">
                                </datalist>
                            </div>

                            {{-- Model --}}
                            <div class="sm:col-span-2">
                                <label for="model" class="block text-xs font-bold text-slate-700 mb-1">
                                    Model
                                </label>
                                <input type="text"
                                       id="model"
                                       name="model"
                                       placeholder="C320, MA5608T..."
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 2: SNMP Configuration (Gambar 2) ── --}}
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 tracking-tight flex items-center gap-2 text-[13px]">
                                <i class="fa-solid fa-sliders text-indigo-600 text-xs"></i>
                                SNMP Configuration
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Digunakan untuk monitoring status OLT secara berkala</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- SNMP Port * --}}
                            <div>
                                <label for="snmp_port" class="block text-xs font-bold text-slate-700 mb-1">
                                    SNMP Port <span class="text-rose-500">*</span>
                                </label>
                                <input type="number"
                                       id="snmp_port"
                                       name="snmp_port"
                                       value="161"
                                       required
                                       placeholder="161"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono">
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">Default: 161</p>
                            </div>

                            {{-- SNMP Version * --}}
                            <div>
                                <label for="snmp_version" class="block text-xs font-bold text-slate-700 mb-1">
                                    SNMP Version <span class="text-rose-500">*</span>
                                </label>
                                <select id="snmp_version"
                                        name="snmp_version"
                                        required
                                        class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono bg-white">
                                    <option value="v1">v1</option>
                                    <option value="v2c" selected>v2c</option>
                                    <option value="v3">v3</option>
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">Rekomendasi: v2c</p>
                            </div>

                            {{-- SNMP Community * --}}
                            <div>
                                <label for="snmp_community" class="block text-xs font-bold text-slate-700 mb-1">
                                    SNMP Community <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="snmp_community"
                                       name="snmp_community"
                                       value="public"
                                       required
                                       placeholder="public"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono">
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">Contoh: public, private</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 3: Location & Status (Optional / Gambar 1-2) ── --}}
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Location --}}
                            <div>
                                <label for="location" class="block text-xs font-bold text-slate-700 mb-1">
                                    Location / POP
                                </label>
                                <input type="text"
                                       id="location"
                                       name="location"
                                       placeholder="e.g. POP Babakan Tarogong / Rack 01"
                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-xs font-bold text-slate-700 mb-1">
                                    Initial Status
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                                    <option value="Up" selected>Up (Online)</option>
                                    <option value="Down">Down (Offline)</option>
                                </select>
                            </div>

                            {{-- Description --}}
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-xs font-bold text-slate-700 mb-1">
                                    Description / Catatan
                                </label>
                                <textarea id="description"
                                          name="description"
                                          rows="2"
                                          placeholder="Catatan tambahan mengenai perangkat OLT ini..."
                                          class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                            onclick="closeOltModal()"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all cursor-pointer shadow-xs">
                        Batal
                    </button>
                    <button type="submit"
                            id="btnSubmitModal"
                            class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl transition-all shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span id="btnSubmitText">Simpan Perangkat</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────────────────
     MODAL DELETE CONFIRMATION
────────────────────────────────────────────────────────── --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-6 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Hapus Perangkat OLT?</h3>
                    <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <p class="text-xs text-slate-600 mb-6">
                Apakah Anda yakin ingin menghapus perangkat <strong id="deleteDeviceName" class="text-slate-800 font-bold"></strong> dari sistem?
            </p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-md shadow-rose-500/20 cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────────────────
     JAVASCRIPT LOGIC
────────────────────────────────────────────────────────── --}}
<script>
    const baseUrl = "{{ url('/olt') }}";

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Perangkat OLT';
        document.getElementById('modalSubtitle').textContent = 'Masukkan informasi perangkat dan konfigurasi SNMP';
        document.getElementById('btnSubmitText').textContent = 'Simpan Perangkat';
        
        const form = document.getElementById('oltForm');
        form.action = baseUrl;
        document.getElementById('formMethod').value = 'POST';

        // Reset inputs
        document.getElementById('name').value = '';
        document.getElementById('hostname').value = '';
        document.getElementById('ip_address').value = '';
        document.getElementById('vendor').value = '';
        document.getElementById('model').value = '';
        document.getElementById('snmp_port').value = '161';
        document.getElementById('snmp_version').value = 'v2c';
        document.getElementById('snmp_community').value = 'public';
        document.getElementById('location').value = '';
        document.getElementById('status').value = 'Up';
        document.getElementById('description').value = '';

        document.getElementById('oltModal').classList.remove('hidden');
    }

    function openEditModal(device) {
        document.getElementById('modalTitle').textContent = 'Edit Perangkat OLT: ' + device.name;
        document.getElementById('modalSubtitle').textContent = 'Perbarui informasi perangkat dan konfigurasi SNMP';
        document.getElementById('btnSubmitText').textContent = 'Simpan Perubahan';

        const form = document.getElementById('oltForm');
        form.action = baseUrl + '/' + device.id;
        document.getElementById('formMethod').value = 'PUT';

        // Populate inputs
        document.getElementById('name').value = device.name || '';
        document.getElementById('hostname').value = device.hostname || '';
        document.getElementById('ip_address').value = device.ip_address || '';
        document.getElementById('vendor').value = device.vendor || '';
        document.getElementById('model').value = device.model || '';
        document.getElementById('snmp_port').value = device.snmp_port || '161';
        document.getElementById('snmp_version').value = device.snmp_version || 'v2c';
        document.getElementById('snmp_community').value = device.snmp_community || 'public';
        document.getElementById('location').value = device.location || '';
        document.getElementById('status').value = device.status || 'Up';
        document.getElementById('description').value = device.description || '';

        document.getElementById('oltModal').classList.remove('hidden');
    }

    function closeOltModal() {
        document.getElementById('oltModal').classList.add('hidden');
    }

    function confirmDelete(id, name) {
        document.getElementById('deleteDeviceName').textContent = name;
        document.getElementById('deleteForm').action = baseUrl + '/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Quick Test Connection
    function testConnection(id, btnElement) {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-blue-600"></i>';
        btnElement.disabled = true;

        fetch(baseUrl + '/' + id + '/test-connection')
            .then(res => res.json())
            .then(data => {
                btnElement.innerHTML = originalHtml;
                btnElement.disabled = false;

                if (data.reachable) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(err => {
                btnElement.innerHTML = originalHtml;
                btnElement.disabled = false;
                alert('Gagal menghubungi server untuk pengujian koneksi.');
            });
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeOltModal();
            closeDeleteModal();
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
    @php /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */ @endphp

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('tiket') }}" class="hover:text-blue-500 transition-colors">IMS</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-gray-600 font-medium">Gangguan Layanan</span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">Tiket Gangguan</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Filter Bar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">

            {{-- Bulan --}}
            <div class="relative">
                <select class="appearance-none bg-gray-50 border border-gray-200 focus:border-blue-400 text-gray-700 py-2 pl-3 pr-8 text-xs font-semibold rounded-lg outline-none cursor-pointer transition-colors">
                    @foreach (['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $i => $m)
                        <option {{ $i === 6 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            {{-- Tahun --}}
            <div class="relative">
                <select class="appearance-none bg-gray-50 border border-gray-200 focus:border-blue-400 text-gray-700 py-2 pl-3 pr-8 text-xs font-semibold rounded-lg outline-none cursor-pointer transition-colors">
                    @foreach ([2024,2025,2026,2027] as $y)
                        <option {{ $y === 2026 ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            {{-- Status --}}
            <div class="relative">
                <select class="appearance-none bg-gray-50 border border-gray-200 focus:border-blue-400 text-gray-700 py-2 pl-3 pr-8 text-xs font-semibold rounded-lg outline-none cursor-pointer transition-colors">
                    <option selected>ANTRIAN</option><option>PROSES</option><option>SELESAI</option><option>DITUTUP</option>
                </select>
                <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            {{-- Search --}}
            <div class="flex-1 min-w-[180px] relative">
                <i class="fa-solid fa-magnifying-glass text-gray-300 absolute left-3 top-1/2 -translate-y-1/2 text-xs pointer-events-none"></i>
                <input type="text" placeholder="Cari nama / nomor internet…"
                       class="w-full bg-gray-50 border border-gray-200 focus:border-blue-400 text-gray-700 py-2 pl-8 pr-3 text-xs rounded-lg outline-none transition-colors placeholder-gray-400">
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2 ml-auto">
                <button class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition-all shadow-sm">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                <button class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </button>
                <button class="flex items-center gap-1.5 bg-amber-400 hover:bg-amber-500 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition-all shadow-sm">
                    <i class="fa-solid fa-file-export"></i> Export
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Tiket</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-52">Pelanggan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Info</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keluhan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-100">
                            <td class="px-5 py-3.5 align-top">
                                <p class="font-semibold text-blue-600 text-xs">{{ $r->tiket }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $r->date_create }}</p>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block font-semibold text-gray-800 hover:text-blue-600 text-xs transition-colors">{{ $r->nama_display ?: '-' }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-[11px] text-blue-500 hover:underline mt-0.5">{{ $r->nomor_internet }}</a>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <p class="text-xs font-semibold text-gray-700">{{ $r->paket ?: '-' }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-relaxed">{{ $r->alamat_p ?: '-' }}</p>
                                @if($r->nomor_hp)
                                    <p class="text-[11px] text-gray-400 mt-0.5">HP: <span class="text-gray-600 font-medium">{{ $r->nomor_hp }}</span></p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $r->keluhan ?: '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                @if($r->status)
                                    @php
                                        $statusColors = [
                                            'ANTRIAN' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'PROSES'  => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'SELESAI' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                            'DITUTUP' => 'bg-gray-100 text-gray-500 border-gray-200',
                                        ];
                                        $sc = $statusColors[$r->status] ?? 'bg-violet-50 text-violet-600 border-violet-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $sc }}">
                                        {{ $r->status }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center">
                                <div class="inline-flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        <i class="fa-solid fa-headset text-2xl text-gray-300"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-500">Tidak ada tiket gangguan</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Data dari <code class="text-gray-500">trx_tiket_gangguan</code></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($rows->total())
            <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <p class="text-xs text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-600">{{ $rows->firstItem() }}–{{ $rows->lastItem() }}</span> dari <span class="font-semibold text-gray-600">{{ $rows->total() }}</span> data
                </p>
                @include('partials.pagination', ['rows' => $rows])
            </div>
        @endif

    </div>

@endsection
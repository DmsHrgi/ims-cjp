@extends('layouts.app')

@section('content')
    @php
        /** @var array $counts */
        $cards = [
            ['t' => 'Gangguan Layanan',  'n' => $counts['gangguan'],  'route' => 'tiket.gangguan-layanan', 'grad' => 'from-indigo-400 to-blue-500',     'shadow' => 'shadow-blue-200/50',  'icon' => 'fa-headset'],
            ['t' => 'Ubah Password',     'n' => $counts['password'],  'route' => 'tiket.ganti-password',     'grad' => 'from-pink-400 to-rose-500',       'shadow' => 'shadow-pink-200/50',  'icon' => 'fa-key'],
            ['t' => 'Cek Coverage Area', 'n' => $counts['coverage'],  'route' => 'tiket.coverage-area',      'grad' => 'from-amber-400 to-yellow-500',    'shadow' => 'shadow-amber-200/50', 'icon' => 'fa-map-location-dot'],
            ['t' => 'Terminasi',         'n' => $counts['terminasi'], 'route' => 'permintaan.terminasi',     'grad' => 'from-indigo-400 to-blue-500',     'shadow' => 'shadow-blue-200/50',  'icon' => 'fa-user-xmark'],
            ['t' => 'Suspend Layanan',   'n' => $counts['suspend'],   'route' => 'permintaan.suspend',       'grad' => 'from-pink-400 to-rose-500',       'shadow' => 'shadow-pink-200/50',  'icon' => 'fa-pause-circle'],
            ['t' => 'Pemasangan Baru',   'n' => $counts['pasang'],    'route' => 'pendaftaran',              'grad' => 'from-amber-400 to-yellow-500',    'shadow' => 'shadow-amber-200/50', 'icon' => 'fa-box-open'],
            ['t' => 'Ubah Layanan',      'n' => $counts['ubah'],      'route' => 'permintaan.up-downgrade',  'grad' => 'from-cyan-400 to-sky-500',        'shadow' => 'shadow-cyan-200/50',  'icon' => 'fa-gauge-high'],
        ];
    @endphp

    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Tiket</h1>
            <nav class="text-sm text-gray-500 mt-2 md:mt-0">
                <span class="hover:text-blue-600 cursor-pointer transition-colors">IMS</span>
                <span class="mx-2 text-gray-300">></span>
                <span class="text-gray-700 font-medium">Tiket</span>
            </nav>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($cards as $c)
            <a href="{{ route($c['route']) }}" class="group relative bg-gradient-to-br {{ $c['grad'] }} rounded-xl p-6 shadow-lg {{ $c['shadow'] }} hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/25 to-transparent skew-x-12 group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
                <div class="relative z-10 flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $c['t'] }}</h3>
                        <p class="text-sm font-semibold text-white/80 tabular-nums">{{ number_format($c['n'], 0, ',', '.') }} Tiket</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shadow-inner">
                        <i class="fa-solid {{ $c['icon'] }} text-white text-2xl transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
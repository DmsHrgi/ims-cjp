@extends('layouts.app')

@section('content')
    @php
        /** @var array $chart */
        /** @var int $maxChart */
        /** @var array $c1 */
        /** @var array $c2 */
        /** @var array $c3 */
        /** @var array $c4 */

        $cards = [
            ['title' => 'Pendaftaran Baru', 'stat' => $c1, 'icon' => 'fa-user-plus',  'color' => 'blue',   'bg' => 'bg-blue-500',   'soft' => 'bg-blue-50 text-blue-600'],
            ['title' => 'Tiket Gangguan',   'stat' => $c2, 'icon' => 'fa-headset',    'color' => 'rose',   'bg' => 'bg-rose-500',   'soft' => 'bg-rose-50 text-rose-600'],
            ['title' => 'Suspend',          'stat' => $c3, 'icon' => 'fa-pause',      'color' => 'amber',  'bg' => 'bg-amber-500',  'soft' => 'bg-amber-50 text-amber-600'],
            ['title' => 'Terminasi',        'stat' => $c4, 'icon' => 'fa-user-xmark', 'color' => 'violet', 'bg' => 'bg-violet-500', 'soft' => 'bg-violet-50 text-violet-600'],
        ];
    @endphp

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-sm text-gray-400 mt-0.5">Selamat datang kembali 👋 Berikut ringkasan bulan ini.</p>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach ($cards as $c)
            @php $s = $c['stat']; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow duration-200">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ $c['title'] }}</p>
                    <p class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ number_format($s['now'], 0, ',', '.') }}</p>
                    <div class="mt-2 text-xs font-medium">
                        @if ($s['now'] == 0 && $s['prev'] == 0)
                            <span class="text-gray-400">Belum ada data</span>
                        @else
                            <span class="flex items-center gap-1 {{ $s['dir'] === 'up' ? 'text-emerald-600' : ($s['dir'] === 'down' ? 'text-red-500' : 'text-gray-400') }}">
                                <i class="fa-solid {{ $s['dir'] === 'up' ? 'fa-arrow-up' : ($s['dir'] === 'down' ? 'fa-arrow-down' : 'fa-minus') }}"></i>
                                {{ abs($s['trend']) }}% vs bulan lalu
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-11 h-11 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fa-solid {{ $c['icon'] }} text-white text-base"></i>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Pendaftaran 7 Bulan Terakhir</h2>
                <p class="text-xs text-gray-400 mt-0.5">Jumlah registrasi baru per bulan</p>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">trx_batchjob_register</span>
        </div>

        <div class="h-52 flex items-end gap-2">
            @foreach ($chart as $bar)
                @php $h = $maxChart > 0 ? (int) round($bar['count'] / $maxChart * 100) : 0; @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group">
                    <span class="text-[10px] font-semibold text-gray-400 mb-1 tabular-nums group-hover:text-blue-500 transition-colors">{{ $bar['count'] }}</span>
                    @if ($h > 0)
                        <div title="{{ $bar['label'] }}: {{ $bar['count'] }}"
                             class="w-full max-w-[36px] rounded-t-md bg-blue-500 opacity-80 group-hover:opacity-100 group-hover:bg-blue-600 transition-all duration-200"
                             style="height: {{ max($h, 4) }}%"></div>
                    @else
                        <div class="w-full max-w-[36px] h-1 rounded-t-md bg-gray-100"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex gap-2 mt-3 border-t border-gray-100 pt-3">
            @foreach ($chart as $bar)
                <div class="flex-1 text-center text-[10px] font-semibold text-gray-400 uppercase tracking-wide">{{ $bar['label'] }}</div>
            @endforeach
        </div>
    </div>

@endsection
@extends('layouts.app')

@section('content')
    @php
        /** @var string $module */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
        /** @var \Illuminate\Support\Collection $cards */

        // Palet warna khusus untuk 8 kartu status Terminasi sesuai Screenshot 1
        $termColors = [
            '11'   => 'from-pink-400 to-rose-400 shadow-pink-200/50',
            '12'   => 'from-pink-400 to-rose-400 shadow-pink-200/50',
            '12.1' => 'from-pink-400 to-rose-400 shadow-pink-200/50',
            '13'   => 'from-amber-400 to-yellow-500 shadow-amber-200/50',
            '14'   => 'from-emerald-400 to-teal-400 shadow-emerald-200/50',
            '15'   => 'from-amber-400 to-yellow-500 shadow-amber-200/50',
            '16'   => 'from-emerald-400 to-teal-400 shadow-emerald-200/50',
            '17'   => 'from-amber-400 to-yellow-500 shadow-amber-200/50',
        ];
    @endphp

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">IMS</a>
        <span class="mx-2 text-gray-300">></span>
        <span class="text-gray-700 font-medium">Terminasi</span>
    </nav>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
            <div>
                <p class="font-semibold">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5"></i>
            <div>
                <p class="font-semibold">Terjadi kesalahan:</p>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Filter Card (2 baris) -->
    <form method="GET" action="{{ route('permintaan.terminasi') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[180px]">
                <select name="layanan" onchange="this.form.submit()" class="w-full appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option value="">SEMUA LAYANAN</option>
                    @foreach($masterLayanan as $l)
                        <option value="{{ $l->nama_kategori_bandwith }}" {{ request('layanan') == $l->nama_kategori_bandwith ? 'selected' : '' }}>{{ $l->nama_kategori_bandwith }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="NAMA / NOMOR LAYANAN" class="w-full bg-transparent border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 px-3 text-sm uppercase tracking-wide outline-none transition-colors placeholder-gray-400">
            </div>
            <div class="relative flex-1 min-w-[180px]">
                <select name="wilayah" onchange="this.form.submit()" class="w-full appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option value="">SEMUA WILAYAH</option>
                    @foreach($masterWilayah as $w)
                        <option value="{{ $w->nama_kota }}" {{ request('wilayah') == $w->nama_kota ? 'selected' : '' }}>{{ $w->nama_kota }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="relative flex-1 min-w-[180px]">
                <select name="status" onchange="this.form.submit()" class="w-full appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option value="">SEMUA STATUS</option>
                    @foreach($masterStatus as $st)
                        <option value="{{ $st->status_terminasi }}" {{ request('status') == $st->status_terminasi ? 'selected' : '' }}>(KD{{ $st->status_terminasi }}) {{ $st->desc_terminasi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-blue-200/50 transition-all duration-200"><i class="fa-solid fa-magnifying-glass"></i>Cari</button>
                <a href="{{ route('permintaan.terminasi') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-rose-200/50 transition-all duration-200"><i class="fa-solid fa-rotate"></i>Reset</a>
                <a href="{{ route('permintaan.terminasi.export', request()->query()) }}" class="bg-amber-400 hover:bg-amber-500 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-amber-200/50 transition-all duration-200"><i class="fa-solid fa-file-export"></i>Export</a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-4 mt-5">
            <div class="relative min-w-[160px]">
                <select name="bulan" onchange="this.form.submit()" class="w-full appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option value="">SEMUA BULAN TER...</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="relative min-w-[140px]">
                <select name="tahun" onchange="this.form.submit()" class="w-full appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option value="">SEMUA TAHUN</option>
                    @foreach(range(date('Y'), 2020) as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
        </div>
    </form>

    <!-- Status Cards (8 Kartu persis Screenshot 1) -->
    @if ($cards->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            @foreach ($cards as $c)
                @php $g = $termColors[$c->code] ?? 'from-slate-400 to-slate-500 shadow-slate-200/50'; @endphp
                <div class="group relative bg-gradient-to-r {{ $g }} rounded-lg px-4 py-3 shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 cursor-default overflow-hidden">
                    <span class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors duration-200"></span>
                    <p class="relative text-xs font-bold text-white">{{ $c->label }} : {{ number_format($c->total, 0, ',', '.') }} User</p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- DataTable Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select class="bg-white border border-gray-200 rounded px-2 py-1 text-sm outline-none focus:border-blue-400"><option>10</option><option>25</option><option>50</option></select>
                <span>entries</span>
            </div>
            @include('partials.pagination', ['rows' => $rows])
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-56">Customer</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Info</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-72">Detail</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-44">State</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-36">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="odd:bg-white even:bg-slate-50/50 hover:bg-blue-50/50 border-b border-gray-100 transition-colors duration-150">
                            {{-- Customer --}}
                            <td class="py-3 px-4 align-top">
                                <p class="text-[11px] text-gray-400 font-medium">{{ $r->kode_trx_terminasi }}</p>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs text-blue-600 hover:text-blue-700 hover:underline transition-colors font-medium mt-0.5">{{ $r->nomor_internet }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs font-bold text-gray-800 hover:text-blue-700 transition-colors mt-0.5">{{ $r->nama_display ?: '-' }}</a>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $r->paket ?: '-' }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">{{ $r->date_create }}</p>
                            </td>

                            {{-- Info --}}
                            <td class="py-3 px-4 align-top">
                                <p class="text-xs font-bold text-slate-800 uppercase">{{ $r->jenis_bangunan ?: 'RUMAH-PRIBADI' }}</p>
                                <p class="text-xs text-gray-500 leading-relaxed mt-1">{{ $r->alamat_p ?: '-' }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    HP : <span class="font-semibold text-gray-700">{{ $r->nomor_hp ?: '-' }}</span>
                                    Email : <span class="font-semibold text-gray-700">{{ $r->email ?: '-' }}</span>
                                </p>
                            </td>

                            {{-- Detail --}}
                            <td class="py-3 px-4 align-top text-xs space-y-1.5">
                                <div>
                                    <span class="text-slate-600 font-medium">Collect Perangkat : </span>
                                    <span class="inline-block {{ $r->collect_perangkat_label === 'Done' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-rose-100 text-rose-600 border border-rose-200' }} text-[10px] font-semibold px-2 py-0.5 rounded">
                                        {{ $r->collect_perangkat_label }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-600 font-medium">Pending Tagihan : </span>
                                    <span class="inline-block {{ $r->collect_payment_label === 'Done' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-rose-100 text-rose-600 border border-rose-200' }} text-[10px] font-semibold px-2 py-0.5 rounded">
                                        {{ $r->collect_payment_label }}
                                    </span>
                                </div>
                            </td>

                            {{-- State --}}
                            <td class="py-3 px-4 align-top">
                                @php
                                    $stBg = 'bg-blue-100 text-blue-600';
                                    if (str_contains($r->status_terminasi, '12')) $stBg = 'bg-amber-100 text-amber-700';
                                    if ($r->status_terminasi == '14') $stBg = 'bg-emerald-100 text-emerald-700';
                                    if ($r->status_terminasi == '16') $stBg = 'bg-rose-100 text-rose-700';
                                @endphp
                                <span class="inline-block {{ $stBg }} text-[11px] font-semibold px-2 py-0.5 rounded">
                                    (KD{{ $r->status_terminasi }}) {{ $r->desc_terminasi ?: 'Req. Terminasi' }}
                                </span>
                                <p class="text-[11px] text-gray-400 mt-1 font-medium">{{ $r->date_create }}</p>
                            </td>

                            {{-- Action --}}
                            <td class="py-3 px-4 align-top">
                                <div class="flex flex-col gap-3">
                                    <button type="button"
                                            onclick="openScheduleCollectModal(
                                                '{{ $r->kode_trx_terminasi }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ $r->date_collect_start ?? '' }}',
                                                '{{ $r->time_collect_start ?? '' }}',
                                                '{{ addslashes($r->team_collect ?? '') }}',
                                                '{{ addslashes($r->note_collect_start ?? '') }}'
                                            )"
                                            class="flex items-start gap-2.5 text-left text-slate-700 hover:text-blue-600 group transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-blue-500 text-lg mt-0.5 shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-blue-600">
                                            Schedule<br>Collect
                                        </span>
                                    </button>

                                    <button type="button"
                                            onclick="openCancelTerminasiModal(
                                                '{{ $r->kode_trx_terminasi }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ addslashes($r->note_termin_cancel ?? '') }}'
                                            )"
                                            class="flex items-center gap-2.5 text-left text-slate-700 hover:text-rose-600 group transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-rose-500 text-lg shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-rose-600">
                                            Cancel
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400 text-sm border-b border-gray-100">No data available in table</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 gap-3">
            <div class="text-sm text-gray-500">Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} entries</div>
            @include('partials.pagination', ['rows' => $rows])
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL SCHEDULE COLLECT TERMINASI -->
    <!-- ============================================ -->
    <div id="modalScheduleCollect" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

            {{-- Header --}}
            <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800">
                    Form Schedule Collect An/ <span id="scheduleCollectNamaHeader" class="text-slate-900"></span>
                </h3>
                <button type="button" onclick="closeScheduleCollectModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="formScheduleCollect" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6 custom-modal-scroll">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left: Date & Time & Note -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Date Schedule<span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_collect_start" id="scheduleCollectDate" required
                                       placeholder="Schedule Collect"
                                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    waktu<span class="text-red-500">*</span>
                                </label>
                                <select name="time_collect_start" id="scheduleCollectTime" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                    <option value="">Select a State</option>
                                    <option value="08:00 - 10:00 WIB">08:00 - 10:00 WIB</option>
                                    <option value="10:00 - 12:00 WIB">10:00 - 12:00 WIB</option>
                                    <option value="13:00 - 15:00 WIB">13:00 - 15:00 WIB</option>
                                    <option value="15:00 - 17:00 WIB">15:00 - 17:00 WIB</option>
                                    <option value="FULL DAY">FULL DAY</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    note<span class="text-red-500">*</span>
                                </label>
                                <textarea name="note_collect_start" id="scheduleCollectNote" rows="3" placeholder="note collect.."
                                          class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Right: Team Checkboxes -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Team<span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2 max-h-56 overflow-y-auto p-3 border border-slate-300 rounded-lg bg-slate-50/50 text-xs">
                                @foreach($teamList ?? [] as $tm)
                                    <label class="flex items-center gap-2 cursor-pointer hover:text-cyan-600 select-none">
                                        <input type="checkbox" name="team_collect[]" value="{{ $tm->nama_karyawan }}" class="team-collect-checkbox rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                                        <span class="font-semibold uppercase text-slate-700 text-xs">{{ $tm->nama_karyawan }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="button" onclick="closeScheduleCollectModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-xmark"></i> Tutup
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL CANCEL TERMINASI -->
    <!-- ============================================ -->
    <div id="modalCancelTerminasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

                {{-- Header --}}
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800">
                        Form cancel terminasi An/ <span id="cancelTerminasiNamaHeader" class="text-slate-900"></span>
                    </h3>
                    <button type="button" onclick="closeCancelTerminasiModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <form id="formCancelTerminasi" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')

                    <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-modal-scroll">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                note<span class="text-red-500">*</span>
                            </label>
                            <textarea name="note_termin_cancel" id="cancelTerminasiNote" rows="3" placeholder="catatan cancel terminasi.."
                                      class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button type="button" onclick="closeCancelTerminasiModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-xmark"></i> Tutup
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i> Update
                        </button>
                    </div>
                </form>

        </div>
    </div>

    <script>
        // Modal Schedule Collect
        function openScheduleCollectModal(kodeTrx, nama, dateVal, timeVal, teamVal, noteVal) {
            const form = document.getElementById('formScheduleCollect');
            form.action = '/permintaan/terminasi/' + encodeURIComponent(kodeTrx) + '/schedule-collect';

            document.getElementById('scheduleCollectNamaHeader').textContent = nama || '-';
            document.getElementById('scheduleCollectDate').value = dateVal || '';

            const timeSelect = document.getElementById('scheduleCollectTime');
            if (timeSelect) timeSelect.value = timeVal || '';

            const selectedTeams = teamVal ? teamVal.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.team-collect-checkbox').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            document.getElementById('scheduleCollectNote').value = noteVal || '';

            document.getElementById('modalScheduleCollect').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeScheduleCollectModal() {
            document.getElementById('modalScheduleCollect').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Modal Cancel Terminasi
        function openCancelTerminasiModal(kodeTrx, nama, noteVal) {
            const form = document.getElementById('formCancelTerminasi');
            form.action = '/permintaan/terminasi/' + encodeURIComponent(kodeTrx) + '/cancel';

            document.getElementById('cancelTerminasiNamaHeader').textContent = nama || '-';
            document.getElementById('cancelTerminasiNote').value = noteVal || '';

            document.getElementById('modalCancelTerminasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelTerminasiModal() {
            document.getElementById('modalCancelTerminasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ESC handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeScheduleCollectModal();
                closeCancelTerminasiModal();
            }
        });
    </script>
@endsection
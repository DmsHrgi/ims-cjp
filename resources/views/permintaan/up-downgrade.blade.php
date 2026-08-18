@extends('layouts.app')

@section('content')
    @php
        /** @var string $module */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
        /** @var \Illuminate\Support\Collection $cards */

        // Palet warna kartu per modul
        $palettes = [
            'ubah'      => ['pink','amber','teal','teal'],
            'terminasi' => ['pink','pink','pink','amber','teal','amber','teal','amber'],
            'suspend'   => ['amber','blue','pink'],
        ];
        $toneGrad = [
            'pink'  => 'from-pink-400 to-rose-400 shadow-pink-200/50',
            'amber' => 'from-amber-400 to-yellow-500 shadow-amber-200/50',
            'teal'  => 'from-emerald-400 to-teal-400 shadow-emerald-200/50',
            'blue'  => 'from-blue-500 via-blue-400 to-teal-400 shadow-blue-200/50',
            'slate' => 'from-slate-400 to-slate-500 shadow-slate-200/50',
        ];
        $pal   = $palettes[$module] ?? ['slate'];
        $grid  = $module === 'suspend' ? 'md:grid-cols-3' : 'lg:grid-cols-4';
    @endphp

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">IMS</a>
        <span class="mx-2 text-gray-300">></span>
        <span class="text-gray-700 font-medium">Ubah Layanan</span>
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

    <!-- Filter Card -->
    <form method="GET" action="{{ route('permintaan.up-downgrade') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
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
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="NAMA /NOMOR LAYANAN" class="w-full bg-transparent border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 px-3 text-sm uppercase tracking-wide outline-none transition-colors placeholder-gray-400">
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
                        <option value="{{ $st->status_ubah_layanan }}" {{ request('status') == $st->status_ubah_layanan ? 'selected' : '' }}>(KD{{ $st->status_ubah_layanan }}) {{ $st->desc_ubah_layanan }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-blue-200/50 transition-all duration-200"><i class="fa-solid fa-magnifying-glass"></i>Cari</button>
                <a href="{{ route('permintaan.up-downgrade') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-rose-200/50 transition-all duration-200"><i class="fa-solid fa-rotate"></i>Reset</a>
                <a href="{{ route('permintaan.up-downgrade.export', request()->query()) }}" class="bg-amber-400 hover:bg-amber-500 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-amber-200/50 transition-all duration-200"><i class="fa-solid fa-file-export"></i>Export</a>
            </div>
        </div>
    </form>

    <!-- Status Cards -->
    @if ($cards->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $grid }} gap-4 mb-6">
            @foreach ($cards as $i => $c)
                @php $g = $toneGrad[$pal[$i % count($pal)]] ?? $toneGrad['slate']; @endphp
                <div class="group relative bg-gradient-to-r {{ $g }} rounded-lg px-5 py-3.5 shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 cursor-default overflow-hidden">
                    <span class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors duration-200"></span>
                    <p class="relative text-sm font-semibold text-white">{{ $c->label }} : {{ number_format($c->total, 0, ',', '.') }} User</p>
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
                        @foreach (['Customer','Address','Old','New','State','Action'] as $h)
                            <th class="group text-left py-3 px-4 text-sm font-semibold text-gray-700 select-none">
                                <div class="flex items-center justify-between pr-4"><span>{{ $h }}</span></div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="odd:bg-white even:bg-slate-50/50 hover:bg-blue-50/50 border-b border-gray-100 transition-colors duration-150">
                            {{-- Customer --}}
                            <td class="py-3 px-4 align-top">
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs text-blue-600 hover:text-blue-700 hover:underline transition-colors font-medium">{{ $r->nomor_internet }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-sm font-bold text-gray-800 hover:text-blue-700 transition-colors mt-0.5">{{ $r->nama_display ?: '-' }}</a>
                            </td>
                            {{-- Address --}}
                            <td class="py-3 px-4 align-top">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-800 uppercase">{{ $r->jenis_bangunan ?: 'RUMAH-PRIBADI' }}</span>
                                    <span class="inline-block bg-blue-100 text-blue-700 text-[10px] font-semibold px-1.5 py-0.5 rounded">Aktif</span>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed mt-1">{{ $r->alamat_p ?: '-' }}</p>
                            </td>
                            {{-- Old --}}
                            <td class="py-3 px-4 align-top">
                                <p class="text-xs font-bold text-slate-800 uppercase">{{ $r->nama_kategori_bandwith_lama ?: '-' }}</p>
                                <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $r->nominal_bandwith_lama ? $r->nominal_bandwith_lama . ' Mbps' : '-' }}</p>
                            </td>
                            {{-- New --}}
                            <td class="py-3 px-4 align-top">
                                <p class="text-xs font-bold text-slate-800 uppercase">{{ $r->nama_kategori_bandwith_baru ?: '-' }}</p>
                                <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $r->nominal_bandwith_baru ? $r->nominal_bandwith_baru . ' Mbps' : '-' }}</p>
                            </td>
                            {{-- State --}}
                            <td class="py-3 px-4 align-top">
                                @php
                                    $stBg = 'bg-blue-100 text-blue-600';
                                    if ($r->status_ubah_layanan == '12') $stBg = 'bg-amber-100 text-amber-700';
                                    if ($r->status_ubah_layanan == '13') $stBg = 'bg-emerald-100 text-emerald-700';
                                    if ($r->status_ubah_layanan == '14') $stBg = 'bg-rose-100 text-rose-700';
                                @endphp
                                <span class="inline-block {{ $stBg }} text-[11px] font-semibold px-2 py-0.5 rounded">
                                    {{ $r->desc_ubah_layanan ?: 'Request' }}
                                </span>
                                <p class="text-[11px] text-gray-400 mt-1 font-medium">
                                    {{ $r->date_request ? \Carbon\Carbon::parse($r->date_request)->format('d F Y') : ($r->date_create ? \Carbon\Carbon::parse($r->date_create)->format('d F Y') : '-') }}
                                </p>
                            </td>
                            {{-- Action --}}
                            <td class="py-3 px-4 align-top">
                                <div class="flex flex-col gap-3">
                                    <button type="button"
                                            onclick="openScheduleModal(
                                                '{{ $r->kode_trx_ubah_layanan }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ addslashes($r->new_pack) }}',
                                                '{{ $r->date_schedule ?? '' }}',
                                                '{{ addslashes($r->note_schedule ?? '') }}'
                                            )"
                                            class="flex items-center gap-2.5 text-left text-slate-700 hover:text-blue-600 group transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-blue-500 text-lg shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-blue-600">
                                            Schedule
                                        </span>
                                    </button>

                                    <button type="button"
                                            onclick="openCancelModal(
                                                '{{ $r->kode_trx_ubah_layanan }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ addslashes($r->nama_kategori_bandwith_lama ?? '') }}',
                                                '{{ $r->nominal_bandwith_lama ?? '' }} Mbps',
                                                '{{ $r->date_cancel ?? '' }}',
                                                '{{ addslashes($r->note_cancel ?? '') }}'
                                            )"
                                            class="flex items-center gap-2.5 text-left text-slate-700 hover:text-rose-600 group transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-rose-500 text-lg shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-rose-600">
                                            Canceled
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400 text-sm border-b border-gray-100">No data available in table</td></tr>
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
    <!-- MODAL SCHEDULE UBAH LAYANAN -->
    <!-- ============================================ -->
    <div id="modalSchedule" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

            {{-- Header --}}
            <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800">
                    Form Schedule Ubah Layanan An/ <span id="scheduleNamaHeader" class="text-slate-900"></span>
                </h3>
                <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="formSchedule" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-modal-scroll">
                    {{-- Permintaan Layanan Baru Box --}}
                    <div>
                        <div class="border-l-4 border-blue-500 pl-3">
                            <p class="text-xs font-semibold text-slate-600">Permintaan Layanan Baru</p>
                        </div>
                        <div class="mt-2 bg-white border border-slate-200 rounded-xl p-5 shadow-xs text-center">
                            <h2 id="scheduleNewPackText" class="text-xl font-bold text-slate-800 uppercase tracking-wide">-</h2>
                        </div>
                    </div>

                    {{-- Schedule Update* --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Schedule Update<span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_schedule" id="scheduleDate" required
                               placeholder="Tanggal Reschedule"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                    </div>

                    {{-- note* --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            note<span class="text-red-500">*</span>
                        </label>
                        <textarea name="note_schedule" id="scheduleNote" rows="3" placeholder="catatan schedule"
                                  class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="button" onclick="closeScheduleModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
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
    <!-- MODAL CANCEL UBAH LAYANAN -->
    <!-- ============================================ -->
    <div id="modalCancel" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

                {{-- Header --}}
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800">
                        Form Cancel Ubah Layanan An/ <span id="cancelNamaHeader" class="text-slate-900"></span>
                    </h3>
                    <button type="button" onclick="closeCancelModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <form id="formCancel" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')

                    <div class="flex-1 overflow-y-auto p-6 custom-modal-scroll">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Left Column: Layanan Lama -->
                            <div>
                                <div class="border-l-4 border-blue-500 pl-3 mb-3">
                                    <p class="text-xs font-semibold text-slate-600">Layanan Lama</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-center text-center">
                                        <span id="cancelOldKatText" class="text-sm font-bold text-slate-800 uppercase tracking-wide">-</span>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-center text-center">
                                        <span id="cancelOldNomText" class="text-sm font-bold text-slate-800 tracking-wide">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Date Update & Note -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        date Update<span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_cancel" id="cancelDate" required
                                           placeholder="Tanggal Cancel"
                                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        note<span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="note_cancel" id="cancelNote" rows="3" placeholder="catatan Cancel"
                                              class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button type="button" onclick="closeCancelModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
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
        // Modal Schedule
        function openScheduleModal(kodeTrx, nama, newPack, dateVal, noteVal) {
            const form = document.getElementById('formSchedule');
            form.action = '/permintaan/up-downgrade/' + encodeURIComponent(kodeTrx) + '/schedule';

            document.getElementById('scheduleNamaHeader').textContent = nama || '-';
            document.getElementById('scheduleNewPackText').textContent = newPack || '-';
            document.getElementById('scheduleDate').value = dateVal || '';
            document.getElementById('scheduleNote').value = noteVal || '';

            document.getElementById('modalSchedule').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeScheduleModal() {
            document.getElementById('modalSchedule').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Modal Cancel
        function openCancelModal(kodeTrx, nama, oldKat, oldNom, dateVal, noteVal) {
            const form = document.getElementById('formCancel');
            form.action = '/permintaan/up-downgrade/' + encodeURIComponent(kodeTrx) + '/cancel';

            document.getElementById('cancelNamaHeader').textContent = nama || '-';
            document.getElementById('cancelOldKatText').textContent = oldKat || '-';
            document.getElementById('cancelOldNomText').textContent = oldNom || '-';
            document.getElementById('cancelDate').value = dateVal || '';
            document.getElementById('cancelNote').value = noteVal || '';
            document.getElementById('cancelDate').value = dateVal || '';
            document.getElementById('cancelNote').value = noteVal || '';

            document.getElementById('modalCancel').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('modalCancel').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Tutup modal dengan ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeScheduleModal();
                closeCancelModal();
            }
        });
    </script>
@endsection
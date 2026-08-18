@extends('layouts.app')

@section('content')
    @php
        /** @var string $module */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
        /** @var \Illuminate\Support\Collection $cards */

        // Palet warna kartu Suspend (Request: Amber, Suspend: Blue, Req. Unsuspend: Pink)
        $suspendColors = [
            '11' => 'from-amber-400 to-yellow-500 shadow-amber-200/50',
            '12' => 'from-blue-500 via-blue-400 to-teal-400 shadow-blue-200/50',
            '13' => 'from-pink-400 to-rose-400 shadow-pink-200/50',
        ];
    @endphp

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">IMS</a>
        <span class="mx-2 text-gray-300">></span>
        <span class="text-gray-700 font-medium">Suspend</span>
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
    <form method="GET" action="{{ route('permintaan.suspend') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
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
                        <option value="{{ $st->status_suspend }}" {{ request('status') == $st->status_suspend ? 'selected' : '' }}>({{ $st->status_suspend }}) {{ $st->desc_status_suspend }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-blue-200/50 transition-all duration-200"><i class="fa-solid fa-magnifying-glass"></i>Cari</button>
                <a href="{{ route('permintaan.suspend') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-rose-200/50 transition-all duration-200"><i class="fa-solid fa-rotate"></i>Reset</a>
                <a href="{{ route('permintaan.suspend.export', request()->query()) }}" class="bg-amber-400 hover:bg-amber-500 text-white px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-amber-200/50 transition-all duration-200"><i class="fa-solid fa-file-export"></i>Export</a>
            </div>
        </div>
    </form>

    <!-- Status Cards (3 Kartu persis Screenshot 1) -->
    @if ($cards->count())
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @foreach ($cards as $c)
                @php $g = $suspendColors[$c->code] ?? 'from-slate-400 to-slate-500 shadow-slate-200/50'; @endphp
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
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-64">Customer</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Alasan Suspend</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-56">State</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-36">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="odd:bg-white even:bg-slate-50/50 hover:bg-blue-50/50 border-b border-gray-100 transition-colors duration-150">
                            {{-- Customer --}}
                            <td class="py-3 px-4 align-top">
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs text-blue-600 hover:text-blue-700 hover:underline transition-colors font-medium">{{ $r->nomor_internet }}</a>
                                <a href="{{ route('pelanggan.detail', $r->nomor_internet) }}" class="block text-xs font-bold text-gray-800 hover:text-blue-700 transition-colors mt-0.5">{{ $r->nama_display ?: '-' }}</a>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $r->paket ?: '-' }}</p>
                            </td>

                            {{-- Alasan Suspend --}}
                            <td class="py-3 px-4 align-top">
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $r->desc_suspend ?: '-' }}</p>
                            </td>

                            {{-- State --}}
                            <td class="py-3 px-4 align-top">
                                @php
                                    $stBg = 'bg-blue-100 text-blue-600';
                                    if ($r->status_suspend == '11') $stBg = 'bg-amber-100 text-amber-700';
                                    if ($r->status_suspend == '12') $stBg = 'bg-blue-100 text-blue-600';
                                    if ($r->status_suspend == '13') $stBg = 'bg-emerald-100 text-emerald-700';
                                    if ($r->status_suspend == '14') $stBg = 'bg-rose-100 text-rose-700';
                                @endphp
                                <span class="inline-block {{ $stBg }} text-[11px] font-semibold px-2 py-0.5 rounded">
                                    (KD{{ $r->status_suspend }}) {{ $r->desc_status_suspend ?: 'Request' }}
                                </span>
                                <p class="text-[11px] text-gray-400 mt-1 font-medium">{{ $r->durasi }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Start : {{ $r->suspend_start ?? '' }}</p>
                            </td>

                            {{-- Action --}}
                            <td class="py-3 px-4 align-top">
                                <div class="flex flex-col gap-3">
                                    <button type="button"
                                            onclick="openApproveModal(
                                                '{{ $r->kode_suspend }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ $r->nomor_internet }}',
                                                '{{ addslashes($r->paket) }}',
                                                '{{ $r->suspend_start ?? '' }}'
                                            )"
                                            class="flex items-center gap-2.5 text-left text-slate-700 hover:text-blue-600 group transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-blue-500 text-lg shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-blue-600">
                                            Approve
                                        </span>
                                    </button>

                                    <button type="button"
                                            onclick="openCancelModal(
                                                '{{ $r->kode_suspend }}',
                                                '{{ addslashes($r->nama_display) }}',
                                                '{{ $r->nomor_internet }}',
                                                '{{ addslashes($r->paket) }}',
                                                '{{ addslashes($r->desc_suspend_cancel ?? '') }}'
                                            )"
                                            class="flex items-center gap-2.5 text-left text-slate-700 hover:text-rose-600 group transition-colors">
                                        <i class="fa-solid fa-xmark text-rose-500 text-lg shrink-0"></i>
                                        <span class="text-xs font-semibold leading-tight text-slate-800 group-hover:text-rose-600">
                                            Canceled
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-gray-400 text-sm border-b border-gray-100">No data available in table</td></tr>
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
    <!-- MODAL APPROVE SUSPEND -->
    <!-- ============================================ -->
    <div id="modalApprove" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

            {{-- Header --}}
            <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800">Form Approve suspend</h3>
                <button type="button" onclick="closeApproveModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="formApprove" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-modal-scroll">
                    {{-- Data Pelanggan Box --}}
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-xs font-bold text-slate-700 mb-2">Data Pelanggan</p>
                        <ul class="text-xs text-slate-600 space-y-1.5 font-medium">
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span id="approveNamaPelanggan" class="font-bold text-slate-800 uppercase">-</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span>Nomor Layanan <span id="approveNoInternet" class="font-semibold text-slate-800">-</span></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span id="approvePaket" class="font-semibold text-slate-800">-</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Start Suspend* --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Start Suspend<span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_suspend_start" id="approveDate" required
                               placeholder="Tanggal suspend"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none">
                    </div>

                    {{-- Kirim whatsapp ke Pelanggan ?* --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Kirim whatsapp ke Pelanggan ?<span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-6 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700 select-none">
                                <input type="radio" name="wa_notif" value="YA" checked class="text-blue-600 focus:ring-blue-500">
                                <span>YA</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700 select-none">
                                <input type="radio" name="wa_notif" value="TIDAK" class="text-blue-600 focus:ring-blue-500">
                                <span>TIDAK</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="button" onclick="closeApproveModal()" class="px-5 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
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
    <!-- MODAL CANCEL SUSPEND -->
    <!-- ============================================ -->
    <div id="modalCancel" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[88vh] flex flex-col overflow-hidden border border-slate-200">

            {{-- Header --}}
            <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800">Form Cancel suspend layanan</h3>
                <button type="button" onclick="closeCancelModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="formCancel" method="POST" action="" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-modal-scroll">
                    {{-- Data Pelanggan Box --}}
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-xs font-bold text-slate-700 mb-2">Data Pelanggan</p>
                        <ul class="text-xs text-slate-600 space-y-1.5 font-medium">
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span id="cancelNamaPelanggan" class="font-bold text-slate-800 uppercase">-</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span>Nomor Layanan <span id="cancelNoInternet" class="font-semibold text-slate-800">-</span></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-slate-400">•</span>
                                <span id="cancelPaket" class="font-semibold text-slate-800">-</span>
                            </li>
                        </ul>
                    </div>

                    {{-- note* --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            note<span class="text-red-500">*</span>
                        </label>
                        <textarea name="note_suspend_cancel" id="cancelNote" rows="3" placeholder="catatan Cancel Suspend.."
                                  class="no-uppercase w-full border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 outline-none resize-none"></textarea>
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
        // Modal Approve
        function openApproveModal(kodeTrx, nama, noInternet, paket, dateVal) {
            const form = document.getElementById('formApprove');
            form.action = '/permintaan/suspend/' + encodeURIComponent(kodeTrx) + '/approve';

            document.getElementById('approveNamaPelanggan').textContent = nama || '-';
            document.getElementById('approveNoInternet').textContent = noInternet || '-';
            document.getElementById('approvePaket').textContent = paket || '-';
            document.getElementById('approveDate').value = dateVal || '';

            document.getElementById('modalApprove').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('modalApprove').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Modal Cancel
        function openCancelModal(kodeTrx, nama, noInternet, paket, noteVal) {
            const form = document.getElementById('formCancel');
            form.action = '/permintaan/suspend/' + encodeURIComponent(kodeTrx) + '/cancel';

            document.getElementById('cancelNamaPelanggan').textContent = nama || '-';
            document.getElementById('cancelNoInternet').textContent = noInternet || '-';
            document.getElementById('cancelPaket').textContent = paket || '-';
            document.getElementById('cancelNote').value = noteVal || '';

            document.getElementById('modalCancel').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('modalCancel').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ESC handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeCancelModal();
            }
        });
    </script>
@endsection
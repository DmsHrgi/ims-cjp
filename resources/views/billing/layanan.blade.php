@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Top Header & Breadcrumb --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="hover:text-gray-700">IMS</span>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="font-semibold text-gray-800">Billing Layanan</span>
        </div>

        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">
            <i class="fa-solid fa-plus text-xs"></i>
            Generate Invoice
        </button>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stat Cards Row (Screenshot 3) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        
        {{-- Card 1: Generating... Auto Publish (Yellow) --}}
        <div class="bg-gradient-to-r from-amber-400 to-amber-500 rounded-2xl p-4 text-white shadow-sm">
            <p class="text-xs font-bold tracking-wide uppercase">Generating... Auto Publish</p>
            <p class="text-sm font-semibold mt-1">
                {{ $statAutoPublish['count'] }} User / Rp {{ number_format($statAutoPublish['amount'], 2, ',', '.') }}
            </p>
        </div>

        {{-- Card 2: Publish Billing (Blue) --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-sm">
            <p class="text-xs font-bold tracking-wide uppercase">Publish Billing</p>
            <p class="text-sm font-semibold mt-1">
                {{ $statPublish['count'] }} User / Rp {{ number_format($statPublish['amount'], 2, ',', '.') }}
            </p>
        </div>

        {{-- Card 3: Waiting Payment (Teal) --}}
        <div class="bg-gradient-to-r from-teal-400 to-teal-500 rounded-2xl p-4 text-white shadow-sm">
            <p class="text-xs font-bold tracking-wide uppercase">Waiting Payment</p>
            <p class="text-sm font-semibold mt-1">
                {{ $statWaiting['count'] }} User / Rp {{ number_format($statWaiting['amount'], 2, ',', '.') }}
            </p>
        </div>

        {{-- Card 4: Paid (Rose/Red) --}}
        <div class="bg-gradient-to-r from-rose-500 to-rose-600 rounded-2xl p-4 text-white shadow-sm">
            <p class="text-xs font-bold tracking-wide uppercase">Paid</p>
            <p class="text-sm font-semibold mt-1">
                {{ $statPaid['count'] }} User / Rp {{ number_format($statPaid['amount'], 2, ',', '.') }}
            </p>
        </div>

    </div>

    {{-- Filter Bar Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 space-y-3">
        <form method="GET" action="{{ route('billing.layanan') }}" class="space-y-3">
            
            {{-- Row 1 Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                
                {{-- 1. Bulan --}}
                <div class="min-w-[130px] flex-1">
                    <select name="bulan" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA BULAN</option>
                        @foreach($bulanList as $k => $v)
                            <option value="{{ $k }}" {{ request('bulan') == $k ? 'selected' : '' }}>{{ strtoupper($v) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Tahun --}}
                <div class="min-w-[120px] flex-1">
                    <select name="tahun" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA TAHUN</option>
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Layanan --}}
                <div class="min-w-[150px] flex-1">
                    <select name="layanan" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA LAYANAN</option>
                        @foreach($layananList as $l)
                            <option value="{{ $l }}" {{ request('layanan') == $l ? 'selected' : '' }}>{{ strtoupper($l) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Status User --}}
                <div class="min-w-[150px] flex-1">
                    <select name="status_user" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA STATUS USER</option>
                        <option value="23" {{ request('status_user') == '23' ? 'selected' : '' }}>USER AKTIF</option>
                        <option value="suspend" {{ request('status_user') == 'suspend' ? 'selected' : '' }}>USER SUSPEND</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                    <a href="{{ route('billing.layanan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors shadow-sm">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                        Reset
                    </a>
                    <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-colors shadow-sm">
                        <i class="fa-solid fa-file-export text-xs"></i>
                        Export
                    </button>
                </div>

            </div>

            {{-- Row 2 Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                
                {{-- 5. Search Nama / Nomor --}}
                <div class="min-w-[200px] flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="NAMA / NOMOR LAYANAN" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-3 pr-8 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-400">
                        @if(request('search'))
                            <a href="{{ route('billing.layanan', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 6. Wilayah --}}
                <div class="min-w-[150px] flex-1">
                    <select name="wilayah" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA WILAYAH</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ strtoupper($w) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 7. Metode Bayar --}}
                <div class="min-w-[150px] flex-1">
                    <select name="metode_bayar" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA METODE BAYAR</option>
                        @foreach($metodeBayarList as $mb)
                            <option value="{{ $mb->payment_type }}" {{ request('metode_bayar') == $mb->payment_type ? 'selected' : '' }}>{{ strtoupper($mb->desc_payment_type) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 7b. Bank / Rekening --}}
                <div class="min-w-[170px] flex-1">
                    <select name="bank" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA BANK / REKENING</option>
                        @foreach($bankList as $bk)
                            <option value="{{ $bk->no_rekening }}" {{ request('bank') == $bk->no_rekening ? 'selected' : '' }}>
                                {{ strtoupper($bk->nama_bank) }} ({{ $bk->no_rekening }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 8. Status Bayar --}}
                <div class="min-w-[150px] flex-1">
                    <select name="status_bayar" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        <option value="">SEMUA STATUS BAYAR</option>
                        @foreach($statusBayarList as $sb)
                            <option value="{{ $sb->status_bill_lay }}" {{ request('status_bayar') == $sb->status_bill_lay ? 'selected' : '' }}>{{ strtoupper($sb->desc_bill_lay) }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

        </form>
    </div>

    {{-- Main Content Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        {{-- Table Controls Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span>Show</span>
                <select class="bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs font-medium outline-none">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <span>entries</span>
            </div>
            <form method="GET" action="{{ route('billing.layanan') }}" class="flex items-center gap-2">
                @foreach(request()->except('search', 'page') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label for="table-search" class="font-medium text-gray-600">Search:</label>
                <input type="text" id="table-search" name="search" value="{{ request('search') }}" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs outline-none focus:border-blue-500 focus:bg-white transition-all">
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Billing Info</th>
                        <th class="py-3.5 px-4">Billing Date & File</th>
                        <th class="py-3.5 px-4">Periode</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Billing State</th>
                        <th class="py-3.5 px-4">Payment Method & Bank</th>
                        <th class="py-3.5 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($rows as $row)
                        @php
                            $jkStr = ($row->jenis_kelamin == 1) ? '(L)' : (($row->jenis_kelamin == 2) ? '(P)' : '');
                            $grossAmount = number_format((float)($row->total_layanan ?? 0), 2, ',', '.');
                            $descState = $row->desc_bill_lay ?: 'Publish Billing';
                            $payMap = ['1' => 'Midtrans', '2' => 'Manual Transfer', '3' => 'Cash To Collector'];
                            $paymentName = $row->desc_payment_type ?: ($payMap[(string)$row->payment_type] ?? 'Midtrans');
                        @endphp
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            
                            {{-- 1. Billing Info --}}
                            <td class="py-4 px-5">
                                <p class="font-mono font-bold text-gray-800 text-[13px]">
                                    BL-{{ $row->nomor_internet ?: $row->kode_billing_layanan }}
                                </p>
                                <p class="font-semibold text-gray-700 mt-0.5">
                                    <span class="underline decoration-gray-300">{{ $row->nomor_internet }}</span> / {{ strtoupper($row->nama_pelanggan ?: ($row->nama_penduduk ?: 'PELANGGAN')) }} {{ $jkStr }}
                                </p>
                                <p class="text-[11px] text-gray-400 font-medium tracking-wide mt-0.5">
                                    {{ strtoupper($row->nama_kategori_bandwith ?: 'UP TO NEW') }} {{ $row->nominal_bandwith ? $row->nominal_bandwith.' Mbps' : '' }}
                                </p>
                            </td>

                            {{-- 2. Billing Date & File --}}
                            <td class="py-4 px-4 text-gray-600">
                                @if($row->payment_publish)
                                    <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($row->payment_publish)->format('Y-m-d H:i:s') }}</p>
                                @else
                                    <p class="text-gray-500 font-medium">Billing Belum diPublish</p>
                                @endif

                                @if($row->expiry)
                                    <p class="text-[11px] text-gray-400 mt-0.5">Jatuh Tempo : {{ \Carbon\Carbon::parse($row->expiry)->format('Y-m-d H:i:s') }}</p>
                                @endif

                                @if($row->invoice_file)
                                    <a href="#" class="text-[11px] text-blue-600 hover:underline font-medium block mt-1">
                                        {{ $row->invoice_file }}
                                    </a>
                                @endif
                            </td>

                            {{-- 3. Periode --}}
                            <td class="py-4 px-4 font-semibold text-gray-700 whitespace-nowrap">
                                {{ $row->periode_tagihan ?: ($row->bulan_tagihan . ' ' . $row->tahun_tagihan) }}
                            </td>

                            {{-- 4. Amount --}}
                            <td class="py-4 px-4 font-bold text-gray-800 tabular-nums whitespace-nowrap">
                                Rp {{ $grossAmount }}
                                <i class="fa-solid fa-lock text-[10px] text-gray-400 ml-1"></i>
                            </td>

                            {{-- 5. Billing State --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <p class="text-[11px] font-semibold text-gray-700">
                                    {{ str_starts_with(strtoupper($row->desc_registrasi ?? ''), 'SUSPEND') ? 'User Suspend' : 'User Aktif' }} {{ strtoupper($row->nama_kota_pasang ?: 'BANDUNG') }}
                                </p>
                                @if($row->status_bill_lay == '15')
                                    <div class="mt-1">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 shadow-2xs">
                                            (KD15) Paid<br><span class="text-[9px] uppercase font-semibold text-purple-900">{{ $row->no_rekening ?: ($row->nama_bank ?: 'CASH') }}</span>
                                        </span>
                                    </div>
                                @elseif($row->status_bill_lay == '16')
                                    <div class="mt-1 space-y-0.5">
                                        <p class="text-[10px] font-semibold text-rose-500">({{ $row->status_bill_lay }}) Cancel Billing</p>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-700 uppercase">TEMPORARY DELETE</span>
                                    </div>
                                @else
                                    <p class="text-[10px] font-semibold text-blue-500 mt-0.5">
                                        ({{ $row->status_bill_lay ?: 'KD13' }}) {{ $descState }}
                                    </p>
                                @endif
                            </td>

                            {{-- 6. Payment Method & Bank --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                @php
                                    $btnBg = 'bg-blue-600 hover:bg-blue-700';
                                    if ($row->payment_type == '1') $btnBg = 'bg-indigo-600 hover:bg-indigo-700';
                                    if ($row->payment_type == '2') $btnBg = 'bg-blue-600 hover:bg-blue-700';
                                    if ($row->payment_type == '3') $btnBg = 'bg-purple-600 hover:bg-purple-700';
                                @endphp
                                <div class="space-y-1">
                                    <button type="button" onclick="openChangePaymentModal('{{ $row->kode_billing_layanan }}', '{{ $row->payment_type }}', '{{ $row->no_rekening ?? '' }}', 'layanan')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $btnBg }} text-white shadow-sm transition-colors cursor-pointer">
                                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                        {{ $paymentName }}
                                    </button>
                                    @if(!empty($row->nama_bank) || !empty($row->no_rekening))
                                        <p class="text-[10px] font-semibold text-slate-600 flex items-center gap-1">
                                            <i class="fa-solid fa-building-columns text-[9px] text-blue-500"></i>
                                            {{ $row->nama_bank ?: $row->no_rekening }}
                                        </p>
                                    @endif
                                    <div class="space-y-0.5 text-[10px] font-semibold text-rose-500">
                                        <div class="flex items-center gap-2">
                                            <a href="#" class="hover:underline flex items-center gap-0.5"><i class="fa-solid fa-paper-plane"></i> Send</a>
                                            <a href="#" class="hover:underline flex items-center gap-0.5"><i class="fa-brands fa-whatsapp"></i> Send</a>
                                        </div>
                                        @if($row->payment_type == '1' || str_contains(strtolower($row->desc_payment_type ?? ''), 'midtrans'))
                                            <a href="#" class="text-blue-500 hover:underline block">Check Link Payment</a>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- 7. Action --}}
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                @php
                                    $isMidtrans = ($row->payment_type == '1' || str_contains(strtolower($row->desc_payment_type ?? ''), 'midtrans'));
                                    $isManualOrCash = ($row->payment_type == '2' || $row->payment_type == '3' || str_contains(strtolower($row->desc_payment_type ?? ''), 'manual') || str_contains(strtolower($row->desc_payment_type ?? ''), 'collector') || str_contains(strtolower($row->desc_payment_type ?? ''), 'cash'));
                                    $isBelumPublish = (empty($row->payment_publish) || $row->status_bill_lay == '11');
                                    $isPaid = ($row->status_bill_lay == '15');
                                @endphp

                                @if($isPaid)
                                    {{-- Tampilan Aksi Khusus Status Paid (Adjust & Rollback) --}}
                                    <div class="flex flex-col items-end gap-1.5">
                                        <button type="button" onclick="openAdjustModal('{{ $row->kode_billing_layanan }}', '{{ addslashes($row->nama_pelanggan ?: ($row->nama_penduduk ?: 'Pelanggan')) }}', '{{ (float)($row->total_layanan ?? 0) }}', '{{ (float)($row->potongan ?? 0) }}', '{{ addslashes($row->desc_potongan ?? '-') }}', '{{ addslashes($row->note_adjusment ?? '') }}')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 px-2.5 py-1 rounded-lg transition-colors cursor-pointer shadow-2xs" title="Adjust / Penyesuaian Invoice">
                                            <i class="fa-solid fa-coins text-xs text-teal-500"></i>
                                            Adjust
                                        </button>

                                        <form method="POST" action="{{ route('billing.layanan.rollback') }}" onsubmit="return confirm('Rollback status invoice ini dari Paid ke Publish Billing?')" class="inline">
                                            @csrf
                                            <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_layanan }}">
                                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-lg transition-colors cursor-pointer shadow-2xs" title="Rollback Pembayaran">
                                                <i class="fa-solid fa-rotate-left text-xs text-amber-500"></i>
                                                Rollback
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Hapus --}}
                                        <form method="POST" action="{{ route('billing.layanan.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus billing ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_layanan }}">
                                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer" title="Hapus Invoice">
                                                <i class="fa-solid fa-trash-can text-xs text-rose-500"></i>
                                                Hapus
                                            </button>
                                        </form>

                                        @if($isBelumPublish)
                                            {{-- Tombol Publish --}}
                                            <form method="POST" action="{{ route('billing.layanan.publish') }}" onsubmit="return confirm('Publish invoice ini sekarang?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_layanan }}">
                                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer shadow-xs" title="Publish Invoice">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-xs text-emerald-500"></i>
                                                    Publish
                                                </button>
                                            </form>
                                        @elseif($isMidtrans)
                                            {{-- Tombol Renew Link (Khusus Midtrans) --}}
                                            <form method="POST" action="{{ route('billing.layanan.renew-link') }}" onsubmit="return confirm('Perbarui link pembayaran (Renew Link) untuk invoice ini?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_layanan }}">
                                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer shadow-xs" title="Renew Link Payment">
                                                    <i class="fa-solid fa-arrows-rotate text-xs text-teal-500"></i>
                                                    Renew Link
                                                </button>
                                            </form>
                                        @elseif($isManualOrCash)
                                            {{-- Tombol Accept (Khusus Manual Transfer & Cash To Collector) --}}
                                            <form method="POST" action="{{ route('billing.layanan.accept') }}" onsubmit="return confirm('Accept pembayaran untuk invoice ini (Ubah status menjadi Paid)?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_layanan }}">
                                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer shadow-xs" title="Accept Pembayaran">
                                                    <i class="fa-solid fa-money-bill-wave text-xs text-emerald-500"></i>
                                                    Accept
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                                Tidak ada data billing layanan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($rows->hasPages())
            <div class="p-4 sm:p-5 border-t border-gray-100">
                {{ $rows->links() }}
            </div>
        @endif

    </div>

</div>

{{-- Modal Change Payment Method & Bank --}}
<div id="changePaymentModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 relative space-y-4">
        
        <form method="POST" action="{{ route('billing.update-payment-type') }}" id="changePaymentForm" class="space-y-4">
            @csrf
            <input type="hidden" name="billing_type" id="modalBillingType" value="layanan">
            <input type="hidden" name="kode_billing" id="modalKodeBilling" value="">

            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-blue-600"></i>
                    <span>Ubah Metode Bayar & Bank</span>
                </h3>
                <button type="button" onclick="closeChangePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Radio Options for Payment Type --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700">Metode Pembayaran (m_payment_type):</label>
                <div class="flex flex-col gap-2 text-xs font-semibold text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-200">
                    @foreach($metodeBayarList as $mb)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="payment_type" value="{{ $mb->payment_type }}" class="payRadio text-blue-600 focus:ring-blue-500">
                            <span>{{ $mb->desc_payment_type }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Select Dropdown for Bank (m_bank) --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700">Bank / Rekening Tujuan (m_bank):</label>
                <select name="no_rekening" id="modalNoRekening" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <option value="">-- Tanpa Bank Khusus / Default --</option>
                    @foreach($bankList as $bk)
                        <option value="{{ $bk->no_rekening }}">
                            {{ $bk->nama_bank }} ({{ $bk->no_rekening }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeChangePaymentModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors shadow-xs">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-900 hover:bg-blue-950 transition-colors shadow-xs">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Update Payment
                </button>
            </div>

        </form>

    </div>
</div>

{{-- Modal Adjust / Penyesuaian Invoice Layanan --}}
<div id="adjustModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 relative space-y-4">
        
        <form method="POST" action="{{ route('billing.layanan.adjust') }}" id="adjustForm" class="space-y-4">
            @csrf
            <input type="hidden" name="kode_billing" id="adjustHiddenKodeBilling" value="">

            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-teal-600"></i>
                    <span>Penyesuaian (Adjust) Tagihan</span>
                </h3>
                <button type="button" onclick="closeAdjustModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Info Pelanggan --}}
            <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl space-y-1 text-xs">
                <p class="text-slate-500 font-medium">Nomor Invoice: <span id="adjustKodeBilling" class="font-mono font-bold text-slate-800"></span></p>
                <p class="text-slate-500 font-medium">Nama Pelanggan: <span id="adjustNamaPelanggan" class="font-bold text-slate-800"></span></p>
            </div>

            {{-- Total Tagihan --}}
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-gray-700">Total Tagihan Layanan (Rp):</label>
                <input type="text" name="total_layanan" id="adjustTotalLayanan" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 focus:bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all">
            </div>

            {{-- Potongan / Diskon --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700">Potongan / Diskon (Rp):</label>
                    <input type="text" name="potongan" id="adjustPotongan" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all" placeholder="0">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700">Alasan Potongan:</label>
                    <input type="text" name="desc_potongan" id="adjustDescPotongan" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all" placeholder="Misal: Diskon Promo">
                </div>
            </div>

            {{-- Catatan Penyesuaian --}}
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-gray-700">Catatan Penyesuaian (Note Adjustment):</label>
                <textarea name="note_adjusment" id="adjustNote" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs font-medium text-gray-800 focus:bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all resize-none" placeholder="Catatan penyesuaian oleh finance..."></textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeAdjustModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors shadow-xs">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 transition-colors shadow-xs">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Adjust
                </button>
            </div>

        </form>

    </div>
</div>

<script>
    function openChangePaymentModal(kodeBilling, currentType, currentRekening, billingType) {
        document.getElementById('modalKodeBilling').value = kodeBilling;
        document.getElementById('modalBillingType').value = billingType || 'layanan';

        var radios = document.querySelectorAll('#changePaymentForm input[name="payment_type"]');
        var matchFound = false;

        radios.forEach(function(r) {
            if (String(r.value) === String(currentType)) {
                r.checked = true;
                matchFound = true;
            } else {
                r.checked = false;
            }
        });

        if (!matchFound && radios.length > 0) {
            radios[0].checked = true;
        }

        var bankSelect = document.getElementById('modalNoRekening');
        if (bankSelect) {
            bankSelect.value = currentRekening || '';
        }

        document.getElementById('changePaymentModal').classList.remove('hidden');
    }

    function closeChangePaymentModal() {
        document.getElementById('changePaymentModal').classList.add('hidden');
    }

    function openAdjustModal(kodeBilling, namaPelanggan, totalLayanan, potongan, descPotongan, note) {
        document.getElementById('adjustHiddenKodeBilling').value = kodeBilling;
        document.getElementById('adjustKodeBilling').textContent = kodeBilling;
        document.getElementById('adjustNamaPelanggan').textContent = namaPelanggan;
        document.getElementById('adjustTotalLayanan').value = totalLayanan ? parseInt(totalLayanan).toLocaleString('id-ID') : '0';
        document.getElementById('adjustPotongan').value = potongan ? parseInt(potongan).toLocaleString('id-ID') : '0';
        document.getElementById('adjustDescPotongan').value = descPotongan || '';
        document.getElementById('adjustNote').value = note || '';

        document.getElementById('adjustModal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').classList.add('hidden');
    }
</script>
@endsection

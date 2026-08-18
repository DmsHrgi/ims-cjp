@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <span class="hover:text-gray-700">IMS</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="font-semibold text-gray-800">Billing Registrasi</span>
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

    {{-- Filter Bar Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('billing.registrasi') }}" class="flex flex-wrap items-center gap-3">
            
            {{-- 1. Layanan --}}
            <div class="min-w-[160px] flex-1">
                <select name="layanan" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <option value="">SEMUA LAYANAN</option>
                    @foreach($layananList as $l)
                        <option value="{{ $l }}" {{ request('layanan') == $l ? 'selected' : '' }}>{{ strtoupper($l) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Nama / Nomor Pelanggan --}}
            <div class="min-w-[200px] flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NAMA PELANGGAN" class="w-full bg-gray-50 border border-cyan-400 rounded-xl pl-3 pr-8 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-400">
                    @if(request('search'))
                        <a href="{{ route('billing.registrasi', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- 3. Status Bayar --}}
            <div class="min-w-[150px] flex-1">
                <select name="status_bayar" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <option value="" {{ request('status_bayar') == '' ? 'selected' : '' }}>DRAFT (BELUM DIPUBLISH)</option>
                    <option value="all" {{ request('status_bayar') == 'all' ? 'selected' : '' }}>SEMUA STATUS BAYAR</option>
                    @foreach($statusBayarList as $sb)
                        <option value="{{ $sb->status_bill_reg }}" {{ request('status_bayar') == $sb->status_bill_reg ? 'selected' : '' }}>{{ strtoupper($sb->desc_bill_reg) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Metode Bayar --}}
            <div class="min-w-[160px] flex-1">
                <select name="metode_bayar" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <option value="">SEMUA METODE BAYAR</option>
                    @foreach($metodeBayarList as $mb)
                        <option value="{{ $mb->payment_type }}" {{ request('metode_bayar') == $mb->payment_type ? 'selected' : '' }}>{{ strtoupper($mb->desc_payment_type) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4b. Bank / Rekening --}}
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

            {{-- 5. Wilayah --}}
            <div class="min-w-[160px] flex-1">
                <select name="wilayah" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <option value="">SEMUA WILAYAH</option>
                    @foreach($wilayahList as $w)
                        <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ strtoupper($w) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                <a href="{{ route('billing.registrasi') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    Reset
                </a>
                <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-colors shadow-sm">
                    <i class="fa-solid fa-file-export text-xs"></i>
                    Export
                </button>
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
            <form method="GET" action="{{ route('billing.registrasi') }}" class="flex items-center gap-2">
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
                        <th class="py-3.5 px-4">Billing Date</th>
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
                            $grossAmount = number_format((float)($row->total_reg ?? 0), 2, ',', '.');
                            $descState = $row->desc_bill_reg ?: 'Draft Billing';
                            $payMap = ['1' => 'Midtrans', '2' => 'Manual Transfer', '3' => 'Cash To Collector'];
                            $paymentName = $row->desc_payment_type ?: ($payMap[(string)$row->payment_type] ?? 'Midtrans');
                        @endphp
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            
                            {{-- 1. Billing Info --}}
                            <td class="py-4 px-5">
                                <p class="font-mono font-bold text-gray-800 text-[13px]">
                                    REG-{{ $row->nomor_internet ?: $row->kode_billing_registrasi }}
                                </p>
                                <p class="font-semibold text-gray-700 mt-0.5">
                                    <span class="underline decoration-gray-300">{{ $row->nomor_internet }}</span> / {{ strtoupper($row->nama_pelanggan ?: ($row->nama_penduduk ?: 'PELANGGAN')) }} {{ $jkStr }}
                                </p>
                                <p class="text-[11px] text-gray-400 font-medium tracking-wide mt-0.5">
                                    {{ strtoupper($row->nama_kategori_bandwith ?: 'UP TO NEW') }} {{ $row->nominal_bandwith ? $row->nominal_bandwith.' Mbps' : '' }}
                                </p>
                            </td>

                            {{-- 2. Billing Date --}}
                            <td class="py-4 px-4 text-gray-600">
                                @if($row->payment_publish)
                                    <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($row->payment_publish)->format('d M Y H:i') }}</p>
                                @else
                                    <p class="text-gray-500 font-medium">Billing Belum diPublish</p>
                                @endif
                            </td>

                            {{-- 3. Amount --}}
                            <td class="py-4 px-4 font-bold text-gray-800 tabular-nums whitespace-nowrap">
                                Rp {{ $grossAmount }}
                                <i class="fa-solid fa-lock text-[10px] text-gray-400 ml-1"></i>
                            </td>

                            {{-- 4. Billing State --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    {{ $descState }}
                                </span>
                            </td>

                            {{-- 5. Payment Method & Bank --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                @php
                                    $btnBg = 'bg-blue-600 hover:bg-blue-700';
                                    if ($row->payment_type == '1') $btnBg = 'bg-indigo-600 hover:bg-indigo-700';
                                    if ($row->payment_type == '2') $btnBg = 'bg-blue-600 hover:bg-blue-700';
                                    if ($row->payment_type == '3') $btnBg = 'bg-purple-600 hover:bg-purple-700';
                                @endphp
                                <div class="space-y-1">
                                    <button type="button" onclick="openChangePaymentModal('{{ $row->kode_billing_registrasi }}', '{{ $row->payment_type }}', '{{ $row->no_rekening ?? '' }}', 'registrasi')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $btnBg }} text-white shadow-sm transition-colors cursor-pointer">
                                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                        {{ $paymentName }}
                                    </button>
                                    @if(!empty($row->nama_bank) || !empty($row->no_rekening))
                                        <p class="text-[10px] font-semibold text-slate-600 flex items-center gap-1">
                                            <i class="fa-solid fa-building-columns text-[9px] text-blue-500"></i>
                                            {{ $row->nama_bank ?: $row->no_rekening }}
                                        </p>
                                    @endif
                                    <div class="flex items-center gap-2 text-[10px] font-semibold text-rose-500">
                                        <a href="#" class="hover:underline flex items-center gap-0.5"><i class="fa-solid fa-paper-plane"></i> UnSend</a>
                                        <a href="#" class="hover:underline flex items-center gap-0.5"><i class="fa-brands fa-whatsapp"></i> UnSend</a>
                                    </div>
                                </div>
                            </td>

                            {{-- 6. Action --}}
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('billing.registrasi.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus billing registrasi ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_registrasi }}">
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer" title="Hapus Invoice Registrasi">
                                            <i class="fa-solid fa-trash-can text-xs text-rose-500"></i>
                                            Hapus
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('billing.registrasi.publish') }}" onsubmit="return confirm('Publish invoice registrasi ini dan pindahkan ke Invoice Layanan?')" class="inline">
                                        @csrf
                                        <input type="hidden" name="kode_billing" value="{{ $row->kode_billing_registrasi }}">
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shadow-xs" title="Publish ke Invoice Layanan">
                                            <i class="fa-solid fa-wand-magic-sparkles text-xs text-emerald-500"></i>
                                            Publish
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                                Tidak ada data billing registrasi yang ditemukan.
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
            <input type="hidden" name="billing_type" id="modalBillingType" value="registrasi">
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

<script>
    function openChangePaymentModal(kodeBilling, currentType, currentRekening, billingType) {
        document.getElementById('modalKodeBilling').value = kodeBilling;
        document.getElementById('modalBillingType').value = billingType || 'registrasi';

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
</script>
@endsection

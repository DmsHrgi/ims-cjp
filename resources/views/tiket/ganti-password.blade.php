@extends('layouts.app')

@section('content')
    @php /** @var \Illuminate\Support\Collection $rows */ @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <nav class="text-sm text-gray-500">
            <a href="{{ route('tiket') }}" class="hover:text-blue-600 transition-colors">IMS</a>
            <span class="mx-2 text-gray-300">></span>
            <span class="text-gray-700 font-medium">Ganti Password</span>
        </nav>
        <button onclick="toggleModal('modalBuatTiket')" class="mt-3 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <i class="fa-solid fa-user-plus"></i> Buat Tiket
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Tiket Permintaan Ganti Password</h2>

        <div class="flex flex-wrap items-center gap-4 mb-6">
            <div class="relative">
                <select class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    @foreach (['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $i => $m)
                        <option {{ $i === 6 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="relative">
                <select class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    @foreach ([2024,2025,2026,2027] as $y)<option {{ $y === 2026 ? 'selected' : '' }}>{{ $y }}</option>@endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="relative">
                <select class="appearance-none bg-white border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 pl-3 pr-8 text-sm font-semibold uppercase tracking-wide outline-none transition-colors cursor-pointer">
                    <option selected>ANTRIAN</option><option>PROSES</option><option>SELESAI</option><option>DITUTUP</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" placeholder="nama/internet" class="w-full bg-transparent border-b-2 border-gray-200 focus:border-cyan-400 text-gray-700 py-2 px-3 text-sm outline-none transition-colors placeholder-gray-400">
            </div>
            <div class="flex items-center gap-2">
                <button class="bg-cyan-400 hover:bg-cyan-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-cyan-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-rotate"></i>Find</button>
                <button class="bg-pink-400 hover:bg-pink-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-pink-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-rotate"></i>Reset</button>
                <button class="bg-amber-400 hover:bg-amber-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-amber-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-file-export"></i>Export</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200">
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-32">Tiket</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Pelanggan</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-32">Info</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Password</th>
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 w-32">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="group odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 border-b border-gray-100 transition-colors duration-150">
                            <td class="relative py-4 px-4 align-top">
                                <span class="absolute left-0 top-0 h-full w-1 bg-pink-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></span>
                                <p class="font-bold text-blue-600 text-sm">{{ $r->tiket ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-4 align-top text-sm text-gray-700">{{ $r->nama_display ?? '-' }}</td>
                            <td class="py-4 px-4 align-top text-sm text-gray-700">{{ $r->nomor_internet ?? '-' }}</td>
                            <td class="py-4 px-4 align-top text-sm text-gray-700">{{ $r->password ?? '-' }}</td>
                            <td class="py-4 px-4 align-top text-sm text-gray-700">{{ $r->status ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200/60 flex items-center justify-center mb-4 shadow-inner">
                                        <i class="fa-solid fa-key text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Tidak ada tiket ganti password</p>
                                    <p class="text-xs text-gray-400 mt-1">Modul ini belum memiliki tabel transaksi pada skema <span class="font-mono text-gray-500">ims</span>.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat Tiket -->
    <div id="modalBuatTiket" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalBuatTiket')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Tiket Ganti Password</h3>
                    <button onclick="toggleModal('modalBuatTiket')" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="p-6">
                    <div class="bg-slate-50 rounded-lg p-6 border border-gray-100">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Internet<span class="text-red-500">*</span></label>
                            <div class="flex gap-3">
                                <input type="text" placeholder="Search" class="flex-1 bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2.5 px-4 text-sm rounded-lg outline-none transition-colors placeholder-gray-400">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-blue-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">CEK</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Perubahan<span class="text-red-500">*</span></label>
                            <textarea rows="4" class="w-full bg-white border border-gray-200 focus:border-blue-500 text-gray-700 py-2.5 px-4 text-sm rounded-lg outline-none transition-colors resize-none" placeholder="Password Lama :&#10;[ketikdisini]&#10;&#10;Password Baru :&#10;[ketikdisini]"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <button onclick="toggleModal('modalBuatTiket')" class="bg-cyan-400 hover:bg-cyan-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-cyan-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-xmark"></i>Tutup</button>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-200/50 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><i class="fa-solid fa-floppy-disk"></i>Update</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(id){var m=document.getElementById(id);m.classList.toggle('hidden');document.body.style.overflow=m.classList.contains('hidden')?'auto':'hidden';}
        document.addEventListener('keydown',function(e){if(e.key==='Escape'){var m=document.getElementById('modalBuatTiket');if(m&&!m.classList.contains('hidden'))toggleModal('modalBuatTiket');}});
    </script>
@endsection
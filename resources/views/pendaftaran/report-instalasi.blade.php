@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-500 transition-colors">IMS</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <a href="{{ route('pendaftaran') }}" class="hover:text-blue-500 transition-colors">Pendaftaran</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-gray-600 font-medium">Report Instalasi</span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">
                Report Instalasi An/{{ $customer->nama_pelanggan ?: $customer->nama_penduduk ?: 'Pelanggan' }}
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                No. Internet: <span class="font-bold text-blue-600">{{ $customer->nomor_internet }}</span> | 
                Layanan: <span class="font-medium text-gray-700">{{ $customer->nama_kategori_bandwith }} {{ $customer->nominal_bandwith }} Mbps</span>
            </p>
        </div>

        <a href="{{ route('pendaftaran') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-xl text-xs shadow-sm transition-all">
            <i class="fa-solid fa-arrow-left text-gray-400"></i>
            <span>Kembali ke Pendaftaran</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Terdapat kesalahan pada inputan:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('pendaftaran.update-report-instalasi', $customer->nomor_internet) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- ==================== KOLOM KIRI ==================== -->
                <div class="lg:col-span-6 space-y-6">
                    
                    <!-- 1. Jadwal Ulang Pemasangan -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-rose-500">Jadwal Ulang Pemasangan ?</span>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-gray-500 font-medium">
                            <input type="checkbox" id="is_reschedule" name="is_reschedule" value="1" onchange="toggleReschedule(this)" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span>Ya, Jadwal Ulang</span>
                        </label>
                    </div>

                    <!-- Reschedule Date Input (Hidden by default) -->
                    <div id="reschedule_box" class="hidden bg-rose-50/60 border border-rose-100 p-4 rounded-xl space-y-2">
                        <label class="block text-xs font-semibold text-rose-700">Tanggal Jadwal Ulang Baru</label>
                        <input type="date" name="reschedule_date" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:border-blue-500 outline-none">
                    </div>

                    <!-- 2. Selesai Instalasi & Catatan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Selesai Instalasi <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="instalasi_date_finish" 
                                   value="{{ old('instalasi_date_finish', $instalasi->instalasi_date_finish ?? date('Y-m-d')) }}" 
                                   class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-800 focus:border-blue-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Catatan Selesai Instalasi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="instalasi_note_finish" 
                                   value="{{ old('instalasi_note_finish', $instalasi->instalasi_note_finish ?? '') }}" 
                                   placeholder="catatan Instalasi" 
                                   class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-800 focus:border-blue-500 outline-none" required>
                        </div>
                    </div>

                    <!-- 3. Team Instalasi -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Team Instalasi <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-48 overflow-y-auto p-3 border border-gray-100 rounded-xl bg-gray-50/50">
                            @foreach ($teamList as $tm)
                                @php
                                    $isChecked = in_array(trim($tm->nama_karyawan), $selectedTeams);
                                @endphp
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-gray-700 hover:text-blue-600 transition-colors">
                                    <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" {{ $isChecked ? 'checked' : '' }} class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="truncate font-medium uppercase text-[11px]">{{ $tm->nama_karyawan }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Update Foto Mapping -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Update Foto Mapping <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative border-2 border-dashed border-gray-200 hover:border-blue-400 bg-gray-50/50 hover:bg-blue-50/30 rounded-2xl p-6 text-center transition-all cursor-pointer group" onclick="document.getElementById('foto_mapping').click()">
                            <input type="file" id="foto_mapping" name="foto_mapping" accept="image/*" class="hidden" onchange="previewImage(this)">
                            
                            <div id="dropzone_content" class="space-y-2">
                                <div class="w-12 h-12 rounded-full bg-white border border-gray-100 shadow-xs mx-auto flex items-center justify-center text-gray-400 group-hover:text-blue-500 group-hover:scale-110 transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">
                                    Drag and drop a file here or click
                                </p>
                                <p class="text-[10px] text-gray-400">Format: JPG, PNG, WEBP (Max 5MB)</p>
                            </div>

                            <!-- Preview Container -->
                            <div id="preview_box" class="{{ !empty($instalasi->foto_peta) ? '' : 'hidden' }} space-y-2">
                                <img id="img_preview" src="{{ !empty($instalasi->foto_peta) ? asset($instalasi->foto_peta) : '' }}" class="max-h-36 mx-auto rounded-lg shadow-sm object-cover border border-gray-200">
                                <span id="file_name" class="block text-xs font-semibold text-gray-600 truncate">
                                    {{ !empty($instalasi->foto_peta) ? basename($instalasi->foto_peta) : '' }}
                                </span>
                                <span class="text-[10px] text-blue-600 underline">Klik untuk mengganti foto</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ==================== KOLOM KANAN ==================== -->
                <div class="lg:col-span-6 space-y-6 lg:border-l lg:border-gray-100 lg:pl-8">
                    
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                        Perangkat / Peralatan Yang Digunakan
                    </h3>

                    <!-- Input Tambah Perangkat -->
                    <div class="grid grid-cols-12 gap-3 items-end bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
                        <div class="col-span-6">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Perangkat</label>
                            <select id="select_barang" onchange="updateSatuanLabel()" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-800 focus:border-blue-500 outline-none">
                                <option value="">Pilih Perangkat</option>
                                @foreach ($barangList as $b)
                                    <option value="{{ $b->kode_barang }}" data-satuan="{{ $b->satuan ?: 'UNIT' }}" data-nama="{{ $b->nama_barang }} {{ $b->tipe_barang }}">
                                        {{ $b->nama_barang }} {{ $b->tipe_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                                Jumlah (<span id="satuan_label">Unit</span>)
                            </label>
                            <input type="number" id="input_jumlah" min="1" value="1" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-800 focus:border-blue-500 outline-none">
                        </div>
                        <div class="col-span-2">
                            <button type="button" onclick="addBarangItem()" class="w-full bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold py-2 rounded-lg transition-all shadow-xs flex items-center justify-center gap-1">
                                <span>Add</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Daftar Perangkat Terpasang -->
                    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-2xs">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-gray-600 font-bold border-b border-gray-100 uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th class="py-3 px-4">Barang</th>
                                    <th class="py-3 px-4 w-36">Jumlah</th>
                                    <th class="py-3 px-4 w-16 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="table_barang_body" class="divide-y divide-gray-100 font-medium">
                                @forelse ($installedBarang as $ib)
                                    <tr class="item-row hover:bg-slate-50/50">
                                        <td class="py-3 px-4 font-semibold text-gray-800 uppercase">
                                            {{ $ib->nama_barang }} {{ $ib->tipe_barang }}
                                            <input type="hidden" name="items[{{ $loop->index }}][kode_barang]" value="{{ $ib->kode_barang }}">
                                        </td>
                                        <td class="py-3 px-4 font-bold text-gray-700 uppercase">
                                            {{ $ib->jumlah_barang }} {{ $ib->satuan ?: 'UNIT' }}
                                            <input type="hidden" name="items[{{ $loop->index }}][jumlah]" value="{{ $ib->jumlah_barang }}">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <button type="button" onclick="removeItemRow(this)" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-500 transition-colors inline-flex items-center justify-center">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty_row">
                                        <td colspan="3" class="py-6 text-center text-gray-400 italic">
                                            Belum ada perangkat ditambahkan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('pendaftaran') }}" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-all shadow-xs flex items-center gap-2">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Batal</span>
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-blue-200 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Update</span>
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count($installedBarang) }};

    function toggleReschedule(checkbox) {
        const box = document.getElementById('reschedule_box');
        if (checkbox.checked) {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    function updateSatuanLabel() {
        const select = document.getElementById('select_barang');
        const selectedOpt = select.options[select.selectedIndex];
        const satuan = selectedOpt ? selectedOpt.getAttribute('data-satuan') : 'Unit';
        document.getElementById('satuan_label').textContent = satuan || 'Unit';
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img_preview').src = e.target.result;
                document.getElementById('file_name').textContent = input.files[0].name;
                document.getElementById('dropzone_content').classList.add('hidden');
                document.getElementById('preview_box').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addBarangItem() {
        const select = document.getElementById('select_barang');
        const kodeBarang = select.value;
        if (!kodeBarang) {
            alert('Pilih perangkat terlebih dahulu!');
            return;
        }

        const selectedOpt = select.options[select.selectedIndex];
        const namaBarang = selectedOpt.getAttribute('data-nama');
        const satuan = selectedOpt.getAttribute('data-satuan') || 'UNIT';
        const jumlah = parseInt(document.getElementById('input_jumlah').value) || 1;

        const emptyRow = document.getElementById('empty_row');
        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('table_barang_body');
        const tr = document.createElement('tr');
        tr.className = 'item-row hover:bg-slate-50/50';
        tr.innerHTML = `
            <td class="py-3 px-4 font-semibold text-gray-800 uppercase">
                ${namaBarang}
                <input type="hidden" name="items[${itemIndex}][kode_barang]" value="${kodeBarang}">
            </td>
            <td class="py-3 px-4 font-bold text-gray-700 uppercase">
                ${jumlah} ${satuan}
                <input type="hidden" name="items[${itemIndex}][jumlah]" value="${jumlah}">
            </td>
            <td class="py-3 px-4 text-center">
                <button type="button" onclick="removeItemRow(this)" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-500 transition-colors inline-flex items-center justify-center">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        itemIndex++;

        // Reset inputs
        select.value = '';
        document.getElementById('input_jumlah').value = 1;
        updateSatuanLabel();
    }

    function removeItemRow(btn) {
        const tr = btn.closest('tr');
        tr.remove();

        const tbody = document.getElementById('table_barang_body');
        if (tbody.querySelectorAll('.item-row').length === 0) {
            tbody.innerHTML = `
                <tr id="empty_row">
                    <td colspan="3" class="py-6 text-center text-gray-400 italic">
                        Belum ada perangkat ditambahkan
                    </td>
                </tr>
            `;
        }
    }
</script>
@endsection

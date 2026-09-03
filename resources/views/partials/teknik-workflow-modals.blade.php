    <!-- ============================================ -->
    <!-- MODAL JADWAL SURVEY (ROLE TEKNIK - PROSES 1) -->
    <!-- ============================================ -->
    <div id="modalSurvey" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeSurveyModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="surveyModalTitle">Form Survey An/</h3>
                    <button type="button" onclick="closeSurveyModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formSurvey" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Tanggal Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Survey<span class="text-rose-500">*</span></label>
                                <input type="date" name="survey_date_start" id="surveyDateStart" required class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all">
                            </div>

                            <!-- Catatan Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Survey<span class="text-rose-500">*</span></label>
                                <textarea name="survey_note" id="surveyNote" rows="3" required placeholder="masukan catatan untuk teknisi lapangan saat proses instalasi" class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all placeholder-slate-400"></textarea>
                            </div>

                            <!-- Foto Mapping -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Mapping<span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="file" name="foto_mapping" id="fotoMappingInput" accept="image/*" class="hidden" onchange="handleFotoMappingChange(this, 'survey')">
                                    
                                    <!-- Dropzone -->
                                    <div id="fotoMappingSurveyDropzone" class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingInput').click()">
                                        <div class="flex flex-col items-center justify-center space-y-1.5">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                            </div>
                                            <p class="text-xs text-slate-600 font-medium">Klik untuk upload foto mapping</p>
                                            <p class="text-[10px] text-slate-400">JPG, PNG, WEBP (Max 5MB)</p>
                                        </div>
                                    </div>

                                    <!-- Preview Box -->
                                    <div id="fotoMappingSurveyPreviewBox" class="hidden border border-slate-200 rounded-xl p-3 bg-white shadow-xs">
                                        <div class="flex items-center gap-3">
                                            <div class="relative group/img cursor-pointer w-20 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0" onclick="viewCurrentFotoMapping('survey')">
                                                <img id="fotoMappingSurveyImg" src="" alt="Preview Foto Mapping" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span id="fotoMappingSurveyBadge" class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 mb-1">Foto Baru</span>
                                                <p id="fotoMappingSurveyFileName" class="text-xs font-semibold text-slate-700 truncate">nama_file.webp</p>
                                                <div class="flex items-center gap-2 mt-1.5">
                                                    <button type="button" onclick="viewCurrentFotoMapping('survey')" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                                        <i class="fa-solid fa-eye"></i> Lihat
                                                    </button>
                                                    <span class="text-slate-300">•</span>
                                                    <button type="button" onclick="document.getElementById('fotoMappingInput').click()" class="text-[11px] font-semibold text-slate-600 hover:text-slate-800 flex items-center gap-1">
                                                        <i class="fa-solid fa-arrows-rotate"></i> Ganti
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Waktu Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Waktu Survey<span class="text-rose-500">*</span></label>
                                <select name="survey_time" id="surveyTime" required class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 py-2.5 px-3.5 text-xs rounded-xl outline-none transition-all">
                                    <option value="" disabled selected>Pilih waktu survey</option>
                                    <option value="08:00 - 10:00">08:00 - 10:00</option>
                                    <option value="10:00 - 12:00">10:00 - 12:00</option>
                                    <option value="13:00 - 15:00">13:00 - 15:00</option>
                                    <option value="15:00 - 17:00">15:00 - 17:00</option>
                                    <option value="17:00 - 19:00">17:00 - 19:00</option>
                                </select>
                            </div>

                            <!-- Team Survey -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Survey</label>
                                <div class="grid grid-cols-2 gap-2 max-h-[210px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                    @if(isset($teamTeknisList))
                                        @foreach($teamTeknisList as $tm)
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="survey-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeSurveyModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL REPORT SURVEY (ROLE TEKNIK - PROSES 2) -->
    <!-- ============================================ -->
    <div id="modalReportSurvey" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeReportSurveyModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="reportSurveyModalTitle">Report Survey An/</h3>
                    <button type="button" onclick="closeReportSurveyModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formReportSurvey" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Checkbox Jadwal Ulang Survey -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-rose-500">Jadwal Ulang Survey ?</label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_reschedule" id="checkRescheduleSurvey" onchange="toggleRescheduleSurvey(this)" class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <span class="text-xs font-medium text-slate-600">Ya, Jadwal Ulang</span>
                                </label>
                            </div>

                            <!-- Form Reschedule (hidden by default) -->
                            <div id="sectionRescheduleSurvey" class="hidden space-y-3 p-3 bg-amber-50/50 border border-amber-200/80 rounded-xl">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Jadwal Ulang Baru<span class="text-rose-500">*</span></label>
                                    <input type="date" name="reschedule_date" id="rescheduleDate" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                            </div>

                            <!-- Form Selesai Survey -->
                            <div id="sectionSelesaiSurvey" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai Survey<span class="text-rose-500">*</span></label>
                                        <input type="date" name="survey_date_finish" id="surveyDateFinish" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Selesai Survey<span class="text-rose-500">*</span></label>
                                        <input type="text" name="survey_note_finish" id="surveyNoteFinish" required placeholder="catatan survey" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                </div>

                                <!-- Team Survey -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Survey</label>
                                    <div class="grid grid-cols-2 gap-2 max-h-[170px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                        @if(isset($teamTeknisList))
                                            @foreach($teamTeknisList as $tm)
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                    <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="report-survey-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                    <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Update Foto Mapping -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Foto Mapping<span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="file" name="foto_mapping" id="fotoMappingUpdateInput" accept="image/*" class="hidden" onchange="handleFotoMappingChange(this, 'report')">
                                        
                                        <!-- Dropzone -->
                                        <div id="fotoMappingReportDropzone" class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingUpdateInput').click()">
                                            <div class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                                </div>
                                                <p class="text-xs text-slate-600 font-medium">Klik untuk upload foto mapping baru</p>
                                                <p class="text-[10px] text-slate-400">JPG, PNG, WEBP (Max 5MB)</p>
                                            </div>
                                        </div>

                                        <!-- Preview Box -->
                                        <div id="fotoMappingReportPreviewBox" class="hidden border border-slate-200 rounded-xl p-3 bg-white shadow-xs">
                                            <div class="flex items-center gap-3">
                                                <div class="relative group/img cursor-pointer w-20 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0" onclick="viewCurrentFotoMapping('report')">
                                                    <img id="fotoMappingReportImg" src="" alt="Preview Foto Mapping" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span id="fotoMappingReportBadge" class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 mb-1">Foto Tersimpan</span>
                                                    <p id="fotoMappingReportFileName" class="text-xs font-semibold text-slate-700 truncate">nama_file.webp</p>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <button type="button" onclick="viewCurrentFotoMapping('report')" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                                            <i class="fa-solid fa-eye"></i> Lihat
                                                        </button>
                                                        <span class="text-slate-300">•</span>
                                                        <button type="button" onclick="document.getElementById('fotoMappingUpdateInput').click()" class="text-[11px] font-semibold text-slate-600 hover:text-slate-800 flex items-center gap-1">
                                                            <i class="fa-solid fa-arrows-rotate"></i> Ganti
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="reportSurveySelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @if(isset($barangList))
                                                @foreach($barangList as $b)
                                                    <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }} {{ $b->tipe_barang }}" data-satuan="{{ $b->satuan ?: 'UNIT' }}">{{ $b->nama_barang }} {{ $b->tipe_barang }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="reportSurveyQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addReportSurveyBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[170px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableReportSurveyBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyReportSurveyBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenReportSurveyBarangContainer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeReportSurveyModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL JADWAL INSTALASI (ROLE TEKNIK - PROSES 3)-->
    <!-- ============================================ -->
    <div id="modalFormInstalasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeFormInstalasiModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="formInstalasiModalTitle">Form Instalasi An/</h3>
                    <button type="button" onclick="closeFormInstalasiModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formInstalasiTeknik" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- PERMINTAAN DARI PELANGGAN -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Permintaan Dari Pelanggan</label>
                                <div class="bg-amber-400 text-slate-900 font-bold p-3.5 rounded-xl text-sm uppercase shadow-xs border border-amber-500/30" id="instalasiNoteRequest">
                                    -
                                </div>
                            </div>

                            <!-- Tanggal & Waktu Instalasi -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Instalasi<span class="text-rose-500">*</span></label>
                                    <input type="date" name="instalasi_date_start" id="instalasiDateStart" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Instalasi<span class="text-rose-500">*</span></label>
                                    <select name="instalasi_time" id="instalasiTime" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                        <option value="" disabled selected>Pilih waktu</option>
                                        <option value="08:00 - 10:00">08:00 - 10:00</option>
                                        <option value="10:00 - 12:00">10:00 - 12:00</option>
                                        <option value="13:00 - 15:00">13:00 - 15:00</option>
                                        <option value="15:00 - 17:00">15:00 - 17:00</option>
                                        <option value="17:00 - 19:00">17:00 - 19:00</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Team Instalasi -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Instalasi</label>
                                <div class="grid grid-cols-2 gap-2 max-h-[170px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                    @if(isset($teamTeknisList))
                                        @foreach($teamTeknisList as $tm)
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="instalasi-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Catatan Pemasangan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Pemasangan<span class="text-rose-500">*</span></label>
                                <textarea name="instalasi_note" id="instalasiNote" rows="3" required placeholder="masukan catatan untuk teknisi lapangan saat proses instalasi." class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-xl outline-none placeholder-slate-400"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="instalasiSelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @if(isset($barangList))
                                                @foreach($barangList as $b)
                                                    <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }} {{ $b->tipe_barang }}" data-satuan="{{ $b->satuan ?: 'UNIT' }}">{{ $b->nama_barang }} {{ $b->tipe_barang }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="instalasiQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addInstalasiBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[170px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableInstalasiBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyInstalasiBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenInstalasiBarangContainer"></div>
                            </div>

                            <!-- Foto Mapping Peta (Read-only Preview with Zoom) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Mapping</label>
                                <div id="fotoMappingInstalasiContainer" class="hidden">
                                    <div class="border border-slate-200 rounded-xl p-3 bg-white shadow-xs">
                                        <div class="flex items-center gap-3">
                                            <div class="relative group/img cursor-pointer w-20 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0" onclick="viewCurrentFotoMapping('instalasi')">
                                                <img id="fotoMappingInstalasiImg" src="" alt="Preview Foto Mapping" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 mb-1">Hasil Survey</span>
                                                <p id="fotoMappingInstalasiFileName" class="text-xs font-semibold text-slate-700 truncate">foto_mapping.webp</p>
                                                <button type="button" onclick="viewCurrentFotoMapping('instalasi')" class="mt-1.5 text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                                    <i class="fa-solid fa-eye"></i> Lihat Foto Peta
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="fotoMappingInstalasiEmpty" class="border border-dashed border-slate-200 rounded-xl p-4 text-center bg-slate-50/50">
                                    <p class="text-xs text-slate-400 italic">Belum ada foto mapping dari proses survey</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeFormInstalasiModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL REPORT INSTALASI (ROLE TEKNIK - PROSES 4)-->
    <!-- ============================================ -->
    <div id="modalReportInstalasi" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeReportInstalasiModal()"></div>

        <div class="flex min-h-screen w-full items-center justify-center p-3 sm:p-4 md:p-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden border border-slate-200/80 my-auto transform transition-all">

                <!-- Modal Header -->
                <div class="shrink-0 flex items-center justify-between px-6 py-4 bg-white border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800" id="reportInstalasiModalTitle">Report Instalasi An/</h3>
                    <button type="button" onclick="closeReportInstalasiModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="formReportInstalasi" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Checkbox Jadwal Ulang Pemasangan -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-rose-500">Jadwal Ulang Pemasangan ?</label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_reschedule" id="checkRescheduleReportInstalasi" onchange="toggleRescheduleReportInstalasi(this)" class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <span class="text-xs font-medium text-slate-600">Ya, Jadwal Ulang</span>
                                </label>
                            </div>

                            <!-- Form Reschedule (hidden by default) -->
                            <div id="sectionRescheduleReportInstalasi" class="hidden space-y-3 p-3 bg-amber-50/50 border border-amber-200/80 rounded-xl">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Jadwal Ulang Baru<span class="text-rose-500">*</span></label>
                                    <input type="date" name="reschedule_date" id="reportInstalasiRescheduleDate" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                </div>
                            </div>

                            <!-- Form Selesai Instalasi -->
                            <div id="sectionSelesaiReportInstalasi" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai Instalasi<span class="text-rose-500">*</span></label>
                                        <input type="date" name="instalasi_date_finish" id="reportInstalasiDateFinish" required class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Selesai Instalasi<span class="text-rose-500">*</span></label>
                                        <input type="text" name="instalasi_note_finish" id="reportInstalasiNoteFinish" required placeholder="catatan Instalasi" class="w-full bg-white border border-slate-200 focus:border-blue-500 text-slate-800 py-2 px-3 text-xs rounded-lg outline-none">
                                    </div>
                                </div>

                                <!-- Team Instalasi -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Team Instalasi</label>
                                    <div class="grid grid-cols-2 gap-2 max-h-[170px] overflow-y-auto custom-modal-scroll p-3 border border-slate-200 rounded-xl text-xs text-slate-700 bg-slate-50/50">
                                        @if(isset($teamTeknisList))
                                            @foreach($teamTeknisList as $tm)
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1.5 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                    <input type="checkbox" name="teams[]" value="{{ $tm->nama_karyawan }}" class="report-instalasi-team-cb w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                    <span class="truncate uppercase text-[11px] font-semibold text-slate-700">{{ $tm->nama_karyawan }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Update Foto Mapping -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Foto Mapping<span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="file" name="foto_mapping" id="fotoMappingReportInstalasiInput" accept="image/*" class="hidden" onchange="handleFotoMappingChange(this, 'reportInstalasi')">
                                        
                                        <!-- Dropzone -->
                                        <div id="fotoMappingReportInstalasiDropzone" class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all group cursor-pointer" onclick="document.getElementById('fotoMappingReportInstalasiInput').click()">
                                            <div class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                                </div>
                                                <p class="text-xs text-slate-600 font-medium">Klik untuk upload foto mapping baru</p>
                                                <p class="text-[10px] text-slate-400">JPG, PNG, WEBP (Max 5MB)</p>
                                            </div>
                                        </div>

                                        <!-- Preview Box -->
                                        <div id="fotoMappingReportInstalasiPreviewBox" class="hidden border border-slate-200 rounded-xl p-3 bg-white shadow-xs">
                                            <div class="flex items-center gap-3">
                                                <div class="relative group/img cursor-pointer w-20 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0" onclick="viewCurrentFotoMapping('reportInstalasi')">
                                                    <img id="fotoMappingReportInstalasiImg" src="" alt="Preview Foto Mapping" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span id="fotoMappingReportInstalasiBadge" class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 mb-1">Foto Tersimpan</span>
                                                    <p id="fotoMappingReportInstalasiFileName" class="text-xs font-semibold text-slate-700 truncate">nama_file.webp</p>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <button type="button" onclick="viewCurrentFotoMapping('reportInstalasi')" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                                            <i class="fa-solid fa-eye"></i> Lihat
                                                        </button>
                                                        <span class="text-slate-300">•</span>
                                                        <button type="button" onclick="document.getElementById('fotoMappingReportInstalasiInput').click()" class="text-[11px] font-semibold text-slate-600 hover:text-slate-800 flex items-center gap-1">
                                                            <i class="fa-solid fa-arrows-rotate"></i> Ganti
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Perangkat / Peralatan Yang Digunakan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-800">Perangkat/ Peralatan Yang Digunakan</h4>
                                
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perangkat</label>
                                        <select id="reportInstalasiSelectBarang" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none">
                                            <option value="">Pilih Perangkat</option>
                                            @if(isset($barangList))
                                                @foreach($barangList as $b)
                                                    <option value="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }} {{ $b->tipe_barang }}" data-satuan="{{ $b->satuan ?: 'UNIT' }}">{{ $b->nama_barang }} {{ $b->tipe_barang }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah</label>
                                        <input type="number" id="reportInstalasiQtyBarang" min="1" value="1" class="w-full bg-white border border-slate-200 text-slate-800 py-1.5 px-2.5 text-xs rounded-lg outline-none text-center">
                                    </div>
                                    <button type="button" onclick="addReportInstalasiBarang()" class="px-3 py-1.5 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-xs font-bold transition-colors">
                                        Add
                                    </button>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[170px] overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                                <th class="py-1.5 px-3 text-left">Barang</th>
                                                <th class="py-1.5 px-3 text-center">Jumlah</th>
                                                <th class="py-1.5 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableReportInstalasiBarang" class="divide-y divide-slate-100">
                                            <tr id="emptyReportInstalasiBarangRow">
                                                <td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="hiddenReportInstalasiBarangContainer"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeReportInstalasiModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-500 hover:bg-cyan-600 transition-colors shadow-xs">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Foto / Lightbox -->
    <div id="modalGlobalFotoPreview" class="hidden fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4" onclick="if(event.target === this) closeGlobalFotoPreview()">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-200">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-image text-blue-600"></i>
                    <h3 id="globalFotoPreviewTitle" class="text-sm font-bold text-slate-800 truncate">Preview Foto Mapping</h3>
                </div>
                <div class="flex items-center gap-2">
                    <a id="globalFotoPreviewDownload" href="#" target="_blank" class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-white rounded-lg border border-slate-200 transition-all flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Asli
                    </a>
                    <button type="button" onclick="closeGlobalFotoPreview()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 bg-slate-900/5 flex items-center justify-center min-h-[250px] max-h-[75vh] overflow-auto">
                <img id="globalFotoPreviewImg" src="" alt="Foto Mapping" class="max-w-full max-h-[70vh] rounded-lg shadow-md object-contain">
            </div>
        </div>
    </div>

    <script>
        // State tracking URL foto mapping untuk setiap modal
        const currentFotoMappingState = {
            survey: { url: '', name: '', title: '' },
            report: { url: '', name: '', title: '' },
            instalasi: { url: '', name: '', title: '' },
            reportInstalasi: { url: '', name: '', title: '' }
        };

        function setupFotoMappingUI(type, existingUrl, title) {
            const capitalType = type.charAt(0).toUpperCase() + type.slice(1);
            const dropzone = document.getElementById('fotoMapping' + capitalType + 'Dropzone');
            const previewBox = document.getElementById('fotoMapping' + capitalType + 'PreviewBox');
            const img = document.getElementById('fotoMapping' + capitalType + 'Img');
            const fileName = document.getElementById('fotoMapping' + capitalType + 'FileName');
            const badge = document.getElementById('fotoMapping' + capitalType + 'Badge');

            currentFotoMappingState[type] = {
                url: existingUrl || '',
                name: existingUrl ? existingUrl.split('/').pop() : '',
                title: title || 'Preview Foto Mapping'
            };

            if (existingUrl) {
                if (img) img.src = existingUrl;
                if (fileName) fileName.textContent = existingUrl.split('/').pop();
                if (badge) {
                    badge.textContent = 'Foto Tersimpan';
                    badge.className = 'inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 mb-1';
                }
                if (previewBox) previewBox.classList.remove('hidden');
                if (dropzone) dropzone.classList.add('hidden');
            } else {
                if (img) img.src = '';
                if (fileName) fileName.textContent = '';
                if (previewBox) previewBox.classList.add('hidden');
                if (dropzone) dropzone.classList.remove('hidden');
            }
        }

        function handleFotoMappingChange(input, type) {
            const file = input.files && input.files[0];
            if (!file) return;

            const capitalType = type.charAt(0).toUpperCase() + type.slice(1);
            const dropzone = document.getElementById('fotoMapping' + capitalType + 'Dropzone');
            const previewBox = document.getElementById('fotoMapping' + capitalType + 'PreviewBox');
            const img = document.getElementById('fotoMapping' + capitalType + 'Img');
            const fileName = document.getElementById('fotoMapping' + capitalType + 'FileName');
            const badge = document.getElementById('fotoMapping' + capitalType + 'Badge');

            const reader = new FileReader();
            reader.onload = function(e) {
                const url = e.target.result;
                currentFotoMappingState[type] = {
                    url: url,
                    name: file.name,
                    title: 'Preview ' + file.name
                };

                if (img) img.src = url;
                if (fileName) fileName.textContent = file.name;
                if (badge) {
                    badge.textContent = 'Foto Baru';
                    badge.className = 'inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 mb-1';
                }
                if (previewBox) previewBox.classList.remove('hidden');
                if (dropzone) dropzone.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function viewCurrentFotoMapping(type) {
            const item = currentFotoMappingState[type];
            if (item && item.url) {
                openGlobalFotoPreview(item.url, item.title || 'Preview Foto Mapping');
            }
        }

        function openGlobalFotoPreview(url, title) {
            const modal = document.getElementById('modalGlobalFotoPreview');
            const img = document.getElementById('globalFotoPreviewImg');
            const titleEl = document.getElementById('globalFotoPreviewTitle');
            const dlBtn = document.getElementById('globalFotoPreviewDownload');

            if (modal && img) {
                img.src = url;
                if (titleEl) titleEl.textContent = title || 'Preview Foto Mapping';
                if (dlBtn) dlBtn.href = url;
                modal.classList.remove('hidden');
            }
        }

        function closeGlobalFotoPreview() {
            const modal = document.getElementById('modalGlobalFotoPreview');
            const img = document.getElementById('globalFotoPreviewImg');
            if (modal) {
                modal.classList.add('hidden');
                if (img) img.src = '';
            }
        }

        function openSurveyModal(nomorInternet, namaPelanggan, surveyDate, surveyTime, surveyNote, surveyTeamStr, fotoPetaUrl) {
            var form = document.getElementById('formSurvey');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/jadwal-survey';

            document.getElementById('surveyModalTitle').textContent = 'Form Survey An/' + (namaPelanggan || '');
            document.getElementById('surveyDateStart').value = surveyDate || new Date().toISOString().split('T')[0];
            
            var timeSelect = document.getElementById('surveyTime');
            if (timeSelect) {
                timeSelect.value = surveyTime || '';
            }

            document.getElementById('surveyNote').value = surveyNote || '';
            
            // Reset input file
            var fileInput = document.getElementById('fotoMappingInput');
            if (fileInput) fileInput.value = '';
            setupFotoMappingUI('survey', fotoPetaUrl || '', 'Foto Mapping - ' + (namaPelanggan || ''));

            var selectedTeams = surveyTeamStr ? surveyTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.survey-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            document.getElementById('modalSurvey').classList.remove('hidden');
        }

        function closeSurveyModal() {
            document.getElementById('modalSurvey').classList.add('hidden');
        }

        // ── Report Survey Functions (Proses 2) ──
        let globalReportSurveyItems = [];

        function openReportSurveyModal(nomorInternet, namaPelanggan, surveyDateFinish, surveyNoteFinish, surveyTeamStr, fotoPetaUrl) {
            var form = document.getElementById('formReportSurvey');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/report-survey';

            document.getElementById('reportSurveyModalTitle').textContent = 'Report Survey An/' + (namaPelanggan || '');
            document.getElementById('surveyDateFinish').value = surveyDateFinish || new Date().toISOString().split('T')[0];
            document.getElementById('surveyNoteFinish').value = surveyNoteFinish || '';
            
            var fileInput = document.getElementById('fotoMappingUpdateInput');
            if (fileInput) fileInput.value = '';
            setupFotoMappingUI('report', fotoPetaUrl || '', 'Foto Mapping - ' + (namaPelanggan || ''));
            
            var rescheduleCb = document.getElementById('checkRescheduleSurvey');
            if (rescheduleCb) {
                rescheduleCb.checked = false;
                toggleRescheduleSurvey(rescheduleCb);
            }

            var selectedTeams = surveyTeamStr ? surveyTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.report-survey-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalReportSurveyItems = [];
            renderReportSurveyBarangTable();

            document.getElementById('modalReportSurvey').classList.remove('hidden');
        }

        function closeReportSurveyModal() {
            document.getElementById('modalReportSurvey').classList.add('hidden');
        }

        function toggleRescheduleSurvey(cb) {
            var resSec = document.getElementById('sectionRescheduleSurvey');
            var finSec = document.getElementById('sectionSelesaiSurvey');
            if (cb.checked) {
                resSec.classList.remove('hidden');
                finSec.classList.add('hidden');
                document.getElementById('surveyDateFinish').required = false;
                document.getElementById('surveyNoteFinish').required = false;
                document.getElementById('rescheduleDate').required = true;
            } else {
                resSec.classList.add('hidden');
                finSec.classList.remove('hidden');
                document.getElementById('surveyDateFinish').required = true;
                document.getElementById('surveyNoteFinish').required = true;
                document.getElementById('rescheduleDate').required = false;
            }
        }

        function addReportSurveyBarang() {
            var sel = document.getElementById('reportSurveySelectBarang');
            var qtyInput = document.getElementById('reportSurveyQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalReportSurveyItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalReportSurveyItems[existingIndex].jumlah += jumlah;
            } else {
                globalReportSurveyItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderReportSurveyBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeReportSurveyBarang(index) {
            globalReportSurveyItems.splice(index, 1);
            renderReportSurveyBarangTable();
        }

        function renderReportSurveyBarangTable() {
            var tbody = document.getElementById('tableReportSurveyBarang');
            var container = document.getElementById('hiddenReportSurveyBarangContainer');
            if (!tbody || !container) return;

            tbody.innerHTML = '';
            container.innerHTML = '';

            if (globalReportSurveyItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyReportSurveyBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalReportSurveyItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/80 transition-colors';
                tr.innerHTML = 
                    '<td class="py-2 px-3 text-slate-700 font-medium">' + item.nama_barang + '</td>' +
                    '<td class="py-2 px-3 text-center text-slate-700 font-semibold">' + item.jumlah + '</td>' +
                    '<td class="py-2 px-3 text-center">' +
                        '<button type="button" onclick="removeReportSurveyBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-medium text-xs">' +
                            '<i class="fa-solid fa-trash-can"></i>' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'barang[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJumlah = document.createElement('input');
                inputJumlah.type = 'hidden';
                inputJumlah.name = 'barang[' + idx + '][jumlah]';
                inputJumlah.value = item.jumlah;

                container.appendChild(inputKode);
                container.appendChild(inputJumlah);
            });
        }

        // ── Form Instalasi Functions (Proses 3) ──
        let globalInstalasiItems = [];

        function openFormInstalasiModal(nomorInternet, namaPelanggan, noteRequest, instalasiDate, instalasiTime, instalasiNote, instalasiTeamStr, existingItems, fotoPetaUrl) {
            var form = document.getElementById('formInstalasiTeknik');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/jadwal-instalasi';

            document.getElementById('formInstalasiModalTitle').textContent = 'Form Instalasi An/' + (namaPelanggan || '');
            document.getElementById('instalasiNoteRequest').textContent = noteRequest || '-';
            document.getElementById('instalasiDateStart').value = instalasiDate || new Date().toISOString().split('T')[0];
            
            var timeSelect = document.getElementById('instalasiTime');
            if (timeSelect) {
                timeSelect.value = instalasiTime || '';
            }

            document.getElementById('instalasiNote').value = instalasiNote || '';

            var selectedTeams = instalasiTeamStr ? instalasiTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.instalasi-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalInstalasiItems = [];
            if (existingItems && Array.isArray(existingItems) && existingItems.length > 0) {
                existingItems.forEach(function(item) {
                    globalInstalasiItems.push({
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang + (item.tipe_barang ? ' ' + item.tipe_barang : ''),
                        jumlah: parseInt(item.jumlah_barang) || 1
                    });
                });
            }
            renderInstalasiBarangTable();

            var fotoContainer = document.getElementById('fotoMappingInstalasiContainer');
            var fotoEmpty = document.getElementById('fotoMappingInstalasiEmpty');
            var fotoImg = document.getElementById('fotoMappingInstalasiImg');
            var fotoFileName = document.getElementById('fotoMappingInstalasiFileName');

            if (fotoPetaUrl) {
                currentFotoMappingState['instalasi'] = {
                    url: fotoPetaUrl,
                    name: fotoPetaUrl.split('/').pop(),
                    title: 'Foto Mapping Hasil Survey - ' + (namaPelanggan || '')
                };
                if (fotoImg) fotoImg.src = fotoPetaUrl;
                if (fotoFileName) fotoFileName.textContent = fotoPetaUrl.split('/').pop();
                if (fotoContainer) fotoContainer.classList.remove('hidden');
                if (fotoEmpty) fotoEmpty.classList.add('hidden');
            } else {
                currentFotoMappingState['instalasi'] = { url: '', name: '', title: '' };
                if (fotoContainer) fotoContainer.classList.add('hidden');
                if (fotoEmpty) fotoEmpty.classList.remove('hidden');
            }

            document.getElementById('modalFormInstalasi').classList.remove('hidden');
        }

        function closeFormInstalasiModal() {
            document.getElementById('modalFormInstalasi').classList.add('hidden');
        }

        function addInstalasiBarang() {
            var sel = document.getElementById('instalasiSelectBarang');
            var qtyInput = document.getElementById('instalasiQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalInstalasiItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalInstalasiItems[existingIndex].jumlah += jumlah;
            } else {
                globalInstalasiItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderInstalasiBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeInstalasiBarang(index) {
            globalInstalasiItems.splice(index, 1);
            renderInstalasiBarangTable();
        }

        function renderInstalasiBarangTable() {
            var tbody = document.getElementById('tableInstalasiBarang');
            var container = document.getElementById('hiddenInstalasiBarangContainer');
            if (!tbody || !container) return;

            tbody.innerHTML = '';
            container.innerHTML = '';

            if (globalInstalasiItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyInstalasiBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalInstalasiItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/80 transition-colors';
                tr.innerHTML = 
                    '<td class="py-2 px-3 text-slate-700 font-medium">' + item.nama_barang + '</td>' +
                    '<td class="py-2 px-3 text-center text-slate-700 font-semibold">' + item.jumlah + '</td>' +
                    '<td class="py-2 px-3 text-center">' +
                        '<button type="button" onclick="removeInstalasiBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-medium text-xs">' +
                            '<i class="fa-solid fa-trash-can"></i>' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'barang[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJumlah = document.createElement('input');
                inputJumlah.type = 'hidden';
                inputJumlah.name = 'barang[' + idx + '][jumlah]';
                inputJumlah.value = item.jumlah;

                container.appendChild(inputKode);
                container.appendChild(inputJumlah);
            });
        }

        // ── Report Instalasi Functions (Proses 4) ──
        let globalReportInstalasiItems = [];

        function openReportInstalasiModal(nomorInternet, namaPelanggan, instalasiDateFinish, instalasiNoteFinish, instalasiTeamStr, existingItems, fotoPetaUrl) {
            var form = document.getElementById('formReportInstalasi');
            form.action = '/pendaftaran/' + encodeURIComponent(nomorInternet) + '/report-instalasi';

            document.getElementById('reportInstalasiModalTitle').textContent = 'Report Instalasi An/' + (namaPelanggan || '');
            document.getElementById('reportInstalasiDateFinish').value = instalasiDateFinish || new Date().toISOString().split('T')[0];
            document.getElementById('reportInstalasiNoteFinish').value = instalasiNoteFinish || '';
            
            var fileInput = document.getElementById('fotoMappingReportInstalasiInput');
            if (fileInput) fileInput.value = '';
            setupFotoMappingUI('reportInstalasi', fotoPetaUrl || '', 'Foto Mapping - ' + (namaPelanggan || ''));
            
            var rescheduleCb = document.getElementById('checkRescheduleReportInstalasi');
            if (rescheduleCb) {
                rescheduleCb.checked = false;
                toggleRescheduleReportInstalasi(rescheduleCb);
            }

            var selectedTeams = instalasiTeamStr ? instalasiTeamStr.split(',').map(function(s) { return s.trim(); }) : [];
            document.querySelectorAll('.report-instalasi-team-cb').forEach(function(cb) {
                cb.checked = selectedTeams.indexOf(cb.value) !== -1;
            });

            globalReportInstalasiItems = [];
            if (existingItems && Array.isArray(existingItems) && existingItems.length > 0) {
                existingItems.forEach(function(item) {
                    globalReportInstalasiItems.push({
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang + (item.tipe_barang ? ' ' + item.tipe_barang : ''),
                        jumlah: parseInt(item.jumlah_barang) || 1
                    });
                });
            }
            renderReportInstalasiBarangTable();

            document.getElementById('modalReportInstalasi').classList.remove('hidden');
        }

        function closeReportInstalasiModal() {
            document.getElementById('modalReportInstalasi').classList.add('hidden');
        }

        function toggleRescheduleReportInstalasi(cb) {
            var resSec = document.getElementById('sectionRescheduleReportInstalasi');
            var finSec = document.getElementById('sectionSelesaiReportInstalasi');
            if (cb.checked) {
                resSec.classList.remove('hidden');
                finSec.classList.add('hidden');
                document.getElementById('reportInstalasiDateFinish').required = false;
                document.getElementById('reportInstalasiNoteFinish').required = false;
                document.getElementById('reportInstalasiRescheduleDate').required = true;
            } else {
                resSec.classList.add('hidden');
                finSec.classList.remove('hidden');
                document.getElementById('reportInstalasiDateFinish').required = true;
                document.getElementById('reportInstalasiNoteFinish').required = true;
                document.getElementById('reportInstalasiRescheduleDate').required = false;
            }
        }

        function addReportInstalasiBarang() {
            var sel = document.getElementById('reportInstalasiSelectBarang');
            var qtyInput = document.getElementById('reportInstalasiQtyBarang');
            var kodeBarang = sel.value;
            var namaBarang = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-nama') : '';
            var jumlah = parseInt(qtyInput.value) || 1;

            if (!kodeBarang) return;

            var existingIndex = globalReportInstalasiItems.findIndex(function(it) { return it.kode_barang === kodeBarang; });
            if (existingIndex !== -1) {
                globalReportInstalasiItems[existingIndex].jumlah += jumlah;
            } else {
                globalReportInstalasiItems.push({ kode_barang: kodeBarang, nama_barang: namaBarang, jumlah: jumlah });
            }

            renderReportInstalasiBarangTable();
            sel.value = '';
            qtyInput.value = 1;
        }

        function removeReportInstalasiBarang(index) {
            globalReportInstalasiItems.splice(index, 1);
            renderReportInstalasiBarangTable();
        }

        function renderReportInstalasiBarangTable() {
            var tbody = document.getElementById('tableReportInstalasiBarang');
            var container = document.getElementById('hiddenReportInstalasiBarangContainer');
            if (!tbody || !container) return;

            tbody.innerHTML = '';
            container.innerHTML = '';

            if (globalReportInstalasiItems.length === 0) {
                tbody.innerHTML = '<tr id="emptyReportInstalasiBarangRow"><td colspan="3" class="py-4 text-center text-xs text-slate-400">No data available in table</td></tr>';
                return;
            }

            globalReportInstalasiItems.forEach(function(item, idx) {
                var tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/80 transition-colors';
                tr.innerHTML = 
                    '<td class="py-2 px-3 text-slate-700 font-medium">' + item.nama_barang + '</td>' +
                    '<td class="py-2 px-3 text-center text-slate-700 font-semibold">' + item.jumlah + '</td>' +
                    '<td class="py-2 px-3 text-center">' +
                        '<button type="button" onclick="removeReportInstalasiBarang(' + idx + ')" class="text-rose-500 hover:text-rose-700 font-medium text-xs">' +
                            '<i class="fa-solid fa-trash-can"></i>' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(tr);

                var inputKode = document.createElement('input');
                inputKode.type = 'hidden';
                inputKode.name = 'barang[' + idx + '][kode_barang]';
                inputKode.value = item.kode_barang;

                var inputJumlah = document.createElement('input');
                inputJumlah.type = 'hidden';
                inputJumlah.name = 'barang[' + idx + '][jumlah]';
                inputJumlah.value = item.jumlah;

                container.appendChild(inputKode);
                container.appendChild(inputJumlah);
            });
        }
    </script>

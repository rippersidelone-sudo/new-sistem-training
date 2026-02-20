{{-- resources/views/coordinator/batch-management/batch-detail-modal.blade.php --}}
<div x-show="openDetailBatch" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openDetailBatch = false" 
         class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        {{-- Header --}}
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Detail Batch</h2>
                <p class="text-sm opacity-90">Informasi lengkap batch pelatihan</p>
            </div>
            <button @click="openDetailBatch = false" 
                    class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto flex-1">
            {{-- Loading State --}}
            <div x-show="detailLoading" class="flex flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#10AF13] mb-4"></div>
                <p class="text-gray-500 font-medium">Memuat detail batch...</p>
            </div>

            {{-- Error State --}}
            <div x-show="detailError" x-cloak class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto mb-3 text-red-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p class="text-red-500 font-medium">Gagal memuat detail batch</p>
                <button @click="openDetailBatch = false" class="mt-4 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>

            {{-- Content --}}
            <div x-show="!detailLoading && !detailError" x-cloak class="space-y-5">
                
                {{-- SECTION 1: Informasi Batch --}}
                <div>
                    <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                            <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Informasi Batch
                    </h3>

                    <div class="space-y-3">
                        {{-- Title --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Judul Batch</div>
                            <div class="col-span-2 text-sm text-gray-900" x-text="detailData.title"></div>
                        </div>

                        {{-- Code --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Kode Batch</div>
                            <div class="col-span-2 text-sm text-gray-900" x-text="detailData.code"></div>
                        </div>

                        {{-- Category --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Kategori</div>
                            <div class="col-span-2 text-sm text-gray-900" x-text="detailData.category"></div>
                        </div>

                        {{-- Status --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Status</div>
                            <div class="col-span-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full uppercase"
                                      :class="{
                                        'bg-blue-100 text-blue-700': detailData.status === 'Scheduled',
                                        'bg-green-100 text-green-700': detailData.status === 'Ongoing',
                                        'bg-orange-100 text-orange-700': detailData.status === 'Completed'
                                      }"
                                      x-text="detailData.status">
                                </span>
                            </div>
                        </div>

                        {{-- Sessions Count --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Jumlah Hari</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                <span x-text="detailData.sessions_count"></span> Hari
                            </div>
                        </div>

                        {{-- Participants --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Peserta</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                <span x-text="detailData.participants_count"></span> / 
                                <span x-text="detailData.max_quota"></span> peserta
                            </div>
                        </div>

                        {{-- Quota --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-semibold text-gray-700">Min - Max Kuota</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                <span x-text="detailData.min_quota"></span> - 
                                <span x-text="detailData.max_quota"></span> peserta
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-5"></div>

                {{-- SECTION 2: Default Zoom Link --}}
                <div>
                    <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        Default Zoom Link
                    </h3>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <a :href="detailData.zoom_link" 
                               target="_blank"
                               class="flex-1 text-blue-600 hover:text-blue-800 font-medium text-sm break-all"
                               x-text="detailData.zoom_link">
                            </a>
                            <button @click="copyToClipboard(detailData.zoom_link)"
                                    class="flex-shrink-0 px-3 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition text-sm font-medium">
                                Copy
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            Link default untuk semua sesi (kecuali jika ada zoom link khusus)
                        </p>
                    </div>
                </div>

                <div class="border-t pt-5"></div>

                {{-- SECTION 3: Sessions Detail --}}
                <div>
                    <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Jadwal Pelatihan Per Hari
                    </h3>

                    <div class="space-y-4">
                        <template x-for="(session, index) in detailData.sessions" :key="session.id">
                            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                {{-- Session Header --}}
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-900">
                                            Hari ke-<span x-text="session.session_number"></span>
                                            <span x-show="session.title" class="text-gray-600 font-normal text-sm">
                                                - <span x-text="session.title"></span>
                                            </span>
                                        </h4>
                                        <div class="px-3 py-1 bg-[#10AF13]/10 text-[#10AF13] rounded-full text-xs font-semibold">
                                            <span x-text="session.duration_text"></span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1" x-text="session.formatted_date"></p>
                                </div>

                                {{-- Session Info Grid --}}
                                <div class="space-y-3">
                                    {{-- Trainer --}}
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="text-sm font-semibold text-gray-700">Trainer</div>
                                        <div class="col-span-2 text-sm text-gray-900" x-text="session.trainer_name"></div>
                                    </div>

                                    {{-- Time --}}
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="text-sm font-semibold text-gray-700">Waktu</div>
                                        <div class="col-span-2 text-sm text-gray-900">
                                            <span x-text="session.start_time"></span> - <span x-text="session.end_time"></span>
                                        </div>
                                    </div>

                                    {{-- Zoom Link (if different) --}}
                                    <div x-show="session.zoom_link && session.zoom_link !== detailData.zoom_link" class="grid grid-cols-3 gap-4">
                                        <div class="text-sm font-semibold text-gray-700">Zoom Link Khusus</div>
                                        <div class="col-span-2">
                                            <div class="flex items-center gap-2">
                                                <a :href="session.zoom_link" 
                                                   target="_blank"
                                                   class="flex-1 text-blue-600 hover:text-blue-800 font-medium text-sm break-all"
                                                   x-text="session.zoom_link">
                                                </a>
                                                <button @click="copyToClipboard(session.zoom_link)"
                                                        class="flex-shrink-0 px-2 py-1 bg-[#10AF13] text-white rounded text-xs font-medium hover:bg-[#0e8e0f] transition">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    <div x-show="session.notes" class="grid grid-cols-3 gap-4">
                                        <div class="text-sm font-semibold text-gray-700">Catatan</div>
                                        <div class="col-span-2 text-sm text-gray-700" x-text="session.notes"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center flex-shrink-0">
            <button @click="openDetailBatch = false"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                Tutup
            </button>
            <button @click="openDetailBatch = false; editBatch(detailData.id)"
                    class="px-5 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Edit Batch
            </button>
        </div>
    </div>
</div>
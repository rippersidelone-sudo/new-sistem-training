@extends('layouts.trainer')

@section('content')
    <div class="px-2 flex justify-between items-center" x-data="{ openUploadMateri: false }">
        <div>
            <h1 class="text-2xl font-semibold">Upload Materi</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola materi pelatihan untuk peserta</p>
        </div>
        <button @click="openUploadMateri = true" class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transitionn font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                <path d="M7 9l5 -5l5 5" />
                <path d="M12 4l0 12" />
            </svg>
            <span>Upload Materi</span>
        </button>

        <!-- Modal Upload Materi -->
        <div x-show="openUploadMateri" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="openUploadMateri = false" class="bg-white w-full max-w-xl rounded-2xl p-6 relative">
                <button @click="openUploadMateri = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex justify-between items-center mb-4 p-2">
                    <div>
                        <h2 class="text-xl font-semibold">Upload Materi Baru</h2>
                        <p class="text-[#737373]">Upload materi pembelajaran untuk peserta</p>
                    </div>
                </div>
                <form method="POST">
                    <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-4 mx-2 mb-2 pb-7">
                        <div>
                            <label class="text-md font-medium text-gray-700">
                                Batch <span class="text-[#ff0000] text-lg">*</span>
                            </label>
                            <select id="role" name="role_id"
                                class="w-full mt-1 px-3 py-2
                                cursor-pointer border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm" required>
                                <option value="">Pilih batch</option>
                                <option value="pyCode">Python Coder Batch 3</option>
                                <option value="pyGame">Pythone Game Developer Batch 1</option>
                                <option value="webDev">Web Development Fundamentals</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-md font-medium text-gray-700">
                                Judul Materi <span class="text-[#ff0000] text-lg">*</span>
                            </label>
                            <input type="email" name="email"
                                class="w-full mt-1 px-3 py-2
                                border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm" placeholder="Contoh: Modul Python Game Development" required>
                        </div>
                        <div>
                            <label class="text-md font-medium text-gray-700">
                                Tipe materi <span class="text-[#ff0000] text-lg">*</span>
                            </label>
                            <select id="role" name="tipe_materi"
                                class="w-full mt-1 px-3 py-2
                                cursor-pointer border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm">
                                <option value="">Pilih tipe materi</option>
                                <option value="pdf">PDF</option>
                                <option value="video">Video</option>
                                <option value="recording">Recording</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-md font-medium text-gray-700">
                                URL/Link <span class="text-[#ff0000] text-lg">*</span>
                            </label>
                            <input type="text" name="text" class="w-full mt-1px-3 py-2
                                border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm" placeholder="https://..." required>
                            <p class="text-md font-medium text-gray-500 pt-2">Link ke file materi (Google Drive, Dropbox, dll)</p>
                        </div>
                    </div>

                    <hr class="mt-4 ms-2 me-2">

                    <!-- Button -->
                    <div class="mt-3 flex justify-end gap-3 me-2">
                        <button 
                            @click="openUploadMateri = false"
                            class="gap-3 px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button class="flex justify-center items-center gap-3 px-4 py-2 rounded-lg text-white bg-[#10AF13] hover:bg-[#0e8e0f] font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 9l5 -5l5 5" />
                                <path d="M12 4l0 12" />
                            </svg>
                            <p>Upload</p>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Materi',
            'value'=>3,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'PDF',
            'value'=>2,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Video',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Link',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path fill="#0059FF" 
                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/></svg>',
            'color'=>'text-[#0059FF]'
        ])
    </div>

    <!-- Batch 1 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">
                    Python Coder Batch 3
                </h2>
                <p class="text-lg font-medium text-gray-600">
                    TRN-2025-002
                </p>
            </div>
            <div class="flex border px-2 rounded-lg items-center gap-1 font-medium text-sm">
                <p>0</p>
                <p>materi</p>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text text-[#FF4D00]">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 9l1 0" />
                        <path d="M9 13l6 0" />
                        <path d="M9 17l6 0" />
                    </svg>
                    <div>
                        <h3 class="text-md font-medium text-gray-800">
                            Modul Python Game Development
                        </h3>
                        <p class="text-md text-[#737373] flex flex-wrap gap-2 font-medium">
                            <span class="text-[#FF4D00] bg-orange-100 px-3 rounded-lg text-sm flex items-center">
                                PDF
                            </span>
                            <span>15/11/2025</span>
                            <span>oleh Ahmad</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="px-3 py-1 text-sm font-medium rounded-lg border flex justify-center items-center gap-3 hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#000000" 
                            d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                            2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                            3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                            1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/>
                        </svg>
                        <p>Lihat</p>
                    </button>
                    <button class="text-[#ff0000] hover:text-[#E81B1B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch 2 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">
                    Python Game Developer Batch 1
                </h2>
                <p class="text-lg font-medium text-gray-600">
                    TRN-2025-001
                </p>
            </div>
            <div class="flex border px-2 rounded-lg items-center gap-1 font-medium text-sm">
                <p>1</p>
                <p>materi</p>
            </div>
        </div>

        <div class="mt-4 space-y-4">
            <div class="p-5 flex justify-center text-gray-600 font-medium text-lg">
                <h4>
                    Belum ada materi
                </h4>
            </div>
        </div>
    </div>

    <!-- Batch 3 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">
                    Web Development Fundamentals
                </h2>
                <p class="text-lg font-medium text-gray-600">
                    TRN-2025-003
                </p>
            </div>
            <div class="flex border px-2 rounded-lg items-center gap-1 font-medium text-sm">
                <p>2</p>
                <p>materi</p>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text text-[#FF4D00]">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 9l1 0" />
                        <path d="M9 13l6 0" />
                        <path d="M9 17l6 0" />
                    </svg>
                    <div>
                        <h3 class="text-md font-medium text-gray-800">
                            HTML & CSS Basics
                        </h3>
                        <p class="text-md text-[#737373] flex flex-wrap gap-2 font-medium">
                            <span class="text-[#FF4D00] bg-orange-100 px-3 rounded-lg text-sm flex items-center">
                                PDF
                            </span>
                            <span>15/10/2025</span>
                            <span>oleh Ahmad</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="px-3 py-1 text-sm font-medium rounded-lg border flex justify-center items-center gap-3 hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#000000" 
                            d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                            2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                            3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                            1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/>
                        </svg>
                        <p>Lihat</p>
                    </button>
                    <button class="text-[#ff0000] hover:text-[#E81B1B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video text-[#AE00FF]">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                        <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                    </svg>
                    <div>
                        <h3 class="text-md font-medium text-gray-800">
                            Recording Sesi 1
                        </h3>
                        <p class="text-md text-[#737373] flex flex-wrap gap-2 font-medium">
                            <span class="text-[#AE00FF] bg-purple-100 px-3 rounded-lg text-sm flex items-center">
                                Recording
                            </span>
                            <span>20/10/2025</span>
                            <span>oleh Ahmad</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="px-3 py-1 text-sm font-medium rounded-lg border flex justify-center items-center gap-3 hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#000000" 
                            d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                            2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                            3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                            1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/>
                        </svg>
                        <p>Lihat</p>
                    </button>
                    <button class="text-[#ff0000] hover:text-[#E81B1B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
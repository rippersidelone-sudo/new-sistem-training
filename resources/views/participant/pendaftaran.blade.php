@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Pendaftaran Training</h1>
        <p class="text-[#737373] mt-2 font-medium">Daftar batch training yang tersedia</p>
    </div>

    <div class="grid grid-cols-1 border gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
        <!-- Search -->
        <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-[#737373]">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text" name="search"
                class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                placeholder="Cari batch, kategori, atau trainer..." />
        </div>
    </div>

    <!-- Daftar Batch -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2" x-data="{ detailBatch: false, detailBatch1: false }">
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <!-- TOP: Title + Status -->
            <div class="flex justify-between items-start">
                <h1 class="text-black font-medium text-xl">
                    Python Game Developer Batch 1
                </h1>
            </div>

            <!-- Status -->
            <div class="flex items-start mt-2">
                <div class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-[#10AF13]">
                    <p class="uppercase">Ongoing</p>
                </div>
            </div>

            <div class="mt-7">
                <p class="text-md font-medium text-gray-600">Pelatihan pengembangan game menggunakan Python dan Pygame</p>
            </div>

            <!-- BOTTOM: VALUE -->
            <div class="mt-5 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                <p class="text-md font-semibold">
                    Python Game Developer
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                    height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                </svg>
                <p class="text-md font-semibold">
                    15 November 2025
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-9">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M12 12h-3.5" />
                    <path d="M12 7v5" />
                </svg>
                <p class="text-md font-semibold">
                    09:00 - 16:00
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
                <p class="text-md font-semibold">
                    8 / 15 peserta
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chalkboard-teacher">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                    <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001" />
                    <path d="M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                </svg>
                <p class="text-md font-semibold">
                    Ahmad
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 mt-6">
                <div
                    class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-orange-100 border border-orange-300 text-[#FF4D00] gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M12 9v4" />
                        <path d="M12 16v.01" />
                    </svg>
                    <p class="text-md">
                        Memerlukan prerequisite
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-6">
                <!-- Button 1 -->
                <button type="button" @click="detailBatch = true"
                    class="w-full px-4 py-1 border rounded-lg flex flex-col items-center justify-center hover:bg-gray-100 text-md font-semibold text-black">
                    Detail
                </button>

                <!-- Button 2 -->
                <button type="submit"
                    class="w-full px-4 rounded-lg flex flex-col items-center justify-center bg-[#10AF13]/60 text-md font-semibold text-white">
                    Sudah Terdaftar
                </button>
            </div>

            <!-- Modal Detail -->
            <div x-show="detailBatch" x-cloak x-transition id="detailBatch"
                class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                <div @click.outside="detailBatch = false" class="bg-white max-w-xl rounded-2xl shadow-lg p-8 relative max-h-[89vh] overflow-y-auto">

                    <!-- Close Button -->
                    <button @click="detailBatch = false"
                        class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Header -->
                    <h2 class="text-xl font-semibold">Detail Peserta</h2>
                    <p class="text-[#737373] mb-6">Informasi lengkap tentang batch training</p>

                    <!-- Judul Batch -->
                    <h2 class="text-lg font-medium">Python Game Developer Batch 1</h2>
                    <p class="text-[#737373] mb-6 uppercase">TRN-2025-001</p>

                    <!-- Content -->
                    <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Deskripsi</p>
                            <p>Pelatihan pengembangan game menggunakan Python dan Pygame</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Kategori</p>
                            <p>Python Game Developer</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Trainer</p>
                            <p>Ahmad</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Tanggal</p>
                            <p>10 November 2025</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Waktu</p>
                            <p>09:00 - 15:00</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Kapasitas</p>
                            <p>8 / 15 peserta</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Status</p>
                            <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full bg-green-100 text-[#10AF13]">
                                Ongoing
                            </span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Prerequisite</p>
                            <span class="inline-block px-2 py-1 capitalize text-xs font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                                python coder
                            </span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Jadwal Sesi</p>
                            <div class="bg-gray-200 p-2 rounded-lg mt-2">
                                <h4 class="text-black capitalize font-medium">Pengenalan Pygame & Setup Environment</h4>
                                <p class="text-gray-700 font-medium">10/11/2025 • 09:00 - 12:00</p>
                            </div>
                            <div class="bg-gray-200 p-2 rounded-lg mt-2">
                                <h4 class="text-black capitalize font-medium">Membuat game pertama</h4>
                                <p class="text-gray-700 font-medium">10/11/2025 • 13:00 - 15:00</p>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">

                    <div class="flex justify-end gap-3 pt-4 me-2">
                        <button type="button"
                            @click="detailBatch = false"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Tutup
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#10AF13]/60 text-white rounded-lg font-medium">
                            Sudah Terdaftar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <!-- TOP: Title + Status -->
            <div class="flex justify-between items-start">
                <h1 class="text-black font-medium text-xl">
                    Python Coder Batch 3
                </h1>
            </div>

            <!-- Status -->
            <div class="flex items-start mt-2">
                <div class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-[#0059FF]">
                    <p class="uppercase">Scheduled</p>
                </div>
            </div>

            <div class="mt-7">
                <p class="text-md font-medium text-gray-600">Pelatihan dasar pemrograman Python untuk pemula</p>
            </div>

            <div class="mt-5 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                <p class="text-md font-semibold">
                    Python Coder
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                    height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                </svg>
                <p class="text-md font-semibold">
                    15 November 2025
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-9">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M12 12h-3.5" />
                    <path d="M12 7v5" />
                </svg>
                <p class="text-md font-semibold">
                    09:00 - 16:00
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
                <p class="text-md font-semibold">
                    8 / 15 peserta
                </p>
            </div>
            <div class="mt-2 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chalkboard-teacher">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                    <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001" />
                    <path d="M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                </svg>
                <p class="text-md font-semibold">
                    Ahmad
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-6">
                <!-- Button 1 -->
                <button @click="detailBatch1 = true" type="button"
                    class="w-full px-4 py-1 border rounded-lg flex flex-col items-center justify-center hover:bg-gray-100 text-md font-semibold text-black">
                    Detail
                </button>

                <!-- Button 2 -->
                <button type="submit"
                    class="w-full px-4 rounded-lg flex flex-col items-center justify-center bg-[#10AF13] hover:bg-[#0e8e0f] text-md font-semibold text-white">
                    Daftar
                </button>
            </div>

            <!-- Modal Detail -->
            <div x-show="detailBatch1" x-cloak x-transition id="detailBatch1"
                class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                <div @click.outside="detailBatch1 = false" class="bg-white max-w-xl rounded-2xl shadow-lg p-8 relative max-h-[89vh] overflow-y-auto">

                    <!-- Close Button -->
                    <button @click="detailBatch1 = false"
                        class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Header -->
                    <h2 class="text-xl font-semibold">Detail Peserta</h2>
                    <p class="text-[#737373] mb-6">Informasi lengkap tentang batch training</p>

                    <!-- Judul Batch -->
                    <h2 class="text-lg font-medium">Python Coder Batch 3</h2>
                    <p class="text-[#737373] mb-6 uppercase">TRN-2025-002</p>

                    <!-- Content -->
                    <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Deskripsi</p>
                            <p>Pelatihan dasar pemrograman Python untuk pemula</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Kategori</p>
                            <p>Python Coder</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Trainer</p>
                            <p>Ahmad</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Tanggal</p>
                            <p>10 November 2025</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Waktu</p>
                            <p>09:00 - 16:00</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Kapasitas</p>
                            <p>8 / 15 peserta</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Status</p>
                            <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full bg-blue-100 text-[#0059FF]">
                                Scheduled
                            </span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Jadwal Sesi</p>
                            <div class="bg-gray-200 p-2 rounded-lg mt-2">
                                <h4 class="text-black capitalize font-medium">Python Basics & Variables</h4>
                                <p class="text-gray-700 font-medium">15/11/2025 • 09:00 - 12:00</p>
                            </div>
                            <div class="bg-gray-200 p-2 rounded-lg mt-2">
                                <h4 class="text-black capitalize font-medium">Functions & Loops</h4>
                                <p class="text-gray-700 font-medium">15/11/2025 • 13:00 - 16:00</p>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4">

                    <div class="flex justify-end gap-3 pt-4 me-2">
                        <button type="button"
                            @click="detailBatch1 = false"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#10AF13]/60 text-white rounded-lg font-medium">
                            Daftar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

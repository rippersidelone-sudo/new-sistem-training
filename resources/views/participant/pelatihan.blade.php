@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Pelatihan Saya</h1>
        <p class="text-[#737373] mt-2 font-medium">Daftar pelatihan yang Anda ikuti</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2" x-data="{ trainingDetail: false }">
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <!-- TOP: Title + Status -->
            <div class="flex justify-between items-start">
                <h1 class="text-black font-medium text-xl">
                    Python Game Developer Batch 1
                </h1>
            </div>

            <div class="flex justify-between">
                <div class="mt-4 text-gray-600 gap-2 items-center">
                    <h2 class="text-md font-medium">
                        Status Pelatihan
                    </h2>
                    <div class="px-3 py-1 w-fit uppercase rounded-full bg-green-100 text-[#10AF13]">
                        <p class="text-xs font-medium">Ongoing</p>
                    </div>
                </div>
                <div class="mt-4 text-gray-600 gap-2 items-center">
                    <h2 class="text-md font-medium">
                        Status Pendaftaran
                    </h2>
                    <div class="px-3 py-1 w-fit uppercase rounded-full bg-green-100 text-[#10AF13]">
                        <p class="text-xs font-medium">Ongoing</p>
                    </div>
                </div>
            </div>

            <!-- BOTTOM: VALUE -->
            <div class="mt-7 flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
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
                    10 November 2025
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
                    09:00 - 15:00
                </p>
            </div>

            <hr class="border-gray-200 mt-4">

            <div class="flex items-start gap-20 mt-3">
                <div>
                    <h2 class="text-md font-medium text-gray-600">
                        Materi
                    </h2>
                    <p class="text-md font-semibold text-black">
                        1
                    </p>
                </div>
                <div>
                    <h2 class="text-md font-medium text-gray-600">
                        Tugas
                    </h2>
                    <p class="text-md font-semibold text-black">
                        1
                    </p>
                </div>
            </div>

            <div class="mt-4 text-gray-600 gap-2 items-center">
                <h2 class="text-md font-medium">
                    Kehadiran
                </h2>
                <div class="px-3 py-1 w-fit text-xs uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                    Check-In
                </div>
            </div>

            <div class="gap-2 mt-6">
                <button class="w-full px-4 py-1 border rounded-lg flex justify-center items-center gap-3 hover:bg-gray-100"
                    @click="trainingDetail = true">
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                    </svg>
                    <p class="text-md font-semibold text-black">
                        Lihat Detail
                    </p>
                </button>
            </div>
        </div>
        
        <!-- Modal Training Detail -->
        <div x-show="trainingDetail" x-cloak x-transition id="detailBatch"
            class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
            <div @click.outside="trainingDetail = false"
                class="bg-white w-full max-w-xl rounded-2xl shadow-lg p-8 relative">
                <!-- Close Button -->
                <button @click="trainingDetail = false"
                    class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <h2 class="text-xl font-semibold">Detail Pelatihan</h2>
                <p class="text-[#737373] mb-4">Informasi lengkap tentang pelatihan Anda</p>

                <div x-data="{ tab: 'info-pelatihan' }" x-cloak>
                    <div class="flex bg-[#eaeaea] p-1 rounded-2xl mb-5">
                        <button @click="tab = 'info-pelatihan'" :class="tab === 'info-pelatihan' ? 'bg-white' : ''"
                            class="w-full py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                            Info
                        </button>

                        <button @click="tab = 'materi-pelatihan'" :class="tab === 'materi-pelatihan' ? 'bg-white' : ''"
                            class="w-full py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                            Materi
                        </button>

                        <button @click="tab = 'tugas-pelatihan'" :class="tab === 'tugas-pelatihan' ? 'bg-white' : ''"
                            class="w-full py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                            Tugas
                        </button>

                        <button @click="tab = 'jadwal-pelatihan'" :class="tab === 'jadwal-pelatihan' ? 'bg-white' : ''"
                            class="w-full py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                            Jadwal
                        </button>
                    </div>

                    <!-- Info Pelatihan -->
                    <div x-show="tab === 'info-pelatihan'">
                        <!-- Content -->
                        <div class="bg-gray-50 rounded-xl p-6 grid lg:grid-cols-2 gap-y-4 gap-x-10">
                            <div class="col-span-2">
                                <div class="mb-4">
                                    <h2 class="text-black text-xl font-semibold">Python Game Developer Batch 1</h2>
                                    <p class="text-gray-700 text-md font-medium">TRN-2025-001</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-md font-medium">Deskripsi</p>
                                    <p class="text-black text-md font-medium">Pelatihan pengembangan game menggunakan
                                        Python
                                        dan Pygame</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Kategori</p>
                                <p class="text-black text-md font-medium">Python Game Developer</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Trainer</p>
                                <p class="text-black text-md font-medium">Ahmad</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Daftar</p>
                                <p class="text-black text-md font-medium">20 Oktober 2025
                                <p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Status</p>
                                <span
                                    class="inline-block px-4 py-1 text-xs font-medium rounded-full bg-green-100 text-[#10AF13]">
                                    ONGOING
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Status Kehadiran</p>
                                <span
                                    class="inline-block px-4 py-1 text-xs font-medium uppercase rounded-full bg-orange-100 text-[#FF4D00]">
                                    Check_In
                                </span>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-md font-medium">Link Zoom</p>
                                <a href="#"
                                    class="py-1 text-md font-medium text-[#0059FF] hover:underline">https://zoom.us/j/123456789</a>
                            </div>
                        </div>
                    </div>

                    <!-- Materi Pelatihan -->
                    <div x-show="tab === 'materi-pelatihan'">
                        <!-- Content -->
                        <div class="mt-5 space-y-4">
                            <div
                                class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-text text-[#FF4D00]">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
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
                                            <span
                                                class="text-[#FF4D00] bg-orange-100 px-3 rounded-lg text-sm flex items-center">
                                                PDF
                                            </span>
                                            <span>•</span>
                                            <span>15/11/2025</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button class="text-black hover:bg-gray-200 border p-2 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 11l5 5l5 -5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tugas Pelatihan -->
                    <div x-show="tab === 'tugas-pelatihan'">
                        <!-- Content -->
                        <div class="mt-5 space-y-4">
                            <div class="p-4 border rounded-xl hover:bg-gray-50 transition">
                                <div>
                                    <h3 class="text-lg font-medium text-black">
                                        Buat Game Sederhana dengan Pygame
                                    </h3>
                                    <p class="text-md text-gray-700 font-medium mt-2">
                                        Buat sebuah game sederhana menggunakan Pygame. Game harus memiliki minimal: player
                                        control, collision detection, dan scoring system.
                                    </p>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-calendar text-gray-700" width="20"
                                        height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                    <p class="text-md text-gray-700 font-medium">Deadline: 15 November 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Pelatihan -->
                    <div x-show="tab === 'jadwal-pelatihan'">
                        <!-- Content -->
                        <div class="mt-5 space-y-4">
                            <div class="p-4 border rounded-xl hover:bg-gray-50 transition">
                                <h3 class="text-lg font-medium text-black">
                                    Buat Game Sederhana dengan Pygame
                                </h3>
                                <div class="flex gap-2 mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-calendar text-gray-700" width="20"
                                        height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                    <p class="text-md text-gray-700 font-medium">10 November 2025</p>
                                </div>
                                <div class="flex gap-2 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-9">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 12h-3.5" />
                                        <path d="M12 7v5" />
                                    </svg>
                                    <p class="text-md text-gray-700 font-medium">09:00 - 12:00</p>
                                </div>
                                <div class="text-md font-medium text-[#0059FF] mt-2">
                                    <a href="#" class="inline-block hover:underline">Join Zoom Meeting</a>
                                </div>
                            </div>
                            <div class="p-4 border rounded-xl hover:bg-gray-50 transition">
                                <h3 class="text-lg font-medium text-black">
                                    Membuat Game Pertama
                                </h3>
                                <div class="flex gap-2 mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-calendar text-gray-700" width="20"
                                        height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                    <p class="text-md text-gray-700 font-medium">10 November 2025</p>
                                </div>
                                <div class="flex gap-2 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-9">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 12h-3.5" />
                                        <path d="M12 7v5" />
                                    </svg>
                                    <p class="text-md text-gray-700 font-medium">13:00 - 15:00</p>
                                </div>
                                <div class="text-md font-medium text-[#0059FF] mt-2">
                                    <a href="#" class="inline-block hover:underline">Join Zoom Meeting</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

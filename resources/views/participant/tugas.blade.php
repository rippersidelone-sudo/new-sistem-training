@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Submission Tugas</h1>
        <p class="text-[#737373] mt-2 font-medium">Submit dan monitor status tugas Anda</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Tugas',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Submitted',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" />
                <path d="M12 4l0 12" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Accepted',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M9 12l2 2l4 -4" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Pending Review',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Rejected',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color'=>'text-[#ff0000]'
        ])
    </div>

    <!-- Tugas 1 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">
                    Buat Game Sederhana dengan Pygame
                </h2>
                <p class="text-lg font-medium text-gray-700">
                    Python Game Developer Batch 1
                </p>
            </div>
            <div class="flex px-2 rounded-full items-center gap-2 font-medium text-sm py-1 w-fit bg-orange-100 text-[#FF4D00]">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M12 12l3 2" />
                    <path d="M12 7v5" />
                </svg>
                <p>Pending</p>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-lg font-medium text-gray-700">
                Buat sebuah game sederhana menggunakan Pygame. Game harus memiliki minimal: player control, collision detection, dan scoring system.
            </p>
        </div>

        <div class="flex items-center gap-2 font-medium text-md mt-3 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20" height="20" viewBox="0 0 24 24" 
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                <path d="M16 3v4" />
                <path d="M8 3v4" />
                <path d="M4 11h16" />
            </svg>
            <p>Deadline: 15 November 2025</p>
        </div>

        <div class="mt-5 space-y-4">
            <div class="p-4 rounded-xl bg-gray-50">
                <div class="mb-2 flex justify-between">
                    <p class="text-md font-medium text-gray-700">File Submission</p>
                    <button class="text-md font-medium text-[#0059FF] hover:underline">
                        Download
                    </button>
                </div>
                <div>
                    <h3 class="text-md font-medium text-black">
                        Game Snake Sederhana dengan Scoring System
                    </h3>
                    <p class="text-md text-gray-700 font-medium pt-2">
                        Submitted: 12/11/2025
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tugas 2 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">
                    Buat Game Sederhana dengan Pygame
                </h2>
                <p class="text-lg font-medium text-gray-700">
                    Python Game Developer Batch 1
                </p>
            </div>
            {{-- <div class="flex px-2 rounded-full items-center gap-2 font-medium text-sm py-1 w-fit bg-orange-100 text-[#FF4D00]">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M12 12l3 2" />
                    <path d="M12 7v5" />
                </svg>
                <p>Pending</p>
            </div> --}}
        </div>

        <div class="mt-6">
            <p class="text-lg font-medium text-gray-700">
                Buat sebuah game sederhana menggunakan Pygame. Game harus memiliki minimal: player control, collision detection, dan scoring system.
            </p>
        </div>

        <div class="flex items-center gap-2 font-medium text-md mt-3 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20" height="20" viewBox="0 0 24 24" 
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                <path d="M16 3v4" />
                <path d="M8 3v4" />
                <path d="M4 11h16" />
            </svg>
            <p>Deadline: 15 November 2025</p>
        </div>

        <div class="mt-5 space-y-4">
            <button @click="openUploadTugas = true" class="flex items-center justify-center bg-[#0059FF] text-white rounded-lg w-full gap-3 py-2 cursor-pointer hover:bg-blue-700 transition font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 9l5 -5l5 5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Submit Tugas</span>
            </button>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="openUploadTugas" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openUploadTugas = false" class="bg-white w-full max-w-xl rounded-2xl p-6 relative">
            <button @click="openUploadTugas = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
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
                    <h2 class="text-xl font-semibold">Submit Tugas</h2>
                    <p class="text-[#737373]">Upload file tugas dan tambahkan catatan</p>
                </div>
            </div>
            <form method="POST" action="">
                <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-1 mx-2 mb-2 pb-7">
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            File Tugas <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <input type="file" name="name" class="w-full mt-1 border border-gray-300 rounded-md
                            px-3 py-2 file:border-0 file:text-white file:bg-[#10AF13] file:px-3 file:font-medium font-medium
                            file:rounded-full focus:outline-none focus:ring-[#10AF13] focus:ring-1 focus:border-[#10AF13]" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            Catatan <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <textarea class="bg-gray-200 focus:ring-[#10AF13] focus:border-[#10AF13] border-none font-medium rounded-xl w-full resize-none focus:border-double" rows="4" placeholder="Tambahkan catatan untuk submission Anda..."></textarea>
                    </div>
                </div>

                <hr class="mt-4 ms-2 me-2">

                <div class="flex justify-end gap-3 pt-4 me-2">
                    <button type="button"
                        @click="openUploadTugas = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                        Submit Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Absensi Online</h1>
        <p class="text-[#737373] mt-2 font-medium">Check-in kehadiran untuk batch training Anda</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Validated',
            'value' => 0,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]',
        ])
        @include('dashboard.card', [
            'title' => 'Pending Approval',
            'value' => 0,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]',
        ])
        @include('dashboard.card', [
            'title' => 'Belum Check-In',
            'value' => 0,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 9v4" /><path d="M12 16v.01" /></svg>',
            'color' => 'text-gray-700',
        ])
    </div>

    {{-- <div class="grid grid-cols-1 border gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
        <p class="text-lg font-medium text-gray-500 pt-8 flex justify-center">Tidak ada batch yang perlu check-in</p>
    </div> --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <!-- TOP: Title + Status -->
            <div class="flex justify-between items-start">
                <h1 class="text-black font-medium text-xl">
                    Python Game Developer Batch 1
                </h1>
            </div>

            <!-- Sub Title -->
            <div class="flex justify-between">
                <div class="mt-4 text-gray-600 gap-2 items-center">
                    <h2 class="text-md font-medium">
                        Status Pelatihan
                    </h2>
                    <div class="px-3 py-1 w-fit uppercase rounded-full bg-green-100 text-[#10AF13]">
                        <p class="text-xs font-medium">Ongoing</p>
                    </div>
                </div>
                {{-- <div class="mt-4 text-gray-600 gap-2 items-center">
                    <h2 class="text-md font-medium">
                        Status Absensi
                    </h2>
                    <div
                        class="px-3 py-1 w-fit flex items-center gap-2 uppercase rounded-full bg-orange-100 text-[#FF4D00]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12l3 2" />
                            <path d="M12 7v5" />
                        </svg>
                        <p class="text-xs font-medium">Pending</p>
                    </div>
                </div> --}}
            </div>

            <!-- BOTTOM: VALUE -->
            <div class="mt-6 flex gap-2 text-gray-600 items-center">
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

            <div class="gap-2 mt-5">
                <button class="w-full px-4 py-1 rounded-lg flex justify-center items-center gap-3 bg-[#10AF13] hover:bg-[#0e8e0f] text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p class="text-md capitalize font-semibold text-white">
                        Check-in Sekarang
                    </p>
                </button>
            </div>
        </div>
        
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <!-- TOP: Title + Status -->
            <div class="flex justify-between items-start">
                <h1 class="text-black font-medium text-xl">
                    Python Game Developer Batch 1
                </h1>
            </div>

            <!-- Sub Title -->
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
                        Status Absensi
                    </h2>
                    <div
                        class="px-3 py-1 w-fit flex items-center gap-2 uppercase rounded-full bg-orange-100 text-[#FF4D00]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12l3 2" />
                            <path d="M12 7v5" />
                        </svg>
                        <p class="text-xs font-medium">Pending</p>
                    </div>
                </div>
            </div>

            <!-- BOTTOM: VALUE -->
            <div class="mt-6 flex gap-2 text-gray-600 items-center">
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

            <div class="mt-5">
                <div
                    class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-blue-100 border border-blue-300 gap-2">
                    <p class="text-md capitalize text-[#0059FF]">
                        Check-in: 9 Des, 14.59
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <div
                    class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start text-[#FF4D00] bg-orange-100 border border-orange-300 gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M12 12l3 2" />
                        <path d="M12 7v5" />
                    </svg>
                    <p class="text-md">
                        Menunggu Validasi dari Trainer
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

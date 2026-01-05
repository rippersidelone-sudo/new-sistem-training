@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">My Batches</h1>
        <p class="text-[#737373] mt-2 font-medium">Batch pelatihan yang diampu</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Shceduled',
            'value' => 1,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" />
                        <path d="M8 3v4" /><path d="M4 11h16" /></svg>',
            'color' => 'text-[#0059FF]',
        ])
        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => 1,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                    viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]',
        ])
        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => 1,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                        fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                        <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                        <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]',
        ])
    </div>

    <div x-data="{ tab: 'scheduled' }" x-cloak>
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl mt-8 mx-2">
            <button @click="tab = 'scheduled'" :class="tab === 'scheduled' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Scheduled (1)
            </button>

            <button @click="tab = 'ongoing'" :class="tab === 'ongoing' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Ongoing (1)
            </button>

            <button @click="tab = 'completed'" :class="tab === 'completed' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Completed (1)
            </button>
        </div>

        <!-- Scheduled -->
        <div x-show="tab === 'scheduled'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
                    <!-- TOP: Title + Status -->
                    <div class="flex justify-between items-start">
                        <h1 class="text-black font-bold text-xl">
                            Python Coder Batch 3
                        </h1>
                        <div class="px-3 py-1 text-xs font-medium rounded-full uppercase bg-blue-100 text-[#0059FF]">
                            Scheduled
                        </div>
                    </div>

                    <div class="flex items-start mt-1">
                        <h2 class="text-gray-600 font-medium text-base">
                            TRN-2025-002
                        </h2>
                    </div>

                    <div class="px-3 py-1 w-fit mt-4 text-xs font-bold rounded-lg border">
                        Python Coder
                    </div>

                    <!-- BOTTOM: VALUE -->
                    <div class="mt-5 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                            height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                        <p class="text-md font-medium">
                            15/11/2025
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
                        <p class="text-md font-medium">
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
                        <p class="text-md font-medium">
                            0 peserta
                        </p>
                    </div>
                    <div class="mt-2 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4B5563"
                                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75
                                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69
                                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0
                                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z" />
                        </svg>
                        <a href="#" class="text-md font-medium text-[#0059FF] hover:underline">
                            Zoom Link
                        </a>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="flex items-start gap-20">
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Attendance
                            </h2>
                            <p class="text-md font-medium text-black">
                                0/0
                            </p>
                        </div>
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Completed
                            </h2>
                            <p class="text-md font-medium text-black">
                                0/0
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Materi:
                        </h2>
                        <p class="text-md font-medium">
                            1
                        </p>
                    </div>

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Tugas:
                        </h2>
                        <p class="text-md font-medium">
                            1
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ongoing -->
        <div x-show="tab === 'ongoing'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
                    <!-- TOP: Title + Status -->
                    <div class="flex justify-between items-start">
                        <h1 class="text-black font-bold text-xl">
                            Python Game Developer Batch 1
                        </h1>
                        <div class="px-3 py-1 text-xs font-medium rounded-full uppercase bg-green-100 text-[#10AF13]">
                            Ongoing
                        </div>
                    </div>

                    <div class="flex items-start mt-1">
                        <h2 class="text-gray-600 font-medium text-base">
                            TRN-2025-001
                        </h2>
                    </div>

                    <div class="px-3 py-1 w-fit mt-4 text-xs font-bold rounded-lg border">
                        Python Game Developer
                    </div>

                    <!-- BOTTOM: VALUE -->
                    <div class="mt-5 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                            height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                        <p class="text-md font-medium">
                            10/11/2025
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
                        <p class="text-md font-medium">
                            09:00 - 15:00
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
                        <p class="text-md font-medium">
                            2 peserta
                        </p>
                    </div>
                    <div class="mt-2 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4B5563"
                                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75
                                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69
                                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0
                                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z" />
                        </svg>
                        <a href="#" class="text-md font-medium text-[#0059FF] hover:underline">
                            Zoom Link
                        </a>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="flex items-start gap-20">
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Attendance
                            </h2>
                            <p class="text-md font-medium text-black">
                                0/2
                            </p>
                        </div>
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Completed
                            </h2>
                            <p class="text-md font-medium text-black">
                                0/2
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Materi:
                        </h2>
                        <p class="text-md font-medium">
                            1
                        </p>
                    </div>

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Tugas:
                        </h2>
                        <p class="text-md font-medium">
                            1
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div x-show="tab === 'completed'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
                    <!-- TOP: Title + Status -->
                    <div class="flex justify-between items-start">
                        <h1 class="text-black font-bold text-xl">
                            Web Developmemt Fundamentals
                        </h1>
                        <div class="px-3 py-1 text-xs font-medium rounded-full uppercase bg-orange-100 text-[#FF4D00]">
                            Completed
                        </div>
                    </div>

                    <div class="flex items-start mt-1">
                        <h2 class="text-gray-600 font-medium text-base">
                            TRN-2025-003
                        </h2>
                    </div>

                    <div class="px-3 py-1 w-fit mt-4 text-xs font-bold rounded-lg border">
                        Web Development Fundamentals
                    </div>

                    <!-- BOTTOM: VALUE -->
                    <div class="mt-5 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                            height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                        <p class="text-md font-medium">
                            20/11/2025
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
                        <p class="text-md font-medium">
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
                        <p class="text-md font-medium">
                            1 peserta
                        </p>
                    </div>
                    <div class="mt-2 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4B5563"
                                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75
                                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69
                                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0
                                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z" />
                        </svg>
                        <a href="#" class="text-md font-medium text-[#0059FF] hover:underline">
                            Zoom Link
                        </a>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="flex items-start gap-20">
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Attendance
                            </h2>
                            <p class="text-md font-medium text-black">
                                1/1
                            </p>
                        </div>
                        <div class="mt-2">
                            <h2 class="text-md font-medium text-gray-600">
                                Completed
                            </h2>
                            <p class="text-md font-medium text-black">
                                1/1
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 mt-3">

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Materi:
                        </h2>
                        <p class="text-md font-medium">
                            2
                        </p>
                    </div>

                    <div class="mt-2 text-gray-600 flex gap-2 items-center">
                        <h2 class="text-md font-medium">
                            Tugas:
                        </h2>
                        <p class="text-md font-medium">
                            0
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

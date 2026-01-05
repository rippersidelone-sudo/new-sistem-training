@extends('layouts.coordinator')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Monitoring Absensi</h1>
        <p class="text-[#737373] mt-2 font-medium">Monitor kehadiran peserta di setiap batch pelatihan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                // stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                // class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                // <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                // <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Validated',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Check-In',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-x-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Absent',
            'value'=>2,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-x-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color'=>'text-[#ff0000]'
        ])
    </div>

    <!-- Tingkat Kehadiran Keseluruhan -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center gap-2 position-relative w-full mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-check">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M9 14l2 2l4 -4" />
                </svg>
                <h2 class="text-lg font-semibold">
                    Tingkat Kehadiran Keseluruhan
                </h2>
            </div>
            <div class="flex items-center justify-between">
                <h4 class="text-md font-medium text-gray-700">
                    Attendance Rate
                </h4>
                <p class="text-sm font-semibold text-[#FF4D00] px-3 bg-orange-100 rounded-full">
                    31%
                </p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                <div class="bg-black h-2 rounded-full" style="width: 31%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">

        <!-- Dropdown Batch -->
        <div x-data="{ open: false, value: '', label: 'Semua Batch' }" class="relative w-full">
            <button @click="open = !open"
                :class="open
                    ?
                    'border-[#10AF13] ring-1 ring-[#10AF13]' :
                    'border-gray-300'"
                class="w-full px-3 py-2 rounded-lg border cursor-pointer
                flex justify-between items-center text-sm bg-white transition">
                <span x-text="label"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                <!-- Item -->
                <template
                    x-for="item in [
                        { value: '', label: 'Semua Batch' },
                        { value: 'pyGame', label: 'Python Game Developer Batch 1' },
                        { value: 'pyCoder', label: 'Python Coder Batch 3' },
                        { value: 'webDev', label: 'Web Development Fundamentals' }
                    ]"
                    :key="item.value">

                    <div @click="value = item.value; label = item.label; open = false"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">

                        <span x-text="item.label"></span>

                        <!-- Check Icon -->
                        <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Hidden input untuk backend -->
            <input type="hidden" name="batch" :value="value">
        </div>

        <!-- Dropdown Cabang -->
        <div x-data="{ open: false, value: '', label: 'Semua Cabang' }" class="relative w-full">
            <button @click="open = !open"
                :class="open
                    ?
                    'border-[#10AF13] ring-1 ring-[#10AF13]' :
                    'border-gray-300'"
                class="w-full px-3 py-2 rounded-lg border cursor-pointer
                flex justify-between items-center text-sm bg-white transition">
                <span x-text="label"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                <!-- Item -->
                <template
                    x-for="item in [
                        { value: '', label: 'Semua Cabang' },
                        { value: 'jakarta-pusat', label: 'Jakarta Pusat' },
                        { value: 'bandung', label: 'Bandung' },
                        { value: 'surabaya', label: 'Surabaya' }
                    ]"
                    :key="item.value">

                    <div @click="value = item.value; label = item.label; open = false"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">

                        <span x-text="item.label"></span>

                        <!-- Check Icon -->
                        <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Hidden input untuk backend -->
            <input type="hidden" name="cabang" :value="value">
        </div>
    </div>

    <!-- Tingkat Kehadiran per Batch -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
            <div class="mb-5">
                <h2 class="text-lg font-semibold">
                    Tingkat Kehadiran per Batch
                </h2>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-md font-semibold text-black">
                            Python Game Developer Batch 1
                        </h4>
                        <p class="text-md font-medium text-gray-700">2 validated / 1 check-in / 4 total</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#FF4D00] px-3 bg-orange-100 rounded-full">
                            31%
                        </p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-black h-2 rounded-full" style="width: 31%"></div>
                </div>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-md font-semibold text-black">
                            Python Coder Batch 3
                        </h4>
                        <p class="text-md font-medium text-gray-700">0 validated / 0 check-in / 0 total</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#ff0000] px-3 bg-red-100 rounded-full">
                            0%
                        </p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1 mb-4">
                    <div class="bg-black h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-md font-semibold text-black">
                            Web Development Fundamentals
                        </h4>
                        <p class="text-md font-medium text-gray-700">2 validated / 0 check-in / 2 total</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#10AF13] px-3 bg-green-100 rounded-full">
                            100%
                        </p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1 mb-4">
                    <div class="bg-black h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kehadiran -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Detail Kehadiran
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIP</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Status Kehadiran</th>
                            <th class="px-4 py-3 text-center">Check-In Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3">
                                Guru Peserta
                            </td>
                            <td class="px-4 py-3">
                                198501012010011001
                            </td>
                            <td class="px-4 py-3">
                                Jakarta Pusat
                            </td>
                            <td class="px-4 py-3">
                                Python Game Developer Batch 1
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-orange-100 text-[#FF4D00]">
                                    <p>Check-In</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <p>10 Nov, 16.55</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3">
                                Rina Wati
                            </td>
                            <td class="px-4 py-3">
                                199002152012012002
                            </td>
                            <td class="px-4 py-3">
                                Jakarta Pusat
                            </td>
                            <td class="px-4 py-3">
                                Python Game Developer Batch 1
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-green-100 text-[#10AF13]">
                                    <p>Validated</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <p>10 Nov, 16.50</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3">
                                Budi Hartono
                            </td>
                            <td class="px-4 py-3">
                                198708202011011003
                            </td>
                            <td class="px-4 py-3">
                                Jakarta Pusat
                            </td>
                            <td class="px-4 py-3">
                                Python Coder Batch 3
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-gray-200 text-gray-700">
                                    <p>Belum Absen</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <p>-</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3">
                                Dewi Lestari
                            </td>
                            <td class="px-4 py-3">
                                199106152013012005
                            </td>
                            <td class="px-4 py-3">
                                Jakarta Pusat
                            </td>
                            <td class="px-4 py-3">
                                Web Development Fundamentals
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-red-100 text-[#ff0000]">
                                    <p>Absent</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <p>-</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
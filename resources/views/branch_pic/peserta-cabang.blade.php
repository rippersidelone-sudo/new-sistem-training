@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Peserta Cabang</h1>
        <p class="text-[#737373] mt-2 font-medium">Cabang Jakarta Pusat</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>4,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Ongoing',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Completed',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Sertifikat',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color'=>'text-[#D4AF37]'
        ])
    </div>

    <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
        <!-- Search -->
        <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                class="text-[#737373]">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text"
                name="search"
                class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                placeholder="Cari nama, email, NIP, atau batch..." />
        </div>

        <!-- Dropdown Status -->
        <div x-data="{ open: false, value: '', label: 'Semua Status' }" class="relative w-full">
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
                        { value: '', label: 'Semua Status' },
                        { value: 'completed', label: 'Completed' },
                        { value: 'ongoing', label: 'Ongoing' },
                        { value: 'approved', label: 'Approved' },
                        { value: 'registered', label: 'Registered' },
                        { value: 'rejected', label: 'Rejected' }
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
            <input type="hidden" name="statusPeserta" :value="value">
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6 max-h-[440px] overflow-y-auto pr-1">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Daftar Peserta Cabang
                </h2>
            </div>
            <div class="overflow-x-auto" x-data="{ detailPeserta: false }">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIP</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Guru Peserta
                            </td>
                            <td class="px-4 py-3">
                                198501012010011001
                            </td>
                            <td class="px-4 py-3">
                                gurupeserta@gmail.com
                            </td>
                            <td class="px-4 py-3">
                                Python Game Developer Batch 1
                            </td>
                            <td class="px-4 py-3">
                                22/10/2025
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs font-medium uppercase rounded-full gap-2 bg-green-100 text-[#10AF13]">
                                    <p>Ongoing</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="detailPeserta = true">
                                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Rina Wati
                            </td>
                            <td class="px-4 py-3">
                                199002152012012002
                            </td>
                            <td class="px-4 py-3">
                                rinawati@gmail.com
                            </td>
                            <td class="px-4 py-3">
                                Python Game Developer Batch 1
                            </td>
                            <td class="px-4 py-3">
                                22/10/2025
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-blue-100 text-[#0059FF]">
                                    <p>Approved</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button>
                                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Budi Hartono
                            </td>
                            <td class="px-4 py-3">
                                198708202011011003
                            </td>
                            <td class="px-4 py-3">
                                budihartono@gmail.com
                            </td>
                            <td class="px-4 py-3">
                                Python Coder Batch 3
                            </td>
                            <td class="px-4 py-3">
                                28/10/2025
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-gray-200 text-gray-700">
                                    <p>Registered</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-4">
                                    <button>
                                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Dewi Lestari
                            </td>
                            <td class="px-4 py-3">
                                199106152013012005
                            </td>
                            <td class="px-4 py-3">
                                dewilestari@gmail.com
                            </td>
                            <td class="px-4 py-3">
                                Web Development Fundamentals
                            </td>
                            <td class="px-4 py-3">
                                31/10/2025
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-orange-100 text-[#FF4D00]">
                                    <p>Completed</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-4">
                                    <button>
                                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Modal Deyail -->
                <div x-show="detailPeserta" x-cloak x-transition id="detailBatch" class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                    <div @click.outside="detailPeserta = false" class="bg-white max-w-xl rounded-2xl shadow-lg p-8 relative">

                        <!-- Close Button -->
                        <button @click="detailPeserta = false" class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Header -->
                        <h2 class="text-xl font-semibold">Detail Peserta</h2>
                        <p class="text-[#737373] mb-6">Informasi lengkap peserta pelatihan</p>

                        <!-- Content -->
                        <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                            <div>
                                <p class="text-gray-700 text-md font-medium">Nama Lengkap</p>
                                <p>Guru Peserta</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">NIP</p>
                                <p>198501012010011001</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Email</p>
                                <p>gurupeserta@gmail.com</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Cabang</p>
                                <p>Jakarta Pusat</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-md font-medium">Batch Pelatihan</p>
                                <p>Python Game Developer Batch 1</p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Training</p>
                                <p>10 November 2025<span class="text-[#737373]"></span></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Waktu</p>
                                <p>09:00 - 15:00<span class="text-[#737373]"></span></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Pendaftaran</p>
                                <p>22 Oktober 2025<span class="text-[#737373]"></span></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Status</p>
                                <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full bg-green-100 text-[#10AF13]">
                                    Ongoing
                                </span>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-md font-medium">Status</p>
                                <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                                    Check-In
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
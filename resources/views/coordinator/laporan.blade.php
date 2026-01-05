@extends('layouts.coordinator')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Laporan Pelatihan</h1>
            <p class="text-[#737373] mt-2 font-medium">Analisis dan rekap data pelatihan</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="flex items-center border border-[#d1d1d1] rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-gray-200 transitionn font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Export CSV</span>
            </button>
            <button class="flex items-center border bg-black text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-[#1d1d1d] transitionn font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Export PDF</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Batch',
            'value'=>4,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                // <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color'=>'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title'=>'Batch Selesai',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#000000">
                // <g fill="none" stroke="#10AF13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                // <path d="M8 3H2v15h7c1.7 0 3 1.3 3 3V7c0-2.2-1.8-4-4-4Zm8 9l2 2l4-4"/><path d="M22 6V3h-6c-2.2 0-4 1.8-4 4v14c0-1.7 1.3-3 3-3h7v-2.3"/></g></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Peserta Lulus',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-trending-up mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>',
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
        @include('dashboard.card', [
            'title'=>'Avg Attendance',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                // stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-bar-popular">
                // <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                // <path d="M9 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20h14" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
    </div>

    <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">

        <!-- Dropdown Periode -->
        <div x-data="{ open: false, value: '', label: 'Semua Periode' }" class="relative w-full">
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
                        { value: '', label: 'Semua Periode' },
                        { value: 'bulanIni', label: 'Bulan Ini' },
                        { value: 'bulanLalu', label: 'Bulan Lalu' },
                        { value: 'tahunIni', label: 'Tahun Ini' }
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

    <div x-data="{ tab: 'overview' }" x-cloak>
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl mt-8 mx-2">
            <button
                @click="tab = 'overview'"
                :class="tab === 'overview' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Overview
            </button>

            <button
                @click="tab = 'batch'"
                :class="tab === 'batch' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Batch
            </button>

            <button
                @click="tab = 'peserta'"
                :class="tab === 'peserta' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Peserta
            </button>
            
            <button
                @click="tab = 'performa'"
                :class="tab === 'performa' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Performa
            </button>
        </div>

        <!-- Overview -->
        <div x-show="tab === 'overview'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 mt-8 px-2">

                <!-- Status Batch -->
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Status Batch</h2>
                    <!-- WRAPPER HEIGHT -->
                    <div class="flex-1 flex items-center justify-center">
                        <canvas id="statusBatch">
                            <script>
                                new Chart(document.getElementById('statusBatch'), {
                                    type: 'pie',
                                    data: {
                                        labels: ['Scheduled', 'Ongoing', 'Completed'],
                                        datasets: [{
                                            data: [1, 1, 1],
                                            backgroundColor: [
                                                '#3B82F6',
                                                '#10B981',
                                                '#F59E0B'
                                            ],
                                            borderColor: '#ffffff',
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'right'
                                            }
                                        }
                                    }
                                });
                            </script>
                        </canvas>
                    </div>
                </div>

                <!-- Status Peserta -->
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Status Peserta</h2>
                    <!-- WRAPPER HEIGHT -->
                    <div class="flex-1 flex items-center justify-center">
                        <canvas id="statusPeserta">
                            <script>
                                new Chart(document.getElementById('statusPeserta'), {
                                    type: 'pie',
                                    data: {
                                        labels: ['Approved', 'Registered', 'Failed', 'Completed', 'Ongoing'],
                                        datasets: [{
                                            data: [1, 1, 0, 1, 1],
                                            backgroundColor: [
                                                '#3B82F6',
                                                '#374151',
                                                '#EF4444',
                                                '#F59E0B',
                                                '#10B981'
                                            ],
                                            borderColor: '#ffffff',
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'right'
                                            }
                                        }
                                    }
                                });
                            </script>
                        </canvas>
                    </div>
                </div>
            </div>

            <!-- Peserta per Cabang -->
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Peserta per Cabang
                </h2>

                <div class="flex-1">
                    <canvas id="pesertaChart">
                        <script>
                            new Chart(document.getElementById('pesertaChart'), {
                                type: 'bar',
                                data: {
                                    labels: ['JKT-PST', 'BDG', 'SBY'],
                                    datasets: [
                                        {
                                            label: 'Jumlah Peserta',
                                            data: [2, 1, 0],
                                            backgroundColor: '#AD49E1',
                                            barPercentage: 0.6,
                                            categoryPercentage: 0.6
                                        },
                                        {
                                            label: 'Lulus',
                                            data: [0, 1, 0],
                                            backgroundColor: '#F59E0B',
                                            barPercentage: 0.6,
                                            categoryPercentage: 0.6
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',     // <<< menampilkan semua dataset dalam 1 label
                                        intersect: false   // <<< hover tidak harus tepat mengenai bar
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1
                                            },
                                            grid: {
                                                borderDash: [4, 4],
                                                color: '#D1D5DB'
                                            }
                                        }
                                    }
                                }
                            });
                        </script>
                    </canvas>
                </div>
                <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-3 bg-[#AD49E1]"></span>
                        Jumlah Peserta
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-4 h-3 bg-[#F59E0B]"></span>
                        Lulus
                    </div>
                </div>
            </div>
        </div>

        <!-- Batch -->
        <div x-show="tab === 'batch'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4 mt-8 px-2">
                <!-- Distribusi Status Peserta -->
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Status Peserta</h2>

                    <div class="space-y-4 max-h-[440px] overflow-y-auto pr-1">
                        <!-- ITEM 1 -->
                        <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                            <div>
                                <h3 class="text-md font-semibold text-gray-800">
                                    Python Coder Batch 3
                                </h3>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    <span>TRN-2025-002</span>
                                    <span>•</span>
                                    <span>Python Coder</span>
                                </p>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    15/11/2025 - 15/11/2025
                                </p>
                            </div>

                            <div>
                                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-blue-100 text-[#0059FF]">
                                    Scheduled
                                </span>
                                <p class="text-md font-medium text-[#737373] pt-2 text-right">
                                    1 peserta
                                </p>
                            </div>
                        </div>

                        <!-- ITEM 2 -->
                        <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                            <div>
                                <h3 class="text-md font-semibold text-gray-800">
                                    Python Game Developer Batch 1
                                </h3>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    <span>TRN-2025-001</span>
                                    <span>•</span>
                                    <span>Python Game Developer</span>
                                </p>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    10/11/2025 - 10/11/2025
                                </p>
                            </div>

                            <div>
                                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-green-100 text-[#10AF13]">
                                    Ongoing
                                </span>
                                <p class="text-md font-medium text-[#737373] pt-2 text-right">
                                    1 peserta
                                </p>
                            </div>
                        </div>

                        <!-- ITEM 3 -->
                        <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                            <div>
                                <h3 class="text-md font-semibold text-gray-800">
                                    Web Development Fundamentals
                                </h3>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    <span>TRN-2025-003</span>
                                    <span>•</span>
                                    <span>Web Development Fundamentals</span>
                                </p>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    20/10/2025 - 20/10/2025
                                </p>
                            </div>

                            <div>
                                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                                    Completed
                                </span>
                                <p class="text-md font-medium text-[#737373] pt-2 text-right">
                                    1 peserta
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peserta -->
        <div x-show="tab === 'peserta'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-4">Ringkasan Peserta</h2>
                    <div class="grid sm:grid-cols-5 gap-4">
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-gray-700 text-lg font-medium">
                                1
                            </h2>
                            <p class="text-md font-medium text-gray-700">
                                Registered
                            </p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#0059FF] text-lg font-medium">
                                1
                            </h2>
                            <p class="text-md font-medium text-gray-700">
                                Approved
                            </p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#10AF13] text-lg font-medium">
                                1
                            </h2>
                            <p class="text-md font-medium text-gray-700">
                                Ongoing
                            </p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#FF4D00] text-lg font-medium">
                                1
                            </h2>
                            <p class="text-md font-medium text-gray-700">
                                Completed
                            </p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#ff0000] text-lg font-medium">
                                1
                            </h2>
                            <p class="text-md font-medium text-gray-700">
                                Failed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performa -->
        <div x-show="tab === 'performa'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4 mt-8 px-2">

                <!-- Performa per Kategori -->
                <div class="bg-white border rounded-2xl p-6 mx-2">
                    <h2 class="text-lg font-semibold mb-5">
                        Performa per Kategori
                    </h2>

                    <div class="flex-1">
                        <canvas id="kategoriChart">
                            <script>
                                new Chart(document.getElementById('kategoriChart'), {
                                    type: 'bar',
                                    data: {
                                        labels: ['Python Coder', 'Python Game Developer', 'Web Development Fundamentals'],
                                        datasets: [
                                            {
                                                label: 'Batch',
                                                data: [1, 1, 1],
                                                backgroundColor: '#5EABD6',
                                                barPercentage: 0.6,
                                                categoryPercentage: 0.6
                                            },
                                            {
                                                label: 'Lulus',
                                                data: [0, 0, 1],
                                                backgroundColor: '#F59E0B',
                                                barPercentage: 0.6,
                                                categoryPercentage: 0.6
                                            },
                                            {
                                                label: 'Peserta',
                                                data: [1, 1, 1],
                                                backgroundColor: '#AD49E1',
                                                barPercentage: 0.6,
                                                categoryPercentage: 0.6
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        interaction: {
                                            mode: 'index',     // <<< menampilkan semua dataset dalam 1 label
                                            intersect: false   // <<< hover tidak harus tepat mengenai bar
                                        },
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                mode: 'index',
                                                intersect: false
                                            }
                                        },
                                        scales: {
                                            x: {
                                                grid: {
                                                    display: false
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1
                                                },
                                                grid: {
                                                    borderDash: [4, 4],
                                                    color: '#D1D5DB'
                                                }
                                            }
                                        }
                                    }
                                });
                            </script>
                        </canvas>
                    </div>
                    <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-2">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-3 bg-[#5EABD6]"></span>
                            Batch
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="w-4 h-3 bg-[#F59E0B]"></span>
                            Lulus
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-3 bg-[#AD49E1]"></span>
                            Peserta
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold">
                            Completion Rate
                        </h2>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-semibold text-black">
                                    Python Coder
                                </h4>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-black">
                                    0%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-[#10AF13] h-2 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-semibold text-black">
                                    Python Game Developer
                                </h4>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-black">
                                    0%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1 mb-4">
                            <div class="bg-[#10AF13] h-2 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-semibold text-black">
                                    Web Development Fundamentals
                                </h4>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-black">
                                    100%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1 mb-4">
                            <div class="bg-[#10AF13] h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                </div>

                <!-- Highlights -->
                <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold">
                            Highlights
                        </h2>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#FF4D00] bg-orange-100 border border-orange-300">
                            <p class="text-black">Tingkat Kelulusan</p>
                            <h2 class="pt-1">50%</h2>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#AE00FF] bg-purple-100 border border-purple-300">
                            <p class="text-black">Rata-rata Peserta per Batch</p>
                            <h2 class="pt-1">3 Peserta</h2>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#10AF13] bg-green-100 border border-green-300">
                            <p class="text-black">Total Kategori Aktif</p>
                            <h2 class="pt-1">3 Kategori</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
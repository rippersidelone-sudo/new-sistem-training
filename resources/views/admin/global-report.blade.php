@extends('layouts.app')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Global Report</h1>
            <p class="text-[#737373] mt-2 font-medium">Laporan per bulan dan per cabang</p>
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
                <span>Export Bulanan</span>
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
                <span>Export Lengkap</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">

        <!-- Dropdown Bulan -->
        <div x-data="{ open: false, value: '', label: 'Pilih Bulan' }" class="relative w-full">
            <h2 class="text-md font-semibold text-[#737373]">
                Pilih Bulan
            </h2>
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
                        { value: '', label: 'Pilih Bulan' },
                        { value: 'juni', label: 'Juni 2025' },
                        { value: 'agustus', label: 'Agustus 2025' },
                        { value: 'september', label: 'September 2025' },
                        { value: 'oktober', label: 'Oktober 2025' },
                        { value: 'november', label: 'November 2025' }
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
            <input type="hidden" name="bulan" :value="value">
        </div>

        <!-- Dropdown Cabang -->
        <div x-data="{ open: false, value: '', label: 'Semua Cabang' }" class="relative w-full">
            <h2 class="text-md font-semibold text-[#737373]">
                Pilih Cabang
            </h2>
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

    <div x-data="{ tab: 'laporan-bulanan' }" x-cloak>
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl mt-8 mx-2 w-fit">
            <button
                @click="tab = 'laporan-bulanan'"
                :class="tab === 'laporan-bulanan' ? 'bg-white' : ''"
                class="px-4 py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                Laporan Bulanan
            </button>

            <button
                @click="tab = 'laporan-cabang'"
                :class="tab === 'laporan-cabang' ? 'bg-white' : ''"
                class="px-4 py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                Laporan per Cabang
            </button>

            <button
                @click="tab = 'analisis-performa'"
                :class="tab === 'analisis-performa' ? 'bg-white' : ''"
                class="px-4 py-1 rounded-full text-sm font-semibold hover:bg-white transition">
                Analisis Performa
            </button>
        </div>

        <!-- Laporan Bulanan -->
        <div x-show="tab === 'laporan-bulanan'">
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Tren Pelatihan dan Peserta
                </h2>

                <div class="flex-1">
                    <canvas id="trendChart">
                        <script>
                            const trendCtx = document.getElementById('trendChart');
                            new Chart(trendCtx, {
                                type: 'line',
                                data: {
                                    labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov'],
                                    datasets: [
                                        {
                                            label: 'Batch',
                                            data: [5, 8, 6, 10, 7],
                                            borderColor: '#5EABD6',
                                            backgroundColor: '#5EABD6',
                                            tension: 0.4,
                                            pointRadius: 4
                                        },
                                        {
                                            label: 'Lulus',
                                            data: [42, 75, 58, 88, 45],
                                            borderColor: '#F59E0B',
                                            backgroundColor: '#F59E0B',
                                            tension: 0.4,
                                            pointRadius: 4
                                        },
                                        {
                                            label: 'Peserta',
                                            data: [45, 78, 62, 98, 68],
                                            borderColor: '#10B981',
                                            backgroundColor: '#10B981',
                                            tension: 0.4,
                                            pointRadius: 4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false 
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100
                                        }
                                    }
                                }
                            });
                        </script>
                    </canvas>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                @include('dashboard.card1', [
                    'title'=>'Total Batch',
                    'value'=>35,
                    'text'=>'6 bulan terakhir',
                    'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        // <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
                    'color'=>'text-[#5EABD6]'
                ])
                @include('dashboard.card1', [
                    'title'=>'Total Peserta',
                    'value'=>348,
                    'text'=>'6 bulan terakhir',
                    'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                        fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
                    'color'=>'text-[#10AF13]'
                ])
                @include('dashboard.card1', [
                    'title'=>'Tingkat Kelulusan',
                    'value'=>'92.5%',
                    'text'=>'308 dari 348 peserta',
                    'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-trending-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>',
                    'color'=>'text-[#FF4D00]'
                ])
            </div>
        </div>

        <!-- Laporan per Cabang -->
        <div x-show="tab === 'laporan-cabang'">
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Performa per Cabang
                </h2>

                <div class="flex-1">
                    <canvas id="cabangChart">
                        <script>
                            new Chart(document.getElementById('cabangChart'), {
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                @include('dashboard.card2', [
                    'title'=>'JKT-PST',
                    'text'=>'Total Peserta',
                    'value'=>2,
                    'text1'=>'Lulus',
                    'value1'=>0,
                    'text2'=>'Tingkat Kelulusan',
                    'value2'=>'0.0%',
                ])
                @include('dashboard.card2', [
                    'title'=>'BDG',
                    'text'=>'Total Peserta',
                    'value'=>1,
                    'text1'=>'Lulus',
                    'value1'=>1,
                    'text2'=>'Tingkat Kelulusan',
                    'value2'=>'100%',
                ])
                @include('dashboard.card2', [
                    'title'=>'SBY',
                    'text'=>'Total Peserta',
                    'value'=>0,
                    'text1'=>'Lulus',
                    'value1'=>0,
                    'text2'=>'Tingkat Kelulusan',
                    'value2'=>'0.0%',
                ])
            </div>
        </div>

        <!-- Analisis Performa -->
        <div x-show="tab === 'analisis-performa'">
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Analisis performa
                </h2>
                <h4 class="text-lg mb-3 mt-5 text-gray-500">
                    Top Performing Batches
                </h4>
                <div class="space-y-4">
                    <!-- ITEM 1 -->
                    <div class="flex items-center gap-3 px-4 py-2 border rounded-xl hover:bg-gray-50 transition">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-[#0059FF] flex items-center justify-center text-lg font-bold">
                            1
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Web Development Fundamentals
                            </h3>
                            <p class="text-md text-[#737373]">
                                TRN-2025-003
                            </p>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="px-3 py-1 text-md font-medium">
                                18 peserta
                            </p>
                            <p class="px-3 py-1 text-md font-medium text-[#10AF13]">
                                Completion Rate: 95%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
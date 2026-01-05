@extends('layouts.coordinator')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Coordinator</h1>
        <p class="text-[#737373] mt-2 font-medium">Selamat datang, {{ Auth::user()->name }}</p>
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
            'title'=>'Scheduled',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" 
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" />
                <path d="M8 3v4" /><path d="M4 11h16" /></svg>',
            'color'=>'text-[#0059FF]'
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
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Pending Approval',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color'=>'text-gray-700'
        ])
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 px-2">

        <!-- Partisipasi per Batch -->
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Partisipasi per Batch</h2>
            
            <div class="flex-1">
                <canvas id="batchChart">
                    <script>
                        new Chart(document.getElementById('batchChart'), {
                            type: 'bar',
                            data: {
                                labels: ['Scheduled', 'Ongoing', 'Completed'],
                                datasets: [
                                    {
                                        label: 'Jumlah',
                                        data: [1, 1, 1],
                                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                                        barPercentage: 0.6,
                                        categoryPercentage: 0.6
                                    },
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
                    <span class="w-4 h-3 bg-[#3B82F6]"></span>
                        Scheduled
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-3 bg-[#10B981]"></span>
                    Ongoing
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-3 bg-[#F59E0B]"></span>
                    Completed
                </div>
            </div>
        </div>

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
                            <span>0/20 peserta</span>
                        </p>
                    </div>

                    <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-blue-100 text-[#0059FF]">
                        Scheduled
                    </span>
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
                            <span>2/20 peserta</span>
                        </p>
                    </div>

                    <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-green-100 text-[#10AF13]">
                        Ongoing
                    </span>
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
                            <span>1/20 peserta</span>
                        </p>
                    </div>

                    <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                        Completed
                    </span>
                </div>
                
                <!-- ITEM 4 -->
                <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">
                            Web Development Fundamentals
                        </h3>
                        <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                            <span>TRN-2025-003</span>
                            <span>•</span>
                            <span>1/20 peserta</span>
                        </p>
                    </div>

                    <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                        Completed
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2" x-data="{ showAll: false }">
        <div class="space-y-4">
            <div class="flex items-center justify-between rounded-xl">
                <div>
                    <h3 class="text-lg font-semibold text-black">
                        Pendaftaran Menunggu Approval
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Ada 1 pendaftaran yang perlu divalidasi
                    </p>
                </div>
                <button @click="showAll = !showAll" class="px-4 py-2 text-md font-semibold rounded-lg bg-[#0059FF] text-white cursor-pointer hover:bg-blue-700 transition">
                    <span x-show="!showAll">Lihat Semua</span>
                    <span x-show="showAll">Sembunyikan</span>
                </button>
            </div>
            <div class="space-y-4 max-h-[440px] overflow-y-auto pr-1">
                <div x-show="showAll" class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">
                            Budi Hartono
                        </h3>
                        <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                            Python Coder Batch 3
                        </p>
                    </div>
                    <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-gray-200 text-gray-700">
                        Registered
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Laporan Cabang</h1>
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
                                labels: ['Python Developer Batch 1', 'Web Development Fundamentals', 'Python Coder Batch 3'],
                                datasets: [
                                    {
                                        label: 'Completed',
                                        data: [0, 1, 0],
                                        backgroundColor: '#F59E0B',
                                        barPercentage: 0.6,
                                        categoryPercentage: 0.6
                                    },
                                    {
                                        label: 'Ongoing',
                                        data: [1, 0, 0],
                                        backgroundColor: '#10B981',
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
                    <span class="w-4 h-3 bg-[#F59E0B]"></span>
                    Completed
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-3 bg-[#10B981]"></span>
                    Ongoing
                </div>
            </div>
        </div>

        <!-- Distribusi Status Peserta -->
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Distribusi Status Peserta</h2>

            <!-- Pie Chart -->
            <div class="flex-1 flex items-center justify-center">
                <canvas id="statusChart">
                    <script>
                        new Chart(document.getElementById('statusChart'), {
                            type: 'pie',
                            data: {
                                labels: ['Completed', 'Ongoing'],
                                datasets: [{
                                    data: [1, 1],
                                    backgroundColor: [
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

    <!-- Daftar Peserta -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center gap-2 position-relative w-full mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 9l1 0" />
                    <path d="M9 13l6 0" />
                    <path d="M9 17l6 0" />
                </svg>
                <h2 class="text-lg font-semibold">
                    Ringkasan Batch Terbaru
                </h2>
            </div>
            <div class="max-h-[540px] overflow-y-auto pr-1">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Trainer</th>
                            <th class="px-4 py-3">Peserta</th>
                            <th class="px-4 py-3">Completed</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Python Coder Batch 3
                                <p class="text-[#737373]">TRN-2025-002</p>
                            </td>
                            <td class="px-4 py-3">
                                15 Nov 2025
                            </td>
                            <td class="px-4 py-3">
                                Ahmad
                            </td>
                            <td class="px-4 py-3">
                                1
                            </td>
                            <td class="px-4 py-3">
                                0
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs font-medium uppercase rounded-full gap-2 bg-blue-100 text-[#0059FF]">
                                    <p>Scheduled</p>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Python Game Developer Batch 1
                                <p class="text-[#737373]">TRN-2025-001</p>
                            </td>
                            <td class="px-4 py-3">
                                10 Nov 2025
                            </td>
                            <td class="px-4 py-3">
                                Ahmad
                            </td>
                            <td class="px-4 py-3">
                                2
                            </td>
                            <td class="px-4 py-3">
                                0
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-green-100 text-[#10AF13]">
                                    <p>Ongoing</p>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                Web Development Fundamentals
                                <p class="text-[#737373]">TRN-2025-003</p>
                            </td>
                            <td class="px-4 py-3">
                                21 Okt 2025
                            </td>
                            <td class="px-4 py-3">
                                Ahmad
                            </td>
                            <td class="px-4 py-3">
                                1
                            </td>
                            <td class="px-4 py-3">
                                1
                            </td>
                            <td class="px-4 py-3">
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full gap-2 bg-orange-100 text-[#FF4D00]">
                                    <p>Completed</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
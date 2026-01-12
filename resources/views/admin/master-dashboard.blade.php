{{-- resources/views/admin/master-dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Master Dashboard</h1>
        <p class="text-[#737373] mt-2 font-medium">Overview semua batch dan cabang pelatihan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Batch',
            'value'=>$totalBatches,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color'=>'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title'=>'Batch Aktif',
            'value'=>$activeBatches,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#10AF13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7zM6 8h2m-2 4h2m8-4h2m-2 4h2"/></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>$totalParticipants,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Lulus',
            'value'=>$passedParticipants,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-trending-up mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Cabang Aktif',
            'value'=>$activeBranches,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-map-2 mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" /><path d="M9 4v13" /><path d="M15 7v5.5" />
                <path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                <path d="M19 18v.01" /></svg>',
            'color'=>'text-[#64E2B7]'
        ])
        @include('dashboard.card', [
            'title'=>'Sertifikat',
            'value'=>$totalCertificates,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color'=>'text-[#D4AF37]'
        ])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 px-2">
        
        <!-- TREND BULANAN -->
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Tren Bulanan</h2>
            <div class="flex-1 min-h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- STATUS BATCH -->
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Status Batch</h2>
            <div class="flex-1 flex items-center justify-center min-h-[300px]">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-4">
            Distribusi Peserta per Cabang
        </h2>

        <div class="h-[350px]">
            <canvas id="cabangChart"></canvas>
        </div>
        <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-2">
            <div class="flex items-center gap-2">
                <span class="w-4 h-3 bg-[#AD49E1]"></span>
                Jumlah Peserta
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-4">
            Batch Terbaru
        </h2>

        <div class="space-y-4 max-h-[440px] overflow-y-auto pr-1">
            @forelse($recentBatches as $batch)
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex-1">
                    <h3 class="text-md font-semibold text-gray-800">
                        {{ $batch->title }}
                    </h3>
                    <p class="text-md font-medium text-[#737373]">
                        {{ $batch->category?->name ?? '-' }} • 
                        {{ $batch->start_date->format('d/m/Y') }} • 
                        {{ $batch->batch_participants_count }} peserta
                    </p>
                </div>

                <span class="px-3 py-1 text-sm font-medium rounded-full {{ badgeStatus($batch->status) }}">
                    {{ strtoupper($batch->status) }}
                </span>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                    class="text-gray-400 mb-3">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <p class="font-medium">Belum ada batch terdaftar</p>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyTrend['labels']),
                datasets: [
                    {
                        label: 'Batch',
                        data: @json($monthlyTrend['batches']),
                        borderColor: '#5EABD6',
                        backgroundColor: 'rgba(94, 171, 214, 0.1)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#5EABD6',
                        fill: true
                    },
                    {
                        label: 'Peserta',
                        data: @json($monthlyTrend['participants']),
                        borderColor: '#AD49E1',
                        backgroundColor: 'rgba(173, 73, 225, 0.1)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#AD49E1',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Scheduled', 'Ongoing', 'Completed'],
                datasets: [{
                    data: [
                        {{ $batchStatus['Scheduled'] }},
                        {{ $batchStatus['Ongoing'] }},
                        {{ $batchStatus['Completed'] }}
                    ],
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Cabang Chart
        const cabangCtx = document.getElementById('cabangChart').getContext('2d');
        new Chart(cabangCtx, {
            type: 'bar',
            data: {
                labels: @json($participantsPerBranch->pluck('code')),
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: @json($participantsPerBranch->pluck('count')),
                    backgroundColor: '#AD49E1',
                    barPercentage: 0.6,
                    categoryPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return @json($participantsPerBranch->pluck('name'))[index];
                            }
                        }
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
    @endpush
@endsection
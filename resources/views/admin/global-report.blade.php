@extends('layouts.app')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Global Report</h1>
            <p class="text-[#737373] mt-2 font-medium">Laporan per bulan dan per cabang</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.export', ['type' => 'monthly'] + request()->query()) }}"
                class="flex items-center border border-[#d1d1d1] rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-gray-200 transition font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Export Bulanan</span>
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'complete'] + request()->query()) }}"
                class="flex items-center border bg-black text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-[#1d1d1d] transition font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Export Lengkap</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reports.index') }}" id="filterForm">
        <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">

            <!-- Dropdown Bulan -->
            <div x-data="{ 
                open: false, 
                value: '{{ request('month', '') }}', 
                label: '{{ request('month') ? date('F Y', strtotime(request('month') . '-01')) : 'Pilih Bulan' }}' 
            }" class="relative w-full">
                <h2 class="text-md font-semibold text-[#737373] mb-2">
                    Pilih Bulan
                </h2>
                <button type="button" @click="open = !open"
                    :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
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
                    class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">

                    <!-- Clear Filter -->
                    <div @click="value = ''; label = 'Pilih Bulan'; open = false; $refs.monthInput.value = ''; document.getElementById('filterForm').submit();"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                        <span>Semua Bulan</span>
                        <svg x-show="value === ''" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>

                    <!-- Last 12 Months -->
                    @for($i = 0; $i < 12; $i++)
                        @php
                            $date = now()->subMonths($i);
                            $monthValue = $date->format('Y-m');
                            $monthLabel = $date->format('F Y');
                        @endphp
                        <div @click="value = '{{ $monthValue }}'; label = '{{ $monthLabel }}'; open = false; $refs.monthInput.value = value; document.getElementById('filterForm').submit();"
                            class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                            <span>{{ $monthLabel }}</span>
                            <svg x-show="value === '{{ $monthValue }}'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                    @endfor
                </div>

                <input type="hidden" name="month" x-ref="monthInput" :value="value">
            </div>

            <!-- Dropdown Cabang -->
            <div x-data="{ 
                open: false, 
                value: '{{ request('branch_id', '') }}', 
                label: '{{ request('branch_id') ? $branches->firstWhere('id', request('branch_id'))->name ?? 'Semua Cabang' : 'Semua Cabang' }}' 
            }" class="relative w-full">
                <h2 class="text-md font-semibold text-[#737373] mb-2">
                    Pilih Cabang
                </h2>
                <button type="button" @click="open = !open"
                    :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                    <span x-text="label"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M6 9l6 6l6 -6" />
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                    <div @click="value = ''; label = 'Semua Cabang'; open = false; $refs.branchInput.value = ''; document.getElementById('filterForm').submit();"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                        <span>Semua Cabang</span>
                        <svg x-show="value === ''" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>

                    @foreach($branches as $branch)
                    <div @click="value = '{{ $branch->id }}'; label = '{{ $branch->name }}'; open = false; $refs.branchInput.value = value; document.getElementById('filterForm').submit();"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                        <span>{{ $branch->name }}</span>
                        <svg x-show="value === '{{ $branch->id }}'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                    @endforeach
                </div>

                <input type="hidden" name="branch_id" x-ref="branchInput" :value="value">
            </div>
        </div>
    </form>

    <!-- Tabs -->
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
                    Tren Pelatihan dan Peserta (6 Bulan Terakhir)
                </h2>

                <div class="flex-1">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
                @include('dashboard.card1', [
                    'title' => 'Total Batch',
                    'value' => $totalBatches,
                    'text' => '6 bulan terakhir',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
                    'color' => 'text-[#5EABD6]'
                ])
                @include('dashboard.card1', [
                    'title' => 'Total Peserta',
                    'value' => $totalParticipants,
                    'text' => '6 bulan terakhir',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                        fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
                    'color' => 'text-[#10AF13]'
                ])
                @include('dashboard.card1', [
                    'title' => 'Tingkat Kelulusan',
                    'value' => $passRate . '%',
                    'text' => $passedCount . ' dari ' . $totalParticipants . ' peserta',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-trending-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>',
                    'color' => 'text-[#FF4D00]'
                ])
            </div>
        </div>

        <!-- Laporan per Cabang -->
        <div x-show="tab === 'laporan-cabang'">
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Performa per Cabang
                </h2>

                <div class="flex-1" style="height: 300px;">
                    <canvas id="cabangChart"></canvas>
                </div>
                <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-4">
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
                @foreach($branchPerformance as $branch)
                @include('dashboard.card2', [
                    'title' => $branch['name'],
                    'text' => 'Total Peserta',
                    'value' => $branch['participants'],
                    'text1' => 'Lulus',
                    'value1' => $branch['passed'],
                    'text2' => 'Tingkat Kelulusan',
                    'value2' => $branch['pass_rate'] . '%',
                ])
                @endforeach
            </div>
        </div>

        <!-- Analisis Performa -->
        <div x-show="tab === 'analisis-performa'">
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">
                    Analisis Performa
                </h2>
                <h4 class="text-lg mb-3 mt-5 text-gray-500">
                    Top Performing Batches
                </h4>
                
                @if($topBatches->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="font-medium">Belum ada batch yang selesai</p>
                    <p class="text-sm mt-1">Top performing batches akan muncul setelah ada batch yang completed</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($topBatches as $index => $item)
                    <div class="flex items-center gap-3 px-4 py-2 border rounded-xl hover:bg-gray-50 transition">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-[#0059FF] flex items-center justify-center text-lg font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ $item['batch']->title }}
                            </h3>
                            <p class="text-md text-[#737373]">
                                {{ formatBatchCode($item['batch']->id, $item['batch']->created_at->year) }}
                            </p>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="px-3 py-1 text-md font-medium">
                                {{ $item['batch']->participants_count }} peserta
                            </p>
                            <p class="px-3 py-1 text-md font-medium text-[#10AF13]">
                                Completion Rate: {{ $item['completion_rate'] }}%
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Monthly Trend Chart
        const trendCtx = document.getElementById('trendChart');
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
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true
                    },
                    {
                        label: 'Peserta',
                        data: @json($monthlyTrend['participants']),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true
                    },
                    {
                        label: 'Lulus',
                        data: @json($monthlyTrend['passed']),
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false 
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [4, 4],
                            color: '#E5E7EB'
                        },
                        ticks: {
                            padding: 8
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 8
                        }
                    }
                }
            }
        });

        // Branch Performance Chart
        const branchData = @json($branchPerformance->values());
        new Chart(document.getElementById('cabangChart'), {
            type: 'bar',
            data: {
                labels: branchData.map(b => b.name.substring(0, 10)),
                datasets: [
                    {
                        label: 'Jumlah Peserta',
                        data: branchData.map(b => b.participants),
                        backgroundColor: '#AD49E1',
                        barPercentage: 0.6,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Lulus',
                        data: branchData.map(b => b.passed),
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
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 8
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            padding: 8
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
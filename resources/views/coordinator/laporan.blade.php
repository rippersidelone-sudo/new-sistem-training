@extends('layouts.coordinator')

@section('content')
    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-notification type="error">{{ $error }}</x-notification>
        @endforeach
    @endif

    {{-- Header --}}
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Laporan Pelatihan</h1>
            <p class="text-[#737373] mt-2 font-medium">Analisis dan rekap data pelatihan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('coordinator.reports.export', request()->all()) }}" 
                class="flex items-center border border-[#d1d1d1] rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-gray-200 transition font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Batch',
            'value' => $totalBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color' => 'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title' => 'Batch Selesai',
            'value' => $completedBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <g fill="none" stroke="#10AF13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M8 3H2v15h7c1.7 0 3 1.3 3 3V7c0-2.2-1.8-4-4-4Zm8 9l2 2l4-4"/><path d="M22 6V3h-6c-2.2 0-4 1.8-4 4v14c0-1.7 1.3-3 3-3h7v-2.3"/></g></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $totalParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Peserta Lulus',
            'value' => $passedParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" />
                <path d="M14 7l7 0l0 7" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Sertifikat',
            'value' => $certificatesIssued,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color' => 'text-[#D4AF37]'
        ])
        @include('dashboard.card', [
            'title' => 'Avg Attendance',
            'value' => number_format($avgAttendanceRate, 1) . '%',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                <path d="M9 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                <path d="M15 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                <path d="M4 20h14" /></svg>',
            'color' => 'text-[#0059FF]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('coordinator.reports.index')"
            :hideSearch="true"
            :filters="[
                [
                    'name' => 'period',
                    'placeholder' => 'Semua Periode',
                    'options' => [
                        ['value' => 'all', 'label' => 'Semua Periode'],
                        ['value' => 'month', 'label' => 'Bulan Ini'],
                        ['value' => 'year', 'label' => 'Tahun Ini'],
                        ['value' => 'custom', 'label' => 'Custom'],
                    ]
                ],
                [
                    'name' => 'branch_id',
                    'placeholder' => 'Semua Cabang',
                    'options' => array_merge(
                        [['value' => '', 'label' => 'Semua Cabang']],
                        $branches->map(fn($b) => ['value' => $b->id, 'label' => $b->name])->toArray()
                    )
                ]
            ]"
        />
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'overview' }" x-cloak>
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl mt-8 mx-2">
            <button @click="tab = 'overview'"
                :class="tab === 'overview' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Overview
            </button>
            <button @click="tab = 'batch'"
                :class="tab === 'batch' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Batch
            </button>
            <button @click="tab = 'peserta'"
                :class="tab === 'peserta' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Peserta
            </button>
            <button @click="tab = 'performa'"
                :class="tab === 'performa' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Performa
            </button>
        </div>

        {{-- Tab: Overview --}}
        <div x-show="tab === 'overview'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 mt-8 px-2">
                {{-- Status Batch Chart --}}
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Status Batch</h2>
                    <div class="flex-1 flex items-center justify-center" style="min-height: 300px;">
                        <canvas id="statusBatchChart"></canvas>
                    </div>
                </div>

                {{-- Status Peserta Chart --}}
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Status Peserta</h2>
                    <div class="flex-1 flex items-center justify-center" style="min-height: 300px;">
                        <canvas id="statusPesertaChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Peserta per Cabang Chart --}}
            <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
                <h2 class="text-lg font-semibold mb-5">Peserta per Cabang</h2>
                <div class="flex-1" style="height: 300px;">
                    <canvas id="pesertaCabangChart"></canvas>
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

        {{-- Tab: Batch --}}
        <div x-show="tab === 'batch'">
            <div class="grid grid-cols-1 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Daftar Batch</h2>
                    <div class="space-y-4 max-h-[440px] overflow-y-auto pr-1">
                        @forelse($batches as $batch)
                        <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                            <div>
                                <h3 class="text-md font-semibold text-gray-800">{{ $batch['title'] }}</h3>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    <span>{{ $batch['code'] }}</span>
                                    <span>•</span>
                                    <span>{{ $batch['category'] }}</span>
                                </p>
                                <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                    {{ formatDate($batch['start_date']) }} - {{ formatDate($batch['end_date']) }}
                                </p>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full {{ badgeStatus($batch['status']) }}">
                                    {{ $batch['status'] }}
                                </span>
                                <p class="text-md font-medium text-[#737373] pt-2 text-right">
                                    {{ $batch['participants_count'] }} peserta
                                </p>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-8">Tidak ada data batch</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Peserta --}}
        <div x-show="tab === 'peserta'">
            <div class="grid grid-cols-1 gap-4 mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-4">Ringkasan Peserta</h2>
                    <div class="grid sm:grid-cols-5 gap-4">
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-gray-700 text-lg font-medium">
                                {{ $participantStatusData['pending'] }}
                            </h2>
                            <p class="text-md font-medium text-gray-700">Pending</p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#0059FF] text-lg font-medium">
                                {{ $participantStatusData['approved'] }}
                            </h2>
                            <p class="text-md font-medium text-gray-700">Approved</p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#10AF13] text-lg font-medium">
                                {{ $participantStatusData['ongoing'] }}
                            </h2>
                            <p class="text-md font-medium text-gray-700">Ongoing</p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#FF4D00] text-lg font-medium">
                                {{ $participantStatusData['completed'] }}
                            </h2>
                            <p class="text-md font-medium text-gray-700">Completed</p>
                        </div>
                        <div class="bg-white border rounded-2xl p-6 text-center">
                            <h2 class="text-[#ff0000] text-lg font-medium">
                                {{ $participantStatusData['rejected'] }}
                            </h2>
                            <p class="text-md font-medium text-gray-700">Rejected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Performa --}}
        <div x-show="tab === 'performa'">
            <div class="grid grid-cols-1 gap-4 mt-8 px-2">
                {{-- Performa per Kategori Chart --}}
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-5">Performa per Kategori</h2>
                    <div class="flex-1" style="height: 300px;">
                        <canvas id="kategoriChart"></canvas>
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
                {{-- Completion Rate --}}
                <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold">Completion Rate</h2>
                    </div>
                    @foreach($categoryPerformance as $category)
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-semibold text-black">{{ $category['name'] }}</h4>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-black">
                                    {{ number_format($category['completion_rate'], 1) }}%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-[#10AF13] h-2 rounded-full" style="width: {{ $category['completion_rate'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Highlights --}}
                <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold">Highlights</h2>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#FF4D00] bg-orange-100 border border-orange-300">
                            <p class="text-black">Tingkat Kelulusan</p>
                            <h2 class="pt-1">{{ number_format($highlights['pass_rate'], 1) }}%</h2>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#AE00FF] bg-purple-100 border border-purple-300">
                            <p class="text-black">Rata-rata Peserta per Batch</p>
                            <h2 class="pt-1">{{ number_format($highlights['avg_participants_per_batch'], 1) }} Peserta</h2>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="p-4 rounded-xl font-medium text-[#10AF13] bg-green-100 border border-green-300">
                            <p class="text-black">Total Kategori Aktif</p>
                            <h2 class="pt-1">{{ $highlights['active_categories'] }} Kategori</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Status Batch Chart
        new Chart(document.getElementById('statusBatchChart'), {
            type: 'pie',
            data: {
                labels: ['Scheduled', 'Ongoing', 'Completed'],
                datasets: [{
                    data: [
                        {{ $batchStatusData['scheduled'] }},
                        {{ $batchStatusData['ongoing'] }},
                        {{ $batchStatusData['completed'] }}
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
                    legend: { position: 'right' }
                }
            }
        });

        // Status Peserta Chart
        new Chart(document.getElementById('statusPesertaChart'), {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Rejected', 'Completed', 'Ongoing'],
                datasets: [{
                    data: [
                        {{ $participantStatusData['approved'] }},
                        {{ $participantStatusData['pending'] }},
                        {{ $participantStatusData['rejected'] }},
                        {{ $participantStatusData['completed'] }},
                        {{ $participantStatusData['ongoing'] }}
                    ],
                    backgroundColor: ['#3B82F6', '#374151', '#EF4444', '#F59E0B', '#10B981'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Peserta per Cabang Chart
        new Chart(document.getElementById('pesertaCabangChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($participantsPerBranch->pluck('branch_name')) !!},
                datasets: [
                    {
                        label: 'Jumlah Peserta',
                        data: {!! json_encode($participantsPerBranch->pluck('total_participants')) !!},
                        backgroundColor: '#AD49E1',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Lulus',
                        data: {!! json_encode($participantsPerBranch->pluck('passed_participants')) !!},
                        backgroundColor: '#F59E0B',
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [4, 4], color: '#D1D5DB' }
                    }
                }
            }
        });

        // Kategori Chart
        new Chart(document.getElementById('kategoriChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoryPerformance->pluck('name')) !!},
                datasets: [
                    {
                        label: 'Batch',
                        data: {!! json_encode($categoryPerformance->pluck('total_batches')) !!},
                        backgroundColor: '#5EABD6',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Lulus',
                        data: {!! json_encode($categoryPerformance->pluck('passed_participants')) !!},
                        backgroundColor: '#F59E0B',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Peserta',
                        data: {!! json_encode($categoryPerformance->pluck('total_participants')) !!},
                        backgroundColor: '#AD49E1',
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [4, 4], color: '#D1D5DB' }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection
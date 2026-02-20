{{-- resources/views/coordinator/laporan.blade.php --}}
@extends('layouts.coordinator')

@section('content')
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

    {{-- Statistics Cards  --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Batch',
            'value' => $totalBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color' => 'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title' => 'Batch Selesai',
            'value' => $completedBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <g fill="none" stroke="#10AF13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M8 3H2v15h7c1.7 0 3 1.3 3 3V7c0-2.2-1.8-4-4-4Zm8 9l2 2l4-4"/>
                <path d="M22 6V3h-6c-2.2 0-4 1.8-4 4v14c0-1.7 1.3-3 3-3h7v-2.3"/></g></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $totalParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Peserta Lulus',
            'value' => $passedParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 17l6 -6l4 4l8 -8" />
                <path d="M14 7l7 0l0 7" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Avg Attendance',
            'value' => number_format($avgAttendanceRate, 1) . '%',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
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
                        ['value' => 'all',   'label' => 'Semua Periode'],
                        ['value' => 'month', 'label' => 'Bulan Ini'],
                        ['value' => 'year',  'label' => 'Tahun Ini'],
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 px-2">
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
            <div class="bg-white border rounded-2xl p-6 mt-6 mx-2">
                <h2 class="text-lg font-semibold mb-5">Peserta per Cabang</h2>
                @if($participantsPerBranch->count() > 0)
                    <div style="height: 300px;">
                        <canvas id="pesertaCabangChart"></canvas>
                    </div>
                    <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-4 h-3 rounded-sm bg-[#AD49E1]"></span>
                            Jumlah Peserta
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-4 h-3 rounded-sm bg-[#F59E0B]"></span>
                            Lulus
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <p class="font-medium">Tidak ada data cabang</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Batch --}}
        <div x-show="tab === 'batch'">
            <div class="mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold">Daftar Batch</h2>
                        <p class="text-sm text-gray-500">Total: {{ count($batches) }} batch</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                            <thead class="bg-[#F1F1F1]">
                                <tr class="text-left text-sm font-semibold text-gray-700">
                                    <th class="px-4 py-3 w-12 text-center">No</th>
                                    <th class="px-4 py-3">Batch</th>
                                    <th class="px-4 py-3">Kategori</th>
                                    <th class="px-4 py-3">Trainer</th>
                                    <th class="px-4 py-3">Periode</th>
                                    <th class="px-4 py-3 text-center">Peserta</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-sm">
                                @forelse($batches as $i => $batch)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $batch['title'] }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $batch['code'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $batch['category'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $batch['trainer'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <div>{{ formatDate($batch['start_date']) }}</div>
                                            <div class="text-xs text-gray-400">{{ formatDate($batch['end_date']) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $batch['participants_count'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2.5 py-1 text-xs uppercase font-semibold rounded-full {{ badgeStatus($batch['status']) }}">
                                                {{ $batch['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                            <p class="font-medium">Tidak ada data batch</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Peserta --}}
        <div x-show="tab === 'peserta'">
            <div class="mt-8 px-2">
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-5">Ringkasan Status Peserta</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div class="border rounded-2xl p-5 text-center">
                            <p class="text-2xl font-bold text-gray-700">{{ $participantStatusData['pending'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Pending</p>
                        </div>
                        <div class="border rounded-2xl p-5 text-center">
                            <p class="text-2xl font-bold text-[#0059FF]">{{ $participantStatusData['approved'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Approved</p>
                        </div>
                        <div class="border rounded-2xl p-5 text-center">
                            <p class="text-2xl font-bold text-[#10AF13]">{{ $participantStatusData['ongoing'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Ongoing</p>
                        </div>
                        <div class="border rounded-2xl p-5 text-center">
                            <p class="text-2xl font-bold text-[#FF4D00]">{{ $participantStatusData['completed'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Completed</p>
                        </div>
                        <div class="border rounded-2xl p-5 text-center">
                            <p class="text-2xl font-bold text-[#ff0000]">{{ $participantStatusData['rejected'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Rejected</p>
                        </div>
                    </div>
                </div>

                {{-- Peserta per Cabang Detail --}}
                @if($participantsPerBranch->count() > 0)
                <div class="bg-white border rounded-2xl p-6 mt-6">
                    <h2 class="text-lg font-semibold mb-5">Peserta per Cabang</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                            <thead class="bg-[#F1F1F1]">
                                <tr class="text-left text-sm font-semibold text-gray-700">
                                    <th class="px-4 py-3 w-12 text-center">No</th>
                                    <th class="px-4 py-3">Cabang</th>
                                    <th class="px-4 py-3 text-center">Total Peserta</th>
                                    <th class="px-4 py-3 text-center">Lulus</th>
                                    <th class="px-4 py-3 text-center">Tingkat Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-sm">
                                @foreach($participantsPerBranch as $i => $branch)
                                    @php
                                        $rate = $branch->total_participants > 0
                                            ? round(($branch->passed_participants / $branch->total_participants) * 100, 1)
                                            : 0;
                                        $rateClass = $rate >= 80 ? 'text-[#10AF13]' : ($rate >= 50 ? 'text-[#FF4D00]' : 'text-[#ff0000]');
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $branch->branch_name }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $branch->total_participants }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $branch->passed_participants }}</td>
                                        <td class="px-4 py-3 text-center font-semibold {{ $rateClass }}">{{ $rate }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tab: Performa --}}
        <div x-show="tab === 'performa'">
            <div class="mt-8 px-2">
                {{-- Performa per Kategori Chart --}}
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-5">Performa per Kategori</h2>
                    @if($categoryPerformance->count() > 0)
                        <div style="height: 300px;">
                            <canvas id="kategoriChart"></canvas>
                        </div>
                        <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-4 h-3 rounded-sm bg-[#5EABD6]"></span>
                                Batch
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-4 h-3 rounded-sm bg-[#F59E0B]"></span>
                                Lulus
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-4 h-3 rounded-sm bg-[#AD49E1]"></span>
                                Peserta
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <p class="font-medium">Tidak ada data kategori</p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    {{-- Completion Rate --}}
                    <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
                        <h2 class="text-lg font-semibold mb-5">Completion Rate per Kategori</h2>
                        @forelse($categoryPerformance as $category)
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-semibold text-gray-800">{{ $category['name'] }}</h4>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ number_format($category['completion_rate'], 1) }}%
                                    </p>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-[#10AF13] h-2 rounded-full transition-all"
                                         style="width: {{ $category['completion_rate'] }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $category['completed_batches'] }} dari {{ $category['total_batches'] }} batch selesai
                                </p>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                        @endforelse
                    </div>

                    {{-- Highlights --}}
                    <div class="bg-white border rounded-2xl p-6">
                        <h2 class="text-lg font-semibold mb-5">Highlights</h2>
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-orange-50 border border-orange-200">
                                <p class="text-sm text-gray-600">Tingkat Kelulusan</p>
                                <p class="text-2xl font-bold text-[#FF4D00] mt-1">
                                    {{ number_format($highlights['pass_rate'], 1) }}%
                                </p>
                            </div>
                            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200">
                                <p class="text-sm text-gray-600">Rata-rata Peserta per Batch</p>
                                <p class="text-2xl font-bold text-[#AE00FF] mt-1">
                                    {{ number_format($highlights['avg_participants_per_batch'], 1) }}
                                    <span class="text-base font-medium">peserta</span>
                                </p>
                            </div>
                            <div class="p-4 rounded-xl bg-green-50 border border-green-200">
                                <p class="text-sm text-gray-600">Total Kategori Aktif</p>
                                <p class="text-2xl font-bold text-[#10AF13] mt-1">
                                    {{ $highlights['active_categories'] }}
                                    <span class="text-base font-medium">kategori</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Status Batch Chart
            const statusBatchCtx = document.getElementById('statusBatchChart');
            if (statusBatchCtx) {
                new Chart(statusBatchCtx, {
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
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // Status Peserta Chart
            const statusPesertaCtx = document.getElementById('statusPesertaChart');
            if (statusPesertaCtx) {
                new Chart(statusPesertaCtx, {
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
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // Peserta per Cabang Chart
            const pesertaCabangCtx = document.getElementById('pesertaCabangChart');
            if (pesertaCabangCtx) {
                const branchData = @json($participantsPerBranch);
                if (branchData.length > 0) {
                    new Chart(pesertaCabangCtx, {
                        type: 'bar',
                        data: {
                            labels: branchData.map(b => b.branch_name),
                            datasets: [
                                {
                                    label: 'Jumlah Peserta',
                                    data: branchData.map(b => b.total_participants),
                                    backgroundColor: '#AD49E1',
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'Lulus',
                                    data: branchData.map(b => b.passed_participants),
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
                }
            }

            // Kategori Chart
            const kategoriCtx = document.getElementById('kategoriChart');
            if (kategoriCtx) {
                const kategoriData = @json($categoryPerformance);
                if (kategoriData.length > 0) {
                    new Chart(kategoriCtx, {
                        type: 'bar',
                        data: {
                            labels: kategoriData.map(k => k.name),
                            datasets: [
                                {
                                    label: 'Batch',
                                    data: kategoriData.map(k => k.total_batches),
                                    backgroundColor: '#5EABD6',
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'Lulus',
                                    data: kategoriData.map(k => k.passed_participants),
                                    backgroundColor: '#F59E0B',
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'Peserta',
                                    data: kategoriData.map(k => k.total_participants),
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
                }
            }
        });
    </script>
    @endpush
@endsection
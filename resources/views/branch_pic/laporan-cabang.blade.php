{{-- resources/views/branch_pic/laporan-cabang.blade.php --}}
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Laporan Cabang</h1>
        <p class="text-[#737373] mt-2 font-medium">Cabang {{ $branch->name ?? 'Unknown' }}</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $totalParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        
        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => $ongoingCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        
        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => $completedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        
        @include('dashboard.card', [
            'title' => 'Sertifikat',
            'value' => $certificatesCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color' => 'text-[#D4AF37]'
        ])
    </div>

    {{-- Date Filter & Export --}}
    <div class="mt-8 px-2">
        <div class="bg-white border rounded-2xl p-5">
            <form method="GET" action="{{ route('branch_pic.reports.index') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                {{-- Start Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#10AF13] focus:border-[#10AF13]">
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#10AF13] focus:border-[#10AF13]">
                </div>

                {{-- Filter Button --}}
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full h-[42px] flex items-center justify-center gap-2 bg-[#10AF13] text-white rounded-lg px-4 text-sm font-medium hover:bg-[#0e8e0f] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18" />
                            <path d="M7 12h10" />
                            <path d="M10 18h4" />
                        </svg>
                        Filter
                    </button>
                </div>

                {{-- Export Button --}}
                <div class="flex items-end">
                    <a href="{{ route('branch_pic.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="w-full h-[42px] flex items-center justify-center gap-2 border border-[#10AF13] text-[#10AF13] rounded-lg px-4 text-sm font-medium hover:bg-green-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                        Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 px-2">
        {{-- Partisipasi per Batch --}}
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Partisipasi per Batch</h2>
            
            <div class="flex-1 min-h-[300px]">
                <canvas id="batchChart"></canvas>
            </div>
            
            <div class="flex gap-6 text-sm font-medium text-gray-700 justify-center mt-4">
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

        {{-- Distribusi Status Peserta --}}
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Distribusi Status Peserta</h2>

            <div class="flex-1 flex items-center justify-center min-h-[300px]">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Batches Table --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 9l1 0" />
                    <path d="M9 13l6 0" />
                    <path d="M9 17l6 0" />
                </svg>
                <h2 class="text-lg font-semibold">Ringkasan Batch Terbaru</h2>
            </div>
            
            <div class="max-h-[540px] overflow-y-auto pr-1">
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Trainer</th>
                            <th class="px-4 py-3">Peserta</th>
                            <th class="px-4 py-3">Completed</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($recentBatches as $index => $batch)
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                {{ $batch->title }}
                                <p class="text-[#737373]">TRN-{{ $batch->start_date->format('Y') }}-{{ str_pad($batch->id, 3, '0', STR_PAD_LEFT) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                {{ $batch->start_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $batch->trainer->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $batch->total_participants ?? 0 }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $batch->completed_participants ?? 0 }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match($batch->status) {
                                        'Scheduled' => 'bg-blue-100 text-[#0059FF]',
                                        'Ongoing' => 'bg-green-100 text-[#10AF13]',
                                        'Completed' => 'bg-orange-100 text-[#FF4D00]',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <div class="px-2 py-1 w-fit text-xs font-medium uppercase rounded-full {{ $badgeClass }}">
                                    <p>{{ $batch->status }}</p>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="font-medium">Belum ada data batch</p>
                                <p class="text-sm text-gray-400 mt-1">Batch akan muncul setelah peserta dari cabang Anda mendaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination untuk recent batches jika dipaginasi --}}
            @if(isset($recentBatches) && method_exists($recentBatches, 'hasPages') && $recentBatches->hasPages())
                <div class="mt-6">
                    <x-pagination :paginator="$recentBatches" />
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartLabels = @json($chartLabels);
    const chartCompleted = @json($chartCompleted);
    const chartOngoing = @json($chartOngoing);
    const statusDistribution = @json($statusDistribution);

    // Bar Chart - Partisipasi per Batch
    const batchCtx = document.getElementById('batchChart');
    if (batchCtx) {
        new Chart(batchCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Completed',
                        data: chartCompleted,
                        backgroundColor: '#F59E0B',
                        barPercentage: 0.6,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Ongoing',
                        data: chartOngoing,
                        backgroundColor: '#10B981',
                        barPercentage: 0.6,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 45, minRotation: 45 }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [4, 4], color: '#D1D5DB' }
                    }
                }
            }
        });
    }

    // Pie Chart - Distribusi Status
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Ongoing'],
                datasets: [{
                    data: [statusDistribution.completed || 0, statusDistribution.ongoing || 0],
                    backgroundColor: ['#F59E0B', '#10B981'],
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
                        labels: { padding: 20, font: { size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
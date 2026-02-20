{{-- resources/views/branch_pic/dashboard.blade.php --}}
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Branch</h1>
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

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('branch_pic.dashboard')"
            searchPlaceholder="Cari nama atau email peserta..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'ongoing', 'label' => 'Ongoing'],
                        ['value' => 'completed', 'label' => 'Completed'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'registered', 'label' => 'Registered'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Participants Table --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Peserta Cabang</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $participants->total() }} peserta</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Batch Terakhir</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($participants as $index => $p)
                            @php
                                $latestBp = $p->batchParticipants->first();

                                $batchTitle = '-';
                                $registrationDate = '-';

                                $badgeClass = 'bg-gray-200 text-gray-700';
                                $statusText = 'Not Registered';

                                if ($latestBp) {
                                    $batchTitle = $latestBp->batch->title ?? '-';
                                    $registrationDate = optional($latestBp->created_at)->format('d/m/Y') ?? '-';

                                    if (($latestBp->batch->status ?? null) === 'Ongoing' && $latestBp->status === 'Approved') {
                                        $badgeClass = 'bg-green-100 text-[#10AF13]';
                                        $statusText = 'Ongoing';
                                    } elseif (($latestBp->batch->status ?? null) === 'Completed' && $latestBp->status === 'Approved') {
                                        $badgeClass = 'bg-orange-100 text-[#FF4D00]';
                                        $statusText = 'Completed';
                                    } elseif ($latestBp->status === 'Approved') {
                                        $badgeClass = 'bg-blue-100 text-[#0059FF]';
                                        $statusText = 'Approved';
                                    } elseif ($latestBp->status === 'Pending') {
                                        $badgeClass = 'bg-gray-200 text-gray-700';
                                        $statusText = 'Registered';
                                    } elseif ($latestBp->status === 'Rejected') {
                                        $badgeClass = 'bg-red-100 text-red-700';
                                        $statusText = 'Rejected';
                                    } else {
                                        $badgeClass = 'bg-gray-100 text-gray-700';
                                        $statusText = $latestBp->status ?? 'Registered';
                                    }
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 transition text-left">
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $participants->firstItem() + $index }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $p->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $p->email }}
                                </td>

                                <td class="px-4 py-3">
                                    <div>{{ $batchTitle }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $latestBp?->batch?->code ?? '' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $registrationDate }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="px-2 py-1 w-fit text-xs font-medium uppercase rounded-full {{ $badgeClass }}">
                                        <p>{{ $statusText }}</p>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="font-medium">Belum ada peserta</p>
                                    <p class="text-sm text-gray-400 mt-1">
                                        @if(request('search') || request('status'))
                                            Tidak ada peserta yang sesuai dengan filter
                                        @else
                                            Semua peserta cabang akan muncul di sini
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participants->hasPages())
                <div class="mt-6">
                    <x-pagination :paginator="$participants" />
                </div>
            @endif
        </div>
    </div>
@endsection
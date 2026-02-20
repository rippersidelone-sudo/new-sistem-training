{{-- resources/views/coordinator/monitoring-absensi.blade.php --}}
@extends('layouts.coordinator')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Monitoring Absensi</h1>
        <p class="text-[#737373] mt-2 font-medium">Monitor kehadiran peserta di setiap batch pelatihan</p>
    </div>

    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-notification type="error">{{ $error }}</x-notification>
        @endforeach
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $totalParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Validated',
            'value' => $validatedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Check-In',
            'value' => $checkinCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Absent',
            'value' => $absentCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color' => 'text-[#ff0000]'
        ])
    </div>

    {{-- Overall Attendance Rate --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M9 14l2 2l4 -4" />
                </svg>
                <h2 class="text-lg font-semibold">Tingkat Kehadiran Keseluruhan</h2>
            </div>
            @php
                $rateColorClass = $attendanceRate >= 80
                    ? 'text-[#10AF13] bg-green-100'
                    : ($attendanceRate >= 50 ? 'text-[#FF4D00] bg-orange-100' : 'text-[#ff0000] bg-red-100');
            @endphp
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-700">Attendance Rate</h4>
                <p class="text-sm font-semibold {{ $rateColorClass }} px-3 py-1 rounded-full">
                    {{ number_format($attendanceRate, 1) }}%
                </p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-black h-2 rounded-full transition-all" style="width: {{ $attendanceRate }}%"></div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('coordinator.monitoring.attendance')"
            :hideSearch="true"
            :filters="[
                [
                    'name' => 'batch_id',
                    'placeholder' => 'Semua Batch',
                    'options' => array_merge(
                        [['value' => '', 'label' => 'Semua Batch']],
                        $batches->map(fn($b) => ['value' => $b['id'], 'label' => $b['label'] . ' (' . $b['code'] . ')'])->toArray()
                    )
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

    {{-- Attendance Rate per Batch --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6 max-h-[380px] overflow-y-auto">
            <h2 class="text-lg font-semibold mb-5">Tingkat Kehadiran per Batch</h2>
            @forelse($batchAttendance as $batch)
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <h4 class="text-sm font-semibold text-black">{{ $batch['title'] }}</h4>
                            <p class="text-xs text-gray-500">
                                {{ $batch['validated'] }} validated · {{ $batch['checkin'] }} check-in · {{ $batch['total_participants'] }} total
                            </p>
                        </div>
                        <p class="text-sm font-semibold {{ $batch['rate_color'] }} px-3 py-1 rounded-full">
                            {{ number_format($batch['rate'], 1) }}%
                        </p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-black h-2 rounded-full" style="width: {{ $batch['rate'] }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">Tidak ada data batch</p>
            @endforelse
        </div>
    </div>

    {{-- Tabel Detail Kehadiran --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Detail Kehadiran</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $attendances->total() }} data</p>
                </div>
                <a href="{{ route('coordinator.monitoring.attendance.export', request()->all()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                    </svg>
                    Export CSV
                </a>
            </div>

            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Cabang</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Check-In Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($attendances as $i => $attendance)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">{{ $attendances->firstItem() + $i }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $attendance['user_name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $attendance['branch_name'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">{{ $attendance['batch_title'] }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $attendance['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $attendance['status_class'] }}">
                                            {{ $attendance['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $attendance['checkin_time'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3">
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 14l2 2l4 -4" />
                    </svg>
                    <p class="font-medium">Tidak ada data kehadiran</p>
                    <p class="text-sm mt-1">Pilih batch atau cabang untuk melihat data</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel Peserta Belum Absen --}}
    @if($notAttendedParticipants->count() > 0)
    <div class="grid gap-6 mt-6 px-2 mb-8">
        <div class="bg-white border rounded-2xl p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold">Peserta Belum Absen</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $notAttendedParticipants->count() }} peserta belum melakukan check-in
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @foreach($notAttendedParticipants as $i => $participant)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $participant['user_name'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $participant['branch_name'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $participant['batch_title'] }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $participant['batch_code'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $participant['status_class'] }}">
                                        {{ $participant['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

@endsection
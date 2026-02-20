{{-- resources/views/trainer/approval-kehadiran.blade.php --}}
@extends('layouts.trainer')

@section('content')
<div x-data="{
    openManualCheckin: false,
    selectedUser: null,
    openAbsentModal: false,
    selectedAttendance: null
}" x-cloak>

    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Approval Kehadiran</h1>
            <p class="text-[#737373] mt-2 font-medium">Validasi check-in kehadiran peserta</p>
        </div>

        @if($stats['pending'] > 0)
            <form action="{{ route('trainer.attendance.approve-all') }}" method="POST">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batchId }}">
                <button type="submit"
                    class="flex items-center bg-[#10AF13] text-white rounded-lg px-3 gap-3 py-2 cursor-pointer hover:bg-[#0e8e0f] transition font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <span>Validasi Semua ({{ $stats['pending'] }})</span>
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Pending Approval',
            'value' => $stats['pending'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Validated',
            'value' => $stats['validated'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Absent',
            'value' => $stats['absent'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color' => 'text-red-600'
        ])
    </div>

    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('trainer.approval-kehadiran')"
            searchPlaceholder="Cari nama peserta..."
            :hideSearch="true"
            :filters="[
                [
                    'name'        => 'batch_id',
                    'placeholder' => 'Semua Batch',
                    'options'     => $batches->map(fn($b) => [
                        'value' => $b['id'],
                        'label' => $b['label']
                    ])->prepend(['value' => '', 'label' => 'Semua Batch'])->toArray()
                ],
                [
                    'name'        => 'branch_id',
                    'placeholder' => 'Semua Cabang',
                    'options'     => $branches->map(fn($b) => [
                        'value' => $b['id'],
                        'label' => $b['label']
                    ])->prepend(['value' => '', 'label' => 'Semua Cabang'])->toArray()
                ]
            ]"
        />
    </div>

    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Kehadiran</h2>
                    @if($attendancesData && $attendancesData->count() > 0)
                        <p class="text-sm text-gray-500 mt-1">Total: {{ $attendancesData->count() }} peserta</p>
                    @endif
                </div>
            </div>

            @if($attendancesData && $attendancesData->count() > 0)
                @php
                    $perPage     = 10;
                    $page        = request()->get('page', 1);
                    $paginated   = $attendancesData->forPage($page, $perPage);
                    $offset      = ($page - 1) * $perPage;
                    $totalItems  = $attendancesData->count();
                    $totalPages  = ceil($totalItems / $perPage);
                    $currentPage = (int) $page;
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Peserta</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Cabang</th>
                                <th class="px-4 py-3">Check-In</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($paginated as $attendance)
                                @php
                                    $statusConfig = [
                                        'Checked-in'    => ['label' => 'Pending',        'class' => 'bg-orange-100 text-[#FF4D00]'],
                                        'Approved'      => ['label' => 'Validated',      'class' => 'bg-green-100 text-[#10AF13]'],
                                        'Not Checked-in'=> ['label' => 'Belum Check-In', 'class' => 'bg-gray-100 text-gray-600'],
                                        'Absent'        => ['label' => 'Absent',         'class' => 'bg-red-100 text-red-600'],
                                    ];
                                    $config = $statusConfig[$attendance['status']] ?? $statusConfig['Not Checked-in'];
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">{{ $offset + $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $attendance['user_name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $attendance['user_email'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $attendance['batch_title'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $attendance['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $attendance['branch_name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $attendance['checkin_time'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $config['class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            @if($attendance['status'] === 'Checked-in')
                                                <form action="{{ route('trainer.attendance.approve', $attendance['id']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#10AF13] bg-green-50 hover:bg-green-100 rounded-lg transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M5 12l5 5l10 -10" />
                                                        </svg>
                                                        Validasi
                                                    </button>
                                                </form>
                                            @elseif($attendance['status'] === 'Not Checked-in')
                                                <button @click="openManualCheckin = true; selectedUser = {
                                                        id: {{ $attendance['user_id'] }},
                                                        name: '{{ addslashes($attendance['user_name']) }}',
                                                        batch_id: {{ $attendance['batch_id'] }}
                                                    }"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#10AF13] bg-green-50 hover:bg-green-100 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 12l5 5l10 -10" />
                                                    </svg>
                                                    Manual
                                                </button>
                                            @else
                                                <span class="inline-flex px-3 py-1.5 text-xs text-gray-300">—</span>
                                            @endif

                                            @if(in_array($attendance['status'], ['Checked-in', 'Not Checked-in']))
                                                <button @click="openAbsentModal = true; selectedAttendance = {
                                                        id: {{ $attendance['id'] ?? 'null' }},
                                                        user_id: {{ $attendance['user_id'] }},
                                                        batch_id: {{ $attendance['batch_id'] }},
                                                        name: '{{ addslashes($attendance['user_name']) }}'
                                                    }"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                                                    </svg>
                                                    Absent
                                                </button>
                                            @else
                                                <span class="inline-flex px-3 py-1.5 text-xs text-gray-300">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($totalPages > 1)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 mt-4 border-t">
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-semibold text-gray-800">{{ $offset + 1 }}</span>–<span class="font-semibold text-gray-800">{{ min($offset + $perPage, $totalItems) }}</span>
                            dari <span class="font-semibold text-gray-800">{{ $totalItems }}</span> peserta
                        </p>
                        <div class="flex items-center gap-2">
                            @if($currentPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                                </span>
                            @endif

                            @php $sp = max($currentPage-2,1); $ep = min($sp+4,$totalPages); $sp = max($ep-4,1); @endphp
                            @if($sp > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">1</a>
                                @if($sp > 2)<span class="text-gray-400 text-sm">...</span>@endif
                            @endif
                            @for($i = $sp; $i <= $ep; $i++)
                                @if($i == $currentPage)
                                    <span class="px-4 py-2 text-sm font-semibold text-white bg-[#10AF13] rounded-lg">{{ $i }}</span>
                                @else
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">{{ $i }}</a>
                                @endif
                            @endfor
                            @if($ep < $totalPages)
                                @if($ep < $totalPages - 1)<span class="text-gray-400 text-sm">...</span>@endif
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">{{ $totalPages }}</a>
                            @endif

                            @if($currentPage < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 14l2 2l4 -4" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada data kehadiran</p>
                    <p class="text-sm mt-1">Pilih batch untuk melihat data kehadiran</p>
                </div>
            @endif
        </div>
    </div>

    <div x-show="openManualCheckin"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
        <div @click.outside="openManualCheckin = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Manual Check-In</h2>
                    <p class="text-sm opacity-90">Check-in manual untuk peserta yang berhalangan</p>
                </div>
                <button @click="openManualCheckin = false" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('trainer.attendance.manual-checkin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batch_id" x-bind:value="selectedUser?.batch_id">
                    <input type="hidden" name="user_id" x-bind:value="selectedUser?.id">
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-xs text-gray-500 mb-1">Peserta</p>
                        <p class="font-semibold text-gray-900" x-text="selectedUser?.name"></p>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-semibold text-gray-700">Notes <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea name="notes" rows="3"
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 text-sm"
                            placeholder="Alasan check-in manual..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="openManualCheckin = false"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium text-sm transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium text-sm transition">Check-In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="openAbsentModal"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
        <div @click.outside="openAbsentModal = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-7 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                    stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9" />
                    <path d="M10 10l4 4m0 -4l-4 4" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tandai Tidak Hadir?</h3>
            <p class="text-sm text-gray-500 mb-1">Anda akan menandai</p>
            <p class="font-semibold text-gray-800 mb-5" x-text="selectedAttendance?.name"></p>
            <form action="{{ route('trainer.attendance.reject') }}" method="POST">
                @csrf
                <input type="hidden" name="attendance_id" x-bind:value="selectedAttendance?.id">
                <input type="hidden" name="user_id" x-bind:value="selectedAttendance?.user_id">
                <input type="hidden" name="batch_id" x-bind:value="selectedAttendance?.batch_id">
                <div class="mb-4 text-left">
                    <label class="text-sm font-semibold text-gray-700">Alasan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea name="reason" rows="2"
                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] text-sm"
                        placeholder="Alasan tidak hadir..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="openAbsentModal = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm shadow-lg shadow-red-600/30">Ya, Absent</button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
{{-- resources/views/trainer/approval-kehadiran.blade.php --}}
@extends('layouts.trainer')

@section('content')
    <!-- Wrap SEMUA konten dalam satu x-data untuk sharing state -->
    <div x-data="{ 
        openManualCheckin: false, 
        selectedUser: null,
        openAbsentModal: false,
        selectedAttendance: null
    }">
        
        <div class="px-2 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold">Approval Kehadiran</h1>
                <p class="text-[#737373] mt-2 font-medium">Validasi check-in kehadiran peserta</p>
            </div>
            
            @if($stats['pending'] > 0)
                <form action="{{ route('trainer.attendance.approve-all') }}" method="POST" 
                      onsubmit="return confirm('Validasi semua {{ $stats['pending'] }} kehadiran yang pending?')">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batchId }}">
                    <button type="submit" class="flex items-center bg-[#10AF13] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-[#0e8e0f] transition font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M9 12l2 2l4 -4" />
                        </svg>
                        <span>Validasi Semua ({{ $stats['pending'] }})</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
            @include('dashboard.card', [
                'title' => 'Pending Approval',
                'value' => $stats['pending'] ?? 0,
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
                'color' => 'text-[#FF4D00]'
            ])
            
            @include('dashboard.card', [
                'title' => 'Validated',
                'value' => $stats['validated'] ?? 0,
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
                'color' => 'text-[#10AF13]'
            ])
            
            @include('dashboard.card', [
                'title' => 'Absent',
                'value' => $stats['absent'] ?? 0,
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-x-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
                'color' => 'text-[#ff0000]'
            ])
        </div>

        {{-- Filter Bar --}}
        <div class="mt-8 mx-2">
            <x-filter-bar 
                :action="route('trainer.approval-kehadiran')"
                searchPlaceholder="Cari nama peserta..."
                :hideSearch="true"
                :filters="[
                    [
                        'name' => 'batch_id',
                        'placeholder' => 'Semua Batch',
                        'options' => $batches->map(fn($b) => [
                            'value' => $b['id'],
                            'label' => $b['label']
                        ])->prepend(['value' => '', 'label' => 'Semua Batch'])->toArray()
                    ],
                    [
                        'name' => 'branch_id',
                        'placeholder' => 'Semua Cabang',
                        'options' => $branches->map(fn($b) => [
                            'value' => $b['id'],
                            'label' => $b['label']
                        ])->prepend(['value' => '', 'label' => 'Semua Cabang'])->toArray()
                    ]
                ]"
            />
        </div>

        {{-- Daftar Kehadiran --}}
        <div class="grid gap-6 mt-8 px-2">
            <div class="bg-white border rounded-2xl p-6">
                <div class="flex items-center justify-between position-relative w-full mb-5">
                    <h2 class="text-lg font-semibold">Daftar Kehadiran</h2>
                </div>
                
                @if($attendancesData && $attendancesData->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full rounded-xl overflow-hidden">
                            <thead class="border-b">
                                <tr class="text-left text-sm font-semibold text-gray-700">
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Batch</th>
                                    <th class="px-4 py-3">Cabang</th>
                                    <th class="px-4 py-3">Check-In Time</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($attendancesData as $attendance)
                                    <tr class="hover:bg-gray-50 transition text-left">
                                        <td class="px-4 py-3">{{ $attendance['user_name'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $attendance['user_email'] }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $attendance['batch_title'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $attendance['batch_code'] }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $attendance['branch_name'] }}</td>
                                        <td class="px-4 py-3">{{ $attendance['checkin_time'] }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusConfig = [
                                                    'Checked-in' => ['label' => 'Pending', 'class' => 'bg-orange-100 text-[#FF4D00]', 'icon' => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" />'],
                                                    'Approved' => ['label' => 'Validated', 'class' => 'bg-green-100 text-[#10AF13]', 'icon' => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" />'],
                                                    'Not Checked-in' => ['label' => 'Belum Check-In', 'class' => 'bg-gray-100 text-gray-700', 'icon' => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4M12 17h.01" />'],
                                                    'Absent' => ['label' => 'Absent', 'class' => 'bg-red-100 text-[#ff0000]', 'icon' => '<circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" />']
                                                ];
                                                $config = $statusConfig[$attendance['status']] ?? $statusConfig['Not Checked-in'];
                                            @endphp
                                            
                                            <div class="px-2 py-1 w-fit text-xs font-medium rounded-full flex gap-2 items-center {{ $config['class'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" 
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    {!! $config['icon'] !!}
                                                </svg>
                                                <p>{{ $config['label'] }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center gap-3">
                                                {{-- Approve Button --}}
                                                @if($attendance['status'] === 'Checked-in')
                                                    <form action="{{ route('trainer.attendance.approve', $attendance['id']) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" title="Approve" class="text-[#10AF13] hover:text-[#0e8e0f]">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                                <path d="M9 12l2 2l4 -4" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @elseif($attendance['status'] === 'Not Checked-in')
                                                    {{-- Manual Check-in Button --}}
                                                    <button @click="openManualCheckin = true; 
                                                                   selectedUser = { 
                                                                       id: {{ $attendance['user_id'] }}, 
                                                                       name: '{{ addslashes($attendance['user_name']) }}', 
                                                                       batch_id: {{ $attendance['batch_id'] }} 
                                                                   }" 
                                                            title="Manual Check-in"
                                                            class="text-[#10AF13] hover:text-[#0e8e0f]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                            <path d="M9 12l2 2l4 -4" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                
                                                {{-- Mark as Absent Button --}}
                                                @if(in_array($attendance['status'], ['Checked-in', 'Not Checked-in']))
                                                    <button @click="openAbsentModal = true; 
                                                                   selectedAttendance = { 
                                                                       id: {{ $attendance['id'] ?? 'null' }}, 
                                                                       user_id: {{ $attendance['user_id'] }}, 
                                                                       batch_id: {{ $attendance['batch_id'] }},
                                                                       name: '{{ addslashes($attendance['user_name']) }}'
                                                                   }" 
                                                            title="Mark as Absent"
                                                            class="text-[#ff0000] hover:text-[#E81B1B]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="9" />
                                                            <path d="M10 10l4 4m0 -4l-4 4" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
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

        {{-- Modal Manual Check-in --}}
        <div x-show="openManualCheckin" 
             x-cloak
             x-transition 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="openManualCheckin = false" class="bg-white w-full max-w-md rounded-2xl p-6 relative">
                <button @click="openManualCheckin = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
                
                <h2 class="text-xl font-semibold mb-2">Manual Check-In</h2>
                <p class="text-[#737373] mb-6">Check-in manual untuk peserta</p>
                
                <form action="{{ route('trainer.attendance.manual-checkin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batch_id" x-bind:value="selectedUser?.batch_id">
                    <input type="hidden" name="user_id" x-bind:value="selectedUser?.id">
                    
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-sm text-gray-600">Peserta:</p>
                        <p class="font-semibold" x-text="selectedUser?.name"></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-700">Notes (opsional)</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30" 
                                  placeholder="Alasan check-in manual..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="openManualCheckin = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                            Check-In
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Mark as Absent --}}
        <div x-show="openAbsentModal" 
             x-cloak
             x-transition 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="openAbsentModal = false" class="bg-white w-full max-w-md rounded-2xl p-6 relative">
                <button @click="openAbsentModal = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
                
                <h2 class="text-xl font-semibold mb-2">Tandai Tidak Hadir</h2>
                <p class="text-[#737373] mb-6">Peserta akan ditandai sebagai absent</p>
                
                <form action="{{ route('trainer.attendance.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_id" x-bind:value="selectedAttendance?.id">
                    <input type="hidden" name="user_id" x-bind:value="selectedAttendance?.user_id">
                    <input type="hidden" name="batch_id" x-bind:value="selectedAttendance?.batch_id">
                    
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-sm text-gray-600">Peserta:</p>
                        <p class="font-semibold" x-text="selectedAttendance?.name"></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-700">Alasan (opsional)</label>
                        <textarea name="reason" rows="3" 
                                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30" 
                                  placeholder="Alasan tidak hadir..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="openAbsentModal = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#ff0000] text-white rounded-lg hover:bg-[#E81B1B] font-medium">
                            Tandai Absent
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div> {{-- End of main x-data wrapper --}}

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif

    @if(session('error'))
        <x-notification type="error">{{ session('error') }}</x-notification>
    @endif

    @if(session('info'))
        <x-notification type="info">{{ session('info') }}</x-notification>
    @endif

    <style>
        [x-cloak] { 
            display: none !important; 
        }
    </style>
@endsection
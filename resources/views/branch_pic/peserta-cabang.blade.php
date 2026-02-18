{{-- resources/views/branch_pic/peserta-cabang.blade.php --}}
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Peserta Cabang</h1>
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

    {{-- Filter Section --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('branch_pic.participants.index')"
            searchPlaceholder="Cari nama, email, atau batch..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'completed', 'label' => 'Completed'],
                        ['value' => 'ongoing', 'label' => 'Ongoing'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'registered', 'label' => 'Registered'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Participants Table --}}
    <div class="grid gap-6 mt-8 px-2" x-data="participantManagement()">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Daftar Peserta Cabang
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($participants as $participant)
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3 text-left">
                                {{ $participant->user->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $participant->user->email }}
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $participant->batch->title }}</div>
                                <div class="text-xs text-gray-500">{{ $participant->batch->code }}</div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $participant->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    // Determine badge based on batch status and participant status
                                    if ($participant->batch->status === 'Ongoing' && $participant->status === 'Approved') {
                                        $badgeClass = 'bg-green-100 text-[#10AF13]';
                                        $statusText = 'Ongoing';
                                    } elseif ($participant->batch->status === 'Completed' && $participant->status === 'Approved') {
                                        $badgeClass = 'bg-orange-100 text-[#FF4D00]';
                                        $statusText = 'Completed';
                                    } elseif ($participant->status === 'Approved') {
                                        $badgeClass = 'bg-blue-100 text-[#0059FF]';
                                        $statusText = 'Approved';
                                    } elseif ($participant->status === 'Pending') {
                                        $badgeClass = 'bg-gray-200 text-gray-700';
                                        $statusText = 'Registered';
                                    } elseif ($participant->status === 'Rejected') {
                                        $badgeClass = 'bg-red-100 text-red-700';
                                        $statusText = 'Rejected';
                                    } else {
                                        $badgeClass = 'bg-gray-100 text-gray-700';
                                        $statusText = $participant->status;
                                    }
                                @endphp
                                <div class="px-2 py-1 w-fit text-xs font-medium uppercase rounded-full {{ $badgeClass }}">
                                    <p>{{ $statusText }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="showDetail({{ $participant->id }})" 
                                        class="inline-flex items-center justify-center hover:bg-gray-100 rounded p-1 transition"
                                        title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 hover:text-gray-900" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                    </svg>
                                </button>
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
                                        Peserta yang mendaftar akan muncul di sini
                                    @endif
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($participants->hasPages())
            <div class="mt-6">
                {{ $participants->links() }}
            </div>
            @endif
        </div>

        {{-- Detail Modal --}}
        <div x-show="detailModal" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div @click.outside="detailModal = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white max-w-2xl w-full rounded-2xl shadow-lg relative">

                {{-- Close Button --}}
                <button @click="detailModal = false" 
                        class="absolute top-6 right-6 text-[#737373] hover:text-black transition z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>

                {{-- Loading State --}}
                <div x-show="loading" class="p-8">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#10AF13] mb-4"></div>
                        <p class="text-gray-500 font-medium">Memuat detail peserta...</p>
                    </div>
                </div>

                {{-- Error State --}}
                <div x-show="error" x-cloak class="p-8">
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto mb-3 text-red-500">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p class="text-red-500 font-medium">Gagal memuat detail peserta</p>
                        <p class="text-gray-500 text-sm mt-1">Silakan coba lagi</p>
                        <button @click="detailModal = false" 
                                class="mt-4 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Tutup
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div x-show="!loading && !error && selectedParticipant" x-cloak class="p-8">
                    {{-- Header --}}
                    <h2 class="text-2xl font-semibold">Detail Peserta</h2>
                    <p class="text-[#737373] mb-6">Informasi lengkap peserta pelatihan</p>

                    {{-- Details Grid --}}
                    <template x-if="selectedParticipant">
                        <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</p>
                                <p class="text-gray-900" x-text="selectedParticipant.name"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Email</p>
                                <p class="text-gray-900 break-all" x-text="selectedParticipant.email"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-sm font-semibold mb-1">Cabang</p>
                                <p class="text-gray-900" x-text="selectedParticipant.branch"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-sm font-semibold mb-1">Batch Pelatihan</p>
                                <p class="text-gray-900" x-text="selectedParticipant.batch_title"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="selectedParticipant.batch_code"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Training</p>
                                <p class="text-gray-900" x-text="selectedParticipant.batch_start"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Waktu</p>
                                <p class="text-gray-900" x-text="selectedParticipant.batch_time"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Pendaftaran</p>
                                <p class="text-gray-900" x-text="selectedParticipant.registration_date"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Status Batch</p>
                                <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-green-100 text-[#10AF13]': selectedParticipant.batch_status === 'Ongoing',
                                          'bg-orange-100 text-[#FF4D00]': selectedParticipant.batch_status === 'Completed',
                                          'bg-blue-100 text-[#0059FF]': selectedParticipant.batch_status === 'Scheduled'
                                      }"
                                      x-text="selectedParticipant.batch_status">
                                </span>
                            </div>
                            <div class="col-span-2" x-show="selectedParticipant.attendance_status">
                                <p class="text-gray-700 text-sm font-semibold mb-1">Status Kehadiran</p>
                                <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-orange-100 text-[#FF4D00]': selectedParticipant.attendance_status === 'Check-In',
                                          'bg-green-100 text-[#10AF13]': selectedParticipant.attendance_status === 'Approved',
                                          'bg-gray-200 text-gray-700': selectedParticipant.attendance_status === 'Pending'
                                      }"
                                      x-text="selectedParticipant.attendance_status">
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js Component --}}
    <script>
    function participantManagement() {
        return {
            detailModal: false,
            selectedParticipant: null,
            loading: false,
            error: false,

            async showDetail(participantId) {
                // Reset states
                this.loading = true;
                this.error = false;
                this.selectedParticipant = null;
                this.detailModal = true;

                try {
                    const response = await fetch(`{{ route('branch_pic.participants.index') }}/${participantId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        this.selectedParticipant = data.data;
                        this.loading = false;
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                } catch (error) {
                    console.error('Error fetching participant details:', error);
                    this.loading = false;
                    this.error = true;
                }
            }
        };
    }
    </script>
@endsection
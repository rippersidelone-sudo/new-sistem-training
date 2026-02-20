{{-- resources/views/branch_pic/peserta-cabang.blade.php --}}
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Peserta Cabang</h1>
        <p class="text-[#737373] mt-2 font-medium">Cabang {{ $branch->name ?? 'Unknown' }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $totalParticipants,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])

        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => $ongoingCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]'
        ])

        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => $completedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12l2 2l4 -4" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])

        @include('dashboard.card', [
            'title' => 'Sertifikat',
            'value' => $certificatesCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" /><path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color' => 'text-[#D4AF37]'
        ])
    </div>

    {{-- Filter Bar --}}
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

    <div class="grid gap-6 mt-8 px-2" x-data="participantManagement()">
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
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @forelse($participants as $index => $participant)
                        @php
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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">
                                {{ $participants->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $participant->user->name }}</td>
                            <td class="px-4 py-3">{{ $participant->user->email }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $participant->batch->title }}</div>
                                <div class="text-xs text-gray-500">{{ $participant->batch->code }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $participant->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="px-3 py-1 w-fit text-xs font-medium uppercase rounded-full {{ $badgeClass }}">
                                    {{ $statusText }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="showDetail({{ $participant->id }})"
                                        class="inline-flex items-center justify-center hover:bg-gray-100 rounded p-1 transition"
                                        title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="text-gray-600 hover:text-gray-900">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
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

            @if($participants->hasPages())
            <div class="mt-6">
                <x-pagination :paginator="$participants" />
            </div>
            @endif
        </div>

        {{-- Detail Modal --}}
        <div x-show="detailModal" x-cloak x-transition
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div @click.outside="detailModal = false"
                 class="bg-white max-w-2xl w-full rounded-2xl shadow-lg relative">

                <button @click="detailModal = false"
                        class="absolute top-6 right-6 text-[#737373] hover:text-black transition z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                    </svg>
                </button>

                <div x-show="loading" class="p-8">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#10AF13] mb-4"></div>
                        <p class="text-gray-500 font-medium">Memuat detail peserta...</p>
                    </div>
                </div>

                <div x-show="error" x-cloak class="p-8 text-center py-8">
                    <p class="text-red-500 font-medium">Gagal memuat detail peserta</p>
                    <button @click="detailModal = false"
                            class="mt-4 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Tutup
                    </button>
                </div>

                <div x-show="!loading && !error && selectedParticipant" x-cloak class="p-8">
                    <h2 class="text-2xl font-semibold">Detail Peserta</h2>
                    <p class="text-[#737373] mb-6">Informasi lengkap peserta pelatihan</p>

                    <template x-if="selectedParticipant">
                        <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</p>
                                <p x-text="selectedParticipant.name"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Email</p>
                                <p x-text="selectedParticipant.email" class="break-all"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-sm font-semibold mb-1">Cabang</p>
                                <p x-text="selectedParticipant.branch"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-sm font-semibold mb-1">Batch Pelatihan</p>
                                <p x-text="selectedParticipant.batch_title"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="selectedParticipant.batch_code"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Training</p>
                                <p x-text="selectedParticipant.batch_start"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Waktu</p>
                                <p x-text="selectedParticipant.batch_time"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Pendaftaran</p>
                                <p x-text="selectedParticipant.registration_date"></p>
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
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
    function participantManagement() {
        return {
            detailModal: false,
            selectedParticipant: null,
            loading: false,
            error: false,

            async showDetail(participantId) {
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

                    if (!response.ok) throw new Error('Network error');

                    const data = await response.json();

                    if (data.success) {
                        this.selectedParticipant = data.data;
                        this.loading = false;
                    } else {
                        throw new Error(data.message || 'Failed');
                    }
                } catch (error) {
                    this.loading = false;
                    this.error = true;
                }
            }
        };
    }
    </script>
@endsection
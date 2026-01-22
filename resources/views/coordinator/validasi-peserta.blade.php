@extends('layouts.coordinator')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Validasi Peserta</h1>
        <p class="text-[#737373] mt-2 font-medium">Kelola persetujuan pendaftaran peserta pelatihan</p>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-notification type="error">{{ $error }}</x-notification>
        @endforeach
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Pending',
            'value' => $pendingCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-gray-700'
        ])
        @include('dashboard.card', [
            'title' => 'Approved',
            'value' => $approvedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Rejected',
            'value' => $rejectedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-x-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color' => 'text-[#ff0000]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('coordinator.participants.index')"
            searchPlaceholder="Cari nama, email..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'Pending', 'label' => 'Pending'],
                        ['value' => 'Approved', 'label' => 'Approved'],
                        ['value' => 'Rejected', 'label' => 'Rejected'],
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

    {{-- Participants Table --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">Daftar Peserta</h2>
            </div>
            <div class="overflow-x-auto" x-data="{ selectedParticipant: null, showModal: false, showRejectModal: false }">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3">Batch</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($participants as $participant)
                        <tr class="hover:bg-gray-50 transition text-left">
                            <td class="px-4 py-3">{{ $participant['user_name'] }}</td>
                            <td class="px-4 py-3">{{ $participant['user_email'] }}</td>
                            <td class="px-4 py-3">{{ $participant['branch_name'] }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $participant['batch_title'] }}</div>
                                <div class="text-xs text-gray-500">{{ $participant['batch_code'] }}</div>
                            </td>
                            <td class="px-4 py-3">{{ formatDate($participant['created_at']) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = match($participant['status']) {
                                        'Pending' => 'bg-gray-200 text-gray-700',
                                        'Approved' => 'bg-blue-100 text-[#0059FF]',
                                        'Rejected' => 'bg-red-100 text-[#ff0000]',
                                        default => 'bg-gray-200 text-gray-700'
                                    };
                                @endphp
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full {{ $statusClass }}">
                                    <p>{{ $participant['status'] }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-4">
                                    <button @click="selectedParticipant = {{ json_encode($participant) }}; showModal = true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>

                                    @if($participant['status'] === 'Pending')
                                    <form method="POST" action="{{ route('coordinator.participants.approve', $participant['id']) }}" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Setujui pendaftaran peserta ini?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-check text-[#10AF13] hover:text-[#0e8e0f]">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                                <path d="M15 19l2 2l4 -4" />
                                            </svg>
                                        </button>
                                    </form>

                                    <button @click="selectedParticipant = {{ json_encode($participant) }}; showRejectModal = true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-x text-[#ff0000] hover:text-[#E81B1B]">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                            <path d="M22 22l-5 -5" />
                                            <path d="M17 22l5 -5" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data peserta
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $participants->links() }}
                </div>

                {{-- Detail Modal --}}
                <div x-show="showModal" x-cloak x-transition class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                    <div @click.outside="showModal = false" class="bg-white max-w-xl rounded-2xl shadow-lg p-8 relative">
                        <button @click="showModal = false" class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>

                        <h2 class="text-2xl font-semibold">Detail Peserta</h2>
                        <p class="text-[#737373] mb-6">Informasi lengkap peserta pelatihan</p>

                        <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                            <div>
                                <p class="text-gray-700 text-md font-medium">Nama Lengkap</p>
                                <p x-text="selectedParticipant?.user_name"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Email</p>
                                <p x-text="selectedParticipant?.user_email"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Cabang</p>
                                <p x-text="selectedParticipant?.branch_name"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-700 text-md font-medium">Batch Pelatihan</p>
                                <p x-text="selectedParticipant?.batch_title"></p>
                                <p class="text-xs text-gray-500" x-text="selectedParticipant?.batch_code"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Pendaftaran</p>
                                <p x-text="selectedParticipant?.created_at ? new Date(selectedParticipant.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'}) : '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Status</p>
                                <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full"
                                    :class="{
                                        'bg-gray-200 text-gray-700': selectedParticipant?.status === 'Pending',
                                        'bg-blue-100 text-[#0059FF]': selectedParticipant?.status === 'Approved',
                                        'bg-red-100 text-[#ff0000]': selectedParticipant?.status === 'Rejected'
                                    }"
                                    x-text="selectedParticipant?.status">
                                </span>
                            </div>
                            <div class="col-span-2" x-show="selectedParticipant?.rejection_reason">
                                <p class="text-gray-700 text-md font-medium">Alasan Penolakan</p>
                                <p x-text="selectedParticipant?.rejection_reason"></p>
                            </div>
                            <div class="col-span-2" x-show="selectedParticipant?.approved_by_name">
                                <p class="text-gray-700 text-md font-medium">Disetujui Oleh</p>
                                <p x-text="selectedParticipant?.approved_by_name"></p>
                            </div>
                        </div>

                        <template x-if="selectedParticipant?.status === 'Pending'">
                            <div class="mt-6 flex justify-end gap-3">
                                <button @click="showRejectModal = true; showModal = false" 
                                    class="flex justify-center items-center gap-3 px-4 py-2 border rounded-lg text-[#ff0000] hover:bg-gray-50 font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                        <path d="M22 22l-5 -5" />
                                        <path d="M17 22l5 -5" />
                                    </svg>
                                    <p>Tolak</p>
                                </button>
                                <form :action="'/coordinator/participants/' + selectedParticipant?.id + '/approve'" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                        onclick="return confirm('Setujui pendaftaran peserta ini?')"
                                        class="flex justify-center items-center gap-3 px-4 py-2 rounded-lg text-white bg-[#10AF13] hover:bg-[#0e8e0f] font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                            <path d="M15 19l2 2l4 -4" />
                                        </svg>
                                        <p>Setujui</p>
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Reject Modal --}}
                <div x-show="showRejectModal" x-cloak x-transition class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                    <div @click.outside="showRejectModal = false" class="bg-white max-w-xl w-full mx-4 rounded-2xl shadow-lg p-8 relative">
                        <button @click="showRejectModal = false" class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>

                        <h2 class="text-2xl font-semibold">Tolak Pendaftaran</h2>
                        <p class="text-[#737373] mb-6">Berikan alasan penolakan untuk peserta</p>

                        <form :action="'/coordinator/participants/' + selectedParticipant?.id + '/reject'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="rejection_reason" rows="4" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#10AF13] focus:border-transparent"
                                    placeholder="Masukkan alasan penolakan..."></textarea>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showRejectModal = false"
                                    class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 rounded-lg text-white bg-[#ff0000] hover:bg-[#E81B1B] font-medium">
                                    Tolak Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
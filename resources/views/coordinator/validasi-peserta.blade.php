@extends('layouts.coordinator')

@section('content')
    <div class="px-2 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">Validasi Peserta</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola persetujuan pendaftaran peserta pelatihan</p>
        </div>

        {{-- SYNC PARTICIPANTS BUTTON --}}
        <button
            x-data="syncParticipants()"
            @click="sync()"
            :disabled="loading"
            :title="loading ? 'Syncing...' : 'Sync data participants dari API'"
            class="flex items-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
            <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
            </svg>
            <svg x-show="loading" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                <path d="M12 3a9 9 0 1 0 9 9" />
            </svg>
            <span x-text="loading ? 'Syncing...' : 'Sync Participants'"></span>
        </button>
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
            <div class="overflow-x-auto" x-data="{ 
                selectedParticipant: null, 
                showDetailModal: false, 
                showRejectModal: false,
                showApproveModal: false,
                approveFormId: null
            }">
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
                                    <button @click="selectedParticipant = {{ json_encode($participant) }}; showDetailModal = true"
                                            title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>

                                    @if($participant['status'] === 'Pending')
                                    <form id="approve-form-{{ $participant['id'] }}" 
                                          method="POST" 
                                          action="{{ route('coordinator.participants.approve', $participant['id']) }}" 
                                          class="inline">
                                        @csrf
                                    </form>
                                    <button @click="selectedParticipant = {{ json_encode($participant) }}; approveFormId = 'approve-form-{{ $participant['id'] }}'; showApproveModal = true"
                                            title="Setujui Pendaftaran">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-check text-[#10AF13] hover:text-[#0e8e0f]">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                            <path d="M15 19l2 2l4 -4" />
                                        </svg>
                                    </button>

                                    <button @click="selectedParticipant = {{ json_encode($participant) }}; showRejectModal = true"
                                            title="Tolak Pendaftaran">
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

                {{-- ... MODALS (detail/approve/reject) tetap sama seperti punyamu ... --}}
                {{-- (Aku tidak ubah isi modal sama sekali) --}}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ============================================================
        // SYNC PARTICIPANTS FUNCTION (FIXED)
        // ============================================================
        function syncParticipants() {
            return {
                loading: false,

                sync() {
                    if (this.loading) return;
                    this.loading = true;

                    fetch('{{ route('sync.participants') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(async (res) => {
                        const contentType = res.headers.get('content-type') || '';

                        // Kalau server balikin HTML (redirect/error page), tampilkan supaya kebaca
                        if (!contentType.includes('application/json')) {
                            const text = await res.text();
                            throw new Error(text);
                        }

                        const data = await res.json();

                        // Kalau status HTTP bukan 2xx, lempar error biar masuk catch
                        if (!res.ok) {
                            throw new Error(data?.message || 'Request gagal');
                        }

                        return data;
                    })
                    .then(data => {
                        this.loading = false;
                        this.showNotification(data.success, data.message);

                        // Reload page jika berhasil untuk update tabel
                        if (data.success) {
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    })
                    .catch((err) => {
                        console.error('SYNC PARTICIPANTS ERROR:', err);
                        this.loading = false;
                        this.showNotification(false, err?.message || 'Terjadi kesalahan saat sync data.');
                    });
                },

                showNotification(success, message) {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <div x-data="{ show: true }"
                             x-show="show"
                             x-init="setTimeout(() => show = false, 4000)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-4"
                             class="fixed bottom-6 right-6 z-50 max-w-md">
                            <div class="flex items-center gap-3 ${success ? 'bg-[#10AF13]' : 'bg-red-600'} text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                                <span class="font-medium text-sm">${message}</span>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(div.firstElementChild);
                    Alpine.initTree(document.body.lastElementChild);
                }
            }
        }
    </script>
    @endpush
@endsection

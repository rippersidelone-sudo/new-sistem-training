@extends('layouts.coordinator')

@section('content')
    <div class="px-2 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">Monitoring Peserta</h1>
            <p class="text-[#737373] mt-2 font-medium">Monitor status pendaftaran peserta pelatihan</p>
        </div>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Pending',
            'value' => $pendingCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-gray-700'
        ])
        @include('dashboard.card', [
            'title' => 'Approved',
            'value' => $approvedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Rejected',
            'value' => $rejectedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
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

    {{-- Tabel Peserta --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Peserta</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Total: <span class="font-semibold text-gray-700">{{ $participants->total() }}</span> peserta
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto" x-data="{
                selectedParticipant: null,
                showDetailModal: false,
                showApproveModal: false,
                showRejectModal: false,
                approveFormId: null,
                rejectReason: '',
                openDetail(participant) {
                    this.selectedParticipant = participant;
                    this.showDetailModal = true;
                }
            }">
                @if($participants->count() > 0)
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Cabang</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Tanggal Daftar</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($participants as $index => $participant)
                                @php
                                    $statusClass = match($participant['status']) {
                                        'Pending'  => 'bg-yellow-100 text-yellow-700',
                                        'Approved' => 'bg-green-100 text-green-700',
                                        'Rejected' => 'bg-red-100 text-red-600',
                                        default    => 'bg-gray-100 text-gray-600'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">{{ $participants->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $participant['user_name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $participant['user_email'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $participant['branch_name'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">{{ $participant['batch_title'] }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $participant['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ formatDate($participant['created_at']) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $participant['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="openDetail({{ json_encode($participant) }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0059FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                                Detail
                                            </button>
                                            @if($participant['status'] === 'Pending')
                                                <form id="approve-form-{{ $participant['id'] }}"
                                                      method="POST"
                                                      action="{{ route('coordinator.participants.approve', $participant['id']) }}"
                                                      class="hidden">
                                                    @csrf
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $participants->links() }}
                    </div>

                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada data peserta</p>
                        <p class="text-sm mt-1 text-gray-400">
                            @if(request('status') || request('branch_id') || request('search'))
                                Tidak ada peserta yang sesuai filter
                            @else
                                Belum ada peserta yang mendaftar
                            @endif
                        </p>
                    </div>
                @endif

                {{-- ============================================================ --}}
                {{-- MODAL DETAIL PESERTA (diambil dari kode asli, tidak diubah)   --}}
                {{-- ============================================================ --}}
                <div x-show="showDetailModal" x-cloak x-transition.opacity.duration.300
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">

                    <div @click.outside="showDetailModal = false"
                         class="bg-white w-full max-w-lg sm:max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col ring-1 ring-gray-200/70">

                        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold tracking-tight">Detail Peserta</h2>
                                <p class="text-sm text-white/90 mt-0.5">Informasi pendaftaran pelatihan</p>
                            </div>
                            <button @click="showDetailModal = false"
                                    class="p-1.5 rounded-full hover:bg-white/15 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 sm:p-7 overflow-y-auto flex-1 space-y-5">
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-full bg-[#10AF13]/10 flex items-center justify-center text-[#10AF13] font-semibold text-xl shrink-0 ring-1 ring-[#10AF13]/20">
                                        <span x-text="selectedParticipant?.user_name?.charAt(0) || '?'"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate" x-text="selectedParticipant?.user_name"></h3>
                                        <p class="text-sm text-gray-600 mt-0.5 truncate" x-text="selectedParticipant?.user_email"></p>
                                    </div>
                                    <span class="inline-flex px-3.5 py-1.5 text-xs font-bold uppercase tracking-wide rounded-full"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800 border border-yellow-200': selectedParticipant?.status === 'Pending',
                                              'bg-[#10AF13]/10 text-[#10AF13] border border-[#10AF13]/30': selectedParticipant?.status === 'Approved',
                                              'bg-red-100 text-red-800 border border-red-200': selectedParticipant?.status === 'Rejected'
                                          }"
                                          x-text="selectedParticipant?.status">
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm text-gray-500 font-medium">Cabang</dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedParticipant?.branch_name || '-'"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500 font-medium">Kategori</dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedParticipant?.category_name || '-'"></dd>
                                        </div>
                                    </dl>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                    <dt class="text-sm text-gray-500 font-medium">Batch Pelatihan</dt>
                                    <dd class="mt-1">
                                        <div class="text-sm font-medium text-gray-900" x-text="selectedParticipant?.batch_title || '-'"></div>
                                        <div class="text-xs text-gray-500 mt-1" x-text="selectedParticipant?.batch_code || '-'"></div>
                                    </dd>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm text-gray-500 font-medium">Tanggal Daftar</dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900"
                                                x-text="selectedParticipant?.created_at
                                                    ? new Date(selectedParticipant.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                                    : '-'">
                                            </dd>
                                        </div>
                                        <template x-if="selectedParticipant?.status !== 'Pending'">
                                            <div>
                                                <dt class="text-sm text-gray-500 font-medium">Divalidasi Oleh</dt>
                                                <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedParticipant?.approved_by_name || '-'"></dd>
                                            </div>
                                        </template>
                                    </dl>
                                </div>
                            </div>

                            <template x-if="selectedParticipant?.status === 'Rejected' && selectedParticipant?.rejection_reason">
                                <div class="bg-red-50 rounded-xl p-5 border border-red-100">
                                    <p class="text-sm font-medium text-red-800 mb-2">Alasan Penolakan</p>
                                    <p class="text-sm text-red-700 leading-relaxed" x-text="selectedParticipant.rejection_reason"></p>
                                </div>
                            </template>
                        </div>

                        <div class="px-6 py-5 border-t bg-gray-50 flex items-center justify-end gap-3">
                            <template x-if="selectedParticipant?.status === 'Pending'">
                                <div class="flex gap-3 mr-auto">
                                    <button @click="showDetailModal = false; approveFormId = 'approve-form-' + selectedParticipant.id; showApproveModal = true"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#10AF13] text-white font-medium rounded-lg hover:bg-[#0e9e10] shadow-md shadow-[#10AF13]/20 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Setujui
                                    </button>
                                    <button @click="showDetailModal = false; showRejectModal = true"
                                        class="inline-flex items-center px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                                        Tolak
                                    </button>
                                </div>
                            </template>
                            <button @click="showDetailModal = false"
                                    class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- MODAL KONFIRMASI SETUJUI                                      --}}
                {{-- ============================================================ --}}
                <div x-show="showApproveModal" x-cloak x-transition.opacity.duration.200
                     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 px-4">
                    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-7 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Setujui Pendaftaran?</h3>
                        <p class="text-sm text-gray-500 mb-1">Anda akan menyetujui pendaftaran</p>
                        <p class="font-semibold text-gray-800 mb-5" x-text="selectedParticipant?.user_name"></p>
                        <div class="flex gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                                Batal
                            </button>
                            <button type="button" @click="showApproveModal = false; document.getElementById(approveFormId).submit()"
                                class="flex-1 px-4 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold text-sm shadow-lg shadow-[#10AF13]/30">
                                Ya, Setujui
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- MODAL KONFIRMASI TOLAK                                        --}}
                {{-- ============================================================ --}}
                <div x-show="showRejectModal" x-cloak x-transition.opacity.duration.200
                     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 px-4">
                    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
                        <div class="bg-red-600 px-6 py-5 text-white flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold">Tolak Pendaftaran?</h3>
                                <p class="text-sm opacity-90">Berikan alasan penolakan</p>
                            </div>
                            <button @click="showRejectModal = false" class="text-white hover:text-gray-200 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 mb-4">
                                Anda akan menolak pendaftaran
                                <span class="font-semibold text-gray-900" x-text="selectedParticipant?.user_name"></span>.
                            </p>
                            <form :action="'{{ url('coordinator/participants') }}/' + selectedParticipant?.id + '/reject'" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="rejection_reason" rows="3" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 text-sm resize-none"
                                        placeholder="Berikan alasan penolakan..."></textarea>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="showRejectModal = false"
                                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm shadow-lg shadow-red-600/30">
                                        Ya, Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>{{-- end overflow-x-auto + x-data --}}
        </div>
    </div>

    @push('scripts')
    <script>
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
                        if (!contentType.includes('application/json')) {
                            const text = await res.text();
                            throw new Error(text || 'Response bukan JSON');
                        }
                        const data = await res.json();
                        if (!res.ok) throw new Error(data?.message || 'Request gagal');
                        return data;
                    })
                    .then(data => {
                        this.loading = false;
                        if (data.success) setTimeout(() => window.location.reload(), 1500);
                    })
                    .catch((err) => {
                        console.error('SYNC ERROR:', err);
                        this.loading = false;
                    });
                }
            }
        }
    </script>
    @endpush
@endsection
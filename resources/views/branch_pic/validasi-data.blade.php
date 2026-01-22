{{-- resources/views/branch_pic/validasi-data.blade.php --}}
@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Validasi Pendaftaran</h1>
        <p class="text-[#737373] mt-2 font-medium">Kelola persetujuan pendaftaran peserta dari Cabang {{ $branch->name ?? 'Unknown' }}</p>
    </div>

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

    {{-- Filter Section --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('branch_pic.validation.index')"
            searchPlaceholder="Cari nama atau email..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Participants Table --}}
    <div class="grid gap-6 mt-8 px-2" x-data="{ 
        detailModal: false,
        rejectModal: false,
        selectedParticipant: null,
        selectedIds: [],
        async showDetail(participantId) {
            try {
                const response = await fetch(`{{ route('branch_pic.validation.index') }}/${participantId}`);
                const data = await response.json();
                if (data.success) {
                    this.selectedParticipant = data.data;
                    this.detailModal = true;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat detail peserta');
            }
        },
        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:not(:disabled)'))
                    .map(cb => parseInt(cb.value));
            } else {
                this.selectedIds = [];
            }
        },
        async bulkApprove() {
            if (this.selectedIds.length === 0) {
                alert('Pilih minimal satu peserta');
                return;
            }
            if (!confirm(`Setujui ${this.selectedIds.length} pendaftaran?`)) return;
            
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                this.selectedIds.forEach(id => formData.append('participant_ids[]', id));
                
                const response = await fetch('{{ route('branch_pic.validation.bulk-approve') }}', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menyetujui pendaftaran');
            }
        }
    }">
        <div class="bg-white border rounded-2xl p-6 max-h-[540px] overflow-y-auto pr-1">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold">Daftar Peserta</h2>
                
                {{-- Bulk Actions --}}
                <div class="flex gap-2" x-show="selectedIds.length > 0">
                    <button @click="bulkApprove()" 
                            class="flex items-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg text-sm font-medium hover:bg-[#0e8e0f] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Setujui (<span x-text="selectedIds.length"></span>)
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">
                                <input type="checkbox" 
                                       @change="toggleSelectAll($event)"
                                       class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                            </th>
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
                            <td class="px-4 py-3">
                                <input type="checkbox" 
                                       value="{{ $participant->id }}"
                                       class="participant-checkbox rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]"
                                       x-model="selectedIds"
                                       {{ $participant->status !== 'Pending' ? 'disabled' : '' }}>
                            </td>
                            <td class="px-4 py-3">{{ $participant->user->name }}</td>
                            <td class="px-4 py-3">{{ $participant->user->email }}</td>
                            <td class="px-4 py-3">{{ $participant->batch->title }}</td>
                            <td class="px-4 py-3">{{ $participant->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match($participant->status) {
                                        'Pending' => 'bg-gray-200 text-gray-700',
                                        'Approved' => 'bg-green-100 text-[#10AF13]',
                                        'Rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <div class="px-2 py-1 w-fit text-xs uppercase font-medium rounded-full {{ $badgeClass }}">
                                    {{ $participant->status }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    {{-- View Button --}}
                                    <button @click="showDetail({{ $participant->id }})"
                                            class="hover:bg-gray-100 rounded p-1 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>
                                    
                                    @if($participant->status === 'Pending')
                                    {{-- Approve Button --}}
                                    <form method="POST" action="{{ route('branch_pic.validation.approve', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Setujui pendaftaran ini?')"
                                                class="hover:bg-green-50 rounded p-1 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                stroke="currentColor" stroke-width="2" class="text-[#10AF13] hover:text-[#0e8e0f]">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                                <path d="M15 19l2 2l4 -4" />
                                            </svg>
                                        </button>
                                    </form>
                                    
                                    {{-- Reject Button --}}
                                    <button @click="selectedParticipant = {{ json_encode(['id' => $participant->id, 'name' => $participant->user->name]) }}; rejectModal = true"
                                            class="hover:bg-red-50 rounded p-1 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" class="text-[#ff0000] hover:text-[#E81B1B]">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="font-medium">Belum ada data pendaftaran</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    @if(request('search') || request('status'))
                                        Tidak ada pendaftaran yang sesuai dengan filter
                                    @else
                                        Pendaftaran peserta akan muncul di sini
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

        {{-- Detail Modal (View Only) --}}
        <div x-show="detailModal" 
             x-cloak 
             x-transition
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div @click.outside="detailModal = false" 
                 class="bg-white max-w-xl w-full rounded-2xl shadow-lg p-8 relative">
                <button @click="detailModal = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-xl font-semibold">Detail Peserta</h2>
                <p class="text-[#737373] mb-6">Informasi lengkap peserta pelatihan</p>

                <template x-if="selectedParticipant">
                    <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                        <div>
                            <p class="text-gray-700 text-md font-medium">Nama Lengkap</p>
                            <p x-text="selectedParticipant.name"></p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Email</p>
                            <p x-text="selectedParticipant.email" class="break-all"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Cabang</p>
                            <p x-text="selectedParticipant.branch"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-700 text-md font-medium">Batch Pelatihan</p>
                            <p x-text="selectedParticipant.batch_title"></p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Tanggal Pendaftaran</p>
                            <p x-text="selectedParticipant.registration_date"></p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-md font-medium">Status</p>
                            <span class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full"
                                  :class="{
                                      'bg-gray-200 text-gray-700': selectedParticipant.status === 'Pending',
                                      'bg-green-100 text-[#10AF13]': selectedParticipant.status === 'Approved',
                                      'bg-red-100 text-red-700': selectedParticipant.status === 'Rejected'
                                  }"
                                  x-text="selectedParticipant.status">
                            </span>
                        </div>
                        <div class="col-span-2" x-show="selectedParticipant.rejection_reason">
                            <p class="text-gray-700 text-md font-medium">Alasan Penolakan</p>
                            <p x-text="selectedParticipant.rejection_reason" class="text-red-600"></p>
                        </div>
                        <div class="col-span-2" x-show="selectedParticipant.approved_by">
                            <p class="text-gray-700 text-md font-medium">Disetujui Oleh</p>
                            <p x-text="selectedParticipant.approved_by"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Reject Modal with Form --}}
        <div x-show="rejectModal" 
             x-cloak 
             x-transition
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div @click.outside="rejectModal = false" 
                 class="bg-white max-w-md w-full rounded-2xl shadow-lg p-8 relative">
                <button @click="rejectModal = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-xl font-semibold text-red-600">Tolak Pendaftaran</h2>
                <p class="text-[#737373] mb-6">Berikan alasan penolakan untuk peserta ini</p>

                <template x-if="selectedParticipant">
                    <form :action="`{{ route('branch_pic.validation.index') }}/${selectedParticipant.id}/reject`" 
                          method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Peserta
                            </label>
                            <p class="text-gray-900 font-medium" x-text="selectedParticipant.name"></p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" 
                                      rows="4" 
                                      required
                                      maxlength="500"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                      placeholder="Jelaskan alasan penolakan..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" 
                                    @click="rejectModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                Tolak Pendaftaran
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
@endsection

@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, 3000)"
     class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, 3000)"
     class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
    {{ session('error') }}
</div>
@endif
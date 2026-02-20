{{-- resources/views/trainer/penilaian-tugas.blade.php --}}
@extends('layouts.trainer')

@section('content')

{{-- 
    URL template pakai dummy ID=0, lalu JS replace '0' dengan ID asli.
    Ini cara paling aman agar route() helper generate URL yang benar.
--}}
@php
    $acceptUrl   = route('trainer.submissions.accept',   0);
    $rejectUrl   = route('trainer.submissions.reject',   0);
    $downloadUrl = route('trainer.submissions.download', 0);
@endphp

<div x-data="{
    openSubmission:     false,
    openConfirmAccept:  false,
    openConfirmReject:  false,
    currentSubmission:  null,

    acceptBaseUrl:   '{{ $acceptUrl }}',
    rejectBaseUrl:   '{{ $rejectUrl }}',
    downloadBaseUrl: '{{ $downloadUrl }}',

    makeUrl(base, id) {
        return base.replace('/0/', '/' + id + '/').replace(/\/0$/, '/' + id);
    },

    openReviewModal(submission) {
        this.currentSubmission = submission;
        this.openSubmission    = true;
    },

    confirmAccept() {
        this.openConfirmAccept = true;
    },

    confirmReject() {
        const feedback = document.querySelector('#reviewForm textarea[name=feedback]').value;
        if (!feedback.trim()) {
            alert('Harap isi feedback sebelum menolak submission.');
            return;
        }
        this.openConfirmReject = true;
    },

    submitAccept() {
        const form    = document.getElementById('reviewForm');
        form.action   = this.makeUrl(this.acceptBaseUrl, this.currentSubmission.id);
        form.method   = 'POST';
        form.submit();
    },

    submitReject() {
        const form    = document.getElementById('reviewForm');
        form.action   = this.makeUrl(this.rejectBaseUrl, this.currentSubmission.id);
        form.method   = 'POST';
        form.submit();
    }
}" x-cloak>

    <div class="px-2">
        <h1 class="text-2xl font-semibold">Penilaian Tugas</h1>
        <p class="text-[#737373] mt-2 font-medium">Review dan beri penilaian submission peserta</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Pending',
            'value' => $stats['pending'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Accepted',
            'value' => $stats['accepted'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Rejected',
            'value' => $stats['rejected'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>',
            'color' => 'text-red-600'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('trainer.penilaian-tugas')"
            searchPlaceholder="Cari nama peserta..."
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
                    'name'        => 'status',
                    'placeholder' => 'Semua Status',
                    'options'     => [
                        ['value' => '',         'label' => 'Semua Status'],
                        ['value' => 'Pending',  'label' => 'Pending'],
                        ['value' => 'Accepted', 'label' => 'Accepted'],
                        ['value' => 'Rejected', 'label' => 'Rejected'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Tabel Submission --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Submission</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $submissions->total() }} submission</p>
                </div>
            </div>

            @if($submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Peserta</th>
                                <th class="px-4 py-3">Tugas</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3">Notes</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($submissions as $index => $submission)
                                @php
                                    $badgeClass = match($submission['status']) {
                                        'Pending'  => 'bg-orange-100 text-[#FF4D00]',
                                        'Accepted' => 'bg-green-100 text-[#10AF13]',
                                        'Rejected' => 'bg-red-100 text-red-600',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $submissions->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['user_name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $submission['user_email'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['task_title'] }}</div>
                                        <div class="text-xs text-gray-400 line-clamp-1">{{ Str::limit($submission['task_description'], 40) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['batch_title'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $submission['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $submission['submitted_at'] }}</td>
                                    <td class="px-4 py-3 max-w-[140px]">
                                        <div class="text-xs text-gray-500 line-clamp-2">{{ $submission['notes'] ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $submission['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="openReviewModal({{ json_encode($submission) }})"
                                                title="Review Submission"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0059FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 15l2 2l4 -4" />
                                            </svg>
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <x-pagination :paginator="$submissions" />
                </div>

            @else
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 15l2 2l4 -4" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada submission</p>
                    <p class="text-sm mt-1 text-gray-400">
                        @if(request('batch_id') || request('status') || request('search'))
                            Tidak ada submission yang sesuai filter
                        @else
                            Belum ada tugas yang dikumpulkan peserta
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL REVIEW                                                  --}}
    {{-- ============================================================ --}}
    <div x-show="openSubmission"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
        <div @click.outside="openSubmission = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between rounded-t-2xl flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold">Review Submission</h2>
                    <p class="text-sm opacity-90">Beri penilaian dan feedback untuk peserta</p>
                </div>
                <button @click="openSubmission = false" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">

                {{-- Info --}}
                <div class="bg-gray-50 rounded-xl p-5 grid grid-cols-2 gap-y-4 gap-x-8 mb-5">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Peserta</p>
                        <p class="font-semibold text-gray-900" x-text="currentSubmission?.user_name"></p>
                        <p class="text-xs text-gray-400" x-text="currentSubmission?.user_email"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tugas</p>
                        <p class="font-semibold text-gray-900" x-text="currentSubmission?.task_title"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Batch</p>
                        <p class="font-medium text-gray-700" x-text="currentSubmission?.batch_title"></p>
                        <p class="text-xs text-gray-400" x-text="currentSubmission?.batch_code"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Submitted</p>
                        <p class="text-gray-700" x-text="currentSubmission?.submitted_at"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Notes dari Peserta</p>
                        <p class="text-sm text-gray-700" x-text="currentSubmission?.notes || '-'"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Status Saat Ini</p>
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full"
                              :class="{
                                  'bg-orange-100 text-[#FF4D00]': currentSubmission?.status === 'Pending',
                                  'bg-green-100 text-[#10AF13]':  currentSubmission?.status === 'Accepted',
                                  'bg-red-100 text-red-600':      currentSubmission?.status === 'Rejected'
                              }"
                              x-text="currentSubmission?.status">
                        </span>
                    </div>
                    <template x-if="currentSubmission?.feedback">
                        <div class="col-span-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Feedback Sebelumnya</p>
                            <p class="text-sm bg-white border rounded-lg p-3 text-gray-700" x-text="currentSubmission?.feedback"></p>
                        </div>
                    </template>
                </div>

                {{-- Download — pakai route yang sudah benar via x-bind:href --}}
                <a :href="currentSubmission ? makeUrl(downloadBaseUrl, currentSubmission.id) : '#'"
                   class="flex items-center justify-center gap-2 w-full py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" />
                    </svg>
                    Download File Submission
                </a>

                {{-- Form: action diisi JS sebelum submit --}}
                <form id="reviewForm" method="POST" action="">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Feedback untuk Peserta
                            <span class="text-red-400 text-xs font-normal ml-1">(wajib diisi jika menolak)</span>
                        </label>
                        <textarea name="feedback" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-1 focus:ring-[#10AF13] resize-none transition text-sm"
                                  placeholder="Berikan catatan atau feedback untuk peserta..."
                                  :value="currentSubmission?.feedback || ''"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="openSubmission = false"
                                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                            Tutup
                        </button>

                        <template x-if="currentSubmission?.status !== 'Rejected'">
                            <button type="button" @click="confirmReject()"
                                    class="flex items-center gap-2 px-5 py-2.5 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition font-medium text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                                </svg>
                                Tolak
                            </button>
                        </template>

                        <template x-if="currentSubmission?.status !== 'Accepted'">
                            <button type="button" @click="confirmAccept()"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium text-sm shadow-lg shadow-[#10AF13]/30">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Terima
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL KONFIRMASI TERIMA                                       --}}
    {{-- ============================================================ --}}
    <div x-show="openConfirmAccept"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-7 text-center">

            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                    stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2">Terima Submission?</h3>
            <p class="text-sm text-gray-500 mb-1">Anda akan menerima submission dari</p>
            <p class="font-semibold text-gray-800 mb-5" x-text="currentSubmission?.user_name"></p>

            <div class="flex gap-3">
                <button type="button" @click="openConfirmAccept = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                    Batal
                </button>
                <button type="button" @click="openConfirmAccept = false; submitAccept()"
                        class="flex-1 px-4 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold text-sm shadow-lg shadow-[#10AF13]/30">
                    Ya, Terima
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL KONFIRMASI TOLAK                                        --}}
    {{-- ============================================================ --}}
    <div x-show="openConfirmReject"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-7 text-center">

            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                    stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                    <path d="M12 9v4" /><path d="M12 16h.01" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2">Tolak Submission?</h3>
            <p class="text-sm text-gray-500 mb-1">Anda akan menolak submission dari</p>
            <p class="font-semibold text-gray-800 mb-2" x-text="currentSubmission?.user_name"></p>
            <p class="text-xs text-gray-400 mb-5">Peserta akan menerima feedback yang Anda tulis.</p>

            <div class="flex gap-3">
                <button type="button" @click="openConfirmReject = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                    Batal
                </button>
                <button type="button" @click="openConfirmReject = false; submitReject()"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm shadow-lg shadow-red-600/30">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

<style>[x-cloak] { display: none !important; }</style>
@endsection
{{-- resources/views/trainer/penilaian-tugas.blade.php --}}
@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Penilaian Tugas</h1>
        <p class="text-[#737373] mt-2 font-medium">Review dan beri penilaian submission peserta</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Pending',
            'value' => $stats['pending'] ?? 0,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        
        @include('dashboard.card', [
            'title' => 'Accepted',
            'value' => $stats['accepted'] ?? 0,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        
        @include('dashboard.card', [
            'title' => 'Rejected',
            'value' => $stats['rejected'] ?? 0,
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
            :action="route('trainer.penilaian-tugas')"
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
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'Pending', 'label' => 'Pending'],
                        ['value' => 'Accepted', 'label' => 'Accepted'],
                        ['value' => 'Rejected', 'label' => 'Rejected']
                    ]
                ]
            ]"
        />
    </div>

    {{-- Daftar Submission --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">Daftar Submission</h2>
            </div>
            
            @if($submissions && $submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full rounded-xl overflow-hidden">
                        <thead class="border-b">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Tugas</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3">Notes</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-50 transition text-left">
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['user_name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $submission['user_email'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['task_title'] }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-1">{{ Str::limit($submission['task_description'], 50) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $submission['batch_title'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $submission['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $submission['submitted_at'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-gray-600 line-clamp-2">
                                            {{ $submission['notes'] ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusConfig = [
                                                'Pending' => ['class' => 'bg-orange-100 text-[#FF4D00]', 'icon' => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" />'],
                                                'Accepted' => ['class' => 'bg-green-100 text-[#10AF13]', 'icon' => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" />'],
                                                'Rejected' => ['class' => 'bg-red-100 text-[#ff0000]', 'icon' => '<circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" />']
                                            ];
                                            $config = $statusConfig[$submission['status']] ?? $statusConfig['Pending'];
                                        @endphp
                                        
                                        <div class="px-2 py-1 w-fit text-xs font-medium rounded-full flex gap-2 items-center {{ $config['class'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" 
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                {!! $config['icon'] !!}
                                            </svg>
                                            <p>{{ $submission['status'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-4">
                                            {{-- Download Button --}}
                                            <a href="{{ route('trainer.submissions.download', $submission['id']) }}" 
                                               title="Download File"
                                               class="text-gray-600 hover:text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 11l5 5l5 -5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                            </a>
                                            
                                            {{-- Review Button --}}
                                            <button @click="openReviewModal({{ json_encode($submission) }})" 
                                                    title="Review Submission"
                                                    class="text-[#0059FF] hover:text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                    <path d="M9 15l2 2l4 -4" />
                                                </svg>
                                            </button>
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
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 15l2 2l4 -4" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada submission</p>
                    <p class="text-sm mt-1">Belum ada tugas yang dikumpulkan</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Review Submission --}}
    <div x-data="{ 
        openSubmission: false, 
        currentSubmission: null,
        openReviewModal(submission) {
            this.currentSubmission = submission;
            this.openSubmission = true;
        }
    }" x-cloak>
        <div x-show="openSubmission" x-transition class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
            <div @click.outside="openSubmission = false" class="bg-white max-w-2xl rounded-2xl shadow-lg p-8 relative max-h-[90vh] overflow-y-auto">
                {{-- Close Button --}}
                <button @click="openSubmission = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>

                {{-- Header --}}
                <h2 class="text-xl font-semibold">Review Submission</h2>
                <p class="text-[#737373] mb-6">Berikan penilaian dan feedback untuk tugas peserta</p>

                {{-- Submission Info --}}
                <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-4 gap-x-10 mb-6">
                    <div>
                        <p class="text-gray-700 text-sm font-medium">Peserta</p>
                        <p class="font-semibold" x-text="currentSubmission?.user_name"></p>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm font-medium">Tugas</p>
                        <p class="font-semibold" x-text="currentSubmission?.task_title"></p>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm font-medium">Batch</p>
                        <p class="font-semibold" x-text="currentSubmission?.batch_title"></p>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm font-medium">Submitted</p>
                        <p x-text="currentSubmission?.submitted_at"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-700 text-sm font-medium">Notes dari Peserta</p>
                        <p class="text-sm" x-text="currentSubmission?.notes || '-'"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-700 text-sm font-medium">Status Saat Ini</p>
                        <template x-if="currentSubmission?.status === 'Pending'">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-[#FF4D00]">
                                Pending
                            </span>
                        </template>
                        <template x-if="currentSubmission?.status === 'Accepted'">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-[#10AF13]">
                                Accepted
                            </span>
                        </template>
                        <template x-if="currentSubmission?.status === 'Rejected'">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-[#ff0000]">
                                Rejected
                            </span>
                        </template>
                    </div>
                    <template x-if="currentSubmission?.feedback">
                        <div class="col-span-2">
                            <p class="text-gray-700 text-sm font-medium">Feedback Sebelumnya</p>
                            <p class="text-sm bg-white p-3 rounded-lg mt-1" x-text="currentSubmission?.feedback"></p>
                        </div>
                    </template>
                </div>

                {{-- Download File --}}
                <a :href="currentSubmission ? '{{ route('trainer.submissions.download', '') }}/' + currentSubmission.id : '#'" 
                   class="bg-white rounded-lg border w-full mt-4 hover:bg-gray-50 block">
                    <div class="flex justify-center gap-3 py-3 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                        <p class="font-medium">Download File Submission</p>
                    </div>
                </a>

                {{-- Accept Form --}}
                <form :action="currentSubmission ? '{{ route('trainer.submissions.accept', '') }}/' + currentSubmission.id : '#'" 
                      method="POST" id="acceptForm" class="mt-6">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Feedback untuk Peserta</label>
                        <textarea name="feedback" rows="4" 
                                  class="w-full mt-2 px-3 py-2 bg-gray-100 border-none rounded-xl focus:ring-[#10AF13] focus:border-[#10AF13] resize-none" 
                                  placeholder="Berikan feedback..." 
                                  :value="currentSubmission?.feedback || ''"></textarea>
                    </div>
                    
                    <hr class="mt-6 mb-4">
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="openSubmission = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                            Tutup
                        </button>
                        
                        <template x-if="currentSubmission?.status !== 'Rejected'">
                            <button type="button" 
                                    @click="if(confirm('Yakin menolak submission ini?')) {
                                        const form = document.getElementById('acceptForm');
                                        form.action = form.action.replace('/accept/', '/reject/');
                                        form.submit();
                                    }"
                                    class="flex items-center gap-2 px-4 py-2 border border-red-300 text-[#ff0000] rounded-lg hover:bg-red-50 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M10 10l4 4m0 -4l-4 4" />
                                </svg>
                                Tolak
                            </button>
                        </template>
                        
                        <template x-if="currentSubmission?.status !== 'Accepted'">
                            <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M9 12l2 2l4 -4" />
                                </svg>
                                Terima
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif

    @if(session('error'))
        <x-notification type="error">{{ session('error') }}</x-notification>
    @endif
@endsection
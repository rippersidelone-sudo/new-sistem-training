{{-- resources/views/trainer/tugas/kelola-tugas.blade.php --}}
@extends('layouts.trainer')

@section('content')
<div x-data="kelolaTugasData()" x-cloak>

    <div class="px-2 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Kelola Tugas</h1>
            <p class="text-[#737373] mt-2 font-medium">Buat dan kelola tugas untuk batch pelatihan</p>
        </div>
        <button @click="openCreateModal = true"
            class="flex items-center gap-2 px-5 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5l0 14" /><path d="M5 12l14 0" />
            </svg>
            <span>Buat Tugas Baru</span>
        </button>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Tugas',
            'value' => $stats['total'],
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Tugas Aktif',
            'value' => $stats['active'],
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Overdue',
            'value' => $stats['overdue'],
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('trainer.kelola-tugas')"
            searchPlaceholder="Cari judul tugas..."
            :filters="[
                [
                    'name'        => 'batch_id',
                    'placeholder' => 'Semua Batch',
                    'options'     => collect($batches)->map(fn($b) => [
                        'value' => $b['id'],
                        'label' => $b['label'],
                    ])->prepend(['value' => '', 'label' => 'Semua Batch'])->toArray()
                ],
                [
                    'name'        => 'status',
                    'placeholder' => 'Semua Status',
                    'options'     => [
                        ['value' => '',        'label' => 'Semua Status'],
                        ['value' => 'active',  'label' => 'Aktif'],
                        ['value' => 'overdue', 'label' => 'Overdue'],
                    ]
                ],
            ]"
        />
    </div>

    {{-- Tabel Tugas --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Tugas</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $tasks->count() }} tugas</p>
                </div>
            </div>

            @if($tasks->count() > 0)
                @php
                    $perPage     = 10;
                    $currentPage = (int) request()->get('page', 1);
                    $offset      = ($currentPage - 1) * $perPage;
                    $paginated   = $tasks->forPage($currentPage, $perPage);
                    $totalPages  = (int) ceil($tasks->count() / $perPage);
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Tugas</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Deadline</th>
                                <th class="px-4 py-3">Submission</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($paginated as $task)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">{{ $offset + $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $task['title'] }}</div>
                                        <div class="text-xs text-gray-400 line-clamp-1">{{ Str::limit($task['description'], 40) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $task['batch_title'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $task['batch_code'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $task['deadline_formatted'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                {{ $task['total_submissions'] }} total
                                            </span>
                                            @if($task['pending_submissions'] > 0)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-orange-100 text-orange-700">
                                                    {{ $task['pending_submissions'] }} pending
                                                </span>
                                            @endif
                                            @if($task['accepted_submissions'] > 0)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                                    {{ $task['accepted_submissions'] }} accepted
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($task['is_overdue'])
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">
                                                Overdue
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-[#10AF13]">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="editTask({{ json_encode($task) }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0059FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                                Edit
                                            </button>
                                            <button @click="deleteTask({{ $task['id'] }}, '{{ addslashes($task['title']) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($totalPages > 1)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 mt-4 border-t">
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-semibold text-gray-800">{{ $offset + 1 }}</span>–<span class="font-semibold text-gray-800">{{ min($offset + $perPage, $tasks->count()) }}</span>
                            dari <span class="font-semibold text-gray-800">{{ $tasks->count() }}</span> tugas
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
                    </svg>
                    <p class="text-lg font-medium">Belum ada tugas</p>
                    <p class="text-sm mt-1 text-gray-400">
                        @if(request('batch_id') || request('status') || request('search'))
                            Tidak ada tugas yang sesuai filter
                        @else
                            Klik "Buat Tugas Baru" untuk memulai
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    @include('trainer.tugas.create-task-modal', ['batches' => $batches])
    @include('trainer.tugas.edit-task-modal',   ['batches' => $batches])
    @include('trainer.tugas.delete-task-modal')

</div>{{-- end x-data --}}

@if(session('success'))
    <x-notification type="success">{{ session('success') }}</x-notification>
@endif
@if(session('error'))
    <x-notification type="error">{{ session('error') }}</x-notification>
@endif
@if(session('warning'))
    <x-notification type="warning">{{ session('warning') }}</x-notification>
@endif
@if($errors->any())
    <x-notification type="error">
        @foreach($errors->all() as $error){{ $error }}@if(!$loop->last)<br>@endif@endforeach
    </x-notification>
@endif

<style>[x-cloak] { display: none !important; }</style>

<script>
function kelolaTugasData() {
    return {
        openCreateModal: false,
        openEditModal:   false,
        openDeleteModal: false,
        currentTask:     null,
        deleteTaskId:    null,
        deleteTaskTitle: '',

        editTask(task) {
            this.currentTask   = task;
            this.openEditModal = true;
        },

        deleteTask(taskId, taskTitle) {
            this.deleteTaskId    = taskId;
            this.deleteTaskTitle = taskTitle;
            this.openDeleteModal = true;
        },

        confirmDelete() {
            if (this.deleteTaskId) {
                document.getElementById('delete-task-form-' + this.deleteTaskId).submit();
            }
        }
    }
}
</script>
@endsection
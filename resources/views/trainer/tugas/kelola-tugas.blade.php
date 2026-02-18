<!-- resources/views/trainer/tugas/kelola-tugas.blade.php -->
@extends('layouts.trainer')

@section('content')
    {{-- ✅ PINDAHKAN x-data ke wrapper paling atas --}}
    <div x-data="kelolaTugasData()" x-cloak>
        <div class="px-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">Kelola Tugas</h1>
                    <p class="text-[#737373] mt-2 font-medium">Buat dan kelola tugas untuk batch pelatihan</p>
                </div>
                
                {{-- Button Buat Tugas Baru --}}
                <button @click="openCreateModal = true"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    <span>Buat Tugas Baru</span>
                </button>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 px-2">
            @include('dashboard.card', [
                'title' => 'Total Tugas',
                'value' => $stats['total'],
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>',
                'color' => 'text-[#0059FF]'
            ])

            @include('dashboard.card', [
                'title' => 'Tugas Aktif',
                'value' => $stats['active'],
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M9 12l2 2l4 -4" /></svg>',
                'color' => 'text-[#10AF13]'
            ])

            @include('dashboard.card', [
                'title' => 'Overdue',
                'value' => $stats['overdue'],
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 7v5l3 3" /></svg>',
                'color' => 'text-[#FF4D00]'
            ])
        </div>

        {{-- Filters --}}
        <div class="mt-8 px-2">
            <div class="bg-white border rounded-2xl p-5 mb-6">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1">
                        
                        {{-- Search Input --}}
                        <form method="GET" action="{{ route('trainer.kelola-tugas') }}" class="flex items-center bg-[#F1F1F1] rounded-lg px-3 h-[42px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="text-[#737373]">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                                   placeholder="Cari judul tugas..." />
                            <input type="hidden" name="batch_id" value="{{ request('batch_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </form>

                        {{-- Batch Filter --}}
                        <form method="GET" action="{{ route('trainer.kelola-tugas') }}">
                            <select name="batch_id" 
                                    onchange="this.form.submit()"
                                    class="w-full h-[42px] px-3 py-2 rounded-lg border border-gray-300 cursor-pointer text-sm bg-white">
                                <option value="">Semua Batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch['id'] }}" {{ request('batch_id') == $batch['id'] ? 'selected' : '' }}>
                                        {{ $batch['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </form>

                        {{-- Status Filter --}}
                        <form method="GET" action="{{ route('trainer.kelola-tugas') }}">
                            <select name="status" 
                                    onchange="this.form.submit()"
                                    class="w-full h-[42px] px-3 py-2 rounded-lg border border-gray-300 cursor-pointer text-sm bg-white">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="batch_id" value="{{ request('batch_id') }}">
                        </form>
                    </div>

                    {{-- Reset Button --}}
                    @if(request('search') || request('batch_id') || request('status'))
                        <div class="flex items-center lg:w-auto">
                            <a href="{{ route('trainer.kelola-tugas') }}"
                               class="w-full lg:w-auto h-[42px] flex items-center justify-center gap-2 border border-gray-300 text-gray-700 bg-white rounded-lg px-4 text-sm font-medium hover:bg-gray-50 transition whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                </svg>
                                Reset
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tasks List --}}
            <div class="bg-white border rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold">Daftar Tugas</h2>
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $tasks->count() }}</span> tugas
                    </div>
                </div>

                @if($tasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <div class="border rounded-xl p-5 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ $task['title'] }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $task['batch_title'] }} • {{ $task['batch_code'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        @if($task['is_overdue'])
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Overdue
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $task['description'] }}</p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-6 text-sm text-gray-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <span>{{ $task['deadline_formatted'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                            </svg>
                                            <span>{{ $task['total_submissions'] }} submission(s)</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if($task['pending_submissions'] > 0)
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                {{ $task['pending_submissions'] }} pending
                                            </span>
                                        @endif
                                        @if($task['accepted_submissions'] > 0)
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                {{ $task['accepted_submissions'] }} accepted
                                            </span>
                                        @endif
                                        
                                        {{-- Action Buttons --}}
                                        <button @click="editTask({{ json_encode($task) }})"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        
                                        <button @click="deleteTask({{ $task['id'] }}, '{{ $task['title'] }}')"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                             stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        </svg>
                        <p class="text-lg font-medium">Belum ada tugas</p>
                        <p class="text-sm mt-1">Klik tombol "Buat Tugas Baru" untuk memulai</p>
                    </div>
                @endif
            </div>

            {{-- Modals --}}
            @include('trainer.tugas.create-task-modal', ['batches' => $batches])
            @include('trainer.tugas.edit-task-modal', ['batches' => $batches])
            @include('trainer.tugas.delete-task-modal')
        </div>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">
            {{ session('success') }}
        </x-notification>
    @endif

    @if(session('error'))
        <x-notification type="error">
            {{ session('error') }}
        </x-notification>
    @endif

    <style>
        [x-cloak] { 
            display: none !important; 
        }
    </style>

    <script>
        function kelolaTugasData() {
            return {
                openCreateModal: false,
                openEditModal: false,
                openDeleteModal: false,
                currentTask: null,
                deleteTaskId: null,
                deleteTaskTitle: '',
                
                editTask(task) {
                    this.currentTask = task;
                    this.openEditModal = true;
                },
                
                deleteTask(taskId, taskTitle) {
                    this.deleteTaskId = taskId;
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
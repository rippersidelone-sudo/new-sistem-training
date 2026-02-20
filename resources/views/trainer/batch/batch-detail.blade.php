<!-- resources/views/trainer/batch/batch-detail.blade.php -->
@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        {{-- Breadcrumb with Back Button --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('trainer.batches') }}" class="hover:text-[#10AF13]">My Batches</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 6l6 6l-6 6"/>
                </svg>
                <span class="text-gray-900 font-medium">Detail Batch</span>
            </div>
            
            {{-- Back Button --}}
            <a href="{{ route('trainer.batches') }}" 
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Header --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold">{{ $batch->title }}</h1>
                <p class="text-[#737373] mt-2 font-medium">{{ $batch->code }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-medium rounded-full uppercase {{ badgeStatus($batch->status) }}">
                {{ $batch->status }}
            </span>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $stats['total_participants'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])

        @include('dashboard.card', [
            'title' => 'Hadir',
            'value' => $stats['total_attended'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]'
        ])

        @include('dashboard.card', [
            'title' => 'Total Tugas',
            'value' => $stats['total_tasks'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#0059FF]'
        ])

        @include('dashboard.card', [
            'title' => 'Pending Review',
            'value' => $stats['pending_submissions'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FFE100]'
        ])

        @include('dashboard.card', [
            'title' => 'Selesai',
            'value' => $stats['completed_participants'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
    </div>

    {{-- Tabs --}}
    <div x-data="batchDetailData()" x-cloak class="mt-8 px-2">
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl">
            <button @click="tab = 'info'" :class="tab === 'info' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Info Batch
            </button>

            <button @click="tab = 'participants'" :class="tab === 'participants' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Peserta (<span x-text="filteredParticipants.length"></span>)
            </button>

            <button @click="tab = 'tasks'" :class="tab === 'tasks' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Tugas ({{ $stats['total_tasks'] }})
            </button>
        </div>

        {{-- Info Batch Tab --}}
        <div x-show="tab === 'info'" class="mt-6">
            <div class="bg-white border rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-5">Informasi Batch</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Kategori</p>
                        <p class="text-base font-semibold">{{ $batch->category->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Trainer</p>
                        <p class="text-base font-semibold">{{ $batch->trainer->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Jadwal</p>
                        <p class="text-base font-semibold">{{ formatDate($batch->start_date) }}</p>
                        <p class="text-sm text-gray-500">{{ $batch->start_date->format('H:i') }} - {{ $batch->end_date->format('H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Kuota Peserta</p>
                        <p class="text-base font-semibold">{{ $stats['total_participants'] }} / {{ $batch->max_quota }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-600 mb-1">Zoom Link</p>
                        <a href="{{ $batch->zoom_link }}" target="_blank" 
                           class="text-base font-semibold text-[#0059FF] hover:underline">
                            {{ $batch->zoom_link }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Participants Tab --}}
        <div x-show="tab === 'participants'" class="mt-6">
            {{-- Filter Bar --}}
            <div class="bg-white border rounded-2xl p-5 mb-6">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1">
                    
                    {{-- Search Input --}}
                    <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3 h-[42px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-[#737373]">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                        <input type="text" 
                               x-model="searchParticipant"
                               class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                               placeholder="Cari nama atau email..." />
                    </div>

                    {{-- Attendance Status Filter --}}
                    <div x-data="{ 
                        open: false, 
                        value: filterAttendance, 
                        label: filterAttendance === 'Approved' ? 'Hadir' : (filterAttendance === 'Not Approved' ? 'Tidak Hadir' : 'Status Kehadiran')
                    }" class="relative w-full">
                        
                        <button type="button" 
                                @click="open = !open"
                                :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                                class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                            <span x-text="label" class="truncate"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="flex-shrink-0 ml-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 9l6 6l6 -6" />
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95" 
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150" 
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">

                            <div @click="filterAttendance = ''; label = 'Status Kehadiran'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Status Kehadiran</span>
                                <svg x-show="!filterAttendance" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>

                            <div @click="filterAttendance = 'Approved'; label = 'Hadir'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Hadir</span>
                                <svg x-show="filterAttendance === 'Approved'" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>

                            <div @click="filterAttendance = 'Not Approved'; label = 'Tidak Hadir'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Tidak Hadir</span>
                                <svg x-show="filterAttendance === 'Not Approved'" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Completion Filter --}}
                    <div x-data="{ 
                        open: false, 
                        value: filterCompletion, 
                        label: filterCompletion === 'completed' ? 'Selesai' : (filterCompletion === 'incomplete' ? 'Belum Selesai' : 'Status Penyelesaian')
                    }" class="relative w-full">
                        
                        <button type="button" 
                                @click="open = !open"
                                :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                                class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                            <span x-text="label" class="truncate"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="flex-shrink-0 ml-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 9l6 6l6 -6" />
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95" 
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150" 
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">

                            <div @click="filterCompletion = ''; label = 'Status Penyelesaian'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Status Penyelesaian</span>
                                <svg x-show="!filterCompletion" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>

                            <div @click="filterCompletion = 'completed'; label = 'Selesai'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Selesai</span>
                                <svg x-show="filterCompletion === 'completed'" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>

                            <div @click="filterCompletion = 'incomplete'; label = 'Belum Selesai'; open = false"
                                class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                                <span>Belum Selesai</span>
                                <svg x-show="filterCompletion === 'incomplete'" 
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                    stroke="#10AF13" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Reset Button --}}
                    <div class="flex items-center lg:w-auto">
                        <button @click="resetFilters()"
                                x-show="searchParticipant || filterAttendance || filterCompletion"
                                class="w-full lg:w-auto h-[42px] flex items-center justify-center gap-2 border border-gray-300 text-gray-700 bg-white rounded-lg px-4 text-sm font-medium hover:bg-gray-50 transition whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" 
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Participants Table --}}
            <div class="bg-white border rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold">Daftar Peserta</h2>
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold" x-text="filteredParticipants.length"></span> dari {{ $participants->count() }} peserta
                    </div>
                </div>

                <div x-show="filteredParticipants.length > 0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b">
                                <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Cabang</th>
                                    <th class="px-4 py-3">Kehadiran</th>
                                    <th class="px-4 py-3">Tugas</th>
                                    <th class="px-4 py-3">Feedback</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                <template x-for="participant in filteredParticipants" :key="participant.email">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium" x-text="participant.name"></td>
                                        <td class="px-4 py-3 text-gray-600" x-text="participant.email"></td>
                                        <td class="px-4 py-3 text-gray-600" x-text="participant.branch"></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full"
                                                  :class="participant.attendance_status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                  x-text="participant.attendance_status"></span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600" x-text="participant.tasks_completed"></td>
                                        <td class="px-4 py-3">
                                            <svg x-show="participant.has_feedback" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                 stroke="currentColor" stroke-width="2" class="text-green-600">
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                            <svg x-show="!participant.has_feedback" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                 stroke="currentColor" stroke-width="2" class="text-gray-400">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M10 10l4 4m0 -4l-4 4" />
                                            </svg>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span x-show="participant.is_completed" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                            <span x-show="!participant.is_completed" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                                Belum Selesai
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredParticipants.length === 0" class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada peserta yang sesuai filter</p>
                    <button @click="resetFilters()" class="mt-4 text-[#10AF13] hover:underline font-medium">
                        Reset filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Tasks Tab --}}
        <div x-show="tab === 'tasks'" class="mt-6">
            @if($tasks->count() > 0)
                {{-- Ada Tugas - Tampilkan List --}}
                <div class="bg-white border rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold">Daftar Tugas</h2>
                        <a href="{{ route('trainer.kelola-tugas', ['batch_id' => $batch->id]) }}" 
                           class="flex items-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M9 12l.01 0" />
                                <path d="M13 12l2 0" />
                                <path d="M9 16l.01 0" />
                                <path d="M13 16l2 0" />
                            </svg>
                            Kelola Tugas
                        </a>
                    </div>

                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <div class="border rounded-xl p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $task['title'] }}</h3>
                                    @if($task['is_overdue'])
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 mb-3">{{ $task['description'] }}</p>

                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-4 text-gray-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <span>{{ $task['deadline'] }}</span>
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
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                {{ $task['pending_submissions'] }} pending
                                            </span>
                                        @endif
                                        @if($task['accepted_submissions'] > 0)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                {{ $task['accepted_submissions'] }} accepted
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Empty State - Belum Ada Tugas --}}
                <div class="bg-white border rounded-2xl p-12">
                    <div class="max-w-md mx-auto text-center">
                        {{-- Icon --}}
                        <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="1.5" class="text-gray-400">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M9 12l.01 0" />
                                <path d="M13 12l2 0" />
                                <path d="M9 16l.01 0" />
                                <path d="M13 16l2 0" />
                            </svg>
                        </div>

                        {{-- Heading --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Tugas</h3>
                        
                        {{-- Description --}}
                        <p class="text-gray-600 mb-6">
                            Batch ini belum memiliki tugas untuk peserta. Buat tugas pertama untuk memulai pembelajaran dan evaluasi peserta.
                        </p>

                        {{-- Info Box --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                            <div class="flex gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                     stroke="currentColor" stroke-width="2" class="text-blue-600 flex-shrink-0 mt-0.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Tips:</p>
                                    <p>Tugas membantu Anda mengevaluasi pemahaman peserta dan memberikan feedback yang konstruktif untuk pembelajaran mereka.</p>
                                </div>
                            </div>
                        </div>

                        {{-- CTA Button --}}
                        <a href="{{ route('trainer.kelola-tugas', ['batch_id' => $batch->id]) }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            <span>Buat Tugas Pertama</span>
                        </a>

                        {{-- Secondary Link --}}
                        <p class="text-sm text-gray-500 mt-4">
                            Atau 
                            <a href="{{ route('trainer.kelola-tugas') }}" class="text-[#10AF13] hover:underline font-medium">
                                lihat semua tugas
                            </a>
                        </p>
                    </div>
                </div>
            @endif
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

        .max-h-60::-webkit-scrollbar {
            width: 6px;
        }

        .max-h-60::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .max-h-60::-webkit-scrollbar-thumb {
            background: #10AF13;
            border-radius: 10px;
        }

        .max-h-60::-webkit-scrollbar-thumb:hover {
            background: #0e8e0f;
        }
    </style>

    <script>
        function batchDetailData() {
            return {
                tab: 'info',
                searchParticipant: '',
                filterAttendance: '',
                filterCompletion: '',
                participantsData: @json($participants),
                
                get filteredParticipants() {
                    let filtered = [...this.participantsData];
                    
                    if (this.searchParticipant) {
                        const search = this.searchParticipant.toLowerCase();
                        filtered = filtered.filter(p => 
                            p.name.toLowerCase().includes(search) ||
                            p.email.toLowerCase().includes(search)
                        );
                    }
                    
                    if (this.filterAttendance) {
                        filtered = filtered.filter(p => p.attendance_status === this.filterAttendance);
                    }
                    
                    if (this.filterCompletion === 'completed') {
                        filtered = filtered.filter(p => p.is_completed === true);
                    } else if (this.filterCompletion === 'incomplete') {
                        filtered = filtered.filter(p => p.is_completed === false);
                    }
                    
                    return filtered;
                },
                
                resetFilters() {
                    this.searchParticipant = '';
                    this.filterAttendance = '';
                    this.filterCompletion = '';
                }
            }
        }
    </script>
@endsection
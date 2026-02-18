{{-- resources/views/participant/pelatihan.blade.php --}}
@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Pelatihan Saya</h1>
        <p class="text-[#737373] mt-2 font-medium">Daftar pelatihan yang Anda ikuti</p>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('participant.pelatihan')"
            searchPlaceholder="Cari pelatihan..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'Scheduled', 'label' => 'Scheduled'],
                        ['value' => 'Ongoing', 'label' => 'Ongoing'],
                        ['value' => 'Completed', 'label' => 'Completed'],
                    ]
                ],
                [
                    'name' => 'registration_status',
                    'placeholder' => 'Status Pendaftaran',
                    'options' => [
                        ['value' => '', 'label' => 'Semua'],
                        ['value' => 'Approved', 'label' => 'Approved'],
                        ['value' => 'Pending', 'label' => 'Pending'],
                        ['value' => 'Rejected', 'label' => 'Rejected'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Batch Cards --}}
    @if($batches->isEmpty())
        <div class="grid grid-cols-1 border gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
            <p class="text-lg font-medium text-gray-500 pt-8 flex justify-center">
                Anda belum terdaftar di pelatihan manapun
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2" x-data="{ trainingDetail: false, selectedBatch: null }">
            @foreach($batches as $batch)
                <div class="bg-white border rounded-2xl p-6 flex flex-col h-auto hover:shadow-md transition">
                    {{-- Title --}}
                    <div class="flex justify-between items-start">
                        <h1 class="text-black font-medium text-xl">
                            {{ $batch->title }}
                        </h1>
                    </div>

                    {{-- Status Section --}}
                    <div class="flex justify-between mt-4">
                        <div class="text-gray-600 gap-2 items-center">
                            <h2 class="text-md font-medium">Status Pelatihan</h2>
                            <div class="px-3 py-1 w-fit uppercase rounded-full {{ badgeStatus($batch->status) }}">
                                <p class="text-xs font-medium">{{ $batch->status }}</p>
                            </div>
                        </div>
                        <div class="text-gray-600 gap-2 items-center">
                            <h2 class="text-md font-medium">Status Pendaftaran</h2>
                            <div class="px-3 py-1 w-fit uppercase rounded-full 
                                {{ $batch->registration_status === 'Approved' ? 'bg-green-100 text-[#10AF13]' : 
                                   ($batch->registration_status === 'Pending' ? 'bg-orange-100 text-[#FF4D00]' : 'bg-red-100 text-red-700') }}">
                                <p class="text-xs font-medium">{{ $batch->registration_status }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Batch Details --}}
                    <div class="mt-7 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                        </svg>
                        <p class="text-md font-semibold">
                            {{ $batch->category->name }}
                        </p>
                    </div>

                    <div class="mt-2 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="20"
                            height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                        <p class="text-md font-semibold">
                            {{ formatDate($batch->start_date) }}
                        </p>
                    </div>

                    <div class="mt-2 flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-9">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12h-3.5" />
                            <path d="M12 7v5" />
                        </svg>
                        <p class="text-md font-semibold">
                            {{ $batch->start_date->format('H:i') }} - {{ $batch->end_date->format('H:i') }}
                        </p>
                    </div>

                    <hr class="border-gray-200 mt-4">

                    {{-- Statistics --}}
                    <div class="flex items-start gap-20 mt-3">
                        <div>
                            <h2 class="text-md font-medium text-gray-600">Materi</h2>
                            <p class="text-md font-semibold text-black">{{ $batch->materials_count }}</p>
                        </div>
                        <div>
                            <h2 class="text-md font-medium text-gray-600">Tugas</h2>
                            <p class="text-md font-semibold text-black">{{ $batch->tasks_count }}</p>
                        </div>
                    </div>

                    {{-- Attendance Status --}}
                    <div class="mt-4 text-gray-600 gap-2 items-center">
                        <h2 class="text-md font-medium">Kehadiran</h2>
                        <div class="px-3 py-1 w-fit text-xs uppercase font-medium rounded-full 
                            {{ $batch->attendance_status === 'Present' ? 'bg-green-100 text-[#10AF13]' : 
                               ($batch->attendance_status === 'Pending' ? 'bg-orange-100 text-[#FF4D00]' : 'bg-gray-100 text-gray-700') }}">
                            {{ $batch->attendance_status }}
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="gap-2 mt-6">
                        <button 
                            @click="trainingDetail = true; selectedBatch = {{ $batch->id }}"
                            class="w-full px-4 py-2 border rounded-lg flex justify-center items-center gap-3 hover:bg-gray-100 font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                            </svg>
                            <p class="text-md text-black">Lihat Detail</p>
                        </button>
                    </div>
                </div>
            @endforeach

            {{-- Modal Training Detail --}}
            <div x-show="trainingDetail" 
                 x-cloak 
                 x-transition 
                 class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                <div @click.outside="trainingDetail = false"
                    class="bg-white w-full max-w-2xl rounded-2xl shadow-lg p-8 relative max-h-[90vh] overflow-y-auto">
                    
                    {{-- Close Button --}}
                    <button @click="trainingDetail = false"
                        class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>

                    @foreach($batches as $batch)
                        <div x-show="selectedBatch === {{ $batch->id }}">
                            {{-- Header --}}
                            <h2 class="text-xl font-semibold">Detail Pelatihan</h2>
                            <p class="text-[#737373] mb-4">Informasi lengkap tentang pelatihan Anda</p>

                            <div x-data="{ tab: 'info-pelatihan' }" x-cloak>
                                {{-- Tabs --}}
                                <div class="flex bg-[#eaeaea] p-1 rounded-2xl mb-5">
                                    <button @click="tab = 'info-pelatihan'" 
                                        :class="tab === 'info-pelatihan' ? 'bg-white' : ''"
                                        class="w-full py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                                        Info
                                    </button>
                                    <button @click="tab = 'materi-pelatihan'" 
                                        :class="tab === 'materi-pelatihan' ? 'bg-white' : ''"
                                        class="w-full py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                                        Materi
                                    </button>
                                    <button @click="tab = 'tugas-pelatihan'" 
                                        :class="tab === 'tugas-pelatihan' ? 'bg-white' : ''"
                                        class="w-full py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                                        Tugas
                                    </button>
                                    <button @click="tab = 'jadwal-pelatihan'" 
                                        :class="tab === 'jadwal-pelatihan' ? 'bg-white' : ''"
                                        class="w-full py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                                        Jadwal
                                    </button>
                                </div>

                                {{-- Tab: Info Pelatihan --}}
                                <div x-show="tab === 'info-pelatihan'">
                                    <div class="bg-gray-50 rounded-xl p-6 grid lg:grid-cols-2 gap-y-4 gap-x-10">
                                        <div class="col-span-2">
                                            <div class="mb-4">
                                                <h2 class="text-black text-xl font-semibold">{{ $batch->title }}</h2>
                                                <p class="text-gray-700 text-md font-medium">{{ formatBatchCode($batch->id, $batch->start_date->year) }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-gray-700 text-md font-medium">Kategori</p>
                                            <p class="text-black text-md font-medium">{{ $batch->category->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-700 text-md font-medium">Trainer</p>
                                            <p class="text-black text-md font-medium">{{ $batch->trainer->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-700 text-md font-medium">Tanggal Daftar</p>
                                            <p class="text-black text-md font-medium">{{ formatDate($batch->registered_at) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-700 text-md font-medium">Status</p>
                                            <span class="inline-block px-4 py-1 text-xs font-medium rounded-full {{ badgeStatus($batch->status) }}">
                                                {{ strtoupper($batch->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-gray-700 text-md font-medium">Status Kehadiran</p>
                                            <span class="inline-block px-4 py-1 text-xs font-medium uppercase rounded-full 
                                                {{ $batch->attendance_status === 'Present' ? 'bg-green-100 text-[#10AF13]' : 'bg-orange-100 text-[#FF4D00]' }}">
                                                {{ $batch->attendance_status }}
                                            </span>
                                        </div>
                                        @if($batch->zoom_link)
                                        <div class="col-span-2">
                                            <p class="text-gray-700 text-md font-medium">Link Zoom</p>
                                            <a href="{{ $batch->zoom_link }}" target="_blank"
                                                class="py-1 text-md font-medium text-[#0059FF] hover:underline">
                                                {{ $batch->zoom_link }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tab: Materi Pelatihan --}}
                                <div x-show="tab === 'materi-pelatihan'">
                                    @if($batch->materials->isEmpty())
                                        <p class="text-center text-gray-500 py-8">Belum ada materi yang diupload</p>
                                    @else
                                        <div class="mt-5 space-y-4">
                                            @foreach($batch->materials as $material)
                                            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-{{ $material->type === 'pdf' ? '[#FF4D00]' : '[#0059FF]' }}">
                                                        {!! $material->type_icon !!}
                                                    </div>
                                                    <div>
                                                        <h3 class="text-md font-medium text-gray-800">{{ $material->title }}</h3>
                                                        <p class="text-md text-[#737373] flex flex-wrap gap-2 font-medium">
                                                            <span class="{{ $material->type_badge }} px-3 rounded-lg text-sm flex items-center">
                                                                {{ strtoupper($material->type) }}
                                                            </span>
                                                            <span>•</span>
                                                            <span>{{ formatDate($material->created_at) }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <a href="{{ $material->url }}" target="_blank"
                                                        class="text-black hover:bg-gray-200 border p-2 rounded-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                            <path d="M7 11l5 5l5 -5" />
                                                            <path d="M12 4l0 12" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Tab: Tugas Pelatihan --}}
                                <div x-show="tab === 'tugas-pelatihan'">
                                    @if($batch->tasks->isEmpty())
                                        <p class="text-center text-gray-500 py-8">Belum ada tugas</p>
                                    @else
                                        <div class="mt-5 space-y-4">
                                            @foreach($batch->tasks as $task)
                                            <div class="p-4 border rounded-xl hover:bg-gray-50 transition">
                                                <div class="flex justify-between items-start">
                                                    <h3 class="text-lg font-medium text-black">{{ $task->title }}</h3>
                                                    @if($task->submission_status !== 'Not Submitted')
                                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                                            {{ $task->submission_status === 'Accepted' ? 'bg-green-100 text-[#10AF13]' : 
                                                               ($task->submission_status === 'Pending' ? 'bg-orange-100 text-[#FF4D00]' : 'bg-red-100 text-red-700') }}">
                                                            {{ $task->submission_status }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-md text-gray-700 font-medium mt-2">{{ $task->description }}</p>
                                                <div class="flex gap-2 mt-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-700" width="20" height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                    </svg>
                                                    <p class="text-md text-gray-700 font-medium">Deadline: {{ formatDate($task->deadline) }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Tab: Jadwal Pelatihan (VERSI BARU & LENGKAP) --}}
                                <div x-show="tab === 'jadwal-pelatihan'">
                                    @if($batch->sessions->isEmpty())
                                        {{-- Fallback untuk batch lama yang belum punya data sessions --}}
                                        <div class="mt-5 space-y-4">
                                            <div class="p-5 border rounded-xl bg-gray-50">
                                                <div class="flex items-start gap-3 mb-4">
                                                    <div class="p-2 bg-[#10AF13]/10 rounded-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-black">Sesi Pelatihan</h3>
                                                        <p class="text-sm text-gray-600">{{ formatDate($batch->start_date) }} - {{ formatDate($batch->end_date) }}</p>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <div class="flex items-center gap-3 text-gray-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                            <path d="M12 12h-3.5M12 7v5" />
                                                        </svg>
                                                        <p class="text-sm font-medium">
                                                            {{ $batch->start_date->format('H:i') }} - {{ $batch->end_date->format('H:i') }}
                                                        </p>
                                                    </div>

                                                    <div class="flex items-center gap-3 text-gray-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                                                            <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                                        </svg>
                                                        <p class="text-sm font-medium">{{ $batch->trainer->name }}</p>
                                                    </div>

                                                    @if($batch->zoom_link)
                                                        <div class="mt-4 pt-4 border-t">
                                                            <a href="{{ $batch->zoom_link }}" target="_blank" 
                                                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0059FF] text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                                                                    <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                                                                </svg>
                                                                Join Zoom Meeting
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Menampilkan daftar session (multi sesi) --}}
                                        <div class="mt-5 space-y-4">
                                            @foreach($batch->sessions->sortBy('session_number') as $session)
                                                <div class="p-5 border rounded-xl hover:bg-gray-50 transition 
                                                    {{ $session->isToday() ? 'border-[#10AF13] bg-[#10AF13]/5' : '' }} 
                                                    {{ $session->hasEnded() ? 'opacity-75' : '' }}">
                                                    
                                                    {{-- Header sesi --}}
                                                    <div class="flex items-start justify-between mb-4">
                                                        <div class="flex items-start gap-3">
                                                            <div class="p-2 {{ $session->isToday() ? 'bg-[#10AF13] text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg font-bold text-sm min-w-[70px] text-center">
                                                                Hari {{ $session->session_number }}
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-black">
                                                                    @if($session->title)
                                                                        {{ $session->title }}
                                                                    @else
                                                                        Sesi {{ $session->session_number }}
                                                                    @endif
                                                                </h3>
                                                                <p class="text-sm text-gray-600 font-medium mt-1">
                                                                    {{ formatDate($session->start_datetime, 'l, d M Y') }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        @if($session->isToday())
                                                            <span class="px-3 py-1 bg-[#10AF13] text-white text-xs font-semibold rounded-full animate-pulse">
                                                                HARI INI
                                                            </span>
                                                        @elseif($session->hasEnded())
                                                            <span class="px-3 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                                                SELESAI
                                                            </span>
                                                        @elseif($session->hasStarted())
                                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                                ONGOING
                                                            </span>
                                                        @else
                                                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                                                                AKAN DATANG
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Detail sesi --}}
                                                    <div class="space-y-3 ml-11 md:ml-0 md:pl-20">
                                                        <div class="flex items-center gap-3 text-gray-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                                <path d="M12 12h-3.5M12 7v5" />
                                                            </svg>
                                                            <p class="text-sm font-medium">
                                                                {{ $session->start_datetime->format('H:i') }} - {{ $session->end_datetime->format('H:i') }}
                                                                <span class="text-gray-500 ml-1">({{ $session->getDurationInHours() }} jam)</span>
                                                            </p>
                                                        </div>

                                                        <div class="flex items-center gap-3 text-gray-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                                                                <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                                            </svg>
                                                            <p class="text-sm font-medium">{{ $session->trainer->name }}</p>
                                                        </div>

                                                        @if($session->zoom_link || $batch->zoom_link)
                                                            <div class="mt-4 pt-3 border-t">
                                                                <a href="{{ $session->zoom_link ?? $batch->zoom_link }}" target="_blank"
                                                                   class="inline-flex items-center gap-2 px-4 py-2.5 
                                                                   {{ $session->isToday() ? 'bg-[#10AF13] hover:bg-[#0e8e0f]' : 'bg-[#0059FF] hover:bg-blue-700' }} 
                                                                   text-white rounded-lg transition font-medium text-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                                                                        <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                                                                    </svg>
                                                                    Join Zoom Meeting
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Ringkasan di bawah --}}
                                            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div class="flex items-start gap-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600 flex-shrink-0 mt-0.5">
                                                        <circle cx="12" cy="12" r="10"/>
                                                        <path d="M12 16v-4"/>
                                                        <path d="M12 8h.01"/>
                                                    </svg>
                                                    <div class="text-sm">
                                                        <p class="font-semibold text-blue-900">
                                                            Total {{ $batch->sessions->count() }} Hari Pelatihan
                                                        </p>
                                                        <p class="text-blue-700 mt-1">
                                                            Durasi: {{ $batch->getTotalDurationInHours() }} jam total
                                                        </p>
                                                        @if($batch->getAllTrainers()->count() > 1)
                                                            <p class="text-blue-700">
                                                                {{ $batch->getAllTrainers()->count() }} Trainer: 
                                                                {{ $batch->getAllTrainers()->pluck('name')->join(', ') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Notifications --}}
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
            @foreach($errors->all() as $error)
                {{ $error }}@if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection
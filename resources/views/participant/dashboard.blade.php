{{-- resources/views/participant/dashboard.blade.php --}}
@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Participant</h1>
        <p class="text-[#737373] mt-2 font-medium">Selamat datang, {{ Auth::user()->name }}</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Batch',
            'value' => $totalBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color' => 'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => $ongoingBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => $completedBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Sertifikat',
            'value' => $certificatesCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color' => 'text-[#D4AF37]'
        ])
    </div>

    {{-- Latest Batch Section --}}
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-5">
            Batch Saya
        </h2>

        @if($latestBatch)
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">
                            {{ $latestBatch->title }}
                        </h3>
                        <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                            <span>{{ $latestBatch->start_date->format('d/m/Y') }}</span>
                            <span>•</span>
                            <span>Kehadiran: {{ $attendanceStatus }}</span>
                        </p>
                    </div>

                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ badgeStatus($latestBatch->status) }}">
                        {{ strtoupper($latestBatch->status) }}
                    </span>
                </div>

                {{-- Quick Actions --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                    <a href="{{ route('participant.pelatihan') }}" 
                       class="flex items-center justify-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        <span class="text-sm font-medium">Lihat Pelatihan</span>
                    </a>

                    <a href="{{ route('participant.absensi') }}" 
                       class="flex items-center justify-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                            <path d="M9 12l.01 0M13 12l2 0M9 16l.01 0M13 16l2 0" />
                        </svg>
                        <span class="text-sm font-medium">Check-in</span>
                    </a>

                    <a href="{{ route('participant.tugas') }}" 
                       class="flex items-center justify-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 9l1 0M9 13l6 0M9 17l6 0" />
                        </svg>
                        <span class="text-sm font-medium">Tugas</span>
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-gray-300 mb-4">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <p class="text-lg font-medium text-gray-500">Belum ada batch yang diikuti</p>
                <p class="text-sm text-gray-400 mt-1">Silakan daftar batch pelatihan terlebih dahulu</p>
                <a href="{{ route('participant.pendaftaran') }}" 
                   class="inline-flex items-center gap-2 mt-4 px-6 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <span class="font-medium">Daftar Batch</span>
                </a>
            </div>
        @endif
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

    @if(session('warning'))
        <x-notification type="warning">
            {{ session('warning') }}
        </x-notification>
    @endif
@endsection
<!-- resources/views/trainer/dashboard.blade.php -->
@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Trainer</h1>
        <p class="text-[#737373] mt-2 font-medium">Selamat datang, {{ $trainer->name }}</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Batch',
            'value' => $stats['total_batches'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color' => 'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title' => 'Total Peserta',
            'value' => $stats['total_participants'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Pending Grading',
            'value' => $stats['pending_grading'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FFE100]'
        ])
        @include('dashboard.card', [
            'title' => 'Materials',
            'value' => $stats['total_materials'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
    </div>

    {{-- Recent Batches --}}
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-5">
            Batch Saya
        </h2>

        @if($recentBatches->count() > 0)
            <div class="space-y-4">
                @foreach($recentBatches as $batch)
                    <a href="{{ route('trainer.batches.show', $batch['id']) }}" 
                       class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                        <div>
                            <h3 class="text-md font-semibold text-gray-800">
                                {{ $batch['title'] }}
                            </h3>
                            <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                                <span>{{ $batch['code'] }}</span>
                                <span>•</span>
                                <span>{{ $batch['date'] }}</span>
                                <span>•</span>
                                <span>{{ $batch['participants_count'] }} peserta</span>
                            </p>
                        </div>

                        <span class="px-3 py-1 text-sm uppercase font-medium rounded-full {{ badgeStatus($batch['status']) }}">
                            {{ $batch['status'] }}
                        </span>
                    </a>
                @endforeach
            </div>
            
            @if($stats['total_batches'] > 3)
                <div class="mt-4 text-center">
                    <a href="{{ route('trainer.batches') }}" 
                       class="text-[#10AF13] hover:text-[#0e8e0f] font-medium text-sm inline-flex items-center gap-1">
                        Lihat Semua Batch 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <p class="text-lg font-medium text-gray-600">Belum ada batch yang ditugaskan</p>
                <p class="mt-2 text-sm">Batch akan muncul di sini setelah coordinator menugaskan Anda</p>
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

    @if(session('info'))
        <x-notification type="info">
            {{ session('info') }}
        </x-notification>
    @endif
@endsection
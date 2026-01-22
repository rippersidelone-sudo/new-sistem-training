<!-- resources/views/trainer/batch/batches.blade.php -->
@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">My Batches</h1>
        <p class="text-[#737373] mt-2 font-medium">Batch pelatihan yang diampu</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Scheduled',
            'value' => $statusCounts['scheduled'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" />
                        <path d="M8 3v4" /><path d="M4 11h16" /></svg>',
            'color' => 'text-[#0059FF]',
        ])
        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => $statusCounts['ongoing'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                    viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]',
        ])
        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => $statusCounts['completed'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                        fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                        <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                        <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]',
        ])
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'scheduled' }" x-cloak>
        <div class="flex bg-[#eaeaea] p-1 rounded-2xl mt-8 mx-2">
            <button @click="tab = 'scheduled'" :class="tab === 'scheduled' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Scheduled ({{ $statusCounts['scheduled'] }})
            </button>

            <button @click="tab = 'ongoing'" :class="tab === 'ongoing' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Ongoing ({{ $statusCounts['ongoing'] }})
            </button>

            <button @click="tab = 'completed'" :class="tab === 'completed' ? 'bg-white' : ''"
                class="flex-1 text-center py-2 rounded-full text-sm font-semibold hover:bg-white transition">
                Completed ({{ $statusCounts['completed'] }})
            </button>
        </div>

        {{-- Scheduled Batches --}}
        <div x-show="tab === 'scheduled'">
            @if($scheduledBatches->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2 auto-rows-fr">
                    @foreach($scheduledBatches as $batch)
                        @include('trainer.batch.batch-card', ['batch' => $batch])
                    @endforeach
                </div>
            @else
                <div class="bg-white border rounded-2xl p-12 mt-8 mx-2 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                        <path d="M16 3v4" />
                        <path d="M8 3v4" />
                        <path d="M4 11h16" />
                    </svg>
                    <p class="text-lg font-medium text-gray-600">Tidak ada batch scheduled</p>
                </div>
            @endif
        </div>

        {{-- Ongoing Batches --}}
        <div x-show="tab === 'ongoing'">
            @if($ongoingBatches->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2 auto-rows-fr">
                    @foreach($ongoingBatches as $batch)
                        @include('trainer.batch.batch-card', ['batch' => $batch])
                    @endforeach
                </div>
            @else
                <div class="bg-white border rounded-2xl p-12 mt-8 mx-2 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path d="M12 3 a9 9 0 1 0 9 9" />
                    </svg>
                    <p class="text-lg font-medium text-gray-600">Tidak ada batch ongoing</p>
                </div>
            @endif
        </div>

        {{-- Completed Batches --}}
        <div x-show="tab === 'completed'">
            @if($completedBatches->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2 auto-rows-fr">
                    @foreach($completedBatches as $batch)
                        @include('trainer.batch.batch-card', ['batch' => $batch])
                    @endforeach
                </div>
            @else
                <div class="bg-white border rounded-2xl p-12 mt-8 mx-2 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" />
                        <path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p class="text-lg font-medium text-gray-600">Tidak ada batch completed</p>
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
@endsection
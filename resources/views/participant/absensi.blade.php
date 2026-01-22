{{-- resources/views/participant/absensi.blade.php --}}
@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Absensi Online</h1>
        <p class="text-[#737373] mt-2 font-medium">Check-in kehadiran untuk batch training Anda</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Validated',
            'value' => $validatedCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]',
        ])
        @include('dashboard.card', [
            'title' => 'Pending Approval',
            'value' => $pendingCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]',
        ])
        @include('dashboard.card', [
            'title' => 'Belum Check-In',
            'value' => $notCheckedInCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 9v4" /><path d="M12 16v.01" /></svg>',
            'color' => 'text-gray-700',
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('participant.absensi')"
            searchPlaceholder="Cari batch..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'can_checkin', 'label' => 'Bisa Check-In'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'validated', 'label' => 'Validated'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Batch List --}}
    @if($batches->isEmpty())
        <div class="grid grid-cols-1 border gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
            <p class="text-lg font-medium text-gray-500 pt-8 flex justify-center">
                Tidak ada batch yang perlu check-in
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
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
                        
                        @if($batch->today_attendance)
                            <div class="text-gray-600 gap-2 items-center">
                                <h2 class="text-md font-medium">Status Absensi</h2>
                                <div class="px-3 py-1 w-fit flex items-center gap-2 uppercase rounded-full 
                                    {{ $batch->attendance_status === 'Present' ? 'bg-green-100 text-[#10AF13]' : 'bg-orange-100 text-[#FF4D00]' }}">
                                    @if($batch->attendance_status === 'Pending')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M12 12l3 2" />
                                            <path d="M12 7v5" />
                                        </svg>
                                    @endif
                                    <p class="text-xs font-medium">{{ $batch->attendance_status }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Batch Details --}}
                    <div class="mt-6 flex gap-2 text-gray-600 items-center">
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

                    {{-- Action Buttons --}}
                    <div class="mt-5">
                        @if($batch->can_checkin)
                            {{-- Check-in Button --}}
                            <form action="{{ route('participant.absensi.checkin', $batch) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 rounded-lg flex justify-center items-center gap-3 bg-[#10AF13] hover:bg-[#0e8e0f] text-white font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    <span class="text-md capitalize">Check-in Sekarang</span>
                                </button>
                            </form>
                        @elseif($batch->today_attendance)
                            {{-- Already Checked In --}}
                            <div class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-blue-100 border border-blue-300 gap-2">
                                <p class="text-md capitalize text-[#0059FF]">
                                    Check-in: {{ formatDateTime($batch->checkin_time) }}
                                </p>
                            </div>
                            
                            @if($batch->attendance_status === 'Pending')
                                <div class="mt-3 w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start text-[#FF4D00] bg-orange-100 border border-orange-300 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 12l3 2" />
                                        <path d="M12 7v5" />
                                    </svg>
                                    <p class="text-md">Menunggu Validasi dari Trainer</p>
                                </div>
                            @endif
                        @else
                            <div class="w-full px-4 py-2 font-medium rounded-lg text-center bg-gray-100 text-gray-600 border border-gray-300">
                                Belum Waktunya Check-in
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

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

    @if($errors->any())
        <x-notification type="error">
            @foreach($errors->all() as $error)
                {{ $error }}
                @if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection
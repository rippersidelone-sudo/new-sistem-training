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
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#10AF13]',
        ])
        @include('dashboard.card', [
            'title' => 'Menunggu Validasi',
            'value' => $pendingCount,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color' => 'text-[#FF4D00]',
        ])
        @include('dashboard.card', [
            'title' => 'Belum Check-In',
            'value' => $notCheckedInCount,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
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
                <div class="bg-white border rounded-2xl p-6 flex flex-col hover:shadow-md transition">

                    {{-- Title --}}
                    <h1 class="text-black font-semibold text-lg leading-snug">
                        {{ $batch->title }}
                    </h1>

                    {{-- Status Row --}}
                    <div class="flex flex-wrap gap-3 mt-3">
                        {{-- Status Pelatihan --}}
                        <div class="flex flex-col gap-1">
                            <span class="text-xs text-gray-500 font-medium">Status Pelatihan</span>
                            <div class="px-3 py-1 w-fit uppercase rounded-full {{ badgeStatus($batch->status) }}">
                                <p class="text-xs font-semibold">{{ $batch->status }}</p>
                            </div>
                        </div>

                        {{-- Status Absensi (jika sudah check-in) --}}
                        @if($batch->today_attendance)
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-gray-500 font-medium">Status Absensi</span>
                                <div class="px-3 py-1 w-fit flex items-center gap-1.5 uppercase rounded-full 
                                    {{ $batch->attendance_status === 'Approved' ? 'bg-green-100 text-[#10AF13]' : 'bg-orange-100 text-[#FF4D00]' }}">
                                    @if($batch->attendance_status === 'Checked-in')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M12 12l3 2" />
                                            <path d="M12 7v5" />
                                        </svg>
                                    @elseif($batch->attendance_status === 'Approved')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    @endif
                                    <p class="text-xs font-semibold">{{ $batch->attendance_status }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Batch Info --}}
                    <div class="mt-4 space-y-2">
                        <div class="flex gap-2 text-gray-600 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                <path d="M16 3v4M8 3v4M4 11h16" />
                            </svg>
                            <p class="text-sm font-semibold">{{ formatDate($batch->start_date) }}</p>
                        </div>

                        <div class="flex gap-2 text-gray-600 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 12h-3.5M12 7v5" />
                            </svg>
                            <p class="text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($batch->start_date)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($batch->end_date)->format('H:i') }}
                            </p>
                        </div>

                        <div class="flex gap-2 text-gray-600 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                                <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                                <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                            </svg>
                            <p class="text-sm font-semibold">{{ $batch->trainer->name }}</p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <hr class="border-gray-100 my-4">

                    {{-- Action Section --}}
                    <div class="mt-auto">
                        @if($batch->can_checkin)
                            {{-- ✅ Bisa Check-in --}}
                            <form action="{{ route('participant.absensi.checkin', $batch) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2.5 rounded-xl flex justify-center items-center gap-2 bg-[#10AF13] hover:bg-[#0e8e0f] text-white font-semibold text-sm transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    Check-in Sekarang
                                </button>
                            </form>

                        @elseif($batch->today_attendance)
                            {{-- ✅ Sudah Check-in --}}
                            <div class="space-y-2">
                                <div class="w-full px-4 py-2.5 font-medium rounded-xl flex items-center justify-center gap-2 bg-blue-50 border border-blue-200 text-[#0059FF] text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    {{-- ✅ Fix tampilan checkin_time (string time) --}}
                                    Check-in: {{ $batch->checkin_time ? \Carbon\Carbon::parse($batch->checkin_time)->format('H:i') : '-' }}
                                </div>

                                @if($batch->attendance_status === 'Checked-in')
                                    <div class="w-full px-4 py-2.5 font-medium rounded-xl flex items-center justify-center gap-2 text-[#FF4D00] bg-orange-50 border border-orange-200 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M12 12l3 2" />
                                            <path d="M12 7v5" />
                                        </svg>
                                        Menunggu Validasi Trainer
                                    </div>
                                @elseif($batch->attendance_status === 'Approved')
                                    <div class="w-full px-4 py-2.5 font-medium rounded-xl flex items-center justify-center gap-2 text-[#10AF13] bg-green-50 border border-green-200 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                        Kehadiran Divalidasi
                                    </div>
                                @endif
                            </div>

                        @else
                            {{-- Belum waktunya --}}
                            <div class="w-full px-4 py-2.5 font-medium rounded-xl text-center bg-gray-100 text-gray-500 border border-gray-200 text-sm">
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
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif
    @if(session('error'))
        <x-notification type="error">{{ session('error') }}</x-notification>
    @endif
    @if(session('warning'))
        <x-notification type="warning">{{ session('warning') }}</x-notification>
    @endif
    @if(session('info'))
        <x-notification type="info">{{ session('info') }}</x-notification>
    @endif
    @if($errors->any())
        <x-notification type="error">
            @foreach($errors->all() as $error)
                {{ $error }}@if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection
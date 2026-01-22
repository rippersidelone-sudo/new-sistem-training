{{-- resources/views/participant/pendaftaran.blade.php --}}
@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Pendaftaran Training</h1>
        <p class="text-[#737373] mt-2 font-medium">Daftar batch training yang tersedia</p>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('participant.pendaftaran')"
            searchPlaceholder="Cari batch, kategori, atau trainer..."
            :filters="[
                [
                    'name' => 'category',
                    'placeholder' => 'Semua Kategori',
                    'options' => $batches->pluck('category')->unique('id')->map(fn($cat) => [
                        'value' => $cat->id,
                        'label' => $cat->name
                    ])->values()->toArray()
                ],
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => 'scheduled', 'label' => 'Scheduled'],
                        ['value' => 'ongoing', 'label' => 'Ongoing'],
                        ['value' => 'completed', 'label' => 'Completed'],
                        ['value' => 'cancelled', 'label' => 'Cancelled'],
                    ]
                ],
            ]"
        />
    </div>

    {{-- Batch Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2" x-data="{ detailBatch: null }">
        @forelse($batches as $batch)
            <div class="bg-white border rounded-2xl p-6 flex flex-col hover:shadow-md transition">
                {{-- Header: Title & Status Badge --}}
                <div class="mb-4">
                    <h1 class="text-black font-medium text-xl mb-3">
                        {{ $batch->title }}
                    </h1>
                    <div class="px-3 py-1 text-xs font-medium rounded-full {{ badgeStatus($batch->status) }} inline-block">
                        <p class="uppercase">{{ $batch->status }}</p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-5">
                    <p class="text-md font-medium text-gray-600 line-clamp-2">
                        {{ $batch->category->description ?? 'Tidak ada deskripsi' }}
                    </p>
                </div>

                {{-- Batch Info - Fixed spacing --}}
                <div class="space-y-3 mb-5">
                    {{-- Category --}}
                    <div class="flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="flex-shrink-0">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                        </svg>
                        <p class="text-sm font-semibold">
                            {{ $batch->category->name }}
                        </p>
                    </div>

                    {{-- Start Date --}}
                    <div class="flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" class="flex-shrink-0">
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4M8 3v4M4 11h16" />
                        </svg>
                        <p class="text-sm font-semibold">
                            {{ $batch->start_date->format('d/m/Y') }}
                        </p>
                    </div>

                    {{-- Time --}}
                    <div class="flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12h-3.5M12 7v5" />
                        </svg>
                        <p class="text-sm font-semibold">
                            09:00 - 16:00
                        </p>
                    </div>

                    {{-- Participants --}}
                    <div class="flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        <p class="text-sm font-semibold">
                            {{ $batch->current_participants }} / {{ $batch->max_quota }} peserta
                        </p>
                    </div>

                    {{-- Trainer --}}
                    <div class="flex gap-2 text-gray-600 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                            <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                            <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                        </svg>
                        <p class="text-sm font-semibold">
                            {{ $batch->trainer->name }}
                        </p>
                    </div>
                </div>

                {{-- Prerequisites Warning --}}
                @if($batch->has_prerequisites)
                <div class="mb-5">
                    <div class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-orange-100 border border-orange-300 text-[#FF4D00] gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0M12 9v4M12 16v.01" />
                        </svg>
                        <p class="text-sm">Memerlukan prerequisite</p>
                    </div>
                </div>
                @endif

                {{-- Spacer to push buttons to bottom --}}
                <div class="flex-grow"></div>

                {{-- Action Buttons - Fixed width and consistent spacing --}}
                <div class="grid grid-cols-2 gap-3 mt-auto pt-4">
                    {{-- Detail Button --}}
                    <button type="button" 
                            @click="detailBatch = {{ $batch->id }}"
                            class="px-4 py-2.5 border rounded-lg flex items-center justify-center hover:bg-gray-50 text-sm font-semibold text-black transition">
                        Detail
                    </button>

                    {{-- Register Button --}}
                    @if($batch->is_registered)
                        <button type="button" disabled
                                class="px-4 py-2.5 rounded-lg flex items-center justify-center bg-[#10AF13]/60 text-sm font-semibold text-white cursor-not-allowed">
                            Sudah Terdaftar
                        </button>
                    @elseif($batch->is_full)
                        <button type="button" disabled
                                class="px-4 py-2.5 rounded-lg flex items-center justify-center bg-gray-400 text-sm font-semibold text-white cursor-not-allowed">
                            Batch Penuh
                        </button>
                    @else
                        <form method="POST" action="{{ route('participant.pendaftaran.register', $batch) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2.5 rounded-lg flex items-center justify-center bg-[#10AF13] hover:bg-[#0e8e0f] text-sm font-semibold text-white transition">
                                Daftar
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Modal Detail Batch --}}
                <div x-show="detailBatch === {{ $batch->id }}" 
                     x-cloak 
                     x-transition
                     class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
                    <div @click.outside="detailBatch = null" 
                         class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                        
                        {{-- Header Modal Hijau --}}
                        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
                            <div>
                                <h2 class="text-xl font-bold">Detail Batch</h2>
                                <p class="text-sm opacity-90">Informasi lengkap tentang batch training</p>
                            </div>
                            <button @click="detailBatch = null" class="text-white hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6l-12 12M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Body Modal (scrollable) --}}
                        <div class="p-6 overflow-y-auto flex-1">
                            {{-- Batch Title & Code --}}
                            <h2 class="text-lg font-medium text-gray-900">{{ $batch->title }}</h2>
                            <p class="text-[#737373] mb-6 uppercase text-sm font-medium">{{ formatBatchCode($batch->id, $batch->start_date->year) }}</p>

                            {{-- Content Box --}}
                            <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                                <div class="col-span-2">
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Deskripsi</p>
                                    <p class="text-gray-900">{{ $batch->category->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Kategori</p>
                                    <p class="text-gray-900">{{ $batch->category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Trainer</p>
                                    <p class="text-gray-900">{{ $batch->trainer->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Mulai</p>
                                    <p class="text-gray-900">{{ $batch->start_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Tanggal Selesai</p>
                                    <p class="text-gray-900">{{ $batch->end_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Kapasitas</p>
                                    <p class="text-gray-900">{{ $batch->current_participants }} / {{ $batch->max_quota }} peserta</p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Status</p>
                                    <span class="inline-block px-3 py-1 uppercase text-xs font-medium rounded-full {{ badgeStatus($batch->status) }}">
                                        {{ $batch->status }}
                                    </span>
                                </div>
                                
                                @if($batch->has_prerequisites)
                                <div class="col-span-2">
                                    <p class="text-gray-700 text-sm font-semibold mb-2">Prerequisite</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($batch->category->prerequisites as $prereq)
                                        <span class="inline-block px-3 py-1 capitalize text-xs font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                                            {{ $prereq->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($batch->zoom_link)
                                <div class="col-span-2">
                                    <p class="text-gray-700 text-sm font-semibold mb-1">Link Zoom</p>
                                    <a href="{{ $batch->zoom_link }}" target="_blank"
                                       class="text-[#0059FF] hover:underline break-all font-medium">
                                        {{ $batch->zoom_link }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                                <button type="button"
                                        @click="detailBatch = null"
                                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                                    Tutup
                                </button>
                                
                                @if($batch->is_registered)
                                    <button type="button" disabled
                                            class="px-6 py-3 bg-[#10AF13]/60 text-white rounded-lg font-medium cursor-not-allowed">
                                        Sudah Terdaftar
                                    </button>
                                @elseif($batch->is_full)
                                    <button type="button" disabled
                                            class="px-6 py-3 bg-gray-400 text-white rounded-lg font-medium cursor-not-allowed">
                                        Batch Penuh
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('participant.pendaftaran.register', $batch) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                                            <span class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                                Daftar Sekarang
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white border rounded-2xl p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-gray-300 mb-4">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">Tidak ada batch tersedia</p>
                    <p class="text-sm text-gray-400 mt-1">Batch training akan muncul di sini ketika tersedia</p>
                </div>
            </div>
        @endforelse
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

    @if($errors->any())
        <x-notification type="error">
            @foreach($errors->all() as $error)
                {{ $error }}
                @if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection
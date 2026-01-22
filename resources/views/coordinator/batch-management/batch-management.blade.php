{{-- resources/views/coordinator/batch-management/batch-management.blade.php --}}
@extends('layouts.coordinator')

@section('content')
<div x-data="batchManagement()" x-init="init()">

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

    @if($errors->any())
        <x-notification type="error">
            {{ $errors->first() }}
        </x-notification>
    @endif

    {{-- Header --}}
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Batch Management</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola batch pelatihan</p>
        </div>
        <button @click="openAddBatch = true; resetTasks();" 
                class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg>
            <span>Buat Batch Baru</span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Batch',
            'value' => $totalBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color' => 'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title' => 'Scheduled',
            'value' => $scheduledBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" />
                <path d="M8 3v4" /><path d="M4 11h16" /></svg>',
            'color' => 'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Ongoing',
            'value' => $ongoingBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Completed',
            'value' => $completedBatches,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
    </div>

    {{-- Filter Bar dengan Sort Dropdown --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('coordinator.batches.index')"
            searchPlaceholder="Cari batch, kategori, atau trainer..."
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
                    'name' => 'sort',
                    'placeholder' => 'Urutkan',
                    'options' => [
                        ['value' => 'latest', 'label' => 'Terbaru'],
                        ['value' => 'oldest', 'label' => 'Terlama'],
                        ['value' => 'start_date_asc', 'label' => 'Tanggal Mulai (Asc)'],
                        ['value' => 'start_date_desc', 'label' => 'Tanggal Mulai (Desc)'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Batch Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8 px-2">
        @forelse($batches as $batch)
        <div class="bg-white border rounded-2xl p-6 flex flex-col hover:shadow-lg transition-shadow">
            {{-- Header: Title + Status --}}
            <div class="mb-3">
                <h1 class="text-black font-semibold text-xl mb-2">
                    {{ $batch['title'] }}
                </h1>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1 text-xs font-semibold rounded-full uppercase {{ badgeStatus($batch['status']) }} inline-block">
                        {{ $batch['status'] }}
                    </div>
                </div>
            </div>

            {{-- Batch Code --}}
            <p class="text-gray-600 font-medium text-sm mb-4">
                {{ $batch['code'] }}
            </p>

            {{-- Category --}}
            <div class="px-3 py-1 w-fit mb-4 text-xs font-semibold rounded-lg border border-gray-300">
                {{ $batch['category'] }}
            </div>

            {{-- Batch Info --}}
            <div class="space-y-3 mb-4">
                {{-- Date --}}
                <div class="flex gap-3 text-gray-700 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" 
                        stroke="currentColor" stroke-width="2" fill="none" class="flex-shrink-0">
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                        <path d="M16 3v4M8 3v4M4 11h16" />
                    </svg>
                    <span class="text-sm font-medium">
                        {{ formatDate($batch['start_date']) }}
                    </span>
                </div>

                {{-- Time --}}
                <div class="flex gap-3 text-gray-700 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" class="flex-shrink-0">
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M12 12h-3.5M12 7v5" />
                    </svg>
                    <span class="text-sm font-medium">
                        {{ \Carbon\Carbon::parse($batch['start_date'])->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($batch['end_date'])->format('H:i') }}
                    </span>
                </div>

                {{-- Participants --}}
                <div class="flex gap-3 text-gray-700 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" class="flex-shrink-0">
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0 -3 -3.85" />
                    </svg>
                    <span class="text-sm font-medium">
                        {{ $batch['participants_count'] }} / {{ $batch['max_quota'] }} peserta
                    </span>
                </div>

                {{-- Trainer --}}
                <div class="flex gap-3 text-gray-700 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" class="flex-shrink-0">
                        <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                        <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                    </svg>
                    <span class="text-sm font-medium">{{ $batch['trainer'] }}</span>
                </div>
            </div>

            {{-- Action Buttons (Push to bottom) --}}
            <div class="mt-auto grid grid-cols-2 gap-3">
                <button @click="editBatch({{ $batch['id'] }})" 
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Edit
                </button>
                <button @click="deleteBatch({{ $batch['id'] }})" 
                        class="px-4 py-2.5 border border-red-300 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                    Hapus
                </button>
            </div>

            {{-- Hidden Delete Form --}}
            <form id="delete-form-{{ $batch['id'] }}" 
                  action="{{ route('coordinator.batches.destroy', $batch['id']) }}" 
                  method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white border rounded-2xl p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <p class="font-medium text-lg text-gray-500">Tidak ada batch ditemukan</p>
                @if(request()->hasAny(['search', 'status', 'sort']))
                    <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau pencarian Anda</p>
                @else
                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Buat Batch Baru" untuk membuat batch pertama</p>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($batches->hasPages())
    <div class="mt-6 px-2">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-medium">{{ $batches->firstItem() }}</span> 
                sampai <span class="font-medium">{{ $batches->lastItem() }}</span> 
                dari <span class="font-medium">{{ $batches->total() }}</span> batch
            </div>
            <div>
                {{ $batches->links() }}
            </div>
        </div>
    </div>
    @endif

    {{-- Include Modals --}}
    @include('coordinator.batch-management.batch-create-modal')
    @include('coordinator.batch-management.batch-edit-modal')

    {{-- Modal Delete Confirmation --}}
    <div x-show="openDeleteConfirm" x-cloak x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openDeleteConfirm = false" 
             class="bg-white w-full max-w-md rounded-2xl p-6 relative">
            
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Batch?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Tindakan ini tidak dapat dibatalkan. Batch yang dihapus akan hilang permanen.
                </p>
                
                <div class="flex gap-3">
                    <button @click="openDeleteConfirm = false" 
                            type="button"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition">
                        Batal
                    </button>
                    <button @click="confirmDelete()" 
                            type="button"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@include('coordinator.batch-management.batch-scripts')
@endsection

{{-- resources/views/trainer/upload-materi.blade.php --}}
@extends('layouts.trainer')

@section('content')
<div x-data="{ openUploadMateri: false }" x-cloak>

    {{-- Header --}}
    <div class="px-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Upload Materi</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola materi pelatihan untuk peserta</p>
        </div>
        <button @click="openUploadMateri = true"
            class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                <path d="M7 9l5 -5l5 5" />
                <path d="M12 4l0 12" />
            </svg>
            <span>Upload Materi</span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', ['title' => 'Total Materi', 'value' => $stats['total'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>',
            'color' => 'text-[#10AF13]'])
        @include('dashboard.card', ['title' => 'PDF', 'value' => $stats['pdf'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>',
            'color' => 'text-[#FF4D00]'])
        @include('dashboard.card', ['title' => 'Video', 'value' => $stats['video'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#AE00FF]'])
        @include('dashboard.card', ['title' => 'Recording', 'value' => $stats['recording'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#AE00FF]'])
        @include('dashboard.card', ['title' => 'Link', 'value' => $stats['link'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path fill="#0059FF" d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/></svg>',
            'color' => 'text-[#0059FF]'])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            action="{{ route('trainer.upload-materi') }}"
            searchPlaceholder="Cari judul materi..."
            :filters="[
                [
                    'name'        => 'batch_id',
                    'placeholder' => 'Semua Batch',
                    'options'     => $batchOptions->map(fn($opt) => ['value' => $opt['value'], 'label' => $opt['label']])
                        ->prepend(['value' => '', 'label' => 'Semua Batch'])->toArray()
                ],
                [
                    'name'        => 'type',
                    'placeholder' => 'Semua Tipe',
                    'options'     => [
                        ['value' => '',          'label' => 'Semua Tipe'],
                        ['value' => 'pdf',       'label' => 'PDF'],
                        ['value' => 'video',     'label' => 'Video'],
                        ['value' => 'recording', 'label' => 'Recording'],
                        ['value' => 'link',      'label' => 'Link'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Tabel Materi --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Materi</h2>
                    @php
                        $allMaterials = collect($batches)->flatMap(fn($b) => collect($b['materials'])->map(fn($m) => array_merge($m, [
                            'batch_title_display' => $b['title'],
                            'batch_code_display'  => $b['code'],
                        ])));
                    @endphp
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $allMaterials->count() }} materi</p>
                </div>
            </div>

            @if($allMaterials->count() > 0)
                @php
                    $perPage     = 10;
                    $currentPage = (int) request()->get('page', 1);
                    $offset      = ($currentPage - 1) * $perPage;
                    $paginated   = $allMaterials->forPage($currentPage, $perPage);
                    $totalPages  = (int) ceil($allMaterials->count() / $perPage);
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Judul</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Batch</th>
                                <th class="px-4 py-3">Diupload</th>
                                <th class="px-4 py-3">Oleh</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @foreach($paginated as $material)
                                @php
                                    $typeBadge = match($material['type']) {
                                        'pdf'       => 'bg-orange-100 text-[#FF4D00]',
                                        'video'     => 'bg-purple-100 text-[#AE00FF]',
                                        'recording' => 'bg-purple-100 text-[#AE00FF]',
                                        'link'      => 'bg-blue-100 text-[#0059FF]',
                                        default     => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500">{{ $offset + $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium max-w-[200px] truncate" title="{{ $material['title'] }}">{{ $material['title'] }}</div>
                                        @if(!empty($material['description']))
                                            <div class="text-xs text-gray-400 line-clamp-1">{{ Str::limit($material['description'], 40) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $typeBadge }}">
                                            {{ $material['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $material['batch_title_display'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $material['batch_code_display'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $material['uploaded_at'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $material['uploaded_by'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ $material['url'] }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0059FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-1.5 1.5" />
                                                    <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l1.5 -1.5" />
                                                </svg>
                                                Lihat
                                            </a>
                                            <form action="{{ route('trainer.materials.destroy', $material['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus materi ini?')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($totalPages > 1)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 mt-4 border-t">
                        <p class="text-sm text-gray-500">
                            Menampilkan
                            <span class="font-semibold text-gray-800">{{ $offset + 1 }}</span>–<span class="font-semibold text-gray-800">{{ min($offset + $perPage, $allMaterials->count()) }}</span>
                            dari <span class="font-semibold text-gray-800">{{ $allMaterials->count() }}</span> materi
                        </p>
                        <div class="flex items-center gap-2">
                            @if($currentPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                                </span>
                            @endif

                            @php $sp = max($currentPage-2,1); $ep = min($sp+4,$totalPages); $sp = max($ep-4,1); @endphp
                            @if($sp > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">1</a>
                                @if($sp > 2)<span class="text-gray-400 text-sm">...</span>@endif
                            @endif
                            @for($i = $sp; $i <= $ep; $i++)
                                @if($i == $currentPage)
                                    <span class="px-4 py-2 text-sm font-semibold text-white bg-[#10AF13] rounded-lg">{{ $i }}</span>
                                @else
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">{{ $i }}</a>
                                @endif
                            @endfor
                            @if($ep < $totalPages)
                                @if($ep < $totalPages - 1)<span class="text-gray-400 text-sm">...</span>@endif
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">{{ $totalPages }}</a>
                            @endif
                            @if($currentPage < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" />
                    </svg>
                    <p class="text-lg font-medium">
                        @if(request('batch_id') || request('type') || request('search'))
                            Tidak ada materi yang sesuai filter
                        @else
                            Belum ada materi
                        @endif
                    </p>
                    <p class="text-sm mt-1 text-gray-400">Klik "Upload Materi" untuk menambah materi baru</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL UPLOAD MATERI                                           --}}
    {{-- ============================================================ --}}
    <div x-show="openUploadMateri"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
        <div @click.outside="openUploadMateri = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold">Upload Materi Baru</h2>
                    <p class="text-sm opacity-90">Upload materi pembelajaran untuk peserta</p>
                </div>
                <button @click="openUploadMateri = false" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1" x-data="{
                batchOpen: false, batchValue: '{{ old('batch_id', '') }}',
                batchLabel: '{{ old('batch_id') ? collect($batchOptions)->firstWhere('value', old('batch_id'))['label'] ?? '-- Pilih Batch --' : '-- Pilih Batch --' }}',
                typeOpen: false, typeValue: '{{ old('type', '') }}',
                typeLabel: '{{ old('type') ? ucfirst(old('type')) : '-- Pilih Tipe Materi --' }}'
            }">
                @if($errors->any())
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-4 border border-red-200">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('trainer.materials.store') }}">
                    @csrf
                    <div class="space-y-5">
                        {{-- Batch --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Batch <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button type="button" @click="batchOpen = !batchOpen"
                                    :class="batchOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/20' : 'border-gray-300'"
                                    class="w-full h-[48px] px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition focus:outline-none">
                                    <span x-text="batchLabel" :class="batchValue === '' ? 'text-gray-400' : 'text-gray-900'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        class="flex-shrink-0 ml-2 transition-transform" :class="batchOpen ? 'rotate-180' : ''">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </button>
                                <div x-show="batchOpen" @click.outside="batchOpen = false"
                                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    style="position: fixed; z-index: 9999; max-height: 240px;"
                                    x-init="$watch('batchOpen', value => { if(value) { $nextTick(() => { const r = $el.previousElementSibling.getBoundingClientRect(); $el.style.top = r.bottom + window.scrollY + 8 + 'px'; $el.style.left = r.left + 'px'; $el.style.width = r.width + 'px'; }); } })"
                                    class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-y-auto" x-cloak>
                                    @foreach($batchOptions as $option)
                                    <div @click="batchValue = '{{ $option['value'] }}'; batchLabel = '{{ $option['label'] }}'; batchOpen = false"
                                        class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                        :class="batchValue === '{{ $option['value'] }}' ? 'bg-gray-50' : ''">
                                        <span class="text-gray-900">{{ $option['label'] }}</span>
                                        <svg x-show="batchValue === '{{ $option['value'] }}'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2" x-cloak>
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="batch_id" :value="batchValue" required>
                            </div>
                        </div>

                        {{-- Judul --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                placeholder="Contoh: Modul Python Game Development">
                        </div>

                        {{-- Tipe --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Materi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button type="button" @click="typeOpen = !typeOpen"
                                    :class="typeOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/20' : 'border-gray-300'"
                                    class="w-full h-[48px] px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition focus:outline-none">
                                    <span x-text="typeLabel" :class="typeValue === '' ? 'text-gray-400' : 'text-gray-900'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        class="flex-shrink-0 ml-2 transition-transform" :class="typeOpen ? 'rotate-180' : ''">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </button>
                                <div x-show="typeOpen" @click.outside="typeOpen = false"
                                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    style="position: fixed; z-index: 9999;"
                                    x-init="$watch('typeOpen', value => { if(value) { $nextTick(() => { const r = $el.previousElementSibling.getBoundingClientRect(); $el.style.top = r.bottom + window.scrollY + 8 + 'px'; $el.style.left = r.left + 'px'; $el.style.width = r.width + 'px'; }); } })"
                                    class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden" x-cloak>
                                    @foreach(['pdf' => 'PDF', 'video' => 'Video', 'recording' => 'Recording', 'link' => 'Link'] as $val => $label)
                                    <div @click="typeValue = '{{ $val }}'; typeLabel = '{{ $label }}'; typeOpen = false"
                                        class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                        :class="typeValue === '{{ $val }}' ? 'bg-gray-50' : ''">
                                        <span class="text-gray-900">{{ $label }}</span>
                                        <svg x-show="typeValue === '{{ $val }}'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2" x-cloak>
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="type" :value="typeValue" required>
                            </div>
                        </div>

                        {{-- URL --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">URL/Link <span class="text-red-500">*</span></label>
                            <input type="url" name="url" value="{{ old('url') }}" required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                placeholder="https://...">
                            <p class="text-xs text-gray-500 mt-1">Link ke file materi (Google Drive, Dropbox, dll)</p>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none"
                                placeholder="Deskripsi materi (opsional)">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-6 border-t">
                        <button type="button" @click="openUploadMateri = false"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium text-sm">Batal</button>
                        <button type="submit"
                            class="flex items-center gap-2 px-5 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium text-sm shadow-lg shadow-[#10AF13]/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Upload Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

@if(session('success'))
    <x-notification type="success">{{ session('success') }}</x-notification>
@endif
@if(session('error'))
    <x-notification type="error">{{ session('error') }}</x-notification>
@endif
@if($errors->any())
    <x-notification type="error">
        @foreach($errors->all() as $error){{ $error }}@if(!$loop->last)<br>@endif@endforeach
    </x-notification>
@endif

<style>
[x-cloak] { display: none !important; }
[x-ref="batchDropdown"]::-webkit-scrollbar, [x-ref="typeDropdown"]::-webkit-scrollbar { width: 6px; }
[x-ref="batchDropdown"]::-webkit-scrollbar-thumb, [x-ref="typeDropdown"]::-webkit-scrollbar-thumb { background: #10AF13; border-radius: 10px; }
</style>
@endsection
{{-- resources/views/trainer/upload-materi.blade.php --}}
@extends('layouts.trainer')

@section('content')
    <div class="px-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" x-data="{ openUploadMateri: false }">
        <div>
            <h1 class="text-2xl font-semibold">Upload Materi</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola materi pelatihan untuk peserta</p>
        </div>
        <button @click="openUploadMateri = true" 
                class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                <path d="M7 9l5 -5l5 5" />
                <path d="M12 4l0 12" />
            </svg>
            <span>Upload Materi</span>
        </button>

        <!-- Modal Upload Materi -->
        <div x-show="openUploadMateri" 
             x-cloak 
             x-transition 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="openUploadMateri = false" 
                 class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                
                <!-- Header Modal Hijau -->
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

                <!-- Body Modal (bisa di-scroll) -->
                <div class="p-6 overflow-y-auto flex-1">
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trainer.materials.store') }}" x-data="{
                        batchOpen: false,
                        batchValue: '{{ old('batch_id', '') }}',
                        batchLabel: '{{ old('batch_id') ? collect($batchOptions)->firstWhere('value', old('batch_id'))['label'] ?? '-- Pilih Batch --' : '-- Pilih Batch --' }}',
                        typeOpen: false,
                        typeValue: '{{ old('type', '') }}',
                        typeLabel: '{{ old('type') ? ucfirst(old('type')) : '-- Pilih Tipe Materi --' }}'
                    }">
                        @csrf
                        <div class="space-y-5">
                            <!-- Batch Dropdown Modern dengan Fixed Position -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Batch <span class="text-red-500">*</span>
                                </label>
                                <div class="relative z-30">
                                    <!-- Dropdown Button -->
                                    <button type="button" 
                                            @click="batchOpen = !batchOpen"
                                            :class="batchOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/20' : 'border-gray-300'"
                                            class="w-full h-[48px] px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition focus:outline-none">
                                        <span x-text="batchLabel" :class="batchValue === '' ? 'text-gray-400' : 'text-gray-900'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="flex-shrink-0 ml-2 transition-transform" :class="batchOpen ? 'rotate-180' : ''">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Content dengan Fixed Position -->
                                    <div x-show="batchOpen" 
                                         @click.outside="batchOpen = false" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95" 
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150" 
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         style="position: fixed; z-index: 9999; max-height: 240px;"
                                         x-ref="batchDropdown"
                                         @resize.window="
                                            if(batchOpen) {
                                                const button = $el.previousElementSibling;
                                                const rect = button.getBoundingClientRect();
                                                $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                $el.style.left = rect.left + 'px';
                                                $el.style.width = rect.width + 'px';
                                            }
                                         "
                                         x-init="
                                            $watch('batchOpen', value => {
                                                if(value) {
                                                    $nextTick(() => {
                                                        const button = $el.previousElementSibling;
                                                        const rect = button.getBoundingClientRect();
                                                        $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                        $el.style.left = rect.left + 'px';
                                                        $el.style.width = rect.width + 'px';
                                                    });
                                                }
                                            })
                                         "
                                         class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-y-auto"
                                         x-cloak>
                                        
                                        @foreach($batchOptions as $option)
                                        <div @click="batchValue = '{{ $option['value'] }}'; batchLabel = '{{ $option['label'] }}'; batchOpen = false"
                                            class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                            :class="batchValue === '{{ $option['value'] }}' ? 'bg-gray-50' : ''">
                                            <span class="text-gray-900">{{ $option['label'] }}</span>
                                            <svg x-show="batchValue === '{{ $option['value'] }}'" 
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                x-cloak>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="batch_id" :value="batchValue" required>
                                </div>
                            </div>

                            <!-- Judul Materi -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Judul Materi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                    placeholder="Contoh: Modul Python Game Development">
                            </div>

                            <!-- Tipe Materi Dropdown Modern dengan Fixed Position -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipe materi <span class="text-red-500">*</span>
                                </label>
                                <div class="relative z-20">
                                    <!-- Dropdown Button -->
                                    <button type="button" 
                                            @click="typeOpen = !typeOpen"
                                            :class="typeOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/20' : 'border-gray-300'"
                                            class="w-full h-[48px] px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition focus:outline-none">
                                        <span x-text="typeLabel" :class="typeValue === '' ? 'text-gray-400' : 'text-gray-900'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="flex-shrink-0 ml-2 transition-transform" :class="typeOpen ? 'rotate-180' : ''">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Content dengan Fixed Position -->
                                    <div x-show="typeOpen" 
                                         @click.outside="typeOpen = false" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95" 
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150" 
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         style="position: fixed; z-index: 9999;"
                                         x-ref="typeDropdown"
                                         @resize.window="
                                            if(typeOpen) {
                                                const button = $el.previousElementSibling;
                                                const rect = button.getBoundingClientRect();
                                                $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                $el.style.left = rect.left + 'px';
                                                $el.style.width = rect.width + 'px';
                                            }
                                         "
                                         x-init="
                                            $watch('typeOpen', value => {
                                                if(value) {
                                                    $nextTick(() => {
                                                        const button = $el.previousElementSibling;
                                                        const rect = button.getBoundingClientRect();
                                                        $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                        $el.style.left = rect.left + 'px';
                                                        $el.style.width = rect.width + 'px';
                                                    });
                                                }
                                            })
                                         "
                                         class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden"
                                         x-cloak>

                                        <div @click="typeValue = 'pdf'; typeLabel = 'PDF'; typeOpen = false"
                                            class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                            :class="typeValue === 'pdf' ? 'bg-gray-50' : ''">
                                            <span class="text-gray-900">PDF</span>
                                            <svg x-show="typeValue === 'pdf'" 
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                x-cloak>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </div>

                                        <div @click="typeValue = 'video'; typeLabel = 'Video'; typeOpen = false"
                                            class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                            :class="typeValue === 'video' ? 'bg-gray-50' : ''">
                                            <span class="text-gray-900">Video</span>
                                            <svg x-show="typeValue === 'video'" 
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                x-cloak>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </div>

                                        <div @click="typeValue = 'recording'; typeLabel = 'Recording'; typeOpen = false"
                                            class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                            :class="typeValue === 'recording' ? 'bg-gray-50' : ''">
                                            <span class="text-gray-900">Recording</span>
                                            <svg x-show="typeValue === 'recording'" 
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                x-cloak>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </div>

                                        <div @click="typeValue = 'link'; typeLabel = 'Link'; typeOpen = false"
                                            class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition"
                                            :class="typeValue === 'link' ? 'bg-gray-50' : ''">
                                            <span class="text-gray-900">Link</span>
                                            <svg x-show="typeValue === 'link'" 
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                x-cloak>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </div>
                                    </div>

                                    <input type="hidden" name="type" :value="typeValue" required>
                                </div>
                            </div>

                            <!-- URL/Link -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    URL/Link <span class="text-red-500">*</span>
                                </label>
                                <input type="url" name="url" value="{{ old('url') }}" required
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                    placeholder="https://...">
                                <p class="text-xs text-gray-500 mt-1">Link ke file materi (Google Drive, Dropbox, dll)</p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Deskripsi <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none" 
                                    placeholder="Deskripsi materi (opsional)">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                            <button type="button" @click="openUploadMateri = false"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Upload Materi
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Materi',
            'value' => $stats['total'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'PDF',
            'value' => $stats['pdf'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Video',
            'value' => $stats['video'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Recording',
            'value' => $stats['recording'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Link',
            'value' => $stats['link'],
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path fill="#0059FF" 
                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/></svg>',
            'color' => 'text-[#0059FF]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            action="{{ route('trainer.upload-materi') }}"
            searchPlaceholder="Cari materi..."
            :filters="[
                [
                    'name' => 'batch_id',
                    'placeholder' => 'Semua Batch',
                    'options' => $batchOptions->map(fn($opt) => [
                        'value' => $opt['value'],
                        'label' => $opt['label']
                    ])->prepend(['value' => '', 'label' => 'Semua Batch'])->toArray()
                ],
                [
                    'name' => 'type',
                    'placeholder' => 'Semua Tipe',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Tipe'],
                        ['value' => 'pdf', 'label' => 'PDF'],
                        ['value' => 'video', 'label' => 'Video'],
                        ['value' => 'recording', 'label' => 'Recording'],
                        ['value' => 'link', 'label' => 'Link'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Materials by Batch --}}
    @forelse($batches as $batch)
        <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold">{{ $batch['title'] }}</h2>
                    <p class="text-lg font-medium text-gray-600">{{ $batch['code'] }}</p>
                </div>
                <div class="flex border px-2 rounded-lg items-center gap-1 font-medium text-sm">
                    <p>{{ $batch['materials_count'] }}</p>
                    <p>materi</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse($batch['materials'] as $material)
                    <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="text-{{ $material['type'] === 'pdf' ? '[#FF4D00]' : ($material['type'] === 'link' ? '[#0059FF]' : '[#AE00FF]') }}">
                                {!! $material['type_icon'] !!}
                            </div>
                            <div>
                                <h3 class="text-md font-medium text-gray-800">
                                    {{ $material['title'] }}
                                </h3>
                                <p class="text-md text-[#737373] flex flex-wrap gap-2 font-medium">
                                    <span class="{{ $material['type_badge'] }} px-3 rounded-lg text-sm flex items-center uppercase">
                                        {{ $material['type'] }}
                                    </span>
                                    <span>{{ $material['uploaded_at'] }}</span>
                                    <span>oleh {{ $material['uploaded_by'] }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ $material['url'] }}" target="_blank"
                               class="px-3 py-1 text-sm font-medium rounded-lg border flex justify-center items-center gap-3 hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="#000000" 
                                        d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                                        2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                                        3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                                        1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/>
                                </svg>
                                <p>Lihat</p>
                            </a>

                            <form action="{{ route('trainer.materials.destroy', $material['id']) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus materi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[#ff0000] hover:text-[#E81B1B]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-5 flex justify-center text-gray-600 font-medium text-lg">
                        <h4>Belum ada materi</h4>
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
            <div class="text-center py-16 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 9l5 -5l5 5" />
                    <path d="M12 4l0 12" />
                </svg>
                <p class="text-lg font-medium text-gray-600">Belum ada batch yang ditugaskan</p>
                <p class="mt-2 text-gray-500">Hubungi koordinator untuk mendapatkan batch</p>
            </div>
        </div>
    @endforelse

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
            @foreach($errors->all() as $error)
                {{ $error }}
                @if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection

    <style>
    /* Custom scrollbar untuk dropdown modal */
    [x-ref="batchDropdown"]::-webkit-scrollbar,
    [x-ref="typeDropdown"]::-webkit-scrollbar {
        width: 6px;
    }

    [x-ref="batchDropdown"]::-webkit-scrollbar-track,
    [x-ref="typeDropdown"]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    [x-ref="batchDropdown"]::-webkit-scrollbar-thumb,
    [x-ref="typeDropdown"]::-webkit-scrollbar-thumb {
        background: #10AF13;
        border-radius: 10px;
    }

    [x-ref="batchDropdown"]::-webkit-scrollbar-thumb:hover,
    [x-ref="typeDropdown"]::-webkit-scrollbar-thumb:hover {
        background: #0e8e0f;
    }

    [x-cloak] { 
        display: none !important; 
    }
    </style>
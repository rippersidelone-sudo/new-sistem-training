@extends('layouts.coordinator')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Manajemen Kategori Pelatihan</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola kategori dan prerequisite pelatihan</p>
        </div>
        <button @click="openAddCategory = true" class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="openAddCategory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openAddCategory = false" class="bg-white w-full max-w-xl rounded-2xl p-6 relative">
            <button @click="openAddCategory = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
            <div class="flex justify-between items-center mb-4 p-2">
                <div>
                    <h2 class="text-xl font-semibold">Tambah Kategori</h2>
                    <p class="text-[#737373]">Buat kategori pelatihan baru dengan atau tanpa prerequisite</p>
                </div>
            </div>
            <form method="POST" action="">
                <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-1 mx-2 mb-2 pb-7">
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            Nama Kategori <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <input type="text" name="name" class="w-full mt-1 px-3 py-2
                            border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" placeholder="Contoh: Python Game Developer" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            Deskripsi <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <textarea class="bg-gray-200 focus:ring-[#10AF13] focus:border-[#10AF13] border-none font-medium rounded-xl w-full resize-none focus:border-double" rows="4" placeholder="Berikan deskripsi..."></textarea>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">Prerequisite (opsional)</label>
                        <p class="text-sm font-medium text-gray-500">Pilih kategori yang harus diselesaikan terlebih dahulu sebelum mengambil kategori ini</p>
                        <div class="w-full border border-gray-300 rounded-xl mt-1 p-4 flex flex-col gap-1 max-h-48 overflow-y-auto">
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Python Coder</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Python Game Developer</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Web Development Fundamentals</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">React Advanced</span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="mt-4 ms-2 me-2">

                <div class="flex justify-end gap-3 pt-4 me-2">
                    <button type="button"
                        @click="openAddCategory = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                        Tambah Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Kategori',
            'value'=>3,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                // stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                // class="icon icon-tabler icons-tabler-outline icon-tabler-stack-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                // <path d="M12 4l-8 4l8 4l8 -4l-8 -4" /><path d="M4 12l8 4l8 -4" /><path d="M4 16l8 4l8 -4" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Tanpa Prerequisite',
            'value'=>2,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                // stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open">
                // <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                // <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 11v-5a4 4 0 0 1 8 0" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Dengan Prerequisite',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                // stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                // class="icon icon-tabler icons-tabler-outline icon-tabler-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                // <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                // <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
    </div>

    <div class="grid grid-cols-1 border lg:grid-cols-2 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
        <!-- Search -->
        <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                class="text-[#737373]">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text"
                name="search"
                class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                placeholder="Cari kategori..." />
        </div>

        <!-- Dropdown Status -->
        <div x-data="{ open: false, value: '', label: 'Semua Status' }" class="relative w-full">
            <button @click="open = !open"
                :class="open
                    ?
                    'border-[#10AF13] ring-1 ring-[#10AF13]' :
                    'border-gray-300'"
                class="w-full px-3 py-2 rounded-lg border cursor-pointer
                flex justify-between items-center text-sm bg-white transition">
                <span x-text="label"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                <!-- Item -->
                <template
                    x-for="item in [
                        { value: '', label: 'Semua Status' },
                        { value: 'prerequisite', label: ' Dengan Prerequisite' },
                        { value: 'non-prerequisite', label: 'Tanpa Prerequisite' },
                    ]"
                    :key="item.value">

                    <div @click="value = item.value; label = item.label; open = false"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">

                        <span x-text="item.label"></span>

                        <!-- Check Icon -->
                        <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Hidden input untuk backend -->
            <input type="hidden" name="statusKategori" :value="value">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <h1 class="text-black font-bold text-xl">
                    Python Coder
                </h1>
                <button @click="openEditCategory = true" class="px-3 py-1 text-xs font-medium rounded-full text-[#10AF13] hover:text-[#0e8e0f]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                </button>
            </div>

            {{-- <div class="px-3 py-1 w-fit mt-1 text-xs font-medium rounded-full bg-orange-100">
                <p class="text-[#FF4D00]">Dengan Prerequisite</p>
            </div> --}}

            <div class="mt-7 text-gray-600">
                <p class="text-md font-medium">
                    Dasar-dasar pemrograman Python
                </p>
            </div>

            {{-- <hr class="border-gray-200 mt-3"> --}}

            <hr class="border-gray-200 mt-3">

            <div class="mt-2 text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Dibuat:
                </h2>
                <p class="text-md font-medium">
                    15/1/2025
                </p>
            </div>

            <div class="text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Oleh:
                </h2>
                <p class="text-md font-medium">
                    {{ Auth::user()->name }}
                </p>
            </div>
        </div>
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <h1 class="text-black font-bold text-xl">
                    Python Game Developer
                </h1>
                <button class="px-3 py-1 text-xs font-medium rounded-full text-[#10AF13] hover:text-[#0e8e0f]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                </button>
            </div>

            <div class="px-3 py-1 w-fit mt-1 text-xs font-medium rounded-full bg-orange-100">
                <p class="text-[#FF4D00]">Dengan Prerequisite</p>
            </div>

            <div class="mt-7 text-gray-600">
                <p class="text-md font-medium">
                    Pengembangan game menggunakan Python dan Pygame
                </p>
            </div>

            <hr class="border-gray-200 mt-3">

            <div class="mt-2">
                <h2 class="text-md font-medium text-gray-600">
                    Prerequisite:
                </h2>
                <p class="text-md font-medium text-black">
                    Python Coder
                </p>
            </div>

            <hr class="border-gray-200 mt-3">

            <div class="mt-2 text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Dibuat:
                </h2>
                <p class="text-md font-medium">
                    20/1/2025
                </p>
            </div>

            <div class="text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Oleh:
                </h2>
                <p class="text-md font-medium">
                    {{ Auth::user()->name }}
                </p>
            </div>
        </div>
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <h1 class="text-black font-bold text-xl">
                    Web Development Fundamentals
                </h1>
                <button class="px-3 py-1 text-xs font-medium rounded-full text-[#10AF13] hover:text-[#0e8e0f]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                </button>
            </div>

            {{-- <div class="px-3 py-1 w-fit mt-1 text-xs font-medium rounded-full bg-orange-100">
                <p class="text-[#FF4D00]">Dengan Prerequisite</p>
            </div> --}}

            <div class="mt-7 text-gray-600">
                <p class="text-md font-medium">
                    HTML, CSS, dan JavaScript untuk pemula
                </p>
            </div>

            {{-- <hr class="border-gray-200 mt-3">

            <div class="mt-2">
                <h2 class="text-md font-medium text-gray-600">
                    Prerequisite:
                </h2>
                <p class="text-md font-medium text-black">
                    Python Coder
                </p>
            </div> --}}

            <hr class="border-gray-200 mt-3">

            <div class="mt-2 text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Dibuat:
                </h2>
                <p class="text-md font-medium">
                    1/2/2025
                </p>
            </div>

            <div class="text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Oleh:
                </h2>
                <p class="text-md font-medium">
                    {{ Auth::user()->name }}
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div x-show="openEditCategory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openEditCategory = false" class="bg-white w-full max-w-xl rounded-2xl p-6 relative">
            <button @click="openEditCategory = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
            <div class="flex justify-between items-center mb-4 p-2">
                <div>
                    <h2 class="text-xl font-semibold">Edit Kategori</h2>
                    <p class="text-[#737373]">Ubah informasi kategori pelatihan</p>
                </div>
            </div>
            <form method="POST" action="">
                <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-1 mx-2 mb-2 pb-7">
                    @csrf
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            Nama Kategori <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <input type="text" name="name" class="w-full mt-1 px-3 py-2
                            border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" placeholder="Contoh: Python Game Developer" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">
                            Deskripsi <span class="text-[#ff0000] text-lg">*</span>
                        </label>
                        <textarea class="bg-gray-200 focus:ring-[#10AF13] focus:border-[#10AF13] border-none font-medium rounded-xl w-full resize-none focus:border-double" rows="4" placeholder="Berikan deskripsi..."></textarea>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">Prerequisite (opsional)</label>
                        <p class="text-sm font-medium text-gray-500">Pilih kategori yang harus diselesaikan terlebih dahulu sebelum mengambil kategori ini</p>
                        <div class="w-full border border-gray-300 rounded-xl mt-1 p-4 flex flex-col gap-1 max-h-48 overflow-y-auto">
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Python Coder</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Python Game Developer</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">Web Development Fundamentals</span>
                            </label>
                            <label class="items-center cursor-pointer hover:bg-gray-100 p-2">
                                <input id="pyCode" type="checkbox"
                                    class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                <span class="ms-2 text-md font-semibold text-gray-700">React Advanced</span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="mt-4 ms-2 me-2">

                <div class="flex justify-end gap-3 pt-4 me-2">
                    <button type="button"
                        @click="openEditCategory = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                        Tambah Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
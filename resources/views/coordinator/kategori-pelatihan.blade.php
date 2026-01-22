@extends('layouts.coordinator')

@section('content')
<div x-data="{ 
    openAddCategory: false, 
    openEditCategory: false,
    openDeleteCategory: false,
    editCategory: null,
    deleteCategory: null,
    selectedPrerequisites: [],
    
    openEdit(category) {
        this.editCategory = category;
        this.selectedPrerequisites = category.prerequisites || [];
        this.openEditCategory = true;
    },
    
    openDelete(category) {
        this.deleteCategory = category;
        this.openDeleteCategory = true;
    },
    
    resetForm() {
        this.editCategory = null;
        this.deleteCategory = null;
        this.selectedPrerequisites = [];
    }
}">
    
    {{-- Header --}}
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Manajemen Kategori Pelatihan</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola kategori dan prerequisite pelatihan</p>
        </div>
        <button @click="openAddCategory = true" class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg>
            <span>Tambah Kategori</span>
        </button>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif

    @if($errors->any())
        <x-notification type="error">
            @foreach($errors->all() as $error)
                {{ $error }}
            @endforeach
        </x-notification>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Kategori',
            'value'=>$totalCategories,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 3l-8 4l8 4l8 -4l-8 -4" />
                <path d="M4 12l8 4l8 -4" />
                <path d="M4 16l8 4l8 -4" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Tanpa Prerequisite',
            'value'=>$withoutPrerequisite,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 11v-5a4 4 0 0 1 8 0" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Dengan Prerequisite',
            'value'=>$withPrerequisite,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('coordinator.categories.index')"
            searchPlaceholder="Cari kategori..."
            :filters="[
                [
                    'name' => 'prerequisite_filter',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'with', 'label' => 'Dengan Prerequisite'],
                        ['value' => 'without', 'label' => 'Tanpa Prerequisite'],
                    ]
                ],
                [
                    'name' => 'sort',
                    'placeholder' => 'Urutkan',
                    'options' => [
                        ['value' => 'latest', 'label' => 'Terbaru'],
                        ['value' => 'oldest', 'label' => 'Terlama'],
                        ['value' => 'name_asc', 'label' => 'Nama A-Z'],
                        ['value' => 'name_desc', 'label' => 'Nama Z-A'],
                    ]
                ]
            ]"
        />
    </div>

    {{-- Categories Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8 px-2">
        @forelse($categories as $category)
        <div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <h1 class="text-black font-bold text-xl">
                    {{ $category->name }}
                </h1>
                <div class="flex gap-2">
                    <button @click="openEdit({{ $category->load('prerequisites') }})" 
                            class="p-1 text-[#10AF13] hover:text-[#0e8e0f] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                    </button>
                    <button @click="openDelete({{ $category }})" 
                            class="p-1 text-red-600 hover:text-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </button>
                </div>
            </div>

            @if($category->prerequisites_count > 0)
            <div class="px-3 py-1 w-fit mt-1 text-xs font-medium rounded-full bg-orange-100">
                <p class="text-[#FF4D00]">Dengan Prerequisite</p>
            </div>
            @endif

            <div class="mt-7 text-gray-600">
                <p class="text-md font-medium">
                    {{ $category->description }}
                </p>
            </div>

            @if($category->prerequisites_count > 0)
            <hr class="border-gray-200 mt-3">

            <div class="mt-2">
                <h2 class="text-md font-medium text-gray-600">
                    Prerequisite:
                </h2>
                @foreach($category->prerequisites as $prereq)
                <p class="text-md font-medium text-black">
                    {{ $prereq->name }}
                </p>
                @endforeach
            </div>
            @endif

            <hr class="border-gray-200 mt-3">

            <div class="mt-2 text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Dibuat:
                </h2>
                <p class="text-md font-medium">
                    {{ formatDate($category->created_at) }}
                </p>
            </div>

            <div class="text-gray-600 flex gap-2 items-center">
                <h2 class="text-md font-medium">
                    Total Batch:
                </h2>
                <p class="text-md font-medium">
                    {{ $category->batches_count }}
                </p>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 3l-8 4l8 4l8 -4l-8 -4" />
                <path d="M4 12l8 4l8 -4" />
                <path d="M4 16l8 4l8 -4" />
            </svg>
            <p class="text-gray-500 font-medium">Belum ada kategori</p>
            <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Kategori" untuk membuat kategori baru</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
    <div class="mt-6 px-2">
        {{ $categories->links() }}
    </div>
    @endif

    {{-- Modal Tambah Kategori --}}
    <div x-show="openAddCategory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openAddCategory = false" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Tambah Kategori</h2>
                    <p class="text-sm opacity-90">Buat kategori pelatihan baru dengan atau tanpa prerequisite</p>
                </div>
                <button @click="openAddCategory = false" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form method="POST" action="{{ route('coordinator.categories.store') }}">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                placeholder="Contoh: Python Game Developer">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" required rows="4"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none" 
                                placeholder="Berikan deskripsi kategori...">{{ old('description') }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Prerequisite <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Pilih kategori yang harus diselesaikan terlebih dahulu</p>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2">
                                @forelse($allCategories as $cat)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                    <input type="checkbox" name="prerequisites[]" value="{{ $cat->id }}"
                                        {{ in_array($cat->id, old('prerequisites', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                    <span class="ms-3 text-sm font-medium text-gray-700">{{ $cat->name }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500 text-center py-2">Belum ada kategori lain</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                        <button type="button" @click="openAddCategory = false"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Tambah Kategori
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Kategori --}}
    <div x-show="openEditCategory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openEditCategory = false; resetForm()" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Edit Kategori</h2>
                    <p class="text-sm opacity-90">Perbarui informasi kategori pelatihan</p>
                </div>
                <button @click="openEditCategory = false; resetForm()" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form method="POST" :action="`{{ route('coordinator.categories.index') }}/${editCategory?.id}`" x-show="editCategory">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" :value="editCategory?.name" required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" x-text="editCategory?.description" required rows="4"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Prerequisite <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Pilih kategori yang harus diselesaikan terlebih dahulu</p>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto space-y-2">
                                @foreach($allCategories as $cat)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                    <input type="checkbox" name="prerequisites[]" value="{{ $cat->id }}"
                                        :checked="editCategory?.prerequisites?.some(p => p.id === {{ $cat->id }})"
                                        :disabled="editCategory?.id === {{ $cat->id }}"
                                        class="rounded border-gray-300 text-[#10AF13] focus:ring-[#10AF13]">
                                    <span class="ms-3 text-sm font-medium text-gray-700">{{ $cat->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                        <button type="button" @click="openEditCategory = false; resetForm()"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Simpan Perubahan
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Delete Kategori --}}
    <div x-show="openDeleteCategory" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openDeleteCategory = false; resetForm()" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-3">Hapus Kategori?</h2>
            <p class="text-gray-600 mb-6">
                Yakin ingin menghapus kategori <span class="font-semibold" x-text="deleteCategory?.name"></span>?
            </p>

            <div class="flex justify-end gap-3">
                <button @click="openDeleteCategory = false; resetForm()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                    Batal
                </button>
                <form :action="`{{ route('coordinator.categories.index') }}/${deleteCategory?.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
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
    <div class="px-2 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">Manajemen Kategori Pelatihan</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola kategori dan prerequisite pelatihan</p>
        </div>

        <div class="flex gap-3">
            {{-- SYNC CATEGORIES BUTTON --}}
            <button
                x-data="syncCategories()"
                @click="sync()"
                :disabled="loading"
                :title="loading ? 'Syncing...' : 'Sync categories dari API'"
                class="flex items-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                </svg>
                <svg x-show="loading" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                    <path d="M12 3a9 9 0 1 0 9 9" />
                </svg>
                <span x-text="loading ? 'Syncing...' : 'Sync Categories'"></span>
            </button>

            {{-- ADD CATEGORY BUTTON --}}
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
            <div class="flex justify-between items-start gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <x-tooltip-text 
                        :text="$category->name" 
                        class="text-black font-bold text-xl"
                        position="top"
                    />
                </div>
                
                <div class="flex gap-2 flex-shrink-0">
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
                <x-tooltip-text 
                    :text="$category->description" 
                    class="text-md font-medium text-gray-600"
                    position="top"
                />
            </div>

            @if($category->prerequisites_count > 0)
            <hr class="border-gray-200 mt-3">

            <div class="mt-2">
                <h2 class="text-md font-medium text-gray-600">
                    Prerequisite:
                </h2>
                @foreach($category->prerequisites as $prereq)
                <x-tooltip-text 
                    :text="$prereq->name" 
                    class="text-md font-medium text-black"
                    position="top"
                />
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
        <x-pagination :paginator="$categories" />
    </div>
    @endif

    {{-- Include Modals --}}
    @include('coordinator.kategori-pelatihan.kategori-create-modal')
    @include('coordinator.kategori-pelatihan.kategori-edit-modal')
    @include('coordinator.kategori-pelatihan.kategori-delete-modal')

</div>

@push('scripts')
<script>
    // ============================================================
    // SYNC CATEGORIES FUNCTION (FIXED)
    // ============================================================
    function syncCategories() {
        return {
            loading: false,

            sync() {
                if (this.loading) return;
                this.loading = true;

                fetch('{{ route('sync.categories') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(async (res) => {
                    const contentType = res.headers.get('content-type') || '';

                    // Kalau server balikin HTML (redirect/error page), tampilkan supaya kebaca
                    if (!contentType.includes('application/json')) {
                        const text = await res.text();
                        throw new Error(text);
                    }

                    const data = await res.json();

                    // Kalau status HTTP bukan 2xx, lempar error biar masuk catch
                    if (!res.ok) {
                        throw new Error(data?.message || 'Request gagal');
                    }

                    return data;
                })
                .then(data => {
                    this.loading = false;
                    this.showNotification(data.success, data.message);

                    // Reload page jika berhasil untuk update grid
                    if (data.success) {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                })
                .catch((err) => {
                    console.error('SYNC CATEGORIES ERROR:', err);
                    this.loading = false;
                    this.showNotification(false, err?.message || 'Terjadi kesalahan saat sync data.');
                });
            },

            showNotification(success, message) {
                const div = document.createElement('div');
                div.innerHTML = `
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 4000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-4"
                         class="fixed bottom-6 right-6 z-50 max-w-md">
                        <div class="flex items-center gap-3 ${success ? 'bg-[#10AF13]' : 'bg-red-600'} text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                            <span class="font-medium text-sm">${message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(div.firstElementChild);
                Alpine.initTree(document.body.lastElementChild);
            }
        }
    }
</script>
@endpush
@endsection

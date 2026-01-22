{{-- resources/views/coordinator/batch-management/batch-edit-modal.blade.php --}}
<div x-show="openEditBatch" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openEditBatch = false" 
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        {{-- Header Modal Hijau --}}
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Edit Batch</h2>
                <p class="text-sm opacity-90">Ubah informasi batch pelatihan</p>
            </div>
            <button @click="openEditBatch = false" 
                    class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body Modal (bisa di-scroll) --}}
        <div class="p-6 overflow-y-auto flex-1">
            {{-- Form Container - Will be populated via JavaScript --}}
            <div id="edit-batch-form-container">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#10AF13] mb-4"></div>
                    <p class="text-gray-500 font-medium">Memuat data batch...</p>
                </div>
            </div>
        </div>
    </div>
</div>
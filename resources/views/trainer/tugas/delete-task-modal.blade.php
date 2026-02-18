{{-- resources/views/trainer/modals/delete-task-modal.blade.php --}}
<div x-show="openDeleteModal" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openDeleteModal = false" 
         class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        
        {{-- Header Modal --}}
        <div class="bg-red-600 px-6 py-5 text-white flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                 stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                <path d="M12 9v4" />
                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                <path d="M12 16h.01" />
            </svg>
            <div>
                <h2 class="text-lg font-bold">Hapus Tugas</h2>
                <p class="text-sm opacity-90">Konfirmasi penghapusan tugas</p>
            </div>
        </div>

        {{-- Body Modal --}}
        <div class="p-6">
            <p class="text-gray-700 mb-4">
                Apakah Anda yakin ingin menghapus tugas <span class="font-semibold" x-text="deleteTaskTitle"></span>?
            </p>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="2" class="text-red-600 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <div class="text-sm text-red-800">
                        <p class="font-medium mb-1">Peringatan:</p>
                        <p>Tugas yang memiliki submission tidak dapat dihapus. Aksi ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
            <button type="button"
                    @click="openDeleteModal = false"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                Batal
            </button>
            <button type="button"
                    @click="confirmDelete()"
                    class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7l16 0" />
                        <path d="M10 11l0 6" />
                        <path d="M14 11l0 6" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg>
                    Hapus Tugas
                </span>
            </button>
        </div>

        {{-- Hidden Delete Forms --}}
        @foreach($tasks as $task)
            <form id="delete-task-form-{{ $task['id'] }}" 
                  method="POST" 
                  action="{{ route('trainer.tasks.destroy', $task['id']) }}" 
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</div>
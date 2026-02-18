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
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
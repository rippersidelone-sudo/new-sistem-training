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
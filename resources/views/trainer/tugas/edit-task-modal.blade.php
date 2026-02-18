{{-- resources/views/trainer/modals/edit-task-modal.blade.php --}}
<div x-show="openEditModal" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openEditModal = false" 
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        {{-- Header Modal --}}
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Edit Tugas</h2>
                <p class="text-sm opacity-90">Perbarui informasi tugas</p>
            </div>
            <button @click="openEditModal = false" 
                    class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body Modal --}}
        <div class="p-6 overflow-y-auto flex-1">
            <form x-bind:action="`{{ url('trainer/tasks') }}/${currentTask?.id}`" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    
                    {{-- Batch Selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Batch <span class="text-red-500">*</span>
                        </label>
                        <select name="batch_id" 
                                x-model="currentTask.batch_id"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                required>
                            <option value="">Pilih Batch</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch['id'] }}">
                                    {{ $batch['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Task Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               x-model="currentTask.title"
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                               placeholder="Contoh: Membuat Game Snake dengan Python" 
                               required>
                    </div>

                    {{-- Task Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi Tugas <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="6"
                                  x-model="currentTask.description"
                                  class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none" 
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta..."
                                  required></textarea>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="deadline" 
                               x-model="currentTask.deadline"
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                               required>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Perbarui deadline jika diperlukan
                        </p>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                    <button type="button"
                            @click="openEditModal = false"
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
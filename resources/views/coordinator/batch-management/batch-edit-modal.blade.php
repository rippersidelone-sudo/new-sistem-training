{{-- resources/views/coordinator/batch-management/batch-edit-modal.blade.php --}}
<div x-show="openEditBatch" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openEditBatch = false" 
         class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
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
            {{-- Loading State --}}
            <div x-show="editLoading" class="flex flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#10AF13] mb-4"></div>
                <p class="text-gray-500 font-medium">Memuat data batch...</p>
            </div>

            {{-- Error State --}}
            <div x-show="editError" x-cloak class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto mb-3 text-red-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p class="text-red-500 font-medium">Gagal memuat data batch</p>
                <p class="text-gray-500 text-sm mt-1">Silakan coba lagi</p>
                <button @click="openEditBatch = false" class="mt-4 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>

            {{-- Form --}}
            <form x-show="!editLoading && !editError" 
                  :action="`{{ url('coordinator/batches') }}/${editBatchId}`"
                  method="POST"
                  x-cloak>
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    
                    {{-- Informasi Dasar --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Informasi Dasar
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Judul Batch <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       x-model="editFormData.title"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                       required>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                {{-- Custom Dropdown Kategori --}}
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kategori Pelatihan <span class="text-red-500">*</span>
                                    </label>
                                    
                                    {{-- Hidden Input --}}
                                    <input type="hidden" name="category_id" x-model="editFormData.category_id" required>
                                    
                                    {{-- Dropdown Button --}}
                                    <button type="button"
                                            @click="editCategoryOpen = !editCategoryOpen"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition text-left flex items-center justify-between group hover:border-gray-400">
                                        <span :class="editFormData.category_name ? 'text-gray-900' : 'text-gray-400'">
                                            <span x-text="editFormData.category_name || 'Pilih Kategori'"></span>
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="h-5 w-5 text-gray-400 transition-transform group-hover:text-gray-600"
                                             :class="editCategoryOpen ? 'rotate-180' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Dropdown Menu --}}
                                    <div x-show="editCategoryOpen"
                                         @click.outside="editCategoryOpen = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                        <div class="py-1">
                                            <template x-for="category in editCategories" :key="category.id">
                                                <button type="button"
                                                        @click="selectEditCategory(category.id, category.name)"
                                                        class="w-full px-4 py-3 text-left hover:bg-gray-50 transition flex items-center justify-between group"
                                                        :class="editFormData.category_id == category.id ? 'bg-[#10AF13]/5 text-[#10AF13]' : 'text-gray-700'">
                                                    <span class="font-medium" x-text="category.name"></span>
                                                    <svg x-show="editFormData.category_id == category.id" 
                                                         xmlns="http://www.w3.org/2000/svg" 
                                                         class="h-5 w-5 text-[#10AF13]" 
                                                         viewBox="0 0 24 24" 
                                                         fill="none" 
                                                         stroke="currentColor" 
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Custom Dropdown Trainer --}}
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Main Trainer <span class="text-red-500">*</span>
                                    </label>
                                    
                                    {{-- Hidden Input --}}
                                    <input type="hidden" name="trainer_id" x-model="editFormData.trainer_id" required>
                                    
                                    {{-- Dropdown Button --}}
                                    <button type="button"
                                            @click="editTrainerOpen = !editTrainerOpen"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition text-left flex items-center justify-between group hover:border-gray-400">
                                        <span :class="editFormData.trainer_name ? 'text-gray-900' : 'text-gray-400'">
                                            <span x-text="editFormData.trainer_name || 'Pilih Trainer'"></span>
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="h-5 w-5 text-gray-400 transition-transform group-hover:text-gray-600"
                                             :class="editTrainerOpen ? 'rotate-180' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Dropdown Menu --}}
                                    <div x-show="editTrainerOpen"
                                         @click.outside="editTrainerOpen = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                        <div class="py-1">
                                            <template x-for="trainer in editTrainers" :key="trainer.id">
                                                <button type="button"
                                                        @click="selectEditTrainer(trainer.id, trainer.name)"
                                                        class="w-full px-4 py-3 text-left hover:bg-gray-50 transition flex items-center justify-between group"
                                                        :class="editFormData.trainer_id == trainer.id ? 'bg-[#10AF13]/5 text-[#10AF13]' : 'text-gray-700'">
                                                    <span class="font-medium" x-text="trainer.name"></span>
                                                    <svg x-show="editFormData.trainer_id == trainer.id" 
                                                         xmlns="http://www.w3.org/2000/svg" 
                                                         class="h-5 w-5 text-[#10AF13]" 
                                                         viewBox="0 0 24 24" 
                                                         fill="none" 
                                                         stroke="currentColor" 
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- Sessions Section --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Jadwal Pelatihan
                        </h3>
                        
                        <div class="space-y-4">
                            <template x-for="(session, index) in editFormData.sessions" :key="index">
                                <div class="border border-gray-200 rounded-lg p-4 bg-gradient-to-r from-gray-50 to-white">
                                    {{-- Session Header --}}
                                    <div class="mb-4 pb-3 border-b border-gray-200 flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-lg">
                                                Hari <span x-text="index + 1"></span>
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1" x-text="formatEditSessionDate(session.start_date)"></p>
                                        </div>
                                        <div class="px-3 py-1 bg-[#10AF13]/10 text-[#10AF13] rounded-full text-xs font-semibold">
                                            Session <span x-text="index + 1"></span>
                                        </div>
                                    </div>

                                    {{-- Hidden inputs --}}
                                    <input type="hidden" :name="'sessions[' + index + '][id]'" :value="session.id">
                                    <input type="hidden" :name="'sessions[' + index + '][session_number]'" :value="index + 1">
                                    <input type="hidden" :name="'sessions[' + index + '][start_date]'" :value="session.start_date">
                                    <input type="hidden" :name="'sessions[' + index + '][end_date]'" :value="session.end_date">

                                    <div class="grid gap-4">
                                        {{-- Custom Dropdown Trainer --}}
                                        <div class="relative"
                                             x-data="{
                                                 editSessionTrainerOpen: false,
                                                 getEditTrainerName(id) {
                                                     const trainer = editTrainers.find(t => t.id == id);
                                                     return trainer ? trainer.name : 'Pilih Trainer';
                                                 },
                                                 selectEditSessionTrainer(trainerId) {
                                                     session.trainer_id = trainerId;
                                                     this.editSessionTrainerOpen = false;
                                                 }
                                             }">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Trainer <span class="text-red-500">*</span>
                                            </label>
                                            
                                            <input type="hidden" 
                                                   :name="'sessions[' + index + '][trainer_id]'" 
                                                   :value="session.trainer_id" 
                                                   required>
                                            
                                            <button type="button"
                                                    @click="editSessionTrainerOpen = !editSessionTrainerOpen"
                                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition text-left flex items-center justify-between group hover:border-gray-400">
                                                <span :class="session.trainer_id ? 'text-gray-900' : 'text-gray-400'">
                                                    <span x-text="getEditTrainerName(session.trainer_id)"></span>
                                                </span>
                                                <svg xmlns="http://www.w3.org/2000/svg" 
                                                     class="h-5 w-5 text-gray-400 transition-transform"
                                                     :class="editSessionTrainerOpen ? 'rotate-180' : ''"
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            
                                            <div x-show="editSessionTrainerOpen"
                                                 @click.outside="editSessionTrainerOpen = false"
                                                 x-transition
                                                 class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                                <div class="py-1">
                                                    <template x-for="trainer in editTrainers" :key="trainer.id">
                                                        <button type="button"
                                                                @click="selectEditSessionTrainer(trainer.id)"
                                                                class="w-full px-4 py-3 text-left hover:bg-gray-50 transition flex items-center justify-between"
                                                                :class="session.trainer_id == trainer.id ? 'bg-[#10AF13]/5 text-[#10AF13]' : 'text-gray-700'">
                                                            <span class="font-medium" x-text="trainer.name"></span>
                                                            <svg x-show="session.trainer_id == trainer.id" 
                                                                 xmlns="http://www.w3.org/2000/svg" 
                                                                 class="h-5 w-5 text-[#10AF13]" 
                                                                 viewBox="0 0 24 24" 
                                                                 fill="none" 
                                                                 stroke="currentColor" 
                                                                 stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Time --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Waktu Mulai <span class="text-red-500">*</span>
                                                </label>
                                                <input type="time" 
                                                       :name="'sessions[' + index + '][start_time]'"
                                                       x-model="session.start_time"
                                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                                       required>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Waktu Selesai <span class="text-red-500">*</span>
                                                </label>
                                                <input type="time" 
                                                       :name="'sessions[' + index + '][end_time]'"
                                                       x-model="session.end_time"
                                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                                       required>
                                            </div>
                                        </div>

                                        {{-- Zoom Link --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Zoom Link Khusus (Opsional)
                                            </label>
                                            <input type="url" 
                                                   :name="'sessions[' + index + '][zoom_link]'"
                                                   x-model="session.zoom_link"
                                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                                   placeholder="https://zoom.us/j/... (kosongkan jika sama dengan default)">
                                        </div>

                                        {{-- Title --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Judul/Catatan Hari Ini (Opsional)
                                            </label>
                                            <input type="text" 
                                                   :name="'sessions[' + index + '][title]'"
                                                   x-model="session.title"
                                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                                   placeholder="Contoh: Introduction to Python Basics">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- Quota & Zoom Link --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Kuota Peserta & Meeting
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Min Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               name="min_quota" 
                                               x-model="editFormData.min_quota"
                                               min="0"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Max Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               name="max_quota" 
                                               x-model="editFormData.max_quota"
                                               min="1"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Default Zoom Link (Opsional)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" class="text-gray-400">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                        </svg>
                                    </div>
                                    <input type="url" 
                                           name="zoom_link" 
                                           x-model="editFormData.zoom_link"
                                           class="w-full pl-11 pr-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                           placeholder="https://zoom.us/j/...">
                                </div>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                    </svg>
                                    Link meeting untuk peserta (kosongkan jika setiap sesi punya link sendiri)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status Batch <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                x-model="editFormData.status"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                    <button type="button"
                            @click="openEditBatch = false"
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
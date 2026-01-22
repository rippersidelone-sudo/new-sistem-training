{{-- resources/views/coordinator/modals/batch-create.blade.php --}}
{{-- Passed variables: $categories, $trainers --}}

<div x-show="openAddBatch" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openAddBatch = false" 
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        {{-- Header Modal Hijau --}}
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Buat Batch Baru</h2>
                <p class="text-sm opacity-90">Buat batch pelatihan baru dengan jadwal, trainer, dan tugas</p>
            </div>
            <button @click="openAddBatch = false; resetTasks();" 
                    class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body Modal (bisa di-scroll) --}}
        <div class="p-6 overflow-y-auto flex-1">
            {{-- Form --}}
            <form method="POST" action="{{ route('coordinator.batches.store') }}">
                @csrf
                
                <div class="space-y-5">
                    
                    {{-- Informasi Dasar --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Judul Batch <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       value="{{ old('title') }}"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                       placeholder="Contoh: Python Game Developer Batch 1" 
                                       required>
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kategori Pelatihan <span class="text-red-500">*</span>
                                    </label>
                                    <select name="category_id" 
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Trainer <span class="text-red-500">*</span>
                                    </label>
                                    <select name="trainer_id" 
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                            required>
                                        <option value="">Pilih Trainer</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                                {{ $trainer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('trainer_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- Jadwal --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4">Jadwal</h3>
                        
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="start_date" 
                                           value="{{ old('start_date') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="end_date" 
                                           value="{{ old('end_date') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Waktu Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" 
                                           name="start_time" 
                                           value="{{ old('start_time', '09:00') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('start_time')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Waktu Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" 
                                           name="end_time" 
                                           value="{{ old('end_time', '16:00') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('end_time')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- Quota & Zoom Link --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4">Jumlah Peserta dan Link Zoom</h3>
                        
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Min Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="min_quota" 
                                           value="{{ old('min_quota', 5) }}"
                                           min="0"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('min_quota')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Max Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="max_quota" 
                                           value="{{ old('max_quota', 20) }}"
                                           min="1"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                           required>
                                    @error('max_quota')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Zoom Link <span class="text-red-500">*</span>
                                </label>
                                <input type="url" 
                                       name="zoom_link" 
                                       value="{{ old('zoom_link') }}"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                       placeholder="https://zoom.us/j/..."
                                       required>
                                @error('zoom_link')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- Tasks Section --}}
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold text-gray-900">Tugas (Opsional)</h3>
                            <button type="button" 
                                    @click="addTask()" 
                                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                <span class="text-sm">Tambah Tugas</span>
                            </button>
                        </div>

                        {{-- Task List --}}
                        <div class="space-y-4">
                            <template x-for="(task, index) in tasks" :key="index">
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            Tugas <span x-text="(index + 1)"></span>
                                        </h4>
                                        <button type="button"
                                                @click="removeTask(index)" 
                                                class="text-red-600 text-sm font-medium hover:text-red-700 transition">
                                            Hapus
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Tugas</label>
                                            <input type="text" 
                                                   :name="'tasks[' + index + '][title]'"
                                                   x-model="task.title"
                                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                                   placeholder="Judul tugas">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Tugas</label>
                                            <textarea :name="'tasks[' + index + '][description]'"
                                                      x-model="task.description"
                                                      class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition resize-none" 
                                                      rows="4" 
                                                      placeholder="Berikan deskripsi tugas..."></textarea>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deadline</label>
                                            <input type="date" 
                                                   :name="'tasks[' + index + '][deadline]'"
                                                   x-model="task.deadline"
                                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                    <button type="button"
                            @click="openAddBatch = false; resetTasks();"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                        <span class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Buat Batch
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
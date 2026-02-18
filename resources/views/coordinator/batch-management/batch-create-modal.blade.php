{{-- resources/views/coordinator/batch-management/batch-create-modal.blade.php --}}
<div x-show="openAddBatch" x-cloak x-transition 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openAddBatch = false" 
         class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        {{-- Header --}}
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Buat Batch Baru</h2>
                <p class="text-sm opacity-90">Set jadwal pelatihan dengan waktu per hari</p>
            </div>
            <button @click="openAddBatch = false" 
                    class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto flex-1">
            <form method="POST" action="{{ route('coordinator.batches.store') }}" 
                  x-data="batchCreateForm()">
                @csrf
                
                <div class="space-y-6">
                    
                    {{-- SECTION 1: Informasi Dasar --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Informasi Dasar
                        </h3>
                        
                        <div class="space-y-4">
                            {{-- Title --}}
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
                                {{-- Category --}}
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kategori Pelatihan <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <input type="hidden" name="category_id" :value="selectedCategory" required>
                                    
                                    <button type="button"
                                            @click="categoryOpen = !categoryOpen"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition text-left flex items-center justify-between group hover:border-gray-400">
                                        <span :class="selectedCategoryName ? 'text-gray-900' : 'text-gray-400'">
                                            <span x-text="selectedCategoryName || 'Pilih Kategori'"></span>
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="h-5 w-5 text-gray-400 transition-transform"
                                             :class="categoryOpen ? 'rotate-180' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    
                                    <div x-show="categoryOpen"
                                         @click.outside="categoryOpen = false"
                                         x-transition
                                         class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                        <div class="py-1">
                                            @foreach($categories as $category)
                                            <button type="button"
                                                    @click="selectCategory('{{ $category->id }}', '{{ $category->name }}')"
                                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition flex items-center justify-between"
                                                    :class="selectedCategory == '{{ $category->id }}' ? 'bg-[#10AF13]/5 text-[#10AF13]' : 'text-gray-700'">
                                                <span class="font-medium">{{ $category->name }}</span>
                                                <svg x-show="selectedCategory == '{{ $category->id }}'" 
                                                     xmlns="http://www.w3.org/2000/svg" 
                                                     class="h-5 w-5 text-[#10AF13]" 
                                                     viewBox="0 0 24 24" 
                                                     fill="none" 
                                                     stroke="currentColor" 
                                                     stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    @error('category_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                {{-- Main Trainer --}}
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Main Trainer <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <input type="hidden" name="trainer_id" :value="selectedTrainer" required>
                                    
                                    <button type="button"
                                            @click="trainerOpen = !trainerOpen"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition text-left flex items-center justify-between group hover:border-gray-400">
                                        <span :class="selectedTrainerName ? 'text-gray-900' : 'text-gray-400'">
                                            <span x-text="selectedTrainerName || 'Pilih Trainer'"></span>
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="h-5 w-5 text-gray-400 transition-transform"
                                             :class="trainerOpen ? 'rotate-180' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    
                                    <div x-show="trainerOpen"
                                         @click.outside="trainerOpen = false"
                                         x-transition
                                         class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                        <div class="py-1">
                                            @foreach($trainers as $trainer)
                                            <button type="button"
                                                    @click="selectTrainer('{{ $trainer->id }}', '{{ $trainer->name }}')"
                                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition flex items-center justify-between"
                                                    :class="selectedTrainer == '{{ $trainer->id }}' ? 'bg-[#10AF13]/5 text-[#10AF13]' : 'text-gray-700'">
                                                <span class="font-medium">{{ $trainer->name }}</span>
                                                <svg x-show="selectedTrainer == '{{ $trainer->id }}'" 
                                                     xmlns="http://www.w3.org/2000/svg" 
                                                     class="h-5 w-5 text-[#10AF13]" 
                                                     viewBox="0 0 24 24" 
                                                     fill="none" 
                                                     stroke="currentColor" 
                                                     stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    @error('trainer_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-5"></div>

                    {{-- SECTION 2: Range Tanggal Batch --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Periode Batch
                        </h3>
                        
                        <div class="grid sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       x-model="batchStartDate"
                                       @change="generateSessions()"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       x-model="batchEndDate"
                                       @change="generateSessions()"
                                       :min="batchStartDate"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                       required>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div x-show="sessions.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600 flex-shrink-0 mt-0.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-900">
                                        <span x-text="sessions.length"></span> Hari Pelatihan Terdeteksi
                                    </p>
                                    <p class="text-xs text-blue-700 mt-1">
                                        Atur waktu mulai dan selesai untuk setiap hari di bawah ini
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Sessions per Hari (Auto-generated) --}}
                    <div x-show="sessions.length > 0">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-md font-semibold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 12h-3.5M12 7v5" />
                                </svg>
                                Waktu Pelatihan Per Hari
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(session, index) in sessions" :key="session.id">
                                <div class="border border-gray-200 rounded-lg p-4 bg-gradient-to-r from-gray-50 to-white">
                                    {{-- Header Hari --}}
                                    <div class="mb-4 pb-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-lg">
                                                    Hari <span x-text="index + 1"></span>
                                                </h4>
                                                <p class="text-sm text-gray-600 mt-1" x-text="formatDate(session.date)"></p>
                                            </div>
                                            <div class="px-3 py-1 bg-[#10AF13]/10 text-[#10AF13] rounded-full text-xs font-semibold">
                                                Session <span x-text="index + 1"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden inputs --}}
                                    <input type="hidden" :name="'sessions[' + index + '][session_number]'" :value="index + 1">
                                    <input type="hidden" :name="'sessions[' + index + '][start_date]'" :value="session.date">
                                    <input type="hidden" :name="'sessions[' + index + '][end_date]'" :value="session.date">

                                    <div class="grid gap-4">
                                        {{-- Trainer untuk hari ini --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Trainer <span class="text-red-500">*</span>
                                            </label>
                                            <select :name="'sessions[' + index + '][trainer_id]'"
                                                    x-model="session.trainer_id"
                                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                                    required>
                                                <option value="">Pilih Trainer untuk Hari Ini</option>
                                                @foreach($trainers as $trainer)
                                                <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Waktu --}}
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

                                        {{-- Zoom Link Khusus (Optional) --}}
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

                                        {{-- Catatan Hari (Optional) --}}
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

                    {{-- SECTION 4: Kuota & Default Zoom --}}
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" class="text-[#10AF13]">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Kuota Peserta & Default Meeting
                        </h3>
                        
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
                                           value="{{ old('zoom_link') }}"
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
                                    Link default untuk semua hari (kosongkan jika setiap sesi punya link sendiri)
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                    <button type="button"
                            @click="openAddBatch = false"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="sessions.length === 0"
                            :class="sessions.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
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
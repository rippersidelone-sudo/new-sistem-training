{{-- resources/views/trainer/tugas/create-task-modal.blade.php --}}
<div x-show="openCreateModal" x-cloak x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openCreateModal = false"
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold">Buat Tugas Baru</h2>
                <p class="text-sm opacity-90">Tambahkan tugas untuk peserta batch</p>
            </div>
            <button @click="openCreateModal = false" class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <form method="POST" action="{{ route('trainer.tasks.store') }}"
                  x-data="{
                      deadlineDate: '',
                      deadlineTime: '23:59',
                      get deadlineCombined() {
                          return this.deadlineDate && this.deadlineTime
                              ? this.deadlineDate + 'T' + this.deadlineTime
                              : '';
                      }
                  }"
                  @submit="$el.querySelector('[name=deadline]').value = deadlineCombined">
                @csrf

                <div class="space-y-5">

                    {{-- Batch Custom Dropdown --}}
                    <div x-data="{
                            isOpen: false,
                            selectedValue: '{{ old('batch_id') }}',
                            selectedLabel: 'Pilih Batch',
                            options: @js(collect($batches)->map(fn($b) => ['value' => (string)$b['id'], 'label' => $b['label']])->values()),
                            init() {
                                const found = this.options.find(o => o.value === String(this.selectedValue));
                                if (found) this.selectedLabel = found.label;
                            },
                            select(value, label) {
                                this.selectedValue = value;
                                this.selectedLabel = label;
                                this.isOpen = false;
                            }
                        }"
                        @click.outside="isOpen = false">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Batch <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="batch_id" :value="selectedValue">

                        <div class="relative">
                            <button type="button"
                                    @click="isOpen = !isOpen"
                                    :class="isOpen ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                                    class="w-full h-[42px] px-3 py-2 bg-white border rounded-lg cursor-pointer flex justify-between items-center text-sm transition">
                                <span :class="selectedValue ? 'text-gray-900' : 'text-[#737373]'"
                                      x-text="selectedLabel"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     :class="isOpen ? 'rotate-180' : ''"
                                     class="flex-shrink-0 ml-2 transition-transform">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 9l6 6l6 -6" />
                                </svg>
                            </button>

                            <div x-show="isOpen"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-30 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">
                                <template x-for="option in options" :key="option.value">
                                    <div @click="select(option.value, option.label)"
                                         class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100"
                                         :class="selectedValue === option.value ? 'bg-[#10AF13]/5 text-[#10AF13]' : ''">
                                        <span x-text="option.label"></span>
                                        <svg x-show="selectedValue === option.value"
                                             x-cloak
                                             xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('batch_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Judul --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-1 focus:ring-[#10AF13] transition"
                               placeholder="Contoh: Membuat Game Snake dengan Python"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi Tugas <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="5"
                                  class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-1 focus:ring-[#10AF13] transition resize-none"
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Deadline <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
                                <input type="date" x-model="deadlineDate"
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-1 focus:ring-[#10AF13] transition"
                                       required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Jam</label>
                                <input type="time" x-model="deadlineTime"
                                       class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-1 focus:ring-[#10AF13] transition"
                                       required>
                            </div>
                        </div>
                        <input type="hidden" name="deadline" :value="deadlineCombined">
                        @error('deadline')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t">
                    <button type="button" @click="openCreateModal = false"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Buat Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
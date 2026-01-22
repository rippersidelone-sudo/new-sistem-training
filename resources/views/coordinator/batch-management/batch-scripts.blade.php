<script>
function batchManagement() {
    return {
        openAddBatch: false,
        openEditBatch: false,
        openDeleteConfirm: false,
        currentBatchId: null,
        tasks: [],
        editTasks: [],
        
        init() {
            console.log('Batch Management initialized');
            
            // Scroll to top on page load if coming from form submission
            @if(session('success') || session('error'))
                window.scrollTo({ top: 0, behavior: 'smooth' });
            @endif
        },
        
        addTask() {
            this.tasks.push({
                title: '',
                description: '',
                deadline: ''
            });
        },
        
        removeTask(index) {
            this.tasks.splice(index, 1);
        },
        
        resetTasks() {
            this.tasks = [];
        },
        
        addEditTask() {
            this.editTasks.push({
                id: null,
                title: '',
                description: '',
                deadline: '',
                is_active: true
            });
        },
        
        removeEditTask(index) {
            this.editTasks.splice(index, 1);
        },
        
        editBatch(batchId) {
            console.log('Edit batch clicked:', batchId);
            
            // Open modal
            this.openEditBatch = true;
            
            // Fetch batch data
            fetch(`{{ url('coordinator/batches') }}/${batchId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const batch = data.batch;
                const categories = data.categories;
                const trainers = data.trainers;
                const tasks = data.tasks || [];
                
                // Populate Alpine tasks data
                this.editTasks = tasks;
                
                // Build form HTML
                const formHtml = this.buildEditForm(batch, categories, trainers);
                
                document.getElementById('edit-batch-form-container').innerHTML = formHtml;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('edit-batch-form-container').innerHTML = `
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto mb-3 text-red-500">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p class="text-red-500 font-medium">Gagal memuat data batch</p>
                        <p class="text-gray-500 text-sm mt-1">Silakan coba lagi</p>
                    </div>
                `;
            });
        },
        
        buildEditForm(batch, categories, trainers) {
            return `
                <form method="POST" action="{{ url('coordinator/batches') }}/${batch.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="space-y-5">
                        
                        <div>
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Judul Batch <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           value="${this.escapeHtml(batch.title)}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                           required>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Kategori Pelatihan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="category_id" 
                                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                                required>
                                            ${categories.map(cat => `
                                                <option value="${cat.id}" ${cat.id === batch.category_id ? 'selected' : ''}>
                                                    ${this.escapeHtml(cat.name)}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Trainer <span class="text-red-500">*</span>
                                        </label>
                                        <select name="trainer_id" 
                                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                                required>
                                            ${trainers.map(trainer => `
                                                <option value="${trainer.id}" ${trainer.id === batch.trainer_id ? 'selected' : ''}>
                                                    ${this.escapeHtml(trainer.name)}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-5"></div>

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
                                               value="${batch.start_date}"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Tanggal Selesai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" 
                                               name="end_date" 
                                               value="${batch.end_date}"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Waktu Mulai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="time" 
                                               name="start_time" 
                                               value="${batch.start_time}"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Waktu Selesai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="time" 
                                               name="end_time" 
                                               value="${batch.end_time}"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-5"></div>

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
                                               value="${batch.min_quota}"
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
                                               value="${batch.max_quota}"
                                               min="1"
                                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Zoom Link <span class="text-red-500">*</span>
                                    </label>
                                    <input type="url" 
                                           name="zoom_link" 
                                           value="${this.escapeHtml(batch.zoom_link)}"
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition" 
                                           placeholder="https://zoom.us/j/..."
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-5"></div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Status Batch <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer"
                                    required>
                                <option value="Scheduled" ${batch.status === 'Scheduled' ? 'selected' : ''}>Scheduled</option>
                                <option value="Ongoing" ${batch.status === 'Ongoing' ? 'selected' : ''}>Ongoing</option>
                                <option value="Completed" ${batch.status === 'Completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        </div>

                        <div class="border-t pt-5"></div>

                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold text-gray-900">Tugas (Opsional)</h3>
                                <button type="button" 
                                        @click="addEditTask()" 
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

                            <div class="space-y-4">
                                <template x-for="(task, index) in editTasks" :key="index">
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                Tugas <span x-text="(index + 1)"></span>
                                            </h4>
                                            <button type="button"
                                                    @click="removeEditTask(index)" 
                                                    class="text-red-600 text-sm font-medium hover:text-red-700 transition">
                                                Hapus
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <input type="hidden" :name="'tasks[' + index + '][id]'" x-model="task.id">
                                            
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
            `;
        },
        
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        },
        
        deleteBatch(batchId) {
            this.currentBatchId = batchId;
            this.openDeleteConfirm = true;
        },
        
        confirmDelete() {
            document.getElementById('delete-form-' + this.currentBatchId).submit();
        }
    };
}
</script>
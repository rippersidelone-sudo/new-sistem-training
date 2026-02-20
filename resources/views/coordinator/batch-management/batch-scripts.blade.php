{{-- resources/views/coordinator/batch-management/batch-scripts.blade.php --}}
<script>
// ============================================================
// MAIN BATCH MANAGEMENT COMPONENT
// ============================================================
function batchManagement() {
    return {
        // Modal States
        openAddBatch: false,
        openEditBatch: false,
        openDetailBatch: false,
        openDeleteConfirm: false,
        
        // Current IDs
        currentBatchId: null,
        editBatchId: null,
        detailBatchId: null,
        
        // Edit Form States
        editLoading: false,
        editError: false,
        editCategoryOpen: false,
        editTrainerOpen: false,
        editCategories: [],
        editTrainers: [],
        editFormData: {
            title: '',
            category_id: '',
            category_name: '',
            trainer_id: '',
            trainer_name: '',
            min_quota: '',
            max_quota: '',
            zoom_link: '',
            status: 'Scheduled',
            sessions: []
        },
        
        // Detail Modal States
        detailLoading: false,
        detailError: false,
        detailData: {
            id: null,
            code: '',
            title: '',
            category: '',
            status: '',
            participants_count: 0,
            max_quota: 0,
            min_quota: 0,
            zoom_link: '',
            sessions_count: 0,
            sessions: []
        },
        
        // ============================================================
        // INITIALIZATION
        // ============================================================
        init() {
            console.log('Batch Management initialized');
            
            // Scroll to top on page load if coming from form submission
            @if(session('success') || session('error'))
                window.scrollTo({ top: 0, behavior: 'smooth' });
            @endif
        },
        
        // ============================================================
        // VIEW BATCH DETAIL
        // ============================================================
        viewBatchDetail(batchId) {
            console.log('View batch detail:', batchId);
            
            this.detailBatchId = batchId;
            this.detailLoading = true;
            this.detailError = false;
            this.openDetailBatch = true;
            
            // Fetch batch detail
            fetch(`{{ url('coordinator/batches') }}/${batchId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const batch = data.batch;
                
                // Format sessions with duration calculation
                const formattedSessions = data.sessions.map(session => {
                    const durationMinutes = session.duration_minutes;
                    const durationHours = Math.floor(durationMinutes / 60);
                    const durationRemainingMinutes = durationMinutes % 60;
                    
                    let durationText = '';
                    if (durationHours > 0) {
                        durationText = `${durationHours} jam`;
                        if (durationRemainingMinutes > 0) {
                            durationText += ` ${durationRemainingMinutes} menit`;
                        }
                    } else {
                        durationText = `${durationRemainingMinutes} menit`;
                    }
                    
                    return {
                        id: session.id,
                        session_number: session.session_number,
                        title: session.title,
                        trainer_name: session.trainer_name,
                        start_date: session.start_date,
                        end_date: session.end_date,
                        start_time: session.start_time,
                        end_time: session.end_time,
                        zoom_link: session.zoom_link,
                        notes: session.notes || '',
                        formatted_date: session.formatted_date,
                        duration_text: durationText
                    };
                });
                
                this.detailData = {
                    id: batch.id,
                    code: batch.code,
                    title: batch.title,
                    category: batch.category_name,
                    status: batch.status,
                    participants_count: batch.participants_count || 0,
                    max_quota: batch.max_quota,
                    min_quota: batch.min_quota,
                    zoom_link: batch.zoom_link,
                    sessions_count: formattedSessions.length,
                    sessions: formattedSessions
                };
                
                this.detailLoading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.detailLoading = false;
                this.detailError = true;
            });
        },
        
        // ============================================================
        // EDIT BATCH
        // ============================================================
        editBatch(batchId) {
            console.log('Edit batch clicked:', batchId);
            
            // Close detail modal if open
            this.openDetailBatch = false;
            
            // Reset states
            this.editBatchId = batchId;
            this.editLoading = true;
            this.editError = false;
            this.editCategoryOpen = false;
            this.editTrainerOpen = false;
            
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
                this.editCategories = data.categories;
                this.editTrainers = data.trainers;
                
                // Find category and trainer names
                const category = data.categories.find(c => c.id === batch.category_id);
                const trainer = data.trainers.find(t => t.id === batch.trainer_id);
                
                // Populate form data
                this.editFormData = {
                    title: batch.title,
                    category_id: batch.category_id,
                    category_name: category?.name || '',
                    trainer_id: batch.trainer_id,
                    trainer_name: trainer?.name || '',
                    min_quota: batch.min_quota,
                    max_quota: batch.max_quota,
                    zoom_link: batch.zoom_link,
                    status: batch.status,
                    sessions: data.sessions || []
                };
                
                this.editLoading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.editLoading = false;
                this.editError = true;
            });
        },
        
        selectEditCategory(id, name) {
            this.editFormData.category_id = id;
            this.editFormData.category_name = name;
            this.editCategoryOpen = false;
        },
        
        selectEditTrainer(id, name) {
            this.editFormData.trainer_id = id;
            this.editFormData.trainer_name = name;
            this.editTrainerOpen = false;
        },
        
        // ============================================================
        // DELETE BATCH
        // ============================================================
        deleteBatch(batchId) {
            this.currentBatchId = batchId;
            this.openDeleteConfirm = true;
        },
        
        confirmDelete() {
            document.getElementById('delete-form-' + this.currentBatchId).submit();
        },
        
        // ============================================================
        // HELPER METHODS
        // ============================================================
        resetTasks() {
            // Reset function if needed for create modal
        },
        
        formatEditSessionDate(dateString) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            const date = new Date(dateString);
            const dayName = days[date.getDay()];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            
            return `${dayName}, ${day} ${month} ${year}`;
        },
        
        formatDate(dateString) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            const date = new Date(dateString);
            const dayName = days[date.getDay()];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            
            return `${dayName}, ${day} ${month} ${year}`;
        },
        
        formatBatchCode(id) {
            const year = new Date().getFullYear();
            return `BATCH-${year}-${String(id).padStart(4, '0')}`;
        }
    };
}

// ============================================================
// BATCH CREATE FORM COMPONENT
// ============================================================
function batchCreateForm() {
    return {
        // Category & Trainer
        categoryOpen: false,
        trainerOpen: false,
        selectedCategory: '{{ old('category_id') }}',
        selectedCategoryName: '',
        selectedTrainer: '{{ old('trainer_id') }}',
        selectedTrainerName: '',
        
        // Batch Dates
        batchStartDate: '',
        batchEndDate: '',
        
        // Sessions (auto-generated dari date range)
        sessions: [],
        
        // ✅ FIX: Store trainers data as array of objects
        availableTrainers: @json($trainers->map(fn($t) => ['id' => $t->id, 'name' => $t->name])),
        
        selectCategory(id, name) {
            this.selectedCategory = id;
            this.selectedCategoryName = name;
            this.categoryOpen = false;
        },
        
        selectTrainer(id, name) {
            this.selectedTrainer = id;
            this.selectedTrainerName = name;
            this.trainerOpen = false;
            
            // Auto-fill trainer untuk semua sessions
            this.sessions.forEach(session => {
                if (!session.trainer_id) {
                    session.trainer_id = id;
                }
            });
        },
        
        generateSessions() {
            if (!this.batchStartDate || !this.batchEndDate) {
                this.sessions = [];
                return;
            }
            
            const start = new Date(this.batchStartDate);
            const end = new Date(this.batchEndDate);
            
            // Validate
            if (end < start) {
                alert('Tanggal selesai harus setelah tanggal mulai');
                this.batchEndDate = '';
                this.sessions = [];
                return;
            }
            
            // Generate sessions untuk setiap hari
            const newSessions = [];
            const currentDate = new Date(start);
            let sessionNumber = 1;
            
            while (currentDate <= end) {
                newSessions.push({
                    id: Date.now() + sessionNumber,
                    session_number: sessionNumber,
                    date: this.formatDateForInput(currentDate),
                    title: '',
                    trainer_id: this.selectedTrainer || '',
                    start_time: '09:00',
                    end_time: '16:00',
                    zoom_link: ''
                });
                
                currentDate.setDate(currentDate.getDate() + 1);
                sessionNumber++;
            }
            
            this.sessions = newSessions;
        },
        
        formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        
        formatDate(dateString) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            const date = new Date(dateString);
            const dayName = days[date.getDay()];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            
            return `${dayName}, ${day} ${month} ${year}`;
        },
        
        // ✅ FIX: Method untuk get trainer name by ID
        getTrainerName(id) {
            if (!id) return 'Pilih Trainer untuk Hari Ini';
            const trainer = this.availableTrainers.find(t => t.id == id);
            return trainer ? trainer.name : 'Pilih Trainer untuk Hari Ini';
        }
    }
}

// ============================================================
// SESSION TRAINER DROPDOWN COMPONENT (for nested use in sessions)
// ============================================================
function sessionTrainerDropdown(session, availableTrainers) {
    return {
        isOpen: false,
        trainers: availableTrainers,
        
        init() {
            // Ensure trainers data is available
            console.log('Session trainer dropdown initialized', this.trainers);
        },
        
        getTrainerName(id) {
            if (!id) return 'Pilih Trainer untuk Hari Ini';
            const trainer = this.trainers.find(t => t.id == id);
            return trainer ? trainer.name : 'Pilih Trainer untuk Hari Ini';
        },
        
        selectTrainer(id, name) {
            session.trainer_id = id;
            this.isOpen = false;
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        
        closeDropdown() {
            this.isOpen = false;
        }
    }
}

// ============================================================
// EDIT SESSION TRAINER DROPDOWN COMPONENT
// ============================================================
function editSessionTrainerDropdown(session, editTrainers) {
    return {
        isOpen: false,
        trainers: editTrainers,
        
        init() {
            console.log('Edit session trainer dropdown initialized', this.trainers);
        },
        
        getTrainerName(id) {
            if (!id) return 'Pilih Trainer';
            const trainer = this.trainers.find(t => t.id == id);
            return trainer ? trainer.name : 'Pilih Trainer';
        },
        
        selectTrainer(id, name) {
            session.trainer_id = id;
            this.isOpen = false;
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        
        closeDropdown() {
            this.isOpen = false;
        }
    }
}

// ============================================================
// COPY TO CLIPBOARD UTILITY
// ============================================================
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link berhasil disalin!');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        alert('Link berhasil disalin!');
    } catch (err) {
        alert('Gagal menyalin link');
    }
    document.body.removeChild(textArea);
}
</script>
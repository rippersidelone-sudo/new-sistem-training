{{-- resources/views/admin/role-permission/modal-create.blade.php --}}

<!-- Modal Tambah User - OPTIMIZED WITH BETTER SPACING -->
<div x-show="openAddUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div @click.outside="openAddUser = false" 
        class="bg-white w-full max-w-xl rounded-2xl shadow-2xl flex flex-col"
        style="max-height: 90vh;">
        
        <!-- Header Modal Hijau -->
        <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0 rounded-t-2xl">
            <div>
                <h2 class="text-xl font-bold">Tambah User</h2>
                <p class="text-sm opacity-90">Tambahkan user baru ke sistem</p>
            </div>
            <button @click="openAddUser = false" class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6l-12 12M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body Modal dengan Scrollable Content -->
        <div class="overflow-y-auto flex-1 px-6 pt-6">
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" id="addUserForm"
                x-data="{
                    selectedRoleId: '{{ old('role_id') }}',
                    selectedRoleName: '',
                    selectedBranchId: '{{ old('branch_id') }}',
                    selectedBranchName: '',
                    showBranch: false,
                    showPassword: false,
                    roleDropdownOpen: false,
                    branchDropdownOpen: false,
                    roles: window.rolesData || [],
                    branches: window.branchesData || [],
                    scrollToElement(elementId) {
                        this.$nextTick(() => {
                            const element = document.getElementById(elementId);
                            if (element) {
                                const modalBody = element.closest('.overflow-y-auto');
                                if (modalBody) {
                                    const elementTop = element.offsetTop;
                                    const dropdownHeight = 240; // max-height dropdown
                                    const offset = 20; // extra padding
                                    
                                    modalBody.scrollTo({
                                        top: elementTop - offset,
                                        behavior: 'smooth'
                                    });
                                }
                            }
                        });
                    },
                    selectRole(roleId, roleName) {
                        this.selectedRoleId = roleId;
                        this.selectedRoleName = roleName;
                        this.roleDropdownOpen = false;
                        
                        const role = this.roles.find(r => r.id == roleId);
                        this.showBranch = role && (role.name === 'Branch Coordinator' || role.name === 'Participant');
                        
                        if (!this.showBranch) {
                            this.selectedBranchId = '';
                            this.selectedBranchName = '';
                        }
                    },
                    selectBranch(branchId, branchName) {
                        this.selectedBranchId = branchId;
                        this.selectedBranchName = branchName;
                        this.branchDropdownOpen = false;
                    }
                }"
                x-init="
                    if (selectedRoleId) {
                        const role = roles.find(r => r.id == selectedRoleId);
                        if (role) {
                            selectedRoleName = role.name;
                            showBranch = role.name === 'Branch Coordinator' || role.name === 'Participant';
                        }
                    }
                    if (selectedBranchId) {
                        const branch = branches.find(b => b.id == selectedBranchId);
                        if (branch) selectedBranchName = branch.name;
                    }
                ">
                @csrf

                <div class="space-y-5 pb-6">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email') }}" placeholder="email@example.com"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                    </div>

                    <!-- Password dengan Toggle -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                placeholder="Minimal 8 karakter"
                                class="w-full px-4 py-3 pr-12 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                            
                            <!-- Toggle Button -->
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" x-cloak>
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" x2="22" y1="2" y2="22"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                    </div>

                    <!-- Role - Custom Dropdown Modern -->
                    <div class="relative z-30" id="roleDropdownContainer">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                        
                        <!-- Hidden Input -->
                        <input type="hidden" name="role_id" :value="selectedRoleId" required>
                        
                        <!-- Dropdown Button -->
                        <button type="button"
                                @click="roleDropdownOpen = !roleDropdownOpen; branchDropdownOpen = false; if(roleDropdownOpen) scrollToElement('roleDropdownContainer')"
                                class="w-full px-4 py-3 bg-white border-2 rounded-lg focus:outline-none transition text-left flex items-center justify-between"
                                :class="roleDropdownOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/30' : 'border-gray-300'">
                            <span :class="selectedRoleName ? 'text-gray-900' : 'text-gray-400'" x-text="selectedRoleName || 'Pilih Role'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                                 class="transition-transform duration-200"
                                 :class="roleDropdownOpen ? 'rotate-180' : ''">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu with Better Spacing -->
                        <div x-show="roleDropdownOpen" 
                             @click.outside="roleDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="absolute w-full mt-2 bg-white border-2 border-[#10AF13] rounded-lg shadow-xl overflow-hidden"
                             style="max-height: 240px;">
                            
                            <div class="overflow-y-auto max-h-full">
                                <template x-for="role in roles" :key="role.id">
                                    <button type="button"
                                            @click="selectRole(role.id, role.name)"
                                            class="w-full px-4 py-3 text-left hover:bg-[#10AF13]/10 transition flex items-center justify-between border-b border-gray-100 last:border-b-0"
                                            :class="selectedRoleId == role.id ? 'bg-[#10AF13]/20' : ''">
                                        <span x-text="role.name" class="text-sm font-medium" :class="selectedRoleId == role.id ? 'text-[#10AF13]' : 'text-gray-700'"></span>
                                        <svg x-show="selectedRoleId == role.id" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-[#10AF13]">
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Cabang (Conditional) - Custom Dropdown Modern -->
                    <div x-show="showBranch" x-transition class="relative z-20" id="branchDropdownContainer">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang <span class="text-red-500">*</span></label>
                        
                        <!-- Hidden Input -->
                        <input type="hidden" name="branch_id" :value="selectedBranchId" :required="showBranch">
                        
                        <!-- Dropdown Button -->
                        <button type="button"
                                @click="branchDropdownOpen = !branchDropdownOpen; roleDropdownOpen = false; if(branchDropdownOpen) scrollToElement('branchDropdownContainer')"
                                class="w-full px-4 py-3 bg-white border-2 rounded-lg focus:outline-none transition text-left flex items-center justify-between"
                                :class="branchDropdownOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/30' : 'border-gray-300'">
                            <span :class="selectedBranchName ? 'text-gray-900' : 'text-gray-400'" x-text="selectedBranchName || 'Pilih Cabang'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                                 class="transition-transform duration-200"
                                 :class="branchDropdownOpen ? 'rotate-180' : ''">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu with Better Spacing -->
                        <div x-show="branchDropdownOpen" 
                             @click.outside="branchDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="absolute w-full mt-2 bg-white border-2 border-[#10AF13] rounded-lg shadow-xl overflow-hidden"
                             style="max-height: 240px;">
                            
                            <div class="overflow-y-auto max-h-full">
                                <template x-for="branch in branches" :key="branch.id">
                                    <button type="button"
                                            @click="selectBranch(branch.id, branch.name)"
                                            class="w-full px-4 py-3 text-left hover:bg-[#10AF13]/10 transition flex items-center justify-between border-b border-gray-100 last:border-b-0"
                                            :class="selectedBranchId == branch.id ? 'bg-[#10AF13]/20' : ''">
                                        <span x-text="branch.name" class="text-sm font-medium" :class="selectedBranchId == branch.id ? 'text-[#10AF13]' : 'text-gray-700'"></span>
                                        <svg x-show="selectedBranchId == branch.id" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-[#10AF13]">
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Wajib untuk Branch Coordinator dan Participant</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tombol Aksi - Sticky Footer -->
        <div class="sticky bottom-0 border-t border-gray-200 bg-white px-6 py-4 flex justify-end gap-3 flex-shrink-0 rounded-b-2xl">
            <button type="button" @click="openAddUser = false"
                    class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                Batal
            </button>
            <button type="submit" form="addUserForm"
                    class="px-6 py-2.5 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                    Simpan User
                </span>
            </button>
        </div>
    </div>
</div>
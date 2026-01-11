@extends('layouts.app')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Role & Permission Management</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola user dan permission sistem</p>
        </div>
        <button @click="openAddUser = true" class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M16 19h6" />
                <path d="M19 16v6" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
            </svg>
            <span>Tambah User</span>
        </button>
    </div>

    <!-- Modal Tambah User -->
    <div x-show="openAddUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.outside="openAddUser = false" 
            class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            
            <!-- Header Modal Hijau -->
            <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
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

            <!-- Body Modal (bisa di-scroll) -->
            <div class="p-6 overflow-y-auto flex-1">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" 
                    x-data="{
                        selectedRoleId: '{{ old('role_id') }}',
                        showToken: false,
                        showBranch: false,
                        showPassword: false,
                        roles: {{ json_encode($roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name])) }}
                    }"
                    x-init="
                        if (selectedRoleId) {
                            const role = roles.find(r => r.id == selectedRoleId);
                            if (role) {
                                showToken = role.name !== 'Participant';
                                showBranch = role.name === 'Branch Coordinator' || role.name === 'Participant';
                            }
                        }
                    ">
                    @csrf

                    <div class="space-y-5">
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
                                    <!-- Eye Open Icon -->
                                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <!-- Eye Closed Icon -->
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

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select name="role_id" required x-model="selectedRoleId"
                                    @change="
                                        const role = roles.find(r => r.id == selectedRoleId);
                                        showToken = role && role.name !== 'Participant';
                                        showBranch = role && (role.name === 'Branch Coordinator' || role.name === 'Participant');
                                    "
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cabang (Conditional) -->
                        <div x-show="showBranch" x-transition>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang <span class="text-red-500">*</span></label>
                            <select name="branch_id" :required="showBranch"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Wajib untuk Branch Coordinator dan Participant</p>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                        <button type="button" @click="openAddUser = false"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-lg shadow-[#10AF13]/30">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Simpan User
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'HQ Curriculum Admin',
            'value'=>$dashboardUserCounts['totalHqCurriculumAdminUsers'] ?? 0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-user-shield"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M6 21v-2a4 4 0 0 1 4 -4h2" /><path d="M22 16c0 4 -2.5 6 -3.5 6s-3.5 -2 -3.5 -6c1 0 2.5 -.5 3.5 -1.5c1 1 2.5 1.5 3.5 1.5z" />
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Training Coordinator',
            'value'=>$dashboardUserCounts['totalTrainingCoordinatorUsers'] ?? 0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" /><path d="M16 3v4" /><path d="M8 3v4" />
                <path d="M4 11h16" /><path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Trainer',
            'value'=>$dashboardUserCounts['totalTrainerUsers'] ?? 0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-chalkboard-teacher">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" />
                <path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001" />
                <path d="M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Branch PIC',
            'value'=>$dashboardUserCounts['totalBranchPicUsers'] ?? 0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-user-pin">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" /><path d="M19 18v.01" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Participant',
            'value'=>$dashboardUserCounts['totalParticipantUsers'] ?? 0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>',
            'color'=>'text-[#5EABD6]'
        ])
    </div>
    
    <!-- Daftar User -->
<div class="grid gap-6 mt-8 px-2">
    <div class="bg-white border rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-semibold">Daftar User</h2>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $users->total() }} user</p>
            </div>
        </div>

        {{-- Filter Bar (Di dalam Daftar User) --}}
        <div class="mb-6">
            <x-filter-bar 
                :action="route('admin.users.index')"
                searchPlaceholder="Cari user (nama, email, role, cabang)..."
                :filters="$filterOptions"
                :activeFiltersCount="$activeFiltersCount"
            />
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                <thead class="bg-[#F1F1F1]">
                    <tr class="text-left text-sm font-semibold text-gray-700">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Cabang</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ badgeRole($user->role?->name) }}">
                                {{ $user->role?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $user->branch?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-4">
                                <button 
                                    @click="
                                        openEditUser = true; 
                                        selectedUser = {
                                            id: {{ $user->id }},
                                            name: '{{ addslashes($user->name) }}',
                                            email: '{{ $user->email }}',
                                            role_id: {{ $user->role_id }},
                                            branch_id: {{ $user->branch_id ?? 'null' }}
                                        };
                                    "
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit text-[#10AF13] hover:text-[#0e8e0f] transition cursor-pointer">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </button>
                                <button type="button"
                                    @click="
                                        deleteUserId = {{ $user->id }};
                                        deleteUserName = '{{ addslashes($user->name) }}';
                                        deleteUserRole = '{{ addslashes($user->role?->name ?? '-') }}';
                                        openDeleteUser = true;
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icon-tabler-trash text-red-600 hover:text-red-700 transition cursor-pointer">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                                    class="text-gray-400 mb-2">
                                    <path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                                <p class="font-medium">Tidak ada user yang ditemukan</p>
                                @if(request()->hasAny(['search', 'role_id', 'branch_id']))
                                    <a href="{{ route('admin.users.index') }}" class="text-[#10AF13] text-sm mt-2 hover:underline">
                                        Reset Filter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="mt-6 border-t pt-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-medium">{{ $users->firstItem() }}</span> 
                    sampai <span class="font-medium">{{ $users->lastItem() }}</span> 
                    dari <span class="font-medium">{{ $users->total() }}</span> user
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif

            <!-- Modal Edit User -->
            <div x-show="openEditUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div @click.outside="openEditUser = false" 
                    class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                    
                    <!-- Header Modal Hijau -->
                    <div class="bg-[#10AF13] px-6 py-5 text-white flex items-center justify-between flex-shrink-0">
                        <div>
                            <h2 class="text-xl font-bold">Edit User</h2>
                            <p class="text-sm opacity-90">Perbarui informasi user</p>
                        </div>
                        <button @click="openEditUser = false" class="text-white hover:text-gray-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6l-12 12M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <div class="p-6 overflow-y-auto flex-1">
                        @if ($errors->any())
                            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" :action="`{{ route('admin.users.index') }}/${selectedUser.id}`"
                            x-data="{
                                editRoleId: selectedUser.role_id,
                                editBranchId: selectedUser.branch_id,
                                editToken: '',
                                showBranchEdit: false,
                                showTokenEdit: false,
                                showPasswordEdit: false,
                                roles: {{ json_encode($roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name])) }},
                                updateFieldsVisibility() {
                                    const role = this.roles.find(r => r.id == this.editRoleId);
                                    
                                    this.showBranchEdit = role && (role.name === 'Branch Coordinator' || role.name === 'Participant');
                                    this.showTokenEdit = role && role.name !== 'Participant';
                                    
                                    if (!this.showBranchEdit) {
                                        this.editBranchId = null;
                                    }
                                    if (!this.showTokenEdit) {
                                        this.editToken = '';
                                    }
                                },
                                generateRandomToken() {
                                    this.editToken = Math.random().toString(36).substring(2, 10).toUpperCase() + 
                                                    Math.random().toString(36).substring(2, 6).toUpperCase();
                                }
                            }"
                            x-init="$watch('editRoleId', () => updateFieldsVisibility()); updateFieldsVisibility()">
                            @csrf
                            @method('PUT')

                            <div class="space-y-5">
                                <!-- Nama -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        name="name" 
                                        required 
                                        :value="selectedUser.name"
                                        placeholder="Masukkan nama lengkap"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                        name="email" 
                                        required 
                                        :value="selectedUser.email"
                                        placeholder="email@example.com"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <select name="role_id" 
                                            required 
                                            x-model="editRoleId"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Cabang (Conditional) -->
                                <div x-show="showBranchEdit" x-transition>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Cabang <span class="text-red-500" x-show="showBranchEdit">*</span>
                                    </label>
                                    <select name="branch_id" 
                                            x-model="editBranchId"
                                            :required="showBranchEdit"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition cursor-pointer">
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Wajib untuk Branch Coordinator dan Participant</p>
                                </div>

                                

                                <!-- Password dengan Toggle (Optional) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Password Baru <span class="text-gray-400">(Opsional)</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showPasswordEdit ? 'text' : 'password'" 
                                            name="password" 
                                            placeholder="Kosongkan jika tidak ingin mengubah"
                                            class="w-full px-4 py-3 pr-12 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/30 transition">
                                        
                                        <!-- Toggle Button -->
                                        <button type="button" 
                                                @click="showPasswordEdit = !showPasswordEdit"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition">
                                            <!-- Eye Open -->
                                            <svg x-show="!showPasswordEdit" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <!-- Eye Closed -->
                                            <svg x-show="showPasswordEdit" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" x-cloak>
                                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                                <line x1="2" x2="22" y1="2" y2="22"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah</p>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="mt-8 flex justify-end gap-4 pt-6 border-t">
                                <button type="button" 
                                        @click="openEditUser = false"
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

            <!-- Modal Delete User -->
            <div x-show="openDeleteUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div @click.outside="openDeleteUser = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Hapus User?</h2>
                    <p class="text-gray-600 mb-6">
                        Yakin ingin menghapus <span class="font-semibold" x-text="deleteUserName"></span> 
                        sebagai <span class="font-semibold" x-text="deleteUserRole"></span>?
                    </p>

                    <div class="flex justify-end gap-4">
                        <button @click="openDeleteUser = false"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <form :action="`/admin/users/${deleteUserId}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Matrix -->
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Permission Matrix
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full rounded-xl overflow-hidden">
                    <thead class="border-b">
                        <tr class="text-center text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3 text-left">Resource</th>
                            <th class="px-4 py-3">HQ Curriculum Admin</th>
                            <th class="px-4 py-3">Training Coordinator</th>
                            <th class="px-4 py-3">Trainer</th>
                            <th class="px-4 py-3">Branch PIC</th>
                            <th class="px-4 py-3">Participant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">
                                Batch Management
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                View
                            </td>
                            <td class="px-4 py-3">
                                View
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">
                                User Management
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">
                                Attendance Management
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                View
                            </td>
                            <td class="px-4 py-3">
                                Self
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">
                                Upload Materials
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                            <td class="px-4 py-3">
                                -
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">
                                Global Reports
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">
                                Own
                            </td>
                            <td class="px-4 py-3">
                                Branch
                            </td>
                            <td class="px-4 py-3">
                                Self
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection
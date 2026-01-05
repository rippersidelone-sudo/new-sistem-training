@extends('layouts.app')

@section('content')
    <div class="px-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Role & Permission Management</h1>
            <p class="text-[#737373] mt-2 font-medium">Kelola user dan permission sistem</p>
        </div>
        <button @click="openAddUser = true" class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 w-fit cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus">
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
        <div @click.outside="openAddUser = false" class="bg-white w-full max-w-xl rounded-2xl p-6 relative">
            <button @click="openAddUser = false" class="absolute top-6 right-6 text-[#737373] hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
            <div class="flex justify-between items-center mb-4 p-2">
                <div>
                    <h2 class="text-xl font-semibold">Tambah User</h2>
                    <p class="text-[#737373]">Tambahkan user baru ke sistem</p>
                </div>
            </div>
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('users.store') }}">
                <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-1 mx-2 mb-2 pb-7">
                    @csrf
                    <div>
                        <label class="text-md font-semibold text-gray-700">Nama</label>
                        <input type="text" name="name" class="w-full mt-1 px-3 py-2
                            border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" placeholder="Masukkan nama" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">Email</label>
                        <input type="email" name="email"
                            class="w-full mt-1 px-3 py-2
                            border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" placeholder="email@example.com" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">Password</label>
                        <input type="password" name="password" class="w-full mt-1px-3 py-2
                            border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" placeholder="Masukkan password" required>
                    </div>
                    <div>
                        <label class="text-md font-semibold text-gray-700">Role</label>
                        <select id="role" name="role_id" required onchange="toggleTokenField()"
                            class="w-full mt-1 px-3 py-2
                            cursor-pointer border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                            dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium">
                            <option value="">Pilih role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" data-require-token="{{ $role->name !== 'participant' ? '1' : '0' }}">
                                    {{ $role->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Token -->
                    <div class="mt-4 hidden" id="tokenCreateUser">
                        <x-input-label for="tokenInputUser" :value="__('Token Akses')" />
                        <x-text-input id="tokenInputUser"
                                    class="block mt-1 w-full focus:ring-[#10AF13] focus:border-[#10AF13]"
                                    type="password"
                                    name="tokenInputUser"
                                    :value="old('token')"
                                    placeholder="Masukkan token akses" />
                        <x-input-error :messages="$errors->get('token')" class="mt-2" />
                    </div>
                </div>

                <hr class="mt-4 ms-2 me-2">

                <div class="flex justify-end gap-3 pt-4 me-2">
                    <button type="button"
                        @click="openAddUser = false"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] font-medium">
                        Simpan
                    </button>
                </div>
            </form>
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
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Daftar User
                </h2>
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
                        @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ badgeRole($user->role?->name) }}">
                                    {{ $user->role?->description ?? '-' }}
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
                                                name: '{{ $user->name }}',
                                                email: '{{ $user->email }}',
                                                role_id: {{ $user->role_id }}
                                            };
                                        "
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit text-[#10AF13] hover:text-[#0e8e0f]">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        @click="
                                            deleteUserId = {{ $user->id }};
                                            deleteUserName = '{{ $user->name }}';
                                            deleteUserRole = '{{ $user->role?->description ?? '-' }}';
                                            openDeleteUser = true;
                                        ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icon-tabler-trash text-[#ff0000] hover:text-[#E81B1B]">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal Edit User -->
            <div x-show="openEditUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div @click.outside="openEditUser = false" class="bg-white w-full max-w-xl rounded-2xl p-6">
                    <div class="flex justify-between items-center mb-4 p-2">
                        <div>
                            <h2 class="text-xl font-semibold">Edit User</h2>
                            <p class="text-[#737373]">Perbarui informasi user yang sudah terdaftar di sistem</p>
                        </div>
                        <button @click="openEditUser = false" class="text-[#737373] hover:text-black">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if ($errors->any())
                        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" :action="`/users/${selectedUser.id}`">
                        <div class="space-y-4 bg-gray-50 rounded-xl px-6 py-1 mx-2 mb-2 pb-7">
                        @csrf
                        @method('PUT')
                            <div>
                                <label class="text-md font-semibold">Nama</label>
                                <input type="text" name="name" class="w-full mt-1 px-3 py-2
                                    border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                    dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" 
                                    x-model="selectedUser.name" placeholder="Masukkan nama" required>
                            </div>
                            <div>
                                <label class="text-md font-semibold">Email</label>
                                <input type="email" name="email"
                                    class="w-full mt-1 px-3 py-2
                                    border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                    dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium" 
                                    x-model="selectedUser.email" placeholder="email@example.com" required>
                            </div>
                            <div>
                                <label class="text-md font-semibold">Role</label>
                                <select id="edit_role" x-model="selectedUser.role_id" name="role_id" required
                                    class="w-full mt-1 px-3 py-2
                                    border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#10AF13] 
                                    dark:focus:border-[#10AF13] focus:ring-[#10AF13] dark:focus:ring-[#10AF13] rounded-md shadow-sm font-medium cursor-pointer">
                                    <option value="">Pilih role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="mt-4 ms-2 me-2">

                        <div class="flex justify-end gap-3 pt-4 me-2">
                            <button type="button"
                                @click="openEditUser = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f]">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Delete User -->
            <div x-show="openDeleteUser" x-cloak x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div @click.outside="openDeleteUser = false"
                    class="bg-white w-full max-w-md rounded-2xl p-6 space-y-4">

                    <!-- Icon -->
                    <div class="flex justify-center text-[#ff0000]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                            <path d="M22 22l-5 -5" />
                            <path d="M17 22l5 -5" />
                        </svg>
                    </div>

                    <!-- Text -->
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold">Hapus User</h2>
                        <p class="text-gray-600 mt-2">
                            Yakin ingin menghapus
                            <span class="font-semibold" x-text="deleteUserName"></span>
                            sebagai
                            <span class="font-semibold" x-text="deleteUserRole"></span>?
                        </p>
                    </div>

                    <!-- Aksi -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button @click="openDeleteUser = false"
                            class="px-4 py-2 border rounded-lg">
                            Batal
                        </button>
                        <form :action="`/users/${deleteUserId}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-[#ff0000] text-white rounded-lg hover:bg-[#E81B1B]">
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
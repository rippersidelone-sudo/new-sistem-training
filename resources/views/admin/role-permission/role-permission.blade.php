{{-- resources/views/admin/role-permission/role-permission.blade.php --}}
@extends('layouts.admin')

@section('content')
<div x-data="{ 
    openAddUser: false, 
    openEditUser: false, 
    openDeleteUser: false,
    selectedUser: {},
    deleteUserId: null,
    deleteUserName: '',
    deleteUserRole: ''
}">

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

    {{-- Include Modal Tambah User --}}
    @include('admin.role-permission.modal-create')

    {{-- Include Modal Edit User --}}
    @include('admin.role-permission.modal-edit')

    {{-- Include Modal Delete User --}}
    @include('admin.role-permission.modal-delete')

    {{-- Dashboard Cards --}}
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

    {{-- User List Section --}}
    <div class="grid gap-6 mt-8 px-2" data-filter-content>
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar User</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $users->total() }} user</p>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="mb-6">
                <x-filter-bar
                    :action="route('admin.users.index')"
                    searchPlaceholder="Cari user (nama, email, role, cabang)..."
                    :filters="$filterOptions"
                />
            </div>
           
            {{-- Table content --}}
            <div class="overflow-x-auto">
                @if($users->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" 
                            class="mx-auto mb-4 text-gray-400">
                            <path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        <p class="font-medium">Tidak ada user yang ditemukan</p>
                        @if(request()->hasAny(['search', 'role_id', 'branch_id']))
                            <a href="{{ route('admin.users.index') }}" class="text-[#10AF13] text-sm mt-2 hover:underline inline-block">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @else
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
                                        {{ $user->role?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $user->branch?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-4">
                                        {{-- Edit Button - Optimized --}}
                                        <button 
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role_id }}"
                                            data-user-branch="{{ $user->branch_id ?? '' }}"
                                            @click="
                                                openEditUser = true; 
                                                selectedUser = {
                                                    id: parseInt($el.dataset.userId),
                                                    name: $el.dataset.userName,
                                                    email: $el.dataset.userEmail,
                                                    role_id: parseInt($el.dataset.userRole),
                                                    branch_id: $el.dataset.userBranch ? parseInt($el.dataset.userBranch) : null
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
                                        {{-- Delete Button - Optimized --}}
                                        <button type="button"
                                            data-delete-id="{{ $user->id }}"
                                            data-delete-name="{{ $user->name }}"
                                            data-delete-role="{{ $user->role?->name ?? '-' }}"
                                            @click="
                                                deleteUserId = parseInt($el.dataset.deleteId);
                                                deleteUserName = $el.dataset.deleteName;
                                                deleteUserRole = $el.dataset.deleteRole;
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
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination Component --}}
                    <x-pagination :paginator="$users" />
                @endif
            </div>
        </div>
    </div>

    {{-- Permission Matrix --}}
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
                            <td class="px-4 py-3 text-left">Batch Management</td>
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
                            <td class="px-4 py-3">View</td>
                            <td class="px-4 py-3">View</td>
                            <td class="px-4 py-3">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">User Management</td>
                            <td class="px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-check inline-flex mx-auto">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </td>
                            <td class="px-4 py-3">-</td>
                            <td class="px-4 py-3">-</td>
                            <td class="px-4 py-3">-</td>
                            <td class="px-4 py-3">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">Attendance Management</td>
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
                            <td class="px-4 py-3">View</td>
                            <td class="px-4 py-3">Self</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">Upload Materials</td>
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
                            <td class="px-4 py-3">-</td>
                            <td class="px-4 py-3">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-left">Global Reports</td>
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
                            <td class="px-4 py-3">Own</td>
                            <td class="px-4 py-3">Branch</td>
                            <td class="px-4 py-3">Self</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.role-permission.scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
</div>
@endsection
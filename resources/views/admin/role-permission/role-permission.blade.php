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
        <button @click="openAddUser = true"
            class="flex items-center bg-[#0059FF] text-white rounded-lg px-3 gap-3 py-2 cursor-pointer hover:bg-blue-700 transition font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M16 19h6" />
                <path d="M19 16v6" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
            </svg>
            <span>Tambah User</span>
        </button>
    </div>

    @include('admin.role-permission.modal-create')
    @include('admin.role-permission.modal-edit')
    @include('admin.role-permission.modal-delete')

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'HQ Curriculum Admin',
            'value' => $dashboardUserCounts['totalHqCurriculumAdminUsers'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 21v-2a4 4 0 0 1 4 -4h2" /><path d="M22 16c0 4 -2.5 6 -3.5 6s-3.5 -2 -3.5 -6c1 0 2.5 -.5 3.5 -1.5c1 1 2.5 1.5 3.5 1.5z" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /></svg>',
            'color' => 'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Training Coordinator',
            'value' => $dashboardUserCounts['totalTrainingCoordinatorUsers'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Trainer',
            'value' => $dashboardUserCounts['totalTrainerUsers'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 19h-3a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v11a1 1 0 0 1 -1 1" /><path d="M12 14a2 2 0 1 0 4.001 -.001a2 2 0 0 0 -4.001 .001" /><path d="M17 19a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" /></svg>',
            'color' => 'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Branch PIC',
            'value' => $dashboardUserCounts['totalBranchPicUsers'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" /><path d="M19 18v.01" /></svg>',
            'color' => 'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title' => 'Participant',
            'value' => $dashboardUserCounts['totalParticipantUsers'] ?? 0,
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>',
            'color' => 'text-[#5EABD6]'
        ])
    </div>

    {{-- Tabel Daftar User --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold">Daftar User</h2>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $users->total() }} user</p>
                </div>
            </div>

            <div class="mb-6">
                <x-filter-bar
                    :action="route('admin.users.index')"
                    searchPlaceholder="Cari user (nama, username, email, role, cabang)..."
                    :filters="$filterOptions"
                />
            </div>

            <div class="overflow-x-auto">
                @if($users->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                            <path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        <p class="font-medium">Tidak ada user yang ditemukan</p>
                        @if(request()->hasAny(['search', 'role_id', 'branch_id']))
                            <a href="{{ route('admin.users.index') }}"
                                class="text-[#10AF13] text-sm mt-2 hover:underline inline-block">Reset Filter</a>
                        @endif
                    </div>
                @else
                    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-[#F1F1F1]">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Cabang</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @php $offset = ($users->currentPage() - 1) * $users->perPage(); @endphp
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-center text-gray-600 font-medium">
                                    {{ $offset + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->username ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ badgeRole($user->role?->name) }}">
                                        {{ $user->role?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->branch?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-3">
                                        <button
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-username="{{ $user->username ?? '' }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role_id }}"
                                            data-user-branch="{{ $user->branch_id ?? '' }}"
                                            @click="
                                                openEditUser = true;
                                                selectedUser = {
                                                    id: parseInt($el.dataset.userId),
                                                    name: $el.dataset.userName,
                                                    username: $el.dataset.userUsername,
                                                    email: $el.dataset.userEmail,
                                                    role_id: parseInt($el.dataset.userRole),
                                                    branch_id: $el.dataset.userBranch ? parseInt($el.dataset.userBranch) : null
                                                };
                                            "
                                            class="p-1.5 text-[#10AF13] hover:bg-green-50 rounded-lg transition"
                                            title="Edit user">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            data-delete-id="{{ $user->id }}"
                                            data-delete-name="{{ $user->name }}"
                                            data-delete-role="{{ $user->role?->name ?? '-' }}"
                                            @click="
                                                deleteUserId = parseInt($el.dataset.deleteId);
                                                deleteUserName = $el.dataset.deleteName;
                                                deleteUserRole = $el.dataset.deleteRole;
                                                openDeleteUser = true;
                                            "
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus user">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                    <x-pagination :paginator="$users" />
                @endif
            </div>
        </div>
    </div>

    {{-- Permission Matrix --}}
    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold">Permission Matrix</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-center text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3 text-left">Resource</th>
                            <th class="px-4 py-3">HQ Curriculum Admin</th>
                            <th class="px-4 py-3">Training Coordinator</th>
                            <th class="px-4 py-3">Trainer</th>
                            <th class="px-4 py-3">Branch PIC</th>
                            <th class="px-4 py-3">Participant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @php
                            $check = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2.5" class="inline-flex mx-auto"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
                            $matrix = [
                                ['Batch Management',      [$check, $check, 'View',   'View',   '-']],
                                ['User Management',       [$check, '-',    '-',      '-',      '-']],
                                ['Attendance Management', [$check, $check, $check,   'View',   'Self']],
                                ['Upload Materials',      [$check, $check, $check,   '-',      '-']],
                                ['Global Reports',        [$check, $check, 'Own',    'Branch', 'Self']],
                            ];
                        @endphp
                        @foreach($matrix as $index => [$resource, $perms])
                        <tr class="hover:bg-gray-50 transition text-center">
                            <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-left font-medium text-gray-800">{{ $resource }}</td>
                            @foreach($perms as $perm)
                                <td class="px-4 py-3 text-gray-600">{!! $perm !!}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.role-permission.scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
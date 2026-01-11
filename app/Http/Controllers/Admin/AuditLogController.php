<?php
// app/Http/Controllers/Admin/AuditLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Traits\HasAdvancedFilters;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use HasAdvancedFilters;

    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        
        // Get all audit logs (dummy data for now)
        // TODO: Implement proper audit logging with spatie/laravel-activitylog
        $allLogs = collect([
            [
                'id' => 1,
                'action' => 'update_batch',
                'role' => 'Training Coordinator',
                'role_id' => 2,
                'user' => 'Koordinator Pelatihan',
                'description' => 'Update batch: Python Game Developer Batch 1',
                'created_at' => '2025-01-09 11:53:00',
            ],
            [
                'id' => 2,
                'action' => 'create_batch',
                'role' => 'Training Coordinator',
                'role_id' => 2,
                'user' => 'Koordinator Pelatihan',
                'description' => 'Membuat batch baru: Python Game Developer Batch 1',
                'created_at' => '2025-01-08 18:30:00',
            ],
            [
                'id' => 3,
                'action' => 'update',
                'role' => 'Training Coordinator',
                'role_id' => 2,
                'user' => 'Koordinator Pelatihan',
                'description' => 'Mengubah status batch menjadi ONGOING',
                'created_at' => '2025-01-07 16:00:00',
            ],
            [
                'id' => 4,
                'action' => 'approve',
                'role' => 'Branch Coordinator',
                'role_id' => 4,
                'user' => 'PIC Jakarta',
                'description' => 'Menyetujui pendaftaran peserta: Guru Peserta',
                'created_at' => '2025-01-06 22:20:00',
            ],
            [
                'id' => 5,
                'action' => 'validate',
                'role' => 'Trainer',
                'role_id' => 3,
                'user' => 'Ahmad',
                'description' => 'Validasi kehadiran peserta: Guru Peserta',
                'created_at' => '2025-01-05 17:05:00',
            ],
            [
                'id' => 6,
                'action' => 'submit',
                'role' => 'Participant',
                'role_id' => 5,
                'user' => 'Guru Peserta',
                'description' => 'Submit tugas: Game Sederhana dengan Pygame',
                'created_at' => '2025-01-04 22:30:00',
            ],
            [
                'id' => 7,
                'action' => 'create',
                'role' => 'HQ Admin',
                'role_id' => 1,
                'user' => 'Admin Pusat',
                'description' => 'Membuat user baru: Trainer Baru',
                'created_at' => '2025-01-03 10:15:00',
            ],
            [
                'id' => 8,
                'action' => 'delete',
                'role' => 'HQ Admin',
                'role_id' => 1,
                'user' => 'Admin Pusat',
                'description' => 'Menghapus user: User Lama',
                'created_at' => '2025-01-02 14:20:00',
            ],
            [
                'id' => 9,
                'action' => 'reject',
                'role' => 'Training Coordinator',
                'role_id' => 2,
                'user' => 'Koordinator Pelatihan',
                'description' => 'Menolak pendaftaran peserta: Peserta X karena tidak memenuhi syarat',
                'created_at' => '2025-01-01 09:00:00',
            ],
        ]);

        // Apply filters
        $filteredLogs = $allLogs;

        // Search filter
        if ($search = $request->input('search')) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($search) {
                return stripos($log['user'], $search) !== false || 
                       stripos($log['description'], $search) !== false;
            });
        }

        // Action filter
        if ($action = $request->input('action')) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($action) {
                return $log['action'] === $action;
            });
        }

        // Role filter
        if ($roleId = $request->input('role_id')) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($roleId) {
                return $log['role_id'] == $roleId;
            });
        }

        // Count active filters
        $activeFiltersCount = $this->getActiveFiltersCount($request, [
            'search', 'action', 'role_id'
        ]);

        // Build filter options for component
        $filterOptions = [
            [
                'name' => 'action',
                'placeholder' => 'Semua Aksi',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Aksi'],
                    ['value' => 'create', 'label' => 'CREATE'],
                    ['value' => 'update', 'label' => 'UPDATE'],
                    ['value' => 'delete', 'label' => 'DELETE'],
                    ['value' => 'approve', 'label' => 'APPROVE'],
                    ['value' => 'reject', 'label' => 'REJECT'],
                    ['value' => 'validate', 'label' => 'VALIDATE'],
                    ['value' => 'submit', 'label' => 'SUBMIT'],
                ])
            ],
            [
                'name' => 'role_id',
                'placeholder' => 'Semua Role',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Role']
                ])->merge(
                    $roles->map(fn($role) => [
                        'value' => $role->id,
                        'label' => $role->name
                    ])
                )
            ]
        ];

        // Sort by latest
        $auditLogs = $filteredLogs->sortByDesc('created_at')->values();

        return view('admin.audit-log', compact(
            'roles', 
            'auditLogs',
            'filterOptions',
            'activeFiltersCount'
        ));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        
        // Filter parameters
        $search = $request->input('search');
        $action = $request->input('action');
        $roleId = $request->input('role_id');

        // For now, return dummy data
        // In production, you should implement proper audit logging
        // using packages like spatie/laravel-activitylog
        
        $auditLogs = collect([
            [
                'action' => 'UPDATE_BATCH',
                'role' => 'Training Coordinator',
                'user' => 'Koordinator Pelatihan',
                'description' => 'Update batch: Python Game Developer Batch 1',
                'created_at' => '2025-12-02 11:53:00',
            ],
            [
                'action' => 'CREATE_BATCH',
                'role' => 'Training Coordinator',
                'user' => 'Koordinator Pelatihan',
                'description' => 'Membuat batch baru: Python Game Developer Batch 1',
                'created_at' => '2025-11-01 18:30:00',
            ],
            [
                'action' => 'UPDATE_BATCH_STATUS',
                'role' => 'Training Coordinator',
                'user' => 'Koordinator Pelatihan',
                'description' => 'Mengubah status batch menjadi ONGOING',
                'created_at' => '2025-11-10 16:00:00',
            ],
            [
                'action' => 'APPROVE_PARTICIPANT',
                'role' => 'Branch Coordinator',
                'user' => 'PIC Jakarta',
                'description' => 'Menyetujui pendaftaran peserta: Guru Peserta',
                'created_at' => '2025-10-21 22:20:00',
            ],
            [
                'action' => 'VALIDATE_ATTENDANCE',
                'role' => 'Trainer',
                'user' => 'Ahmad',
                'description' => 'Validasi kehadiran peserta: Guru Peserta',
                'created_at' => '2025-11-10 17:05:00',
            ],
            [
                'action' => 'SUBMIT_ASSIGNMENT',
                'role' => 'Participant',
                'user' => 'Guru Peserta',
                'description' => 'Submit tugas: Game Sederhana dengan Pygame',
                'created_at' => '2025-11-12 22:30:00',
            ],
        ]);

        return view('admin.audit-log', compact('roles', 'auditLogs'));
    }
}
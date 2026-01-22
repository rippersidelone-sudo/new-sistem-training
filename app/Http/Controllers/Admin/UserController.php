<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users with advanced filters
     */
    public function index(Request $request)
    {
        // Start query with eager loading
        $query = User::with(['role', 'branch']);

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('role', fn($query) => $query->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('branch', fn($query) => $query->where('name', 'like', "%{$search}%"));
            });
        }

        // Apply role filter
        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }

        // Apply branch filter
        if ($branchId = $request->input('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        // Get paginated results with query string preservation
        $users = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        // Get filter options
        $roles = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        // Build filter options for component
        $filterOptions = [
            [
                'name' => 'role_id',
                'placeholder' => 'Semua Role',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Role']
                ])->merge(
                    $roles->map(fn($role) => [
                        'value' => (string) $role->id,
                        'label' => $role->name
                    ])
                )
            ],
            [
                'name' => 'branch_id',
                'placeholder' => 'Semua Cabang',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Cabang']
                ])->merge(
                    $branches->map(fn($branch) => [
                        'value' => (string) $branch->id,
                        'label' => $branch->name
                    ])
                )
            ]
        ];

        // Count users by role for dashboard cards
        $dashboardUserCounts = [
            'totalHqCurriculumAdminUsers' => User::whereHas('role', fn($q) => $q->where('name', 'HQ Admin'))->count(),
            'totalTrainingCoordinatorUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Training Coordinator'))->count(),
            'totalTrainerUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Trainer'))->count(),
            'totalBranchPicUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Branch Coordinator'))->count(),
            'totalParticipantUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Participant'))->count(),
        ];

        return view('admin.role-permission', compact(
            'users', 
            'roles', 
            'branches', 
            'dashboardUserCounts',
            'filterOptions'
        ));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Get role first to check if branch is required
        $role = Role::find($request->role_id);
        
        // Dynamic validation based on role
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        // Add branch_id validation if role requires it
        if ($role && in_array($role->name, ['Branch Coordinator', 'Participant'])) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable', 'exists:branches,id'];
        }

        $validated = $request->validate($rules, [
            'branch_id.required' => 'Cabang wajib diisi untuk role ' . ($role?->name ?? 'ini'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role' => $user->role->name, 'branch' => $user->branch?->name])
            ->log('created user');

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" berhasil ditambahkan!');
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Get role first to check if branch is required
        $role = Role::find($request->role_id);
        
        // Dynamic validation based on role
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', Password::min(8)],
        ];

        // Add branch_id validation if role requires it
        if ($role && in_array($role->name, ['Branch Coordinator', 'Participant'])) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable', 'exists:branches,id'];
        }

        $validated = $request->validate($rules, [
            'branch_id.required' => 'Cabang wajib diisi untuk role ' . ($role?->name ?? 'ini'),
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'branch_id' => $validated['branch_id'] ?? null,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role' => $user->role->name, 'branch' => $user->branch?->name])
            ->log('updated user');

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri!']);
        }

        // Check if user has related data
        $hasRelations = $user->trainedBatches()->exists() || 
                       $user->participatingBatches()->exists() ||
                       $user->approvedParticipants()->exists();

        if ($hasRelations) {
            return back()->withErrors([
                'error' => 'User "' . $user->name . '" tidak dapat dihapus karena memiliki data terkait (batch, participants, dll). Pertimbangkan untuk menonaktifkan user ini.'
            ]);
        }

        $userName = $user->name;

        // Log before delete
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('deleted user');

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $userName . '" berhasil dihapus!');
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        // Apply same filters as index
        $query = User::with(['role', 'branch']);
        
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('role', fn($query) => $query->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('branch', fn($query) => $query->where('name', 'like', "%{$search}%"));
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }

        if ($branchId = $request->input('branch_id')) {
            $query->where('branch_id', $branchId);
        }
        
        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->streamDownload(function() use ($users) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($handle, ['Nama', 'Email', 'Role', 'Cabang', 'Tanggal Dibuat']);
            
            // Data
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->role?->name ?? '-',
                    $user->branch?->name ?? '-',
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($handle);
        }, 'users-' . date('Y-m-d-His') . '.csv');
    }
}
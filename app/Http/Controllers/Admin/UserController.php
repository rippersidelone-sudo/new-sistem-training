<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Traits\HasAdvancedFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HasAdvancedFilters;

    /**
     * Display a listing of users with advanced filters
     */
    public function index(Request $request)
{
    // Start query
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

    // Get paginated results
    $users = $query->orderBy('created_at', 'desc')
                   ->paginate(15)
                   ->withQueryString(); // PENTING: Preserve query string

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

    // Count active filters
    $activeFiltersCount = collect(['search', 'role_id', 'branch_id'])
        ->filter(fn($key) => $request->filled($key))
        ->count();

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
        'filterOptions',
        'activeFiltersCount'
    ));
}
    public function export(Request $request)
    {
        // Apply same filters as index
        $query = User::with(['role', 'branch']);
        
        $query = $this->applySearch($query, $request, ['name', 'email', 'role.name', 'branch.name']);
        $query = $this->applyFilter($query, $request, 'role_id', 'role_id');
        $query = $this->applyFilter($query, $request, 'branch_id', 'branch_id');
        $query = $this->applyDateRange($query, $request);
        
        $users = $query->get();

        return response()->streamDownload(function() use ($users) {
            $handle = fopen('php://output', 'w');
            
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
        }, 'users-' . date('Y-m-d') . '.csv');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
        'role_id' => ['required', 'exists:roles,id'],
        'branch_id' => ['nullable', 'exists:branches,id'],
        // 'token' tidak lagi divalidasi di sini
    ]);

    $role = Role::findOrFail($request->role_id);

    // Jika role bukan Participant dan ada token yang diisi, simpan ke role (opsional untuk admin)
    if ($role->name !== 'Participant' && $request->filled('token')) {
        $role->update(['access_token' => $request->token]);
    }

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
        'branch_id' => $request->branch_id,
    ]);

    return redirect()->route('admin.users.index')
        ->with('success', 'User berhasil ditambahkan!');
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'role_id' => ['required', 'exists:roles,id'],
        'branch_id' => ['nullable', 'exists:branches,id'],
        'password' => ['nullable', 'string', 'min:8'],
        // token tidak divalidasi lagi
    ]);

    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'branch_id' => $request->branch_id,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    // Opsional: update access_token role jika diisi dan bukan Participant
    $newRole = Role::find($request->role_id);
    if ($newRole->name !== 'Participant' && $request->filled('token')) {
        $newRole->update(['access_token' => $request->token]);
    }

    return redirect()->route('admin.users.index')
        ->with('success', 'User berhasil diperbarui!');
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

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $userName . '" berhasil dihapus!');
    }
}
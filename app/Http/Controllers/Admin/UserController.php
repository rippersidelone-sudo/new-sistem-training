<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users (untuk role-permission page)
     */
    public function index()
    {
        $users = User::with(['role', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        // Count users by role for dashboard cards
        $dashboardUserCounts = [
            'totalHqCurriculumAdminUsers' => User::whereHas('role', fn($q) => $q->where('name', 'HQ Admin'))->count(),
            'totalTrainingCoordinatorUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Training Coordinator'))->count(),
            'totalTrainerUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Trainer'))->count(),
            'totalBranchPicUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Branch Coordinator'))->count(),
            'totalParticipantUsers' => User::whereHas('role', fn($q) => $q->where('name', 'Participant'))->count(),
        ];

        return view('admin.role-permission', compact('users', 'roles', 'branches', 'dashboardUserCounts'));
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
            'tokenInputUser' => ['nullable', 'string'],
        ]);

        // Verify token for non-participant roles
        $role = Role::find($request->role_id);
        if ($role->name !== 'Participant' && $request->tokenInputUser !== $role->access_token) {
            return back()->withErrors(['tokenInputUser' => 'Token akses tidak valid untuk role ini.']);
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

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'branch_id' => $request->branch_id,
        ]);

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

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
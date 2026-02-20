<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Cache::remember('roles_list_simple', 86400, function () {
            return Role::orderBy('name')->get(['id', 'name']);
        });

        $branches = Cache::remember('branches_list_simple', 86400, function () {
            return Branch::orderBy('name')->get(['id', 'name']);
        });

        $query = User::query()
            ->select('users.id', 'users.name', 'users.username', 'users.email', 'users.role_id', 'users.branch_id')
            ->with([
                'role:id,name',
                'branch:id,name'
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('role', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('branch', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }

        if ($branchId = $request->input('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        $users = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $filterOptions = [
            [
                'name'        => 'role_id',
                'placeholder' => 'Semua Role',
                'options'     => collect([['value' => '', 'label' => 'Semua Role']])->merge(
                    $roles->map(fn($role) => ['value' => (string) $role->id, 'label' => $role->name])
                )->toArray(),
            ],
            [
                'name'        => 'branch_id',
                'placeholder' => 'Semua Cabang',
                'options'     => collect([['value' => '', 'label' => 'Semua Cabang']])->merge(
                    $branches->map(fn($branch) => ['value' => (string) $branch->id, 'label' => $branch->name])
                )->toArray(),
            ],
        ];

        $dashboardUserCounts = Cache::remember('user_counts_dashboard', 600, function () use ($roles) {
            $counts    = [];
            $roleCounts = User::select('role_id', DB::raw('COUNT(*) as total'))
                ->groupBy('role_id')
                ->pluck('total', 'role_id');

            foreach ($roles as $role) {
                $counts[$role->name] = $roleCounts[$role->id] ?? 0;
            }
            return $counts;
        });

        $dashboardUserCounts = [
            'totalHqCurriculumAdminUsers'      => $dashboardUserCounts['HQ Admin'] ?? 0,
            'totalTrainingCoordinatorUsers'    => $dashboardUserCounts['Training Coordinator'] ?? 0,
            'totalTrainerUsers'                => $dashboardUserCounts['Trainer'] ?? 0,
            'totalBranchPicUsers'              => $dashboardUserCounts['Branch Coordinator'] ?? 0,
            'totalParticipantUsers'            => $dashboardUserCounts['Participant'] ?? 0,
        ];

        return view('admin.role-permission.role-permission', compact(
            'users',
            'roles',
            'branches',
            'dashboardUserCounts',
            'filterOptions'
        ));
    }

    public function store(Request $request)
    {
        $role = Role::findOrFail($request->role_id);

        $rules = [
            'name'     => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
            'role_id'  => ['required', 'exists:roles,id'],
        ];

        if (in_array($role->name, ['Branch Coordinator', 'Participant'])) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable', 'exists:branches,id'];
        }

        $validated = $request->validate($rules, [
            'username.regex'      => 'Username hanya boleh mengandung huruf, angka, titik (.), strip (-), dan underscore (_).',
            'branch_id.required'  => 'Cabang wajib diisi untuk role ' . ($role?->name ?? 'ini'),
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $validated['name'],
                'username'  => $validated['username'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role_id'   => $validated['role_id'],
                'branch_id' => $validated['branch_id'] ?? null,
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties(['role' => $user->role->name, 'branch' => $user->branch?->name])
                ->log('created user');

            Cache::forget('user_counts_dashboard');
            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $user->name . '" berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan user. Silakan coba lagi.'])->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        $role = Role::findOrFail($request->role_id);

        $rules = [
            'name'     => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id, 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'role_id'  => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', Password::min(8)],
        ];

        if (in_array($role->name, ['Branch Coordinator', 'Participant'])) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable', 'exists:branches,id'];
        }

        $validated = $request->validate($rules, [
            'username.regex'      => 'Username hanya boleh mengandung huruf, angka, titik (.), strip (-), dan underscore (_).',
            'branch_id.required'  => 'Cabang wajib diisi untuk role ' . ($role?->name ?? 'ini'),
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name'      => $validated['name'],
                'username'  => $validated['username'],
                'email'     => $validated['email'],
                'role_id'   => $validated['role_id'],
                'branch_id' => $validated['branch_id'] ?? null,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $oldRoleId = $user->role_id;
            $user->update($updateData);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties(['role' => $user->role->name, 'branch' => $user->branch?->name])
                ->log('updated user');

            if ($oldRoleId !== $user->role_id) {
                Cache::forget('user_counts_dashboard');
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $user->name . '" berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui user. Silakan coba lagi.']);
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri!']);
        }

        $hasRelations = $user->trainedBatches()->exists()
            || $user->batchParticipants()->exists()
            || $user->approvedParticipants()->exists();

        if ($hasRelations) {
            return back()->withErrors([
                'error' => 'User "' . $user->name . '" tidak dapat dihapus karena memiliki data terkait.',
            ]);
        }

        DB::beginTransaction();
        try {
            $userName = $user->name;

            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('deleted user');

            $user->delete();
            Cache::forget('user_counts_dashboard');
            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $userName . '" berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus user. Silakan coba lagi.']);
        }
    }

    public function export(Request $request)
    {
        $query = User::with(['role:id,name', 'branch:id,name'])
            ->select('id', 'name', 'username', 'email', 'role_id', 'branch_id', 'created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('role', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('branch', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }
        if ($branchId = $request->input('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Nama', 'Username', 'Email', 'Role', 'Cabang', 'Tanggal Dibuat']);

            $query->chunk(1000, function ($users) use ($handle) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->name,
                        $user->username ?? '-',
                        $user->email,
                        $user->role?->name ?? '-',
                        $user->branch?->name ?? '-',
                        $user->created_at->format('d/m/Y H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 'users-' . date('Y-m-d-His') . '.csv');
    }
}
<?php

// app/Http/Middleware/CheckBranchAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBranchAccess
{
    /**
     * Handle an incoming request.
     * Ensures Branch Coordinator can only access their own branch data
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role->name;

        // HQ Admin and Training Coordinator have access to all branches
        if (in_array($userRole, ['HQ Admin', 'Training Coordinator'])) {
            return $next($request);
        }

        // Branch Coordinator can only access their own branch
        if ($userRole === 'Branch Coordinator') {
            $branchId = $request->route('branch_id') ?? $request->input('branch_id');
            
            if ($branchId && $user->branch_id != $branchId) {
                abort(403, 'Anda hanya dapat mengakses data cabang Anda sendiri.');
            }
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BatchParticipant;
use App\Models\User;

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

        // Branch Coordinator must have a branch assigned
        if ($userRole === 'Branch Coordinator') {
            // Cek apakah user punya branch_id
            if (!$user->branch_id) {
                // Jika AJAX request, return JSON
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum terdaftar di cabang manapun. Hubungi administrator.'
                    ], 403);
                }
                
                abort(403, 'Anda belum terdaftar di cabang manapun. Hubungi administrator.');
            }

            // Jika ada parameter branch_id di route atau request
            $branchId = $request->route('branch_id') ?? $request->input('branch_id');
            
            if ($branchId && $user->branch_id != $branchId) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda hanya dapat mengakses data cabang Anda sendiri.'
                    ], 403);
                }
                
                abort(403, 'Anda hanya dapat mengakses data cabang Anda sendiri.');
            }

            // Validasi tambahan untuk akses user/participant
            // Pastikan user/participant yang diakses adalah dari cabang yang sama
            if ($request->route('user')) {
                $routeUser = $request->route('user');
                
                // PERBAIKAN: Cek apakah sudah object atau masih ID
                if (is_numeric($routeUser)) {
                    $routeUser = User::find($routeUser);
                }
                
                if ($routeUser && $routeUser->branch_id !== $user->branch_id) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda hanya dapat mengakses peserta dari cabang Anda sendiri.'
                        ], 403);
                    }
                    
                    abort(403, 'Anda hanya dapat mengakses peserta dari cabang Anda sendiri.');
                }
            }

            // Validasi untuk BatchParticipant
            if ($request->route('participant')) {
                $participant = $request->route('participant');
                
                // PERBAIKAN: Cek apakah sudah object atau masih ID
                if (is_numeric($participant)) {
                    $participant = BatchParticipant::with('user')->find($participant);
                }
                
                if ($participant && $participant->user && $participant->user->branch_id !== $user->branch_id) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda hanya dapat mengakses data peserta dari cabang Anda sendiri.'
                        ], 403);
                    }
                    
                    abort(403, 'Anda hanya dapat mengakses data peserta dari cabang Anda sendiri.');
                }
            }
        }

        return $next($request);
    }
}
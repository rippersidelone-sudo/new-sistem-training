<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\RoleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Redirect based on user role
        $user = Auth::user(); 
        
        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the redirect path based on user role
     */
    protected function redirectPath($user): string
    {
        if (RoleHelper::isHQAdmin($user)) {
            return route('admin.dashboard');
        }

        if (RoleHelper::isCoordinator($user)) {
            return route('coordinator.dashboard');
        }

        if (RoleHelper::isTrainer($user)) {
            return route('trainer.dashboard');
        }

        if (RoleHelper::isBranchCoordinator($user)) {
            return route('branch_pic.dashboard');
        }

        if (RoleHelper::isParticipant($user)) {
            return route('participant.dashboard');
        }

        // Default fallback
        return route('dashboard');
    }
}
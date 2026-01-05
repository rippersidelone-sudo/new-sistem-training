<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Get all branches for dropdown
        $branches = Branch::orderBy('name')->get();
        
        return view('auth.register', compact('branches'));
    }

    /**
     * Handle an incoming registration request.
     * New users automatically registered as Participant
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'branch_id' => ['required', 'exists:branches,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Get Participant role
        $participantRole = Role::where('name', 'Participant')->first();
        
        if (!$participantRole) {
            return back()->withErrors([
                'role' => 'Sistem error: Role Participant tidak ditemukan. Hubungi administrator.'
            ]);
        }

        $user = User::create([
            'role_id' => $participantRole->id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Auto login after registration
        Auth::login($user);

        // Redirect to participant dashboard
        return redirect()->route('participant.dashboard')
            ->with('success', 'Selamat datang! Akun Anda berhasil dibuat.');
    }
}
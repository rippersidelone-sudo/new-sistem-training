<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Get authenticated user
        $user = $request->user();
        
        // CRITICAL: Check if $user is actually a User object
        if (!$user) {
            abort(401, 'User not authenticated');
        }
        
        // Load relations with error handling
        try {
            $user->load(['role', 'branch']);
        } catch (\Exception $e) {
            Log::error('Error loading user relations in ProfileController: ' . $e->getMessage());
            // Continue without relations if they fail to load
        }
        
        // Get role name safely
        $roleName = $user->role?->name ?? 'Unknown';
        
        // Return view based on role - SEMUA FILE SETTINGS.BLADE.PHP
        switch ($roleName) {
            case 'HQ Admin':
                return view('admin.settings', compact('user'));
            
            case 'Branch Coordinator':
                return view('branch_pic.settings', compact('user'));
            
            case 'Training Coordinator':
                return view('coordinator.settings', compact('user'));
            
            case 'Trainer':
                return view('trainer.settings', compact('user'));
            
            case 'Participant':
                return view('participant.settings', compact('user'));
            
            default:
                return view('profile.edit', compact('user'));
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return Redirect::back()->with('error', 'User tidak terautentikasi');
        }
        
        try {
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Get role name safely
            $roleName = $user->role?->name ?? 'Unknown';
            
            // Redirect based on role - KONSISTEN DENGAN NAMA ROUTE
            $route = match ($roleName) {
                'HQ Admin' => 'admin.settings',
                'Branch Coordinator' => 'branch.settings-branch-pic',
                'Training Coordinator' => 'coordinator.settings',
                'Trainer' => 'trainer.settings',
                'Participant' => 'participant.settings',
                default => 'profile.edit'
            };

            return Redirect::route($route)->with('success', 'Profile berhasil diperbarui!');
            
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return Redirect::back()->with('error', 'Terjadi kesalahan saat memperbarui profile. Silakan coba lagi.');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            if (!$user) {
                return Redirect::back()->with('error', 'User tidak terautentikasi');
            }

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('success', 'Akun berhasil dihapus');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::back()
                ->withErrors($e->errors(), 'userDeletion')
                ->with('error', 'Password yang Anda masukkan salah');
                
        } catch (\Exception $e) {
            Log::error('Error deleting account: ' . $e->getMessage());
            return Redirect::back()->with('error', 'Terjadi kesalahan saat menghapus akun. Silakan coba lagi.');
        }
    }
}
<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BatchController extends Controller
{
    /**
     * Display available batches for registration
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Get user's registered batch IDs
        $registeredBatchIds = $user->participatingBatches()->pluck('batches.id');

        // Query batches
        $query = Batch::with(['category', 'trainer', 'participants'])
            ->where('status', '!=', 'Cancelled');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('trainer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', ucfirst($request->status));
        }

        // Filter by trainer
        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        $batches = $query->orderBy('start_date', 'desc')->get();

        // Add computed properties
        $batches->each(function($batch) use ($registeredBatchIds, $user) {
            $batch->is_registered = $registeredBatchIds->contains($batch->id);
            
            // Get current participants count
            $batch->current_participants = $batch->participants()
                ->wherePivot('status', 'Approved')
                ->count();
            $batch->is_full = $batch->current_participants >= $batch->max_quota;
            
            // Check prerequisites
            $batch->has_prerequisites = $batch->category->prerequisites()->exists();
            
            // Check if prerequisites are met
            if ($batch->has_prerequisites) {
                $batch->prerequisites_met = $this->checkPrerequisitesMet($user, $batch->category);
            } else {
                $batch->prerequisites_met = true;
            }

            // Get registration status if registered
            if ($batch->is_registered) {
                $registration = BatchParticipant::where('batch_id', $batch->id)
                    ->where('user_id', $user->id)
                    ->first();
                $batch->registration_status = $registration?->status ?? 'Unknown';
            }
        });

        // Get filter options
        $categories = Category::orderBy('name')->get();
        $trainers = User::whereHas('role', function($q) {
            $q->where('name', 'Trainer');
        })->orderBy('name')->get();

        return view('participant.pendaftaran', compact('batches', 'categories', 'trainers'));
    }

    /**
     * Show specific batch details
     */
    public function show(Batch $batch): View
    {
        $user = Auth::user();
        
        $batch->load([
            'category.prerequisites',
            'trainer',
            'participants' => function($query) {
                $query->wherePivot('status', 'Approved');
            }
        ]);

        // Check if user is registered
        $isRegistered = $user->participatingBatches()
            ->where('batches.id', $batch->id)
            ->exists();

        // Get registration status if registered
        $registrationStatus = null;
        if ($isRegistered) {
            $registration = BatchParticipant::where('batch_id', $batch->id)
                ->where('user_id', $user->id)
                ->first();
            $registrationStatus = $registration?->status;
        }

        // Current participant count
        $currentParticipants = $batch->participants->count();
        
        // Check if full
        $isFull = $currentParticipants >= $batch->max_quota;

        // Check prerequisites
        $hasPrerequisites = $batch->category->prerequisites()->exists();
        $prerequisitesMet = $this->checkPrerequisitesMet($user, $batch->category);

        return view('participant.batch-detail', compact(
            'batch',
            'isRegistered',
            'registrationStatus',
            'currentParticipants',
            'isFull',
            'hasPrerequisites',
            'prerequisitesMet'
        ));
    }

    /**
     * Register participant to a batch
     */
    public function register(Request $request, Batch $batch): RedirectResponse
    {
        $user = Auth::user();

        // Validation checks
        if ($batch->status === 'Cancelled') {
            return redirect()->back()
                ->with('error', 'Batch ini telah dibatalkan');
        }

        // Check if already registered
        $existingRegistration = BatchParticipant::where('batch_id', $batch->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRegistration) {
            return redirect()->back()
                ->with('warning', 'Anda sudah terdaftar di batch ini');
        }

        // Check quota
        $currentParticipants = BatchParticipant::where('batch_id', $batch->id)
            ->where('status', 'Approved')
            ->count();

        if ($currentParticipants >= $batch->max_quota) {
            return redirect()->back()
                ->with('error', 'Batch sudah penuh');
        }

        // Check prerequisites
        $batch->load('category.prerequisites');
        
        if (!$this->checkPrerequisitesMet($user, $batch->category)) {
            $prerequisites = $batch->category->prerequisites;
            
            // Get completed prerequisite IDs
            $completedPrerequisiteIds = $user->participatingBatches()
                ->whereHas('category', function($query) use ($prerequisites) {
                    $query->whereIn('categories.id', $prerequisites->pluck('id'));
                })
                ->where('batches.status', 'Completed')
                ->wherePivot('status', 'Approved')
                ->distinct()
                ->pluck('category_id');

            $missingPrerequisites = $prerequisites
                ->whereNotIn('id', $completedPrerequisiteIds)
                ->pluck('name')
                ->join(', ');

            return redirect()->back()
                ->with('error', "Anda belum menyelesaikan prerequisite: {$missingPrerequisites}");
        }

        try {
            DB::beginTransaction();

            // Create registration
            BatchParticipant::create([
                'batch_id' => $batch->id,
                'user_id' => $user->id,
                'status' => 'Pending', // Waiting for approval
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pendaftaran berhasil! Menunggu persetujuan dari koordinator.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Check if user has completed prerequisites for a category
     */
    private function checkPrerequisitesMet(User $user, Category $category): bool
    {
        $prerequisites = $category->prerequisites;
        
        if ($prerequisites->isEmpty()) {
            return true;
        }

        $prerequisiteIds = $prerequisites->pluck('id');
        
        // Get completed batches with their categories
        $completedCategoryIds = $user->participatingBatches()
            ->whereHas('category', function($query) use ($prerequisiteIds) {
                $query->whereIn('categories.id', $prerequisiteIds);
            })
            ->where('batches.status', 'Completed')
            ->wherePivot('status', 'Approved')
            ->distinct()
            ->pluck('category_id');
        
        // Check if all prerequisites are completed
        return $prerequisiteIds->count() === $completedCategoryIds->count();
    }
}
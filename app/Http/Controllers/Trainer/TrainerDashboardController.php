<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\TaskSubmission;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerDashboardController extends Controller
{
    /**
     * Display the trainer dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get authenticated trainer
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get all batches assigned to this trainer
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with(['category', 'participants', 'tasks'])
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_batches' => $batches->count(),
            'total_participants' => $this->getTotalParticipants($batches),
            'pending_grading' => $this->getPendingGradingCount($trainer->id),
            'total_materials' => $this->getTotalMaterialsCount($batches),
        ];

        // Get recent batches for display (latest 3)
        $recentBatches = $batches->take(3)->map(function ($batch) {
            return [
                'id' => $batch->id,
                'title' => $batch->title,
                'code' => formatBatchCode($batch->id),
                'date' => formatDate($batch->start_date),
                'participants_count' => $batch->participants()
                    ->wherePivot('status', 'Approved')
                    ->count(),
                'status' => $batch->status,
            ];
        });

        // Count batches by status
        $batchesByStatus = [
            'scheduled' => $batches->where('status', 'Scheduled')->count(),
            'ongoing' => $batches->where('status', 'Ongoing')->count(),
            'completed' => $batches->where('status', 'Completed')->count(),
        ];

        return view('trainer.dashboard', compact(
            'trainer',
            'stats',
            'recentBatches',
            'batchesByStatus'
        ));
    }

    /**
     * Get total approved participants across all batches
     *
     * @param \Illuminate\Database\Eloquent\Collection $batches
     * @return int
     */
    private function getTotalParticipants($batches): int
    {
        return $batches->sum(function ($batch) {
            return $batch->participants()
                ->wherePivot('status', 'Approved')
                ->count();
        });
    }

    /**
     * Get count of pending task submissions
     *
     * @param int $trainerId
     * @return int
     */
    private function getPendingGradingCount(int $trainerId): int
    {
        return TaskSubmission::whereHas('task.batch', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })
        ->where('status', 'Pending')
        ->count();
    }

    /**
     * Get total materials count for trainer's batches
     * Since there's no materials table yet, counting tasks instead
     *
     * @param \Illuminate\Database\Eloquent\Collection $batches
     * @return int
     */
    private function getTotalMaterialsCount($batches): int
    {
        // Placeholder: counting total tasks as "materials"
        // TODO: Replace with actual materials count when table is created
        return $batches->sum(function ($batch) {
            return $batch->tasks()->count();
        });
    }
}
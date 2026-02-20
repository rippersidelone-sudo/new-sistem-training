<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchMaterial;
use App\Models\TaskSubmission;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerDashboardController extends Controller
{
    public function index(): View
    {
        $trainer = Auth::user();

        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        $batches = Batch::where('trainer_id', $trainer->id)
            ->with(['category', 'participants', 'tasks', 'materials'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Scheduled', 'Completed')")
            ->orderBy('start_date', 'desc')
            ->get();

        $stats = [
            'total_batches'      => $batches->count(),
            'total_participants' => $this->getTotalParticipants($batches),
            'pending_grading'    => $this->getPendingGradingCount($trainer->id),
            'total_materials'    => $this->getTotalMaterialsCount($trainer->id),
        ];

        $recentBatches = $batches->take(3)->map(function ($batch) {
            return [
                'id'                 => $batch->id,
                'title'              => $batch->title,
                'code'               => formatBatchCode($batch->id),
                'date'               => formatDate($batch->start_date),
                'participants_count' => $batch->participants()
                    ->wherePivot('status', 'Approved')
                    ->count(),
                'status'             => $batch->status,
            ];
        });

        $batchesByStatus = [
            'scheduled' => $batches->where('status', 'Scheduled')->count(),
            'ongoing'   => $batches->where('status', 'Ongoing')->count(),
            'completed' => $batches->where('status', 'Completed')->count(),
        ];

        return view('trainer.dashboard', compact(
            'trainer',
            'stats',
            'recentBatches',
            'batchesByStatus'
        ));
    }

    private function getTotalParticipants($batches): int
    {
        return $batches->sum(function ($batch) {
            return $batch->participants()
                ->wherePivot('status', 'Approved')
                ->count();
        });
    }

    private function getPendingGradingCount(int $trainerId): int
    {
        return TaskSubmission::whereHas('task.batch', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })
        ->where('status', 'Pending')
        ->count();
    }

    private function getTotalMaterialsCount(int $trainerId): int
    {
        return BatchMaterial::whereHas('batch', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })->count();
    }
}
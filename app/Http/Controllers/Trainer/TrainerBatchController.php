<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class TrainerBatchController extends Controller
{
    /**
     * Display all batches for the trainer
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get all batches with relationships
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with(['category', 'participants', 'tasks', 'attendances'])
            ->orderBy('start_date', 'desc')
            ->get();

        // Group batches by status
        $scheduledBatches = $this->formatBatches(
            $batches->where('status', 'Scheduled')
        );
        
        $ongoingBatches = $this->formatBatches(
            $batches->where('status', 'Ongoing')
        );
        
        $completedBatches = $this->formatBatches(
            $batches->where('status', 'Completed')
        );

        // Count by status
        $statusCounts = [
            'scheduled' => $scheduledBatches->count(),
            'ongoing' => $ongoingBatches->count(),
            'completed' => $completedBatches->count(),
        ];

        return view('trainer.batch.batches', compact(
            'scheduledBatches',
            'ongoingBatches',
            'completedBatches',
            'statusCounts'
        ));
    }

    /**
     * Display the specified batch detail
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\View\View
     */
    public function show(Batch $batch): View
    {
        $trainer = Auth::user();

        // Verify trainer owns this batch
        if ($batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses ke batch ini');
        }

        // Load relationships INCLUDING TASKS
        $batch->load([
            'category',
            'trainer',
            'participants' => function ($query) {
                $query->wherePivot('status', 'Approved')
                    ->with('branch');
            },
            'tasks.submissions', // ✅ Load tasks dengan submissions
            'attendances',
            'feedback'
        ]);

        // Add batch code
        $batch->code = formatBatchCode($batch->id);

        // Calculate batch statistics
        $stats = $this->getBatchStatistics($batch);

        // Get participants list with their progress
        $participants = $this->getParticipantsProgress($batch);

        // Get tasks with submission status
        $tasks = $this->getTasksWithStatus($batch);

        // ✅ FIXED: Update view path ke trainer.batch.batch-detail
        return view('trainer.batch.batch-detail', compact(
            'batch',
            'stats',
            'participants',
            'tasks'
        ));
    }

    /**
     * Format batches collection for display
     *
     * @param \Illuminate\Database\Eloquent\Collection $batches
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function formatBatches($batches)
    {
        return $batches->map(function ($batch) {
            $approvedParticipants = $batch->participants()
                ->wherePivot('status', 'Approved')
                ->count();

            $attendedCount = $batch->attendances()
                ->where('status', 'Approved')
                ->distinct('user_id')
                ->count('user_id');

            $totalTasks = $batch->tasks()->count();
            
            // Count completed participants (attended + filled feedback)
            $completedCount = $batch->participants()
                ->wherePivot('status', 'Approved')
                ->whereHas('attendances', function ($query) use ($batch) {
                    $query->where('batch_id', $batch->id)
                        ->where('status', 'Approved');
                })
                ->whereHas('feedback', function ($query) use ($batch) {
                    $query->where('batch_id', $batch->id);
                })
                ->count();

            return [
                'id' => $batch->id,
                'title' => $batch->title,
                'batch_code' => formatBatchCode($batch->id),
                'category_name' => $batch->category->name ?? '-',
                'start_date' => formatDate($batch->start_date),
                'start_time' => $batch->start_date->format('H:i'),
                'end_time' => $batch->end_date->format('H:i'),
                'zoom_link' => $batch->zoom_link,
                'participants_count' => $approvedParticipants,
                'attendance_count' => $attendedCount,
                'completed_count' => $completedCount,
                'total_tasks' => $totalTasks,
                'status' => $batch->status,
            ];
        });
    }

    /**
     * Get batch statistics
     *
     * @param \App\Models\Batch $batch
     * @return array
     */
    private function getBatchStatistics(Batch $batch): array
    {
        $totalParticipants = $batch->participants()
            ->wherePivot('status', 'Approved')
            ->count();

        $totalAttended = $batch->attendances()
            ->where('status', 'Approved')
            ->distinct('user_id')
            ->count('user_id');

        $totalTasks = $batch->tasks()->count();

        $pendingSubmissions = $batch->tasks()
            ->with('submissions')
            ->get()
            ->sum(function ($task) {
                return $task->submissions()
                    ->where('status', 'Pending')
                    ->count();
            });

        $completedParticipants = $batch->participants()
            ->wherePivot('status', 'Approved')
            ->whereHas('attendances', function ($query) use ($batch) {
                $query->where('batch_id', $batch->id)
                    ->where('status', 'Approved');
            })
            ->whereHas('feedback', function ($query) use ($batch) {
                $query->where('batch_id', $batch->id);
            })
            ->count();

        return [
            'total_participants' => $totalParticipants,
            'total_attended' => $totalAttended,
            'total_tasks' => $totalTasks,
            'pending_submissions' => $pendingSubmissions,
            'completed_participants' => $completedParticipants,
        ];
    }

    /**
     * Get participants with their progress
     *
     * @param \App\Models\Batch $batch
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getParticipantsProgress(Batch $batch)
    {
        return $batch->participants()
            ->wherePivot('status', 'Approved')
            ->with(['branch', 'attendances', 'taskSubmissions', 'feedback'])
            ->get()
            ->map(function ($participant) use ($batch) {
                $attendance = $participant->attendances()
                    ->where('batch_id', $batch->id)
                    ->first();

                $submittedTasks = $participant->taskSubmissions()
                    ->whereHas('task', function ($query) use ($batch) {
                        $query->where('batch_id', $batch->id);
                    })
                    ->where('status', 'Accepted')
                    ->count();

                $totalTasks = $batch->tasks()->count();

                $feedback = $participant->feedback()
                    ->where('batch_id', $batch->id)
                    ->first();

                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'branch' => $participant->branch->name ?? '-',
                    'attendance_status' => $attendance->status ?? 'Absent',
                    'tasks_completed' => $submittedTasks . '/' . $totalTasks,
                    'has_feedback' => $feedback ? true : false,
                    'is_completed' => $attendance && 
                                     $attendance->status === 'Approved' && 
                                     $feedback ? true : false,
                ];
            });
    }

    /**
     * Get tasks with submission status
     *
     * @param \App\Models\Batch $batch
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTasksWithStatus(Batch $batch)
    {
        return $batch->tasks()
            ->with('submissions')
            ->get()
            ->map(function ($task) {
                $totalSubmissions = $task->submissions()->count();
                $pendingSubmissions = $task->submissions()
                    ->where('status', 'Pending')
                    ->count();
                $acceptedSubmissions = $task->submissions()
                    ->where('status', 'Accepted')
                    ->count();

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'deadline' => formatDateTime($task->deadline),
                    'is_overdue' => $task->deadline < now(),
                    'total_submissions' => $totalSubmissions,
                    'pending_submissions' => $pendingSubmissions,
                    'accepted_submissions' => $acceptedSubmissions,
                ];
            });
    }
}
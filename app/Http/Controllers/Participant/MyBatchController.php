<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyBatchController extends Controller
{
    /**
     * Display participant's registered batches
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get all batches where user is participant
        $batches = $user->participatingBatches()
            ->with([
                'category',
                'trainer',
                'materials',
                'tasks' => function($query) {
                    $query->orderBy('deadline', 'desc');
                }
            ])
            ->withPivot('status', 'created_at')
            ->orderBy('start_date', 'desc')
            ->get();

        // Add computed properties for each batch
        $batches->each(function($batch) use ($user) {
            // Get latest attendance status
            $latestAttendance = $user->attendances()
                ->where('batch_id', $batch->id)
                ->latest('attendance_date')
                ->first();
            
            $batch->attendance_status = $latestAttendance?->status ?? 'Belum Check-In';
            
            // Count materials and tasks
            $batch->materials_count = $batch->materials->count();
            $batch->tasks_count = $batch->tasks->count();
            
            // Registration info
            $batch->registration_status = $batch->pivot->status;
            $batch->registered_at = $batch->pivot->created_at;
        });

        return view('participant.pelatihan', compact('batches'));
    }

    /**
     * Show specific batch details for participant
     */
    public function show(Batch $batch): View
    {
        $user = Auth::user();
        
        // Verify user is registered in this batch
        $registration = $user->participatingBatches()
            ->where('batches.id', $batch->id)
            ->first();

        if (!$registration) {
            abort(403, 'Anda tidak terdaftar di batch ini');
        }

        // Load relationships
        $batch->load([
            'category',
            'trainer',
            'materials' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'tasks' => function($query) {
                $query->orderBy('deadline', 'desc');
            }
        ]);

        // Get user's task submissions for this batch
        $taskSubmissions = $user->taskSubmissions()
            ->whereHas('task', function($query) use ($batch) {
                $query->where('batch_id', $batch->id);
            })
            ->with('task')
            ->get()
            ->keyBy('task_id');

        // Add submission status to each task
        $batch->tasks->each(function($task) use ($taskSubmissions) {
            $submission = $taskSubmissions->get($task->id);
            $task->submission_status = $submission?->status ?? 'Not Submitted';
            $task->has_submission = $submission !== null;
            $task->submission = $submission;
        });

        // Get attendance records
        $attendanceRecords = $user->attendances()
            ->where('batch_id', $batch->id)
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Latest attendance status
        $latestAttendance = $attendanceRecords->first();
        $attendanceStatus = $latestAttendance?->status ?? 'Belum Check-In';

        // Registration details
        $registrationStatus = $registration->pivot->status;
        $registeredAt = $registration->pivot->created_at;

        // Schedule/Sessions (if you have a sessions table, otherwise use batch dates)
        // For now, using sample data structure from your view
        $sessions = [
            [
                'title' => 'Pengenalan ' . $batch->category->name,
                'date' => $batch->start_date->format('d/m/Y'),
                'time' => '09:00 - 12:00',
            ],
            [
                'title' => 'Praktik & Workshop',
                'date' => $batch->start_date->format('d/m/Y'),
                'time' => '13:00 - 15:00',
            ],
        ];

        return view('participant.pelatihan', compact(
            'batch',
            'registrationStatus',
            'registeredAt',
            'attendanceStatus',
            'attendanceRecords',
            'taskSubmissions',
            'sessions'
        ));
    }
}
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
        
        $batches = $user->participatingBatches()
            ->with([
                'category',
                'trainer',
                'materials',
                'sessions.trainer',
                'tasks' => function($query) {
                    $query->orderBy('deadline', 'desc');
                }
            ])
            ->withPivot('status', 'created_at', 'rejection_reason') // ✅ tambah rejection_reason
            ->orderBy('start_date', 'desc')
            ->get();

        $batches->each(function($batch) use ($user) {
            $latestAttendance = $user->attendances()
                ->where('batch_id', $batch->id)
                ->latest('attendance_date')
                ->first();
            
            $batch->attendance_status  = $latestAttendance?->status ?? 'Belum Check-In';
            $batch->materials_count    = $batch->materials->count();
            $batch->tasks_count        = $batch->tasks->count();
            $batch->registration_status = $batch->pivot->status;
            $batch->registered_at      = $batch->pivot->created_at;
            $batch->rejection_reason   = $batch->pivot->rejection_reason; // ✅
        });

        return view('participant.pelatihan', compact('batches'));
    }

    /**
     * Show specific batch details for participant
     */
    public function show(Batch $batch): View
    {
        $user = Auth::user();
        
        $registration = $user->participatingBatches()
            ->where('batches.id', $batch->id)
            ->first();

        if (!$registration) {
            abort(403, 'Anda tidak terdaftar di batch ini');
        }

        // Block access if rejected
        if ($registration->pivot->status === 'Rejected') {
            abort(403, 'Pendaftaran Anda di batch ini telah ditolak.');
        }

        $batch->load([
            'category',
            'trainer',
            'sessions.trainer',
            'materials' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'tasks' => function($query) {
                $query->orderBy('deadline', 'desc');
            }
        ]);

        $taskSubmissions = $user->taskSubmissions()
            ->whereHas('task', function($query) use ($batch) {
                $query->where('batch_id', $batch->id);
            })
            ->with('task')
            ->get()
            ->keyBy('task_id');

        $batch->tasks->each(function($task) use ($taskSubmissions) {
            $submission = $taskSubmissions->get($task->id);
            $task->submission_status = $submission?->status ?? 'Not Submitted';
            $task->has_submission    = $submission !== null;
            $task->submission        = $submission;
        });

        $attendanceRecords = $user->attendances()
            ->where('batch_id', $batch->id)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $latestAttendance  = $attendanceRecords->first();
        $attendanceStatus  = $latestAttendance?->status ?? 'Belum Check-In';
        $registrationStatus = $registration->pivot->status;
        $registeredAt       = $registration->pivot->created_at;

        return view('participant.pelatihan', compact(
            'batch',
            'registrationStatus',
            'registeredAt',
            'attendanceStatus',
            'attendanceRecords',
            'taskSubmissions'
        ));
    }
}
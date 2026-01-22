<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display tasks from participant's batches
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Get batch_id filter if provided
        $batchId = $request->input('batch_id');
        
        // Get approved batches
        $batches = $user->participatingBatches()
            ->wherePivot('status', 'Approved')
            ->with('category')
            ->get();

        // Build tasks query
        $tasksQuery = Task::with(['batch.category'])
            ->whereIn('batch_id', $batches->pluck('id'));

        if ($batchId) {
            $tasksQuery->where('batch_id', $batchId);
        }

        $tasks = $tasksQuery->orderBy('deadline', 'desc')->get();

        // Get user's submissions
        $submissions = $user->taskSubmissions()
            ->whereIn('task_id', $tasks->pluck('id'))
            ->get()
            ->keyBy('task_id');

        // Add submission info to each task
        $tasks->each(function($task) use ($submissions) {
            $submission = $submissions->get($task->id);
            $task->has_submission = $submission !== null;
            $task->submission = $submission;
            $task->submission_status = $submission?->status ?? 'Not Submitted';
            $task->is_overdue = $task->deadline < Carbon::now();
        });

        // Statistics
        $totalTasks = $tasks->count();
        $submittedCount = $tasks->filter(fn($t) => $t->has_submission)->count();
        $acceptedCount = $tasks->filter(fn($t) => $t->submission_status === 'Accepted')->count();
        $pendingCount = $tasks->filter(fn($t) => $t->submission_status === 'Pending')->count();
        $rejectedCount = $tasks->filter(fn($t) => $t->submission_status === 'Rejected')->count();

        return view('participant.tugas', compact(
            'tasks',
            'batches',
            'batchId',
            'totalTasks',
            'submittedCount',
            'acceptedCount',
            'pendingCount',
            'rejectedCount'
        ));
    }

    /**
     * Show task details
     */
    public function show(Task $task): View
    {
        $user = Auth::user();
        
        // Verify user is approved in the task's batch
        $isApproved = $user->participatingBatches()
            ->where('batches.id', $task->batch_id)
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isApproved) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini');
        }

        $task->load('batch.category');

        // Get user's submission if exists
        $submission = $user->taskSubmissions()
            ->where('task_id', $task->id)
            ->first();

        $canSubmit = !$submission && $task->deadline >= Carbon::now();
        $canResubmit = $submission && $submission->status === 'Rejected' && $task->deadline >= Carbon::now();

        return view('participant.task-detail', compact(
            'task',
            'submission',
            'canSubmit',
            'canResubmit'
        ));
    }

    /**
     * Submit task
     */
    public function submit(Request $request, Task $task): RedirectResponse
    {
        $user = Auth::user();

        // Verify user is approved participant
        $isApproved = $user->participatingBatches()
            ->where('batches.id', $task->batch_id)
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isApproved) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses ke tugas ini');
        }

        // Check if task is still open
        if ($task->deadline < Carbon::now()) {
            return redirect()->back()
                ->with('error', 'Deadline tugas sudah lewat');
        }

        // Check if already submitted (and not rejected)
        $existingSubmission = TaskSubmission::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['Rejected'])
            ->first();

        if ($existingSubmission) {
            return redirect()->back()
                ->with('warning', 'Anda sudah submit tugas ini');
        }

        // Validation
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'notes' => 'nullable|string|max:1000',
        ], [
            'file.required' => 'File tugas harus diupload',
            'file.max' => 'Ukuran file maksimal 10MB',
            'notes.max' => 'Catatan maksimal 1000 karakter',
        ]);

        try {
            DB::beginTransaction();

            // Upload file
            $file = $request->file('file');
            $filename = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $filename, 'public');

            // Create submission
            TaskSubmission::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'file_path' => $filePath,
                'notes' => $validated['notes'] ?? null,
                'status' => 'Pending',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Tugas berhasil disubmit! Menunggu review dari trainer.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal submit tugas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display all submissions (history)
     */
    public function submissions(): View
    {
        $user = Auth::user();
        
        $submissions = $user->taskSubmissions()
            ->with(['task.batch.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $totalSubmissions = $user->taskSubmissions()->count();
        $pendingCount = $user->taskSubmissions()->where('status', 'Pending')->count();
        $acceptedCount = $user->taskSubmissions()->where('status', 'Accepted')->count();
        $rejectedCount = $user->taskSubmissions()->where('status', 'Rejected')->count();

        return view('participant.submissions', compact(
            'submissions',
            'totalSubmissions',
            'pendingCount',
            'acceptedCount',
            'rejectedCount'
        ));
    }

    /**
     * Download submission file
     */
    public function download(TaskSubmission $submission): RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();

        // Verify ownership
        if ($submission->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke file ini');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($submission->file_path)) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($submission->file_path);
    }
}   
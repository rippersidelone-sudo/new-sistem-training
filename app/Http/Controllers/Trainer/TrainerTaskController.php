<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Models\Task;
use App\Models\Batch;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TrainerTaskController extends Controller
{
    // ============================================================================
    // KELOLA TUGAS (CRUD Tasks)
    // ============================================================================

    /**
     * Display task management page (Kelola Tugas)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function kelolaTugas(Request $request): View
    {
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $search = $request->input('search');
        $batchId = $request->input('batch_id');
        $status = $request->input('status'); // active, overdue

        // Get trainer's batches for filter dropdown
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with('category')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'label' => $batch->title . ' - ' . formatBatchCode($batch->id),
                    'value' => $batch->id,
                ];
            });

        // Build query for tasks
        $tasksQuery = Task::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })
        ->with(['batch.category', 'submissions']);

        // Apply search filter
        if ($search) {
            $tasksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply batch filter
        if ($batchId) {
            $tasksQuery->where('batch_id', $batchId);
        }

        // Apply status filter
        if ($status === 'active') {
            $tasksQuery->where('deadline', '>=', now());
        } elseif ($status === 'overdue') {
            $tasksQuery->where('deadline', '<', now());
        }

        // Get tasks and format for display
        $tasks = $tasksQuery->orderBy('deadline', 'desc')
            ->get()
            ->map(function ($task) {
                $totalSubmissions = $task->submissions()->count();
                $pendingSubmissions = $task->submissions()->where('status', 'Pending')->count();
                $acceptedSubmissions = $task->submissions()->where('status', 'Accepted')->count();
                $rejectedSubmissions = $task->submissions()->where('status', 'Rejected')->count();

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'batch_id' => $task->batch_id,
                    'batch_title' => $task->batch->title,
                    'batch_code' => formatBatchCode($task->batch->id),
                    'deadline' => $task->deadline->format('Y-m-d'),
                    'deadline_formatted' => formatDateTime($task->deadline),
                    'is_overdue' => $task->deadline < now(),
                    'total_submissions' => $totalSubmissions,
                    'pending_submissions' => $pendingSubmissions,
                    'accepted_submissions' => $acceptedSubmissions,
                    'rejected_submissions' => $rejectedSubmissions,
                    'created_at' => formatDate($task->created_at),
                ];
            });

        // Calculate statistics
        $allTasks = Task::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })->get();

        $stats = [
            'total' => $allTasks->count(),
            'active' => $allTasks->where('deadline', '>=', now())->count(),
            'overdue' => $allTasks->where('deadline', '<', now())->count(),
        ];

        // ✅ UPDATE: Path view yang baru
        return view('trainer.tugas.kelola-tugas', compact(
            'tasks',
            'stats',
            'batches',
            'batchId',
            'status',
            'search'
        ));
    }

    /**
     * Store a new task
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
        ], [
            'batch_id.required' => 'Batch harus dipilih',
            'batch_id.exists' => 'Batch tidak valid',
            'title.required' => 'Judul tugas harus diisi',
            'title.max' => 'Judul tugas maksimal 255 karakter',
            'description.required' => 'Deskripsi tugas harus diisi',
            'deadline.required' => 'Deadline harus diisi',
            'deadline.date' => 'Format deadline tidak valid',
            'deadline.after' => 'Deadline harus setelah waktu sekarang',
        ]);

        // Verify trainer owns the batch
        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk batch ini')
                ->withInput();
        }

        try {
            Task::create([
                'batch_id' => $validated['batch_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'deadline' => $validated['deadline'],
            ]);

            return redirect()
                ->route('trainer.kelola-tugas')
                ->with('success', 'Tugas berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat tugas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get task data for edit modal
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Task $task): JsonResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        $data = [
            'id' => $task->id,
            'batch_id' => $task->batch_id,
            'title' => $task->title,
            'description' => $task->description,
            'deadline' => $task->deadline->format('Y-m-d\TH:i'),
        ];

        return response()->json($data);
    }

    /**
     * Update an existing task
     *
     * @param \App\Models\Task $task
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Task $task, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
        ], [
            'batch_id.required' => 'Batch harus dipilih',
            'batch_id.exists' => 'Batch tidak valid',
            'title.required' => 'Judul tugas harus diisi',
            'title.max' => 'Judul tugas maksimal 255 karakter',
            'description.required' => 'Deskripsi tugas harus diisi',
            'deadline.required' => 'Deadline harus diisi',
            'deadline.date' => 'Format deadline tidak valid',
        ]);

        // Verify new batch also belongs to trainer
        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk batch ini')
                ->withInput();
        }

        try {
            $task->update([
                'batch_id' => $validated['batch_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'deadline' => $validated['deadline'],
            ]);

            return redirect()
                ->route('trainer.kelola-tugas')
                ->with('success', 'Tugas berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a task
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        // Check if task has submissions
        $submissionsCount = $task->submissions()->count();
        
        if ($submissionsCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Tugas tidak dapat dihapus karena sudah memiliki {$submissionsCount} submission");
        }

        try {
            $taskTitle = $task->title;
            $task->delete();

            return redirect()
                ->route('trainer.kelola-tugas')
                ->with('success', "Tugas \"{$taskTitle}\" berhasil dihapus");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }

    // ============================================================================
    // PENILAIAN TUGAS (Review Submissions)
    // ============================================================================

    /**
     * Display task submissions for grading
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $batchId = $request->input('batch_id');
        $status = $request->input('status');

        // Get trainer's batches for filter dropdown
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with('category')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'label' => $batch->title . ' - ' . formatBatchCode($batch->id),
                    'value' => $batch->id,
                ];
            });

        // Build query for submissions
        $submissionsQuery = TaskSubmission::whereHas('task.batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })
        ->with(['task.batch.category', 'user.branch']);

        // Apply filters
        if ($batchId) {
            $submissionsQuery->whereHas('task', function ($query) use ($batchId) {
                $query->where('batch_id', $batchId);
            });
        }

        if ($status) {
            $submissionsQuery->where('status', $status);
        }

        // Get submissions and format for display
        $submissions = $submissionsQuery->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'user_name' => $submission->user->name,
                    'user_email' => $submission->user->email,
                    'task_title' => $submission->task->title,
                    'task_description' => $submission->task->description,
                    'batch_title' => $submission->task->batch->title,
                    'batch_code' => formatBatchCode($submission->task->batch->id),
                    'submitted_at' => formatDateTime($submission->created_at),
                    'file_path' => $submission->file_path,
                    'notes' => $submission->notes,
                    'status' => $submission->status,
                    'feedback' => $submission->feedback,
                    'reviewed_by' => $submission->reviewed_by 
                        ? $submission->reviewer->name 
                        : null,
                    'reviewed_at' => $submission->reviewed_at 
                        ? formatDateTime($submission->reviewed_at)
                        : null,
                ];
            });

        // Calculate statistics
        $stats = [
            'pending' => $submissions->where('status', 'Pending')->count(),
            'accepted' => $submissions->where('status', 'Accepted')->count(),
            'rejected' => $submissions->where('status', 'Rejected')->count(),
        ];

        return view('trainer.penilaian-tugas', compact(
            'submissions',
            'stats',
            'batches',
            'batchId',
            'status'
        ));
    }

    /**
     * Get submission detail for review modal
     *
     * @param \App\Models\TaskSubmission $submission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TaskSubmission $submission): JsonResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        $data = [
            'id' => $submission->id,
            'user_name' => $submission->user->name,
            'user_email' => $submission->user->email,
            'task_title' => $submission->task->title,
            'task_description' => $submission->task->description,
            'batch_title' => $submission->task->batch->title,
            'submitted_at' => formatDateTime($submission->created_at),
            'file_path' => $submission->file_path,
            'file_url' => Storage::url($submission->file_path),
            'status' => $submission->status,
            'feedback' => $submission->feedback,
            'notes' => $submission->notes,
        ];

        return response()->json($data);
    }

    /**
     * Accept a task submission
     *
     * @param \App\Models\TaskSubmission $submission
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        // Validate feedback
        $validated = $request->validate([
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Update submission
        $submission->update([
            'status' => 'Accepted',
            'feedback' => $validated['feedback'] ?? 'Tugas diterima',
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Tugas berhasil diterima');
    }

    /**
     * Reject a task submission
     *
     * @param \App\Models\TaskSubmission $submission
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        // Validate feedback (required for rejection)
        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        // Update submission
        $submission->update([
            'status' => 'Rejected',
            'feedback' => $validated['feedback'],
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Tugas ditolak. Peserta akan menerima feedback Anda');
    }

    /**
     * Download submission file
     *
     * @param \App\Models\TaskSubmission $submission
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(TaskSubmission $submission)
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        // Check if file exists
        if (!Storage::exists($submission->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        // Get original filename from path or generate one
        $filename = basename($submission->file_path);
        $downloadName = $submission->user->name . '_' . $submission->task->title . '_' . $filename;

        return Storage::download($submission->file_path, $downloadName);
    }

    /**
     * Bulk accept submissions
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAccept(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:task_submissions,id',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Get submissions
        $submissions = TaskSubmission::whereIn('id', $validated['submission_ids'])
            ->whereHas('task.batch', function ($query) use ($trainer) {
                $query->where('trainer_id', $trainer->id);
            })
            ->where('status', 'Pending')
            ->get();

        if ($submissions->isEmpty()) {
            return redirect()
                ->back()
                ->with('info', 'Tidak ada submission yang dapat divalidasi');
        }

        // Update all submissions
        foreach ($submissions as $submission) {
            $submission->update([
                'status' => 'Accepted',
                'feedback' => $validated['feedback'] ?? 'Tugas diterima (validasi massal)',
                'reviewed_by' => $trainer->id,
                'reviewed_at' => now(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', "Berhasil menerima {$submissions->count()} submission");
    }

    /**
     * Update feedback for existing submission
     *
     * @param \App\Models\TaskSubmission $submission
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFeedback(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        $submission->update([
            'feedback' => $validated['feedback'],
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Feedback berhasil diperbarui');
    }
}
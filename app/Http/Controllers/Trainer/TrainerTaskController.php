<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Models\Batch;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TrainerTaskController extends Controller
{
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
                    'notes' => $submission->notes, // ← TAMBAHAN
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
            'notes' => $submission->notes, // Notes from participant
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
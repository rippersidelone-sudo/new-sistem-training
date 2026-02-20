<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Task;
use App\Models\TaskSubmission;
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
    private function getBatchOptions($trainerId): \Illuminate\Support\Collection
    {
        return Batch::where('trainer_id', $trainerId)
            ->with('category')
            ->orderByRaw("FIELD(status, 'Ongoing', 'Scheduled', 'Completed')")
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                $statusLabel = match($batch->status) {
                    'Ongoing'   => 'ONGOING -',
                    'Scheduled' => 'SCHEDULED -',
                    'Completed' => 'COMPLETED -',
                    default     => '',
                };
                return [
                    'id'    => $batch->id,
                    'label' => $statusLabel . ' ' . $batch->title . ' — ' . formatBatchCode($batch->id),
                    'value' => $batch->id,
                ];
            });
    }

    public function index(Request $request): View
    {
        $trainer = Auth::user();

        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        $batchId = $request->input('batch_id');
        $status  = $request->input('status');
        $search  = $request->input('search');

        $batches = $this->getBatchOptions($trainer->id);

        $submissionsQuery = TaskSubmission::whereHas('task.batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })
        ->with(['task.batch.category', 'user.branch']);

        if ($batchId) {
            $submissionsQuery->whereHas('task', function ($query) use ($batchId) {
                $query->where('batch_id', $batchId);
            });
        }

        if ($status) {
            $submissionsQuery->where('status', $status);
        }

        if ($search) {
            $submissionsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ✅ Gunakan paginate() bukan get() agar x-pagination bisa bekerja
        $submissionsPaginator = $submissionsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Map data untuk view — pakai through() agar tetap paginator
        $submissions = $submissionsPaginator->through(function ($submission) {
            return [
                'id'               => $submission->id,
                'user_name'        => $submission->user->name,
                'user_email'       => $submission->user->email,
                'task_title'       => $submission->task->title,
                'task_description' => $submission->task->description,
                'batch_title'      => $submission->task->batch->title,
                'batch_code'       => formatBatchCode($submission->task->batch->id),
                'submitted_at'     => formatDateTime($submission->created_at),
                'file_path'        => $submission->file_path,
                'notes'            => $submission->notes,
                'status'           => $submission->status,
                'feedback'         => $submission->feedback,
                'reviewed_by'      => $submission->reviewed_by
                    ? optional($submission->reviewer)->name
                    : null,
                'reviewed_at'      => $submission->reviewed_at
                    ? formatDateTime($submission->reviewed_at)
                    : null,
            ];
        });

        // Stats dari semua data (bukan hanya page ini) — query ulang tanpa paginate
        $statsQuery = TaskSubmission::whereHas('task.batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        });

        if ($batchId) {
            $statsQuery->whereHas('task', fn($q) => $q->where('batch_id', $batchId));
        }

        if ($status) {
            $statsQuery->where('status', $status);
        }

        $statsData = $statsQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'pending'  => $statsData['Pending']  ?? 0,
            'accepted' => $statsData['Accepted'] ?? 0,
            'rejected' => $statsData['Rejected'] ?? 0,
        ];

        return view('trainer.penilaian-tugas', compact(
            'submissions', 'stats', 'batches', 'batchId', 'status', 'search'
        ));
    }

    public function kelolaTugas(Request $request): View
    {
        $trainer = Auth::user();

        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        $search  = $request->input('search');
        $batchId = $request->input('batch_id');
        $status  = $request->input('status');

        $batches = $this->getBatchOptions($trainer->id);

        $tasksQuery = Task::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })
        ->with(['batch.category', 'submissions']);

        if ($search) {
            $tasksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($batchId) {
            $tasksQuery->where('batch_id', $batchId);
        }

        if ($status === 'active') {
            $tasksQuery->where('deadline', '>=', now());
        } elseif ($status === 'overdue') {
            $tasksQuery->where('deadline', '<', now());
        }

        $tasks = $tasksQuery->orderBy('deadline', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id'                  => $task->id,
                    'title'               => $task->title,
                    'description'         => $task->description,
                    'batch_id'            => $task->batch_id,
                    'batch_title'         => $task->batch->title,
                    'batch_code'          => formatBatchCode($task->batch->id),
                    'deadline'            => $task->deadline->format('Y-m-d'),
                    'deadline_formatted'  => formatDateTime($task->deadline),
                    'is_overdue'          => $task->deadline < now(),
                    'total_submissions'   => $task->submissions()->count(),
                    'pending_submissions' => $task->submissions()->where('status', 'Pending')->count(),
                    'accepted_submissions'=> $task->submissions()->where('status', 'Accepted')->count(),
                    'rejected_submissions'=> $task->submissions()->where('status', 'Rejected')->count(),
                    'created_at'          => formatDate($task->created_at),
                ];
            });

        $allTasks = Task::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })->get();

        $stats = [
            'total'   => $allTasks->count(),
            'active'  => $allTasks->filter(fn($t) => $t->deadline >= now())->count(),
            'overdue' => $allTasks->filter(fn($t) => $t->deadline < now())->count(),
        ];

        return view('trainer.tugas.kelola-tugas', compact(
            'tasks', 'stats', 'batches', 'batchId', 'status', 'search'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id'    => 'required|exists:batches,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date|after:now',
        ], [
            'batch_id.required'    => 'Batch harus dipilih',
            'title.required'       => 'Judul tugas harus diisi',
            'description.required' => 'Deskripsi tugas harus diisi',
            'deadline.required'    => 'Deadline harus diisi',
            'deadline.after'       => 'Deadline harus setelah waktu sekarang',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk batch ini')->withInput();
        }

        try {
            Task::create([
                'batch_id'    => $validated['batch_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'deadline'    => $validated['deadline'],
            ]);

            return redirect()->route('trainer.kelola-tugas')->with('success', 'Tugas berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tugas: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Task $task): JsonResponse
    {
        $trainer = Auth::user();

        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        return response()->json([
            'id'          => $task->id,
            'batch_id'    => $task->batch_id,
            'title'       => $task->title,
            'description' => $task->description,
            'deadline'    => $task->deadline->format('Y-m-d\TH:i'),
        ]);
    }

    public function update(Task $task, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        $validated = $request->validate([
            'batch_id'    => 'required|exists:batches,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk batch ini')->withInput();
        }

        try {
            $task->update($validated);
            return redirect()->route('trainer.kelola-tugas')->with('success', 'Tugas berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Task $task): RedirectResponse
    {
        $trainer = Auth::user();

        if ($task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk task ini');
        }

        $submissionsCount = $task->submissions()->count();
        if ($submissionsCount > 0) {
            return redirect()->back()
                ->with('error', "Tugas tidak dapat dihapus karena sudah memiliki {$submissionsCount} submission");
        }

        try {
            $taskTitle = $task->title;
            $task->delete();
            return redirect()->route('trainer.kelola-tugas')->with('success', "Tugas \"{$taskTitle}\" berhasil dihapus");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }

    public function show(TaskSubmission $submission): JsonResponse
    {
        $trainer = Auth::user();

        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        return response()->json([
            'id'               => $submission->id,
            'user_name'        => $submission->user->name,
            'user_email'       => $submission->user->email,
            'task_title'       => $submission->task->title,
            'task_description' => $submission->task->description,
            'batch_title'      => $submission->task->batch->title,
            'submitted_at'     => formatDateTime($submission->created_at),
            'file_path'        => $submission->file_path,
            'file_url'         => Storage::url($submission->file_path),
            'status'           => $submission->status,
            'feedback'         => $submission->feedback,
            'notes'            => $submission->notes,
        ]);
    }

    public function accept(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        $validated = $request->validate(['feedback' => 'nullable|string|max:1000']);

        $submission->update([
            'status'      => 'Accepted',
            'feedback'    => $validated['feedback'] ?? 'Tugas diterima',
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil diterima');
    }

    public function reject(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        $validated = $request->validate(['feedback' => 'required|string|max:1000']);

        $submission->update([
            'status'      => 'Rejected',
            'feedback'    => $validated['feedback'],
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tugas ditolak. Peserta akan menerima feedback Anda');
    }

    public function download(TaskSubmission $submission)
    {
        $trainer = Auth::user();

        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403);
        }

        $path = $submission->file_path;

        // Coba disk public dulu, fallback ke local
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, 
                $submission->user->name . '_' . $submission->task->title . '.' . pathinfo($path, PATHINFO_EXTENSION)
            );
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path,
                $submission->user->name . '_' . $submission->task->title . '.' . pathinfo($path, PATHINFO_EXTENSION)
            );
        }

        // Kalau path ada prefix 'public/', strip dulu
        $strippedPath = ltrim(str_replace('public/', '', $path), '/');
        if (Storage::disk('public')->exists($strippedPath)) {
            return Storage::disk('public')->download($strippedPath,
                $submission->user->name . '_' . $submission->task->title . '.' . pathinfo($strippedPath, PATHINFO_EXTENSION)
            );
        }

        abort(404, 'File tidak ditemukan. Path: ' . $path);
    }

    public function updateFeedback(TaskSubmission $submission, Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        if ($submission->task->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk submission ini');
        }

        $validated = $request->validate(['feedback' => 'required|string|max:1000']);

        $submission->update([
            'feedback'    => $validated['feedback'],
            'reviewed_by' => $trainer->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Feedback berhasil diperbarui');
    }
}
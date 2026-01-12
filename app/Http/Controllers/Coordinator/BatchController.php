<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Category;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BatchController extends Controller
{
    /**
     * Display a listing of batches
     */
    public function index(Request $request)
    {
        // Start query with relationships
        $query = Batch::with(['category', 'trainer'])
            ->withCount('batchParticipants');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($query) => $query->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('trainer', fn($query) => $query->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get paginated results
        $batches = $query->orderBy('start_date', 'desc')
            ->paginate(9)
            ->withQueryString();

        // Transform data for view
        $batches->getCollection()->transform(function($batch) {
            return [
                'id' => $batch->id,
                'code' => formatBatchCode($batch->id, $batch->created_at->year),
                'title' => $batch->title,
                'category' => $batch->category->name,
                'trainer' => $batch->trainer->name,
                'start_date' => $batch->start_date,
                'end_date' => $batch->end_date,
                'status' => $batch->status,
                'participants_count' => $batch->batch_participants_count ?? 0,
                'max_quota' => $batch->max_quota,
                'zoom_link' => $batch->zoom_link,
            ];
        });

        // Statistics for cards
        $totalBatches = Batch::count();
        $scheduledBatches = Batch::where('status', 'Scheduled')->count();
        $ongoingBatches = Batch::where('status', 'Ongoing')->count();
        $completedBatches = Batch::where('status', 'Completed')->count();

        // Get data for form dropdowns
        $categories = Category::orderBy('name')->get();
        $trainers = User::where('role_id', 3)->orderBy('name')->get(); // role_id 3 = Trainer

        return view('coordinator.batch-management', compact(
            'batches',
            'totalBatches',
            'scheduledBatches',
            'ongoingBatches',
            'completedBatches',
            'categories',
            'trainers'
        ));
    }

    /**
     * Store a newly created batch with tasks
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'end_time' => ['required', 'date_format:H:i'],
            'min_quota' => ['required', 'integer', 'min:0'],
            'max_quota' => ['required', 'integer', 'min:1', 'gte:min_quota'],
            'zoom_link' => ['required', 'url'],
            
            // Tasks (optional)
            'tasks' => ['nullable', 'array'],
            'tasks.*.title' => ['required_with:tasks', 'string', 'max:255'],
            'tasks.*.description' => ['required_with:tasks', 'string'],
            'tasks.*.deadline' => ['required_with:tasks', 'date'],
        ]);

        DB::beginTransaction();
        try {
            // Combine date and time
            $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);

            // Validate end datetime is after start datetime
            if ($endDateTime->lte($startDateTime)) {
                return back()->withErrors([
                    'end_time' => 'Waktu selesai harus setelah waktu mulai.'
                ])->withInput();
            }

            // Create batch
            $batch = Batch::create([
                'title' => $validated['title'],
                'category_id' => $validated['category_id'],
                'trainer_id' => $validated['trainer_id'],
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'min_quota' => $validated['min_quota'],
                'max_quota' => $validated['max_quota'],
                'zoom_link' => $validated['zoom_link'],
                'status' => 'Scheduled', // Default status
            ]);

            // Create tasks if provided
            if (!empty($validated['tasks'])) {
                foreach ($validated['tasks'] as $taskData) {
                    $batch->tasks()->create([
                        'title' => $taskData['title'],
                        'description' => $taskData['description'],
                        'deadline' => Carbon::parse($taskData['deadline']),
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('coordinator.batches.index')
                ->with('success', 'Batch "' . $batch->title . '" berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified batch with details
     */
    public function show(Batch $batch)
    {
        $batch->load([
            'category',
            'trainer',
            'tasks' => function($query) {
                $query->orderBy('deadline', 'asc');
            },
            'batchParticipants.user',
            'attendances' => function($query) {
                $query->orderBy('attendance_date', 'desc');
            }
        ]);

        return view('coordinator.batches.show', compact('batch'));
    }

    /**
     * Update the specified batch
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'end_time' => ['required', 'date_format:H:i'],
            'min_quota' => ['required', 'integer', 'min:0'],
            'max_quota' => ['required', 'integer', 'min:1', 'gte:min_quota'],
            'zoom_link' => ['required', 'url'],
            'status' => ['required', 'in:Scheduled,Ongoing,Completed'],
            
            // Tasks (for update)
            'tasks' => ['nullable', 'array'],
            'tasks.*.id' => ['nullable', 'exists:tasks,id'],
            'tasks.*.title' => ['required_with:tasks', 'string', 'max:255'],
            'tasks.*.description' => ['required_with:tasks', 'string'],
            'tasks.*.deadline' => ['required_with:tasks', 'date'],
            'tasks.*.is_active' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            // Combine date and time
            $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);

            // Validate end datetime is after start datetime
            if ($endDateTime->lte($startDateTime)) {
                return back()->withErrors([
                    'end_time' => 'Waktu selesai harus setelah waktu mulai.'
                ])->withInput();
            }

            // Update batch
            $batch->update([
                'title' => $validated['title'],
                'category_id' => $validated['category_id'],
                'trainer_id' => $validated['trainer_id'],
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'min_quota' => $validated['min_quota'],
                'max_quota' => $validated['max_quota'],
                'zoom_link' => $validated['zoom_link'],
                'status' => $validated['status'],
            ]);

            // Update or create tasks
            if (!empty($validated['tasks'])) {
                $existingTaskIds = [];
                
                foreach ($validated['tasks'] as $taskData) {
                    if (!empty($taskData['id'])) {
                        // Update existing task
                        $task = Task::find($taskData['id']);
                        if ($task && $task->batch_id === $batch->id) {
                            $task->update([
                                'title' => $taskData['title'],
                                'description' => $taskData['description'],
                                'deadline' => Carbon::parse($taskData['deadline']),
                                'is_active' => $taskData['is_active'] ?? true,
                            ]);
                            $existingTaskIds[] = $task->id;
                        }
                    } else {
                        // Create new task
                        $task = $batch->tasks()->create([
                            'title' => $taskData['title'],
                            'description' => $taskData['description'],
                            'deadline' => Carbon::parse($taskData['deadline']),
                            'is_active' => true,
                        ]);
                        $existingTaskIds[] = $task->id;
                    }
                }

                // Soft delete tasks that were removed
                $batch->tasks()->whereNotIn('id', $existingTaskIds)->delete();
            }

            DB::commit();

            return redirect()->route('coordinator.batches.index')
                ->with('success', 'Batch "' . $batch->title . '" berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified batch
     */
    public function destroy(Batch $batch)
    {
        // Check if batch has approved participants
        $approvedCount = $batch->batchParticipants()->where('status', 'Approved')->count();
        
        if ($approvedCount > 0) {
            return back()->withErrors([
                'error' => 'Batch "' . $batch->title . '" tidak dapat dihapus karena memiliki ' . $approvedCount . ' peserta yang sudah disetujui.'
            ]);
        }

        // Check if batch is ongoing or completed
        if (in_array($batch->status, ['Ongoing', 'Completed'])) {
            return back()->withErrors([
                'error' => 'Batch "' . $batch->title . '" tidak dapat dihapus karena sudah ' . strtolower($batch->status) . '.'
            ]);
        }

        $batchTitle = $batch->title;
        $batch->delete();

        return redirect()->route('coordinator.batches.index')
            ->with('success', 'Batch "' . $batchTitle . '" berhasil dihapus!');
    }

    /**
     * Display batch monitoring page
     */
    public function monitoring(Batch $batch)
    {
        $batch->load([
            'category',
            'trainer',
            'batchParticipants.user.branch',
            'attendances.user',
            'tasks.submissions.user'
        ]);

        // Statistics
        $totalParticipants = $batch->batchParticipants()->where('status', 'Approved')->count();
        $totalAttendances = $batch->attendances()->where('status', 'Approved')->count();
        $totalTasks = $batch->tasks()->count();
        $completedTasks = $batch->tasks()
            ->whereHas('submissions', function($query) {
                $query->where('status', 'Accepted');
            })
            ->count();

        return view('coordinator.monitoring-absensi', compact(
            'batch',
            'totalParticipants',
            'totalAttendances',
            'totalTasks',
            'completedTasks'
        ));
    }
}
<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchSession;
use App\Models\Category;
use App\Models\User;
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
        // Start query with relationships including sessions
        $query = Batch::with(['category', 'trainer', 'sessions.trainer'])
            ->withCount('batchParticipants');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($query) => $query->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('trainer', fn($query) => $query->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('sessions.trainer', fn($query) => $query->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort filter
        $sort = $request->input('sort', 'latest');
        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'start_date_asc') {
            $query->orderBy('start_date', 'asc');
        } elseif ($sort === 'start_date_desc') {
            $query->orderBy('start_date', 'desc');
        }

        // Get paginated results
        $batches = $query->paginate(9)->withQueryString();

        // Transform data for view
        $batches->getCollection()->transform(function($batch) {
            $firstSession = $batch->sessions->first();
            $lastSession = $batch->sessions->last();
            
            return [
                'id' => $batch->id,
                'code' => formatBatchCode($batch->id, $batch->created_at->year),
                'title' => $batch->title,
                'category' => $batch->category->name,
                'trainer' => $batch->trainer->name,
                'start_date' => $firstSession ? $firstSession->start_datetime : $batch->start_date,
                'end_date' => $lastSession ? $lastSession->end_datetime : $batch->end_date,
                'status' => $batch->status,
                'participants_count' => $batch->batch_participants_count ?? 0,
                'max_quota' => $batch->max_quota,
                'zoom_link' => $batch->zoom_link,
                
                // Sessions data
                'sessions_count' => $batch->sessions->count(),
                'sessions' => $batch->sessions,
                'date_range_summary' => $batch->getDateRangeSummary(),
                'trainers_summary' => $batch->getTrainersSummary(),
            ];
        });

        // Statistics for cards
        $totalBatches = Batch::count();
        $scheduledBatches = Batch::where('status', 'Scheduled')->count();
        $ongoingBatches = Batch::where('status', 'Ongoing')->count();
        $completedBatches = Batch::where('status', 'Completed')->count();

        // Get data for form dropdowns
        $categories = Category::orderBy('name')->get();
        $trainers = User::where('role_id', 3)->orderBy('name')->get();

        return view('coordinator.batch-management.batch-management', compact(
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
     * Store a newly created batch with sessions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'min_quota' => ['required', 'integer', 'min:0'],
            'max_quota' => ['required', 'integer', 'min:1', 'gte:min_quota'],
            'zoom_link' => ['nullable', 'url'], // ✅ NULLABLE
            
            // Sessions validation
            'sessions' => ['required', 'array', 'min:1'],
            'sessions.*.session_number' => ['required', 'integer', 'min:1'],
            'sessions.*.trainer_id' => ['required', 'exists:users,id'],
            'sessions.*.start_date' => ['required', 'date'],
            'sessions.*.start_time' => ['required', 'date_format:H:i'],
            'sessions.*.end_date' => ['required', 'date'],
            'sessions.*.end_time' => ['required', 'date_format:H:i'],
            'sessions.*.zoom_link' => ['nullable', 'url'],
            'sessions.*.title' => ['nullable', 'string', 'max:200'],
        ]);

        DB::beginTransaction();
        try {
            // Sort sessions and get date boundaries
            $sessions = collect($validated['sessions'])->sortBy('session_number');
            $firstSession = $sessions->first();
            $lastSession = $sessions->last();
            
            $startDateTime = Carbon::parse($firstSession['start_date'] . ' ' . $firstSession['start_time']);
            $endDateTime = Carbon::parse($lastSession['end_date'] . ' ' . $lastSession['end_time']);

            // Validate overall date range
            if ($endDateTime->lte($startDateTime)) {
                return back()->withErrors([
                    'sessions' => 'Waktu selesai batch harus setelah waktu mulai batch.'
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
                'zoom_link' => $validated['zoom_link'], // Can be null
                'status' => 'Scheduled',
            ]);

            // Create sessions
            foreach ($validated['sessions'] as $sessionData) {
                $sessionStart = Carbon::parse($sessionData['start_date'] . ' ' . $sessionData['start_time']);
                $sessionEnd = Carbon::parse($sessionData['end_date'] . ' ' . $sessionData['end_time']);
                
                // Validate session time
                if ($sessionEnd->lte($sessionStart)) {
                    throw new \Exception('Sesi ke-' . $sessionData['session_number'] . ': Waktu selesai harus setelah waktu mulai');
                }
                
                BatchSession::create([
                    'batch_id' => $batch->id,
                    'trainer_id' => $sessionData['trainer_id'],
                    'session_number' => $sessionData['session_number'],
                    'title' => $sessionData['title'] ?? null,
                    'start_datetime' => $sessionStart,
                    'end_datetime' => $sessionEnd,
                    'zoom_link' => $sessionData['zoom_link'] ?? $validated['zoom_link'], // Use session zoom or default
                ]);
            }

            DB::commit();

            $sessionText = count($validated['sessions']) === 1 ? '1 sesi' : count($validated['sessions']) . ' sesi';
            
            return redirect()->route('coordinator.batches.index')
                ->with('success', 'Batch "' . $batch->title . '" dengan ' . $sessionText . ' berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified batch with details (AJAX for modal)
     */
    public function show(Batch $batch)
    {
        // If AJAX request for modal detail
        if (request()->ajax() || request()->wantsJson()) {
            $batch->load([
                'category',
                'trainer',
                'sessions' => function($query) {
                    $query->orderBy('session_number');
                },
                'sessions.trainer'
            ]);
            
            return response()->json([
                'batch' => [
                    'id' => $batch->id,
                    'code' => formatBatchCode($batch->id, $batch->created_at->year),
                    'title' => $batch->title,
                    'category_id' => $batch->category_id,
                    'category_name' => $batch->category->name,
                    'trainer_id' => $batch->trainer_id,
                    'trainer_name' => $batch->trainer->name,
                    'status' => $batch->status,
                    'min_quota' => $batch->min_quota,
                    'max_quota' => $batch->max_quota,
                    'zoom_link' => $batch->zoom_link,
                    'participants_count' => $batch->participants_count,
                    'sessions_count' => $batch->sessions->count(),
                    'date_range_summary' => $batch->getDateRangeSummary(),
                    'trainers_summary' => $batch->getTrainersSummary(),
                ],
                'sessions' => $batch->sessions->map(function($session) {
                    return [
                        'id' => $session->id,
                        'session_number' => $session->session_number,
                        'title' => $session->title,
                        'trainer_id' => $session->trainer_id,
                        'trainer_name' => $session->trainer->name ?? '',
                        'start_date' => $session->start_datetime->format('Y-m-d'),
                        'start_time' => $session->start_datetime->format('H:i'),
                        'end_date' => $session->end_datetime->format('Y-m-d'),
                        'end_time' => $session->end_datetime->format('H:i'),
                        'zoom_link' => $session->zoom_link,
                        'notes' => $session->notes,
                        'duration_minutes' => $session->getDurationInMinutes(),
                        'formatted_date' => formatDate($session->start_datetime, 'd M Y'),
                    ];
                }),
            ]);
        }
        
        // Regular page view (untuk monitoring page, dll)
        $batch->load([
            'category',
            'trainer',
            'sessions.trainer',
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
     * Show the form for editing the specified batch (AJAX)
     */
    public function edit(Batch $batch)
    {
        // Only respond to AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            $batch->load('category', 'trainer', 'sessions.trainer');
            
            return response()->json([
                'batch' => [
                    'id' => $batch->id,
                    'title' => $batch->title,
                    'category_id' => $batch->category_id,
                    'trainer_id' => $batch->trainer_id,
                    'min_quota' => $batch->min_quota,
                    'max_quota' => $batch->max_quota,
                    'zoom_link' => $batch->zoom_link,
                    'status' => $batch->status,
                ],
                'sessions' => $batch->sessions->map(function($session) {
                    return [
                        'id' => $session->id,
                        'session_number' => $session->session_number,
                        'trainer_id' => $session->trainer_id,
                        'trainer_name' => $session->trainer->name ?? '',
                        'title' => $session->title,
                        'start_date' => $session->start_datetime->format('Y-m-d'),
                        'start_time' => $session->start_datetime->format('H:i'),
                        'end_date' => $session->end_datetime->format('Y-m-d'),
                        'end_time' => $session->end_datetime->format('H:i'),
                        'zoom_link' => $session->zoom_link,
                    ];
                }),
                'categories' => Category::orderBy('name')->get(['id', 'name']),
                'trainers' => User::where('role_id', 3)->orderBy('name')->get(['id', 'name']),
            ]);
        }
        
        return abort(404);
    }

    /**
     * Update the specified batch with sessions
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'trainer_id' => ['required', 'exists:users,id'],
            'min_quota' => ['required', 'integer', 'min:0'],
            'max_quota' => ['required', 'integer', 'min:1', 'gte:min_quota'],
            'zoom_link' => ['nullable', 'url'], // ✅ NULLABLE
            'status' => ['required', 'in:Scheduled,Ongoing,Completed'],
            
            // Sessions validation
            'sessions' => ['required', 'array', 'min:1'],
            'sessions.*.id' => ['nullable', 'exists:batch_sessions,id'],
            'sessions.*.session_number' => ['required', 'integer', 'min:1'],
            'sessions.*.trainer_id' => ['required', 'exists:users,id'],
            'sessions.*.start_date' => ['required', 'date'],
            'sessions.*.start_time' => ['required', 'date_format:H:i'],
            'sessions.*.end_date' => ['required', 'date'],
            'sessions.*.end_time' => ['required', 'date_format:H:i'],
            'sessions.*.zoom_link' => ['nullable', 'url'],
            'sessions.*.title' => ['nullable', 'string', 'max:200'],
        ]);

        DB::beginTransaction();
        try {
            // Sort sessions and get date boundaries
            $sessions = collect($validated['sessions'])->sortBy('session_number');
            $firstSession = $sessions->first();
            $lastSession = $sessions->last();
            
            $startDateTime = Carbon::parse($firstSession['start_date'] . ' ' . $firstSession['start_time']);
            $endDateTime = Carbon::parse($lastSession['end_date'] . ' ' . $lastSession['end_time']);

            // Validate overall date range
            if ($endDateTime->lte($startDateTime)) {
                return back()->withErrors([
                    'sessions' => 'Waktu selesai batch harus setelah waktu mulai batch.'
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
                'zoom_link' => $validated['zoom_link'], // Can be null
                'status' => $validated['status'],
            ]);

            // Delete old sessions
            $batch->sessions()->delete();

            // Create new sessions
            foreach ($validated['sessions'] as $sessionData) {
                $sessionStart = Carbon::parse($sessionData['start_date'] . ' ' . $sessionData['start_time']);
                $sessionEnd = Carbon::parse($sessionData['end_date'] . ' ' . $sessionData['end_time']);
                
                // Validate session time
                if ($sessionEnd->lte($sessionStart)) {
                    throw new \Exception('Sesi ke-' . $sessionData['session_number'] . ': Waktu selesai harus setelah waktu mulai');
                }
                
                BatchSession::create([
                    'batch_id' => $batch->id,
                    'trainer_id' => $sessionData['trainer_id'],
                    'session_number' => $sessionData['session_number'],
                    'title' => $sessionData['title'] ?? null,
                    'start_datetime' => $sessionStart,
                    'end_datetime' => $sessionEnd,
                    'zoom_link' => $sessionData['zoom_link'] ?? $validated['zoom_link'], // Use session zoom or default
                ]);
            }

            DB::commit();

            return redirect()->route('coordinator.batches.index')
                ->with('success', 'Batch "' . $batch->title . '" berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
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
        
        DB::beginTransaction();
        try {
            // Delete sessions
            $batch->sessions()->delete();
            
            // Delete related tasks
            $batch->tasks()->delete();
            
            // Delete the batch
            $batch->delete();
            
            DB::commit();

            return redirect()->route('coordinator.batches.index')
                ->with('success', 'Batch "' . $batchTitle . '" berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat menghapus batch: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display batch monitoring page
     */
    public function monitoring(Batch $batch)
    {
        $batch->load([
            'category',
            'trainer',
            'sessions.trainer',
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
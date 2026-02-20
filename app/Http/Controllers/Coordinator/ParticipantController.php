<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\BatchParticipant;
use App\Models\Batch;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants with filter and search
     */
    public function index(Request $request)
    {
        // Start query with relationships
        $query = BatchParticipant::with([
            'user.branch',
            'batch.category',
            'approver'
        ]);

        // Search filter (name, email, NIP)
        if ($search = $request->input('search')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id'));
            });
        }

        // Batch filter (optional)
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->input('batch_id'));
        }

        // Get paginated results
        $participants = $query->orderByRaw("
                CASE 
                    WHEN status = 'Pending' THEN 1
                    WHEN status = 'Approved' THEN 2
                    WHEN status = 'Rejected' THEN 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Transform data for view
        $participants->getCollection()->transform(function($participant) {
            return [
                'id' => $participant->id,
                'user_id' => $participant->user->id,
                'user_name' => $participant->user->name,
                'user_email' => $participant->user->email,
                'branch_name' => $participant->user->branch->name ?? '-',
                'batch_id' => $participant->batch->id,
                'batch_title' => $participant->batch->title,
                'batch_code' => formatBatchCode($participant->batch->id, $participant->batch->created_at->year),
                'category_name' => $participant->batch->category->name,
                'status' => $participant->status,
                'created_at' => $participant->created_at,
                'approved_by_name' => $participant->approver->name ?? null,
                'rejection_reason' => $participant->rejection_reason,
            ];
        });

        // Statistics for cards
        $pendingCount = BatchParticipant::where('status', 'Pending')->count();
        $approvedCount = BatchParticipant::where('status', 'Approved')->count();
        $rejectedCount = BatchParticipant::where('status', 'Rejected')->count();

        // Get data for filters
        $branches = Branch::orderBy('name')->get();
        $batches = Batch::with('category')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('coordinator.monitoring-peserta', compact(
            'participants',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'branches',
            'batches'
        ));
    }

    /**
     * Approve participant registration
     */
    public function approve(Request $request, BatchParticipant $participant)
    {
        // Validate participant is pending
        if ($participant->status !== 'Pending') {
            return back()->withErrors([
                'error' => 'Hanya pendaftaran dengan status Pending yang dapat disetujui.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Check if batch is full
            $batch = $participant->batch;
            $approvedCount = $batch->batchParticipants()
                ->where('status', 'Approved')
                ->count();

            if ($approvedCount >= $batch->max_quota) {
                return back()->withErrors([
                    'error' => 'Batch "' . $batch->title . '" sudah mencapai kuota maksimal (' . $batch->max_quota . ' peserta).'
                ]);
            }

            // Check prerequisites (if category has prerequisites)
            $category = $batch->category;
            if ($category->prerequisites()->exists()) {
                $hasCompletedPrerequisites = $this->checkPrerequisites(
                    $participant->user_id, 
                    $category->id
                );

                if (!$hasCompletedPrerequisites) {
                    return back()->withErrors([
                        'error' => 'Peserta belum menyelesaikan prerequisite untuk kategori "' . $category->name . '".'
                    ]);
                }
            }

            // Approve participant
            $participant->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
                'rejection_reason' => null,
            ]);

            DB::commit();

            return redirect()->route('coordinator.participants.index')
                ->with('success', 'Pendaftaran ' . $participant->user->name . ' untuk batch "' . $batch->title . '" berhasil disetujui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject participant registration
     */
    public function reject(Request $request, BatchParticipant $participant)
    {
        // Validate participant is pending
        if ($participant->status !== 'Pending') {
            return back()->withErrors([
                'error' => 'Hanya pendaftaran dengan status Pending yang dapat ditolak.'
            ]);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            $batch = $participant->batch;

            // Reject participant
            $participant->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            DB::commit();

            return redirect()->route('coordinator.participants.index')
                ->with('success', 'Pendaftaran ' . $participant->user->name . ' untuk batch "' . $batch->title . '" telah ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk approve participants
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'participant_ids' => ['required', 'array'],
            'participant_ids.*' => ['exists:batch_participants,id'],
        ]);

        DB::beginTransaction();
        try {
            $approved = 0;
            $failed = [];

            foreach ($validated['participant_ids'] as $participantId) {
                $participant = BatchParticipant::find($participantId);

                if ($participant->status !== 'Pending') {
                    $failed[] = $participant->user->name . ' (sudah diproses)';
                    continue;
                }

                // Check quota
                $batch = $participant->batch;
                $approvedCount = $batch->batchParticipants()
                    ->where('status', 'Approved')
                    ->count();

                if ($approvedCount >= $batch->max_quota) {
                    $failed[] = $participant->user->name . ' (kuota penuh)';
                    continue;
                }

                // Check prerequisites
                $category = $batch->category;
                if ($category->prerequisites()->exists()) {
                    if (!$this->checkPrerequisites($participant->user_id, $category->id)) {
                        $failed[] = $participant->user->name . ' (prerequisite tidak terpenuhi)';
                        continue;
                    }
                }

                // Approve
                $participant->update([
                    'status' => 'Approved',
                    'approved_by' => auth()->id(),
                ]);

                $approved++;
            }

            DB::commit();

            $message = $approved . ' pendaftaran berhasil disetujui.';
            if (!empty($failed)) {
                $message .= ' Gagal: ' . implode(', ', $failed);
            }

            return redirect()->route('coordinator.participants.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if user has completed prerequisites for a category
     */
    private function checkPrerequisites(int $userId, int $categoryId): bool
    {
        $category = \App\Models\Category::with('prerequisites')->find($categoryId);

        if (!$category || $category->prerequisites->isEmpty()) {
            return true; // No prerequisites required
        }

        // Get all prerequisite category IDs
        $prerequisiteIds = $category->prerequisites->pluck('id')->toArray();

        // Check if user has completed batches in all prerequisite categories
        $completedPrerequisites = DB::table('batch_participants')
            ->join('batches', 'batch_participants.batch_id', '=', 'batches.id')
            ->where('batch_participants.user_id', $userId)
            ->where('batch_participants.status', 'Approved')
            ->whereIn('batches.category_id', $prerequisiteIds)
            ->where('batches.status', 'Completed')
            ->whereHas('attendances', function($query) use ($userId) {
                // User must have attended (validated attendance)
                $query->where('user_id', $userId)
                      ->where('status', 'Approved');
            })
            ->whereHas('feedback', function($query) use ($userId) {
                // User must have given feedback
                $query->where('user_id', $userId);
            })
            ->distinct()
            ->pluck('batches.category_id')
            ->toArray();

        // Check if all prerequisites are completed
        return count($completedPrerequisites) === count($prerequisiteIds);
    }

    /**
     * Get participant details (for modal/AJAX)
     */
    public function show(BatchParticipant $participant)
    {
        $participant->load([
            'user.branch',
            'batch.category',
            'approver'
        ]);

        return response()->json([
            'id' => $participant->id,
            'user' => [
                'id' => $participant->user->id,
                'name' => $participant->user->name,
                'email' => $participant->user->email,
                'branch' => $participant->user->branch->name ?? '-',
            ],
            'batch' => [
                'id' => $participant->batch->id,
                'title' => $participant->batch->title,
                'code' => formatBatchCode($participant->batch->id, $participant->batch->created_at->year),
                'category' => $participant->batch->category->name,
            ],
            'status' => $participant->status,
            'created_at' => formatDateTime($participant->created_at),
            'approved_by' => $participant->approver->name ?? null,
            'rejection_reason' => $participant->rejection_reason,
        ]);
    }
}
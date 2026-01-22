<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BatchParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValidationController extends Controller
{
    /**
     * Display list of participants pending validation
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        // Query untuk batch participants dari cabang ini
        $query = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['user', 'batch.category', 'batch.trainer', 'approver']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by validation status
        if ($request->filled('status')) {
            $status = $request->status;
            
            switch ($status) {
                case 'pending':
                    $query->where('status', 'Pending');
                    break;
                    
                case 'approved':
                    $query->where('status', 'Approved');
                    break;
                    
                case 'rejected':
                    $query->where('status', 'Rejected');
                    break;
            }
        }

        // Get participants with pagination
        $participants = $query->latest()->paginate(20);

        // Statistics for cards
        $pendingCount = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('status', 'Pending')
            ->count();

        $approvedCount = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('status', 'Approved')
            ->count();

        $rejectedCount = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('status', 'Rejected')
            ->count();

        return view('branch_pic.validasi-data', compact(
            'branch',
            'participants',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Display detailed information of a specific participant registration
     */
    public function show($participantId)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        // Get batch participant with relations
        $participant = BatchParticipant::with([
                'user.branch',
                'batch.category',
                'batch.trainer',
                'approver'
            ])
            ->whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->findOrFail($participantId);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $participant->id,
                'name' => $participant->user->name,
                'email' => $participant->user->email,
                'branch' => $participant->user->branch->name ?? '-',
                'batch_title' => $participant->batch->title,
                'registration_date' => $participant->created_at->format('d F Y'),
                'status' => $participant->status,
                'rejection_reason' => $participant->rejection_reason,
                'approved_by' => $participant->approver ? $participant->approver->name : null,
            ]
        ]);
    }

    /**
     * Approve participant registration
     */
    public function approve($participantId)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        try {
            DB::beginTransaction();

            // Get participant
            $participant = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->findOrFail($participantId);

            // Check if already processed
            if ($participant->status !== 'Pending') {
                return back()->with('error', 'Pendaftaran ini sudah diproses sebelumnya.');
            }

            // Check batch quota
            $batch = $participant->batch;
            $approvedCount = $batch->batchParticipants()
                ->where('status', 'Approved')
                ->count();

            if ($approvedCount >= $batch->max_quota) {
                return back()->with('error', 'Kuota batch sudah penuh. Maksimal ' . $batch->max_quota . ' peserta.');
            }

            // Approve participant
            $participant->update([
                'status' => 'Approved',
                'approved_by' => $user->id,
                'rejection_reason' => null
            ]);

            DB::commit();

            return back()->with('success', 'Pendaftaran peserta berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject participant registration
     */
    public function reject(Request $request, $participantId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.'
        ]);

        $user = Auth::user();
        $branchId = $user->branch_id;

        try {
            DB::beginTransaction();

            // Get participant
            $participant = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->findOrFail($participantId);

            // Check if already processed
            if ($participant->status !== 'Pending') {
                return back()->with('error', 'Pendaftaran ini sudah diproses sebelumnya.');
            }

            // Reject participant
            $participant->update([
                'status' => 'Rejected',
                'approved_by' => $user->id,
                'rejection_reason' => $request->rejection_reason
            ]);

            DB::commit();

            return back()->with('success', 'Pendaftaran peserta berhasil ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve participants
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:batch_participants,id'
        ]);

        $user = Auth::user();
        $branchId = $user->branch_id;

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errors = [];

            foreach ($request->participant_ids as $participantId) {
                $participant = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                        $q->where('branch_id', $branchId);
                    })
                    ->find($participantId);

                if (!$participant) {
                    continue;
                }

                // Check if already processed
                if ($participant->status !== 'Pending') {
                    $errors[] = "Pendaftaran {$participant->user->name} sudah diproses.";
                    continue;
                }

                // Check batch quota
                $batch = $participant->batch;
                $approvedCount = $batch->batchParticipants()
                    ->where('status', 'Approved')
                    ->count();

                if ($approvedCount >= $batch->max_quota) {
                    $errors[] = "Kuota batch {$batch->title} sudah penuh.";
                    continue;
                }

                // Approve
                $participant->update([
                    'status' => 'Approved',
                    'approved_by' => $user->id,
                    'rejection_reason' => null
                ]);

                $successCount++;
            }

            DB::commit();

            if ($successCount > 0) {
                $message = "Berhasil menyetujui {$successCount} pendaftaran.";
                if (!empty($errors)) {
                    $message .= ' Namun ada beberapa error: ' . implode(', ', $errors);
                }
                return back()->with('success', $message);
            } else {
                return back()->with('error', 'Tidak ada pendaftaran yang berhasil disetujui. ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
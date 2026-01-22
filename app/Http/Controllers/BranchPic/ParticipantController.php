<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    /**
     * Display list of participants from this branch
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
            ->with(['user', 'batch.category', 'batch.trainer']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('batch', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by batch status
        if ($request->filled('status')) {
            $status = $request->status;
            
            switch ($status) {
                case 'completed':
                    $query->whereHas('batch', function($q) {
                        $q->where('status', 'Completed');
                    })->where('status', 'Approved');
                    break;
                    
                case 'ongoing':
                    $query->whereHas('batch', function($q) {
                        $q->where('status', 'Ongoing');
                    })->where('status', 'Approved');
                    break;
                    
                case 'approved':
                    $query->where('status', 'Approved');
                    break;
                    
                case 'registered':
                    $query->where('status', 'Pending');
                    break;
                    
                case 'rejected':
                    $query->where('status', 'Rejected');
                    break;
            }
        }

        // Get participants with pagination
        $participants = $query->latest()->paginate(20);

        // Statistics for cards
        $totalParticipants = BatchParticipant::whereHas('user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->distinct('user_id')->count('user_id');

        $ongoingCount = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function($q) {
                $q->where('status', 'Ongoing');
            })
            ->where('status', 'Approved')
            ->count();

        $completedCount = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function($q) {
                $q->where('status', 'Completed');
            })
            ->where('status', 'Approved')
            ->count();

        $certificatesCount = Certificate::whereHas('user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->count();

        return view('branch_pic.peserta-cabang', compact(
            'branch',
            'participants',
            'totalParticipants',
            'ongoingCount',
            'completedCount',
            'certificatesCount'
        ));
    }

    /**
     * Display detailed information of a specific participant
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
                'user.attendances' => function($query) use ($participantId) {
                    $query->where('user_id', $participantId);
                }
            ])
            ->whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->findOrFail($participantId);

        // Get attendance status for this participant in this batch
        $attendance = $participant->user->attendances()
            ->where('batch_id', $participant->batch_id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $participant->user->name,
                'email' => $participant->user->email,
                'branch' => $participant->user->branch->name ?? '-',
                'batch_title' => $participant->batch->title,
                'batch_start' => $participant->batch->start_date->format('d F Y'),
                'batch_time' => $participant->batch->start_date->format('H:i') . ' - ' . 
                               $participant->batch->end_date->format('H:i'),
                'registration_date' => $participant->created_at->format('d F Y'),
                'participant_status' => $participant->status,
                'batch_status' => $participant->batch->status,
                'attendance_status' => $attendance ? $attendance->status : null,
            ]
        ]);
    }
}
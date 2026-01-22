<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display branch dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        // Get all participants from this branch
        $participantsQuery = User::where('branch_id', $branchId)
            ->whereHas('role', function($query) {
                $query->where('name', 'Participant');
            });

        // Total Peserta
        $totalParticipants = $participantsQuery->count();

        // Get batch participants with status
        $batchParticipants = BatchParticipant::whereHas('user', function($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with(['batch', 'user']);

        // Ongoing - peserta yang batch-nya ongoing
        $ongoingCount = $batchParticipants->clone()
            ->whereHas('batch', function($query) {
                $query->where('status', 'Ongoing');
            })
            ->where('status', 'Approved')
            ->count();

        // Completed - peserta yang batch-nya completed
        $completedCount = $batchParticipants->clone()
            ->whereHas('batch', function($query) {
                $query->where('status', 'Completed');
            })
            ->where('status', 'Approved')
            ->count();

        // Sertifikat - total sertifikat yang diterbitkan untuk peserta cabang ini
        $certificatesCount = Certificate::whereHas('user', function($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->count();

        // Peserta Terbaru (latest 6)
        $recentParticipants = BatchParticipant::whereHas('user', function($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->with(['user', 'batch.category'])
            ->latest()
            ->limit(6)
            ->get();

        return view('branch_pic.dashboard', compact(
            'branch',
            'totalParticipants',
            'ongoingCount',
            'completedCount',
            'certificatesCount',
            'recentParticipants'
        ));
    }
}
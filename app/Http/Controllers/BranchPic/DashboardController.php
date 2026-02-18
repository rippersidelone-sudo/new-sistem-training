<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BatchParticipant;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        $participantsQuery = User::query()
            ->where('branch_id', $branchId)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Participant');
            });

        $totalParticipants = (clone $participantsQuery)->count();

        $batchParticipants = BatchParticipant::query()
            ->whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['batch', 'user']);

        $ongoingCount = (clone $batchParticipants)
            ->whereHas('batch', function ($q) {
                $q->where('status', 'Ongoing');
            })
            ->where('status', 'Approved')
            ->count();

        $completedCount = (clone $batchParticipants)
            ->whereHas('batch', function ($q) {
                $q->where('status', 'Completed');
            })
            ->where('status', 'Approved')
            ->count();

        $certificatesCount = Certificate::query()
            ->whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->count();

        // paginate tetap dipakai supaya komponen pagination bisa munculin total + range
        $participants = (clone $participantsQuery)
            ->with([
                'batchParticipants' => function ($q) {
                    $q->latest()->limit(1);
                },
                'batchParticipants.batch',
            ])
            ->latest()
            ->paginate(10);

        return view('branch_pic.dashboard', compact(
            'branch',
            'totalParticipants',
            'ongoingCount',
            'completedCount',
            'certificatesCount',
            'participants'
        ));
    }
}

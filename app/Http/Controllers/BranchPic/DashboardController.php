<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BatchParticipant;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        // === STATISTICS CARDS (tidak dipengaruhi filter) ===
        $totalParticipants = User::where('branch_id', $branchId)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Participant');
            })
            ->count();

        $ongoingCount = BatchParticipant::whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function ($q) {
                $q->where('status', 'Ongoing');
            })
            ->where('status', 'Approved')
            ->count();

        $completedCount = BatchParticipant::whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function ($q) {
                $q->where('status', 'Completed');
            })
            ->where('status', 'Approved')
            ->count();

        $certificatesCount = Certificate::whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->count();

        // === TABEL PESERTA dengan filter ===
        // Query berbasis User agar setiap baris = 1 peserta (bukan 1 batch_participant)
        $query = User::where('branch_id', $branchId)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Participant');
            })
            ->with([
                'batchParticipants' => function ($q) {
                    $q->with('batch')->latest()->limit(1);
                },
            ]);

        // Filter: search nama / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter: status (berdasarkan batch terakhir peserta)
        if ($request->filled('status')) {
            $status = $request->status;

            $query->whereHas('batchParticipants', function ($q) use ($status) {
                switch ($status) {
                    case 'ongoing':
                        $q->where('status', 'Approved')
                          ->whereHas('batch', fn($b) => $b->where('status', 'Ongoing'));
                        break;

                    case 'completed':
                        $q->where('status', 'Approved')
                          ->whereHas('batch', fn($b) => $b->where('status', 'Completed'));
                        break;

                    case 'approved':
                        $q->where('status', 'Approved');
                        break;

                    case 'registered':
                        $q->where('status', 'Pending');
                        break;

                    case 'rejected':
                        $q->where('status', 'Rejected');
                        break;
                }
            });
        }

        $participants = $query->latest()->paginate(10)->withQueryString();

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
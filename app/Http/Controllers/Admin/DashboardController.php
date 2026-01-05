<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\BatchParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics Cards
        $totalBatches = Batch::count();
        $activeBatches = Batch::where('status', 'Ongoing')->count();
        $totalParticipants = User::whereHas('role', function($q) {
            $q->where('name', 'Participant');
        })->count();
        
        // Count participants who passed (has attendance AND feedback)
        $passedParticipants = BatchParticipant::where('status', 'Approved')
            ->whereHas('user.attendances', function($q) {
                $q->where('status', 'Approved');
            })
            ->whereHas('user.feedback')
            ->distinct('user_id')
            ->count('user_id');
        
        $activeBranches = Branch::whereHas('users')->count();
        $totalCertificates = Certificate::count();

        // Monthly Trend Data (Last 5 months)
        $monthlyTrend = $this->getMonthlyTrend();

        // Batch Status Distribution
        $batchStatus = [
            'Scheduled' => Batch::where('status', 'Scheduled')->count(),
            'Ongoing' => Batch::where('status', 'Ongoing')->count(),
            'Completed' => Batch::where('status', 'Completed')->count(),
        ];

        // Participants per Branch
        $participantsPerBranch = Branch::withCount(['users' => function($q) {
            $q->whereHas('role', function($query) {
                $query->where('name', 'Participant');
            });
        }])->get()->map(function($branch) {
            return [
                'name' => $branch->name,
                'count' => $branch->users_count
            ];
        });

        // Recent Batches (Latest 3)
        $recentBatches = Batch::with(['trainer', 'category'])
            ->withCount('participants')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('admin.master-dashboard', compact(
            'totalBatches',
            'activeBatches',
            'totalParticipants',
            'passedParticipants',
            'activeBranches',
            'totalCertificates',
            'monthlyTrend',
            'batchStatus',
            'participantsPerBranch',
            'recentBatches'
        ));
    }

    /**
     * Get monthly trend data for the last 5 months
     */
    private function getMonthlyTrend()
    {
        $months = [];
        $batchData = [];
        $participantData = [];

        for ($i = 4; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            // Count batches created in this month
            $batchCount = Batch::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $batchData[] = $batchCount;

            // Count participants registered in this month
            $participantCount = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->distinct('user_id')
                ->count('user_id');
            $participantData[] = $participantCount;
        }

        return [
            'labels' => $months,
            'batches' => $batchData,
            'participants' => $participantData,
        ];
    }
}
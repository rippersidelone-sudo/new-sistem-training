<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\BatchParticipant;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics Cards
        $totalBatches = Batch::count();
        $activeBatches = Batch::where('status', 'Ongoing')->count();
        
        // Total participants (users with Participant role)
        $totalParticipants = User::whereHas('role', function($q) {
            $q->where('name', 'Participant');
        })->count();
        
        // Passed participants (approved batch participants)
        $passedParticipants = BatchParticipant::where('status', 'Approved')
            ->distinct('user_id')
            ->count('user_id');
        
        // Active branches (branches that have users)
        // Jika batches punya branch_id, gunakan: Branch::has('batches')->count()
        $activeBranches = Branch::has('users')->count();
        
        // Total certificates issued
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
        }])
        ->orderBy('name')
        ->get()
        ->map(function($branch) {
            return [
                'name' => $branch->name,
                'code' => $this->getBranchCode($branch->name),
                'count' => $branch->users_count
            ];
        });

        // Recent Batches (Latest 5)
        $recentBatches = Batch::with(['trainer', 'category'])
            ->withCount('batchParticipants')
            ->orderBy('created_at', 'desc')
            ->take(5)
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
            
            // Format bulan Indonesia
            $monthName = $date->locale('id')->translatedFormat('M');
            $months[] = $monthName;

            // Count batches created in this month
            $batchCount = Batch::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $batchData[] = $batchCount;

            // Count participants registered in this month
            $participantCount = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'Approved')
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

    /**
     * Get branch code from name (first 3 uppercase letters)
     * Jakarta Pusat -> JKT-PST
     * Bandung -> BDG
     * Surabaya -> SBY
     */
    private function getBranchCode($name)
    {
        // Remove common words
        $name = str_replace(['Cabang', 'Branch'], '', $name);
        $name = trim($name);
        
        $words = explode(' ', $name);
        
        if (count($words) > 1) {
            // Multi-word: take first 3 letters of each word
            return strtoupper(
                substr($words[0], 0, 3) . '-' . substr($words[1], 0, 3)
            );
        }
        
        // Single word: take first 3 letters
        return strtoupper(substr($name, 0, 3));
    }
}
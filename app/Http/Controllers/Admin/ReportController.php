<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\BatchParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::orderBy('name')->get();
        
        // Get filter values
        $selectedMonth = $request->input('month');
        $selectedBranch = $request->input('branch_id');

        // Monthly Trend Data (Last 6 months)
        $monthlyTrend = $this->getMonthlyTrend();

        // Overall Statistics (Last 6 months)
        $totalBatches = Batch::where('created_at', '>=', now()->subMonths(6))->count();
        $totalParticipants = BatchParticipant::where('created_at', '>=', now()->subMonths(6))
            ->distinct('user_id')
            ->count('user_id');
        
        // Calculate pass rate
        $passedCount = BatchParticipant::where('created_at', '>=', now()->subMonths(6))
            ->where('status', 'Approved')
            ->whereHas('user.attendances', function($q) {
                $q->where('status', 'Approved');
            })
            ->whereHas('user.feedback')
            ->distinct('user_id')
            ->count('user_id');
        
        $passRate = $totalParticipants > 0 ? round(($passedCount / $totalParticipants) * 100, 1) : 0;

        // Branch Performance Data
        $branchPerformance = $this->getBranchPerformance();

        // Top Performing Batches
        $topBatches = $this->getTopPerformingBatches();

        return view('admin.global-report', compact(
            'branches',
            'monthlyTrend',
            'totalBatches',
            'totalParticipants',
            'passedCount',
            'passRate',
            'branchPerformance',
            'topBatches'
        ));
    }

    /**
     * Export report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'monthly'); // 'monthly' or 'complete'
        
        // Implement CSV export logic here
        // For now, return a simple response
        return response()->streamDownload(function() use ($type) {
            echo "Report Export - Type: {$type}\n";
            echo "Implementation coming soon...\n";
        }, 'report-' . date('Y-m-d') . '.csv');
    }

    private function getMonthlyTrend()
    {
        $months = [];
        $batchData = [];
        $participantData = [];
        $passedData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            // Batches
            $batchCount = Batch::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $batchData[] = $batchCount;

            // Participants
            $participantCount = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->distinct('user_id')
                ->count('user_id');
            $participantData[] = $participantCount;

            // Passed
            $passedCount = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'Approved')
                ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
                ->whereHas('user.feedback')
                ->distinct('user_id')
                ->count('user_id');
            $passedData[] = $passedCount;
        }

        return [
            'labels' => $months,
            'batches' => $batchData,
            'participants' => $participantData,
            'passed' => $passedData,
        ];
    }

    private function getBranchPerformance()
    {
        return Branch::withCount(['users as participants_count' => function($q) {
            $q->whereHas('role', fn($query) => $query->where('name', 'Participant'));
        }])
        ->get()
        ->map(function($branch) {
            $participants = $branch->participants_count;
            
            // Count passed participants
            $passed = User::where('branch_id', $branch->id)
                ->whereHas('role', fn($q) => $q->where('name', 'Participant'))
                ->whereHas('batchParticipants', function($q) {
                    $q->where('status', 'Approved')
                        ->whereHas('batch.attendances', fn($query) => $query->where('status', 'Approved'));
                })
                ->whereHas('feedback')
                ->distinct()
                ->count();
            
            $passRate = $participants > 0 ? round(($passed / $participants) * 100, 1) : 0;

            return [
                'name' => $branch->name,
                'participants' => $participants,
                'passed' => $passed,
                'pass_rate' => $passRate,
            ];
        });
    }

    private function getTopPerformingBatches()
    {
        return Batch::with(['category', 'trainer'])
            ->withCount('participants')
            ->where('status', 'Completed')
            ->get()
            ->map(function($batch) {
                $total = $batch->participants_count;
                $passed = BatchParticipant::where('batch_id', $batch->id)
                    ->where('status', 'Approved')
                    ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
                    ->whereHas('user.feedback')
                    ->count();
                
                $completionRate = $total > 0 ? round(($passed / $total) * 100) : 0;

                return [
                    'batch' => $batch,
                    'completion_rate' => $completionRate,
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(5);
    }
}
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
        $monthlyTrend = $this->getMonthlyTrend($selectedMonth, $selectedBranch);

        // Overall Statistics (Last 6 months or filtered)
        $query = Batch::query();
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } else {
            $query->where('created_at', '>=', now()->subMonths(6));
        }
        
        $totalBatches = $query->count();
        
        // Participants
        $participantQuery = BatchParticipant::query();
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $participantQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } else {
            $participantQuery->where('created_at', '>=', now()->subMonths(6));
        }
        
        if ($selectedBranch) {
            $participantQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
        }
        
        $totalParticipants = $participantQuery->distinct('user_id')->count('user_id');
        
        // Calculate pass rate
        $passedQuery = clone $participantQuery;
        $passedCount = $passedQuery
            ->where('status', 'Approved')
            ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
            ->whereHas('user.feedback')
            ->distinct('user_id')
            ->count('user_id');
        
        $passRate = $totalParticipants > 0 ? round(($passedCount / $totalParticipants) * 100, 1) : 0;

        // Branch Performance Data
        $branchPerformance = $this->getBranchPerformance($selectedMonth);

        // Top Performing Batches
        $topBatches = $this->getTopPerformingBatches($selectedMonth, $selectedBranch);

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
        $type = $request->input('type', 'monthly');
        $month = $request->input('month');
        $branchId = $request->input('branch_id');
        
        if ($type === 'monthly') {
            return $this->exportMonthlyReport($month, $branchId);
        } else {
            return $this->exportCompleteReport($month, $branchId);
        }
    }

    private function exportMonthlyReport($month, $branchId)
    {
        $query = Batch::with(['trainer', 'category']);
        
        if ($month) {
            [$year, $m] = explode('-', $month);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $m);
        } else {
            $query->where('created_at', '>=', now()->subMonths(6));
        }
        
        $batches = $query->get();

        return response()->streamDownload(function() use ($batches) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($handle, [
                'Kode Batch',
                'Judul',
                'Kategori',
                'Trainer',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Total Peserta',
                'Lulus',
                'Status',
                'Tingkat Kelulusan (%)'
            ]);
            
            // Data
            foreach ($batches as $batch) {
                $totalParticipants = $batch->batchParticipants()->count();
                $passedParticipants = $batch->batchParticipants()
                    ->where('status', 'Approved')
                    ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
                    ->whereHas('user.feedback')
                    ->count();
                
                $passRate = $totalParticipants > 0 ? round(($passedParticipants / $totalParticipants) * 100, 1) : 0;
                
                fputcsv($handle, [
                    formatBatchCode($batch->id, $batch->created_at->year),
                    $batch->title,
                    $batch->category->name ?? '-',
                    $batch->trainer->name ?? '-',
                    $batch->start_date->format('d/m/Y H:i'),
                    $batch->end_date->format('d/m/Y H:i'),
                    $totalParticipants,
                    $passedParticipants,
                    $batch->status,
                    $passRate
                ]);
            }
            
            fclose($handle);
        }, 'laporan-bulanan-' . date('Y-m-d') . '.csv');
    }

    private function exportCompleteReport($month, $branchId)
    {
        $query = BatchParticipant::with(['batch', 'user.branch']);
        
        if ($month) {
            [$year, $m] = explode('-', $month);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $m);
        }
        
        if ($branchId) {
            $query->whereHas('user', fn($q) => $q->where('branch_id', $branchId));
        }
        
        $participants = $query->get();

        return response()->streamDownload(function() use ($participants) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($handle, [
                'Kode Batch',
                'Judul Batch',
                'Nama Peserta',
                'Email',
                'Cabang',
                'Status Pendaftaran',
                'Kehadiran',
                'Feedback',
                'Status Kelulusan'
            ]);
            
            // Data
            foreach ($participants as $participant) {
                $attendance = $participant->user->attendances()
                    ->where('batch_id', $participant->batch_id)
                    ->where('status', 'Approved')
                    ->exists();
                    
                $feedback = $participant->user->feedback()
                    ->where('batch_id', $participant->batch_id)
                    ->exists();
                
                $isPassed = $participant->status === 'Approved' && $attendance && $feedback;
                
                fputcsv($handle, [
                    formatBatchCode($participant->batch->id, $participant->batch->created_at->year),
                    $participant->batch->title,
                    $participant->user->name,
                    $participant->user->email,
                    $participant->user->branch->name ?? '-',
                    $participant->status,
                    $attendance ? 'Hadir' : 'Tidak Hadir',
                    $feedback ? 'Sudah' : 'Belum',
                    $isPassed ? 'LULUS' : 'TIDAK LULUS'
                ]);
            }
            
            fclose($handle);
        }, 'laporan-lengkap-' . date('Y-m-d') . '.csv');
    }

    private function getMonthlyTrend($selectedMonth = null, $selectedBranch = null)
    {
        $months = [];
        $batchData = [];
        $participantData = [];
        $passedData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            // Batches
            $batchQuery = Batch::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            $batchCount = $batchQuery->count();
            $batchData[] = $batchCount;

            // Participants
            $participantQuery = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if ($selectedBranch) {
                $participantQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
            }
            
            $participantCount = $participantQuery->distinct('user_id')->count('user_id');
            $participantData[] = $participantCount;

            // Passed
            $passedQuery = BatchParticipant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'Approved')
                ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
                ->whereHas('user.feedback');
            
            if ($selectedBranch) {
                $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
            }
            
            $passedCount = $passedQuery->distinct('user_id')->count('user_id');
            $passedData[] = $passedCount;
        }

        return [
            'labels' => $months,
            'batches' => $batchData,
            'participants' => $participantData,
            'passed' => $passedData,
        ];
    }

    private function getBranchPerformance($selectedMonth = null)
    {
        return Branch::withCount(['users as participants_count' => function($q) use ($selectedMonth) {
            $q->whereHas('role', fn($query) => $query->where('name', 'Participant'));
            
            if ($selectedMonth) {
                [$year, $month] = explode('-', $selectedMonth);
                $q->whereHas('batchParticipants', function($query) use ($year, $month) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                });
            }
        }])
        ->get()
        ->map(function($branch) use ($selectedMonth) {
            $participants = $branch->participants_count;
            
            // Count passed participants
            $passedQuery = User::where('branch_id', $branch->id)
                ->whereHas('role', fn($q) => $q->where('name', 'Participant'))
                ->whereHas('batchParticipants', function($q) use ($selectedMonth) {
                    $q->where('status', 'Approved');
                    
                    if ($selectedMonth) {
                        [$year, $month] = explode('-', $selectedMonth);
                        $q->whereYear('created_at', $year)->whereMonth('created_at', $month);
                    }
                })
                ->whereHas('attendances', fn($q) => $q->where('status', 'Approved'))
                ->whereHas('feedback');
            
            $passed = $passedQuery->distinct()->count();
            $passRate = $participants > 0 ? round(($passed / $participants) * 100, 1) : 0;

            return [
                'name' => $branch->name,
                'participants' => $participants,
                'passed' => $passed,
                'pass_rate' => $passRate,
            ];
        });
    }

    private function getTopPerformingBatches($selectedMonth = null, $selectedBranch = null)
    {
        $query = Batch::with(['category', 'trainer'])
            ->withCount('batchParticipants as participants_count')
            ->where('status', 'Completed');
        
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }
        
        return $query->get()
            ->map(function($batch) use ($selectedBranch) {
                $totalQuery = BatchParticipant::where('batch_id', $batch->id);
                $passedQuery = clone $totalQuery;
                
                if ($selectedBranch) {
                    $totalQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                    $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $total = $totalQuery->count();
                $passed = $passedQuery
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
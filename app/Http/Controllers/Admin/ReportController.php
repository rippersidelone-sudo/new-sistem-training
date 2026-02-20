<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\BatchParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::orderBy('name')->get();
        
        $period = $request->input('period', 'this_year'); 
        $selectedBranch = $request->input('branch_id');
        $dateRange = $this->getDateRange($period);      
        $monthlyTrend = $this->getMonthlyTrend($period, $dateRange, $selectedBranch);
        $totalBatches = Batch::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        // Participants
        $participantQuery = BatchParticipant::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        
        if ($selectedBranch) {
            $participantQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
        }
        
        $totalParticipants = $participantQuery->where('status', 'Approved')->distinct('user_id')->count('user_id');
        
        // Calculate pass rate
        $passedQuery = BatchParticipant::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'Approved')
            ->whereHas('user', function($q) {
                $q->whereHas('attendances', fn($subQ) => $subQ->where('status', 'Approved'));
            });
        
        if ($selectedBranch) {
            $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
        }
        
        $passedCount = $passedQuery->distinct('user_id')->count('user_id');
        $passRate = $totalParticipants > 0 ? round(($passedCount / $totalParticipants) * 100, 1) : 0;

        // Branch Performance Data
        $branchPerformance = $this->getBranchPerformance($dateRange, $selectedBranch);

        // Top Performing Batches
        $topBatches = $this->getTopPerformingBatches($dateRange, $selectedBranch);

        // ✅ Build filter options - PERIOD & BRANCH
        $filterOptions = $this->buildFilterOptions($period, $selectedBranch);

        return view('admin.global-report', compact(
            'branches',
            'monthlyTrend',
            'totalBatches',
            'totalParticipants',
            'passedCount',
            'passRate',
            'branchPerformance',
            'topBatches',
            'filterOptions',
            'dateRange'
        ));
    }

    /**
     * ✅ Get date range based on period (SAMA SEPERTI DASHBOARD)
     */
    private function getDateRange($period)
    {
        $now = now();
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Hari Ini'
                ];
                
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'label' => 'Minggu Ini'
                ];
                
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => $now->locale('id')->translatedFormat('F Y')
                ];
                
            case 'last_3_months':
                return [
                    'start' => $now->copy()->subMonths(3)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => '3 Bulan Terakhir'
                ];
                
            case 'last_6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => '6 Bulan Terakhir'
                ];
                
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'label' => 'Tahun ' . $now->year
                ];
                
            case 'last_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear(),
                    'end' => $now->copy()->subYear()->endOfYear(),
                    'label' => 'Tahun ' . ($now->year - 1)
                ];
                
            case 'all_time':
            default:
                $firstBatch = Batch::orderBy('created_at')->first();
                $startDate = $firstBatch ? $firstBatch->created_at : $now->copy()->subYears(5);
                return [
                    'start' => $startDate->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Semua Waktu'
                ];
        }
    }

    /**
     * ✅ Build filter options
     */
    private function buildFilterOptions($period, $selectedBranch = null)
    {
        $branches = Branch::orderBy('name')->get();

        return [
            [
                'name' => 'period',
                'placeholder' => 'Pilih Periode',
                'options' => collect([
                    ['value' => 'this_month',    'label' => 'Bulan Ini'],
                    ['value' => 'last_3_months', 'label' => '3 Bulan Terakhir'],
                    ['value' => 'last_6_months', 'label' => '6 Bulan Terakhir'],
                    ['value' => 'this_year',     'label' => 'Tahun Ini'],
                    ['value' => 'last_year',     'label' => 'Tahun Lalu'],
                    ['value' => 'all_time',      'label' => 'Semua Waktu'],
                ]),
                'selected' => $period,  
            ],
            [
                'name' => 'branch_id',
                'placeholder' => 'Semua Cabang',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Cabang']
                ])->merge(
                    $branches->map(fn($branch) => [
                        'value' => (string) $branch->id,
                        'label' => $branch->name
                    ])
                ),
                'selected' => (string) $selectedBranch,  
            ]
        ];
    }

    /**
     * Export report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'monthly');
        $period = $request->input('period', 'this_year');
        $branchId = $request->input('branch_id');
        
        $dateRange = $this->getDateRange($period);
        
        if ($type === 'monthly') {
            return $this->exportMonthlyReport($dateRange, $branchId);
        } else {
            return $this->exportCompleteReport($dateRange, $branchId);
        }
    }

    private function exportMonthlyReport($dateRange, $branchId)
    {
        $batches = Batch::with(['trainer', 'category'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

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
                $totalParticipants = $batch->batchParticipants()->where('status', 'Approved')->count();
                $passedParticipants = $batch->batchParticipants()
                    ->where('status', 'Approved')
                    ->whereHas('user', function($q) use ($batch) {
                        $q->whereHas('attendances', function($subQ) use ($batch) {
                            $subQ->where('batch_id', $batch->id)
                                ->where('status', 'Approved');
                        });
                    })
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

    private function exportCompleteReport($dateRange, $branchId)
    {
        $query = BatchParticipant::with(['batch', 'user.branch'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        
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
                'Status Kelulusan'
            ]);
            
            // Data
            foreach ($participants as $participant) {
                $attendance = $participant->user->attendances()
                    ->where('batch_id', $participant->batch_id)
                    ->where('status', 'Approved')
                    ->exists();
                
                $isPassed = $participant->status === 'Approved' && $attendance;
                
                fputcsv($handle, [
                    formatBatchCode($participant->batch->id, $participant->batch->created_at->year),
                    $participant->batch->title,
                    $participant->user->name,
                    $participant->user->email,
                    $participant->user->branch->name ?? '-',
                    $participant->status,
                    $attendance ? 'Hadir' : 'Tidak Hadir',
                    $isPassed ? 'LULUS' : 'TIDAK LULUS'
                ]);
            }
            
            fclose($handle);
        }, 'laporan-lengkap-' . date('Y-m-d') . '.csv');
    }

    /**
     * ✅ Get monthly trend based on period
     */
    private function getMonthlyTrend($period, $dateRange, $selectedBranch = null)
    {
        $months = [];
        $batchData = [];
        $participantData = [];
        $passedData = [];

        // ✅ Untuk yearly view, tampilkan 12 bulan
        if (in_array($period, ['this_year', 'last_year'])) {
            $year = $dateRange['start']->year;
            
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($year, $month, 1);
                $months[] = $date->locale('id')->translatedFormat('M');

                // Batches
                $batchCount = Batch::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
                $batchData[] = $batchCount;

                // Participants
                $participantQuery = BatchParticipant::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'Approved');
                
                if ($selectedBranch) {
                    $participantQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $participantCount = $participantQuery->distinct('user_id')->count('user_id');
                $participantData[] = $participantCount;

                // Passed
                $passedQuery = BatchParticipant::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'Approved')
                    ->whereHas('user', function($q) {
                        $q->whereHas('attendances', fn($subQ) => $subQ->where('status', 'Approved'));
                    });
                
                if ($selectedBranch) {
                    $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $passedCount = $passedQuery->distinct('user_id')->count('user_id');
                $passedData[] = $passedCount;
            }
        } else {
            // ✅ Untuk periode lain, tampilkan 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->locale('id')->translatedFormat('M Y');

                // Batches
                $batchCount = Batch::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $batchData[] = $batchCount;

                // Participants
                $participantQuery = BatchParticipant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'Approved');
                
                if ($selectedBranch) {
                    $participantQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $participantCount = $participantQuery->distinct('user_id')->count('user_id');
                $participantData[] = $participantCount;

                // Passed
                $passedQuery = BatchParticipant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'Approved')
                    ->whereHas('user', function($q) {
                        $q->whereHas('attendances', fn($subQ) => $subQ->where('status', 'Approved'));
                    });
                
                if ($selectedBranch) {
                    $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $passedCount = $passedQuery->distinct('user_id')->count('user_id');
                $passedData[] = $passedCount;
            }
        }

        return [
            'labels' => $months,
            'batches' => $batchData,
            'participants' => $participantData,
            'passed' => $passedData,
        ];
    }

    /**
     * ✅ Get branch performance based on date range
     */
    private function getBranchPerformance($dateRange, $selectedBranch = null)
    {
        $query = Branch::query();
        
        if ($selectedBranch) {
            $query->where('id', $selectedBranch);
        }
        
        return $query->get()->map(function($branch) use ($dateRange) {
            // Total participants in period
            $participants = BatchParticipant::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'Approved')
                ->whereHas('user', fn($q) => $q->where('branch_id', $branch->id))
                ->distinct('user_id')
                ->count('user_id');
            
            // Passed participants
            $passed = BatchParticipant::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'Approved')
                ->whereHas('user', function($q) use ($branch) {
                    $q->where('branch_id', $branch->id)
                      ->whereHas('attendances', fn($subQ) => $subQ->where('status', 'Approved'));
                })
                ->distinct('user_id')
                ->count('user_id');
            
            $passRate = $participants > 0 ? round(($passed / $participants) * 100, 1) : 0;

            return [
                'name' => $branch->name,
                'participants' => $participants,
                'passed' => $passed,
                'pass_rate' => $passRate,
            ];
        })->filter(fn($item) => $item['participants'] > 0); // Only show branches with participants
    }

    /**
     * ✅ Get top performing batches based on date range
     */
    private function getTopPerformingBatches($dateRange, $selectedBranch = null)
    {
        $query = Batch::with(['category', 'trainer'])
            ->withCount('batchParticipants as participants_count')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'Completed');
        
        return $query->get()
            ->map(function($batch) use ($selectedBranch) {
                $totalQuery = BatchParticipant::where('batch_id', $batch->id)
                    ->where('status', 'Approved');
                $passedQuery = clone $totalQuery;
                
                if ($selectedBranch) {
                    $totalQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                    $passedQuery->whereHas('user', fn($q) => $q->where('branch_id', $selectedBranch));
                }
                
                $total = $totalQuery->count();
                $passed = $passedQuery
                    ->whereHas('user', function($q) use ($batch) {
                        $q->whereHas('attendances', function($subQ) use ($batch) {
                            $subQ->where('batch_id', $batch->id)
                                ->where('status', 'Approved');
                        });
                    })
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
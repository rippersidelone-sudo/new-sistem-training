<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Category;
use App\Models\Attendance;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display comprehensive training reports
     */
    public function index(Request $request)
    {
        // Date filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'all'); // all, month, year, custom
        $branchId = $request->input('branch_id');

        // Set date range based on period
        [$startDate, $endDate] = $this->getDateRange($period, $startDate, $endDate);

        // Query base with date filter
        $batchQuery = Batch::query();
        if ($startDate && $endDate) {
            $batchQuery->whereBetween('start_date', [$startDate, $endDate]);
        }

        // ========== STATISTICS ==========
        
        // Total Batches
        $totalBatches = (clone $batchQuery)->count();
        $completedBatches = (clone $batchQuery)->where('status', 'Completed')->count();
        
        // Total Participants (unique users)
        $totalParticipants = BatchParticipant::whereHas('batch', function($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            }
        })
        ->when($branchId, function($q) use ($branchId) {
            $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
        })
        ->where('status', 'Approved')
        ->distinct('user_id')
        ->count('user_id');

        // Participants who passed (has attendance + feedback)
        $passedParticipants = BatchParticipant::whereHas('batch', function($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->where('status', 'Completed');
            } else {
                $q->where('status', 'Completed');
            }
        })
        ->when($branchId, function($q) use ($branchId) {
            $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
        })
        ->where('status', 'Approved')
        ->whereHas('user.attendances', function($q) {
            $q->where('status', 'Approved');
        })
        ->whereHas('user.feedback')
        ->distinct('user_id')
        ->count('user_id');

        $certificatesIssued = 0;

        // Average attendance rate
        $avgAttendanceRate = $this->calculateAverageAttendance($startDate, $endDate, $branchId);

        // ========== BATCH STATUS DISTRIBUTION ==========
        $batchStatusData = [
            'scheduled' => (clone $batchQuery)->where('status', 'Scheduled')->count(),
            'ongoing' => (clone $batchQuery)->where('status', 'Ongoing')->count(),
            'completed' => (clone $batchQuery)->where('status', 'Completed')->count(),
        ];

        // ========== PARTICIPANT STATUS DISTRIBUTION ==========
        $participantStatusQuery = BatchParticipant::whereHas('batch', function($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            }
        })
        ->when($branchId, function($q) use ($branchId) {
            $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
        });

        $participantStatusData = [
            'approved' => (clone $participantStatusQuery)->where('status', 'Approved')->count(),
            'pending' => (clone $participantStatusQuery)->where('status', 'Pending')->count(),
            'rejected' => (clone $participantStatusQuery)->where('status', 'Rejected')->count(),
            'completed' => $passedParticipants,
            'ongoing' => (clone $participantStatusQuery)
                ->where('status', 'Approved')
                ->whereHas('batch', fn($q) => $q->where('status', 'Ongoing'))
                ->count(),
        ];

        // ========== PARTICIPANTS PER BRANCH ==========
        $participantsPerBranch = DB::table('batch_participants')
            ->join('users', 'batch_participants.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->join('batches', 'batch_participants.batch_id', '=', 'batches.id')
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                $q->whereBetween('batches.start_date', [$startDate, $endDate]);
            })
            ->where('batch_participants.status', 'Approved')
            ->select(
                'branches.name as branch_name',
                DB::raw('COUNT(DISTINCT batch_participants.user_id) as total_participants'),
                DB::raw('COUNT(DISTINCT CASE WHEN batches.status = "Completed" THEN batch_participants.user_id END) as passed_participants')
            )
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total_participants', 'desc')
            ->get();

        // ========== BATCH LIST WITH DETAILS ==========
        $batches = Batch::with(['category', 'trainer'])
            ->withCount('batchParticipants')
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'code' => formatBatchCode($batch->id, $batch->created_at->year),
                    'title' => $batch->title,
                    'category' => $batch->category->name,
                    'trainer' => $batch->trainer->name,
                    'start_date' => $batch->start_date,
                    'end_date' => $batch->end_date,
                    'status' => $batch->status,
                    'participants_count' => $batch->batch_participants_count ?? 0,
                ];
            });

        // ========== PERFORMANCE PER CATEGORY ==========
        $categoryPerformance = Category::withCount([
            'batches' => function($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                }
            },
            'batches as completed_batches_count' => function($q) use ($startDate, $endDate) {
                $q->where('status', 'Completed');
                if ($startDate && $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                }
            }
        ])
        ->having('batches_count', '>', 0)
        ->get()
        ->map(function($category) use ($startDate, $endDate, $branchId) {
            // Get participants for this category
            $participants = BatchParticipant::whereHas('batch', function($q) use ($category, $startDate, $endDate) {
                $q->where('category_id', $category->id);
                if ($startDate && $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                }
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            })
            ->where('status', 'Approved')
            ->distinct('user_id')
            ->count('user_id');

            // Get passed participants
            $passed = BatchParticipant::whereHas('batch', function($q) use ($category, $startDate, $endDate) {
                $q->where('category_id', $category->id)
                  ->where('status', 'Completed');
                if ($startDate && $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                }
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            })
            ->where('status', 'Approved')
            ->whereHas('user.attendances', fn($q) => $q->where('status', 'Approved'))
            ->whereHas('user.feedback')
            ->distinct('user_id')
            ->count('user_id');

            return [
                'name' => $category->name,
                'total_batches' => $category->batches_count,
                'completed_batches' => $category->completed_batches_count,
                'total_participants' => $participants,
                'passed_participants' => $passed,
                'completion_rate' => $category->completed_batches_count > 0 
                    ? round(($category->completed_batches_count / $category->batches_count) * 100, 2) 
                    : 0,
            ];
        })
        ->sortByDesc('total_participants')
        ->values();

        // ========== HIGHLIGHTS ==========
        $highlights = [
            'pass_rate' => $totalParticipants > 0 ? round(($passedParticipants / $totalParticipants) * 100, 2) : 0,
            'avg_participants_per_batch' => $totalBatches > 0 ? round($totalParticipants / $totalBatches, 2) : 0,
            'active_categories' => Category::has('batches')->count(),
        ];

        // Get filter options
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('coordinator.laporan', compact(
            'totalBatches',
            'completedBatches',
            'totalParticipants',
            'passedParticipants',
            'certificatesIssued',
            'avgAttendanceRate',
            'batchStatusData',
            'participantStatusData',
            'participantsPerBranch',
            'batches',
            'categoryPerformance',
            'highlights',
            'branches',
            'period',
            'branchId'
        ));
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period, $customStart = null, $customEnd = null): array
    {
        $now = Carbon::now();

        return match($period) {
            'month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth()
            ],
            'year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear()
            ],
            'custom' => [
                $customStart ? Carbon::parse($customStart) : null,
                $customEnd ? Carbon::parse($customEnd) : null
            ],
            default => [null, null] // all time
        };
    }

    /**
     * Calculate average attendance rate
     */
    private function calculateAverageAttendance($startDate = null, $endDate = null, $branchId = null): float
    {
        $query = Attendance::whereHas('batch', function($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            }
        })
        ->when($branchId, function($q) use ($branchId) {
            $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
        });

        $totalAttendances = (clone $query)->count();
        $approvedAttendances = (clone $query)->where('status', 'Approved')->count();

        return $totalAttendances > 0 ? round(($approvedAttendances / $totalAttendances) * 100, 2) : 0;
    }

    /**
     * Export report to CSV
     */
    public function export(Request $request)
    {
        // Get same filters as index
        $period = $request->input('period', 'all');
        $branchId = $request->input('branch_id');
        [$startDate, $endDate] = $this->getDateRange(
            $period, 
            $request->input('start_date'), 
            $request->input('end_date')
        );

        // Get batch data
        $batches = Batch::with(['category', 'trainer'])
            ->withCount('batchParticipants')
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->orderBy('start_date', 'desc')
            ->get();

        $filename = 'training-report-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($batches) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, [
                'Kode Batch',
                'Judul',
                'Kategori',
                'Trainer',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Status',
                'Jumlah Peserta'
            ]);
            
            // Data
            foreach ($batches as $batch) {
                fputcsv($handle, [
                    formatBatchCode($batch->id, $batch->created_at->year),
                    $batch->title,
                    $batch->category->name,
                    $batch->trainer->name,
                    $batch->start_date->format('d/m/Y'),
                    $batch->end_date->format('d/m/Y'),
                    $batch->status,
                    $batch->batch_participants_count ?? 0,
                ]);
            }
            
            fclose($handle);
        }, $filename);
    }
}
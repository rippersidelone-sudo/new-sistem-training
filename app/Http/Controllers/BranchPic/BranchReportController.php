<?php

namespace App\Http\Controllers\BranchPic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchReportController extends Controller
{
    /**
     * Display branch reports and analytics
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        // Date filter (default: current year)
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfYear()->format('Y-m-d'));

        // === STATISTICS CARDS ===
        $participantsQuery = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            });

        // Total Peserta (unique users)
        $totalParticipants = $participantsQuery->clone()
            ->distinct('user_id')
            ->count('user_id');

        // Ongoing - peserta dengan batch ongoing
        $ongoingCount = $participantsQuery->clone()
            ->whereHas('batch', function($q) {
                $q->where('status', 'Ongoing');
            })
            ->where('status', 'Approved')
            ->count();

        // Completed - peserta dengan batch completed
        $completedCount = $participantsQuery->clone()
            ->whereHas('batch', function($q) {
                $q->where('status', 'Completed');
            })
            ->where('status', 'Approved')
            ->count();

        // Certificates issued
        $certificatesCount = Certificate::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->count();

        // === CHART DATA: Partisipasi per Batch ===
        $batchParticipation = Batch::whereBetween('start_date', [$startDate, $endDate])
            ->whereHas('batchParticipants.user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->withCount([
                'batchParticipants as completed_count' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('batch', function($batchQuery) {
                          $batchQuery->where('status', 'Completed');
                      })
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      });
                },
                'batchParticipants as ongoing_count' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('batch', function($batchQuery) {
                          $batchQuery->where('status', 'Ongoing');
                      })
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      });
                }
            ])
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        // Prepare chart data
        $chartLabels = $batchParticipation->pluck('title')->toArray();
        $chartCompleted = $batchParticipation->pluck('completed_count')->toArray();
        $chartOngoing = $batchParticipation->pluck('ongoing_count')->toArray();

        // === PIE CHART: Distribusi Status Peserta ===
        $statusDistribution = [
            'completed' => $completedCount,
            'ongoing' => $ongoingCount,
        ];

        // === TABLE: Ringkasan Batch Terbaru ===
        $recentBatches = Batch::whereBetween('start_date', [$startDate, $endDate])
            ->whereHas('batchParticipants.user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['trainer', 'category'])
            ->withCount([
                'batchParticipants as total_participants' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      });
                },
                'batchParticipants as completed_participants' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      })
                      ->whereHas('batch', function($batchQuery) {
                          $batchQuery->where('status', 'Completed');
                      });
                }
            ])
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        return view('branch_pic.laporan-cabang', compact(
            'branch',
            'totalParticipants',
            'ongoingCount',
            'completedCount',
            'certificatesCount',
            'chartLabels',
            'chartCompleted',
            'chartOngoing',
            'statusDistribution',
            'recentBatches',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export branch report to Excel/CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = $user->branch;

        // Date filter
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfYear()->format('Y-m-d'));

        // Get detailed participant data
        $participants = BatchParticipant::whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereHas('batch', function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->with(['user', 'batch.category', 'batch.trainer'])
            ->get();

        // Prepare CSV data
        $filename = 'laporan_cabang_' . $branch->name . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($participants, $branch) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Cabang',
                'Nama Peserta',
                'Email',
                'Batch',
                'Kategori',
                'Trainer',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Status Batch',
                'Status Peserta',
                'Tanggal Daftar'
            ]);
            
            // Data rows
            foreach ($participants as $participant) {
                fputcsv($file, [
                    $branch->name,
                    $participant->user->name,
                    $participant->user->email,
                    $participant->batch->title,
                    $participant->batch->category->name ?? '-',
                    $participant->batch->trainer->name ?? '-',
                    $participant->batch->start_date->format('d/m/Y'),
                    $participant->batch->end_date->format('d/m/Y'),
                    $participant->batch->status,
                    $participant->status,
                    $participant->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get chart data for AJAX requests
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfYear()->format('Y-m-d'));

        // Get batch participation data
        $batchParticipation = Batch::whereBetween('start_date', [$startDate, $endDate])
            ->whereHas('batchParticipants.user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->withCount([
                'batchParticipants as completed_count' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('batch', function($batchQuery) {
                          $batchQuery->where('status', 'Completed');
                      })
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      });
                },
                'batchParticipants as ongoing_count' => function($q) use ($branchId) {
                    $q->where('status', 'Approved')
                      ->whereHas('batch', function($batchQuery) {
                          $batchQuery->where('status', 'Ongoing');
                      })
                      ->whereHas('user', function($userQuery) use ($branchId) {
                          $userQuery->where('branch_id', $branchId);
                      });
                }
            ])
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $batchParticipation->pluck('title'),
                'completed' => $batchParticipation->pluck('completed_count'),
                'ongoing' => $batchParticipation->pluck('ongoing_count'),
            ]
        ]);
    }
}
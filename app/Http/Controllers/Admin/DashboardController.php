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
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Simplified filter with preset periods
        $period = $request->input('period', 'this_month');
        $year = $request->input('year');
        $month = $request->input('month');
        
        // ✅ Get date range based on period
        $dateRange = $this->getDateRangeByPeriod($period, $year, $month);
        
        // ✅ Build filter options
        $filterOptions = $this->buildSimplifiedFilterOptions($period, $year, $month);

        // Statistics Cards (filtered)
        $totalBatches = Batch::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $activeBatches = Batch::where('status', 'Ongoing')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        
        $totalParticipants = User::whereHas('role', function($q) {
            $q->where('name', 'Participant');
        })
        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
        ->count();
        
        $passedParticipants = BatchParticipant::where('status', 'Approved')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->distinct('user_id')
            ->count('user_id');
        
        $activeBranches = Branch::whereHas('users', function($q) use ($dateRange) {
            $q->whereBetween('users.created_at', [$dateRange['start'], $dateRange['end']]);
        })->count();
        
        $totalCertificates = Certificate::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        // Monthly Trend
        $monthlyTrend = $this->getMonthlyTrend($period, $dateRange);

        // Batch Status
        $batchStatus = [
            'Scheduled' => Batch::where('status', 'Scheduled')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'Ongoing' => Batch::where('status', 'Ongoing')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'Completed' => Batch::where('status', 'Completed')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
        ];

        // Participants per Course/Category
        $participantsPerCategory = DB::table('batch_participants')
            ->join('batches', 'batch_participants.batch_id', '=', 'batches.id')
            ->join('categories', 'batches.category_id', '=', 'categories.id')
            ->whereBetween('batch_participants.created_at', [$dateRange['start'], $dateRange['end']])
            ->where('batch_participants.status', 'Approved')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT batch_participants.user_id) as participant_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('participant_count')
            ->limit(10) // Top 10 categories
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->participant_count
                ];
            });

        // Recent Batches
        $recentBatches = Batch::with(['trainer', 'category'])
            ->withCount('batchParticipants')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
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
            'participantsPerCategory',
            'recentBatches',
            'filterOptions',
            'dateRange'
        ));
    }

    /**
     * ✅ Simplified date range logic with preset periods
     */
    private function getDateRangeByPeriod($period, $year = null, $month = null)
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
                
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => '30 Hari Terakhir'
                ];
                
            case 'last_90_days':
                return [
                    'start' => $now->copy()->subDays(90)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => '90 Hari Terakhir'
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
                
            case 'specific_year':
                $yearNum = $year ?? $now->year;
                return [
                    'start' => Carbon::create($yearNum, 1, 1)->startOfYear(),
                    'end' => Carbon::create($yearNum, 12, 31)->endOfYear(),
                    'label' => 'Tahun ' . $yearNum
                ];
                
            case 'specific_month':
                if ($year && $month) {
                    $date = Carbon::create($year, $month, 1);
                    return [
                        'start' => $date->copy()->startOfMonth(),
                        'end' => $date->copy()->endOfMonth(),
                        'label' => $date->locale('id')->translatedFormat('F Y')
                    ];
                }
                // Fallback to current month
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => $now->locale('id')->translatedFormat('F Y')
                ];
                
            case 'all_time':
                $firstBatch = Batch::orderBy('created_at')->first();
                $startDate = $firstBatch ? $firstBatch->created_at : $now->copy()->subYears(5);
                return [
                    'start' => $startDate->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Semua Waktu'
                ];
                
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => $now->locale('id')->translatedFormat('F Y')
                ];
        }
    }

    /**
     * ✅ Build simplified filter options
     */
    private function buildSimplifiedFilterOptions($period, $year, $month)
    {
        // Preset periods
        $periods = [
            ['value' => 'today', 'label' => 'Hari Ini'],
            ['value' => 'this_week', 'label' => 'Minggu Ini'],
            ['value' => 'this_month', 'label' => 'Bulan Ini'],
            ['value' => 'last_30_days', 'label' => '30 Hari Terakhir'],
            ['value' => 'last_90_days', 'label' => '90 Hari Terakhir'],
            ['value' => 'this_year', 'label' => 'Tahun Ini'],
            ['value' => 'last_year', 'label' => 'Tahun Lalu'],
            ['value' => 'specific_year', 'label' => 'Pilih Tahun'],
            ['value' => 'specific_month', 'label' => 'Pilih Bulan'],
            ['value' => 'all_time', 'label' => 'Semua Waktu'],
        ];

        // Years list (last 5 years)
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $yearNum = now()->year - $i;
            $years[] = [
                'value' => (string)$yearNum,
                'label' => (string)$yearNum
            ];
        }

        // Months list
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create(null, $m, 1);
            $months[] = [
                'value' => str_pad($m, 2, '0', STR_PAD_LEFT),
                'label' => $date->locale('id')->translatedFormat('F')
            ];
        }

        return [
            'period' => $period,
            'year' => $year ?? (string)now()->year,
            'month' => $month ?? now()->format('m'),
            'periods' => $periods,
            'years' => $years,
            'months' => $months,
        ];
    }

    /**
     * ✅ Get monthly trend data
     */
    private function getMonthlyTrend($period, $dateRange)
    {
        $labels = [];
        $batchData = [];
        $participantData = [];

        // For yearly view, show all 12 months
        if (in_array($period, ['this_year', 'last_year', 'specific_year'])) {
            $year = $dateRange['start']->year;
            
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($year, $month, 1);
                $labels[] = $date->locale('id')->translatedFormat('M');

                $batchData[] = Batch::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $participantData[] = BatchParticipant::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'Approved')
                    ->distinct('user_id')
                    ->count('user_id');
            }
        } else {
            // For other periods, show last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->locale('id')->translatedFormat('M Y');

                $batchData[] = Batch::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $participantData[] = BatchParticipant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'Approved')
                    ->distinct('user_id')
                    ->count('user_id');
            }
        }

        return [
            'labels' => $labels,
            'batches' => $batchData,
            'participants' => $participantData,
        ];
    }
}
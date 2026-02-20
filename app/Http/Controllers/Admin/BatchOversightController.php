<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BatchOversightController extends Controller
{
    /**
     * Display batch oversight with filters (WITH PERIOD FILTER)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $period = $request->input('period', 'all_time');
        
        // ✅ Get date range based on period
        $dateRange = $this->getDateRange($period);

        $batches = Batch::query()
            ->select([
                'id', 
                'title', 
                'trainer_id', 
                'category_id', 
                'start_date', 
                'end_date', 
                'status', 
                'created_at'
            ])
            ->with([
                'trainer:id,name',
                'category:id,name'
            ])
            ->withCount([
                'batchParticipants as participants_count' => function($query) {
                    $query->where('status', 'Approved');
                },
                'batchParticipants as passed_count' => function($query) {
                    $query->where('status', 'Approved')
                        ->whereHas('user', function($q) {
                            $q->whereHas('attendances', function($subQ) {
                                $subQ->where('status', 'Approved');
                            });
                        });
                }
            ])
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhereHas('trainer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            // ✅ Filter by period (created_at)
            ->when($period !== 'all_time', function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // ✅ Build filter options with period
        $filterOptions = $this->buildFilterOptions($period);

        return view('admin.batch-oversight', compact('batches', 'filterOptions', 'dateRange'));
    }

    /**
     * ✅ Get date range based on period
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
    private function buildFilterOptions($period)
    {
        return [
            [
                'name' => 'status',
                'placeholder' => 'Semua Status',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'Scheduled', 'label' => 'Scheduled'],
                    ['value' => 'Ongoing', 'label' => 'Ongoing'],
                    ['value' => 'Completed', 'label' => 'Completed']
                ])
            ],
            [
                'name' => 'period',
                'placeholder' => 'Semua Waktu',
                'options' => collect([
                    ['value' => 'all_time', 'label' => 'Semua Waktu'],
                    ['value' => 'today', 'label' => 'Hari Ini'],
                    ['value' => 'this_week', 'label' => 'Minggu Ini'],
                    ['value' => 'this_month', 'label' => 'Bulan Ini'],
                    ['value' => 'last_30_days', 'label' => '30 Hari Terakhir'],
                    ['value' => 'last_90_days', 'label' => '90 Hari Terakhir'],
                    ['value' => 'this_year', 'label' => 'Tahun Ini'],
                    ['value' => 'last_year', 'label' => 'Tahun Lalu'],
                ])
            ]
        ];
    }

    /**
     * Show batch detail
     */
    public function show(Batch $batch)
    {
        $batch->load(['trainer:id,name', 'category:id,name']);

        $participantsCount = $batch->batchParticipants()
            ->where('status', 'Approved')
            ->count();

        $passedCount = $batch->batchParticipants()
            ->where('status', 'Approved')
            ->whereHas('user', function($query) use ($batch) {
                $query->whereHas('attendances', function($q) use ($batch) {
                    $q->where('batch_id', $batch->id)
                      ->where('status', 'Approved');
                });
            })
            ->count();
        
        return response()->json([
            'success' => true,
            'batch' => [
                'id' => $batch->id,
                'title' => $batch->title,
                'category' => $batch->category,
                'trainer' => $batch->trainer,
                'start_date' => $batch->start_date,
                'end_date' => $batch->end_date,
                'status' => $batch->status,
                'zoom_link' => $batch->zoom_link,
                'participants_count' => $participantsCount,
                'passed_count' => $passedCount,
                'created_at' => $batch->created_at,
            ],
        ]);
    }

    /**
     * Export batch data to CSV
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $period = $request->input('period', 'all_time');
        
        $dateRange = $this->getDateRange($period);

        $batches = Batch::with(['trainer:id,name', 'category:id,name'])
            ->withCount([
                'batchParticipants as participants_count' => function($q) {
                    $q->where('status', 'Approved');
                },
                'batchParticipants as passed_count' => function($q) {
                    $q->where('status', 'Approved')
                        ->whereHas('user', function($query) {
                            $query->whereHas('attendances', function($subQuery) {
                                $subQuery->where('status', 'Approved');
                            });
                        });
                }
            ])
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhereHas('trainer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($period !== 'all_time', function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->streamDownload(function() use ($batches) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8 support
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
                'Peserta Lulus',
                'Tingkat Kelulusan (%)',
                'Status',
                'Zoom Link'
            ]);
            
            // Data
            foreach ($batches as $batch) {
                $passRate = $batch->participants_count > 0 
                    ? round(($batch->passed_count / $batch->participants_count) * 100, 1) 
                    : 0;

                fputcsv($handle, [
                    formatBatchCode($batch->id, $batch->created_at->year),
                    $batch->title,
                    $batch->category->name ?? '-',
                    $batch->trainer->name ?? '-',
                    $batch->start_date->format('d/m/Y H:i'),
                    $batch->end_date->format('d/m/Y H:i'),
                    $batch->participants_count,
                    $batch->passed_count,
                    $passRate . '%',
                    $batch->status,
                    $batch->zoom_link ?? '-',
                ]);
            }
            
            fclose($handle);
        }, 'batch-oversight-' . date('Y-m-d-His') . '.csv');
    }
}
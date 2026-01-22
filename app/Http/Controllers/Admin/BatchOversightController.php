<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use Illuminate\Http\Request;

class BatchOversightController extends Controller
{
    /**
     * Display batch oversight with filters
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $status = $request->input('status');
        $branchId = $request->input('branch_id');

        // Query batches with filters
        $batches = Batch::with(['trainer', 'category'])
            ->withCount([
                'batchParticipants as participants_count',
                'batchParticipants as passed_count' => function($q) {
                    $q->where('status', 'Approved')
                        ->whereHas('user', function($query) {
                            // User must have attendance AND feedback
                            $query->whereHas('attendances', function($subQuery) {
                                $subQuery->where('status', 'Approved');
                            })
                            ->whereHas('feedback');
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
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('batchParticipants.user', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $branches = Branch::orderBy('name')->get();

        // Build filter options - ADA STATUS & BRANCH (TIDAK ADA ROLE)
        $filterOptions = [
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
                'name' => 'branch_id',
                'placeholder' => 'Semua Cabang',
                'options' => collect([
                    ['value' => '', 'label' => 'Semua Cabang']
                ])->merge(
                    $branches->map(fn($branch) => [
                        'value' => (string) $branch->id,
                        'label' => $branch->name
                    ])
                )
            ]
        ];

        return view('admin.batch-oversight', compact('batches', 'branches', 'filterOptions'));
    }

    /**
     * Show detailed batch information (AJAX)
     */
    public function show(Batch $batch)
    {
        \Log::info('Batch Detail Request', ['batch_id' => $batch->id]);
        
        $batch->load([
            'trainer', 
            'category', 
            'batchParticipants' => function($query) {
                $query->where('status', 'Approved');
            },
            'batchParticipants.user.branch'
        ]);

        // Calculate passed participants
        $passedCount = $batch->batchParticipants()
            ->where('status', 'Approved')
            ->whereHas('user', function($query) use ($batch) {
                $query->whereHas('attendances', function($q) use ($batch) {
                    $q->where('batch_id', $batch->id)
                    ->where('status', 'Approved');
                })
                ->whereHas('feedback', function($q) use ($batch) {
                    $q->where('batch_id', $batch->id);
                });
            })
            ->count();

        $batch->passed_count = $passedCount;
        $batch->participants_count = $batch->batchParticipants->count();
        
        \Log::info('Batch Detail Response', [
            'batch_id' => $batch->id,
            'title' => $batch->title,
            'participants_count' => $batch->participants_count,
            'passed_count' => $passedCount
        ]);
        
        return response()->json([
            'success' => true,
            'batch' => $batch,
        ]);
    }

    /**
     * Export batch data to CSV
     */
    public function export(Request $request)
    {
        // Apply same filters as index
        $search = $request->input('search');
        $status = $request->input('status');
        $branchId = $request->input('branch_id');

        $batches = Batch::with(['trainer', 'category'])
            ->withCount([
                'batchParticipants as participants_count',
                'batchParticipants as passed_count' => function($q) {
                    $q->where('status', 'Approved')
                        ->whereHas('user', function($query) {
                            $query->whereHas('attendances', function($subQuery) {
                                $subQuery->where('status', 'Approved');
                            })
                            ->whereHas('feedback');
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
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('batchParticipants.user', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
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
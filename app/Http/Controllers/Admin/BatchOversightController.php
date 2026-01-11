<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use Illuminate\Http\Request;

class BatchOversightController extends Controller
{
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

        $branches = Branch::orderBy('name')->get();

        return view('admin.batch-oversight', compact('batches', 'branches'));
    }

    /**
     * Show detailed batch information
     */
    public function show(Batch $batch)
    {
        $batch->load([
            'trainer', 
            'category', 
            'batchParticipants.user.branch'
        ]);
        
        return response()->json([
            'success' => true,
            'batch' => $batch,
        ]);
    }

    /**
     * Export batch data to CSV
     */
    public function export()
    {
        $batches = Batch::with(['trainer', 'category'])
            ->withCount('batchParticipants as participants_count')
            ->get();

        return response()->streamDownload(function() use ($batches) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, ['Kode', 'Judul', 'Kategori', 'Trainer', 'Tanggal Mulai', 'Tanggal Selesai', 'Peserta', 'Status']);
            
            // Data
            foreach ($batches as $batch) {
                fputcsv($handle, [
                    formatBatchCode($batch->id, $batch->created_at->year),
                    $batch->title,
                    $batch->category->name ?? '-',
                    $batch->trainer->name ?? '-',
                    $batch->start_date->format('d/m/Y'),
                    $batch->end_date->format('d/m/Y'),
                    $batch->participants_count,
                    $batch->status,
                ]);
            }
            
            fclose($handle);
        }, 'batch-oversight-' . date('Y-m-d') . '.csv');
    }
}
<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total batches
        $totalBatches = Batch::count();
        
        // Get batches by status
        $scheduledBatches = Batch::where('status', 'Scheduled')->count();
        $ongoingBatches = Batch::where('status', 'Ongoing')->count();
        $completedBatches = Batch::where('status', 'Completed')->count();
        
        // Get pending approvals
        $pendingApprovals = BatchParticipant::where('status', 'Pending')->count();
        
        // Get total participants (unique users who have registered)
        $totalParticipants = BatchParticipant::distinct('user_id')->count('user_id');
        
        // Chart Data: Participation per Batch Status
        $batchChartData = [
            'labels' => ['Scheduled', 'Ongoing', 'Completed'],
            'data' => [$scheduledBatches, $ongoingBatches, $completedBatches],
        ];
        
        // Recent Batches with Status
        $recentBatches = Batch::with(['category'])
            ->withCount('batchParticipants as participants_count')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'title' => $batch->title,
                    'code' => formatBatchCode($batch->id, $batch->created_at->year),
                    'participants_count' => $batch->participants_count,
                    'max_quota' => $batch->max_quota,
                    'status' => $batch->status,
                ];
            });
        
        // Pending Participants with Details
        $pendingParticipants = BatchParticipant::with(['user', 'batch'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'user_name' => $participant->user->name,
                    'batch_title' => $participant->batch->title,
                    'created_at' => $participant->created_at,
                ];
            });
        
        return view('coordinator.dashboard', compact(
            'totalBatches',
            'scheduledBatches',
            'ongoingBatches',
            'completedBatches',
            'pendingApprovals',
            'totalParticipants',
            'batchChartData',
            'recentBatches',
            'pendingParticipants'
        ));
    }
}
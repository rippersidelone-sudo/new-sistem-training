<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the participant dashboard
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get participating batches with relationships
        $participatingBatches = $user->participatingBatches()
            ->with(['category', 'trainer'])
            ->withPivot('status')
            ->get();

        // Statistics
        $totalBatches = $participatingBatches->count();
        
        $ongoingBatches = $participatingBatches->filter(function($batch) {
            return $batch->status === 'Ongoing' && 
                   $batch->pivot->status === 'Approved';
        })->count();
        
        $completedBatches = $participatingBatches->filter(function($batch) {
            return $batch->status === 'Completed' && 
                   $batch->pivot->status === 'Approved';
        })->count();

        // Get certificates count
        $certificatesCount = $user->certificates()->count();

        // Get latest batch with attendance status
        $latestBatch = $participatingBatches
            ->where('pivot.status', 'Approved')
            ->sortByDesc('start_date')
            ->first();

        $attendanceStatus = null;
        if ($latestBatch) {
            $latestAttendance = $user->attendances()
                ->where('batch_id', $latestBatch->id)
                ->latest('attendance_date')
                ->first();
            
            $attendanceStatus = $latestAttendance?->status ?? 'Belum Check-In';
        }

        return view('participant.dashboard', compact(
            'totalBatches',
            'ongoingBatches',
            'completedBatches',
            'certificatesCount',
            'latestBatch',
            'attendanceStatus'
        ));
    }
}
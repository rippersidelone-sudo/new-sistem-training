<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance page with batches that need check-in
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get approved batches for today or ongoing
        // FIX: Specify table name for ambiguous 'status' column
        $batches = $user->participatingBatches()
            ->with(['category', 'trainer'])
            ->wherePivot('status', 'Approved')
            ->whereIn('batches.status', ['Scheduled', 'Ongoing']) // ← SPECIFY TABLE NAME
            ->orderBy('batches.start_date', 'desc') // ← SPECIFY TABLE NAME
            ->get();

        // Get attendance statistics
        $allAttendances = $user->attendances()->get();
        
        $validatedCount = $allAttendances->where('status', 'Present')->count();
        $pendingCount = $allAttendances->where('status', 'Pending')->count();
        $notCheckedInCount = 0;

        // Process each batch with attendance info
        $batches->each(function($batch) use ($user, &$notCheckedInCount) {
            // Check if there's attendance for today
            $todayAttendance = $user->attendances()
                ->where('batch_id', $batch->id)
                ->whereDate('attendance_date', Carbon::today())
                ->first();

            $batch->today_attendance = $todayAttendance;
            $batch->attendance_status = $todayAttendance?->status ?? 'Belum Check-In';
            $batch->can_checkin = !$todayAttendance && $batch->status === 'Ongoing';
            $batch->checkin_time = $todayAttendance?->checkin_time;

            if (!$todayAttendance && $batch->status === 'Ongoing') {
                $notCheckedInCount++;
            }
        });

        return view('participant.absensi', compact(
            'batches',
            'validatedCount',
            'pendingCount',
            'notCheckedInCount'
        ));
    }

    /**
     * Check-in attendance for a batch
     */
    public function checkin(Request $request, Batch $batch): RedirectResponse
    {
        $user = Auth::user();

        // Verify user is approved participant
        $isApproved = $user->participatingBatches()
            ->where('batches.id', $batch->id)
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isApproved) {
            return redirect()->back()
                ->with('error', 'Anda tidak terdaftar atau belum disetujui di batch ini');
        }

        // Check if batch is ongoing
        if ($batch->status !== 'Ongoing') {
            return redirect()->back()
                ->with('error', 'Batch ini belum dimulai atau sudah selesai');
        }

        // Check if already checked in today
        $existingAttendance = Attendance::where('batch_id', $batch->id)
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('warning', 'Anda sudah check-in untuk batch ini hari ini');
        }

        try {
            DB::beginTransaction();

            // Create attendance record
            $attendance = Attendance::create([
                'batch_id' => $batch->id,
                'user_id' => $user->id,
                'attendance_date' => Carbon::today(),
                'checkin_time' => Carbon::now(),
                'status' => 'Pending', // Waiting for trainer validation
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Check-in berhasil! Menunggu validasi dari trainer.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }

    /**
     * Get attendance history
     */
    public function history(): View
    {
        $user = Auth::user();
        
        $attendances = $user->attendances()
            ->with(['batch.category', 'batch.trainer'])
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        // Statistics
        $totalAttendances = $user->attendances()->count();
        $presentCount = $user->attendances()->where('status', 'Present')->count();
        $pendingCount = $user->attendances()->where('status', 'Pending')->count();
        $absentCount = $user->attendances()->where('status', 'Absent')->count();

        $attendanceRate = $totalAttendances > 0 
            ? round(($presentCount / $totalAttendances) * 100, 2) 
            : 0;

        return view('participant.attendance-history', compact(
            'attendances',
            'totalAttendances',
            'presentCount',
            'pendingCount',
            'absentCount',
            'attendanceRate'
        ));
    }
}
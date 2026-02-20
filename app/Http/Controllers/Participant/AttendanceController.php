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
        
        $batches = $user->participatingBatches()
            ->with(['category', 'trainer'])
            ->wherePivot('status', 'Approved')
            ->whereIn('batches.status', ['Scheduled', 'Ongoing'])
            ->orderBy('batches.start_date', 'desc')
            ->get();

        // ✅ Auto-update status batch berdasarkan tanggal hari ini
        $batches->each(function($batch) {
            $today    = Carbon::today();
            $startDay = Carbon::parse($batch->start_date)->startOfDay();
            $endDay   = Carbon::parse($batch->end_date)->endOfDay();

            if ($batch->status === 'Scheduled' && $today->between($startDay, $endDay)) {
                $batch->update(['status' => 'Ongoing']);
                $batch->status = 'Ongoing';
            } elseif ($batch->status === 'Ongoing' && $today->gt($endDay)) {
                $batch->update(['status' => 'Completed']);
                $batch->status = 'Completed';
            }
        });

        // Keluarkan batch yang sudah completed setelah update
        $batches = $batches->filter(fn($b) => in_array($b->status, ['Scheduled', 'Ongoing']));

        // Statistik kehadiran
        $allAttendances  = $user->attendances()->get();
        $validatedCount  = $allAttendances->where('status', 'Approved')->count();
        $pendingCount    = $allAttendances->where('status', 'Checked-in')->count();
        $notCheckedInCount = 0;

        // Proses setiap batch
        $batches->each(function($batch) use ($user, &$notCheckedInCount) {
            $today = Carbon::today()->toDateString();
            $batchStartDate = Carbon::parse($batch->start_date)->toDateString();
            $batchEndDate   = Carbon::parse($batch->end_date)->toDateString();
            $isActiveToday  = $today >= $batchStartDate && $today <= $batchEndDate;

            // Cek absensi hari ini
            $todayAttendance = $user->attendances()
                ->where('batch_id', $batch->id)
                ->whereDate('attendance_date', Carbon::today())
                ->first();

            $batch->today_attendance  = $todayAttendance;
            $batch->attendance_status = $todayAttendance?->status ?? 'Belum Check-In';
            $batch->checkin_time      = $todayAttendance?->checkin_time; // string HH:MM:SS

            // ✅ Fix: can_checkin berdasarkan tanggal aktif, bukan hanya status Ongoing
            $batch->can_checkin = !$todayAttendance &&
                in_array($batch->status, ['Ongoing', 'Scheduled']) &&
                $isActiveToday;

            if (!$todayAttendance && $isActiveToday) {
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

        // Verifikasi user adalah participant yang approved
        $isApproved = $user->participatingBatches()
            ->where('batches.id', $batch->id)
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isApproved) {
            return redirect()->back()
                ->with('error', 'Anda tidak terdaftar atau belum disetujui di batch ini');
        }

        // ✅ Fix: Cek apakah batch aktif hari ini (bukan hanya status Ongoing)
        $today          = Carbon::today()->toDateString();
        $batchStartDate = Carbon::parse($batch->start_date)->toDateString();
        $batchEndDate   = Carbon::parse($batch->end_date)->toDateString();
        $isActiveToday  = $today >= $batchStartDate && $today <= $batchEndDate;

        if (!$isActiveToday) {
            return redirect()->back()
                ->with('error', 'Batch ini tidak aktif hari ini');
        }

        if ($batch->status === 'Completed') {
            return redirect()->back()
                ->with('error', 'Batch ini sudah selesai');
        }

        // Cek apakah sudah check-in hari ini
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

            // ✅ Simpan checkin_time sebagai string TIME (HH:MM:SS) sesuai kolom DB
            Attendance::create([
                'batch_id'        => $batch->id,
                'user_id'         => $user->id,
                'attendance_date' => Carbon::today()->toDateString(),
                'checkin_time'    => Carbon::now()->format('H:i:s'),
                'status'          => 'Checked-in',
            ]);

            // Auto-update status batch ke Ongoing jika masih Scheduled
            if ($batch->status === 'Scheduled') {
                $batch->update(['status' => 'Ongoing']);
            }

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

        $totalAttendances = $user->attendances()->count();
        $presentCount     = $user->attendances()->where('status', 'Approved')->count();
        $pendingCount     = $user->attendances()->where('status', 'Checked-in')->count();
        $absentCount      = $user->attendances()->where('status', 'Absent')->count();

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
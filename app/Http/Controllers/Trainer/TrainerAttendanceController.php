<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TrainerAttendanceController extends Controller
{
    /**
     * Display attendance approval page
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $batchId = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        // Get trainer's batches for filter dropdown
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with('category')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'label' => $batch->title . ' - ' . formatBatchCode($batch->id),
                    'value' => $batch->id,
                ];
            });

        // Get branches for filter dropdown
        $branches = DB::table('branches')
            ->whereIn('id', function ($query) use ($trainer) {
                $query->select('branch_id')
                    ->from('users')
                    ->whereIn('id', function ($subQuery) use ($trainer) {
                        $subQuery->select('user_id')
                            ->from('batch_participants')
                            ->whereIn('batch_id', function ($batchQuery) use ($trainer) {
                                $batchQuery->select('id')
                                    ->from('batches')
                                    ->where('trainer_id', $trainer->id);
                            })
                            ->where('status', 'Approved');
                    })
                    ->whereNotNull('branch_id');
            })
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'label' => $branch->name,
                    'value' => $branch->id,
                ];
            });

        // Get all attendances data
        $attendancesData = $this->getAttendancesData($trainer->id, $batchId, $branchId);

        // Calculate statistics
        $stats = [
            'pending' => $attendancesData->where('status', 'Checked-in')->count(),
            'validated' => $attendancesData->where('status', 'Approved')->count(),
            'absent' => $attendancesData->where('status', 'Absent')->count() 
                      + $attendancesData->where('status', 'Not Checked-in')->count(),
        ];

        return view('trainer.approval-kehadiran', compact(
            'attendancesData',
            'stats',
            'batches',
            'branches',
            'batchId',
            'branchId'
        ));
    }

    /**
     * Get attendances data including participants who haven't checked in
     *
     * @param int $trainerId
     * @param int|null $batchId
     * @param int|null $branchId
     * @return \Illuminate\Support\Collection
     */
    private function getAttendancesData(int $trainerId, ?int $batchId = null, ?int $branchId = null)
    {
        // Get all approved participants for trainer's batches
        $participantsQuery = BatchParticipant::whereHas('batch', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })
        ->where('status', 'Approved')
        ->with(['user.branch', 'batch.category']);

        // Apply filters
        if ($batchId) {
            $participantsQuery->where('batch_id', $batchId);
        }

        if ($branchId) {
            $participantsQuery->whereHas('user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }

        $participants = $participantsQuery->get();

        // Map participants to attendance data
        return $participants->map(function ($participant) {
            // Check if participant has attendance record for today
            $attendance = Attendance::where('batch_id', $participant->batch_id)
                ->where('user_id', $participant->user_id)
                ->where('attendance_date', now()->toDateString())
                ->first();

            if ($attendance) {
                // Has attendance record
                return [
                    'id' => $attendance->id,
                    'type' => 'attendance', // untuk identify apakah ada record
                    'user_id' => $participant->user_id,
                    'user_name' => $participant->user->name,
                    'user_email' => $participant->user->email,
                    'batch_id' => $participant->batch_id,
                    'batch_title' => $participant->batch->title,
                    'batch_code' => formatBatchCode($participant->batch->id),
                    'branch_name' => $participant->user->branch->name ?? '-',
                    'checkin_time' => $attendance->checkin_time 
                        ? formatDateTime($attendance->checkin_time) 
                        : '-',
                    'status' => $attendance->status,
                    'notes' => $attendance->notes,
                ];
            } else {
                // No attendance record - "Belum Check-In"
                return [
                    'id' => null,
                    'type' => 'no-attendance',
                    'user_id' => $participant->user_id,
                    'user_name' => $participant->user->name,
                    'user_email' => $participant->user->email,
                    'batch_id' => $participant->batch_id,
                    'batch_title' => $participant->batch->title,
                    'batch_code' => formatBatchCode($participant->batch->id),
                    'branch_name' => $participant->user->branch->name ?? '-',
                    'checkin_time' => '-',
                    'status' => 'Not Checked-in', // Custom status
                    'notes' => null,
                ];
            }
        });
    }

    /**
     * Approve a single attendance
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Attendance $attendance): RedirectResponse
    {
        $trainer = Auth::user();

        // Verify trainer owns the batch
        if ($attendance->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk memvalidasi kehadiran ini');
        }

        // Only allow approval for checked-in status
        if ($attendance->status !== 'Checked-in') {
            return redirect()
                ->back()
                ->with('error', 'Kehadiran ini sudah divalidasi sebelumnya');
        }

        // Update attendance status
        $attendance->update([
            'status' => 'Approved',
            'notes' => $attendance->notes 
                ? $attendance->notes . ' | Divalidasi oleh ' . $trainer->name 
                : 'Divalidasi oleh ' . $trainer->name,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Kehadiran berhasil divalidasi');
    }

    /**
     * Reject/Mark as absent a single attendance
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'attendance_id' => 'nullable|exists:attendances,id',
            'user_id' => 'required|exists:users,id',
            'batch_id' => 'required|exists:batches,id',
            'reason' => 'nullable|string|max:500',
        ]);

        // Verify trainer owns the batch
        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk batch ini');
        }

        $notes = $validated['reason'] ?? 'Ditandai tidak hadir oleh ' . $trainer->name;

        // If attendance record exists, update it
        if ($validated['attendance_id']) {
            $attendance = Attendance::findOrFail($validated['attendance_id']);
            $attendance->update([
                'status' => 'Absent',
                'notes' => $attendance->notes 
                    ? $attendance->notes . ' | ' . $notes 
                    : $notes,
            ]);
        } else {
            // Create new attendance record with Absent status
            Attendance::create([
                'batch_id' => $validated['batch_id'],
                'user_id' => $validated['user_id'],
                'attendance_date' => now()->toDateString(),
                'checkin_time' => null,
                'status' => 'Absent',
                'notes' => $notes,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Kehadiran ditandai sebagai tidak hadir');
    }

    /**
     * Approve all pending attendances for trainer's batches
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveAll(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        // Get batch_id filter if provided
        $batchId = $request->input('batch_id');

        // Build query for pending attendances
        $query = Attendance::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })
        ->where('status', 'Checked-in');

        // Apply batch filter if provided
        if ($batchId) {
            $query->where('batch_id', $batchId);
        }

        // Update all pending attendances
        $updatedCount = $query->update([
            'status' => 'Approved',
            'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' | Divalidasi massal oleh " . $trainer->name . "')"),
        ]);

        if ($updatedCount === 0) {
            return redirect()
                ->back()
                ->with('info', 'Tidak ada kehadiran yang perlu divalidasi');
        }

        return redirect()
            ->back()
            ->with('success', "Berhasil memvalidasi {$updatedCount} kehadiran");
    }

    /**
     * Manual check-in for participant (if they can't do it themselves)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manualCheckIn(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify trainer owns the batch
        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk batch ini');
        }

        // Verify user is a participant in this batch
        $isParticipant = $batch->participants()
            ->where('user_id', $validated['user_id'])
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isParticipant) {
            return redirect()
                ->back()
                ->with('error', 'User bukan peserta batch ini');
        }

        // Check if attendance already exists
        $existingAttendance = Attendance::where('batch_id', $validated['batch_id'])
            ->where('user_id', $validated['user_id'])
            ->where('attendance_date', now()->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()
                ->back()
                ->with('error', 'Kehadiran untuk peserta ini sudah tercatat');
        }

        // Create attendance record
        Attendance::create([
            'batch_id' => $validated['batch_id'],
            'user_id' => $validated['user_id'],
            'attendance_date' => now()->toDateString(),
            'checkin_time' => now(),
            'status' => 'Approved', // Direct approval by trainer
            'notes' => ($validated['notes'] ?? '') . ' | Check-in manual oleh ' . $trainer->name,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Check-in manual berhasil dicatat');
    }
}
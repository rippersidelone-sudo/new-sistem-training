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
    public function index(Request $request): View
    {
        $trainer = Auth::user();

        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        $batchId  = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        $batches = Batch::where('trainer_id', $trainer->id)
            ->with('category')
            ->orderByRaw("FIELD(status, 'Ongoing', 'Scheduled', 'Completed')")
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                $statusLabel = match($batch->status) {
                    'Ongoing'   => 'ONGOING -',
                    'Scheduled' => 'SCHEDULED -',
                    'Completed' => 'COMPLETED -',
                    default     => '',
                };
                return [
                    'id'    => $batch->id,
                    'label' => $statusLabel . ' ' . $batch->title . ' — ' . formatBatchCode($batch->id),
                    'value' => $batch->id,
                ];
            });

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
            ->orderBy('name')
            ->get()
            ->map(function ($branch) {
                return [
                    'id'    => $branch->id,
                    'label' => $branch->name,
                    'value' => $branch->id,
                ];
            });

        $attendancesData = $this->getAttendancesData($trainer->id, $batchId, $branchId);

        $stats = [
            'pending'   => $attendancesData->where('status', 'Checked-in')->count(),
            'validated' => $attendancesData->where('status', 'Approved')->count(),
            'absent'    => $attendancesData->where('status', 'Absent')->count()
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

    private function getAttendancesData(int $trainerId, ?int $batchId = null, ?int $branchId = null)
    {
        $participantsQuery = BatchParticipant::whereHas('batch', function ($query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        })
        ->where('status', 'Approved')
        ->with(['user.branch', 'batch.category']);

        if ($batchId) {
            $participantsQuery->where('batch_id', $batchId);
        }

        if ($branchId) {
            $participantsQuery->whereHas('user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }

        $participants = $participantsQuery->get();

        return $participants->map(function ($participant) {
            $attendance = Attendance::where('batch_id', $participant->batch_id)
                ->where('user_id', $participant->user_id)
                ->whereDate('attendance_date', now()->toDateString())
                ->first();

            if ($attendance) {
                return [
                    'id'           => $attendance->id,
                    'type'         => 'attendance',
                    'user_id'      => $participant->user_id,
                    'user_name'    => $participant->user->name,
                    'user_email'   => $participant->user->email,
                    'batch_id'     => $participant->batch_id,
                    'batch_title'  => $participant->batch->title,
                    'batch_code'   => formatBatchCode($participant->batch->id),
                    'branch_name'  => $participant->user->branch->name ?? '-',
                    'checkin_time' => $attendance->checkin_time
                        ? formatTime($attendance->checkin_time)
                        : '-',
                    'status'       => $attendance->status,
                    'notes'        => $attendance->notes,
                ];
            }

            return [
                'id'           => null,
                'type'         => 'no-attendance',
                'user_id'      => $participant->user_id,
                'user_name'    => $participant->user->name,
                'user_email'   => $participant->user->email,
                'batch_id'     => $participant->batch_id,
                'batch_title'  => $participant->batch->title,
                'batch_code'   => formatBatchCode($participant->batch->id),
                'branch_name'  => $participant->user->branch->name ?? '-',
                'checkin_time' => '-',
                'status'       => 'Not Checked-in',
                'notes'        => null,
            ];
        });
    }

    public function approve(Attendance $attendance): RedirectResponse
    {
        $trainer = Auth::user();

        if ($attendance->batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk memvalidasi kehadiran ini');
        }

        if ($attendance->status !== 'Checked-in') {
            return redirect()->back()->with('error', 'Kehadiran ini sudah divalidasi sebelumnya');
        }

        $attendance->update([
            'status' => 'Approved',
            'notes'  => $attendance->notes
                ? $attendance->notes . ' | Divalidasi oleh ' . $trainer->name
                : 'Divalidasi oleh ' . $trainer->name,
        ]);

        return redirect()->back()->with('success', 'Kehadiran berhasil divalidasi');
    }

    public function reject(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'attendance_id' => 'nullable|exists:attendances,id',
            'user_id'       => 'required|exists:users,id',
            'batch_id'      => 'required|exists:batches,id',
            'reason'        => 'nullable|string|max:500',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk batch ini');
        }

        $notes = $validated['reason'] ?? 'Ditandai tidak hadir oleh ' . $trainer->name;

        if ($validated['attendance_id']) {
            $attendance = Attendance::findOrFail($validated['attendance_id']);
            $attendance->update([
                'status' => 'Absent',
                'notes'  => $attendance->notes
                    ? $attendance->notes . ' | ' . $notes
                    : $notes,
            ]);
        } else {
            Attendance::create([
                'batch_id'        => $validated['batch_id'],
                'user_id'         => $validated['user_id'],
                'attendance_date' => now()->toDateString(),
                'checkin_time'    => null,
                'status'          => 'Absent',
                'notes'           => $notes,
            ]);
        }

        return redirect()->back()->with('success', 'Kehadiran ditandai sebagai tidak hadir');
    }

    public function approveAll(Request $request): RedirectResponse
    {
        $trainer = Auth::user();
        $batchId = $request->input('batch_id');

        $query = Attendance::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        })->where('status', 'Checked-in');

        if ($batchId) {
            $query->where('batch_id', $batchId);
        }

        $updatedCount = $query->update([
            'status' => 'Approved',
            'notes'  => DB::raw("CONCAT(COALESCE(notes, ''), ' | Divalidasi massal oleh " . $trainer->name . "')"),
        ]);

        if ($updatedCount === 0) {
            return redirect()->back()->with('info', 'Tidak ada kehadiran yang perlu divalidasi');
        }

        return redirect()->back()->with('success', "Berhasil memvalidasi {$updatedCount} kehadiran");
    }

    public function manualCheckIn(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'user_id'  => 'required|exists:users,id',
            'notes'    => 'nullable|string|max:500',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            abort(403, 'Anda tidak memiliki akses untuk batch ini');
        }

        $isParticipant = $batch->participants()
            ->where('user_id', $validated['user_id'])
            ->wherePivot('status', 'Approved')
            ->exists();

        if (!$isParticipant) {
            return redirect()->back()->with('error', 'User bukan peserta batch ini');
        }

        $existingAttendance = Attendance::where('batch_id', $validated['batch_id'])
            ->where('user_id', $validated['user_id'])
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Kehadiran untuk peserta ini sudah tercatat');
        }

        Attendance::create([
            'batch_id'        => $validated['batch_id'],
            'user_id'         => $validated['user_id'],
            'attendance_date' => now()->toDateString(),
            'checkin_time'    => now()->format('H:i:s'),
            'status'          => 'Approved',
            'notes'           => trim(($validated['notes'] ?? '') . ' | Check-in manual oleh ' . $trainer->name),
        ]);

        return redirect()->back()->with('success', 'Check-in manual berhasil dicatat');
    }
}
<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\BatchParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $batchId  = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        // ========== STATISTICS ==========
        $participantsQuery = BatchParticipant::where('status', 'Approved')
            ->when($batchId,  fn($q) => $q->where('batch_id', $batchId))
            ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)));

        $totalParticipants = (clone $participantsQuery)->distinct('user_id')->count('user_id');

        $baseAttendance = Attendance::query()
            ->when($batchId,  fn($q) => $q->where('batch_id', $batchId))
            ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)));

        // 'Approved' = validated oleh trainer, 'Checked-in' = sudah check-in belum divalidasi
        $validatedCount = (clone $baseAttendance)->where('status', 'Approved')->distinct('user_id')->count('user_id');
        $checkinCount   = (clone $baseAttendance)->where('status', 'Checked-in')->distinct('user_id')->count('user_id');
        $absentCount    = (clone $baseAttendance)->where('status', 'Absent')->distinct('user_id')->count('user_id');

        $attendanceRate = $totalParticipants > 0
            ? round(($validatedCount / $totalParticipants) * 100, 2)
            : 0;

        // ========== ATTENDANCE PER BATCH ==========
        $batchAttendance = Batch::with('category')
            ->when($batchId, fn($q) => $q->where('id', $batchId))
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) use ($branchId) {
                $totalInBatch = BatchParticipant::where('batch_id', $batch->id)
                    ->where('status', 'Approved')
                    ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
                    ->distinct('user_id')
                    ->count('user_id');

                if ($totalInBatch === 0) return null;

                $validated = Attendance::where('batch_id', $batch->id)
                    ->where('status', 'Approved')
                    ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
                    ->distinct('user_id')
                    ->count('user_id');

                $checkin = Attendance::where('batch_id', $batch->id)
                    ->where('status', 'Checked-in')
                    ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
                    ->distinct('user_id')
                    ->count('user_id');

                $rate = round(($validated / $totalInBatch) * 100, 2);

                $rateColor = $rate >= 80
                    ? 'text-[#10AF13] bg-green-100'
                    : ($rate >= 50 ? 'text-[#FF4D00] bg-orange-100' : 'text-[#ff0000] bg-red-100');

                return [
                    'id'               => $batch->id,
                    'title'            => $batch->title,
                    'code'             => formatBatchCode($batch->id, $batch->created_at->year),
                    'category'         => $batch->category->name,
                    'validated'        => $validated,
                    'checkin'          => $checkin,
                    'total_participants' => $totalInBatch,
                    'rate'             => $rate,
                    'rate_color'       => $rateColor,
                ];
            })
            ->filter()
            ->values();

        // ========== DETAIL TABEL ==========
        $attendances = Attendance::with(['user.branch', 'batch.category'])
            ->when($batchId,  fn($q) => $q->where('batch_id', $batchId))
            ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
            ->orderBy('attendance_date', 'desc')
            ->orderBy('checkin_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $attendances->getCollection()->transform(function ($attendance) {
            [$statusLabel, $statusClass] = match ($attendance->status) {
                'Approved'   => ['Validated',  'bg-green-100 text-[#10AF13]'],
                'Checked-in' => ['Check-In',   'bg-orange-100 text-[#FF4D00]'],
                'Absent'     => ['Absent',      'bg-red-100 text-[#ff0000]'],
                default      => [$attendance->status, 'bg-gray-100 text-gray-600'],
            };

            return [
                'id'           => $attendance->id,
                'user_name'    => $attendance->user->name,
                'branch_name'  => $attendance->user->branch->name ?? '-',
                'batch_title'  => $attendance->batch->title,
                'batch_code'   => formatBatchCode($attendance->batch->id, $attendance->batch->created_at->year),
                'status'       => $attendance->status,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'checkin_time' => $attendance->checkin_time
                    ? \Carbon\Carbon::parse($attendance->checkin_time)->format('d M, H:i')
                    : '-',
            ];
        });

        // Peserta belum absen (hanya jika ada filter)
        $notAttendedParticipants = collect();
        if ($batchId || $branchId) {
            $attendedUserIds = Attendance::when($batchId,  fn($q) => $q->where('batch_id', $batchId))
                ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
                ->pluck('user_id')
                ->unique();

            $notAttendedParticipants = BatchParticipant::with(['user.branch', 'batch'])
                ->where('status', 'Approved')
                ->whereNotIn('user_id', $attendedUserIds)
                ->when($batchId,  fn($q) => $q->where('batch_id', $batchId))
                ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
                ->get()
                ->map(fn($p) => [
                    'user_name'    => $p->user->name,
                    'branch_name'  => $p->user->branch->name ?? '-',
                    'batch_title'  => $p->batch->title,
                    'batch_code'   => formatBatchCode($p->batch->id, $p->batch->created_at->year),
                    'status'       => 'Not Attended',
                    'status_label' => 'Belum Absen',
                    'status_class' => 'bg-gray-100 text-gray-600',
                    'checkin_time' => '-',
                ]);
        }

        $batches = Batch::with('category')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'label' => $b->title,
                'code' => formatBatchCode($b->id, $b->created_at->year),
            ]);

        $branches = Branch::orderBy('name')->get();

        return view('coordinator.monitoring-absensi', compact(
            'totalParticipants',
            'validatedCount',
            'checkinCount',
            'absentCount',
            'attendanceRate',
            'batchAttendance',
            'attendances',
            'notAttendedParticipants',
            'batches',
            'branches',
        ));
    }

    public function export(Request $request)
    {
        $batchId  = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        $attendances = Attendance::with(['user.branch', 'batch.category'])
            ->when($batchId,  fn($q) => $q->where('batch_id', $batchId))
            ->when($branchId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('branch_id', $branchId)))
            ->orderBy('attendance_date', 'desc')
            ->get();

        $filename = 'attendance-monitoring-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Nama', 'Cabang', 'Batch', 'Kode Batch', 'Status', 'Tanggal', 'Waktu Check-In']);

            foreach ($attendances as $a) {
                $statusLabel = match ($a->status) {
                    'Approved'   => 'Validated',
                    'Checked-in' => 'Check-In',
                    'Absent'     => 'Absent',
                    default      => '-',
                };

                fputcsv($handle, [
                    $a->user->name,
                    $a->user->branch->name ?? '-',
                    $a->batch->title,
                    formatBatchCode($a->batch->id, $a->batch->created_at->year),
                    $statusLabel,
                    \Carbon\Carbon::parse($a->attendance_date)->format('d/m/Y'),
                    $a->checkin_time
                        ? \Carbon\Carbon::parse($a->checkin_time)->format('H:i')
                        : '-',
                ]);
            }

            fclose($handle);
        }, $filename);
    }
}
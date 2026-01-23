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
    /**
     * Display attendance monitoring page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $batchId = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        // Base query for attendances
        $attendanceQuery = Attendance::with(['user.branch', 'batch.category']);

        // Apply filters
        if ($batchId) {
            $attendanceQuery->where('batch_id', $batchId);
        }

        if ($branchId) {
            $attendanceQuery->whereHas('user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        // ========== STATISTICS ==========
        
        // Get all approved participants based on filters
        $participantsQuery = BatchParticipant::where('status', 'Approved')
            ->when($batchId, function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            });

        $totalParticipants = (clone $participantsQuery)->distinct('user_id')->count('user_id');

        // Get attendance statistics
        $validatedCount = (clone $attendanceQuery)->where('status', 'Present')->distinct('user_id')->count('user_id');
        $checkinCount = (clone $attendanceQuery)->where('status', 'Pending')->distinct('user_id')->count('user_id');
        
        // Calculate absent (total participants - those who have attendance records)
        $attendedUsers = (clone $attendanceQuery)->distinct('user_id')->pluck('user_id');
        $absentCount = $totalParticipants - $attendedUsers->count();

        // Calculate overall attendance rate
        $attendanceRate = $totalParticipants > 0 
            ? round(($validatedCount / $totalParticipants) * 100, 2) 
            : 0;

        // ========== ATTENDANCE PER BATCH ==========
        $batchAttendanceQuery = Batch::with('category')
            ->when($batchId, function($q) use ($batchId) {
                $q->where('id', $batchId);
            })
            ->orderBy('start_date', 'desc');

        $batchAttendance = $batchAttendanceQuery->get()->map(function($batch) use ($branchId) {
            // Get approved participants for this batch
            $batchParticipants = BatchParticipant::where('batch_id', $batch->id)
                ->where('status', 'Approved')
                ->when($branchId, function($q) use ($branchId) {
                    $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
                })
                ->distinct('user_id')
                ->count('user_id');

            // Get validated attendances (Present status)
            $validated = Attendance::where('batch_id', $batch->id)
                ->where('status', 'Present')
                ->when($branchId, function($q) use ($branchId) {
                    $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
                })
                ->distinct('user_id')
                ->count('user_id');

            // Get check-in (pending) attendances
            $checkin = Attendance::where('batch_id', $batch->id)
                ->where('status', 'Pending')
                ->when($branchId, function($q) use ($branchId) {
                    $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
                })
                ->distinct('user_id')
                ->count('user_id');

            // Calculate rate
            $rate = $batchParticipants > 0 
                ? round(($validated / $batchParticipants) * 100, 2) 
                : 0;

            // Determine color based on rate
            $rateColor = 'text-[#ff0000] bg-red-100'; // Default red for 0%
            if ($rate >= 80) {
                $rateColor = 'text-[#10AF13] bg-green-100';
            } elseif ($rate >= 50) {
                $rateColor = 'text-[#FF4D00] bg-orange-100';
            }

            return [
                'id' => $batch->id,
                'title' => $batch->title,
                'code' => formatBatchCode($batch->id, $batch->created_at->year),
                'category' => $batch->category->name,
                'validated' => $validated,
                'checkin' => $checkin,
                'total_participants' => $batchParticipants,
                'rate' => $rate,
                'rate_color' => $rateColor,
            ];
        })->filter(function($batch) {
            return $batch['total_participants'] > 0; // Only show batches with participants
        })->values();

        // ========== ATTENDANCE DETAILS ==========
        $attendances = Attendance::with(['user.branch', 'batch.category'])
            ->when($batchId, function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            })
            ->orderBy('attendance_date', 'desc')
            ->orderBy('checkin_time', 'desc') // FIXED: checkin_time (no underscore)
            ->paginate(15)
            ->withQueryString();

        // Transform attendance data
        $attendances->getCollection()->transform(function($attendance) {
            // Determine status display
            $statusLabel = '';
            $statusClass = '';
            
            switch($attendance->status) {
                case 'Present':
                    $statusLabel = 'Validated';
                    $statusClass = 'bg-green-100 text-[#10AF13]';
                    break;
                case 'Pending':
                    $statusLabel = 'Check-In';
                    $statusClass = 'bg-orange-100 text-[#FF4D00]';
                    break;
                case 'Absent':
                    $statusLabel = 'Absent';
                    $statusClass = 'bg-red-100 text-[#ff0000]';
                    break;
            }

            return [
                'id' => $attendance->id,
                'user_name' => $attendance->user->name,
                'user_nip' => $attendance->user->nip ?? '-',
                'branch_name' => $attendance->user->branch->name ?? '-',
                'batch_title' => $attendance->batch->title,
                'batch_code' => formatBatchCode($attendance->batch->id, $attendance->batch->created_at->year),
                'status' => $attendance->status,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'checkin_time' => $attendance->checkin_time // FIXED: checkin_time
                    ? \Carbon\Carbon::parse($attendance->checkin_time)->format('d M, H:i')
                    : '-',
                'attendance_date' => $attendance->attendance_date,
            ];
        });

        // Get participants who haven't attended yet
        if ($batchId || $branchId) {
            $attendedUserIds = Attendance::when($batchId, function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            })
            ->pluck('user_id')
            ->unique();

            $notAttendedParticipants = BatchParticipant::with(['user.branch', 'batch.category'])
                ->where('status', 'Approved')
                ->whereNotIn('user_id', $attendedUserIds)
                ->when($batchId, function($q) use ($batchId) {
                    $q->where('batch_id', $batchId);
                })
                ->when($branchId, function($q) use ($branchId) {
                    $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
                })
                ->get()
                ->map(function($participant) {
                    return [
                        'id' => null,
                        'user_name' => $participant->user->name,
                        'user_nip' => $participant->user->nip ?? '-',
                        'branch_name' => $participant->user->branch->name ?? '-',
                        'batch_title' => $participant->batch->title,
                        'batch_code' => formatBatchCode($participant->batch->id, $participant->batch->created_at->year),
                        'status' => 'Not Attended',
                        'status_label' => 'Belum Absen',
                        'status_class' => 'bg-gray-200 text-gray-700',
                        'checkin_time' => '-',
                        'attendance_date' => null,
                    ];
                });

            // Merge with existing attendances
            $allAttendanceData = collect($attendances->items())->merge($notAttendedParticipants);
        } else {
            $allAttendanceData = collect($attendances->items());
        }

        // Get filter options
        $batches = Batch::with('category')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'label' => $batch->title,
                    'code' => formatBatchCode($batch->id, $batch->created_at->year),
                ];
            });

        $branches = Branch::orderBy('name')->get();

        return view('coordinator.monitoring-absensi', compact(
            'totalParticipants',
            'validatedCount',
            'checkinCount',
            'absentCount',
            'attendanceRate',
            'batchAttendance',
            'attendances',
            'allAttendanceData',
            'batches',
            'branches',
            'batchId',
            'branchId'
        ));
    }

    /**
     * Export attendance report
     */
    public function export(Request $request)
    {
        $batchId = $request->input('batch_id');
        $branchId = $request->input('branch_id');

        // Get attendance data
        $attendances = Attendance::with(['user.branch', 'batch.category'])
            ->when($batchId, function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('user', fn($query) => $query->where('branch_id', $branchId));
            })
            ->orderBy('attendance_date', 'desc')
            ->get();

        $filename = 'attendance-monitoring-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($attendances) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, [
                'Nama',
                'NIP',
                'Cabang',
                'Batch',
                'Kode Batch',
                'Status Kehadiran',
                'Tanggal',
                'Waktu Check-In'
            ]);
            
            // Data
            foreach ($attendances as $attendance) {
                $statusLabel = match($attendance->status) {
                    'Present' => 'Validated',
                    'Pending' => 'Check-In',
                    'Absent' => 'Absent',
                    default => '-'
                };

                fputcsv($handle, [
                    $attendance->user->name,
                    $attendance->user->nip ?? '-',
                    $attendance->user->branch->name ?? '-',
                    $attendance->batch->title,
                    formatBatchCode($attendance->batch->id, $attendance->batch->created_at->year),
                    $statusLabel,
                    \Carbon\Carbon::parse($attendance->attendance_date)->format('d/m/Y'),
                    $attendance->checkin_time // FIXED: checkin_time
                        ? \Carbon\Carbon::parse($attendance->checkin_time)->format('H:i')
                        : '-',
                ]);
            }
            
            fclose($handle);
        }, $filename);
    }
}
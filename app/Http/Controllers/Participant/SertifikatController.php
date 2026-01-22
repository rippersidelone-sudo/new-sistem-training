<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class SertifikatController extends Controller
{
    /**
     * Display participant's certificates
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Get year filter if provided
        $year = $request->input('year');
        
        // Build certificates query
        $query = $user->certificates()
            ->with(['batch.category', 'batch.trainer'])
            ->orderBy('issued_at', 'desc');

        // Filter by year if specified
        if ($year) {
            $query->whereYear('issued_at', $year);
        }

        $certificates = $query->get();

        // Get available years for filter
        $availableYears = $user->certificates()
            ->selectRaw('YEAR(issued_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Statistics
        $totalCertificates = $user->certificates()->count();
        $currentYear = Carbon::now()->year;
        $thisYearCount = $user->certificates()
            ->whereYear('issued_at', $currentYear)
            ->count();

        // Group certificates by year for display
        $certificatesByYear = $certificates->groupBy(function($certificate) {
            return $certificate->issued_at->format('Y');
        });

        return view('participant.sertifikat', compact(
            'certificates',
            'certificatesByYear',
            'totalCertificates',
            'thisYearCount',
            'availableYears',
            'year'
        ));
    }

    /**
     * Show certificate details
     */
    public function show(Certificate $certificate): View
    {
        $user = Auth::user();

        // Verify ownership
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini');
        }

        $certificate->load(['batch.category', 'batch.trainer']);

        return view('participant.certificate-detail', compact('certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download(Certificate $certificate): RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();

        // Verify ownership
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini');
        }

        // Check if file exists
        if (!$certificate->file_path) {
            return redirect()->back()
                ->with('error', 'File sertifikat belum tersedia');
        }

        if (!Storage::disk('public')->exists($certificate->file_path)) {
            return redirect()->back()
                ->with('error', 'File sertifikat tidak ditemukan');
        }

        // Generate download filename
        $filename = 'Certificate_' . $certificate->batch->title . '_' . $user->name . '.pdf';
        $filename = str_replace(' ', '_', $filename);

        return Storage::disk('public')->download($certificate->file_path, $filename);
    }

    /**
     * Preview certificate (if needed)
     */
    public function preview(Certificate $certificate): View
    {
        $user = Auth::user();

        // Verify ownership
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini');
        }

        $certificate->load(['batch.category', 'batch.trainer']);

        // Generate certificate data for preview
        $certificateData = [
            'participant_name' => $user->name,
            'batch_title' => $certificate->batch->title,
            'category_name' => $certificate->batch->category->name,
            'trainer_name' => $certificate->batch->trainer->name,
            'issued_date' => $certificate->issued_at->format('d F Y'),
            'completion_date' => $certificate->batch->end_date->format('d F Y'),
            'certificate_number' => $this->generateCertificateNumber($certificate),
        ];

        return view('participant.certificate-preview', compact('certificate', 'certificateData'));
    }

    /**
     * Generate certificate number
     */
    private function generateCertificateNumber(Certificate $certificate): string
    {
        // Format: CERT-{BATCH_CODE}-{USER_ID}-{YEAR}{MONTH}
        $batchCode = strtoupper(substr($certificate->batch->title, 0, 3));
        $userId = str_pad($certificate->user_id, 4, '0', STR_PAD_LEFT);
        $dateCode = $certificate->issued_at->format('Ym');
        
        return "CERT-{$batchCode}-{$userId}-{$dateCode}";
    }

    /**
     * Check certificate eligibility for a batch
     */
    public function checkEligibility(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $batchId = $request->input('batch_id');

        if (!$batchId) {
            return response()->json([
                'eligible' => false,
                'message' => 'Batch ID required'
            ], 400);
        }

        // Check if batch is completed
        $batch = $user->participatingBatches()
            ->where('batches.id', $batchId)
            ->wherePivot('status', 'Approved')
            ->first();

        if (!$batch) {
            return response()->json([
                'eligible' => false,
                'message' => 'Anda tidak terdaftar di batch ini'
            ]);
        }

        if ($batch->status !== 'Completed') {
            return response()->json([
                'eligible' => false,
                'message' => 'Batch belum selesai'
            ]);
        }

        // Check if certificate already issued
        $existingCertificate = Certificate::where('batch_id', $batchId)
            ->where('user_id', $user->id)
            ->exists();

        if ($existingCertificate) {
            return response()->json([
                'eligible' => true,
                'has_certificate' => true,
                'message' => 'Sertifikat sudah diterbitkan'
            ]);
        }

        // Check attendance rate (minimum 80%)
        $totalSessions = $batch->attendances()
            ->distinct('attendance_date')
            ->count();

        $userAttendances = $user->attendances()
            ->where('batch_id', $batchId)
            ->where('status', 'Present')
            ->count();

        $attendanceRate = $totalSessions > 0 
            ? ($userAttendances / $totalSessions) * 100 
            : 0;

        if ($attendanceRate < 80) {
            return response()->json([
                'eligible' => false,
                'message' => "Kehadiran kurang dari 80% ({$attendanceRate}%)"
            ]);
        }

        // Check all tasks completed
        $totalTasks = $batch->tasks()->count();
        $completedTasks = $user->taskSubmissions()
            ->whereHas('task', function($query) use ($batchId) {
                $query->where('batch_id', $batchId);
            })
            ->where('status', 'Accepted')
            ->count();

        if ($totalTasks > 0 && $completedTasks < $totalTasks) {
            return response()->json([
                'eligible' => false,
                'message' => "Belum menyelesaikan semua tugas ({$completedTasks}/{$totalTasks})"
            ]);
        }

        return response()->json([
            'eligible' => true,
            'has_certificate' => false,
            'message' => 'Memenuhi syarat untuk mendapatkan sertifikat'
        ]);
    }
}
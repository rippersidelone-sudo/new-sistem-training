<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Helpers\RoleHelper;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if (RoleHelper::isHQAdmin($user)) {
            return redirect()->route('admin.dashboard');
        }
        if (RoleHelper::isCoordinator($user)) {
            return redirect()->route('coordinator.dashboard');
        }
        if (RoleHelper::isTrainer($user)) {
            return redirect()->route('trainer.dashboard');
        }
        if (RoleHelper::isBranchCoordinator($user)) {
            return redirect()->route('branch_pic.dashboard');
        }
        if (RoleHelper::isParticipant($user)) {
            return redirect()->route('participant.dashboard');
        }
    }
    
    return redirect()->route('login');
})->name('home');

// ============================================================================
// GUEST ROUTES (Auth)
// ============================================================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ============================================================================
// PROTECTED ROUTES (Requires Authentication)
// ============================================================================

Route::middleware(['auth'])->group(function () {
    
    // ============================================================================
    // EMAIL VERIFICATION ROUTES (untuk menghilangkan error)
    // ============================================================================
    Route::post('/email/verification-notification', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.edit');
        }
        
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
    
    // ============================================================================
    // PROFILE / SETTINGS
    // ============================================================================
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
    
    Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])->name('settings');
    
    // ============================================================================
    // HQ ADMIN ROUTES
    // ============================================================================
    Route::middleware(['role:HQ Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::get('/users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        
        // Role Management
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        
        // Branch Management
        Route::resource('branches', App\Http\Controllers\Admin\BranchController::class);
        
        // Batch Oversight
        Route::get('/batch-oversight/export', [App\Http\Controllers\Admin\BatchOversightController::class, 'export'])->name('batch-oversight.export');
        Route::get('/batch-oversight', [App\Http\Controllers\Admin\BatchOversightController::class, 'index'])->name('batch-oversight.index');
        Route::get('/batch-oversight/{batch}', [App\Http\Controllers\Admin\BatchOversightController::class, 'show'])->name('batch-oversight.show');
        
        // Reports & Analytics
        Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        
        // Audit Log
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');
        
        // Settings
        Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])->name('settings');
    });

    // ============================================================================
    // TRAINING COORDINATOR ROUTES
    // ============================================================================
    Route::middleware(['role:Training Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Coordinator\DashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', App\Http\Controllers\Coordinator\CategoryController::class);

        // Batch Management
        Route::resource('batches', App\Http\Controllers\Coordinator\BatchController::class);
        Route::get('/batches/{batch}/monitoring', [App\Http\Controllers\Coordinator\BatchController::class, 'monitoring'])->name('batches.monitoring');

        // Participant Approval
        Route::resource('participants', App\Http\Controllers\Coordinator\ParticipantController::class)->only(['index', 'show']);
        Route::post('/participants/{participant}/approve', [App\Http\Controllers\Coordinator\ParticipantController::class, 'approve'])->name('participants.approve');
        Route::post('/participants/{participant}/reject', [App\Http\Controllers\Coordinator\ParticipantController::class, 'reject'])->name('participants.reject');
        Route::post('/participants/bulk-approve', [App\Http\Controllers\Coordinator\ParticipantController::class, 'bulkApprove'])->name('participants.bulk-approve');

        // Monitoring Attendance
        Route::get('/monitoring/attendance', [App\Http\Controllers\Coordinator\AttendanceController::class, 'index'])->name('monitoring.attendance');
        Route::get('/monitoring/attendance/export', [App\Http\Controllers\Coordinator\AttendanceController::class, 'export'])->name('monitoring.attendance.export');

        // Reports
        Route::get('/reports', [App\Http\Controllers\Coordinator\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Coordinator\ReportController::class, 'export'])->name('reports.export');

        // Settings
        Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])->name('settings');
    });

    // ============================================================================
    // SYNC ROUTES (HQ Admin, Coordinator, Branch Coordinator)
    // ============================================================================
    Route::middleware(['role:HQ Admin,Training Coordinator,Branch Coordinator'])
        ->prefix('sync')
        ->name('sync.')
        ->group(function () {
            Route::post('/all',          [App\Http\Controllers\SyncController::class, 'syncAll'])->name('all');
            Route::post('/branches',     [App\Http\Controllers\SyncController::class, 'syncBranches'])->name('branches');
            Route::post('/participants', [App\Http\Controllers\SyncController::class, 'syncParticipants'])->name('participants');
            Route::post('/categories',   [App\Http\Controllers\SyncController::class, 'syncCategories'])->name('categories');
            Route::post('/test',         [App\Http\Controllers\SyncController::class, 'testConnection'])->name('test');
        });

    // ============================================================================
    // TRAINER ROUTES   
    // ============================================================================
    Route::middleware(['role:Trainer'])
        ->prefix('trainer')
        ->name('trainer.')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Trainer\TrainerDashboardController::class, 'index'])
            ->name('dashboard');

        // MY BATCHES
        Route::get('/batches', [App\Http\Controllers\Trainer\TrainerBatchController::class, 'index'])
            ->name('batches');
        Route::get('/batches/{batch}', [App\Http\Controllers\Trainer\TrainerBatchController::class, 'show'])
            ->name('batches.show');

        // ATTENDANCE APPROVAL (Approval Kehadiran)
        Route::get('/approval-kehadiran', [App\Http\Controllers\Trainer\TrainerAttendanceController::class, 'index'])
            ->name('approval-kehadiran');
        Route::post('/attendance/{attendance}/approve', [App\Http\Controllers\Trainer\TrainerAttendanceController::class, 'approve'])
            ->name('attendance.approve');
        Route::post('/attendance/reject', [App\Http\Controllers\Trainer\TrainerAttendanceController::class, 'reject'])
            ->name('attendance.reject');
        Route::post('/attendance/approve-all', [App\Http\Controllers\Trainer\TrainerAttendanceController::class, 'approveAll'])
            ->name('attendance.approve-all');
        Route::post('/attendance/manual-checkin', [App\Http\Controllers\Trainer\TrainerAttendanceController::class, 'manualCheckIn'])
            ->name('attendance.manual-checkin');

        // KELOLA TUGAS (Task CRUD - NEW)
        Route::get('/kelola-tugas', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'kelolaTugas'])
            ->name('kelola-tugas');
        Route::post('/tasks', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'store'])
            ->name('tasks.store');
        Route::get('/tasks/{task}/edit', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'edit'])
            ->name('tasks.edit');
        Route::put('/tasks/{task}', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'update'])
            ->name('tasks.update');
        Route::delete('/tasks/{task}', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'destroy'])
            ->name('tasks.destroy');

        // TASK SUBMISSIONS (Penilaian Tugas)
        Route::get('/penilaian-tugas', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'index'])
            ->name('penilaian-tugas');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'show'])
            ->name('submissions.show');
        Route::post('/submissions/{submission}/accept', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'accept'])
            ->name('submissions.accept');
        Route::post('/submissions/{submission}/reject', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'reject'])
            ->name('submissions.reject');
        Route::get('/submissions/{submission}/download', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'download'])
            ->name('submissions.download');
        Route::post('/submissions/bulk-accept', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'bulkAccept'])
            ->name('submissions.bulk-accept');
        Route::post('/submissions/{submission}/feedback', [App\Http\Controllers\Trainer\TrainerTaskController::class, 'updateFeedback'])
            ->name('submissions.update-feedback');

        // MATERIALS (Upload Materi)
        Route::get('/upload-materi', [App\Http\Controllers\Trainer\TrainerMaterialController::class, 'index'])
            ->name('upload-materi');
        Route::post('/materials', [App\Http\Controllers\Trainer\TrainerMaterialController::class, 'store'])
            ->name('materials.store');
        Route::delete('/materials/{material}', [App\Http\Controllers\Trainer\TrainerMaterialController::class, 'destroy'])
            ->name('materials.destroy');

        // SETTINGS (Profile)
        Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])
            ->name('settings');
    });

    // ============================================================================
    // BRANCH COORDINATOR ROUTES
    // ============================================================================
    Route::middleware(['role:Branch Coordinator', 'branch.access'])
        ->prefix('branch-pic')->name('branch_pic.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\BranchPic\DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Participants Management
        Route::get('/participants', [App\Http\Controllers\BranchPic\ParticipantController::class, 'index'])
            ->name('participants.index');
        Route::get('/participants/{participant}', [App\Http\Controllers\BranchPic\ParticipantController::class, 'show'])
            ->name('participants.show');
        
        // Validation / Approval
        Route::get('/validation', [App\Http\Controllers\BranchPic\ValidationController::class, 'index'])
            ->name('validation.index');
        Route::get('/validation/{participant}', [App\Http\Controllers\BranchPic\ValidationController::class, 'show'])
            ->name('validation.show');
        Route::post('/validation/{participant}/approve', [App\Http\Controllers\BranchPic\ValidationController::class, 'approve'])
            ->name('validation.approve');
        Route::post('/validation/{participant}/reject', [App\Http\Controllers\BranchPic\ValidationController::class, 'reject'])
            ->name('validation.reject');
        Route::post('/validation/bulk-approve', [App\Http\Controllers\BranchPic\ValidationController::class, 'bulkApprove'])
            ->name('validation.bulk-approve');
        
        // Reports & Analytics - UPDATED CONTROLLER NAME
        Route::get('/reports', [App\Http\Controllers\BranchPic\BranchReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\BranchPic\BranchReportController::class, 'export'])
            ->name('reports.export');
        Route::get('/reports/chart-data', [App\Http\Controllers\BranchPic\BranchReportController::class, 'getChartData'])
            ->name('reports.chart-data');
        
        // Settings (Profile)
        Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])
            ->name('settings');
    });

    // ============================================================================
    // PARTICIPANT ROUTES
    // ============================================================================
    Route::middleware(['role:Participant'])
        ->prefix('participant')
        ->name('participant.')
        ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [App\Http\Controllers\Participant\DashboardController::class, 'index'])
            ->name('dashboard');

        // PENDAFTARAN (Browse & Register to Batches)
        Route::get('/pendaftaran', [App\Http\Controllers\Participant\BatchController::class, 'index'])
            ->name('pendaftaran');
        Route::get('/pendaftaran/{batch}', [App\Http\Controllers\Participant\BatchController::class, 'show'])
            ->name('pendaftaran.show');
        Route::post('/pendaftaran/{batch}/register', [App\Http\Controllers\Participant\BatchController::class, 'register'])
            ->name('pendaftaran.register');

        // PELATIHAN (My Batches)
        Route::get('/pelatihan', [App\Http\Controllers\Participant\MyBatchController::class, 'index'])
            ->name('pelatihan');
        Route::get('/pelatihan/{batch}', [App\Http\Controllers\Participant\MyBatchController::class, 'show'])
            ->name('pelatihan.show');

        // ABSENSI (Attendance)
        Route::get('/absensi', [App\Http\Controllers\Participant\AttendanceController::class, 'index'])
            ->name('absensi');
        Route::post('/absensi/{batch}/checkin', [App\Http\Controllers\Participant\AttendanceController::class, 'checkin'])
            ->name('absensi.checkin');
        Route::get('/absensi/history', [App\Http\Controllers\Participant\AttendanceController::class, 'history'])
            ->name('absensi.history');

        // TUGAS (Tasks & Submissions)
        Route::get('/tugas', [App\Http\Controllers\Participant\TaskController::class, 'index'])
            ->name('tugas');
        Route::get('/tugas/{task}', [App\Http\Controllers\Participant\TaskController::class, 'show'])
            ->name('tugas.show');
        Route::post('/tugas/{task}/submit', [App\Http\Controllers\Participant\TaskController::class, 'submit'])
            ->name('tugas.submit');
        Route::get('/tugas/submissions/history', [App\Http\Controllers\Participant\TaskController::class, 'submissions'])
            ->name('tugas.submissions');
        Route::get('/tugas/submissions/{submission}/download', [App\Http\Controllers\Participant\TaskController::class, 'download'])
            ->name('tugas.download');

        // SERTIFIKAT (Certificates)
        Route::get('/sertifikat', [App\Http\Controllers\Participant\SertifikatController::class, 'index'])
            ->name('sertifikat');
        Route::get('/sertifikat/{certificate}', [App\Http\Controllers\Participant\SertifikatController::class, 'show'])
            ->name('sertifikat.show');
        Route::get('/sertifikat/{certificate}/download', [App\Http\Controllers\Participant\SertifikatController::class, 'download'])
            ->name('sertifikat.download');
        Route::get('/sertifikat/{certificate}/preview', [App\Http\Controllers\Participant\SertifikatController::class, 'preview'])
            ->name('sertifikat.preview');
        Route::post('/sertifikat/check-eligibility', [App\Http\Controllers\Participant\SertifikatController::class, 'checkEligibility'])
            ->name('sertifikat.check-eligibility');

        // SETTINGS (Profile)
        Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])
            ->name('settings');
    });
});

// ============================================================================
// FALLBACK ROUTE
// ============================================================================

Route::fallback(function () {
    abort(404);
});
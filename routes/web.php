<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Helpers\RoleHelper;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    // Jika sudah login, redirect ke dashboard sesuai role
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
            return redirect()->route('branch.dashboard');
        }
        if (RoleHelper::isParticipant($user)) {
            return redirect()->route('participant.dashboard');
        }
    }
    
    // Jika belum login, redirect ke halaman login
    return redirect()->route('login');
})->name('home');

// ============================================================================
// GUEST ROUTES (Auth)
// ============================================================================

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    // Register
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Logout (requires authentication)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ============================================================================
// PROTECTED ROUTES (Requires Authentication)
// ============================================================================

Route::middleware(['auth'])->group(function () {
    
    // --------------------------------------------------------------------
    // PROFILE / SETTINGS (Available for all authenticated users)
    // --------------------------------------------------------------------
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Password update
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
    
    // Alias route for settings (same as profile)
    Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'edit'])->name('settings');
    
    // --------------------------------------------------------------------
    // HQ ADMIN ROUTES
    // --------------------------------------------------------------------
    Route::middleware(['role:HQ Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // User Management (role-permission page)
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        
        // Role Management
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        
        // Branch Management
        Route::resource('branches', App\Http\Controllers\Admin\BranchController::class);
        
        // Batch Oversight
        Route::get('/batch-oversight', [App\Http\Controllers\Admin\BatchOversightController::class, 'index'])->name('batch-oversight.index');
        Route::get('/batch-oversight/{batch}', [App\Http\Controllers\Admin\BatchOversightController::class, 'show'])->name('batch-oversight.show');
        Route::get('/batch-oversight/export', [App\Http\Controllers\Admin\BatchOversightController::class, 'export'])->name('batch-oversight.export');
        
        // Global Reports & Analytics
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
        
        // Audit Log
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');
    });

    // --------------------------------------------------------------------
    // TRAINING COORDINATOR ROUTES
    // --------------------------------------------------------------------
    Route::middleware(['role:Training Coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Coordinator\DashboardController::class, 'index'])->name('dashboard');
        
        // Category Management
        Route::resource('categories', App\Http\Controllers\Coordinator\CategoryController::class);
        
        // Batch Management
        Route::resource('batches', App\Http\Controllers\Coordinator\BatchController::class);
        
        // Participant Approval
        Route::get('/participants', [App\Http\Controllers\Coordinator\ParticipantController::class, 'index'])->name('participants.index');
        Route::post('/participants/{participant}/approve', [App\Http\Controllers\Coordinator\ParticipantController::class, 'approve'])->name('participants.approve');
        Route::post('/participants/{participant}/reject', [App\Http\Controllers\Coordinator\ParticipantController::class, 'reject'])->name('participants.reject');
        
        // Batch Monitoring
        Route::get('/batches/{batch}/monitoring', [App\Http\Controllers\Coordinator\BatchController::class, 'monitoring'])->name('batches.monitoring');
        
        // Reports
        Route::get('/reports', [App\Http\Controllers\Coordinator\ReportController::class, 'index'])->name('reports.index');
    });

    // --------------------------------------------------------------------
    // TRAINER ROUTES
    // --------------------------------------------------------------------
    Route::middleware(['role:Trainer'])->prefix('trainer')->name('trainer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Trainer\DashboardController::class, 'index'])->name('dashboard');
        
        // My Batches
        Route::get('/batches', [App\Http\Controllers\Trainer\BatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/{batch}', [App\Http\Controllers\Trainer\BatchController::class, 'show'])->name('batches.show');
        
        // Attendance Management
        Route::get('/batches/{batch}/attendances', [App\Http\Controllers\Trainer\AttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/attendances/{attendance}/approve', [App\Http\Controllers\Trainer\AttendanceController::class, 'approve'])->name('attendances.approve');
        Route::post('/attendances/{attendance}/reject', [App\Http\Controllers\Trainer\AttendanceController::class, 'reject'])->name('attendances.reject');
        
        // Task & Submission Management
        Route::resource('batches.tasks', App\Http\Controllers\Trainer\TaskController::class)->shallow();
        Route::get('/submissions', [App\Http\Controllers\Trainer\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Trainer\SubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{submission}/review', [App\Http\Controllers\Trainer\SubmissionController::class, 'review'])->name('submissions.review');
        
        // Materials Upload
        Route::post('/batches/{batch}/materials', [App\Http\Controllers\Trainer\MaterialController::class, 'store'])->name('materials.store');
        Route::delete('/materials/{material}', [App\Http\Controllers\Trainer\MaterialController::class, 'destroy'])->name('materials.destroy');
        
        // Participant Feedback View
        Route::get('/batches/{batch}/feedback', [App\Http\Controllers\Trainer\FeedbackController::class, 'index'])->name('feedback.index');
    });

    // --------------------------------------------------------------------
    // BRANCH COORDINATOR ROUTES
    // --------------------------------------------------------------------
    Route::middleware(['role:Branch Coordinator', 'branch.access'])->prefix('branch')->name('branch.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Branch\DashboardController::class, 'index'])->name('dashboard');
        
        // Branch Participants
        Route::get('/participants', [App\Http\Controllers\Branch\ParticipantController::class, 'index'])->name('participants.index');
        Route::get('/participants/{user}', [App\Http\Controllers\Branch\ParticipantController::class, 'show'])->name('participants.show');
        
        // Branch Reports
        Route::get('/reports', [App\Http\Controllers\Branch\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Branch\ReportController::class, 'export'])->name('reports.export');
        
        // Monitoring Progress
        Route::get('/monitoring', [App\Http\Controllers\Branch\MonitoringController::class, 'index'])->name('monitoring.index');
    });

    // --------------------------------------------------------------------
    // PARTICIPANT ROUTES
    // --------------------------------------------------------------------
    Route::middleware(['role:Participant'])->prefix('participant')->name('participant.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Participant\DashboardController::class, 'index'])->name('dashboard');
        
        // Browse & Register to Batches
        Route::get('/batches', [App\Http\Controllers\Participant\BatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/{batch}', [App\Http\Controllers\Participant\BatchController::class, 'show'])->name('batches.show');
        Route::post('/batches/{batch}/register', [App\Http\Controllers\Participant\BatchController::class, 'register'])->name('batches.register');
        
        // My Registrations
        Route::get('/my-batches', [App\Http\Controllers\Participant\MyBatchController::class, 'index'])->name('my-batches.index');
        Route::get('/my-batches/{batch}', [App\Http\Controllers\Participant\MyBatchController::class, 'show'])->name('my-batches.show');
        
        // Attendance (Check-in)
        Route::post('/batches/{batch}/checkin', [App\Http\Controllers\Participant\AttendanceController::class, 'checkin'])->name('attendance.checkin');
        
        // Task Submissions
        Route::get('/batches/{batch}/tasks', [App\Http\Controllers\Participant\TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks/{task}/submit', [App\Http\Controllers\Participant\TaskController::class, 'submit'])->name('tasks.submit');
        Route::get('/submissions', [App\Http\Controllers\Participant\TaskController::class, 'submissions'])->name('submissions.index');
        
        // Feedback
        Route::get('/batches/{batch}/feedback/create', [App\Http\Controllers\Participant\FeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/batches/{batch}/feedback', [App\Http\Controllers\Participant\FeedbackController::class, 'store'])->name('feedback.store');
        
        // Certificates
        Route::get('/certificates', [App\Http\Controllers\Participant\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}/download', [App\Http\Controllers\Participant\CertificateController::class, 'download'])->name('certificates.download');
        
        // Training History
        Route::get('/history', [App\Http\Controllers\Participant\HistoryController::class, 'index'])->name('history.index');
    });
});

// ============================================================================
// FALLBACK ROUTE
// ============================================================================

Route::fallback(function () {
    abort(404);
});
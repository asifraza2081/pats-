<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ResultController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/downloads', [HomeController::class, 'downloads'])->name('downloads');
Route::get('/procurement', [HomeController::class, 'procurement'])->name('procurement');
Route::get('/csr', [HomeController::class, 'csr'])->name('csr');
Route::get('/instructions', [HomeController::class, 'instructions'])->name('instructions');


Route::get('/projects', [HomeController::class, 'projects'])->name('projects');
Route::get('/projects/{project}', [HomeController::class, 'project'])->name('projects.show');
Route::get('/projects/{project}/jobs/{job}', [HomeController::class, 'job'])->name('jobs.show');

// Public result search
Route::get('/results', [ResultController::class, 'search'])->name('results.search');
Route::post('/results', [ResultController::class, 'search'])->name('results.search.post');
Route::get('/results/verify/{roll}', [ResultController::class, 'verify'])->name('results.verify');
Route::get('/safe-download', [App\Http\Controllers\PublicDownloadController::class, 'download'])
    ->name('public.download.signed')
    ->middleware('signed');

// ═══════════════════════════════════════════════════
// AUTH ROUTES (guests only)
// ═══════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/register',           [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register',          [AuthController::class, 'register'])->middleware('throttle:3,1');
    Route::get('/login',              [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',             [AuthController::class, 'login'])->middleware('throttle:5,1')->name('auth.login.post');
    Route::get('/forgot-password',    [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
    Route::post('/forgot-password',   [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',    [AuthController::class, 'resetPassword'])->middleware('throttle:3,1')->name('auth.reset-password');
});

// OTP verification (Deactivated in Phase 26)
// Route::get('/verify-otp',    [AuthController::class, 'showOtp'])->name('auth.otp');
// Route::post('/verify-otp',   [AuthController::class, 'verifyOtp'])->middleware('throttle:5,1');
// Route::post('/resend-otp',   [AuthController::class, 'resendOtp'])->middleware('throttle:3,1')->name('auth.otp.resend');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// ═══════════════════════════════════════════════════
// CANDIDATE ROUTES
// ═══════════════════════════════════════════════════
Route::middleware(['auth', 'role:candidate', \App\Http\Middleware\InactivityLogout::class])->prefix('candidate')->name('candidate.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',      [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/bio',  [ProfileController::class, 'viewProfile'])->name('profile.bio');
    Route::put('/profile',      [ProfileController::class, 'update'])->name('profile.update');

    // Education History
    Route::post('/education',           [ProfileController::class, 'addEducation'])->name('education.store');
    Route::put('/education/{edu}',      [ProfileController::class, 'updateEducation'])->name('education.update');
    Route::delete('/education/{edu}',   [ProfileController::class, 'deleteEducation'])->name('education.destroy');

    // Work Experience
    Route::post('/experience',          [ProfileController::class, 'addExperience'])->name('experience.store');
    Route::put('/experience/{exp}',     [ProfileController::class, 'updateExperience'])->name('experience.update');
    Route::delete('/experience/{exp}',  [ProfileController::class, 'deleteExperience'])->name('experience.destroy');

    // Applications
    Route::get('/apply/{job}',              [ApplicationController::class, 'create'])->name('apply');
    Route::post('/apply/{job}',             [ApplicationController::class, 'store'])->name('apply.store');
    Route::get('/applications',             [ApplicationController::class, 'index'])->name('applications');
    Route::get('/applications/{app}',       [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{app}/challan',  [ApplicationController::class, 'challan'])->name('challan')->middleware('signed');
    Route::get('/applications/{app}/slip',     [ApplicationController::class, 'slip'])->name('slip')->middleware('signed');
    Route::get('/applications/{app}/result',   [ApplicationController::class, 'result'])->name('result');
});

// ═══════════════════════════════════════════════════
// ADMIN ROUTES
// ═══════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin|data_entry|super_admin', \App\Http\Middleware\InactivityLogout::class])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Projects & Jobs
    Route::resource('projects', Admin\ProjectController::class);
    Route::get('projects/{project}/documents', [Admin\ProjectController::class, 'documents'])->name('projects.documents');
    Route::get('projects/{project}/centers', [Admin\ProjectCenterController::class, 'index'])->name('projects.centers.index');
    Route::post('projects/{project}/centers', [Admin\ProjectCenterController::class, 'sync'])->name('projects.centers.sync');
    Route::resource('projects.jobs', Admin\JobController::class)->shallow();

    // Test Centers & Batches
    Route::resource('cities', Admin\CityController::class);
    Route::resource('centers', Admin\TestCenterController::class);
    Route::get('batches/stats', [Admin\BatchController::class, 'stats'])->name('batches.stats');
    Route::get('batches/centers-json/{project}', [Admin\BatchController::class, 'centersForProject'])->name('batches.centers-json');
    Route::resource('batches', Admin\BatchController::class);
    Route::post('batches/{batch}/ready', [Admin\BatchController::class, 'markReady'])->name('batches.ready');
    Route::get('batches/{batch}/summary', [Admin\BatchController::class, 'summary'])->name('batches.summary');
    Route::get('batches/{batch}/attendance', [Admin\BatchController::class, 'attendance'])->name('batches.attendance');
    Route::get('batches/{batch}/attendance-sheet', [Admin\BatchController::class, 'attendanceSheet'])->name('batches.attendance-sheet');
    Route::get('batches/{batch}/answer-sheets', [Admin\BatchController::class, 'answerSheets'])->name('batches.answer-sheets');
    Route::get('batches/{batch}/bulk-slips', [Admin\BatchController::class, 'bulkSlips'])->name('batches.bulk-slips');
    Route::post('batches/{batch}/scans', [Admin\BatchController::class, 'uploadScan'])->name('batches.scans.upload');
    Route::post('batches/{batch}/mark-attendance', [Admin\BatchController::class, 'markAttendance'])->name('batches.attendance.mark');
    Route::post('batches/{batch}/toggle-results', [Admin\BatchController::class, 'toggleResults'])->name('batches.toggle-results');

    // Applications
    Route::get('applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{app}', [Admin\ApplicationController::class, 'show'])->name('applications.show');

    // Candidates Management
    Route::get('candidates', [Admin\CandidateController::class, 'index'])->name('candidates.index');
    Route::get('candidates/{candidate}', [Admin\CandidateController::class, 'show'])->name('candidates.show');

    // Payments
    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');

    // Roll Numbers
    Route::get('roll-numbers', [Admin\RollNumberController::class, 'index'])->name('rollnumbers.index');
    Route::get('applications/{app}/slip', [Admin\RollNumberController::class, 'slip'])->name('rollnumbers.slip');

    // Results
    Route::get('results', [Admin\ResultController::class, 'index'])->name('results.index');
    Route::get('results/upload', [Admin\ResultController::class, 'showUpload'])->name('results.upload');
    Route::post('results/upload', [Admin\ResultController::class, 'upload'])->name('results.upload.post');
    Route::post('results/publish/{project}', [Admin\ResultController::class, 'publish'])->name('results.publish');
    Route::get('results/{app}', [Admin\ResultController::class, 'show'])->name('results.show');

    // Notifications API
    Route::get('notifications/unread', [Admin\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/mark-read', [Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Attendance Sheets
    Route::get('attendance', [Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/print/{batch}', [Admin\AttendanceController::class, 'print'])->name('attendance.print');

    // User Management (super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
        Route::get('activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{log}', [Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
});

    // Examiner Portal
    Route::middleware(['auth', 'role:examiner', \App\Http\Middleware\InactivityLogout::class])->prefix('examiner')->name('examiner.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Examiner\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/sessions/{batch}', [App\Http\Controllers\Examiner\DashboardController::class, 'showSession'])->name('sessions.show');
        
        // Dedicated Examiner Printing Routes (Fix for 403 errors)
        Route::get('/sessions/{batch}/summary', [App\Http\Controllers\Admin\BatchController::class, 'summary'])
            ->middleware('can:view-session,batch')
            ->name('sessions.summary');
            
        Route::get('/sessions/{batch}/attendance-sheet', [App\Http\Controllers\Admin\BatchController::class, 'attendanceSheet'])
            ->middleware('can:view-session,batch')
            ->name('sessions.attendance-sheet');
            
        Route::get('/sessions/{batch}/answer-sheets', [App\Http\Controllers\Admin\BatchController::class, 'answerSheets'])
            ->middleware('can:view-session,batch')
            ->name('sessions.answer-sheets');
            
        Route::get('/sessions/{batch}/bulk-slips', [App\Http\Controllers\Admin\BatchController::class, 'bulkSlips'])
            ->middleware('can:view-session,batch')
            ->name('sessions.bulk-slips');

        // Attendance Management for Examiners
        Route::get('/sessions/{batch}/attendance', [App\Http\Controllers\Admin\BatchController::class, 'attendance'])
            ->middleware('can:view-session,batch')
            ->name('sessions.attendance');
            
        Route::post('/sessions/{batch}/mark-attendance', [App\Http\Controllers\Admin\BatchController::class, 'markAttendance'])
            ->middleware('can:view-session,batch')
            ->name('sessions.mark-attendance');
            
        Route::post('/sessions/{batch}/upload-scan', [App\Http\Controllers\Admin\BatchController::class, 'uploadScan'])
            ->middleware('can:view-session,batch')
            ->name('sessions.upload-scan');
    });

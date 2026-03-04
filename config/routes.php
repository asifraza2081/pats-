<?php

declare(strict_types=1);

/**
 * ============================================================
 * PATS Route Definitions
 * $router is available via Router::load() — add routes here.
 * ============================================================
 */

use App\Controllers\AuthController;
use App\Controllers\CandidateController;
use App\Controllers\ProjectController;
use App\Controllers\JobController;
use App\Controllers\ApplicationController;
use App\Controllers\ChallanController;
use App\Controllers\RollNumberController;
use App\Controllers\ResultController;

use App\Controllers\Admin\DashboardController    as AdminDashboard;
use App\Controllers\Admin\ProjectController      as AdminProject;
use App\Controllers\Admin\JobController          as AdminJob;
use App\Controllers\Admin\TestCenterController   as AdminTestCenter;
use App\Controllers\Admin\ApplicationController  as AdminApplication;
use App\Controllers\Admin\ChallanController      as AdminChallan;
use App\Controllers\Admin\ResultController       as AdminResult;
use App\Controllers\Admin\SmsController          as AdminSms;
use App\Controllers\Admin\ReportController       as AdminReport;

// ── Public / Guest Routes ─────────────────────────────────

$router->get('/',                   [ProjectController::class, 'home']);
$router->get('/projects',           [ProjectController::class, 'index']);
$router->get('/projects/{id}',      [ProjectController::class, 'show']);
$router->get('/result',             [ResultController::class, 'search']);
$router->post('/result',            [ResultController::class, 'search']);

// ── Auth Routes ───────────────────────────────────────────

$router->get('/register',           [AuthController::class, 'showRegister']);
$router->post('/register',          [AuthController::class, 'register']);
$router->get('/login',              [AuthController::class, 'showLogin']);
$router->post('/login',             [AuthController::class, 'login']);
$router->get('/logout',             [AuthController::class, 'logout']);
$router->get('/forgot-password',    [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password',   [AuthController::class, 'forgotPassword']);
$router->post('/verify-otp',        [AuthController::class, 'verifyOtp']);

// ── Candidate Portal Routes ────────────────────────────────

$router->get('/dashboard',          [CandidateController::class, 'dashboard']);
$router->get('/profile',            [CandidateController::class, 'showProfile']);
$router->post('/profile',           [CandidateController::class, 'updateProfile']);

$router->get('/apply/{jobId}',      [ApplicationController::class, 'create']);
$router->post('/apply',             [ApplicationController::class, 'store']);
$router->get('/applications',       [ApplicationController::class, 'index']);
$router->get('/applications/{id}/edit',  [ApplicationController::class, 'edit']);
$router->post('/applications/{id}/edit', [ApplicationController::class, 'update']);

$router->get('/challan/{id}',       [ChallanController::class, 'print']);
$router->get('/slip/{id}',          [RollNumberController::class, 'slip']);

// AJAX endpoints
$router->get('/api/centers/{projectId}',  [ApplicationController::class, 'centersForProject']);
$router->get('/api/slots/{centerId}',     [ApplicationController::class, 'slotsForCenter']);

// ── Admin Routes ───────────────────────────────────────────

$router->get('/admin',                       [AdminDashboard::class, 'index']);
$router->get('/admin/dashboard',             [AdminDashboard::class, 'index']);

// Projects
$router->get('/admin/projects',              [AdminProject::class, 'index']);
$router->get('/admin/projects/create',       [AdminProject::class, 'create']);
$router->post('/admin/projects',             [AdminProject::class, 'store']);
$router->get('/admin/projects/{id}/edit',    [AdminProject::class, 'edit']);
$router->post('/admin/projects/{id}',        [AdminProject::class, 'update']);
$router->post('/admin/projects/{id}/delete', [AdminProject::class, 'delete']);

// Jobs
$router->get('/admin/jobs',                  [AdminJob::class, 'index']);
$router->get('/admin/jobs/create',           [AdminJob::class, 'create']);
$router->post('/admin/jobs',                 [AdminJob::class, 'store']);
$router->get('/admin/jobs/{id}/edit',        [AdminJob::class, 'edit']);
$router->post('/admin/jobs/{id}',            [AdminJob::class, 'update']);
$router->post('/admin/jobs/{id}/delete',     [AdminJob::class, 'delete']);

// Test Centers
$router->get('/admin/centers',               [AdminTestCenter::class, 'index']);
$router->get('/admin/centers/create',        [AdminTestCenter::class, 'create']);
$router->post('/admin/centers',              [AdminTestCenter::class, 'store']);
$router->get('/admin/centers/{id}/edit',     [AdminTestCenter::class, 'edit']);
$router->post('/admin/centers/{id}',         [AdminTestCenter::class, 'update']);
$router->get('/admin/centers/{id}/slots',    [AdminTestCenter::class, 'slots']);
$router->post('/admin/centers/{id}/slots',   [AdminTestCenter::class, 'storeSlot']);
$router->post('/admin/slots/{id}/delete',    [AdminTestCenter::class, 'deleteSlot']);

// Applications
$router->get('/admin/applications',                      [AdminApplication::class, 'index']);
$router->post('/admin/applications/{id}/approve',        [AdminApplication::class, 'approve']);
$router->post('/admin/applications/{id}/reject',         [AdminApplication::class, 'reject']);

// Challans
$router->get('/admin/challans',                          [AdminChallan::class, 'index']);
$router->post('/admin/challans/{id}/verify',             [AdminChallan::class, 'verify']);

// Results
$router->get('/admin/results',                           [AdminResult::class, 'index']);
$router->get('/admin/results/upload',                    [AdminResult::class, 'uploadForm']);
$router->post('/admin/results/upload',                   [AdminResult::class, 'upload']);
$router->post('/admin/results/commit',                   [AdminResult::class, 'commit']);
$router->get('/admin/results/{id}/edit',                 [AdminResult::class, 'edit']);
$router->post('/admin/results/{id}',                     [AdminResult::class, 'update']);

// SMS
$router->get('/admin/sms',                               [AdminSms::class, 'index']);
$router->post('/admin/sms/send',                         [AdminSms::class, 'send']);

// Reports
$router->get('/admin/reports',                           [AdminReport::class, 'index']);
$router->post('/admin/reports/export',                   [AdminReport::class, 'export']);

<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Application;

class ApplicationController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();
        
        // Simple filter by job ID if provided
        $jobId = $request->get('job_id');
        $where = $jobId ? "WHERE a.job_id = " . (int)$jobId : "";

        $applications = $db->fetchAll(
            "SELECT a.*, 
                    u.name as candidate_name, u.cnic,
                    j.title as job_title, j.bps_grade,
                    p.name as project_name,
                    tc.name as center_name, tc.city,
                    cs.slot_date, cs.slot_time,
                    pay.status as payment_status
             FROM applications a
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             JOIN center_slots cs ON cs.id = a.slot_id
             JOIN test_centers tc ON tc.id = cs.center_id
             LEFT JOIN payments pay ON pay.application_id = a.id
             $where
             ORDER BY a.applied_at DESC
             LIMIT 500"
        );

        $jobs = $db->fetchAll('SELECT id, title, project_id FROM jobs ORDER BY id DESC');

        View::render('admin/applications/index', [
            'pageTitle'    => 'Manage Applications',
            'applications' => $applications,
            'jobs'         => $jobs,
            'jobFilter'    => $jobId
        ], 'admin');
    }

    public function approve(Request $request, array $params = []): void
    {
        CSRF::check();
        $appId = (int) $params['id'];
        
        $app = Application::find($appId);
        if (!$app) Response::abort(404);
        
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            Application::updateWhere(['status' => 'scheduled'], ['id' => $appId]);
            
            // Generate Roll Number using standardised format
            $rollNo = \App\Models\RollNumber::generate($appId);
            
            $db->commit();
            
            // Try to send SMS
            $fullApp = Application::getDetails($appId);
            if ($fullApp && $fullApp['phone']) {
                $sms = new \App\Services\SmsService();
                $msg = "PATS: You are scheduled. Roll No: {$rollNo}. Download your admit card from the portal.";
                $sms->send($fullApp['phone'], $msg, 'roll_no_assigned');
            }
            
            Session::flash('success', "Scheduled. Roll No. {$rollNo} assigned.");
        } catch (\Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Error assigning roll number: ' . $e->getMessage());
        }

        Response::redirect('/admin/applications');
    }

    public function reject(Request $request, array $params = []): void
    {
        CSRF::check();
        $appId = (int) $params['id'];
        
        $db = Database::getInstance();
        $app = Application::find($appId);
        if ($app) {
            // Free up the booked seat
            $db->query('UPDATE center_slots SET booked_seats = booked_seats - 1 WHERE id = ? AND booked_seats > 0', [$app['slot_id']]);
            Application::deleteWhere(['id' => $appId]);
        }
        
        Session::flash('success', 'Application rejected and seat freed.');
        Response::redirect('/admin/applications');
    }
}

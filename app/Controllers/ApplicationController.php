<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Project;
use App\Models\TestCenter;
use App\Models\CenterSlot;
use App\Models\Application;
use App\Models\Payment;

class ApplicationController
{
    public function __construct()
    {
        Auth::requireAuth();
        Auth::requireRole('candidate');
    }

    public function index(Request $request, array $params = []): void
    {
        $profile = Candidate::getFullProfile(Auth::id());
        
        $db = Database::getInstance();
        $applications = $db->fetchAll(
            'SELECT a.*, j.title as job_title, j.fee, p.name as project_name, p.close_date,
                    tc.city, cs.slot_date, cs.slot_time, pay.status as payment_status
             FROM applications a
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             JOIN center_slots cs ON cs.id = a.slot_id
             JOIN test_centers tc ON tc.id = cs.center_id
             LEFT JOIN payments pay ON pay.application_id = a.id
             WHERE a.candidate_id = ?
             ORDER BY a.applied_at DESC',
            [$profile['id']]
        );

        View::render('candidate/applications', [
            'pageTitle'    => 'My Applications',
            'applications' => $applications
        ]);
    }

    public function create(Request $request, array $params = []): void
    {
        $jobId = (int) $params['jobId'];
        $job = Job::getWithProject($jobId);
        
        if (!$job || $job['project_status'] !== 'open') {
            Session::flash('error', 'This job is no longer accepting applications.');
            Response::redirect('/projects');
        }

        $profile = Candidate::getFullProfile(Auth::id());
        $completion = Candidate::completionPercent($profile);

        if ($completion < 100) {
            Session::flash('error', 'Please complete your profile (100%) before applying.');
            Response::redirect('/profile');
        }

        if (Application::hasApplied((int)$profile['id'], $jobId)) {
            Session::flash('error', 'You have already applied for this post.');
            Response::redirect('/applications');
        }

        // Fetch centers assigned to this project
        $centers = TestCenter::getForProject((int)$job['project_id']);

        View::render('candidate/apply', [
            'pageTitle' => 'Apply for ' . $job['title'],
            'job'       => $job,
            'centers'   => $centers
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        CSRF::check();

        $jobId    = (int) $request->post('job_id');
        $slotId   = (int) $request->post('slot_id');
        $centerId = (int) $request->post('center_id'); // used for validation
        
        $job = Job::getWithProject($jobId);
        if (!$job || $job['project_status'] !== 'open') Response::abort(404);

        $profile = Candidate::getFullProfile(Auth::id());
        $candidateId = (int)$profile['id'];

        if (Application::hasApplied($candidateId, $jobId)) {
            Session::flash('error', 'Already applied.');
            Response::redirect('/applications');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Check slot capacity and lock row for update
            $slot = $db->fetchOne('SELECT * FROM center_slots WHERE id = ? FOR UPDATE', [$slotId]);
            
            if (!$slot || $slot['booked_seats'] >= $slot['total_seats']) {
                throw new \Exception('Sorry, the selected time slot is now full. Please select another slot.');
            }

            // Create Application
            $appId = Application::create([
                'candidate_id'  => $candidateId,
                'job_id'        => $jobId,
                'slot_id'       => $slotId,
                'status'        => 'submitted'
            ]);

            // Generate unique Challan Ref (e.g. PATS-PROJ-JOB-APP)
            $challanRef = sprintf('PATS-%03d-%04d-%06d', $job['project_id'], $jobId, $appId);
            Application::updateWhere(['challan_ref' => $challanRef], ['id' => $appId]);

            // Create Payment Log
            Payment::create([
                'application_id' => $appId,
                'amount'         => $job['fee'],
                'method'         => 'challan',
                'status'         => 'pending'
            ]);

            // Increment booked seats
            $db->query('UPDATE center_slots SET booked_seats = booked_seats + 1 WHERE id = ?', [$slotId]);

            // Lock candidate profile if not locked
            if (!$profile['profile_locked']) {
                Candidate::updateWhere(['profile_locked' => 1], ['id' => $candidateId]);
            }

            $db->commit();
            Session::flash('success', 'Application submitted! Please download and pay the fee challan.');
            Response::redirect('/applications');

        } catch (\Exception $e) {
            $db->rollBack();
            Session::flash('error', $e->getMessage());
            Response::redirect("/apply/{$jobId}");
        }
    }

    // ── AJAX Endpoints ────────────────────────────────────────

    public function centersForProject(Request $request, array $params = []): void
    {
        $projectId = (int) $params['projectId'];
        $centers = TestCenter::getForProject($projectId);
        Response::json($centers);
    }

    public function slotsForCenter(Request $request, array $params = []): void
    {
        $centerId = (int) $params['centerId'];
        $projectId = (int) $request->get('project_id'); // passed as query param

        $slots = CenterSlot::getAvailable($projectId, $centerId);
        Response::json($slots);
    }
}

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
                    tc.city, tc.name as center_name, cs.slot_date, cs.slot_time,
                    pay.status as payment_status
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

    /**
     * Show eligibility & profile summary page before the center/slot form.
     */
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
            Session::flash('error', 'Please complete your profile (100%) before applying. Required: Photo, CNIC copy, DOB, Gender, Domicile, Province, Address.');
            Response::redirect('/profile');
        }

        if (Application::hasApplied((int)$profile['id'], $jobId)) {
            Session::flash('error', 'You have already applied for this post.');
            Response::redirect('/applications');
        }

        // Build eligibility summary — check against job requirements
        $eligibilityIssues = $this->checkEligibility($profile, $job);

        // Fetch centers assigned to this project
        $centers = TestCenter::getForProject((int)$job['project_id']);

        View::render('candidate/apply', [
            'pageTitle'         => 'Apply for ' . $job['title'],
            'job'               => $job,
            'profile'           => $profile,
            'centers'           => $centers,
            'eligibilityIssues' => $eligibilityIssues,
        ]);
    }

    /**
     * Submit the application with automatic slot overflow handling.
     */
    public function store(Request $request, array $params = []): void
    {
        CSRF::check();

        $jobId    = (int) $request->post('job_id');
        $slotId   = (int) $request->post('slot_id');
        $centerId = (int) $request->post('center_id');
        
        $job = Job::getWithProject($jobId);
        if (!$job || $job['project_status'] !== 'open') Response::abort(404);

        $profile     = Candidate::getFullProfile(Auth::id());
        $candidateId = (int)$profile['id'];

        if (Application::hasApplied($candidateId, $jobId)) {
            Session::flash('error', 'You have already applied for this post.');
            Response::redirect('/applications');
        }

        // Re-check eligibility server-side
        $eligibilityIssues = $this->checkEligibility($profile, $job);
        if (!empty($eligibilityIssues)) {
            Session::flash('error', 'You do not meet the eligibility criteria: ' . implode('. ', $eligibilityIssues));
            Response::redirect("/apply/{$jobId}");
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Lock the chosen slot for update
            $slot = $db->fetchOne('SELECT * FROM center_slots WHERE id = ? FOR UPDATE', [$slotId]);
            
            // If selected slot is full, find the NEXT available slot at the same center
            $autoAssigned = false;
            if (!$slot || $slot['booked_seats'] >= $slot['total_seats']) {
                $slot = $db->fetchOne(
                    'SELECT * FROM center_slots
                     WHERE center_id = ? AND project_id = ?
                       AND booked_seats < total_seats
                       AND slot_date >= CURDATE()
                     ORDER BY slot_date ASC, slot_time ASC
                     LIMIT 1 FOR UPDATE',
                    [$centerId, $job['project_id']]
                );
                if (!$slot) {
                    throw new \Exception('All slots at this center are now full. Please try a different test center.');
                }
                $autoAssigned = true;
                $slotId = (int)$slot['id'];
            }

            // Create Application
            $appId = Application::create([
                'candidate_id' => $candidateId,
                'job_id'       => $jobId,
                'slot_id'      => $slotId,
                'status'       => 'submitted'
            ]);

            // Generate unique Challan Ref
            $challanRef = sprintf('PATS-%03d-%04d-%06d', $job['project_id'], $jobId, $appId);
            Application::updateWhere(['challan_ref' => $challanRef], ['id' => $appId]);

            // Create Payment Log
            Payment::create([
                'application_id' => $appId,
                'amount'         => $job['fee'],
                'method'         => 'challan',
                'status'         => 'pending'
            ]);

            // Increment booked seats on the (possibly reassigned) slot
            $db->query('UPDATE center_slots SET booked_seats = booked_seats + 1 WHERE id = ?', [$slotId]);

            // Lock candidate profile if not locked
            if (!$profile['profile_locked']) {
                Candidate::updateWhere(['profile_locked' => 1], ['id' => $candidateId]);
            }

            $db->commit();

            if ($autoAssigned) {
                $slotInfo = date('D, d M Y', strtotime($slot['slot_date'])) . ' at ' . date('h:i A', strtotime($slot['slot_time']));
                Session::flash('warning', "Your original slot was full. You have been automatically assigned to the next available slot: {$slotInfo}.");
            }

            Session::flash('success', 'Application submitted successfully! Please download and pay your fee challan.');
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
        $centerId  = (int) $params['centerId'];
        $projectId = (int) $request->get('project_id');

        $slots = CenterSlot::getAvailable($projectId, $centerId);
        Response::json($slots);
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Check candidate profile against job requirements.
     * Returns an array of human-readable issue strings (empty = eligible).
     */
    private function checkEligibility(array $profile, array $job): array
    {
        $issues = [];

        // Age check
        if (!empty($profile['dob']) && (!empty($job['age_min']) || !empty($job['age_max']))) {
            $age = (int) date_diff(date_create($profile['dob']), date_create('today'))->y;
            if (!empty($job['age_min']) && $age < (int)$job['age_min']) {
                $issues[] = "Minimum age is {$job['age_min']} years (you are {$age})";
            }
            if (!empty($job['age_max']) && $age > (int)$job['age_max']) {
                $issues[] = "Maximum age is {$job['age_max']} years (you are {$age})";
            }
        }

        // Domicile check
        if (!empty($job['domicile_required'])) {
            $required = strtolower(trim($job['domicile_required']));
            $candidateDomicile = strtolower(trim($profile['province'] ?? ''));
            $candidateDistrict = strtolower(trim($profile['domicile'] ?? ''));
            if ($required !== $candidateDomicile && $required !== $candidateDistrict) {
                $issues[] = "Domicile must be from {$job['domicile_required']}";
            }
        }

        return $issues;
    }
}

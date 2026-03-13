<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\PatsJob;
use App\Models\City;
use App\Models\Payment;
use App\Services\EligibilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function __construct(
        private EligibilityService $eligibility,
    ) {}

    public function index()
    {
        $candidate    = Auth::user()->candidate;
        $applications = Application::where('candidate_id', $candidate->id)
            ->with(['job.project', 'batch.center', 'payment', 'examRollno', 'result'])
            ->latest('applied_at')->paginate(10);
        return view('candidate.applications', compact('applications'));
    }

    public function create(PatsJob $job)
    {
        $project   = $job->project;
        $candidate = Auth::user()->candidate;

        // Guard: project open
        abort_if(!$project->isRegistrationOpen(), 403, 'Applications for this project are closed.');

        // Guard: already applied
        $existing = Application::where('candidate_id', $candidate->id)->where('job_id', $job->id)->first();
        if ($existing) {
            return redirect()->route('candidate.applications.show', $existing)->with('info', 'You have already applied for this position.');
        }

        // Guard: profile complete
        if ($candidate->completionPercent() < 100) {
            return redirect()->route('candidate.profile.show')->with('error', 'Please complete your profile (100%) before applying.');
        }

        // Run eligibility check
        $eligibilityResult = $this->eligibility->check($candidate, $job);
        $cities = City::orderBy('name')->get();

        return view('candidate.apply', compact('job', 'project', 'candidate', 'eligibilityResult', 'cities'));
    }

    public function store(Request $request, PatsJob $job)
    {
        $project   = $job->project;
        $candidate = Auth::user()->candidate;

        abort_if(!$project->isRegistrationOpen(), 403);

        // EXTRA GUARD: Atomic check for existing application to prevent race conditions during concurrent POST
        $existing = Application::where('candidate_id', $candidate->id)
            ->where('job_id', $job->id)
            ->first();
            
        if ($existing) {
            return redirect()->route('candidate.applications')->with('info', 'You have already applied for this position.');
        }

        $data = $request->validate([
            'desired_test_city_id' => 'required|exists:cities,id',
            'age_relaxation_type'  => 'nullable|string|max:80',
            'age_relaxation_years' => 'nullable|integer|min:1|max:10',
        ]);

        // Re-run eligibility
        $eligResult = $this->eligibility->check($candidate, $job);

        DB::transaction(function () use ($candidate, $job, $data, $eligResult) {
            $application = Application::create([
                'candidate_id'          => $candidate->id,
                'job_id'                => $job->id,
                'project_id'            => $job->project_id,
                'desired_test_city_id'  => $data['desired_test_city_id'],
                'age_relaxation_type'   => $data['age_relaxation_type'] ?? null,
                'age_relaxation_years'  => $data['age_relaxation_years'] ?? null,
                'status'                => 'submitted',
                'eligibility_warnings'  => $eligResult['warnings'],
                'applied_at'            => now(),
            ]);

            Payment::create([
                'application_id' => $application->id,
                'challan_ref'    => Payment::generateRef(),
                'amount'         => $job->fee,
                'status'         => 'pending',
            ]);

            // Lock profile
            $candidate->update(['profile_locked' => true]);
        });

        return redirect()->route('candidate.applications')->with('success', 'Application submitted! Download your fee challan to proceed.');
    }

    public function show(Application $app)
    {
        abort_if($app->candidate_id !== Auth::user()->candidate->id, 403);
        $app->load(['job.project', 'batch.center', 'payment', 'examRollno', 'result']);
        return view('candidate.application-show', compact('app'));
    }

    /** Download fee challan PDF */
    public function challan(Application $app)
    {
        abort_if($app->candidate_id !== Auth::user()->candidate->id, 403);
        abort_if(!$app->payment, 404, 'No payment record found.');
        
        // 3-day grace period for payment after project closing
        $closeDate = $app->job->project->close_date;
        if ($closeDate && now()->isAfter($closeDate->copy()->addDays(3))) {
            abort(403, 'The payment window for this project (including grace period) has closed.');
        }

        $app->load(['job.project', 'batch.center', 'payment']);
        $pdf = Pdf::loadView('pdf.challan', compact('app'));
        return $pdf->download("Challan_{$app->payment->challan_ref}.pdf");
    }

    /** Download roll number slip PDF */
    public function slip(Application $app)
    {
        abort_if($app->candidate_id !== Auth::user()->candidate->id, 403);
        $app->load(['job.project', 'examRollno.testCenter', 'examRollno.city']);
        $examRollno = $app->examRollno;

        abort_if(!$examRollno || !$examRollno->roll_no, 403, 'Roll number slip is not yet generated.');
        abort_if(!$examRollno->slip_ready, 403, 'Roll number slip is not yet available for download. Please check back later.');
        abort_if($examRollno->test_date && now()->isAfter(\Carbon\Carbon::parse($examRollno->test_date->format('Y-m-d') . ' ' . $examRollno->start_time)), 403, 'Slip download is disabled after the test has started.');

        $candidate = Auth::user()->candidate;
        $pdf = Pdf::loadView('pdf.slip', compact('app', 'candidate'));
        return $pdf->download("Slip_{$examRollno->roll_no}.pdf");
    }

    /** View result card */
    public function result(Application $app)
    {
        abort_if($app->candidate_id !== Auth::user()->candidate->id, 403);
        $result = $app->result;
        abort_if(!$result || !$result->isPublished(), 404, 'Results are not yet published.');
        $app->load(['job.project', 'examRollno.center']);
        return view('candidate.result', compact('app', 'result'));
    }
}

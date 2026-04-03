<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatsJob as Job;
use App\Models\Project;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Get all active jobs (from projects that are open)
        $jobs = Job::whereHas('project', function($query) {
            $query->where('status', 'open');
        })
        ->with(['project'])
        ->orderBy('project_id')
        ->orderBy('title')
        ->get();

        $selectedJobId = $request->get('job_id');
        $selectedJob = null;
        $groupedCandidates = collect();
        $analytics = [
            'total_applied' => 0,
            'paid_eligible' => 0,
            'unpaid_ineligible' => 0
        ];

        if ($selectedJobId) {
            if ($selectedJobId === 'all') {
                $selectedJob = (object)[
                    'id' => 'all',
                    'title' => 'All Active Jobs',
                    'project' => (object)['name' => 'Consolidated View']
                ];
                
                $applications = \App\Models\Application::with(['candidate.user', 'desiredTestCity', 'payment', 'job'])
                    ->whereIn('job_id', $jobs->pluck('id'))
                    ->get();
            } else {
                $selectedJob = Job::with(['project'])->findOrFail($selectedJobId);
                
                // Fetch all applications for this job with relevant relations
                $applications = \App\Models\Application::with(['candidate.user', 'desiredTestCity', 'payment', 'job'])
                    ->where('job_id', $selectedJobId)
                    ->get();
            }

            $analytics['total_applied'] = $applications->count();
            
            // Organize candidates into:
            // Group By City -> Payment Status (Paid / Unpaid) -> Candidates List
            foreach ($applications as $app) {
                $city = $app->desiredTestCity ? $app->desiredTestCity->name : 'Unassigned City';
                
                $isPaid = ($app->job->fee <= 0) || ($app->payment && $app->payment->status === PaymentStatus::PAID);
                $paymentStatus = $isPaid ? 'Paid & Eligible' : 'Pending Payment';

                if (!isset($groupedCandidates[$city])) {
                    $groupedCandidates[$city] = collect(['Paid & Eligible' => collect(), 'Pending Payment' => collect()]);
                }
                
                $groupedCandidates[$city][$paymentStatus]->push($app);

                if ($paymentStatus === 'Paid & Eligible') {
                    $analytics['paid_eligible']++;
                } else {
                    $analytics['unpaid_ineligible']++;
                }
            }
        }

        return view('admin.analytics.index', compact('jobs', 'selectedJob', 'groupedCandidates', 'analytics'));
    }
}

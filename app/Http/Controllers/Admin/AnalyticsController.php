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
            'unpaid_ineligible' => 0,
            'gender' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            'age_groups' => ['18-25' => 0, '26-30' => 0, '31-40' => 0, '41+' => 0],
            'geography' => [],
            'domicile' => [],
            'allocated' => 0,
        ];

        if ($selectedJobId) {
            $baseQuery = \App\Models\Application::with(['candidate.user', 'desiredTestCity', 'payment', 'job', 'examRollno']);

            if ($selectedJobId === 'all') {
                $selectedJob = (object)[
                    'id' => 'all',
                    'title' => 'All Active Jobs',
                    'project' => (object)['name' => 'Consolidated View']
                ];
                
                $applications = $baseQuery->whereIn('job_id', $jobs->pluck('id'))->get();
            } else {
                $selectedJob = Job::with(['project'])->findOrFail($selectedJobId);
                $applications = $baseQuery->where('job_id', $selectedJobId)->get();
            }

            $analytics['total_applied'] = $applications->count();
            
            foreach ($applications as $app) {
                $city = $app->desiredTestCity ? $app->desiredTestCity->name : 'Unassigned City';
                $candidate = $app->candidate;
                
                // 1. Payment Status
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

                // 2. Allocation Status
                if ($app->examRollno) {
                    $analytics['allocated']++;
                }

                // 3. Gender
                $gender = $candidate->gender ?? 'Other';
                $analytics['gender'][$gender] = ($analytics['gender'][$gender] ?? 0) + 1;

                // 4. Age Groups
                if ($candidate->dob) {
                    $age = \Carbon\Carbon::parse($candidate->dob)->age;
                    if ($age <= 25) $analytics['age_groups']['18-25']++;
                    elseif ($age <= 30) $analytics['age_groups']['26-30']++;
                    elseif ($age <= 40) $analytics['age_groups']['31-40']++;
                    else $analytics['age_groups']['41+']++;
                }

                // 5. Geography (Test City)
                $analytics['geography'][$city] = ($analytics['geography'][$city] ?? 0) + 1;

                // 6. Domicile (Province)
                $province = $candidate->province_of_domicile ?? 'Unknown';
                $analytics['domicile'][$province] = ($analytics['domicile'][$province] ?? 0) + 1;
            }
        }

        $analytics['paidPct'] = $analytics['total_applied'] > 0 ? round(($analytics['paid_eligible'] / $analytics['total_applied']) * 100) : 0;
        $analytics['unpaidPct'] = $analytics['total_applied'] > 0 ? round(($analytics['unpaid_ineligible'] / $analytics['total_applied']) * 100) : 0;
        $analytics['allocPct'] = $analytics['paid_eligible'] > 0 ? round(($analytics['allocated'] / $analytics['paid_eligible']) * 100) : 0;

        return view('admin.analytics.index', compact('jobs', 'selectedJob', 'groupedCandidates', 'analytics'));
    }
}

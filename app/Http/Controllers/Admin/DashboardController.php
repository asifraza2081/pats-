<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\PatsJob;
use App\Models\Payment;
use App\Models\Project;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\FinancialLedger;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = cache()->remember('admin_dashboard_stats', now()->addMinutes(5), function() {
            return [
                'projects'     => Project::count(),
                'jobs'          => PatsJob::count(),
                'applications'  => Application::count(),
                'pending_pay'   => Payment::where('status', PaymentStatus::UNPAID)->count(),
                'verified_pay'  => Payment::where('status', PaymentStatus::PAID)->count(),
                'appeared'      => Application::where('status', ApplicationStatus::APPEARED)->count(),
            ];
        });

        $dashboardCards = [
            ['label'=>'Active Projects',   'value'=>$stats['projects'],    'icon'=>'briefcase',       'color'=>'primary'],
            ['label'=>'Total Job Posts',   'value'=>$stats['jobs'],        'icon'=>'list-check',      'color'=>'info'],
            ['label'=>'Total Applications','value'=>$stats['applications'], 'icon'=>'file-text',       'color'=>'dark'],
            ['label'=>'Pending Payments',  'value'=>$stats['pending_pay'], 'icon'=>'clock-hour-4',    'color'=>'warning'],
            ['label'=>'Verified Payments', 'value'=>$stats['verified_pay'], 'icon'=>'cash',            'color'=>'success'],
            ['label'=>'Appeared in Test',  'value'=>$stats['appeared'],    'icon'=>'user-check',      'color'=>'secondary'],
        ];

        $recentApps = Application::with(['candidate.user', 'job.project', 'payment'])
            ->latest('applied_at')->take(10)->get();

        $openProjects = Project::where('status', ProjectStatus::OPEN)->latest()->take(5)->get();

        // 🟢 PATH 2: Tactical Analytics Data
        // 1. Geographic Distribution (City-wise)
        $cityDistribution = cache()->remember('dashboard_city_distribution', now()->addMinutes(15), function() use ($stats) {
            return Application::select('desired_test_city_id', DB::raw('count(*) as count'))
                ->groupBy('desired_test_city_id')
                ->with('desiredTestCity')
                ->get()
                ->map(function($item) use ($stats) {
                    return [
                        'city' => $item->desiredTestCity->name ?? 'Unknown',
                        'count' => $item->count,
                        'pct' => $stats['applications'] > 0 ? round(($item->count / $stats['applications']) * 100) : 0,
                    ];
                })
                ->sortByDesc('count')
                ->values();
        });

        // 2. Regional (Provincial) Distribution for Heatmap
        $regionalStats = cache()->remember('dashboard_regional_stats', now()->addMinutes(15), function() {
            $provinceMap = [
                'Punjab' => 'PK-PB',
                'Sindh' => 'PK-SD',
                'Khyber Pakhtunkhwa' => 'PK-KP',
                'Balochistan' => 'PK-BA',
                'Islamabad Capital Territory' => 'PK-IS',
                'Gilgit-Baltistan' => 'PK-GB',
                'Azad Jammu & Kashmir' => 'PK-JK',
            ];

            $allApps = Application::join('candidates', 'applications.candidate_id', '=', 'candidates.id')
                ->join('cities', 'candidates.domicile_city_id', '=', 'cities.id')
                ->select('cities.province', DB::raw('count(*) as count'))
                ->groupBy('cities.province')
                ->get();

            $paidApps = Application::whereNotIn('status', [
                    ApplicationStatus::SUBMITTED, 
                    ApplicationStatus::REJECTED, 
                    ApplicationStatus::NOT_SHORTLISTED
                ])
                ->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
                ->join('cities', 'candidates.domicile_city_id', '=', 'cities.id')
                ->select('cities.province', DB::raw('count(*) as count'))
                ->groupBy('cities.province')
                ->get();

            $stats = ['total' => [], 'verified_paid' => []];
            foreach ($provinceMap as $name => $code) {
                $stats['total'][$code] = $allApps->firstWhere('province', $name)?->count ?? 0;
                $stats['verified_paid'][$code] = $paidApps->firstWhere('province', $name)?->count ?? 0;
            }

            // Mapping for IIOJK and enclaves as per official 2020 map
            // Note: IIOJK (Disputed) has 0 applicants tracked in local DB
            $stats['total']['PK-II'] = 0;
            $stats['verified_paid']['PK-II']  = 0;

            // Mocking Junagadh/Manavadar for visual demonstration if no data exists
            $stats['total']['PK-JD'] = 0;
            $stats['total']['PK-MN'] = 0;
            $stats['verified_paid']['PK-JD'] = 0;
            $stats['verified_paid']['PK-MN'] = 0;

            return $stats;
        });

        // 3. Project ROI (Revenue vs Expenses)
        $projectRoi = cache()->remember('dashboard_project_roi', now()->addMinutes(15), function() {
            return Project::whereIn('status', [ProjectStatus::OPEN->value])
                ->get()
                ->map(function($project) {
                    $revenue = FinancialLedger::revenue()->forProject($project->id)->sum('net_amount');
                    $expense = FinancialLedger::expense()->forProject($project->id)->sum('net_amount');
                    
                    return [
                        'name'    => $project->name,
                        'revenue' => (float)$revenue,
                        'expense' => (float)$expense,
                        'net'     => (float)($revenue - $expense),
                    ];
                });
        });

        $totalRev = $projectRoi->sum('revenue');
        $totalExp = $projectRoi->sum('expense');
        $margin = $totalRev > 0 ? (($totalRev - $totalExp) / $totalRev) * 100 : 0;

        $statusColors = [
            'submitted'=>'secondary',
            'fee_paid'=>'primary',
            'appeared'=>'success',
            'absent'=>'danger',
        ];

        return view('admin.dashboard', compact(
            'stats', 'dashboardCards', 'recentApps', 'openProjects', 
            'cityDistribution', 'projectRoi', 'regionalStats',
            'totalRev', 'totalExp', 'margin', 'statusColors'
        ));
    }
}

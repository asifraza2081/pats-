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
use Illuminate\Support\Facades\DB;

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

        $recentApps = Application::with(['candidate.user', 'job.project', 'payment'])
            ->latest('applied_at')->take(10)->get();

        $openProjects = Project::where('status', ProjectStatus::OPEN)->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentApps', 'openProjects'));
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\PatsJob;

class HomeController extends Controller
{
    public function index()
    {
        $projects = \Illuminate\Support\Facades\Cache::remember('home_projects', now()->addHours(2), function () {
            return Project::where('status', 'open')->latest()->take(6)->get();
        });
        
        $results = \Illuminate\Support\Facades\Cache::remember('home_results', now()->addHours(2), function () {
            return Project::where('status', 'closed')->latest()->take(6)->get();
        });

        // Dynamic Stats
        $stats = \Illuminate\Support\Facades\Cache::remember('home_stats', now()->addMinutes(30), function () {
            return [
                'active_projects' => Project::where('status', 'open')->count(),
                'job_posts'       => PatsJob::whereHas('project', function($q){ $q->where('status', 'open'); })->count(),
                'applications'    => \App\Models\Application::count(),
                'results'         => Project::where('status', 'closed')->count(),
            ];
        });

        // Persistent Announcements (Database Driven)
        $announcements = \Illuminate\Support\Facades\Cache::remember('home_announcements', now()->addHours(2), function () use ($projects, $results) {
            $anns = \App\Models\Announcement::active()->get();
            
            if ($anns->isEmpty()) {
                // Fallback for simulation
                foreach($projects->take(2) as $p) {
                    $anns->push((object)[
                        'text' => "Registration for {$p->org_name} ({$p->name}) is now OPEN.",
                        'type' => 'new'
                    ]);
                }
                foreach($results->take(2) as $r) {
                    $anns->push((object)[
                        'text' => "Official Results for {$r->name} have been declared.",
                        'type' => 'result'
                    ]);
                }
            }
            return $anns;
        });

        return view('welcome', compact('projects', 'results', 'stats', 'announcements'));
    }

    public function about() { return view('public.about'); }
    public function contact() { return view('public.contact'); }
    public function downloads() { return view('public.downloads'); }
    public function procurement() { return view('public.procurement'); }
    public function csr() { return view('public.csr'); }
    public function instructions() { return view('public.instructions'); }

    public function projects()
    {
        $projects = Project::whereIn('status', ['open', 'closed'])->latest()->paginate(12);
        return view('public.projects', compact('projects'));
    }

    public function project(Project $project)
    {
        $jobs = $project->jobs()->get();
        return view('public.project', compact('project', 'jobs'));
    }

    public function job(Project $project, PatsJob $job)
    {
        abort_if($job->project_id !== $project->id, 404);
        return view('public.job', compact('project', 'job'));
    }
}

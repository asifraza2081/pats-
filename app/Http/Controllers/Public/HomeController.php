<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\PatsJob;

class HomeController extends Controller
{
    public function index()
    {
        $projects = Project::where('status', 'open')->latest()->take(6)->get();
        $results = Project::where('status', 'closed')->latest()->take(6)->get();

        // Dynamic Stats
        $stats = [
            'active_projects' => Project::where('status', 'open')->count(),
            'job_posts'       => PatsJob::whereHas('project', function($q){ $q->where('status', 'open'); })->count(),
            'applications'    => \App\Models\Application::count(),
            'results'         => Project::where('status', 'closed')->count(),
        ];

        // Dynamic Announcements (Simulated from latest events)
        $announcements = collect();
        
        // Latest open projects
        foreach($projects->take(2) as $p) {
            $announcements->push((object)[
                'text' => "Registration for {$p->org_name} ({$p->name}) is now OPEN.",
                'type' => 'new'
            ]);
        }

        // Latest results
        foreach($results->take(2) as $r) {
            $announcements->push((object)[
                'text' => "Official Results for {$r->name} have been declared.",
                'type' => 'result'
            ]);
        }

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

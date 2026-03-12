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
        $results = Project::where('status', 'closed')->latest()->take(6)->get(); // For "Latest Results" section
        return view('welcome', compact('projects', 'results'));
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

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
        return view('welcome', compact('projects'));
    }

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

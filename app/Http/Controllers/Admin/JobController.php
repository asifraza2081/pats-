<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\JobRequest;
use App\Http\Controllers\Controller;
use App\Models\PatsJob;
use App\Models\Project;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function create(Project $project) { return view('admin.jobs.create', compact('project')); }

    public function store(JobRequest $request, Project $project)
    {
        $data = $request->validated();

        $data['project_id'] = $project->id;
        $data['job_code']   = PatsJob::nextJobCode($project->id);

        PatsJob::create($data);
        return redirect()->route('admin.projects.show', $project)->with('success', 'Job post created.');
    }

    public function edit(PatsJob $job) { return view('admin.jobs.edit', compact('job')); }

    public function update(JobRequest $request, PatsJob $job)
    {
        $data = $request->validated();
        $job->update($data);
        return redirect()->route('admin.projects.show', $job->project_id)->with('success', 'Job updated.');
    }

    public function destroy(PatsJob $job)
    {
        if ($job->applications()->exists()) {
            return back()->with('error', 'Cannot delete job with associated candidate applications.');
        }
        $projectId = $job->project_id;
        $job->delete();
        return redirect()->route('admin.projects.show', $projectId)->with('success', 'Job deleted.');
    }
}

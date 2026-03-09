<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatsJob;
use App\Models\Project;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function create(Project $project) { return view('admin.jobs.create', compact('project')); }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'                 => 'required|string|max:150',
            'department'            => 'nullable|string|max:120',
            'bps_grade'             => 'nullable|string|max:20',
            'total_seats'           => 'required|integer|min:1',
            'quota_notes'           => 'nullable|string',
            'fee'                   => 'required|numeric|min:0',
            'min_degree_level'      => 'nullable|integer|min:1|max:6',
            'min_qualification_name'=> 'nullable|string|max:100',
            'required_subject'      => 'nullable|string|max:100',
            'min_experience_years'  => 'nullable|numeric|min:0',
            'experience_sector'     => 'required|in:Any,Public,Private',
            'age_min'               => 'nullable|integer|min:16',
            'age_max'               => 'nullable|integer|max:70|gte:age_min',
            'domicile_required'     => 'nullable|string|max:80',
        ]);

        $data['project_id'] = $project->id;
        $data['job_code']   = PatsJob::nextJobCode($project->id);

        PatsJob::create($data);
        return redirect()->route('admin.projects.show', $project)->with('success', 'Job post created.');
    }

    public function edit(PatsJob $job) { return view('admin.jobs.edit', compact('job')); }

    public function update(Request $request, PatsJob $job)
    {
        $data = $request->validate([
            'title'                 => 'required|string|max:150',
            'department'            => 'nullable|string|max:120',
            'bps_grade'             => 'nullable|string|max:20',
            'total_seats'           => 'required|integer|min:1',
            'quota_notes'           => 'nullable|string',
            'fee'                   => 'required|numeric|min:0',
            'min_degree_level'      => 'nullable|integer|min:1|max:6',
            'min_qualification_name'=> 'nullable|string|max:100',
            'required_subject'      => 'nullable|string|max:100',
            'min_experience_years'  => 'nullable|numeric|min:0',
            'experience_sector'     => 'required|in:Any,Public,Private',
            'age_min'               => 'nullable|integer|min:16',
            'age_max'               => 'nullable|integer|max:70',
            'domicile_required'     => 'nullable|string|max:80',
        ]);
        $job->update($data);
        return redirect()->route('admin.projects.show', $job->project_id)->with('success', 'Job updated.');
    }

    public function destroy(PatsJob $job)
    {
        $projectId = $job->project_id;
        $job->delete();
        return redirect()->route('admin.projects.show', $projectId)->with('success', 'Job deleted.');
    }
}

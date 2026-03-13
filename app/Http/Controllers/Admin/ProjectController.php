<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount('jobs')->latest()->paginate(15);
        return view('admin.projects.index', compact('projects'));
    }

    public function create() { return view('admin.projects.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'org_name'   => 'required|string|max:200',
            'description'=> 'nullable|string',
            'open_date'  => 'nullable|date',
            'close_date' => 'nullable|date|after_or_equal:open_date',
            'test_date'  => 'nullable|date',
            'status'     => 'required|in:draft,open,closed,result_declared',
        ]);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        $data['created_by'] = Auth::id();
        Project::create($data);
        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load('jobs', 'batches.center', 'centers');
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project) { return view('admin.projects.edit', compact('project')); }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'org_name'   => 'required|string|max:200',
            'description'=> 'nullable|string',
            'open_date'  => 'nullable|date',
            'close_date' => 'nullable|date|after_or_equal:open_date',
            'test_date'  => 'nullable|date',
            'status'     => 'required|in:draft,open,closed,result_declared',
        ]);
        if ($request->hasFile('logo')) {
            if ($project->logo_path) Storage::delete($project->logo_path);
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        $project->update($data);
        return redirect()->route('admin.projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        if ($project->applications()->exists()) {
            return back()->with('error', 'Cannot delete a project that has active applications. Please close the project first.');
        }
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }
}

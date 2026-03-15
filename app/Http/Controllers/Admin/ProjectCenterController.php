<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TestCenter;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectCenterController extends Controller
{
    /**
     * Show the management interface for project centers and examiners.
     */
    public function index(Project $project)
    {
        $project->load('centers');
        $allCenters = TestCenter::where('is_active', true)->orderBy('name')->get();
        $examiners = User::role('examiner')->get();

        // Get pivot data specifically for this project
        $assignedCenters = $project->centers->mapWithKeys(function ($center) {
            return [$center->id => $center->pivot->examiner_id];
        });

        return view('admin.projects.centers', compact('project', 'allCenters', 'examiners', 'assignedCenters'));
    }

    /**
     * Sync centers and assignment examiners.
     */
    public function sync(Request $request, Project $project)
    {
        $request->validate([
            'centers' => 'required|array',
            'centers.*' => 'exists:test_centers,id',
            'examiner' => 'nullable|array',
            'examiner.*' => 'nullable|exists:users,id',
        ]);

        $syncData = [];
        foreach ($request->centers as $centerId) {
            $syncData[$centerId] = [
                'examiner_id' => $request->examiner[$centerId] ?? null
            ];
        }

        $project->centers()->sync($syncData);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project centers and examiners updated successfully.');
    }
}

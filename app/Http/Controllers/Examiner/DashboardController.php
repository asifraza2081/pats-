<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch centers assigned to this examiner
        $assignedCenterIds = $user->assignedCenters()->pluck('test_centers.id');

        $sessions = Batch::with(['project', 'center.city'])
            ->whereIn('center_id', $assignedCenterIds)
            ->where('test_date', '>=', now()->toDateString())
            ->orderBy('test_date')
            ->get();

        return view('examiner.dashboard', compact('sessions'));
    }

    public function showSession(Batch $batch)
    {
        $assignedCenterIds = Auth::user()->assignedCenters()->pluck('test_centers.id');
        abort_unless($assignedCenterIds->contains($batch->center_id), 403, 'Unauthorized access to this session.');

        $batch->load(['project', 'center.city', 'examRollnos.application.candidate.user', 'examRollnos.job']);
        return view('examiner.session-detail', compact('batch'));
    }
}

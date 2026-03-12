<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $candidate = $user->candidate;
        $completion = $candidate ? $candidate->completionPercent() : 0;

        $applications = Application::where('candidate_id', $candidate?->id)
            ->with(['job.project', 'desiredTestCity', 'examRollno.center', 'payment', 'result'])
            ->latest('applied_at')
            ->get();

        return view('candidate.dashboard', compact('user', 'candidate', 'completion', 'applications'));
    }
}

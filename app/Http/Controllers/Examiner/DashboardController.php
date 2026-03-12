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
        // For now, let's assume examiners see sessions assigned to their specific centers
        // or just all active sessions for demo purposes if no assignment logic is yet fully built.
        // Usually, an examiner is linked to a center or a specific batch.
        
        $sessions = Batch::with(['project', 'center.city'])
            ->where('test_date', '>=', now()->toDateString())
            ->orderBy('test_date')
            ->get();

        return view('examiner.dashboard', compact('sessions'));
    }

    public function showSession(Batch $batch)
    {
        $batch->load(['project', 'center.city', 'examRollnos.application.candidate.user', 'examRollnos.job']);
        return view('examiner.session-detail', compact('batch'));
    }
}

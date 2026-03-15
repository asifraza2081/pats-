<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['user'])->withCount('applications');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('cnic', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $candidates = $query->latest()->paginate(25);
        return view('admin.candidates.index', compact('candidates'));
    }

    public function show(Candidate $candidate)
    {
        $candidate->load(['user', 'education', 'experience', 'applications.job.project']);
        return view('admin.candidates.show', compact('candidate'));
    }
}

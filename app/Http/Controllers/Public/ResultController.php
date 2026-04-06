<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function search(Request $request)
    {
        // If no inputs are provided, just return the view with the search form
        if (!$request->has('cnic') && !$request->has('roll_no')) {
            return view('public.results');
        }

        $request->validate([
            'cnic'    => 'required|string',
            'roll_no' => 'required|string',
        ]);

        $cnic = $request->input('cnic');
        $roll = $request->input('roll_no');

        // Attempt to find the results by BOTH roll number AND CNIC
        $results = Result::with(['application.candidate.user', 'application.job.project'])
            ->where('published_at', '<=', now()) // ONLY SHOW PUBLISHED RESULTS
            ->where('roll_no', $roll)
            ->whereHas('application.candidate.user', function($u) use ($cnic) {
                $u->where('cnic', $cnic);
            })
            ->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'No results found for the provided CNIC and Roll Number. Please verify your credentials and try again.');
        }

        return view('public.results', ['results' => $results]);
    }

    public function verify($roll)
    {
        $result = Result::with(['application.candidate.user', 'application.job.project'])
            ->where('roll_no', $roll)
            ->whereNotNull('published_at') // ONLY SHOW PUBLISHED RESULTS
            ->firstOrFail();

        // Used by QR Code scans on result cards
        return view('public.results', compact('result'));
    }
}

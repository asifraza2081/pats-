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
        if (!$request->has('identifier')) {
            return view('public.results');
        }

        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = $request->input('identifier');

        // Attempt to find the results by EITHER roll number OR CNIC
        $results = Result::with(['application.candidate.user', 'application.job.project'])
            ->where('published_at', '<=', now()) // ONLY SHOW PUBLISHED RESULTS
            ->where(function($query) use ($identifier) {
                $query->where('roll_no', $identifier)
                      ->orWhereHas('application.candidate.user', function($u) use ($identifier) {
                          $u->where('cnic', $identifier);
                      });
            })
            ->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'No results found for the provided Roll Number or CNIC. Please verify your credentials and try again.');
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

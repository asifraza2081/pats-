<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // If no query is provided, we just return the view with the search form
        if (!$request->has('query')) {
            return view('public.results');
        }

        // If a query is provided, attempt to find the results by roll number or CNIC
        $results = collect();
        if ($query) {
            $results = Result::with(['application.candidate.user', 'application.job.project'])
                ->whereNotNull('published_at') // ONLY SHOW PUBLISHED RESULTS
                ->where(function($q) use ($query) {
                    $q->where('roll_no', $query)
                      ->orWhereHas('application.candidate.user', function($sq) use ($query) {
                          $sq->where('cnic', $query);
                      });
                })
                ->get();
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

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

        // If a query is provided, attempt to find the result by roll number or CNIC
        $result = null;
        if ($query) {
            $result = Result::with(['application.candidate.user', 'application.job.project'])
                ->where('roll_number', $query)
                ->orWhereHas('application.candidate.user', function($q) use ($query) {
                    $q->where('cnic', $query);
                })
                ->first();
        }

        return view('public.results', ['result' => $result ?? false]);
    }

    public function verify($roll)
    {
        $result = Result::with(['application.candidate.user', 'application.job.project'])
            ->where('roll_number', $roll)
            ->firstOrFail();

        // Used by QR Code scans on result cards
        return view('public.results', compact('result'));
    }
}

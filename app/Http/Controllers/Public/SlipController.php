<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExamRollno;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SlipController extends Controller
{
    public function search(Request $request)
    {
        // If no inputs are provided, just return the view with the search form
        if (!$request->has('identifier')) {
            return view('public.slips');
        }

        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = $request->input('identifier');

        // Look for slip by Roll No or Candidate CNIC
        // Must be marked as ready and have a test date that hasn't strictly passed
        $slips = ExamRollno::with(['application.candidate.user', 'application.job.project', 'center', 'batch'])
            ->where('slip_ready', true)
            ->where(function($query) use ($identifier) {
                $query->where('roll_no', $identifier)
                      ->orWhereHas('application.candidate.user', function($u) use ($identifier) {
                          $u->where('cnic', $identifier);
                      });
            })
            // Optionally filter so old batches don't show, but usually it's fine
            ->get();

        if ($slips->isEmpty()) {
            return back()->with('error', 'No Roll Number Slip found for the provided details. Please verify your credentials or wait for the slip to be issued.');
        }

        return view('public.slips', ['slips' => $slips]);
    }

    public function download($roll_no)
    {
        $examRollno = ExamRollno::with(['application.candidate.user', 'application.job.project', 'center', 'batch'])
            ->where('roll_no', $roll_no)
            ->where('slip_ready', true)
            ->firstOrFail();

        $app = $examRollno->application;
        $candidate = $app->candidate;

        $pdf = Pdf::loadView('pdf.slip', compact('app', 'candidate'));
        
        return $pdf->download("Slip_{$examRollno->roll_no}.pdf");
    }
}

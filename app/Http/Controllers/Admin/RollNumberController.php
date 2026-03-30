<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamRollno;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RollNumberController extends Controller
{
    /**
     * Display a listing of all generated roll numbers.
     */
    public function index(Request $request)
    {
        $query = ExamRollno::with(['application.candidate.user', 'project', 'job', 'city', 'center']);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('roll_no', 'like', "%{$request->search}%")
                  ->orWhereHas('application.candidate.user', function($qu) use ($request) {
                      $qu->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%")
                        ->orWhere('cnic', 'like', "%{$request->search}%");
                  });
            });
        }

        $rollNumbers = $query->latest()->paginate(25);
            
        return view('admin.rollnumbers.index', compact('rollNumbers'));
    }

    /**
     * Generate/Stream the Roll Number Slip PDF for Admin.
     */
    public function slip(Application $app)
    {
        $app->load(['candidate.user', 'job.project', 'examRollno.center', 'examRollno.city']);
        $examRollno = $app->examRollno;

        abort_if(!$examRollno, 404, 'Roll number data not found for this application.');

        $candidate = $app->candidate;
        
        // Admin can download slips regardless of slip_ready status for printing/distribution
        $pdf = Pdf::loadView('pdf.slip', compact('app', 'examRollno', 'candidate'));
        return $pdf->stream("Slip_{$examRollno->roll_no}.pdf");
    }
}

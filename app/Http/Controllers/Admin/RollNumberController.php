<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RollNumber;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RollNumberController extends Controller
{
    public function index()
    {
        $rollNumbers = RollNumber::with(['application.candidate.user', 'application.job.project', 'application.batch.center'])
            ->latest('assigned_at')
            ->paginate(25);
            
        return view('admin.rollnumbers.index', compact('rollNumbers'));
    }

    public function slip(Application $app)
    {
        $app->load(['candidate.user', 'job.project', 'batch.center', 'rollNumber']);
        $roll = $app->rollNumber;

        abort_if(!$roll, 404, 'Roll number not found for this application.');

        $candidate = $app->candidate;
        
        // Admin can download slips regardless of slip_ready status for debugging/printing
        $pdf = Pdf::loadView('pdf.slip', compact('app', 'roll', 'candidate'));
        return $pdf->stream("Admin_Slip_{$roll->roll_number}.pdf");
    }
}

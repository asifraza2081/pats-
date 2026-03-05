<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Request;
use App\Models\Application;
use App\Models\RollNumber;
use TCPDF;

/**
 * Custom TCPDF class for Roll Number Slip layout.
 */
class PattSlipPDF extends TCPDF
{
    // Minimal custom header/footer if needed
}

class RollNumberController
{
    public function __construct()
    {
        Auth::requireAuth();
        Auth::requireRole('candidate', 'admin', 'super_admin');
    }

    public function slip(Request $request, array $params = []): void
    {
        $appId = (int) $params['id'];
        $appData = Application::getDetails($appId);

        if (!$appData) Response::abort(404);

        if (Auth::isCandidate() && (int)$appData['user_id'] !== Auth::id()) {
            Response::abort(403);
        }

        // Must be fully scheduled or result declared to have a slip
        if (!in_array($appData['status'], ['scheduled', 'result_declared'])) {
            \App\Core\Session::flash('error', 'Roll number slip is not available yet.');
            Response::redirect('/applications');
        }

        $rollRecord = RollNumber::getForApplication($appId);
        $rollNo = $rollRecord ? $rollRecord['roll_number'] : 'PENDING';

        $pdf = new PattSlipPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PATS System');
        $pdf->SetTitle('Roll Number Slip - ' . $appData['cnic']);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'PAKISTAN APTITUDE TESTING SERVICE (PATS)', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'ROLL NUMBER SLIP', 0, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'Post Applied For: ' . $appData['job_title'], 1, 1, 'C');
        $pdf->Cell(0, 8, 'Project: ' . $appData['project_name'], 1, 1, 'C');
        $pdf->Ln(5);

        // Candidate Details
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, ' Roll Number:', 1, 0, 'L', 0);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(140, 8, '  ' . $rollNo, 1, 1, 'L', 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, ' CNIC:', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(140, 8, '  ' . substr($appData['cnic'], 0, 5) . '-' . substr($appData['cnic'], 5, 7) . '-' . substr($appData['cnic'], 12), 1, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, ' Name:', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(140, 8, '  ' . strtoupper($appData['candidate_name']), 1, 1, 'L');

        $pdf->Ln(5);

        // Test Details
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, ' Test Date & Time:', 1, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(140, 8, '  ' . date('l, d F Y', strtotime($appData['slot_date'])) . ' at ' . date('h:i A', strtotime($appData['slot_time'])), 1, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, ' Test Center:', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(140, 8, '  ' . $appData['center_name'] . ', ' . $appData['center_city'], 1, 'L', false, 1);

        $pdf->Ln(15);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'Important Instructions for Candidate:', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $ins = "1. You must bring Original CNIC and this Roll Number Slip to the Test Center.\n";
        $ins .= "2. Mobile phones, calculators or any electronic gadgets are strictly prohibited.\n";
        $ins .= "3. Reach the test center at least 30 minutes before the test time.\n";
        $ins .= "4. Use only Blue/Black ballpoint for filling the bubble sheet.\n";
        $pdf->MultiCell(0, 6, $ins, 0, 'L');

        $pdf->Ln(20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 5, 'This is a system generated document and does not require signatures.', 0, 1, 'C');

        while (ob_get_level()) ob_end_clean();
        $pdf->Output('RollNoSlip_' . $appData['cnic'] . '.pdf', 'I');
        exit;
    }
}

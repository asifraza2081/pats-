<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Application;
use TCPDF;

/**
 * Custom TCPDF class for Challan layout.
 */
class PattChallanPDF extends TCPDF
{
    public function Header() {}
    public function Footer() {}
}

class ChallanController
{
    public function __construct()
    {
        Auth::requireAuth();
        Auth::requireRole('candidate', 'admin', 'super_admin');
    }

    public function print(Request $request, array $params = []): void
    {
        $appId = (int) $params['id'];
        $appData = Application::getDetails($appId);

        if (!$appData) Response::abort(404);

        // Security check: Candidate can only print their own challan
        if (Auth::isCandidate() && (int)$appData['user_id'] !== Auth::id()) {
            Response::abort(403);
        }

        $pdf = new PattChallanPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PATS System');
        $pdf->SetTitle('Fee Challan - ' . $appData['challan_ref']);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', '', 9);

        // Render 3 copies (Bank, PATS, Candidate) horizontally on landscape A4
        $copies = ['Bank Copy', 'PATS Copy', 'Candidate Copy'];
        $width = 90; // mm per section
        $x = 10;
        
        foreach ($copies as $copyName) {
            $pdf->SetXY($x, 10);
            
            // Header frame
            $pdf->Rect($x, 10, $width, 185);
            
            // Title
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell($width, 7, 'PATS - Fee Challan', 0, 1, 'C');
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetX($x);
            $pdf->Cell($width, 5, $copyName, 0, 1, 'C');
            
            $pdf->SetX($x);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell($width, 4, 'Branch: ______________________', 0, 1, 'L');
            $pdf->SetX($x);
            $pdf->Cell($width, 4, 'Date: ________________________', 0, 1, 'L');
            
            $pdf->Ln(4);
            $pdf->SetX($x);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell($width, 6, 'A/C Title: PATS Admin', 1, 1, 'C');
            $pdf->SetX($x);
            $pdf->Cell($width, 6, 'A/C No: 1234-5678-9012 (HBL)', 1, 1, 'C');
            
            $pdf->Ln(4);
            $pdf->SetFont('helvetica', '', 9);
            
            // Details
            $details = [
                'Challan Ref' => $appData['challan_ref'],
                'CNIC'        => $appData['cnic'],
                'Name'        => $appData['candidate_name'],
                'Project'     => (strlen((string)$appData['project_name']) > 20) ? substr((string)$appData['project_name'], 0, 17).'...' : $appData['project_name'],
                'Post'        => (strlen((string)$appData['job_title']) > 20) ? substr((string)$appData['job_title'], 0, 17).'...' : $appData['job_title'],
                'Fee (Rs.)'   => number_format($appData['fee'])
            ];
            
            foreach ($details as $label => $val) {
                $pdf->SetX($x);
                $pdf->Cell(30, 6, $label . ':', 1, 0, 'L');
                $pdf->Cell($width - 30, 6, ' ' . $val, 1, 1, 'L');
            }
            
            $pdf->Ln(8);
            $pdf->SetX($x);
            $pdf->Cell($width, 4, 'Applicant Signature: ______________', 0, 1, 'L');
            $pdf->Ln(6);
            $pdf->SetX($x);
            $pdf->Cell($width, 4, 'Cashier Signature: _______________', 0, 1, 'L');
            $pdf->Ln(6);
            $pdf->SetX($x);
            $pdf->Cell($width, 4, 'Officer Signature: _______________', 0, 1, 'L');
            
            $pdf->Ln(10);
            $pdf->SetX($x);
            $pdf->SetFont('helvetica', 'I', 7);
            $pdf->MultiCell($width, 4, 'Please deposit fee in any online branch of HBL. Non-refundable. System generated document.', 0, 'C');

            $x += $width + 5; // move right for next copy
        }

        // Output to browser
        // For development, clear any output buffers
        while (ob_get_level()) ob_end_clean();
        $pdf->Output('Challan_' . $appData['challan_ref'] . '.pdf', 'I');
        exit;
    }
}

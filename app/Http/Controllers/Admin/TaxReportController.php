<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialSetting;
use App\Services\FinancialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TaxReportController extends Controller
{
    public function __construct(private FinancialService $service) {}

    // ── Tax Report Home ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $fbrMode  = FinancialSetting::fbrModeEnabled();
        $org      = $this->service->orgSettings();
        $allFys   = $this->service->allFiscalYears();
        $currentFy = $this->service->currentFiscalYear();

        return view('admin.tax-reports.index', compact('fbrMode', 'org', 'allFys', 'currentFy'));
    }

    // ── FBR Annex-A: Withholding Tax Register ────────────────────────────────

    public function annexA(Request $request)
    {
        $fy       = $request->get('fy', $this->service->currentFiscalYear());
        $expenses = $this->service->getFbrAnnexA($fy);
        $org      = $this->service->orgSettings();
        $fyDates  = [
            'start' => $this->service->fyStartDate($fy)->format('d-M-Y'),
            'end'   => $this->service->fyEndDate($fy)->format('d-M-Y'),
        ];
        $totalTax = $expenses->sum('tax_amount');

        return view('admin.tax-reports.annex-a', compact('fy', 'expenses', 'org', 'fyDates', 'totalTax'));
    }

    // ── Annex-A: Print PDF ────────────────────────────────────────────────────

    public function printAnnexA(Request $request)
    {
        $fy       = $request->get('fy', $this->service->currentFiscalYear());
        $expenses = $this->service->getFbrAnnexA($fy);
        $org      = $this->service->orgSettings();
        $fyDates  = [
            'start' => $this->service->fyStartDate($fy)->format('d-M-Y'),
            'end'   => $this->service->fyEndDate($fy)->format('d-M-Y'),
        ];
        $totalTax = $expenses->sum('tax_amount');

        $pdf = Pdf::loadView('pdf.financial.annex-a', compact('fy', 'expenses', 'org', 'fyDates', 'totalTax'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("fbr-annex-a-{$fy}.pdf");
    }

    // ── Annex-A: Export CSV ────────────────────────────────────────────────

    public function exportAnnexA(Request $request)
    {
        $fy       = $request->get('fy', $this->service->currentFiscalYear());
        $expenses = $this->service->getFbrAnnexA($fy);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Annex-A');

        // Headers
        $headers = [
            'S.No', 'Payment Date', 'Recipient Name', 'NTN / CNIC',
            'Description', 'Gross Amount (PKR)', 'Tax Rate (%)',
            'Tax Deducted (PKR)', 'Net Paid (PKR)', 'Voucher No.', 'Project',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = $sheet->getStyle('A1:K1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1B4F72');
        $headerStyle->getFont()->getColor()->setRGB('FFFFFF');

        // Data rows
        foreach ($expenses as $i => $expense) {
            $row = $i + 2;
            $sheet->fromArray([
                $i + 1,
                $expense->expense_date->format('d-M-Y'),
                $expense->recipient_name ?? 'N/A',
                $expense->recipient_ntn ?? $expense->recipient_cnic ?? 'N/A',
                $expense->description,
                number_format($expense->gross_amount, 2),
                $expense->tax_rate . '%',
                number_format($expense->tax_amount, 2),
                number_format($expense->net_amount, 2),
                $expense->voucher_no ?? '',
                $expense->project?->name ?? 'General',
            ], null, "A{$row}");
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "fbr-annex-a-{$fy}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ── Income & Expenditure Summary ─────────────────────────────────────────

    public function incomeSummary(Request $request)
    {
        $fy   = $request->get('fy', $this->service->currentFiscalYear());
        $data = $this->service->getIncomeSummary($fy);
        $allFys = $this->service->allFiscalYears();

        return view('admin.tax-reports.income-summary', array_merge($data, compact('allFys')));
    }

    // ── Income Summary: Print PDF ─────────────────────────────────────────────

    public function printIncomeSummary(Request $request)
    {
        $fy   = $request->get('fy', $this->service->currentFiscalYear());
        $data = $this->service->getIncomeSummary($fy);

        $pdf = Pdf::loadView('pdf.financial.income-summary', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream("income-expenditure-{$fy}.pdf");
    }
}

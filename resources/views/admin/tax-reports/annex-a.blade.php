@extends('layouts.dashboard')
@section('page-title', "FBR Annex-A â€” FY {$fy}")
@section('page-actions')
    <a href="{{ route('admin.tax-reports.annex-a.print', ['fy' => $fy]) }}" target="_blank" class="btn btn-primary btn-sm">
        <i class="ti ti-printer me-1"></i> Print PDF
    </a>
    <a href="{{ route('admin.tax-reports.annex-a.export', ['fy' => $fy]) }}" class="btn btn-success btn-sm">
        <i class="ti ti-file-excel me-1"></i> Export to Excel
    </a>
    <a href="{{ route('admin.tax-reports.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')
{{-- Header Info --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1B4F72, #2980B9);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fw-black mb-1">ANNEX-A â€” WITHHOLDING TAX REGISTER</h2>
                <div class="opacity-75">Under Section 153 â€” Income Tax Ordinance, 2001</div>
            </div>
            <div class="col-auto text-end">
                <div class="fw-bold">{{ $org['name'] }}</div>
                <div class="small opacity-75">NTN: {{ $org['ntn'] ?: 'â€”' }}</div>
                <div class="badge bg-white text-primary mt-1">FY {{ $fy }} ({{ $fyDates['start'] }} â€” {{ $fyDates['end'] }})</div>
            </div>
        </div>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-primary-lt text-center p-3">
            <div class="small text-muted fw-semibold">Total Payments</div>
            <div class="h3 fw-black text-primary mb-0">{{ $expenses->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-danger-lt text-center p-3">
            <div class="small text-muted fw-semibold">Total Gross Paid</div>
            <div class="h3 fw-black text-danger mb-0">PKR {{ number_format($expenses->sum('gross_amount'), 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-warning-lt text-center p-3">
            <div class="small text-muted fw-semibold">Total Tax Deducted</div>
            <div class="h3 fw-black text-warning mb-0">PKR {{ number_format($totalTax, 2) }}</div>
        </div>
    </div>
</div>

{{-- Annex-A Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter mb-0">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>Payment Date</th>
                    <th>Recipient Name</th>
                    <th>NTN / CNIC</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Voucher No</th>
                    <th class="text-end">Gross (PKR)</th>
                    <th class="text-end">Rate %</th>
                    <th class="text-end">Tax Deducted (PKR)</th>
                    <th class="text-end">Net Paid (PKR)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $expense)
                <tr>
                    <td class="text-muted small">{{ $i + 1 }}</td>
                    <td class="small">{{ $expense->expense_date->format('d-M-Y') }}</td>
                    <td class="fw-semibold">{{ $expense->recipient_name ?? 'â€”' }}</td>
                    <td>
                        @if($expense->recipient_ntn)
                            <span class="badge bg-primary-lt text-primary">{{ $expense->recipient_ntn }}</span>
                        @elseif($expense->recipient_cnic)
                            <span class="badge bg-secondary-lt text-secondary">{{ $expense->recipient_cnic }}</span>
                        @else
                            <span class="badge bg-danger-lt text-danger">Missing</span>
                        @endif
                    </td>
                    <td class="small text-truncate" style="max-width: 200px;">{{ $expense->description }}</td>
                    <td class="small">{{ $expense->category?->name ?? 'â€”' }}</td>
                    <td class="small text-muted">{{ $expense->voucher_no ?? 'â€”' }}</td>
                    <td class="text-end small">{{ number_format($expense->gross_amount, 2) }}</td>
                    <td class="text-end small">{{ $expense->tax_rate }}%</td>
                    <td class="text-end fw-bold text-warning">{{ number_format($expense->tax_amount, 2) }}</td>
                    <td class="text-end small">{{ number_format($expense->net_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center text-muted py-5">No taxable expenses recorded for FY {{ $fy }}.</td></tr>
                @endforelse
            </tbody>
            @if($expenses->isNotEmpty())
            <tfoot class="table-dark fw-bold">
                <tr>
                    <td colspan="7" class="text-end">TOTALS:</td>
                    <td class="text-end">{{ number_format($expenses->sum('gross_amount'), 2) }}</td>
                    <td class="text-end">â€”</td>
                    <td class="text-end text-warning">{{ number_format($totalTax, 2) }}</td>
                    <td class="text-end">{{ number_format($expenses->sum('net_amount'), 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    <div class="card-footer text-muted small">
        <i class="ti ti-info-circle me-1"></i>
        This register is prepared under Section 153 of the Income Tax Ordinance, 2001. Entries with "Missing" NTN/CNIC should be resolved before FBR filing.
    </div>
</div>
@endsection



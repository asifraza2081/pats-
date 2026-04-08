@extends('layouts.dashboard')
@section('page-title', 'Tax Reports — FBR Pakistan')
@section('page-actions')
    <a href="{{ route('admin.financials.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Financial Dashboard
    </a>
@endsection

@section('content')

@if(!$fbrMode)
{{-- FBR Mode Disabled --}}
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="mb-3">
            <span class="avatar avatar-xl bg-warning-lt mx-auto"><i class="ti ti-receipt-tax text-warning" style="font-size: 2.5rem;"></i></span>
        </div>
        <h2 class="fw-black">FBR Mode is Not Enabled</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width:500px;">
            FBR tax compliance features are currently disabled. Enable FBR Mode in Financial Settings to generate
            Annex-A withholding tax registers, Income & Expenditure statements, and IRIS-compatible exports.
        </p>
        <a href="{{ route('admin.financials.settings') }}" class="btn btn-primary px-4">
            <i class="ti ti-settings me-1"></i> Enable FBR Mode in Settings
        </a>
    </div>
</div>
@else

{{-- FBR Mode Active --}}
<div class="alert alert-success d-flex gap-3 mb-4">
    <i class="ti ti-shield-check fs-2 text-green"></i>
    <div>
        <strong>FBR Mode Active</strong>
        — Organisation: <strong>{{ $org['name'] }}</strong> | NTN: <strong>{{ $org['ntn'] ?: 'Not set' }}</strong> | STRN: <strong>{{ $org['strn'] ?: 'Not set' }}</strong>
        <a href="{{ route('admin.financials.settings') }}" class="ms-2 text-muted small">Edit →</a>
    </div>
</div>

{{-- FY Selector --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <span class="text-muted fw-bold">Select Fiscal Year:</span>
    @foreach($allFys as $fyOption)
    <a href="{{ request()->fullUrlWithQuery(['fy' => $fyOption]) }}"
       class="btn btn-sm {{ request('fy', $currentFy) === $fyOption ? 'btn-primary' : 'btn-outline-secondary' }}">
        FY {{ $fyOption }}
    </a>
    @endforeach
</div>

<div class="row g-4">
    {{-- Annex-A Card --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="avatar avatar-lg bg-primary-lt rounded"><i class="ti ti-file-spreadsheet text-primary fs-2"></i></span>
                    <div>
                        <h3 class="fw-black mb-1">Annex-A (Section 153)</h3>
                        <p class="text-muted small mb-0">Withholding Tax Register — listing of all payments from which tax was deducted at source.</p>
                    </div>
                </div>
                <ul class="list-unstyled small text-muted mb-4">
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Recipient NTN / CNIC details</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Gross, tax rate, and tax deducted columns</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Compatible with FBR IRIS format</li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tax-reports.annex-a', ['fy' => request('fy', $currentFy)]) }}" class="btn btn-primary">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
                    <a href="{{ route('admin.tax-reports.annex-a.print', ['fy' => request('fy', $currentFy)]) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="ti ti-printer me-1"></i> Print PDF
                    </a>
                    <a href="{{ route('admin.tax-reports.annex-a.export', ['fy' => request('fy', $currentFy)]) }}" class="btn btn-outline-success">
                        <i class="ti ti-file-excel me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Income & Expenditure Card --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="avatar avatar-lg bg-teal-lt rounded"><i class="ti ti-report text-teal fs-2"></i></span>
                    <div>
                        <h3 class="fw-black mb-1">Income & Expenditure Statement</h3>
                        <p class="text-muted small mb-0">Annual I&E summary — required for FBR NPO/Trust or corporate income tax return filing.</p>
                    </div>
                </div>
                <ul class="list-unstyled small text-muted mb-4">
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Total receipts by category</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Total expenditures by category</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-2"></i>Net surplus / deficit for the FY</li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tax-reports.income-summary', ['fy' => request('fy', $currentFy)]) }}" class="btn btn-teal">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
                    <a href="{{ route('admin.tax-reports.income-summary.print', ['fy' => request('fy', $currentFy)]) }}" target="_blank" class="btn btn-outline-teal">
                        <i class="ti ti-printer me-1"></i> Print PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

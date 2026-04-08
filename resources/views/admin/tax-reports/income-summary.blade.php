@extends('layouts.dashboard')
@section('page-title', "Income & Expenditure Statement — FY {$fiscal_year}")
@section('page-actions')
    <a href="{{ route('admin.tax-reports.income-summary.print', ['fy' => $fiscal_year]) }}" target="_blank" class="btn btn-primary btn-sm">
        <i class="ti ti-printer me-1"></i> Print PDF
    </a>
    <a href="{{ route('admin.tax-reports.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')

{{-- Header --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #0d6832, #2fb344);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fw-black mb-1">INCOME & EXPENDITURE STATEMENT</h2>
                <div class="opacity-75">For the Fiscal Year {{ $fy_start->format('d M Y') }} to {{ $fy_end->format('d M Y') }}</div>
            </div>
            <div class="col-auto text-end">
                <div class="fw-bold">{{ $org['name'] }}</div>
                <div class="small opacity-75">NTN: {{ $org['ntn'] ?: '—' }}</div>
                <div class="small opacity-75">{{ $org['address'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 text-center p-3 bg-success-lt">
            <div class="small text-muted fw-semibold">Total Income</div>
            <div class="h3 fw-black text-success mb-0">PKR {{ number_format($kpis['total_revenue'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 text-center p-3 bg-danger-lt">
            <div class="small text-muted fw-semibold">Total Expenditure</div>
            <div class="h3 fw-black text-danger mb-0">PKR {{ number_format($kpis['total_expense'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 text-center p-3 {{ $kpis['net_surplus'] >= 0 ? 'bg-azure-lt' : 'bg-warning-lt' }}">
            <div class="small text-muted fw-semibold">Net {{ $kpis['net_surplus'] >= 0 ? 'Surplus' : 'Deficit' }}</div>
            <div class="h3 fw-black {{ $kpis['net_surplus'] >= 0 ? 'text-azure' : 'text-warning' }} mb-0">PKR {{ number_format(abs($kpis['net_surplus']), 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 text-center p-3 bg-yellow-lt">
            <div class="small text-muted fw-semibold">WHT Deducted</div>
            <div class="h3 fw-black text-yellow mb-0">PKR {{ number_format($kpis['tax_deducted'], 2) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Revenue by Category --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-success-lt">
                <h3 class="card-title fw-black m-0 text-success"><i class="ti ti-trending-up me-2"></i>INCOME</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Amount (PKR)</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenue_by_category as $item)
                        <tr>
                            <td>{{ $item->category }}</td>
                            <td class="text-end fw-semibold text-success">{{ number_format($item->total, 2) }}</td>
                            <td class="text-end text-muted small">
                                {{ $kpis['total_revenue'] > 0 ? number_format($item->total / $kpis['total_revenue'] * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-success fw-bold">
                        <tr>
                            <td>TOTAL INCOME</td>
                            <td class="text-end">PKR {{ number_format($kpis['total_revenue'], 2) }}</td>
                            <td class="text-end">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Expense by Category --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-danger-lt">
                <h3 class="card-title fw-black m-0 text-danger"><i class="ti ti-trending-down me-2"></i>EXPENDITURE</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Amount (PKR)</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expense_by_category as $item)
                        <tr>
                            <td>{{ $item->category }}</td>
                            <td class="text-end fw-semibold text-danger">{{ number_format($item->total, 2) }}</td>
                            <td class="text-end text-muted small">
                                {{ $kpis['total_expense'] > 0 ? number_format($item->total / $kpis['total_expense'] * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-danger fw-bold">
                        <tr>
                            <td>TOTAL EXPENDITURE</td>
                            <td class="text-end">PKR {{ number_format($kpis['total_expense'], 2) }}</td>
                            <td class="text-end">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Net Summary Bar --}}
<div class="card border-0 shadow-sm mt-4" style="background: {{ $kpis['net_surplus'] >= 0 ? '#0d6832' : '#9b2226' }};">
    <div class="card-body p-4 text-white text-center">
        <h3 class="fw-black mb-1">NET {{ $kpis['net_surplus'] >= 0 ? 'SURPLUS' : 'DEFICIT' }} FOR FY {{ $fiscal_year }}</h3>
        <div class="display-5 fw-black">PKR {{ number_format(abs($kpis['net_surplus']), 2) }}</div>
        <div class="opacity-75 small mt-1">Total Income ({{ number_format($kpis['total_revenue'],2) }}) − Total Expenditure ({{ number_format($kpis['total_expense'],2) }})</div>
    </div>
</div>
@endsection

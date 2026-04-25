@extends('layouts.dashboard')
@section('page-title', 'Financial Dashboard')
@section('page-actions')
    <a href="{{ route('admin.financials.settings') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-settings me-1"></i> Settings
    </a>
    <a href="{{ route('admin.financials.ledger') }}" class="btn btn-outline-primary btn-sm">
        <i class="ti ti-list me-1"></i> Full Ledger
    </a>
    @role('super_admin')
    <a href="{{ route('admin.tax-reports.index') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-receipt-tax me-1"></i> Tax Reports
    </a>
    @endrole
@endsection

@section('content')
@php
    use Illuminate\Support\Number;
    function pkr($val) { return 'PKR ' . number_format($val, 2); }
@endphp

{{-- FBR Mode Banner --}}
@if(!$fbrMode)
<div class="alert alert-info alert-dismissible mb-4 d-flex align-items-center gap-3">
    <i class="ti ti-info-circle fs-2 text-azure"></i>
    <div>
        <strong>Simple Mode Active.</strong>
        <a href="{{ route('admin.financials.settings') }}" class="alert-link ms-1">Enable FBR Mode</a>
        to unlock Pakistan tax compliance features â€” Annex-A, NTN tracking, and IRIS-compatible exports.
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@else
<div class="alert alert-success mb-4 d-flex align-items-center gap-3">
    <i class="ti ti-shield-check fs-2 text-green"></i>
    <div><strong>FBR Mode Enabled.</strong> Tax compliance features are active. All expenses with withholding tax will appear in Annex-A.</div>
</div>
@endif

{{-- Fiscal Year Selector --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <span class="text-muted fw-bold">Fiscal Year:</span>
    <div class="btn-group">
        @foreach($allFys as $fyOption)
        <a href="{{ request()->fullUrlWithQuery(['fy' => $fyOption]) }}"
           class="btn btn-sm {{ $fy === $fyOption ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $fyOption }}
        </a>
        @endforeach
        @if($allFys->isEmpty())
        <span class="badge bg-secondary px-3 py-2">{{ $fy }}</span>
        @endif
    </div>
</div>

{{-- â”€â”€ KPI Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #2fb344 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-success-lt me-2 rounded"><i class="ti ti-trending-up text-success"></i></span>
                    <span class="text-muted small fw-semibold">Total Revenue</span>
                </div>
                <div class="h2 fw-black mb-0 text-success">{{ pkr($kpis['total_revenue']) }}</div>
                <div class="text-muted small mt-1">FY {{ $fy }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #e74c3c !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-danger-lt me-2 rounded"><i class="ti ti-trending-down text-danger"></i></span>
                    <span class="text-muted small fw-semibold">Total Expenses</span>
                </div>
                <div class="h2 fw-black mb-0 text-danger">{{ pkr($kpis['total_expense']) }}</div>
                <div class="text-muted small mt-1">FY {{ $fy }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #3498db !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm {{ $kpis['net_surplus'] >= 0 ? 'bg-azure-lt' : 'bg-warning-lt' }} me-2 rounded">
                        <i class="ti ti-scale {{ $kpis['net_surplus'] >= 0 ? 'text-azure' : 'text-warning' }}"></i>
                    </span>
                    <span class="text-muted small fw-semibold">Net Surplus</span>
                </div>
                <div class="h2 fw-black mb-0 {{ $kpis['net_surplus'] >= 0 ? 'text-azure' : 'text-warning' }}">
                    {{ pkr($kpis['net_surplus']) }}
                </div>
                <div class="text-muted small mt-1">Revenue âˆ’ Expenses</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #f39c12 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-warning-lt me-2 rounded"><i class="ti ti-receipt-tax text-warning"></i></span>
                    <span class="text-muted small fw-semibold">Tax Withheld</span>
                </div>
                <div class="h2 fw-black mb-0 text-warning">{{ pkr($kpis['tax_deducted']) }}</div>
                <div class="text-muted small mt-1">WHT deducted from expenses</div>
            </div>
        </div>
    </div>
</div>

{{-- â”€â”€ Charts Row â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="row g-3 mb-4">
    {{-- Monthly Trend Bar Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-0">
                <h3 class="card-title fw-bold"><i class="ti ti-chart-bar me-2 text-primary"></i>Monthly Revenue vs Expenses â€” FY {{ $fy }}</h3>
            </div>
            <div class="card-body">
                <div id="chart-trend" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    {{-- Expense Category Donut --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-0">
                <h3 class="card-title fw-bold"><i class="ti ti-chart-donut me-2 text-indigo"></i>Expense Breakdown</h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($donut->isEmpty())
                    <div class="text-center text-muted py-5"><i class="ti ti-mood-empty fs-1 d-block mb-2"></i>No expenses recorded</div>
                @else
                    <div id="chart-donut" style="min-height: 250px; width: 100%;"></div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- â”€â”€ Project-wise Breakdown Table â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold m-0"><i class="ti ti-briefcase me-2 text-secondary"></i>Project-wise Financial Summary</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th class="text-end">Revenue Collected</th>
                            <th class="text-end">Total Expenses</th>
                            <th class="text-end">Net Surplus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $projectId => $entries)
                        @php
                            $projName = $entries->first()?->project?->name ?? 'General';
                            $rev = $entries->where('type', 'revenue')->sum('total');
                            $exp = $entries->where('type', 'expense')->sum('total');
                            $net = $rev - $exp;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $projName }}</td>
                            <td class="text-end text-success">{{ pkr($rev) }}</td>
                            <td class="text-end text-danger">{{ pkr($exp) }}</td>
                            <td class="text-end fw-bold {{ $net >= 0 ? 'text-azure' : 'text-warning' }}">{{ pkr($net) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No project-level financial data for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- â”€â”€ Recent Ledger Entries â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold m-0"><i class="ti ti-list me-2 text-teal"></i>Recent Ledger Entries</h3>
        <a href="{{ route('admin.financials.ledger') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Project</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $entry)
                <tr>
                    <td class="text-muted small">{{ $entry->ledger_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $entry->type === 'revenue' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }}">
                            {{ ucfirst($entry->type) }}
                        </span>
                    </td>
                    <td class="small">{{ $entry->category }}</td>
                    <td class="text-truncate small" style="max-width: 250px;">{{ $entry->description }}</td>
                    <td class="small text-muted">{{ $entry->project?->name ?? 'â€”' }}</td>
                    <td class="text-end fw-semibold {{ $entry->type === 'revenue' ? 'text-success' : 'text-danger' }}">
                        {{ $entry->amount_formatted }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No ledger entries yet. Verify a payment or record an expense to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/js/apexcharts.min.js') }}"></script>
<script>
// â”€â”€ Monthly Trend Chart â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
var trendOptions = {
    series: [
        { name: 'Revenue', data: @json($trend['revenue']), color: '#2fb344' },
        { name: 'Expenses', data: @json($trend['expenses']), color: '#e74c3c' },
    ],
    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '60%', grouped: true } },
    dataLabels: { enabled: false },
    xaxis: { categories: @json($trend['labels']), labels: { style: { fontSize: '11px' } } },
    yaxis: { labels: { formatter: function(v) { return 'PKR ' + v.toLocaleString(); } } },
    tooltip: { y: { formatter: function(v) { return 'PKR ' + v.toLocaleString('en', {minimumFractionDigits: 2}); } } },
    grid: { borderColor: '#e9ecef', strokeDashArray: 3 },
    legend: { position: 'top' },
};
new ApexCharts(document.getElementById('chart-trend'), trendOptions).render();

@if($donut->isNotEmpty())
// â”€â”€ Expense Category Donut â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
var donutOptions = {
    series: @json($donut->pluck('total')->map(fn($v) => round($v, 2))->values()),
    labels: @json($donut->pluck('category')->values()),
    chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', formatter: function(w) { var total = w.globals.seriesTotals.reduce((a,b) => a+b, 0); return 'PKR ' + total.toLocaleString('en', {minimumFractionDigits: 2}); } } } } } },
    legend: { position: 'bottom', fontSize: '11px' },
    colors: ['#206bc4','#4299e1','#2fb344','#f59f00','#d63939','#ae3ec9','#17a589','#566573'],
    tooltip: { y: { formatter: function(v) { return 'PKR ' + v.toLocaleString('en', {minimumFractionDigits:2}); } } },
};
new ApexCharts(document.getElementById('chart-donut'), donutOptions).render();
@endif
</script>
@endpush



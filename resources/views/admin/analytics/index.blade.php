@extends('layouts.dashboard')
@section('title', 'Candidate Funnel Analytics')
@section('page-title', 'Candidate Intelligence Hub')

@push('styles')
<style>
    /* ── Skeleton Shimmer ─────────────────────── */
    .chart-skeleton {
        border-radius: 8px;
        background: linear-gradient(90deg, var(--tblr-bg-surface-secondary, #f0f4f8) 25%, var(--tblr-bg-surface-tertiary, #e4e9f0) 50%, var(--tblr-bg-surface-secondary, #f0f4f8) 75%);
        background-size: 200% 100%;
        animation: chart-shimmer 1.5s infinite;
    }
    @keyframes chart-shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ── KPI Cards ────────────────────────────── */
    .kpi-card {
        border-left: 4px solid transparent !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,.09) !important; }
    .kpi-card.kpi-blue   { border-left-color: var(--tblr-primary) !important; }
    .kpi-card.kpi-green  { border-left-color: var(--tblr-success) !important; }
    .kpi-card.kpi-yellow { border-left-color: var(--tblr-warning) !important; }
    .kpi-card.kpi-purple { border-left-color: var(--tblr-purple)  !important; }

    /* ── Filter Bar ───────────────────────────── */
    .analytics-filter-bar { background: var(--tblr-bg-surface); }

    /* ── Accordion Accents ────────────────────── */
    .accordion-item.acc-paid    { border-left: 3px solid var(--tblr-success) !important; }
    .accordion-item.acc-pending { border-left: 3px solid var(--tblr-warning) !important; }
    .accordion-item.acc-pending .accordion-button { background-color: rgba(var(--tblr-warning-rgb), 0.04); }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════
     FILTER BAR (full width — replaces sidebar)
═══════════════════════════════════════════════════════ --}}
<div class="card shadow-sm border-0 rounded-4 mb-4 analytics-filter-bar">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="row g-3 align-items-center">
            <div class="col-auto">
                <span class="fw-bold text-primary"><i class="ti ti-chart-pie me-2"></i>Intelligence Filter</span>
            </div>
            <div class="col col-md-4">
                <select name="job_id" class="form-select" onchange="this.form.submit()">
                    <option value="">— Choose Active Job —</option>
                    <option value="all" {{ ($selectedJob && $selectedJob->id == 'all') ? 'selected' : '' }}>ALL ACTIVE JOBS</option>
                    @foreach($jobs as $job)
                        <option value="{{ $job->id }}" {{ ($selectedJob && $selectedJob->id == $job->id) ? 'selected' : '' }}>
                            {{ $job->title }} ({{ $job->project->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedJob)
            <div class="col-auto ms-auto text-muted small">
                <i class="ti ti-clock me-1"></i> Data aggregated live &nbsp;·&nbsp; {{ now()->format('H:i') }}
            </div>
            @endif
        </form>
    </div>
</div>

@if($selectedJob)

{{-- ═══════════════════════════════════════════════════════
     KPI CARDS — 4 columns, color-coded left borders
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    @php
        $totalApplied  = $analytics['total_applied'];
        $paidEligible  = $analytics['paid_eligible'];
        $unpaid        = $analytics['unpaid_ineligible'];
        $allocated     = $analytics['allocated'];
        $paidPct       = $totalApplied > 0 ? round(($paidEligible / $totalApplied) * 100) : 0;
        $allocPct      = $paidEligible  > 0 ? round(($allocated   / $paidEligible)  * 100) : 0;
    @endphp

    {{-- Applied --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-sm border-0 shadow-sm rounded-4 kpi-card kpi-blue h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-primary-lt text-primary me-3 rounded-3"><i class="ti ti-users"></i></span>
                    <div class="text-muted small fw-semibold">Applied Total</div>
                </div>
                <div class="fw-black fs-2 text-dark">{{ number_format($totalApplied) }}</div>
                <div class="progress mt-2" style="height:4px;">
                    <div class="progress-bar bg-primary" style="width:100%"></div>
                </div>
                <div class="text-muted small mt-1">Total pipeline</div>
            </div>
        </div>
    </div>

    {{-- Paid & Verified --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-sm border-0 shadow-sm rounded-4 kpi-card kpi-green h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-success-lt text-success me-3 rounded-3"><i class="ti ti-check"></i></span>
                    <div class="text-muted small fw-semibold">Paid & Verified</div>
                </div>
                <div class="fw-black fs-2 text-success">{{ number_format($paidEligible) }} <small class="fs-5 fw-normal text-muted">Ready</small></div>
                <div class="progress mt-2" style="height:4px;">
                    <div class="progress-bar bg-success" style="width:{{ $paidPct }}%"></div>
                </div>
                <div class="text-muted small mt-1">{{ $paidPct }}% of applicants</div>
            </div>
        </div>
    </div>

    {{-- Pending --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-sm border-0 shadow-sm rounded-4 kpi-card kpi-yellow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-warning-lt text-warning me-3 rounded-3"><i class="ti ti-clock"></i></span>
                    <div class="text-muted small fw-semibold">Pending Payments</div>
                </div>
                <div class="fw-black fs-2 text-warning">{{ number_format($unpaid) }} <small class="fs-5 fw-normal text-muted">Waiting</small></div>
                <div class="progress mt-2" style="height:4px;">
                    <div class="progress-bar bg-warning" style="width:{{ $totalApplied > 0 ? round(($unpaid/$totalApplied)*100) : 0 }}%"></div>
                </div>
                <div class="text-muted small mt-1">Awaiting payment verification</div>
            </div>
        </div>
    </div>

    {{-- Allocated --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-sm border-0 shadow-sm rounded-4 kpi-card kpi-purple h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="avatar avatar-sm bg-purple-lt text-purple me-3 rounded-3"><i class="ti ti-calendar-event"></i></span>
                    <div class="text-muted small fw-semibold">Allocated / Seated</div>
                </div>
                <div class="fw-black fs-2 text-purple">{{ number_format($allocated) }} <small class="fs-5 fw-normal text-muted">Seated</small></div>
                <div class="progress mt-2" style="height:4px;">
                    <div class="progress-bar bg-purple" style="width:{{ $allocPct }}%"></div>
                </div>
                <div class="text-muted small mt-1">{{ $allocPct }}% of verified pool</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CHART ROW 1: Funnel (col-8) | Gender + Age (col-4 stacked)
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark"><i class="ti ti-chart-bar me-2 text-primary"></i>Application Funnel Progression</h3>
            </div>
            <div class="card-body">
                <div id="chart-funnel"><div class="chart-skeleton" style="height:220px;"></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 d-flex flex-column gap-3">
        <div class="card shadow-sm border-0 rounded-4 flex-fill">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark">Gender Spread</h3>
            </div>
            <div class="card-body pt-0">
                <div id="chart-gender"><div class="chart-skeleton" style="height:160px;"></div></div>
            </div>
        </div>
        <div class="card shadow-sm border-0 rounded-4 flex-fill">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark">Age Distribution</h3>
            </div>
            <div class="card-body pt-0">
                <div id="chart-age"><div class="chart-skeleton" style="height:160px;"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CHART ROW 2: Geo horizontal (col-8) | Domicile (col-4)
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark"><i class="ti ti-map-pin me-2 text-teal"></i>Test City Distribution</h3>
            </div>
            <div class="card-body">
                <div id="chart-geography"><div class="chart-skeleton" style="height:280px;"></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark"><i class="ti ti-building me-2 text-yellow"></i>Province / Domicile</h3>
            </div>
            <div class="card-body">
                <div id="chart-domicile"><div class="chart-skeleton" style="height:280px;"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     DEEP BREAKDOWN: City Accordion
═══════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center mb-3 pb-2 border-bottom">
    <h3 class="fw-bold text-dark mb-0"><i class="ti ti-list-details me-2 text-primary"></i>Candidate Breakdown by City</h3>
</div>

@foreach($groupedCandidates as $city => $paymentGroups)
@php
    $cityPaid    = $paymentGroups['Paid & Eligible']->count();
    $cityPending = $paymentGroups['Pending Payment']->count();
    $cityTotal   = $cityPaid + $cityPending;
@endphp
<div class="card shadow-sm border-0 mb-3 rounded-4 overflow-hidden">
    <div class="card-header border-bottom bg-light bg-opacity-50 pb-2 pt-3 d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold text-navy mb-0">
            <i class="ti ti-map-pin text-teal me-2"></i>{{ $city }}
        </h3>
        <div class="d-flex gap-2 align-items-center">
            @if($cityPaid > 0)
            <span class="badge bg-success-lt text-success px-2 py-1 fw-semibold">
                <i class="ti ti-check me-1"></i>{{ $cityPaid }} Paid
            </span>
            @endif
            @if($cityPending > 0)
            <span class="badge bg-warning-lt text-warning px-2 py-1 fw-semibold">
                <i class="ti ti-clock me-1"></i>{{ $cityPending }} Pending
            </span>
            @endif
            <span class="badge bg-indigo-lt px-3 py-1 fw-bold fs-6">{{ $cityTotal }} Total</span>
        </div>
    </div>

    <div class="accordion accordion-flush" id="accordion-city-{{ Str::slug($city) }}">
        @foreach(['Paid & Eligible' => ['success', 'acc-paid'], 'Pending Payment' => ['warning', 'acc-pending']] as $status => [$colorClass, $accClass])
        @if($paymentGroups[$status]->count() > 0)
        <div class="accordion-item shadow-none border-0 border-bottom {{ $accClass }}">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed py-3 fw-medium text-dark bg-white" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ Str::slug($city . '-' . $status) }}">
                    <div class="d-flex w-100 justify-content-between align-items-center me-3">
                        <span>
                            <span class="status-indicator status-{{ $colorClass }} status-indicator-animated me-2"></span>
                            {{ $status }}
                        </span>
                        <span class="badge bg-{{ $colorClass }}-lt px-2">{{ $paymentGroups[$status]->count() }}</span>
                    </div>
                </button>
            </h2>
            <div id="collapse-{{ Str::slug($city . '-' . $status) }}"
                 class="accordion-collapse collapse"
                 data-bs-parent="#accordion-city-{{ Str::slug($city) }}">
                <div class="accordion-body p-0">
                    <form action="{{ route('admin.applications.bulk-mark-paid') }}" method="POST">
                        @csrf
                        @if($status === 'Pending Payment')
                        <div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <span class="small text-muted fw-bold ps-2">Selected candidates will be moved to the verified pool.</span>
                            <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm rounded-pill">
                                <i class="ti ti-check me-1"></i>Bulk Mark Paid
                            </button>
                        </div>
                        @endif
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-vcenter table-hover card-table table-sm">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        @if($status === 'Pending Payment')<th class="w-1"></th>@endif
                                        <th class="w-1">Sr.</th>
                                        <th>Applicant Name</th>
                                        <th>CNIC</th>
                                        <th>Contact</th>
                                        <th>Submission Date</th>
                                        @if($status === 'Pending Payment')<th class="w-1">Action</th>@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentGroups[$status] as $index => $app)
                                    <tr>
                                        @if($status === 'Pending Payment')
                                        <td><input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="form-check-input"></td>
                                        @endif
                                        <td class="text-secondary small text-center">{{ $index + 1 }}</td>
                                        <td class="fw-bold">
                                            <a href="{{ route('admin.applications.show', $app->id) }}" target="_blank" class="text-reset">
                                                {{ $app->candidate->user->full_name }}
                                            </a>
                                        </td>
                                        <td class="text-secondary font-mono">{{ $app->candidate->user->cnic }}</td>
                                        <td class="text-secondary">{{ $app->candidate->user->phone }}</td>
                                        <td class="text-secondary small">{{ $app->created_at->format('M d, Y') }}</td>
                                        @if($status === 'Pending Payment')
                                        <td>
                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-success border-0"
                                                    onclick="event.preventDefault(); ajaxMarkPaid('{{ route('admin.applications.mark-paid', $app->id) }}', this)"
                                                    data-bs-toggle="tooltip" title="Mark as Paid">
                                                <i class="ti ti-currency-dollar"></i>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endforeach

@else
{{-- ── Empty State ── --}}
<div class="text-center py-6">
    <div class="empty bg-transparent">
        <div class="empty-icon text-primary mb-4">
            <i class="ti ti-chart-pie" style="font-size: 4rem;"></i>
        </div>
        <h2 class="empty-title mb-3">Candidate Intelligence Tracking</h2>
        <p class="empty-subtitle text-secondary mb-4 mx-auto" style="max-width: 600px;">
            Select a target active job position above. The system will aggregate the full pipeline — who has paid, who is verified, and exactly which test center each candidate maps to.
        </p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/js/apexcharts.min.js') }}"></script>
<script>
function ajaxMarkPaid(url, button) {
    if(!confirm('Mark this candidate as paid?')) return;
    button.disabled = true;
    const orig = button.innerHTML;
    button.innerHTML = '<i class="ti ti-loader ti-spin"></i>';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => {
        if(r.ok) window.location.reload();
        else { toastr.error('Failed to mark paid. Check permissions.'); button.disabled = false; button.innerHTML = orig; }
    }).catch(() => { toastr.error('Unexpected error.'); button.disabled = false; button.innerHTML = orig; });
}

document.addEventListener('DOMContentLoaded', () => {
    @if($selectedJob && isset($analytics))
    // ── Chart helpers ──────────────────────────
    function clearSkeleton(id) {
        const el = document.querySelector(id + ' .chart-skeleton');
        if (el) el.remove();
    }

    // 1. Funnel (horizontal bar)
    new ApexCharts(document.querySelector('#chart-funnel'), {
        series: [{ name: 'Candidates', data: [{{ $analytics['total_applied'] }}, {{ $analytics['paid_eligible'] }}, {{ $analytics['allocated'] }}] }],
        chart: { type: 'bar', height: 220, toolbar: { show: false }, animations: { speed: 600 } },
        plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 4, dataLabels: { position: 'center' } } },
        colors: ['#3b82f6', '#10b981', '#8b5cf6'],
        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: 'bold', colors: ['#fff'] } },
        xaxis: { categories: ['Applied', 'Paid & Verified', 'Allocated'] },
        legend: { show: false },
        grid: { borderColor: 'transparent' },
    }).render().then(() => clearSkeleton('#chart-funnel'));

    // 2. Gender (donut)
    new ApexCharts(document.querySelector('#chart-gender'), {
        series: [{{ $analytics['gender']['Male'] }}, {{ $analytics['gender']['Female'] }}, {{ $analytics['gender']['Other'] }}],
        chart: { type: 'donut', height: 160, toolbar: { show: false } },
        labels: ['Male', 'Female', 'Other'],
        colors: ['#206bc4', '#d63384', '#616876'],
        legend: { position: 'right', fontSize: '11px' },
        plotOptions: { pie: { donut: { size: '60%' } } },
        dataLabels: { enabled: false },
    }).render().then(() => clearSkeleton('#chart-gender'));

    // 3. Age (pie)
    new ApexCharts(document.querySelector('#chart-age'), {
        series: [{{ $analytics['age_groups']['18-25'] }}, {{ $analytics['age_groups']['26-30'] }}, {{ $analytics['age_groups']['31-40'] }}, {{ $analytics['age_groups']['41+'] }}],
        chart: { type: 'pie', height: 160, toolbar: { show: false } },
        labels: ['18–25', '26–30', '31–40', '41+'],
        colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
        legend: { position: 'right', fontSize: '11px' },
        dataLabels: { enabled: false },
    }).render().then(() => clearSkeleton('#chart-age'));

    // 4. Geography — HORIZONTAL for readability
    new ApexCharts(document.querySelector('#chart-geography'), {
        series: [{ name: 'Candidates', data: {!! json_encode(array_values($analytics['geography'])) !!} }],
        chart: { type: 'bar', height: 280, toolbar: { show: false }, animations: { speed: 600 } },
        plotOptions: { bar: { horizontal: true, borderRadius: 4, dataLabels: { position: 'top' } } },
        xaxis: { categories: {!! json_encode(array_keys($analytics['geography'])) !!} },
        colors: ['#008FFB'],
        grid: { xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
        dataLabels: { enabled: true, offsetX: 4, style: { fontSize: '11px', colors: ['#333'] } },
    }).render().then(() => clearSkeleton('#chart-geography'));

    // 5. Domicile (horizontal bar)
    new ApexCharts(document.querySelector('#chart-domicile'), {
        series: [{ name: 'Candidates', data: {!! json_encode(array_values($analytics['domicile'])) !!} }],
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
        xaxis: { categories: {!! json_encode(array_keys($analytics['domicile'])) !!} },
        colors: ['#FEB019'],
        dataLabels: { enabled: true, offsetX: 4, style: { fontSize: '11px', colors: ['#333'] } },
    }).render().then(() => clearSkeleton('#chart-domicile'));
    @endif
});
</script>
@endpush

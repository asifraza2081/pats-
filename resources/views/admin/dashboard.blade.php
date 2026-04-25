@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('page-title', 'Overview & Intelligence')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-md shadow-sm border-0 bg-primary text-primary-fg" style="background: linear-gradient(135deg, #206bc4 0%, #1e5bb0 100%) !important; border: 0;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="h1 fw-bold mb-1">Welcome back, {{ auth()->user()->first_name ?? 'Administrator' }}!</h2>
                        <div class="opacity-75 fs-3">
                            Here is a snapshot of your system's current performance and recent operational activities.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0 fw-bold text-secondary">
                        <i class="ti ti-bolt text-warning me-2"></i> Operational Intelligence & Shortcuts
                    </h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary btn-pill">
                            <i class="ti ti-plus me-1"></i> New Project
                        </a>
                        <a href="{{ route('admin.batches.create') }}" class="btn btn-outline-success btn-pill">
                            <i class="ti ti-calendar-plus me-1"></i> Schedule Session
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-warning btn-pill">
                            <i class="ti ti-discount-check me-1"></i> Verify Payments
                        </a>
                        <a href="{{ route('admin.results.upload') }}" class="btn btn-outline-purple btn-pill">
                            <i class="ti ti-file-upload me-1"></i> Post Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mb-4">
    @php
    $cards = [
        ['label'=>'Active Projects',   'value'=>$stats['projects'],    'icon'=>'briefcase',       'color'=>'primary'],
        ['label'=>'Total Job Posts',   'value'=>$stats['jobs'],        'icon'=>'list-check',      'color'=>'info'],
        ['label'=>'Total Applications','value'=>$stats['applications'], 'icon'=>'file-text',       'color'=>'dark'],
        ['label'=>'Pending Payments',  'value'=>$stats['pending_pay'], 'icon'=>'clock-hour-4',    'color'=>'warning'],
        ['label'=>'Verified Payments', 'value'=>$stats['verified_pay'], 'icon'=>'cash',            'color'=>'success'],
        ['label'=>'Appeared in Test',  'value'=>$stats['appeared'],    'icon'=>'user-check',      'color'=>'secondary'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card card-sm shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-{{ $c['color'] }} text-white avatar avatar-sm shadow-sm">
                            <i class="ti ti-{{ $c['icon'] }}"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="fw-bold mb-0">
                            {{ number_format($c['value'] ?? 0) }}
                        </div>
                        <div class="text-secondary small text-truncate" title="{{ $c['label'] }}">
                            {{ $c['label'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- 🗺️ Tactical Analytics Restoration (Heatmap & ROI) -->
<div class="row row-cards mb-4">
    <!-- Geographic Intelligence -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 bg-transparent pb-0">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="card-title fw-bold text-dark m-0">
                        <i class="ti ti-map-pin-2 text-primary me-2 fs-2"></i> Applicant Geographic Heatmap
                    </h3>
                    <div class="btn-group">
                        <input type="radio" class="btn-check" name="heatmap-view" id="view-total" value="total" checked>
                        <label class="btn btn-outline-secondary btn-sm" for="view-total">Total</label>
                        <input type="radio" class="btn-check" name="heatmap-view" id="view-paid" value="verified">
                        <label class="btn btn-outline-secondary btn-sm" for="view-paid">Paid</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div id="pakistan-map" style="height: 350px;"></div>
                    </div>
                    <div class="col-md-5 border-start">
                        <div class="mb-3">
                            <h4 class="small fw-bold text-secondary mb-3">TOP RECRUITMENT HUBS</h4>
                            @foreach($cityDistribution->take(5) as $city)
                                @php $pct = $stats['applications'] > 0 ? round(($city['count'] / $stats['applications']) * 100) : 0; @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold text-dark">{{ $city['city'] }}</span>
                                        <span class="text-secondary small">{{ number_format($city['count']) }}</span>
                                    </div>
                                    <div class="progress progress-sm rounded-pill">
                                        <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Financial ROI -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 bg-transparent pb-0">
                <h3 class="card-title fw-bold text-dark">
                    <i class="ti ti-cash-banknote text-success me-2 fs-2"></i> Project Financial Performance
                </h3>
            </div>
            <div class="card-body">
                <div id="chart-roi" style="min-height: 280px;"></div>
                <div class="mt-3 p-3 bg-light rounded-3">
                    @php
                        $totalRev = $projectRoi->sum('revenue');
                        $totalExp = $projectRoi->sum('expense');
                        $margin = $totalRev > 0 ? (($totalRev - $totalExp) / $totalRev) * 100 : 0;
                    @endphp
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="text-secondary tiny text-uppercase fw-bold" style="font-size: 0.6rem;">Avg. Margin</div>
                            <div class="fs-3 fw-bold text-success">{{ round($margin, 1) }}%</div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary tiny text-uppercase fw-bold" style="font-size: 0.6rem;">Net Surplus</div>
                            <div class="fs-3 fw-bold text-primary">PKR {{ number_format($totalRev - $totalExp) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <!-- Recent Applications -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold"><i class="ti ti-history text-primary me-2 fs-2 align-text-bottom"></i> Recent Applications</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">View All</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Applied Post</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApps as $app)
                        <tr>
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    <span class="avatar me-2 rounded" style="background-image: url('{{ $app->candidate->photo_path ? Storage::url($app->candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($app->candidate->user->first_name) }}')"></span>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium text-dark">{{ $app->candidate->user->full_name }}</div>
                                        <div class="text-secondary small">{{ $app->candidate->user->cnic }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-body fw-semibold">{{ Str::limit($app->job->title, 30) }}</div>
                                <div class="text-secondary small">{{ Str::limit($app->job->project->name, 25) }}</div>
                            </td>
                            <td>
                                @php 
                                    $colors=[
                                        'submitted'=>'secondary',
                                        'fee_paid'=>'primary',
                                        'appeared'=>'success',
                                        'absent'=>'danger',
                                        'result_declared'=>'info'
                                    ]; 
                                @endphp
                                <span class="badge bg-{{ $colors[$app->status->value] ?? 'secondary' }} text-{{ $colors[$app->status->value] ?? 'secondary' }}-fg text-capitalize">
                                    {{ str_replace('_',' ',$app->status->value) }}
                                </span>
                            </td>
                            <td class="text-secondary">{{ $app->applied_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-ghost-primary btn-icon btn-sm" title="Inspect">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No recent applications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Open Projects Overview -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold"><i class="ti ti-folder-check text-success me-2 fs-2 align-text-bottom"></i> Active Projects</h3>
            </div>
            <div class="list-group list-group-flush list-group-hoverable">
                @forelse($openProjects as $project)
                <div class="list-group-item px-3 py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-success"></span>
                        </div>
                        <div class="col text-truncate">
                            <a href="{{ route('admin.projects.show', $project) }}" class="text-body d-block fw-semibold text-truncate">{{ $project->name }}</a>
                            <div class="text-secondary small d-block mt-n1 text-truncate">
                                Org: {{ $project->org_name }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="text-secondary small">
                                Closes: <br>
                                <strong class="text-dark">{{ $project->close_date ? $project->close_date->format('d M') : 'Open' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-secondary py-4">There are currently no active projects.</div>
                @endforelse
            </div>
            <div class="card-footer bg-transparent border-0 text-center">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost-primary btn-sm w-100">View Project Directory</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
<style>
    .jvm-container { background: transparent !important; font-family: inherit; }
    .jvm-tooltip { 
        background: #ffffff !important; 
        border: none !important; 
        border-radius: 8px !important; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        color: #1e293b !important;
        padding: 0 !important;
        font-family: inherit;
    }
    .jvm-zoom-btn { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="{{ asset('assets/maps/pakistan_official.js') }}"></script>
<script src="{{ asset('assets/vendor/js/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const regionalData = @json($regionalStats);

        const buildStats = (dataObj) => {
            if (!dataObj) return {};
            const result = {};
            Object.entries(dataObj).forEach(([code, val]) => {
                // Keep the exact case for jsVectorMap matching (PK-PB, PK-SD, etc)
                result[code] = parseInt(val) || 0;
            });
            ['PK-JK', 'PK-GB', 'PK-II'].forEach(k => { if (!(k in result)) result[k] = 0; });
            return result;
        };
        const stats = { total: buildStats(regionalData.total), verified: buildStats(regionalData.verified_paid) };

        const mapContainer = document.querySelector("#pakistan-map");
        if (mapContainer) {
            const map = new jsVectorMap({
                selector: "#pakistan-map",
                map: "pakistan_official",
                showTooltip: true,
                zoomOnScroll: false,
                zoomButtons: false,
                regionStyle: {
                    initial: { 
                        fill: '#f1f5f9', 
                        stroke: '#ffffff', 
                        strokeWidth: 1.5,
                        fillOpacity: 1
                    },
                    hover: { 
                        fillOpacity: 0.85,
                        stroke: '#3b82f6',
                        strokeWidth: 2
                    }
                },
                series: {
                    regions: [{ 
                        attribute: 'fill', 
                        scale: ['#dbeafe', '#1e5bb0'], 
                        values: stats.total, 
                        min: 0 
                    }]
                },
                onRegionTooltipShow(event, tooltip, code) {
                    if (!code.startsWith('PK')) {
                        event.preventDefault();
                        return;
                    }
                    
                    const name = tooltip.text();
                    const totalApps = (stats.total[code] || 0).toLocaleString();
                    const verifiedApps = (stats.verified[code] || 0).toLocaleString();
                    
                    tooltip.text(
                        `<div class="p-3" style="min-width: 180px;">
                            <div class="fw-bold fs-3 border-bottom pb-2 mb-2 text-dark">${name}</div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary small">Total Applied:</span>
                                <span class="text-dark fw-bold">${totalApps}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary small">Verified Paid:</span>
                                <span class="text-success fw-bold">${verifiedApps}</span>
                            </div>
                        </div>`, true
                    );
                }
            });

            document.querySelectorAll('input[name="heatmap-view"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const colorScale = this.value === 'total' ? ['#dbeafe', '#1e5bb0'] : ['#dcfce7', '#2fb344'];
                    map.updateSeries({ 
                        regions: [{ 
                            scale: colorScale, 
                            values: stats[this.value === 'total' ? 'total' : 'verified'] 
                        }] 
                    });
                });
            });
        }

        // Financial ROI Chart (ApexCharts)
        const roiEl = document.querySelector("#chart-roi");
        if (roiEl && typeof ApexCharts !== 'undefined') {
            new ApexCharts(roiEl, {
                series: [{ name: 'Revenue', data: @json($projectRoi->pluck('revenue')) }, { name: 'Expense', data: @json($projectRoi->pluck('expense')) }],
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
                colors: ['#206bc4', '#d63939'],
                xaxis: { categories: @json($projectRoi->pluck('name')->map(fn($n) => Str::limit($n, 12))) },
                yaxis: { labels: { formatter: (val) => "PKR " + (val/1000).toFixed(0) + "k" } },
                tooltip: { y: { formatter: (val) => "PKR " + val.toLocaleString() } }
            }).render();
        }
    });
</script>
@endpush

@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('page-title', 'Overview & Intelligence')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-md shadow-sm border-0 bg-primary text-primary-fg">
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
    @foreach($dashboardCards as $c)
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
                    <div class="col-md-7 position-relative">
                        <div id="pakistan-map" style="height: 380px;">
                            <svg id="pk-svg-map" viewBox="0 0 900 900" preserveAspectRatio="xMidYMid meet" 
                                 style="width:100%;height:100%;" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <filter id="shadow" x="-5%" y="-5%" width="110%" height="110%">
                                        <feDropShadow dx="0" dy="1" stdDeviation="2" flood-opacity="0.15"/>
                                    </filter>
                                </defs>
                                <g id="pk-provinces" filter="url(#shadow)"></g>
                            </svg>
                        </div>
                        <!-- Floating tooltip -->
                        <div id="pk-map-tooltip" class="position-absolute shadow-lg rounded-3 bg-white border" 
                             style="display:none; z-index:10; pointer-events:none; min-width:170px;">
                        </div>
                    </div>
                    <div class="col-md-5 border-start">
                        <div class="mb-3">
                            <h4 class="small fw-bold text-secondary mb-3">TOP RECRUITMENT HUBS</h4>
                            @foreach($cityDistribution->take(5) as $city)

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold text-dark">{{ $city['city'] }}</span>
                                        <span class="text-secondary small">{{ number_format($city['count']) }}</span>
                                    </div>
                                    <div class="progress progress-sm rounded-pill">
                                        <div class="progress-bar bg-primary" style="width: {{ $city['pct'] }}%"></div>
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

                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="text-secondary small text-uppercase fw-bold">Avg. Margin</div>
                            <div class="fs-3 fw-bold text-success">{{ round($margin, 1) }}%</div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary small text-uppercase fw-bold">Net Surplus</div>
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
                                <span class="badge bg-{{ $statusColors[$app->status->value] ?? 'secondary' }} text-white text-capitalize">
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
<style>
    #pk-provinces path {
        stroke: #ffffff;
        stroke-width: 1.5;
        stroke-linejoin: round;
        cursor: pointer;
        transition: fill 0.3s ease, stroke 0.2s ease, stroke-width 0.2s ease;
    }
    #pk-provinces path:hover {
        stroke: #3b82f6;
        stroke-width: 2.5;
        filter: brightness(1.08);
    }
    #pk-provinces path.disputed {
        stroke: #94a3b8;
        stroke-width: 1.2;
        stroke-dasharray: 6 3;
        fill-opacity: 0.5;
    }
    #pk-provinces path.disputed:hover {
        stroke: #ef4444;
        stroke-width: 2;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/js/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── Province SVG Data (generated from Highcharts GeoJSON) ──
        const provinceData = @json(json_decode(file_get_contents(public_path('assets/maps/pakistan_svg_data.json'))));

        // ── Regional Stats from Controller ──
        const regionalData = @json($regionalStats);
        const buildStats = (dataObj) => {
            if (!dataObj) return {};
            const result = {};
            Object.entries(dataObj).forEach(([code, val]) => {
                result[code] = parseInt(val) || 0;
            });
            return result;
        };
        const stats = { total: buildStats(regionalData.total), verified: buildStats(regionalData.verified_paid) };

        // ── Color Interpolation ──
        function interpolateColor(value, max, palette) {
            if (max === 0) return palette[0];
            const t = Math.min(value / max, 1);
            // Parse hex colors
            const c1 = palette[0].match(/\w\w/g).map(x => parseInt(x, 16));
            const c2 = palette[1].match(/\w\w/g).map(x => parseInt(x, 16));
            const r = Math.round(c1[0] + (c2[0] - c1[0]) * t);
            const g = Math.round(c1[1] + (c2[1] - c1[1]) * t);
            const b = Math.round(c1[2] + (c2[2] - c1[2]) * t);
            return `rgb(${r},${g},${b})`;
        }

        let currentMetric = 'total';
        const palettes = {
            total:    ['#dbeafe', '#1e40af'],
            verified: ['#dcfce7', '#15803d']
        };

        // ── Render SVG Provinces ──
        const group = document.getElementById('pk-provinces');
        const tooltip = document.getElementById('pk-map-tooltip');
        const mapContainer = document.getElementById('pakistan-map');

        if (group && provinceData) {
            const maxVal = (metric) => Math.max(1, ...Object.values(stats[metric]));

            function renderMap() {
                const max = maxVal(currentMetric);
                const palette = palettes[currentMetric];
                
                group.innerHTML = '';
                provinceData.forEach(prov => {
                    const isDisputed = prov.disputed || false;
                    const val = stats[currentMetric][prov.code] || 0;
                    const fill = isDisputed ? '#f1f5f9' : interpolateColor(val, max, palette);
                    
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    path.setAttribute('d', prov.path);
                    path.setAttribute('fill', fill);
                    path.setAttribute('data-code', prov.code);
                    path.setAttribute('data-name', prov.name);
                    if (isDisputed) path.classList.add('disputed');
                    
                    // Tooltip events
                    path.addEventListener('mouseenter', function(e) {
                        const totalV = (stats.total[prov.code] || 0).toLocaleString();
                        const paidV = (stats.verified[prov.code] || 0).toLocaleString();
                        const disputedBadge = isDisputed ? '<span class="badge bg-danger-lt text-danger mb-2" style="font-size:0.65rem;">Illegally Indian Occupied</span>' : '';
                        tooltip.innerHTML = `
                            <div class="p-3">
                                <div class="fw-bold border-bottom pb-2 mb-2 text-dark" style="font-size:0.95rem;">${prov.name}</div>
                                ${disputedBadge}
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-secondary" style="font-size:0.8rem;">Total Applied:</span>
                                    <span class="text-dark fw-bold" style="font-size:0.8rem;">${totalV}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary" style="font-size:0.8rem;">Verified Paid:</span>
                                    <span class="text-success fw-bold" style="font-size:0.8rem;">${paidV}</span>
                                </div>
                            </div>
                        `;
                        tooltip.style.display = 'block';
                    });
                    
                    path.addEventListener('mousemove', function(e) {
                        const rect = mapContainer.getBoundingClientRect();
                        tooltip.style.left = (e.clientX - rect.left + 12) + 'px';
                        tooltip.style.top = (e.clientY - rect.top - 10) + 'px';
                    });
                    
                    path.addEventListener('mouseleave', function() {
                        tooltip.style.display = 'none';
                    });
                    
                    group.appendChild(path);
                });
            }

            renderMap();

            // Toggle Total / Paid
            document.querySelectorAll('input[name="heatmap-view"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    currentMetric = this.value === 'total' ? 'total' : 'verified';
                    renderMap();
                });
            });
        }

        // ── Financial ROI Chart (ApexCharts) ──
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


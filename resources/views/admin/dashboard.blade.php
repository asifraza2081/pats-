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
                    <div class="col-auto">
                        <i class="ti ti-dashboard text-white opacity-50 pe-3" style="font-size: 4rem;"></i>
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
        ['label'=>'Pending Payments',  'value'=>$stats['pending_pay'], 'icon'=>'clock-hourglass', 'color'=>'warning'],
        ['label'=>'Verified Payments', 'value'=>$stats['verified_pay'], 'icon'=>'cash',            'color'=>'success'],
        ['label'=>'Appeared in Test',  'value'=>$stats['appeared'],    'icon'=>'user-check',      'color'=>'secondary'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-{{ $c['color'] }} text-white avatar avatar-md shadow-sm">
                            <i class="ti ti-{{ $c['icon'] }} fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-4 text-dark mb-1">
                            {{ $c['label'] }}
                        </div>
                        <div class="text-secondary small">
                            Recorded {{ strtolower(explode(' ', $c['label'])[1] ?? 'items') }} in system
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="fw-bolder fs-1 text-primary">{{ number_format($c['value']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row row-cards">
    <!-- Recent Applications -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold"><i class="ti ti-history text-primary me-2 fs-2 align-text-bottom"></i> Recent Applications</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-primary d-none d-sm-inline-block">View All</a>
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
                                    <span class="avatar me-2" style="background-image: url('{{ $app->candidate->photo_path ? Storage::url($app->candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($app->candidate->user->first_name) }}')"></span>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">{{ $app->candidate->user->full_name }}</div>
                                        <div class="text-secondary small">{{ $app->candidate->user->cnic }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-body">{{ Str::limit($app->job->title, 30) }}</div>
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
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-outline-secondary btn-sm">Inspect</a>
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
                <div class="card-actions">
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-success d-none d-sm-inline-block">Directory</a>
                </div>
            </div>
            <div class="list-group list-group-flush list-group-hoverable">
                @forelse($openProjects as $project)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-success"></span>
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
        </div>
    </div>
</div>
@endsection

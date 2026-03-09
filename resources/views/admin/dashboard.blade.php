@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats Row --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Projects',        'value'=>$stats['projects'],    'icon'=>'folder2',          'color'=>'primary'],
        ['label'=>'Job Posts',       'value'=>$stats['jobs'],         'icon'=>'briefcase',        'color'=>'info'],
        ['label'=>'Applications',    'value'=>$stats['applications'], 'icon'=>'file-earmark-text','color'=>'dark'],
        ['label'=>'Pending Payment', 'value'=>$stats['pending_pay'],  'icon'=>'hourglass-split',  'color'=>'warning'],
        ['label'=>'Payment Verified','value'=>$stats['verified_pay'], 'icon'=>'cash-stack',       'color'=>'success'],
        ['label'=>'Appeared',        'value'=>$stats['appeared'],     'icon'=>'person-check',     'color'=>'secondary'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card shadow-sm border-0 rounded-4 p-3 text-center">
            <i class="bi bi-{{ $c['icon'] }} fs-2 mb-2 text-{{ $c['color'] }}"></i>
            <div class="fw-bold fs-4">{{ $c['value'] }}</div>
            <div class="text-muted small">{{ $c['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Recent Applications --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Applications</h6>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th class="px-4 py-3">Candidate</th>
                                <th>Post</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApps as $app)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold small">{{ $app->candidate->user->full_name }}</div>
                                    <div class="text-muted" style="font-size:.75rem">{{ $app->candidate->user->cnic }}</div>
                                </td>
                                <td class="small">{{ Str::limit($app->job->title, 25) }}</td>
                                <td>
                                    @php $colors=['submitted'=>'secondary','fee_paid'=>'primary','appeared'=>'success','absent'=>'danger','result_declared'=>'purple']; @endphp
                                    <span class="badge bg-{{ $colors[$app->status] ?? 'secondary' }} text-capitalize">
                                        {{ str_replace('_',' ',$app->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $app->applied_at->format('d M Y') }}</td>
                                <td><a href="{{ route('admin.applications.show',$app) }}" class="btn btn-xs btn-outline-secondary px-2 py-1">View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No applications yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Open Projects --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-folder2-open me-2 text-success"></i>Open Projects</h6>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-success">All</a>
            </div>
            <ul class="list-group list-group-flush rounded-bottom-4">
                @forelse($openProjects as $project)
                <li class="list-group-item px-4 py-3">
                    <div class="fw-semibold small">{{ $project->name }}</div>
                    <div class="text-muted" style="font-size:.75rem">Closes: {{ $project->close_date?->format('d M Y') ?? '—' }}</div>
                    <a href="{{ route('admin.projects.show',$project) }}" class="stretched-link"></a>
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-3">No open projects</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Current Opportunities — PATS')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold mb-1" style="color:var(--pats-primary)">Current Opportunities</h1>
    <p class="text-muted mb-4">All open recruitment projects. Click a project to see available posts.</p>
    @if($projects->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-hourglass fs-1 d-block mb-3"></i>
        <p>No open projects at the moment. Check back soon.</p>
    </div>
    @else
    <div class="row g-4">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">{{ $project->name }}</h5>
                    <div class="text-muted small mb-3">{{ $project->org_name }}</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-success">Open</span>
                        @if($project->close_date)
                        <span class="badge" style="background:#fff3e0;color:#e65100">Closes {{ $project->close_date->format('d M Y') }}</span>
                        @endif
                        <span class="badge bg-light text-dark">{{ $project->jobs_count }} Post{{ $project->jobs_count !== 1 ? 's' : '' }}</span>
                    </div>
                    @if($project->description)
                    <p class="small text-muted">{{ Str::limit($project->description, 120) }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                    <a href="{{ route('projects.show',$project) }}" class="btn btn-pats w-100 fw-semibold">
                        <i class="bi bi-arrow-right me-1"></i>View Posts
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

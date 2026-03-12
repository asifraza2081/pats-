@extends('layouts.public')
@section('title', 'Current Opportunities — PATS')
@section('header-title', 'Current Opportunities')

@section('content')
<div class="container-xl py-5">
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title fw-bold" style="color:var(--pats-primary)">Current Opportunities</h2>
                <div class="text-muted mt-1">All open recruitment projects. Select a project to view available posts.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($projects->isEmpty())
    <div class="empty">
        <div class="empty-icon">
            <i class="ti ti-hourglass-empty text-muted"></i>
        </div>
        <p class="empty-title">No projects found</p>
        <p class="empty-subtitle text-muted">
            There are currently no active recruitment projects. Please check back later.
        </p>
    </div>
    @else
    <div class="row row-cards">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-rounded bg-primary-lt">
                            <i class="ti ti-briefcase text-primary fs-3"></i>
                        </span>
                        <div class="ms-3">
                            <h3 class="card-title mb-0 fw-bold">{{ $project->name }}</h3>
                            <div class="text-muted small">{{ $project->org_name }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-success text-success-fg">Active</span>
                        @if($project->close_date)
                            <span class="badge {{ $project->close_date->isPast() ? 'bg-danger text-danger-fg' : 'bg-orange text-orange-fg' }}">
                                <i class="ti ti-calendar-clock me-1"></i> Closes {{ $project->close_date->format('d M Y') }}
                            </span>
                        @endif
                        <span class="badge bg-secondary text-secondary-fg">
                            <i class="ti ti-users me-1"></i> {{ $project->jobs_count }} Post{{ $project->jobs_count !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    @if($project->description)
                        <p class="text-muted small mb-0">{{ Str::limit($project->description, 120) }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent p-3">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-primary w-100">
                        View Complete Details <i class="ti ti-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

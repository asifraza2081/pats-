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
    <div class="row row-cards g-4">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4">
            <div class="card card-nts h-100 border-0 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-rounded bg-teal-lt p-3">
                            <i class="ti ti-briefcase text-teal fs-2"></i>
                        </span>
                        <div class="ms-3">
                            <h3 class="h3 mb-0 fw-extrabold text-dark">{{ $project->name }}</h3>
                            <div class="text-muted small fw-medium">{{ $project->org_name }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-green-lt text-green border-0">ACTIVE</span>
                        @if($project->close_date)
                            <span class="badge {{ $project->close_date->isPast() ? 'bg-red-lt text-red' : 'bg-orange-lt text-orange' }} border-0">
                                <i class="ti ti-calendar-clock me-1"></i> Closes {{ $project->close_date->format('d M') }}
                            </span>
                        @endif
                    </div>

                    @if($project->description)
                        <p class="text-muted small mb-0 line-clamp-3">{{ $project->description }}</p>
                    @endif
                </div>
                <div class="card-footer bg-light border-0 p-4">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-teal w-100 fw-bold text-white shadow-sm border-0">
                        View Details / Apply <i class="ti ti-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

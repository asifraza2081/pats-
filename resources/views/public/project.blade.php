@extends('layouts.app')
@section('title', $project->name . ' — PATS')

@section('content')
<div class="container py-5">
    {{-- Project Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="row g-0">
            <div class="col-md-9 p-4">
                <div class="small text-muted mb-1">{{ $project->org_name }}</div>
                <h2 class="fw-bold mb-2" style="color:var(--pats-primary)">{{ $project->name }}</h2>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-success">{{ ucfirst($project->status) }}</span>
                    @if($project->close_date)
                    <span class="badge bg-warning text-dark"><i class="bi bi-calendar me-1"></i>Closes: {{ $project->close_date->format('d M Y') }}</span>
                    @endif
                    @if($project->test_date)
                    <span class="badge bg-info text-dark"><i class="bi bi-calendar-check me-1"></i>Test: {{ $project->test_date->format('d M Y') }}</span>
                    @endif
                </div>
                @if($project->description)
                <p class="text-muted">{{ $project->description }}</p>
                @endif
            </div>
            @if($project->logo_path)
            <div class="col-md-3 d-flex align-items-center justify-content-center p-3 bg-light">
                <img src="{{ asset('storage/'.$project->logo_path) }}" style="max-height:100px;max-width:150px">
            </div>
            @endif
        </div>
    </div>

    {{-- Job Posts --}}
    <h4 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-primary"></i>Available Posts</h4>
    @if($project->jobs->isEmpty())
    <p class="text-muted">No posts available for this project yet.</p>
    @else
    <div class="row g-3">
        @foreach($project->jobs as $job)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-secondary">{{ str_pad($job->job_code,2,'0',STR_PAD_LEFT) }}</span>
                                <h6 class="mb-0 fw-bold">{{ $job->title }}</h6>
                            </div>
                            @if($job->department)<div class="text-muted small">{{ $job->department }}</div>@endif
                            @if($job->bps_grade)<div class="text-muted small"><i class="bi bi-tag me-1"></i>{{ $job->bps_grade }}</div>@endif
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-wrap gap-2 small text-muted">
                                <span><i class="bi bi-people me-1"></i>{{ $job->total_seats }} seats</span>
                                @if($job->age_min || $job->age_max)
                                <span><i class="bi bi-person me-1"></i>{{ $job->age_min }}–{{ $job->age_max }} yrs</span>
                                @endif
                                @if($job->min_degree_level)
                                <span><i class="bi bi-mortarboard me-1"></i>{{ \App\Models\EducationHistory::$levelLabels[$job->min_degree_level] ?? '' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2 text-md-end d-flex flex-column gap-2">
                            <span class="fw-bold text-primary">PKR {{ number_format($job->fee) }}</span>
                            @auth
                            @if(auth()->user()->hasRole('candidate'))
                            <a href="{{ route('candidate.apply',$job) }}" class="btn btn-pats btn-sm fw-semibold">Apply Now</a>
                            @endif
                            @else
                            <a href="{{ route('auth.login') }}" class="btn btn-outline-primary btn-sm">Login to Apply</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

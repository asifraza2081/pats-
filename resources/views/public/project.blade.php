@extends('layouts.public')
@section('title', $project->name)
@section('meta_description', 'Apply for ' . $project->name . ' by ' . $project->org_name . '. View eligibility, available positions, and application deadlines on PATS.')
@section('meta_keywords', $project->name . ', ' . $project->org_name . ', government jobs Pakistan, PATS recruitment, ' . $project->org_name . ' jobs')
@section('header-title', 'Project Details')
@section('header-breadcrumb')
    <a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a> 
    <span class="mx-2">/</span> 
    <a href="{{ route('projects') }}" class="text-white text-decoration-none">Projects</a>
    <span class="mx-2">/</span>
    <span class="text-white-50">{{ $project->org_name }}</span>
@endsection

@section('content')
<div class="container-xl py-5">
    
    <!-- Page Header -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle text-muted fw-bold tracking-wide text-uppercase">
                    {{ $project->org_name }}
                </div>
                <h2 class="page-title text-pats-primary fw-bold fs-1 mt-1">
                    {{ $project->name }}
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('projects') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <i class="ti ti-arrow-left me-2"></i> Back to Projects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-12 col-md-9">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-success text-success-fg">Status: {{ ucfirst($project->status->value) }}</span>
                        
                        @if($project->close_date)
                            <span class="badge {{ $project->close_date->isPast() ? 'bg-danger text-danger-fg' : 'bg-warning text-dark' }}">
                                <i class="ti ti-calendar-time me-1"></i> Application Deadline: {{ $project->close_date->format('d M Y') }}
                            </span>
                        @endif
                        
                        @if($project->test_date)
                            <span class="badge bg-info text-info-fg">
                                <i class="ti ti-calendar-event me-1"></i> Tentative Test: {{ $project->test_date->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                    
                    @if($project->description)
                        <div class="text-secondary markdown">
                            <p>{{ $project->description }}</p>
                        </div>
                    @endif
                </div>
                
                @if($project->logo_path)
                <div class="col-12 col-md-3 mt-3 mt-md-0 border-start-md px-md-4 d-flex justify-content-center align-items-center">
                    <img src="{{ asset('storage/'.$project->logo_path) }}" alt="{{ $project->org_name }} Logo" class="img-fluid rounded shadow-sm" style="max-height: 120px;" loading="lazy" decoding="async">
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Job Posts -->
    <div class="d-flex align-items-center mb-3 mt-5">
        <h3 class="m-0 fw-bold"><i class="ti ti-briefcase text-pats-primary me-2"></i> Available Positions</h3>
        <span class="badge bg-secondary ms-2 rounded-pill">{{ $project->jobs->count() }}</span>
    </div>

    @if($project->jobs->isEmpty())
        <div class="empty bg-white rounded border">
            <div class="empty-icon">
                <i class="ti ti-search text-muted"></i>
            </div>
            <p class="empty-title">No positions currently open</p>
            <p class="empty-subtitle text-muted">
                There are no open job positions available for this project at the moment. Please check back later.
            </p>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush list-group-hoverable">
                @foreach($project->jobs as $job)
                <div class="list-group-item py-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-secondary-lt me-2 tracking-wide fw-bold">POST-{{ str_pad($job->job_code, 2, '0', STR_PAD_LEFT) }}</span>
                                <h4 class="m-0 fw-bold text-dark fs-3">{{ $job->title }}</h4>
                            </div>
                            <div class="text-secondary small d-flex flex-wrap gap-3">
                                @if($job->department)
                                    <span><i class="ti ti-building me-1"></i> {{ $job->department }}</span>
                                @endif
                                @if($job->bps_grade)
                                    <span><i class="ti ti-rosette me-1"></i> BPS-{{ $job->bps_grade }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column gap-2 text-secondary small">
                                <span><i class="ti ti-users me-2 text-muted"></i> <strong>{{ $job->total_seats }}</strong> alloc. seats</span>
                                @if($job->age_min || $job->age_max)
                                    <span><i class="ti ti-calendar-user me-2 text-muted"></i> <strong>{{ $job->age_min }} &ndash; {{ $job->age_max }}</strong> years limit</span>
                                @endif
                                @if($job->min_degree_level)
                                    <span><i class="ti ti-certificate me-2 text-muted"></i> <strong>{{ \App\Models\EducationHistory::$levelLabels[$job->min_degree_level] ?? '' }}</strong> min. req.</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3 text-end d-flex flex-column align-items-end justify-content-center border-start ps-3">
                            <div class="text-muted small mb-1">Application Fee (PKR)</div>
                            <div class="fs-2 fw-bold text-dark mb-3">Rs. {{ number_format($job->fee) }}</div>
                            
                            @auth
                                @if(auth()->user()->hasRole('candidate'))
                                    <a href="{{ route('candidate.apply', $job) }}" class="btn btn-primary fw-bold px-4">
                                        <i class="ti ti-send me-2"></i> Apply Now
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary fw-bold">
                                    Login to Apply
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.public')
@section('title', 'Open Projects — Prime Assessment & Testing Services')
@section('header-title', 'Available Projects')

@section('content')
<div class="container-xl py-5">
    <div class="row align-items-center mb-6 animate__animated animate__fadeIn">
        <div class="col">
            <div class="badge bg-teal-lt text-teal px-4 py-2 mb-3 rounded-pill fw-black">RECRUITMENT PORTAL</div>
            <h2 class="display-4 fw-black text-dark mb-1">Open Opportunities</h2>
            <p class="text-muted fs-3 opacity-80">Explore active projects and apply for positions that match your profile.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('home') }}" class="btn glass-panel text-dark rounded-pill px-4 fw-black border-0 shadow-sm">
                <i class="ti ti-arrow-left me-2"></i> Home
            </a>
        </div>
    </div>

    @if($projects->isEmpty())
    <div class="card glass-panel border-0 py-8 text-center rounded-5 animate__animated animate__pulse">
        <div class="avatar avatar-xl bg-light text-muted mb-4 mx-auto rounded-circle">
            <i class="ti ti-hourglass-empty fs-0"></i>
        </div>
        <h3 class="text-dark fw-black h2">No Active Projects</h3>
        <p class="text-muted fs-4 max-w-md mx-auto">We are currently finalizing new recruitment cycles. Please check back soon or follow our social media for announcements.</p>
    </div>
    @else
    <div class="row row-cards g-5">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4">
            <div class="card card-pats h-100 border-0 overflow-hidden shadow-lg animate__animated animate__fadeInUp">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-grad-accent text-white p-3 rounded-4 me-3 shadow-teal-30">
                            <i class="ti ti-briefcase fs-1"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="h3 mb-0 fw-black text-dark text-truncate">{{ $project->org_name }}</h3>
                            <div class="text-teal small fw-bold">Active Project</div>
                        </div>
                    </div>
                    
                    <h3 class="fw-black text-dark h2 mb-3 lh-sm">{{ $project->name }}</h3>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-green-lt text-green border-0 rounded-pill px-3 fw-bold">OPEN</span>
                        @if($project->close_date)
                            <span class="badge {{ $project->close_date->isPast() ? 'bg-red-lt text-red' : 'bg-orange-lt text-orange' }} border-0 rounded-pill px-3 fw-bold">
                                <i class="ti ti-calendar-clock me-1"></i> Closes {{ $project->close_date->format('d M, Y') }}
                            </span>
                        @endif
                    </div>

                    @if($project->description)
                        <p class="text-muted fs-4 mb-0 line-clamp-3 opacity-80">{{ $project->description }}</p>
                    @endif
                </div>
                <div class="card-footer bg-light border-0 p-5 pt-0">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-teal w-100 fw-black text-white shadow-teal-30 border-0 rounded-pill py-3">
                        VIEW & APPLY <i class="ti ti-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

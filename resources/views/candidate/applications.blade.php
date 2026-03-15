@extends('layouts.dashboard')
@section('page-title', 'My Applications')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <h2 class="mb-4">My Submitted Applications</h2>

        @if($applications->isEmpty())
        <div class="empty bg-white rounded border">
            <div class="empty-icon">
                <i class="ti ti-folder-off text-muted"></i>
            </div>
            <p class="empty-title">No applications found</p>
            <p class="empty-subtitle text-muted">
                You haven't applied for any positions yet. Browse open opportunities to get started.
            </p>
            <div class="empty-action">
                <a href="{{ route('projects') }}" class="btn btn-primary">
                    <i class="ti ti-search me-2"></i> Browse Opportunities
                </a>
            </div>
        </div>
        @else
        <div class="d-flex flex-column gap-3">
            @foreach($applications as $app)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="text-muted small mb-1"><i class="ti ti-building me-1"></i> {{ $app->job->project->org_name }}</div>
                            <h3 class="m-0 fw-bold fs-3 text-pats-primary">{{ $app->job->title }}</h3>
                            
                            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                                @if($app->job->bps_grade)
                                <span><i class="ti ti-rosette me-1"></i> BPS-{{ $app->job->bps_grade }}</span>
                                @endif
                                @if($app->examRollno)
                                <span><i class="ti ti-building-bank me-1"></i> {{ $app->examRollno->center->name ?? 'N/A' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="text-muted small mb-1">Application Status</div>
                            <span class="badge bg-{{ $app->status->color() }} text-{{ $app->status->color() }}-fg px-3 py-2 text-uppercase tracking-wide fs-5">
                                {{ $app->status->label() }}
                            </span>
                            
                            @if($app->status === \App\Enums\ApplicationStatus::SCHEDULED && !$app->examRollno)
                            <div class="text-info small mt-1"><i class="ti ti-hourglass-empty me-1"></i> Assigning center...</div>
                            @endif
                        </div>

                        <div class="col-md-3 d-flex flex-column gap-2 text-md-end">
                            <a href="{{ route('candidate.applications.show', $app) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-eye me-1"></i> View Details
                            </a>
                            
                            @if($app->status === \App\Enums\ApplicationStatus::SUBMITTED && $app->payment)
                            <a href="{{ route('candidate.challan', $app) }}" class="btn btn-warning btn-sm" target="_blank">
                                <i class="ti ti-download me-1"></i> Download Challan
                            </a>
                            @endif
                            
                            @if($app->examRollno && $app->examRollno->roll_no)
                            <a href="{{ route('candidate.slip', $app) }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="ti ti-ticket me-1"></i> Roll No Slip
                            </a>
                            @endif
                            
                            @if($app->result?->isPublished())
                            <a href="{{ route('candidate.result', $app) }}" class="btn btn-info btn-sm">
                                <i class="ti ti-chart-bar me-1"></i> View Result
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($app->examRollno && $app->examRollno->test_date)
                <div class="card-footer bg-transparent py-3">
                    <div class="row align-items-center small text-muted">
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <i class="ti ti-calendar text-success me-1"></i> Test Date: <strong class="text-dark">{{ \Carbon\Carbon::parse($app->examRollno->test_date)->format('d M Y') }}</strong>
                        </div>
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <i class="ti ti-clock text-warning me-1"></i> Reporting Time: <strong class="text-dark">{{ \Carbon\Carbon::parse($app->examRollno->reporting_time)->format('h:i A') }}</strong>
                        </div>
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <i class="ti ti-map-pin text-primary me-1"></i> City: <strong class="text-dark">{{ $app->examRollno->city->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-12 col-md-auto ms-md-auto ms-auto">
                            Applied: {{ $app->applied_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        
        @if($applications->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $applications->links('pagination::bootstrap-5') }}
        </div>
        @endif
        
        @endif
    </div>
</div>
@endsection

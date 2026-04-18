@extends('layouts.dashboard')
@section('page-title', 'My Applications')

@section('content')
<div class="row row-cards">
    <div class="col-12 mb-2">
        <div class="card border-0 shadow-sm rounded-5 overflow-hidden text-white" style="background: linear-gradient(135deg, #0a3d62 0%, #174b76 100%);">
            <div class="card-body p-4 p-md-5 d-flex align-items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-circle me-4 d-none d-md-flex">
                    <i class="ti ti-folder fs-1 text-white"></i>
                </div>
                <div>
                    <h2 class="display-6 fw-black mb-1">My Applications</h2>
                    <p class="m-0 fs-3 opacity-75">Track the status of all your submitted job applications and download your slips.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">

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
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden transition-all hover-shadow-xl" style="border-left: 6px solid var(--pats-primary) !important;">
                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="row align-items-center g-4">
                        <div class="col-md-6">
                            <div class="opacity-75 small mb-2 fw-bold tracking-widest text-uppercase"><i class="ti ti-building me-1"></i> {{ $app->job->project->org_name }}</div>
                            <h3 class="m-0 fw-black fs-2 text-dark">{{ $app->job->title }}</h3>
                            
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                @if($app->job->bps_grade)
                                <span class="badge bg-light text-dark shadow-sm border border-secondary border-opacity-25 px-3 py-2 rounded-pill"><i class="ti ti-rosette me-1 text-primary"></i> BPS-{{ $app->job->bps_grade }}</span>
                                @endif
                                @if($app->examRollno)
                                <span class="badge bg-light text-dark shadow-sm border border-secondary border-opacity-25 px-3 py-2 rounded-pill"><i class="ti ti-building-bank me-1 text-primary"></i> {{ $app->examRollno->center->name ?? 'N/A' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 border-start border-md-0 ps-md-4">
                            <div class="text-muted small mb-2 fw-bold tracking-widest">CURRENT STATUS</div>
                            <span class="badge bg-{{ $app->status->color() }}-lt text-{{ $app->status->color() }} px-3 py-2 rounded-pill shadow-sm text-uppercase tracking-wide">
                                {{ $app->status->label() }}
                            </span>
                            
                            @if($app->status === \App\Enums\ApplicationStatus::SCHEDULED && !$app->examRollno)
                            <div class="text-info small mt-2 fw-bold"><i class="ti ti-loader ti-spin me-1"></i> Assigning center...</div>
                            @endif
                        </div>

                        <div class="col-md-3 d-flex flex-column gap-2 text-md-end border-start border-md-0 ps-md-4">
                            <a href="{{ route('candidate.applications.show', $app) }}" class="btn btn-outline-secondary rounded-pill shadow-sm fw-bold">
                                <i class="ti ti-eye me-1"></i> View Details
                            </a>
                            
                            @if($app->status === \App\Enums\ApplicationStatus::SUBMITTED && $app->payment)
                            <a href="{{ URL::patsDownload($app, 'challan') }}" class="btn btn-dark rounded-pill shadow-sm fw-bold" target="_blank">
                                <i class="ti ti-receipt me-1"></i> Download Challan
                            </a>
                            @endif
                            
                            @if($app->examRollno && $app->examRollno->roll_no)
                            <a href="{{ URL::patsDownload($app, 'slip') }}" class="btn btn-teal rounded-pill shadow-sm fw-bold" target="_blank">
                                <i class="ti ti-ticket me-1"></i> Roll No Slip
                            </a>
                            @endif
                            
                            @if($app->result?->isPublished())
                            <a href="{{ route('candidate.result', $app) }}" class="btn btn-green rounded-pill shadow-sm fw-bold">
                                <i class="ti ti-award me-1"></i> View Result Report
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($app->examRollno && $app->examRollno->batch?->test_date)
                <div class="card-footer bg-light bg-opacity-50 py-3 px-4 px-md-5 border-0 border-top">
                    <div class="row align-items-center small">
                        <div class="col-12 col-md-auto mb-2 mb-md-0 fw-bold">
                            <i class="ti ti-calendar text-primary me-1 fs-3"></i> Test Date: <strong class="text-dark">{{ \Carbon\Carbon::parse($app->examRollno->batch->test_date)->format('d M Y') }}</strong>
                        </div>
                        <div class="col-12 col-md-auto mb-2 mb-md-0 ms-md-4 fw-bold">
                            <i class="ti ti-clock text-warning me-1 fs-3"></i> Reporting Time: <strong class="text-dark">{{ $app->examRollno->batch->reporting_time ? \Carbon\Carbon::parse($app->examRollno->batch->reporting_time)->format('h:i A') : 'TBD' }}</strong>
                        </div>
                        <div class="col-12 col-md-auto mb-2 mb-md-0 ms-md-4 fw-bold">
                            <i class="ti ti-map-pin text-danger me-1 fs-3"></i> City: <strong class="text-dark">{{ $app->examRollno->city->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-12 col-md-auto ms-md-auto ms-auto opacity-75">
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

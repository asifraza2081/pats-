@extends('layouts.app')
@section('title', 'My Applications — PATS')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4">My Applications</h4>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    @if($applications->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
        <p>You haven't applied for any position yet.</p>
        <a href="{{ route('projects') }}" class="btn btn-pats"><i class="bi bi-briefcase me-1"></i>Browse Opportunities</a>
    </div>
    @else
    <div class="d-flex flex-column gap-3">
        @foreach($applications as $app)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">{{ $app->job->project->org_name }}</div>
                        <div class="fw-bold fs-6">{{ $app->job->title }}</div>
                        <div class="text-muted small mt-1">
                            @if($app->job->bps_grade)<span class="me-2"><i class="bi bi-tag me-1"></i>{{ $app->job->bps_grade }}</span>@endif
                            @if($app->batch)<span><i class="bi bi-building me-1"></i>{{ $app->batch->center->name }}</span>@endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        @php
                            $statusColors = [
                                'submitted'  => 'secondary',
                                'fee_paid'   => 'primary',
                                'processed'  => 'info',
                                'appeared'   => 'success',
                                'absent'     => 'danger',
                            ];
                        @endphp
                        <div class="fw-semibold small text-muted mb-1">Status</div>
                        <span class="badge bg-{{ $statusColors[$app->status] ?? 'secondary' }} text-capitalize fs-6 px-3 py-2">
                            {{ str_replace('_', ' ', $app->status) }}
                        </span>
                        @if($app->status === 'processed')
                        <div class="small text-info mt-1"><i class="bi bi-hourglass me-1"></i>Roll number slip being prepared…</div>
                        @endif
                    </div>
                    <div class="col-md-3 d-flex flex-column gap-2">
                        <a href="{{ route('candidate.applications.show', $app) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        @if($app->status === 'submitted' && $app->payment)
                        <a href="{{ route('candidate.challan', $app) }}" class="btn btn-warning btn-sm text-dark" target="_blank">
                            <i class="bi bi-download me-1"></i>Download Challan
                        </a>
                        @endif
                        @if($app->rollNumber?->slip_ready && !$app->batch?->hasStarted())
                        <a href="{{ route('candidate.slip', $app) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="bi bi-ticket-perforated me-1"></i>Roll Number Slip
                        </a>
                        @endif
                        @if($app->result?->isPublished())
                        <a href="{{ route('candidate.result', $app) }}" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-bar-chart me-1"></i>View Result
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @if($app->batch)
            <div class="card-footer bg-light border-0 px-4 py-2 d-flex flex-wrap gap-3 small text-muted">
                <span><i class="bi bi-calendar me-1"></i>Test Date: <strong>{{ $app->batch->test_date->format('d M Y') }}</strong></span>
                <span><i class="bi bi-clock me-1"></i>Reporting: <strong>{{ \Carbon\Carbon::parse($app->batch->reporting_time)->format('h:i A') }}</strong></span>
                <span><i class="bi bi-geo-alt me-1"></i>{{ $app->batch->center->city }}</span>
                <span class="ms-auto">Applied: {{ $app->applied_at->format('d M Y') }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @if($applications->hasPages())
    <div class="mt-4">{{ $applications->links() }}</div>
    @endif
    @endif
</div>
@endsection

@extends('layouts.public')
@section('title', $job->title . ' — ' . $project->name)

@section('content')
<div class="page page-center h-100">
    <div class="container container-tight py-5">
        <div class="mb-4">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i> Back to Project Postings
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <span class="avatar avatar-xl me-4 bg-pats-primary-lt" style="background: rgba(10, 61, 98, 0.1);">
                        <i class="ti ti-briefcase text-pats-primary fs-1"></i>
                    </span>
                    <div>
                        <div class="text-muted small mb-1 uppercase tracking-wide fw-semibold">{{ $project->org_name }}</div>
                        <h1 class="h1 fw-bold text-pats-primary mb-0">{{ $job->title }}</h1>
                        @if($job->bps_grade)
                            <span class="badge bg-secondary-lt fs-5 px-3 py-2 mt-2">BPS-{{ $job->bps_grade }}</span>
                        @endif
                    </div>
                </div>

                <div class="row row-cards mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 bg-light p-3 h-100">
                            <i class="ti ti-currency-dollar fs-2 text-success mb-2"></i>
                            <div class="text-muted small">Application Fee</div>
                            <div class="fw-bold fs-3">PKR {{ number_format($job->fee) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-light p-3 h-100">
                            <i class="ti ti-calendar-event fs-2 text-danger mb-2"></i>
                            <div class="text-muted small">Deadline</div>
                            <div class="fw-bold fs-3">{{ $project->close_date?->format('d M Y') ?? 'TBD' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-light p-3 h-100">
                            <i class="ti ti-users fs-2 text-primary mb-2"></i>
                            <div class="text-muted small">Vacancies</div>
                            <div class="fw-bold fs-3">{{ $job->vacancies ?? 'As per rules' }}</div>
                        </div>
                    </div>
                </div>

                <div class="hr-text fw-bold text-pats-primary">Application Requirements</div>

                <div class="datagrid mb-5">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Education Required</div>
                        <div class="datagrid-content fw-semibold">
                            @php
                                $levels = [1=>'Matric', 2=>'Intermediate', 3=>"Bachelor's", 4=>"Master's", 5=>'M.Phil', 6=>'PhD'];
                                echo $levels[$job->min_degree_level] ?? 'Minimum Required Qualification';
                            @endphp
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Minimum Age</div>
                        <div class="datagrid-content">{{ $job->age_min ?? '18' }} Years</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Maximum Age</div>
                        <div class="datagrid-content">{{ $job->age_max ?? '35' }} Years <span class="text-muted small">(Excl. relaxation)</span></div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Job Category</div>
                        <div class="datagrid-content">{{ $job->job_category ?? 'General Merit' }}</div>
                    </div>
                </div>

                <div class="alert alert-important alert-info rounded-3 mb-0 py-4 d-flex align-items-center">
                    <i class="ti ti-info-circle fs-1 me-3"></i>
                    <div>
                        <div class="fw-bold mb-1">Applying is simple through the PATS Portal!</div>
                        <div class="small">Ensure your profile is 100% complete and your original documents are ready before clicking Apply Now.</div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light py-4 text-center">
                @if($project->isRegistrationOpen())
                    <a href="{{ route('candidate.apply', $job) }}" class="btn btn-pats-primary btn-lg px-5 py-3 shadow">
                        <i class="ti ti-send me-2"></i> Apply for this position
                    </a>
                @else
                    <button class="btn btn-secondary btn-lg px-5 py-3 disabled" disabled>
                        <i class="ti ti-lock me-2"></i> Registration Closed
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-pats-primary-lt { background: rgba(10, 61, 98, 0.1); }
    .text-pats-primary { color: #0a3d62 !important; }
    .btn-pats-primary { background: #0a3d62; color: white !important; border: none; }
    .btn-pats-primary:hover { background: #062b46; transform: translateY(-2px); transition: all 0.2s; }
</style>
@endpush
@endsection

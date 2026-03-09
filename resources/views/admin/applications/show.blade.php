@extends('layouts.admin')

@section('title', 'Application Details')
@section('header', 'Application #'.$app->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Applications
    </a>
</div>

<div class="row g-4">
    <!-- Candidate Overview -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                @if($app->candidate->photo_path)
                    <img src="{{ asset('storage/'.$app->candidate->photo_path) }}" alt="Photo" class="rounded-circle mb-3 border" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 border" style="width: 120px; height: 120px;">
                        <i class="bi bi-person text-secondary fs-1"></i>
                    </div>
                @endif
                h5 class="mb-1">{{ $app->candidate->user->first_name }} {{ $app->candidate->user->last_name }}</h5>
                <p class="text-muted mb-2">{{ $app->candidate->user->cnic }}</p>
                
                @php
                    $bg = match($app->status) {
                        'submitted' => 'bg-secondary',
                        'fee_paid' => 'bg-info',
                        'appeared' => 'bg-primary',
                        'absent' => 'bg-danger',
                        'result_declared' => 'bg-success',
                        default => 'bg-dark'
                    };
                @endphp
                <span class="badge {{ $bg }} mb-3">{{ strtoupper(str_replace('_', ' ', $app->status)) }}</span>

                <div class="d-grid gap-2">
                    @if($app->rollNumber)
                        <a href="{{ route('rollnumbers.slip', $app) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> View Slip
                        </a>
                    @endif
                    @if($app->result)
                        <a href="{{ route('results.show', $app) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-trophy"></i> View Result
                        </a>
                    @endif
                </div>
            </div>
            <ul class="list-group list-group-flush border-top">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Phone:</span>
                    <strong>{{ $app->candidate->user->phone }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Father:</span>
                    <strong>{{ $app->candidate->father_name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">DOB:</span>
                    <strong>{{ $app->candidate->dob?->format('d M, Y') }} ({{ $app->candidate->age }} yrs)</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Domicile:</span>
                    <strong>{{ $app->candidate->district_of_domicile }}, {{ $app->candidate->province_of_domicile }}</strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- Application Details -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Job & Test Details</h6>
            </div>
            <div class="card-body">
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Project</label>
                        <h6 class="mb-0">{{ $app->job->project->name }}</h6>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Job Title</label>
                        <h6 class="mb-0">{{ $app->job->title }} ({{ $app->job->job_code }})</h6>
                    </div>
                </div>
                
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Test Center Priority 1</label>
                        <h6 class="mb-0">{{ $app->test_city_priority_1 }}</h6>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Assigned Batch</label>
                        @if($app->batch)
                            <h6 class="mb-0">Batch {{ $app->batch->batch_number }} <span class="text-muted small">({{ $app->batch->test_date->format('d M') }})</span></h6>
                            <small class="text-muted">{{ $app->batch->center->name }}, {{ $app->batch->center->city }}</small>
                        @else
                            <h6 class="mb-0 text-danger">Pending Assignment</h6>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Payment Status</label>
                        @if($app->payment)
                            <span class="badge bg-{{ $app->payment->status === 'paid' ? 'success' : 'warning text-dark' }}">
                                {{ strtoupper($app->payment->status) }}
                            </span>
                            @if($app->payment->status === 'paid')
                                <small class="d-block mt-1 text-muted">Verified on: {{ $app->payment->deposit_date?->format('d M, Y') }}</small>
                            @endif
                        @else
                            <span class="badge bg-danger">Not Generated</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted d-block small mb-1">Roll Number</label>
                        @if($app->rollNumber)
                            <h5 class="mb-0 text-primary">{{ $app->rollNumber->roll_number }}</h5>
                            @if($app->rollNumber->slip_ready)
                                <span class="badge bg-success mt-1">Slip Downloadable</span>
                            @else
                                <span class="badge bg-secondary mt-1">Pending Release</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Not Assigned</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($app->eligibility_warnings)
        <div class="alert alert-warning border-warning">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle"></i> Eligibility Warnings on Submission</h6>
            @php $warnings = json_decode($app->eligibility_warnings, true); @endphp
            @if(is_array($warnings) && count($warnings) > 0)
                <ul class="mb-0 mt-2">
                    @foreach($warnings as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            @else
                <p class="mb-0">No specific warnings recorded.</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

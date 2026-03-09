@extends('layouts.app')
@section('title', 'Dashboard — PATS')

@section('content')
<div class="container py-4">
    {{-- Welcome + Profile Completion --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background:linear-gradient(135deg,#0a3d62,#1a5276)">
                <div class="d-flex align-items-center gap-3">
                    @if($candidate?->photo_path)
                        <img src="{{ asset('storage/' . $candidate->photo_path) }}" class="rounded-circle" width="60" height="60" style="object-fit:cover;border:3px solid rgba(255,255,255,.3)">
                    @else
                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center" style="width:60px;height:60px;font-size:1.5rem;color:var(--pats-primary)">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    @endif
                    <div class="text-white">
                        <h5 class="fw-bold mb-0">Welcome, {{ $user->first_name }}!</h5>
                        <div class="small opacity-75">CNIC: {{ $user->cnic }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold mb-3">Profile Completion</h6>
                <div class="progress mb-2" style="height:10px">
                    <div class="progress-bar {{ $completion < 50 ? 'bg-danger' : ($completion < 100 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $completion }}%"></div>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">{{ $completion }}% complete</span>
                    @if($completion < 100)
                    <a href="{{ route('candidate.profile.show') }}" class="text-primary">Complete Profile</a>
                    @else
                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Complete</span>
                    @endif
                </div>
                @if($completion < 100)
                <div class="alert alert-warning mt-3 mb-0 small py-2">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Profile must be 100% to apply.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- My Applications --}}
    <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-2" style="color:var(--pats-accent)"></i>My Applications</h5>

    @if($applications->isEmpty())
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
        <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
        <p>No applications yet.</p>
        <a href="{{ route('projects') }}" class="btn btn-pats">Browse Open Posts</a>
    </div>
    @else
    <div class="row g-3">
        @foreach($applications as $app)
        @php
            $statusColors = ['submitted'=>'secondary','fee_paid'=>'primary','appeared'=>'success','absent'=>'danger','result_declared'=>'purple'];
            $statusColor = $statusColors[$app->status] ?? 'secondary';
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-1">{{ $app->job->title }}</h6>
                            <div class="text-muted small">{{ $app->job->project->name }}</div>
                            <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Applied: {{ $app->applied_at->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-{{ $statusColor }} text-capitalize">
                                {{ str_replace('_', ' ', $app->status) }}
                            </span>
                            @if($app->rollNumber)
                            <div class="small mt-1 fw-bold text-primary">Roll No: {{ $app->rollNumber->roll_number }}</div>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end d-flex flex-wrap gap-2 justify-content-md-end">
                            @if($app->payment && $app->payment->status === 'pending' && $app->job->project->isRegistrationOpen())
                            <a href="{{ route('candidate.challan', $app) }}" class="btn btn-sm btn-outline-dark" target="_blank">
                                <i class="bi bi-receipt me-1"></i>Challan
                            </a>
                            @endif
                            @if($app->rollNumber?->slip_ready && !$app->batch->hasStarted())
                            <a href="{{ route('candidate.slip', $app) }}" class="btn btn-sm btn-pats" target="_blank">
                                <i class="bi bi-download me-1"></i>Roll Slip
                            </a>
                            @endif
                            @if($app->result?->isPublished())
                            <a href="{{ route('candidate.result', $app) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-bar-chart-line me-1"></i>Result
                            </a>
                            @endif
                            @if($app->payment && $app->payment->status === 'paid' && !$app->rollNumber)
                            <span class="badge bg-info text-dark small align-self-center">
                                <i class="bi bi-hourglass-split me-1"></i>Processing
                            </span>
                            @endif
                        </div>
                    </div>
                    @if(!empty($app->eligibility_warnings))
                    <div class="mt-2 p-2 rounded-3 bg-warning bg-opacity-10 border border-warning small">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                        <strong>Eligibility Warnings:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($app->eligibility_warnings as $w)<li>{{ $w }}</li>@endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('projects') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>Apply for Another Post
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'My Result — PATS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
    <div class="col-lg-7">

    <div class="mb-3">
        <a href="{{ route('candidate.applications.show', $app) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Application</a>
    </div>

    {{-- Result Card --}}
    <div class="card border-0 shadow rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="p-4 text-center" style="background:linear-gradient(135deg,#0a3d62,#1a5276);color:#fff">
            <div class="small text-white-50 mb-1">{{ $app->job->project->org_name }}</div>
            <h5 class="fw-bold mb-1">{{ $app->job->title }}</h5>
            <div class="small text-white-75">{{ $app->job->project->name }}</div>
        </div>

        <div class="card-body p-4">
            {{-- Status Badge --}}
            <div class="text-center mb-4">
                @php
                    $rc = ['pass'=>'success','fail'=>'danger','absent'=>'secondary','withheld'=>'warning'];
                    $rl = ['pass'=>'PASS','fail'=>'FAIL','absent'=>'ABSENT','withheld'=>'WITHHELD'];
                @endphp
                <span class="badge fs-4 px-5 py-3 bg-{{ $rc[$result->result_status] ?? 'secondary' }}">
                    {{ $rl[$result->result_status] ?? strtoupper($result->result_status) }}
                </span>
                @if($result->result_status === 'pass')
                <div class="mt-2 text-success fw-semibold small"><i class="bi bi-check-circle-fill me-1"></i>Congratulations!</div>
                @endif
            </div>

            {{-- Candidate Info --}}
            <div class="bg-light rounded-3 p-3 mb-4">
                <div class="row g-2 small">
                    <div class="col-6"><div class="text-muted">Candidate Name</div><div class="fw-bold">{{ auth()->user()->full_name }}</div></div>
                    <div class="col-6"><div class="text-muted">Roll Number</div><div class="fw-bold text-primary">{{ $result->roll_number }}</div></div>
                    <div class="col-6"><div class="text-muted">CNIC</div><div class="fw-semibold">{{ auth()->user()->cnic }}</div></div>
                    <div class="col-6"><div class="text-muted">Test Date</div><div>{{ $app->batch->test_date->format('d M Y') }}</div></div>
                    <div class="col-6"><div class="text-muted">Test Center</div><div>{{ $app->batch->center->name }}</div></div>
                    <div class="col-6"><div class="text-muted">City</div><div>{{ $app->batch->center->city }}</div></div>
                </div>
            </div>

            {{-- Score --}}
            <div class="row g-3 mb-4 text-center">
                <div class="col-4">
                    <div class="card border-0 bg-light rounded-3 p-3">
                        <div class="display-6 fw-bold text-primary">{{ number_format($result->score, 1) }}</div>
                        <div class="small text-muted">Score</div>
                        <div class="small text-muted">/ {{ number_format($result->total_marks, 1) }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 bg-light rounded-3 p-3">
                        <div class="display-6 fw-bold {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }}">{{ number_format($result->percentage, 1) }}%</div>
                        <div class="small text-muted">Percentage</div>
                    </div>
                </div>
                @if($result->percentile)
                <div class="col-4">
                    <div class="card border-0 bg-light rounded-3 p-3">
                        <div class="display-6 fw-bold text-info">{{ number_format($result->percentile, 1) }}</div>
                        <div class="small text-muted">Percentile</div>
                    </div>
                </div>
                @endif
            </div>

            <p class="text-muted text-center small">
                <i class="bi bi-info-circle me-1"></i>
                This result is provisional and subject to verification of original documents.
            </p>

            {{-- Print --}}
            <div class="text-center">
                <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print Result Card</button>
            </div>
        </div>
    </div>

    </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Check Results — PATS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="text-center mb-5">
            <i class="bi bi-bar-chart-line-fill fs-1 mb-3 d-block" style="color:var(--pats-primary)"></i>
            <h2 class="fw-bold" style="color:var(--pats-primary)">Check Your Result</h2>
            <p class="text-muted">Enter your Roll Number or CNIC to view your result.</p>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <form method="GET" action="{{ route('results.search') }}">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Roll Number or CNIC</label>
                    <input type="text" name="query" class="form-control form-control-lg" placeholder="e.g. 0010102001 or 3520100111111" value="{{ request('query') }}" autofocus>
                </div>
                <button type="submit" class="btn btn-pats w-100 py-2 fw-semibold"><i class="bi bi-search me-1"></i>Search Result</button>
            </form>
        </div>

        @if(isset($result))
            @if($result)
            <div class="card border-0 shadow-sm rounded-4 p-4" style="border-top:4px solid var(--pats-accent) !important">
                <div class="text-center mb-3">
                    <span class="badge fs-6 px-4 py-2
                        @if($result->result_status === 'pass') bg-success
                        @elseif($result->result_status === 'fail') bg-danger
                        @elseif($result->result_status === 'absent') bg-secondary
                        @else bg-warning text-dark @endif">
                        {{ strtoupper($result->result_status) }}
                    </span>
                </div>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Candidate Name</td><td class="fw-semibold">{{ $result->application->candidate->user->full_name }}</td></tr>
                    <tr><td class="text-muted">Father's Name</td><td>{{ $result->application->candidate->father_name }}</td></tr>
                    <tr><td class="text-muted">CNIC</td><td class="fw-semibold">{{ $result->application->candidate->user->cnic }}</td></tr>
                    <tr><td class="text-muted">Post Applied For</td><td>{{ $result->application->job->title }}</td></tr>
                    <tr><td class="text-muted">Roll Number</td><td class="fw-bold text-primary">{{ $result->roll_number }}</td></tr>
                    <tr><td class="text-muted">Score</td><td class="fw-bold">{{ $result->score }} / {{ $result->total_marks }}</td></tr>
                    <tr><td class="text-muted">Percentage</td><td class="fw-bold {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }}">{{ $result->percentage }}%</td></tr>
                    @if($result->percentile)<tr><td class="text-muted">Percentile</td><td class="fw-bold">{{ number_format($result->percentile, 1) }}th</td></tr>@endif
                </table>
                <div class="text-center mt-2 small text-muted">Candidates are provisionally allowed subject to verification of original documents.</div>
            </div>
            @else
            <div class="alert alert-warning text-center">
                <i class="bi bi-search me-2"></i>No result found for <strong>{{ request('query') }}</strong>. Please check and try again.
            </div>
            @endif
        @endif
    </div>
    </div>
</div>
@endsection

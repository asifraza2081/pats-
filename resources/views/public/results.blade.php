@extends('layouts.public')
@section('title', 'Check Results — PATS')
@section('header-title', 'Search Results')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="ti ti-chart-bar fs-1 mb-3 d-block text-pats-primary" style="font-size: 3rem !important;"></i>
                <h2 class="fw-bold text-pats-primary fs-1">Check Your Result</h2>
                <p class="text-muted fs-4">Enter your Roll Number or CNIC to view your official test result.</p>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-md-5">
                    <form method="GET" action="{{ route('results.search') }}">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Enter Roll Number or CNIC</label>
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" name="query" class="form-control form-control-lg fs-3 py-3" placeholder="e.g. 01010203-001 or 3520100111111" value="{{ request('query') }}" autofocus required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fs-3 fw-bold">
                            <i class="ti ti-search me-2"></i> Search Candidate Result
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($result))
                @if($result)
                <div class="card border-0 shadow-sm" style="border-top: 4px solid var(--pats-accent) !important;">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 text-center d-block">
                        @php
                            $statusColors = [
                                'pass' => 'bg-success text-success-fg',
                                'fail' => 'bg-danger text-danger-fg',
                                'absent' => 'bg-secondary text-secondary-fg',
                            ];
                            $statusColor = $statusColors[$result->result_status] ?? 'bg-warning text-dark';
                        @endphp
                        <span class="badge {{ $statusColor }} fs-3 px-4 py-2 tracking-wide text-uppercase">
                            {{ $result->result_status }}
                        </span>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-borderless table-striped-columns mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted w-50">Candidate Name</td>
                                        <td class="fw-bold fs-3">{{ $result->application->candidate->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Father's Name</td>
                                        <td class="fw-semibold">{{ $result->application->candidate->father_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">CNIC</td>
                                        <td class="fw-semibold text-monospace">{{ $result->application->candidate->user->cnic }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Post Applied For</td>
                                        <td class="fw-semibold">{{ $result->application->job->title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted border-top border-bottom py-3">Roll Number</td>
                                        <td class="fw-bold text-pats-primary fs-3 border-top border-bottom py-3 text-monospace">{{ $result->roll_no }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Score Obtained</td>
                                        <td class="fw-bold fs-2 text-dark">{{ $result->score }} <span class="fs-4 text-muted fw-normal">/ {{ $result->total_marks }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Percentage</td>
                                        <td class="fw-bold fs-3 {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }}">{{ $result->percentage }}%</td>
                                    </tr>
                                    @if($result->percentile)
                                    <tr>
                                        <td class="text-muted">Percentile Rank</td>
                                        <td class="fw-bold fs-3 text-primary">{{ number_format($result->percentile, 1) }}<sup>th</sup></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent text-center border-0 pb-4">
                        <div class="text-muted small fst-italic">
                            <i class="ti ti-info-circle me-1"></i> Candidates are provisionally allowed subject to verification of original academic and professional documents.
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-important alert-warning alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-triangle fs-2 me-3"></i></div>
                        <div>
                            No valid result found for <strong>{{ request('query') }}</strong>. Please verify your query and try again.
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

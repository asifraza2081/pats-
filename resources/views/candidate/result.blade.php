@extends('layouts.dashboard')
@section('page-title', 'My Result')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4 d-print-none">
            <a href="{{ route('candidate.applications.show', $app) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i> Back to Application
            </a>
            <button class="btn btn-primary float-end" onclick="window.print()">
                <i class="ti ti-printer me-2"></i> Print Result Card
            </button>
        </div>

        <div class="card shadow-sm border-0 rounded-3 overflow-hidden" style="border-top: 4px solid var(--pats-primary) !important;">
            <!-- Header -->
            <div class="card-header border-0 bg-transparent text-center d-block pt-5 pb-0">
                <div class="text-muted text-uppercase tracking-wide small fw-bold mb-1">{{ $app->job->project->org_name }}</div>
                <h2 class="h1 fw-bold text-pats-primary mb-1">{{ $app->job->title }}</h2>
                <div class="text-secondary small">{{ $app->job->project->name }}</div>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Status Badge -->
                <div class="text-center mb-5">
                    @php
                        $rc = [
                            'pass' => 'bg-success text-success-fg',
                            'fail' => 'bg-danger text-danger-fg',
                            'absent' => 'bg-secondary text-secondary-fg',
                            'withheld' => 'bg-warning text-warning-fg'
                        ];
                        $rl = ['pass' => 'PASS', 'fail' => 'FAIL', 'absent' => 'ABSENT', 'withheld' => 'WITHHELD'];
                        $curStatus = (string)$result->result_status;
                    @endphp
                    <span class="badge {{ $rc[$curStatus] ?? 'bg-secondary' }} px-5 py-3 fs-3 tracking-wide">
                        {{ $rl[$curStatus] ?? strtoupper($curStatus) }}
                    </span>
                    @if($result->result_status === 'pass')
                    <div class="text-success fw-bold fs-4 mt-3">
                        <i class="ti ti-discount-check-filled fs-2 me-1 align-text-bottom"></i> Congratulations!
                    </div>
                    @endif
                </div>

                <!-- Candidate Info -->
                <div class="card bg-blue-lt border-0 mb-5">
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Candidate Name</div>
                                <div class="datagrid-content fw-bold fs-3 text-dark">{{ auth()->user()->full_name }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Roll Number</div>
                                <div class="datagrid-content fw-bold text-primary fs-3 text-monospace">{{ $result->roll_no }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">CNIC</div>
                                <div class="datagrid-content fw-semibold text-monospace">{{ auth()->user()->cnic }}</div>
                            </div>
                            
                            @if($app->examRollno)
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Date</div>
                                <div class="datagrid-content">{{ $app->examRollno->test_date ? \Carbon\Carbon::parse($app->examRollno->test_date)->format('d M Y') : 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Center</div>
                                <div class="datagrid-content">{{ $app->examRollno->testCenter->name ?? 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">City</div>
                                <div class="datagrid-content">{{ $app->examRollno->city->name ?? 'N/A' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Score Board -->
                <div class="row row-cards text-center mb-5">
                    <div class="col-sm-4">
                        <div class="card card-sm bg-light border-0 shadow-none h-100">
                            <div class="card-body">
                                <div class="display-5 fw-bold text-pats-primary mb-1">{{ number_format($result->score, 1) }}</div>
                                <div class="text-muted small text-uppercase tracking-wide fw-bold">Score Obtained</div>
                                <div class="text-secondary small mt-1">/ {{ number_format($result->total_marks, 1) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card card-sm bg-light border-0 shadow-none h-100">
                            <div class="card-body">
                                <div class="display-5 fw-bold {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }} mb-1">{{ number_format($result->percentage, 1) }}%</div>
                                <div class="text-muted small text-uppercase tracking-wide fw-bold">Percentage</div>
                            </div>
                        </div>
                    </div>
                    @if($result->percentile)
                    <div class="col-sm-4">
                        <div class="card card-sm bg-light border-0 shadow-none h-100">
                            <div class="card-body">
                                <div class="display-5 fw-bold text-info mb-1">{{ number_format($result->percentile, 1) }}</div>
                                <div class="text-muted small text-uppercase tracking-wide fw-bold">Percentile Rank</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Scanned Answer Sheet -->
                @if($result->scanned_sheet_path)
                <div class="mt-5 pt-4 border-top">
                    <h3 class="h3 fw-bold mb-3"><i class="ti ti-file-scan text-primary me-2"></i> Scanned Answer Sheet</h3>
                    <div class="card border-0 bg-dark-lt overflow-hidden">
                        <div class="card-body p-0 text-center">
                            <img src="{{ asset('storage/'.$result->scanned_sheet_path) }}" class="img-fluid rounded" alt="Scanned Answer Sheet" style="max-height: 800px;">
                        </div>
                    </div>
                    <div class="text-center mt-3 d-print-none">
                        <a href="{{ asset('storage/'.$result->scanned_sheet_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-external-link me-1"></i> Open in New Tab / Zoom
                        </a>
                    </div>
                </div>
                @endif

                <!-- Footer Note -->
                <div class="text-center text-muted small mt-4 pt-4 border-top">
                    <i class="ti ti-info-circle text-primary me-1"></i>
                    This computer-generated result card is provisional and subject to verification of original academic and professional documents. Omission or errors are accepted.
                </div>
            </div>
        </div>
        
    </div>
</div>

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .page-header, .navbar, .d-print-none, .btn { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    }
</style>
@endpush
@endsection

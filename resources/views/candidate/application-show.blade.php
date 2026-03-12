@extends('layouts.dashboard')
@section('page-title', 'Application Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="mb-4">
            <a href="{{ route('candidate.applications') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i> Back to Applications
            </a>
        </div>

        <!-- Application Status Header -->
        @php 
            $statusColors = [
                'submitted' => 'bg-secondary',
                'fee_paid'  => 'bg-primary',
                'processed' => 'bg-info',
                'appeared'  => 'bg-success',
                'absent'    => 'bg-danger'
            ];
            $statusColor = $statusColors[$app->status] ?? 'bg-secondary';
        @endphp
        
        <div class="card mb-4 border-0 shadow-sm overflow-hidden">
            <div class="card-status-top {{ str_replace('bg-', '', $statusColor) }}"></div>
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="text-muted text-uppercase tracking-wide small fw-bold mb-2">
                            <i class="ti ti-building me-1"></i> {{ $app->job->project->org_name }} &bull; {{ $app->job->project->name }}
                        </div>
                        <h2 class="h1 fw-bold mb-3 text-pats-primary">{{ $app->job->title }}</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge {{ $statusColor }} text-white px-3 py-2 text-uppercase tracking-wide fs-5">
                                {{ str_replace('_', ' ', $app->status) }}
                            </span>
                            @if($app->job->bps_grade)
                            <span class="badge bg-secondary-lt px-3 py-2 fs-5">BPS-{{ $app->job->bps_grade }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4 mt-4 mt-md-0 text-md-end">
                        <div class="text-muted small mb-1">Application Submitted</div>
                        <div class="fw-bold fs-3">{{ $app->applied_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            @if($app->payment && $app->status === 'submitted')
            <a href="{{ route('candidate.challan', $app) }}" class="btn btn-warning" target="_blank">
                <i class="ti ti-download me-2"></i> Download Fee Challan
            </a>
            @endif
            
            @if($app->examRollno && $app->examRollno->roll_no)
            <a href="{{ route('candidate.slip', $app) }}" class="btn btn-success" target="_blank">
                <i class="ti ti-ticket me-2"></i> Download Roll Number Slip
            </a>
            @endif
            
            @if($app->result?->isPublished())
            <a href="{{ route('candidate.result', $app) }}" class="btn btn-info">
                <i class="ti ti-chart-bar me-2"></i> View Test Result
            </a>
            @endif
        </div>

        <div class="row row-cards">
            <!-- Payment Information -->
            @if($app->payment)
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold"><i class="ti ti-receipt text-warning me-2"></i> Fee Payment Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Challan Reference</div>
                                <div class="fw-bold fs-3 text-primary">{{ $app->payment->challan_ref }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Amount Payable</div>
                                <div class="fw-bold fs-3">PKR {{ number_format($app->payment->amount) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Payment Status</div>
                                @php 
                                    $pc = ['pending' => 'bg-warning text-warning-fg', 'paid' => 'bg-success text-success-fg', 'failed' => 'bg-danger text-danger-fg']; 
                                @endphp
                                <span class="badge {{ $pc[$app->payment->status] ?? 'bg-secondary' }} px-2 py-1 text-uppercase">{{ $app->payment->status }}</span>
                            </div>
                            
                            @if($app->payment->status === 'paid')
                            <div class="col-md-4">
                                <div class="text-muted small">Bank Name</div>
                                <div class="fw-semibold">{{ $app->payment->bank_name ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Deposit Date</div>
                                <div class="fw-semibold">{{ $app->payment->deposit_date?->format('d M Y') ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Transaction ID</div>
                                <div class="fw-semibold">{{ $app->payment->transaction_id ?? '—' }}</div>
                            </div>
                            @endif
                        </div>
                        
                        @if($app->payment->status === 'pending')
                        <div class="alert alert-important alert-warning mt-4 mb-0" role="alert">
                            <div class="d-flex">
                                <div><i class="ti ti-alert-triangle fs-2 me-3"></i></div>
                                <div>
                                    Please download and deposit your fee challan at the designated bank before the deadline (<strong>{{ $app->job->project->close_date?->format('d M Y') ?? 'TBD' }}</strong>) to complete your application.
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Roll Number and Venue Integration -->
            @if($app->examRollno)
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold"><i class="ti ti-id-badge text-success me-2"></i> Candidature & Roll No</h3>
                    </div>
                    <div class="card-body">
                        @if($app->examRollno->roll_no)
                            <div class="display-5 fw-bold text-pats-primary mb-3 text-monospace">{{ $app->examRollno->roll_no }}</div>
                            <div class="alert alert-important alert-success mb-0" role="alert">
                                <div class="d-flex">
                                    <div><i class="ti ti-check fs-2 me-2"></i></div>
                                    <div>Your roll number slip has been generated and is ready for download.</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-important alert-info mb-0" role="alert">
                                <div class="d-flex">
                                    <div><i class="ti ti-hourglass fs-2 me-2"></i></div>
                                    <div>Roll number is currently being assigned. Your slip will be available soon.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold"><i class="ti ti-map-pin text-danger me-2"></i> Test Venue & Schedule</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Center</div>
                                <div class="datagrid-content fw-bold">{{ $app->examRollno->testCenter->name ?? 'TBD' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">City</div>
                                <div class="datagrid-content">{{ $app->examRollno->city->name ?? 'TBD' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Date</div>
                                <div class="datagrid-content fw-bold text-primary">{{ $app->examRollno->test_date ? \Carbon\Carbon::parse($app->examRollno->test_date)->format('l, d M Y') : 'TBD' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Reporting Time</div>
                                <div class="datagrid-content fw-bold">{{ $app->examRollno->reporting_time ? \Carbon\Carbon::parse($app->examRollno->reporting_time)->format('h:i A') : 'TBD' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Start Time</div>
                                <div class="datagrid-content fw-bold">{{ $app->examRollno->start_time ? \Carbon\Carbon::parse($app->examRollno->start_time)->format('h:i A') : 'TBD' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Eligibility Warnings -->
            @if(!empty($app->eligibility_warnings))
            <div class="col-12">
                <div class="card border-warning border-start border-0 border-3 shadow-sm rounded-3">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title fw-bold text-warning"><i class="ti ti-alert-triangle me-2"></i> Eligibility Notices</h3>
                    </div>
                    <div class="card-body">
                        <ul class="mb-2">
                            @foreach($app->eligibility_warnings as $w)
                                <li>{{ $w }}</li>
                            @endforeach
                        </ul>
                        <div class="text-muted small fst-italic mt-3">
                            These status indicators are advisory. Final eligibility is always subject to strict document verification at the test center or during interviews.
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

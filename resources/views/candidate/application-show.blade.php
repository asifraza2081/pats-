@extends('layouts.app')
@section('title', 'Application Details — PATS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
    <div class="col-lg-8">

    <div class="mb-3">
        <a href="{{ route('candidate.applications') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>My Applications</a>
    </div>

    {{-- Application Status Banner --}}
    @php $statusColors=['submitted'=>'secondary','fee_paid'=>'primary','processed'=>'info','appeared'=>'success','absent'=>'danger']; @endphp
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background:linear-gradient(135deg,#0a3d62,#1a5276);color:#fff">
        <div class="card-body p-4">
            <div class="small text-white-50">{{ $app->job->project->org_name }} · {{ $app->job->project->name }}</div>
            <h5 class="fw-bold mt-1 mb-2">{{ $app->job->title }}</h5>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-{{ $statusColors[$app->status] ?? 'secondary' }} fs-6 px-3 py-2 text-capitalize">{{ str_replace('_',' ',$app->status) }}</span>
                @if($app->job->bps_grade)<span class="badge bg-white text-dark">{{ $app->job->bps_grade }}</span>@endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        @if($app->payment && $app->status === 'submitted')
        <a href="{{ route('candidate.challan', $app) }}" class="btn btn-warning text-dark" target="_blank">
            <i class="bi bi-download me-1"></i>Download Fee Challan
        </a>
        @endif
        @if($app->rollNumber?->slip_ready && !$app->batch?->hasStarted())
        <a href="{{ route('candidate.slip', $app) }}" class="btn btn-success" target="_blank">
            <i class="bi bi-ticket-perforated me-1"></i>Download Roll Number Slip
        </a>
        @endif
        @if($app->result?->isPublished())
        <a href="{{ route('candidate.result', $app) }}" class="btn btn-info text-white">
            <i class="bi bi-bar-chart me-1"></i>View Result
        </a>
        @endif
    </div>

    {{-- Payment Info --}}
    @if($app->payment)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-warning"></i>Fee Payment</h6>
            <div class="row g-2 small">
                <div class="col-md-4"><div class="text-muted">Challan Ref</div><div class="fw-bold text-primary">{{ $app->payment->challan_ref }}</div></div>
                <div class="col-md-4"><div class="text-muted">Amount</div><div class="fw-bold">PKR {{ number_format($app->payment->amount) }}</div></div>
                <div class="col-md-4"><div class="text-muted">Status</div>
                    @php $pc=['pending'=>'warning','paid'=>'success','failed'=>'danger']; @endphp
                    <span class="badge bg-{{ $pc[$app->payment->status] ?? 'secondary' }} text-capitalize">{{ $app->payment->status }}</span>
                </div>
                @if($app->payment->status === 'paid')
                <div class="col-md-4"><div class="text-muted">Bank</div><div>{{ $app->payment->bank_name ?? '—' }}</div></div>
                <div class="col-md-4"><div class="text-muted">Deposit Date</div><div>{{ $app->payment->deposit_date?->format('d M Y') ?? '—' }}</div></div>
                <div class="col-md-4"><div class="text-muted">Transaction ID</div><div>{{ $app->payment->transaction_id ?? '—' }}</div></div>
                @endif
            </div>
            @if($app->payment->status === 'pending')
            <div class="alert alert-warning small mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i>Please download and deposit your fee challan at the designated bank before the deadline (<strong>{{ $app->job->project->close_date?->format('d M Y') ?? 'TBD' }}</strong>) to complete your application.
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Roll Number --}}
    @if($app->rollNumber)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2 text-success"></i>Roll Number</h6>
            <div class="display-6 fw-bold text-primary mb-3">{{ $app->rollNumber->roll_number }}</div>
            @if($app->rollNumber->slip_ready)
            <div class="alert alert-success small mb-0"><i class="bi bi-check-circle me-1"></i>Your roll number slip is ready for download.</div>
            @else
            <div class="alert alert-info small mb-0"><i class="bi bi-hourglass me-1"></i>Roll number assigned. Slip will be available soon — you'll be notified via SMS.</div>
            @endif
        </div>
    </div>
    @endif

    {{-- Test Venue --}}
    @if($app->batch)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-danger"></i>Test Venue &amp; Schedule</h6>
            <div class="row g-2 small">
                <div class="col-md-6"><div class="text-muted">Center</div><div class="fw-semibold">{{ $app->batch->center->name }}</div></div>
                <div class="col-md-6"><div class="text-muted">City</div><div>{{ $app->batch->center->city }}</div></div>
                <div class="col-md-6"><div class="text-muted">Address</div><div>{{ $app->batch->center->address }}</div></div>
                @if($app->batch->center->map_url)
                <div class="col-md-6"><a href="{{ $app->batch->center->map_url }}" target="_blank" class="btn btn-xs btn-outline-secondary"><i class="bi bi-map me-1"></i>View Map</a></div>
                @endif
                <div class="col-md-4"><div class="text-muted">Test Date</div><div class="fw-bold">{{ $app->batch->test_date->format('l, d M Y') }}</div></div>
                <div class="col-md-4"><div class="text-muted">Reporting Time</div><div class="fw-bold">{{ \Carbon\Carbon::parse($app->batch->reporting_time)->format('h:i A') }}</div></div>
                <div class="col-md-4"><div class="text-muted">Test Starts</div><div class="fw-bold">{{ \Carbon\Carbon::parse($app->batch->start_time)->format('h:i A') }}</div></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Eligibility Warnings --}}
    @if(!empty($app->eligibility_warnings))
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-warning border-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Eligibility Notices</h6>
            <ul class="mb-0 small">
                @foreach($app->eligibility_warnings as $w)<li>{{ $w }}</li>@endforeach
            </ul>
            <small class="text-muted mt-2 d-block">These are advisory. Final eligibility is subject to document verification at the test center.</small>
        </div>
    </div>
    @endif

    </div>
    </div>
</div>
@endsection

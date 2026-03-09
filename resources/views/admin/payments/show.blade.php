@extends('layouts.admin')
@section('title', 'Payment — ' . $payment->challan_ref)
@section('page-title', 'Payment Verification')

@section('content')
<div class="row g-4">
    {{-- Payment Info --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3">Payment Details</h6>
            @php $pc=['pending'=>'warning','paid'=>'success','failed'=>'danger']; @endphp
            <span class="badge bg-{{ $pc[$payment->status] ?? 'secondary' }} mb-3 fs-6">{{ ucfirst($payment->status) }}</span>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted small">Challan Ref</td><td class="fw-bold">{{ $payment->challan_ref }}</td></tr>
                <tr><td class="text-muted small">Amount</td><td class="fw-bold text-primary">PKR {{ number_format($payment->amount) }}</td></tr>
                <tr><td class="text-muted small">Created</td><td class="small">{{ $payment->created_at->format('d M Y') }}</td></tr>
                @if($payment->verified_at)
                <tr><td class="text-muted small">Verified At</td><td class="small">{{ $payment->verified_at->format('d M Y H:i') }}</td></tr>
                <tr><td class="text-muted small">Bank</td><td class="small">{{ $payment->bank_name ?? '—' }}</td></tr>
                <tr><td class="text-muted small">Branch Code</td><td class="small">{{ $payment->branch_code ?? '—' }}</td></tr>
                <tr><td class="text-muted small">Transaction ID</td><td class="small">{{ $payment->transaction_id ?? '—' }}</td></tr>
                <tr><td class="text-muted small">Deposit Date</td><td class="small">{{ $payment->deposit_date ? \Carbon\Carbon::parse($payment->deposit_date)->format('d M Y') : '—' }}</td></tr>
                @endif
            </table>
            <hr>
            <h6 class="fw-bold mb-2">Candidate</h6>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted small">Name</td><td class="small">{{ $payment->application->candidate->user->full_name }}</td></tr>
                <tr><td class="text-muted small">Father's Name</td><td class="small">{{ $payment->application->candidate->father_name }}</td></tr>
                <tr><td class="text-muted small">CNIC</td><td class="small fw-semibold">{{ $payment->application->candidate->user->cnic }}</td></tr>
                <tr><td class="text-muted small">Phone</td><td class="small">{{ $payment->application->candidate->user->phone }}</td></tr>
            </table>
            <hr>
            <h6 class="fw-bold mb-2">Application</h6>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted small">Post</td><td class="small">{{ $payment->application->job->title }}</td></tr>
                <tr><td class="text-muted small">Project</td><td class="small">{{ $payment->application->job->project->name }}</td></tr>
                <tr><td class="text-muted small">Test Center</td><td class="small">{{ $payment->application->batch->center->name }}</td></tr>
                <tr><td class="text-muted small">Test Date</td><td class="small">{{ $payment->application->batch->test_date->format('d M Y') }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Verify Form --}}
    @if($payment->status === 'pending')
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-success"></i>Verify Payment</h6>
            <div class="alert alert-info small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                After verification, a roll number will be automatically assigned and the candidate will be notified via SMS.
            </div>
            <form method="POST" action="{{ route('admin.payments.verify',$payment) }}" onsubmit="return confirm('Verify this payment? A roll number will be assigned.')">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. HBL">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Branch Code</label>
                        <input type="text" name="branch_code" class="form-control" placeholder="e.g. 0123">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Transaction / Slip ID</label>
                        <input type="text" name="transaction_id" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Deposit Date *</label>
                        <input type="date" name="deposit_date" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-1"></i>Confirm & Assign Roll Number
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @elseif($payment->status === 'paid')
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle-fill me-2"></i>
                Payment verified. Roll No: <strong>{{ $payment->application->rollNumber?->roll_number ?? 'N/A' }}</strong>
            </div>
            <a href="{{ route('admin.rollnumbers.slip',$payment->application) }}" class="btn btn-outline-primary mt-3" target="_blank">
                <i class="bi bi-download me-1"></i>View Roll Number Slip
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

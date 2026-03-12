@extends('layouts.dashboard')
@section('title', 'Payment Verification — ' . $payment->challan_number)
@section('page-title', 'Payment Review')

@section('page-actions')
<a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Transactions
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Payment Information Card -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Transaction Details</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    @php
                        $st = match($payment->status) {
                            'pending' => ['c'=>'warning', 'l'=>'Pending Verification'],
                            'paid' => ['c'=>'success', 'l'=>'Verified / Paid'],
                            'expired' => ['c'=>'secondary', 'l'=>'Expired'],
                            default => ['c'=>'dark', 'l'=>strtoupper($payment->status)]
                        };
                    @endphp
                    <span class="badge bg-{{ $st['c'] }}-lt text-{{ $st['c'] }} px-3 py-2 fs-4">
                        {{ $st['l'] }}
                    </span>
                </div>

                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Challan Number</div>
                        <div class="datagrid-content fw-bold">{{ $payment->challan_number }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Amount (PKR)</div>
                        <div class="datagrid-content text-success fw-bold">Rs. {{ number_format($payment->amount) }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Receipt Generated</div>
                        <div class="datagrid-content">{{ $payment->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @if($payment->verified_at)
                    <div class="datagrid-item">
                        <div class="datagrid-title">Verification Date</div>
                        <div class="datagrid-content">{{ $payment->verified_at->format('d M Y, H:i') }}</div>
                    </div>
                    @endif
                </div>

                @if($payment->status === 'paid')
                <div class="mt-4 border-top pt-3">
                    <h4 class="fw-bold mb-2">Deposit Proof</h4>
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Bank Name</div>
                            <div class="datagrid-content">{{ $payment->bank_name ?: '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Branch Code</div>
                            <div class="datagrid-content">{{ $payment->branch_code ?: '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Transaction ID</div>
                            <div class="datagrid-content">{{ $payment->transaction_id ?: '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Deposit Date</div>
                            <div class="datagrid-content">{{ $payment->deposit_date ? date('d M Y', strtotime($payment->deposit_date)) : '—' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="card-footer bg-light-lt">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Candidate</div>
                        <div class="datagrid-content fw-bold">{{ $payment->application->candidate->user->full_name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">CNIC</div>
                        <div class="datagrid-content">{{ $payment->application->candidate->user->cnic }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Actions -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-checkup-list me-2"></i> Verification Portal</h3>
            </div>
            <div class="card-body">
                @if($payment->status === 'pending')
                <div class="alert alert-info bg-info-lt mb-4 border-0">
                    <div class="d-flex">
                        <div><i class="ti ti-info-circle fs-2 me-2"></i></div>
                        <div>
                            <div class="fw-bold">Process Instructions</div>
                            <div class="text-secondary small">Review the physical/digital challan receipt. Once verified, the application status will change to <strong>"Fee Paid"</strong>, making the candidate eligible for the upcoming Seat Allocation Batch.</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" onsubmit="return confirm('Confirm payment verification?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. HBL, NBP, Meezan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Code / City</label>
                            <input type="text" name="branch_code" class="form-control" placeholder="e.g. 0123 / Lahore">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction / Slip Number</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="e.g. TXN987654">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Actual Deposit Date</label>
                            <input type="date" name="deposit_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-checkbox me-2"></i> Confirm Verification
                            </button>
                        </div>
                    </div>
                </form>
                @elseif($payment->status === 'paid')
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="ti ti-circle-check text-success fs-1"></i>
                    </div>
                    <h2 class="fw-bold">Payment Verified</h2>
                    <p class="text-secondary">This application is now ready for Seat Allocation.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.applications.show', $payment->application_id) }}" class="btn btn-outline-primary">
                            <i class="ti ti-file-text me-2"></i> View Application Profile
                        </a>
                    </div>
                </div>
                @else
                <div class="alert alert-secondary text-center py-4 border-0">
                    <i class="ti ti-clock-stop fs-1 mb-2"></i>
                    <p class="mb-0">This payment record is no longer eligible for verification.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Application Summary -->
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Target Project</div>
                        <div class="datagrid-content">{{ $payment->application->job->project->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Job Post</div>
                        <div class="datagrid-content">{{ $payment->application->job->title }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Desired Test City</div>
                        <div class="datagrid-content text-blue fw-medium">{{ $payment->application->desiredTestCity->name ?? 'Not Set' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

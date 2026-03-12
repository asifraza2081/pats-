@extends('layouts.dashboard')
@section('title', 'Payment Management')
@section('page-title', 'Fee & Payments')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">Transaction History</h3>
        <div class="card-actions">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Verified (Paid)</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th class="w-1">Payment ID</th>
                    <th>Candidate & CNIC</th>
                    <th>Challan / Post</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Generation Date</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td><span class="text-secondary fw-bold">#{{ $payment->id }}</span></td>
                    <td>
                        <div class="font-weight-medium fw-bold text-body">{{ $payment->application->candidate->user->full_name }}</div>
                        <div class="text-secondary small">{{ $payment->application->candidate->user->cnic }}</div>
                    </td>
                    <td>
                        <div class="font-weight-medium text-body">ID: {{ $payment->challan_number }}</div>
                        <div class="text-secondary small">{{ $payment->application->job->title }}</div>
                    </td>
                    <td>
                        <div class="h4 mb-0 fw-bold text-success">PKR {{ number_format($payment->amount) }}</div>
                    </td>
                    <td>
                        @php
                            $st = match($payment->status) {
                                'pending' => ['c'=>'warning', 'l'=>'Pending Verification'],
                                'paid' => ['c'=>'success', 'l'=>'Verified / Paid'],
                                'expired' => ['c'=>'secondary', 'l'=>'Expired'],
                                default => ['c'=>'dark', 'l'=>strtoupper($payment->status)]
                            };
                        @endphp
                        <span class="badge bg-{{ $st['c'] }}-lt text-{{ $st['c'] }} py-1 px-2">
                            {{ $st['l'] }}
                        </span>
                    </td>
                    <td><span class="text-secondary small">{{ $payment->created_at->format('d M, Y') }}</span></td>
                    <td>
                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Review Payment">
                            <i class="ti ti-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-0">
                        <x-empty-state 
                            icon="ti ti-receipt-off" 
                            title="No payment records found" 
                            subtitle="Fee transactions will appear here once candidates generate their challans."
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

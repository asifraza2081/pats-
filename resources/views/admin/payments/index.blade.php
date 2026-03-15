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
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Verified (Paid)</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr class="bg-gray-50 text-gray-700">
                    <th class="px-4 py-3 text-left">Challan Ref</th>
                    <th class="px-4 py-3 text-left">Candidate</th>
                    <th class="px-4 py-3 text-left">Project/Job</th>
                    <th class="px-4 py-3 text-center">Amount</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-left">Verified By</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm">#{{ $payment->challan_ref }}</td>
                    <td class="px-4 py-3">
                        <div>{{ $payment->application->candidate->user->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $payment->application->candidate->user->cnic }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $payment->application->job->title }}</div>
                        <div class="text-xs text-gray-500">{{ $payment->application->job->project->name }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold">
                        PKR {{ number_format($payment->amount) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-{{ $payment->status->color() }}-lt text-{{ $payment->status->color() }} py-1 px-2">
                            {{ $payment->status->label() }}
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

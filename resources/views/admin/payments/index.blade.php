@extends('layouts.admin')
@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
{{-- Filter --}}
<form class="d-flex gap-2 mb-3" method="GET">
    <select name="status" class="form-select form-select-sm w-auto">
        <option value="">All Status</option>
        @foreach(['pending','paid','failed'] as $s)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button class="btn btn-sm btn-outline-primary">Filter</button>
    @if(request('status'))<a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>@endif
</form>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="px-4 py-3">Challan Ref</th><th>Candidate</th><th>Job</th><th>Amount</th><th>Status</th><th>Applied</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="px-4 fw-bold text-primary">{{ $payment->challan_ref }}</td>
                    <td>
                        <div class="fw-semibold small">{{ $payment->application->candidate->user->full_name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $payment->application->candidate->user->cnic }}</div>
                    </td>
                    <td class="small">{{ Str::limit($payment->application->job->title, 25) }}</td>
                    <td class="small fw-semibold">PKR {{ number_format($payment->amount) }}</td>
                    <td>
                        @php $pc=['pending'=>'warning','paid'=>'success','failed'=>'danger']; @endphp
                        <span class="badge bg-{{ $pc[$payment->status] ?? 'secondary' }} text-dark text-capitalize">{{ $payment->status }}</span>
                    </td>
                    <td class="small text-muted">{{ $payment->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.payments.show',$payment) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-white border-0 px-4 pb-3">{{ $payments->links() }}</div>
    @endif
</div>
@endsection

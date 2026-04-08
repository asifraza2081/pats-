@extends('layouts.dashboard')
@section('page-title', 'Expenses')
@section('page-actions')
    <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i> New Expense
    </a>
    <a href="{{ route('admin.financials.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-chart-bar me-1"></i> Dashboard
    </a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible"><i class="ti ti-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible"><i class="ti ti-x me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Project</label>
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Totals --}}
@if($totals && ($totals->gross > 0))
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 bg-danger-lt text-center p-3">
            <div class="small text-muted fw-semibold">Gross Total</div>
            <div class="h4 fw-black text-danger mb-0">PKR {{ number_format($totals->gross, 2) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 bg-warning-lt text-center p-3">
            <div class="small text-muted fw-semibold">Tax Withheld</div>
            <div class="h4 fw-black text-warning mb-0">PKR {{ number_format($totals->tax, 2) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 bg-blue-lt text-center p-3">
            <div class="small text-muted fw-semibold">Net Paid</div>
            <div class="h4 fw-black text-azure mb-0">PKR {{ number_format($totals->net, 2) }}</div>
        </div>
    </div>
</div>
@endif

{{-- Expense Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Project</th>
                    <th>Recipient</th>
                    <th class="text-end">Gross (PKR)</th>
                    <th class="text-end">Tax %</th>
                    <th class="text-end">Tax (PKR)</th>
                    <th class="text-end">Net (PKR)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td class="text-muted small">{{ $expense->id }}</td>
                    <td class="small">{{ $expense->expense_date->format('d M Y') }}</td>
                    <td><span class="badge bg-secondary-lt text-secondary">{{ $expense->category->name ?? '—' }}</span></td>
                    <td class="text-truncate small" style="max-width: 200px;" title="{{ $expense->description }}">{{ $expense->description }}</td>
                    <td class="small text-muted">{{ $expense->project?->name ?? '—' }}</td>
                    <td class="small">
                        {{ $expense->recipient_name ?? '—' }}
                        @if($expense->recipient_ntn)
                        <div class="text-muted" style="font-size:10px;">NTN: {{ $expense->recipient_ntn }}</div>
                        @endif
                    </td>
                    <td class="text-end fw-semibold text-danger small">{{ number_format($expense->gross_amount, 2) }}</td>
                    <td class="text-end small text-muted">{{ $expense->tax_rate }}%</td>
                    <td class="text-end small text-warning">{{ number_format($expense->tax_amount, 2) }}</td>
                    <td class="text-end fw-bold small">{{ number_format($expense->net_amount, 2) }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-outline-primary btn-icon" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="{{ route('admin.expenses.print-voucher', $expense) }}" target="_blank" class="btn btn-outline-secondary btn-icon" title="Print Voucher">
                                <i class="ti ti-printer"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this expense? The ledger entry will be preserved.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-icon" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-5">
                        <i class="ti ti-mood-empty fs-1 d-block mb-2 text-muted"></i>
                        No expenses recorded yet.
                        <a href="{{ route('admin.expenses.create') }}" class="d-block mt-2">Add your first expense →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        {{ $expenses->links() }}
    </div>
</div>
@endsection

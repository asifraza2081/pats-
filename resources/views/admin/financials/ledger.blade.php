@extends('layouts.dashboard')
@section('page-title', 'Financial Ledger')
@section('page-actions')
    <a href="{{ route('admin.financials.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
    </a>
@endsection

@section('content')

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.financials.ledger') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
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
                <label class="form-label small fw-semibold mb-1">Fiscal Year</label>
                <select name="fy" class="form-select form-select-sm">
                    <option value="">All FYs</option>
                    @foreach($allFys as $fyOption)
                    <option value="{{ $fyOption }}" {{ request('fy') === $fyOption ? 'selected' : '' }}>FY {{ $fyOption }}</option>
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
                <a href="{{ route('admin.financials.ledger') }}" class="btn btn-light btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Ledger Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold m-0"><i class="ti ti-list me-2 text-teal"></i>Audit Ledger</h3>
        <span class="badge bg-muted text-muted border">Immutable — no deletions</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>FY</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Project</th>
                    <th class="text-end">Gross (PKR)</th>
                    <th class="text-end">Tax (PKR)</th>
                    <th class="text-end">Net (PKR)</th>
                    <th>Posted By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td class="text-muted small">{{ $entry->id }}</td>
                    <td class="small">{{ $entry->ledger_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $entry->type === 'revenue' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }}">
                            {{ ucfirst($entry->type) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $entry->fiscal_year }}</td>
                    <td class="small">{{ $entry->category }}</td>
                    <td class="small text-truncate" style="max-width: 250px;" title="{{ $entry->description }}">{{ $entry->description }}</td>
                    <td class="small text-muted">{{ $entry->project?->name ?? '—' }}</td>
                    <td class="text-end small fw-semibold {{ $entry->type === 'revenue' ? 'text-success' : 'text-danger' }}">
                        {{ number_format($entry->amount, 2) }}
                    </td>
                    <td class="text-end small text-warning">{{ number_format($entry->tax_amount, 2) }}</td>
                    <td class="text-end small fw-bold">{{ number_format($entry->net_amount, 2) }}</td>
                    <td class="small text-muted">{{ $entry->creator?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center text-muted py-5">No ledger entries found.</td></tr>
                @endforelse
            </tbody>
            @if($entries->isNotEmpty())
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="7" class="text-end pe-3">Page Totals:</td>
                    <td class="text-end">{{ number_format($entries->sum('amount'), 2) }}</td>
                    <td class="text-end text-warning">{{ number_format($entries->sum('tax_amount'), 2) }}</td>
                    <td class="text-end">{{ number_format($entries->sum('net_amount'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    <div class="card-footer">
        {{ $entries->links() }}
    </div>
</div>
@endsection

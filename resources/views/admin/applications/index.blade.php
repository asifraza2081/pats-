@extends('layouts.dashboard')
@section('title', 'All Applications')
@section('page-title', 'Applications Management')

@section('content')
<form action="{{ route('admin.applications.bulk-mark-paid') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold">Recent Candidate Submissions</h3>
            <div class="card-actions">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="ti ti-check me-2"></i> Bulk Mark Paid
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter table-mobile-md datatable">
                <thead>
                    <tr>
                        <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
                        <th class="w-1 text-uppercase small text-secondary">App ID</th>
                        <th class="text-uppercase small text-secondary">Candidate Details</th>
                        <th class="text-uppercase small text-secondary">Job / Project</th>
                        <!-- RESTORED AUDIT COLUMNS -->
                        <th class="text-uppercase small text-secondary">Desired City</th>
                        <th class="text-uppercase small text-secondary">Roll / Center</th>
                        <th class="text-uppercase small text-secondary">Status</th>
                        <th class="text-uppercase small text-secondary">Applied On</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td>
                            @if($app->status !== \App\Enums\ApplicationStatus::FEE_PAID)
                            <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="form-check-input row-checkbox">
                            @endif
                        </td>
                        <td><span class="fw-bold">#{{ $app->id }}</span></td>
                        <td>
                            <div class="d-flex py-1 align-items-center">
                                <span class="avatar avatar-sm me-2 bg-blue-lt text-blue fw-bold">{{ substr($app->candidate->user->first_name, 0, 1) }}</span>
                                <div class="flex-fill">
                                    <div class="fw-bold text-body">{{ $app->candidate->user->full_name }}</div>
                                    <div class="text-secondary small">{{ $app->candidate->user->cnic }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-body">{{ $app->job->title }}</div>
                            <div class="text-secondary small">{{ $app->job->project->name }}</div>
                        </td>
                        <!-- RESTORED DATA -->
                        <td><span class="text-body">{{ $app->desiredTestCity->name ?? 'N/A' }}</span></td>
                        <td>
                            @if($app->examRollno)
                            <div class="fw-bold text-blue">{{ $app->examRollno->roll_no }}</div>
                            <div class="text-secondary small">{{ $app->examRollno->center->name }}</div>
                            @else
                            <span class="badge bg-yellow-lt text-yellow">Pending Allocation</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $app->status->color() }}-lt text-{{ $app->status->color() }} text-uppercase">
                                {{ $app->status->label() }}
                            </span>
                        </td>
                        <td class="text-secondary small">{{ $app->applied_at->format('d M, Y') }}<br><span class="opacity-50 fs-5">{{ $app->applied_at->format('h:i A') }}</span></td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                @if($app->status !== \App\Enums\ApplicationStatus::FEE_PAID)
                                <button type="submit" form="single-mark-paid-{{ $app->id }}" class="btn btn-icon btn-ghost-success btn-sm" data-bs-toggle="tooltip" title="Mark Paid">
                                    <i class="ti ti-check"></i>
                                </button>
                                @endif
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-icon btn-ghost-primary btn-sm" data-bs-toggle="tooltip" title="Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-secondary py-5">
                            <div class="empty">
                                <div class="empty-icon text-secondary"><i class="ti ti-folders-off fs-1"></i></div>
                                <p class="empty-title">No applications found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $applications->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</form>

{{-- Static forms for individual actions --}}
@foreach($applications as $app)
    @if($app->status !== \App\Enums\ApplicationStatus::FEE_PAID)
    <form id="single-mark-paid-{{ $app->id }}" action="{{ route('admin.applications.mark-paid', $app) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach

@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection

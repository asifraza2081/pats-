@extends('layouts.dashboard')
@section('title', 'All Applications')
@section('page-title', 'Applications Management')

@section('content')
<form action="{{ route('admin.applications.bulk-mark-paid') }}" method="POST">
    @csrf
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pb-1 pt-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold text-primary">Recent Candidate Submissions</h3>
            <div class="card-actions">
                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                    <i class="ti ti-check me-2"></i> Bulk Mark Paid
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable table-hover">
                <thead>
                    <tr>
                        <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
                        <th class="w-1">App ID</th>
                        <th>Candidate</th>
                        <th>Job Post / Project</th>
                        <th>Desired City</th>
                        <th>Roll No / Center</th>
                        <th>Status</th>
                        <th>Applied On</th>
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
                        <td><span class="text-secondary fw-bold">#{{ $app->id }}</span></td>
                        <td>
                            <div class="d-flex py-1 align-items-center">
                                <span class="avatar me-2 bg-blue-lt text-blue fw-bold">{{ substr($app->candidate->user->first_name, 0, 1) }}</span>
                                <div class="flex-fill">
                                    <div class="font-weight-medium fw-bold text-body">{{ $app->candidate->user->full_name }}</div>
                                    <div class="text-secondary small">{{ $app->candidate->user->cnic }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-weight-medium text-body">{{ $app->job->title }}</div>
                            <div class="text-secondary small">{{ $app->job->project->name }}</div>
                        </td>
                        <td>
                            <span class="text-body fw-medium">{{ $app->desiredTestCity->name ?? 'Not Set' }}</span>
                        </td>
                        <td>
                            @if($app->examRollno)
                            <div class="font-weight-medium text-blue fw-bold">{{ $app->examRollno->roll_no }}</div>
                            <div class="text-secondary small">{{ $app->examRollno->center->name }}</div>
                            @else
                            <span class="badge bg-yellow-lt text-yellow px-2 py-1">Pending Allocation</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $app->status->color() }}-lt text-{{ $app->status->color() }}">
                                {{ $app->status->label() }}
                            </span>
                        </td>
                        <td><span class="text-secondary small">{{ $app->applied_at->format('d M, Y H:i') }}</span></td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                @if($app->status !== \App\Enums\ApplicationStatus::FEE_PAID)
                                <button type="submit" form="single-mark-paid-{{ $app->id }}" class="btn btn-icon btn-outline-success btn-sm" data-bs-toggle="tooltip" title="Mark as Paid">
                                    <i class="ti ti-currency-dollar"></i>
                                </button>
                                @endif
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="View Details">
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
                                <p class="empty-title">No applications found in the records.</p>
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

{{-- Static forms for individual actions to avoid nested forms --}}
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

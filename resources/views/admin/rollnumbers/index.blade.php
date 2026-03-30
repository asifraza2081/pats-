@extends('layouts.dashboard')
@section('title', 'Roll Numbers')
@section('page-title', 'Roll Numbers Directory')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Assigned Roll Numbers</h3>
                <div class="card-actions">
                    <form action="{{ route('admin.rollnumbers.index') }}" method="GET" class="input-group input-group-flat">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search Roll No, CNIC, Name...">
                        <span class="input-group-text">
                            <button type="submit" class="link-secondary border-0 bg-transparent" title="Search"><i class="ti ti-search fs-2"></i></button>
                        </span>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable table-hover">
                    <thead>
                        <tr>
                            <th class="w-1">Roll Number</th>
                            <th>Status</th>
                            <th>Candidate</th>
                            <th>Job / Project</th>
                            <th>City / Center</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rollNumbers as $roll)
                        <tr>
                            <td><span class="text-primary fw-bold fs-3 tracking-tight">{{ $roll->roll_no }}</span></td>
                            <td>
                                @if($roll->slip_ready)
                                <span class="badge bg-success-lt text-success"><i class="ti ti-check me-1"></i> Ready</span>
                                @else
                                <span class="badge bg-warning-lt text-warning"><i class="ti ti-clock me-1"></i> Processing</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-medium fw-bold text-body">{{ $roll->application->candidate->user->full_name }}</div>
                                <div class="text-secondary small">{{ $roll->application->candidate->user->cnic }}</div>
                            </td>
                            <td>
                                <div class="text-body fw-medium">{{ $roll->job->title }}</div>
                                <div class="text-secondary small">{{ $roll->project->name }}</div>
                            </td>
                            <td>
                                <div class="text-body fw-medium">{{ $roll->center->name ?? 'TBD' }}</div>
                                <div class="text-secondary small">{{ $roll->city->name ?? 'TBD' }} (Batch {{ $roll->batch_no ?? '01' }})</div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('admin.rollnumbers.slip', $roll->application_id) }}" target="_blank" class="btn btn-outline-primary btn-sm btn-icon" title="View Slip PDF">
                                        <i class="ti ti-file-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5 italic">
                                <div class="empty">
                                    <div class="empty-icon text-secondary"><i class="ti ti-file-off fs-1"></i></div>
                                    <p class="empty-title">No roll numbers found.</p>
                                    <p class="empty-subtitle text-secondary">Roll numbers are generated through the Seat Engine's batching process.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rollNumbers->hasPages())
            <div class="card-footer d-flex align-items-center">
                {{ $rollNumbers->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

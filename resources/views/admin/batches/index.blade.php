@extends('layouts.dashboard')
@section('title', 'Test Sessions & Allocation')
@section('page-title', 'Examination Sessions')

@section('page-actions')
<a href="{{ route('admin.batches.create') }}" class="btn btn-primary">
    <i class="ti ti-calendar-plus me-2"></i> Schedule New Session
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">Scheduled Test Shifts</h3>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th class="w-1">Session ID</th>
                    <th>Project</th>
                    <th>Test Center</th>
                    <th>Test Date & Time</th>
                    <th>Allocation</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                <tr>
                    <td><span class="text-secondary fw-bold">#{{ $batch->id }}</span></td>
                    <td>
                        <div class="font-weight-medium text-body">{{ $batch->project->name }}</div>
                        <div class="text-secondary small">Session No: {{ $batch->batch_number }}</div>
                    </td>
                    <td>
                        <div class="font-weight-medium text-body">{{ $batch->center->name }}</div>
                        <div class="text-secondary small">{{ $batch->center->city->name }} (TCID: {{ $batch->center->tcid }})</div>
                    </td>
                    <td>
                        <div class="font-weight-medium text-body">{{ $batch->test_date->format('d M, Y') }}</div>
                        <div class="text-secondary small">{{ date('h:i A', strtotime($batch->reporting_time)) }} - {{ date('h:i A', strtotime($batch->start_time)) }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="font-weight-medium mb-1">{{ $batch->booked_seats }} / {{ $batch->total_seats }}</div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-blue" style="width: {{ ($batch->booked_seats / $batch->total_seats) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($batch->is_ready)
                        <span class="badge bg-success-lt text-success"><i class="ti ti-check me-1"></i> Published</span>
                        @else
                        <span class="badge bg-yellow-lt text-yellow"><i class="ti ti-clock me-1"></i> Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-outline-primary btn-icon btn-sm" title="View Session Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('admin.batches.attendance', $batch) }}" class="btn btn-outline-info btn-icon btn-sm" title="Attendance Tracking">
                                <i class="ti ti-user-check"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon text-primary mb-4">
                                <i class="ti ti-calendar-event" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="empty-title mb-3">No Test Sessions Scheduled Yet</h2>
                            <p class="empty-subtitle text-secondary mb-4 mx-auto" style="max-width: 600px;">
                                Welcome to the Seat Allocation Engine! To generate Roll Numbers and schedule exams, follow these 3 simple steps:
                            </p>
                            
                            <div class="row align-items-center justify-content-center text-start mx-auto mb-4" style="max-width: 800px;">
                                <div class="col-md-4 mb-3">
                                    <div class="card card-sm border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar bg-primary-lt rounded me-2">1</span>
                                                <h4 class="card-title m-0">Select Project</h4>
                                            </div>
                                            <div class="small text-secondary">Pick the recruitment project and the target city.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card card-sm border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar bg-primary-lt rounded me-2">2</span>
                                                <h4 class="card-title m-0">Set Date & Time</h4>
                                            </div>
                                            <div class="small text-secondary">Define when candidates need to report to the center.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card card-sm border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar bg-primary-lt rounded me-2">3</span>
                                                <h4 class="card-title m-0">Allocate Centers</h4>
                                            </div>
                                            <div class="small text-secondary">Choose the test centers. The system handles the rest!</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="empty-action mt-4">
                                <a href="{{ route('admin.batches.create') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                                    <i class="ti ti-calendar-plus me-2 fs-2"></i> Let's Schedule Your First Session
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $batches->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

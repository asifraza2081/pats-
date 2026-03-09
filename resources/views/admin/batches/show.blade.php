@extends('layouts.admin')
@section('title', 'Batch: BATCH-' . $batch->batch_number)
@section('page-title', 'Batch BATCH-' . $batch->batch_number . ' — ' . $batch->center->name)

@section('content')
<div class="row g-4">
    {{-- Batch Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-3">
            <h6 class="fw-bold mb-3">Batch Details</h6>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted small">Project</td><td class="small fw-semibold">{{ $batch->project->name }}</td></tr>
                <tr><td class="text-muted small">Center</td><td class="small">{{ $batch->center->name }}</td></tr>
                <tr><td class="text-muted small">TCID</td><td class="small">{{ $batch->center->tcid }}</td></tr>
                <tr><td class="text-muted small">City</td><td class="small">{{ $batch->center->city }}</td></tr>
                <tr><td class="text-muted small">Test Date</td><td class="small fw-bold">{{ $batch->test_date->format('l, d M Y') }}</td></tr>
                <tr><td class="text-muted small">Reporting</td><td class="small">{{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }}</td></tr>
                <tr><td class="text-muted small">Start Time</td><td class="small">{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</td></tr>
                <tr><td class="text-muted small">Seats</td><td class="small">{{ $batch->booked_seats }}/{{ $batch->total_seats }}</td></tr>
                <tr><td class="text-muted small">Envelope Size</td><td class="small">{{ $batch->envelope_size }}</td></tr>
            </table>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <a href="{{ route('admin.batches.edit',$batch) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                <a href="{{ route('admin.batches.attendance',$batch) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-person-check me-1"></i>Attendance</a>
            </div>
            <hr>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.batches.summary',$batch) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-printer me-1"></i>Print Batch Summary</a>
                <a href="{{ route('admin.batches.attendance-sheet',$batch) }}" target="_blank" class="btn btn-sm btn-outline-dark w-100"><i class="bi bi-printer me-1"></i>Print Attendance Sheet</a>
            </div>
            <hr>
            <form method="POST" action="{{ route('admin.batches.ready',$batch) }}" onsubmit="return confirm('Mark all slips as READY? This will notify all candidates via SMS.')">
                @csrf
                <button class="btn btn-success w-100"><i class="bi bi-send-check me-1"></i>Mark Slips Ready & Notify</button>
            </form>
        </div>
    </div>

    {{-- Roll number summary --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Roll Number Summary by Job</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr class="small text-muted">
                        <th class="px-4 py-3">Job</th><th>From</th><th>To</th><th>Count</th>
                    </tr></thead>
                    <tbody>
                        @forelse($summary as $jobId => $data)
                        <tr>
                            <td class="px-4 fw-semibold small">{{ $data['job']->title }}</td>
                            <td class="small text-primary fw-bold">{{ $data['from'] }}</td>
                            <td class="small text-primary fw-bold">{{ $data['to'] }}</td>
                            <td><span class="badge bg-primary">{{ $data['count'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No roll numbers assigned yet. Verify payments to assign roll numbers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Candidate list --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>Candidates in Batch ({{ $batch->applications->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:.85rem">
                    <thead class="table-light"><tr class="text-muted">
                        <th class="px-4 py-2">Roll No</th><th>Candidate</th><th>Post</th><th>Status</th><th>Slip</th>
                    </tr></thead>
                    <tbody>
                        @forelse($batch->applications as $app)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $app->rollNumber?->roll_number ?? '—' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $app->candidate->user->full_name }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $app->candidate->user->cnic }}</div>
                            </td>
                            <td>{{ Str::limit($app->job->title,25) }}</td>
                            <td><span class="badge bg-{{ ['submitted'=>'secondary','fee_paid'=>'primary','appeared'=>'success','absent'=>'danger'][$app->status]??'secondary' }} text-capitalize">{{ str_replace('_',' ',$app->status) }}</span></td>
                            <td>
                                @if($app->rollNumber?->slip_ready)
                                <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                <i class="bi bi-clock text-muted"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No candidates in batch yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

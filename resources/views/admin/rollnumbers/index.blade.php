@extends('layouts.admin')

@section('title', 'Roll Numbers')
@section('header', 'Roll Numbers Directory')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Assigned Roll Numbers</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Roll Number</th>
                        <th>Status</th>
                        <th>Candidate</th>
                        <th>Job / Project</th>
                        <th>Test Batch</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rollNumbers as $roll)
                        <tr>
                            <td class="ps-4 text-primary fw-bold">{{ $roll->roll_number }}</td>
                            <td>
                                @if($roll->slip_ready)
                                    <span class="badge bg-success">Ready</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                {{ $roll->application->candidate->user->first_name }} {{ $roll->application->candidate->user->last_name }}<br>
                                <small class="text-muted">{{ $roll->application->candidate->user->cnic }}</small>
                            </td>
                            <td>
                                <strong>{{ $roll->application->job->title }}</strong><br>
                                <small class="text-muted">{{ $roll->application->job->project->name }}</small>
                            </td>
                            <td>
                                Batch {{ $roll->application->batch->batch_number }}<br>
                                <small class="text-muted">{{ $roll->application->batch->center->city }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('rollnumbers.slip', $roll->application_id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Slip">
                                    <i class="bi bi-file-pdf"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No roll numbers assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rollNumbers->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $rollNumbers->links() }}
        </div>
    @endif
</div>
@endsection

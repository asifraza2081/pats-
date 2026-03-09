@extends('layouts.admin')

@section('title', 'All Applications')
@section('header', 'Applications Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Recent Candidate Applications</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">App ID</th>
                        <th>Candidate</th>
                        <th>Job / Project</th>
                        <th>Batch / Center</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="ps-4">#{{ $app->id }}</td>
                            <td>
                                <strong>{{ $app->candidate->user->first_name }} {{ $app->candidate->user->last_name }}</strong><br>
                                <small class="text-muted">{{ $app->candidate->user->cnic }}</small>
                            </td>
                            <td>
                                <strong>{{ $app->job->title }}</strong><br>
                                <small class="text-muted">{{ $app->job->project->name }}</small>
                            </td>
                            <td>
                                @if($app->batch)
                                    B-{{ $app->batch->batch_number }}<br>
                                    <small class="text-muted">{{ $app->batch->center->name }}</small>
                                @else
                                    <span class="text-danger">No Batch</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $bg = match($app->status) {
                                        'submitted' => 'bg-secondary',
                                        'fee_paid' => 'bg-info',
                                        'appeared' => 'bg-primary',
                                        'absent' => 'bg-danger',
                                        'result_declared' => 'bg-success',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $bg }}">{{ strtoupper(str_replace('_', ' ', $app->status)) }}</span>
                            </td>
                            <td>{{ $app->applied_at->format('d M, Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($applications->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection

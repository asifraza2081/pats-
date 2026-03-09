@extends('layouts.admin')
@section('title', $project->name)
@section('page-title', $project->name)

@section('content')
<div class="row g-4">
    {{-- Project Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            @if($project->logo_path)
            <img src="{{ asset('storage/'.$project->logo_path) }}" class="mb-3 rounded" style="max-height:60px">
            @endif
            <h5 class="fw-bold">{{ $project->name }}</h5>
            <div class="text-muted small mb-3">{{ $project->org_name }}</div>
            @php $sc=['draft'=>'secondary','open'=>'success','closed'=>'dark','result_declared'=>'primary']; @endphp
            <span class="badge bg-{{ $sc[$project->status] ?? 'secondary' }} mb-3">{{ ucwords(str_replace('_',' ',$project->status)) }}</span>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted small">Open Date</td><td class="small">{{ $project->open_date?->format('d M Y') ?? '—' }}</td></tr>
                <tr><td class="text-muted small">Close Date</td><td class="small">{{ $project->close_date?->format('d M Y') ?? '—' }}</td></tr>
                <tr><td class="text-muted small">Test Date</td><td class="small">{{ $project->test_date?->format('d M Y') ?? '—' }}</td></tr>
            </table>
            <div class="d-flex gap-2 mt-2">
                <a href="{{ route('admin.projects.edit',$project) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                <form method="POST" action="{{ route('admin.projects.destroy',$project) }}" onsubmit="return confirm('Delete project?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Jobs --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-primary"></i>Job Posts</h6>
                <a href="{{ route('admin.projects.jobs.create',$project) }}" class="btn btn-sm btn-pats"><i class="bi bi-plus me-1"></i>Add Job</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr class="small text-muted">
                        <th class="px-4 py-3">Code</th><th>Title</th><th>BPS</th><th>Seats</th><th>Fee</th><th></th>
                    </tr></thead>
                    <tbody>
                        @forelse($project->jobs as $job)
                        <tr>
                            <td class="px-4"><span class="badge bg-secondary">{{ str_pad($job->job_code,2,'0',STR_PAD_LEFT) }}</span></td>
                            <td class="fw-semibold small">{{ $job->title }}<div class="text-muted" style="font-size:.75rem">{{ $job->department }}</div></td>
                            <td class="small">{{ $job->bps_grade ?? '—' }}</td>
                            <td class="small">{{ $job->total_seats }}</td>
                            <td class="small">{{ number_format($job->fee) }}</td>
                            <td>
                                <a href="{{ route('admin.jobs.edit',$job) }}" class="btn btn-xs btn-outline-secondary px-2 py-1 me-1"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.jobs.destroy',$job) }}" class="d-inline" onsubmit="return confirm('Delete job?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger px-2 py-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No jobs added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Batches summary --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar2-week me-2 text-info"></i>Batches</h6>
                <a href="{{ route('admin.batches.create') }}" class="btn btn-sm btn-outline-info"><i class="bi bi-plus me-1"></i>Add Batch</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($project->batches as $batch)
                <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold small">BATCH-{{ $batch->batch_number }} — {{ $batch->center->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $batch->test_date->format('d M Y') }} · {{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }}</div>
                    </div>
                    <div class="text-end small">
                        <div class="text-muted">{{ $batch->booked_seats }}/{{ $batch->total_seats }} booked</div>
                        <a href="{{ route('admin.batches.show',$batch) }}" class="btn btn-xs btn-outline-primary px-2 py-1">Manage</a>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-3">No batches yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

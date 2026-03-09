@extends('layouts.admin')
@section('title', 'Batches')
@section('page-title', 'Batches')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.batches.create') }}" class="btn btn-pats"><i class="bi bi-plus-circle me-1"></i>New Batch</a>
</div>
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="px-4 py-3">Batch</th><th>Project</th><th>Center</th><th>Test Date</th><th>Slots</th><th>Seats</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                <tr>
                    <td class="px-4 fw-bold">BATCH-{{ $batch->batch_number }}</td>
                    <td class="small">{{ Str::limit($batch->project->name,30) }}</td>
                    <td class="small">{{ $batch->center->name }}</td>
                    <td class="small">{{ $batch->test_date->format('d M Y') }}</td>
                    <td class="small text-muted">{{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }}</td>
                    <td>
                        <div class="small">{{ $batch->booked_seats }}/{{ $batch->total_seats }}</div>
                        <div class="progress mt-1" style="height:4px;width:60px">
                            <div class="progress-bar {{ $batch->booked_seats >= $batch->total_seats ? 'bg-danger' : 'bg-success' }}"
                                 style="width:{{ $batch->total_seats>0 ? round($batch->booked_seats/$batch->total_seats*100) : 0 }}%"></div>
                        </div>
                    </td>
                    <td class="pe-4">
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.batches.show',$batch) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.batches.attendance',$batch) }}" class="btn btn-sm btn-outline-info" title="Attendance"><i class="bi bi-person-check"></i></a>
                            <a href="{{ route('admin.batches.summary',$batch) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Summary Sheet"><i class="bi bi-printer"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No batches yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
    <div class="card-footer bg-white border-0 px-4 pb-3">{{ $batches->links() }}</div>
    @endif
</div>
@endsection

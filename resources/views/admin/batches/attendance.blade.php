@extends('layouts.admin')
@section('title', 'Attendance — BATCH-' . $batch->batch_number)
@section('page-title', 'Attendance — BATCH-' . $batch->batch_number . ' · ' . $batch->center->name)

@section('content')
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.batches.show',$batch) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Batch</a>
    <a href="{{ route('admin.batches.attendance-sheet',$batch) }}" class="btn btn-sm btn-outline-dark" target="_blank"><i class="bi bi-printer me-1"></i>Print Attendance Sheet</a>
</div>

{{-- Upload scans --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-upload me-2 text-primary"></i>Upload Scanned Attendance Sheets</h6>
        <form method="POST" action="{{ route('admin.batches.scans.upload',$batch) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="file" name="scans[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf" required>
                    <div class="form-text">Accepted: JPG, PNG, PDF · Max 10MB each</div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-cloud-upload me-1"></i>Upload</button>
                </div>
            </div>
        </form>
        @if($batch->scans->count())
        <div class="mt-3">
            <div class="fw-semibold small mb-2">Uploaded Scans ({{ $batch->scans->count() }})</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($batch->scans as $scan)
                <a href="{{ asset('storage/'.$scan->file_path) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                    <i class="bi bi-file-earmark me-1"></i>Page {{ $scan->page_number }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Mark Attendance --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
        <h6 class="fw-bold mb-0"><i class="bi bi-person-check me-2 text-success"></i>Mark Attendance ({{ $batch->applications->count() }} candidates)</h6>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.batches.attendance.mark',$batch) }}">
            @csrf
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.85rem">
                    <thead class="table-light"><tr class="small text-muted">
                        <th>Roll No.</th><th>Candidate</th><th>Post</th><th class="text-center">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($batch->applications as $app)
                        <tr>
                            <td class="fw-bold text-primary">{{ $app->rollNumber?->roll_number ?? '—' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $app->candidate->user->full_name }}</div>
                                <div class="text-muted small">{{ $app->candidate->user->cnic }}</div>
                            </td>
                            <td class="small">{{ $app->job->title }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $app->id }}]" value="appeared"
                                               id="app_{{ $app->id }}" {{ in_array($app->status,['appeared']) ? 'checked' : '' }}>
                                        <label class="form-check-label text-success fw-semibold small" for="app_{{ $app->id }}">Present</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $app->id }}]" value="absent"
                                               id="abs_{{ $app->id }}" {{ in_array($app->status,['absent']) ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger fw-semibold small" for="abs_{{ $app->id }}">Absent</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No candidates in this batch.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($batch->applications->count())
            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-all me-1"></i>Save Attendance</button>
                <button type="button" class="btn btn-outline-secondary" onclick="markAll('appeared')">Mark All Present</button>
                <button type="button" class="btn btn-outline-danger" onclick="markAll('absent')">Mark All Absent</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAll(val) {
    document.querySelectorAll(`input[type=radio][value="${val}"]`).forEach(r => r.checked = true);
}
</script>
@endpush

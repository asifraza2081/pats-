@extends('layouts.dashboard')
@section('title', 'Attendance Tracking — Session ' . $batch->id)
@section('page-title', 'Pre-Result Processing')

@section('page-actions')
<a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Session
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Attendance Scans Upload -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-camera me-2"></i> Attendance Sheet Scans</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.batches.scans.upload', $batch) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Select Scanned Files <span class="text-secondary small">(JPG, PNG or PDF)</span></label>
                            <input type="file" name="scans[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-cloud-upload me-2"></i> Upload Scans
                            </button>
                        </div>
                    </div>
                </form>

                @if($batch->scans->count())
                <div class="mt-4">
                    <label class="form-label text-secondary small text-uppercase">Uploaded Pages</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($batch->scans as $scan)
                        <a href="{{ asset('storage/'.$scan->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm badge-pill">
                            <i class="ti ti-file-description me-1"></i> Page {{ $scan->page_number }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Marking Table -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-user-check me-2"></i> Mark Candidate Presence</h3>
                <div class="card-actions">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="markAll('appeared')">Mark All Present</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="markAll('absent')">Mark All Absent</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <form method="POST" action="{{ route('admin.batches.attendance.mark', $batch) }}" id="attendanceForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Roll Number</th>
                                    <th>Candidate Details</th>
                                    <th>Post</th>
                                    <th class="text-center w-1">Status Tracking</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->examRollnos as $roll)
                                <tr>
                                    <td><span class="text-primary fw-bold">{{ $roll->roll_no }}</span></td>
                                    <td>
                                        <div class="font-weight-medium text-body">{{ $roll->application->candidate->user->full_name }}</div>
                                        <div class="text-secondary small">{{ $roll->application->candidate->user->cnic }}</div>
                                    </td>
                                    <td><span class="text-secondary small">{{ Str::limit($roll->job->title, 25) }}</span></td>
                                    <td>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <label class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio" name="attendance[{{ $roll->application_id }}]" value="appeared" {{ $roll->application->status === \App\Enums\ApplicationStatus::APPEARED ? 'checked' : '' }}>
                                                <span class="form-check-label text-success fw-bold">Present</span>
                                            </label>
                                            <label class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio" name="attendance[{{ $roll->application_id }}]" value="absent" {{ $roll->application->status === \App\Enums\ApplicationStatus::ABSENT ? 'checked' : '' }}>
                                                <span class="form-check-label text-danger fw-bold">Absent</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-secondary py-5 italic">No candidates in this session.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($batch->examRollnos->count())
                    <div class="card-footer bg-light-lt text-end">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="ti ti-device-floppy me-2"></i> Save Attendance & Proceed
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
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

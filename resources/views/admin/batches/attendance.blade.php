@extends('layouts.dashboard')
@section('title', 'Attendance Tracking — Session ' . $batch->id)
@section('page-title', 'Pre-Result Processing')

@php
    $isAdmin = request()->is('admin/*');
    $backRoute = $isAdmin 
        ? route('admin.batches.show', $batch) 
        : route('examiner.sessions.show', $batch);
    $uploadRoute = $isAdmin 
        ? route('admin.batches.scans.upload', $batch) 
        : route('examiner.sessions.upload-scan', $batch);
    $markRoute = $isAdmin 
        ? route('admin.batches.attendance.mark', $batch) 
        : route('examiner.sessions.mark-attendance', $batch);
@endphp

@section('page-actions')
<a href="{{ $backRoute }}" class="btn btn-outline-secondary">
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
                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
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
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="markAllConfirmed('appeared')">Mark All Signed Present</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="markAll('absent')">Mark All Absent</button>
                    </div>
                </div>
            </div>
            <div class="alert alert-info mx-3 mt-3 mb-0 rounded-3 d-flex align-items-center gap-2">
                <i class="ti ti-info-circle fs-3 flex-shrink-0"></i>
                <span class="small fw-medium">
                    <strong>Physical Signature Required:</strong> Tick "Signed" only after you have verified the candidate's signature in the corresponding box on the physical attendance sheet. A candidate cannot be marked <em>Present</em> without confirming the signature.
                </span>
            </div>
            <div class="card-body p-0">
                <form method="POST" action="{{ $markRoute }}" id="attendanceForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Roll Number</th>
                                    <th>Candidate Details</th>
                                    <th>Post</th>
                                    <th class="text-center">Signed Sheet</th>
                                    <th class="text-center w-1">Status</th>
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
                                    <td class="text-center">
                                        <label class="form-check form-check-inline m-0" title="Confirm candidate signed the physical sheet">
                                            <input class="form-check-input sig-check" type="checkbox"
                                                data-app-id="{{ $roll->application_id }}"
                                                {{ $roll->application->status === \App\Enums\ApplicationStatus::APPEARED ? 'checked' : '' }}>
                                            <span class="form-check-label text-secondary small fw-bold">Signed</span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <label class="form-check form-check-inline m-0">
                                                <input class="form-check-input present-radio" type="radio"
                                                    name="attendance[{{ $roll->application_id }}]"
                                                    value="appeared"
                                                    data-app-id="{{ $roll->application_id }}"
                                                    {{ $roll->application->status === \App\Enums\ApplicationStatus::APPEARED ? 'checked' : '' }}
                                                    {{ $roll->application->status !== \App\Enums\ApplicationStatus::APPEARED ? 'disabled' : '' }}>
                                                <span class="form-check-label text-success fw-bold">Present</span>
                                            </label>
                                            <label class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio"
                                                    name="attendance[{{ $roll->application_id }}]"
                                                    value="absent"
                                                    {{ $roll->application->status === \App\Enums\ApplicationStatus::ABSENT ? 'checked' : '' }}>
                                                <span class="form-check-label text-danger fw-bold">Absent</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-secondary py-5 italic">No candidates in this session.</td></tr>
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
// Toggle Present radio based on signature checkbox
document.querySelectorAll('.sig-check').forEach(function(chk) {
    chk.addEventListener('change', function() {
        const appId = this.dataset.appId;
        const presentRadio = document.querySelector(`.present-radio[data-app-id="${appId}"]`);
        if (presentRadio) {
            presentRadio.disabled = !this.checked;
            if (!this.checked && presentRadio.checked) {
                // Uncheck present and switch to absent if signature is unticked
                presentRadio.checked = false;
                const absentRadio = document.querySelector(`input[name="attendance[${appId}]"][value="absent"]`);
                if (absentRadio) absentRadio.checked = true;
            }
        }
    });
});

// Mark All Present — only for rows where signature is confirmed
function markAllConfirmed(val) {
    document.querySelectorAll('.sig-check:checked').forEach(function(chk) {
        const appId = chk.dataset.appId;
        const radio = document.querySelector(`input[name="attendance[${appId}]"][value="${val}"]`);
        if (radio && !radio.disabled) radio.checked = true;
    });
}

// Mark All Absent — no signature check needed
function markAll(val) {
    document.querySelectorAll(`input[type=radio][value="${val}"]`).forEach(r => r.checked = true);
}
</script>
@endpush


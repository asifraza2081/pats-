@extends('layouts.dashboard')
@section('title', 'Test Sessions & Allocation')
@section('page-title', 'Examination Sessions')

@section('page-actions')
<div class="btn-list">
    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-print-portal">
        <i class="ti ti-printer me-2"></i> Print Portal
    </button>
    <a href="{{ route('admin.batches.create') }}" class="btn btn-primary">
        <i class="ti ti-calendar-plus me-2"></i> Schedule New Session
    </a>
</div>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">Scheduled Test Shifts</h3>
    </div>
    <div class="card-body p-0">
        @if($groupedBatches->isEmpty())
        <div class="text-center py-5">
            <div class="empty bg-transparent">
                <div class="empty-icon text-primary mb-4">
                    <i class="ti ti-calendar-event" style="font-size: 4rem;"></i>
                </div>
                <h2 class="empty-title mb-3">No Test Sessions Scheduled Yet</h2>
                <p class="empty-subtitle text-secondary mb-4 mx-auto" style="max-width: 600px;">
                    Welcome to the Seat Allocation Engine! To generate Roll Numbers and schedule exams, follow these 3 simple steps:
                </p>
                <div class="empty-action mt-4">
                    <a href="{{ route('admin.batches.create') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="ti ti-calendar-plus me-2 fs-2"></i> Let's Schedule Your First Session
                    </a>
                </div>
            </div>
        </div>
        @else
            <!-- Draft Sessions -->
            @if(isset($groupedBatches['draft']))
            <div class="card-header bg-yellow-lt border-bottom-0 pb-1">
                <h3 class="card-title fw-bold text-yellow"><i class="ti ti-clock me-2"></i> Draft Sessions</h3>
            </div>
            <div class="accordion accordion-flush">
                @foreach($groupedBatches['draft'] as $centerName => $batches)
                <div class="accordion-item shadow-none border-0 border-bottom bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3 fw-medium text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-draft-{{ Str::slug($centerName) }}">
                            <i class="ti ti-building-community text-muted me-2"></i> {{ $centerName }} (Drafting)
                            <span class="ms-auto badge bg-yellow-lt me-3">{{ collect($batches)->count() }} session(s)</span>
                        </button>
                        <div class="px-3 pb-3 bg-white">
                            <form action="{{ route('admin.batches.bulk-publish') }}" method="POST" onsubmit="return confirm('This will notify all candidates in these sessions via SMS. Proceed?')">
                                @csrf
                                @foreach($batches as $b)
                                    <input type="hidden" name="batch_ids[]" value="{{ $b->id }}">
                                @endforeach
                                <button type="submit" class="btn btn-yellow btn-sm w-100 rounded-pill fw-bold">
                                    <i class="ti ti-notification me-1"></i> Publish All Slips & Notify Candidates
                                </button>
                            </form>
                        </div>
                    </h2>
                    <div id="collapse-draft-{{ Str::slug($centerName) }}" class="accordion-collapse collapse">
                        <div class="table-responsive">
                            @include('admin.batches.partials._batch_table', ['batches' => $batches])
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Published Sessions -->
            @if(isset($groupedBatches['published']))
            <div class="card-header bg-success-lt border-bottom-0 pb-1 {{ isset($groupedBatches['draft']) ? 'border-top' : '' }}">
                <h3 class="card-title fw-bold text-success"><i class="ti ti-check me-2"></i> Published Sessions</h3>
            </div>
            <div class="accordion accordion-flush">
                @foreach($groupedBatches['published'] as $centerName => $batches)
                <div class="accordion-item shadow-none border-0 border-bottom bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button py-3 fw-medium text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-published-{{ Str::slug($centerName) }}">
                            <i class="ti ti-building-community text-success me-2"></i> {{ $centerName }}
                            <span class="ms-auto badge bg-success-lt text-success border border-success me-3">{{ collect($batches)->count() }} session(s)</span>
                        </button>
                        <div class="px-3 pb-3 bg-white border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted text-nowrap"><i class="ti ti-printer me-1"></i> Bulk Print Group:</span>
                                <form action="{{ route('admin.batches.bulk-print') }}" method="GET" target="_blank" class="d-inline">
                                    @foreach($batches as $b) <input type="hidden" name="batch_ids[]" value="{{ $b->id }}"> @endforeach
                                    <input type="hidden" name="type" value="slips">
                                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">Slips</button>
                                </form>
                                <form action="{{ route('admin.batches.bulk-print') }}" method="GET" target="_blank" class="d-inline">
                                    @foreach($batches as $b) <input type="hidden" name="batch_ids[]" value="{{ $b->id }}"> @endforeach
                                    <input type="hidden" name="type" value="attendance">
                                    <button type="submit" class="btn btn-outline-info btn-sm rounded-pill">Attendance</button>
                                </form>
                                <form action="{{ route('admin.batches.bulk-print') }}" method="GET" target="_blank" class="d-inline">
                                    @foreach($batches as $b) <input type="hidden" name="batch_ids[]" value="{{ $b->id }}"> @endforeach
                                    <input type="hidden" name="type" value="omr">
                                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill">OMR</button>
                                </form>
                            </div>
                        </div>
                    </h2>
                    <div id="collapse-published-{{ Str::slug($centerName) }}" class="accordion-collapse collapse show">
                        <div class="table-responsive">
                            @include('admin.batches.partials._batch_table', ['batches' => $batches])
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endif
    </div>
    @if($batchesPaginator->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $batchesPaginator->links('pagination::bootstrap-5') }}
    </div>
@endif
</div>

<!-- Print Portal Modal -->
<div class="modal modal-blur fade" id="modal-print-portal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-grad-pats text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="ti ti-printer me-1"></i> Examination Print Portal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light bg-opacity-50">
                <div class="mb-4">
                    <label class="form-label font-weight-bold text-primary mb-2">1. Select Recruitment Project</label>
                    <select id="project-selector" class="form-select" placeholder="Search project...">
                        <option value="">Select Project to load centers...</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->org_name }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded-3 border border-1 border-light shadow-sm">
                    <div class="row text-center g-2">
                        <div class="col-3">
                            <div class="small fw-bold text-blue"><i class="ti ti-id me-1"></i> Slips</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Candidate Entry Passes</div>
                        </div>
                        <div class="col-3 border-start">
                            <div class="small fw-bold text-info"><i class="ti ti-file-text me-1"></i> Sheet</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Hall Attendance Signatures</div>
                        </div>
                        <div class="col-3 border-start">
                            <div class="small fw-bold text-warning"><i class="ti ti-circle-check me-1"></i> OMR</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Optic-Scan Answer Sheets</div>
                        </div>
                        <div class="col-3 border-start">
                            <div class="small fw-bold text-success"><i class="ti ti-tag me-1"></i> Stickers</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Seat / OMR Label Stickers</div>
                        </div>
                    </div>
                </div>

                <!-- Sticker Printing: Range-based -->
                <div class="mt-4 p-3 bg-white rounded-3 border shadow-sm">
                    <div class="fw-bold mb-2 text-success small"><i class="ti ti-tag me-1"></i> 2. Print Stickers by Roll No Range</div>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Roll No From</label>
                            <input type="text" id="sticker-roll-from" class="form-control" placeholder="e.g. 30135221">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Roll No To</label>
                            <input type="text" id="sticker-roll-to" class="form-control" placeholder="e.g. 30135250">
                        </div>
                        <div class="col-md-4">
                            <a id="sticker-print-btn" href="#" target="_blank" class="btn btn-success w-100" onclick="return openStickerPrint(this)">
                                <i class="ti ti-tag me-1"></i> Print Stickers
                            </a>
                        </div>
                    </div>
                    <div class="form-text mt-1 text-muted">Centre & City are auto-detected from the roll number range.</div>
                </div>
                
                <div id="portal-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 text-secondary fw-medium">Fetching centers and batch allocations...</div>
                </div>

                <div id="center-list-container" class="mt-4" style="max-height: 480px; overflow-y: auto;">
                    <div class="text-center text-secondary py-5">
                        <i class="ti ti-building-broadcast shadow-sm p-4 rounded-circle bg-white mb-3 text-primary border" style="font-size: 3rem;"></i>
                        <p class="mb-0">Select a project above to list centers and generate documents.</p>
                        <small class="text-muted">Only projects with active test sessions will appear in the list.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 py-3">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Close portal</button>
                <div class="text-muted small">
                    Secure Printing Module
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const stickerBaseUrl = "{{ route('admin.batches.stickers') }}";

function openStickerPrint(el) {
    const from = document.getElementById('sticker-roll-from').value.trim();
    const to   = document.getElementById('sticker-roll-to').value.trim();
    if (!from || !to) {
        alert('Please enter both Roll No From and Roll No To.');
        return false;
    }
    el.href = stickerBaseUrl + '?roll_from=' + encodeURIComponent(from) + '&roll_to=' + encodeURIComponent(to);
    return true; // allow link to open in new tab
}

document.addEventListener('DOMContentLoaded', function () {
    const projectSelector = document.getElementById('project-selector');
    const container = document.getElementById('center-list-container');
    const loader = document.getElementById('portal-loading');

    // Initialize TomSelect if available
    let tsInstance = null;
    if (window.TomSelect && projectSelector) {
        tsInstance = new TomSelect(projectSelector, {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    }

    projectSelector.addEventListener('change', function() {
        const projectId = this.value;
        if (!projectId) {
            container.innerHTML = `<div class="text-center text-secondary py-5"><i class="ti ti-building-broadcast shadow-sm p-4 rounded-circle bg-white mb-3 text-primary border" style="font-size: 3rem;"></i><p class="mb-0">Select a project above to list centers and generate documents.</p></div>`;
            return;
        }

        // Show Loading
        container.innerHTML = '';
        loader.classList.remove('d-none');

        const url = "{{ route('admin.batches.centers-json', ':id') }}".replace(':id', projectId);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.length === 0) {
                    container.innerHTML = `<div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="ti ti-info-circle me-2"></i> No active test sessions or centers found for this project.
                    </div>`;
                    return;
                }

                const exportUrlBase = "{{ route('admin.batches.export', ':id') }}";
                let html = `
                    <div class="d-flex justify-content-between align-items-end mb-3 pb-2 border-bottom">
                        <div>
                            <h4 class="m-0 fw-bold text-dark">Centers & Sessions Map</h4>
                            <small class="text-muted">Total active centers: ${data.length}</small>
                        </div>
                        <a href="${exportUrlBase.replace(':id', projectId)}" class="btn btn-success fw-bold">
                            <i class="ti ti-file-spreadsheet me-2"></i> Export Master List (CSV)
                        </a>
                    </div>
                    <div class="row g-3">
                `;
                data.forEach(center => {
                    html += `
                        <div class="col-12">
                            <div class="card card-sm border-0 shadow-sm hover-lift">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h4 class="m-0 font-weight-bold text-dark">${center.name}</h4>
                                            <div class="text-secondary small">
                                                <i class="ti ti-map-pin me-1"></i> ${center.city} 
                                                <span class="mx-1">•</span> 
                                                <i class="ti ti-users me-1"></i> ${center.batches.length} Session(s)
                                            </div>
                                        </div>
                                        <div class="badge bg-green-lt px-3 py-2 rounded-pill">Active Scoping</div>
                                    </div>
                                    
                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                        ${center.batches.map(batch => `
                                            <div class="list-group-item d-flex align-items-center justify-content-between py-2 border-0 border-bottom last-border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs bg-primary-lt me-2 rounded">${batch.batch_number}</div>
                                                    <div>
                                                        <span class="fw-bold d-block">Session #${batch.id}</span>
                                                        <small class="text-muted">${batch.booked_seats} Candidates Allocated</small>
                                                    </div>
                                                </div>
                                                <div class="btn-group shadow-sm">
                                                    <a href="${batch.slips_url}" target="_blank" class="btn btn-white btn-sm px-3" title="Generate Roll Number Slips">
                                                        <i class="ti ti-id me-1"></i> Slips
                                                    </a>
                                                    <a href="${batch.attendance_url}" target="_blank" class="btn btn-white btn-sm px-3" title="Generate Attendance List">
                                                        <i class="ti ti-file-text me-1 text-info"></i> Sheet
                                                    </a>
                                                    <a href="${batch.omr_url}" target="_blank" class="btn btn-white btn-sm px-3" title="Generate Answer Sheets">
                                                        <i class="ti ti-circle-check me-1 text-warning"></i> OMR
                                                    </a>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            })
            .catch(error => {
                loader.classList.add('d-none');
                container.innerHTML = `<div class="alert alert-danger border-0">Failed to load centers. Error: ${error.message}</div>`;
            });
    });
});
</script>
@endpush
@endsection

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
                            <a href="{{ route('admin.batches.group-show', ['project' => $batch->project_id, 'test_date' => $batch->test_date->toDateString(), 'batch_number' => $batch->batch_number]) }}" class="btn btn-outline-purple btn-icon btn-sm" title="View Mega Session Group">
                                <i class="ti ti-layout-distribute-vertical"></i>
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
                
                <!-- Logistics Guide: Clear Definitions for Admins -->
                <div class="mt-4 p-3 bg-light rounded-3 border border-1 border-light shadow-sm">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="small fw-bold text-blue"><i class="ti ti-id me-1"></i> Slips</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Candidate Entry Passes</div>
                        </div>
                        <div class="col-4 border-start border-end">
                            <div class="small fw-bold text-info"><i class="ti ti-file-text me-1"></i> Sheet</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Hall Attendance Signatures</div>
                        </div>
                        <div class="col-4">
                            <div class="small fw-bold text-warning"><i class="ti ti-circle-check me-1"></i> OMR</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">Optic-Scan Answer Sheets</div>
                        </div>
                    </div>
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

        fetch(`/admin/batches/centers-json/${projectId}`)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.length === 0) {
                    container.innerHTML = `<div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="ti ti-info-circle me-2"></i> No active test sessions or centers found for this project.
                    </div>`;
                    return;
                }

                let html = '<div class="row g-3">';
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

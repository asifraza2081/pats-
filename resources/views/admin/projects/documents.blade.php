@extends('layouts.dashboard')
@section('title', 'Project Documents - ' . $project->name)
@section('page-title', 'Center-wise Documents')

@section('page-actions')
<a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Project
</a>
@endsection

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold">Select Center to Generate Documents</h3>
            </div>
            <div class="card-body pt-2">
                <p class="text-secondary small">Choose a test center to access and generate bulk documents for all sessions assigned to that location. Every generation is automatically archived in the institutional storage system.</p>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr>
                            <th>Center Name</th>
                            <th>Location / City</th>
                            <th>Scheduled Sessions</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->centers as $center)
                            @php
                                $centerBatches = $project->batches->where('center_id', $center->id);
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $center->name }}</div>
                                    <div class="text-secondary small">TCID: {{ $center->tcid }}</div>
                                </td>
                                <td>{{ $center->city->name }}</td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ $centerBatches->count() }} Sessions</span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-printer me-1"></i> Generate Docs
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                            <div class="dropdown-header">Batch Documents</div>
                                            @forelse($centerBatches as $batch)
                                                <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.batches.bulk-slips', $batch) }}" target="_blank">
                                                    <i class="ti ti-id me-2 text-primary"></i> 
                                                    <div>
                                                        <strong>Slips: Batch #{{ $batch->id }}</strong>
                                                        <div class="small text-muted">{{ $batch->test_date->format('d M') }} ({{ $batch->booked_seats }} Candidates)</div>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.batches.attendance-sheet', $batch) }}" target="_blank">
                                                    <i class="ti ti-clipboard-check me-2 text-success"></i> 
                                                    <div>Attendance: Batch #{{ $batch->id }}</div>
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.batches.answer-sheets', $batch) }}" target="_blank">
                                                    <i class="ti ti-file-barcode me-2 text-info"></i> 
                                                    <div>OMR Sheets: Batch #{{ $batch->id }}</div>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                            @empty
                                                <span class="dropdown-item text-muted">No sessions scheduled</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-secondary">No centers assigned to this project yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-12 mt-4">
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex">
                <div><i class="ti ti-info-circle icon alert-icon me-3"></i></div>
                <div>
                    <h4 class="alert-title fw-bold">Institutional Archiving Active</h4>
                    <p class="text-secondary mb-0">All generated PDF documents are automatically saved to: 
                        <code>storage/app/public/exports/{Date}/{Project}/{Center}/</code>
                        This ensures a physical record is maintained for every test session.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

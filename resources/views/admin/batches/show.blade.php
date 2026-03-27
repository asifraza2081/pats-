@extends('layouts.dashboard')
@section('title', 'Session Details — ' . $batch->id)
@section('page-title', 'Session Allocation Review')

@section('page-actions')
<div class="btn-list">
    <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-2"></i> All Sessions
    </a>
    <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-outline-primary">
        <i class="ti ti-edit me-2"></i> Edit Shift Rules
    </a>
</div>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Left Column: Batch Stats & Actions -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Shift Information</h3>
                <div class="card-actions">
                    @if($batch->is_ready)
                    <span class="badge bg-success-lt text-success">Published</span>
                    @else
                    <span class="badge bg-yellow-lt text-yellow">Draft</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Project</div>
                        <div class="datagrid-content fw-bold">{{ $batch->project->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Test Center</div>
                        <div class="datagrid-content">{{ $batch->center->name }} ({{ $batch->center->tcid }})</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">City</div>
                        <div class="datagrid-content text-blue fw-medium">{{ $batch->center->city->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Test Date</div>
                        <div class="datagrid-content fw-bold">{{ $batch->test_date->format('l, d M Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Reporting Time</div>
                        <div class="datagrid-content text-secondary">{{ date('h:i A', strtotime($batch->reporting_time)) }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Start Time</div>
                        <div class="datagrid-content text-secondary">{{ date('h:i A', strtotime($batch->start_time)) }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Seats Utilization</div>
                        <div class="datagrid-content">
                            <span class="fw-bold text-{{ $batch->booked_seats >= $batch->total_seats ? 'danger' : 'success' }}">
                                {{ $batch->booked_seats }} / {{ $batch->total_seats }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h4 class="fw-bold mb-3">Roll Number Ranges</h4>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr class="bg-gray-50 text-gray-700">
                                    <th class="px-4 py-3 text-left font-semibold">Job Position</th>
                                    <th class="px-4 py-3 text-center font-semibold">Roll No From</th>
                                    <th class="px-4 py-3 text-center font-semibold">Roll No To</th>
                                    <th class="px-4 py-3 text-center font-semibold">Allocated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($summary as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $row->job->title }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-sm">{{ $row->roll_from }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-sm">{{ $row->roll_to }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $row->count }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h4 class="fw-bold mb-3">Admin Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.batches.summary', $batch) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="ti ti-printer me-2"></i> Print Session Summary
                        </a>
                        <a href="{{ route('admin.batches.attendance-sheet', $batch) }}" target="_blank" class="btn btn-outline-dark">
                            <i class="ti ti-file-text me-2"></i> Print Attendance Sheet
                        </a>
                        <a href="{{ route('admin.batches.answer-sheets', $batch) }}" target="_blank" class="btn btn-outline-info">
                            <i class="ti ti-forms me-2"></i> Print Answer Sheets (Standard Style)
                        </a>
                        <a href="{{ route('admin.batches.bulk-slips', $batch) }}" target="_blank" class="btn btn-outline-success">
                            <i class="ti ti-id me-2"></i> Print Bulk Roll No Slips
                        </a>
                        <a href="{{ route('admin.batches.attendance', $batch) }}" class="btn btn-outline-primary">
                            <i class="ti ti-user-check me-2"></i> Post-Test Attendance Tracking
                        </a>
                    </div>
                </div>
            </div>
            
            @if(!$batch->is_ready)
            <div class="card-footer bg-yellow-lt">
                <form method="POST" action="{{ route('admin.batches.ready', $batch) }}" onsubmit="return confirm('Release all slips for this session and notify candidates?')">
                    @csrf
                    <button type="submit" class="btn btn-yellow w-100">
                        <i class="ti ti-broadcast me-2"></i> Publish Slips & Notify Candidates
                    </button>
                </form>
            </div>
            @else
            <div class="card-footer bg-success-lt">
                <div class="d-flex flex-column gap-2">
                    <div class="text-center py-2">
                         <span class="text-success small fw-bold"><i class="ti ti-check me-1"></i> Slips have been published.</span>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.batches.toggle-results', $batch) }}" onsubmit="return confirm('{{ $batch->results_published ? 'Open results for editing?' : 'Lock results and finalize marks? This will block further changes.' }}')">
                        @csrf
                        @if($batch->results_published)
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="ti ti-lock-open me-2"></i> Unpublish Results (Unlock)
                            </button>
                        @else
                            <button type="submit" class="btn btn-success w-100">
                                <i class="ti ti-lock me-2"></i> Publish Results (Final Lockdown)
                            </button>
                        @endif
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Roll Number Summary & Candidate List -->
    <div class="col-lg-8">
        <!-- Job-wise Roll No Ranges -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-list-numbers me-2"></i> Roll Number Ranges</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Job Post</th>
                            <th>Roll From</th>
                            <th>Roll To</th>
                            <th class="w-1">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $row)
                        <tr>
                            <td><div class="fw-bold">{{ $row->job->title }}</div></td>
                            <td><span class="badge bg-blue-lt text-blue fw-bold">{{ $row->roll_from }}</span></td>
                            <td><span class="badge bg-blue-lt text-blue fw-bold">{{ $row->roll_to }}</span></td>
                            <td><span class="text-body fw-bold">{{ $row->count }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-secondary py-3 italic">No allocations generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Candidate Roster -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-users me-2"></i> Allocation Roster ({{ $batch->examRollnos->count() }})</h3>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th>Roll Number</th>
                            <th>Candidate</th>
                            <th>Job Post</th>
                            <th>Status</th>
                            <th class="w-1">Slip</th>
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
                            <td><span class="text-secondary small">{{ Str::limit($roll->job->title, 30) }}</span></td>
                            <td>
                                <span class="badge bg-{{ $roll->application->status->color() }}-lt text-{{ $roll->application->status->color() }}">
                                    {{ $roll->application->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if($roll->slip_ready)
                                <i class="ti ti-circle-check text-success" data-bs-toggle="tooltip" title="Slip Published"></i>
                                @else
                                <i class="ti ti-clock text-secondary" data-bs-toggle="tooltip" title="Draft Slip"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No candidates assigned to this shift.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

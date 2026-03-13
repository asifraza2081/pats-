@extends('layouts.dashboard')
@section('title', 'Session Portal')
@section('page-title', 'Session Operations Hub')

@section('page-actions')
<a href="{{ route('examiner.dashboard') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Dashboard
</a>
@endsection

@section('content')
<div class="row row-cards">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="avatar avatar-xl bg-primary-lt shadow-sm mb-3">
                        <i class="ti ti-building-community fs-1"></i>
                    </span>
                    <h3 class="mb-1">{{ $batch->center->name }}</h3>
                    <div class="text-muted">{{ $batch->center->city->name }}</div>
                </div>
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Project</div>
                        <div class="datagrid-content">{{ $batch->project->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Test Date</div>
                        <div class="datagrid-content fw-bold">{{ $batch->test_date->format('l, d M Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Reporting Time</div>
                        <div class="datagrid-content">{{ $batch->reporting_time }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assigned Strength</div>
                        <div class="datagrid-content">{{ $batch->examRollnos->count() }} Candidates</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pt-3">
                <h3 class="card-title fw-bold">Critical Documents & Printing</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card card-stacked shadow-none border bg-light">
                            <div class="card-body">
                                <h3 class="fw-bold mb-1">Attendance Sheet</h3>
                                <p class="text-secondary small">Comprehensive roster with candidate photos and signature spots.</p>
                                <a href="{{ route('examiner.sessions.attendance-sheet', $batch) }}" target="_blank" class="btn btn-primary w-100">
                                    <i class="ti ti-printer me-2"></i> Print Attendance Sheet
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-stacked shadow-none border bg-light">
                            <div class="card-body">
                                <h3 class="fw-bold mb-1">Session Summary</h3>
                                <p class="text-secondary small">Summary report showing total envelopes, seat plan, and job breakdown.</p>
                                <a href="{{ route('examiner.sessions.summary', $batch) }}" target="_blank" class="btn btn-dark w-100">
                                    <i class="ti ti-file-text me-2"></i> Print Batch Summary
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h3 class="fw-bold mb-3">Session Management</h3>
                <div class="d-flex gap-2">
                    @if($batch->results_published)
                        <button class="btn btn-secondary" disabled title="Results are published and locked.">
                            <i class="ti ti-lock me-2"></i> Attendance Locked (Published)
                        </button>
                    @else
                        <a href="{{ route('examiner.sessions.attendance', $batch) }}" class="btn btn-outline-info">
                            <i class="ti ti-user-check me-2"></i> Mark Attendance / Results Scans
                        </a>
                    @endif
                    <a href="{{ route('examiner.sessions.answer-sheets', $batch) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="ti ti-file-pencil me-2"></i> Print Answer Sheets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Examiner Dashboard')
@section('page-title', 'Assigned Test Sessions')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Live & Upcoming Sessions</h3>
                <div class="card-actions">
                    <span class="badge bg-success-lt px-3">Role: Test Examiner</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Center & City</th>
                            <th>Project / Job</th>
                            <th>Date & Time</th>
                            <th>Strength</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $session->center->name }}</div>
                                <div class="text-secondary small">{{ $session->center->city->name }}</div>
                            </td>
                            <td>
                                <div class="text-body">{{ $session->project->name }}</div>
                                <div class="text-secondary small">Session #{{ $session->batch_number }}</div>
                            </td>
                            <td>
                                <div class="text-body fw-bold">{{ $session->test_date->format('d M Y') }}</div>
                                <div class="text-secondary small">{{ $session->reporting_time }} (Report)</div>
                            </td>
                            <td>
                                <div class="text-primary fw-bold">{{ $session->examRollnos->count() }} Candidates</div>
                                <div class="text-secondary small">Total Capacity: {{ $session->total_seats }}</div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('examiner.sessions.attendance-sheet', $session) }}" target="_blank" class="btn btn-outline-dark btn-sm" title="Print Attendance">
                                        <i class="ti ti-file-text"></i>
                                    </a>
                                    <a href="{{ route('examiner.sessions.answer-sheets', $session) }}" target="_blank" class="btn btn-outline-info btn-sm" title="Print Answer Sheets">
                                        <i class="ti ti-forms"></i>
                                    </a>
                                    <a href="{{ route('examiner.sessions.show', $session) }}" class="btn btn-primary btn-sm">
                                        Open Portal
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-secondary">No assigned sessions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

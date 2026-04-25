@extends('layouts.dashboard')
@section('title', 'Examiner Dashboard')
@section('page-title', 'Assigned Test Sessions')

@section('content')
<div class="row row-cards mb-4">
    <!-- Welcome Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-white" style="background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-xl rounded-circle shadow-sm border border-2 border-white border-opacity-25 bg-white bg-opacity-10" style="width: 70px; height: 70px;">
                            <i class="ti ti-user-shield fs-1"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h2 class="display-6 fw-black mb-1">Examiner Portal</h2>
                        <div class="opacity-75 fs-3">Authorized Access: <strong class="text-white">{{ auth()->user()->full_name }}</strong> <span class="mx-2">•</span> Assigned Sessions: <strong class="text-white">{{ $sessions->count() }}</strong></div>
                    </div>
                    <div class="col-md-auto mt-3 mt-md-0 d-flex gap-2">
                        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 text-center" style="min-width: 100px;">
                            <div class="fs-4 fw-black">{{ $sessions->where('test_date', '>=', now()->startOfDay())->count() }}</div>
                            <div class="small opacity-75 text-uppercase tracking-wider">Upcoming</div>
                        </div>
                        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 text-center" style="min-width: 100px;">
                            <div class="fs-4 fw-black text-yellow">{{ $sessions->where('test_date', now()->toDateString())->count() }}</div>
                            <div class="small opacity-75 text-uppercase tracking-wider">Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

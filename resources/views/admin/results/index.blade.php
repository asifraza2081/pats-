@extends('layouts.dashboard')
@section('title', 'Test Results Management')
@section('page-title', 'Performance & Scoring')

@section('page-actions')
<a href="{{ route('admin.results.upload') }}" class="btn btn-primary">
    <i class="ti ti-upload me-2"></i> Import New Results
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">Published Results Directory</h3>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Roll Number</th>
                    <th>Project / Post</th>
                    <th>Score</th>
                    <th>Percentile</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $result)
                <tr>
                    <td>
                        <div class="font-weight-medium fw-bold text-body">{{ $result->application->candidate->user->full_name }}</div>
                        <div class="text-secondary small">{{ $result->application->candidate->user->cnic }}</div>
                    </td>
                    <td><span class="text-primary fw-bold">{{ $result->roll_no }}</span></td>
                    <td>
                        <div class="font-weight-medium text-body">{{ Str::limit($result->application->job->title, 25) }}</div>
                        <div class="text-secondary small">{{ $result->application->job->project->name }}</div>
                    </td>
                    <td>
                        <div class="h4 mb-0 fw-bold">{{ number_format($result->score, 1) }} / {{ number_format($result->total_marks, 0) }}</div>
                        <div class="text-secondary small">{{ $result->percentage }}%</div>
                    </td>
                    <td>
                        <div class="fw-bold text-blue">{{ $result->percentile }}<sup>th</sup></div>
                    </td>
                    <td>
                        @php
                            $st = match($result->result_status) {
                                'pass' => ['c'=>'success', 'l'=>'Pass'],
                                'fail' => ['c'=>'danger', 'l'=>'Fail'],
                                'absent' => ['c'=>'secondary', 'l'=>'Absent'],
                                'withheld' => ['c'=>'warning', 'l'=>'Withheld'],
                                default => ['c'=>'dark', 'l'=>strtoupper((string)$result->result_status)]
                            };
                        @endphp
                        <span class="badge bg-{{ $st['c'] }}-lt text-{{ $st['c'] }}">{{ $st['l'] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.results.show', $result->application_id) }}" class="btn btn-icon btn-outline-primary btn-sm" title="View Result Card">
                            <i class="ti ti-id"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-5">
                        <div class="empty">
                            <div class="empty-icon text-secondary"><i class="ti ti-award-off fs-1"></i></div>
                            <p class="empty-title">No results have been published yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($results->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $results->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

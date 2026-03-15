@extends('layouts.dashboard')
@section('title', 'Preview Results Import')
@section('page-title', 'Analysis Results')

@section('page-actions')
<a href="{{ route('admin.results.upload') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Re-Upload
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Analysis Summary -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0 mb-4 bg-blue-lt">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-1 text-blue">Import Analysis Completed</h3>
                    <p class="text-secondary small mb-0">Found **{{ count($preview) }}** valid matches. Please review before publishing.</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.results.publish', $projectId) }}" onsubmit="return confirm('Publish these results and notify candidates?')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="ti ti-cloud-check me-2"></i> Confirm & Publish
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(!empty($warnings))
        <div class="card shadow-sm border-0 border-start border-yellow border-4 mb-4">
            <div class="card-body">
                <h4 class="text-yellow fw-bold mb-3"><i class="ti ti-alert-triangle me-2"></i> Import Warnings ({{ count($warnings) }})</h4>
                <div class="bg-white p-3 border rounded" style="max-height: 200px; overflow-y: auto;">
                    <ul class="mb-0 text-secondary small">
                        @foreach($warnings as $w)
                        <li class="mb-1 text-danger">{{ $w }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Preview Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-clipboard-data me-2"></i> Matched Candidate Records</h3>
            </div>
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th>Roll Number</th>
                            <th>Candidate</th>
                            <th>Target Post</th>
                            <th>Score</th>
                            <th>Max Marks</th>
                            <th>Status</th>
                            <th>Answer Sheet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($preview as $row)
                        <tr>
                            <td><span class="text-primary fw-bold">{{ $row['roll_number'] }}</span></td>
                            <td>
                            <td>
                                <div class="font-weight-medium text-body">{{ $row['candidate_name'] }}</div>
                                <div class="text-secondary small">{{ $row['cnic'] }}</div>
                            </td>
                            <td><span class="text-secondary small">{{ Str::limit($row['job_title'], 25) }}</span></td>
                            <td><span class="fw-bold h4 mb-0 text-blue">{{ number_format($row['score'], 1) }}</span></td>
                            <td><span class="text-secondary fw-bold">{{ number_format($row['total_marks'], 0) }}</span></td>
                            <td>
                                @php
                                    $st = match($row['result_status']) {
                                        'pass' => ['c'=>'success', 'l'=>'Pass'],
                                        'fail' => ['c'=>'danger', 'l'=>'Fail'],
                                        'absent' => ['c'=>'secondary', 'l'=>'Absent'],
                                        default => ['c'=>'dark', 'l'=>strtoupper($row['result_status'])]
                                    };
                                @endphp
                                <span class="badge bg-{{ $st['c'] }}-lt text-{{ $st['c'] }}">{{ $st['l'] }}</span>
                            </td>
                            <td>
                                @if($row['scan_path'])
                                    <span class="badge bg-success-lt text-success" title="Scan Matched: {{ basename($row['scan_path']) }}">
                                        <i class="ti ti-circle-check me-1"></i> Attached
                                    </span>
                                @else
                                    <span class="badge bg-secondary-lt text-secondary">
                                        <i class="ti ti-circle-x me-1"></i> No Scan
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5 italic">No valid records found for import.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

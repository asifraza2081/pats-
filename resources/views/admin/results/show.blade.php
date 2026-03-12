@extends('layouts.dashboard')
@section('title', 'Result Card — ' . $app->candidate->user->full_name)
@section('page-title', 'Performance Record')

@section('page-actions')
<a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> All Results
</a>
@endsection

@section('content')
<div class="row row-cards justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 border-top border-blue border-3 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="fw-bold text-primary mb-1">OFFICIAL RESULT CARD</h1>
                        <p class="text-secondary small mb-0">Project: {{ $app->job->project->name }}</p>
                    </div>
                    <div class="text-end">
                        @if($app->candidate->photo_path)
                        <span class="avatar avatar-xl rounded border shadow-sm" style="background-image: url('{{ asset('storage/'.$app->candidate->photo_path) }}'); width: 100px; height: 100px;"></span>
                        @else
                        <span class="avatar avatar-xl rounded bg-blue-lt text-blue fw-bold" style="width: 100px; height: 100px; font-size: 2.5rem">{{ substr($app->candidate->user->first_name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase">Candidate Information</label>
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Full Name</div>
                                <div class="datagrid-content fw-bold h3 mb-0">{{ $app->candidate->user->full_name }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Father's Name</div>
                                <div class="datagrid-content text-body fw-medium">{{ $app->candidate->father_name }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">CNIC Number</div>
                                <div class="datagrid-content text-body fw-medium">{{ $app->candidate->user->cnic }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase">Examination Details</label>
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Roll Number</div>
                                <div class="datagrid-content fw-bold h3 mb-0 text-blue">{{ $result->roll_no }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Job Position</div>
                                <div class="datagrid-content text-body fw-medium">{{ $app->job->title }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Date</div>
                                <div class="datagrid-content text-body fw-medium">{{ $result->published_at?->format('d M, Y') ?: $app->examRollno->test_date->format('d M, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-light-lt p-4 rounded mb-4">
                    <div class="row text-center align-items-center">
                        <div class="col-md-3 border-end">
                            <span class="text-secondary small text-uppercase d-block mb-1">Obtained Score</span>
                            <div class="h1 mb-0 fw-bold text-dark">{{ number_format($result->score, 1) }}</div>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-secondary small text-uppercase d-block mb-1">Total Marks</span>
                            <div class="h2 mb-0 text-secondary">{{ number_format($result->total_marks, 0) }}</div>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-secondary small text-uppercase d-block mb-1">Percentage</span>
                            <div class="h2 mb-0 text-blue fw-bold">{{ $result->percentage }}%</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-secondary small text-uppercase d-block mb-1">Percentile Rank</span>
                            <div class="h2 mb-0 text-primary fw-bold">{{ $result->percentile }}<sup>th</sup></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <div>
                        <span class="text-secondary small">Result Status:</span>
                        <span class="badge bg-{{ $result->result_status === 'pass' ? 'success' : ($result->result_status === 'fail' ? 'danger' : 'secondary') }}-lt px-3">
                            {{ strtoupper($result->result_status) }}
                        </span>
                    </div>
                    <div class="text-center">
                        {{-- QR Code would go here in PDF --}}
                        <div class="text-secondary small italic">Digitally verified on: {{ $result->published_at?->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light-lt text-center py-2 d-print-none">
                <button type="button" class="btn btn-ghost-primary" onclick="window.print()">
                    <i class="ti ti-printer me-2"></i> Print official copy
                </button>
            </div>
        </div>

        <div class="mt-4 text-center d-print-none">
            <p class="text-secondary small mb-0">Note: This is an administrative preview of the candidate's result card. The official PDF layout for the candidate portal may differ slightly in formatting but contains the same data.</p>
        </div>
    </div>
</div>
@endsection

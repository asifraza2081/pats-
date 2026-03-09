@extends('layouts.admin')

@section('title', 'Result Details')
@section('header', 'Candidate Result Card')

@section('content')
<div class="mb-3">
    <a href="{{ route('results.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Results
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-center py-4">
                <h4 class="mb-1">Result Card</h4>
                <p class="mb-0 text-white-50">{{ $app->job->project->name }}</p>
                <h5 class="mt-2 text-white">{{ $app->job->title }}</h5>
            </div>
            <div class="card-body p-4">
                
                <div class="row align-items-center mb-4">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($app->candidate->photo_path)
                            <img src="{{ asset('storage/'.$app->candidate->photo_path) }}" 
                                 class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="img-thumbnail rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted w-25">Roll Number</th>
                                <td class="fw-bold fs-5 text-primary">{{ $result->roll_number }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Name</th>
                                <td class="fw-bold">{{ $app->candidate->user->first_name }} {{ $app->candidate->user->last_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">CNIC</th>
                                <td>{{ $app->candidate->user->cnic }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Test Center</th>
                                <td>{{ $app->batch->center->name }}, {{ $app->batch->center->city }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row g-3 text-center mb-4">
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted mb-1">Score</h6>
                            <h3 class="mb-0">{{ number_format($result->score, 2) }} <small class="text-muted fs-6">/ {{ number_format($result->total_marks, 0) }}</small></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted mb-1">Percentage</h6>
                            <h3 class="mb-0 {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($result->percentage, 2) }}%
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted mb-1">Percentile</h6>
                            <h3 class="mb-0 text-info">{{ number_format($result->percentile, 2) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="text-center p-3 border rounded {{ $result->result_status === 'pass' ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger' }}">
                    <h5 class="mb-1 {{ $result->result_status === 'pass' ? 'text-success' : 'text-danger' }}">
                        STATUS: {{ strtoupper($result->result_status) }}
                    </h5>
                    @if($result->remarks)
                        <p class="mb-0 small text-muted">{{ $result->remarks }}</p>
                    @endif
                </div>

            </div>
            <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Published On: {{ $result->published_at?->format('d M, Y H:i') }}</small>
                <div>
                   <button onclick="window.print()" class="btn btn-sm btn-primary">
                       <i class="bi bi-printer"></i> Print Result
                   </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

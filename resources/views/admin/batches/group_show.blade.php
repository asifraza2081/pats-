@extends('layouts.dashboard')
@section('title', 'Mega Session Group')
@section('page-title', 'Mega Session Viewer')

@section('page-actions')
<div class="btn-list">
    <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary shadow-sm">
        <i class="ti ti-arrow-left me-2"></i> All Sessions
    </a>
</div>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Top Stats Bar -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-3 bg-primary text-primary-fg">
            <div class="card-stamp">
                <div class="card-stamp-icon bg-white text-primary">
                    <i class="ti ti-building-community"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h3 class="mb-1 fw-bold fs-2">{{ $project->name }}</h3>
                        <div class="opacity-75 fs-4">
                            <i class="ti ti-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($testDate)->format('l, d M Y') }}
                            <span class="mx-2">|</span> 
                            <i class="ti ti-clock me-1"></i> BATCH-{{ $batchNumber }}
                        </div>
                    </div>
                    <div class="col-md-7 d-flex justify-content-md-end gap-4 mt-3 mt-md-0">
                        <div class="text-center px-4 border-end border-light border-opacity-25">
                            <div class="text-uppercase tracking-wide opacity-75 fs-6 mb-1">Total Centers</div>
                            <div class="fs-2 fw-black">{{ $batches->count() }}</div>
                        </div>
                        <div class="text-center px-4 border-end border-light border-opacity-25">
                            <div class="text-uppercase tracking-wide opacity-75 fs-6 mb-1">Mega Capacity</div>
                            <div class="fs-2 fw-black">{{ number_format($totalSeats) }}</div>
                        </div>
                        <div class="text-center px-4">
                            <div class="text-uppercase tracking-wide opacity-75 fs-6 mb-1">Total Allocated</div>
                            <div class="fs-2 fw-black">{{ number_format($bookedSeats) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grouped Candidates View -->
    <div class="col-12">
        @foreach($groupedCandidates as $centerName => $jobs)
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
            <div class="card-header border-bottom bg-light bg-opacity-50 pb-2 pt-3 d-flex justify-content-between">
                <h3 class="card-title fw-bold text-navy"><i class="ti ti-map-pin text-teal me-2"></i> {{ $centerName }}</h3>
                <span class="badge bg-blue-lt px-3 py-2 fw-bold fs-5 shadow-sm">{{ $jobs->flatten()->count() }} Candidates</span>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="accordion-center-{{ Str::slug($centerName) }}">
                    @foreach($jobs as $jobTitle => $candidates)
                    <div class="accordion-item shadow-none border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-3 fw-medium text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($centerName . '-' . $jobTitle) }}">
                                <i class="ti ti-briefcase text-muted me-2"></i>
                                {{ $jobTitle }}
                                <span class="ms-auto badge bg-indigo-lt me-3">{{ $candidates->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapse-{{ Str::slug($centerName . '-' . $jobTitle) }}" class="accordion-collapse collapse" data-bs-parent="#accordion-center-{{ Str::slug($centerName) }}">
                            <div class="accordion-body p-0 pt-0">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-hover card-table text-nowrap">
                                        <thead>
                                            <tr class="bg-light bg-opacity-75">
                                                <th class="w-1 text-center font-semibold">Sr.</th>
                                                <th class="font-semibold text-primary">Roll Number</th>
                                                <th class="font-semibold">Candidate Name</th>
                                                <th class="font-semibold">CNIC / Mobile</th>
                                                <th class="font-semibold text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $index => $roll)
                                            <tr class="bg-white">
                                                <td class="text-center text-secondary small">{{ $index + 1 }}</td>
                                                <td><span class="badge border border-primary text-primary fw-bold font-mono px-2 py-1 fs-5 shadow-sm">{{ $roll->roll_no }}</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm rounded bg-primary-lt me-2">{{ substr($roll->application->candidate->user->first_name, 0, 1) }}</span>
                                                        <div class="fw-bold text-body">{{ $roll->application->candidate->user->full_name }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="font-mono text-muted text-sm">{{ $roll->application->candidate->user->cnic }}</div>
                                                    <div class="small text-secondary">{{ $roll->application->candidate->user->phone_number }}</div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.applications.show', $roll->application->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">View App</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

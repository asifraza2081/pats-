@extends('layouts.dashboard')
@section('title', 'Application Details')
@section('page-title', 'Application Profile')

@section('page-actions')
<a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> List All
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Candidate Sidebar Info -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body">
                <div class="mb-3">
                    @if($app->candidate->photo_path)
                    <span class="avatar avatar-xl rounded-circle border shadow-sm" style="background-image: url('{{ asset('storage/'.$app->candidate->photo_path) }}')"></span>
                    @else
                    <span class="avatar avatar-xl rounded-circle bg-blue-lt text-blue fw-bold fs-1">{{ substr($app->candidate->user->first_name, 0, 1) }}</span>
                    @endif
                </div>
                <h3 class="m-0 mb-1 fw-bold text-body">{{ $app->candidate->user->full_name }}</h3>
                <div class="text-secondary small mb-3"><i class="ti ti-id me-1"></i> {{ $app->candidate->user->cnic }}</div>
                
                <span class="badge bg-{{ $app->status->color() }}-lt text-{{ $app->status->color() }} px-3 py-2 mb-4">
                    {{ $app->status->label() }}
                </span>

                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label text-secondary fs-5 mb-1">Contact Details</label>
                        <div class="d-flex align-items-center mb-1"><i class="ti ti-phone text-secondary me-2"></i> <strong>{{ $app->candidate->user->phone }}</strong></div>
                        <div class="d-flex align-items-center"><i class="ti ti-mail text-secondary me-2"></i> <span class="text-secondary small">{{ $app->candidate->user->email ?: 'No Email' }}</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fs-5 mb-1">Personal Info</label>
                        <div class="text-secondary small">Father's Name: <span class="text-body fw-medium">{{ $app->candidate->father_name }}</span></div>
                        <div class="text-secondary small">DOB: <span class="text-body fw-medium">{{ $app->candidate->dob?->format('d M Y') }} ({{ $app->candidate->age }} yrs)</span></div>
                        <div class="text-secondary small">Domicile: <span class="text-body fw-medium">{{ $app->candidate->district_of_domicile }}, {{ $app->candidate->province_of_domicile }}</span></div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light-lt">
                <div class="btn-list">
                    @if($app->status !== \App\Enums\ApplicationStatus::FEE_PAID)
                    <form action="{{ route('admin.applications.mark-paid', $app) }}" method="POST" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2 shadow-sm" onclick="return confirm('Forcibly mark this candidate as paid?')">
                            <i class="ti ti-check me-2"></i> Mark as Paid
                        </button>
                    </form>
                    @endif
                    @if($app->examRollno)
                    <a href="{{ route('admin.rollnumbers.slip', $app) }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="ti ti-file-download me-2"></i> Print Slip
                    </a>
                    @endif
                    @if($app->result)
                    <a href="{{ route('admin.results.show', $app) }}" class="btn btn-outline-success w-100">
                        <i class="ti ti-trophy me-2"></i> View Score
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Job, Education, Experience -->
    <div class="col-lg-8">
        <!-- Job Post Context -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-briefcase me-2"></i> Applied Position</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Project Name</div>
                        <div class="datagrid-content fw-bold">{{ $app->job->project->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Target Job Post</div>
                        <div class="datagrid-content">{{ $app->job->title }} ({{ $app->job->job_code }})</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Desired City</div>
                        <div class="datagrid-content text-blue fw-medium">{{ $app->desiredTestCity->name ?? 'Not Set' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Applied On</div>
                        <div class="datagrid-content">{{ $app->applied_at->format('d M, Y \a\t H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seat Allocation Status -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-map-pin me-2"></i> Examination Status</h3>
            </div>
            <div class="card-body">
                @if($app->examRollno)
                <div class="row items-center border p-3 rounded bg-blue-lt">
                    <div class="col-md-3 text-center border-end">
                        <div class="text-secondary small mb-1">Roll Number</div>
                        <div class="h2 mb-0 text-blue fw-bold">{{ $app->examRollno->roll_no }}</div>
                    </div>
                    <div class="col-md-9 ps-4">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Center</div>
                                <div class="datagrid-content fw-bold">{{ $app->examRollno->center->name }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Test Schedule</div>
                                <div class="datagrid-content">{{ $app->examRollno->batch->test_date->format('l, d M Y') }} at {{ date('h:i A', strtotime($app->examRollno->batch->start_time)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning mb-0">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-triangle fs-2 me-2"></i></div>
                        <div>
                            <div class="fw-bold">Pending Allocation</div>
                            <div class="text-secondary">This candidate has not been assigned a seat or roll number yet. Batch assignment usually happens after the project closing date.</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Education Records -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-school me-2"></i> Education Background</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Degree / Level</th>
                            <th>Institute</th>
                            <th>Passing Year</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($app->candidate->education as $edu)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $edu->degree_name }}</div>
                                <div class="text-secondary small">Level {{ $edu->degree_level }}</div>
                            </td>
                            <td>{{ $edu->institution }}</td>
                            <td>{{ $edu->passing_year }}</td>
                            <td>{{ $edu->obtained_marks }} / {{ $edu->total_marks }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-secondary italic py-3">No education records provided.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Work Experience -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-history me-2"></i> Professional Experience</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Designation</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($app->candidate->experience as $exp)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $exp->organization }}</div>
                                <div class="text-secondary small">Sector: {{ $exp->sector }}</div>
                            </td>
                            <td>{{ $exp->designation }}</td>
                            <td>
                                {{ $exp->start_date->format('M Y') }} — 
                                {{ $exp->is_current ? 'Present' : $exp->end_date?->format('M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-secondary italic py-3">No work experience records provided.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Eligibility Debugging -->
        @if($app->eligibility_warnings)
        <div class="card shadow-sm border-0 bg-red-lt mb-4">
            <div class="card-body">
                <h4 class="text-red fw-bold mb-3"><i class="ti ti-alert-circle me-2"></i> Screening Warnings</h4>
                <ul class="mb-0">
                    @foreach($app->eligibility_warnings as $warning)
                    <li class="text-red-600 mb-1">{{ $warning }}</li>
                    @endforeach
                </ul>
                <div class="mt-3 small text-secondary italic">Note: These warnings were generated based on candidate's profile at the time of submission.</div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

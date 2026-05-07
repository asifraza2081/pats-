@extends('layouts.dashboard')
@section('title', 'Candidate Profile — ' . $candidate->user->full_name)
@section('page-title', 'Full Candidate Profile')

@section('page-actions')
<a href="{{ route('admin.candidates.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> List All
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body">
                <div class="mb-3">
                    @if($candidate->photo_path)
                    <span class="avatar avatar-xl rounded-circle border shadow-sm" style="background-image: url('{{ asset('storage/'.$candidate->photo_path) }}')"></span>
                    @else
                    <span class="avatar avatar-xl rounded-circle bg-blue-lt text-blue fw-bold fs-1">{{ substr($candidate->user->first_name, 0, 1) }}</span>
                    @endif
                </div>
                <h3 class="m-0 mb-1 fw-bold text-body">{{ $candidate->user->full_name }}</h3>
                <div class="text-secondary small mb-3"><i class="ti ti-id me-1"></i> {{ $candidate->user->cnic }}</div>
                
                <div class="text-start border-top pt-3">
                    <label class="form-label text-primary fw-bold mb-2 small text-uppercase tracking-wider">Contact Details</label>
                    <div class="mb-2"><i class="ti ti-phone text-secondary me-2"></i> <strong>{{ $candidate->user->phone }}</strong></div>
                    <div class="mb-3"><i class="ti ti-mail text-secondary me-2"></i> <span class="text-secondary">{{ $candidate->user->email ?: 'No Email' }}</span></div>

                    <label class="form-label text-primary fw-bold mb-2 small text-uppercase tracking-wider border-top pt-2 d-block">Personal Details</label>
                    <div class="text-secondary small mb-1">Father's Name: <span class="text-body fw-medium">{{ $candidate->father_name }}</span></div>
                    <div class="text-secondary small mb-1">DOB: <span class="text-body fw-medium">{{ $candidate->dob?->format('d M Y') ?? 'N/A' }}</span></div>
                    <div class="text-secondary small mb-1">Gender: <span class="text-body fw-medium">{{ ucfirst($candidate->gender) }}</span></div>
                    <div class="text-secondary small mb-1">Religion: <span class="text-body fw-medium">{{ ucfirst($candidate->religion) }}</span></div>
                    <div class="text-secondary small mb-1">Domicile: <span class="text-body fw-medium">{{ $candidate->domicileCity?->name }}, {{ $candidate->domicileCity?->province }}</span></div>
                    
                    <label class="form-label text-primary fw-bold mb-2 small text-uppercase tracking-wider border-top pt-2 d-block mt-2">Address</label>
                    <div class="text-secondary small">{{ $candidate->postal_address }}</div>
                    <div class="text-body fw-bold small">{{ $candidate->addressCity?->name }}, {{ $candidate->addressCity?->province }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Education -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-school me-2"></i> Academic History</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Degree</th>
                            <th>Institute</th>
                            <th>Year</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidate->education as $edu)
                        <tr>
                            <td><div class="fw-bold">{{ $edu->degree_name }}</div><div class="text-secondary small">{{ $edu->degree_level }}</div></td>
                            <td>{{ $edu->institution }}</td>
                            <td>{{ $edu->passing_year }}</td>
                            <td>{{ $edu->obtained_marks }} / {{ $edu->total_marks }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-secondary py-3 italic">No academic records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Experience -->
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
                        @forelse($candidate->experience as $exp)
                        <tr>
                            <td><div class="fw-bold">{{ $exp->organization_name }}</div><div class="text-secondary small">{{ $exp->job_type }}</div></td>
                            <td>{{ $exp->designation }}</td>
                            <td>{{ $exp->from_date->format('M Y') }} - {{ $exp->is_current ? 'Present' : $exp->to_date?->format('M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-secondary py-3 italic">No work history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Applications History -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-folder me-2"></i> Applications Submitted</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th>Project / Job</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidate->applications as $app)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $app->job->title }}</div>
                                <div class="text-secondary small">{{ $app->job->project->name }}</div>
                            </td>
                            <td>{{ $app->applied_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status->color() }}-lt px-2">
                                    {{ $app->status->label() }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-ghost-primary btn-icon btn-sm" title="View App Details">
                                    <i class="ti ti-chevron-right fs-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-secondary py-3">This candidate hasn't applied for any projects yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

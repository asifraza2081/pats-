@extends('layouts.dashboard')
@section('title', 'Candidate Bio')
@section('page-title', 'My Professional Profile')

@section('page-actions')
<a href="{{ route('candidate.profile.show') }}" class="btn btn-outline-primary">
    <i class="ti ti-edit me-2"></i> Edit Profile
</a>
<button onclick="window.print()" class="btn btn-dark d-print-none">
    <i class="ti ti-printer me-2"></i> Print Bio
</button>
@endsection

@section('content')
<div class="row row-cards justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <!-- Header with Avatar -->
                <div class="row align-items-center mb-5">
                    <div class="col-auto">
                        <span class="avatar avatar-xl rounded shadow-sm" style="width: 120px; height: 120px; background-image: url('{{ $candidate->photo_path ? Storage::url($candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($candidate->user->first_name).'&size=120' }}')"></span>
                    </div>
                    <div class="col">
                        <h1 class="fw-bold text-dark mb-1">{{ $candidate->user->full_name }}</h1>
                        <div class="fs-2 text-muted mb-2">
                           CNIC: <span class="text-dark fw-semibold">{{ $candidate->user->cnic }}</span> • 
                           Email: <span class="text-dark fw-semibold">{{ $candidate->user->email }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-lt">Candidate Profile Verified</span>
                            <span class="badge bg-success-lt">Account Active</span>
                        </div>
                    </div>
                </div>

                <div class="row g-5">
                    <!-- Personal Info -->
                    <div class="col-md-6 border-end">
                        <h3 class="card-title fw-bold text-primary mb-3">Personal Information</h3>
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Father's Name</div>
                                <div class="datagrid-content">{{ $candidate->father_name ?? 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Date of Birth</div>
                                <div class="datagrid-content">{{ $candidate->dob ? $candidate->dob->format('d M Y') : 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Gender</div>
                                <div class="datagrid-content">{{ $candidate->gender ?? 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Religion</div>
                                <div class="datagrid-content">{{ $candidate->religion ?? 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Domicile</div>
                                <div class="datagrid-content">{{ $candidate->domicileCity?->name }} ({{ $candidate->province_of_domicile }})</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Contact Number</div>
                                <div class="datagrid-content">{{ $candidate->user->phone }}</div>
                            </div>
                        </div>
                        
                        <h3 class="card-title fw-bold text-primary mb-3 mt-5">Contact Details</h3>
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Postal Address</div>
                                <div class="datagrid-content">{{ $candidate->postal_address ?? 'N/A' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Postal City</div>
                                <div class="datagrid-content">{{ $candidate->addressCity?->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Experience -->
                    <div class="col-md-6">
                        <h3 class="card-title fw-bold text-primary mb-3">Education History</h3>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-nowrap card-table">
                                <thead>
                                    <tr>
                                        <th>Degree</th>
                                        <th>Passing</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($candidate->education as $edu)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $edu->degree_name }}</div>
                                            <div class="text-secondary small">{{ $edu->institution }}</div>
                                        </td>
                                        <td class="text-secondary">{{ $edu->passing_year }}</td>
                                        <td>{{ $edu->obtained_marks }}/{{ $edu->total_marks }} ({{ $edu->marks_type }})</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted">No education records found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h3 class="card-title fw-bold text-primary mb-3 mt-5">Work Experience</h3>
                        <ul class="list-unstyled list-separated">
                            @forelse($candidate->experience as $exp)
                            <li class="list-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-blue-lt"><i class="ti ti-briefcase"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold mb-0 text-dark">{{ $exp->designation }}</div>
                                        <div class="text-secondary small">{{ $exp->organization_name }} • {{ $exp->job_type }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="text-secondary small">
                                            {{ $exp->from_date->format('M Y') }} - 
                                            {{ $exp->is_current ? 'Present' : ($exp->to_date ? $exp->to_date->format('M Y') : 'N/A') }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="list-item text-center text-muted">No work experience mentioned.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

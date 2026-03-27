@extends('layouts.dashboard')
@section('title', $project->name)
@section('page-title', 'Overview')

@section('page-actions')
<a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary me-2">
    <i class="ti ti-arrow-left me-2"></i> Directory
</a>
<a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-primary d-none d-sm-inline-block">
    <i class="ti ti-edit me-2"></i> Edit Project
</a>
@endsection

@section('content')
<div class="row row-cards">
    <!-- Project Meta Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                @if($project->logo_path)
                <span class="avatar avatar-xl mb-3 rounded" style="background-image: url('{{ asset('storage/'.$project->logo_path) }}')"></span>
                @else
                <span class="avatar avatar-xl mb-3 rounded bg-blue-lt text-blue fw-bold">{{ substr($project->name, 0, 2) }}</span>
                @endif
                <h3 class="m-0 mb-1 fw-bold">{{ $project->name }}</h3>
                <div class="text-secondary mb-3">{{ $project->org_name }}</div>
                
                <span class="badge bg-{{ $project->status->color() }}-lt text-{{ $project->status->color() }} text-capitalize px-3 py-2 mb-4">
                    {{ $project->status->label() }}
                </span>
                
                <div class="text-start">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1"><i class="ti ti-calendar-event text-secondary me-2"></i> <strong>Opening Date</strong></div>
                        <div class="ms-4 text-secondary">{{ $project->open_date?->format('l, d M Y') ?? 'Not Set' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1"><i class="ti ti-calendar-event text-secondary me-2"></i> <strong>Closing Date</strong></div>
                        <div class="ms-4 text-secondary">{{ $project->close_date?->format('l, d M Y') ?? 'Not Set' }}</div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1"><i class="ti ti-calendar-check text-secondary me-2"></i> <strong>Target Test Date</strong></div>
                        <div class="ms-4 text-secondary">{{ $project->test_date?->format('l, d M Y') ?? 'TBD' }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline-secondary w-50">Configure</a>
                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="w-50" onsubmit="return confirm('Delete this project permanently?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="ti ti-trash me-1"></i> Terminate</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content: Jobs & Batches -->
    <div class="col-lg-8">
        <!-- Job Posts -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold"><i class="ti ti-briefcase text-primary me-2 fs-2 align-text-bottom"></i> Configured Job Posts</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.projects.jobs.create', $project) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i> Add Post
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable table-hover">
                    <thead>
                        <tr>
                            <th class="w-1">Code</th>
                            <th>Post Title</th>
                            <th>BPS Grade</th>
                            <th>Target Seats</th>
                            <th>Challan Fee</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->jobs as $job)
                        <tr>
                            <td><span class="badge bg-secondary-lt text-secondary">{{ str_pad($job->job_code, 2, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="font-weight-medium fw-bold text-body">{{ $job->title }}</div>
                                <div class="text-secondary small">{{ $job->department ?: 'General Dept' }}</div>
                            </td>
                            <td><span class="text-secondary">{{ $job->bps_grade ? 'BPS-'.$job->bps_grade : '—' }}</span></td>
                            <td><span class="text-secondary">{{ $job->total_seats }}</span></td>
                            <td><span class="fw-semibold text-success">PKR {{ number_format($job->fee) }}</span></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Edit Post"><i class="ti ti-edit"></i></a>
                                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="d-inline" onsubmit="return confirm('Remove this job post?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Post"><i class="ti ti-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                <div class="empty">
                                    <div class="empty-icon text-secondary"><i class="ti ti-briefcase-off fs-1"></i></div>
                                    <p class="empty-title">No job posts created yet.</p>
                                    <p class="empty-subtitle text-secondary">Start configuring the positions available for this project.</p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.projects.jobs.create', $project) }}" class="btn btn-primary"><i class="ti ti-plus me-2"></i>Add First Job Post</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Test Centers & Examiners -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold"><i class="ti ti-map-pin text-primary me-2 fs-2 align-text-bottom"></i> Assigned Test Centers</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.projects.centers.index', $project) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-settings me-1"></i> Manage Centers
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable table-hover">
                    <thead>
                        <tr>
                            <th>Center Name</th>
                            <th>City</th>
                            <th>Assigned Examiner</th>
                            <th>Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->centers as $center)
                        <tr>
                            <td><span class="fw-bold">{{ $center->name }}</span></td>
                            <td>{{ $center->city->name }}</td>
                            <td>
                                @if($center->pivot->examiner_id)
                                    @php $examiner = \App\Models\User::find($center->pivot->examiner_id); @endphp
                                    <span class="badge bg-blue-lt">{{ $examiner?->full_name ?? 'Unknown' }}</span>
                                @else
                                    <span class="text-muted small">No Examiner Assigned</span>
                                @endif
                            </td>
                            <td>{{ $center->seating_capacity }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">No centers assigned to this project yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Test Session Scheduling Quick Link -->
        <div class="card shadow-sm border-0 bg-primary-lt">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-primary mb-1"><i class="ti ti-users-group me-2"></i> Test Session Scheduling</h4>
                        <p class="text-secondary mb-0 small">Schedule test sessions, select centers, and allocate candidates who have applied to this project.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.batches.index', ['project_id' => $project->id]) }}" class="btn btn-outline-primary shadow-sm me-2"><i class="ti ti-calendar-plus me-2"></i> Schedule Sessions</a>
                        <a href="{{ route('admin.projects.documents', $project) }}" class="btn btn-primary shadow-sm"><i class="ti ti-printer me-2"></i> Print Center Documents</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

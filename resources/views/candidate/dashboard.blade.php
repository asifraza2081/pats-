@extends('layouts.dashboard')

@section('page-title', 'Candidate Dashboard')

@section('content')
<div class="row row-cards">
    <!-- Welcome & Profile Completion -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-xl me-3 rounded" style="background-image: url('{{ $candidate?->photo_path ? Storage::url($candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->first_name) }}')"></span>
                    <div>
                        <h2 class="m-0 mb-1">Welcome back, {{ $user->first_name }}! 👋</h2>
                        <div class="text-muted mb-2">CNIC: <strong>{{ $user->cnic }}</strong> • Applied to <strong>{{ $applications->count() }}</strong> jobs.</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('candidate.profile.bio') }}" class="btn btn-outline-primary btn-sm btn-pill">
                                <i class="ti ti-user-circle me-1"></i> View My professional Bio
                            </a>
                            <a href="{{ route('candidate.profile.show') }}" class="btn btn-outline-secondary btn-sm btn-pill">
                                <i class="ti ti-edit me-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dynamic Next Step Indicator -->
            <div class="card-footer bg-blue-lt py-2 px-3 border-0">
                <div class="d-flex align-items-center">
                    <span class="status-dot status-dot-animated bg-primary me-2"></span>
                    <span class="fw-bold text-primary small">NEXT STEP: </span>
                    <span class="text-primary small ms-2">
                        @if($completion < 100)
                            Your profile is incomplete. Click "Edit Profile" to finish and become eligible for jobs.
                        @elseif($applications->isEmpty())
                            Profile complete! Click "Browse Open Projects" below to find and apply for jobs.
                        @elseif($applications->where('status', 'submitted')->isNotEmpty())
                            You have pending applications. Please download and pay the Challan to proceed to test scheduling.
                        @else
                            All set! We will notify you via SMS when your Roll Number Slip is ready for download.
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-auto">
                        <h3 class="card-title mb-0">Profile Completion</h3>
                    </div>
                    <div>
                        <span class="text-{{ $completion === 100 ? 'success' : 'warning' }} fs-2 fw-bold">{{ $completion }}%</span>
                    </div>
                </div>
                <div class="progress progress-lg mb-3">
                    <div class="progress-bar {{ $completion === 100 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $completion }}%" role="progressbar"></div>
                </div>
                <div>
                    @if($completion === 100)
                        <div class="text-success d-flex align-items-center justify-content-center bg-success-lt py-1 rounded">
                            <i class="ti ti-discount-check fs-2 me-1"></i> Verified & Eligible
                        </div>
                    @else
                        <a href="{{ route('candidate.profile.show') }}" class="btn btn-warning w-100">
                           <i class="ti ti-edit me-1"></i> Complete Profile Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Missing Information Checklist -->
    @php
        $missingFields = [];
        if (!$candidate) {
            $missingFields[] = 'Initial Profile Setup';
        } else {
            $fields = [
                'father_name' => 'Father\'s Name', 
                'dob' => 'Date of Birth', 
                'gender' => 'Gender', 
                'marital_status' => 'Marital Status', 
                'religion' => 'Religion',
                'domicile_city_id' => 'Domicile City', 
                'address_city_id' => 'Postal City',
                'permanent_address' => 'Permanent Address', 
                'postal_address' => 'Postal Address',
                'photo_path' => 'Profile Picture', 
                'cnic_front_path' => 'CNIC Front Image',
            ];
            foreach ($fields as $col => $label) {
                if (empty($candidate->$col)) $missingFields[] = $label;
            }
            if (!$candidate->education()->exists()) $missingFields[] = 'Add Minimum 1 Degree/Qualification';
        }
    @endphp

    @if(count($missingFields) > 0)
    <div class="col-12">
        <div class="alert alert-important alert-warning alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-alert-triangle fs-2 me-3 mt-1"></i>
                </div>
                <div>
                    <strong class="fs-3">Missing Required Information</strong>
                    <p class="mb-2 mt-1">You cannot apply for any jobs until your profile is 100% complete. Please update the following:</p>
                    <div class="row">
                        @foreach($missingFields as $mf)
                            <div class="col-md-4 col-sm-6 mb-1">
                                <i class="ti ti-x text-danger me-1 fw-bold"></i> {{ $mf }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Applications List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-1">
                <h3 class="card-title"><i class="ti ti-file-text me-2 text-primary"></i> My Job Applications</h3>
            </div>
            @if($applications->isEmpty())
                <div class="card-body text-center py-5">
                    <i class="ti ti-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                    <h3 class="text-muted">No Applications Found</h3>
                    <p class="text-muted mb-4">You have not submitted any job applications yet.</p>
                    <a href="{{ route('projects') }}" class="btn btn-primary">Browse Open Projects</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                            <tr>
                                <th>Project & Job Title</th>
                                <th>Test City</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                                @php
                                    $statusColors = [
                                        'submitted' => 'secondary',
                                        'fee_paid' => 'primary',
                                        'appeared' => 'success',
                                        'absent' => 'danger',
                                        'result_declared' => 'purple'
                                    ];
                                    $color = $statusColors[$app->status] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td data-label="Job Title">
                                        <div class="font-weight-medium">{{ $app->job->title }}</div>
                                        <div class="text-muted"><a href="#" class="text-reset">{{ $app->job->project->name }}</a></div>
                                        @if(!empty($app->eligibility_warnings))
                                            <div class="text-warning small mt-1"><i class="ti ti-alert-circle"></i> View eligibility warnings</div>
                                        @endif
                                    </td>
                                    <td data-label="Test City">
                                        {{ $app->desiredTestCity ? $app->desiredTestCity->name : 'N/A' }}
                                    </td>
                                    <td data-label="Applied Date" class="text-muted">
                                        {{ $app->applied_at->format('d M Y') }}
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge bg-{{ $color }} me-1"></span> {{ str_replace('_', ' ', Str::title($app->status)) }}
                                        @if($app->examRollno)
                                            <div class="small fw-bold text-primary mt-1">Roll No: {{ $app->examRollno->roll_no }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if($app->payment && $app->payment->status === 'pending' && $app->job->project->isRegistrationOpen())
                                                <a href="{{ route('candidate.challan', $app) }}" class="btn btn-sm btn-outline-dark" target="_blank">
                                                    <i class="ti ti-receipt me-1"></i> Challan
                                                </a>
                                            @endif
                                            @if($app->examRollno && $app->examRollno->slip_ready)
                                                <a href="{{ route('candidate.slip', $app) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="ti ti-download me-1"></i> Slip
                                                </a>
                                            @endif
                                            @if($app->result && $app->result->isPublished())
                                                <a href="{{ route('candidate.result', $app) }}" class="btn btn-sm btn-success">
                                                    <i class="ti ti-chart-bar me-1"></i> Result
                                                </a>
                                            @endif
                                            @if($app->payment && $app->payment->status === 'paid' && !$app->examRollno)
                                                <span class="badge bg-info-lt small"><i class="ti ti-hourglass-empty me-1"></i> Processing</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

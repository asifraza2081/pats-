@extends('layouts.dashboard')

@section('page-title', 'Candidate Dashboard')

@section('content')
<div class="row row-cards mb-4">
    <!-- Welcome & Banner (takes full width, beautiful gradient) -->
    <div class="col-12">
        <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-white" style="background: linear-gradient(135deg, #0a3d62 0%, #1e5a8c 100%);">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-xl rounded-circle shadow-sm border border-2 border-white border-opacity-25" style="background-image: url('{{ $candidate?->photo_path ? asset('storage/'.$candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->first_name).'&background=random' }}'); width: 80px; height: 80px;"></span>
                    </div>
                    <div class="col">
                        <h2 class="display-6 fw-black mb-1">Welcome back, {{ $user->first_name }}! 👋</h2>
                        <div class="opacity-75 fs-3 mb-3">CNIC: <strong class="text-white">{{ $user->cnic }}</strong> <span class="mx-2">•</span> Active Applications: <strong class="text-white">{{ $applications->count() }}</strong></div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('candidate.profile.bio') }}" class="btn btn-dark rounded-pill border-0 shadow-sm px-4">
                                <i class="ti ti-user-circle me-2"></i> View Professional Bio
                            </a>
                            <a href="{{ route('candidate.profile.show') }}" class="btn bg-white text-dark rounded-pill border-0 shadow-sm px-4 fw-bold hover-bg-light">
                                <i class="ti ti-edit me-2"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                    
                    <!-- Profile Completion inline widget -->
                    <div class="col-md-4 mt-4 mt-md-0 border-start border-white border-opacity-25 ps-md-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase tracking-widest small fw-bold opacity-75">Profile Completion</div>
                            <div class="fs-2 fw-black {{ $completion === 100 ? 'text-green' : 'text-yellow' }}">{{ $completion }}%</div>
                        </div>
                        <div class="progress progress-sm mb-3 bg-white bg-opacity-20 rounded-pill">
                            <div class="progress-bar {{ $completion === 100 ? 'bg-green' : 'bg-yellow' }} rounded-pill" style="width: {{ $completion }}%" role="progressbar"></div>
                        </div>
                        
                        @if($completion === 100)
                            <div class="d-flex align-items-center text-green small fw-bold bg-green-lt bg-opacity-10 py-2 px-3 rounded-pill mt-2">
                                <i class="ti ti-discount-check-filled fs-3 me-2"></i> Verified & Ready to Apply
                            </div>
                        @else
                            <a href="{{ route('candidate.profile.show') }}" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold shadow-sm">
                               <i class="ti ti-alert-triangle-filled me-1"></i> Complete Profile Now
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Dynamic Next Step Indicator -->
            <div class="card-footer bg-dark bg-opacity-25 py-3 px-5 border-0">
                <div class="d-flex align-items-center">
                    <div class="spinner-grow spinner-grow-sm text-yellow me-3 opacity-75" role="status"></div>
                    <span class="text-white small">
                        <strong>NEXT ASSIGNMENT:</strong> 
                        @if($completion < 100)
                            Your profile is incomplete. Click "Complete Profile Now" to finish and become eligible for jobs.
                        @elseif($applications->isEmpty())
                            Profile complete! Start browsing open projects to find your next opportunity.
                        @elseif($applications->where('status', 'submitted')->isNotEmpty())
                            You have pending applications. Please download and pay your Challan(s).
                        @else
                            All set! We will notify you via SMS when your Roll Number Slip is ready.
                        @endif
                    </span>
                </div>
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
            ];
            foreach ($fields as $col => $label) {
                if (empty($candidate->$col)) $missingFields[] = $label;
            }
            if (!$candidate->education()->exists()) $missingFields[] = 'Add Minimum 1 Degree/Qualification';
        }
    @endphp

    @if(count($missingFields) > 0)
    <div class="col-12 mb-4">
        <div class="card border-0 bg-yellow-lt shadow-sm rounded-5 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row gap-4 align-items-md-center">
                    <div class="bg-yellow text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="ti ti-alert-triangle-filled fs-1"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="fs-2 fw-black text-dark mb-1">Action Required: Complete Your Profile</h3>
                        <p class="text-muted mb-3 fs-3">You cannot apply for any jobs until your profile is 100% complete. Please provide the missing information below:</p>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($missingFields as $mf)
                                <span class="badge bg-white text-dark border border-yellow border-opacity-25 px-3 py-2 rounded-pill shadow-sm fs-4">
                                    <i class="ti ti-x text-danger me-1 fw-bold"></i> {{ $mf }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="ms-md-auto mt-4 mt-md-0 flex-shrink-0">
                        <a href="{{ route('candidate.profile.show') }}" class="btn btn-yellow btn-lg rounded-pill shadow-sm fw-bold px-4">
                            Fix Missing Details <i class="ti ti-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Applications List - Timeline UI -->
    <div class="col-12">
        <h3 class="page-title mb-3"><i class="ti ti-file-text me-2 text-primary"></i> My Job Applications</h3>
        
        @if($applications->isEmpty())
            <div class="card border-0 shadow-lg rounded-5 bg-light pb-5 pt-3">
                <div class="card-body text-center py-5">
                    <div class="avatar avatar-xl bg-white shadow-sm mb-4 mx-auto rounded-circle border border-primary border-opacity-10 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="ti ti-inbox text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fs-1 fw-black text-dark mb-2">No Applications Found</h3>
                    <p class="text-muted mb-5 fs-3">You have not submitted any job applications yet.</p>
                    <a href="{{ route('projects') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                        <i class="ti ti-search me-2"></i> Browse Open Projects
                    </a>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($applications as $app)
                    @php
                        // Determine step status
                        $isPaid = $app->payment && $app->payment->status->value === 'paid';
                        $hasSlip = $app->examRollno && $app->examRollno->slip_ready;
                        $hasResult = $app->result && $app->result->isPublished();
                        
                        // Current tracking index: 1-4
                        $currentStep = 1;
                        if ($isPaid) $currentStep = 2;
                        if ($hasSlip) $currentStep = 3;
                        if ($hasResult) $currentStep = 4;
                    @endphp
                    
                    <div class="col-12">
                        <div class="card border-0 shadow-lg rounded-5 overflow-hidden transition-all hover-shadow-xl" style="border-left: 6px solid var(--pats-primary) !important;">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <!-- Left Section: Job Details -->
                                    <div class="col-md-4 p-4 p-md-5 d-flex flex-column justify-content-center border-end" style="background: linear-gradient(135deg, rgba(248, 249, 250, 0.9) 0%, rgba(233, 236, 239, 0.5) 100%);">
                                        <div class="mb-3">
                                            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold tracking-widest shadow-sm">
                                                APPLIED: {{ strtoupper($app->applied_at->format('d M Y')) }}
                                            </span>
                                        </div>
                                        <h3 class="fs-1 fw-black text-dark mb-1">{{ mb_strimwidth($app->job->title, 0, 45, '...') }}</h3>
                                        <div class="opacity-75 fw-bold mb-4 tracking-widest small text-uppercase">{{ mb_strimwidth($app->job->project->name, 0, 40, '...') }}</div>
                                        
                                        @if(!empty($app->eligibility_warnings))
                                            <div class="text-yellow-dark small fw-bold d-flex align-items-center bg-yellow-lt rounded-pill px-3 py-2 w-fit">
                                                <i class="ti ti-alert-triangle-filled me-2 fs-3"></i> Eligibility Warning
                                            </div>
                                        @else
                                            <div class="text-green small fw-bold d-flex align-items-center bg-green-lt rounded-pill px-3 py-2 w-fit shadow-sm">
                                                <i class="ti ti-discount-check-filled me-2 fs-3"></i> Profile Eligible
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Right Section: Timeline Steps -->
                                    <div class="col-md-8 p-4 p-md-5">
                                        <div class="steps steps-blue steps-counter steps-vertical steps-vertical-md mb-0">
                                            
                                            <!-- STEP 1: APPLIED -->
                                            <div class="step step-active">
                                                <div class="mb-1">
                                                    <strong class="fs-3 text-dark">Application Submitted</strong>
                                                </div>
                                                <div class="text-muted small">You successfully applied for <strong class="text-dark">{{ $app->desiredTestCity ? $app->desiredTestCity->name : 'N/A' }}</strong> as your preferred test city.</div>
                                            </div>
                                            
                                            <!-- STEP 2: PAYMENT VERIFIED -->
                                            <div class="step {{ $currentStep >= 2 ? 'step-active' : '' }}">
                                                <div class="mb-1">
                                                    <strong class="fs-3 {{ $currentStep >= 2 ? 'text-dark' : 'text-muted' }}">Payment Verified</strong>
                                                </div>
                                                @if($app->job->fee == 0)
                                                    <div class="text-green small fw-bold"><i class="ti ti-check me-1 fs-4"></i> Fee Not Required</div>
                                                @elseif($isPaid)
                                                    <div class="text-green small fw-bold"><i class="ti ti-check me-1 fs-4"></i> Fee Paid Confirmed</div>
                                                @else
                                                    @if($app->payment && $app->payment->status->value === 'unpaid' && $app->job->project->isRegistrationOpen())
                                                        <div class="text-muted small mb-3">Please download your challan and pay the processing fee at any participating branch.</div>
                                                        <a href="{{ URL::patsDownload($app, 'challan') }}" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" target="_blank">
                                                            <i class="ti ti-receipt me-1"></i> Download Challan
                                                        </a>
                                                    @else
                                                        <div class="text-yellow-dark small fw-bold"><i class="ti ti-clock me-1"></i> Payment pending or closed</div>
                                                    @endif
                                                @endif
                                            </div>
                                            
                                            <!-- STEP 3: SLIP ISSUED -->
                                            <div class="step {{ $currentStep >= 3 ? 'step-active' : '' }}">
                                                <div class="mb-1">
                                                    <strong class="fs-3 {{ $currentStep >= 3 ? 'text-dark' : 'text-muted' }}">Admit Card Allocated</strong>
                                                </div>
                                                @if($hasSlip)
                                                    <div class="text-teal small mb-3 fw-bold"><i class="ti ti-calendar-event me-1"></i> Test date: {{ $app->examRollno->batch->test_date->format('d M Y') }}</div>
                                                    <a href="{{ URL::patsDownload($app, 'slip') }}" class="btn btn-sm btn-teal rounded-pill px-3 fw-bold shadow-sm" target="_blank">
                                                        <i class="ti ti-download me-1"></i> Download Slip
                                                    </a>
                                                @else
                                                    <div class="text-muted small">Not scheduled yet. Download links will appear here.</div>
                                                @endif
                                            </div>
                                            
                                            <!-- STEP 4: RESULT PUBLISHED -->
                                            <div class="step {{ $currentStep >= 4 ? 'step-active' : '' }}">
                                                <div class="mb-1">
                                                    <strong class="fs-3 {{ $currentStep >= 4 ? 'text-dark' : 'text-muted' }}">Result Declared</strong>
                                                </div>
                                                @if($hasResult)
                                                    <div class="text-green mb-3 small fw-bold"><i class="ti ti-award me-1"></i> Final Score: {{ $app->result->score }} / {{ $app->result->total_marks }}</div>
                                                    <a href="{{ route('candidate.result', $app) }}" class="btn btn-sm btn-green rounded-pill px-3 fw-bold shadow-sm">
                                                        <i class="ti ti-medal me-1"></i> View Result Report
                                                    </a>
                                                @else
                                                    <div class="text-muted small">Awaiting final result processing. Check back later.</div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
        @endif
    </div>
</div>
@endsection

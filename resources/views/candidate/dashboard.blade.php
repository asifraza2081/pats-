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
                        <h2 class="display-6 fw-black mb-1">Welcome back, {{ $user->first_name }}! ðŸ‘‹</h2>
                        <div class="opacity-75 fs-3 mb-3">CNIC: <strong class="text-white">{{ $user->cnic }}</strong> <span class="mx-2">â€¢</span> Active Applications: <strong class="text-white">{{ $applications->count() }}</strong></div>
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
                    <div class="col-md-5 mt-4 mt-md-0 border-start border-white border-opacity-25 ps-md-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase tracking-widest small fw-bold opacity-75">Wizard Progress</div>
                            <div class="fs-2 fw-black {{ $completion === 100 ? 'text-green' : 'text-yellow' }}">{{ $completion }}%</div>
                        </div>
                        <div class="progress progress-sm mb-3 bg-white bg-opacity-20 rounded-pill">
                            <div class="progress-bar {{ $completion === 100 ? 'bg-green' : 'bg-yellow' }} rounded-pill" style="width: {{ $completion }}%" role="progressbar"></div>
                        </div>
                        
                        <div class="row g-2">
                            @foreach(['1', '2', '3', '4'] as $s)
                                @php
                                    $isStepComplete = $status['step'.$s]['success'] && ($s < $status['next_step'] || $completion === 100);
                                    $isStepActive = $status['next_step'] == $s && $completion < 100;
                                @endphp
                                <div class="col-3">
                                    <div class="p-2 rounded-2 text-center {{ $isStepComplete ? 'bg-green text-white' : ($isStepActive ? 'bg-yellow text-dark' : 'bg-white bg-opacity-10 text-white opacity-50') }}" style="font-size: 10px; font-weight: 800;">
                                        STEP {{ $s }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dynamic Next Step Indicator -->
            <div class="card-footer bg-dark bg-opacity-25 py-3 px-5 border-0">
                <div class="d-flex align-items-center">
                    @if($completion < 100)
                        <div class="spinner-grow spinner-grow-sm text-yellow me-3 opacity-75" role="status"></div>
                        <span class="text-white small">
                            <strong>ACTION REQUIRED:</strong> 
                            You are currently on <strong>Step {{ $status['next_step'] }}: {{ $status['step'.$status['next_step']]['label'] }}</strong>. 
                            Complete this stage to unlock the next part of your profile.
                        </span>
                    @else
                        <div class="ti ti-discount-check-filled text-green me-3 fs-2"></div>
                        <span class="text-white small">
                            <strong>PROFILE VERIFIED:</strong> 
                            Congratulations! Your profile is 100% complete. You are now eligible to apply for all active jobs.
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Wizard Requirements Checklist -->
    @if($completion < 100)
    <div class="col-12 mb-5">
        <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="card-header bg-yellow-lt border-0 py-3 px-4">
                <h3 class="card-title fw-black text-yellow-emphasis m-0"><i class="ti ti-list-check me-2"></i> Wizard Requirements Checklist</h3>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    @foreach(['1', '2', '3', '4'] as $s)
                        @php $sdata = $status['step'.$s]; @endphp
                        <div class="col-md-6 col-lg-3">
                            <div class="p-4 rounded-4 border-2 {{ $sdata['success'] ? 'border-success bg-success-lt' : ($status['next_step'] == $s ? 'border-yellow bg-yellow-lt' : 'border-light bg-light opacity-50') }} h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-sm rounded-circle me-3 {{ $sdata['success'] ? 'bg-success text-white' : ($status['next_step'] == $s ? 'bg-yellow text-dark' : 'bg-secondary text-white') }}">
                                        @if($sdata['success']) <i class="ti ti-check"></i> @else {{ $s }} @endif
                                    </span>
                                    <h4 class="m-0 fw-black {{ $sdata['success'] ? 'text-success' : ($status['next_step'] == $s ? 'text-yellow-emphasis' : 'text-muted') }}">{{ $sdata['label'] }}</h4>
                                </div>
                                <div class="small">
                                    @if($sdata['success'] && $s <= $status['next_step'])
                                        <span class="text-success fw-bold">Verified Complete</span>
                                    @elseif($status['next_step'] == $s)
                                        <ul class="list-unstyled mb-0 text-yellow-emphasis fw-bold">
                                            @foreach($sdata['errors'] as $err)
                                                <li>â€¢ {{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Sequence Locked</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-5">
                    <a href="{{ route('candidate.profile.show', ['step' => $status['next_step']]) }}" class="btn btn-yellow btn-lg rounded-pill shadow-lg fw-black px-5 py-3 fs-3">
                        CONTINUE WIZARD AT STEP {{ $status['next_step'] }} <i class="ti ti-arrow-right-bar ms-2"></i>
                    </a>
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



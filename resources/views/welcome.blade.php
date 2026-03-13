@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero mb-5 rounded-4 overflow-hidden">
    <div class="container-xl text-center py-6">
        <h1 class="display-3 fw-bold mb-3 tracking-tight">Precision Assessment & Testing Service</h1>
        <p class="fs-2 mb-4 opacity-90 mx-auto" style="max-width: 800px;">Pakistan's premier platform for merit-based recruitment, professional certifications, and educational assessments.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('projects') }}" class="btn btn-teal btn-lg text-white px-5 shadow" style="background: var(--pats-teal)">Apply for Jobs</a>
            <a href="{{ route('results.search') }}" class="btn btn-outline-light btn-lg px-5">Check Results</a>
        </div>
    </div>
</section>

<!-- Notice Board (Ticker) -->
<div class="notice-board mb-5 rounded-3">
    <div class="container-xl py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="badge bg-danger shadow-sm px-3 py-2">NEW ANNOUNCEMENTS</span>
            </div>
            <div class="col marquee-container">
                <div class="marquee-content text-primary">
                    <i class="ti ti-star-filled me-2 text-warning"></i> Registration for Ministry of IT Projects is now OPEN. 
                    <span class="mx-4">|</span>
                    <i class="ti ti-star-filled me-2 text-warning"></i> Results for Punjab Police Screening Test have been declared.
                    <span class="mx-4">|</span>
                    <i class="ti ti-star-filled me-2 text-warning"></i> Download Roll No Slips for Healthcare Recruitment Phase 2.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Projects Section -->
    <div class="col-lg-8">
        <h2 class="section-header">LATEST PROJECTS</h2>
        
        @if(isset($projects) && $projects->count() > 0)
            <div class="row row-cards g-4">
                @foreach($projects as $project)
                <div class="col-md-6">
                    <div class="card card-nts h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary-lt p-2 rounded me-3">
                                    <i class="ti ti-briefcase fs-1"></i>
                                </div>
                                <div>
                                    <h3 class="h3 fw-bold mb-0">{{ $project->org_name }}</h3>
                                    <div class="text-secondary small">Posted on {{ $project->created_at->format('d M, Y') }}</div>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-2">{{ $project->name }}</h4>
                            <p class="text-muted small mb-4">{{ Str::limit($project->description, 120) }}</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary w-100 fw-bold">DETAILS / APPLY NOW</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('projects') }}" class="btn btn-link text-teal fw-bold">VIEW ALL ACTIVE PROJECTS <i class="ti ti-arrow-narrow-right ms-1"></i></a>
            </div>
        @else
            <div class="card bg-light border-0 py-5 text-center">
                <i class="ti ti-notebook-off text-muted opacity-50 display-1 mb-3"></i>
                <h3 class="text-muted">No open projects at this moment.</h3>
            </div>
        @endif
    </div>

    <!-- Sidebar / News & Results -->
    <div class="col-lg-4 mt-5 mt-lg-0">
        <h2 class="section-header">RECENT RESULTS</h2>
        <div class="card border-0 shadow-sm mb-4">
            <div class="list-group list-group-flush list-group-hoverable">
                @foreach($results as $result)
                <a href="{{ route('results.search') }}" class="list-group-item list-group-item-action py-3">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="badge bg-success">LIVE</span></div>
                        <div class="col">
                            <div class="fw-bold text-dark">{{ $result->name }}</div>
                            <div class="text-muted small">{{ $result->org_name }}</div>
                        </div>
                        <div class="col-auto"><i class="ti ti-chevron-right text-muted"></i></div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="card-footer bg-light text-center">
                <a href="{{ route('results.search') }}" class="fw-bold text-decoration-none small">VIEW RESULT ARCHIVE</a>
            </div>
        </div>

        <h2 class="section-header">QUICK LINKS</h2>
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <a href="{{ route('downloads') }}" class="list-group-item py-3"><i class="ti ti-download me-2 text-primary"></i> Sample Papers & Syllabi</a>
                <a href="#" class="list-group-item py-3"><i class="ti ti-info-circle me-2 text-primary"></i> Instructions for Candidates</a>
                <a href="{{ route('contact') }}" class="list-group-item py-3"><i class="ti ti-headset me-2 text-primary"></i> Helpdesk & Queries</a>
                <a href="#" class="list-group-item py-3"><i class="ti ti-file-text me-2 text-primary"></i> Tenders & Procurement</a>
            </div>
        </div>
    </div>
</div>

<!-- Our Process Section (Infographic) -->
<section class="bg-white py-6 border-top mt-6">
    <div class="container-xl">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="section-header">OUR TRANSPARENT PROCESS</h2>
                <h3 class="display-6 fw-bold mb-4">A Bird's Eye View of Merit</h3>
                <p class="text-secondary fs-3 mb-4">
                    PATS follows a globally recognized assessment methodology designed to ensure that merit is never compromised. Our end-to-end digital tracking allows candidates to monitor their status in real-time.
                </p>
                <div class="row g-4 pt-2">
                    <div class="col-6">
                        <div class="fw-bold h4 mb-1 text-teal"><i class="ti ti-check me-2"></i> Secure PBT/CBT</div>
                        <p class="small text-muted">Multiple test formats including Paper Based and Computer Based testing.</p>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold h4 mb-1 text-teal"><i class="ti ti-check me-2"></i> OMR Scanning</div>
                        <p class="small text-muted">High-speed optical mark recognition for error-free marking.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                    <img src="{{ asset('assets/pats_process.png') }}" alt="PATS Process Flow" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works (Professional Style) -->
<section class="bg-white py-6 mt-5 border-top">
    <div class="container-xl">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">EFFICIENT ASSESSMENT FLOW</h2>
            <p class="text-muted">Our streamlined 4-step process ensures a smooth journey from application to result.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="mb-3"><span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-user-plus fs-1"></i></span></div>
                <h3 class="fw-bold">1. PROFILE</h3>
                <p class="text-muted small">Register and build your permanent profile once for all future projects.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3"><span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-send fs-1"></i></span></div>
                <h3 class="fw-bold">2. APPLY</h3>
                <p class="text-muted small">Automatic eligibility checks based on your profile for rapid submission.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3"><span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-id fs-1"></i></span></div>
                <h3 class="fw-bold">3. ADMIT</h3>
                <p class="text-muted small">Download NTS-style roll number slips with exact venue and shift details.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3"><span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-trophy fs-1"></i></span></div>
                <h3 class="fw-bold">4. RESULT</h3>
                <p class="text-muted small">Transparent result declaration with percentile ranking and scanned sheets.</p>
            </div>
        </div>
    </div>
</section>
@endsection

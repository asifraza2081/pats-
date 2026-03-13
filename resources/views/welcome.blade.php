@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero mb-5 rounded-4 overflow-hidden shadow-lg">
    <div class="container-xl text-center py-6 position-relative z-index-2">
        <div class="badge bg-teal-lt text-white px-3 py-2 mb-4 animate__animated animate__fadeInDown">
            <i class="ti ti-shield-check me-2"></i> ISO 9001:2015 CERTIFIED TESTING AGENCY
        </div>
        <h1 class="display-2 fw-extrabold mb-3 tracking-tight text-white animate__animated animate__fadeInUp">
            Precision Assessment & <br><span class="text-teal-light">Testing Service</span>
        </h1>
        <p class="fs-2 mb-5 text-white-50 mx-auto animate__animated animate__fadeInUp animate__delay-1s" style="max-width: 850px;">
            Pakistan's premier platform for merit-based recruitment, professional certifications, and educational assessments. Empowering organizations with transparent, data-driven selection.
        </p>
        <div class="d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-2s">
            <a href="{{ route('projects') }}" class="btn btn-teal btn-lg text-white px-5 shadow-lg hover-lift border-0 fs-3 fw-bold" style="background: var(--pats-teal)">
                Explore Job Projects <i class="ti ti-arrow-right ms-2"></i>
            </a>
            <a href="{{ route('results.search') }}" class="btn btn-outline-light btn-lg px-5 hover-lift fs-3 fw-bold">
                Check Results
            </a>
        </div>
    </div>
</section>

<!-- Notice Board (Dynamic Ticker) -->
<div class="notice-board mb-5 p-1 glass-panel">
    <div class="container-xl py-2">
        <div class="row align-items-center g-0">
            <div class="col-auto me-3">
                <span class="badge bg-danger shadow-sm px-4 py-2 fw-bold pulse-red">LATEST UPDATES</span>
            </div>
            <div class="col marquee-container">
                <div class="marquee-content text-dark fs-3 font-weight-medium">
                    @forelse($announcements as $ann)
                        <i class="ti ti-{{ $ann->type == 'new' ? 'star-filled text-warning' : 'circle-check-filled text-success' }} me-2"></i> {{ $ann->text }} 
                        <span class="mx-5 text-muted opacity-30">|</span>
                    @empty
                        <i class="ti ti-info-circle me-2 text-primary"></i> Registration processes are active. Please visit the Projects section for details.
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Stats Section -->
<section class="mb-6">
    <div class="row g-4 text-center">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-white h-100 hover-lift shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-value text-primary mb-1">{{ number_format($stats['active_projects']) }}</div>
                    <div class="text-uppercase tracking-wider fw-bold text-muted small">Active Projects</div>
                </div>
                <div class="bg-primary pt-1"></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-white h-100 hover-lift shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-value text-teal mb-1">{{ number_format($stats['job_posts']) }}</div>
                    <div class="text-uppercase tracking-wider fw-bold text-muted small">Open Job Posts</div>
                </div>
                <div class="bg-teal pt-1"></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-white h-100 hover-lift shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-value text-indigo mb-1">{{ number_format($stats['applications'] / 1000, 1) }}K+</div>
                    <div class="text-uppercase tracking-wider fw-bold text-muted small">Candidates Served</div>
                </div>
                <div class="bg-indigo pt-1"></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-white h-100 hover-lift shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-value text-success mb-1">{{ number_format($stats['results']) }}</div>
                    <div class="text-uppercase tracking-wider fw-bold text-muted small">Results Declared</div>
                </div>
                <div class="bg-success pt-1"></div>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <!-- Projects Section -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="section-header m-0">FEATURED RECRUITMENTS</h2>
            <a href="{{ route('projects') }}" class="btn btn-link text-teal p-0 fw-bold">View All <i class="ti ti-chevron-right"></i></a>
        </div>
        
        @if(isset($projects) && $projects->count() > 0)
            <div class="row row-cards g-4">
                @foreach($projects as $project)
                <div class="col-md-6">
                    <div class="card card-nts h-100 border-0 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-teal-lt p-3 rounded-circle me-3">
                                    <i class="ti ti-building-estate fs-1 text-teal"></i>
                                </div>
                                <div>
                                    <h3 class="h3 fw-extrabold mb-0 text-dark">{{ $project->org_name }}</h3>
                                    <div class="text-secondary small fw-medium">Posted {{ $project->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3 fs-3 text-dark">{{ $project->name }}</h4>
                            <p class="text-muted small mb-4 line-clamp-3">{{ $project->description }}</p>
                            
                            <div class="d-flex gap-2">
                                <span class="badge bg-green-lt text-green border-0"><i class="ti ti-clock me-1"></i> Open Now</span>
                                @if($project->close_date)
                                <span class="badge bg-red-lt text-red border-0"><i class="ti ti-calendar-event me-1"></i> Closes {{ $project->close_date->format('M d') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4">
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-teal w-100 fw-bold text-white shadow-sm border-0">APPLY FOR THIS PROJECT</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card glass-panel border-0 py-6 text-center shadow-none">
                <i class="ti ti-notebook-off text-muted opacity-30 display-1 mb-3"></i>
                <h3 class="text-muted fw-bold">No recruitment projects are currently accepting applications.</h3>
                <p class="text-muted small">Check back soon or view our result archive.</p>
            </div>
        @endif
    </div>

    <!-- Sidebar / News & Results -->
    <div class="col-lg-4 mt-5 mt-lg-0">
        <h2 class="section-header">LATEST RESULTS</h2>
        <div class="card border-0 shadow-lg overflow-hidden rounded-4 mb-5">
            <div class="list-group list-group-flush list-group-hoverable">
                @forelse($results as $result)
                <a href="{{ route('results.search') }}" class="list-group-item list-group-item-action py-4 px-4">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="badge bg-green shadow-sm pulse-green badge-dot"></span></div>
                        <div class="col">
                            <div class="fw-extrabold text-dark fs-4 mb-1">{{ $result->name }}</div>
                            <div class="text-muted small fw-medium">{{ $result->org_name }}</div>
                        </div>
                        <div class="col-auto"><i class="ti ti-arrow-up-right text-teal fs-2"></i></div>
                    </div>
                </a>
                @empty
                    <div class="p-4 text-center text-muted">No recent results found.</div>
                @endforelse
            </div>
            <div class="card-footer bg-teal text-white text-center py-3">
                <a href="{{ route('results.search') }}" class="fw-bold text-decoration-none text-white small d-block">EXPLORE RESULT ARCHIVE <i class="ti ti-arrow-right ms-1"></i></a>
            </div>
        </div>

        <h2 class="section-header">RESOURCES</h2>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="list-group list-group-flush">
                <a href="{{ route('downloads') }}" class="list-group-item list-group-item-action py-4 hover-lift">
                    <div class="d-flex w-100 align-items-center">
                        <i class="ti ti-file-download me-4 text-primary fs-1"></i>
                        <div>
                            <div class="fw-bold fs-4 text-dark">Sample Papers</div>
                            <div class="small text-muted">Test patterns and syllebi</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('instructions') }}" class="list-group-item list-group-item-action py-4 hover-lift">
                    <div class="d-flex w-100 align-items-center">
                        <i class="ti ti-help-circle me-4 text-teal fs-1"></i>
                        <div>
                            <div class="fw-bold fs-4 text-dark">Candidate Guide</div>
                            <div class="small text-muted">How to apply & FAQs</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('contact') }}" class="list-group-item list-group-item-action py-4 hover-lift">
                    <div class="d-flex w-100 align-items-center">
                        <i class="ti ti-headset me-4 text-indigo fs-1"></i>
                        <div>
                            <div class="fw-bold fs-4 text-dark">Support Helpdesk</div>
                            <div class="small text-muted">Technical queries & complaints</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Our Process Section (Modernized) -->
<section class="bg-white py-6 border-top mt-6 glass-panel rounded-4 mx-3 shadow-none animate__animated animate__fadeIn">
    <div class="container-xl">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="badge bg-teal-lt text-teal px-3 py-1 mb-3">OUR METHODOLOGY</div>
                <h2 class="display-5 fw-extrabold mb-4 text-dark">Transparent Assessment Process</h2>
                <p class="text-secondary fs-3 mb-4 lh-base">
                    PATS follows a globally recognized assessment methodology designed to ensure that merit is never compromised. Our end-to-end digital tracking allows candidates to monitor their status in real-time.
                </p>
                <div class="row g-4 pt-2">
                    <div class="col-6">
                        <div class="card bg-light border-0 p-3 hover-lift h-100">
                            <div class="fw-bold h4 mb-1 text-teal"><i class="ti ti-check-box me-2"></i> Secure PBT/CBT</div>
                            <p class="small text-muted mb-0">Multiple test formats including Paper Based and Computer Based testing.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light border-0 p-3 hover-lift h-100">
                            <div class="fw-bold h4 mb-1 text-teal"><i class="ti ti-scan me-2"></i> OMR Scanning</div>
                            <p class="small text-muted mb-0">High-speed optical mark recognition for error-free marking.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg overflow-hidden rounded-4 hover-rotate-sm transition-all duration-500">
                    <img src="{{ asset('assets/pats_process.png') }}" alt="PATS Process Flow" class="img-fluid">
                    <div class="card-img-overlay d-flex align-items-end p-0">
                        <div class="w-100 p-3 glass-panel-dark text-white rounded-0">
                            <i class="ti ti-info-circle me-2"></i> End-to-end Merit Tracking
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Assessment Flow (Modernized) -->
<section class="py-6 mt-5 bg-teal-lt mx-3 rounded-4 animate__animated animate__fadeIn">
    <div class="container-xl">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-extrabold text-dark">Efficient Assessment Flow</h2>
            <p class="text-muted fs-3">Our streamlined 4-step process ensures a smooth journey from application to result.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="mb-4"><span class="avatar avatar-xl bg-white text-teal rounded-circle shadow-sm hover-lift"><i class="ti ti-user-plus fs-1"></i></span></div>
                <h3 class="fw-extrabold text-dark">1. PROFILE</h3>
                <p class="text-secondary small px-3">Register and build your permanent profile once for all future projects.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-4"><span class="avatar avatar-xl bg-white text-teal rounded-circle shadow-sm hover-lift"><i class="ti ti-send fs-1"></i></span></div>
                <h3 class="fw-extrabold text-dark">2. APPLY</h3>
                <p class="text-secondary small px-3">Automatic eligibility checks based on your profile for rapid submission.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-4"><span class="avatar avatar-xl bg-white text-teal rounded-circle shadow-sm hover-lift"><i class="ti ti-id fs-1"></i></span></div>
                <h3 class="fw-extrabold text-dark">3. ADMIT</h3>
                <p class="text-secondary small px-3">Download roll number slips with exact venue and shift details.</p>
            </div>
            <div class="col-md-3">
                <div class="mb-4"><span class="avatar avatar-xl bg-white text-teal rounded-circle shadow-sm hover-lift"><i class="ti ti-trophy fs-1"></i></span></div>
                <h3 class="fw-extrabold text-dark">4. RESULT</h3>
                <p class="text-secondary small px-3">Transparent result declaration with percentile ranking and scanned sheets.</p>
            </div>
        </div>
    </div>
</section>

<style>
    .pulse-red {
        box-shadow: 0 0 0 rgba(220, 53, 69, 0.4);
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .pulse-green {
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
@endsection

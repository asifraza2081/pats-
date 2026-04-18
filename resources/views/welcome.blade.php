@extends('layouts.public')

@section('content')

<!-- Hero Section -->
<section class="hero bg-mesh-gradient mb-5 rounded-4 overflow-hidden shadow-2xl border-0 position-relative" style="padding: 100px 0;">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>

    <div class="container-xl position-relative z-index-2">
        <div class="row align-items-center g-5">
            <!-- Text Content -->
            <div class="col-lg-6 text-center text-lg-start animate__animated animate__fadeInLeft">
                <div class="d-inline-flex badge glass-panel text-teal-light px-4 py-2 mb-4 shadow-sm border-0 fs-5 fw-bold floating-element">
                    <i class="ti ti-shield-check me-2"></i> ISO 9001:2025 CERTIFIED AGENCY
                </div>
                <h1 class="display-1 fw-black mb-4 tracking-tighter text-white lh-1" style="letter-spacing: -2px; text-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    Prime Assessment & <br><span class="text-teal-light">Testing Services</span>
                </h1>
                <p class="fs-2 mb-5 text-white-50 opacity-90 mx-auto mx-lg-0" style="max-width: 600px; font-weight: 500;">
                    Pakistan's leading autonomous testing agency, committed to merit, transparency, and building a professional workforce through verifiable <span class="text-white border-bottom border-teal-light border-3 pb-1">digital assessment methodologies</span>.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-4 mt-4 animate__animated animate__fadeInUp animate__delay-1s">

                    <!-- Explore Projects -->
                    <a href="{{ route('projects') }}"
                        class="btn hero-btn btn-project btn-lg px-5 fs-3 fw-black py-3 rounded-pill">
                        Explore Projects
                        <i class="ti ti-arrow-right ms-2 fs-2"></i>
                    </a>

                    <!-- Check Results -->
                    <a href="{{ route('results.search') }}"
                        class="btn hero-btn btn-result btn-lg px-5 fs-3 fw-bold py-3 rounded-pill">
                        Check Results
                        <i class="ti ti-search ms-2 fs-2"></i>
                    </a>

                </div>
            </div>

            <!-- Imagery -->
            <!-- Cards Section (Replaces Imagery) -->
            <div class="col-lg-6 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="row g-4">

                    <!-- Open Projects -->
                    <div class="col-6">
                        <a href="{{ route('projects') }}" class="text-decoration-none d-block">
                            <div class="pats-card h-100">
                                <div class="card-content text-center">
                                    <div class="icon-wrap">
                                        <i class="ti ti-briefcase"></i>
                                    </div>
                                    <h5>Open Projects</h5>
                                    <p>Explore available testing projects</p>
                                </div>
                                <div class="shine"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Candidate Portal -->
                    <div class="col-6">
                        <a href="{{ route('login') }}" class="text-decoration-none d-block">
                            <div class="pats-card h-100">
                                <div class="card-content text-center">
                                    <div class="icon-wrap">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <h5>Candidate Portal</h5>
                                    <p>Login to your secure account</p>
                                </div>
                                <div class="shine"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Results -->
                    <div class="col-6">
                        <a href="{{ route('results.search') }}" class="text-decoration-none d-block">
                            <div class="pats-card h-100">
                                <div class="card-content text-center">
                                    <div class="icon-wrap">
                                        <i class="ti ti-file-search"></i>
                                    </div>
                                    <h5>Results</h5>
                                    <p>Check your test results</p>
                                </div>
                                <div class="shine"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Apply Now -->
                    <div class="col-6">
                        <a href="{{ route('projects') }}" class="text-decoration-none d-block">
                            <div class="pats-card h-100">
                                <div class="card-content text-center">
                                    <div class="icon-wrap">
                                        <i class="ti ti-pencil-plus"></i>
                                    </div>
                                    <h5>Apply Now</h5>
                                    <p>Submit your application</p>
                                </div>
                                <div class="shine"></div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
</section>

<!-- Notice Board (Dynamic Ticker) -->
<div class="notice-board mb-5 p-1 glass-panel border-0 shadow-sm rounded-pill overflow-hidden">
    <div class="container-xl py-2">
        <div class="row align-items-center g-0">
            <div class="col-auto me-3">
                <span class="badge bg-grad-accent shadow-sm px-4 py-2 fw-black rounded-pill animate__animated animate__pulse animate__infinite">LATEST UPDATES</span>
            </div>
            <div class="col marquee-container">
                <div class="marquee-content text-dark fs-3 fw-bold">
                    @forelse($announcements as $ann)
                    <span class="d-inline-flex align-items-center">
                        <i class="ti ti-{{ $ann->type == 'new' ? 'star-filled text-warning' : 'circle-check-filled text-teal' }} me-2 fs-2"></i>
                        {{ $ann->text }}
                    </span>
                    <span class="mx-5 text-muted opacity-30">|</span>
                    @empty
                    <span class="d-inline-flex align-items-center">
                        <i class="ti ti-info-circle me-2 text-teal fs-2"></i>
                        New registration cycles are opening soon. Stay tuned!
                    </span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Stats Section -->
<section class="mb-6">
    <div class="row g-4 text-center">
        <div class="col-6 col-md-3">
            <div class="card stat-card glass-panel border-0 hover-lift h-100">
                <div class="card-body py-5">
                    <div class="stat-value text-teal mb-2 fw-black">{{ number_format($stats['active_projects']) }}</div>
                    <div class="text-uppercase tracking-widest fw-black text-muted small opacity-80">ACTIVE PROJECTS</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card glass-panel border-0 hover-lift h-100">
                <div class="card-body py-5">
                    <div class="stat-value text-indigo mb-2 fw-black">{{ number_format($stats['job_posts']) }}</div>
                    <div class="text-uppercase tracking-widest fw-black text-muted small opacity-80">JOB LISTINGS</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card glass-panel border-0 hover-lift h-100">
                <div class="card-body py-5">
                    <div class="stat-value text-primary mb-2 fw-black">{{ $stats['applications'] >= 1000 ? number_format($stats['applications'] / 1000, 1) . 'K+' : $stats['applications'] }}</div>
                    <div class="text-uppercase tracking-widest fw-black text-muted small opacity-80">CANDIDATES</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card glass-panel border-0 hover-lift h-100">
                <div class="card-body py-5">
                    <div class="stat-value text-success mb-2 fw-black">{{ number_format($stats['results']) }}</div>
                    <div class="text-uppercase tracking-widest fw-black text-muted small opacity-80">RESULTS OUT</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row mt-5">
    <!-- Projects Section -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-5 pb-2 border-bottom border-teal-light border-opacity-20">
            <div>
                <h2 class="display-6 fw-black text-dark mb-1">FEATURED PROJECTS</h2>
                <p class="text-muted small fw-bold mb-0">Open recruitment cycles with active registration links.</p>
            </div>
            <a href="{{ route('projects') }}" class="btn btn-ghost-teal rounded-pill px-4 fw-black">View All <i class="ti ti-chevron-right ms-1"></i></a>
        </div>

        @if(isset($projects) && $projects->count() > 0)
        <div class="row row-cards g-4">
            @foreach($projects as $project)
            <div class="col-md-6">
                <div class="card card-pats h-100 border-0 overflow-hidden shadow-sm">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-grad-accent text-white p-3 rounded-4 me-3 shadow-teal-30">
                                <i class="ti ti-building-estate fs-1"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="h3 fw-black mb-0 text-dark text-truncate">{{ $project->org_name }}</h3>
                                <div class="text-teal small fw-bold">{{ $project->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3 fs-3 text-dark lh-sm">{{ $project->name }}</h4>
                        <p class="text-muted small mb-4 line-clamp-3 opacity-80">{{ $project->description }}</p>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-green-lt text-green border-0 rounded-pill px-3"><i class="ti ti-bolt me-1"></i> Open Now</span>
                            @if($project->close_date)
                            <span class="badge bg-red-lt text-red border-0 rounded-pill px-3"><i class="ti ti-calendar-event me-1"></i> Closes {{ $project->close_date->format('M d') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-3 px-4">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-teal w-100 fw-black text-white shadow-sm border-0 rounded-pill py-2">APPLY NOW <i class="ti ti-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="card glass-panel border-0 py-7 text-center rounded-5">
            <div class="avatar avatar-xl bg-light text-muted mb-4 mx-auto rounded-circle">
                <i class="ti ti-notebook-off fs-0"></i>
            </div>
            <h3 class="text-dark fw-black h2">No Open Recruitments</h3>
            <p class="text-muted fs-4 max-w-md mx-auto">We are currently preparing new testing cycles. Check back soon or browse our result archive for previous records.</p>
            <div class="mt-4">
                <a href="{{ route('results.search') }}" class="btn btn-teal rounded-pill px-5">Browse Results</a>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar / News & Results -->
    <div class="col-lg-4 mt-5 mt-lg-0">
        <h2 class="display-6 fw-black text-dark mb-4 ps-2 border-start border-teal border-5" style="font-size: 1.5rem;">LATEST RESULTS</h2>
        <div class="card border-0 shadow-lg overflow-hidden rounded-5 mb-5 glass-panel">
            <div class="list-group list-group-flush list-group-hoverable">
                @forelse($results as $result)
                <a href="{{ route('results.search') }}" class="list-group-item list-group-item-action py-4 px-4 border-teal border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-teal p-2 rounded-circle shadow-teal-30 pulse-green"></div>
                        </div>
                        <div class="col">
                            <div class="fw-black text-dark fs-4 mb-1">{{ $result->name }}</div>
                            <div class="text-muted small fw-bold uppercase opacity-60 tracking-wider">{{ $result->org_name }}</div>
                        </div>
                        <div class="col-auto"><i class="ti ti-chevron-right text-teal fs-3"></i></div>
                    </div>
                </a>
                @empty
                <div class="p-5 text-center text-muted">
                    <i class="ti ti-search fs-0 opacity-20 d-block mb-3"></i>
                    No recent results found.
                </div>
                @endforelse
            </div>
            <div class="card-footer bg-grad-accent text-white text-center py-3 border-0">
                <a href="{{ route('results.search') }}" class="fw-black text-decoration-none text-white small d-block">EXPLORE ALL RESULTS <i class="ti ti-arrow-right ms-2"></i></a>
            </div>
        </div>

        <h2 class="display-6 fw-black text-dark mb-4 ps-2 border-start border-indigo border-5" style="font-size: 1.5rem;">RESOURCES</h2>
        <div class="card border-0 shadow-sm rounded-5 overflow-hidden glass-panel">
            <div class="list-group list-group-flush">
                <a href="{{ route('downloads') }}" class="list-group-item list-group-item-action py-4 hover-lift border-0">
                    <div class="d-flex w-100 align-items-center">
                        <div class="bg-primary-lt p-3 rounded-4 me-4 shadow-sm">
                            <i class="ti ti-file-download text-primary fs-1"></i>
                        </div>
                        <div>
                            <div class="fw-black fs-4 text-dark">Sample Papers</div>
                            <div class="small text-muted fw-bold">Test patterns & syllabi</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('instructions') }}" class="list-group-item list-group-item-action py-4 hover-lift border-0">
                    <div class="d-flex w-100 align-items-center">
                        <div class="bg-teal-lt p-3 rounded-4 me-4 shadow-sm">
                            <i class="ti ti-help-circle text-teal fs-1"></i>
                        </div>
                        <div>
                            <div class="fw-black fs-4 text-dark">Candidate Guide</div>
                            <div class="small text-muted fw-bold">How to apply & FAQs</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('contact') }}" class="list-group-item list-group-item-action py-4 hover-lift border-0">
                    <div class="d-flex w-100 align-items-center">
                        <div class="bg-indigo-lt p-3 rounded-4 me-4 shadow-sm">
                            <i class="ti ti-headset text-indigo fs-1"></i>
                        </div>
                        <div>
                            <div class="fw-black fs-4 text-dark">Support Helpdesk</div>
                            <div class="small text-muted fw-bold">Technical queries</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Our Process Section (Modernized) -->
<section class="py-8 border-top border-teal border-opacity-10 mt-6 animate__animated animate__fadeIn">
    <div class="container-xl">
        <div class="row align-items-center g-6">
            <div class="col-lg-6">
                <div class="badge bg-teal-lt text-teal px-4 py-2 mb-4 rounded-pill fw-black">OUR METHODOLOGY</div>
                <h2 class="display-3 fw-black mb-4 text-dark lh-tight">Transparent <br><span class="text-grad-pats">Assessment Process</span></h2>
                <p class="text-secondary fs-2 mb-5 lh-lg opacity-80">
                    PATS utilizes state-of-the-art methodology ensuring merit is never compromised. We combine human expertise with digital verification for a zero-trust assessment environment.
                </p>
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5">
                            <div class="avatar avatar-md bg-teal text-white rounded-3 mb-3 shadow-teal-30"><i class="ti ti-check-box fs-2"></i></div>
                            <div class="fw-black h3 mb-2 text-dark">Secure Testing</div>
                            <p class="small text-muted mb-0 fw-medium">Encryption-standard Paper Based and CBT formats.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5">
                            <div class="avatar avatar-md bg-indigo text-white rounded-3 mb-3 shadow-sm"><i class="ti ti-scan fs-2"></i></div>
                            <div class="fw-black h3 mb-2 text-dark">OMR Scanning</div>
                            <p class="small text-muted mb-0 fw-medium">Rapid optical recognition for bias-free grading.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="card border-0 shadow-2xl overflow-hidden rounded-5 hover-rotate-sm transition-all duration-700">
                        <img src="{{ asset('assets/pats_process.png') }}" alt="PATS Assessment Process Flow" class="img-fluid" loading="lazy" decoding="async">
                        <div class="card-img-overlay d-flex align-items-end p-0">
                            <div class="w-100 p-4 glass-panel-dark text-white rounded-0">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-shield-check-filled text-teal me-3 fs-1"></i>
                                    <div>
                                        <div class="fw-black fs-4">End-to-End Integrity</div>
                                        <div class="small opacity-80">Digitally signed results & verifyable merit.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Assessment Flow (Modernized) -->
<section class="py-6 mt-5 bg-teal-lt mx-3 rounded-5 position-relative overflow-hidden animate__animated animate__fadeIn">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, var(--pats-teal) 1px, transparent 0); background-size: 24px 24px;"></div>
    <div class="container-xl position-relative z-index-2">
        <div class="text-center mb-6">
            <div class="badge bg-teal text-white px-4 py-2 mb-3 rounded-pill fw-bold">THE PATS JOURNEY</div>
            <h2 class="display-4 fw-black text-dark mb-3">Efficient Assessment Flow</h2>
            <p class="text-muted fs-2 max-w-2xl mx-auto">Our streamlined process ensures transparency and merit from application to result declaration.</p>
        </div>
        <div class="row g-5 text-center">
            <div class="col-md-3">
                <div class="process-step mb-4">
                    <div class="avatar avatar-xl bg-white text-teal rounded-circle shadow-lg hover-rotate mb-4 border border-teal border-opacity-10" style="width: 100px; height: 100px;">
                        <i class="ti ti-user-plus fs-0 fw-bold"></i>
                    </div>
                    <div class="step-line d-none d-md-block"></div>
                </div>
                <h3 class="fw-black text-dark h2 mb-2">1. Profile</h3>
                <p class="text-secondary fs-4 px-3 opacity-80">Register once and build your lifelong professional profile.</p>
            </div>
            <div class="col-md-3">
                <div class="process-step mb-4">
                    <div class="avatar avatar-xl bg-white text-teal rounded-circle shadow-lg hover-rotate mb-4 border border-teal border-opacity-10" style="width: 100px; height: 100px;">
                        <i class="ti ti-click fs-0 fw-bold"></i>
                    </div>
                    <div class="step-line d-none d-md-block"></div>
                </div>
                <h3 class="fw-black text-dark h2 mb-2">2. Apply</h3>
                <p class="text-secondary fs-4 px-3 opacity-80">One-click application with automatic eligibility checks.</p>
            </div>
            <div class="col-md-3">
                <div class="process-step mb-4">
                    <div class="avatar avatar-xl bg-white text-teal rounded-circle shadow-lg hover-rotate mb-4 border border-teal border-opacity-10" style="width: 100px; height: 100px;">
                        <i class="ti ti-id fs-0 fw-bold"></i>
                    </div>
                    <div class="step-line d-none d-md-block"></div>
                </div>
                <h3 class="fw-black text-dark h2 mb-2">3. Admit</h3>
                <p class="text-secondary fs-4 px-3 opacity-80">Secure admit cards with real-time venue verification.</p>
            </div>
            <div class="col-md-3">
                <div class="process-step mb-4">
                    <div class="avatar avatar-xl bg-teal text-white rounded-circle shadow-lg hover-rotate mb-4 shadow-teal-30" style="width: 100px; height: 100px;">
                        <i class="ti ti-trophy fs-0 fw-bold"></i>
                    </div>
                </div>
                <h3 class="fw-black text-dark h2 mb-2">4. Result</h3>
                <p class="text-secondary fs-4 px-3 opacity-80">Digital results with scanned sheets and merit ranking.</p>
            </div>
        </div>
    </div>
</section>
<!-- WhatsApp Floating Button -->
<a href="https://wa.me/923001234567" target="_blank" class="whatsapp-float">
    <i class="ti ti-brand-whatsapp"></i>
</a>
<style>
    /* Base Card */
    /* Base Card */
    /* ===== HERO OVERLAY FIX (MAIN ISSUE) ===== */
    .col-6 a {
        display: block;
        position: relative;
        z-index: 3;
    }

    .hero::after,
    .hero::before {
        pointer-events: none !important;
        z-index: -1 !important;
        /* send behind everything */
    }

    /* Ensure content stays above */
    .hero {
        position: relative;
        z-index: 1;
    }

    /* Fix cards click + hover */
    .pats-card {
        position: relative;
        z-index: 2;
        cursor: pointer;
    }

    /* Prevent any layer blocking */
    .pats-card::before,
    .pats-card .shine {
        pointer-events: none;
    }

    .pats-card {
        position: relative;
        border-radius: 20px;
        padding: 30px;
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #fff;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.4s ease;
        transform-style: preserve-3d;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }

    /* Hover Effect */
    .pats-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
    }

    /* Gradient Glow */
    .pats-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(0, 255, 200, 0.3), transparent);
        opacity: 0;
        transition: 0.4s;
        pointer-events: none;
        /* FIX */
    }

    .pats-card:hover::before {
        opacity: 1;
    }

    /* Shine Effect */
    .shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transform: skewX(-25deg);
        pointer-events: none;
        /* FIX */
    }

    .pats-card:hover .shine {
        left: 130%;
        transition: 0.8s;
    }

    /* Icon */
    .icon-wrap {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        backdrop-filter: blur(10px);
        transition: 0.4s;
    }

    .pats-card:hover .icon-wrap {
        transform: translateY(-8px) scale(1.1);
    }

    /* Text */
    .card-content h5 {
        font-weight: 900;
        margin-bottom: 5px;
    }

    .card-content p {
        font-size: 13px;
        opacity: 0.75;
    }

    /* Colors */
    .col-6:nth-child(1) .pats-card {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }

    .col-6:nth-child(2) .pats-card {
        background: linear-gradient(135deg, #396afc, #2948ff);
    }

    .col-6:nth-child(3) .pats-card {
        background: linear-gradient(135deg, #f7971e, #ffd200);
    }

    .col-6:nth-child(4) .pats-card {
        background: linear-gradient(135deg, #ff512f, #dd2476);
    }

    /* ===== COMMON BUTTON STYLE ===== */
    /* ===== HERO BUTTON BASE ===== */
    .hero-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        transform: translateY(0);
        color: #fff !important;
        border: none;
    }

    /* Hover Lift */
    .hero-btn:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
    }

    /* Glow */
    .hero-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.25), transparent);
        opacity: 0;
        transition: 0.4s;
        pointer-events: none;
    }

    .hero-btn:hover::before {
        opacity: 1;
    }

    /* Shine */
    .hero-btn::after {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        transform: skewX(-25deg);
    }

    .hero-btn:hover::after {
        left: 130%;
        transition: 0.8s;
    }

    /* Icon animation */
    .hero-btn i {
        transition: transform 0.3s ease;
    }

    .hero-btn:hover i {
        transform: translateX(6px);
    }

    /* ===== MATCH CARD COLORS ===== */

    /* Open Projects (Green like card 1) */
    .btn-project {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }

    /* Results (Yellow like card 3) */
    .btn-result {
        background: linear-gradient(135deg, #f7971e, #ffd200);
        color: #000 !important;
        /* better contrast */
    }


    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(46, 204, 113, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(46, 204, 113, 0);
        }
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* WhatsApp Floating Button */
    .whatsapp-float {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    /* Hover Effect */
    .whatsapp-float:hover {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
    }

    /* Pulse Animation */
    .whatsapp-float::before {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(37, 211, 102, 0.5);
        animation: whatsapp-pulse 2s infinite;
        z-index: -1;
    }

    .whatsapp-float,
    .whatsapp-float:hover,
    .whatsapp-float:focus,
    .whatsapp-float:active {
        text-decoration: none !important;
        color: #fff;
    }

    .whatsapp-float i {
        line-height: 1;
    }

    @keyframes whatsapp-pulse {
        0% {
            transform: scale(1);
            opacity: 0.7;
        }

        70% {
            transform: scale(1.5);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
</style>
@endsection
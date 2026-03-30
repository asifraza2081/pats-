@extends('layouts.public')
@section('title', 'About Us — Prime Assessment & Testing Services')
@section('header-title', 'ABOUT PATS')

@section('content')
{{-- Stats Bar --}}
<div class="row g-3 mb-6 text-center animate__animated animate__fadeIn">
    @foreach([
        ['value' => '50+', 'label' => 'Test Centers Nationwide', 'icon' => 'ti-building', 'color' => 'text-teal'],
        ['value' => '30+', 'label' => 'Client Organizations', 'icon' => 'ti-briefcase', 'color' => 'text-indigo'],
        ['value' => '4', 'label' => 'Major Cities Covered', 'icon' => 'ti-map-pin', 'color' => 'text-primary'],
        ['value' => '2024', 'label' => 'Year Established', 'icon' => 'ti-calendar-event', 'color' => 'text-success'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card glass-panel border-0 hover-lift h-100 rounded-5 shadow-sm">
            <div class="card-body py-4">
                <i class="ti {{ $stat['icon'] }} fs-1 {{ $stat['color'] }} mb-2 d-block"></i>
                <div class="display-5 fw-black {{ $stat['color'] }} mb-1">{{ $stat['value'] }}</div>
                <div class="text-muted small fw-bold text-uppercase tracking-wider">{{ $stat['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-5">
    {{-- Main Content --}}
    <div class="col-lg-8">
        {{-- Mission --}}
        <div class="card glass-panel p-5 border-0 mb-5 rounded-5 animate__animated animate__fadeIn shadow-sm">
            <div class="badge bg-teal-lt text-teal px-4 py-2 mb-4 rounded-pill fw-black">OUR MISSION</div>
            <h2 class="display-5 fw-black text-dark mb-4 lh-tight">Building a <span class="text-teal">Transparent Meritocracy</span></h2>
            <p class="fs-2 text-dark opacity-80 fw-medium lh-lg mb-4">
                Prime Assessment &amp; Testing Services (PATS) was established with a singular vision: to revolutionize the testing and recruitment landscape in Pakistan through unwavering commitment to merit, transparency, and innovation.
            </p>
            <p class="text-secondary fs-4 lh-lg opacity-80 mb-5">
                PATS is an autonomous organization providing comprehensive testing and assessment services to public and private sector institutions. Our expertise spans recruitment screening, educational entrance exams, professional certification, and organizational capacity building.
            </p>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-teal text-white rounded-3 mb-3 shadow-teal-30"><i class="ti ti-eye fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Transparency</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">Open results, scanned answer sheets, and merit-driven ranking with verifiable digital records.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-indigo text-white rounded-3 mb-3 shadow-sm"><i class="ti ti-shield-check fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Integrity</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">Strict security protocols, biometric verification, and zero-tolerance for malpractice at every stage.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-primary text-white rounded-3 mb-3 shadow-sm"><i class="ti ti-cpu fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Innovation</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">End-to-end digital platform covering application, scheduling, OMR grading, and result publishing.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-success text-white rounded-3 mb-3 shadow-sm"><i class="ti ti-users fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Inclusivity</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">Test centers across all major cities ensuring equal access for every qualified candidate nationwide.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Milestones Timeline --}}
        <div class="card glass-panel border-0 p-5 mb-5 rounded-5 shadow-sm animate__animated animate__fadeIn">
            <div class="badge bg-indigo-lt text-indigo px-4 py-2 mb-4 rounded-pill fw-black">OUR JOURNEY</div>
            <h2 class="display-6 fw-black text-dark mb-5">Key Milestones</h2>

            <div class="timeline">
                @foreach([
                    ['year' => '2024 Q1', 'title' => 'Organisation Founded', 'desc' => 'PATS was incorporated as an autonomous testing agency with a mandate to serve public sector recruitment.', 'color' => 'bg-teal'],
                    ['year' => '2024 Q2', 'title' => 'First Recruitment Drive', 'desc' => 'Successfully conducted the first multi-city recruitment test with 50+ invigilated centers simultaneously.', 'color' => 'bg-indigo'],
                    ['year' => '2024 Q3', 'title' => 'Digital Platform Launch', 'desc' => 'Launched the PATS Online Portal enabling candidates to register, apply, and download slips fully online.', 'color' => 'bg-primary'],
                    ['year' => '2024 Q4', 'title' => 'OMR Integration', 'desc' => 'Integrated optical mark recognition for bias-free automated grading with digital result publication.', 'color' => 'bg-success'],
                ] as $i => $milestone)
                <div class="row g-0 mb-4 {{ $i < 3 ? 'pb-4 border-bottom border-teal border-opacity-10' : '' }}">
                    <div class="col-auto me-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="avatar avatar-sm {{ $milestone['color'] }} text-white rounded-circle shadow-sm fw-bold" style="font-size: 0.65rem; width: 2.5rem; height: 2.5rem;">{{ explode(' ', $milestone['year'])[1] }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-light text-muted border fw-bold me-2 rounded-pill">{{ $milestone['year'] }}</span>
                        </div>
                        <h4 class="fw-black text-dark mb-1">{{ $milestone['title'] }}</h4>
                        <p class="text-secondary small fw-bold opacity-70 mb-0">{{ $milestone['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Accreditation --}}
        <div class="card glass-panel border-0 p-5 rounded-5 shadow-sm animate__animated animate__fadeIn">
            <div class="badge bg-success-lt text-success px-4 py-2 mb-4 rounded-pill fw-black">STANDARDS & ACCREDITATION</div>
            <h2 class="display-6 fw-black text-dark mb-4">Quality Assurance</h2>
            <div class="row g-4">
                @foreach([
                    ['icon' => 'ti-certificate', 'title' => 'ISO 9001:2015', 'desc' => 'Quality Management System — Certified for standardized testing operations and service delivery.', 'color' => 'bg-success-lt text-success'],
                    ['icon' => 'ti-building-government', 'title' => 'Federal Mandate', 'desc' => 'Operating under Government of Pakistan directives for merit-based public sector recruitment.', 'color' => 'bg-primary-lt text-primary'],
                    ['icon' => 'ti-lock', 'title' => 'Data Security', 'desc' => 'End-to-end encrypted candidate data with role-based access controls and audit trails.', 'color' => 'bg-indigo-lt text-indigo'],
                ] as $acc)
                <div class="col-md-4">
                    <div class="card border-0 bg-light hover-lift rounded-4 p-4 h-100 text-center">
                        <div class="avatar avatar-lg {{ $acc['color'] }} rounded-circle mx-auto mb-3"><i class="ti {{ $acc['icon'] }} fs-2"></i></div>
                        <h5 class="fw-black text-dark mb-1">{{ $acc['title'] }}</h5>
                        <p class="small text-secondary mb-0 fw-medium">{{ $acc['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card glass-panel border-0 p-5 sticky-top rounded-5 shadow-lg animate__animated animate__fadeInRight" style="top: 100px">
            <h3 class="display-6 fw-black text-dark mb-5 border-bottom pb-3">LEADERSHIP</h3>

            @foreach([
                ['initials' => 'DG', 'role' => 'Director General', 'dept' => 'Strategy & Operations', 'color' => 'bg-grad-accent'],
                ['initials' => 'HA', 'role' => 'Head of Assessment', 'dept' => 'Academic & Psychometrics', 'color' => 'bg-dark'],
                ['initials' => 'CF', 'role' => 'Chief of Operations', 'dept' => 'Logistics & Centers', 'color' => 'bg-indigo'],
                ['initials' => 'IT', 'role' => 'IT Director', 'dept' => 'Digital Infrastructure', 'color' => 'bg-primary'],
            ] as $leader)
            <div class="d-flex align-items-center mb-4 pb-4 border-bottom border-teal border-opacity-10 hover-lift px-2">
                <div class="avatar avatar-lg {{ $leader['color'] }} text-white rounded-circle me-4 shadow-sm fw-bold">{{ $leader['initials'] }}</div>
                <div>
                    <div class="fw-black fs-4 text-dark">{{ $leader['role'] }}</div>
                    <div class="text-teal small fw-bold text-uppercase tracking-wider opacity-80">{{ $leader['dept'] }}</div>
                </div>
            </div>
            @endforeach

            <div class="text-center mt-4 pt-2 border-top border-dark border-opacity-5">
                <img src="{{ asset('logo.png') }}" alt="PATS" height="55" class="opacity-10 mb-3 grayscale">
                <p class="small text-muted fw-bold mb-3">Pioneering Merit &amp; Transparency<br>since 2024</p>
                <a href="{{ route('contact') }}" class="btn btn-teal btn-sm rounded-pill px-4 fw-black w-100">Get in Touch</a>
            </div>
        </div>
    </div>
</div>
@endsection

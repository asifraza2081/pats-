@extends('layouts.public')
@section('title', 'About Us')
@section('header-title', 'ABOUT US')

@section('content')
<div class="row g-5">
    <div class="col-lg-8">
        <div class="card glass-panel p-5 border-0 mb-5 rounded-5 animate__animated animate__fadeIn shadow-sm">
            <div class="badge bg-teal-lt text-teal px-4 py-2 mb-4 rounded-pill fw-black">OUR MISSION</div>
            <h2 class="display-5 fw-black text-dark mb-4 lh-tight">Building a <span class="text-teal">Transparent Meritocracy</span></h2>
            <p class="fs-2 text-dark opacity-80 fw-medium lh-lg mb-4">
                Prime Assessment & Testing Services (PATS) was established with a singular vision: to revolutionize the testing and recruitment landscape in Pakistan through unwavering commitment to merit, transparency, and innovation.
            </p>
            <p class="text-secondary fs-4 lh-lg opacity-80 mb-5">
                PATS is an autonomous organization providing comprehensive testing and assessment services to public and private sector institutions. Our expertise spans recruitment screening, educational entrance exams, professional certification, and organizational capacity building.
            </p>
            
            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-teal text-white rounded-3 mb-3 shadow-teal-30"><i class="ti ti-eye fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Transparency</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">Open results, scanned answer sheets, and merit-driven ranking systems.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card glass-panel border-0 p-4 hover-lift h-100 rounded-5 transition-all">
                        <div class="avatar avatar-md bg-indigo text-white rounded-3 mb-3 shadow-sm"><i class="ti ti-shield-check fs-2"></i></div>
                        <h4 class="fw-black h3 mb-2 text-dark">Integrity</h4>
                        <p class="small text-secondary fw-bold opacity-60 mb-0">Strict security protocols and zero-tolerance for malpractice.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-light rounded-5 border border-teal border-opacity-10 d-flex align-items-center">
                <i class="ti ti-certificate text-teal fs-0 me-4 opacity-50"></i>
                <div>
                    <h3 class="fw-black text-dark mb-1">State-of-the-Art Infrastructure</h3>
                    <p class="text-muted small mb-0 fw-bold">With a network of secure test centers across Pakistan, we ensure reliability, accessibility, and efficiency for candidates nationwide.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card glass-panel border-0 p-5 sticky-top rounded-5 shadow-lg animate__animated animate__fadeInRight" style="top: 100px">
            <h3 class="display-6 fw-black text-dark mb-5 border-bottom pb-2">LEADERSHIP</h3>
            
            <div class="d-flex align-items-center mb-5 hover-lift px-2">
                <div class="avatar avatar-xl bg-grad-accent text-white rounded-circle me-4 shadow-teal-30">JD</div>
                <div>
                    <div class="fw-black fs-3 text-dark">Executive Director</div>
                    <div class="text-teal small fw-bold uppercase tracking-widest opacity-80">Strategy & Operations</div>
                </div>
            </div>
            
            <div class="d-flex align-items-center mb-5 hover-lift px-2">
                <div class="avatar avatar-xl bg-dark text-white rounded-circle me-4 shadow-sm">AS</div>
                <div>
                    <div class="fw-black fs-3 text-dark">Head of Assessment</div>
                    <div class="text-teal small fw-bold uppercase tracking-widest opacity-80">Academic & Psychometrics</div>
                </div>
            </div>
            
            <div class="text-center mt-5 pt-4 border-top border-dark border-opacity-5">
                <img src="{{ asset('logo.png') }}" alt="PATS" height="70" class="opacity-10 mb-3 grayscale">
                <p class="small text-muted fw-bold">Pioneering Excellence <br>since 2024</p>
            </div>
        </div>
    </div>
</div>
@endsection

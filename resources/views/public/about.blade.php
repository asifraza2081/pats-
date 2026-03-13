@extends('layouts.public')
@section('title', 'About Us')
@section('header-title', 'ABOUT US')

@section('content')
<div class="row g-5">
    <div class="col-lg-8">
        <div class="card glass-panel p-5 border-0 mb-5 rounded-4 animate__animated animate__fadeIn">
            <h2 class="section-header">OUR MISSION</h2>
            <p class="fs-2 text-dark fw-medium lh-base">
                Prime Assessment & Testing Service (PATS) was established with a singular vision: to revolutionize the testing and recruitment landscape in Pakistan through unwavering commitment to merit, transparency, and innovation.
            </p>
            
            <h2 class="section-header mt-5">WHO WE ARE</h2>
            <p class="text-secondary">
                PATS is an autonomous organization providing comprehensive testing and assessment services to public and private sector institutions. Our expertise spans recruitment screening, educational entrance exams, professional certification, and organizational capacity building.
            </p>
            <p class="text-secondary">
                With a state-of-the-art digital infrastructure and a network of secure test centers across Pakistan, we ensure that the assessment process is not just reliable, but also accessible and efficient for candidates nationwide.
            </p>

            <div class="row g-4 mt-4">
                <div class="col-md-6">
                    <div class="card bg-teal-lt border-0 p-4 hover-lift rounded-3">
                        <h4 class="fw-bold mb-2 text-teal">Transparency</h4>
                        <p class="small text-secondary mb-0">Open results, scanned answer sheets, and merit-driven ranking systems.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-teal-lt border-0 p-4 hover-lift rounded-3">
                        <h4 class="fw-bold mb-2 text-teal">Integrity</h4>
                        <p class="small text-secondary mb-0">Strict security protocols and zero-tolerance for malpractice.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card glass-panel border-0 p-4 sticky-top rounded-4 shadow-lg animate__animated animate__fadeInRight" style="top: 100px">
            <h3 class="fw-bold mb-4">Core Leadership</h3>
            <div class="d-flex align-items-center mb-4">
                <span class="avatar avatar-md rounded-circle me-3">JD</span>
                <div>
                    <div class="fw-bold">Executive Director</div>
                    <div class="text-secondary small">Strategy & Operations</div>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <span class="avatar avatar-md rounded-circle me-3">AS</span>
                <div>
                    <div class="fw-bold">Head of Assessment</div>
                    <div class="text-secondary small">Academic & Psychometrics</div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-center">
                <img src="{{ asset('logo.png') }}" alt="PATS" height="80" class="opacity-20 mb-3">
                <p class="small text-muted">Building a Merit-Based Future since 2024</p>
            </div>
        </div>
    </div>
</div>
@endsection

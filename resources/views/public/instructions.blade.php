@extends('layouts.public')
@section('title', 'How to Apply — Prime Assessment & Testing Services')
@section('header-title', 'Step-by-Step Guide')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card glass-panel border-0 mb-6 rounded-5 shadow-lg animate__animated animate__fadeIn">
            <div class="card-body p-5 p-md-7">
                <div class="badge bg-indigo-lt text-indigo px-4 py-2 mb-4 rounded-pill fw-black">APPLICATION PROTOCOL</div>
                <h2 class="display-5 fw-black text-dark mb-5 border-bottom pb-4">Standard Operating Procedures</h2>
                
                <div class="row g-6">
                    <!-- Step 1 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4">
                            <div class="avatar avatar-xl bg-grad-accent text-white rounded-5 shadow-teal-30"><i class="ti ti-user-plus fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">1. Profile Genesis</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Initialize your account using your CNIC and mobile number. Secure OTP verification ensures identity integrity. Once authenticated, complete your profile with precision.</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4">
                            <div class="avatar avatar-xl bg-dark text-white rounded-5 shadow-sm"><i class="ti ti-briefcase fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">2. Project Engagement</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Navigate to Open Projects to discover opportunities. Our intelligent system automatically matches your eligibility against job requirements in real-time.</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4">
                            <div class="avatar avatar-xl bg-indigo text-white rounded-5 shadow-sm"><i class="ti ti-receipt-2 fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">3. Merit Investment</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Generate your secure Bank Challan. Payments are digitally verified via 1Link and partner banks. Expect status updates within 24-48 business hours.</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4">
                            <div class="avatar avatar-xl bg-teal text-white rounded-5 shadow-teal-30"><i class="ti ti-ticket fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">4. Tactical Readiness</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Download your Roll Number Slip via the dashboard. This document, accompanied by your original CNIC, is mandatory for test center access.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-7 p-5 bg-grad-pats text-white rounded-5 position-relative overflow-hidden shadow-lg animate__animated animate__pulse animate__infinite animate__slower">
                    <div class="position-absolute top-0 end-0 p-5 mt-n4 me-n4 opacity-10">
                        <i class="ti ti-shield-alert" style="font-size: 8rem;"></i>
                    </div>
                    <div class="d-flex align-items-center position-relative z-index-2">
                        <i class="ti ti-info-circle-filled fs-0 me-4"></i>
                        <div>
                            <h3 class="fw-black text-white h2 mb-1">CRITICAL NOTICE: Test Day Integrity</h3>
                            <p class="fs-4 mb-0 opacity-80 fw-medium">
                                Report at least 60 minutes prior to the designated time. Strictly adhere to SOPs. Malpractice will lead to immediate disqualification and legal escalation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-6">
            <h3 class="display-6 fw-black text-dark mb-3">Encountering Friction?</h3>
            <p class="fs-3 text-secondary mb-5 opacity-80">Our technical dispatchers are standing by to resolve any application bottlenecks.</p>
            <a href="{{ route('contact') }}" class="btn btn-teal btn-lg px-6 rounded-pill fw-black shadow-teal-30 border-0 py-3">CONTACT COMMAND CENTER</a>
        </div>
    </div>
</div>
@endsection

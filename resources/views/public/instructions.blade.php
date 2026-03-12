@extends('layouts.public')
@section('title', 'How to Apply')
@section('header-title', 'INSTRUCTIONS FOR CANDIDATES')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-md-5">
                <h2 class="section-header">STEP-BY-STEP APPLICATION GUIDE</h2>
                <div class="row g-5 mt-2">
                    <!-- Step 1 -->
                    <div class="col-md-6 d-flex">
                        <div class="me-4">
                            <span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-user-plus fs-1"></i></span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">1. Create Your Profile</h3>
                            <p class="text-secondary small">Visit the registration page and enter your CNIC and mobile number. You will receive an OTP for verification. Once logged in, complete your basic profile, academic history, and work experience.</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-6 d-flex">
                        <div class="me-4">
                            <span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-briefcase fs-1"></i></span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">2. Browse & Select Project</h3>
                            <p class="text-secondary small">Head to "Open Projects" to see all active job listings. Read the eligibility criteria carefully. If you qualify, click "Apply Now". The system will auto-check your profile against the requirements.</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-6 d-flex">
                        <div class="me-4">
                            <span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-receipt-2 fs-1"></i></span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">3. Fee Payment</h3>
                            <p class="text-secondary small">After submitting, download the system-generated Bank Challan. Pay the fee at any designated bank branch. Your application status will update to "Fee Paid" within 48 hours of verification.</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="col-md-6 d-flex">
                        <div class="me-4">
                            <span class="avatar avatar-lg bg-teal-lt text-teal rounded-circle"><i class="ti ti-ticket fs-1"></i></span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">4. Download Roll No Slip</h3>
                            <p class="text-secondary small">Once the test is scheduled, you will receive an SMS. Log in to your dashboard to download your Roll Number Slip. Ensure you print it and bring it to the test center along with your original CNIC.</p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-5 border-0 shadow-none bg-primary-lt">
                    <div class="d-flex">
                        <div><i class="ti ti-info-circle fs-2 me-3"></i></div>
                        <div>
                            <h4 class="fw-bold mb-1">Important Note for Test Day</h4>
                            <p class="small mb-0 text-secondary">
                                Reach the test center at least 60 minutes before the reporting time. No candidate will be allowed to enter the premises after the test start time. Use only blue/black ballpoints for filling OMR sheets.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <h3 class="fw-bold">Still have questions?</h3>
            <p class="text-secondary">If you're facing any technical issues, our helpdesk is here to assist you.</p>
            <a href="{{ route('contact') }}" class="btn btn-primary px-5">CONTACT HELPDESK</a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.public')
@section('title', 'How to Apply — Prime Assessment & Testing Services')
@section('header-title', 'Application Guide')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card glass-panel border-0 mb-6 rounded-5 shadow-lg animate__animated animate__fadeIn">
            <div class="card-body p-5 p-md-7">
                <div class="badge bg-indigo-lt text-indigo px-4 py-2 mb-4 rounded-pill fw-black">STEP-BY-STEP GUIDE</div>
                <h2 class="display-5 fw-black text-dark mb-2">How to Apply for a Position</h2>
                <p class="text-secondary fs-3 mb-6 opacity-80">Follow these four simple steps to complete your application. The entire process takes approximately 10–15 minutes once your documents are ready.</p>

                <div class="row g-6">
                    <!-- Step 1 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4 flex-shrink-0">
                            <div class="avatar avatar-xl bg-grad-accent text-white rounded-5 shadow-teal-30"><i class="ti ti-user-plus fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">1. Create Your Account</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Register using your valid <strong>CNIC number</strong> and a functional mobile number. You will need this login for all future applications — create it once, use it forever.</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4 flex-shrink-0">
                            <div class="avatar avatar-xl bg-dark text-white rounded-5 shadow-sm"><i class="ti ti-id-badge-2 fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">2. Complete Your Profile</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Fill in personal, educational, and professional details and upload your photograph. Your profile must reach <strong>100% completion</strong> before you can submit any application.</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4 flex-shrink-0">
                            <div class="avatar avatar-xl bg-indigo text-white rounded-5 shadow-sm"><i class="ti ti-receipt-2 fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">3. Apply & Pay the Fee</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Browse open projects and click <em>Apply</em> on a matching position. Download the auto-generated <strong>Bank Challan</strong> and deposit the fee at any designated branch. Payment is verified within 24–48 hours.</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="col-md-6 d-flex align-items-start hover-lift transition-all">
                        <div class="me-4 flex-shrink-0">
                            <div class="avatar avatar-xl bg-teal text-white rounded-5 shadow-teal-30"><i class="ti ti-ticket fs-0"></i></div>
                        </div>
                        <div>
                            <h3 class="fw-black text-dark h2 mb-2">4. Download Your Roll Number Slip</h3>
                            <p class="text-secondary fs-4 lh-lg opacity-80 fw-medium">Once your payment is verified and a session is assigned, your <strong>Roll Number Slip</strong> appears in your dashboard. Print it and bring it with your original CNIC on test day.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-7 p-5 bg-grad-pats text-white rounded-5 position-relative overflow-hidden shadow-lg">
                    <div class="position-absolute top-0 end-0 p-5 mt-n4 me-n4 opacity-10">
                        <i class="ti ti-shield-alert" style="font-size: 8rem;"></i>
                    </div>
                    <div class="d-flex align-items-center position-relative z-index-2">
                        <i class="ti ti-info-circle-filled fs-0 me-4"></i>
                        <div>
                            <h3 class="fw-black text-white h2 mb-1">TEST DAY REQUIREMENTS</h3>
                            <p class="fs-4 mb-0 opacity-80 fw-medium">
                                Arrive at least <strong>30–45 minutes</strong> before the reported time. Bring your printed Roll Number Slip and <strong>original CNIC</strong>. Electronic devices are strictly prohibited. Malpractice results in immediate disqualification.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Accordion -->
        <div class="card glass-panel border-0 mb-6 rounded-5 shadow-sm animate__animated animate__fadeIn">
            <div class="card-body p-5 p-md-7">
                <div class="badge bg-teal-lt text-teal px-4 py-2 mb-4 rounded-pill fw-black">FREQUENTLY ASKED QUESTIONS</div>
                <h2 class="display-6 fw-black text-dark mb-5">Common Questions</h2>

                <div class="accordion" id="faqAccordion">
                    @php
                    $faqs = [
                        ['q' => 'Can I apply for more than one position?', 'a' => 'Yes, you can apply for multiple positions across different projects, provided you meet the eligibility criteria for each. Each application requires a separate fee deposit.'],
                        ['q' => 'What if I make a mistake in my profile?', 'a' => 'Your profile can be edited freely until you submit your first application. Once an application is submitted, your profile is locked to ensure data integrity. Contact support for corrections.'],
                        ['q' => 'How long does payment verification take?', 'a' => 'Bank payments are typically verified within 24–48 business hours from the time of deposit. You will receive an SMS notification when your payment is confirmed.'],
                        ['q' => 'What is the age relaxation policy?', 'a' => 'Age relaxation is available for candidates belonging to Scheduled Castes, Buddhists, persons with disabilities, and government servants as per Government of Pakistan rules. You can indicate your relaxation category during application.'],
                        ['q' => 'What should I do if I forget my login credentials?', 'a' => 'Use the "Forgot Password" option on the login page. You can reset your password using your registered email address.'],
                        ['q' => 'When will my Roll Number Slip be available?', 'a' => 'Roll Number Slips are generated after the project closing date and payment verification. You will receive an SMS once your slip is ready for download from your dashboard.'],
                    ];
                    @endphp

                    @foreach($faqs as $i => $faq)
                    <div class="accordion-item border-0 border-bottom border-teal border-opacity-10 rounded-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-bold text-dark bg-transparent shadow-none px-0 py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c-{{ $i }}">
                                <i class="ti ti-circle-question text-teal me-3 fs-3 flex-shrink-0"></i>
                                {{ $faq['q'] }}
                            </button>
                        </h2>
                        <div id="faq-c-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 pb-4 text-secondary fs-4 fw-medium opacity-80 ps-5">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-center mb-6">
            <h3 class="display-6 fw-black text-dark mb-3">Can't find your answer?</h3>
            <p class="fs-3 text-secondary mb-5 opacity-80">Our support team is available Monday to Friday, 9 AM – 5 PM.</p>
            <a href="{{ route('contact') }}" class="btn btn-teal btn-lg px-6 rounded-pill fw-black shadow-teal-30 border-0 py-3">CONTACT SUPPORT</a>
        </div>
    </div>
</div>
@endsection

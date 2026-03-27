@extends('layouts.public')
@section('title', 'Contact Us')
@section('header-title', 'CONTACT US')

@section('content')
<div class="row g-5">
    <div class="col-lg-7">
        <div class="card glass-panel border-0 p-5 rounded-5 animate__animated animate__fadeInLeft shadow-sm">
            <h2 class="display-5 fw-black text-dark mb-1">SEND A MESSAGE</h2>
            <p class="text-muted small fw-bold mb-5">Our support team typically responds within 24 business hours.</p>
            
            <form action="#" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-wider">Full Name</label>
                        <input type="text" class="form-control form-control-lg border-0 bg-light rounded-4 px-4 shadow-none" name="name" required placeholder="John Doe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-wider">Email Address</label>
                        <input type="email" class="form-control form-control-lg border-0 bg-light rounded-4 px-4 shadow-none" name="email" required placeholder="name@example.com">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-wider">Inquiry Subject</label>
                        <select class="form-select form-control-lg border-0 bg-light rounded-4 px-4 shadow-none" name="subject">
                            <option>General Inquiry</option>
                            <option>Candidate Support / Login Issues</option>
                            <option>Corporate Partnership</option>
                            <option>Procurement / Tenders</option>
                            <option>Malpractice Reporting</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-wider">Detailed Message</label>
                        <textarea class="form-control border-0 bg-light rounded-4 px-4 shadow-none" name="message" rows="6" required placeholder="How can we help you today?"></textarea>
                    </div>
                    <div class="col-md-12 mt-5">
                        <button type="button" class="btn btn-teal btn-lg text-white px-5 shadow-teal-30 fw-black rounded-pill border-0 py-3 w-100 w-md-auto" style="background: var(--pats-teal)">
                            SEND INQUIRY <i class="ti ti-send ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 bg-grad-pats shadow-lg text-white p-5 mb-5 rounded-5 animate__animated animate__fadeInRight position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="ti ti-building-skyscraper fs-0" style="font-size: 15rem;"></i>
            </div>
            <h3 class="display-6 fw-black text-white mb-5 border-bottom border-white border-opacity-20 pb-2 position-relative z-index-2">HEADQUARTERS</h3>
            
            <div class="mb-5 position-relative z-index-2 hover-lift">
                <div class="text-teal-light small fw-black text-uppercase tracking-widest mb-2 opacity-80">Location</div>
                <div class="fs-3 fw-bold">Plot 96, Street 4, H-8/1, <br>Islamabad, Pakistan</div>
            </div>
            <div class="mb-5 position-relative z-index-2 hover-lift">
                <div class="text-teal-light small fw-black text-uppercase tracking-widest mb-2 opacity-80">UAN Helpline</div>
                <div class="fs-3 fw-bold">(051) 111-728-7XX</div>
            </div>
            <div class="mb-5 position-relative z-index-2 hover-lift">
                <div class="text-teal-light small fw-black text-uppercase tracking-widest mb-2 opacity-80">Email Support</div>
                <div class="fs-4 fw-bold">info@pats.org.pk</div>
            </div>
            <div class="mb-0 position-relative z-index-2 glass-panel-dark p-4 rounded-4">
                <div class="d-flex align-items-center">
                    <i class="ti ti-clock-filled text-teal-light me-3 fs-2"></i>
                    <div>
                        <div class="small fw-black text-uppercase tracking-widest opacity-60">Working Hours</div>
                        <div class="fw-bold">Mon - Fri (09:00 AM - 05:00 PM)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card glass-panel border-0 shadow-sm p-5 rounded-5">
            <h3 class="fw-black mb-4 text-dark border-start border-teal border-5 ps-3 py-1">REGIONAL SUPPORT</h3>
            <ul class="list-unstyled mb-0">
                <li class="mb-4 pb-4 border-bottom border-teal border-opacity-10 d-flex align-items-center hover-lift">
                    <div class="bg-teal-lt p-2 rounded-3 me-3"><i class="ti ti-map-pin text-teal fs-2"></i></div>
                    <div>
                        <div class="fw-black text-dark fs-4">Lahore Office</div>
                        <div class="small text-muted fw-bold opacity-60">Building 12-A, Gulberg III, Lahore</div>
                    </div>
                </li>
                <li class="mb-4 pb-4 border-bottom border-teal border-opacity-10 d-flex align-items-center hover-lift">
                    <div class="bg-indigo-lt p-2 rounded-3 me-3"><i class="ti ti-map-pin text-indigo fs-2"></i></div>
                    <div>
                        <div class="fw-black text-dark fs-4">Karachi Office</div>
                        <div class="small text-muted fw-bold opacity-60">Business Center, Shahrah-e-Faisal, Karachi</div>
                    </div>
                </li>
                <li class="d-flex align-items-center hover-lift">
                    <div class="bg-primary-lt p-2 rounded-3 me-3"><i class="ti ti-map-pin text-primary fs-2"></i></div>
                    <div>
                        <div class="fw-black text-dark fs-4">Peshawar Office</div>
                        <div class="small text-muted fw-bold opacity-60">Hayatabad Phase 5, Peshawar</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

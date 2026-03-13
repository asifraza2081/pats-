@extends('layouts.public')
@section('title', 'Contact Us')
@section('header-title', 'CONTACT US')

@section('content')
<div class="row g-5">
    <div class="col-lg-7">
        <div class="card glass-panel border-0 p-5 rounded-4 animate__animated animate__fadeInLeft shadow-lg">
            <h2 class="section-header">SEND US A MESSAGE</h2>
            <form action="#" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="Your Name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required placeholder="email@example.com">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Subject</label>
                        <select class="form-select" name="subject">
                            <option>General Inquiry</option>
                            <option>Candidate Support</option>
                            <option>Corporate Partnership</option>
                            <option>Procurement / Tenders</option>
                            <option>Malpractice Reporting</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="6" required placeholder="Type your message here..."></textarea>
                    </div>
                    <div class="col-md-12 mt-4 text-end">
                        <button type="button" class="btn btn-teal text-white px-5 shadow-sm" style="background: var(--pats-teal)">SUBMIT INQUIRY</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 bg-primary shadow-lg text-white p-5 mb-4 rounded-4 animate__animated animate__fadeInRight">
            <h3 class="fw-bold text-white mb-4">Headquarters</h3>
            <div class="mb-4">
                <div class="text-white-50 small fw-bold text-uppercase tracking-wider mb-1">Address</div>
                <div class="fs-4">Plot 96, Street 4, H-8/1, Islamabad, Pakistan</div>
            </div>
            <div class="mb-4">
                <div class="text-white-50 small fw-bold text-uppercase tracking-wider mb-1">UAN Helpline</div>
                <div class="fs-4">(051) 111-728-7XX</div>
            </div>
            <div class="mb-4">
                <div class="text-white-50 small fw-bold text-uppercase tracking-wider mb-1">Email Support</div>
                <div class="fs-4">info@pats.org.pk</div>
            </div>
            <div class="mb-0">
                <div class="text-white-50 small fw-bold text-uppercase tracking-wider mb-1">Office Hours</div>
                <div class="fs-4">Mon - Fri (09:00 AM - 05:00 PM)</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <h3 class="fw-bold mb-3">Regional Support</h3>
            <ul class="list-unstyled mb-0">
                <li class="mb-3 pb-3 border-bottom">
                    <div class="fw-bold">Lahore Office</div>
                    <div class="small text-muted">Building 12-A, Gulberg III, Lahore</div>
                </li>
                <li class="mb-3 pb-3 border-bottom">
                    <div class="fw-bold">Karachi Office</div>
                    <div class="small text-muted">Business Center, Shahrah-e-Faisal, Karachi</div>
                </li>
                <li>
                    <div class="fw-bold">Peshawar Office</div>
                    <div class="small text-muted">Hayatabad Phase 5, Peshawar</div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'PATS — Prime Assessment & Testing Services')
@section('meta_description', 'Official candidate portal for PATS — Prime Assessment & Testing Services')

@push('styles')
<style>
    .nts-marquee-wrapper {
        background-color: #d9534f; /* bootstrap border-danger red */
        color: white;
        padding: 8px 0;
        font-weight: 500;
        font-size: 0.95rem;
        border-bottom: 2px solid #c9302c;
    }
    .nts-hero {
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"><rect width="100%" height="100%" fill="%230a3d62"/><text x="50%" y="50%" font-size="20" fill="rgba(255,255,255,0.05)" font-family="Arial" text-anchor="middle" alignment-baseline="middle">TESTING EXCELLENCE</text></svg>'), linear-gradient(135deg, #0a3d62 0%, #1a5276 100%);
        padding: 4rem 0;
        color: white;
        border-bottom: 5px solid #f9ca24;
    }
    .nts-quick-btn {
        display: block;
        padding: 1.5rem 1rem;
        background: #fff;
        border: 2px solid #e0e6ed;
        border-left: 5px solid var(--pats-primary);
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    }
    .nts-quick-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        border-color: var(--pats-primary);
        color: var(--pats-primary);
    }
    .nts-quick-btn i {
        font-size: 2rem;
        color: var(--pats-accent);
        margin-bottom: 10px;
        display: block;
    }
    .section-title {
        border-left: 5px solid var(--pats-accent);
        padding-left: 15px;
        color: var(--pats-primary);
        font-weight: 700;
        margin-bottom: 30px;
    }
    .project-card {
        border-top: 3px solid var(--pats-primary);
        transition: all 0.3s ease;
    }
    .project-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')

{{-- NTS Style Marquee --}}
<div class="nts-marquee-wrapper">
    <div class="container d-flex align-items-center">
        <div class="fw-bold pe-3 border-end border-white me-3 text-uppercase" style="white-space:nowrap;">
            <i class="ti ti-bell-ringing me-1"></i> Alerts
        </div>
        <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
            Welcome to the new Prime Assessment & Testing Services (PATS) Portal. Please register and complete your profile before applying for any available positions. For assistance, contact our support desk. 
            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; 
            <strong>New jobs for Ministry of IT are now OPEN! Apply before the closing date.</strong>
        </marquee>
    </div>
</div>

{{-- Hero Section --}}
<section class="nts-hero">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-8 mb-4 mb-md-0">
                <h1 class="display-5 fw-bold mb-3 shadow-sm">Building Merit, Shaping Futures</h1>
                <p class="fs-5 text-white-50 mb-0">
                    Transparent, efficient, and technology-driven testing and assessment services for public and private sector organizations.
                </p>
            </div>
            <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                <div class="bg-white p-4 rounded-3 shadow text-dark d-inline-block text-start w-100" style="max-width: 300px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Candidate Login</h5>
                    <a href="{{ route('login') }}" class="btn btn-pats w-100 mb-2 fw-semibold">
                        <i class="ti ti-login me-2"></i>Sign In
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn btn-outline-secondary w-100 fw-semibold">
                        <i class="ti ti-user-plus me-2"></i>Create Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- NTS Style Quick Links / Main Buttons --}}
<section class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <a href="{{ route('projects') }}" class="nts-quick-btn h-100" style="border-left-color: #0d6efd;">
                    <i class="ti ti-speakerphone" style="color: #0d6efd;"></i>
                    Open Applications
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('login') }}" class="nts-quick-btn h-100" style="border-left-color: #198754;">
                    <i class="ti ti-layout-dashboard" style="color: #198754;"></i>
                    Candidate Portal
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('login') }}" class="nts-quick-btn h-100" style="border-left-color: #fd7e14;">
                    <i class="ti ti-receipt" style="color: #fd7e14;"></i>
                    Roll No Slips
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('results.search') }}" class="nts-quick-btn h-100" style="border-left-color: #dc3545;">
                    <i class="ti ti-trophy" style="color: #dc3545;"></i>
                    All Results
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Main Content Area: Latest Projects & Instructions --}}
<section class="py-5 section-white">
    <div class="container">
        <div class="row">
            {{-- Left Column: Projects --}}
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="section-title">Latest Projects</h3>
                
                @if($openProjects->isEmpty())
                <div class="alert alert-secondary border-0 bg-light p-4 text-center">
                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-muted"></i>
                    No active projects at this time.
                </div>
                @else
                    <div class="d-flex flex-column gap-4">
                        @foreach($openProjects as $project)
                        <div class="card project-card border-0 shadow-sm rounded-3">
                            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                <div class="mb-3 mb-md-0">
                                    <h5 class="fw-bold mb-1 text-dark">{{ $project->name }}</h5>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-building me-1"></i>{{ $project->org_name }}
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Status: Open</span>
                                        @if($project->close_date)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-calendar-x me-1"></i>Last Date: {{ $project->close_date->format('d-M-Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-md-end ms-md-4 mt-3 mt-md-0">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-pats px-4 shadow-sm fw-semibold">
                                        Details & Apply <i class="bi bi-chevron-right ms-1 small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="text-end mt-4">
                        <a href="{{ route('projects') }}" class="text-decoration-none fw-bold" style="color: var(--pats-accent);">
                            View All Projects <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Right Column: Instructions / Information --}}
            <div class="col-lg-4">
                <h3 class="section-title">Instructions</h3>
                
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white fw-bold d-flex align-items-center py-3">
                        <i class="bi bi-info-square-fill text-primary me-2 fs-5"></i> How to Apply?
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item py-3 px-4">
                                <span class="badge bg-primary rounded-circle me-2">1</span> 
                                <strong>Register Account:</strong> Sign up with your functional CNIC and mobile number.
                            </li>
                            <li class="list-group-item py-3 px-4">
                                <span class="badge bg-primary rounded-circle me-2">2</span> 
                                <strong>Complete Profile:</strong> Update your personal, educational, and professional data.
                            </li>
                            <li class="list-group-item py-3 px-4">
                                <span class="badge bg-primary rounded-circle me-2">3</span> 
                                <strong>Submit Application:</strong> Click on specific jobs to apply. Provide required fee challan details.
                            </li>
                            <li class="list-group-item py-3 px-4">
                                <span class="badge bg-primary rounded-circle me-2">4</span> 
                                <strong>Roll No Slip:</strong> Download your slip once payment is verified and test batch is assigned.
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white fw-bold d-flex align-items-center py-3">
                        <i class="bi bi-headset text-success me-2 fs-5"></i> Need Help?
                    </div>
                    <div class="card-body px-4 py-4 small">
                        <p class="mb-2"><i class="bi bi-telephone text-muted me-2"></i> <strong>Call:</strong> +92 (51) 1234567</p>
                        <p class="mb-2"><i class="bi bi-envelope text-muted me-2"></i> <strong>Email:</strong> support@pats.org.pk</p>
                        <p class="mb-0 text-muted mt-3 fst-italic">Timing: Monday to Friday (9:00 AM to 5:00 PM)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

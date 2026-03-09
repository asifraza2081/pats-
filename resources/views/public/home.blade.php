@extends('layouts.app')
@section('title', 'PATS — Prime Assessment & Testing Services')
@section('meta_description', 'Official candidate portal for PATS — Prime Assessment & Testing Services')

@section('content')
{{-- Hero --}}
<section style="background: linear-gradient(135deg, #0a3d62 0%, #1a5276 60%, #2471a3 100%); color:#fff; padding: 5rem 0 4rem;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge mb-3" style="background:rgba(255,255,255,.15); font-size:.85rem; padding:.5em 1em">
                    <i class="bi bi-award-fill me-1" style="color:#f9ca24"></i> Official Testing Platform
                </span>
                <h1 class="display-5 fw-bold mb-3">Prime Assessment &amp; Testing Services</h1>
                <p class="lead mb-4" style="color:rgba(255,255,255,.85)">
                    Register, apply, and manage your test applications — all in one place.
                    Transparent, efficient, and candidate-friendly.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('auth.register') }}" class="btn btn-lg px-4 fw-semibold" style="background:#e84118; color:#fff">
                        <i class="bi bi-person-plus-fill me-2"></i>Register Now
                    </a>
                    <a href="{{ route('projects') }}" class="btn btn-lg btn-outline-light px-4 fw-semibold">
                        <i class="bi bi-briefcase me-2"></i>View Open Jobs
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div class="p-4 rounded-4 text-center" style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); min-width:260px;">
                    <i class="bi bi-clipboard2-check-fill mb-2" style="font-size:3rem; color:#f9ca24"></i>
                    <div class="fw-bold fs-5">How It Works</div>
                    <div class="mt-3 text-start small" style="color:rgba(255,255,255,.8)">
                        <div class="mb-2"><i class="bi bi-1-circle-fill me-2" style="color:#f9ca24"></i>Register &amp; verify mobile</div>
                        <div class="mb-2"><i class="bi bi-2-circle-fill me-2" style="color:#f9ca24"></i>Complete your profile</div>
                        <div class="mb-2"><i class="bi bi-3-circle-fill me-2" style="color:#f9ca24"></i>Apply for a post</div>
                        <div class="mb-2"><i class="bi bi-4-circle-fill me-2" style="color:#f9ca24"></i>Pay fee via challan</div>
                        <div><i class="bi bi-5-circle-fill me-2" style="color:#f9ca24"></i>Download roll number slip</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Open Projects --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold mb-0" style="color:var(--pats-primary)">
                <i class="bi bi-folder2-open me-2" style="color:var(--pats-accent)"></i>Current Opportunities
            </h2>
            <a href="{{ route('projects') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>

        @if($openProjects->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-hourglass-split fs-1 mb-3 d-block"></i>
            <p>No open positions at the moment. Check back soon.</p>
        </div>
        @else
        <div class="row g-4">
            @foreach($openProjects as $project)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden" style="transition:.2s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3 p-2 rounded-3" style="background:#eef4fb">
                                <i class="bi bi-building fs-4" style="color:var(--pats-primary)"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1 fw-bold">{{ $project->name }}</h5>
                                <p class="text-muted small mb-0">{{ $project->org_name }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-success">Open</span>
                            @if($project->close_date)
                            <span class="badge bg-warning text-dark">Closes: {{ $project->close_date->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-pats w-100 fw-semibold">
                            <i class="bi bi-arrow-right me-1"></i>View Posts
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Quick Links --}}
<section class="py-4" style="background:var(--pats-primary); color:#fff">
    <div class="container">
        <div class="row g-3 justify-content-center text-center">
            <div class="col-6 col-md-3">
                <a href="{{ route('results.search') }}" class="text-white text-decoration-none">
                    <i class="bi bi-bar-chart-line-fill fs-2 mb-2 d-block" style="color:#f9ca24"></i>
                    <div class="fw-semibold">Check Results</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('auth.register') }}" class="text-white text-decoration-none">
                    <i class="bi bi-person-plus-fill fs-2 mb-2 d-block" style="color:#f9ca24"></i>
                    <div class="fw-semibold">Register</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('auth.login') }}" class="text-white text-decoration-none">
                    <i class="bi bi-box-arrow-in-right fs-2 mb-2 d-block" style="color:#f9ca24"></i>
                    <div class="fw-semibold">Candidate Portal</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('projects') }}" class="text-white text-decoration-none">
                    <i class="bi bi-briefcase-fill fs-2 mb-2 d-block" style="color:#f9ca24"></i>
                    <div class="fw-semibold">All Jobs</div>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

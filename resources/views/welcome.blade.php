<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PATS') }} - Testing Services</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Tabler Core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body { font-feature-settings: "cv03", "cv04", "cv11"; }
        .hero {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%), url('{{ asset('hero.png') }}') center/cover no-repeat;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
    </style>
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md d-print-none text-bg-light" data-bs-theme="light">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ url('/') }}" class="text-decoration-none fw-bold fs-2 text-primary">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="32" class="navbar-brand-image">
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex me-3">
                        <a href="{{ route('results.search') }}" class="btn btn-outline-primary shadow-sm" rel="noreferrer">
                            <i class="ti ti-search pe-2"></i> Result Search
                        </a>
                    </div>
                    @auth
                        <div class="nav-item">
                            @if(auth()->user()->hasRole('candidate'))
                                <a href="{{ route('candidate.dashboard') }}" class="btn btn-primary shadow-sm">Dashboard</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary shadow-sm">Dashboard</a>
                            @endif
                        </div>
                    @else
                        <div class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-primary shadow-sm">Sign In</a>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <div class="hero mb-5">
            <div class="container-xl">
                <h1 class="display-4 fw-bold mb-3">Professional Assessment & Testing Services</h1>
                <p class="fs-2 mb-4 opacity-75">Merit-based recruitment, transparent testing, and efficient public sector hiring.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#projects" class="btn btn-light btn-lg text-primary shadow-sm fw-bold">View Open Projects</a>
                    @guest
                    <a href="{{ route('auth.register') }}" class="btn btn-outline-light btn-lg">Register Profile</a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <h2 id="projects" class="text-center mb-4"><i class="ti ti-briefcase mb-1"></i> Active Recruitment Projects</h2>
                @if(isset($projects) && $projects->count() > 0)
                <div class="row row-cards">
                    @foreach($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-stacked shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if($project->logo_path)
                                        <span class="avatar me-3 border shadow-none" style="background-image: url('{{ asset('storage/' . $project->logo_path) }}')"></span>
                                    @else
                                        <span class="avatar text-bg-primary me-3 border shadow-none">{{ substr($project->org_name ?? 'P', 0, 1) }}</span>
                                    @endif
                                    <div>
                                        <h3 class="card-title mb-0 text-truncate" style="max-width: 200px;">{{ $project->name }}</h3>
                                        <div class="text-secondary small">{{ $project->org_name }}</div>
                                    </div>
                                </div>
                                <p class="text-secondary" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $project->description }}
                                </p>
                                <div class="mt-4 text-center">
                                    <span class="badge bg-green text-green-fg w-100 py-2 fs-4">Status: {{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                                </div>
                            </div>
                            <div class="card-footer text-end mt-auto">
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary w-100">View Jobs & Apply</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty">
                    <div class="empty-img"><i class="ti ti-folder-off text-muted opacity-50" style="font-size: 8rem;"></i></div>
                    <p class="empty-title">No Active Projects</p>
                    <p class="empty-subtitle text-secondary">
                        There are currently no open recruitment projects available for application. Please check back later.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Features Section -->
        <div class="page-body mt-5">
            <div class="container-xl">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="display-6 fw-bold">Why Choose Prime Assessment & Testing Services?</h2>
                        <p class="text-secondary fs-3">We ensure a seamless, transparent, and merit-driven assessment process.</p>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-5">
                                <span class="bg-primary text-white avatar avatar-xl mb-4 rounded-circle">
                                    <i class="ti ti-shield-check fs-1"></i>
                                </span>
                                <h3 class="fw-bold">100% Transparency</h3>
                                <p class="text-secondary">Our merit-based systems guarantee absolute fairness and equality in all selection procedures and tests.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-5">
                                <span class="bg-primary text-white avatar avatar-xl mb-4 rounded-circle">
                                    <i class="ti ti-rocket fs-1"></i>
                                </span>
                                <h3 class="fw-bold">Fast Processing</h3>
                                <p class="text-secondary">From application to result declaration, our fully digital platform ensures rapid response and processing times.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-5">
                                <span class="bg-primary text-white avatar avatar-xl mb-4 rounded-circle">
                                    <i class="ti ti-headset fs-1"></i>
                                </span>
                                <h3 class="fw-bold">Dedicated Support</h3>
                                <p class="text-secondary">Have a question? Our responsive support desk is available to assist candidates every step of the way.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-5">

                <div class="row align-items-center mb-5">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="display-6 fw-bold mb-3">How to Apply</h2>
                        <div class="list-group list-group-flush list-group-hoverable">
                            <div class="list-group-item border-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="badge bg-primary rounded-circle p-2 fs-2">1</span></div>
                                    <div class="col text-truncate">
                                        <div class="text-body fw-bold fs-3">Register an Account</div>
                                        <div class="text-secondary text-wrap">Create your secure profile using your functional CNIC and active mobile number.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="badge bg-primary rounded-circle p-2 fs-2">2</span></div>
                                    <div class="col text-truncate">
                                        <div class="text-body fw-bold fs-3">Complete Your Profile</div>
                                        <div class="text-secondary text-wrap">Add your academic records, professional experience, and upload your photo.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="badge bg-primary rounded-circle p-2 fs-2">3</span></div>
                                    <div class="col text-truncate">
                                        <div class="text-body fw-bold fs-3">Apply & Pay Fee</div>
                                        <div class="text-secondary text-wrap">Browse active projects, submit your application, and download/pay the challan form.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="badge bg-primary rounded-circle p-2 fs-2">4</span></div>
                                    <div class="col text-truncate">
                                        <div class="text-body fw-bold fs-3">Download Slips & View Results</div>
                                        <div class="text-secondary text-wrap">Access your test roll number slip from your personal dashboard.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div class="bg-light p-5 rounded-4 border">
                            <i class="ti ti-certificate text-primary mb-3" style="font-size: 5rem;"></i>
                            <h3 class="fw-bold mb-3">Ready to begin your journey?</h3>
                            <a href="{{ route('auth.register') }}" class="btn btn-primary btn-lg shadow">Create Candidate Profile</a>
                            <p class="text-secondary mt-3 small">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer footer-transparent d-print-none mt-auto">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright &copy; {{ date('Y') }} PATS. All rights reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>

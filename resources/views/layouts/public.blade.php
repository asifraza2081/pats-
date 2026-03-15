<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PATS') - Professional Assessment & Testing Services</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Tabler Core & Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/inter.css') }}">
    <!-- Core Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/pats-core.css') }}">
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Top Info Bar -->
        <div class="top-bar d-none d-md-block">
            <div class="container-xl">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="ti ti-phone me-1"></i> Helpline: (051) 111-728-7XX
                    </div>
                    <div class="col-auto ms-3">
                        <i class="ti ti-mail me-1"></i> info@pats.org.pk
                    </div>
                    <div class="col text-end">
                        @guest
                            <a href="{{ route('login') }}" class="text-white text-decoration-none me-3">Login</a>
                            <a href="{{ route('auth.register') }}" class="text-white text-decoration-none">Register</a>
                        @else
                            <span class="text-white-50 small me-3">Signed in as <strong>{{ auth()->user()->full_name }}</strong></span>
                            <a href="{{ route('auth.logout') }}" class="text-white text-decoration-none small" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">@csrf</form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <!-- Main Navigation -->
        <header class="main-nav sticky-top" id="navbar">
            <div class="container-xl">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ url('/') }}" class="text-decoration-none hover-lift">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="55" class="pats-logo">
                    </a>
                    
                    <div class="d-none d-lg-block">
                        <ul class="nav">
                            <li class="nav-item"><a href="{{ url('/') }}" class="nav-link text-dark fw-bold">Home</a></li>
                            <li class="nav-item"><a href="{{ route('about') }}" class="nav-link text-dark">About Us</a></li>
                            <li class="nav-item"><a href="{{ route('projects') }}" class="nav-link text-dark">Open Projects</a></li>
                            <li class="nav-item"><a href="{{ route('results.search') }}" class="nav-link text-dark">Results</a></li>
                            <li class="nav-item"><a href="{{ route('downloads') }}" class="nav-link text-dark">Downloads</a></li>
                            <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link text-dark">Contact</a></li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="d-none d-sm-flex gap-2 me-2">
                            <a href="?theme=dark" class="btn btn-icon btn-ghost-secondary rounded-circle hide-theme-dark" title="Enable dark mode">
                                <i class="ti ti-moon"></i>
                            </a>
                            <a href="?theme=light" class="btn btn-icon btn-ghost-secondary rounded-circle hide-theme-light" title="Enable light mode">
                                <i class="ti ti-sun"></i>
                            </a>
                        </div>
                        @auth
                            @php
                                $dashboardRoute = match(true) {
                                    auth()->user()->hasRole('candidate') => route('candidate.dashboard'),
                                    auth()->user()->hasRole('examiner') => route('examiner.dashboard'),
                                    default => route('admin.dashboard')
                                };
                                $portalName = match(true) {
                                    auth()->user()->hasRole('candidate') => 'Candidate Portal',
                                    auth()->user()->hasRole('examiner') => 'Examiner Portal',
                                    default => 'Admin Panel'
                                };
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="btn btn-teal text-white fw-bold shadow-sm px-4">
                                <i class="ti ti-layout-dashboard me-2"></i> {{ $portalName }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="ti ti-login me-2"></i> Sign In
                            </a>
                        @endauth
                        <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                            <i class="ti ti-menu-2 fs-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        @hasSection('header-title')
        <section class="page-header-nts">
            <div class="container-xl py-2">
                <h1 class="display-5 fw-bold mb-0">@yield('header-title')</h1>
                @hasSection('header-breadcrumb')
                    <div class="mt-2 opacity-80">@yield('header-breadcrumb')</div>
                @endif
            </div>
        </section>
        @endif

        <!-- Main Content Area -->
        <div class="page-body">
            <div class="container-xl">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer-nts">
            <div class="container-xl">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="60" class="mb-4 brightness-0 invert opacity-90">
                        <p class="opacity-70 fs-4">PATS is Pakistan's leading autonomous testing agency, committed to merit, transparency, and building a professional workforce through precision assessment.</p>
                        <div class="d-flex gap-3 mt-4">
                            <a href="#" class="btn btn-icon btn-ghost-light rounded-circle"><i class="ti ti-brand-facebook"></i></a>
                            <a href="#" class="btn btn-icon btn-ghost-light rounded-circle"><i class="ti ti-brand-x"></i></a>
                            <a href="#" class="btn btn-icon btn-ghost-light rounded-circle"><i class="ti ti-brand-linkedin"></i></a>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h4 class="fw-bold mb-4 text-white">RESOURCES</h4>
                        <ul class="list-unstyled opacity-75 small">
                            <li class="mb-3"><a href="{{ url('/') }}" class="text-white text-decoration-none hover-lift d-block">Home</a></li>
                            <li class="mb-3"><a href="{{ route('about') }}" class="text-white text-decoration-none hover-lift d-block">About Us</a></li>
                            <li class="mb-3"><a href="{{ route('projects') }}" class="text-white text-decoration-none hover-lift d-block">Open Projects</a></li>
                            <li class="mb-3"><a href="{{ route('downloads') }}" class="text-white text-decoration-none hover-lift d-block">Downloads</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h4 class="fw-bold mb-4 text-white">CANDIDATES</h4>
                        <ul class="list-unstyled opacity-75 small">
                            <li class="mb-3"><a href="{{ route('results.search') }}" class="text-white text-decoration-none hover-lift d-block">Search Results</a></li>
                            <li class="mb-3"><a href="{{ route('auth.register') }}" class="text-white text-decoration-none hover-lift d-block">Registration</a></li>
                            <li class="mb-3"><a href="{{ route('login') }}" class="text-white text-decoration-none hover-lift d-block">Login Portal</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="fw-bold mb-4 text-white">CONTACT INFO</h4>
                        <div class="d-flex mb-3">
                            <i class="ti ti-map-pin me-3 text-teal fs-2"></i>
                            <span class="opacity-75 small">Plot 96, Street 4, H-8/1, Islamabad, Pakistan</span>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="ti ti-phone me-3 text-teal fs-2"></i>
                            <span class="opacity-75 small">(051) 111-728-7XX</span>
                        </div>
                        <div class="d-flex">
                            <i class="ti ti-mail me-3 text-teal fs-2"></i>
                            <span class="opacity-75 small">support@pats.org.pk</span>
                        </div>
                    </div>
                </div>
                <hr class="my-5 opacity-10">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center opacity-60 small">
                    <div class="mb-3 mb-md-0">Copyright &copy; {{ date('Y') }} PATS. All rights reserved.</div>
                    <div class="d-flex gap-4">
                        <a href="#" class="text-white text-decoration-none">Privacy Policy</a>
                        <a href="#" class="text-white text-decoration-none">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- Localized Core Script -->
    <script src="{{ asset('assets/vendor/js/tabler.min.js') }}"></script>
    <script>
        const navbar = document.getElementById('navbar');
        window.onscroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        const currentTheme = localStorage.getItem('pats-theme') || 'light';
        document.body.setAttribute('data-bs-theme', currentTheme);
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PATS') - Professional Assessment & Testing Services</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Tabler Core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
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
        <header class="main-nav sticky-top">
            <div class="container-xl">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="55">
                    </a>
                    
                    <div class="d-none d-lg-block">
                        <ul class="nav">
                            <li class="nav-item"><a href="{{ url('/') }}" class="nav-link text-dark">Home</a></li>
                            <li class="nav-item"><a href="{{ route('about') }}" class="nav-link text-dark">About Us</a></li>
                            <li class="nav-item"><a href="{{ route('projects') }}" class="nav-link text-dark">Open Projects</a></li>
                            <li class="nav-item"><a href="{{ route('results.search') }}" class="nav-link text-dark">Results</a></li>
                            <li class="nav-item"><a href="{{ route('downloads') }}" class="nav-link text-dark">Downloads</a></li>
                            <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link text-dark">Contact</a></li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center">
                        <a href="?theme=dark" class="nav-link px-0 hide-theme-dark me-3" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-moon"></i>
                        </a>
                        <a href="?theme=light" class="nav-link px-0 hide-theme-light me-3" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-sun"></i>
                        </a>
                        @auth
                            @if(auth()->user()->hasRole('candidate'))
                                <a href="{{ route('candidate.dashboard') }}" class="btn btn-teal text-white fw-bold shadow-sm" style="background: var(--pats-teal)">My Dashboard</a>
                            @elseif(auth()->user()->hasRole('examiner'))
                                <a href="{{ route('examiner.dashboard') }}" class="btn btn-teal text-white fw-bold shadow-sm" style="background: var(--pats-teal)">Examiner Portal</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-teal text-white fw-bold shadow-sm" style="background: var(--pats-teal)">Admin Panel</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary d-none d-md-inline-block px-4">Sign In</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        @hasSection('header-title')
        <section class="page-header-nts">
            <div class="container-xl">
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
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="60" class="mb-4 brightness-0 invert">
                        <p class="opacity-75 small">Prime Assessment & Testing Service is Pakistan's leading autonomous testing agency, committed to merit, transparency, and building a professional workforce.</p>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h4 class="fw-bold mb-4">QUICK LINKS</h4>
                        <ul class="list-unstyled opacity-75 small">
                            <li class="mb-2"><a href="{{ url('/') }}" class="text-white text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="{{ route('about') }}" class="text-white text-decoration-none">About Us</a></li>
                            <li class="mb-2"><a href="{{ route('projects') }}" class="text-white text-decoration-none">Open Projects</a></li>
                            <li class="mb-2"><a href="{{ route('downloads') }}" class="text-white text-decoration-none">Downloads</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h4 class="fw-bold mb-4">CANDIDATES</h4>
                        <ul class="list-unstyled opacity-75 small">
                            <li class="mb-2"><a href="{{ route('results.search') }}" class="text-white text-decoration-none">Search Results</a></li>
                            <li class="mb-2"><a href="{{ route('auth.register') }}" class="text-white text-decoration-none">Registration</a></li>
                            <li class="mb-2"><a href="{{ route('login') }}" class="text-white text-decoration-none">Login Portal</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="fw-bold mb-4">CONTACT</h4>
                        <p class="opacity-75 small mb-1"><i class="ti ti-map-pin me-2"></i> Plot 96, Street 4, H-8/1, Islamabad</p>
                        <p class="opacity-75 small mb-1"><i class="ti ti-phone me-2"></i> (051) 111-728-7XX</p>
                        <p class="opacity-75 small"><i class="ti ti-mail me-2"></i> support@pats.org.pk</p>
                    </div>
                </div>
                <hr class="my-5 opacity-20">
                <div class="text-center opacity-60 small">
                    Copyright &copy; {{ date('Y') }} PATS. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('theme')) {
            const theme = urlParams.get('theme');
            localStorage.setItem('pats-theme', theme);
        }
        const currentTheme = localStorage.getItem('pats-theme') || 'light';
        document.body.setAttribute('data-bs-theme', currentTheme);
    </script>
    @stack('scripts')
</body>
</html>

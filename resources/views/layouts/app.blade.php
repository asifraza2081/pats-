<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PATS — Prime Assessment & Testing Services')</title>
    <meta name="description" content="@yield('meta_description', 'PATS — Official recruitment testing platform.')">
    
    <!-- Tabler Core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="{{ asset('assets/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --pats-primary: #0a3d62;
            --pats-accent: #e84118;
            --pats-gold: #f9ca24;
        }
        body { font-feature-settings: "cv03", "cv04", "cv11"; }
        .bg-pats-primary { background-color: var(--pats-primary) !important; color: white; }
        .text-pats-accent { color: var(--pats-accent) !important; }
        .text-pats-gold { color: var(--pats-gold) !important; }
        .btn-pats { background-color: var(--pats-primary); color: white; }
        .btn-pats:hover { background-color: #07305a; color: white; }
    </style>
    @stack('styles')
</head>
<body class="layout-fluid">
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/demo-theme.min.js"></script>
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md d-print-none bg-pats-primary" data-bs-theme="dark">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center py-2">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="65" class="me-2" style="filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.5)); max-height: 65px;">
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex me-3">
                        <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-moon fs-2 text-white"></i>
                        </a>
                        <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-sun fs-2 text-white"></i>
                        </a>
                    </div>
                    @auth
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                                <span class="avatar avatar-sm rounded-circle" style="background-image: url('{{ auth()->user()->hasRole('candidate') && auth()->user()->candidate?->photo_path ? Storage::url(auth()->user()->candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}')"></span>
                                <div class="d-none d-xl-block ps-2">
                                    <div class="text-white">{{ auth()->user()->first_name }}</div>
                                    <div class="mt-1 small text-white-50">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                @if(auth()->user()->hasRole('candidate'))
                                    <a href="{{ route('candidate.dashboard') }}" class="dropdown-item">Dashboard</a>
                                @else
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">Admin Panel</a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        @if(!session('otp_user_id'))
                            <div class="nav-item d-none d-md-flex me-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Sign in</a>
                            </div>
                            <div class="nav-item">
                                <a href="{{ route('auth.register') }}" class="btn btn-warning btn-sm text-dark fw-bold">Register</a>
                            </div>
                        @endif
                    @endauth
                </div>
                
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center justify-content-center">
                        <ul class="navbar-nav">
                            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <a class="nav-link text-white" href="{{ route('home') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home fs-2"></i></span>
                                    <span class="nav-link-title">Home</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('projects*') ? 'active' : '' }}">
                                <a class="nav-link text-white" href="{{ route('projects') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-briefcase fs-2"></i></span>
                                    <span class="nav-link-title">Active Jobs</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('results*') ? 'active' : '' }}">
                                <a class="nav-link text-white" href="{{ route('results.search') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-award fs-2"></i></span>
                                    <span class="nav-link-title">Results</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <!-- Page body -->
            <div class="page-body mt-0">
                <!-- Flash Messages -->
                <div class="container-xl mt-3">
                    @foreach(['success', 'error', 'info', 'warning'] as $type)
                        @if(session($type))
                            <div class="alert alert-important alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-{{ $type === 'success' ? 'check' : ($type === 'error' ? 'alert-circle' : 'info-circle') }} fs-2 me-2"></i>
                                    </div>
                                    <div>{{ session($type) }}</div>
                                </div>
                                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif
                    @endforeach
                </div>

                @yield('content')
            </div>

            <footer class="footer footer-transparent d-print-none mt-auto py-5 bg-dark text-white">
                <div class="container-xl">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-12 col-md-auto text-center text-md-start mb-3 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-3">
                                <i class="ti ti-award text-pats-gold fs-1 me-2"></i>
                                <span class="fs-2 fw-bold">PATS</span>
                            </div>
                            <p class="text-white-50 small mb-0">Prime Assessment &amp; Testing Services</p>
                            <p class="text-white-50 small">Empowering Merit through Transparent Testing.</p>
                        </div>
                        <div class="col-12 col-md-auto text-center text-md-end text-white-50 small">
                            <ul class="list-inline mb-2">
                                <li class="list-inline-item"><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
                                <li class="list-inline-item">&middot;</li>
                                <li class="list-inline-item"><a href="{{ route('projects') }}" class="text-white text-decoration-none">Careers</a></li>
                                <li class="list-inline-item">&middot;</li>
                                <li class="list-inline-item"><a href="{{ route('results.search') }}" class="text-white text-decoration-none">Results</a></li>
                            </ul>
                            <div>&copy; {{ date('Y') }} PATS. All rights reserved.</div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>

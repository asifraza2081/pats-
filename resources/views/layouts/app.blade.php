<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PATS — Prime Assessment & Testing Services')</title>
    <meta name="description" content="@yield('meta_description', 'PATS — Official recruitment testing platform.')">

    {{-- Local Bootstrap 5 --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    <style>
        @font-face {
            font-family: 'Inter';
            src: url("{{ asset('assets/fonts/Inter.woff2') }}") format('woff2');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        :root {
            --pats-primary:   #0a3d62;
            --pats-accent:    #e84118;
            --pats-gold:      #f9ca24;
            --pats-bg:        #f4f6f9;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--pats-bg); }
        .navbar-brand span { color: var(--pats-accent); }
        .btn-pats { background: var(--pats-primary); color: #fff; }
        .btn-pats:hover { background: #07305a; color: #fff; }
        .badge-status-submitted       { background: #6c757d; }
        .badge-status-fee_paid        { background: #0d6efd; }
        .badge-status-appeared        { background: #198754; }
        .badge-status-absent          { background: #dc3545; }
        .badge-status-result_declared { background: #6610f2; }
        .sidebar { min-height: 100vh; background: var(--pats-primary); }
        .sidebar .nav-link { color: rgba(255,255,255,.8); padding: .6rem 1.2rem; border-radius: 6px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
        .sidebar .nav-link i { width: 1.4rem; }
        @media (max-width: 767px) { .sidebar { min-height: auto; } }
    </style>
    @stack('styles')
</head>
<body>

{{-- Public Navbar --}}
<nav class="navbar navbar-expand-lg shadow-sm" style="background:var(--pats-primary);">
    <div class="container">
        <a class="navbar-brand fw-bold text-white fs-5" href="{{ route('home') }}">
            <i class="bi bi-award-fill me-1" style="color:var(--pats-gold)"></i>
            PATS <span>Portal</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('projects') }}">Jobs</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('results.search') }}">Results</a></li>
                @auth
                    @if(auth()->user()->hasRole('candidate'))
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('candidate.dashboard') }}">Dashboard</a></li>
                    @else
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-light ms-2">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="btn btn-sm btn-outline-light ms-2" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-sm ms-2" style="background:var(--pats-accent);color:#fff" href="{{ route('auth.register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
<div class="container mt-3">
    @foreach(['success','error','info','warning'] as $type)
        @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    @endforeach
</div>

@yield('content')

<footer class="mt-5 py-4 text-center text-muted small" style="background:#e9ecef">
    &copy; {{ date('Y') }} Prime Assessment &amp; Testing Services (PATS). All rights reserved.
</footer>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>

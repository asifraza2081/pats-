<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — PATS</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    <style>
        @font-face {
            font-family: 'Inter';
            src: url("{{ asset('assets/fonts/Inter.woff2') }}") format('woff2');
            font-weight: 100 900; font-style: normal; font-display: swap;
        }
        :root { --pats-primary:#0a3d62; --pats-accent:#e84118; }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; }
        .admin-sidebar {
            min-height: 100vh; width: 240px; background: var(--pats-primary);
            position: fixed; top: 0; left: 0; z-index: 1000; overflow-y: auto;
        }
        .admin-sidebar .brand {
            padding: 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff; font-weight: 700; font-size: 1.1rem; text-decoration: none;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,.75); padding: .55rem 1.1rem; border-radius: 6px; margin: 2px 8px;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,.15); color: #fff;
        }
        .admin-sidebar .nav-section {
            color: rgba(255,255,255,.4); font-size: .7rem; text-transform: uppercase;
            letter-spacing: .08em; padding: .8rem 1.2rem .2rem;
        }
        .admin-main { margin-left: 240px; padding: 1.5rem; min-height: 100vh; }
        .admin-topbar {
            background: #fff; padding: .6rem 1.5rem; margin: -1.5rem -1.5rem 1.5rem;
            border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between;
        }
        .stat-card { border: none; border-radius: 12px; transition: transform .15s; }
        .stat-card:hover { transform: translateY(-2px); }
        @media (max-width: 991px) {
            .admin-sidebar { width: 200px; }
            .admin-main { margin-left: 200px; }
        }
        @media (max-width: 767px) {
            .admin-sidebar { display: none; }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand d-flex align-items-center gap-2">
        <i class="bi bi-award-fill" style="color:#f9ca24"></i> PATS Admin
    </a>

    <p class="nav-section mt-3">Main</p>
    <a href="{{ route('admin.dashboard') }}" class="nav-link @active('admin/dashboard')"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

    <p class="nav-section mt-2">Projects</p>
    <a href="{{ route('admin.projects.index') }}" class="nav-link @active('admin/projects*')"><i class="bi bi-folder2 me-2"></i>Projects</a>

    <p class="nav-section mt-2">Test Management</p>
    <a href="{{ route('admin.centers.index') }}" class="nav-link @active('admin/centers*')"><i class="bi bi-building me-2"></i>Test Centers</a>
    <a href="{{ route('admin.batches.index') }}" class="nav-link @active('admin/batches*')"><i class="bi bi-calendar2-week me-2"></i>Batches</a>

    <p class="nav-section mt-2">Candidates</p>
    <a href="{{ route('admin.applications.index') }}" class="nav-link @active('admin/applications*')"><i class="bi bi-file-earmark-text me-2"></i>Applications</a>
    <a href="{{ route('admin.payments.index') }}" class="nav-link @active('admin/payments*')"><i class="bi bi-cash-stack me-2"></i>Payments</a>
    <a href="{{ route('admin.rollnumbers.index') }}" class="nav-link @active('admin/roll-numbers*')"><i class="bi bi-ticket-perforated me-2"></i>Roll Numbers</a>

    <p class="nav-section mt-2">Results</p>
    <a href="{{ route('admin.results.index') }}" class="nav-link @active('admin/results*')"><i class="bi bi-bar-chart-line me-2"></i>Results</a>

    @hasrole('super_admin')
    <p class="nav-section mt-2">Super Admin</p>
    <a href="{{ route('admin.users.index') }}" class="nav-link @active('admin/users*')"><i class="bi bi-people me-2"></i>User Management</a>
    @endhasrole

    <div class="mt-4 px-3 pb-3">
        <div class="text-white-50 small">{{ auth()->user()->full_name }}</div>
        <div class="text-white-50 small mb-2">{{ auth()->user()->getRoleNames()->first() }}</div>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
        </form>
    </div>
</nav>

{{-- Main Content --}}
<div class="admin-main">
    <div class="admin-topbar">
        <h6 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? '')) }}</span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @foreach(['success','error','info','warning'] as $type)
        @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    @endforeach

    @yield('content')
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script>
    // Auto-dismiss success alerts after 4s
    setTimeout(() => {
        document.querySelectorAll('.alert-success').forEach(el => {
            new bootstrap.Alert(el).close();
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>@yield('title', 'Dashboard') - PATS</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Tabler Core & Vendor Assets -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tom-select.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pats-core.css') }}?v={{ time() }}">
    
    <!-- Theme Persistence Script (Instant Apply) -->
    <script>
        (function() {
            const theme = localStorage.getItem('pats-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
        window.setTheme = function(theme) {
            localStorage.setItem('pats-theme', theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.body.setAttribute('data-bs-theme', theme);
            
            // Sync icons manually for high-fidelity response - ensuring they show when they should
            const darkIcons = document.querySelectorAll('.hide-theme-dark');
            const lightIcons = document.querySelectorAll('.hide-theme-light');
            
            if (theme === 'dark') {
                darkIcons.forEach(el => el.style.setProperty('display', 'none', 'important'));
                lightIcons.forEach(el => el.style.setProperty('display', 'block', 'important'));
            } else {
                darkIcons.forEach(el => el.style.setProperty('display', 'block', 'important'));
                lightIcons.forEach(el => el.style.setProperty('display', 'none', 'important'));
            }
            window.dispatchEvent(new Event('theme-changed'));
        };

        // Run on initial load
        document.addEventListener('DOMContentLoaded', () => {
            setTheme(localStorage.getItem('pats-theme') || 'light');
        });
    </script>
    <style>
        :root {
            --tblr-font-sans-serif: 'InterVariable', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        /* Sidebar Enhancements */
        :root {
            --pats-nav-hover: rgba(var(--tblr-primary-rgb), 0.08);
            --pats-nav-active: rgba(var(--tblr-primary-rgb), 0.12);
        }
        [data-bs-theme="dark"] {
            --pats-nav-hover: rgba(var(--tblr-primary-rgb), 0.15);
            --pats-nav-active: rgba(var(--tblr-primary-rgb), 0.25);
        }
        .navbar-nav .nav-item .nav-link {
            transition: all 0.2s ease;
            position: relative;
        }
        .navbar-nav .nav-item .nav-link:hover {
            background: var(--pats-nav-hover);
            color: var(--tblr-primary) !important;
            border-radius: 4px;
        }
        .navbar-nav .nav-item.active > .nav-link {
            background: var(--pats-nav-active) !important;
            color: var(--tblr-primary) !important;
            font-weight: 600;
            border-radius: 4px;
        }
        .navbar-nav .nav-item.active > .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 2px;
            background: var(--tblr-primary);
            border-radius: 2px;
        }
        .nav-link-icon {
            transition: transform 0.2s ease;
        }
        .nav-link:hover .nav-link-icon {
            transform: scale(1.15);
        }

        /* Override Tabler's aggressive body-based theme toggle rules if any */
        body:not([data-bs-theme='dark']) .hide-theme-light, 
        body:not(.theme-dark) .hide-theme-light { 
            display: unset !important; 
        }
        /* CSS Driven Theme Toggle - Targeted to HTML root */
        [data-bs-theme='dark'] .hide-theme-dark { display: none !important; }
        [data-bs-theme='light'] .hide-theme-light { display: none !important; }
    </style>
</head>
<body class="layout-fluid">
    {{-- Theme script can be localized if needed, but it's small and often inlined or local already in some tabler setups --}}
    {{-- For now, let's keep it local as well if we have it --}}
    <div class="page">
        <!-- Navbar: Top Header (Logo & User Menu) -->
        <header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route('home') }}" class="text-decoration-none py-1">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="65" class="navbar-brand-image">
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex align-items-center me-3">
                        <a href="javascript:setTheme('dark')" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-moon-filled fs-2 text-azure"></i>
                        </a>
                        <a href="javascript:setTheme('light')" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="ti ti-sun-filled fs-2 text-yellow"></i>
                        </a>
                    </div>
                    <!-- Notification Center -->
                    <div class="nav-item dropdown d-none d-md-flex me-3">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                            <i class="ti ti-bell fs-2"></i>
                            <span class="badge bg-red d-none" id="notif-badge"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Notifications</h3>
                                </div>
                                <div class="list-group list-group-flush list-group-hoverable" id="notif-list">
                                    <div class="list-group-item text-center py-4 text-muted">No new alerts.</div>
                                </div>
                                <div class="card-footer text-center">
                                    <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link link-secondary btn-sm">Clear All</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0 dropdown-toggle" data-bs-toggle="dropdown" aria-label="Open user menu">
                            <span class="avatar avatar-sm" style="background-image: url('{{ auth()->user()->candidate?->photo_path ? Storage::url(auth()->user()->candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}')"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                <div class="mt-1 small text-muted">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navbar: Secondary Navigation (Links) -->
        <header class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            @role('super_admin|admin|data_entry')
                                <li class="nav-item @if(request()->routeIs('admin.dashboard')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-device-desktop fs-2"></i></span>
                                        <span class="nav-link-title">Dashboard</span>
                                    </a>
                                </li>
                                @can('manage projects')
                                <li class="nav-item @if(request()->routeIs('admin.projects.*')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.projects.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-briefcase fs-2"></i></span>
                                        <span class="nav-link-title">Projects</span>
                                    </a>
                                </li>
                                @endcan
                                @can('manage centers')
                                <li class="nav-item @if(request()->routeIs('admin.centers.*') || request()->routeIs('admin.cities.*')) active @endif dropdown">
                                    <a class="nav-link dropdown-toggle" href="#navbar-locations" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-building-community fs-2"></i></span>
                                        <span class="nav-link-title">Locations</span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item @if(request()->routeIs('admin.centers.*')) active @endif" href="{{ route('admin.centers.index') }}">Test Centers</a>
                                        <a class="dropdown-item @if(request()->routeIs('admin.cities.*')) active @endif" href="{{ route('admin.cities.index') }}">Cities Setup</a>
                                    </div>
                                </li>
                                @endcan
                                <li class="nav-item @if(request()->routeIs('admin.applications.*')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.applications.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-file-text fs-2"></i></span>
                                        <span class="nav-link-title">Applications</span>
                                    </a>
                                </li>
                                <li class="nav-item @if(request()->routeIs('admin.candidates.*')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.candidates.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-users fs-2"></i></span>
                                        <span class="nav-link-title">Candidates</span>
                                    </a>
                                </li>
                                @can('verify payments')
                                <li class="nav-item @if(request()->routeIs('admin.payments.*')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.payments.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-receipt fs-2"></i></span>
                                        <span class="nav-link-title">Payments</span>
                                    </a>
                                </li>
                                @endcan
                                @can('assign rolls')
                                <li class="nav-item dropdown @if(request()->routeIs('admin.batches.*') || request()->routeIs('admin.rollnumbers.*')) active @endif">
                                    <a class="nav-link dropdown-toggle" href="#navbar-seat" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-event fs-2"></i></span>
                                        <span class="nav-link-title">Test Sessions</span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item @if(request()->routeIs('admin.batches.*')) active @endif" href="{{ route('admin.batches.index') }}">Sessions Schedule</a>
                                        <a class="dropdown-item @if(request()->routeIs('admin.rollnumbers.*')) active @endif" href="{{ route('admin.rollnumbers.index') }}">Roll Numbers Archive</a>
                                    </div>
                                </li>
                                @endcan
                                <li class="nav-item @if(request()->routeIs('admin.results.*')) active @endif">
                                    <a class="nav-link" href="{{ route('admin.results.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-chart-bar fs-2"></i></span>
                                        <span class="nav-link-title">Results</span>
                                    </a>
                                </li>
                                @endrole

                                @hasanyrole('examiner|super_admin')
                                <li class="nav-item @if(request()->is('examiner*')) active @endif dropdown">
                                    <a class="nav-link dropdown-toggle" href="#navbar-examiner" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-clipboard-check fs-2"></i></span>
                                        <span class="nav-link-title">Examiner Portal</span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('examiner.dashboard') }}">Assigned Sessions</a>
                                    </div>
                                </li>
                                @endhasanyrole
                            
                            @role('candidate')
                                <li class="nav-item @if(request()->routeIs('candidate.dashboard')) active @endif">
                                    <a class="nav-link" href="{{ route('candidate.dashboard') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-device-desktop fs-2"></i></span>
                                        <span class="nav-link-title">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item @if(request()->routeIs('candidate.profile.*') || request()->routeIs('candidate.profile')) active @endif">
                                    <a class="nav-link" href="{{ route('candidate.profile.show') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-user fs-2"></i></span>
                                        <span class="nav-link-title">My Profile</span>
                                    </a>
                                </li>
                                <li class="nav-item @if(request()->routeIs('candidate.applications*')) active @endif">
                                    <a class="nav-link" href="{{ route('candidate.applications') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-send fs-2"></i></span>
                                        <span class="nav-link-title">My Applications</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('projects') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-search fs-2"></i></span>
                                        <span class="nav-link-title">Browse Open Jobs</span>
                                    </a>
                                </li>
                            @endrole
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <!-- Page header -->
            @if(View::hasSection('page-header') || View::hasSection('page-title') || View::hasSection('page-actions'))
                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <h2 class="page-title">
                                    @yield('page-title')
                                </h2>
                            </div>
                            <!-- Page title actions -->
                            <div class="col-auto ms-auto d-print-none">
                                @yield('page-actions')
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <img src="{{ asset('logo.png') }}" alt="PATS" height="24" class="opacity-50">
                                </li>
                                <li class="list-inline-item">
                                    <span class="text-secondary small">Precision Assessment & Testing Service</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item text-secondary small">
                                    Copyright &copy; {{ date('Y') }} PATS. All rights reserved.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Localized Core & Vendor Scripts -->
    <script src="{{ asset('assets/vendor/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
    
    <!-- Initialize Toastr & Global Components -->
    <script>
        toastr.options = { "positionClass": "toast-bottom-right", "progressBar": true };
        @if(session('success')) toastr.success(@json(session('success'))); @endif
        @if(session('error')) toastr.error(@json(session('error'))); @endif

        // Real-time Notification Poller
        function pollNotifications() {
            $.get("{{ route('admin.notifications.unread') }}", function(data) {
                const badge = $('#notif-badge');
                const list = $('#notif-list');
                
                if (data.length > 0) {
                    badge.removeClass('d-none').text(data.length);
                    list.empty();
                    data.forEach(n => {
                        list.append(`
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="status-dot status-dot-animated bg-${n.data.type || 'info'} d-block"></span></div>
                                    <div class="col text-truncate">
                                        <a href="${n.data.link || '#'}" class="text-body d-block">${n.data.message}</a>
                                        <div class="d-block text-secondary text-truncate mt-n1">${new Date(n.created_at).toLocaleString()}</div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" onclick="markRead('${n.id}')" class="list-group-item-actions"><i class="ti ti-check text-success"></i></a>
                                    </div>
                                </div>
                            </div>
                        `);
                        // Trigger Toastr if it's a very new notification (last 30s)
                        const createdAt = new Date(n.created_at);
                        const now = new Date();
                        if ((now - createdAt) < 35000) { // Slightly more than poll interval
                             if (!window.shownNotifs) window.shownNotifs = new Set();
                             if (!window.shownNotifs.has(n.id)) {
                                 toastr[n.data.type || 'info'](n.data.message);
                                 window.shownNotifs.add(n.id);
                             }
                        }
                    });
                } else {
                    badge.addClass('d-none');
                    list.html('<div class="list-group-item text-center py-4 text-muted">No new alerts.</div>');
                }
            });
        }

        function markRead(id) {
            $.post("{{ route('admin.notifications.mark-read') }}", { _token: "{{ csrf_token() }}", id: id }, function() {
                pollNotifications();
            });
        }

        @auth
            setInterval(pollNotifications, 30000); // Poll every 30s
            pollNotifications(); // Initial call
        @endauth

        // Auto-initialize all .tom-select inputs globally (if not already handled)
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.tom-select').forEach((el) => {
                if (!el.tomselect) {
                    new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" } });
                }
            });
        });
    </script>
    <script>
        // Global Confirmation Helper for high-stakes actions
        window.confirmAction = function(message, type = 'warning') {
            const colors = { 'warning': '#f59e0b', 'danger': '#ef4444', 'primary': '#3b82f6' };
            return confirm(message); // Using native confirm for now, styled ones can be added if requested
        };

        // Auto-initialize tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maskCnic = (e) => {
                let v = e.target.value;
                // If it contains letters or @, it's likely an email, don't mask
                if (/[a-zA-Z@]/.test(v)) return;
                
                let nums = v.replace(/\D/g, '');
                if (nums.length > 13) nums = nums.substring(0, 13);
                
                let out = '';
                if (nums.length > 0) out += nums.substring(0, 5);
                if (nums.length > 5) out += '-' + nums.substring(5, 12);
                if (nums.length > 12) out += '-' + nums.substring(12, 13);
                
                // Only update if changed to avoid cursor jumps
                if (out !== v && nums.length > 0) {
                    e.target.value = out;
                }
            };
            document.querySelectorAll('input[name="cnic"], .cnic-mask').forEach(i => {
                i.addEventListener('input', maskCnic);
                maskCnic({ target: i });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

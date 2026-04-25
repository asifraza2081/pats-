<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ════════════════════════════════════════════════════
         SEO: CORE TAGS
    ════════════════════════════════════════════════════ --}}
    @php
        $seoTitle       = trim(View::yieldContent('title', 'PATS')) . ' — Prime Assessment & Testing Services';
        $seoDescription = View::yieldContent('meta_description', 'PATS is Pakistan\'s leading autonomous testing and assessment agency. Browse open recruitment projects, check results, and apply for government jobs online.');
        $seoKeywords    = View::yieldContent('meta_keywords', 'PATS, Pakistan testing service, government recruitment, online jobs, assessment, test results, merit, NTS alternative');
        $seoImage       = View::yieldContent('meta_image', asset('assets/og-image.png'));
        $canonicalUrl   = url()->current();
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords"    content="{{ $seoKeywords }}">
    <meta name="author"      content="PATS — Prime Assessment & Testing Services">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="{{ $canonicalUrl }}">

    {{-- ════════════════════════════════════════════════════
         SEO: OPEN GRAPH (Facebook / WhatsApp / LinkedIn)
    ════════════════════════════════════════════════════ --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ $canonicalUrl }}">
    <meta property="og:title"       content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image"       content="{{ $seoImage }}">
    <meta property="og:site_name"   content="PATS — Prime Assessment & Testing Services">
    <meta property="og:locale"      content="en_PK">

    {{-- ════════════════════════════════════════════════════
         SEO: TWITTER CARDS
    ════════════════════════════════════════════════════ --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image"       content="{{ $seoImage }}">

    {{-- ════════════════════════════════════════════════════
         FAVICONS
    ════════════════════════════════════════════════════ --}}
    <link rel="icon"             href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="shortcut icon"    href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Tabler Core & Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/inter.css') }}">
    <!-- Core Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/pats-core.css') }}">
    <style>
        /* CSS Driven Theme Toggle - Targeted to HTML root */
        [data-bs-theme='dark'] .hide-theme-dark { display: none !important; }
        [data-bs-theme='light'] .hide-theme-light { display: none !important; }

        /* Override Tabler's aggressive body-based theme toggle rules if any */
        body:not([data-bs-theme='dark']) .hide-theme-light, 
        body:not(.theme-dark) .hide-theme-light { 
            display: unset !important; 
        }
        /* Re-apply our specific logic with higher specificity or after reset */
        html[data-bs-theme='light'] .hide-theme-light { display: none !important; }
        html[data-bs-theme='dark'] .hide-theme-dark { display: none !important; }

        /* ── Public Nav Active Indicator ─────────────────── */
        .main-nav .nav .nav-link {
            position: relative;
            transition: color 0.2s ease;
        }
        .main-nav .nav .nav-link.active,
        .main-nav .nav .nav-link:hover {
            color: var(--pats-teal, #0ca678) !important;
        }
        .main-nav .nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 12px;
            right: 12px;
            height: 2px;
            background: var(--pats-teal, #0ca678);
            border-radius: 2px;
        }
    </style>

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
            window.dispatchEvent(new Event('theme-changed'));
        };

        document.addEventListener('DOMContentLoaded', () => {
            setTheme(localStorage.getItem('pats-theme') || 'light');
        });
    </script>
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Top Info Bar -->
        <div class="top-bar d-none d-md-block glass-panel border-0 border-bottom rounded-0 py-2">
            <div class="container-xl">
                <div class="row align-items-center">
                    <div class="col-auto small opacity-75">
                        <i class="ti ti-phone me-1"></i> Helpline: (051) 111-728-7XX
                    </div>
                    <div class="col-auto ms-3 small opacity-75 border-start ps-3">
                        <i class="ti ti-mail me-1"></i> info@pats.org.pk
                    </div>
                    <div class="col text-end">
                        @guest
                            <a href="{{ route('login') }}" class="opacity-80 text-decoration-none me-3 small font-weight-bold">Login</a>
                            <a href="{{ route('auth.register') }}" class="opacity-80 text-decoration-none small font-weight-bold">Register</a>
                        @else
                            <span class="opacity-60 small me-3">Welcome back, <strong>{{ auth()->user()->full_name }}</strong></span>
                            <a href="{{ route('auth.logout') }}" class="text-danger opacity-80 text-decoration-none small font-weight-bold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">@csrf</form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <header class="main-nav sticky-top glass-panel shadow-sm border-bottom border-teal border-opacity-10" id="navbar">
            <div class="container-xl">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ url('/') }}" class="text-decoration-none hover-lift d-flex align-items-center">
                        <div class="bg-grad-accent p-2 rounded-3 me-2 shadow-teal-30 d-flex align-items-center justify-content-center">
                            <i class="ti ti-shield-check text-white fs-2"></i>
                        </div>
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="48" class="pats-logo hide-theme-dark">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="48" class="pats-logo hide-theme-light brightness-0 invert">
                    </a>
                    
                    <div class="d-none d-lg-block">
                        <ul class="nav">
                            <li class="nav-item"><a href="{{ url('/') }}" class="nav-link fw-bold px-3 {{ request()->is('/') ? 'active' : '' }} hover-lift">Home</a></li>
                            <li class="nav-item"><a href="{{ route('about') }}" class="nav-link px-3 {{ request()->routeIs('about') ? 'active' : '' }} hover-lift">About Us</a></li>
                            <li class="nav-item"><a href="{{ route('projects') }}" class="nav-link px-3 {{ request()->routeIs('projects*') ? 'active' : '' }} hover-lift">Open Projects</a></li>
                            <li class="nav-item"><a href="{{ route('results.search') }}" class="nav-link px-3 {{ request()->routeIs('results*') ? 'active' : '' }} hover-lift">Results</a></li>
                            <li class="nav-item"><a href="{{ route('downloads') }}" class="nav-link px-3 {{ request()->routeIs('downloads') ? 'active' : '' }} hover-lift">Downloads</a></li>
                            <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link px-3 {{ request()->routeIs('contact') ? 'active' : '' }} hover-lift">Contact</a></li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex gap-2 me-3 theme-toggle-wrapper">
                            <a href="javascript:setTheme('dark')" class="btn btn-icon btn-outline-secondary rounded-circle hide-theme-dark" title="Dark Mode">
                                <i class="ti ti-moon fs-2"></i>
                            </a>
                            <a href="javascript:setTheme('light')" class="btn btn-icon btn-outline-warning rounded-circle hide-theme-light" title="Light Mode">
                                <i class="ti ti-sun fs-2"></i>
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
                            
                            <!-- Notification Center -->
                            <div class="nav-item dropdown d-flex me-3">
                                <a href="#" class="nav-link px-0 text-white opacity-75 hover-opacity-100 position-relative" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                                    <i class="ti ti-bell fs-2"></i>
                                    <span class="badge bg-red d-none" id="notif-badge" style="position: absolute; top: 0; right: -5px; padding: 0.25em 0.4em; font-size: 0.6rem;"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" style="min-width: 320px;">
                                    <div class="card border-0 shadow-lg">
                                        <div class="card-header bg-light border-0 py-3">
                                            <h3 class="card-title fw-black small text-uppercase tracking-wider">Recent Alerts</h3>
                                        </div>
                                        <div class="list-group list-group-flush list-group-hoverable" id="notif-list" style="max-height: 350px; overflow-y: auto;">
                                            <div class="list-group-item" id="notif-skeleton">
                                                <div class="placeholder-glow">
                                                    <span class="placeholder col-8 mb-1 rounded"></span>
                                                    <span class="placeholder col-5 rounded"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-center bg-white border-top">
                                            <form method="POST" action="{{ route('global.notifications.mark-all-read') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-link link-secondary btn-sm fw-bold">Mark All as Read</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ $dashboardRoute }}" class="btn btn-teal text-white fw-bold shadow-sm px-4 rounded-pill">
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

        <!-- Mobile Menu (collapse target) -->
        <div class="collapse d-lg-none bg-white border-bottom shadow-sm" id="mobileMenu">
            <div class="container-xl">
                <ul class="nav flex-column py-3">
                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->is('/') ? 'text-teal' : '' }}"><i class="ti ti-home me-2"></i> Home</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->routeIs('about') ? 'text-teal' : '' }}"><i class="ti ti-info-circle me-2"></i> About Us</a></li>
                    <li class="nav-item"><a href="{{ route('projects') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->routeIs('projects*') ? 'text-teal' : '' }}"><i class="ti ti-briefcase me-2"></i> Open Projects</a></li>
                    <li class="nav-item"><a href="{{ route('results.search') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->routeIs('results*') ? 'text-teal' : '' }}"><i class="ti ti-certificate me-2"></i> Results</a></li>
                    <li class="nav-item"><a href="{{ route('downloads') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->routeIs('downloads') ? 'text-teal' : '' }}"><i class="ti ti-download me-2"></i> Downloads</a></li>
                    <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link fw-bold py-3 border-bottom {{ request()->routeIs('contact') ? 'text-teal' : '' }}"><i class="ti ti-mail me-2"></i> Contact</a></li>
                    <li class="nav-item py-3">
                        @auth
                            <a href="{{ $dashboardRoute ?? route('login') }}" class="btn btn-teal text-white fw-bold w-100"><i class="ti ti-layout-dashboard me-2"></i> Go to Dashboard</a>
                        @else
                            <div class="d-grid gap-2">
                                <a href="{{ route('login') }}" class="btn btn-primary fw-bold"><i class="ti ti-login me-2"></i> Sign In</a>
                                <a href="{{ route('auth.register') }}" class="btn btn-outline-secondary fw-bold">Register</a>
                            </div>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>

        @hasSection('header-title')
        <section class="page-header-pats">
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
        <footer class="footer-pats">
            <div class="container-xl">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <img src="{{ asset('logo.png') }}" alt="PATS" height="60" class="mb-4 brightness-0 invert opacity-90">
                        <p class="opacity-70 fs-4">PATS is Pakistan's leading autonomous testing agency, committed to merit, transparency, and building a professional workforce through Prime Assessment & Testing Services.</p>
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
    <!-- Global Script Stack -->
    <script src="{{ asset('assets/vendor/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const navbar = document.getElementById('navbar');
        window.onscroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        // Initialize Toastr
        toastr.options = { "positionClass": "toast-bottom-right", "progressBar": true };

        // Global Confirmation Helper — powered by SweetAlert2
        window.confirmAction = function(message, type = 'warning') {
            const colors = { warning: 'btn-warning', danger: 'btn-danger', primary: 'btn-primary' };
            const btnClass = colors[type] || 'btn-primary';
            return Swal.fire({
                title: 'Confirm Action',
                text: message,
                icon: type === 'danger' ? 'error' : type,
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: `btn ${btnClass} rounded-pill px-4 shadow-sm me-2`,
                    cancelButton: 'btn btn-secondary rounded-pill px-4 shadow-sm'
                },
                buttonsStyling: false
            }).then((result) => result.isConfirmed);
        };

        @auth
            // Real-time Notification Poller
            function pollNotifications() {
                $.get("{{ route('global.notifications.unread') }}", function(data) {
                    const badge = $('#notif-badge');
                    const list  = $('#notif-list');
                    $('#notif-skeleton').remove();
                    
                    if (data.length > 0) {
                        badge.removeClass('d-none').text(data.length);
                        list.empty();
                        data.forEach(n => {
                            list.append(`
                                <div class="list-group-item border-0 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot status-dot-animated bg-${n.data.type || 'info'} d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="${n.data.link || '#'}" class="text-body d-block small fw-bold">${n.data.message}</a>
                                            <div class="d-block text-secondary text-truncate mt-n1" style="font-size: 0.7rem;">${new Date(n.created_at).toLocaleString()}</div>
                                        </div>
                                    </div>
                                </div>
                            `);
                            
                            // Trigger Toastr if new notification
                            const createdAt = new Date(n.created_at);
                            const now = new Date();
                            if ((now - createdAt) < 35000) {
                                if (!window.shownNotifs) window.shownNotifs = new Set();
                                if (!window.shownNotifs.has(n.id)) {
                                    toastr[n.data.type || 'info'](n.data.message);
                                    window.shownNotifs.add(n.id);
                                }
                            }
                        });
                    } else {
                        badge.addClass('d-none');
                        list.html('<div class="list-group-item text-center py-4 text-muted small">No new notifications.</div>');
                    }
                });
            }

            setInterval(pollNotifications, 30000); // 30s
            pollNotifications(); // Initial
        @endauth

        document.addEventListener('DOMContentLoaded', function () {
            const maskCnic = (e) => {
                let v = e.target.value;
                if (/[a-zA-Z@]/.test(v)) return;
                let nums = v.replace(/\D/g, '');
                if (nums.length > 13) nums = nums.substring(0, 13);
                let out = '';
                if (nums.length > 0) out += nums.substring(0, 5);
                if (nums.length > 5) out += '-' + nums.substring(5, 12);
                if (nums.length > 12) out += '-' + nums.substring(12, 13);
                if (out !== v && nums.length > 0) e.target.value = out;
            };
            document.querySelectorAll('input[name="cnic"], .cnic-mask').forEach(i => i.addEventListener('input', maskCnic));

            // Flush Session Alerts
            @if(session('success'))
                Swal.fire({ title: 'Success!', text: @json(session('success')), icon: 'success', customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' }, buttonsStyling: false });
            @elseif(session('error'))
                Swal.fire({ title: 'Alert', text: @json(session('error')), icon: 'error', customClass: { confirmButton: 'btn btn-danger rounded-pill px-4' }, buttonsStyling: false });
            @endif
        });
    </script>
    @include('partials._global_spinner')
    @stack('scripts')
</body>
</html>

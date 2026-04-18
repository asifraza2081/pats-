<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign In') — PATS — Prime Assessment &amp; Testing Services</title>
    <meta name="description" content="Sign in or register for your PATS candidate account.">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pats-core.css') }}">
    <style>
        :root { --tblr-font-sans-serif: 'InterVariable', -apple-system, sans-serif; }
        body  { font-feature-settings: "cv03","cv04","cv11"; }

        /* ── Auth Layout ───────────────────────────────── */
        .auth-page { min-height: 100vh; display: flex; }

        /* Brand Panel — left */
        .auth-brand-panel {
            width: 45%;
            background: linear-gradient(145deg, #0d1b2a 0%, #1a3a5c 50%, #0f4c81 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 64px 56px;
            position: relative;
            overflow: hidden;
        }
        .auth-brand-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56,189,248,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.14) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel .brand-logo { height: 54px; margin-bottom: 40px; filter: brightness(0) invert(1); }
        .auth-brand-panel h1 { font-size: 2.1rem; font-weight: 900; line-height: 1.2; color: #fff; margin-bottom: 16px; letter-spacing: -0.5px; }
        .auth-brand-panel p  { color: rgba(255,255,255,0.62); font-size: 1rem; line-height: 1.65; max-width: 360px; margin-bottom: 40px; }

        .trust-badge {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 12px;
            color: rgba(255,255,255,0.9); font-size: 0.85rem; font-weight: 600;
            backdrop-filter: blur(6px);
        }
        .trust-badge i { font-size: 1.3rem; color: #38ef7d; flex-shrink: 0; }

        /* Form Panel — right */
        .auth-form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 56px 40px;
            background: var(--tblr-bg-surface, #fff);
        }
        .auth-form-inner { width: 100%; max-width: 400px; }

        /* Mobile: stack vertically */
        @media (max-width: 768px) {
            .auth-page { flex-direction: column; }
            .auth-brand-panel { width: 100%; padding: 40px 24px 32px; }
            .auth-brand-panel h1 { font-size: 1.6rem; }
            .auth-form-panel { padding: 40px 24px; }
        }
    </style>
</head>
<body>
    <div class="auth-page">
        {{-- ── Brand Panel (left) ── --}}
        <div class="auth-brand-panel">
            <a href="{{ url('/') }}">
                <img src="{{ asset('logo.png') }}" alt="PATS" class="brand-logo">
            </a>

            <h1>Pakistan's #1<br>Assessment Agency</h1>
            <p>A merit-first, transparent, and digitally-powered platform connecting qualified candidates to government recruitment cycles across Pakistan.</p>

            <div class="trust-badge"><i class="ti ti-shield-check"></i> ISO 9001 Certified Organisation</div>
            <div class="trust-badge"><i class="ti ti-users"></i> 100K+ Registered Candidates</div>
            <div class="trust-badge"><i class="ti ti-certificate"></i> 500+ Projects Completed</div>
        </div>

        {{-- ── Form Panel (right) ── --}}
        <div class="auth-form-panel">
            <div class="auth-form-inner">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <a class="btn-close" data-bs-dismiss="alert"></a>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <a class="btn-close" data-bs-dismiss="alert"></a>
                </div>
                @endif

                @yield('content')
            </div>

            <div class="mt-6 text-center text-muted small">
                <a href="{{ url('/') }}" class="text-decoration-none hover-lift me-3">
                    <i class="ti ti-home me-1"></i>Back to Homepage
                </a>
                <span class="opacity-30">|</span>
                <a href="{{ route('contact') }}" class="text-decoration-none hover-lift ms-3">Support</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
    <script>
        toastr.options = { positionClass: 'toast-bottom-right', progressBar: true };
    </script>
    @stack('scripts')
</body>
</html>

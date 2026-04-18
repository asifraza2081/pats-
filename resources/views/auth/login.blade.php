@extends('layouts.auth')
@section('title', 'Sign In — PATS')

@section('content')
{{-- ── Header ── --}}
<div class="mb-5">
    <h2 class="fw-black mb-1" style="letter-spacing: -0.5px; font-size: 1.8rem;">Welcome Back</h2>
    <p class="text-muted mb-0">Secure access to your PATS candidate portal</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-alert-circle fs-2 me-2"></i>
        <div>{{ $errors->first() }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<form action="{{ route('auth.login.post') }}" method="POST" autocomplete="off" novalidate>
    @csrf

    {{-- CNIC / Email --}}
    <div class="mb-4">
        <label class="form-label fw-semibold required">CNIC or Email</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent"><i class="ti ti-id text-muted"></i></span>
            <input type="text"
                   name="cnic"
                   id="login_identifier"
                   class="form-control @error('cnic') is-invalid @enderror"
                   placeholder="XXXXX-XXXXXXX-X or email address"
                   value="{{ old('cnic') }}"
                   autocomplete="off"
                   required>
        </div>
        <div class="form-hint">Enter your registered CNIC (with dashes) or email</div>
        @error('cnic')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <label class="form-label fw-semibold required">
            Password
            <span class="form-label-description">
                <a href="{{ route('auth.forgot-password') }}" tabindex="-1" class="text-muted text-decoration-none small">Forgot password?</a>
            </span>
        </label>
        <div class="input-group input-group-flat">
            <span class="input-group-text bg-transparent"><i class="ti ti-lock text-muted"></i></span>
            <input type="password" name="password" class="form-control border-start-0" placeholder="Your password" required>
            <span class="input-group-text bg-transparent">
                <a href="#" class="link-secondary toggle-password" title="Show/hide password">
                    <i class="ti ti-eye"></i>
                </a>
            </span>
        </div>
    </div>

    {{-- Remember Me --}}
    <div class="mb-5">
        <label class="form-check cursor-pointer">
            <input type="checkbox" name="remember" class="form-check-input"/>
            <span class="form-check-label text-muted">Keep me signed in on this device</span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 fs-5 rounded-pill shadow-sm">
        <i class="ti ti-login me-2"></i>Sign In Securely
    </button>
</form>

<div class="mt-4 pt-3 border-top text-center text-muted small">
    Don't have an account?
    <a href="{{ route('auth.register') }}" class="fw-bold text-primary ms-1">Register as Candidate</a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
            const icon  = this.querySelector('i');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            icon.classList.toggle('ti-eye', !isPass);
            icon.classList.toggle('ti-eye-off', isPass);
        });
    });

    // CNIC auto-masking (only when starts with digit)
    const loginInput = document.getElementById('login_identifier');
    if (loginInput) {
        loginInput.addEventListener('input', function(e) {
            let val = e.target.value;
            if (/^[a-zA-Z@]/.test(val)) return; // email — don't mask
            let x = val.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            if (x && x[1]) {
                e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
            }
        });
    }
});
</script>
@endpush

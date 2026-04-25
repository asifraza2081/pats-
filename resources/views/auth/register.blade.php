@extends('layouts.auth')
@section('title', 'Join PATS — Candidate Registration')

@section('content')
{{-- ── Header ── --}}
<div class="mb-5">
    <h2 class="fw-black mb-1" style="letter-spacing: -0.5px; font-size: 1.8rem;">Create Account</h2>
    <p class="text-muted mb-0">Join Pakistan's premier assessment portal</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-alert-circle fs-2 me-2"></i>
        <div class="small">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert"></a>
</div>
@endif

<form action="{{ route('auth.register') }}" method="POST" autocomplete="off" novalidate>
    @csrf
    
    <div class="row g-3">
        {{-- Names --}}
        <div class="col-6">
            <label class="form-label fw-semibold required">First Name</label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="John" required>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold required">Last Name</label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Doe" required>
        </div>

        {{-- CNIC (Optional logic maintained) --}}
        <div class="col-12">
            <label class="form-label fw-semibold">CNIC Number <span class="text-muted small fw-normal">(Optional)</span></label>
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="ti ti-id text-muted"></i></span>
                <input type="text" name="cnic" id="reg_cnic" class="form-control @error('cnic') is-invalid @enderror" placeholder="XXXXX-XXXXXXX-X" value="{{ old('cnic') }}" autocomplete="off">
            </div>
            <div class="form-hint">Format: 35201-1234567-1</div>
        </div>

        {{-- Mobile --}}
        <div class="col-12">
            <label class="form-label fw-semibold required">Mobile Number</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="ti ti-phone text-muted"></i></span>
                <input type="tel" name="phone" id="reg_phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0300-1234567" value="{{ old('phone') }}" required>
            </div>
        </div>

        {{-- Email --}}
        <div class="col-12">
            <label class="form-label fw-semibold required">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="ti ti-mail text-muted"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
            </div>
        </div>

        {{-- CAPTCHA --}}
        <div class="col-12">
            <div class="bg-light p-3 rounded-3 border">
                <label class="form-label fw-semibold mb-2">Security Verification</label>
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        <div id="captcha-img-wrapper" class="cursor-pointer" title="Refresh CAPTCHA">
                            <img src="{{ captcha_src('flat') }}" alt="captcha" class="img-fluid rounded border bg-white" style="height: 40px;">
                        </div>
                    </div>
                    <div class="col-auto">
                         <button type="button" class="btn btn-icon btn-ghost-primary rounded-circle" id="refresh-captcha">
                            <i class="ti ti-refresh fs-2"></i>
                        </button>
                    </div>
                    <div class="col">
                        <input type="text" name="captcha" class="form-control" placeholder="Characters" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="col-12">
            <label class="form-label fw-semibold required">Password</label>
            <div class="input-group input-group-flat">
                <span class="input-group-text bg-transparent"><i class="ti ti-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="Min 8 characters" autocomplete="new-password" required>
                <span class="input-group-text bg-transparent">
                    <a href="#" class="link-secondary toggle-password" title="Show password">
                        <i class="ti ti-eye"></i>
                    </a>
                </span>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="col-12">
            <label class="form-label fw-semibold required">Confirm Password</label>
            <div class="input-group input-group-flat">
                <span class="input-group-text bg-transparent"><i class="ti ti-lock-check text-muted"></i></span>
                <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Repeat password" required>
            </div>
        </div>

        <div class="col-12 mt-4">
            <label class="form-check cursor-pointer">
                <input type="checkbox" class="form-check-input" required/>
                <span class="form-check-label text-muted small">I agree to the <a href="#" class="text-primary">terms and policy</a> of PATS.</span>
            </label>
        </div>
    </div>

    <div class="mt-5">
        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 fs-5 rounded-pill shadow-sm">
            <i class="ti ti-user-plus me-2"></i>Create My Account
        </button>
    </div>
</form>

<div class="mt-4 pt-3 border-top text-center text-muted small">
    Already joined?
    <a href="{{ route('login') }}" class="fw-bold text-primary ms-1">Sign In Securely</a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Password toggle
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.closest('.input-group').querySelector('input');
            const icon  = this.querySelector('i');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            icon.classList.toggle('ti-eye', !isPass);
            icon.classList.toggle('ti-eye-off', isPass);
        });
    });

    // 2. Input Masking
    const cnicInput = document.getElementById('reg_cnic');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }

    const phoneInput = document.getElementById('reg_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });
    }

    // 3. CAPTCHA Refresh
    const refreshBtn = document.getElementById('refresh-captcha');
    const refreshWrap = document.getElementById('captcha-img-wrapper');
    const doRefresh = () => {
        const img = document.querySelector('#captcha-img-wrapper img');
        if (img) {
            fetch('/captcha/api/flat')
                .then(r => r.json())
                .then(data => { img.src = data.img; })
                .catch(() => { img.src = '{{ captcha_src("flat") }}' + Math.random(); });
        }
    };
    if (refreshBtn) refreshBtn.addEventListener('click', doRefresh);
    if (refreshWrap) refreshWrap.addEventListener('click', doRefresh);
});
</script>
@endpush

@extends('layouts.public')
@section('title', 'Candidate Registration — PATS')

@section('content')
<div class="page page-center h-100">
    <div class="container container-tight py-4" style="max-width: 550px;">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset('logo.png') }}" alt="PATS Logo" height="80" class="mb-2">
            </a>
        </div>
        
        <div class="card card-md shadow-sm border-0 rounded-3">
            <div class="card-body py-5 px-sm-5">
                <h2 class="h2 text-center mb-1 fw-bold text-pats-primary">Create New Account</h2>
                <p class="text-muted text-center small mb-4">Register your profile to apply for jobs</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                <form action="{{ route('auth.register') }}" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required text-pats-primary fw-bold">First Name</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="Enter first name" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required text-pats-primary fw-bold">Last Name</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Enter last name" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">CNIC Number <span class="text-muted small fw-normal">(Optional)</span></label>
                            <input type="text" name="cnic" id="cnic_mask" class="form-control @error('cnic') is-invalid @enderror" placeholder="XXXXX-XXXXXXX-X" value="{{ old('cnic') }}" autocomplete="off">
                            <div class="form-hint">Format: 35201-1234567-1</div>
                            @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Mobile Number</label>
                            <input type="tel" name="phone" id="phone_mask" class="form-control @error('phone') is-invalid @enderror" placeholder="0300-1234567" value="{{ old('phone') }}" required>
                            <div class="form-hint">Format: 03XX-XXXXXXX</div>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label required">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <div class="bg-light p-3 rounded-3 border">
                                <label class="form-label required mb-2">Security Verification (CAPTCHA)</label>
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="captcha-container d-flex align-items-center gap-2">
                                            <div id="captcha-img-wrapper">
                                                {!! captcha_img('flat') !!}
                                            </div>
                                            <button type="button" class="btn btn-icon btn-ghost-primary rounded-circle" id="refresh-captcha" title="Refresh CAPTCHA">
                                                <i class="ti ti-refresh fs-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <input type="text" name="captcha" class="form-control @error('captcha') is-invalid @enderror" placeholder="Enter characters above" required>
                                        @error('captcha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters" autocomplete="new-password" required>
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary toggle-password" title="Show password" data-bs-toggle="tooltip">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </span>
                            </div>
                            @error('password')<div class="invalid-feedback text-danger d-block mt-1">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" autocomplete="new-password" required>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-check cursor-pointer">
                                <input type="checkbox" class="form-check-input" required/>
                                <span class="form-check-label small text-muted">I agree to the <a href="#" tabindex="-1">terms and policy</a> of PATS Services.</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold fs-3 py-2 shadow-sm rounded-pill"><i class="ti ti-user-plus me-2"></i> Create Account</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center text-muted mt-3">
            Already have an account? <a href="{{ route('login') }}" tabindex="-1" class="fw-bold">Sign In here</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Password Toggle
    const toggleBtns = document.querySelectorAll('.toggle-password');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        });
    });

    // 2. Input Masking (Simple)
    const cnicInput = document.getElementById('cnic_mask');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }

    const phoneInput = document.getElementById('phone_mask');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });
    }

    // 3. CAPTCHA Refresh
    const refreshBtn = document.getElementById('refresh-captcha');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const img = document.querySelector('#captcha-img-wrapper img');
            if (img) {
                // Append timestamp to prevent caching
                img.src = '/captcha/flat?' + Math.random();
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    body.layout-fluid .page-body { margin-top: 0 !important; }
    .page-center { min-height: 80vh; display: flex; flex-direction: column; justify-content: center; }
</style>
@endpush
@endsection

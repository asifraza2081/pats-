@extends('layouts.public')
@section('title', 'Sign In — PATS')

@section('content')
<div class="page page-center h-100">
    <div class="container container-tight py-4" style="max-width: 400px;">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset('logo.png') }}" alt="PATS Logo" height="100" class="mb-2">
            </a>
        </div>
        
        <div class="card card-md shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body py-5 px-4 px-md-5">
                <h2 class="h1 text-center mb-1 fw-bold text-pats-primary" style="letter-spacing: -1px;">Welcome Back</h2>
                <p class="text-muted text-center small mb-4">Secure access to your PATS portal</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                <form action="{{ route('auth.login.post') }}" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">CNIC or Email</label>
                        <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror" placeholder="CNIC (XXXXX-XXXXXXX-X) or Email" value="{{ old('cnic') }}" autocomplete="off" required>
                        <div class="form-hint">Enter your registered CNIC (with dashes) or Email address</div>
                        @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">
                            Password
                            <span class="form-label-description">
                                <a href="{{ route('auth.forgot-password') }}" tabindex="-1">Forgot Password?</a>
                            </span>
                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" name="password" class="form-control" placeholder="Your password" autocomplete="off" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input type="checkbox" name="remember" class="form-check-input"/>
                            <span class="form-check-label">Remember me on this device</span>
                        </label>
                    </div>
                    
                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-pats w-100 fw-bold fs-3 py-2">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center text-muted mt-3">
            Don't have an account yet? <a href="{{ route('auth.register') }}" tabindex="-1" class="fw-bold">Register as Candidate</a>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Full height overrides for auth pages since main app wrapper has margin */
    body.layout-fluid .page-body { margin-top: 0 !important; }
    .page-center { min-height: 70vh; display: flex; flex-direction: column; justify-content: center; }
</style>
@endpush
@endsection

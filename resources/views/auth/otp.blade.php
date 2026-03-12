@extends('layouts.app')
@section('title', 'Verify OTP — PATS')

@section('content')
<div class="page page-center h-100">
    <div class="container container-tight py-5">
        <div class="text-center mb-4 mt-5">
            <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                <i class="ti ti-award text-pats-gold fs-1 me-2" style="color: #f9ca24;"></i>
                <span class="fs-1 fw-bold text-pats-primary" style="color: #0a3d62;">PATS</span>
            </a>
        </div>
        
        <div class="card card-md shadow-sm border-0 rounded-3">
            <div class="card-body py-5 text-center">
                <i class="ti ti-shield-lock text-success fs-1 mb-3 d-block" style="font-size: 3rem !important;"></i>
                <h2 class="h2 text-center mb-1 fw-bold">OTP Verification</h2>
                <p class="text-muted text-center small mb-4">Enter the 6-digit secure code sent to your registered mobile number.</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('auth.otp') }}" autocomplete="off">
                    @csrf
                    <div class="mb-4">
                        <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold tracking-wide fs-2 @error('otp') is-invalid @enderror" maxlength="6" placeholder="000000" autofocus inputmode="numeric" pattern="\d{6}" autocomplete="one-time-code" required>
                    </div>
                    <div class="form-footer mt-2">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2"><i class="ti ti-check me-2"></i> Verify Account</button>
                    </div>
                </form>

                <div class="mt-4">
                    <form method="POST" action="{{ route('auth.otp.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link link-secondary text-decoration-none small">
                            <i class="ti ti-refresh me-1"></i> Resend Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body.layout-fluid .page-body { margin-top: 0 !important; }
    .page-center { min-height: 70vh; display: flex; flex-direction: column; justify-content: center; }
</style>
@endpush
@endsection

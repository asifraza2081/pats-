@extends('layouts.app')
@section('title', 'Verify OTP — PATS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body p-5">
                    <i class="bi bi-shield-lock fs-1 mb-3" style="color:var(--pats-primary)"></i>
                    <h5 class="fw-bold">OTP Verification</h5>
                    <p class="text-muted small">Enter the 6-digit code sent to your mobile number.</p>

                    @if($errors->any())
                    <div class="alert alert-danger small">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('auth.otp') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold @error('otp') is-invalid @enderror"
                                   maxlength="6" placeholder="000000" autofocus inputmode="numeric" pattern="\d{6}" required>
                            @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-pats w-100 fw-semibold mb-3">Verify OTP</button>
                    </form>

                    <form method="POST" action="{{ route('auth.otp.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none">
                            <i class="bi bi-arrow-repeat me-1"></i> Resend OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

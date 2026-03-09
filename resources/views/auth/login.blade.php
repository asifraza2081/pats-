@extends('layouts.app')
@section('title', 'Login — PATS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle fs-1" style="color:var(--pats-primary)"></i>
                        <h4 class="fw-bold mt-2">Sign In to PATS</h4>
                        <p class="text-muted small">Enter your CNIC and password</p>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger small">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('auth.login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">CNIC <span class="text-danger">*</span></label>
                            <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                   placeholder="1234567890123" maxlength="13" value="{{ old('cnic') }}" required>
                            <div class="form-text">13 digits without dashes</div>
                            @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('auth.forgot-password') }}" class="small text-decoration-none">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-pats w-100 fw-semibold">Sign In</button>
                    </form>

                    <div class="text-center mt-4 small">
                        Don't have an account? <a href="{{ route('auth.register') }}" class="fw-semibold">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

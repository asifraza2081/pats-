@extends('layouts.public')
@section('title', 'Forgot Password — PATS')

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
            <div class="card-body py-5 px-sm-5">
                <i class="ti ti-key text-muted fs-1 mb-3 d-block text-center" style="font-size: 3rem !important;"></i>
                <h2 class="h2 text-center mb-1 fw-bold">Forgot password?</h2>
                <p class="text-muted text-center small mb-4">Enter your Email or CNIC and we'll send you an OTP to reset your password.</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                <form action="{{ route('auth.forgot-password') }}" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Email or CNIC</label>
                        <input type="text" name="identifier" class="form-control" placeholder="user@example.com or 1234567890123" required>
                        <div class="form-hint">Enter your registered email address or 13-digit CNIC.</div>
                    </div>
                    
                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="ti ti-mail me-2"></i> Send Reset OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center text-muted mt-3">
            Forget it, <a href="{{ route('login') }}" tabindex="-1" class="fw-bold">send me back</a> to the sign in screen.
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

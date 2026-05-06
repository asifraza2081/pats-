@extends('layouts.auth')
@section('title', 'Forgot Password — PATS')

@section('content')
<div class="text-center mb-4">
    <i class="ti ti-key text-muted fs-1 mb-3 d-block" style="font-size: 3rem !important;"></i>
    <h2 class="h2 mb-1 fw-bold">Forgot password?</h2>
    <p class="text-muted small mb-4">Enter your Email or CNIC and we'll send you an OTP to reset your password.</p>
</div>

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
        <div class="input-group input-group-flat border border-dark border-opacity-10 rounded-pill overflow-hidden bg-light px-2 py-1">
            <span class="input-group-text bg-transparent border-0 pe-2">
                <i class="ti ti-user text-pats-primary fs-3"></i>
            </span>
            <input type="text" name="identifier" class="form-control border-0 bg-transparent shadow-none" placeholder="user@example.com or 1234567890123" maxlength="15" required>
        </div>
        <div class="form-hint px-3 mt-2">Enter your registered email address or 13-digit CNIC.</div>
    </div>
    
    <div class="form-footer mt-5">
        <button type="submit" class="btn btn-teal w-100 rounded-pill py-3 fw-bold fs-3 text-uppercase tracking-widest shadow-teal-30">
            <i class="ti ti-mail me-2"></i> Send Reset OTP
        </button>
    </div>
</form>

<div class="text-center text-muted mt-5">
    Forget it, <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none border-bottom border-dark border-opacity-20 pb-1 hover-border-opacity-100 transition-all">send me back</a> to the sign in screen.
</div>
@endsection

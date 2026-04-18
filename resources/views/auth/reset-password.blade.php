@extends('layouts.auth')
@section('title', 'Reset Password — PATS')

@section('content')
<div class="text-center mb-4">
    <i class="ti ti-lock-check text-green fs-1 d-block mx-auto mb-3" style="font-size: 3rem !important;"></i>
    <h2 class="h2 mb-1 fw-black text-dark">Set New Password</h2>
    <p class="text-muted small mb-4">You have successfully verified your identity. Please create a new strong password.</p>
</div>

@if($errors->any())
<div class="alert alert-important alert-danger alert-dismissible rounded-4" role="alert">
    <div class="d-flex">
        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
        <div>{{ $errors->first() }}</div>
    </div>
</div>
@endif

<form method="POST" action="{{ route('auth.reset-password') }}" autocomplete="off">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    
    <div class="mb-3">
        <label class="form-label required small tracking-widest text-dark opacity-60">New Password</label>
        <div class="input-group input-group-flat border border-dark border-opacity-10 rounded-pill overflow-hidden bg-light px-2 py-1">
            <span class="input-group-text bg-transparent border-0 pe-2">
                <i class="ti ti-lock text-pats-primary fs-3"></i>
            </span>
            <input type="password" name="password" class="form-control border-0 bg-transparent shadow-none" placeholder="Min 8 characters" minlength="8" required>
        </div>
    </div>
    
    <div class="mb-4">
        <label class="form-label required small tracking-widest text-dark opacity-60">Confirm New Password</label>
        <div class="input-group input-group-flat border border-dark border-opacity-10 rounded-pill overflow-hidden bg-light px-2 py-1">
            <span class="input-group-text bg-transparent border-0 pe-2">
                <i class="ti ti-lock-check text-pats-primary fs-3"></i>
            </span>
            <input type="password" name="password_confirmation" class="form-control border-0 bg-transparent shadow-none" placeholder="Type password again" required>
        </div>
    </div>
    
    <div class="form-footer mt-5">
        <button type="submit" class="btn btn-green w-100 rounded-pill py-3 fw-bold fs-3 text-uppercase tracking-widest shadow-sm">
            <i class="ti ti-device-floppy me-2"></i> Save & Login
        </button>
    </div>
</form>
@endsection

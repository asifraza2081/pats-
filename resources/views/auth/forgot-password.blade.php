@extends('layouts.app')
@section('title', 'Forgot Password — PATS')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-key-fill fs-1 mb-2" style="color:var(--pats-primary)"></i>
                    <h5 class="fw-bold">Forgot Password</h5>
                    <p class="text-muted small">Enter your CNIC to receive a reset OTP.</p>
                    @if($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="{{ route('auth.forgot-password') }}">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold">CNIC</label>
                            <input type="text" name="cnic" class="form-control" placeholder="13 digits" maxlength="13" required>
                        </div>
                        <button type="submit" class="btn btn-pats w-100">Send OTP</button>
                    </form>
                    <div class="mt-3 small"><a href="{{ route('login') }}">Back to Login</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

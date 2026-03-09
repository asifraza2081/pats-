@extends('layouts.app')
@section('title', 'Reset Password — PATS')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <h5 class="fw-bold text-center mb-4">Set New Password</h5>
                    @if($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="{{ route('auth.reset-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-pats w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

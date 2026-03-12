@extends('layouts.public')
@section('title', 'Reset Password — PATS')

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
            <div class="card-body py-5 px-sm-5 text-center">
                <i class="ti ti-lock-check text-success fs-1 mb-3 d-block" style="font-size: 3rem !important;"></i>
                <h2 class="h2 text-center mb-1 fw-bold">Set New Password</h2>
                <p class="text-muted text-center small mb-4">You have successfully verified your identity. Please create a new strong password.</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>{{ $errors->first() }}</div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('auth.reset-password') }}" class="text-start" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 8 characters" minlength="8" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label required">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Type password again" required>
                    </div>
                    
                    <div class="form-footer mt-2">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2"><i class="ti ti-device-floppy me-2"></i> Save & Login</button>
                    </div>
                </form>
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

@extends('layouts.auth')
@section('title', 'Access Denied')

@section('content')
<div class="text-center py-5">
    <div class="mb-5">
        <span class="avatar avatar-xl rounded-circle bg-dark-lt shadow-sm ring ring-dark ring-opacity-10" style="width: 120px; height: 120px;">
            <i class="ti ti-lock-access text-dark" style="font-size: 4rem;"></i>
        </span>
    </div>
    
    <h1 class="display-3 fw-black text-dark mb-2">403</h1>
    <h2 class="h2 fw-bold text-muted mb-4">Unauthorized Access</h2>
    
    <p class="text-muted fs-3 mb-5 mx-auto" style="max-width: 400px;">
        Sorry, you do not have permission to access this area. If you believe this is an error, please contact your administrator.
    </p>

    <div class="d-flex flex-column gap-3 max-w-sm mx-auto">
        @auth
            <a href="{{ auth()->user()->hasRole('candidate') ? route('candidate.dashboard') : route('admin.dashboard') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3 fw-bold">
                <i class="ti ti-layout-dashboard me-2"></i> Go to My Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3 fw-bold">
                <i class="ti ti-login me-2"></i> Sign In to Account
            </a>
        @endauth
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg rounded-pill py-3 fw-bold">
            <i class="ti ti-home me-2"></i> Homepage
        </a>
    </div>
</div>
@endsection

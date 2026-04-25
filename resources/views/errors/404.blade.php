@extends('layouts.auth')
@section('title', 'Page Not Found')

@section('content')
<div class="text-center py-5">
    <div class="mb-5 animate__animated animate__bounceIn">
        <span class="avatar avatar-xl rounded-circle bg-red-lt shadow-sm ring ring-red ring-opacity-10" style="width: 120px; height: 120px;">
            <i class="ti ti-map-question text-red" style="font-size: 4rem;"></i>
        </span>
    </div>
    
    <h1 class="display-3 fw-black text-dark mb-2">404</h1>
    <h2 class="h2 fw-bold text-muted mb-4">Under Construction or Lost?</h2>
    
    <p class="text-muted fs-3 mb-5 mx-auto" style="max-width: 400px;">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>

    <div class="d-flex flex-column gap-3 max-w-sm mx-auto">
        <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3 fw-bold">
            <i class="ti ti-home me-2"></i> Return to Homepage
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg rounded-pill py-3 fw-bold">
            <i class="ti ti-arrow-left me-2"></i> Go Back
        </a>
    </div>
</div>
@endsection

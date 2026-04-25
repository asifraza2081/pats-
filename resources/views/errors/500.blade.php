@extends('layouts.auth')
@section('title', 'System Error')

@section('content')
<div class="text-center py-5">
    <div class="mb-5">
        <span class="avatar avatar-xl rounded-circle bg-orange-lt shadow-sm ring ring-orange ring-opacity-10" style="width: 120px; height: 120px;">
            <i class="ti ti-settings-automation text-orange animate__animated animate__spin animate__infinite animate__slower" style="font-size: 4rem;"></i>
        </span>
    </div>
    
    <h1 class="display-3 fw-black text-dark mb-2">500</h1>
    <h2 class="h2 fw-bold text-muted mb-4">Internal Server Error</h2>
    
    <p class="text-muted fs-3 mb-5 mx-auto" style="max-width: 400px;">
        Something went wrong on our end. We've been notified and are working to fix it. Please try again in a few minutes.
    </p>

    <div class="d-flex flex-column gap-3 max-w-sm mx-auto">
        <a href="{{ url()->current() }}" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3 fw-bold">
            <i class="ti ti-refresh me-2"></i> Try Refreshing
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg rounded-pill py-3 fw-bold">
            <i class="ti ti-home me-2"></i> Back to Safety
        </a>
    </div>
</div>
@endsection

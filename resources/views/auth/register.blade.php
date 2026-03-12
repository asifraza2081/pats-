@extends('layouts.app')
@section('title', 'Register — PATS')

@section('content')
<div class="page page-center h-100 bg-white">
    <div class="container container-tight py-4" style="max-width: 550px;">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset('logo.png') }}" alt="PATS Logo" height="80" class="mb-2">
            </a>
        </div>
        
        <div class="card card-md shadow-sm border-0 rounded-3">
            <div class="card-body py-5 px-sm-5">
                <h2 class="h2 text-center mb-1 fw-bold text-pats-primary">Create New Account</h2>
                <p class="text-muted text-center small mb-4">Register your profile to apply for jobs</p>

                @if($errors->any())
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                <form action="{{ route('auth.register') }}" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">First Name</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Last Name</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">CNIC Number</label>
                            <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror" placeholder="13 digits without dashes" maxlength="13" value="{{ old('cnic') }}" autocomplete="off" required>
                            @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Mobile Number</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="03001234567" value="{{ old('phone') }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label required">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label required">Nationality</label>
                            <select name="nationality" class="form-select @error('nationality') is-invalid @enderror" required>
                                <option value="Pakistani" {{ old('nationality', 'Pakistani') === 'Pakistani' ? 'selected' : '' }}>Pakistani</option>
                                <option value="Foreigner" {{ old('nationality') === 'Foreigner' ? 'selected' : '' }}>Foreigner</option>
                            </select>
                            @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters" autocomplete="new-password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" required/>
                                <span class="form-check-label small text-muted">Agree to the <a href="#" tabindex="-1">terms and policy</a>.</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold fs-3 py-2"><i class="ti ti-mail-forward me-2"></i> Register & Get OTP</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center text-muted mt-3">
            Already have an account? <a href="{{ route('login') }}" tabindex="-1" class="fw-bold">Sign In here</a>
        </div>
    </div>
</div>

@push('styles')
<style>
    body.layout-fluid .page-body { margin-top: 0 !important; }
    .page-center { min-height: 80vh; display: flex; flex-direction: column; justify-content: center; }
</style>
@endpush
@endsection

@extends('layouts.app')
@section('title', 'Register — PATS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill fs-1" style="color:var(--pats-primary)"></i>
                        <h4 class="fw-bold mt-2">Create Account</h4>
                        <p class="text-muted small">Fill in your details to register for PATS</p>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('auth.register') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">CNIC <span class="text-danger">*</span></label>
                                <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                       placeholder="13 digits, no dashes" maxlength="13" value="{{ old('cnic') }}" required>
                                @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="03001234567" value="{{ old('phone') }}" required>
                                <div class="form-text">OTP will be sent to this number</div>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nationality <span class="text-danger">*</span></label>
                                <select name="nationality" class="form-select @error('nationality') is-invalid @enderror" required>
                                    <option value="Pakistani" {{ old('nationality','Pakistani')==='Pakistani'?'selected':'' }}>Pakistani</option>
                                    <option value="Foreigner"  {{ old('nationality')==='Foreigner'?'selected':'' }}>Foreigner</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       minlength="8" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pats w-100 fw-semibold">
                                    <i class="bi bi-send me-1"></i> Register & Get OTP
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-4 small">
                        Already registered? <a href="{{ route('auth.login') }}" class="fw-semibold">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

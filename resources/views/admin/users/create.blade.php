@extends('layouts.admin')
@section('title', 'Add Admin User')
@section('page-title', 'Add Admin User')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">CNIC (13 digits) *</label><input type="text" name="cnic" class="form-control" value="{{ old('cnic') }}" maxlength="13" minlength="13" pattern="\d{13}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Phone *</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role *</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role')===$role->name?'selected':'' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Password *</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

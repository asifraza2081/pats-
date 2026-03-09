@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.users.update',$user) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name',$user->first_name) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name',$user->last_name) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">CNIC *</label><input type="text" name="cnic" class="form-control" value="{{ old('cnic',$user->cnic) }}" maxlength="13" minlength="13" pattern="\d{13}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Phone *</label><input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}"></div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role *</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role',$user->getRoleNames()->first())===$role->name?'selected':'' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">New Password <span class="text-muted small">(leave blank to keep)</span></label><input type="password" name="password" class="form-control" minlength="8"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
            <div class="col-12 d-flex align-items-center gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">Account Active</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Save Changes</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

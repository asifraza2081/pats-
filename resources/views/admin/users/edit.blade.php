@extends('layouts.dashboard')
@section('title', 'Edit User')
@section('page-title', 'Edit Administrator')

@section('page-actions')
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Directory
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card shadow-sm border-0">
            @csrf @method('PUT')
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Update Administrator: {{ $user->full_name }}</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label required">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">CNIC Number</label>
                        <input type="text" name="cnic" class="form-control" value="{{ old('cnic', $user->cnic) }}" maxlength="13" minlength="13" pattern="\d{13}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">System Role</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) === $role->name ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">New Password <span class="text-secondary small">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label fw-bold">Account Active Status</span>
                        </label>
                        <div class="text-secondary small">Disabled users cannot log in to the administrative panel.</div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> Save Account Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

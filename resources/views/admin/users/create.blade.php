@extends('layouts.dashboard')
@section('title', 'Add Admin User')
@section('page-title', 'Create Administrator')

@section('page-actions')
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Directory
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="card shadow-sm border-0">
            @csrf
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">New Administrator Account</h3>
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
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="e.g. Ahmad" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="e.g. Khan" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">CNIC Number <span class="text-muted small fw-normal">(Optional)</span></label>
                        <input type="text" name="cnic" class="form-control" value="{{ old('cnic') }}" maxlength="15" placeholder="XXXXX-XXXXXXX-X">
                        <div class="form-hint">Used as login identifier along with Email.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 03001234567" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@pats.gov.pk">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">System Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">— Select Authorization Level —</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role')===$role->name?'selected':'' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" name="password" class="form-control" autocomplete="off" required minlength="8">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Confirm Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="off" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check me-2"></i> Create Account</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Add Test Center')
@section('page-title', 'Create Test Center')

@section('page-actions')
<a href="{{ route('admin.centers.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Directory
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.centers.store') }}" class="card shadow-sm border-0">
            @csrf
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">New Test Center Profile</h3>
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
                    <div class="col-md-4">
                        <label class="form-label required">TCID (Max 4 Letters)</label>
                        <input type="text" name="tcid" class="form-control text-uppercase" maxlength="4" value="{{ old('tcid') }}" placeholder="e.g. LHR1" required>
                        <div class="form-hint">Unique identifier for roll numbers.</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label required">Center Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. NUML University, Main Building" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">City Location</label>
                        <select name="city_id" class="form-select tom-select @error('city_id') is-invalid @enderror" required>
                            <option value="">— Select Target City —</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Total Seating Capacity</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-users"></i></span>
                            <input type="number" name="seating_capacity" class="form-control" value="{{ old('seating_capacity') }}" min="1" placeholder="e.g. 500" required>
                        </div>
                        <div class="form-hint">Max seats available per single shift.</div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label required">Full Physical Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Provide complete address for roll number slips..." required>{{ old('address') }}</textarea>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Google Maps URL (Optional)</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                            <input type="url" name="map_url" class="form-control" value="{{ old('map_url') }}" placeholder="https://maps.google.com/…">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.centers.index') }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check me-2"></i> Register Center</button>
            </div>
        </form>
    </div>
</div>
@endsection

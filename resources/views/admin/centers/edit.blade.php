@extends('layouts.dashboard')
@section('title', 'Edit Center')
@section('page-title', 'Edit Test Center')

@section('page-actions')
<a href="{{ route('admin.centers.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Directory
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.centers.update', $center) }}" class="card shadow-sm border-0">
            @csrf @method('PUT')
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Update Center Profile: {{ $center->tcid }}</h3>
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
                        <label class="form-label required">TCID (3 Letters)</label>
                        <input type="text" name="tcid" class="form-control text-uppercase" maxlength="3" value="{{ old('tcid', $center->tcid) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label required">Center Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $center->name) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">City Location</label>
                        <select name="city_id" class="form-select tom-select" required>
                            <option value="">— Select Target City —</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $center->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }} ({{ $city->province }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Total Seating Capacity</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-users"></i></span>
                            <input type="number" name="seating_capacity" class="form-control" value="{{ old('seating_capacity', $center->seating_capacity) }}" min="1" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $center->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label fw-bold">Active Status</span>
                        </label>
                        <div class="text-secondary small">Inactive centers will be hidden from new batch allocations.</div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label required">Full Physical Address</label>
                        <textarea name="address" class="form-control" rows="3" required>{{ old('address', $center->address) }}</textarea>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Google Maps URL (Optional)</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-map-pin"></i></span>
                            <input type="url" name="map_url" class="form-control" value="{{ old('map_url', $center->map_url) }}" placeholder="https://maps.google.com/…">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.centers.index') }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

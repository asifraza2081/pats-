@extends('layouts.dashboard')
@section('title', 'Add City')
@section('page-title', 'Create New City')

@section('page-actions')
<a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Cities
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="{{ route('admin.cities.store') }}" method="POST" class="card shadow-sm border-0">
            @csrf
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">City Information</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label required">City Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Islamabad" required>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Province</label>
                    <select name="province" class="form-select tom-select @error('province') is-invalid @enderror" required>
                        <option value="">Select province...</option>
                        <option value="Punjab" {{ old('province') == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                        <option value="Sindh" {{ old('province') == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                        <option value="KPK" {{ old('province') == 'KPK' ? 'selected' : '' }}>KPK</option>
                        <option value="Balochistan" {{ old('province') == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                        <option value="Gilgit Baltistan" {{ old('province') == 'Gilgit Baltistan' ? 'selected' : '' }}>Gilgit Baltistan</option>
                        <option value="AJK" {{ old('province') == 'AJK' ? 'selected' : '' }}>AJK</option>
                        <option value="ICT" {{ old('province') == 'ICT' ? 'selected' : '' }}>ICT</option>
                    </select>
                </div>

                <div class="mb-3 mt-4">
                    <label class="form-check form-switch">
                        <input name="is_test_center" class="form-check-input" type="checkbox" value="1" {{ old('is_test_center') ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold">Designate as Test Center City</span>
                        <span class="form-check-description">This will allow candidates to select this city as their preferred test venue.</span>
                    </label>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.cities.index') }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="ti ti-device-floppy me-2"></i> Save City
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

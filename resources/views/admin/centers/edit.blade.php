@extends('layouts.admin')
@section('title', 'Edit Center')
@section('page-title', 'Edit Test Center')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.centers.update',$center) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">TCID *</label>
                <input type="text" name="tcid" class="form-control text-uppercase" maxlength="3" value="{{ old('tcid',$center->tcid) }}" required>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Center Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$center->name) }}" required>
            </div>
            <div class="col-md-4"><label class="form-label fw-semibold">City *</label><input type="text" name="city" class="form-control" value="{{ old('city',$center->city) }}" required></div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Province *</label>
                <select name="province" class="form-select" required>
                    @foreach(['Punjab','Sindh','KPK','Balochistan','Gilgit-Baltistan','AJK','ICT'] as $p)
                    <option value="{{ $p }}" {{ old('province',$center->province)===$p?'selected':'' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label fw-semibold">Total Capacity *</label><input type="number" name="total_capacity" class="form-control" value="{{ old('total_capacity',$center->total_capacity) }}" min="1" required></div>
            <div class="col-12"><label class="form-label fw-semibold">Full Address *</label><textarea name="address" class="form-control" rows="2" required>{{ old('address',$center->address) }}</textarea></div>
            <div class="col-12"><label class="form-label fw-semibold">Map URL</label><input type="url" name="map_url" class="form-control" value="{{ old('map_url',$center->map_url) }}"></div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Save Changes</button>
                <a href="{{ route('admin.centers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

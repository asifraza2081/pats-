@extends('layouts.admin')
@section('title', 'Create Project')
@section('page-title', 'Create Project')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['draft','open','closed','result_declared'] as $s)
                    <option value="{{ $s }}" {{ old('status','draft')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Organisation / Dept <span class="text-danger">*</span></label>
                <input type="text" name="org_name" class="form-control" value="{{ old('org_name') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description / Advertisement</label>
                <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Open Date</label>
                <input type="date" name="open_date" class="form-control" value="{{ old('open_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Close Date</label>
                <input type="date" name="close_date" class="form-control" value="{{ old('close_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Date</label>
                <input type="date" name="test_date" class="form-control" value="{{ old('test_date') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Logo (optional)</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Create Project</button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

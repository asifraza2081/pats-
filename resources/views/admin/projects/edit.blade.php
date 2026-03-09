@extends('layouts.admin')
@section('title', 'Edit Project')
@section('page-title', 'Edit Project')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.projects.update',$project) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Project Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$project->name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status *</label>
                <select name="status" class="form-select" required>
                    @foreach(['draft','open','closed','result_declared'] as $s)
                    <option value="{{ $s }}" {{ old('status',$project->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Organisation / Dept *</label>
                <input type="text" name="org_name" class="form-control" value="{{ old('org_name',$project->org_name) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description / Advertisement</label>
                <textarea name="description" class="form-control" rows="5">{{ old('description',$project->description) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Open Date</label>
                <input type="date" name="open_date" class="form-control" value="{{ old('open_date',$project->open_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Close Date</label>
                <input type="date" name="close_date" class="form-control" value="{{ old('close_date',$project->close_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Date</label>
                <input type="date" name="test_date" class="form-control" value="{{ old('test_date',$project->test_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Logo (replace)</label>
                @if($project->logo_path)
                <div class="mb-1"><img src="{{ asset('storage/'.$project->logo_path) }}" height="40" class="rounded"></div>
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Save Changes</button>
                <a href="{{ route('admin.projects.show',$project) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Edit Project')
@section('page-title', 'Edit Project')

@section('page-actions')
<a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Project
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" class="card shadow-sm border-0">
            @csrf @method('PUT')
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Edit Project Configuration</h3>
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
                    <div class="col-md-8">
                        <label class="form-label required">Project Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Status Phase</label>
                        <select name="status" class="form-select tom-select" required>
                            @foreach(['draft'=>'Draft / Setup', 'open'=>'Open for Applications', 'closed'=>'Applications Closed', 'result_declared'=>'Results Declared'] as $s => $label)
                            <option value="{{ $s }}" {{ old('status', $project->status)===$s?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label required">Organisation / Department</label>
                        <input type="text" name="org_name" class="form-control" value="{{ old('org_name', $project->org_name) }}" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <label class="form-label">Description / Advertisement Text <span class="form-label-description">56/1000</span></label>
                        <textarea name="description" class="form-control" rows="6">{{ old('description', $project->description) }}</textarea>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Opening Date</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <input type="date" name="open_date" class="form-control" value="{{ old('open_date', $project->open_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Closing Date</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <input type="date" name="close_date" class="form-control" value="{{ old('close_date', $project->close_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Test Date (Target)</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <input type="date" name="test_date" class="form-control" value="{{ old('test_date', $project->test_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Client Logo (Replace Optional)</label>
                        @if($project->logo_path)
                        <div class="mb-2 d-flex align-items-center gap-3">
                            <span class="avatar avatar-md border shadow-none" style="background-image: url('{{ asset('storage/'.$project->logo_path) }}')"></span>
                            <span class="text-secondary small">Current Logo</span>
                        </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

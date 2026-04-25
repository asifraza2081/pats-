@extends('layouts.dashboard')
@section('title', 'Create Project')
@section('page-title', 'Create Project')

@section('page-actions')
<a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Directory
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="card">
            @csrf
            <div class="card-header">
                <h3 class="card-title fw-bold">Project Configuration</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger alert-important d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-alert-circle icon alert-icon me-3"></i>
                    <div>
                        <div class="fw-bold fs-3 mb-1">Configuration Errors Detected</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label required">Project Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Health Department Recruitment 2026" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Status Phase</label>
                        <select name="status" class="form-select tom-select" required>
                            @foreach(['draft'=>'Draft / Setup', 'open'=>'Open for Applications', 'closed'=>'Applications Closed', 'result_declared'=>'Results Declared'] as $s => $label)
                            <option value="{{ $s }}" {{ old('status','draft')===$s?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label required">Organisation / Department</label>
                        <input type="text" name="org_name" class="form-control" value="{{ old('org_name') }}" placeholder="Name of the client organization" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <!-- RESTORED CHARACTER COUNTER -->
                        <label class="form-label">Description / Advertisement Text <span class="form-label-description">56/1000</span></label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Provide full details, guidelines, or advertisement text...">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Opening Date</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <!-- RESTORED DATE CONSTRAINTS -->
                            <input type="date" name="open_date" class="form-control" value="{{ old('open_date') }}" max="2099-12-31">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Closing Date</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <input type="date" name="close_date" class="form-control" value="{{ old('close_date') }}" max="2099-12-31">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Test Date (Target)</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-calendar-event"></i></span>
                            <input type="date" name="test_date" class="form-control" value="{{ old('test_date') }}" max="2099-12-31">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Client Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div class="form-hint">Upload organization's branding for portal visibility (PNG/JPG).</div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between gap-3">
                <div class="text-secondary small d-flex align-items-center gap-2">
                    <i class="ti ti-clock text-info fs-3"></i>
                    <span>Once saved, this project may take <strong>up to 2 hours</strong> to appear on the public portal due to caching. Set the Status to <em>Open</em> only when you are ready to go live.</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-link">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-2"></i> Save & Create Project</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

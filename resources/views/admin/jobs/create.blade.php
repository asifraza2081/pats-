@extends('layouts.dashboard')
@section('title', 'Add Job Post')
@section('page-title', 'Add Job Post')

@section('page-actions')
<a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Project
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('admin.projects.jobs.store', $project) }}" class="card shadow-sm border-0">
            @csrf
            
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Job Post Setup: {{ $project->name }}</h3>
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
                
                <h3 class="card-title mt-2 mb-3"><i class="ti ti-info-circle text-blue me-2"></i> Basic Information</h3>
                <div class="row g-4 mb-5">
                    <div class="col-md-8">
                        <label class="form-label required">Job Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Assistant Sub-Inspector" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">BPS Grade</label>
                        <input type="text" name="bps_grade" class="form-control" value="{{ old('bps_grade') }}" placeholder="e.g. 14">
                        <div class="form-hint">Numeric only (e.g. 14).</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Department / Division</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="e.g. Traffic Police">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label required">Total Seats</label>
                        <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats') }}" min="1" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label required">Fee (PKR)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs</span>
                            <input type="number" name="fee" class="form-control" value="{{ old('fee') }}" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Quota / Reservation Details</label>
                        <textarea name="quota_notes" class="form-control" rows="2" placeholder="e.g. 20% women quota, 5% disabled, 5% minorities...">{{ old('quota_notes') }}</textarea>
                    </div>
                </div>

                <h3 class="card-title mb-3"><i class="ti ti-check text-blue me-2"></i> Strict Eligibility Criteria</h3>
                <div class="alert alert-info bg-info-lt border-0 small">
                    <i class="ti ti-info-circle me-1"></i>
                    The Eligibility Engine will automatically screen candidates against these rules before allowing them to apply. Leave fields blank if they are not strictly required.
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label">Min Degree Level</label>
                        <select name="min_degree_level" class="form-select tom-select">
                            <option value="">— Not specified —</option>
                            <option value="1" {{ old('min_degree_level')=='1'?'selected':'' }}>Matric (10 Years)</option>
                            <option value="2" {{ old('min_degree_level')=='2'?'selected':'' }}>Intermediate (12 Years)</option>
                            <option value="3" {{ old('min_degree_level')=='3'?'selected':'' }}>Bachelor's (14/16 Years)</option>
                            <option value="4" {{ old('min_degree_level')=='4'?'selected':'' }}>Master's (16/18 Years)</option>
                            <option value="5" {{ old('min_degree_level')=='5'?'selected':'' }}>M.Phil (18 Years)</option>
                            <option value="6" {{ old('min_degree_level')=='6'?'selected':'' }}>PhD</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Required Degree Name</label>
                        <input type="text" name="min_qualification_name" class="form-control" value="{{ old('min_qualification_name') }}" placeholder="e.g. BSCS, BBA">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Required Subject Field</label>
                        <input type="text" name="required_subject" class="form-control" value="{{ old('required_subject') }}" placeholder="e.g. Computer Science">
                    </div>
                    
                    <div class="col-12"><hr class="my-0"></div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Min Experience (Years)</label>
                        <div class="input-group">
                            <input type="number" name="min_experience_years" class="form-control" value="{{ old('min_experience_years','0') }}" min="0" step="0.5">
                            <span class="input-group-text">Years</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Experience Sector</label>
                        <select name="experience_sector" class="form-select tom-select">
                            <option value="Any" {{ old('experience_sector','Any')==='Any'?'selected':'' }}>Any Sector Allowed</option>
                            <option value="Public" {{ old('experience_sector')==='Public'?'selected':'' }}>Public / Govt Only</option>
                            <option value="Private" {{ old('experience_sector')==='Private'?'selected':'' }}>Private Only</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Strict Domicile Match</label>
                        <input type="text" name="domicile_required" class="form-control" value="{{ old('domicile_required') }}" placeholder="e.g. Punjab">
                        <div class="form-hint">Candidate's Domicile exactly match this.</div>
                    </div>
                    
                    <div class="col-12"><hr class="my-0"></div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Minimum Age Limit</label>
                        <div class="input-group">
                            <input type="number" name="age_min" class="form-control" value="{{ old('age_min') }}" min="16" max="70">
                            <span class="input-group-text">Years</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum Age Limit</label>
                        <div class="input-group">
                            <input type="number" name="age_max" class="form-control" value="{{ old('age_max') }}" min="16" max="70">
                            <span class="input-group-text">Years</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check me-2"></i> Publish Job Post</button>
            </div>
        </form>
    </div>
</div>
@endsection

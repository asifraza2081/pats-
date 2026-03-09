@extends('layouts.admin')
@section('title', 'Add Job Post')
@section('page-title', 'Add Job Post — ' . $project->name)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.projects.jobs.store',$project) }}">
        @csrf
        <h6 class="fw-bold mb-3 text-muted text-uppercase small">Basic Info</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Job Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Assistant Sub-Inspector" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">BPS Grade</label>
                <input type="text" name="bps_grade" class="form-control" value="{{ old('bps_grade') }}" placeholder="e.g. BPS-14">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Department / Division</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="e.g. Police Department">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Total Seats *</label>
                <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats') }}" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Fee (PKR) *</label>
                <input type="number" name="fee" class="form-control" value="{{ old('fee') }}" min="0" step="0.01" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Quota / Reservation Notes</label>
                <textarea name="quota_notes" class="form-control" rows="2" placeholder="e.g. 20% women quota, 5% disabled...">{{ old('quota_notes') }}</textarea>
            </div>
        </div>

        <h6 class="fw-bold mb-3 text-muted text-uppercase small">Eligibility Criteria</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Degree Level</label>
                <select name="min_degree_level" class="form-select">
                    <option value="">— Not specified —</option>
                    <option value="1" {{ old('min_degree_level')=='1'?'selected':'' }}>Matric (Level 1)</option>
                    <option value="2" {{ old('min_degree_level')=='2'?'selected':'' }}>Intermediate (Level 2)</option>
                    <option value="3" {{ old('min_degree_level')=='3'?'selected':'' }}>Bachelor's (Level 3)</option>
                    <option value="4" {{ old('min_degree_level')=='4'?'selected':'' }}>Master's (Level 4)</option>
                    <option value="5" {{ old('min_degree_level')=='5'?'selected':'' }}>M.Phil (Level 5)</option>
                    <option value="6" {{ old('min_degree_level')=='6'?'selected':'' }}>PhD (Level 6)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Qualification Name</label>
                <input type="text" name="min_qualification_name" class="form-control" value="{{ old('min_qualification_name') }}" placeholder="e.g. BA/BSc">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Required Subject/Major</label>
                <input type="text" name="required_subject" class="form-control" value="{{ old('required_subject') }}" placeholder="e.g. Computer Science">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Experience (years)</label>
                <input type="number" name="min_experience_years" class="form-control" value="{{ old('min_experience_years','0') }}" min="0" step="0.5">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Experience Sector</label>
                <select name="experience_sector" class="form-select">
                    <option value="Any" {{ old('experience_sector','Any')==='Any'?'selected':'' }}>Any</option>
                    <option value="Public" {{ old('experience_sector')==='Public'?'selected':'' }}>Public</option>
                    <option value="Private" {{ old('experience_sector')==='Private'?'selected':'' }}>Private</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Domicile Required</label>
                <input type="text" name="domicile_required" class="form-control" value="{{ old('domicile_required') }}" placeholder="e.g. Punjab, Lahore">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Age Min</label>
                <input type="number" name="age_min" class="form-control" value="{{ old('age_min') }}" min="16" max="70">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Age Max</label>
                <input type="number" name="age_max" class="form-control" value="{{ old('age_max') }}" min="16" max="70">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-pats px-4">Add Job Post</button>
                <a href="{{ route('admin.projects.show',$project) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

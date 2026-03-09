@extends('layouts.admin')
@section('title', 'Edit Job Post')
@section('page-title', 'Edit Job Post')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.jobs.update',$job) }}">
        @csrf @method('PUT')
        <h6 class="fw-bold mb-3 text-muted text-uppercase small">Basic Info</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Job Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title',$job->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">BPS Grade</label>
                <input type="text" name="bps_grade" class="form-control" value="{{ old('bps_grade',$job->bps_grade) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Department</label>
                <input type="text" name="department" class="form-control" value="{{ old('department',$job->department) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Seats *</label>
                <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats',$job->total_seats) }}" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Fee (PKR) *</label>
                <input type="number" name="fee" class="form-control" value="{{ old('fee',$job->fee) }}" min="0" step="0.01" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Quota Notes</label>
                <textarea name="quota_notes" class="form-control" rows="2">{{ old('quota_notes',$job->quota_notes) }}</textarea>
            </div>
        </div>
        <h6 class="fw-bold mb-3 text-muted text-uppercase small">Eligibility Criteria</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Degree Level</label>
                <select name="min_degree_level" class="form-select">
                    <option value="">— Not specified —</option>
                    @foreach([1=>'Matric',2=>'Intermediate',3=>"Bachelor's",4=>"Master's",5=>'M.Phil',6=>'PhD'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('min_degree_level',$job->min_degree_level)==$v?'selected':'' }}>{{ $l }} (Level {{ $v }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Qualification Name</label>
                <input type="text" name="min_qualification_name" class="form-control" value="{{ old('min_qualification_name',$job->min_qualification_name) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Required Subject</label>
                <input type="text" name="required_subject" class="form-control" value="{{ old('required_subject',$job->required_subject) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Experience (years)</label>
                <input type="number" name="min_experience_years" class="form-control" value="{{ old('min_experience_years',$job->min_experience_years) }}" min="0" step="0.5">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Experience Sector</label>
                <select name="experience_sector" class="form-select">
                    @foreach(['Any','Public','Private'] as $s)
                    <option value="{{ $s }}" {{ old('experience_sector',$job->experience_sector)===$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Domicile Required</label>
                <input type="text" name="domicile_required" class="form-control" value="{{ old('domicile_required',$job->domicile_required) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Age Min</label>
                <input type="number" name="age_min" class="form-control" value="{{ old('age_min',$job->age_min) }}" min="16" max="70">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Age Max</label>
                <input type="number" name="age_max" class="form-control" value="{{ old('age_max',$job->age_max) }}" min="16" max="70">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-pats px-4">Save Changes</button>
                <a href="{{ route('admin.projects.show',$job->project_id) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Apply — ' . $job->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
    <div class="col-lg-8">

    {{-- Job Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background:linear-gradient(135deg,#0a3d62,#1a5276);color:#fff">
        <div class="card-body p-4">
            <div class="small text-white-50 mb-1">{{ $project->name }} · {{ $project->org_name }}</div>
            <h5 class="fw-bold mb-1">{{ $job->title }}</h5>
            <div class="d-flex flex-wrap gap-3 small mt-2">
                @if($job->bps_grade)<span><i class="bi bi-tag me-1"></i>{{ $job->bps_grade }}</span>@endif
                @if($job->department)<span><i class="bi bi-building me-1"></i>{{ $job->department }}</span>@endif
                <span><i class="bi bi-people me-1"></i>{{ $job->total_seats }} seats</span>
                <span class="fw-bold" style="color:#f9ca24"><i class="bi bi-cash me-1"></i>PKR {{ number_format($job->fee) }}</span>
            </div>
        </div>
    </div>

    {{-- Eligibility Check Result --}}
    @if($eligibilityResult['passed'])
    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>You meet all eligibility criteria for this post.</strong>
    </div>
    @else
    <div class="alert alert-warning">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Eligibility Warnings</strong>
        <p class="small mb-2 mt-1">You may still apply, but please note the following discrepancies. Final eligibility is subject to verification of original documents at the time of test.</p>
        <ul class="mb-0 small">
            @foreach($eligibilityResult['warnings'] as $w)<li>{{ $w }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Eligibility Criteria Display --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Eligibility Criteria</h6>
            <div class="row g-2 small">
                <div class="col-md-6"><i class="bi bi-mortarboard me-2 text-muted"></i>
                    @if($job->min_degree_level){{ \App\Models\EducationHistory::$levelLabels[$job->min_degree_level] ?? '' }}@if($job->min_qualification_name) ({{ $job->min_qualification_name }})@endif
                    @else<span class="text-muted">No minimum degree</span>@endif
                </div>
                <div class="col-md-6"><i class="bi bi-person me-2 text-muted"></i>
                    Age: {{ $job->age_min ?? '—' }} to {{ $job->age_max ?? '—' }} years
                    (Your age: <strong>{{ $candidate->age }}</strong>)
                </div>
                @if($job->required_subject)
                <div class="col-md-6"><i class="bi bi-book me-2 text-muted"></i>Subject: {{ $job->required_subject }}</div>
                @endif
                @if($job->min_experience_years > 0)
                <div class="col-md-6"><i class="bi bi-briefcase me-2 text-muted"></i>
                    Experience: {{ $job->min_experience_years }} yr(s) ({{ $job->experience_sector }})
                    (Your total: <strong>{{ number_format($candidate->totalExperienceYears($job->experience_sector !== 'Any' ? $job->experience_sector : null), 1) }} yr(s)</strong>)
                </div>
                @endif
                @if($job->domicile_required)
                <div class="col-md-6"><i class="bi bi-geo-alt me-2 text-muted"></i>Domicile: {{ $job->domicile_required }}</div>
                @endif
                @if($job->quota_notes)
                <div class="col-12 text-muted"><i class="bi bi-info-circle me-2"></i>{{ $job->quota_notes }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Application Form --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-send me-2 text-success"></i>Application Form</h6>
            <form method="POST" action="{{ route('candidate.apply.store',$job) }}">
                @csrf
                @if($errors->any())<div class="alert alert-danger small"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Preferred Test City (Priority 1) *</label>
                        <input type="text" name="test_city_priority_1" class="form-control" value="{{ old('test_city_priority_1') }}" placeholder="e.g. Lahore" required>
                        <div class="form-text">We'll try to place you in a center in this city.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Preferred Test City (Priority 2)</label>
                        <input type="text" name="test_city_priority_2" class="form-control" value="{{ old('test_city_priority_2') }}" placeholder="e.g. Islamabad">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Age Relaxation Type <span class="text-muted small">(if any)</span></label>
                        <select name="age_relaxation_type" class="form-select">
                            <option value="">— None —</option>
                            <option value="Government Employee" {{ old('age_relaxation_type')==='Government Employee'?'selected':'' }}>Government Employee</option>
                            <option value="Ex-Service Person" {{ old('age_relaxation_type')==='Ex-Service Person'?'selected':'' }}>Ex-Service Person</option>
                            <option value="Disabled Person" {{ old('age_relaxation_type')==='Disabled Person'?'selected':'' }}>Disabled Person</option>
                            <option value="Tribal Area" {{ old('age_relaxation_type')==='Tribal Area'?'selected':'' }}>Tribal Area</option>
                            <option value="Other" {{ old('age_relaxation_type')==='Other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Age Relaxation (years)</label>
                        <input type="number" name="age_relaxation_years" class="form-control" value="{{ old('age_relaxation_years') }}" min="1" max="10">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-secondary small mb-0">
                            <i class="bi bi-receipt me-1"></i>
                            After submitting, you'll need to download and deposit the <strong>fee challan of PKR {{ number_format($job->fee) }}</strong> at the designated bank branch before the deadline.
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmDecl" required>
                            <label class="form-check-label small" for="confirmDecl">
                                I hereby declare that all information provided is correct to the best of my knowledge. I understand that any false information may result in disqualification.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-pats px-5 fw-semibold"><i class="bi bi-send-check me-1"></i>Submit Application</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    </div>
    </div>
</div>
@endsection

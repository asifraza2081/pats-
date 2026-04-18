@extends('layouts.dashboard')
@section('page-title', 'Apply — ' . $job->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-4 d-print-none">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i> Back to Project
            </a>
        </div>

        <!-- Job Header Banner -->
        <div class="card border-0 shadow-lg rounded-5 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0a3d62 0%, #1e5a8c 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-9 mb-3 mb-md-0">
                        <div class="text-white-50 small mb-2 text-uppercase tracking-wide fw-bold">
                            <i class="ti ti-building me-1"></i> {{ $project->org_name }} <span class="mx-2 opacity-50">•</span> {{ $project->name }}
                        </div>
                        <h2 class="display-6 fw-black mb-3 text-white">{{ $job->title }}</h2>
                        <div class="d-flex flex-wrap gap-3 small">
                            @if($job->bps_grade)
                            <span class="d-flex align-items-center badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fs-4 shadow-sm border border-white border-opacity-10"><i class="ti ti-rosette me-2 text-pats-gold"></i> BPS-{{ $job->bps_grade }}</span>
                            @endif
                            @if($job->department)
                            <span class="d-flex align-items-center badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fs-4 shadow-sm border border-white border-opacity-10"><i class="ti ti-building me-2 text-cyan"></i> {{ $job->department }}</span>
                            @endif
                            <span class="d-flex align-items-center badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fs-4 shadow-sm border border-white border-opacity-10"><i class="ti ti-users me-2 text-green"></i> {{ $job->total_seats }} Seats</span>
                        </div>
                    </div>
                    <div class="col-md-3 text-md-end border-start border-white border-opacity-25 ps-md-4 mt-3 mt-md-0">
                        <div class="text-white-50 small mb-1 tracking-widest text-uppercase fw-bold">Processing Fee</div>
                        <div class="fw-black display-6 text-yellow">PKR {{ number_format($job->fee) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eligibility Result Alert -->
        @if($eligibilityResult['passed'])
        <div class="alert alert-important alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check fs-2 me-3"></i></div>
                <div>
                    <strong>Eligibility Confirmed</strong><br>
                    You meet all primary eligibility criteria for this position.
                </div>
            </div>
            <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @else
        <div class="alert alert-important alert-warning alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-triangle fs-2 me-3"></i></div>
                <div>
                    <strong>Eligibility Notices</strong><br>
                    You may proceed with the application, but please be aware of the following discrepancies. Final eligibility is always determined during official document verification.<br>
                    <ul class="mb-0 mt-2">
                        @foreach($eligibilityResult['warnings'] as $w)
                            <li>{{ $w }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="row row-cards">
            <!-- Criteria Display -->
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-5 h-100 overflow-hidden">
                    <div class="card-header border-0 bg-light py-4 px-4">
                        <h3 class="card-title fw-black fs-2 text-dark m-0"><i class="ti ti-list-check text-primary me-2"></i> Required Criteria</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item w-100 mb-3">
                                <div class="datagrid-title d-flex align-items-center"><i class="ti ti-certificate me-2"></i> Minimum Education</div>
                                <div class="datagrid-content fw-semibold">
                                    @if($job->min_degree_level)
                                        {{ \App\Models\EducationHistory::$levelLabels[$job->min_degree_level] ?? '' }}
                                        @if($job->min_qualification_name)
                                            <div class="text-muted small fw-normal">{{ $job->min_qualification_name }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">No specific degree required</span>
                                    @endif
                                </div>
                            </div>

                            <div class="datagrid-item w-100 mb-3">
                                <div class="datagrid-title d-flex align-items-center"><i class="ti ti-calendar-user me-2"></i> Age Limit</div>
                                <div class="datagrid-content fw-semibold">
                                    {{ $job->age_min ?? '—' }} to {{ $job->age_max ?? '—' }} years
                                    <div class="text-muted small fw-normal">Your age: <strong>{{ $candidate->age }}</strong></div>
                                </div>
                            </div>

                            @if($job->required_subject)
                            <div class="datagrid-item w-100 mb-3">
                                <div class="datagrid-title d-flex align-items-center"><i class="ti ti-book me-2"></i> Subject Area</div>
                                <div class="datagrid-content fw-semibold">{{ $job->required_subject }}</div>
                            </div>
                            @endif

                            @if($job->min_experience_years > 0)
                            <div class="datagrid-item w-100 mb-3">
                                <div class="datagrid-title d-flex align-items-center"><i class="ti ti-briefcase me-2"></i> Experience</div>
                                <div class="datagrid-content fw-semibold">
                                    {{ $job->min_experience_years }} year(s) <span class="text-muted small">({{ $job->experience_sector }})</span>
                                    <div class="text-muted small fw-normal">Your matching XP: <strong>{{ number_format($candidate->totalExperienceYears($job->experience_sector !== 'Any' ? $job->experience_sector : null), 1) }} yr(s)</strong></div>
                                </div>
                            </div>
                            @endif

                            @if($job->domicile_required)
                            <div class="datagrid-item w-100 mb-3">
                                <div class="datagrid-title d-flex align-items-center"><i class="ti ti-map-pin me-2"></i> Domicile Required</div>
                                <div class="datagrid-content fw-semibold">{{ $job->domicile_required }}</div>
                            </div>
                            @endif

                            @if($job->quota_notes)
                            <div class="datagrid-item w-100 mt-2">
                                <div class="alert alert-secondary mb-0 p-2 small">
                                    <i class="ti ti-info-circle me-1"></i> {{ $job->quota_notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <div class="col-md-7 col-lg-8">
                <div class="card shadow-lg border-0 rounded-5 h-100 overflow-hidden">
                    <div class="card-header border-0 bg-light py-4 px-4">
                        <h3 class="card-title fw-black fs-2 text-dark m-0"><i class="ti ti-file-text text-primary me-2"></i> Application Configuration</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('candidate.apply.store', $job) }}" autocomplete="off">
                            @csrf
                            
                            @if($errors->any())
                            <div class="alert alert-danger mb-4">
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
                                <div class="col-12">
                                    <label class="form-label required">Desired Test City</label>
                                    <select name="desired_test_city_id" class="form-select @error('desired_test_city_id') is-invalid @enderror" required>
                                        <option value="">— Select Preferred Test City —</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('desired_test_city_id') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}, {{ $city->province }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">PATS will attempt to allocate a test center in your desired city.</div>
                                </div>
                                
                                <div class="col-12 mt-3"><hr class="my-2"></div>

                                <!-- Age Relaxation Hidden as per Client Request -->
                                <input type="hidden" name="age_relaxation_type" value="">
                                <input type="hidden" name="age_relaxation_years" value="0">

                                <div class="col-12 mt-4">
                                    <div class="alert bg-blue-lt mb-4 border-0">
                                        <div class="d-flex">
                                            <div><i class="ti ti-receipt fs-2 me-3 text-primary"></i></div>
                                            <div class="small">
                                                <strong>Next Step:</strong> After submitting this form, you will be required to download a fee challan of <strong>PKR {{ number_format($job->fee) }}</strong>. Please print it and deposit the fee at the designated bank branch to complete your registration.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Disclaimer/Declaration Hidden as per Client Request -->
                                    <input type="hidden" id="confirmDecl" value="1" required>
                                    
                                    <button type="submit" class="btn btn-primary w-100 py-3 fs-3 fw-black rounded-pill shadow-sm hover-shadow-lg transition-all">
                                        <i class="ti ti-send me-2"></i> Submit Final Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

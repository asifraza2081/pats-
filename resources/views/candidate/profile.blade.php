@extends('layouts.dashboard')

@section('page-title', 'Candidate Profile Wizard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css">
<style>
    /* Wizard Container Padding */
    .wizard-container {
        padding: 1.5rem 0;
    }
    
    /* Premium Entry Animations */
    .wizard-card {
        animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    
    @keyframes slideInUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Step Sidebar Enhancements */
    .wizard-sidebar .card {
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
    }
    .wizard-sidebar .card.bg-primary {
        border-left: 4px solid var(--pats-primary-dark);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Next Progression Buttons */
    .btn-next-step {
        background: linear-gradient(135deg, #0a3d62 0%, #1e5a8c 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-next-step:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 90, 140, 0.3);
        color: white;
    }

    /* Loading Overlay */
    #wizard-loading {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div id="wizard-loading">
    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
    <div class="fw-black text-primary text-uppercase tracking-widest">Processing...</div>
</div>
@php
    $status = app(\App\Services\EligibilityService::class)->getProfileStatus($candidate);
@endphp

<div class="row g-4">
    <!-- Sidebar: Step Management -->
    <div class="col-lg-3">
        <div class="card mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body text-center p-4">
                <div class="mb-4 d-none d-lg-block">
                    <img src="{{ $candidate->photo_path ? asset('storage/'.$candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}" 
                         class="rounded-circle border-4 border-white shadow-sm object-cover" 
                         style="width: 120px; height: 120px;"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name) }}&background=f1f5f9&color=64748b'">
                </div>
                <h3 class="m-0 mb-1 fw-black">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                
                <div class="mt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-pats-primary">Overall Progress</span>
                        <span class="badge {{ $status['total_percent'] < 100 ? 'bg-warning-lt text-warning' : 'bg-success-lt text-success' }}">{{ $status['total_percent'] }}%</span>
                    </div>
                    <div class="progress progress-sm mb-0 rounded-pill">
                        <div class="progress-bar {{ $status['total_percent'] < 100 ? 'bg-warning' : 'bg-success' }}" style="width: {{ $status['total_percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 20px;">
            <div class="list-group list-group-flush">
                @foreach(['1' => 'Profile Picture', '2' => 'Personal Bio', '3' => 'Academic History', '4' => 'Work Experience'] as $s => $label)
                    @php 
                        $isAccessible = $candidate->isStepAccessible((int)$s);
                        $isComplete = $status['step'.$s]['success'] ?? false;
                        $isActive = $step == $s;
                    @endphp
                    <a href="{{ $isAccessible ? route('candidate.profile.show', ['step' => $s]) : '#' }}" 
                       class="list-group-item list-group-item-action py-3 border-0 {{ $isActive ? 'bg-pats-primary text-white active' : '' }} {{ !$isAccessible ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($isComplete && $s <= $status['next_step'])
                                    <i class="ti ti-circle-check fs-2 text-success"></i>
                                @elseif($isActive)
                                    <i class="ti ti-circle-dot fs-2 text-primary"></i>
                                @else
                                    <i class="ti ti-circle fs-2 {{ $isAccessible ? 'text-primary' : 'text-muted' }}"></i>
                                @endif
                            </div>
                            <div>
                                <div class="fw-black small">STEP {{ $s }}</div>
                                <div class="small {{ $isActive ? 'text-white' : 'text-muted' }}">{{ $label }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            @if(!$status['step'.$step]['success'] && !empty($status['step'.$step]['errors']))
            <div class="card-footer bg-warning-lt border-0 p-3">
                <div class="small fw-bold text-warning mb-2"><i class="ti ti-alert-triangle me-1"></i> Missing Requirements:</div>
                <ul class="list-unstyled mb-0 small">
                    @foreach($status['step'.$step]['errors'] as $err)
                        <li class="text-warning-emphasis mb-1">• {{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Content: Wizard Body -->
    <div class="col-lg-9">
        @if($candidate->profile_locked)
        <div class="alert alert-important alert-info rounded-4 mb-4 shadow-sm">
            <div class="d-flex">
                <div><i class="ti ti-lock fs-2 me-3"></i></div>
                <div>Your profile is locked due to active applications. Only non-core contact fields can be updated.</div>
            </div>
        </div>
        @endif

        <div class="tab-content">
            <!-- STEP 1: VERIFICATION DOCUMENTS -->
            @if($step == 1)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary" style="background: linear-gradient(135deg, #be185d 0%, #831843 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-camera me-2 fs-1"></i> Step 1: Verification Documents</h3>
                </div>
                <form method="POST" action="{{ route('candidate.profile.update.docs') }}" enctype="multipart/form-data" class="no-spinner" id="docsForm">
                    @csrf @method('PUT')
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-5 justify-content-center">
                            <!-- Photo Upload -->
                            <div class="col-md-6 text-center">
                                <div class="p-4 border-dashed rounded-4 bg-light">
                                    <div class="mb-3">
                                        <img id="photo_preview" src="{{ $candidate->photo_path ? asset('storage/'.$candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}" 
                                             class="rounded-4 border-4 border-white shadow-sm object-cover" 
                                             style="width: 150px; height: 180px;">
                                    </div>
                                    <label class="btn btn-primary rounded-pill px-4">
                                        <i class="ti ti-camera me-2"></i> UPLOAD PROFILE PHOTO
                                        <input type="file" name="photo" class="d-none" onchange="previewFile(this, 'photo_preview')">
                                    </label>
                                    <div class="form-hint mt-2">Passport size, white background. Max 5MB.</div>
                                </div>
                            </div>
                            <!-- CNIC Upload Hidden as per request -->
                            <div class="col-md-6 text-center d-none">
                                <div class="p-4 border-dashed rounded-4 bg-light">
                                    <div class="mb-3">
                                        <img id="cnic_preview" src="{{ $candidate->cnic_front_path ? asset('storage/'.$candidate->cnic_front_path) : 'https://placehold.co/300x200?text=CNIC+FRONT' }}" 
                                             class="rounded-3 border shadow-sm object-cover" 
                                             style="width: 100%; height: 180px;">
                                    </div>
                                    <label class="btn btn-secondary rounded-pill px-4">
                                        <i class="ti ti-id me-2"></i> UPLOAD CNIC FRONT
                                        <input type="file" name="cnic_copy" class="d-none" onchange="previewFile(this, 'cnic_preview')">
                                    </label>
                                    <div class="form-hint mt-2">Scanned copy of CNIC Front side. Max 5MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-ghost-secondary rounded-pill px-4" disabled>PREVIOUS</button>
                        <button type="submit" class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg text-white">
                            SAVE & PROCEED TO PERSONAL BIO <i class="ti ti-arrow-right-bar ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- STEP 2: PERSONAL BIO -->
            @if($step == 2)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-user-check me-2 fs-1 text-primary"></i> Step 2: Personal Information</h3>
                </div>
                <form method="POST" action="{{ route('candidate.profile.update.bio') }}" id="step1Form" class="no-spinner">
                    @csrf @method('PUT')
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6 d-none"> <!-- HIDE NIC AS PER REQUEST -->
                                <label class="form-label text-pats-primary fw-bold">CNIC Number</label>
                                <input type="text" name="cnic" id="cnic_mask" class="form-control bg-light" value="{{ old('cnic', auth()->user()->cnic) }}" placeholder="XXXXX-XXXXXXX-X" readonly tabindex="-1">
                                <div class="form-hint small text-muted">CNIC cannot be changed after registration.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required text-pats-primary fw-bold">Father's Name</label>
                                <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $candidate->father_name) }}" {{ ($candidate->profile_locked && $status['total_percent'] == 100) ? 'readonly' : '' }} required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Primary Mobile Number</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->phone }}" readonly tabindex="-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternative Mobile Number (Optional)</label>
                                <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $candidate->alternate_phone) }}" placeholder="e.g. 0300-1234567">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $candidate->dob?->format('Y-m-d')) }}" 
                                       min="1900-01-01" max="{{ now()->subYears(16)->format('Y-m-d') }}"
                                       {{ ($candidate->profile_locked && $status['total_percent'] == 100) ? 'readonly' : '' }} required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Gender</label>
                                <select name="gender" class="form-select" {{ ($candidate->profile_locked && $status['total_percent'] == 100) ? 'disabled' : '' }} required>
                                    <option value="Male" {{ old('gender', $candidate->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $candidate->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Religion</label>
                                <select name="religion" class="form-select" required>
                                    @foreach(['Islam', 'Christianity', 'Hinduism', 'Sikhism', 'Other'] as $r)
                                        <option value="{{ $r }}" {{ old('religion', $candidate->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Marital Status</label>
                                <select name="marital_status" class="form-select" required>
                                    @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $m)
                                        <option value="{{ $m }}" {{ old('marital_status', $candidate->marital_status) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mt-4 border-top pt-4">
                                <h4 class="text-pats-primary m-0 mb-3 fw-black"><i class="ti ti-map-pin me-2"></i> Address & Domicile</h4>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province of Domicile</label>
                                <select name="province_of_domicile" id="domicile_province" class="form-select">
                                    <option value="">— Select Province —</option>
                                    @foreach(['Punjab', 'Sindh', 'Khyber Pakhtunkhwa', 'Balochistan', 'Islamabad Capital Territory', 'Azad Jammu & Kashmir', 'Gilgit-Baltistan'] as $prov)
                                        <option value="{{ $prov }}" {{ old('province_of_domicile', $candidate->province_of_domicile) == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">District of Domicile</label>
                                <input type="hidden" name="domicile_city_id" id="hidden_domicile_city_id" value="{{ old('domicile_city_id', $candidate->domicile_city_id) }}">
                                <select name="district_of_domicile" id="domicile_district" class="form-select">
                                    <option value="">— Select District —</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Permanent Address</label>
                                <textarea name="permanent_address" class="form-control" rows="2" placeholder="Street, Village/Area, House No." required>{{ old('permanent_address', $candidate->permanent_address) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Postal / Mailing Address</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sync_address" name="same_postal_address" value="1" {{ old('same_postal_address', $candidate->same_postal_address) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="sync_address">Same as permanent address</label>
                                </div>
                                <textarea name="postal_address" id="postal_address_field" class="form-control" rows="2" placeholder="Current address for roll number delivery" required>{{ old('postal_address', $candidate->postal_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('candidate.profile.show', ['step' => 1]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <button type="submit" class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg">
                            SAVE & PROCEED TO ACADEMICS <i class="ti ti-arrow-right-bar ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- STEP 3: ACADEMIC RECORDS -->
            @if($step == 3)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-school me-2 fs-1"></i> Step 3: Academic History</h3>
                    <button type="button" class="btn bg-white text-indigo btn-sm fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addEduForm">
                        <i class="ti ti-plus me-1"></i> ADD DEGREE
                    </button>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="collapse mb-5" id="addEduForm">
                        <div class="card card-body bg-light border-dashed p-4 rounded-4">
                            <form id="ajaxEduForm" method="POST" action="{{ route('candidate.education.store') }}" class="no-spinner">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Level *</label>
                                        <select name="degree_level" class="form-select" required>
                                            @foreach([1=>'SSC / Matric', 2=>'HSSC / Inter', 3=>"Bachelor (14 Years)", 4=>"Bachelor (16 Years)", 5=>'Master (18 Years)', 6=>'PHD (21 Years)'] as $v => $l)
                                                <option value="{{ $v }}">{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5"><label class="form-label small fw-bold required">Degree Name</label><input type="text" name="degree_name" class="form-control" placeholder="e.g. BSCS" required></div>
                                    <div class="col-md-4"><label class="form-label small fw-bold">Institution</label><input type="text" name="institution" class="form-control"></div>
                                    <div class="col-md-4"><label class="form-label small fw-bold">Major</label><input type="text" name="subject_major" class="form-control"></div>
                                    <div class="col-md-3"><label class="form-label small fw-bold">Year</label><input type="number" name="passing_year" class="form-control" min="1970" max="{{ date('Y') }}"></div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Obtained *</label>
                                        <input type="number" step="0.01" name="obtained_marks" id="obt_marks" class="form-control" placeholder="Marks/CGPA" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Total *</label>
                                        <input type="number" step="0.01" name="total_marks" id="total_marks" class="form-control" placeholder="1100 / 4.0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Avg / %</label>
                                        <input type="text" id="calc_perc" class="form-control bg-light" readonly tabindex="-1" placeholder="Auto">
                                    </div>
                                    <input type="hidden" name="marks_type" id="marks_type_hidden" value="Marks">
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-indigo w-100 py-2 rounded-pill fw-bold text-white">
                                            <span class="spinner-border spinner-border-sm me-2 d-none" id="eduSpinner"></span>
                                            SAVE EDUCATION RECORD
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="education_list" class="divide-y">
                        @forelse($candidate->education->sortByDesc('degree_level') as $edu)
                            @include('candidate.partials._education_row', ['edu' => $edu, 'candidate' => $candidate])
                        @empty
                            <div class="text-center py-5 text-muted empty-edu">
                                <i class="ti ti-school fs-0 opacity-20 d-block mb-3"></i>
                                No degrees added yet. Please add your highest qualification to continue.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center">
                    <div class="text-muted small mb-3">Finished adding your educational qualifications?</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('candidate.profile.show', ['step' => 2]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <a href="{{ $status['step3']['success'] ? route('candidate.profile.show', ['step' => 4]) : '#' }}" 
                        class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg {{ !$status['step3']['success'] ? 'disabled opacity-50' : '' }}">
                            PROCEED TO WORK EXPERIENCE <i class="ti ti-arrow-right-bar ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- STEP 4: WORK EXPERIENCE -->
            @if($step == 4)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-briefcase me-2 fs-1"></i> Step 4: Work Experience</h3>
                    <button type="button" class="btn bg-white text-teal btn-sm fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addExpForm">
                        <i class="ti ti-plus me-1"></i> ADD EXPERIENCE
                    </button>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="collapse mb-5" id="addExpForm">
                        <div class="card card-body bg-light border-dashed p-4 rounded-4">
                            <form id="ajaxExpForm" method="POST" action="{{ route('candidate.experience.store') }}" class="no-spinner">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold required">Sector</label>
                                        <select name="job_type" class="form-select" required>
                                            <option value="Public">Public Sector</option>
                                            <option value="Private">Private Sector</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5"><label class="form-label small fw-bold required">Organisation</label><input type="text" name="organization_name" class="form-control" required></div>
                                    <div class="col-md-4"><label class="form-label small fw-bold required">Designation</label><input type="text" name="designation" class="form-control" required></div>
                                    <div class="col-md-3"><label class="form-label small fw-bold required">From Date</label><input type="date" name="from_date" class="form-control" required></div>
                                    <div class="col-md-3" id="to_date_wrapper">
                                        <label class="form-label small fw-bold">To Date</label>
                                        <input type="date" name="to_date" class="form-control">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1">
                                            <label class="form-check-label small fw-bold" for="is_current">Currently Working</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-teal w-100 py-2 rounded-pill fw-bold text-white">
                                            <span class="spinner-border spinner-border-sm me-2 d-none" id="expSpinner"></span>
                                            SAVE EXPERIENCE RECORD
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="experience_list" class="divide-y">
                        @forelse($candidate->experience->sortByDesc('from_date') as $exp)
                            @include('candidate.partials._experience_row', ['exp' => $exp, 'candidate' => $candidate])
                        @empty
                            <div class="text-center py-5 text-muted empty-exp">
                                <i class="ti ti-briefcase fs-0 opacity-20 d-block mb-3"></i>
                                Optional: Add your work history. You can click "Finish" if you have no experience.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center">
                    <div class="text-muted small mb-3 text-teal">Finished your profile? You can now browse and apply for jobs.</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('candidate.profile.show', ['step' => 3]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <a href="{{ route('projects') }}" class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg">
                            FINISH & BROWSE JOBS <i class="ti ti-circle-check ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-black"><i class="ti ti-crop me-2"></i> ADJUST PROFILE PHOTO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="img-container" style="max-height: 500px;">
                    <img id="cropperImage" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer bg-white border-0 py-3">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-dark btn-icon" onclick="cropper.rotate(-90)" title="Rotate Left"><i class="ti ti-rotate-2"></i></button>
                        <button type="button" class="btn btn-outline-dark btn-icon" onclick="cropper.rotate(90)" title="Rotate Right"><i class="ti ti-rotate-clockwise"></i></button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-primary px-4 rounded-pill fw-black" id="cropButton">SAVE CHANGES</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script>
const dbCities = @json($cities);
const districts = {};

dbCities.forEach(city => {
    if (!districts[city.province]) districts[city.province] = [];
    districts[city.province].push({ id: city.id, name: city.name });
});

window.previewFile = function(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
            // If it's the photo preview, trigger cropper
            if (targetId === 'photo_preview') {
                if (window.initCropper) window.initCropper(e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function handleStepAjax(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        window.showSpinners();
        form.querySelectorAll('button').forEach(b => b.disabled = true);

        const formData = new FormData(this);
        
        // If we have a cropped photo, replace it in the formData
        if (formId === 'docsForm' && window.croppedPhotoBlob) {
            formData.delete('photo');
            formData.append('photo', window.croppedPhotoBlob, 'profile_photo.jpg');
        }

        fetch(this.getAttribute('action'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                toastr.success(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                let msg = data.message || 'Validation failed. Please check your input.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('\n');
                }
                Swal.fire({ title: 'Error', text: msg, icon: 'error' });
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            window.stopSpinners();
            form.querySelectorAll('button').forEach(b => b.disabled = false);
        });
    });
}

function handleAjaxForm(formId, targetListId, spinnerId, emptyId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (window.showSpinners) window.showSpinners();
        const spinner = document.getElementById(spinnerId);
        const list = document.getElementById(targetListId);
        const empty = document.querySelector('.' + emptyId);
        
        spinner.classList.remove('d-none');
        form.querySelectorAll('button').forEach(b => b.disabled = true);

        fetch(this.getAttribute('action'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new FormData(this)
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                if (empty) empty.remove();
                list.insertAdjacentHTML('afterbegin', data.html);
                form.reset();
                const coll = bootstrap.Collapse.getInstance(form.closest('.collapse'));
                if (coll) coll.hide();
                toastr.success(data.message);
                setTimeout(() => location.reload(), 800); // Refresh to update progress sidebar
            } else {
                let msg = data.message || 'Validation failed.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('\n');
                }
                Swal.fire({ title: 'Error', text: msg, icon: 'error' });
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error('An error occurred. Please try again.');
        })
        .finally(() => {
            if (spinner) spinner.classList.add('d-none');
            if (window.stopSpinners) window.stopSpinners();
            form.querySelectorAll('button').forEach(b => b.disabled = false);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Stop spinners on load
    window.stopSpinners();
    
    // Domicile Logic
    const provinceSelect = document.getElementById('domicile_province');
    const districtSelect = document.getElementById('domicile_district');
    const hiddenDomicileId = document.getElementById('hidden_domicile_city_id');
    
    if (provinceSelect) {
        function populateDistricts(province, selectedDistrictValue = null) {
            districtSelect.innerHTML = '<option value="">— Select District —</option>';
            if (districts[province]) {
                districts[province].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.textContent = d.name;
                    opt.dataset.id = d.id;
                    if (selectedDistrictValue === d.name) opt.selected = true;
                    districtSelect.appendChild(opt);
                });
            }
        }

        provinceSelect.addEventListener('change', function() { populateDistricts(this.value); });
        districtSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.id) {
                hiddenDomicileId.value = selectedOpt.dataset.id;
            }
        });

        if (provinceSelect.value) {
            populateDistricts(provinceSelect.value, "{{ old('district_of_domicile', $candidate->district_of_domicile) }}");
        }
    }

    // Input Masking
    const cnicInput = document.getElementById('cnic_mask');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }

    // AJAX Forms (Step 2 & 3)
    handleAjaxForm('ajaxEduForm', 'education_list', 'eduSpinner', 'empty-edu');
    handleAjaxForm('ajaxExpForm', 'experience_list', 'expSpinner', 'empty-exp');

    // AJAX Steps (Step 1 & 4)
    handleStepAjax('step1Form');
    handleStepAjax('docsForm');
    
    // Sync Postal Address Logic
    const syncCheckbox = document.getElementById('sync_address');
    const permAddress = document.querySelector('textarea[name="permanent_address"]');
    const postAddress = document.getElementById('postal_address_field');
    
    if (syncCheckbox && permAddress && postAddress) {
        syncCheckbox.addEventListener('change', function() {
            if (this.checked) {
                postAddress.value = permAddress.value;
                postAddress.readOnly = true;
                postAddress.classList.add('bg-light');
            } else {
                postAddress.readOnly = false;
                postAddress.classList.remove('bg-light');
            }
        });
        
        permAddress.addEventListener('input', function() {
            if (syncCheckbox.checked) postAddress.value = this.value;
        });

        if (syncCheckbox.checked) {
            postAddress.readOnly = true;
            postAddress.classList.add('bg-light');
        }
    }

    // Marks Calculation Logic
    const obtInput = document.getElementById('obt_marks');
    const totInput = document.getElementById('total_marks');
    const percInput = document.getElementById('calc_perc');
    const typeInput = document.getElementById('marks_type_hidden');

    if (obtInput && totInput) {
        function updatePerc() {
            const obt = parseFloat(obtInput.value) || 0;
            const tot = parseFloat(totInput.value) || 0;
            if (tot > 0) {
                if (tot <= 10) { // Assume CGPA
                    percInput.value = obt.toFixed(2) + ' CGPA';
                    typeInput.value = 'CGPA';
                } else {
                    percInput.value = ((obt / tot) * 100).toFixed(2) + '%';
                    typeInput.value = 'Marks';
                }
            } else {
                percInput.value = '';
            }
        }
        obtInput.addEventListener('input', updatePerc);
        totInput.addEventListener('input', updatePerc);
    }

    // Experience currently working toggle
    const currentCheck = document.getElementById('is_current');
    const toDateWrapper = document.getElementById('to_date_wrapper');
    if (currentCheck && toDateWrapper) {
        currentCheck.addEventListener('change', function() {
            if (this.checked) {
                toDateWrapper.classList.add('d-none');
                toDateWrapper.querySelector('input').value = '';
            } else {
                toDateWrapper.classList.remove('d-none');
            }
        });
    }

    // Show loading on form submissions
    document.querySelectorAll('form').forEach(form => {
        if (!form.classList.contains('no-spinner')) {
            form.addEventListener('submit', function() {
                window.showSpinners();
            });
        }
    });
});

    window.ajaxDelete = function(url, elementId, spinnerId) {
        if (!confirm('Are you sure you want to delete this record?')) return;
        
        const spinner = document.getElementById(spinnerId);
        if (spinner) spinner.classList.remove('d-none');
        if (window.showSpinners) window.showSpinners();
        
        fetch(url, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json' 
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (document.getElementById(elementId)) document.getElementById(elementId).remove();
                location.reload();
            } else { alert('Error: ' + data.message); }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred.');
        })
        .finally(() => {
            if (spinner) spinner.classList.add('d-none');
            if (window.stopSpinners) window.stopSpinners();
        });
    };

    let cropper;
    const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
    const cropperImage = document.getElementById('cropperImage');

    window.initCropper = function(src) {
        cropperImage.src = src;
        cropperModal.show();
        
        if (cropper) cropper.destroy();
        
        // Wait for modal to show before initializing cropper
        document.getElementById('cropperModal').addEventListener('shown.bs.modal', function () {
            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 2,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }, { once: true });
    };

    document.getElementById('cropButton').addEventListener('click', function() {
        this.blur(); // Prevent aria-hidden focus error
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
        });
        
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            document.getElementById('photo_preview').src = url;
            
            // We can't directly set files on input, so we'll store the blob and use it on form submit
            window.croppedPhotoBlob = blob;
            
            cropperModal.hide();
        }, 'image/jpeg');
    });

    window.showSpinners = function() {
        const s1 = document.getElementById('global-submit-spinner');
        if (s1) s1.classList.remove('d-none');
        const s2 = document.getElementById('wizard-loading');
        if (s2) s2.style.display = 'flex';
    };

    window.stopSpinners = function() {
        const s1 = document.getElementById('global-submit-spinner');
        if (s1) s1.classList.add('d-none');
        const s2 = document.getElementById('wizard-loading');
        if (s2) s2.style.display = 'none';
    };
</script>
@endpush
@endsection

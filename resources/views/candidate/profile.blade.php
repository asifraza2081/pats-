@extends('layouts.dashboard')

@section('page-title', 'Candidate Profile Wizard')

@section('extra-css')
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
@endsection

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
                @foreach(['1' => 'Personal Bio', '2' => 'Academic History', '3' => 'Work Experience', '4' => 'Documents'] as $s => $label)
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
            <!-- STEP 1: PERSONAL BIO -->
            @if($step == 1)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-user-check me-2 fs-1 text-primary"></i> Step 1: Personal Information</h3>
                </div>
                <form method="POST" action="{{ route('candidate.profile.update.bio') }}" id="step1Form">
                    @csrf @method('PUT')
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-pats-primary fw-bold">CNIC Number</label>
                                <input type="text" name="cnic" id="cnic_mask" class="form-control bg-light" value="{{ old('cnic', auth()->user()->cnic) }}" placeholder="XXXXX-XXXXXXX-X" {{ ($candidate->profile_locked && $status['total_percent'] == 100) ? 'readonly' : '' }}>
                                @error('cnic') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required text-pats-primary fw-bold">Father's Name</label>
                                <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $candidate->father_name) }}" {{ ($candidate->profile_locked && $status['total_percent'] == 100) ? 'readonly' : '' }} required>
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
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-ghost-secondary rounded-pill px-4" disabled>PREVIOUS</button>
                        <button type="submit" class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg">
                            SAVE & PROCEED TO ACADEMICS <i class="ti ti-arrow-right-bar ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- STEP 2: ACADEMIC RECORDS -->
            @if($step == 2)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-school me-2 fs-1"></i> Step 2: Academic History</h3>
                    <button type="button" class="btn bg-white text-indigo btn-sm fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addEduForm">
                        <i class="ti ti-plus me-1"></i> ADD DEGREE
                    </button>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="collapse mb-5" id="addEduForm">
                        <div class="card card-body bg-light border-dashed p-4 rounded-4">
                            <form id="ajaxEduForm" action="{{ route('candidate.education.store') }}" class="no-spinner">
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
                                    <div class="col-md-5 d-flex align-items-end">
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
                        <a href="{{ route('candidate.profile.show', ['step' => 1]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <a href="{{ $status['step2']['success'] ? route('candidate.profile.show', ['step' => 3]) : '#' }}" 
                        class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg {{ !$status['step2']['success'] ? 'disabled opacity-50' : '' }}">
                            PROCEED TO WORK EXPERIENCE <i class="ti ti-arrow-right-bar ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- STEP 3: WORK EXPERIENCE -->
            @if($step == 3)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-briefcase me-2 fs-1"></i> Step 3: Work Experience</h3>
                    <button type="button" class="btn bg-white text-teal btn-sm fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="collapse" data-bs-target="#addExpForm">
                        <i class="ti ti-plus me-1"></i> ADD EXPERIENCE
                    </button>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="collapse mb-5" id="addExpForm">
                        <div class="card card-body bg-light border-dashed p-4 rounded-4">
                            <form id="ajaxExpForm" action="{{ route('candidate.experience.store') }}" class="no-spinner">
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
                                Optional: Add your work history. You can click "Next" if you have no experience.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center">
                    <div class="text-muted small mb-3 text-teal">No work experience? No problem. It's optional for many positions.</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('candidate.profile.show', ['step' => 2]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <a href="{{ route('candidate.profile.show', ['step' => 4]) }}" class="btn btn-next-step px-5 py-3 rounded-pill fw-black shadow-lg">
                            PROCEED TO DOCUMENTS <i class="ti ti-arrow-right-bar ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- STEP 4: DOCUMENTS -->
            @if($step == 4)
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInRight">
                <div class="card-header border-0 py-4 px-5 text-white bg-pats-primary" style="background: linear-gradient(135deg, #be185d 0%, #831843 100%) !important;">
                    <h3 class="card-title text-white m-0 fw-black fs-2"><i class="ti ti-file-text me-2 fs-1"></i> Step 4: Verification Documents</h3>
                </div>
                <form method="POST" action="{{ route('candidate.profile.update.docs') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-5">
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
                            <!-- CNIC Upload -->
                            <div class="col-md-6 text-center">
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
                        <a href="{{ route('candidate.profile.show', ['step' => 3]) }}" class="btn btn-ghost-secondary rounded-pill px-4">PREVIOUS</a>
                        <button type="submit" class="btn btn-pink px-5 py-3 rounded-pill fw-black shadow-lg text-white" style="background: linear-gradient(135deg, #be185d 0%, #831843 100%) !important;">
                            SAVE & FINISH PROFILE <i class="ti ti-circle-check ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const dbCities = @json($cities);
const districts = {};

dbCities.forEach(city => {
    if (!districts[city.province]) districts[city.province] = [];
    districts[city.province].push({ id: city.id, name: city.name });
});

function previewFile(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
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

    function handleAjaxForm(formId, targetListId, spinnerId, emptyId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
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
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (empty) empty.remove();
                    list.insertAdjacentHTML('afterbegin', data.html);
                    form.reset();
                    const coll = bootstrap.Collapse.getInstance(form.closest('.collapse'));
                    if (coll) coll.hide();
                    location.reload(); // Refresh to update progress sidebar
                } else { alert('Error: ' + data.message); }
            })
            .catch(err => alert('An error occurred.'))
            .finally(() => {
                spinner.classList.add('d-none');
                form.querySelectorAll('button').forEach(b => b.disabled = false);
            });
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .border-dashed { border: 2px dashed #cbd5e1 !important; }
    .bg-pats-primary { background: linear-gradient(135deg, #0f172a, #1e293b); }
    .list-group-item.active { border-radius: 12px; margin: 4px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-indigo { background: #4f46e5; color: #fff; }
    .btn-teal { background: #0f766e; color: #fff; }
    .btn-pink { background: #be185d; color: #fff; }
    .cursor-not-allowed { cursor: not-allowed !important; }
    .fw-black { font-weight: 850; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loading = document.getElementById('wizard-loading');
        
        // Show loading on form submissions
        document.querySelectorAll('form').forEach(form => {
            if (!form.classList.contains('no-spinner')) {
                form.addEventListener('submit', function() {
                    loading.style.display = 'flex';
                });
            }
        });
    });
</script>
@endpush
@endsection

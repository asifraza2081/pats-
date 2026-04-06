@extends('layouts.dashboard')

@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    <!-- Sidebar -->
    <div class="col-lg-3">
        <div class="card mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <span class="avatar avatar-xl rounded-circle border-4 border-white shadow-sm" style="background-image: url('{{ $candidate->photo_path ? Storage::url($candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}'); width: 100px; height: 100px;"></span>
                </div>
                <h3 class="m-0 mb-1 fw-black">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                <div class="text-muted small mb-3"><i class="ti ti-id-badge me-1"></i> {{ auth()->user()->cnic }}</div>
                
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-pats-primary">Profile Integrity</span>
                    <span class="badge {{ $candidate->completionPercent() < 100 ? 'bg-warning-lt text-warning' : 'bg-success-lt text-success' }}">{{ $candidate->completionPercent() }}%</span>
                </div>
                <div class="progress progress-sm mb-0 rounded-pill">
                    <div class="progress-bar {{ $candidate->completionPercent() < 100 ? 'bg-warning' : 'bg-success' }}" style="width: {{ $candidate->completionPercent() }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 20px;">
            <div class="list-group list-group-flush">
                <a href="#personal" class="list-group-item list-group-item-action fw-black py-3 border-0"><i class="ti ti-user-check me-2 text-primary"></i> General Profile</a>
                <a href="#education" class="list-group-item list-group-item-action fw-black py-3 border-0"><i class="ti ti-school me-2 text-indigo"></i> Education History</a>
                <a href="#experience" class="list-group-item list-group-item-action fw-black py-3 border-0"><i class="ti ti-briefcase me-2 text-secondary"></i> Work Experience</a>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="col-lg-9">
        @if($candidate->profile_locked)
        <div class="alert alert-important alert-info alert-dismissible animate__animated animate__fadeIn" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-lock fs-2 me-3"></i></div>
                <div>Your profile is permanently locked. Core fields cannot be changed because you have already submitted active job applications.</div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data" id="profileSaveForm">
            @csrf @method('PUT')
            
            <!-- Personal Info & Address Consolidated -->
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" id="personal">
                <div class="card-header bg-pats-primary py-3">
                    <h3 class="card-title text-white m-0 fw-black"><i class="ti ti-user-check me-2"></i> General Profile Information</h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <!-- Profile Header -->
                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center gap-4 bg-light p-4 rounded-4 border">
                                <div class="position-relative">
                                    <span class="avatar avatar-xl rounded-circle border-3 border-white shadow-sm" id="profile_photo_preview" style="background-image: url('{{ $candidate->photo_path ? Storage::url($candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}'); width: 100px; height: 100px;"></span>
                                    <label class="btn btn-icon btn-sm btn-pats position-absolute bottom-0 end-0 rounded-circle shadow-sm">
                                        <i class="ti ti-camera"></i>
                                        <input type="file" name="photo" class="d-none" accept="image/*" onchange="previewPhoto(this)">
                                    </label>
                                </div>
                                <div>
                                    <h2 class="fw-black m-0 fs-1">{{ auth()->user()->full_name }}</h2>
                                    <p class="text-muted m-0"><i class="ti ti-id me-1"></i> {{ auth()->user()->cnic }}</p>
                                    <p class="text-muted m-0 small"><i class="ti ti-mail me-1"></i> {{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Core Details -->
                        <div class="col-md-6">
                            <label class="form-label text-pats-primary fw-bold">CNIC Number</label>
                            <input type="text" name="cnic" id="cnic_mask" class="form-control bg-light" value="{{ old('cnic', auth()->user()->cnic) }}" placeholder="XXXXX-XXXXXXX-X" {{ $candidate->profile_locked ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required text-pats-primary fw-bold">Father's Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $candidate->father_name) }}" {{ $candidate->profile_locked ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $candidate->dob?->format('Y-m-d')) }}" 
                                   min="1900-01-01" max="{{ now()->subYears(16)->format('Y-m-d') }}"
                                   {{ $candidate->profile_locked ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Gender</label>
                            <select name="gender" class="form-select" {{ $candidate->profile_locked ? 'disabled' : '' }} required>
                                <option value="Male" {{ old('gender', $candidate->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $candidate->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Marital Status</label>
                            <select name="marital_status" class="form-select" required>
                                @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $m)
                                    <option value="{{ $m }}" {{ old('marital_status', $candidate->marital_status) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Religion</label>
                            <select name="religion" class="form-select" required>
                                @foreach(['Islam', 'Christianity', 'Hinduism', 'Sikhism', 'Other'] as $r)
                                    <option value="{{ $r }}" {{ old('religion', $candidate->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Current Occupation</label>
                            <select name="current_occupation" class="form-select">
                                <option value="">— Select Occupation —</option>
                                @foreach(['Student', 'Private Employee', 'Government Employee', 'Self-Employed', 'Unemployed', 'Other'] as $occ)
                                    <option value="{{ $occ }}" {{ old('current_occupation', $candidate->current_occupation) == $occ ? 'selected' : '' }}>{{ $occ }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alternative Phone</label>
                            <input type="text" name="alternate_phone" id="alt_phone_mask" class="form-control" value="{{ old('alternate_phone', $candidate->alternate_phone) }}" placeholder="03XX-XXXXXXX">
                        </div>

                        <!-- Address Section -->
                        <div class="col-12 mt-5 border-top pt-4">
                            <h4 class="text-pats-primary m-0 mb-3 fw-black"><i class="ti ti-map-pin me-2"></i> Address & Domicile</h4>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">Province of Domicile</label>
                            <select name="province_of_domicile" id="domicile_province" class="form-select" required>
                                <option value="">— Select Province —</option>
                                @foreach(['Punjab', 'Sindh', 'KPK', 'Balochistan', 'Federal', 'AJK', 'Gilgit Baltistan'] as $prov)
                                    <option value="{{ $prov }}" {{ old('province_of_domicile', $candidate->province_of_domicile) == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">District of Domicile</label>
                            <select name="district_of_domicile" id="domicile_district" class="form-select" required>
                                <option value="{{ $candidate->district_of_domicile }}">{{ $candidate->district_of_domicile ?? '— Select District —' }}</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required">Permanent Address</label>
                            <textarea name="permanent_address" class="form-control" rows="2" placeholder="Street, Village/Area, House No." required>{{ old('permanent_address', $candidate->permanent_address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-check form-switch cursor-pointer bg-light p-3 rounded-2">
                                <input class="form-check-input ms-0 me-3" type="checkbox" name="same_postal_address" value="1" id="samePostal" {{ old('same_postal_address', $candidate->same_postal_address) ? 'checked' : '' }} onchange="togglePostal(this.checked)">
                                <span class="form-check-label fw-bold small">Postal address is exactly the same as Permanent Address</span>
                            </label>
                        </div>
                        <div class="col-12" id="postalField" style="display: {{ old('same_postal_address', $candidate->same_postal_address) ? 'none' : 'block' }}">
                            <label class="form-label required">Postal Address</label>
                            <textarea name="postal_address" id="postal_address_input" class="form-control" rows="2" placeholder="Active mailing address" {{ old('same_postal_address', $candidate->same_postal_address) ? '' : 'required' }}>{{ old('postal_address', $candidate->postal_address) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center border-0">
                    <button type="submit" class="btn btn-pats px-5 py-3 rounded-pill fw-black fs-3 shadow-sm" id="btnMainSave">
                        <i class="ti ti-device-floppy me-2"></i> SAVE ALL PROFILE INFORMATION
                    </button>
                    <p class="text-muted small mt-2 mb-0">Changes will be updated across your entire profile instantly.</p>
                </div>
            </div>
        </form>

        <!-- Education History (Collections - AJAX) -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" id="education">
            <div class="card-header bg-indigo py-3 d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white m-0 fw-black"><i class="ti ti-school me-2"></i> Education Records</h3>
                <button type="button" class="btn btn-white btn-sm fw-bold rounded-pill" data-bs-toggle="collapse" data-bs-target="#addEduForm">
                    <i class="ti ti-plus me-1"></i> ADD EDUCATION
                </button>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="collapse mb-5" id="addEduForm">
                    <div class="card card-body bg-light border-dashed p-4 rounded-4">
                        <form id="ajaxEduForm" action="{{ route('candidate.education.store') }}">
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
                                <div class="col-md-4"><label class="form-label small fw-bold required">Degree Name</label><input type="text" name="degree_name" class="form-control" placeholder="e.g. BSCS" required></div>
                                <div class="col-md-5"><label class="form-label small fw-bold">Institution</label><input type="text" name="institution" class="form-control" placeholder="Board / University"></div>
                                <div class="col-md-3"><label class="form-label small fw-bold">Subject / Major</label><input type="text" name="subject_major" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label small fw-bold">Pass Year</label><input type="number" name="passing_year" class="form-control" min="1970" max="{{ date('Y') }}"></div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold required">Type</label>
                                    <select name="marks_type" class="form-select" required>
                                        <option value="Marks">Marks</option>
                                        <option value="CGPA">CGPA</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><label class="form-label small fw-bold">Obtained</label><input type="number" name="obtained_marks" class="form-control" step="0.01"></div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-indigo w-100 py-2 rounded-pill fw-bold">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" id="eduSpinner"></span>
                                        SAVE RECORD
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="education_list" class="divide-y">
                    @forelse($education as $edu)
                        @include('candidate.partials._education_row', ['edu' => $edu, 'candidate' => $candidate])
                    @empty
                        <div class="text-center py-5 text-muted empty-edu">
                            <i class="ti ti-school fs-0 opacity-20 d-block mb-3"></i>
                            No education records added yet. Add your highest qualification first.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Experience (Collections - AJAX) -->
        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" id="experience">
            <div class="card-header bg-secondary py-3 d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white m-0 fw-black"><i class="ti ti-briefcase me-2"></i> Work Experience</h3>
                <button type="button" class="btn btn-white btn-sm fw-bold rounded-pill" data-bs-toggle="collapse" data-bs-target="#addExpForm">
                    <i class="ti ti-plus me-1"></i> ADD EXPERIENCE
                </button>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="collapse mb-5" id="addExpForm">
                    <div class="card card-body bg-light border-dashed p-4 rounded-4">
                        <form id="ajaxExpForm" action="{{ route('candidate.experience.store') }}">
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
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Currently working?</label>
                                    <label class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="is_current" value="1" onchange="document.getElementById('expToDateWrap').style.display = this.checked ? 'none' : 'block'">
                                        <span class="form-check-label small">Yes, I still work here</span>
                                    </label>
                                </div>
                                <div class="col-md-3" id="expToDateWrap"><label class="form-label small fw-bold">To Date</label><input type="date" name="to_date" class="form-control"></div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-secondary w-100 py-2 rounded-pill fw-bold">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" id="expSpinner"></span>
                                        SAVE RECORD
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="experience_list" class="divide-y">
                    @forelse($experience as $exp)
                        @include('candidate.partials._experience_row', ['exp' => $exp, 'candidate' => $candidate])
                    @empty
                        <div class="text-center py-5 text-muted empty-exp">
                            <i class="ti ti-briefcase fs-0 opacity-20 d-block mb-3"></i>
                            Optional: Add relevant work experience for field-based jobs.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const districts = {
    'Punjab': ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi', 'Gujranwala', 'Sargodha', 'Bahawalpur', 'Sialkot', 'Sheikhupura', 'Rahim Yar Khan', 'Jhang', 'Dera Ghazi Khan', 'Gujrat', 'Sahiwal', 'Sargodha', 'Kasur', 'Chiniot', 'Okara'],
    'Sindh': ['Karachi', 'Hyderabad', 'Sukkur', 'Larkana', 'Nawabshah', 'Mirpur Khas', 'Jacobabad', 'Shikarpur', 'Khairpur', 'Thatta'],
    'KPK': ['Peshawar', 'Mardan', 'Mingora', 'Kohat', 'Abbottabad', 'Nowshera', 'Swabi', 'Dera Ismail Khan', 'Charsadda', 'Mansehra'],
    'Balochistan': ['Quetta', 'Turbat', 'Khuzdar', 'Hub', 'Chaman', 'Gwadar', 'Jafarabad', 'Usta Mohammad'],
    'Federal': ['Islamabad'],
    'AJK': ['Muzaffarabad', 'Mirpur', 'Rawalakot', 'Bagh', 'Kotli'],
    'Gilgit Baltistan': ['Gilgit', 'Skardu', 'Hunza', 'Diamer']
};

function togglePostal(checked) {
    const field = document.getElementById('postalField');
    const input = document.getElementById('postal_address_input');
    if (checked) {
        field.style.display = 'none';
        input.removeAttribute('required');
    } else {
        field.style.display = 'block';
        input.setAttribute('required', 'required');
    }
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile_photo_preview').style.backgroundImage = 'url(' + e.target.result + ')';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Province -> District Dropdown
    const provinceSelect = document.getElementById('domicile_province');
    const districtSelect = document.getElementById('domicile_district');
    
    provinceSelect.addEventListener('change', function() {
        const province = this.value;
        districtSelect.innerHTML = '<option value="">— Select District —</option>';
        if (districts[province]) {
            districts[province].forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                districtSelect.appendChild(opt);
            });
        }
    });

    // 2. Input Masking
    const cnicInput = document.getElementById('cnic_mask');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }

    const phoneInput = document.getElementById('alt_phone_mask');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });
    }

    // 3. AJAX Forms
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

            const formData = new FormData(this);
            fetch(this.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (empty) empty.remove();
                    list.insertAdjacentHTML('afterbegin', data.html);
                    form.reset();
                    bootstrap.Collapse.getInstance(form.closest('.collapse')).hide();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('An error occurred. Please try again.'))
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
    .border-dashed { border: 2px dashed #e6e7e9 !important; }
    .bg-pats-primary { background: linear-gradient(135deg, #0f172a, #1e293b); }
    .shadow-teal-30 { box-shadow: 0 10px 30px rgba(20, 184, 166, 0.3); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .btn-pats { background: #14b8a6; color: #fff; border: none; }
    .btn-pats:hover { background: #0d9488; color: #fff; }
</style>
@endpush
@endsection

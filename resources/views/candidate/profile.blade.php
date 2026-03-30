@extends('layouts.dashboard')

@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    <!-- Sidebar -->
    <div class="col-lg-3">
        <div class="card mb-3">
            <div class="card-body text-center">
                <span class="avatar avatar-xl mb-3 rounded-circle border" style="background-image: url('{{ $candidate->photo_path ? Storage::url($candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name) }}')"></span>
                <h3 class="m-0 mb-1 d-block">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                <div class="text-muted mb-3">{{ auth()->user()->cnic }}</div>
                
                <div class="small fw-semibold mb-2">Profile Completion</div>
                <div class="progress progress-sm mb-1">
                    <div class="progress-bar {{ $candidate->completionPercent() < 100 ? 'bg-warning' : 'bg-success' }}" style="width: {{ $candidate->completionPercent() }}%"></div>
                </div>
                <div class="text-muted small">{{ $candidate->completionPercent() }}%</div>
            </div>
        </div>
        
        <div class="card">
            <div class="list-group list-group-flush" data-bs-spy="scroll">
                <a href="#personal" class="list-group-item list-group-item-action fw-semibold border-0"><i class="ti ti-user me-2"></i> Personal Info</a>
                <a href="#address" class="list-group-item list-group-item-action fw-semibold border-0"><i class="ti ti-map-pin me-2"></i> Address & Domicile</a>
                <a href="#documents" class="list-group-item list-group-item-action fw-semibold border-0"><i class="ti ti-file me-2"></i> Documents</a>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="col-lg-9">
        @if($candidate->profile_locked)
        <div class="alert alert-important alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-lock fs-2 me-3"></i></div>
                <div>Your profile is permanently locked. Core fields cannot be changed because you have already submitted active job applications.</div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Personal Info -->
            <div class="card mb-4" id="personal">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title"><i class="ti ti-user-check text-primary me-2"></i> Personal Information</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->first_name }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->last_name }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CNIC (XXXXX-XXXXXXX-X)</label>
                            <input type="text" name="cnic" class="form-control" value="{{ old('cnic', auth()->user()->cnic) }}" placeholder="XXXXX-XXXXXXX-X" maxlength="15" {{ $candidate->profile_locked ? 'readonly' : '' }}>
                            @if(!auth()->user()->cnic)
                                <div class="text-warning small mt-1">CNIC is required for most job applications.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Father's Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $candidate->father_name) }}" {{ $candidate->profile_locked ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $candidate->dob?->format('Y-m-d')) }}" 
                                   min="1900-01-01" max="{{ now()->subYears(16)->format('Y-m-d') }}"
                                   {{ $candidate->profile_locked ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">Gender</label>
                            @if($candidate->profile_locked)
                                <input type="text" class="form-control" name="gender" value="{{ $candidate->gender }}" readonly>
                            @else
                                <select name="gender" class="form-select">
                                    <option value="Male" {{ old('gender', $candidate->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $candidate->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Marital Status</label>
                            <select name="marital_status" class="form-select">
                                @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $m)
                                    <option value="{{ $m }}" {{ old('marital_status', $candidate->marital_status) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Religion</label>
                            <select name="religion" class="form-select">
                                @foreach(['Islam', 'Christianity', 'Hinduism', 'Sikhism', 'Other'] as $r)
                                    <option value="{{ $r }}" {{ old('religion', $candidate->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $candidate->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Occupation</label>
                            <input type="text" name="current_occupation" class="form-control" value="{{ old('current_occupation', $candidate->current_occupation) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alternate Phone</label>
                            <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $candidate->alternate_phone) }}" placeholder="Optional">
                        </div>
                        <div class="col-12 mt-3 border-top pt-3">
                            <label class="form-check form-switch cursor-pointer">
                                <input class="form-check-input" type="checkbox" name="disability" value="1" id="disabilityCheck" {{ old('disability', $candidate->disability) ? 'checked' : '' }} onchange="document.getElementById('disabilityTypeWrapper').style.display = this.checked ? 'block' : 'none'">
                                <span class="form-check-label fw-semibold">Are you a person with a disability?</span>
                            </label>
                        </div>
                        <div class="col-md-12" id="disabilityTypeWrapper" style="display: {{ old('disability', $candidate->disability) ? 'block' : 'none' }}">
                            <label class="form-label">Disability Type</label>
                            <input type="text" name="disability_type" class="form-control" value="{{ old('disability_type', $candidate->disability_type) }}" placeholder="Describe disability (e.g. Visual, Hearing, Physical)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address & Domicile -->
            <div class="card mb-4" id="address">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title"><i class="ti ti-map-pin text-success me-2"></i> Address & Domicile</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">City of Domicile</label>
                            @if($candidate->profile_locked)
                                <input type="hidden" name="domicile_city_id" value="{{ $candidate->domicile_city_id }}">
                                <input type="text" class="form-control" value="{{ $candidate->domicileCity?->name }} ({{ $candidate->domicileCity?->province }})" readonly disabled>
                            @else
                                <select name="domicile_city_id" class="form-select" required>
                                    <option value="">— Select Domicile City —</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->id }}" {{ old('domicile_city_id', $candidate->domicile_city_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->province }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Current Postal City</label>
                            <select name="address_city_id" class="form-select" required>
                                <option value="">— Select Postal City —</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}" {{ old('address_city_id', $candidate->address_city_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->province }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label required">Permanent Address</label>
                            <textarea name="permanent_address" class="form-control" rows="2" required>{{ old('permanent_address', $candidate->permanent_address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="same_postal_address" value="1" id="samePostal" {{ old('same_postal_address', $candidate->same_postal_address) ? 'checked' : '' }} onchange="togglePostal(this.checked)">
                                <span class="form-check-label">Postal address is exactly the same as Permanent Address</span>
                            </label>
                        </div>
                        <div class="col-12" id="postalField" style="display: {{ old('same_postal_address', $candidate->same_postal_address) ? 'none' : 'block' }}">
                            <label class="form-label required">Postal Address</label>
                            <textarea name="postal_address" id="postal_address_input" class="form-control" rows="2" {{ old('same_postal_address', $candidate->same_postal_address) ? '' : 'required' }}>{{ old('postal_address', $candidate->postal_address) }}</textarea>
                        </div>

                        <script>
                            function togglePostal(isSame) {
                                const field = document.getElementById('postalField');
                                const input = document.getElementById('postal_address_input');
                                if (isSame) {
                                    field.style.display = 'none';
                                    input.removeAttribute('required');
                                } else {
                                    field.style.display = 'block';
                                    input.setAttribute('required', 'required');
                                }
                            }
                        </script>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4" id="documents">
                <div class="card-header border-0 pb-0">
                    <h3 class="card-title"><i class="ti ti-file-upload text-warning me-2"></i> Documents & Uploads</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 border-end">
                            <label class="form-label fw-bold">Passport-size Photo</label>
                            @if($candidate->photo_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/'.$candidate->photo_path) }}" height="80" class="rounded border shadow-sm" alt="Profile Photo">
                                    <div class="text-success mt-1 small"><i class="ti ti-check"></i> Uploaded successfully</div>
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png">
                            <div class="text-muted small mt-1">Upload a clear passport-size photo. Max 5MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CNIC Front Copy</label>
                            @if($candidate->cnic_front_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/'.$candidate->cnic_front_path) }}" height="80" class="rounded border shadow-sm" alt="CNIC Front">
                                    <div class="text-success mt-1 small"><i class="ti ti-check"></i> Uploaded successfully</div>
                                </div>
                            @endif
                            <input type="file" name="cnic_copy" class="form-control" accept="image/jpeg,image/png">
                            <div class="text-muted small mt-1">Upload front side of your valid CNIC. Max 5MB.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent py-4 text-end">
                    <button type="submit" class="btn btn-primary px-5"><i class="ti ti-device-floppy me-2"></i> Save Profile Data</button>
                </div>
            </div>
        </form>

        <!-- Education History -->
        <div class="card mb-4" id="education">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="ti ti-school text-info me-2"></i> Education History</h3>
                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="collapse" data-bs-target="#addEduForm"><i class="ti ti-plus me-1"></i> Add Record</button>
            </div>
            <div class="card-body">
                <div class="collapse mb-4" id="addEduForm">
                    <div class="card card-body bg-light border-0">
                        <form method="POST" action="{{ route('candidate.education.store') }}">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">Level *</label>
                                    <select name="degree_level" class="form-select form-select-sm" required>
                                        @foreach([1=>'Matric',2=>'Intermediate',3=>"Bachelor's",4=>"Master's",5=>'M.Phil',6=>'PhD'] as $v => $l)
                                            <option value="{{ $v }}">{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="form-label small required">Degree Name</label><input type="text" name="degree_name" class="form-control form-control-sm" required></div>
                                <div class="col-md-3"><label class="form-label small">Subject/Major</label><input type="text" name="subject_major" class="form-control form-control-sm"></div>
                                <div class="col-md-3"><label class="form-label small">Institution</label><input type="text" name="institution" class="form-control form-control-sm"></div>
                                <div class="col-md-2"><label class="form-label small">Pass Year</label><input type="number" name="passing_year" class="form-control form-control-sm" min="1970" max="{{ date('Y') }}"></div>
                                <div class="col-md-2">
                                    <label class="form-label small required">Marks Type</label>
                                    <select name="marks_type" class="form-select form-select-sm" required>
                                        <option value="Marks">Marks</option>
                                        <option value="CGPA">CGPA</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><label class="form-label small">Obtained</label><input type="number" name="obtained_marks" class="form-control form-control-sm" step="0.01"></div>
                                <div class="col-md-2"><label class="form-label small">Total / Max</label><input type="number" name="total_marks" class="form-control form-control-sm" step="0.01"></div>
                                <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-info w-100"><i class="ti ti-check me-1"></i> Save Education</button></div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="divide-y">
                    @forelse($education as $edu)
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-bold">{{ $edu->degree_name }} <span class="badge bg-blue-lt ms-2">{{ \App\Models\EducationHistory::$levelLabels[$edu->degree_level] ?? '' }}</span></div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-book me-1"></i> {{ $edu->subject_major ?? 'N/A' }} &bull; 
                                <i class="ti ti-building-bank me-1"></i> {{ $edu->institution ?? 'N/A' }} &bull; 
                                <i class="ti ti-calendar me-1"></i> {{ $edu->passing_year ?? 'N/A' }}
                            </div>
                            <div class="text-success small fw-semibold mt-1"><i class="ti ti-chart-bar me-1"></i> {{ $edu->percentage_display }}</div>
                        </div>
                        @if(!$candidate->profile_locked)
                        <form method="POST" action="{{ route('candidate.education.destroy', $edu) }}" onsubmit="return confirm('Remove this record?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-action text-danger" title="Remove"><i class="ti ti-trash"></i></button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted text-center py-3">No education records added yet. Minimum 1 required.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Work Experience -->
        <div class="card mb-4" id="experience">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="ti ti-briefcase text-secondary me-2"></i> Work Experience</h3>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#addExpForm"><i class="ti ti-plus me-1"></i> Add Record</button>
            </div>
            <div class="card-body">
                <div class="collapse mb-4" id="addExpForm">
                    <div class="card card-body bg-light border-0">
                        <form method="POST" action="{{ route('candidate.experience.store') }}">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="form-label small required">Sector</label>
                                    <select name="job_type" class="form-select form-select-sm" required>
                                        <option value="Public">Public</option>
                                        <option value="Private">Private</option>
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="form-label small required">Organisation</label><input type="text" name="organization_name" class="form-control form-control-sm" required></div>
                                <div class="col-md-3"><label class="form-label small required">Designation</label><input type="text" name="designation" class="form-control form-control-sm" required></div>
                                <div class="col-md-3">
                                    <label class="form-label small">Current Job?</label>
                                    <label class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="isCurrent" onchange="document.getElementById('toDateWrap').style.display = this.checked ? 'none' : 'block'">
                                        <span class="form-check-label small">Currently working</span>
                                    </label>
                                </div>
                                <div class="col-md-2"><label class="form-label small required">Start Date</label><input type="date" name="from_date" class="form-control form-control-sm" required min="1950-01-01" max="{{ now()->toDateString() }}"></div>
                                <div class="col-md-2" id="toDateWrap"><label class="form-label small">End Date</label><input type="date" name="to_date" class="form-control form-control-sm" min="1950-01-01" max="{{ now()->toDateString() }}"></div>
                                <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-secondary w-100"><i class="ti ti-check me-1"></i> Save Experience</button></div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="divide-y">
                    @forelse($experience as $exp)
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-bold">{{ $exp->designation }} <span class="badge bg-secondary-lt ms-2">{{ $exp->job_type }}</span></div>
                            <div class="text-muted small mt-1"><i class="ti ti-building me-1"></i> {{ $exp->organization_name }}</div>
                            <div class="text-muted small mt-1">
                                <i class="ti ti-calendar me-1"></i> {{ $exp->from_date->format('M Y') }} &mdash; {{ $exp->to_date ? $exp->to_date->format('M Y') : 'Present' }} 
                                <span class="ms-2 fw-semibold text-secondary">({{ $exp->duration_label }})</span>
                            </div>
                        </div>
                        @if(!$candidate->profile_locked)
                        <form method="POST" action="{{ route('candidate.experience.destroy', $exp) }}" onsubmit="return confirm('Remove this record?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-action text-danger" title="Remove"><i class="ti ti-trash"></i></button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted text-center py-3">No work experience added yet. Required for certain posts.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'My Profile — PATS')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-3">
                @if($candidate->photo_path)
                    <img src="{{ asset('storage/'.$candidate->photo_path) }}" class="rounded-circle mb-3" width="90" height="90" style="object-fit:cover;border:3px solid #e9ecef">
                @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width:90px;height:90px;font-size:2rem;color:#aaa"><i class="bi bi-person-fill"></i></div>
                @endif
                <div class="fw-bold">{{ auth()->user()->full_name }}</div>
                <div class="small text-muted">{{ auth()->user()->cnic }}</div>
                <hr>
                <div class="small mb-1 fw-semibold">Profile Completion</div>
                <div class="progress mb-1" style="height:8px">
                    <div class="progress-bar {{ $candidate->completionPercent() < 100 ? 'bg-warning' : 'bg-success' }}" style="width:{{ $candidate->completionPercent() }}%"></div>
                </div>
                <div class="small text-muted">{{ $candidate->completionPercent() }}%</div>
            </div>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="list-group list-group-flush rounded-4">
                    <a href="#personal" class="list-group-item list-group-item-action small fw-semibold"><i class="bi bi-person me-2"></i>Personal Info</a>
                    <a href="#address" class="list-group-item list-group-item-action small"><i class="bi bi-geo-alt me-2"></i>Address & Domicile</a>
                    <a href="#education" class="list-group-item list-group-item-action small"><i class="bi bi-mortarboard me-2"></i>Education</a>
                    <a href="#experience" class="list-group-item list-group-item-action small"><i class="bi bi-briefcase me-2"></i>Work Experience</a>
                    <a href="#uploads" class="list-group-item list-group-item-action small"><i class="bi bi-paperclip me-2"></i>Documents</a>
                </div>
            </div>
        </div>

        {{-- Main Form --}}
        <div class="col-lg-9">
            @if($candidate->profile_locked)
            <div class="alert alert-warning mb-4"><i class="bi bi-lock-fill me-2"></i>Profile is locked. Some fields cannot be changed after submitting your first application.</div>
            @endif

            <form method="POST" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @if($errors->any())<div class="alert alert-danger small"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif

                {{-- Personal Info --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4" id="personal">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->first_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->last_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Father's Name *</label>
                                <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name',$candidate->father_name) }}" required>
                                @error('father_name')<div class="invalid-feedback">{{$message}}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Date of Birth *</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob',$candidate->dob?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    @foreach(['Male','Female'] as $g)
                                    <option value="{{$g}}" {{ old('gender',$candidate->gender)===$g?'selected':'' }}>{{$g}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Marital Status *</label>
                                <select name="marital_status" class="form-select" required>
                                    @foreach(['Single','Married','Divorced','Widowed'] as $m)
                                    <option value="{{$m}}" {{ old('marital_status',$candidate->marital_status)===$m?'selected':'' }}>{{$m}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Religion *</label>
                                <select name="religion" class="form-select" required>
                                    @foreach(['Islam','Christianity','Hinduism','Sikhism','Other'] as $r)
                                    <option value="{{$r}}" {{ old('religion',$candidate->religion)===$r?'selected':'' }}>{{$r}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Blood Group</label>
                                <select name="blood_group" class="form-select">
                                    <option value="">— Select —</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{$bg}}" {{ old('blood_group',$candidate->blood_group)===$bg?'selected':'' }}>{{$bg}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Current Occupation</label>
                                <input type="text" name="current_occupation" class="form-control" value="{{ old('current_occupation',$candidate->current_occupation) }}" placeholder="e.g. Teacher, Government Officer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alternate Phone</label>
                                <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone',$candidate->alternate_phone) }}" placeholder="03XX-XXXXXXX">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="disability" value="1" id="disabilityCheck"
                                           {{ old('disability',$candidate->disability) ? 'checked' : '' }}
                                           onchange="document.getElementById('disabilityTypeField').style.display=this.checked?'block':'none'">
                                    <label class="form-check-label fw-semibold" for="disabilityCheck">Person with Disability</label>
                                </div>
                            </div>
                            <div class="col-md-8" id="disabilityTypeField" style="{{ old('disability',$candidate->disability) ? '' : 'display:none' }}">
                                <label class="form-label fw-semibold">Disability Type</label>
                                <input type="text" name="disability_type" class="form-control" value="{{ old('disability_type',$candidate->disability_type) }}" placeholder="e.g. Visual, Hearing, Physical">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address & Domicile --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4" id="address">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-success"></i>Address &amp; Domicile</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Province of Domicile *</label>
                                <select name="province_of_domicile" class="form-select" required>
                                    <option value="">— Select Province —</option>
                                    @foreach(['Punjab','Sindh','KPK','Balochistan','Gilgit-Baltistan','AJK','ICT'] as $p)
                                    <option value="{{$p}}" {{ old('province_of_domicile',$candidate->province_of_domicile)===$p?'selected':'' }}>{{$p}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">District of Domicile *</label>
                                <input type="text" name="district_of_domicile" class="form-control" value="{{ old('district_of_domicile',$candidate->district_of_domicile) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Permanent Address *</label>
                                <textarea name="permanent_address" class="form-control" rows="2" required>{{ old('permanent_address',$candidate->permanent_address) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="same_postal_address" value="1" id="samePostal"
                                           onchange="document.getElementById('postalField').style.display=this.checked?'none':'block'">
                                    <label class="form-check-label small" for="samePostal">Postal address same as permanent address</label>
                                </div>
                            </div>
                            <div class="col-12" id="postalField">
                                <label class="form-label fw-semibold">Postal Address *</label>
                                <textarea name="postal_address" class="form-control" rows="2" required>{{ old('postal_address',$candidate->postal_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4" id="uploads">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-2 text-warning"></i>Documents</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Passport-size Photo</label>
                                @if($candidate->photo_path)
                                <div class="mb-2"><img src="{{ asset('storage/'.$candidate->photo_path) }}" height="60" class="rounded border"></div>
                                @endif
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <div class="form-text">Max 5MB. JPEG/PNG.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">CNIC Copy (front)</label>
                                @if($candidate->cnic_front_path)
                                <div class="mb-2"><img src="{{ asset('storage/'.$candidate->cnic_front_path) }}" height="60" class="rounded border"></div>
                                @endif
                                <input type="file" name="cnic_copy" class="form-control" accept="image/*">
                                <div class="form-text">Max 5MB. JPEG/PNG.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-pats px-5 fw-semibold"><i class="bi bi-check2 me-1"></i>Save Profile</button>
            </form>

            {{-- Education --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4" id="education">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-info"></i>Education History</h6>
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#addEduForm"><i class="bi bi-plus"></i> Add</button>
                </div>
                <div class="card-body p-4">
                    <div class="collapse mb-3" id="addEduForm">
                        <div class="card card-body bg-light border-0">
                            <form method="POST" action="{{ route('candidate.education.store') }}">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Level *</label>
                                        <select name="degree_level" class="form-select form-select-sm" required>
                                            @foreach([1=>'Matric',2=>'Intermediate',3=>"Bachelor's",4=>"Master's",5=>'M.Phil',6=>'PhD'] as $v=>$l)
                                            <option value="{{$v}}">{{$l}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3"><label class="form-label small fw-semibold">Degree Name *</label><input type="text" name="degree_name" class="form-control form-control-sm" required></div>
                                    <div class="col-md-3"><label class="form-label small fw-semibold">Subject/Major</label><input type="text" name="subject_major" class="form-control form-control-sm"></div>
                                    <div class="col-md-3"><label class="form-label small fw-semibold">Institution</label><input type="text" name="institution" class="form-control form-control-sm"></div>
                                    <div class="col-md-2"><label class="form-label small fw-semibold">Pass Year</label><input type="number" name="passing_year" class="form-control form-control-sm" min="1970" max="{{ date('Y') }}"></div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Marks Type *</label>
                                        <select name="marks_type" class="form-select form-select-sm" required>
                                            <option value="Marks">Marks</option>
                                            <option value="CGPA">CGPA</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><label class="form-label small fw-semibold">Obtained</label><input type="number" name="obtained_marks" class="form-control form-control-sm" step="0.01"></div>
                                    <div class="col-md-2"><label class="form-label small fw-semibold">Total / Max</label><input type="number" name="total_marks" class="form-control form-control-sm" step="0.01"></div>
                                    <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-info text-white w-100">Add Education</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @forelse($education as $edu)
                    <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $edu->degree_name }} <span class="badge bg-light text-muted">{{ \App\Models\EducationHistory::$levelLabels[$edu->degree_level] ?? '' }}</span></div>
                            <div class="text-muted small">{{ $edu->subject_major ?? '' }}{{ $edu->institution ? ' · '.$edu->institution : '' }}{{ $edu->passing_year ? ' · '.$edu->passing_year : '' }}</div>
                            <div class="small text-success">{{ $edu->percentage_display }}</div>
                        </div>
                        @if(!$candidate->profile_locked)
                        <form method="POST" action="{{ route('candidate.education.destroy',$edu) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger px-2 py-1"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted small py-2">No education records added yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Work Experience --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4" id="experience">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-secondary"></i>Work Experience</h6>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#addExpForm"><i class="bi bi-plus"></i> Add</button>
                </div>
                <div class="card-body p-4">
                    <div class="collapse mb-3" id="addExpForm">
                        <div class="card card-body bg-light border-0">
                            <form method="POST" action="{{ route('candidate.experience.store') }}">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Sector *</label>
                                        <select name="job_type" class="form-select form-select-sm" required>
                                            <option value="Public">Public</option>
                                            <option value="Private">Private</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4"><label class="form-label small fw-semibold">Organisation *</label><input type="text" name="organization_name" class="form-control form-control-sm" required></div>
                                    <div class="col-md-3"><label class="form-label small fw-semibold">Designation *</label><input type="text" name="designation" class="form-control form-control-sm" required></div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Current?</label>
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" name="is_current" value="1" id="isCurrent">
                                            <label class="form-check-label small" for="isCurrent">Currently working here</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2"><label class="form-label small fw-semibold">From *</label><input type="date" name="from_date" class="form-control form-control-sm" required></div>
                                    <div class="col-md-2"><label class="form-label small fw-semibold">To</label><input type="date" name="to_date" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-secondary text-white w-100">Add Experience</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @forelse($experience as $exp)
                    <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $exp->designation }} <span class="badge bg-light text-muted">{{ $exp->job_type }}</span></div>
                            <div class="text-muted small">{{ $exp->organization_name }}</div>
                            <div class="text-muted small">{{ $exp->from_date->format('M Y') }} — {{ $exp->to_date ? $exp->to_date->format('M Y') : 'Present' }} · {{ $exp->duration_label }}</div>
                        </div>
                        @if(!$candidate->profile_locked)
                        <form method="POST" action="{{ route('candidate.experience.destroy',$exp) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger px-2 py-1"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted small py-2">No work experience added yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

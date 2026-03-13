@extends('layouts.dashboard')
@section('title', 'Schedule Test Session')
@section('page-title', 'Schedule New Session')

@section('page-actions')
<a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Sessions
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <form method="POST" action="{{ route('admin.batches.store') }}" class="card shadow-sm border-0" id="batchForm">
            @csrf
            <div class="card-header border-0 pb-1 pt-3 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold text-primary">Schedule Test Session Configuration</h3>
                <div id="projectProgressContainer" style="display: none; width: 40%;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">Overall Project Allocation</span>
                        <span class="small fw-bold" id="progressPercent">0%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8 border-end">
                        @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row g-4">
                            <!-- Step 1: Project -->
                            <div class="col-md-6">
                                <label class="form-label required fs-3 fw-bold">Step 1: Select Target Project</label>
                                <select name="project_id" id="project_id" class="form-select" required>
                                    <option value="">Choose a tracking project...</option>
                                    @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Step 2: Jobs -->
                            <div class="col-md-6">
                                <label class="form-label required fs-3 fw-bold">Step 2: Select Job(s)</label>
                                <select name="job_ids[]" id="job_ids" class="form-select" multiple required>
                                    <option value="">Select project first...</option>
                                </select>
                                <div class="form-hint">You can select multiple jobs to conduct concurrently.</div>
                            </div>

                            <div class="col-12"><hr></div>

                            <!-- Step 3: Logistics -->
                            <div class="col-md-6">
                                <label class="form-label required fs-3 fw-bold">Step 3: Test City & Center</label>
                                <label class="form-label required mt-2">Test City Restriction</label>
                                <select name="city_ids[]" id="city_id" class="form-select" multiple required>
                                    <option value="">Select from available cities...</option>
                                </select>
                                
                                <label class="form-label required mt-3">Test Center Venue</label>
                                <select name="center_ids[]" id="center_id" class="form-select" multiple required>
                                    <option value="">Select center...</option>
                                    @foreach($centers as $c)
                                    <option value="{{ $c->id }}" data-city="{{ $c->city_id }}" data-capacity="{{ $c->seating_capacity }}">{{ $c->name }} ({{ $c->city->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required fs-3 fw-bold">Step 4: Scheduling & Logistics</label>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <label class="form-label required">Session Number</label>
                                        <input type="number" name="batch_number" class="form-control" value="1" min="1" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label required">Test Date</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="ti ti-calendar"></i></span>
                                            <input type="text" name="test_date" id="test_date" class="form-control" required placeholder="Select a date">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label required">Reporting Time</label>
                                        <input type="text" name="reporting_time" id="reporting_time" class="form-control" value="08:00" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label required">Test Start Time</label>
                                        <input type="text" name="start_time" id="start_time" class="form-control" value="09:00" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label required">Envelope Group Size</label>
                                        <input type="number" name="envelope_size" class="form-control" value="30" min="10" max="100" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <!-- Step 5: Capacity Configuration -->
                            <div class="col-md-6">
                                <div class="card bg-primary-lt border-0 h-100">
                                    <div class="card-body">
                                        <label class="form-label required text-primary fw-bold fs-3">Total Venue Seats</label>
                                        <div class="input-icon mb-2">
                                            <span class="input-icon-addon"><i class="ti ti-chair-director"></i></span>
                                            <input type="number" name="total_seats" class="form-control border-primary" min="1" required placeholder="e.g. 500">
                                        </div>
                                        <div class="text-secondary small">Maximum physical capacity for this shift.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success-lt border-0 h-100">
                                    <div class="card-body">
                                        <label class="form-label required text-success fw-bold fs-3">Candidates to Allocate NOW</label>
                                        <div class="input-icon mb-2">
                                            <span class="input-icon-addon"><i class="ti ti-users"></i></span>
                                            <input type="number" name="count_to_allocate" class="form-control border-success" min="1" required placeholder="e.g. 500">
                                        </div>
                                        <div class="text-secondary small">How many to assign from the queue.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Visual Tracker Sidebar -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 1rem;">
                            <div class="card border-primary shadow-sm h-100">
                                <div class="card-header bg-primary-lt py-2">
                                    <h4 class="card-title text-primary m-0"><i class="ti ti-pool me-2"></i> Pool Tracker</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div id="poolTrackerEmpty" class="p-5 text-center text-secondary">
                                        <i class="ti ti-filter display-6 mb-2"></i>
                                        <p>Select a project to analyze the candidate pool.</p>
                                    </div>
                                    <div id="poolTrackerContent" style="display: none;">
                                        <div class="p-3 border-bottom bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Available in Selection</span>
                                                <span class="badge bg-primary fs-3 text-white" id="totalPendingBadge">0</span>
                                            </div>
                                        </div>
                                        <div id="poolCitiesList" style="max-height: 400px; overflow-y: auto;">
                                            <!-- Dynamic City Stats -->
                                        </div>
                                        <div class="p-3 bg-dark-lt mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-1 text-success">
                                                <span class="small fw-bold">PROPOSED DRAIN</span>
                                                <span class="small fw-bold" id="proposedDrainText">-0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="m-0 fw-bold">REMAINING POOL</h3>
                                                <span class="badge bg-success fs-3 text-white" id="remainingPoolBadge">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="postSelectionHint" class="alert alert-info mt-3 py-2 px-3 border-0 shadow-sm" style="display: none;">
                                <i class="ti ti-bulb me-1"></i> Candidates limited to <strong>specific job posts</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end p-4">
                <a href="{{ route('admin.batches.index') }}" class="btn btn-link fs-3">Cancel</a>
                <button type="button" class="btn btn-primary fs-3 shadow px-4 py-2" onclick="showBatchConfirm()">
                   <i class="ti ti-calendar-event me-2"></i> Register Session & Execute Allocation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Confirmation Modal -->
<div class="modal modal-blur fade" id="modal-batch-confirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Session Scheduling</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="py-3 text-center">
                    <i class="ti ti-calendar-event text-primary display-5 mb-3"></i>
                    <p class="fs-3 fw-bold mb-1">Bulk Schedule Implementation</p>
                    <p class="text-secondary">You are about to register multiple test sessions and allocate roll numbers.</p>
                </div>
                <div class="bg-light p-3 rounded">
                    <div id="confirm-summary" class="text-body small">
                        <!-- Summary injected via JS -->
                    </div>
                </div>
                <div class="alert alert-important alert-warning mt-3">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-triangle me-2"></i></div>
                        <div>Candidate roll number allocation will be executed <strong>IMMEDIATELY</strong>.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBatchForm()">Confirm & Schedule</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#test_date", { dateFormat: "Y-m-d" });
    flatpickr("#reporting_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
    flatpickr("#start_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });

    let rawStats = [];
    let projectTotals = { total: 0, unallocated: 0 };
    const projectSelect = document.getElementById('project_id');
    const jobSelect = document.getElementById('job_ids');
    const citySelect = document.getElementById('city_id');
    const centerSelect = document.getElementById('center_id');
    const allocationInput = document.querySelector('input[name="count_to_allocate"]');

    const poolTrackerEmpty = document.getElementById('poolTrackerEmpty');
    const poolTrackerContent = document.getElementById('poolTrackerContent');
    const poolCitiesList = document.getElementById('poolCitiesList');
    const totalPendingBadge = document.getElementById('totalPendingBadge');
    const remainingPoolBadge = document.getElementById('remainingPoolBadge');
    const proposedDrainText = document.getElementById('proposedDrainText');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const projectProgressContainer = document.getElementById('projectProgressContainer');
    const postSelectionHint = document.getElementById('postSelectionHint');

    const originalCentersHTML = centerSelect.innerHTML;
    const originalJobsOptions = {
        @foreach($projects as $p)
            "{{ $p->id }}": [
                @foreach($p->jobs as $j)
                    { "id": "{{ $j->id }}", "title": "{{ addslashes($j->title) }}" },
                @endforeach
            ],
        @endforeach
    };

    let tsJobs = new TomSelect(jobSelect, { 
        plugins: ['remove_button'],
        placeholder: "Search and select job(s)...",
        onChange: (values) => {
            postSelectionHint.style.display = values.length > 0 ? 'block' : 'none';
            renderStats();
        }
    });

    let tsCity = new TomSelect(citySelect, {
        plugins: ['remove_button'],
        placeholder: "Select test city restriction...",
        onChange: (cids) => {
            tsCenter.clear(true);
            tsCenter.clearOptions();
            const cityArray = typeof cids === 'string' ? (cids ? [cids] : []) : cids;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
            const options = tempDiv.querySelectorAll('option');
            options.forEach(opt => {
                if(cityArray.length === 0 || cityArray.includes(opt.dataset.city)) {
                    tsCenter.addOption({value: opt.value, text: opt.innerText});
                }
            });
            tsCenter.refreshOptions(false);
            renderStats();
        }
    });

    let tsCenter = new TomSelect(centerSelect, {
        plugins: ['remove_button'],
        placeholder: "Select test centers...",
        onChange: (values) => {
            let totalCapacity = 0;
            const centerArray = typeof values === 'string' ? (values ? [values] : []) : values;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
            const options = Array.from(tempDiv.querySelectorAll('option'));
            centerArray.forEach(id => {
                const opt = options.find(o => o.value == id);
                if (opt && opt.dataset.capacity) totalCapacity += parseInt(opt.dataset.capacity);
            });
            document.querySelector('input[name="total_seats"]').value = totalCapacity;
            if (!allocationInput.value || allocationInput.value == 0) allocationInput.value = totalCapacity;
            updateDrainPreview();
        }
    });

    let tsProject = new TomSelect(projectSelect, {
        onChange: (pid) => {
            if(!pid) {
                poolTrackerContent.style.display = 'none';
                poolTrackerEmpty.style.display = 'block';
                projectProgressContainer.style.display = 'none';
                tsJobs.clear(); tsCity.clear(); return;
            }

            fetch(`{{ route('admin.batches.stats') }}?project_id=${pid}`)
                .then(res => res.json())
                .then(data => {
                    rawStats = data.stats || [];
                    projectTotals = { total: data.project_total || 0, unallocated: data.project_unallocated || 0 };
                    
                    projectProgressContainer.style.display = 'block';
                    const allocated = projectTotals.total - projectTotals.unallocated;
                    const percent = projectTotals.total > 0 ? Math.round((allocated / projectTotals.total) * 100) : 0;
                    progressBar.style.width = percent + '%';
                    progressPercent.innerText = percent + '%';

                    tsJobs.clear(true); tsJobs.clearOptions();
                    (originalJobsOptions[pid] || []).forEach(j => tsJobs.addOption({value: j.id, text: j.title}));

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
                    const uniqueCitiesMap = new Map();
                    tempDiv.querySelectorAll('option').forEach(opt => {
                        const cityNameText = opt.innerText.match(/\((.*?)\)/);
                        if(cityNameText) uniqueCitiesMap.set(opt.dataset.city, cityNameText[1]);
                    });

                    tsCity.clear(); tsCity.clearOptions();
                    uniqueCitiesMap.forEach((name, id) => tsCity.addOption({value: id, text: name}));
                    renderStats();
                });
        }
    });

    function renderStats() {
        if(rawStats.length === 0) {
            poolTrackerContent.style.display = 'none';
            poolTrackerEmpty.style.display = 'block';
            return;
        }

        poolTrackerEmpty.style.display = 'none';
        poolTrackerContent.style.display = 'block';

        const selectedJobs = tsJobs.getValue() || [];
        const jobArray = typeof selectedJobs === 'string' ? (selectedJobs ? [selectedJobs] : []) : selectedJobs;
        const selectedCities = tsCity.getValue() || [];
        const cityArray = typeof selectedCities === 'string' ? (selectedCities ? [selectedCities] : []) : selectedCities;
        
        const cityGroups = {};
        rawStats.forEach(s => {
            if(!cityGroups[s.city_id]) cityGroups[s.city_id] = { name: s.city_name, pending: 0 };
            if(jobArray.length === 0 || jobArray.includes(s.job_id.toString())) {
                cityGroups[s.city_id].pending += parseInt(s.pending_count);
            }
        });

        poolCitiesList.innerHTML = '';
        let currentSelectionTotal = 0;

        Object.keys(cityGroups).forEach(cid => {
            const group = cityGroups[cid];
            const isSelectedCity = cityArray.length === 0 || cityArray.includes(cid);
            if (isSelectedCity) currentSelectionTotal += group.pending;

            poolCitiesList.innerHTML += `
                <div class="p-3 border-bottom ${isSelectedCity ? 'border-start border-4 border-success' : ''}" style="opacity: ${isSelectedCity ? '1' : '0.4'}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold">${group.name}</span>
                        <span class="badge ${isSelectedCity ? 'bg-success' : 'bg-secondary'} text-white">${group.pending}</span>
                    </div>
                </div>
            `;
        });

        totalPendingBadge.innerText = currentSelectionTotal;
        updateDrainPreview();
    }

    function updateDrainPreview() {
        const drain = parseInt(allocationInput.value) || 0;
        const total = parseInt(totalPendingBadge.innerText) || 0;
        proposedDrainText.innerText = `-${drain}`;
        remainingPoolBadge.innerText = Math.max(0, total - drain);
    }

    allocationInput.addEventListener('input', updateDrainPreview);
    
    window.showBatchConfirm = () => {
        const count = allocationInput.value;
        if(!tsCenter.getValue().length || !tsJobs.getValue().length || !document.getElementById('test_date').value || !count) {
            toastr.error("Please complete all required fields."); return;
        }
        document.getElementById('confirm-summary').innerHTML = `
            <div class="mb-2"><strong>Jobs:</strong> ${tsJobs.getValue().length} selected.</div>
            <div class="mb-2"><strong>Centers:</strong> ${tsCenter.getValue().length} selected.</div>
            <div class="mb-3"><strong>Allocation Target:</strong> ${count} candidates.</div>
            <div class="fw-bold text-dark">Date: ${document.getElementById('test_date').value}</div>
        `;
        (new bootstrap.Modal(document.getElementById('modal-batch-confirm'))).show();
    };

    window.submitBatchForm = () => document.getElementById('batchForm').submit();
});
</script>
@endpush

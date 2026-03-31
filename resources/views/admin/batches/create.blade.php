@extends('layouts.dashboard')
@section('title', 'Schedule Test Session')

@section('page-title', 'Schedule New Session')

@section('page-actions')
<a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
    <i class="ti ti-arrow-left me-2"></i> Back to Sessions
</a>
@endsection

@section('content')
<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-lg-12">
        <form method="POST" action="{{ route('admin.batches.store') }}" class="card glass-panel border-0 shadow-lg rounded-4" id="batchForm">
            @csrf
            
            <div class="card-header bg-navy text-white border-0 py-4 px-5 rounded-top-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title fw-black mb-1 fs-2"><i class="ti ti-calendar-plus text-teal me-2 fs-1"></i> Allocation Engine</h2>
                    <p class="mb-0 text-white text-opacity-75 fs-4">Configure test logistics and initialize bulk tracking allocation.</p>
                </div>
                <div id="projectProgressContainer" style="display: none; min-width: 250px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-white text-opacity-75 uppercase tracking-widest">Project Completion</span>
                        <span class="small fw-bold text-teal" id="progressPercent">0%</span>
                    </div>
                    <div class="progress progress-sm rounded-pill bg-white bg-opacity-10">
                        <div class="progress-bar bg-teal rounded-pill" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                <div class="row g-5">
                    <div class="col-lg-8 border-end border-light">
                        @if($errors->any())
                        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-5" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                                <h4 class="alert-title fw-bold m-0">Validation Error</h4>
                            </div>
                            <ul class="mb-0 ps-4">
                                @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <div class="alert bg-teal-lt text-teal border-0 rounded-4 shadow-sm mb-5 animate__animated animate__pulse animate__delay-1s">
                            <div class="d-flex align-items-start">
                                <div><i class="ti ti-bulb fs-1 me-3"></i></div>
                                <div>
                                    <h4 class="alert-title fw-black fs-3">System Guidelines: Cascade Automation Logic</h4>
                                    <div class="fs-4 opacity-80 lh-lg">
                                        The engine dynamically splits your target pool across the selected centers until all candidates are assigned.
                                        <ul class="mb-0 mt-2 list-unstyled">
                                            <li><i class="ti ti-check text-success me-2"></i><strong>Verified Payment:</strong> Only receipts marked as 'paid' are pooled.</li>
                                            <li><i class="ti ti-check text-success me-2"></i><strong>City Match:</strong> Candidate's "Desired Test City" must match the selected centers.</li>
                                            <li><i class="ti ti-check text-success me-2"></i><strong>Unique Assignment:</strong> Automatically skips duplicate assignments.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-5">
                            <!-- Target Definition -->
                            <div class="col-12">
                                <h3 class="fw-black text-navy border-bottom pb-3 mb-4"><span class="badge bg-navy text-white me-2 rounded-circle px-2 py-1">1</span> Define Target Scope</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Target Project</label>
                                        <select name="project_id" id="project_id" class="form-select" required>
                                            <option value="">Choose a tracking project...</option>
                                            @foreach($projects as $p)
                                            <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Target Job(s)</label>
                                        <select name="job_ids[]" id="job_ids" class="form-select" multiple required>
                                            <option value="">Select project first...</option>
                                        </select>
                                        <div class="form-hint text-teal"><i class="ti ti-info-circle"></i> Multiple selection allowed</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Logistics -->
                            <div class="col-12 mt-6">
                                <h3 class="fw-black text-navy border-bottom pb-3 mb-4"><span class="badge bg-navy text-white me-2 rounded-circle px-2 py-1">2</span> Select Venues</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Test City Restriction</label>
                                        <select name="city_ids[]" id="city_id" class="form-select" multiple required>
                                            <option value="">Select from available cities...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Test Center Venues</label>
                                        <select name="center_ids[]" id="center_id" class="form-select" multiple required>
                                            <option value="">Select center...</option>
                                            @foreach($centers as $c)
                                            <option value="{{ $c->id }}" data-city="{{ $c->city_id }}" data-capacity="{{ $c->seating_capacity }}">{{ $c->name }} ({{ $c->city?->name ?? 'Unknown' }})</option>
                                            @endforeach
                                        </select>
                                        <div class="form-hint text-indigo"><i class="ti ti-info-circle"></i> Combined Capacity: <strong id="totalCapacityVisualText">0</strong> seats</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Schedule Definition -->
                            <div class="col-12 mt-6">
                                <h3 class="fw-black text-navy border-bottom pb-3 mb-4"><span class="badge bg-navy text-white me-2 rounded-circle px-2 py-1">3</span> Scheduling & Timings</h3>
                                <div class="row g-4 bg-light rounded-4 p-4 border border-1 border-light">
                                    <div class="col-md-4">
                                        <label class="form-label required fw-bold text-secondary">Session No.</label>
                                        <input type="number" name="batch_number" class="form-control" value="{{ old('batch_number', 1) }}" min="1" required>
                                        <div class="form-hint">e.g. 1 (Morning), 2 (Evening)</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required fw-bold text-secondary">Test Date</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="ti ti-calendar text-teal"></i></span>
                                            <input type="date" name="test_date" id="test_date" class="form-control" required
                                                   min="{{ now()->toDateString() }}" max="2099-12-31" value="{{ old('test_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required fw-bold text-secondary">Envelope Size</label>
                                        <input type="number" name="envelope_size" class="form-control" value="{{ old('envelope_size', 30) }}" min="10" max="100" required>
                                        <div class="form-hint">Answer sheet packing size</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Reporting Time</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="ti ti-clock text-indigo"></i></span>
                                            <input type="time" name="reporting_time" id="reporting_time" class="form-control" value="{{ old('reporting_time', '08:00') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold text-secondary">Test Start Time</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="ti ti-clock-play text-success"></i></span>
                                            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Final Allocation Target -->
                            <div class="col-12 mt-6">
                                <div class="card bg-navy bg-opacity-10 border-teal border-opacity-50 border-2 rounded-4 overflow-hidden">
                                    <div class="row g-0">
                                        <div class="col-auto bg-teal text-white d-flex align-items-center justify-content-center px-5">
                                            <i class="ti ti-users-group display-4"></i>
                                        </div>
                                        <div class="col p-4">
                                            <label class="form-label required text-navy fw-black fs-2">Final Target Allocation</label>
                                            <div class="text-secondary mb-3 fs-4">Determine exactly how many candidates from the queue will be processed into roll numbers across the selected venues.</div>
                                            <input type="number" name="count_to_allocate" id="allocationInput" class="form-control form-control-lg border-teal fw-bold fs-2 text-primary" min="1" value="{{ old('count_to_allocate') }}" required placeholder="e.g. 500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visual Tracker Sidebar -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 2rem;">
                            <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                                <div class="card-header bg-navy text-white py-3 border-0">
                                    <h3 class="card-title m-0 fw-black fs-3"><i class="ti ti-activity me-2 text-teal"></i> Pool Analyzer</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div id="poolTrackerEmpty" class="p-5 text-center text-muted">
                                        <div class="avatar avatar-xl bg-light text-muted mb-3"><i class="ti ti-filter display-6"></i></div>
                                        <p class="fs-4 fw-medium">Select a project and test date to analyze queue metrics.</p>
                                    </div>
                                    <div id="poolTrackerContent" style="display: none;">
                                        <div class="p-4 bg-light border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-black text-secondary uppercase tracking-widest small">Total Available In Selection</span>
                                                <span class="badge bg-navy fs-1 text-white px-3 py-2 rounded-pill shadow-sm" id="totalPendingBadge">0</span>
                                            </div>
                                        </div>
                                        
                                        <div id="poolCitiesList" class="p-2" style="max-height: 300px; overflow-y: auto;">
                                            <!-- Dynamic City Stats -->
                                        </div>

                                        <div class="p-4 bg-teal bg-opacity-10 mt-auto border-top border-teal border-opacity-25">
                                            <div class="d-flex justify-content-between align-items-center mb-2 text-danger animate__animated animate__fadeIn" id="drainContainer">
                                                <span class="small fw-black uppercase tracking-widest"><i class="ti ti-arrow-down-right me-1"></i> EXPECTED DRAIN</span>
                                                <span class="fs-3 fw-bold" id="proposedDrainText">-0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h3 class="m-0 fw-black text-navy fs-2">REMAINING POOL</h3>
                                                <span class="badge bg-teal fs-1 text-white px-3 py-2 rounded-pill shadow-teal-30 animate__animated" id="remainingPoolBadge">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light border-0 py-4 px-5 text-end rounded-bottom-4">
                <a href="{{ route('admin.batches.index') }}" class="btn btn-ghost-secondary fs-3 fw-bold me-3">Cancel</a>
                <button type="button" class="btn btn-navy fs-3 shadow-sm px-5 py-3 rounded-pill fw-black hover-lift transition-all" onclick="showBatchConfirm()">
                   Execute Target Allocation <i class="ti ti-player-play-filled ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Confirmation Modal -->
<div class="modal modal-blur fade" id="modal-batch-confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="avatar avatar-xl bg-teal-lt text-teal rounded-circle mb-4">
                    <i class="ti ti-calendar-event display-5"></i>
                </div>
                <h2 class="display-6 fw-black text-navy mb-2">Execute Allocation?</h2>
                <p class="text-secondary fs-4 mb-4">You are about to irreversibly assign venues and automatically generate roll numbers for the selected candidate pool.</p>
                
                <div class="bg-light p-4 rounded-4 text-start mb-4 border border-light">
                    <div id="confirm-summary" class="fs-4">
                        <!-- Summary injected via JS -->
                    </div>
                </div>
                
                <div class="alert bg-red-lt text-red border-0 text-start d-flex align-items-center mb-0 rounded-3">
                    <i class="ti ti-alert-triangle fs-2 me-3"></i>
                    <span class="fw-bold">This operation activates immediately and cannot be undone. SMS triggers may apply.</span>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                <button type="button" class="btn btn-ghost-secondary me-auto fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-teal fw-black px-4" onclick="submitBatchForm()">Confirm & Initialize</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const oldJobs = @json(old('job_ids', []));
    const oldCities = @json(old('city_ids', []));
    const oldCenters = @json(old('center_ids', []));
    let isFirstLoad = true;

    let rawStats = [];
    let projectTotals = { total: 0, unallocated: 0 };
    const projectSelect = document.getElementById('project_id');
    const jobSelect = document.getElementById('job_ids');
    const citySelect = document.getElementById('city_id');
    const centerSelect = document.getElementById('center_id');
    const allocationInput = document.getElementById('allocationInput');

    const poolTrackerEmpty = document.getElementById('poolTrackerEmpty');
    const poolTrackerContent = document.getElementById('poolTrackerContent');
    const poolCitiesList = document.getElementById('poolCitiesList');
    const totalPendingBadge = document.getElementById('totalPendingBadge');
    const remainingPoolBadge = document.getElementById('remainingPoolBadge');
    const proposedDrainText = document.getElementById('proposedDrainText');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const projectProgressContainer = document.getElementById('projectProgressContainer');

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
            renderStats();
        }
    });

    let tsCity = new TomSelect(citySelect, {
        plugins: ['remove_button'],
        placeholder: "Select test city restriction...",
        onChange: (cids) => {
            const currentCenters = tsCenter.getValue();
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
            const centerArray = typeof currentCenters === 'string' ? (currentCenters ? [currentCenters] : []) : currentCenters;
            const validCenters = centerArray.filter(id => tsCenter.options[id]);
            tsCenter.setValue(validCenters);
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
            // Update the UI visualizer safely
            document.getElementById('totalCapacityVisualText').innerText = new Intl.NumberFormat().format(totalCapacity);
            
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
            refreshStats();
        }
    });

    const dateInput = document.getElementById('test_date');
    dateInput.addEventListener('change', () => {
        if(tsProject.getValue()) refreshStats();
    });

    let refreshTimeout;
    function refreshStats() {
        clearTimeout(refreshTimeout);
        refreshTimeout = setTimeout(() => {
            const pid = tsProject.getValue();
            const testDate = document.getElementById('test_date').value;
            if(!pid) return;

            // Optional animate out
            poolTrackerContent.style.opacity = 0.5;

            fetch(`{{ route('admin.batches.stats') }}?project_id=${pid}&test_date=${testDate}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    rawStats = data.stats || [];
                    projectTotals = { total: data.project_total || 0, unallocated: data.project_unallocated || 0 };
                    
                    projectProgressContainer.style.display = 'block';
                    const allocated = projectTotals.total - projectTotals.unallocated;
                    const percent = projectTotals.total > 0 ? Math.round((allocated / projectTotals.total) * 100) : 0;
                    progressBar.style.width = percent + '%';
                    progressPercent.innerText = percent + '%';

                    const projectChanged = (tsJobs.lastPid !== pid);
                    tsJobs.lastPid = pid;
                    tsCity.lastPid  = pid;

                    if (projectChanged) {
                        tsJobs.clearOptions();
                        (originalJobsOptions[pid] || []).forEach(j => {
                            const jobStat = (data.job_stats || []).find(js => js.id == j.id);
                            const isPending = jobStat ? jobStat.pending > 0 : false;
                            tsJobs.addOption({
                                value: j.id,
                                text: j.title + (isPending ? '' : ' (Fully Allocated)'),
                                disabled: !isPending
                            });
                        });
                        tsJobs.refreshOptions(false);
                    } else {
                        (originalJobsOptions[pid] || []).forEach(j => {
                            const jobStat = (data.job_stats || []).find(js => js.id == j.id);
                            const isPending = jobStat ? jobStat.pending > 0 : false;
                            const newLabel = j.title + (isPending ? '' : ' (Fully Allocated)');

                            if (tsJobs.options[j.id]) {
                                tsJobs.options[j.id].text     = newLabel;
                                tsJobs.options[j.id].disabled = !isPending;
                                if (tsJobs.renderCache && tsJobs.renderCache['option']) delete tsJobs.renderCache['option'][j.id];
                                if (tsJobs.renderCache && tsJobs.renderCache['item']) delete tsJobs.renderCache['item'][j.id];
                            } else {
                                tsJobs.addOption({
                                    value: j.id,
                                    text: newLabel,
                                    disabled: !isPending
                                });
                            }
                        });
                        tsJobs.refreshOptions(false);
                    }

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
                    const uniqueCitiesMap = new Map();
                    tempDiv.querySelectorAll('option').forEach(opt => {
                        const m = opt.innerText.match(/\((.*?)\)/);
                        if (m) uniqueCitiesMap.set(opt.dataset.city, m[1]);
                    });

                    if (projectChanged) {
                        tsCity.clearOptions();
                        uniqueCitiesMap.forEach((name, id) => tsCity.addOption({ value: id, text: name }));
                    }
                    
                    poolTrackerContent.style.opacity = 1;
                    renderStats();

                    if (isFirstLoad) {
                        if (oldJobs.length) tsJobs.setValue(oldJobs);
                        if (oldCities.length) tsCity.setValue(oldCities);
                        if (oldCenters.length) tsCenter.setValue(oldCenters);
                        isFirstLoad = false;
                    }
                })
                .catch(err => {
                    console.error('Stats Fetch Error:', err);
                    poolTrackerContent.style.opacity = 1;
                });
        }, 500);
    }

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

            const div = document.createElement('div');
            div.className = `p-3 mb-2 rounded-3 transition-all ${isSelectedCity ? 'bg-white shadow-sm border border-teal' : 'bg-transparent text-muted'}`;
            div.style.opacity = isSelectedCity ? '1' : '0.5';
            
            const inner = document.createElement('div');
            inner.className = 'd-flex justify-content-between align-items-center';
            
            const nameSpan = document.createElement('span');
            nameSpan.className = `fw-bold ${isSelectedCity ? 'text-navy' : ''}`;
            nameSpan.textContent = group.name;
            
            const badgeSpan = document.createElement('span');
            badgeSpan.className = `badge ${isSelectedCity ? 'bg-teal' : 'bg-secondary'} text-white rounded-pill px-3 py-1`;
            badgeSpan.textContent = new Intl.NumberFormat().format(group.pending);
            
            inner.appendChild(nameSpan);
            inner.appendChild(badgeSpan);
            div.appendChild(inner);
            poolCitiesList.appendChild(div);
        });

        animateValue(totalPendingBadge, parseInt(totalPendingBadge.innerText.replace(/,/g, '')) || 0, currentSelectionTotal, 500);
        updateDrainPreview(currentSelectionTotal);
    }

    function updateDrainPreview(currentSelectionTotalParam = null) {
        const drain = parseInt(allocationInput.value) || 0;
        const totalArrStr = totalPendingBadge.innerText.replace(/,/g, '');
        const total = isNaN(currentSelectionTotalParam) || currentSelectionTotalParam === null ? (parseInt(totalArrStr) || 0) : currentSelectionTotalParam;
        
        proposedDrainText.innerText = `-${new Intl.NumberFormat().format(drain)}`;
        
        const remaining = Math.max(0, total - drain);
        
        remainingPoolBadge.classList.remove('animate__rubberBand');
        void remainingPoolBadge.offsetWidth; // trigger reflow
        remainingPoolBadge.classList.add('animate__rubberBand');
        
        remainingPoolBadge.innerText = new Intl.NumberFormat().format(remaining);
        
        // Visual indicator if draining whole pool
        if (remaining === 0 && drain > 0) {
            remainingPoolBadge.classList.replace('bg-teal', 'bg-success');
        } else {
            remainingPoolBadge.classList.replace('bg-success', 'bg-teal');
        }
    }

    // Smooth counter animation
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = new Intl.NumberFormat().format(Math.floor(progress * (end - start) + start));
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    allocationInput.addEventListener('input', () => updateDrainPreview());
    
    window.showBatchConfirm = () => {
        const count = allocationInput.value;
        if(!tsCenter.getValue().length || !tsJobs.getValue().length || !document.getElementById('test_date').value || !count) {
            toastr.error("Please complete all required fields."); return;
        }
        document.getElementById('confirm-summary').innerHTML = `
            <div class="mb-2"><i class="ti ti-briefcase text-teal me-2"></i> ${tsJobs.getValue().length} Jobs Targeted</div>
            <div class="mb-2"><i class="ti ti-building text-indigo me-2"></i> ${tsCenter.getValue().length} Centers Selected</div>
            <div class="mb-2"><i class="ti ti-calendar text-danger me-2"></i> ${document.getElementById('test_date').value}</div>
            <div class="mt-3 fs-3 fw-black text-navy"><i class="ti ti-users text-teal me-2"></i> Allocating ${new Intl.NumberFormat().format(count)} Candidates</div>
        `;
        (new bootstrap.Modal(document.getElementById('modal-batch-confirm'))).show();
    };

    window.submitBatchForm = () => document.getElementById('batchForm').submit();

    if (projectSelect.value) {
        refreshStats();
    }
});
</script>
@endpush

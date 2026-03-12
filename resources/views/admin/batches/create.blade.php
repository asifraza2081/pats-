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
    <div class="col-lg-10">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <form method="POST" action="{{ route('admin.batches.store') }}" class="card shadow-sm border-0" id="batchForm">
            @csrf
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Schedule Test Session Configuration</h3>
            </div>
            <div class="card-body">
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
                        <div class="form-hint">You can select multiple jobs to conduct concurrently in this session.</div>
                    </div>

                    <!-- Stats Overview Table -->
                    <div class="col-12" id="statsContainer" style="display: none;">
                        <label class="form-label fs-3 fw-bold text-success"><i class="ti ti-chart-bar me-1"></i> Pending Assignments Queue</label>
                        <div class="table-responsive border rounded">
                            <table class="table table-vcenter table-hover mb-0" id="statsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Target City</th>
                                        <th>Job Title</th>
                                        <th class="w-1">Pending Candidates</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                        <div class="form-hint mt-2">These are the candidates who have paid fees and are awaiting roll number assignments.</div>
                    </div>

                    <div class="col-12"><hr></div>

                    <!-- Step 3: Logistics -->
                    <div class="col-md-6">
                        <label class="form-label required fs-3 fw-bold">Step 3: Select Test City & Center</label>
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
                                <div class="text-secondary small">Maximum physical capacity for this specific shift at the venue.</div>
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
                                <div class="text-secondary small">How many pending candidates from the queue above to assign directly.</div>
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
                    <p class="text-secondary">You are about to register multiple test sessions across the selected cities and centers.</p>
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
    // Initialize Flatpickr for date and time inputs
    flatpickr("#test_date", { dateFormat: "Y-m-d" });
    flatpickr("#reporting_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
    flatpickr("#start_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });

    let rawStats = [];
    const projectSelect = document.getElementById('project_id');
    const jobSelect = document.getElementById('job_ids');
    const citySelect = document.getElementById('city_id');
    const centerSelect = document.getElementById('center_id');
    const statsContainer = document.getElementById('statsContainer');
    const statsTableBody = document.querySelector('#statsTable tbody');

    // Make sure center relies on city filtering
    const originalCentersHTML = centerSelect.innerHTML;
    // Pre-calculate jobs per project for quick JS access
    const originalJobsOptions = {
        @foreach($projects as $p)
            "{{ $p->id }}": [
                @foreach($p->jobs as $j)
                    { "id": "{{ $j->id }}", "title": "{{ addslashes($j->title) }}" },
                @endforeach
            ],
        @endforeach
    };

    // We must manually handle TomSelect for dynamic updates
    let tsJobs = new TomSelect(jobSelect, { 
        plugins: ['remove_button'],
        placeholder: "Search and select job(s)...",
        onChange: function() {
            renderStats();
        }
    });

    let tsCity = new TomSelect(citySelect, {
        plugins: ['remove_button'],
        placeholder: "Select one or more cities...",
        onChange: function(cids) {
            tsCenter.clear(true);
            tsCenter.clearOptions();
            
            const cityArray = typeof cids === 'string' ? (cids ? [cids] : []) : cids;
            
            // Re-read hidden options from original
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
            const options = tempDiv.querySelectorAll('option');
            
            options.forEach(opt => {
                if(cityArray.length === 0 || cityArray.includes(opt.dataset.city)) {
                    tsCenter.addOption({value: opt.value, text: opt.innerText});
                }
            });
            tsCenter.refreshOptions(false);
        }
    });

    let tsCenter = new TomSelect(centerSelect, {
        plugins: ['remove_button'],
        placeholder: "Select test centers...",
        onChange: function(values) {
            let totalCapacity = 0;
            const centerArray = typeof values === 'string' ? (values ? [values] : []) : values;
            
            // Get original options to read data-capacity
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
            const options = Array.from(tempDiv.querySelectorAll('option'));
            
            centerArray.forEach(id => {
                const opt = options.find(o => o.value == id);
                if (opt && opt.dataset.capacity) {
                    totalCapacity += parseInt(opt.dataset.capacity);
                }
            });

            // Update UI fields
            const totalSeatsInput = document.querySelector('input[name="total_seats"]');
            const countToAllocateInput = document.querySelector('input[name="count_to_allocate"]');
            
            if (totalSeatsInput) {
                totalSeatsInput.value = totalCapacity;
                // Trigger change event if needed
                totalSeatsInput.dispatchEvent(new Event('change'));
            }
            
            if (countToAllocateInput && (!countToAllocateInput.value || countToAllocateInput.value == 0)) {
                countToAllocateInput.value = totalCapacity;
            }
        }
    });

    let tsProject = new TomSelect(projectSelect, {
        onChange: function(pid) {
            if(!pid) {
                statsContainer.style.display = 'none';
                tsJobs.clear();
                tsJobs.clearOptions();
                tsCity.clear();
                tsCity.clearOptions();
                return;
            }

            // Fetch pending assignment counts
            fetch(`{{ route('admin.batches.stats') }}?project_id=${pid}`)
                .then(res => res.json())
                .then(data => {
                    rawStats = data.stats || [];
                    
                    // CRITICAL: Clear EVERYTING before adding new project jobs
                    tsJobs.clear(true); 
                    tsJobs.clearOptions();
                    tsJobs.refreshOptions(false);
                    const relatedJobs = originalJobsOptions[pid] || [];
                    relatedJobs.forEach(job => {
                        tsJobs.addOption({value: job.id, text: job.title});
                    });

                    // Populate City Dropdown
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = `<select>${originalCentersHTML}</select>`;
                    const options = tempDiv.querySelectorAll('option');
                    const uniqueCitiesMap = new Map();
                    options.forEach(opt => {
                        if (opt.value && opt.dataset.city) {
                            const cityNameText = opt.innerText.match(/\((.*?)\)/);
                            if(cityNameText) uniqueCitiesMap.set(opt.dataset.city, cityNameText[1]);
                        }
                    });

                    tsCity.clear();
                    tsCity.clearOptions();
                    uniqueCitiesMap.forEach((name, id) => {
                        tsCity.addOption({value: id, text: name});
                    });

                    renderStats();
                });
        }
    });

    function renderStats() {
        if(rawStats.length === 0) {
            statsContainer.style.display = 'none';
            return;
        }

        const selectedJobs = tsJobs.getValue() || [];
        const jobArray = typeof selectedJobs === 'string' ? [selectedJobs] : selectedJobs;
        
        let filteredStats = rawStats;
        if(jobArray.length > 0) {
            filteredStats = rawStats.filter(s => jobArray.includes(s.job_id.toString()));
        }

        // Render Table
        statsTableBody.innerHTML = '';
        let totalQueue = 0;

        filteredStats.forEach(s => {
            totalQueue += parseInt(s.pending_count);
            statsTableBody.innerHTML += `
                <tr>
                    <td><span class="badge bg-purple-lt">${s.city_name}</span></td>
                    <td class="fw-medium">${s.job_title}</td>
                    <td><h3 class="m-0 text-success">${s.pending_count}</h3></td>
                </tr>
            `;
        });

        if (filteredStats.length > 0) {
            statsTableBody.innerHTML += `
                <tr class="table-success fw-bold">
                    <td colspan="2" class="text-end">Total Candidates in Selection:</td>
                    <td><h3 class="m-0 text-success">${totalQueue}</h3></td>
                </tr>
            `;
            statsContainer.style.display = 'block';
        } else {
            statsContainer.style.display = 'none';
        }
    }

    const batchForm = document.getElementById('batchForm');
    const confirmModal = new bootstrap.Modal(document.getElementById('modal-batch-confirm'));

    window.showBatchConfirm = function() {
        // Collect selection summary
        const selectedCenters = tsCenter.getValue();
        const selectedJobs = tsJobs.getValue();
        const testDate = document.getElementById('test_date').value;
        const count = document.querySelector('input[name="count_to_allocate"]').value;

        if(!selectedCenters.length || !selectedJobs.length || !testDate || !count) {
            toastr.error("Please complete all required fields before confirming.");
            return;
        }

        let summary = `
            <div class="mb-2"><strong>Centers:</strong> ${Array.isArray(selectedCenters) ? selectedCenters.length : 1} selected.</div>
            <div class="mb-2"><strong>Jobs:</strong> ${Array.isArray(selectedJobs) ? selectedJobs.length : 1} post(s).</div>
            <div class="mb-3"><strong>Allocation Target:</strong> ${count} candidates.</div>
            <div class="fw-bold text-dark">Date: ${testDate}</div>
        `;
        document.getElementById('confirm-summary').innerHTML = summary;
        confirmModal.show();
    };

    window.submitBatchForm = function() {
        batchForm.submit();
    };
});
</script>
@endpush


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
        
            ""blade_val"": [
                
                    { "id": ""blade_val"", "title": ""blade_val"" },
                ],
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
            refreshStats();
        }
    });

    const dateInput = document.getElementById('test_date');
    dateInput.addEventListener('change', () => {
        if(tsProject.getValue()) refreshStats();
    });

    function refreshStats() {
        const pid = tsProject.getValue();
        const testDate = document.getElementById('test_date').value;
        if(!pid) return;

        fetch(`"blade_val"?project_id=${pid}&test_date=${testDate}`)
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

                tsJobs.clear(true); tsJobs.clearOptions();
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
            })
            .catch(err => {
                console.error('Stats Fetch Error:', err);
                toastr.error("Failed to load candidate stats. Please check your connection.");
            });
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Apply for Post</h3>
    <a href="<?= APP_URL ?>/projects/<?= $job['project_id'] ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Jobs</a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 border-top border-3 border-primary h-100">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-1"><?= \App\Core\View::e($job['title']) ?> <span class="badge bg-light text-dark border ms-2"><?= \App\Core\View::e($job['bps_grade'] ?? '') ?></span></h5>
                <p class="small text-muted mb-4"><i class="bi bi-diagram-2 me-1"></i> <?= \App\Core\View::e($job['department'] ?? 'General') ?></p>

                <ul class="list-unstyled mb-0 small">
                    <li class="mb-3">
                        <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Project</span>
                        <div class="fw-semibold"><?= \App\Core\View::e($job['project_name']) ?></div>
                    </li>
                    <li class="mb-3">
                        <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Minimum Qualification</span>
                        <div><?= \App\Core\View::e($job['min_qualification']) ?></div>
                    </li>
                    <li class="mb-3">
                        <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Age Limit</span>
                        <div><?= $job['age_min'] ?? 18 ?> - <?= $job['age_max'] ?? 35 ?> Years</div>
                    </li>
                    <li class="mb-3">
                        <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Processing Fee</span>
                        <div class="text-success fw-bold fs-5">Rs. <?= number_format($job['fee']) ?>/-</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Non-refundable, payable via Bank Challan</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-geo-alt me-2"></i>Select Test Center & Schedule</div>
            <div class="card-body">
                
                <form method="POST" action="<?= APP_URL ?>/apply" id="applyForm">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">

                    <div class="alert alert-info small border-0">
                        <i class="bi bi-info-circle me-1"></i> Please select your preferred test center. Time slots are allocated on a first-come, first-served basis. If a slot is full, you must choose another available slot or center.
                    </div>

                    <div class="mb-4 mt-3">
                        <label class="form-label fw-semibold">Preferred City / Test Center <span class="text-danger">*</span></label>
                        <select name="center_id" id="centerSelect" class="form-select form-select-lg" required>
                            <option value="">-- Select Test Center --</option>
                            <?php foreach($centers as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= \App\Core\View::e($c['city']) ?> - <?= \App\Core\View::e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Available Time Slots <span class="text-danger">*</span></label>
                        <select name="slot_id" id="slotSelect" class="form-select form-select-lg" required disabled>
                            <option value="">-- Choose Center First --</option>
                        </select>
                        <div id="slotHelp" class="form-text small text-danger d-none">No available slots at this center. Please select a different center.</div>
                    </div>

                    <div class="form-check mb-4 mt-5">
                        <input class="form-check-input" type="checkbox" id="declaration" required>
                        <label class="form-check-label small text-muted" for="declaration">
                            I declare that the information provided in my profile is correct to the best of my knowledge. I understand that my profile will be <strong>LOCKED</strong> upon submission and any false information will result in disqualification.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="submitBtn" disabled>
                        Confirm & Submit Application <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const centerSelect = document.getElementById('centerSelect');
    const slotSelect   = document.getElementById('slotSelect');
    const slotHelp     = document.getElementById('slotHelp');
    const submitBtn    = document.getElementById('submitBtn');
    const declaration  = document.getElementById('declaration');
    const projectId    = <?= $job['project_id'] ?>;

    centerSelect.addEventListener('change', async function() {
        const centerId = this.value;
        slotSelect.innerHTML = '<option value="">Loading slots...</option>';
        slotSelect.disabled = true;
        slotHelp.classList.add('d-none');
        checkForm();

        if (!centerId) {
            slotSelect.innerHTML = '<option value="">-- Choose Center First --</option>';
            return;
        }

        try {
            const res = await fetch(`<?= APP_URL ?>/lookup/slots/${centerId}?project_id=${projectId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const slots = await res.json();

            slotSelect.innerHTML = '<option value="">-- Select Time Slot --</option>';
            if (slots.length === 0) {
                slotSelect.disabled = true;
                slotHelp.classList.remove('d-none');
            } else {
                slots.forEach(s => {
                    const date = new Date(s.slot_date + 'T' + s.slot_time);
                    const formattedDetails = `${date.toDateString()} at ${s.slot_time} (${s.total_seats - s.booked_seats} seats left)`;
                    slotSelect.innerHTML += `<option value="${s.id}">${formattedDetails}</option>`;
                });
                slotSelect.disabled = false;
            }
        } catch (e) {
            slotSelect.innerHTML = '<option value="">Error loading slots</option>';
        }
    });

    slotSelect.addEventListener('change', checkForm);
    declaration.addEventListener('change', checkForm);

    function checkForm() {
        submitBtn.disabled = !(centerSelect.value && slotSelect.value && declaration.checked);
    }
});
</script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Apply — <?= \App\Core\View::e($job['title']) ?></h3>
    <a href="<?= APP_URL ?>/projects/<?= $job['project_id'] ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Jobs</a>
</div>

<?php if (!empty($eligibilityIssues)): ?>
<div class="alert alert-danger mb-4">
    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eligibility Issues Detected</h6>
    <p class="mb-1 small">You do not currently meet all the requirements for this post. You may review your profile and update it before applying.</p>
    <ul class="mb-0 small">
        <?php foreach ($eligibilityIssues as $issue): ?>
            <li><?= \App\Core\View::e($issue) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else: ?>
<div class="alert alert-success mb-4 py-2">
    <i class="bi bi-check-circle-fill me-2"></i><strong>Eligible!</strong> You meet all the stated criteria for this post.
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- LEFT: Job Details + Candidate Verification Panel -->
    <div class="col-md-5">
        <!-- Job Info -->
        <div class="card shadow-sm border-0 border-top border-4 border-primary mb-4">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-1"><?= \App\Core\View::e($job['title']) ?> <span class="badge bg-light text-dark border ms-2"><?= \App\Core\View::e($job['bps_grade'] ?? '') ?></span></h5>
                <p class="small text-muted mb-3"><i class="bi bi-diagram-2 me-1"></i><?= \App\Core\View::e($job['department'] ?? 'General') ?></p>
                <hr class="my-2">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><span class="text-muted">Project:</span> <strong><?= \App\Core\View::e($job['project_name']) ?></strong></li>
                    <li class="mb-2"><span class="text-muted">Min. Qualification:</span> <strong><?= \App\Core\View::e($job['min_qualification'] ?? 'N/A') ?></strong></li>
                    <li class="mb-2"><span class="text-muted">Age Limit:</span> <strong><?= $job['age_min'] ?? 18 ?> – <?= $job['age_max'] ?? 35 ?> Years</strong></li>
                    <?php if (!empty($job['domicile_required'])): ?>
                    <li class="mb-2"><span class="text-muted">Domicile:</span> <strong><?= \App\Core\View::e($job['domicile_required']) ?></strong></li>
                    <?php endif; ?>
                    <li>
                        <span class="text-muted">Processing Fee:</span>
                        <span class="text-success fw-bold fs-5 ms-1">Rs. <?= number_format($job['fee']) ?>/-</span>
                        <div class="text-muted" style="font-size: 0.7rem;">Non-refundable, payable via Bank Challan</div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Candidate Data Verification Panel -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-2"><i class="bi bi-person-vcard me-2 text-primary"></i>Your Profile Data (Auto-filled)</div>
            <div class="card-body small">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Name</td><td class="fw-semibold"><?= \App\Core\View::e($profile['name']) ?></td></tr>
                    <tr><td class="text-muted">CNIC</td><td class="fw-semibold"><?= \App\Core\View::e($profile['cnic']) ?></td></tr>
                    <tr><td class="text-muted">Date of Birth</td><td class="fw-semibold"><?= !empty($profile['dob']) ? date('d M Y', strtotime($profile['dob'])) : '<span class="text-danger">Not set</span>' ?></td></tr>
                    <tr><td class="text-muted">Gender</td><td class="fw-semibold"><?= \App\Core\View::e(ucfirst($profile['gender'] ?? '—')) ?></td></tr>
                    <tr><td class="text-muted">Province</td><td class="fw-semibold"><?= \App\Core\View::e($profile['province'] ?? '—') ?></td></tr>
                    <tr><td class="text-muted">Domicile</td><td class="fw-semibold"><?= \App\Core\View::e($profile['domicile'] ?? '—') ?></td></tr>
                    <tr><td class="text-muted">Phone</td><td class="fw-semibold"><?= \App\Core\View::e($profile['phone']) ?></td></tr>
                    <tr><td class="text-muted">Email</td><td class="fw-semibold"><?= \App\Core\View::e($profile['email']) ?></td></tr>
                </table>
                <a href="<?= APP_URL ?>/profile" class="btn btn-outline-secondary btn-sm w-100 mt-2"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
                <div class="text-danger small mt-1"><i class="bi bi-lock-fill me-1"></i>Profile will be <strong>locked</strong> after submission.</div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Center & Slot Selection Form -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-geo-alt me-2"></i>Select Test Center & Schedule</div>
            <div class="card-body">

                <?php if (!empty($eligibilityIssues)): ?>
                <div class="alert alert-warning small"><i class="bi bi-exclamation-circle me-1"></i>You have eligibility issues. You can still submit, but your application may be rejected.</div>
                <?php endif; ?>

                <form method="POST" action="<?= APP_URL ?>/apply" id="applyForm">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">

                    <div class="alert alert-info small border-0">
                        <i class="bi bi-info-circle me-1"></i>Slots are first-come, first-served. If a slot fills up after you select it, you may be automatically assigned the next available slot at the same center.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Preferred City / Test Center <span class="text-danger">*</span></label>
                        <select name="center_id" id="centerSelect" class="form-select form-select-lg" required>
                            <option value="">-- Select Test Center --</option>
                            <?php foreach($centers as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= \App\Core\View::e($c['city']) ?> — <?= \App\Core\View::e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Available Time Slots <span class="text-danger">*</span></label>
                        <select name="slot_id" id="slotSelect" class="form-select form-select-lg" required disabled>
                            <option value="">-- Choose Center First --</option>
                        </select>
                        <div id="slotHelp" class="form-text small text-danger d-none">No available slots at this center. Please choose another.</div>
                    </div>

                    <div class="form-check mb-4 mt-3">
                        <input class="form-check-input" type="checkbox" id="declaration" required>
                        <label class="form-check-label small text-muted" for="declaration">
                            I declare that all information in my profile is accurate. I understand my profile will be <strong>LOCKED</strong> after submission and any false information will result in disqualification.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="submitBtn" disabled>
                        <i class="bi bi-send me-2"></i>Confirm & Submit Application
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
                    const d = new Date(s.slot_date + 'T' + s.slot_time);
                    const remaining = s.total_seats - s.booked_seats;
                    const label = `${d.toDateString()} at ${s.slot_time.substring(0,5)} — ${remaining} seat${remaining !== 1 ? 's' : ''} remaining`;
                    slotSelect.innerHTML += `<option value="${s.id}">${label}</option>`;
                });
                slotSelect.disabled = false;
            }
        } catch (e) {
            slotSelect.innerHTML = '<option value="">Error loading slots. Refresh page.</option>';
        }
        checkForm();
    });

    slotSelect.addEventListener('change', checkForm);
    declaration.addEventListener('change', checkForm);

    function checkForm() {
        submitBtn.disabled = !(centerSelect.value && slotSelect.value && declaration.checked);
    }
});
</script>

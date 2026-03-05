<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Add New Post / Job</h3>
    <a href="<?= APP_URL ?>/admin/jobs" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <form method="POST" action="<?= APP_URL ?>/admin/jobs">
            <?= \App\Core\CSRF::field() ?>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Select Project (Advertisement) <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select" required autofocus>
                        <option value="">-- Select Project --</option>
                        <?php foreach($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= (strlen((string)($old['project_id'] ?? '')) && $old['project_id'] == $p['id']) ? 'selected' : '' ?>>
                                [<?= strtoupper($p['status']) ?>] <?= \App\Core\View::e($p['name']) ?> (<?= \App\Core\View::e($p['org_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Assistant Director (IT)" value="<?= \App\Core\View::e($old['title'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="e.g. IT Wing" value="<?= \App\Core\View::e($old['department'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">BPS Grade</label>
                    <input type="text" name="bps_grade" class="form-control" placeholder="e.g. BPS-17" value="<?= \App\Core\View::e($old['bps_grade'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Seats <span class="text-danger">*</span></label>
                    <input type="number" name="total_seats" class="form-control" value="<?= $old['total_seats'] ?? 1 ?>" min="1" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Application Fee (Rs.) <span class="text-danger">*</span></label>
                    <input type="number" name="fee" class="form-control" value="<?= $old['fee'] ?? 500 ?>" min="0" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Min. Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="min_qualification" class="form-control" placeholder="e.g. BSCS or equivalent" value="<?= \App\Core\View::e($old['min_qualification'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Age Min (Years)</label>
                    <input type="number" name="age_min" class="form-control" value="<?= $old['age_min'] ?? 18 ?>" min="18">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Age Max (Years)</label>
                    <input type="number" name="age_max" class="form-control" value="<?= $old['age_max'] ?? 35 ?>" min="18">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Domicile Restriction</label>
                    <input type="text" name="domicile_required" class="form-control" placeholder="e.g. Punjab Only (Leave blank for Open Merit)" value="<?= \App\Core\View::e($old['domicile_required'] ?? '') ?>">
                </div>

            </div>

            <hr class="my-4">
            
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-plus-circle me-1"></i> Add Job</button>
        </form>

    </div>
</div>

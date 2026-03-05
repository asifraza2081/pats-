<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Create New Project</h3>
    <a href="<?= APP_URL ?>/admin/projects" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <form method="POST" action="<?= APP_URL ?>/admin/projects">
            <?= \App\Core\CSRF::field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Wapda Recruitment 2024" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Organization Name <span class="text-danger">*</span></label>
                    <input type="text" name="org_name" class="form-control" placeholder="e.g. WAPDA" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Advertisement Description / Details</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Full job advertisement text..."></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Open Date <span class="text-danger">*</span></label>
                    <input type="date" name="open_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Close Date <span class="text-danger">*</span></label>
                    <input type="date" name="close_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tentative Test Date</label>
                    <input type="date" name="test_date" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" selected>Draft (Hidden from public)</option>
                        <option value="open">Open (Accepting applications)</option>
                        <option value="closed">Closed</option>
                        <option value="result_declared">Result Declared</option>
                    </select>
                </div>
            </div>

            <hr class="my-4">
            
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Save Project</button>
        </form>

    </div>
</div>

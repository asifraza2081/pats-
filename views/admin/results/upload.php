<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Upload Bulk Results (CSV/Excel)</h3>
    <a href="<?= APP_URL ?>/admin/results" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Results</a>
</div>

<?php 
    $pending = \App\Core\Session::get('pending_results'); 
    $pendingProjectId = \App\Core\Session::get('pending_project_id');
?>

<?php if (!$pending): ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 border-top border-3 border-primary">
                <div class="card-body p-4">
                    <form method="POST" action="<?= APP_URL ?>/admin/results/upload" enctype="multipart/form-data">
                        <?= \App\Core\CSRF::field() ?>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Target Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select form-select-lg" required>
                                <option value="">-- Select Project --</option>
                                <?php foreach($projects as $p): ?>
                                    <option value="<?= $p['id'] ?>">[<?= strtoupper($p['status']) ?>] <?= \App\Core\View::e($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Upload Evaluation File (.xlsx, .csv) <span class="text-danger">*</span></label>
                            <input type="file" name="result_file" class="form-control form-control-lg" accept=".csv, .xlsx, .xls" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold"><i class="bi bi-upload me-1"></i> Analyze & Upload Data</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Expected File Format</h5>
                    <p class="small text-muted mb-3">Your uploaded file must have a header row and follow this precise column sequence:</p>
                    <table class="table table-bordered table-sm small bg-white">
                        <thead class="table-light"><tr><th>Col</th><th>Header Name</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td>A</td><td>RollNumber</td><td>Format: XXX-XXXX-XXXXXX</td></tr>
                            <tr><td>B</td><td>Score</td><td>Numeric e.g. 75.5</td></tr>
                            <tr><td>C</td><td>Percentage</td><td>Numeric e.g. 75.50</td></tr>
                            <tr><td>D</td><td>Status</td><td>pass, fail, absent, withheld</td></tr>
                            <tr><td>E</td><td>Remarks</td><td>Optional text</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="card shadow-sm border-0 border-top border-3 border-warning mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Review Pending Upload</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light position-sticky top-0 shadow-sm">
                        <tr>
                            <th>#</th>
                            <th>Matched App ID</th>
                            <th>Roll Number</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending as $idx => $r): ?>
                        <tr>
                            <td class="text-muted"><?= $idx+1 ?></td>
                            <td><?= $r['application_id'] ?></td>
                            <td class="fw-bold font-monospace text-primary"><?= \App\Core\View::e($r['roll_number']) ?></td>
                            <td><?= number_format($r['score'], 2) ?></td>
                            <td><?= number_format($r['percentage'], 2) ?>%</td>
                            <td><span class="badge bg-secondary"><?= strtoupper($r['status']) ?></span></td>
                            <td class="small text-muted"><?= \App\Core\View::e($r['remarks']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Total Valid Records:</strong> <span class="badge bg-primary fs-6"><?= count($pending) ?></span>
                    </div>
                    <div>
                        <a href="<?= APP_URL ?>/admin/results/upload" class="btn btn-outline-danger me-2"><i class="bi bi-x-circle me-1"></i> Cancel & Discard</a>
                        <form method="POST" action="<?= APP_URL ?>/admin/results/commit" class="d-inline">
                            <?= \App\Core\CSRF::field() ?>
                            <input type="hidden" name="project_id" value="<?= $pendingProjectId ?>">
                            <div class="form-check form-switch d-inline-block me-3 align-middle">
                                <input class="form-check-input" type="checkbox" name="declare_project" value="1" id="declare" checked>
                                <label class="form-check-label small fw-bold text-success" for="declare">Declare Project Result Publicly</label>
                            </div>
                            <button type="submit" class="btn btn-success fw-bold"><i class="bi bi-check2-circle me-1"></i> Commit Results to DB</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

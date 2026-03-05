<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Export Data & Reports</h3>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 border-top border-3 border-success">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Export Applications Tracker</div>
            <div class="card-body">
                <p class="text-muted small mb-4">Download a full CSV dump of applications including candidate contact details, fee status, roll numbers, and assigned centers. Suitable for MS Excel filtering and generating merit lists.</p>
                
                <form method="GET" action="<?= APP_URL ?>/admin/reports/applications">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by Project (Optional)</label>
                        <select name="project_id" class="form-select">
                            <option value="">All Projects (Complete Dump)</option>
                            <?php foreach($projects as $p): ?>
                                <option value="<?= $p['id'] ?>">[<?= strtoupper($p['status']) ?>] <?= \App\Core\View::e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success fw-bold w-100"><i class="bi bi-download me-2"></i> Export CSV</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 border-top border-3 border-secondary">
            <div class="card-header bg-white fw-bold py-3 text-secondary"><i class="bi bi-shield-lock me-2"></i>System Audit Log (Upcoming)</div>
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-5">
                <i class="bi bi-cone-striped display-4 text-muted mb-3 opacity-25"></i>
                <h5 class="text-muted fw-bold">Under Construction</h5>
                <p class="small text-secondary mb-0">Detailed PDF reports for audit logging and center statistics will be available in the next release.</p>
            </div>
        </div>
    </div>
</div>

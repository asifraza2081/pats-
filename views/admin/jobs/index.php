<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Manage Jobs (Posts)</h3>
    <a href="<?= APP_URL ?>/admin/jobs/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add New Job</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID / Project</th>
                        <th>Job Title & Grade</th>
                        <th>Seats</th>
                        <th>Fee</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No jobs found in the system.</td></tr>
                    <?php else: ?>
                        <?php foreach ($jobs as $j): ?>
                        <tr>
                            <td>
                                <span class="fw-bold">#<?= $j['id'] ?></span><br>
                                <small class="text-muted"><i class="bi bi-folder me-1"></i><?= \App\Core\View::e($j['project_name']) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= \App\Core\View::e($j['title']) ?></div>
                                <div class="small text-muted">BPS: <?= \App\Core\View::e($j['bps_grade'] ?? 'N/A') ?> 
                                    <?= $j['department'] ? ' | Dept: ' . \App\Core\View::e($j['department']) : '' ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= $j['total_seats'] ?> Seats</span><br>
                                <small class="text-muted"><?= \App\Core\View::e($j['domicile_required'] ?? 'Open Merit') ?></small>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">Rs. <?= number_format($j['fee']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= APP_URL ?>/admin/jobs/<?= $j['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="<?= APP_URL ?>/admin/jobs/<?= $j['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Delete this job? Applications tied to it will also be deleted!');">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

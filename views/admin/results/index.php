<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Master Exam Results</h3>
    <a href="<?= APP_URL ?>/admin/results/upload" class="btn btn-primary"><i class="bi bi-cloud-upload me-1"></i> Upload Bulk Results</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>Roll Number</th>
                        <th>Candidate / CNIC</th>
                        <th>Project & Job</th>
                        <th>Score & %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No results declared yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($results as $r): ?>
                        <tr>
                            <td><span class="fw-bold text-primary font-monospace"><?= \App\Core\View::e($r['roll_number']) ?></span></td>
                            <td>
                                <div class="fw-bold"><?= \App\Core\View::e($r['candidate_name']) ?></div>
                                <div class="small text-muted"><?= \App\Core\View::e($r['cnic']) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= \App\Core\View::e($r['job_title']) ?></div>
                                <div class="small text-muted text-truncate" style="max-width:200px;" title="<?= \App\Core\View::e($r['project_name']) ?>">
                                    <?= \App\Core\View::e($r['project_name']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= number_format((float)$r['score'], 2) ?> <span class="text-muted fw-normal small">Marks</span></div>
                                <div class="small text-secondary"><?= number_format((float)$r['percentage'], 2) ?>%</div>
                            </td>
                            <td>
                                <?php
                                    $sColors = ['pass'=>'success', 'fail'=>'danger', 'absent'=>'secondary', 'withheld'=>'warning'];
                                    $c = $sColors[$r['status']] ?? 'dark';
                                ?>
                                <span class="badge bg-<?= $c ?>"><?= strtoupper($r['status']) ?></span>
                                <?php if($r['remarks']): ?>
                                    <div class="small text-muted mt-1"><i class="bi bi-chat-text"></i> <?= \App\Core\View::e($r['remarks']) ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-muted small py-2 text-center">
            Showing up to 500 latest entries.
        </div>
    </div>
</div>

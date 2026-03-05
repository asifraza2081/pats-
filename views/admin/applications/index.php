<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Applications Master List</h3>
    
    <form class="d-flex gap-2" method="GET" action="<?= APP_URL ?>/admin/applications">
        <select name="job_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Jobs / Posts</option>
            <?php foreach($jobs as $j): ?>
                <option value="<?= $j['id'] ?>" <?= (isset($jobFilter) && $jobFilter == $j['id']) ? 'selected' : '' ?>>
                    [#<?= $j['id'] ?>] <?= \App\Core\View::e($j['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn btn-sm btn-primary">Filter</button></noscript>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Candidate / CNIC</th>
                        <th>Job Title & Project</th>
                        <th>Center & Slot</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No applications found matching criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-primary"><?= \App\Core\View::e($app['candidate_name']) ?></div>
                                <div class="text-muted small"><?= \App\Core\View::e($app['cnic']) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= \App\Core\View::e($app['job_title']) ?> <span class="badge bg-light text-dark border ms-1"><?= \App\Core\View::e($app['bps_grade']) ?></span></div>
                                <div class="text-muted small text-truncate" style="max-width: 200px;" title="<?= \App\Core\View::e($app['project_name']) ?>"><?= \App\Core\View::e($app['project_name']) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-geo-alt me-1 text-danger"></i><?= \App\Core\View::e($app['city']) ?></div>
                                <div class="text-muted small"><?= date('d M Y, H:i', strtotime($app['slot_date'] . ' ' . $app['slot_time'])) ?></div>
                            </td>
                            <td>
                                <?php
                                    $sColors = ['submitted'=>'secondary', 'fee_paid'=>'info text-dark', 'scheduled'=>'primary', 'result_declared'=>'success'];
                                    $c = $sColors[$app['status']] ?? 'dark';
                                ?>
                                <span class="badge bg-<?= $c ?>"><?= strtoupper(str_replace('_', ' ', $app['status'])) ?></span>
                            </td>
                            <td class="text-end">
                                <!-- Minimal Actions for Demo -->
                                <?php if ($app['status'] === 'fee_paid'): ?>
                                    <form method="POST" action="<?= APP_URL ?>/admin/applications/<?= $app['id'] ?>/approve" class="d-inline">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button class="btn btn-sm btn-success" title="Approve & Schedule"><i class="bi bi-check2-circle"></i></button>
                                    </form>
                                <?php endif; ?>

                                <?php if (in_array($app['status'], ['submitted', 'fee_paid'])): ?>
                                    <form method="POST" action="<?= APP_URL ?>/admin/applications/<?= $app['id'] ?>/reject" class="d-inline" onsubmit="return confirm('Reject application and free up the seat?');">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button class="btn btn-sm btn-danger" title="Reject Application"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-muted small py-2 text-center">
            Showing latest up to 500 records.
        </div>
    </div>
</div>

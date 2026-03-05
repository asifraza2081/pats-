<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">My Applications</h3>
    <a href="<?= APP_URL ?>/projects" class="btn btn-primary"><i class="bi bi-search me-1"></i> Browse More Jobs</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>Job Title & Project</th>
                        <th>Test Schedule</th>
                        <th>Fee & Challan</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">You have not applied for any jobs yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-primary"><?= \App\Core\View::e($app['job_title']) ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 250px;" title="<?= \App\Core\View::e($app['project_name']) ?>">
                                    <?= \App\Core\View::e($app['project_name']) ?>
                                </div>
                                <div class="small text-muted mt-1">Applied: <?= date('d M Y', strtotime($app['applied_at'])) ?></div>
                            </td>
                            <td>
                                <div class="small fw-semibold"><i class="bi bi-geo-alt me-1 text-danger"></i><?= \App\Core\View::e($app['city']) ?></div>
                                <div class="small text-muted mt-1"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y, h:i A', strtotime($app['slot_date'] . ' ' . $app['slot_time'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold">Rs. <?= number_format($app['fee']) ?></div>
                                <?php if ($app['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success mt-1"><i class="bi bi-check-circle me-1"></i>Paid Verified</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark mt-1"><i class="bi bi-clock me-1"></i>Pending Verification</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $sColors = [
                                        'submitted' => 'secondary',
                                        'fee_paid' => 'info',
                                        'scheduled' => 'primary',
                                        'result_declared' => 'success'
                                    ];
                                    $c = $sColors[$app['status']] ?? 'dark';
                                ?>
                                <span class="badge bg-<?= $c ?> px-2 py-1"><?= strtoupper(str_replace('_', ' ', $app['status'])) ?></span>
                            </td>
                            <td class="text-end">
                                <?php if ($app['payment_status'] !== 'paid'): ?>
                                    <a href="<?= APP_URL ?>/challan/<?= $app['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Download Fee Challan">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Challan
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($app['status'] === 'scheduled' || $app['status'] === 'result_declared'): ?>
                                    <a href="<?= APP_URL ?>/slip/<?= $app['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Download Roll No. Slip">
                                        <i class="bi bi-ticket-detailed me-1"></i> Slip
                                    </a>
                                <?php endif; ?>

                                <?php if ($app['status'] === 'result_declared'): ?>
                                    <a href="<?= APP_URL ?>/result/<?= $app['id'] ?>" class="btn btn-sm btn-success" title="View Result">
                                        <i class="bi bi-trophy"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

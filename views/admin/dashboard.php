<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">System Overview</h3>
    <div class="text-muted small"><i class="bi bi-clock me-1"></i>Last updated: <?= date('d M Y, h:i A') ?></div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="text-muted text-uppercase small fw-bold mb-2">Registered Candidates</div>
                <div class="display-6 fw-bold text-primary"><?= number_format($stats['candidates']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body">
                <div class="text-muted text-uppercase small fw-bold mb-2">Active Projects</div>
                <div class="display-6 fw-bold text-success"><?= number_format($stats['projects']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
            <div class="card-body">
                <div class="text-muted text-uppercase small fw-bold mb-2">Total Applications</div>
                <div class="display-6 fw-bold text-warning"><?= number_format($stats['applications']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body">
                <div class="text-muted text-uppercase small fw-bold mb-2">Fee Collected (Rs)</div>
                <div class="display-6 fw-bold text-info"><?= number_format($stats['revenue']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-activity text-primary me-2"></i>Recent Applications</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Candidate</th>
                                <th>Post Applied</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentApps)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No recent activity.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentApps as $app): ?>
                                <tr>
                                    <td class="fw-semibold text-primary"><?= \App\Core\View::e($app['name']) ?></td>
                                    <td><?= \App\Core\View::e($app['title']) ?></td>
                                    <td class="small text-muted"><?= date('d M, Y H:i', strtotime($app['applied_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= strtoupper($app['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light text-end py-2">
                <a href="<?= APP_URL ?>/admin/applications" class="btn btn-sm btn-link text-decoration-none fw-bold">View All Applications <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Action Items</div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold"><?= number_format($pendingPayments) ?> Pending Payments</h6>
                        <small class="text-muted">Awaiting admin verification.</small>
                    </div>
                    <a href="<?= APP_URL ?>/admin/challans" class="btn btn-sm btn-outline-warning">Review</a>
                </div>
                <!-- Additional action items can go here -->
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Challan / Payment Verification</h3>
</div>

<div class="alert alert-info border-0 shadow-sm mb-4">
    <i class="bi bi-info-circle me-2"></i> In production, this can be replaced by an automated bank API (1Link) cron job. Currently, Admin verifies manually.
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Challan Ref</th>
                        <th>Candidate Info</th>
                        <th>Amount (Rs.)</th>
                        <th>Status</th>
                        <th class="text-end">Verify</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($challans)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No fee records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($challans as $ch): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-primary font-monospace"><?= \App\Core\View::e($ch['challan_ref']) ?></div>
                                <div class="small text-muted">Method: <?= strtoupper(\App\Core\View::e($ch['method'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= \App\Core\View::e($ch['candidate_name']) ?></div>
                                <div class="small text-muted">CNIC: <?= \App\Core\View::e($ch['cnic']) ?> | Ph: <?= \App\Core\View::e($ch['phone']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-success fs-6"><?= number_format($ch['amount']) ?></div>
                            </td>
                            <td>
                                <?php if ($ch['status'] === 'paid'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle me-1"></i> VERIFIED</span><br>
                                    <small class="text-muted dt-format"><?= date('d M Y', strtotime($ch['verified_at'])) ?></small>
                                <?php elseif ($ch['status'] === 'failed'): ?>
                                    <span class="badge bg-danger">FAILED</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> PENDING</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($ch['status'] === 'pending' && $ch['app_status'] === 'submitted'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#verifyModal<?= $ch['id'] ?>">
                                        Verify
                                    </button>
                                    
                                    <!-- Verify Modal -->
                                    <div class="modal fade text-start" id="verifyModal<?= $ch['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold">Verify Payment: <?= \App\Core\View::e($ch['challan_ref']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="<?= APP_URL ?>/admin/challans/<?= $ch['id'] ?>/verify">
                                                    <?= \App\Core\CSRF::field() ?>
                                                    <div class="modal-body p-4">
                                                        <div class="alert alert-warning small border-warning pb-0">
                                                            <ul class="mb-2">
                                                                <li>Application status will be upgraded to <strong>Fee Paid</strong>.</li>
                                                                <li>An automated SMS will be sent to <strong><?= \App\Core\View::e($ch['phone']) ?></strong>.</li>
                                                            </ul>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Bank Transaction ID / Ref No.</label>
                                                            <input type="text" name="bank_ref" class="form-control" placeholder="Optional bank sequence number">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label fw-semibold">Deposit Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="deposit_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-top-0">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success fw-bold"><i class="bi bi-check2-circle me-1"></i> Verify Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light text-muted disabled" title="Already verified or invalid state"><i class="bi bi-shield-check"></i></button>
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

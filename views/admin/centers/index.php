<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Manage Test Centers</h3>
    <a href="<?= APP_URL ?>/admin/centers/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Center</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Center Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($centers)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No test centers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($centers as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= \App\Core\View::e($c['name']) ?></div>
                                <div class="small text-muted text-truncate" style="max-width:300px;"><?= \App\Core\View::e($c['address']) ?></div>
                            </td>
                            <td>
                                <?= \App\Core\View::e($c['city']) ?>, <span class="text-muted"><?= \App\Core\View::e($c['province']) ?></span>
                            </td>
                            <td>
                                <?php if ($c['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= APP_URL ?>/admin/centers/<?= $c['id'] ?>/slots" class="btn btn-sm btn-outline-info me-1" title="Manage Slots / Capacity"><i class="bi bi-calendar3"></i> Slots</a>
                                <a href="<?= APP_URL ?>/admin/centers/<?= $c['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

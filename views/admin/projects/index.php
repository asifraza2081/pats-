<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Manage Projects</h3>
    <a href="<?= APP_URL ?>/admin/projects/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Project</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Project Name / Organization</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No projects found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td>
                                <div class="fw-bold"><?= \App\Core\View::e($p['name']) ?></div>
                                <div class="small text-muted"><?= \App\Core\View::e($p['org_name']) ?></div>
                            </td>
                            <td>
                                <div class="small"><span class="text-muted">Open:</span> <?= $p['open_date'] ? date('d M Y', strtotime($p['open_date'])) : 'N/A' ?></div>
                                <div class="small"><span class="text-muted">Close:</span> <?= $p['close_date'] ? date('d M Y', strtotime($p['close_date'])) : 'N/A' ?></div>
                            </td>
                            <td>
                                <?php 
                                    $colors = ['draft'=>'secondary', 'open'=>'success', 'closed'=>'danger', 'result_declared'=>'info'];
                                    $color = $colors[$p['status']] ?? 'primary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= strtoupper($p['status']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= APP_URL ?>/admin/projects/<?= $p['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="<?= APP_URL ?>/admin/projects/<?= $p['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Delete this project? Warning: This will delete ALL associated jobs and applications!');">
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

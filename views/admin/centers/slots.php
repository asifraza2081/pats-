<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Manage Slots: <?= \App\Core\View::e($center['name']) ?></h3>
    <a href="<?= APP_URL ?>/admin/centers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="row g-4 border-bottom pb-4 mb-4">
    <!-- Slot Form -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 border-top border-3 border-primary">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3"><i class="bi bi-calendar-plus me-2"></i>Add Time Slot</h5>
                <form method="POST" action="<?= APP_URL ?>/admin/centers/<?= $center['id'] ?>/slots">
                    <?= \App\Core\CSRF::field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Project --</option>
                            <?php foreach($projects as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= \App\Core\View::e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Mapping a slot automatically maps this center to the project.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
                            <input type="date" name="slot_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Time <span class="text-danger">*</span></label>
                            <input type="time" name="slot_time" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Capacity (Total Seats) <span class="text-danger">*</span></label>
                        <input type="number" name="total_seats" class="form-control form-control-sm" value="50" min="1" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-plus-circle me-1"></i> Create Slot & Capacity</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Slot List -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Project & DateTime</th>
                                <th>Capacity / Booked</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($slots)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">No slots assigned to this center yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($slots as $s): ?>
                                <?php $pct = round(($s['booked_seats'] / $s['total_seats']) * 100); ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary small"><?= \App\Core\View::e($s['project_name']) ?></div>
                                        <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y, h:i A', strtotime($s['slot_date'] . ' ' . $s['slot_time'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span><?= $s['booked_seats'] ?> Booked</span>
                                            <span><?= $s['total_seats'] ?> Total</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar <?= $pct >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $pct ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <?php if($s['booked_seats'] == 0): ?>
                                        <form method="POST" action="<?= APP_URL ?>/admin/slots/<?= $s['id'] ?>/delete" onsubmit="return confirm('Delete this slot?');">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button class="btn btn-sm btn-outline-danger" title="Delete Slot"><i class="bi bi-trash"></i></button>
                                        </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary disabled" title="Cannot delete, seats booked"><i class="bi bi-lock-fill"></i></button>
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
    </div>
</div>

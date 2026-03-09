<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i>Attendance Sheets</h3>
</div>

<!-- Step 1: Select Center & Date & Slot -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-bold py-3">Step 1: Select Center, Date & Time Slot</div>
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/admin/attendance" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Center</label>
                <select name="center_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Center --</option>
                    <?php foreach($centers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $centerId == $c['id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::e($c['city']) ?> — <?= \App\Core\View::e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($centerId): ?>
                <input type="hidden" name="slot_date" value="<?= \App\Core\View::e($slotDate) ?>">
                <input type="hidden" name="slot_id" value="<?= $slotId ?>">
                <?php endif; ?>
            </div>

            <?php if ($centerId && !empty($dates)): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Test Date</label>
                <select name="slot_date" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Date --</option>
                    <?php foreach($dates as $d): ?>
                        <option value="<?= $d['slot_date'] ?>" <?= $slotDate === $d['slot_date'] ? 'selected' : '' ?>>
                            <?= date('D, d M Y', strtotime($d['slot_date'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="center_id" value="<?= $centerId ?>">
            </div>
            <?php endif; ?>

            <?php if ($slotDate && !empty($slots)): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Time Slot</label>
                <select name="slot_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Slot --</option>
                    <?php foreach($slots as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $slotId == $s['id'] ? 'selected' : '' ?>>
                            <?= date('h:i A', strtotime($s['slot_time'])) ?> — <?= \App\Core\View::e($s['project_name']) ?>
                            (<?= $s['booked_seats'] ?>/<?= $s['total_seats'] ?> booked)
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="center_id" value="<?= $centerId ?>">
                <input type="hidden" name="slot_date" value="<?= $slotDate ?>">
            </div>
            <?php endif; ?>

            <?php if ($slotId): ?>
            <div class="col-md-2 d-flex gap-2">
                <a href="<?= APP_URL ?>/admin/attendance/print/<?= $slotId ?>" target="_blank" class="btn btn-success w-100">
                    <i class="bi bi-printer me-1"></i> Print Sheet
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Attendance Preview -->
<?php if ($slotId && !empty($attendees)): ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold">
            <i class="bi bi-people me-2"></i>
            <?= \App\Core\View::e($center['name'] ?? '') ?> — <?= date('D, d M Y', strtotime($slotInfo['slot_date'])) ?> at <?= date('h:i A', strtotime($slotInfo['slot_time'])) ?>
            <span class="badge bg-primary ms-2"><?= count($attendees) ?> candidates</span>
        </span>
        <a href="<?= APP_URL ?>/admin/attendance/print/<?= $slotId ?>" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-printer me-1"></i> Print Full Sheet
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Roll No.</th>
                        <th>Candidate Name</th>
                        <th>Father's Name</th>
                        <th>CNIC</th>
                        <th>Post Applied</th>
                        <th>Status</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attendees as $i => $a): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="fw-bold"><?= \App\Core\View::e($a['roll_number'] ?? '—') ?></td>
                        <td><?= \App\Core\View::e($a['candidate_name']) ?></td>
                        <td><?= \App\Core\View::e($a['father_name'] ?? '—') ?></td>
                        <td><?= \App\Core\View::e($a['cnic']) ?></td>
                        <td><?= \App\Core\View::e($a['job_title']) ?></td>
                        <td>
                            <?php $sc = ['submitted' => 'secondary', 'fee_paid' => 'info', 'scheduled' => 'primary', 'result_declared' => 'success']; ?>
                            <span class="badge bg-<?= $sc[$a['status']] ?? 'dark' ?>"><?= strtoupper(str_replace('_', ' ', $a['status'])) ?></span>
                        </td>
                        <td class="text-center"><input type="checkbox" class="form-check-input" disabled></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif ($slotId && empty($attendees)): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No candidates are assigned to this slot yet.</div>
<?php elseif ($centerId): ?>
<div class="alert alert-secondary"><i class="bi bi-arrow-up me-2"></i>Select a date and time slot to preview the attendance sheet.</div>
<?php else: ?>
<div class="alert alert-secondary"><i class="bi bi-arrow-up me-2"></i>Select a test center to get started.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="fw-bold"><i class="bi bi-person-lines-fill me-2"></i>My Profile</h2>
    <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a>
</div>

<?php if ($profile['profile_locked']): ?>
<div class="alert alert-warning shadow-sm">
    <i class="bi bi-lock-fill me-2"></i> <strong>Profile Locked!</strong>
    You have successfully submitted applications. To preserve data integrity, basic profile information is now locked. Contact admin if a correction is genuinely required.
</div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/profile" enctype="multipart/form-data">
    <?= \App\Core\CSRF::field() ?>

    <div class="row g-4">
        <!-- Personal Info -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-person me-2"></i>Personal Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Full Name</label>
                            <input type="text" class="form-control bg-light" value="<?= \App\Core\View::e($profile['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">CNIC</label>
                            <input type="text" class="form-control bg-light" value="<?= \App\Core\View::e($profile['cnic']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Email</label>
                            <input type="text" class="form-control bg-light" value="<?= \App\Core\View::e($profile['email']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Mobile</label>
                            <input type="text" class="form-control bg-light" value="<?= \App\Core\View::e($profile['phone']) ?>" readonly>
                        </div>

                        <hr class="w-100 my-4 text-muted">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control" value="<?= \App\Core\View::e($profile['dob']) ?>" required <?= $profile['profile_locked'] ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required <?= $profile['profile_locked'] ? 'disabled' : '' ?>>
                                <option value="">Select Gender</option>
                                <option value="male" <?= $profile['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $profile['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $profile['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if ($profile['profile_locked']): ?><input type="hidden" name="gender" value="<?= $profile['gender'] ?>"><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Religion <span class="text-danger">*</span></label>
                            <select name="religion" class="form-select" required <?= $profile['profile_locked'] ? 'disabled' : '' ?>>
                                <option value="">Select Religion</option>
                                <option value="Islam" <?= $profile['religion'] === 'Islam' ? 'selected' : '' ?>>Islam</option>
                                <option value="Christianity" <?= $profile['religion'] === 'Christianity' ? 'selected' : '' ?>>Christianity</option>
                                <option value="Hinduism" <?= $profile['religion'] === 'Hinduism' ? 'selected' : '' ?>>Hinduism</option>
                                <option value="Other" <?= $profile['religion'] === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if ($profile['profile_locked']): ?><input type="hidden" name="religion" value="<?= $profile['religion'] ?>"><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                            <select name="province" class="form-select" required <?= $profile['profile_locked'] ? 'disabled' : '' ?>>
                                <option value="">Select Province</option>
                                <?php foreach(['Punjab', 'Sindh Urban', 'Sindh Rural', 'KPK', 'Balochistan', 'GB', 'AJK', 'Federal'] as $prov): ?>
                                    <option value="<?= $prov ?>" <?= $profile['province'] === $prov ? 'selected' : '' ?>><?= $prov ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($profile['profile_locked']): ?><input type="hidden" name="province" value="<?= $profile['province'] ?>"><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Domicile District <span class="text-danger">*</span></label>
                            <input type="text" name="domicile" class="form-control" value="<?= \App\Core\View::e($profile['domicile']) ?>" required <?= $profile['profile_locked'] ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Postal Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" required <?= $profile['profile_locked'] ? 'readonly' : '' ?>><?= \App\Core\View::e($profile['address']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education History -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-mortarboard me-2"></i>Academic Qualifications</span>
                        <?php if (!$profile['profile_locked']): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addEducationBtn"><i class="bi bi-plus"></i> Add Entry</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="eduTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Degree / Level</th>
                                    <th>Board / University</th>
                                    <th>Passing Year</th>
                                    <th>Grade / Division</th>
                                    <?php if (!$profile['profile_locked']): ?><th width="50"></th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($education)): ?>
                                    <?php if (!$profile['profile_locked']): ?>
                                    <tr class="edu-row">
                                        <td><input type="text" name="degrees[]" class="form-control form-control-sm" placeholder="e.g. Matric / O-Level" required></td>
                                        <td><input type="text" name="institutions[]" class="form-control form-control-sm" placeholder="e.g. BISE Lahore" required></td>
                                        <td><input type="number" name="years[]" class="form-control form-control-sm" placeholder="2018" min="1950" max="<?= date('Y') ?>" required></td>
                                        <td><input type="text" name="grades[]" class="form-control form-control-sm" placeholder="1st Div / A+" required></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-edu"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                    <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No education history recorded.</td></tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php foreach ($education as $edu): ?>
                                    <tr class="edu-row">
                                        <td><input type="text" name="degrees[]" class="form-control form-control-sm" value="<?= \App\Core\View::e($edu['degree']) ?>" <?= $profile['profile_locked'] ? 'readonly' : 'required' ?>></td>
                                        <td><input type="text" name="institutions[]" class="form-control form-control-sm" value="<?= \App\Core\View::e($edu['institution']) ?>" <?= $profile['profile_locked'] ? 'readonly' : 'required' ?>></td>
                                        <td><input type="number" name="years[]" class="form-control form-control-sm" value="<?= \App\Core\View::e($edu['passing_year']) ?>" <?= $profile['profile_locked'] ? 'readonly' : 'required' ?>></td>
                                        <td><input type="text" name="grades[]" class="form-control form-control-sm" value="<?= \App\Core\View::e($edu['grade']) ?>" <?= $profile['profile_locked'] ? 'readonly' : 'required' ?>></td>
                                        <?php if (!$profile['profile_locked']): ?><td><button type="button" class="btn btn-sm btn-outline-danger remove-edu"><i class="bi bi-trash"></i></button></td><?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Uploads -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 80px;">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-cloud-arrow-up me-2"></i>Documents</div>
                <div class="card-body">
                    
                    <div class="mb-4 text-center">
                        <div class="mb-2">
                            <?php if ($profile['photo_path']): ?>
                                <img src="/pats/storage/uploads/<?= $profile['photo_path'] ?>" alt="Profile Photo" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; border: 2px dashed #ddd;">
                                    <i class="bi bi-person text-secondary display-4"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <label class="form-label fw-semibold mt-2">Passport Size Photo <span class="text-danger">*</span></label>
                        <?php if (!$profile['profile_locked']): ?>
                            <input type="file" name="photo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" <?= !$profile['photo_path'] ? 'required' : '' ?>>
                            <div class="form-text small">Blue/White background. Max 2MB.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">CNIC Front Copy <span class="text-danger">*</span></label>
                        <?php if ($profile['cnic_copy_path']): ?>
                            <div class="mb-2 text-success small"><i class="bi bi-check-circle me-1"></i> Document Uploaded</div>
                        <?php endif; ?>
                        <?php if (!$profile['profile_locked']): ?>
                            <input type="file" name="cnic_copy" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,application/pdf" <?= !$profile['cnic_copy_path'] ? 'required' : '' ?>>
                        <?php endif; ?>
                    </div>

                    <?php if (!$profile['profile_locked']): ?>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-save me-1"></i> Save Profile
                        </button>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</form>

<?php if (!$profile['profile_locked']): ?>
<script>
document.getElementById('addEducationBtn')?.addEventListener('click', function() {
    const tbody = document.querySelector('#eduTable tbody');
    const tr = document.createElement('tr');
    tr.className = 'edu-row';
    tr.innerHTML = `
        <td><input type="text" name="degrees[]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="institutions[]" class="form-control form-control-sm" required></td>
        <td><input type="number" name="years[]" class="form-control form-control-sm" min="1950" max="2030" required></td>
        <td><input type="text" name="grades[]" class="form-control form-control-sm" required></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-edu"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
});
document.getElementById('eduTable')?.addEventListener('click', function(e) {
    if (e.target.closest('.remove-edu')) {
        e.target.closest('tr').remove();
    }
});
</script>
<?php endif; ?>

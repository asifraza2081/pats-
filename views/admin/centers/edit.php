<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Edit Test Center</h3>
    <a href="<?= APP_URL ?>/admin/centers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <form method="POST" action="<?= APP_URL ?>/admin/centers/<?= $center['id'] ?>">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Center/School/Hall Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= \App\Core\View::e($center['name']) ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" value="<?= \App\Core\View::e($center['city']) ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                    <select name="province" class="form-select" required>
                        <?php foreach(['Punjab', 'Sindh', 'KPK', 'Balochistan', 'GB', 'AJK', 'Federal'] as $prov): ?>
                            <option value="<?= $prov ?>" <?= $center['province'] === $prov ? 'selected' : '' ?>><?= $prov ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Full Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= \App\Core\View::e($center['address'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Google Maps URL (Optional)</label>
                    <input type="url" name="map_url" class="form-control" value="<?= \App\Core\View::e($center['map_url'] ?? '') ?>" placeholder="https://maps.google.com/...">
                </div>

                <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" <?= $center['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isActive">Center is Active</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
                <a href="<?= APP_URL ?>/admin/centers/<?= $center['id'] ?>/slots" class="btn btn-outline-info"><i class="bi bi-calendar-event me-1"></i> Manage Slots</a>
            </div>
        </form>

    </div>
</div>

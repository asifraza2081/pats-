<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Add Testing Center</h3>
    <a href="<?= APP_URL ?>/admin/centers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <form method="POST" action="<?= APP_URL ?>/admin/centers">
            <?= \App\Core\CSRF::field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Center/School/Hall Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Govt Degree College Boys" value="<?= \App\Core\View::e($old['name'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" placeholder="e.g. Lahore" value="<?= \App\Core\View::e($old['city'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                    <select name="province" class="form-select" required>
                        <option value="">Select</option>
                        <?php foreach(['Punjab', 'Sindh', 'KPK', 'Balochistan', 'GB', 'AJK', 'Federal'] as $prov): ?>
                            <option value="<?= $prov ?>"><?= $prov ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Full Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Street, Sector, Area..."><?= \App\Core\View::e($old['address'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Google Maps URL (Optional)</label>
                    <input type="url" name="map_url" class="form-control text-muted" placeholder="https://maps.google.com/..." value="<?= \App\Core\View::e($old['map_url'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" checked>
                        <label class="form-check-label fw-semibold" for="isActive">Center is Active</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Save Center</button>
        </form>

    </div>
</div>

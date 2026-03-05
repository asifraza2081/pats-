<div class="row mb-5 text-center">
    <div class="col-12">
        <h1 class="display-5 fw-bold text-primary">Open Projects</h1>
        <p class="lead text-muted">Browse currently active recruitments and apply online.</p>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($projects)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
            <h4 class="mt-3 text-muted">No Open Projects</h4>
            <p>There are currently no active projects accepting applications. Please check back later.</p>
        </div>
    <?php else: ?>
        <?php foreach ($projects as $proj): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 project-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">OPEN</span>
                            <small class="text-danger fw-bold"><i class="bi bi-clock me-1"></i>Closes: <?= date('d M, Y', strtotime($proj['close_date'])) ?></small>
                        </div>
                        <h5 class="card-title fw-bold text-truncate" title="<?= \App\Core\View::e($proj['name']) ?>">
                            <?= \App\Core\View::e($proj['name']) ?>
                        </h5>
                        <h6 class="card-subtitle mb-3 text-muted">
                            <i class="bi bi-building me-1"></i> <?= \App\Core\View::e($proj['org_name']) ?>
                        </h6>
                        <p class="card-text small text-secondary line-clamp-3">
                            <?= \App\Core\View::e(substr((string)$proj['description'], 0, 150)) ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <a href="<?= APP_URL ?>/projects/<?= $proj['id'] ?>" class="btn btn-primary w-100 fw-semibold">
                            View Jobs & Apply <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.project-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.project-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

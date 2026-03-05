<div class="mb-4">
    <a href="<?= APP_URL ?>/projects" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to all projects</a>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-primary text-white p-4">
        <h2 class="fw-bold mb-1"><?= \App\Core\View::e($project['name']) ?></h2>
        <h5 class="mb-0 opacity-75"><i class="bi bi-building me-2"></i><?= \App\Core\View::e($project['org_name']) ?></h5>
    </div>
    <div class="card-body p-4 bg-light text-dark">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="mb-0 lead text-secondary"><?= nl2br(\App\Core\View::e((string)$project['description'])) ?></p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0 border-start border-md-0 border-top border-md-top-0 pt-3 pt-md-0 ps-md-4 text-center text-md-start">
                <div class="mb-2">
                    <span class="text-muted d-block small text-uppercase fw-bold">Advertisement Date</span>
                    <span class="fs-5"><i class="bi bi-calendar-event text-primary me-2"></i><?= date('d M, Y', strtotime($project['open_date'])) ?></span>
                </div>
                <div>
                    <span class="text-muted d-block small text-uppercase fw-bold text-danger">Last Date to Apply</span>
                    <span class="fs-5 text-danger fw-bold"><i class="bi bi-calendar-x me-2"></i><?= date('d M, Y', strtotime($project['close_date'])) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-briefcase me-2"></i>Available Vacancies</h4>

<div class="row g-4 mb-5">
    <?php if (empty($jobs)): ?>
        <div class="col-12 py-4">
            <div class="alert alert-info border-0">
                <i class="bi bi-info-circle me-2"></i> No vacancies listed for this project yet. Please check back later.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $j): ?>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary hover-card">
                    <div class="card-body position-relative">
                        
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-primary mb-0"><?= \App\Core\View::e($j['title']) ?></h5>
                            <span class="badge bg-light text-dark border"><?= \App\Core\View::e($j['bps_grade'] ?? '') ?></span>
                        </div>
                        
                        <?php if ($j['department']): ?>
                            <div class="text-muted small mb-3"><i class="bi bi-diagram-2 me-1"></i> <?= \App\Core\View::e($j['department']) ?></div>
                        <?php endif; ?>

                        <ul class="list-unstyled mb-4 small text-secondary">
                            <li class="mb-2"><i class="bi bi-mortarboard me-2 text-muted"></i><strong>Qualification:</strong> <?= \App\Core\View::e($j['min_qualification']) ?></li>
                            <li class="mb-2"><i class="bi bi-person-check me-2 text-muted"></i><strong>Age Limit:</strong> <?= $j['age_min'] ?? 18 ?> - <?= $j['age_max'] ?? 35 ?> Years</li>
                            <li class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i><strong>Domicile:</strong> <span class="badge bg-secondary"><?= \App\Core\View::e($j['domicile_required'] ?: 'Open Merit') ?></span> &nbsp; | &nbsp; <strong>Seats:</strong> <?= $j['total_seats'] ?></li>
                            <li class="mb-0"><i class="bi bi-cash me-2 text-muted"></i><strong>Processing Fee:</strong> Rs. <?= number_format($j['fee']) ?>/-</li>
                        </ul>

                        <a href="<?= APP_URL ?>/apply/<?= $j['id'] ?>" class="btn btn-outline-primary w-100 fw-bold stretched-link mt-auto">
                            Apply for this Post <i class="bi bi-check2-circle ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; z-index: 10;}
</style>

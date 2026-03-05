<?php
/**
 * PATS Landing Page
 * Inspired by NTS, using PATS Blue Theme
 */
?>

<!-- Hero Section -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div id="heroCarousel" class="carousel slide shadow-sm rounded-4 overflow-hidden h-100" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner h-100">
                <div class="carousel-item active h-100">
                    <div class="h-100 p-5 d-flex flex-column justify-content-center text-white" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); min-height: 400px;">
                        <h1 class="display-4 fw-bold mb-3">Welcome to PATS</h1>
                        <p class="lead mb-4">Pakistan Aptitude Testing Service — Your partner in fair, transparent, and merit-based recruitment across Pakistan.</p>
                        <div>
                            <a href="<?= APP_URL ?>/register" class="btn btn-light btn-lg px-4 fw-bold me-2">Register Now</a>
                            <a href="<?= APP_URL ?>/projects" class="btn btn-outline-light btn-lg px-4">Browse Jobs</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item h-100">
                    <div class="h-100 p-5 d-flex flex-column justify-content-center text-white" style="background: linear-gradient(135deg, #6610f2 0%, #4b0db8 100%); min-height: 400px;">
                        <h1 class="display-4 fw-bold mb-3">Merit Over All</h1>
                        <p class="lead mb-4">We ensure every candidate gets a fair chance to showcase their talent through state-of-the-art testing standards.</p>
                        <div>
                            <a href="<?= APP_URL ?>/result" class="btn btn-light btn-lg px-4 fw-bold">Check Your Result</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item h-100">
                    <div class="h-100 p-5 d-flex flex-column justify-content-center text-white" style="background: linear-gradient(135deg, #0575E6 0%, #021B79 100%); min-height: 400px;">
                        <h1 class="display-4 fw-bold mb-3">Apply Online</h1>
                        <p class="lead mb-4">No more paper applications. Apply to multiple projects from the comfort of your home with our digital portal.</p>
                        <div>
                            <a href="<?= APP_URL ?>/login" class="btn btn-light btn-lg px-4 fw-bold">Candidate Login</a>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white fw-bold py-3">
                <i class="bi bi-megaphone me-2"></i> Latest Announcements
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush announcement-list" style="max-height: 350px; overflow-y: auto;">
                    <a href="#" class="list-group-item list-group-item-action p-3">
                        <div class="d-flex w-100 justify-content-between">
                            <span class="badge bg-danger mb-2">NEW</span>
                            <small class="text-muted">Today</small>
                        </div>
                        <h6 class="mb-1 fw-bold">Result: Inspector Inland Revenue</h6>
                        <p class="mb-1 small text-muted">The results for the Inspector Inland Revenue (FBR) have been uploaded...</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action p-3 text-primary">
                        <div class="d-flex w-100 justify-content-between">
                            <span class="badge bg-primary mb-2">UPDATE</span>
                            <small class="text-muted">2 days ago</small>
                        </div>
                        <h6 class="mb-1 fw-bold">Roll No Slips: Batch 04 Testing</h6>
                        <p class="mb-1 small text-muted">Candidates can now download their roll number slips for Batch 04 starting from next week...</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action p-3">
                         <div class="d-flex w-100 justify-content-between">
                            <span class="badge bg-secondary mb-2">INFO</span>
                            <small class="text-muted">3 days ago</small>
                        </div>
                        <h6 class="mb-1 fw-bold">Maintenance Notice</h6>
                        <p class="mb-1 small text-muted">The portal will be down for maintenance on Sunday between 1AM and 3AM...</p>
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light text-center py-2">
                <a href="#" class="small text-decoration-none">View All Notices</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-5">
    <div class="col-6 col-md-3">
        <a href="<?= APP_URL ?>/projects" class="card h-100 border-0 shadow-sm text-center p-4 text-decoration-none action-card">
            <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3 rounded-circle" style="width: 64px; height: 64px; line-height: 64px;">
                <i class="bi bi-briefcase fs-3"></i>
            </div>
            <h6 class="fw-bold mb-0">Open Jobs</h6>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= APP_URL ?>/register" class="card h-100 border-0 shadow-sm text-center p-4 text-decoration-none action-card">
            <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle" style="width: 64px; height: 64px; line-height: 64px;">
                <i class="bi bi-person-plus fs-3"></i>
            </div>
            <h6 class="fw-bold mb-0">New Registration</h6>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= APP_URL ?>/result" class="card h-100 border-0 shadow-sm text-center p-4 text-decoration-none action-card">
            <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle" style="width: 64px; height: 64px; line-height: 64px;">
                <i class="bi bi-award fs-3"></i>
            </div>
            <h6 class="fw-bold mb-0">View Results</h6>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="#" class="card h-100 border-0 shadow-sm text-center p-4 text-decoration-none action-card">
            <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mb-3 rounded-circle" style="width: 64px; height: 64px; line-height: 64px;">
                <i class="bi bi-info-circle fs-3"></i>
            </div>
            <h6 class="fw-bold mb-0">How to Apply</h6>
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-0 mb-5 shadow-sm rounded-4 overflow-hidden text-center bg-white border">
    <div class="col-md-4 p-4 border-end">
        <h2 class="fw-bold text-primary mb-0">120+</h2>
        <span class="text-muted small text-uppercase fw-bold">Active Projects</span>
    </div>
    <div class="col-md-4 p-4 border-end">
        <h2 class="fw-bold text-primary mb-0">50,000+</h2>
        <span class="text-muted small text-uppercase fw-bold">Registered Candidates</span>
    </div>
    <div class="col-md-4 p-4">
        <h2 class="fw-bold text-primary mb-0">15+</h2>
        <span class="text-muted small text-uppercase fw-bold">Test Centers</span>
    </div>
</div>

<!-- Active Projects Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Featured Projects</h3>
    <a href="<?= APP_URL ?>/projects" class="btn btn-outline-primary btn-sm">View All Projects</a>
</div>

<div class="row g-4">
    <?php if (empty($projects)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
            <h4 class="mt-3 text-muted">No Open Projects</h4>
            <p>There are currently no active projects accepting applications.</p>
        </div>
    <?php else: ?>
        <?php foreach (array_slice($projects, 0, 3) as $proj): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 project-card overflow-hidden">
                    <div class="card-body">
                         <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">OPEN</span>
                            <small class="text-danger fw-bold"><i class="bi bi-calendar-event me-1"></i>Ends: <?= date('d M', strtotime($proj['close_date'])) ?></small>
                        </div>
                        <h6 class="fw-bold text-primary mb-1"><?= \App\Core\View::e($proj['org_name']) ?></h6>
                        <h5 class="card-title fw-bold">
                            <?= \App\Core\View::e($proj['name']) ?>
                        </h5>
                        <p class="card-text small text-secondary">
                            <?= \App\Core\View::e(substr((string)$proj['description'], 0, 100)) ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <a href="<?= APP_URL ?>/projects/<?= $proj['id'] ?>" class="btn btn-primary w-100 fw-semibold">
                            Details & Apply
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.action-card { transition: all 0.2s ease; cursor: pointer; }
.action-card:hover { background-color: #f8f9fa; transform: translateY(-3px); }
.project-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border-top: 4px solid #007bff !important; }
.project-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.announcement-list::-webkit-scrollbar { width: 5px; }
.announcement-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }
</style>

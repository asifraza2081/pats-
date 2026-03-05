<?php
$completionColor = $completion < 100 ? 'bg-warning' : 'bg-success';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Welcome, <?= \App\Core\View::e($profile['name']) ?></h2>
        <p class="text-muted">CNIC: <?= \App\Core\View::e($profile['cnic']) ?> &nbsp;|&nbsp; Status: <?= $profile['is_verified'] ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Unverified</span>' ?></p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= APP_URL ?>/profile" class="btn btn-outline-primary">
            <i class="bi bi-person me-1"></i> Edit Profile
        </a>
        <a href="<?= APP_URL ?>/projects" class="btn btn-primary ms-2">
            <i class="bi bi-search me-1"></i> Browse Jobs
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Profile Completion Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3"><i class="bi bi-person-lines-fill me-2"></i>Profile Status</h6>
                <div class="d-flex align-items-center mb-2">
                    <h3 class="mb-0 me-2"><?= $completion ?>%</h3>
                    <span class="text-muted small">Completed</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar <?= $completionColor ?>" role="progressbar" style="width: <?= $completion ?>%;"></div>
                </div>
                <?php if ($completion < 100): ?>
                    <p class="small text-danger mt-3 mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i> You must complete your profile before applying for any job!
                    </p>
                <?php else: ?>
                    <p class="small text-success mt-3 mb-0">
                        <i class="bi bi-check-circle me-1"></i> Profile is complete and ready.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Active Applications Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-primary text-white">
            <div class="card-body">
                <h6 class="text-uppercase mb-3 opacity-75"><i class="bi bi-file-earmark-text me-2"></i>My Applications</h6>
                <h2 class="display-5 fw-bold mb-0">--</h2> <!-- To be fetched in future DB queries -->
                <p class="small mt-2 mb-0 opacity-75">Total applications submitted</p>
                <a href="<?= APP_URL ?>/applications" class="btn btn-light btn-sm mt-3 fw-semibold">View All</a>
            </div>
        </div>
    </div>

    <!-- Pending Payments Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
            <div class="card-body">
                <h6 class="text-uppercase mb-3 opacity-75"><i class="bi bi-cash-coin me-2"></i>Pending Dues</h6>
                <h2 class="display-5 fw-bold mb-0">--</h2>
                <p class="small mt-2 mb-0 opacity-75">Applications awaiting fee payment</p>
                <a href="<?= APP_URL ?>/applications" class="btn btn-dark btn-sm mt-3 fw-semibold">Pay Now</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h5 class="fw-bold mb-3"><i class="bi bi-megaphone me-2"></i>Recent Announcements</h5>
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <!-- Announcements Placeholder -->
                <div class="list-group-item p-3">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-primary fw-bold">Welcome to PATS Portal</h6>
                        <small class="text-muted">Today</small>
                    </div>
                    <p class="mb-1 small">Please ensure your profile is 100% complete. Upload clear and legible documents to avoid application rejection.</p>
                </div>
            </div>
        </div>
    </div>
</div>

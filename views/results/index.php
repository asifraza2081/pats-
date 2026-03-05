<div class="row justify-content-center mb-5 mt-4">
    <div class="col-md-8 text-center">
        <h1 class="display-5 fw-bold text-primary mb-3"><i class="bi bi-trophy text-warning me-2"></i>Check Your Result</h1>
        <p class="lead text-muted">Enter your Roll Number or CNIC (without dashes) to search for your online test result.</p>
        
        <div class="card shadow-sm border-0 mt-4 text-start">
            <div class="card-body p-4 p-md-5">
                <form method="GET" action="<?= APP_URL ?>/results">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="e.g. 001-0002-000005 or 3520212345671" value="<?= \App\Core\View::e($query ?? '') ?>" required autofocus>
                        <button class="btn btn-primary px-4 fw-bold" type="submit">Find Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (isset($query) && $result): ?>
    <div class="row justify-content-center mb-5">
        <div class="col-md-10">
            <div class="card shadow border-0 overflow-hidden">
                <div class="card-header bg-success text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">Result Card</h4>
                        <span class="badge bg-white text-success fs-6 px-3 py-2">STATUS: <?= strtoupper($result['status']) ?></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-7 p-4 p-md-5 border-end border-light">
                            <h5 class="fw-bold text-primary mb-1"><?= strtoupper(\App\Core\View::e($result['candidate_name'])) ?></h5>
                            <p class="text-muted font-monospace mb-4">CNIC: <?= \App\Core\View::e($result['cnic']) ?></p>

                            <div class="row g-4 text-muted small">
                                <div class="col-6">
                                    <div class="text-uppercase fw-bold mb-1">Roll Number</div>
                                    <div class="fw-semibold text-dark fs-6 font-monospace"><?= \App\Core\View::e($result['roll_number']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-uppercase fw-bold mb-1">Post Applied</div>
                                    <div class="fw-semibold text-dark fs-6"><?= \App\Core\View::e($result['job_title']) ?></div>
                                </div>
                                <div class="col-12">
                                    <div class="text-uppercase fw-bold mb-1">Project / Organization</div>
                                    <div class="fw-semibold text-dark fs-6"><?= \App\Core\View::e($result['project_name']) ?></div>
                                </div>
                                <?php if($result['remarks']): ?>
                                <div class="col-12">
                                    <div class="text-uppercase fw-bold mb-1 text-info">Remarks</div>
                                    <div class="fw-semibold text-dark"><?= \App\Core\View::e($result['remarks']) ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-5 bg-light p-4 p-md-5 d-flex flex-column justify-content-center align-items-center text-center">
                            <div class="mb-2 text-uppercase fw-bold text-muted spacing-1">Marks Obtained</div>
                            <div class="display-3 fw-bold text-dark mb-1"><?= number_format((float)$result['score'], 2) ?></div>
                            <div class="fs-5 text-secondary mb-4">Percentage: <strong><?= number_format((float)$result['percentage'], 2) ?>%</strong></div>

                            <?php if ($result['status'] === 'pass'): ?>
                                <div class="text-success small fw-bold px-4 py-2 bg-success bg-opacity-10 rounded-pill"><i class="bi bi-star-fill me-2"></i>QUALIFIED FOR INTERVIEW</div>
                            <?php elseif ($result['status'] === 'fail'): ?>
                                <div class="text-danger small fw-bold px-4 py-2 bg-danger bg-opacity-10 rounded-pill">NOT QUALIFIED</div>
                            <?php else: ?>
                                <div class="text-warning text-dark small fw-bold px-4 py-2 bg-warning bg-opacity-10 rounded-pill"><?= strtoupper($result['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer me-2"></i> Print Result Card</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * { visibility: hidden; }
            .card, .card * { visibility: visible; }
            .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
            .btn { display: none !important; }
        }
    </style>
<?php endif; ?>

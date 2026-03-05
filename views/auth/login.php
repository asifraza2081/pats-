<?php $pageTitle = 'Login'; ?>

<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock display-4 text-primary"></i>
            <h4 class="fw-bold mt-2">Candidate Login</h4>
            <p class="text-muted small">Enter your CNIC and password to continue</p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/login">
            <?= \App\Core\CSRF::field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">CNIC <span class="text-danger">*</span></label>
                <input type="text" name="cnic" class="form-control"
                       placeholder="e.g. 3520115500015"
                       value="<?= \App\Core\View::e(\App\Core\Session::getFlash('old')['cnic'] ?? '') ?>"
                       required>
                <div class="form-text">Enter 13-digit CNIC without dashes</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </div>
        </form>

        <hr class="my-3">
        <div class="text-center small">
            <a href="<?= APP_URL ?>/forgot-password" class="text-muted">Forgot Password?</a>
            &nbsp;|&nbsp;
            <a href="<?= APP_URL ?>/register" class="text-primary fw-semibold">New Registration</a>
        </div>
    </div>
</div>

</div>
</div>

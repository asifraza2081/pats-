<?php $pageTitle = 'Forgot Password'; ?>

<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="bi bi-key display-4 text-warning"></i>
            <h4 class="fw-bold mt-2">Reset Password</h4>
            <p class="text-muted small">Enter your CNIC to receive a reset OTP</p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/forgot-password">
            <?= \App\Core\CSRF::field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">CNIC <span class="text-danger">*</span></label>
                <input type="text" name="cnic" class="form-control"
                       placeholder="3520115500015" maxlength="13" required>
                <div class="form-text">13-digit CNIC registered with your account</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                    <i class="bi bi-send me-1"></i> Send OTP
                </button>
            </div>
        </form>

        <hr class="my-3">
        <div class="text-center small">
            <a href="<?= APP_URL ?>/login" class="text-muted">Back to Login</a>
        </div>
    </div>
</div>
</div>
</div>

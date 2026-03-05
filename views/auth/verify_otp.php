<?php $pageTitle = 'Verify OTP'; ?>

<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4 text-center">
        <i class="bi bi-phone display-4 text-success"></i>
        <h4 class="fw-bold mt-2">Verify Your Number</h4>
        <p class="text-muted small">Enter the 6-digit OTP sent to your registered mobile</p>

        <form method="POST" action="<?= APP_URL ?>/verify-otp">
            <?= \App\Core\CSRF::field() ?>

            <div class="mb-3">
                <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold"
                       placeholder="000000" maxlength="6" pattern="\d{6}" autofocus required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> Verify OTP
                </button>
            </div>
        </form>

        <div class="mt-3 small text-muted">OTP expires in 10 minutes</div>
    </div>
</div>
</div>
</div>

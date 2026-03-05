<?php
$old    = \App\Core\Session::getFlash('old') ?? [];
$errors = \App\Core\Session::getFlash('errors') ?? [];
$e = fn(string $f) => $errors[$f][0] ?? null;
?>

<div class="row justify-content-center">
<div class="col-md-8 col-lg-7">

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="bi bi-person-plus display-4 text-primary"></i>
            <h4 class="fw-bold mt-2">Applicant Registration</h4>
            <p class="text-muted small">Create your PATS account using your CNIC</p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/register">
            <?= \App\Core\CSRF::field() ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= $e('name') ? 'is-invalid' : '' ?>"
                           value="<?= \App\Core\View::e($old['name'] ?? '') ?>" required>
                    <?php if ($e('name')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('name')) ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">CNIC <span class="text-danger">*</span></label>
                    <input type="text" name="cnic" id="cnic"
                           class="form-control <?= $e('cnic') ? 'is-invalid' : '' ?>"
                           placeholder="3520115500015"
                           value="<?= \App\Core\View::e($old['cnic'] ?? '') ?>"
                           maxlength="13" required>
                    <?php if ($e('cnic')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('cnic')) ?></div><?php endif; ?>
                    <div class="form-text">13 digits, no dashes</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= $e('email') ? 'is-invalid' : '' ?>"
                           value="<?= \App\Core\View::e($old['email'] ?? '') ?>" required>
                    <?php if ($e('email')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('email')) ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control <?= $e('phone') ? 'is-invalid' : '' ?>"
                           placeholder="03001234567"
                           value="<?= \App\Core\View::e($old['phone'] ?? '') ?>" required>
                    <?php if ($e('phone')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('phone')) ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control <?= $e('password') ? 'is-invalid' : '' ?>"
                           minlength="8" required>
                    <?php if ($e('password')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('password')) ?></div><?php endif; ?>
                    <div class="form-text">Minimum 8 characters</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="form-control <?= $e('password_confirmation') ? 'is-invalid' : '' ?>"
                           required>
                    <?php if ($e('password_confirmation')): ?><div class="invalid-feedback"><?= \App\Core\View::e($e('password_confirmation')) ?></div><?php endif; ?>
                </div>

            </div>

            <div class="alert alert-info mt-3 small">
                <i class="bi bi-info-circle me-1"></i>
                An OTP will be sent to your mobile number for verification.
            </div>

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-person-check me-1"></i> Register
                </button>
            </div>
        </form>

        <hr class="my-3">
        <div class="text-center small">
            Already have an account? <a href="<?= APP_URL ?>/login" class="text-primary fw-semibold">Login here</a>
        </div>
    </div>
</div>
</div>
</div>

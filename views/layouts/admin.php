<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?>Admin | <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'PATS') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/admin.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex" id="adminWrapper">

    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white d-flex flex-column p-0" style="min-width:240px;min-height:100vh">
        <div class="p-3 border-bottom border-secondary">
            <a href="<?= APP_URL ?>/admin/dashboard" class="text-white text-decoration-none fw-bold fs-5">
                <i class="bi bi-shield-check me-2"></i><?= htmlspecialchars($_ENV['APP_NAME'] ?? 'PATS') ?>
            </a>
            <div class="small text-secondary mt-1">Admin Panel</div>
        </div>

        <ul class="nav nav-pills flex-column mt-2 px-2 flex-grow-1">
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/dashboard" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-header small text-secondary px-3 pt-3 pb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.08em">Projects</li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/projects" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/projects') ? 'active' : '' ?>">
                    <i class="bi bi-folder2-open me-2"></i>Projects
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/jobs" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/jobs') ? 'active' : '' ?>">
                    <i class="bi bi-briefcase me-2"></i>Jobs / Posts
                </a>
            </li>

            <li class="nav-header small text-secondary px-3 pt-3 pb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.08em">Operations</li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/centers" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/centers') ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt me-2"></i>Test Centers
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/applications" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/applications') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-person me-2"></i>Applications
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/challans" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/challans') ? 'active' : '' ?>">
                    <i class="bi bi-cash-coin me-2"></i>Fee Challans
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/results" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/results') ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line me-2"></i>Results
                </a>
            </li>

            <li class="nav-header small text-secondary px-3 pt-3 pb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.08em">Communications</li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/sms" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/sms') ? 'active' : '' ?>">
                    <i class="bi bi-chat-dots me-2"></i>Send SMS
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/reports" class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI']??'', '/admin/reports') ? 'active' : '' ?>">
                    <i class="bi bi-download me-2"></i>Reports
                </a>
            </li>
        </ul>

        <div class="p-3 border-top border-secondary small text-secondary">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Admin') ?>
            &nbsp;|&nbsp;
            <a href="<?= APP_URL ?>/logout" class="text-danger text-decoration-none">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow-1 d-flex flex-column">

        <!-- Top Bar -->
        <header class="bg-white shadow-sm px-4 py-2 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold text-muted"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h6>
            <small class="text-muted"><?= date('d M Y') ?></small>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 pt-3">
            <?php $error = \App\Core\Session::getFlash('error'); ?>
            <?php $success = \App\Core\Session::getFlash('success'); ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Page Content -->
        <main class="flex-grow-1 p-4">
            <?= $content ?>
        </main>

        <footer class="text-center small text-muted py-2 bg-white border-top">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'PATS') ?> Admin
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/admin.js"></script>
</body>
</html>

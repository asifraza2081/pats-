<?php
include "db.php";

$job_id = $_GET['job_id'];
$user_id = $_SESSION['user'];
$msg = "";
$show_modal = false;

// =========================
// Step 1: Fetch user info
// =========================
$userRes = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $userRes->fetch_assoc();

// =========================
// Step 2: Calculate profile completion
// =========================
$profileFields = [
    $user['father_name'],
    $user['date_of_birth'],
    $user['permanent_address'],
    $user['postal_address'],
    $user['alt_mobile'],
    $user['province'],
    $user['district'],
    $user['gender'],
    $user['religion'],
    $user['marital_status'],
    $user['occupation'],
    $user['disability'],
    $user['profile_photo']
];

$profileFilled = 0;
foreach ($profileFields as $field) {
    if (!empty($field)) $profileFilled++;
}
$profileTotal = count($profileFields);

// Education completeness
$eduRes = $conn->query("SELECT * FROM education WHERE user_id = $user_id");
$educationFilled = 0;
$educationTotal = 0;
if ($eduRes->num_rows > 0) {
    while ($edu = $eduRes->fetch_assoc()) {
        $educationTotal += 5; // 5 fields per education row
        if (!empty($edu['level'])) $educationFilled++;
        if (!empty($edu['institute'])) $educationFilled++;
        if (!empty($edu['board'])) $educationFilled++;
        if (!empty($edu['passing_year'])) $educationFilled++;
        if (!empty($edu['marks'])) $educationFilled++;
    }
}
if ($educationTotal == 0) $educationTotal = 1;

// Weighted overall completion
$profilePct = ($profileFilled / $profileTotal);
$educationPct = ($educationFilled / $educationTotal);
$completion = round(($profilePct * 75) + ($educationPct * 25));

// Determine progress bar color
if ($completion == 100) $barColor = "bg-success";
elseif ($completion >= 50) $barColor = "bg-warning";
else $barColor = "bg-danger";

// =========================
// Step 3: Block if profile not complete
// =========================
if ($completion < 100) {
    echo "<script>
            alert('Complete your profile 100% before applying.');
            window.location='profile.php';
          </script>";
    exit();
}

// =========================
// Step 4: Fetch test centers
// =========================
$centers = $conn->query("SELECT * FROM test_centers");

// =========================
// Step 5: Process apply form
// =========================
if (isset($_POST['apply'])) {
    $center_id = $_POST['center_id'];

    $check = $conn->query("SELECT * FROM applications 
                           WHERE user_id='$user_id' 
                           AND job_id='$job_id'");

    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO applications 
            (user_id, job_id, center_id) 
            VALUES ('$user_id','$job_id','$center_id')");

        $msg = "Application Submitted Successfully!";
        $show_modal = true;
    } else {
        $msg = "You Already Applied!";
        $show_modal = true;
    }
}
?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>

<style>
    .content form {
        max-width: 500px;
        margin: 30px auto;
        border-radius: 10px;
    }

    .progress {
        height: 20px;
    }

    .modal-body {
        font-size: 16px;
        font-weight: 500;
    }
</style>

<div class="content">
    <div class="container">

        <!-- Profile Completion Bar -->
        <div class="mb-4">
            <label>Profile Completion: <?= $completion ?>%</label>
            <div class="progress">
                <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $completion ?>%;"></div>
            </div>
        </div>

        <!-- Success/Error Modal -->
        <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="applyModalLabel">Application Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $msg ?>
                    </div>
                    <div class="modal-footer">
                        <a href="applications.php" class="btn btn-success">Go to My Applications</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apply Form -->
        <form method="POST" class="p-3 shadow-sm rounded bg-light">
            <h5 class="mb-3">Select Your Test Center</h5>
            <select name="center_id" class="form-control mb-3" required>
                <option value="">Select Test Center</option>
                <?php while ($c = $centers->fetch_assoc()) { ?>
                    <option value="<?= $c['id'] ?>"><?= $c['city'] ?></option>
                <?php } ?>
            </select>

            <button name="apply" class="btn btn-primary w-100">Submit Application</button>
        </form>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if ($show_modal): ?>
        var applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
        applyModal.show();
    <?php endif; ?>
</script>

<?php include("includes/footer.php") ?>
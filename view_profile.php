<?php
include "db.php";

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$id = $_SESSION['user'];

/* ===============================
   FETCH USER DATA
=================================*/
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ===============================
   PROFILE COMPLETION
=================================*/
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
foreach ($profileFields as $f) {
    if (!empty($f)) $profileFilled++;
}
$completion = round(($profileFilled / count($profileFields)) * 100);
?>

<?php include("includes/header.php"); ?>
<?php include("includes/sidebar.php"); ?>
<?php include("includes/top.php"); ?>

<div class="content">
    <div class="container">

        <!-- ================= PROFILE HEADER ================= -->
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img src="<?= $user['profile_photo'] ? 'uploads/' . htmlspecialchars($user['profile_photo']) : 'assets/user.png' ?>"
                    class="rounded-circle border mb-3"
                    style="width:120px;height:120px;object-fit:cover;">

                <h4 class="mb-0"><?= htmlspecialchars($user['name']) ?></h4>
                <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>

                <div class="mt-3">
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:<?= $completion ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $completion ?>% Profile Completed</small>
                </div>

                <a href="profile.php" class="btn btn-warning mt-3">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </a>
            </div>
        </div>

        <!-- ================= PERSONAL INFO ================= -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-fill"></i> Personal Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><strong>Father Name:</strong><br><?= $user['father_name'] ?></div>
                    <div class="col-md-4"><strong>DOB:</strong><br><?= $user['date_of_birth'] ?></div>
                    <div class="col-md-4"><strong>Gender:</strong><br><?= $user['gender'] ?></div>

                    <div class="col-md-4"><strong>Religion:</strong><br><?= $user['religion'] ?></div>
                    <div class="col-md-4"><strong>Marital Status:</strong><br><?= $user['marital_status'] ?></div>
                    <div class="col-md-4"><strong>Disability:</strong><br><?= $user['disability'] ?></div>
                </div>
            </div>
        </div>

        <!-- ================= CONTACT & ADDRESS ================= -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-geo-alt-fill"></i> Contact & Address
            </div>
            <div class="card-body">
                <p><strong>Mobile:</strong> <?= $user['mobile'] ?></p>
                <p><strong>Alternative Mobile:</strong> <?= $user['alt_mobile'] ?></p>
                <p><strong>Province:</strong> <?= $user['province'] ?></p>
                <p><strong>District:</strong> <?= $user['district'] ?></p>
                <p><strong>Permanent Address:</strong><br><?= $user['permanent_address'] ?></p>
                <p><strong>Postal Address:</strong><br><?= $user['postal_address'] ?></p>
            </div>
        </div>

        <!-- ================= EDUCATION ================= -->
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-mortarboard-fill"></i> Education
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Institute</th>
                            <th>Board</th>
                            <th>Year</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $edu = $conn->query("SELECT * FROM education WHERE user_id=$id");
                        if ($edu->num_rows > 0):
                            while ($row = $edu->fetch_assoc()):
                        ?>
                                <tr>
                                    <td><?= $row['level'] ?></td>
                                    <td><?= $row['institute'] ?></td>
                                    <td><?= $row['board'] ?></td>
                                    <td><?= $row['passing_year'] ?></td>
                                    <td><?= $row['marks'] ?></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No education added</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= EXPERIENCE ================= -->
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-briefcase-fill"></i> Experience
            </div>
            <div class="card-body">
                <?php
                $exp = $conn->query("SELECT * FROM experience WHERE user_id=$id");
                if ($exp->num_rows > 0):
                    while ($row = $exp->fetch_assoc()):
                ?>
                        <div class="border rounded p-3 mb-2">
                            <h6 class="mb-1"><?= $row['designation'] ?></h6>
                            <p class="mb-0"><?= $row['company'] ?></p>
                            <small class="text-muted"><?= $row['from_date'] ?> → <?= $row['to_date'] ?></small>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p class="text-muted">No experience added.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include("includes/footer.php"); ?>
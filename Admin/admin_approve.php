<?php
include "../db.php"; // adjust path
session_start();

// Optional: filter by job
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;

// =====================
// Step 1: Fetch Pending Candidates
// =====================
$sql = "
    SELECT a.*, u.name, u.nic, j.job_title, p.project_title
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN jobs j ON a.job_id = j.id
    JOIN projects p ON j.project_id = p.id
    WHERE a.status='Pending'
";

if ($job_id) {
    $sql .= " AND a.job_id = $job_id";
}

$sql .= " ORDER BY a.id ASC";

$candidates = $conn->query($sql);

// =====================
// Step 2: Process Approval
// =====================
if (isset($_POST['approve'])) {
    $approved_ids = $_POST['candidate_ids'] ?? [];

    foreach ($approved_ids as $app_id) {
        // Generate roll number: JOBID + YEAR + AUTOINCREMENT
        $app = $conn->query("SELECT * FROM applications WHERE id=$app_id")->fetch_assoc();
        $roll_prefix = $app['job_id'] . date('Y'); // example: 52026
        $roll_number = $roll_prefix . str_pad($app_id, 4, '0', STR_PAD_LEFT); // e.g. 520260001

        $conn->query("UPDATE applications 
                      SET status='Approved', roll_no='$roll_number' 
                      WHERE id=$app_id");
    }

    echo "<script>alert('Selected candidates approved successfully!'); window.location='admin_approve.php';</script>";
    exit();
}
?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>

<div class="content">
    <div class="container mt-4">
        <h3>Pending Applications</h3>

        <?php if ($candidates->num_rows > 0): ?>
            <form method="POST">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th><input type="checkbox" id="select_all"></th>
                            <th>Sr No</th>
                            <th>Project Title</th>
                            <th>Job Title</th>
                            <th>Candidate Name</th>
                            <th>NIC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sr = 1;
                        while ($row = $candidates->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="candidate_ids[]" value="<?= $row['id'] ?>"></td>
                                <td><?= $sr++ ?></td>
                                <td><?= $row['project_title'] ?></td>
                                <td><?= $row['job_title'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['nic'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <button type="submit" name="approve" class="btn btn-success">Approve Selected</button>
            </form>

            <script>
                // Select All Checkbox
                document.getElementById('select_all').addEventListener('change', function() {
                    let checkboxes = document.querySelectorAll('input[name="candidate_ids[]"]');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            </script>

        <?php else: ?>
            <div class="alert alert-info">No pending applications found.</div>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php") ?>
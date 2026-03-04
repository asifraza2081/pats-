<?php

include "db.php";

$user_id = $_SESSION['user']; // get logged-in user ID

$result = $conn->query("
SELECT a.*, j.job_title, p.project_title, t.city
FROM applications a
JOIN jobs j ON a.job_id = j.id
JOIN projects p ON j.project_id = p.id
JOIN test_centers t ON a.center_id = t.id
WHERE a.user_id = '$user_id'
ORDER BY a.id DESC
");
?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>
<style>
    .table-hover tbody tr:hover {
        background-color: #eef6ff;
        transition: 0.3s;
    }

    .card .table {
        margin-bottom: 0;
    }

    .badge {
        font-size: 14px;
        padding: 0.45em 0.65em;
    }

    .btn-sm {
        font-size: 13px;
    }

    h3 {
        font-weight: 600;
    }
</style>
<div class="content">
    <div class="container mt-4">
        <h3 class="mb-3">My Applications</h3>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-primary text-center">
                        <tr>
                            <th style="width:120px;">Applicant No</th>
                            <th>Project Name</th>
                            <th>Post</th>
                            <th>Center</th>
                            <th style="width:120px;">Status</th>
                            <th style="width:150px;">Payment Status</th>
                            <th style="width:180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td class="text-center"><?= $row['id'] ?></td>
                                <td><?= $row['project_title'] ?></td>
                                <td><?= $row['job_title'] ?></td>
                                <td><?= $row['city'] ?></td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 'Pending') { ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['payment_status'] == 'Pending') { ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 'Pending') { ?>
                                        <a href="challan.php?id=<?= $row['id'] ?>"
                                            class="btn btn-primary btn-sm px-3">Print Challan</a>
                                    <?php } else { ?>
                                        <a href="roll_slip.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success btn-sm px-3">Print Roll No Slip</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
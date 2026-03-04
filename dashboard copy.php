<?php include "db.php";
if (!isset($_SESSION['user'])) header("location:login.php");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<?php include "includes/sidebar.php"; ?>

<div class="content">

    <h2>Applicant Portal</h2>

    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card card-modern p-3">
                <h5>Total Jobs</h5>
                <?php
                $j = $conn->query("SELECT COUNT(*) c FROM jobs")->fetch_assoc();
                echo "<h2>" . $j['c'] . "</h2>";
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-modern p-3">
                <h5>My Applications</h5>
                <?php
                $id = $_SESSION['user'];
                $a = $conn->query("SELECT COUNT(*) c FROM applications WHERE user_id=$id")->fetch_assoc();
                echo "<h2>" . $a['c'] . "</h2>";
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-modern p-3">
                <h5>Status</h5>
                <p>Check your job status here</p>
            </div>
        </div>

    </div>
</div>
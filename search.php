<?php include "db.php"; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5 col-md-6">
    <h3>Search Roll No Slip</h3>

    <form method="post">
        <input name="cnic" class="form-control mb-2" placeholder="Enter CNIC without dash">
        <button class="btn btn-primary w-100">Search</button>
    </form>

    <?php
    if ($_POST) {
        $cnic = $_POST['cnic'];

        $q = $conn->query("
SELECT s.name, s.cnic, j.title, a.roll_no, a.center
FROM applications a
JOIN students s ON s.id=a.student_id
JOIN jobs j ON j.id=a.job_id
WHERE s.cnic='$cnic'
");

        while ($r = $q->fetch_assoc()) {
            echo "<div class='card p-3 mt-3'>";
            echo "<h5>" . $r['name'] . "</h5>";
            echo "Job: " . $r['title'] . "<br>";
            echo "Roll No: " . $r['roll_no'] . "<br>";
            echo "Center: " . $r['center'] . "<br>";
            echo "<a href='slip.php?roll=" . $r['roll_no'] . "' class='btn btn-success mt-2'>Download Slip</a>";
            echo "</div>";
        }
    }
    ?>
</div>
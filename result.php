<?php include "db.php"; ?>

<div class="container mt-5 col-md-6">
    <h3>Check Result</h3>

    <form method="post">
        <input name="cnic" class="form-control mb-2" placeholder="Enter CNIC">
        <button class="btn btn-success w-100">Check</button>
    </form>

    <?php
    if ($_POST) {
        $cnic = $_POST['cnic'];

        $q = $conn->query("
SELECT s.name, j.title, a.marks, a.result
FROM applications a
JOIN students s ON s.id=a.student_id
JOIN jobs j ON j.id=a.job_id
WHERE s.cnic='$cnic'
");

        while ($r = $q->fetch_assoc()) {
            echo "<div class='card p-3 mt-3'>";
            echo "<b>" . $r['name'] . "</b><br>";
            echo "Job: " . $r['title'] . "<br>";
            echo "Marks: " . $r['marks'] . "<br>";
            echo "Result: " . $r['result'] . "<br>";
            echo "</div>";
        }
    }
    ?>
</div>
<?php
include "db.php";
$roll = $_GET['roll'];

$r = $conn->query("
SELECT s.name, s.cnic, j.title, a.roll_no, a.center
FROM applications a
JOIN students s ON s.id=a.student_id
JOIN jobs j ON j.id=a.job_id
WHERE a.roll_no='$roll'
")->fetch_assoc();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5 border p-4">
    <h2 class="text-center">Roll Number Slip</h2>
    <hr>

    Name: <?= $r['name'] ?><br>
    CNIC: <?= $r['cnic'] ?><br>
    Job: <?= $r['title'] ?><br>
    Roll No: <?= $r['roll_no'] ?><br>
    Center: <?= $r['center'] ?><br>

    <hr>
    <p class="text-danger">Bring original CNIC on test day.</p>

    <button onclick="window.print()" class="btn btn-primary">Print</button>
</div>
<?php include "../db.php";
if (!isset($_SESSION['admin'])) header("location:admin_login.php");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="d-flex">

    <div class="bg-dark text-white p-3" style="width:250px;height:100vh">
        <h4>Admin Panel</h4>
        <a href="admin_dashboard.php" class="text-white d-block">Dashboard</a>
        <a href="add_jobs.php" class="text-white d-block">Add Job</a>
        <a href="students.php" class="text-white d-block">Students</a>
        <a href="applications.php" class="text-white d-block">Applications</a>
        <a href="logout.php" class="text-danger d-block">Logout</a>
    </div>

    <div class="p-4 w-100">
        <h2>Welcome Admin</h2>
        <p>Manage student portal here.</p>
    </div>

</div>
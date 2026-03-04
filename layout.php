<?php
include "db.php";

if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit();
}
?>

<?php include("includes/header.php") ?>
<!-- ===== Sidebar ===== -->
<?php include("includes/sidebar.php") ?>
<!-- ===== Header ===== -->
<?php include("includes/top.php") ?>


<!-- ===== Main Content ===== -->
<div class="content">

</div>

<?php include("includes/footer.php") ?>
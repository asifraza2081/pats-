<?php include "../db.php";

if ($_POST) {
    $u = $_POST['username'];
    $p = md5($_POST['password']);

    $q = $conn->query("SELECT * FROM admins 
WHERE username='$u' AND password='$p'");

    if ($q->num_rows) {
        $_SESSION['admin'] = 1;
        header("location:admin_dashboard.php");
    } else {
        echo "Invalid Login";
    }
}
?>

<div class="container mt-5 col-md-4">
    <h3>Admin Login</h3>
    <form method="post">
        <input name="username" class="form-control mb-2">
        <input name="password" type="password" class="form-control mb-2">
        <button class="btn btn-dark w-100">Login</button>
    </form>
</div>
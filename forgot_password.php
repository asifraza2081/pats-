<?php
include "db.php";
$msg = "";
$success = "";

if (isset($_POST['reset'])) {

    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $newpass = $_POST['new_password'];
    $cpass = $_POST['confirm_password'];

    if ($newpass != $cpass) {
        $msg = "Passwords do not match!";
    } else {

        $check = $conn->query("SELECT id FROM users WHERE nic='$nic'");

        if ($check->num_rows == 1) {

            $hash = password_hash($newpass, PASSWORD_DEFAULT);

            $conn->query("UPDATE users SET password='$hash' WHERE nic='$nic'");

            $success = "Password updated successfully! You can login now.";
        } else {
            $msg = "NIC not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: linear-gradient(135deg,#4e73df,#1cc88a);">

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-5">

                <div class="card shadow">
                    <div class="card-header text-white text-center py-3" style="background:#4e73df;">
                        <h4>Reset Password</h4>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($msg): ?>
                            <div class="alert alert-danger text-center"><?= $msg ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success text-center"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="post">

                            <input type="text"
                                class="form-control mb-3"
                                name="nic"
                                placeholder="CNIC (11111-1111111-1)"
                                maxlength="15"
                                onkeypress="return onlyDigitsToast(event)"
                                oninput="formatCNIC(this)"
                                value="<?= $_POST['nic'] ?? '' ?>"
                                required>



                            <input type="password"
                                class="form-control mb-3"
                                name="new_password"
                                placeholder="New Password"
                                required>

                            <input type="password"
                                class="form-control mb-3"
                                name="confirm_password"
                                placeholder="Confirm Password"
                                required>

                            <button type="submit"
                                name="reset"
                                class="btn btn-primary w-100">
                                Reset Password
                            </button>

                            <p class="text-center mt-3">
                                <a href="login.php">Back to Login</a>
                            </p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function formatCNIC(input) {
    // keep digits only
    let value = input.value.replace(/\D/g, '');

    // max 13 digits
    value = value.substring(0, 13);

    let formatted = '';

    if (value.length > 5) {
    formatted = value.substring(0, 5) + '-' + value.substring(5);
    } else {
    formatted = value;
    }

    if (value.length > 12) {
    formatted = value.substring(0, 5) + '-' +
    value.substring(5, 12) + '-' +
    value.substring(12);
    }

    input.value = formatted;
    }
    </script>

</body>

</html>
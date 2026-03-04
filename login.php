<?php
include "db.php";
$msg = "";

if (isset($_POST['login'])) {

    $nic = $_POST['nic'];
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE nic='$nic'");
    if ($res->num_rows == 1) {
        $row = $res->fetch_assoc();

        if (password_verify($pass, $row['password'])) {
            $_SESSION['user'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];           
            $_SESSION['user_photo'] = $row['profile_photo'];
            
            header("Location: dashboard.php");
        } else {
            $msg = "Invalid Password!";
        }
    } else {
        $msg = "NIC not found!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1cc88a, #4e73df);
        }

        .card {

            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .card-header {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 15px 15px 0 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #1cc88a;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">

            <div class="col-md-4">
                <div class="card">

                    <div class="card-header text-center text-white py-3">
                        <h4><i class="bi bi-box-arrow-in-right"></i> Login</h4>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($msg): ?>
                            <div class="alert alert-danger text-center py-2"><?= $msg ?></div>
                        <?php endif; ?>

                        <form method="post">

                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                <input type="text"
                                    class="form-control"
                                    id="nic"
                                    name="nic"
                                    placeholder="CNIC (11111-1111111-1)"
                                    maxlength="15"
                                    onkeypress="return onlyDigitsToast(event)"
                                    oninput="formatCNIC(this)"
                                    value="<?= isset($_POST['nic']) ? $_POST['nic'] : '' ?>"
                                    required>
                            </div>

                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="loginPass" name="password" placeholder="Password" required>
                                <span class="input-group-text" onclick="togglePass('loginPass')" style="cursor:pointer">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            <p class="text-end mt-2">
                                <a href="forgot_password.php" class="text-decoration-none">
                                    Forgot Password?
                                </a>
                            </p>
                            <button name="login" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bi bi-unlock"></i> Login
                            </button>

                            <p class="text-center mt-3">
                                Don’t have an account?
                                <a href="register.php" class="fw-bold text-decoration-none">Register</a>
                            </p>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePass(id) {
            let x = document.getElementById(id);
            x.type = (x.type === "password") ? "text" : "password";
        }

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
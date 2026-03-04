<?php
include "db.php";

if (isset($_POST['nic'])) {

    $nic = $_POST['nic'];

    $check = $conn->query("SELECT id FROM users WHERE nic='$nic'");

    if ($check->num_rows > 0) {
        echo "exists";
    } else {
        echo "available";
    }
}

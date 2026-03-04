<?php include "db.php";
$data = $conn->query("SELECT * FROM students");
?>

<div class="container mt-4">
    <h3>Students</h3>

    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>CNIC</th>
        </tr>

        <?php while ($s = $data->fetch_assoc()) { ?>
            <tr>
                <td><?= $s['name'] ?></td>
                <td><?= $s['email'] ?></td>
                <td><?= $s['phone'] ?></td>
                <td><?= $s['cnic'] ?></td>
            </tr>
        <?php } ?>

    </table>
</div>
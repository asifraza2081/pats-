<?php

include "db.php";

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$id = $_SESSION['user'];
$msg = "";


// 1. Save education data if submitted
if (isset($_POST['level']) && is_array($_POST['level'])) {
    $conn->query("DELETE FROM education WHERE user_id=$id");
    foreach ($_POST['level'] as $key => $level) {
        $institute = $_POST['institute'][$key] ?? '';
        $board = $_POST['board'][$key] ?? '';
        $year = $_POST['year'][$key] ?? '';
        $marks = $_POST['marks'][$key] ?? '';

        if (trim($level) !== '' && trim($institute) !== '') {
            $stmt = $conn->prepare("INSERT INTO education (user_id, level, institute, board, passing_year, marks) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $id, $level, $institute, $board, $year, $marks);
            $stmt->execute();
        }
    }
}

// 2. Save experience data if submitted
if (isset($_POST['company']) && is_array($_POST['company'])) {
    $conn->query("DELETE FROM experience WHERE user_id=$id");
    foreach ($_POST['company'] as $key => $company) {
        $designation = $_POST['designation'][$key] ?? '';
        $from = $_POST['from_date'][$key] ?? '';
        $to = $_POST['to_date'][$key] ?? '';

        if (trim($company) !== '') {
            $stmt = $conn->prepare("INSERT INTO experience (user_id, company, designation, from_date, to_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id, $company, $designation, $from, $to);
            $stmt->execute();
        }
    }
}

/* ===============================
   HANDLE PROFILE UPDATE
=================================*/
if (isset($_POST['update'])) {

    $father_name = $_POST['father_name'];
    $dob = $_POST['date_of_birth'];
    $permanent_address = $_POST['permanent_address'];
    $postal_address = $_POST['postal_address'];
    $alt_mobile = $_POST['alt_mobile'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $marital_status = $_POST['marital_status'];
    $occupation = $_POST['occupation'];
    $disability = $_POST['disability'];

    // Fetch old photo first
    $stmtOld = $conn->prepare("SELECT profile_photo FROM users WHERE id=?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $oldData = $stmtOld->get_result()->fetch_assoc();
    $photoName = $oldData['profile_photo'];

    // Photo upload
    if (!empty($_FILES['profile_photo']['name'])) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (
            in_array($_FILES['profile_photo']['type'], $allowedTypes)
            && $_FILES['profile_photo']['size'] <= $maxSize
        ) {

            $photoName = time() . "_" . basename($_FILES['profile_photo']['name']);
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/" . $photoName);
        }
    }

    $stmt = $conn->prepare("UPDATE users SET 
        father_name=?,
        date_of_birth=?,
        permanent_address=?,
        postal_address=?,
        alt_mobile=?,
        province=?,
        district=?,
        gender=?,
        religion=?,
        marital_status=?,
        occupation=?,
        disability=?,
        profile_photo=?
        WHERE id=?");

    $stmt->bind_param(
        "sssssssssssssi",
        $father_name,
        $dob,
        $permanent_address,
        $postal_address,
        $alt_mobile,
        $province,
        $district,
        $gender,
        $religion,
        $marital_status,
        $occupation,
        $disability,
        $photoName,
        $id
    );

    if ($stmt->execute()) {
        header("Location: profile.php?updated=1");
        exit();
    }
}

/* ===============================
   FETCH USER DATA (Always Fresh)
=================================*/
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch existing education rows for this user
$educationRows = '';
$eduRes = $conn->query("SELECT * FROM education WHERE user_id = $id ORDER BY id ASC");
if ($eduRes->num_rows > 0) {
    while ($edu = $eduRes->fetch_assoc()) {
        $educationRows .= "<tr>
            <td>
                <select name='level[]' class='form-control' required>
                    <option " . ($edu['level'] == 'Matric' ? 'selected' : '') . ">Matric</option>
                    <option " . ($edu['level'] == 'Intermediate' ? 'selected' : '') . ">Intermediate</option>
                    <option " . ($edu['level'] == 'Bachelor' ? 'selected' : '') . ">Bachelor</option>
                    <option " . ($edu['level'] == 'Master' ? 'selected' : '') . ">Master</option>
                    <option " . ($edu['level'] == 'PhD' ? 'selected' : '') . ">PhD</option>
                </select>
            </td>
            <td><input type='text' name='institute[]' value='" . htmlspecialchars($edu['institute']) . "' class='form-control' required></td>
            <td><input type='text' name='board[]' value='" . htmlspecialchars($edu['board']) . "' class='form-control' required></td>
            <td><input type='text' name='year[]' value='" . htmlspecialchars($edu['passing_year']) . "' class='form-control' required></td>
            <td><input type='text' name='marks[]' value='" . htmlspecialchars($edu['marks']) . "' class='form-control' required></td>
            <td><button type='button' class='btn btn-danger btn-sm' onclick='this.closest(\"tr\").remove()'>X</button></td>
        </tr>";
    }
}

// Fetch existing experience rows for this user
$experienceRows = '';
$expRes = $conn->query("SELECT * FROM experience WHERE user_id = $id ORDER BY id ASC");
if ($expRes->num_rows > 0) {
    while ($exp = $expRes->fetch_assoc()) {
        $experienceRows .= "<tr>
            <td><input type='text' name='company[]' value='" . htmlspecialchars($exp['company']) . "' class='form-control'></td>
            <td><input type='text' name='designation[]' value='" . htmlspecialchars($exp['designation']) . "' class='form-control'></td>
            <td><input type='date' name='from_date[]' value='" . htmlspecialchars($exp['from_date']) . "' class='form-control'></td>
            <td><input type='date' name='to_date[]' value='" . htmlspecialchars($exp['to_date']) . "' class='form-control'></td>
            <td><button type='button' class='btn btn-danger btn-sm' onclick='this.closest(\"tr\").remove()'>X</button></td>
        </tr>";
    }
}

/* ===============================
   SUCCESS MESSAGE
=================================*/
if (isset($_GET['updated'])) {
    $msg = "Profile Updated Successfully!";
}

/* ===============================
   PROFILE COMPLETION
=================================*/
$fields = [
    $user['father_name'],
    $user['date_of_birth'],
    $user['permanent_address'],
    $user['postal_address'],
    $user['alt_mobile'],
    $user['province'],
    $user['district'],
    $user['gender'],
    $user['religion'],
    $user['marital_status'],
    $user['occupation'],
    $user['disability'],
    $user['profile_photo']
];

$filled = 0;

foreach ($fields as $field) {
    if (!empty($field)) {
        $filled++;
    }
}

$completion = round(($filled / count($fields)) * 100);

if (isset($_POST['level'])) {

    $conn->query("DELETE FROM education WHERE user_id=$id");

    foreach ($_POST['level'] as $key => $level) {

        $institute = $_POST['institute'][$key];
        $board = $_POST['board'][$key];
        $year = $_POST['year'][$key];
        $marks = $_POST['marks'][$key];

        $stmt = $conn->prepare("INSERT INTO education 
            (user_id, level, institute, board, passing_year, marks)
            VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("isssss", $id, $level, $institute, $board, $year, $marks);
        $stmt->execute();
    }
}
if (isset($_POST['company'])) {

    $conn->query("DELETE FROM experience WHERE user_id=$id");

    foreach ($_POST['company'] as $key => $company) {

        $designation = $_POST['designation'][$key];
        $from = $_POST['from_date'][$key];
        $to = $_POST['to_date'][$key];

        $stmt = $conn->prepare("INSERT INTO experience 
            (user_id, company, designation, from_date, to_date)
            VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("issss", $id, $company, $designation, $from, $to);
        $stmt->execute();
    }
}

?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>

<div class="content">
    <div class="container">

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Profile</h4>
            </div>

            <div class="card-body">
                <?php if ($msg): ?>
                    <div id="profileMsg" class="alert alert-success"><?= $msg ?></div>

                    <script>
                        // Hide message after 3 seconds (3000ms)
                        setTimeout(function() {
                            const msg = document.getElementById('profileMsg');
                            if (msg) {
                                msg.style.transition = "opacity 0.5s";
                                msg.style.opacity = "0";
                                setTimeout(() => msg.remove(), 500); // remove from DOM after fade
                            }
                        }, 1000);
                    </script>
                <?php endif; ?>
                <?php
                if ($completion == 100) {
                    $barColor = "bg-success";
                } elseif ($completion >= 50) {
                    $barColor = "bg-warning";
                } else {
                    $barColor = "bg-danger";
                }
                ?>

                <div class="mb-4">
                    <label>Profile Completion: <?= $completion ?>%</label>
                    <div class="progress">
                        <div class="progress-bar <?= $barColor ?>"
                            role="progressbar"
                            style="width: <?= $completion ?>%;"
                            aria-valuenow="<?= $completion ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            <?= $completion ?>%
                        </div>
                    </div>
                </div>
                <form method="post" enctype="multipart/form-data">

                    <div class="row">

                        <!-- NAME (Disabled) -->
                        <div class="col-md-4 mb-3">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="<?= $user['name'] ?>" disabled>
                        </div>
                        <!-- FATHER NAME -->
                        <div class="col-md-4 mb-3">
                            <label>Father Name</label>
                            <input type="text" name="father_name" value="<?= $user['father_name'] ?>" class="form-control" required>
                        </div>
                        <!-- NIC (Disabled) -->
                        <div class="col-md-4 mb-3">
                            <label>NIC</label>
                            <input type="text" class="form-control" value="<?= $user['nic'] ?>" disabled>
                        </div>

                        <!-- EMAIL (Disabled) -->
                        <div class="col-md-3 mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= $user['email'] ?>" disabled>
                        </div>

                        <!-- DOB -->
                        <div class="col-md-3 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?>" class="form-control">
                        </div>

                        <!-- MOBILE (Disabled) -->
                        <div class="col-md-3 mb-3">
                            <label>Mobile</label>
                            <input type="text" class="form-control" value="<?= $user['mobile'] ?>" disabled>
                        </div>

                        <!-- ALT MOBILE -->
                        <div class="col-md-3 mb-3">
                            <label>Alternative Mobile</label>
                            <input type="text" name="alt_mobile" value="<?= $user['alt_mobile'] ?>" class="form-control">
                        </div>

                        <!-- PERMANENT ADDRESS -->
                        <div class="col-md-6 mb-3">
                            <label>Permanent Address</label>
                            <textarea name="permanent_address" id="permAddress" class="form-control"><?= $user['permanent_address'] ?></textarea>
                            <!-- CHECKBOX -->
                            <div class=" mb-2">
                                <input type="checkbox" onclick="copyAddress()"> Same as Permanent Address
                            </div>
                        </div>

                        <!-- POSTAL ADDRESS -->
                        <div class="col-md-6 mb-3">
                            <label>Postal Address</label>
                            <textarea name="postal_address" id="postalAddress" class="form-control"><?= $user['postal_address'] ?></textarea>
                        </div>

                        <!-- PROVINCE -->
                        <div class="col-md-4 mb-3">
                            <label>Province</label>
                            <input type="text" name="province" value="<?= $user['province'] ?>" class="form-control">
                        </div>

                        <!-- DISTRICT -->
                        <div class="col-md-4 mb-3">
                            <label>District</label>
                            <input type="text" name="district" value="<?= $user['district'] ?>" class="form-control">
                        </div>
                        <!-- RELIGION -->
                        <div class="col-md-4 mb-3">
                            <label>Religion</label>
                            <input type="text" name="religion" value="<?= $user['religion'] ?>" class="form-control">
                        </div>
                        <!-- GENDER -->
                        <div class="col-md-4 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option <?= $user['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                <option <?= $user['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                            </select>
                        </div>



                        <!-- MARITAL STATUS -->
                        <div class="col-md-4 mb-3">
                            <label>Marital Status</label>
                            <select name="marital_status" class="form-control">
                                <option <?= $user['marital_status'] == "Single" ? "selected" : "" ?>>Single</option>
                                <option <?= $user['marital_status'] == "Married" ? "selected" : "" ?>>Married</option>
                            </select>
                        </div>

                        <!-- DISABILITY -->
                        <div class="col-md-4 mb-3">
                            <label>Disability</label>
                            <select name="disability" class="form-control">
                                <option <?= $user['disability'] == "No" ? "selected" : "" ?>>No</option>
                                <option <?= $user['disability'] == "Yes" ? "selected" : "" ?>>Yes</option>
                            </select>
                        </div>

                        <!-- OCCUPATION -->
                        <div class="col-md-4 mb-3">
                            <label>Current Occupation</label>
                            <input type="text" name="occupation" value="<?= $user['occupation'] ?>" class="form-control">
                        </div>


                        <!-- PHOTO -->
                        <div class="col-md-4 mb-3">
                            <label>Upload Photo</label>
                            <input type="file" name="profile_photo" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3 text-center">
                            <?php if ($user['profile_photo']) { ?>
                                <img src="uploads/<?= $user['profile_photo'] ?>" width="80" height="80" class="rounded-circle">
                            <?php } ?>
                        </div>

                    </div>
                    <div class="col-md-12 mb-3">
                        <h5 class="mt-4">Education Details</h5>
                        <table class="table table-bordered" id="educationTable">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Institute</th>
                                    <th>Board/University</th>
                                    <th>Year</th>
                                    <th>Marks/CGPA</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody><?= $educationRows ?></tbody>
                        </table>

                        <button type="button" class="btn btn-primary btn-sm text-right" onclick="addEducationRow()">+ Add Education</button>
                    </div>
                    <div class="col-md-12 mb-3">
                        <h5 class="mt-4">Experience Details</h5>

                        <table class="table table-bordered" id="experienceTable">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Designation</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody><?= $experienceRows ?></tbody>
                        </table>

                        <button type="button" class="btn btn-info btn-sm" onclick="addExperienceRow()">+ Add Experience</button>
                    </div>


                    <button type="submit" name="update" class="btn btn-success">Update Profile</button>

                </form>

            </div>
        </div>

    </div>
</div>

<script>
    function addEducationRow() {
        let table = document.getElementById("educationTable").getElementsByTagName('tbody')[0];

        let row = table.insertRow();

        row.innerHTML = `
        <td>
            <select name="level[]" class="form-control" required>
                <option>Matric</option>
                <option>Intermediate</option>
                <option>Bachelor</option>
                <option>Master</option>
                <option>PhD</option>
            </select>
        </td>
        <td><input type="text" name="institute[]" class="form-control" required></td>
        <td><input type="text" name="board[]" class="form-control" required></td>
        <td><input type="text" name="year[]" class="form-control" required></td>
        <td><input type="text" name="marks[]" class="form-control" required></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
    `;
    }
</script>
<script>
    function addExperienceRow() {
        let table = document.getElementById("experienceTable").getElementsByTagName('tbody')[0];

        let row = table.insertRow();

        row.innerHTML = `
        <td><input type="text" name="company[]" class="form-control"></td>
        <td><input type="text" name="designation[]" class="form-control"></td>
        <td><input type="date" name="from_date[]" class="form-control"></td>
        <td><input type="date" name="to_date[]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
    `;
    }
</script>

<?php include("includes/footer.php") ?>
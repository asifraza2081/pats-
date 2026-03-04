<?php
include "db.php";

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$id = $_SESSION['user'];
$msg = "";

/* ===============================
   FETCH USER DATA (Always Fresh)
=================================*/
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ===============================
   FETCH EXISTING EDUCATION & EXPERIENCE
=================================*/
// Education
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

// Experience
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
   HANDLE FORM SUBMISSION
=================================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Save education data if submitted
    if (isset($_POST['level']) && is_array($_POST['level'])) {
        $conn->query("DELETE FROM education WHERE user_id=$id");
        foreach ($_POST['level'] as $key => $level) {
            $institute = $_POST['institute'][$key] ?? '';
            $board = $_POST['board'][$key] ?? '';
            $year = $_POST['year'][$key] ?? '';
            $marks = $_POST['marks'][$key] ?? '';

            if (trim($level) !== '' && trim($institute) !== '') {
                $stmtEdu = $conn->prepare("INSERT INTO education (user_id, level, institute, board, passing_year, marks) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtEdu->bind_param("isssss", $id, $level, $institute, $board, $year, $marks);
                $stmtEdu->execute();
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
                $stmtExp = $conn->prepare("INSERT INTO experience (user_id, company, designation, from_date, to_date) VALUES (?, ?, ?, ?, ?)");
                $stmtExp->bind_param("issss", $id, $company, $designation, $from, $to);
                $stmtExp->execute();
            }
        }
    }

    // 3. Profile update
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

        // Photo upload with validation
        // Photo upload with delete old photo
        if (!empty($_FILES['profile_photo']['name'])) {

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (
                in_array($_FILES['profile_photo']['type'], $allowedTypes) &&
                $_FILES['profile_photo']['size'] <= $maxSize
            ) {

                // 🔥 DELETE OLD PHOTO (if exists)
                if (!empty($photoName) && file_exists("uploads/" . $photoName)) {
                    unlink("uploads/" . $photoName);
                }

                // ✅ Upload new photo
                $photoName = time() . "_" . uniqid() . "_" . basename($_FILES['profile_photo']['name']);
                move_uploaded_file(
                    $_FILES['profile_photo']['tmp_name'],
                    "uploads/" . $photoName
                );
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
/* ===============================
   PROFILE COMPLETION INCLUDING EDUCATION & EXPERIENCE
=================================*/

// Count filled profile fields (your existing ones)
$profileFields = [
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

$profileFilled = 0;
foreach ($profileFields as $field) {
    if (!empty($field)) {
        $profileFilled++;
    }
}
$profileTotal = count($profileFields);

// Calculate education completeness
$eduRes = $conn->query("SELECT * FROM education WHERE user_id = $id");
$educationFilled = 0;
$educationTotal = 0;
if ($eduRes->num_rows > 0) {
    while ($edu = $eduRes->fetch_assoc()) {
        $educationTotal += 5; // 5 fields per education row
        if (!empty($edu['level'])) $educationFilled++;
        if (!empty($edu['institute'])) $educationFilled++;
        if (!empty($edu['board'])) $educationFilled++;
        if (!empty($edu['passing_year'])) $educationFilled++;
        if (!empty($edu['marks'])) $educationFilled++;
    }
}



// To avoid division by zero if no education or experience rows, set minimum total to 1
if ($educationTotal == 0) $educationTotal = 1;


// Calculate weighted overall completion
// Weight profile fields = 50%, education = 25%, experience = 25%
$profilePct = ($profileFilled / $profileTotal);
$educationPct = ($educationFilled / $educationTotal);


$completion = round(($profilePct * 75) + ($educationPct * 25));

// Determine progress bar color
if ($completion == 100) {
    $barColor = "bg-success";
} elseif ($completion >= 50) {
    $barColor = "bg-warning";
} else {
    $barColor = "bg-danger";
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
                                setTimeout(() => msg.remove(), 500);
                            }
                        }, 1000);
                    </script>
                <?php endif; ?>

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
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" disabled>
                        </div>

                        <!-- FATHER NAME -->
                        <div class="col-md-4 mb-3">
                            <label>Father Name</label>
                            <input type="text" name="father_name" value="<?= htmlspecialchars($user['father_name']) ?>" class="form-control" required>
                        </div>

                        <!-- NIC (Disabled) -->
                        <div class="col-md-4 mb-3">
                            <label>NIC</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['nic']) ?>" disabled>
                        </div>

                        <!-- EMAIL (Disabled) -->
                        <div class="col-md-3 mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>

                        <!-- DOB -->
                        <div class="col-md-3 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth']) ?>" class="form-control">
                        </div>

                        <!-- MOBILE (Disabled) -->
                        <div class="col-md-3 mb-3">
                            <label>Mobile</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>" disabled>
                        </div>

                        <!-- ALT MOBILE -->
                        <div class="col-md-3 mb-3">
                            <label>Alternative Mobile</label>
                            <input type="text" name="alt_mobile" value="<?= htmlspecialchars($user['alt_mobile']) ?>" class="form-control">
                        </div>

                        <!-- PERMANENT ADDRESS -->
                        <div class="col-md-6 mb-3">
                            <label>Permanent Address</label>
                            <textarea name="permanent_address" id="permAddress" class="form-control"><?= htmlspecialchars($user['permanent_address']) ?></textarea>
                            <div class="mb-2">
                                <input type="checkbox" onclick="copyAddress()"> Same as Permanent Address
                            </div>
                        </div>

                        <!-- POSTAL ADDRESS -->
                        <div class="col-md-6 mb-3">
                            <label>Postal Address</label>
                            <textarea name="postal_address" id="postalAddress" class="form-control"><?= htmlspecialchars($user['postal_address']) ?></textarea>
                        </div>

                        <!-- PROVINCE -->
                        <div class="col-md-4 mb-3">
                            <label>Province</label>
                            <select name="province" id="province" class="form-control" onchange="loadDistricts()">
                                <option value="">Select Province</option>
                                <option value="Punjab" <?= $user['province'] == "Punjab" ? "selected" : "" ?>>Punjab</option>
                                <option value="south Punjab" <?= $user['province'] == "South Punjab" ? "selected" : "" ?>>South Punjab</option>
                                <option value="Sindh" <?= $user['province'] == "Sindh" ? "selected" : "" ?>>Sindh</option>
                                <option value="KPK" <?= $user['province'] == "KPK" ? "selected" : "" ?>>KPK</option>
                                <option value="Balochistan" <?= $user['province'] == "Balochistan" ? "selected" : "" ?>>Balochistan</option>
                                <option value="Gilgit Baltistan" <?= $user['province'] == "Gilgit Baltistan" ? "selected" : "" ?>>Gilgit Baltistan</option>
                            </select>
                        </div>

                        <!-- DISTRICT -->
                        <div class="col-md-4 mb-3">
                            <label>District</label>

                            <select name="district" id="district" class="form-control">
                                <option value="<?= htmlspecialchars($user['district']) ?>">
                                    <?= htmlspecialchars($user['district']) ?>
                                </option>
                            </select>
                        </div>

                        <!-- RELIGION -->
                        <div class="col-md-4 mb-3">
                            <label>Religion</label>
                            <select name="religion" class="form-control">
                                <option value="">Select Religion</option>
                                <option value="Islam" <?= $user['religion'] == "Islam" ? "selected" : "" ?>>Islam</option>
                                <option value="Hindu" <?= $user['religion'] == "Hindu" ? "selected" : "" ?>>Hindu</option>
                                <option value="Christian" <?= $user['religion'] == "Christian" ? "selected" : "" ?>>Christian</option>
                                <option value="Other" <?= $user['religion'] == "Other" ? "selected" : "" ?>>Other</option>
                            </select>
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
                            <input type="text" name="occupation" value="<?= htmlspecialchars($user['occupation']) ?>" class="form-control">
                        </div>

                        <!-- PHOTO -->
                        <div class="col-md-4 mb-3">
                            
                            <div class="border rounded p-3 text-center">
                                <input type="file" name="profile_photo" id="photoInput"
                                    class="form-control d-none" accept="image/*"
                                    onchange="previewPhoto(this)">
                                <label for="photoInput" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-upload"></i> Choose Photo
                                </label>
                                <small class="text-muted d-block mt-1">JPG / PNG (Max 2MB)</small>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 text-center">
                            <img id="photoPreview"
                                src="<?= $user['profile_photo'] ? 'uploads/' . htmlspecialchars($user['profile_photo']) : '' ?>"
                                class="rounded-circle border"
                                style="width:100px;height:100px;object-fit:cover;">
                        </div>

                    </div>

                    <!-- Education Table -->
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
                            <tbody>
                                <?= $educationRows ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm text-right" onclick="addEducationRow()">+ Add Education</button>
                    </div>

                    <!-- Experience Table -->
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
                            <tbody>
                                <?= $experienceRows ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info btn-sm" onclick="addExperienceRow()">+ Add Experience</button>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" name="update" class="btn btn-success w-100" style="max-width: 350px;">
                            Update Profile
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Copy permanent address to postal address checkbox handler
    function copyAddress() {
        const perm = document.getElementById('permAddress').value;
        const post = document.getElementById('postalAddress');
        post.value = perm;
    }

    // Add Education row dynamically
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

    // Add Experience row dynamically
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

<script>
    const districts = {
        Punjab: ["Lahore", "Rawalpindi", "Faisalabad", "Multan", "Gujranwala"],
        Sindh: ["Karachi", "Hyderabad", "Sukkur", "Larkana"],
        KPK: ["Peshawar", "Mardan", "Swat", "Abbottabad"],
        Balochistan: ["Quetta", "Gwadar", "Turbat", "Khuzdar"]
    };

    function loadDistricts() {
        let province = document.getElementById("province").value;
        let districtSelect = document.getElementById("district");

        districtSelect.innerHTML = "<option value=''>Select District</option>";

        if (districts[province]) {
            districts[province].forEach(d => {
                let opt = document.createElement("option");
                opt.value = d;
                opt.textContent = d;
                districtSelect.appendChild(opt);
            });
        }
    }
</script>
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("photoPreview").src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<?php include("includes/footer.php") ?>
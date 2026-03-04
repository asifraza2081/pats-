<?php include "db.php"; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <a class="navbar-brand fw-bold" href="#">PRIME ASSESSMENT & TESTING SERVICES </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#applications">Open Applications</a></li>
            <li class="nav-item"><a class="nav-link" href="#candidates">Candidates</a></li>
            <li class="nav-item"><a class="nav-link" href="#results">Results</a></li>
            <li class="nav-item"><a href="login.php" class="btn btn-light ms-3">Login</a></li>
            <li class="nav-item"><a href="register.php" class="btn btn-warning ms-2">Register</a></li>
        </ul>
    </div>
</nav>

<!-- HERO SECTION -->
<header class="bg-dark text-white text-center p-5">
    <h1 class="display-4 fw-bold">Online Testing & Job Portal</h1>
    <p class="lead">Apply for jobs, download roll number slip, check results online</p>
    <a href="register.php" class="btn btn-lg btn-warning mt-3">Apply Now</a>
</header>

<!-- CAROUSEL -->
<div id="mainCarousel" class="carousel slide mt-4 container" data-bs-ride="carousel">

    <div class="carousel-inner rounded shadow">

        <div class="carousel-item active">
            <img src="images/slide1.jpg" class="d-block w-100" height="400">
            <div class="carousel-caption bg-dark bg-opacity-50 rounded">
                <h4>Apply Online Easily</h4>
            </div>
        </div>

        <div class="carousel-item">
            <img src="images/slide2.jpg" class="d-block w-100" height="400">
            <div class="carousel-caption bg-dark bg-opacity-50 rounded">
                <h4>Download Roll Number Slip</h4>
            </div>
        </div>

        <div class="carousel-item">
            <img src="images/slide3.jpg" class="d-block w-100" height="400">
            <div class="carousel-caption bg-dark bg-opacity-50 rounded">
                <h4>Check Results Online</h4>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>

<!-- OPEN APPLICATION SECTION -->
<section id="applications" class="container mt-5">
    <h2 class="text-center mb-4">Open Applications</h2>

    <div class="row">
        <?php
        $jobs = $conn->query("SELECT * FROM jobs ORDER BY id DESC LIMIT 6");
        while ($row = $jobs->fetch_assoc()) {
        ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5><?= $row['title'] ?></h5>
                        <p>Last Date: <?= $row['last_date'] ?></p>
                        <a href="register.php" class="btn btn-primary w-100">Apply Now</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<!-- CANDIDATE LIST SECTION -->
<?php
$results = [];

if (isset($_POST['cnic'])) {
    $nic = $_POST['cnic'];

    $stmt = $conn->prepare("
        SELECT students.name, students.email, students.cnic,
               jobs.title, applications.roll_no, applications.center
        FROM applications
        JOIN students ON applications.student_id = students.id
        JOIN jobs ON applications.job_id = jobs.id
        WHERE students.cnic = ?
    ");
    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<section id="candidates" class="bg-light py-5">
    <div class="container">

        <h2 class="text-center mb-4">Search Candidate By CNIC</h2>

        <!-- SEARCH FORM -->
        <form method="post" class="row justify-content-center mb-4">
            <div class="col-md-4">
                <input name="cnic" class="form-control"
                    placeholder="Enter CNIC 17301-1234567-1"
                    required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <!-- RESULT TABLE -->
        <?php if (isset($_POST['cnic'])) { ?>

            <?php if ($results->num_rows > 0) { ?>

                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>CNIC</th>
                            <th>Email</th>
                            <th>Job</th>
                            <th>Roll No</th>
                            <th>Center</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['cnic'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['title'] ?></td>
                                <td><?= $row['roll_no'] ?></td>
                                <td><?= $row['center'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            <?php } else { ?>
                <div class="alert alert-danger text-center">
                    No Candidate Found
                </div>
            <?php } ?>

        <?php } ?>

    </div>
</section>

<!-- RESULT SECTION -->
<section id="results" class="container py-5">
    <h2 class="text-center mb-4">Latest Results</h2>

    <div class="row">
        <?php
        $r = $conn->query("SELECT * FROM results ORDER BY id DESC LIMIT 6");
        while ($row = $r->fetch_assoc()) {
        ?>
            <div class="col-md-4 mb-3">
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center">
                        <h5><?= $row['exam_name'] ?></h5>
                        <p>Published: <?= $row['publish_date'] ?></p>
                        <a href="result.php?id=<?= $row['id'] ?>" class="btn btn-success">View Result</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-primary text-white text-center p-3">
    © <?= date("Y") ?> Student Testing Portal | All Rights Reserved
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
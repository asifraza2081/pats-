<?php 
include "../db.php";

if (isset($_POST['submit'])) {

    $project_id = $_POST['project_id'];
    $title = $_POST['job_title'];
    $desc = $_POST['description'];
    $last = $_POST['last_date'];
    $fee = $_POST['fee'];

    $conn->query("INSERT INTO jobs 
    (project_id, job_title, description, last_date, fee) 
    VALUES ('$project_id','$title','$desc','$last','$fee')");

    echo "<div class='alert alert-success'>Job Added Successfully</div>";
}
?>

<div class="container mt-4">
    <h3>Add Job</h3>
    <form method="POST">

        <!-- Project Dropdown -->
        <select name="project_id" class="form-control mb-2" required>
            <option value="">Select Project</option>
            <?php
            $projects = $conn->query("SELECT * FROM projects ORDER BY project_title ASC");
            while($row = $projects->fetch_assoc()){
                echo "<option value='".$row['id']."'>".$row['project_title']."</option>";
            }
            ?>
        </select>

        <input type="text" name="job_title" class="form-control mb-2" placeholder="Job Title" required>

        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

        <input type="date" name="last_date" class="form-control mb-2" required>

        <input type="number" name="fee" class="form-control mb-2" placeholder="Fee" required>

        <button name="submit" class="btn btn-primary">Add Job</button>
    </form>
</div>
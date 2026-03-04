<?php
include "db.php";
$today = date('Y-m-d');

$jobs = $conn->query("
    SELECT jobs.*, projects.project_title 
    FROM jobs
    JOIN projects ON jobs.project_id = projects.id
    WHERE jobs.last_date >= '$today'
    AND jobs.status='Open'
    ORDER BY projects.project_title ASC
");
?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>
<!-- <style>
    .job-table th {
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
    }

    .job-table td {
        vertical-align: middle;
    }

    .project-cell {
        text-align: center;
        vertical-align: middle !important;
        background: #f8f9fa;
        font-weight: bold;
        font-size: 15px;
    }

    .job-table tbody tr:hover {
        background-color: #eef6ff;
        transition: 0.3s;
    }
</style> -->
<style>
    .job-table th {
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
    }

    .job-table td {
        vertical-align: middle;
    }

    .project-cell {
        text-align: center;
        vertical-align: middle !important;
        background: #f8f9fa;
        font-weight: bold;
        font-size: 15px;
    }

    /* Hover effect */
    .job-table tbody tr:hover {
        background-color: #eef6ff;
        transition: 0.3s;
    }
</style>

<div class="content">
    <div class="row g-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Available Jobs</h5>
            </div>

            <div class="card-body p-0">

                <table class="table table-bordered table-striped table-hover mb-0 job-table">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:70px;">Sr No</th>
                            <th style="width:220px;">Project Title</th>
                            <th>Job Title</th>
                            <th style="width:150px;">Last Date</th>
                            <th style="width:120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $sr = 1;
                        $current_project = "";
                        $project_count = [];
                        $jobs_array = [];

                        while ($row = $jobs->fetch_assoc()) {
                            $jobs_array[] = $row;
                            $project_count[$row['project_title']] =
                                ($project_count[$row['project_title']] ?? 0) + 1;
                        }

                        foreach ($jobs_array as $row) {

                            echo "<tr>";

                            echo "<td class='text-center'>" . $sr++ . "</td>";

                            // Project Title Cell (Centered Fully)
                            if ($current_project != $row['project_title']) {
                                $rowspan = $project_count[$row['project_title']];
                                echo "<td rowspan='$rowspan' class='project-cell'>
                " . $row['project_title'] . "
              </td>";
                                $current_project = $row['project_title'];
                            }

                            echo "<td>" . $row['job_title'] . "</td>";

                            echo "<td class='text-center'>
            <span class='badge bg-warning text-dark'>
                " . $row['last_date'] . "
            </span>
          </td>";

                            echo "<td class='text-center'>
            <a href='apply.php?job_id=" . $row['id'] . "' 
               class='btn btn-success btn-sm px-3'>
               Apply
            </a>
          </td>";

                            echo "</tr>";
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>




    </div>
</div>
<?php include("includes/footer.php") ?>
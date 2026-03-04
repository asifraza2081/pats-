<?php
include "db.php";

if (!isset($_SESSION['user'])) {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['user'];

// Stats
$totalJobs = $conn->query("SELECT COUNT(*) as c FROM jobs")->fetch_assoc()['c'];
$totalApps = $conn->query("SELECT COUNT(*) as c FROM applications WHERE user_id='$user_id'")->fetch_assoc()['c'];
?>

<?php include("includes/header.php") ?>
<?php include("includes/sidebar.php") ?>
<?php include("includes/top.php") ?>

<!-- ===== Main Content ===== -->
<div class="content">

    <div class="row g-4">

        <div class="col-md-3">
            <div class="stat-card">
                <h5 class="mt-2">Total Jobs</h5>
                <h3><?= $totalJobs ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h5 class="mt-2">Applied Applications</h5>
                <h3><?= $totalApps ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h5 class="mt-2">Rejected</h5>
                <h3>0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h5 class="mt-2">Fee Paid</h5>
                <h3>0</h3>
            </div>
        </div>

    </div>

    <!-- ===== Application Process ===== -->

    <div class="process-wrapper">

        <div class="process-header">
            <h3>
                <span class="title-blue">Application Process:</span>

            </h3>
        </div>

        <hr>

        <div class="process-steps">

            <div class="step-box">
                <div class="circle">1</div>
                <p>Complete Profile</p>
            </div>

            <div class="step-box">
                <div class="circle">2</div>
                <p>Apply on Desired Post</p>
            </div>

            <div class="step-box">
                <div class="circle">3</div>
                <p>Download Fee Challan & Pay Fee</p>
            </div>

            <div class="step-box">
                <div class="circle">4</div>
                <p>Download & Print Application</p>
            </div>

            <div class="step-box">
                <div class="circle">5</div>
                <p>Send Application if required</p>
            </div>

        </div>

    </div>

    <!-- ===== Instructions ===== -->
    <!-- ===== Instructions ===== -->
    <div class="instructions-wrapper">

        <h4 class="text-primary mb-4">Instructions</h4>

        <div class="row">

            <!-- English Side -->
            <div class="col-md-6">
                <div class="instruction-box">
                    <p><strong>Caution 1:</strong> Complete your Profile before applying for any post.</p>
                    <p><strong>Caution 2:</strong> Keep checking your portal for roll number slip and result updates.</p>
                    <p><strong>Caution 3:</strong> Edit application before closing date if any correction is required.</p>
                    <p><strong>Caution 4:</strong> Information submitted in application form will be treated as final.</p>
                </div>
            </div>

            <!-- Urdu Side -->
            <div class="col-md-6 text-end" dir="rtl">
                <div class="instruction-box urdu-text">
                    <p><strong>احتیاط 1:</strong> کسی بھی آسامی کے لیے درخواست دینے سے پہلے اپنا پروفائل مکمل کریں۔</p>
                    <p><strong>احتیاط 2:</strong> رول نمبر سلپ اور نتائج کی تازہ معلومات کے لیے اپنا پورٹل باقاعدگی سے چیک کرتے رہیں۔</p>
                    <p><strong>احتیاط 3:</strong> اگر کسی تصحیح کی ضرورت ہو تو آخری تاریخ سے پہلے درخواست میں ترمیم کریں۔</p>
                    <p><strong>احتیاط 4:</strong> درخواست فارم میں جمع کروائی گئی معلومات کو حتمی تصور کیا جائے گا۔</p>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include("includes/footer.php") ?>
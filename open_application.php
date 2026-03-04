<?php include('webincludes/header.php')?>




<section id="open-applications" class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4 mt-5" style="background:#f16c57; display:inline-block; color:white; padding:10px 20px; border-radius:5px;">
            Open Applications
        </h2>

        <!-- Tabs -->
        <ul class="nav nav-tabs mt-4" id="applicationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="open-tab" data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab">
                    Open Applications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="closed-tab" data-bs-toggle="tab" data-bs-target="#closed" type="button" role="tab">
                    Closed Applications
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="applicationTabsContent">

            <!-- Open Applications -->
            <div class="tab-pane fade show active" id="open" role="tabpanel">
                <div class="list-group">
                    <!-- Application 1 -->
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap mb-2 shadow-sm rounded">
                        <div>
                            <h6 class="mb-1">Akhuwat College Kasur (Admission Test 2026-28)</h6>
                            <small class="text-muted">Last Date of Application Form Submission is: 
                                <span class="text-danger fw-bold">Tuesday, 21st April 2026</span>
                            </small>
                        </div>
                        <a href="register.php" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                            <i class="bi bi-journal-text me-1"></i> Details
                        </a>
                    </div>

                    <!-- Application 2 -->
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap mb-2 shadow-sm rounded">
                        <div>
                            <h6 class="mb-1">Walled City of Lahore Authority (Situations Vacant)</h6>
                            <small class="text-muted">Last Date of Application Form Submission is: 
                                <span class="text-danger fw-bold">Thursday, 19th March 2026</span>
                            </small>
                        </div>
                        <a href="register.php" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                            <i class="bi bi-journal-text me-1"></i> Details
                        </a>
                    </div> 
                    
                </div>
            </div>

            <!-- Closed Applications -->
            <div class="tab-pane fade" id="closed" role="tabpanel">
                <div class="alert alert-secondary mt-3">
                    No closed applications at the moment.
                </div>
            </div>

        </div>
    </div>
</section>







<?php include('webincludes/footer.php') ?>
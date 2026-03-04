    <?php
    include "db.php";
    $msg = "";

    if (isset($_POST['register'])) {

        $name   = mysqli_real_escape_string($conn, $_POST['name']);
        $nic    = mysqli_real_escape_string($conn, $_POST['nic']);
        $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
        $email  = mysqli_real_escape_string($conn, $_POST['email']);
        $pass   = $_POST['password'];
        $cpass  = $_POST['cpassword'];

        if ($pass != $cpass) {
            $msg = "Passwords do not match!";
        } else {

            // 🔍 CHECK IF NIC ALREADY EXISTS
            $check = $conn->query("SELECT id FROM users WHERE nic = '$nic'");

            if ($check->num_rows > 0) {
                $msg = "NIC already exists! Please use another NIC.";
            } else {

                $hash = password_hash($pass, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (name, nic, mobile, email, password) 
                        VALUES ('$name','$nic','$mobile','$email','$hash')";

                if ($conn->query($sql)) {
                    header("Location: login.php");
                    exit();
                } else {
                    $msg = "Something went wrong. Please try again.";
                }
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>PATS</title>

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

        <style>
            body {
                background: linear-gradient(135deg, #4e73df, #1cc88a);
            }

            .card {
                /* border-radius: 15px; */
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .card-header {
                background: linear-gradient(135deg, #1cc88a, #4e73df);
                border-radius: 15px 15px 0 0;
            }

            .form-control:focus {
                box-shadow: none;
                border-color: #4e73df;
            }
        </style>
    </head>

    <body>
        
        <div class="container">
            <div class="row justify-content-center align-items-center vh-100">

                <div class="col-md-7">
                    <div class="card">

                        <div class="card-header text-center text-white py-3">
                            <h4><i class="bi bi-person-plus-fill"></i> Applicant Registration Form</h4>
                        </div>

                        <div class="card-body p-4">

                            <?php if ($msg): ?>
                                <div class="alert alert-danger text-center py-2"><?= $msg ?></div>
                            <?php endif; ?>

                            <form method="post">

                                <!-- Full Name (Full Width) -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input class="form-control" name="name" placeholder="Full Name"
                                                value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- NIC + Mobile -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
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
                                        <small id="nicMsg"></small>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="text"
                                                class="form-control"
                                                id="mobile"
                                                name="mobile"
                                                placeholder="Mobile (0300-0000000)"
                                                maxlength="12"
                                                onkeypress="return onlyDigitsToast(event)"
                                                oninput="formatMobile(this)"
                                                value="<?= isset($_POST['mobile']) ? $_POST['mobile'] : '' ?>"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Province -->
                                <!-- <div class="row mb-3"> -->
                                <!-- <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-map"></i></span>
                                            <select class="form-control" name="province" id="province" required onchange="loadDistricts()">
                                                <option value="">Select Province</option>
                                                <option value="Punjab">Punjab</option>
                                                <option value="Sindh">Sindh</option>
                                                <option value="KPK">Khyber Pakhtunkhwa</option>
                                                <option value="Balochistan">Balochistan</option>
                                                <option value="Islamabad">Islamabad Capital</option>
                                            </select>
                                        </div>
                                    </div> -->

                                <!-- District -->
                                <!-- <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <select class="form-control" name="district" id="district" required>
                                                <option value="">Select District</option>
                                            </select>
                                        </div>
                                    </div> -->
                                <!-- </div> -->

                                <!-- Email (Full Width) -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" name="email" placeholder="Email (Optional)">
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input type="password" class="form-control" id="pass"
                                                name="password" placeholder="Password" required>
                                            <span class="input-group-text" onclick="togglePass('pass')" style="cursor:pointer">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" class="form-control" id="cpass"
                                                name="cpassword" placeholder="Confirm Password" required>
                                            <span class="input-group-text" onclick="togglePass('cpass')" style="cursor:pointer">
                                                <i class="bi bi-eye"></i>
                                            </span>

                                        </div>
                                        <small id="passMsg"></small>
                                    </div>
                                </div>

                                <!-- Captcha -->
                                <div class="mb-3">
                                    <label class="form-label text-danger">Security Code</label>

                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" id="captchaInput" class="form-control"
                                            placeholder="Enter code" onkeyup="validateCaptcha()">

                                        <div class="px-3 py-2 bg-danger text-white fw-bold rounded"
                                            id="captchaCode"></div>

                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="generateCaptcha()">↻</button>
                                    </div>

                                    <small id="captchaMsg"></small>
                                </div>


                                <div class="d-grid">
                                    <button type="submit"
                                        name="register"
                                        id="registerBtn"
                                        class="btn btn-primary"
                                        disable>
                                        Register
                                    </button>
                                </div>

                                <p class="text-center mt-3">
                                    Already have an account?
                                    <a href="login.php" class="fw-bold text-decoration-none">Login</a>
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


            let captcha;
            let attempts = 0;
            const maxAttempts = 3;

            function generateCaptcha() {
                captcha = Math.floor(1000 + Math.random() * 9000);
                document.getElementById("captchaCode").innerText = captcha;
                document.getElementById("captchaInput").value = "";
                document.getElementById("captchaMsg").innerHTML = "";
                document.getElementById("registerBtn").disabled = true;
                attempts = 0;
            }

            function validateCaptcha() {
                const input = document.getElementById("captchaInput").value;
                const msg = document.getElementById("captchaMsg");
                const btn = document.getElementById("registerBtn");

                if (input == captcha) {
                    msg.innerHTML = "<span class='text-success'>✔ Captcha verified</span>";
                    btn.disabled = false;
                } else {
                    btn.disabled = true;

                    if (input.length === captcha.toString().length) {
                        attempts++;
                        msg.innerHTML = `<span class='text-danger'>✖ Wrong captcha (${attempts}/${maxAttempts})</span>`;

                        if (attempts >= maxAttempts) {
                            msg.innerHTML = "<span class='text-warning'>Captcha refreshed due to multiple failures</span>";
                            generateCaptcha();
                        }
                    }
                }
            }

            // Generate captcha on page load
            generateCaptcha();

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

            function formatMobile(input) {
                // digits only
                let value = input.value.replace(/\D/g, '');

                // max 11 digits
                value = value.substring(0, 11);

                let formatted = '';

                if (value.length > 4) {
                    formatted = value.substring(0, 4) + '-' + value.substring(4);
                } else {
                    formatted = value;
                }

                input.value = formatted;
            }


            function showToast() {
                const toastEl = document.getElementById('digitToast');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }

            function onlyDigitsToast(e) {
                let char = e.which || e.keyCode;

                // allow backspace & delete
                if (char === 8 || char === 46) return true;

                // digits allowed
                if (char >= 48 && char <= 57) return true;

                // show toast if invalid
                showToast();
                return false;
            }

            // CNIC formatter
            function formatCNIC(input) {
                let value = input.value.replace(/\D/g, '').substring(0, 13);

                if (value.length > 12) {
                    input.value = value.slice(0, 5) + '-' + value.slice(5, 12) + '-' + value.slice(12);
                } else if (value.length > 5) {
                    input.value = value.slice(0, 5) + '-' + value.slice(5);
                } else {
                    input.value = value;
                }
            }

            // Mobile formatter
            function formatMobile(input) {
                let value = input.value.replace(/\D/g, '').substring(0, 11);
                input.value = value.length > 4 ?
                    value.slice(0, 4) + '-' + value.slice(4) :
                    value;
            }

            // const districtsByProvince = {
            //     Punjab: [
            //         "Lahore", "Rawalpindi", "Faisalabad", "Multan", "Gujranwala"
            //     ],
            //     Sindh: [
            //         "Karachi", "Hyderabad", "Sukkur", "Larkana", "Mirpurkhas"
            //     ],
            //     KPK: [
            //         "Peshawar", "Mardan", "Swat", "Abbottabad", "Kohat"
            //     ],
            //     Balochistan: [
            //         "Quetta", "Gwadar", "Turbat", "Khuzdar", "Sibi"
            //     ],
            //     Islamabad: [
            //         "Islamabad"
            //     ]
            // };

            // function loadDistricts() {
            //     const province = document.getElementById("province").value;
            //     const districtSelect = document.getElementById("district");

            //     districtSelect.innerHTML = '<option value="">Select District</option>';

            //     if (province && districtsByProvince[province]) {
            //         districtsByProvince[province].forEach(district => {
            //             const option = document.createElement("option");
            //             option.value = district;
            //             option.textContent = district;
            //             districtSelect.appendChild(option);
            //         });
            //     }
            // }
        </script>
        <script>
            document.getElementById("nic").addEventListener("blur", function() {

                let nic = this.value;
                let msgDiv = document.getElementById("nicMsg");

                if (nic.length > 0) {

                    fetch("check_nic.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "nic=" + nic
                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data === "exists") {
                                msgDiv.innerHTML = "<span class='text-danger'>NIC already exists</span>";
                                document.getElementById("registerBtn").disabled = true;
                            } else {
                                msgDiv.innerHTML = "<span class='text-success'></span>";
                                document.getElementById("registerBtn").disabled = false;
                            }
                        });
                }
            });
        </script>
        <script>
            document.getElementById("cpass").addEventListener("keyup", function() {

                let pass = document.getElementById("pass").value;
                let cpass = this.value;
                let msg = document.getElementById("passMsg");

                if (cpass.length > 0) {
                    if (pass !== cpass) {
                        msg.innerHTML = "<span class='text-danger'>Passwords do not match</span>";
                        document.getElementById("registerBtn").disabled = true;
                    } else {
                        msg.innerHTML = "<span class='text-success'>Passwords match</span>";
                        document.getElementById("registerBtn").disabled = false;
                    }
                }
            });
        </script>



        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="digitToast" class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ❌ Only digits are allowed!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    </body>

    </html>
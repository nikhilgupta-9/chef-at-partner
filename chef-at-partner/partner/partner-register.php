<?php
include_once(__DIR__ . '/../config/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <style>
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }

        .step-circle.active {
            background: #ff6b35;
            color: white;
        }

        .step-circle.completed {
            background: #28a745;
            color: white;
        }

        .alert p {
            margin: 0;
        }
    </style>
</head>

<body>
    <?php include_once '../includes/header.php' ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">
            <div class="col-md-12 py-3">
                <h2 class="text-center">Become a Partner</h2>
                <p class="text-center">Join our platform and start earning</p>

                <div class="step-indicator">
                    <div class="step-circle active" id="step1-indicator">1</div>
                    <div class="step-circle" id="step2-indicator">2</div>
                    <div class="step-circle" id="step3-indicator">3</div>
                </div>

                <!-- Step 1: Basic Information -->
                <div id="step1" class="step active">
                    <h4>Basic Information</h4>
                    <form id="step1-form">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-3" name="full_name" placeholder="Full Name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control mb-3" name="partner_type" required>
                                    <option value="">Select Service Type</option>
                                    <option value="chef">Chef</option>
                                    <option value="bartender">Bartender</option>
                                    <option value="helper">Helper</option>
                                    <option value="cleaner">Cleaner</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="email" class="form-control mb-3" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-md-6">
                                <input type="tel" class="form-control mb-3" name="phone" placeholder="Phone Number"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="password" class="form-control mb-3" name="password" placeholder="Password"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="password" class="form-control mb-3" name="confirm_password"
                                    placeholder="Confirm Password" required>
                            </div>
                        </div>

                        <button type="button" class="btn btn-theme w-100" onclick="nextStep(2)">Continue</button>
                    </form>
                </div>

                <!-- Step 2: Professional Details -->
                <div id="step2" class="step">
                    <h4>Professional Details</h4>
                    <form id="step2-form">
                        <input type="text" class="form-control mb-3" name="aadhar_number"
                            placeholder="Aadhar Card Number" required>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Aadhar Front Image</label>
                                    <input type="file" class="form-control mb-3" name="aadhar_front" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Aadhar Back Image</label>
                                    <input type="file" class="form-control mb-3" name="aadhar_back" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" class="form-control mb-3" name="experience_years"
                                    placeholder="Years of Experience" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control mb-3" name="hourly_rate"
                                    placeholder="Hourly Rate (₹)" min="0" step="0.01" required>
                            </div>
                        </div>

                        <textarea class="form-control mb-3" name="skills" placeholder="Skills (comma separated)"
                            rows="3"></textarea>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Back</button>
                            <button type="button" class="btn btn-theme" onclick="nextStep(3)">Continue</button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Verification -->
                <div id="step3" class="step">
                    <h4>Email Verification</h4>
                    <form id="step3-form">
                        <div class="alert alert-info" id="otp-message">
                            <p>We've sent a 6-digit OTP to your email. Please check your inbox.</p>
                        </div>

                        <input type="text" class="form-control mb-3" name="otp" placeholder="Enter OTP" maxlength="6"
                            required>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
                            <button type="submit" class="btn btn-theme" id="submit-btn">Complete Registration</button>
                        </div>
                    </form>
                </div>

                <p class="mt-3 text-center">
                    Already have an Account? <a href="<?= $BASE_URL ?>login.php" class="auth-link">Log In</a>
                </p>
            </div>
        </div>
    </section>

    <script>
        let registrationData = {};

        function nextStep(next) {
            const current = next - 1;

            // Validate current step
            let isValid = true;
            if (next === 2) {
                const form = document.getElementById('step1-form');
                const password = form.password.value;
                const confirmPassword = form.confirm_password.value;

                // Basic validation
                if (password !== confirmPassword) {
                    alert("Passwords don't match!");
                    return false;
                }

                if (password.length < 6) {
                    alert("Password must be at least 6 characters long!");
                    return false;
                }

                // Collect step 1 data
                registrationData = {
                    ...registrationData,
                    full_name: form.full_name.value,
                    email: form.email.value,
                    phone: form.phone.value,
                    password: password,
                    confirm_password: confirmPassword,
                    partner_type: form.partner_type.value
                };
            }
            else if (next === 3) {
                const form = document.getElementById('step2-form');

                // Collect step 2 data
                registrationData = {
                    ...registrationData,
                    aadhar_number: form.aadhar_number.value,
                    experience_years: form.experience_years.value,
                    hourly_rate: form.hourly_rate.value,
                    skills: form.skills.value
                };
            }

            if (isValid) {
                // Update indicators
                document.getElementById(`step${current}`).classList.remove('active');
                document.getElementById(`step${next}`).classList.add('active');
                document.getElementById(`step${current}-indicator`).classList.remove('active');
                document.getElementById(`step${current}-indicator`).classList.add('completed');
                document.getElementById(`step${next}-indicator`).classList.add('active');

                // If moving to step 3, send OTP
                if (next === 3) {
                    sendOTP();
                }
            }
        }

        function prevStep(prev) {
            const current = prev + 1;
            document.getElementById(`step${current}`).classList.remove('active');
            document.getElementById(`step${prev}`).classList.add('active');
            document.getElementById(`step${current}-indicator`).classList.remove('active');
            document.getElementById(`step${prev}-indicator`).classList.add('active');
            document.getElementById(`step${prev}-indicator`).classList.remove('completed');
        }

        function sendOTP() {
            const otpMessage = document.getElementById('otp-message');
            otpMessage.innerHTML = '<p>Sending OTP... Please wait.</p>';
            otpMessage.className = 'alert alert-info';

            // AJAX call to send OTP
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "<?= $BASE_URL ?>ajax/send-otp.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (this.readyState === 4) {
                    console.log("OTP Response:", this.responseText);
                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            otpMessage.innerHTML = '<p>✓ OTP sent successfully! Check your email. If you don\'t see it, check spam folder.</p>';
                            otpMessage.className = 'alert alert-success';
                        } else {
                            otpMessage.innerHTML = '<p>✗ ' + response.message + '</p>';
                            otpMessage.className = 'alert alert-danger';
                        }
                    } catch (e) {
                        otpMessage.innerHTML = '<p>✗ Error sending OTP. Please try again.</p>';
                        otpMessage.className = 'alert alert-danger';
                        console.error("OTP Error:", e);
                    }
                }
            };

            xhr.onerror = function () {
                otpMessage.innerHTML = '<p>✗ Network error. Please check your connection.</p>';
                otpMessage.className = 'alert alert-danger';
            };

            xhr.send(`email=${encodeURIComponent(registrationData.email)}&action=register`);
        }

        // Handle final submission
        document.getElementById('step3-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const otpInput = this.querySelector('input[name="otp"]');
            const otp = otpInput.value.trim();

            if (!otp || otp.length !== 6) {
                alert("Please enter a valid 6-digit OTP");
                otpInput.focus();
                return;
            }

            // Add OTP to registration data
            const finalData = {
                ...registrationData,
                otp: otp
            };

            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            submitBtn.disabled = true;

            console.log("Sending registration data:", finalData);

            // AJAX call to verify OTP and complete registration
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "complete-partner-registration.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.onreadystatechange = function () {
                if (this.readyState === 4) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    console.log("Registration Response Status:", this.status);
                    console.log("Registration Response:", this.responseText);

                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            alert(response.message);
                            window.location.href = <?= $BASE_URL ?>"login.php";
                        } else {
                            alert("Error: " + response.message);
                        }
                    } catch (e) {
                        console.error("Parse Error:", e);
                        console.error("Raw Response:", this.responseText);
                        alert("Server error. Please check console for details or try again.");
                    }
                }
            };

            xhr.onerror = function () {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert("Network error. Please check your connection.");
            };

            xhr.send(JSON.stringify(finalData));
        });
    </script>

    <?php include_once '../includes/footer.php' ?>
</body>

</html>
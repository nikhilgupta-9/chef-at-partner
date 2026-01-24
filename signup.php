<?php
include_once('config/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Set proper Content-Type for JSON response
    if (in_array($action, ['send_otp', 'verify_otp', 'complete_signup'])) {
        header('Content-Type: application/json');
    }

    if ($action === 'send_otp') {
        // Step 1: Validate and send OTP
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $phone = trim(filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_NUMBER_INT));

        $errors = [];

        // Validate email
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        // Validate phone
        if (empty($phone)) {
            $errors[] = "Phone number is required.";
        } elseif (!preg_match('/^[6-9]\d{9}$/', $phone)) {
            $errors[] = "Please enter a valid 10-digit Indian phone number.";
        }

        if (empty($errors)) {
            // Check if email already exists
            $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();

            if ($check_email->get_result()->num_rows > 0) {
                $errors[] = "Email already registered.";
            }

            // Check if phone already exists
            $check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
            $check_phone->bind_param("s", $phone);
            $check_phone->execute();

            if ($check_phone->get_result()->num_rows > 0) {
                $errors[] = "Phone number already registered.";
            }
        }

        if (empty($errors)) {
            // Generate OTP
            $otp = random_int(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Store OTP in session
            $_SESSION['signup_email'] = $email;
            $_SESSION['signup_phone'] = $phone;
            $_SESSION['signup_otp'] = $otp;
            $_SESSION['signup_otp_expiry'] = $expiry;
            $_SESSION['signup_attempts'] = 0;

            // Include and call OTP email function
            include_once('ajax/send-otp-mail.php');
            $otpSent = sendOTPEmail($email, $otp, 'New User');

            if ($otpSent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'OTP sent to your email!'
                ]);
            } else {
                // For development, still show OTP
                echo json_encode([
                    'success' => true,
                    'message' => 'OTP generated successfully!',
                    'debug_otp' => $otp // For development only
                ]);
            }
            exit();

        } else {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errors)
            ]);
            exit();
        }

    } elseif ($action === 'verify_otp') {
        // Step 2: Verify OTP
        $entered_otp = trim($_POST['otp'] ?? '');
        $stored_otp = $_SESSION['signup_otp'] ?? '';
        $expiry = $_SESSION['signup_otp_expiry'] ?? '';

        if (empty($entered_otp)) {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter OTP'
            ]);
            exit();
        }

        if (empty($stored_otp) || empty($expiry)) {
            echo json_encode([
                'success' => false,
                'message' => 'OTP session expired. Please start over.'
            ]);
            exit();
        }

        if (time() > strtotime($expiry)) {
            echo json_encode([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.'
            ]);
            exit();
        }

        if ($entered_otp == $stored_otp) {
            // OTP verified - mark as verified in session
            $_SESSION['signup_verified'] = true;

            echo json_encode([
                'success' => true,
                'message' => 'OTP verified successfully!'
            ]);
            exit();
        } else {
            // Increment attempt counter
            $_SESSION['signup_attempts'] = ($_SESSION['signup_attempts'] ?? 0) + 1;

            if ($_SESSION['signup_attempts'] >= 3) {
                // Too many failed attempts
                unset(
                    $_SESSION['signup_otp'],
                    $_SESSION['signup_otp_expiry'],
                    $_SESSION['signup_attempts']
                );
                echo json_encode([
                    'success' => false,
                    'message' => 'Too many failed attempts. Please request a new OTP.'
                ]);
                exit();
            }

            $attemptsLeft = 3 - $_SESSION['signup_attempts'];
            echo json_encode([
                'success' => false,
                'message' => 'Invalid OTP. Attempts remaining: ' . $attemptsLeft
            ]);
            exit();
        }

    } elseif ($action === 'complete_signup') {
        // Step 3: Complete signup
        if (!isset($_SESSION['signup_verified']) || $_SESSION['signup_verified'] !== true) {
            echo json_encode([
                'success' => false,
                'message' => 'OTP verification required.'
            ]);
            exit();
        }

        $full_name = trim(htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8'));
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $email = $_SESSION['signup_email'] ?? '';
        $phone = $_SESSION['signup_phone'] ?? '';

        $errors = [];

        // Validate inputs
        if (empty($full_name) || strlen($full_name) < 2) {
            $errors[] = "Full name must be at least 2 characters.";
        }

        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain uppercase, lowercase letters and numbers.";
        }

        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($errors)) {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("
                INSERT INTO users 
                (user_type, full_name, email, phone, password, email_verified, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $user_type = 'customer';
            $email_verified = 1; // Verified via OTP
            $status = 'active';

            $stmt->bind_param(
                "sssssis",
                $user_type,
                $full_name,
                $email,
                $phone,
                $hashedPassword,
                $email_verified,
                $status
            );

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                // Clear session data
                unset(
                    $_SESSION['signup_email'],
                    $_SESSION['signup_phone'],
                    $_SESSION['signup_otp'],
                    $_SESSION['signup_otp_expiry'],
                    $_SESSION['signup_verified'],
                    $_SESSION['signup_attempts']
                );

                // Auto login user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = 'customer';
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;

                echo json_encode([
                    'success' => true,
                    'message' => 'Account created successfully!',
                    'redirect' => 'user/dashboard.php'
                ]);
                exit();

            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }

        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errors)
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up - CHEF AT PARTNER</title>
    <?php include_once 'links.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #c7a07d;
            --primary-dark: #a67c52;
            --primary-light: #f8f3ee;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .auth-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 0;
        }

        .auth-card {
            max-width: 1000px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .auth-form {
            padding: 50px;
        }

        .auth-form h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .auth-form p {
            color: #666;
            margin-bottom: 30px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .step-item.active .step-number {
            background: var(--primary-color);
            color: white;
        }

        .step-item.completed .step-number {
            background: #28a745;
            color: white;
        }

        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(199, 160, 125, 0.25);
        }

        .btn-theme {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        .btn-theme:hover:not(:disabled) {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-theme:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-outline-theme {
            border-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-outline-theme:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(199, 160, 125, 0.25);
            outline: none;
        }

        .timer {
            color: #dc3545;
            font-weight: 600;
            margin: 10px 0;
            text-align: center;
        }

        .resend-otp {
            margin-top: 10px;
            text-align: center;
        }

        .resend-otp a {
            color: var(--primary-color);
            cursor: pointer;
            text-decoration: none;
        }

        .resend-otp a:hover {
            text-decoration: underline;
        }

        .resend-otp.disabled a {
            color: #6c757d;
            cursor: not-allowed;
            text-decoration: none;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #dc3545;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #28a745;
        }

        .alert-info {
            background-color: #f0f9ff;
            color: #0c5460;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .password-strength {
            margin-top: 5px;
        }

        .strength-bar {
            height: 5px;
            background: #e9ecef;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }

        .requirement-list {
            list-style: none;
            padding: 0;
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        .requirement-list li {
            margin-bottom: 3px;
            color: #666;
        }

        .requirement-list li.valid {
            color: #28a745;
        }

        .requirement-list li.invalid {
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .auth-form {
                padding: 30px;
            }

            .otp-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .debug-otp {
            background-color: #f8f9fa;
            border: 1px dashed #dc3545;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include_once 'includes/header.php'; ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">
            <div class="col-md-6 auth-form">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step-item active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Verify Email</div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Verify OTP</div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Complete</div>
                    </div>
                </div>

                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Step 1: Email & Phone Verification -->
                <div class="step active" id="step1">
                    <h2>Verify Your Details</h2>
                    <p>We'll send an OTP to verify your email address</p>

                    <form id="step1Form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="Enter your email" required>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control"
                                placeholder="Enter 10-digit phone number" pattern="[6-9][0-9]{9}" maxlength="10"
                                required>
                            <small class="text-muted">Enter 10-digit Indian phone number starting with 6-9</small>
                        </div>

                        <button type="submit" class="btn btn-theme w-100" id="sendOtpBtn">
                            <i class="fas fa-paper-plane me-2"></i> Send OTP
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0">
                                Already have an account?
                                <a href="login.php" class="text-decoration-none fw-semibold"
                                    style="color: var(--primary-color);">
                                    Log In Here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div class="step" id="step2">
                    <h2>Verify OTP</h2>
                    <p>Enter the 6-digit OTP sent to <span id="verifyEmail" class="fw-semibold"></span></p>

                    <!-- Debug OTP Display (for development) -->
                    <div class="debug-otp" id="debugOtpDisplay" style="display: none;">
                        Development OTP: <span id="debugOtpCode"></span>
                    </div>

                    <form id="step2Form">
                        <div class="otp-input-group">
                            <input type="text" maxlength="1" class="otp-input" data-index="1" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" data-index="2" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" data-index="3" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" data-index="4" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" data-index="5" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" data-index="6" autocomplete="off">
                        </div>
                        <input type="hidden" name="otp" id="otpInput">

                        <div class="timer text-center" id="timer">
                            OTP expires in: <span id="countdown">10:00</span>
                        </div>

                        <div class="resend-otp text-center disabled" id="resendOtp">
                            <a href="javascript:void(0)" onclick="resendOTP()">Resend OTP</a>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline-theme w-50" onclick="goToStep(1)">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" class="btn btn-theme w-50" id="verifyOtpBtn">
                                <i class="fas fa-check me-2"></i> Verify OTP
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Complete Registration -->
                <div class="step" id="step3">
                    <h2>Complete Your Profile</h2>
                    <p>Fill in your details to complete registration</p>

                    <form id="step3Form">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control"
                                placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Create a strong password" required>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                            </div>
                            <ul class="requirement-list" id="passwordRequirements">
                                <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                                <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                                <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                                <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                placeholder="Confirm your password" required>
                            <div id="passwordMatch" class="text-muted small mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="terms-and-conditions.php" target="_blank">Terms &
                                        Conditions</a>
                                    and <a href="privacy-policy.php" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline-theme w-50" onclick="goToStep(2)">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" class="btn btn-theme w-50" id="completeSignupBtn">
                                <i class="fas fa-user-plus me-2"></i> Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6 auth-image"
                style="background-image:url('https://images.unsplash.com/photo-1551218808-94e220e084d2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80')">
                <div class="position-absolute top-50 start-50 translate-middle text-white text-center p-4">
                    <h3 class="mb-3">Join Our Community</h3>
                    <p class="mb-4">Access thousands of verified professionals for all your event needs</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-white bg-opacity-25 p-3 rounded">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h5 class="mb-0">Verified Partners</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white bg-opacity-25 p-3 rounded">
                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                <h5 class="mb-0">Secure Booking</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentStep = 1;
        let timerInterval;
        let otpExpiryTime;

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Phone number formatting
            document.getElementById('phone').addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });

            // OTP input handling
            setupOtpInputs();
        });

        // Step Navigation
        function goToStep(step) {
            // Hide all steps
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(s => s.classList.remove('active', 'completed'));

            // Show target step
            document.getElementById('step' + step).classList.add('active');

            // Update step indicator
            for (let i = 1; i <= step; i++) {
                const stepItem = document.querySelector(`.step-item[data-step="${i}"]`);
                if (i === step) {
                    stepItem.classList.add('active');
                } else {
                    stepItem.classList.add('completed');
                }
            }

            currentStep = step;

            // Start timer if on step 2
            if (step === 2) {
                startTimer();
            }
        }

        // Step 1: Send OTP
        document.getElementById('step1Form').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const btn = document.getElementById('sendOtpBtn');

            if (!email || !phone) {
                showAlert('Please fill in all fields', 'danger');
                return;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Please enter a valid email address', 'danger');
                return;
            }

            // Validate phone (Indian)
            const phoneRegex = /^[6-9]\d{9}$/;
            if (!phoneRegex.test(phone)) {
                showAlert('Please enter a valid 10-digit Indian phone number', 'danger');
                return;
            }

            // Show loading
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Sending OTP...';

            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'send_otp');
            formData.append('email', email);
            formData.append('phone', phone);

            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');

                        // Show debug OTP if present
                        if (data.debug_otp) {
                            document.getElementById('debugOtpDisplay').style.display = 'block';
                            document.getElementById('debugOtpCode').textContent = data.debug_otp;
                        }

                        // Update email display
                        document.getElementById('verifyEmail').textContent = email;

                        // Move to step 2
                        setTimeout(() => goToStep(2), 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // Step 2: Verify OTP
        document.getElementById('step2Form').addEventListener('submit', function (e) {
            e.preventDefault();

            const otp = document.getElementById('otpInput').value;
            const btn = document.getElementById('verifyOtpBtn');

            if (!otp || otp.length !== 6) {
                showAlert('Please enter a valid 6-digit OTP', 'danger');
                return;
            }

            // Show loading
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Verifying...';

            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'verify_otp');
            formData.append('otp', otp);

            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Move to step 3
                        setTimeout(() => goToStep(3), 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // Step 3: Complete Signup
        document.getElementById('step3Form').addEventListener('submit', function (e) {
            e.preventDefault();

            const full_name = document.getElementById('full_name').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            const btn = document.getElementById('completeSignupBtn');

            // Validate inputs
            if (!full_name || full_name.length < 2) {
                showAlert('Full name must be at least 2 characters', 'danger');
                return;
            }

            if (!password || password.length < 8) {
                showAlert('Password must be at least 8 characters', 'danger');
                return;
            }

            // Check password requirements
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            if (!hasUpper || !hasLower || !hasNumber) {
                showAlert('Password must contain uppercase, lowercase letters and numbers', 'danger');
                return;
            }

            if (password !== confirm_password) {
                showAlert('Passwords do not match', 'danger');
                return;
            }

            if (!terms) {
                showAlert('Please agree to the Terms & Conditions', 'danger');
                return;
            }

            // Show loading
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Creating Account...';

            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'complete_signup');
            formData.append('full_name', full_name);
            formData.append('password', password);
            formData.append('confirm_password', confirm_password);

            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');

                        // Redirect if provided
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // OTP Input Setup
        function setupOtpInputs() {
            const otpInputs = document.querySelectorAll('.otp-input');

            otpInputs.forEach((input, index) => {
                // Handle input
                input.addEventListener('input', function (e) {
                    const value = this.value;

                    // Only allow numbers
                    if (value && !/^\d+$/.test(value)) {
                        this.value = '';
                        return;
                    }

                    // Move to next input if value entered
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }

                    // Update hidden OTP field
                    updateOtpValue();
                });

                // Handle backspace
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');

                    if (/^\d{6}$/.test(pastedData)) {
                        for (let i = 0; i < 6; i++) {
                            if (otpInputs[i]) {
                                otpInputs[i].value = pastedData[i];
                            }
                        }
                        otpInputs[5].focus();
                        updateOtpValue();
                    }
                });
            });
        }

        function updateOtpValue() {
            const otpInputs = document.querySelectorAll('.otp-input');
            let otp = '';

            otpInputs.forEach(input => {
                otp += input.value;
            });

            document.getElementById('otpInput').value = otp;
        }

        // Timer Functions
        function startTimer() {
            clearInterval(timerInterval);

            // Set expiry time (10 minutes from now)
            otpExpiryTime = Date.now() + 10 * 60 * 1000;

            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }

        function updateTimer() {
            const now = Date.now();
            const timeLeft = otpExpiryTime - now;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('countdown').textContent = '00:00';
                document.getElementById('resendOtp').classList.remove('disabled');
                showAlert('OTP has expired. Please request a new one.', 'warning');
                return;
            }

            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);

            document.getElementById('countdown').textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Enable resend after 30 seconds
            if (timeLeft < 9.5 * 60 * 1000) {
                document.getElementById('resendOtp').classList.remove('disabled');
            }
        }

        function resendOTP() {
            const resendLink = document.querySelector('#resendOtp a');
            const resendDiv = document.getElementById('resendOtp');

            if (resendDiv.classList.contains('disabled')) {
                return;
            }

            // Show loading
            resendLink.innerHTML = '<span class="loading"></span> Sending...';

            // Get email and phone from step 1
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'send_otp');
            formData.append('email', email);
            formData.append('phone', phone);

            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('New OTP sent successfully!', 'success');

                        // Reset OTP inputs
                        document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                        document.getElementById('otpInput').value = '';
                        document.querySelector('.otp-input').focus();

                        // Restart timer
                        startTimer();

                        // Disable resend temporarily
                        document.getElementById('resendOtp').classList.add('disabled');

                        // Show debug OTP if present
                        if (data.debug_otp) {
                            document.getElementById('debugOtpDisplay').style.display = 'block';
                            document.getElementById('debugOtpCode').textContent = data.debug_otp;
                        }
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to resend OTP. Please try again.', 'danger');
                })
                .finally(() => {
                    resendLink.innerHTML = 'Resend OTP';
                });
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            return Math.min(strength, 5);
        }

        // Update password requirements
        function updateRequirements(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };

            document.getElementById('req-length').className = requirements.length ? 'valid' : 'invalid';
            document.getElementById('req-uppercase').className = requirements.uppercase ? 'valid' : 'invalid';
            document.getElementById('req-lowercase').className = requirements.lowercase ? 'valid' : 'invalid';
            document.getElementById('req-number').className = requirements.number ? 'valid' : 'invalid';

            // Update strength bar
            const strength = checkPasswordStrength(password);
            const percentage = (strength / 5) * 100;

            document.getElementById('strengthFill').style.width = percentage + '%';

            // Color coding
            if (strength <= 2) {
                document.getElementById('strengthFill').style.backgroundColor = '#dc3545';
            } else if (strength <= 4) {
                document.getElementById('strengthFill').style.backgroundColor = '#ffc107';
            } else {
                document.getElementById('strengthFill').style.backgroundColor = '#28a745';
            }
        }

        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');

            if (!password || !confirm) {
                matchDiv.textContent = '';
                return;
            }

            if (password === confirm) {
                matchDiv.textContent = '✓ Passwords match';
                matchDiv.style.color = '#28a745';
            } else {
                matchDiv.textContent = '✗ Passwords do not match';
                matchDiv.style.color = '#dc3545';
            }
        }

        // Initialize password validation
        document.getElementById('password').addEventListener('input', function () {
            updateRequirements(this.value);
            checkPasswordMatch();
        });

        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

        // Alert system
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();

            // Remove existing alerts
            const existingAlerts = alertContainer.querySelectorAll('.alert');
            existingAlerts.forEach(alert => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            });

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.id = alertId;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;

            alertContainer.appendChild(alert);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                const alertEl = document.getElementById(alertId);
                if (alertEl) {
                    alertEl.classList.remove('show');
                    setTimeout(() => alertEl.remove(), 300);
                }
            }, 5000);
        }
    </script>
</body>

</html>
<?php
include_once('config/connect.php');
include_once('ajax/send-otp-mail.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (!$full_name || !$email || !$phone || !$password) {
        $error = "All fields are required.";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            $otp = random_int(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // TRY TO SEND OTP FIRST
            if (!sendOTPEmail($email, $otp, $full_name)) {
                $error = "OTP could not be sent. Please try again.";
            } else {

                // OTP SENT → NOW INSERT USER
                $stmt = $conn->prepare("
                    INSERT INTO users
                    (user_type, full_name, email, phone, password, email_verified, status, reset_token, reset_token_expiry, created_at)
                    VALUES ('customer', ?, ?, ?, ?, 0, 'active', ?, ?, NOW())
                ");

                $stmt->bind_param(
                    "ssssss",
                    $full_name,
                    $email,
                    $phone,
                    $hashedPassword,
                    $otp,
                    $expiry
                );

                if ($stmt->execute()) {
                    $_SESSION['verify_email'] = $email;
                    header("Location: verify-otp.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CHEF AT PARTNER</title>
    <?php include_once 'links.php'; ?>
</head>

<body>

    <?php include_once 'includes/header.php'; ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">

            <div class="col-md-6 auth-form">
                <h2>Create Customer Account</h2>
                <p>Hire trusted professionals for your events</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <input type="text" name="full_name" class="form-control mb-3" placeholder="Full Name" required>

                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

                    <input type="tel" name="phone" class="form-control mb-3" placeholder="Phone Number" required>

                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                    <button type="submit" class="btn btn-theme w-100">
                        Sign Up
                    </button>

                    <p class="mt-3 text-center">
                        Already have an Account!
                        <a href="<?= $BASE_URL ?>login.php" class="auth-link">Log In</a>
                    </p>

                    <p class="mt-3 text-center">
                        Register as Partner!
                        <a href="<?= $BASE_URL ?>partner/partner-register.php" class="auth-link">
                            Sign Up
                        </a>
                    </p>

                </form>
            </div>

            <div class="col-md-6 d-none d-md-block auth-image"
                style="background-image:url('https://images.unsplash.com/photo-1551218808-94e220e084d2');">
            </div>

        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>

</body>

</html>
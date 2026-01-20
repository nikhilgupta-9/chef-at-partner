<?php
include_once(__DIR__ . '/config/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token
        $update = $conn->prepare("
            UPDATE users 
            SET reset_token = ?, reset_token_expiry = ?
            WHERE id = ?
        ");
        $update->bind_param("ssi", $token, $expires, $user['id']);
        $update->execute();

        // Reset link
        $resetLink = $BASE_URL . "reset-password.php?token=" . $token;

        /*
         👉 Send email here using PHPMailer
         Subject: Password Reset Request
         Body: Click link to reset password
         */

        $_SESSION['message'] = "Password reset link has been sent to your email.";
        header("Location: forgot-password.php");
        exit();
    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - CHEF AT PARTNER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once 'links.php'; ?>
</head>

<body>

    <?php include_once 'includes/header.php'; ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">
            <div class="col-md-6 auth-form">
                <h2>Forgot Password</h2>
                <p>Enter your email to reset your password</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['message'];
                        unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Enter your registered email"
                        required>

                    <button type="submit" class="btn btn-theme w-100">
                        Send Reset Link
                    </button>

                    <div class="mt-3 text-center">
                        <a href="login.php" class="auth-link">Back to Login</a>
                    </div>
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
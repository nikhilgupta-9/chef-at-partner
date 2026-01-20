<?php
include_once 'config/connect.php';
session_start();

if (!isset($_SESSION['verify_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['verify_email'];

/* fetch user */
$check = $conn->prepare("
    SELECT email_verified 
    FROM users 
    WHERE email = ? AND status = 'active'
");
$check->bind_param("s", $email);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if (!$user || $user['email_verified'] == 1) {
    unset($_SESSION['verify_email']);
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['otp_attempts'] = ($_SESSION['otp_attempts'] ?? 0) + 1;

    if ($_SESSION['otp_attempts'] > 5) {
        unset($_SESSION['verify_email']);
        die("Too many attempts. Please register again.");
    }

    $otp = trim($_POST['otp']);
    $otp = preg_replace('/\s+/', '', $otp);   // 🔥 FIX

    $stmt = $conn->prepare("
        SELECT id, reset_token, reset_token_expiry 
        FROM users
        WHERE email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    /* DEBUG (remove after test) */
    // echo "DB OTP: ".$res['reset_token']." | USER OTP: ".$otp; exit;

    if ($res && $res['reset_token'] === $otp && strtotime($res['reset_token_expiry']) >= time()) {

        $update = $conn->prepare("
            UPDATE users
            SET email_verified = 1,
                reset_token = NULL,
                reset_token_expiry = NULL
            WHERE email = ?
        ");
        $update->bind_param("s", $email);
        $update->execute();

        unset($_SESSION['verify_email'], $_SESSION['otp_attempts']);

        $_SESSION['message'] = "Email verified successfully. Please login.";
        header("Location: login.php");
        exit();

    } else {
        $error = "Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Email - CHEF AT PARTNER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once 'links.php'; ?>
</head>

<body>

    <?php include_once 'includes/header.php'; ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">
            <div class="col-md-6 auth-form">
                <h2>Verify Your Email</h2>
                <p>Enter the OTP sent to your email</p>

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
                    <input type="text" name="otp" class="form-control mb-3" placeholder="Enter 6-digit OTP" required>
                    <button class="btn btn-theme w-100">Verify OTP</button>
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
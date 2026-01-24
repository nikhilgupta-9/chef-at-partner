<?php
include_once __DIR__ . '/config/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT id, user_type, full_name, password, email_verified, status
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $error = "Invalid email or password.";
    } else {

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            $error = "Invalid email or password.";
        } elseif ($user['status'] !== 'active') {
            $error = "Your account is inactive. Contact support.";
        } elseif ((int) $user['email_verified'] !== 1) {
            $error = "Please verify your email before logging in.";
        } else {

            // 🔐 Secure session
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];

            // 🎯 Role-based redirect
            switch ($user['user_type']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'partner':
                    header("Location: partner/dashboard.php");
                    break;
                default:
                    header("Location: user/dashboard.php");
            }
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CHEF AT PARTNER</title>
    <?php include_once 'links.php' ?>
</head>

<body>
    <?php include_once 'includes/header.php' ?>

    <section class="auth-section container">
        <div class="row auth-card w-100">
            <div class="col-md-6 auth-form">
                <h2>Login</h2>
                <p>Access your account</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="email" class="form-control mb-3" name="email" placeholder="Email" required>
                    <input type="password" class="form-control mb-3" name="password" placeholder="Password" required>


                    <button type="submit" class="btn btn-theme w-100">Login</button>

                    <div class="mt-3 text-center">
                        <a href="forgot-password.php" class="auth-link">Forgot Password?</a>
                        <p class="mt-2">
                            Don't have an account?
                            <a href="<?= $BASE_URL ?>signup.php" class="auth-link">Sign Up as Customer</a> |
                            <a href="<?= $BASE_URL ?>partner/partner-register.php" class="auth-link">Become a
                                Partner</a>
                        </p>
                    </div>
                </form>
            </div>

            <div class="col-md-6 d-none d-md-block auth-image"
                style="background-image:url('https://images.unsplash.com/photo-1551218808-94e220e084d2');">
            </div>
        </div>
    </section>

    <?php include_once 'includes/footer.php' ?>
</body>

</html>
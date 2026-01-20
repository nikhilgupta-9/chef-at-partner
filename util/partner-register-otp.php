<?php
include_once(__DIR__ . '/../config/connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $action = $_POST['action'];

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in database (expires in 10 minutes)
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $conn->prepare("INSERT INTO otp_verification (email, otp, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $otp, $expires_at);

    if ($stmt->execute()) {
        // In production, send email here
        // For now, we'll store in session for demo
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        echo json_encode(['success' => true, 'otp' => $otp]); // Remove otp in production
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
    }
}
?>
<?php
include_once(__DIR__ . '/../config/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("=== Partner Registration Started ===");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get the raw POST data
        $rawData = file_get_contents('php://input');
        error_log("Raw POST data: " . $rawData);

        // Try to decode JSON
        $data = json_decode($rawData, true);

        // If JSON decode fails, use regular POST
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode failed: " . json_last_error_msg());
            $data = $_POST;
        }

        error_log("Processed data: " . print_r($data, true));
        error_log("Session data: " . print_r([
            'email_otp' => $_SESSION['email_otp'] ?? 'NOT SET',
            'email_for_otp' => $_SESSION['email_for_otp'] ?? 'NOT SET',
            'email_otp_time' => $_SESSION['email_otp_time'] ?? 'NOT SET'
        ], true));

        // Validate all required fields
        $required_fields = ['full_name', 'email', 'phone', 'password', 'partner_type', 'aadhar_number', 'experience_years', 'hourly_rate', 'otp', 'confirm_password'];
        $missing_fields = [];

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            error_log("Missing fields: " . implode(', ', $missing_fields));
            echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
            exit;
        }

        // Sanitize inputs
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        $otp = preg_replace('/[^0-9]/', '', $data['otp']);

        // Verify OTP from session
        if (!isset($_SESSION['email_otp']) || !isset($_SESSION['email_otp_time']) || !isset($_SESSION['email_for_otp'])) {
            error_log("OTP session not found");
            echo json_encode(['success' => false, 'message' => 'OTP session expired. Please request a new OTP.']);
            exit;
        }

        // Check if OTP matches email
        if ($_SESSION['email_for_otp'] !== $email) {
            error_log("Email mismatch. Session email: " . $_SESSION['email_for_otp'] . ", Provided email: " . $email);
            echo json_encode(['success' => false, 'message' => 'Email does not match OTP recipient.']);
            exit;
        }

        // Check if OTP is correct
        if ($_SESSION['email_otp'] != $otp) {
            error_log("OTP mismatch. Session OTP: " . $_SESSION['email_otp'] . ", Provided OTP: " . $otp);
            echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please check and try again.']);
            exit;
        }

        // Check if OTP is expired (10 minutes = 600 seconds)
        $otp_time = $_SESSION['email_otp_time'];
        if (time() - $otp_time > 600) {
            error_log("OTP expired. Generated: " . date('Y-m-d H:i:s', $otp_time) . ", Current: " . date('Y-m-d H:i:s'));
            echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
            exit;
        }

        // Check if user already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered. Please login instead.']);
            exit;
        }

        // Check password match
        if ($data['password'] !== $data['confirm_password']) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit;
        }

        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Start transaction
        $conn->begin_transaction();

        // Insert into users table
        $user_type = 'partner';
        $user_stmt = $conn->prepare("INSERT INTO users (user_type, full_name, email, phone, password, status, email_verified, created_at) VALUES (?, ?, ?, ?, ?, 'pending', 1, NOW())");
        $user_stmt->bind_param("sssss", $user_type, $data['full_name'], $email, $phone, $hashed_password);

        if (!$user_stmt->execute()) {
            throw new Exception("Failed to create user account: " . $conn->error);
        }

        $user_id = $conn->insert_id;
        error_log("User created with ID: " . $user_id);

        // Insert into partners table
        $partner_stmt = $conn->prepare("INSERT INTO partners (user_id, partner_type, aadhar_number, experience_years, hourly_rate, skills, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $partner_stmt->bind_param("issids", $user_id, $data['partner_type'], $data['aadhar_number'], $data['experience_years'], $data['hourly_rate'], $data['skills']);

        if (!$partner_stmt->execute()) {
            throw new Exception("Failed to create partner profile: " . $conn->error);
        }

        // Clear OTP session
        unset($_SESSION['email_otp']);
        unset($_SESSION['email_otp_time']);
        unset($_SESSION['email_for_otp']);

        $conn->commit();

        error_log("Registration successful for user ID: " . $user_id);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Please wait for admin verification. You can now login.'
        ]);

    } catch (Exception $e) {
        // Rollback transaction if active
        if ($conn && $conn->begin_transaction) {
            $conn->rollback();
        }

        error_log("Registration Exception: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ]);
    }

    if ($conn) {
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
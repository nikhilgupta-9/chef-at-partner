<?php
include_once(__DIR__ . '/../config/connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;

    // Verify OTP
    $stmt = $conn->prepare("SELECT * FROM otp_verification WHERE email = ? AND otp = ? AND is_used = 0 AND expires_at > NOW()");
    $stmt->bind_param("ss", $data['email'], $data['otp']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // OTP verified, create user
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert into users table
            $user_stmt = $conn->prepare("INSERT INTO users (user_type, full_name, email, phone, password, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $user_stmt->bind_param("sssss", $user_type, $data['full_name'], $data['email'], $data['phone'], $hashed_password);
            $user_type = 'partner';

            if ($user_stmt->execute()) {
                $user_id = $conn->insert_id;

                // Insert into partners table
                $partner_stmt = $conn->prepare("INSERT INTO partners (user_id, partner_type, aadhar_number, experience_years, hourly_rate, skills) VALUES (?, ?, ?, ?, ?, ?)");
                $partner_stmt->bind_param("issids", $user_id, $data['partner_type'], $data['aadhar_number'], $data['experience_years'], $data['hourly_rate'], $data['skills']);

                if ($partner_stmt->execute()) {
                    // Mark OTP as used
                    $otp_stmt = $conn->prepare("UPDATE otp_verification SET is_used = 1 WHERE email = ? AND otp = ?");
                    $otp_stmt->bind_param("ss", $data['email'], $data['otp']);
                    $otp_stmt->execute();

                    // Update user email verification
                    $conn->query("UPDATE users SET email_verified = 1 WHERE id = $user_id");

                    $conn->commit();

                    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
                } else {
                    throw new Exception("Failed to create partner profile");
                }
            } else {
                throw new Exception("Failed to create user account");
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
    }
}
?>
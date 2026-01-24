<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load SMTP config
$config_path = __DIR__ . '/../config/smtp-config.php';
if (!file_exists($config_path)) {
    error_log("SMTP config file not found");
    return false;
}

$smtp_config = require $config_path;

function sendOTPEmail(string $email, string $otp, string $name = 'User'): bool
{
    global $smtp_config;

    // Start session to store debug info
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear previous mail errors
    unset($_SESSION['mail_error']);
    unset($_SESSION['debug_otp']);

    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['smtp_username'];
        $mail->Password = $smtp_config['smtp_password'];
        $mail->Port = $smtp_config['smtp_port'];

        // Set encryption based on config
        if (isset($smtp_config['encryption'])) {
            if ($smtp_config['encryption'] === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtp_config['encryption'] === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
        } else {
            // Auto-detect based on port
            if ($smtp_config['smtp_port'] == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtp_config['smtp_port'] == 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
        }

        // Important: SSL/TLS options for Hostinger
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Timeout settings
        $mail->Timeout = 30;

        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
        $mail->addAddress($email, $name);

        // Reply-to
        $mail->addReplyTo($smtp_config['from_email'], $smtp_config['from_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Verification Code - Chef at Partner';

        // Simple HTML email template
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
            <div style="background: linear-gradient(135deg, #c7a07d, #a67c52); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="margin: 0;">CHEF AT PARTNER</h1>
                <h2 style="margin: 10px 0 0 0;">Email Verification</h2>
            </div>
            <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
                <p>Hello <strong>' . $name . '</strong>,</p>
                <p>Thank you for signing up with CHEF AT PARTNER! Use the OTP below to verify your email address:</p>
                
                <div style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #c7a07d; text-align: center; margin: 20px 0; padding: 15px; background: #fff; border: 2px dashed #ddd; border-radius: 5px;">
                    ' . $otp . '
                </div>
                
                <p>This OTP is valid for <strong>10 minutes</strong>.</p>
                <p>If you didn\'t request this verification, please ignore this email.</p>
                
                <p>Best regards,<br>
                <strong>CHEF AT PARTNER Team</strong></p>
            </div>
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center;">
                <p>This is an automated message, please do not reply to this email.</p>
                <p>&copy; ' . date('Y') . ' CHEF AT PARTNER. All rights reserved.</p>
            </div>
        </div>';

        $mail->AltBody = "Hello {$name},\n\nYour OTP verification code is: {$otp}\n\nThis OTP is valid for 10 minutes.\n\nIf you didn't request this verification, please ignore this email.\n\nBest regards,\nCHEF AT PARTNER Team";

        $result = $mail->send();

        if (!$result) {
            error_log("Failed to send OTP email to {$email}: " . $mail->ErrorInfo);
            $_SESSION['mail_error'] = 'Failed to send email: ' . $mail->ErrorInfo;
            $_SESSION['debug_otp'] = $otp;
            return false;
        }

        return true;

    } catch (Exception $e) {
        error_log("OTP Mail Exception for {$email}: " . $e->getMessage());
        $_SESSION['mail_error'] = 'Email error: ' . $e->getMessage();
        $_SESSION['debug_otp'] = $otp;
        return false;
    }
}

// AJAX handler
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    session_start();

    $response = ['success' => false, 'message' => ''];

    if (isset($_POST['email']) && isset($_POST['name'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $otp = random_int(100000, 999999);

            if (sendOTPEmail($email, $otp, $name)) {
                // Store OTP in session for verification
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp_code'] = $otp;
                $_SESSION['otp_expiry'] = time() + 600;

                $response['success'] = true;
                $response['message'] = 'OTP sent successfully!';
            } else {
                // Include debug OTP in response if email failed
                $response['message'] = 'Failed to send OTP. Please try again.';
                if (isset($_SESSION['debug_otp'])) {
                    $response['debug_otp'] = $_SESSION['debug_otp'];
                }
                if (isset($_SESSION['mail_error'])) {
                    $response['debug_error'] = $_SESSION['mail_error'];
                }
            }
        } else {
            $response['message'] = 'Invalid email address.';
        }
    } else {
        $response['message'] = 'Email and name are required.';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
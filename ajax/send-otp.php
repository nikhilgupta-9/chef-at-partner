<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the base directory
define('BASE_DIR', dirname(__DIR__));

// Include required files
require_once(BASE_DIR . '/vendor/autoload.php');

// Load SMTP configuration
$configPath = BASE_DIR . '/config/smtp-config.php';

if (!file_exists($configPath)) {
    error_log("SMTP config file not found at: $configPath");
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error. Please contact support.'
    ]);
    exit;
}

// Load configuration
$smtp_config = include($configPath);

// Check if config was loaded properly
if (!$smtp_config || !is_array($smtp_config)) {
    error_log("Failed to load SMTP configuration from: $configPath");
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error. Please contact support.'
    ]);
    exit;
}

// Validate required configuration keys
$required_keys = ['smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'from_email', 'from_name'];
foreach ($required_keys as $key) {
    if (!isset($smtp_config[$key]) || empty($smtp_config[$key])) {
        error_log("Missing SMTP configuration key: $key");
        echo json_encode([
            'success' => false,
            'message' => 'Server configuration error. Please contact support.'
        ]);
        exit;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/* =======================
   SEND OTP EMAIL
======================= */
function sendOTPviaSMTP(string $email, string $otp, string $name = ''): array
{
    global $smtp_config;

    // Validate parameters
    if (empty($email) || empty($otp)) {
        return [
            'success' => false,
            'error' => 'Invalid email or OTP'
        ];
    }

    // Log for debugging
    error_log("Attempting to send OTP to: $email");
    error_log("Generated OTP: $otp");
    error_log("SMTP Config: " . print_r($smtp_config, true));

    try {
        $mail = new PHPMailer(true);

        // Server settings - reduced debugging for production
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Change to DEBUG_SERVER for troubleshooting
        $mail->Debugoutput = function ($str, $level) {
            error_log("PHPMailer DEBUG [$level]: $str");
        };

        $mail->isSMTP();
        $mail->Host = $smtp_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['smtp_username'];
        $mail->Password = $smtp_config['smtp_password'];
        $mail->Port = $smtp_config['smtp_port'];

        // Encryption settings for Hostinger
        if ($smtp_config['smtp_port'] == 587) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($smtp_config['smtp_port'] == 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Default for Hostinger
        }

        // SMTP options for debugging
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30;

        // Recipients
        $from_email = $smtp_config['from_email'];
        $from_name = $smtp_config['from_name'];

        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($email, $name ?: 'User');
        $mail->addReplyTo($from_email, $from_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification OTP - CHEF AT PARTNER';

        // Simple HTML email template
        $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #333; text-align: center;'>Email Verification</h2>
                <p>Hello " . htmlspecialchars($name ?: 'User') . ",</p>
                <p>Your OTP for registration is:</p>
                <div style='background: #f5f5f5; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;'>
                    <h1 style='margin: 0; color: #ff6b35; letter-spacing: 5px;'>$otp</h1>
                </div>
                <p><strong>This OTP is valid for 10 minutes.</strong></p>
                <p>If you didn't request this verification, please ignore this email.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='color: #666; font-size: 12px;'>CHEF AT PARTNER Team<br>This is an automated message, please do not reply.</p>
            </div>
        ";

        $plainBody = "Your OTP is: $otp\n\nThis OTP is valid for 10 minutes.\n\nIf you didn't request this verification, please ignore this email.\n\nCHEF AT PARTNER Team";

        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody;

        if ($mail->send()) {
            error_log("Email sent successfully to: $email");
            return ['success' => true];
        } else {
            error_log("Email sending failed (no exception) for: $email");
            return [
                'success' => false,
                'error' => 'Failed to send email. Mailer error.'
            ];
        }

    } catch (Exception $e) {
        error_log("PHPMailer Exception for $email: " . $e->getMessage());
        error_log("PHPMailer Error Info: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'error' => 'Failed to send email. Please try again later.',
            'debug' => $mail->ErrorInfo // Remove in production
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!$email || $action !== 'register') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
        exit;
    }

    // Generate OTP
    $otp = random_int(100000, 999999);

    // Store OTP in session
    $_SESSION['email_otp'] = $otp;
    $_SESSION['email_otp_time'] = time();
    $_SESSION['email_for_otp'] = $email;

    // Send OTP
    $result = sendOTPviaSMTP($email, (string) $otp);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['error'] ?? 'OTP sending failed'
        ]);
    }

    exit;
}


// ... rest of your existing code for sendOTP() and request handling ...
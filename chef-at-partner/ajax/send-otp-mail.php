<?php
require_once __DIR__ . '/../vendor/autoload.php';

$smtp_config = require __DIR__ . '/../config/smtp-config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTPEmail(string $email, string $otp, string $name = 'User'): bool
{
    global $smtp_config;

    if (!is_array($smtp_config)) {
        error_log('SMTP config missing');
        return false;
    }

    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $smtp_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['smtp_username'];
        $mail->Password = $smtp_config['smtp_password'];
        $mail->Port = $smtp_config['smtp_port'];
        $mail->SMTPSecure = ($smtp_config['smtp_port'] == 587)
            ? PHPMailer::ENCRYPTION_STARTTLS
            : PHPMailer::ENCRYPTION_SMTPS;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Verification Code';

        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Hello {$name},</p>
            <p>Your OTP is:</p>
            <h1 style='letter-spacing:4px;'>$otp</h1>
            <p>This OTP is valid for 10 minutes.</p>
        ";

        $mail->AltBody = "Your OTP is: $otp (valid for 10 minutes)";

        return $mail->send();

    } catch (Exception $e) {
        error_log("OTP Mail Error ({$email}): " . $mail->ErrorInfo);
        return false;
    }
}

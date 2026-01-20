<?php
/**
 * Email Service Class for Chef at Partner Portal
 * Handles email verification, partner registration, and notifications
 */

class ChefAtPartnerMailer
{
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    private $mailer;
    private $website_url;

    public function __construct($config = array())
    {
        // Default configuration
        $default_config = array(
            'smtp_host' => 'smtp.hostinger.com',
            'smtp_port' => 587,
            'smtp_username' => 'testing@digitalwebtrackers.com',
            'smtp_password' => 'Q2ogk@3?Gt^',
            'from_email' => 'noreply@chefatpartner.com',
            'from_name' => 'Chef at Partner',
            'website_url' => 'https://chefatpartner.com',
            'use_smtp' => true
        );

        $config = array_merge($default_config, $config);

        $this->smtp_host = $config['smtp_host'];
        $this->smtp_port = $config['smtp_port'];
        $this->smtp_username = $config['smtp_username'];
        $this->smtp_password = $config['smtp_password'];
        $this->from_email = $config['from_email'];
        $this->from_name = $config['from_name'];
        $this->website_url = $config['website_url'];

        $this->initializeMailer($config['use_smtp']);
    }

    private function initializeMailer($use_smtp = true)
    {
        if ($use_smtp) {
            // Using PHPMailer (recommended for better email delivery)
            // You need to include PHPMailer autoload or use Composer
            require_once '../vendor/autoload.php'; // If using Composer

            $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->smtp_host;
            $this->mailer->Port = $this->smtp_port;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->smtp_username;
            $this->mailer->Password = $this->smtp_password;
            $this->mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->CharSet = 'UTF-8';
        } else {
            // Fallback to PHP mail() function
            $this->mailer = null;
        }
    }

    /**
     * Send Email Verification for all user types
     * @param string $email User email
     * @param string $verification_token Verification token
     * @param string $user_type Type of user (chef, partner, admin, customer)
     * @return bool
     */
    public function sendVerificationEmail($email, $verification_token, $user_type = 'chef' || 'bartender' || 'helper')
    {
        $verification_link = $this->website_url . "/verify-email?token=" . $verification_token . "&type=" . $user_type;

        $user_types = array(
            'chef' => 'Chef',
            'partner' => 'Restaurant Partner',
            'admin' => 'Administrator',
            'customer' => 'Customer'
        );

        $user_type_name = isset($user_types[$user_type]) ? $user_types[$user_type] : 'User';

        $subject = "Verify Your Email - Chef at Partner";

        $html_content = $this->getEmailTemplate('verification', array(
            'user_type' => $user_type_name,
            'verification_link' => $verification_link,
            'website_name' => 'Chef at Partner',
            'support_email' => 'support@chefatpartner.com'
        ));

        return $this->sendEmail($email, $subject, $html_content);
    }

    /**
     * Send Partner Registration Email
     * @param string $email Partner email
     * @param array $partner_data Partner registration details
     * @return bool
     */
    public function sendPartnerRegistrationEmail($email, $partner_data)
    {
        $subject = "Partner Registration Received - Chef at Partner";

        $html_content = $this->getEmailTemplate('partner_registration', array(
            'partner_name' => $partner_data['name'],
            'restaurant_name' => $partner_data['restaurant_name'],
            'contact_email' => $partner_data['email'],
            'phone' => $partner_data['phone'],
            'address' => $partner_data['address'],
            'website_name' => 'Chef at Partner',
            'admin_email' => 'partners@chefatpartner.com',
            'status_update_link' => $this->website_url . "/partner/dashboard"
        ));

        return $this->sendEmail($email, $subject, $html_content);
    }

    /**
     * Send Partner Registration Notification to Admin
     * @param array $partner_data Partner registration details
     * @return bool
     */
    public function sendPartnerRegistrationAdminNotification($partner_data)
    {
        $subject = "New Partner Registration - " . $partner_data['restaurant_name'];

        $html_content = $this->getEmailTemplate('partner_registration_admin', array(
            'partner_name' => $partner_data['name'],
            'restaurant_name' => $partner_data['restaurant_name'],
            'contact_email' => $partner_data['email'],
            'phone' => $partner_data['phone'],
            'address' => $partner_data['address'],
            'registration_date' => date('Y-m-d H:i:s'),
            'admin_dashboard_link' => $this->website_url . "/admin/partners/pending"
        ));

        // Send to admin email (can be configured)
        $admin_email = 'admin@chefatpartner.com';
        return $this->sendEmail($admin_email, $subject, $html_content);
    }

    /**
     * Send Welcome Email after successful verification
     * @param string $email User email
     * @param string $name User name
     * @param string $user_type Type of user
     * @return bool
     */
    public function sendWelcomeEmail($email, $name, $user_type = 'chef')
    {
        $dashboard_link = $this->website_url . "/" . $user_type . "/dashboard";

        $user_types = array(
            'chef' => array(
                'title' => 'Professional Chef',
                'features' => ['Create & Manage Menus', 'Get Booking Requests', 'Earn Money', 'Build Your Portfolio']
            ),
            'partner' => array(
                'title' => 'Restaurant Partner',
                'features' => ['Hire Top Chefs', 'Manage Events', 'Track Bookings', 'Increase Revenue']
            ),
            'customer' => array(
                'title' => 'Valued Customer',
                'features' => ['Book Expert Chefs', 'Host Memorable Events', 'Easy Payment', '24/7 Support']
            )
        );

        $user_info = isset($user_types[$user_type]) ? $user_types[$user_type] : $user_types['chef'];

        $subject = "Welcome to Chef at Partner!";

        $html_content = $this->getEmailTemplate('welcome', array(
            'name' => $name,
            'user_type_title' => $user_info['title'],
            'features' => $user_info['features'],
            'dashboard_link' => $dashboard_link,
            'website_name' => 'Chef at Partner',
            'support_email' => 'support@chefatpartner.com',
            'tutorial_link' => $this->website_url . "/getting-started"
        ));

        return $this->sendEmail($email, $subject, $html_content);
    }

    /**
     * Send Password Reset Email
     * @param string $email User email
     * @param string $reset_token Reset token
     * @param string $user_type Type of user
     * @return bool
     */
    public function sendPasswordResetEmail($email, $reset_token, $user_type = 'chef')
    {
        $reset_link = $this->website_url . "/reset-password?token=" . $reset_token . "&type=" . $user_type;

        $subject = "Password Reset Request - Chef at Partner";

        $html_content = $this->getEmailTemplate('password_reset', array(
            'reset_link' => $reset_link,
            'expiry_hours' => 24,
            'website_name' => 'Chef at Partner',
            'support_email' => 'support@chefatpartner.com'
        ));

        return $this->sendEmail($email, $subject, $html_content);
    }

    /**
     * Send Booking Confirmation to Chef
     * @param string $chef_email Chef email
     * @param array $booking_data Booking details
     * @return bool
     */
    public function sendChefBookingConfirmation($chef_email, $booking_data)
    {
        $subject = "New Booking Request - Chef at Partner";

        $html_content = $this->getEmailTemplate('chef_booking', array(
            'chef_name' => $booking_data['chef_name'],
            'customer_name' => $booking_data['customer_name'],
            'event_date' => $booking_data['event_date'],
            'event_type' => $booking_data['event_type'],
            'location' => $booking_data['location'],
            'guests' => $booking_data['guests'],
            'budget' => $booking_data['budget'],
            'booking_id' => $booking_data['booking_id'],
            'dashboard_link' => $this->website_url . "/chef/bookings",
            'website_name' => 'Chef at Partner'
        ));

        return $this->sendEmail($chef_email, $subject, $html_content);
    }

    /**
     * Send Booking Confirmation to Customer
     * @param string $customer_email Customer email
     * @param array $booking_data Booking details
     * @return bool
     */
    public function sendCustomerBookingConfirmation($customer_email, $booking_data)
    {
        $subject = "Booking Confirmed - Chef at Partner";

        $html_content = $this->getEmailTemplate('customer_booking', array(
            'customer_name' => $booking_data['customer_name'],
            'chef_name' => $booking_data['chef_name'],
            'event_date' => $booking_data['event_date'],
            'event_type' => $booking_data['event_type'],
            'location' => $booking_data['location'],
            'guests' => $booking_data['guests'],
            'total_amount' => $booking_data['total_amount'],
            'booking_id' => $booking_data['booking_id'],
            'contact_email' => 'bookings@chefatpartner.com',
            'website_name' => 'Chef at Partner'
        ));

        return $this->sendEmail($customer_email, $subject, $html_content);
    }

    /**
     * Get Email Template
     * @param string $template_name Template identifier
     * @param array $variables Template variables
     * @return string HTML content
     */
    private function getEmailTemplate($template_name, $variables = array())
    {
        $template = '';

        switch ($template_name) {
            case 'verification':
                $template = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Email Verification</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #ff6b35; color: white; padding: 20px; text-align: center; }
                        .content { padding: 30px; background-color: #f9f9f9; }
                        .button { display: inline-block; padding: 12px 24px; background-color: #ff6b35; 
                                color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>Chef at Partner</h1>
                        </div>
                        <div class="content">
                            <h2>Verify Your Email Address</h2>
                            <p>Hello ' . ($variables['user_type'] ?? 'User') . ',</p>
                            <p>Thank you for registering with Chef at Partner! Please verify your email address by clicking the button below:</p>
                            <p style="text-align: center;">
                                <a href="' . $variables['verification_link'] . '" class="button">Verify Email Address</a>
                            </p>
                            <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                            <p><small>' . $variables['verification_link'] . '</small></p>
                            <p>This verification link will expire in 24 hours.</p>
                            <p>If you didn\'t create an account, please ignore this email.</p>
                        </div>
                        <div class="footer">
                            <p>&copy; ' . date('Y') . ' ' . $variables['website_name'] . '. All rights reserved.</p>
                            <p>Need help? Contact us at ' . $variables['support_email'] . '</p>
                        </div>
                    </div>
                </body>
                </html>';
                break;

            case 'partner_registration':
                $template = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Partner Registration</title>
                </head>
                <body>
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background-color: #ff6b35; color: white; padding: 20px; text-align: center;">
                            <h1>Chef at Partner</h1>
                        </div>
                        <div style="padding: 30px; background-color: #f9f9f9;">
                            <h2>Partner Registration Received</h2>
                            <p>Dear ' . $variables['partner_name'] . ',</p>
                            <p>Thank you for your interest in partnering with Chef at Partner!</p>
                            <p>We have received your registration for <strong>' . $variables['restaurant_name'] . '</strong> and our team will review your application shortly.</p>
                            
                            <div style="background-color: white; padding: 20px; border-radius: 5px; margin: 20px 0;">
                                <h3>Registration Details:</h3>
                                <p><strong>Restaurant Name:</strong> ' . $variables['restaurant_name'] . '</p>
                                <p><strong>Contact Person:</strong> ' . $variables['partner_name'] . '</p>
                                <p><strong>Email:</strong> ' . $variables['contact_email'] . '</p>
                                <p><strong>Phone:</strong> ' . $variables['phone'] . '</p>
                                <p><strong>Address:</strong> ' . $variables['address'] . '</p>
                            </div>
                            
                            <p>Our team typically responds within 1-2 business days. Once approved, you\'ll be able to:</p>
                            <ul>
                                <li>Post chef requirements</li>
                                <li>Manage bookings</li>
                                <li>Access our chef network</li>
                                <li>Track your events</li>
                            </ul>
                            
                            <p>You can check your application status here: <br>
                            <a href="' . $variables['status_update_link'] . '">' . $variables['status_update_link'] . '</a></p>
                            
                            <p>For any questions, please contact our partnership team at ' . $variables['admin_email'] . '</p>
                        </div>
                        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
                            <p>&copy; ' . date('Y') . ' ' . $variables['website_name'] . '</p>
                        </div>
                    </div>
                </body>
                </html>';
                break;

            // Add more templates as needed...

            default:
                $template = '<p>' . implode('<br>', $variables) . '</p>';
                break;
        }

        return $template;
    }

    /**
     * Send Email using PHPMailer or mail() function
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $html_body HTML email body
     * @param string $alt_body Plain text alternative
     * @return bool
     */
    private function sendEmail($to, $subject, $html_body, $alt_body = '')
    {
        try {
            if ($this->mailer instanceof PHPMailer\PHPMailer\PHPMailer) {
                // Using PHPMailer
                $this->mailer->clearAddresses();
                $this->mailer->setFrom($this->from_email, $this->from_name);
                $this->mailer->addAddress($to);
                $this->mailer->isHTML(true);
                $this->mailer->Subject = $subject;
                $this->mailer->Body = $html_body;
                $this->mailer->AltBody = $alt_body ?: strip_tags($html_body);

                return $this->mailer->send();
            } else {
                // Fallback to PHP mail() function
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">" . "\r\n";
                $headers .= "Reply-To: " . $this->from_email . "\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                return mail($to, $subject, $html_body, $headers);
            }
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test Email Configuration
     * @param string $test_email Email to send test to
     * @return array Test results
     */
    public function testEmailConfiguration($test_email)
    {
        $results = array(
            'smtp_connection' => false,
            'email_sent' => false,
            'message' => ''
        );

        try {
            // Test SMTP connection
            if ($this->mailer instanceof PHPMailer\PHPMailer\PHPMailer) {
                $this->mailer->smtpConnect();
                $results['smtp_connection'] = true;
                $this->mailer->smtpClose();
            }

            // Send test email
            $subject = "Test Email - Chef at Partner";
            $body = "<h2>Test Email</h2><p>This is a test email from Chef at Partner mail system.</p>";

            $results['email_sent'] = $this->sendEmail($test_email, $subject, $body);
            $results['message'] = $results['email_sent'] ?
                "Test email sent successfully!" :
                "Failed to send test email.";

        } catch (Exception $e) {
            $results['message'] = "Error: " . $e->getMessage();
        }

        return $results;
    }
}

// Configuration file (config/email_config.php)
$email_config = array(
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'noreply@chefatpartner.com',
    'smtp_password' => 'your-secure-password',
    'from_email' => 'noreply@chefatpartner.com',
    'from_name' => 'Chef at Partner',
    'website_url' => 'https://chefatpartner.com',
    'use_smtp' => true
);

// Usage Examples:

// 1. Initialize the mailer
// require_once 'config/email_config.php';
// $mailer = new ChefAtPartnerMailer($email_config);

// 2. Send verification email
// $mailer->sendVerificationEmail('user@example.com', 'verification_token_123', 'chef');

// 3. Send partner registration email
// $partner_data = array(
//     'name' => 'John Doe',
//     'restaurant_name' => 'Gourmet Restaurant',
//     'email' => 'partner@example.com',
//     'phone' => '+1234567890',
//     'address' => '123 Main St, City, Country'
// );
// $mailer->sendPartnerRegistrationEmail('partner@example.com', $partner_data);
// $mailer->sendPartnerRegistrationAdminNotification($partner_data);

// 4. Send welcome email
// $mailer->sendWelcomeEmail('user@example.com', 'John Doe', 'chef');

// 5. Send password reset
// $mailer->sendPasswordResetEmail('user@example.com', 'reset_token_123', 'partner');

// 6. Test email configuration
// $test_results = $mailer->testEmailConfiguration('test@example.com');
// print_r($test_results);
?>
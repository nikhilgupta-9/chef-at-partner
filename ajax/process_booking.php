<?php
include_once(__DIR__ . '/../config/connect.php');
include_once(__DIR__ . '/../util/function.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $occasion = mysqli_real_escape_string($conn, $_POST['occasion']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $total_hours = mysqli_real_escape_string($conn, $_POST['total_hours']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
    $guest_count = (int) $_POST['guest_count'];

    // Generate unique booking ID
    $booking_id = 'BK' . date('Ymd') . strtoupper(uniqid());

    // Calculate total amount (you need to implement your pricing logic)
    $total_amount = calculateBookingAmount($total_hours, $guest_count, $service_type);

    // Insert into database
    $sql = "INSERT INTO bookings (
        booking_id, customer_id, partner_id, service_id, service_type, 
        booking_date, start_time, end_time, total_hours, total_amount, 
        status, address, special_requests, guest_count, created_at
    ) VALUES (
        '$booking_id', 
        NULL,  -- customer_id (should be set if user is logged in)
        NULL,  -- partner_id (to be assigned later)
        '$occasion', 
        '$service_type', 
        '$booking_date', 
        '$start_time', 
        '$end_time', 
        '$total_hours', 
        '$total_amount', 
        'pending', 
        '$address', 
        '$special_requests', 
        '$guest_count', 
        NOW()
    )";

    if (mysqli_query($conn, $sql)) {
        // Send confirmation email (implement this function)
        sendBookingConfirmation($email, $name, $booking_id, $booking_date);

        // Redirect with success message
        header('Location: ' . $BASE_URL . 'booking.php?success=1');
        exit();
    } else {
        header('Location: ' . $BASE_URL . 'booking.php?error=1');
        exit();
    }
}

function calculateBookingAmount($hours, $guests, $service_type)
{
    // Implement your pricing logic here
    $base_rate = 100; // Base rate per hour
    $guest_rate = 10; // Additional rate per guest

    $hours = (float) str_replace(' hours', '', $hours);
    $total = ($base_rate * $hours) + ($guest_rate * $guests);

    // Adjust for service type
    switch ($service_type) {
        case 'full_day':
            $total *= 0.9; // 10% discount for full day
            break;
        case 'multiple_days':
            $total *= 0.85; // 15% discount for multiple days
            break;
    }

    return $total;
}

function sendBookingConfirmation($email, $name, $booking_id, $date)
{
    // Implement email sending logic here
    $subject = "Booking Confirmation - $booking_id";
    $message = "Dear $name,\n\nYour booking has been received successfully.\n\n";
    $message .= "Booking ID: $booking_id\n";
    $message .= "Date: $date\n";
    $message .= "Status: Pending\n\n";
    $message .= "We will contact you within 24 hours to confirm your booking.\n\n";
    $message .= "Thank you for choosing our services!";

    // Use mail() or PHPMailer/SwiftMailer
    // mail($email, $subject, $message);
}
?>
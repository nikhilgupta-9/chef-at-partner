<?php
session_start();
include_once('config/connect.php');
include_once('util/function.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=booking.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'customer';

// Get user details
$user_stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Get occasions
$occasions = get_occasions();

// Get services (partners) available for booking
$services_stmt = $conn->prepare("
    SELECT p.id, p.partner_type, u.full_name, u.profile_image, p.hourly_rate, p.experience_years
    FROM partners p
    JOIN users u ON p.user_id = u.id
    WHERE u.status = 'active' AND p.s = 'active'
    ORDER BY p.partner_type, u.full_name
");
$services_stmt->execute();
$services = $services_stmt->get_result();

// Set date constraints
$date = date('Y-m-d');
$min_date = date('Y-m-d');
$max_date = date('Y-m-d', strtotime('+3 months'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service | CHEF AT PARTNER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-color: #c7a07d;
            --primary-dark: #a67c52;
            --primary-light: #f8f3ee;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-hero {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)),
                url('assets/images/banner/booking-hero.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0 60px;
            margin-bottom: 40px;
        }

        .page-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .page-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .booking-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 50px;
        }

        .booking-sidebar {
            background: var(--primary-light);
            padding: 40px 30px;
            height: 100%;
            border-right: 1px solid rgba(199, 160, 125, 0.2);
        }

        .booking-content {
            padding: 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 10px;
            border: 3px solid white;
            transition: all 0.3s;
        }

        .step.active .step-number {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-number {
            background: #28a745;
            color: white;
        }

        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            text-align: center;
        }

        .step.active .step-label {
            color: var(--primary-color);
        }

        .booking-step {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .booking-step.active {
            display: block;
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-light);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(199, 160, 125, 0.25);
        }

        .btn-theme {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            color: white;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        .btn-theme:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(199, 160, 125, 0.4);
        }

        .btn-outline-theme {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-theme:hover {
            background: var(--primary-color);
            color: white;
        }

        .service-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .service-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .service-card.selected {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .service-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .service-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .service-name {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .service-type {
            background: rgba(199, 160, 125, 0.1);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .service-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 14px;
        }

        .detail-item i {
            color: var(--primary-color);
            width: 16px;
        }

        .price-tag {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-dark);
            text-align: center;
            margin-top: 15px;
        }

        .price-tag small {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .time-slot-picker {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .time-slot {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .time-slot:hover {
            border-color: var(--primary-color);
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .time-slot.unavailable {
            background: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .booking-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6c757d;
            font-weight: 500;
        }

        .summary-value {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .total-amount {
            background: linear-gradient(135deg, var(--primary-light), white);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .total-label {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .total-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .alert-message {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }

        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 30px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 60px 0 40px;
            }

            .page-hero h1 {
                font-size: 2.5rem;
            }

            .booking-content {
                padding: 25px;
            }

            .time-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .floating-label {
            position: relative;
            margin-bottom: 25px;
        }

        .floating-label input,
        .floating-label textarea,
        .floating-label select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            background: transparent;
            transition: all 0.3s;
        }

        .floating-label label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 8px;
            color: #6c757d;
            transition: all 0.3s;
            pointer-events: none;
        }

        .floating-label input:focus+label,
        .floating-label input:not(:placeholder-shown)+label,
        .floating-label textarea:focus+label,
        .floating-label textarea:not(:placeholder-shown)+label,
        .floating-label select:focus+label,
        .floating-label select:not(:value="")+label {
            top: 0;
            font-size: 12px;
            color: var(--primary-color);
        }

        .floating-label input:focus,
        .floating-label textarea:focus,
        .floating-label select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(199, 160, 125, 0.25);
        }

        .calendar-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }

        .service-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .feature-tag {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include_once 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1>Book Your Perfect Event</h1>
                    <p class="lead">Professional chefs, bartenders, waiters, and cleaners for your special occasions.
                        Simple booking, exceptional service.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="text-white">
                        <i class="fas fa-calendar-check fa-4x mb-3"></i>
                        <h4>3-Step Booking</h4>
                        <p>Select → Schedule → Confirm</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Process -->
    <div class="container booking-container">
        <div class="booking-card">
            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 33%"></div>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator px-4">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Select Service</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Date & Time</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>

            <div class="row">
                <!-- Left Sidebar -->
                <div class="col-lg-4 booking-sidebar">
                    <div class="sticky-top" style="top: 20px;">
                        <h4 class="fw-bold mb-4" style="color: var(--primary-dark);">
                            <i class="fas fa-info-circle me-2"></i>Booking Information
                        </h4>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">Your Details</h6>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user-circle fa-2x me-3" style="color: var(--primary-color);"></i>
                                <div>
                                    <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                    <small class="text-muted">Customer</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope me-2" style="color: var(--primary-color);"></i>
                                <small><?php echo htmlspecialchars($user['email']); ?></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone me-2" style="color: var(--primary-color);"></i>
                                <small><?php echo htmlspecialchars($user['phone']); ?></small>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-shield-alt me-2"></i>Why Choose Us
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Background-verified professionals</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Secure online payment</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>24/7 customer support</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Easy cancellation policy</small>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-headset me-2"></i>Need Help?
                            </h6>
                            <p class="small mb-2">Call us at <strong>+91 9876543210</strong></p>
                            <p class="small mb-0">Email: <strong>support@chefatpartner.com</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-8 booking-content">
                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>

                    <!-- Step 1: Select Service -->
                    <div class="booking-step active" id="step1">
                        <h3 class="section-title">1. Choose Your Service Provider</h3>
                        <p class="text-muted mb-4">Select from our verified professionals. Click on a card to choose.
                        </p>

                        <!-- Service Type Filter -->
                        <div class="mb-4">
                            <div class="d-flex flex-wrap gap-2" id="serviceTypeFilter">
                                <button type="button" class="btn btn-outline-theme btn-sm active" data-type="all">All
                                    Services</button>
                                <button type="button" class="btn btn-outline-theme btn-sm"
                                    data-type="chef">Chefs</button>
                                <button type="button" class="btn btn-outline-theme btn-sm"
                                    data-type="bartender">Bartenders</button>
                                <button type="button" class="btn btn-outline-theme btn-sm"
                                    data-type="waiter">Waiters</button>
                                <button type="button" class="btn btn-outline-theme btn-sm"
                                    data-type="cleaner">Cleaners</button>
                            </div>
                        </div>

                        <!-- Services Grid -->
                        <div class="row g-4" id="servicesGrid">
                            <?php while ($service = $services->fetch_assoc()): ?>
                                <div class="col-md-6" data-type="<?php echo strtolower($service['partner_type']); ?>">
                                    <div class="service-card" data-service-id="<?php echo $service['id']; ?>">
                                        <div class="service-header">
                                            <?php if (!empty($service['profile_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($service['profile_image']); ?>"
                                                    alt="<?php echo htmlspecialchars($service['full_name']); ?>"
                                                    class="service-avatar">
                                            <?php else: ?>
                                                <div
                                                    class="service-avatar bg-primary d-flex align-items-center justify-content-center text-white">
                                                    <i class="fas fa-user fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h5 class="service-name mb-1">
                                                    <?php echo htmlspecialchars($service['full_name']); ?>
                                                </h5>
                                                <span
                                                    class="service-type"><?php echo htmlspecialchars($service['partner_type']); ?></span>
                                            </div>
                                        </div>

                                        <div class="service-details">
                                            <div class="detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span>Min <?php echo $service['min_hours'] ?? 2; ?> hrs</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-star"></i>
                                                <span><?php echo $service['experience_years'] ?? 2; ?>+ years</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-rupee-sign"></i>
                                                <span>₹<?php echo $service['hourly_rate'] ?? 500; ?>/hour</span>
                                            </div>
                                        </div>

                                        <div class="service-features">
                                            <span class="feature-tag">
                                                <i class="fas fa-certificate me-1"></i>Verified
                                            </span>
                                            <span class="feature-tag">
                                                <i class="fas fa-shield-alt me-1"></i>Insured
                                            </span>
                                        </div>

                                        <div class="price-tag">
                                            Starting at
                                            ₹<?php echo ($service['hourly_rate'] ?? 500) * ($service['min_hours'] ?? 2); ?>
                                            <small>for <?php echo $service['min_hours'] ?? 2; ?> hours</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- No Services Message -->
                        <div id="noServices" class="text-center py-5" style="display: none;">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h4>No Services Available</h4>
                            <p class="text-muted">No service providers are currently available for the selected
                                category.</p>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between mt-5">
                            <button class="btn btn-outline-theme" disabled>
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button class="btn btn-theme" id="nextStep1" disabled>
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Date & Time -->
                    <div class="booking-step" id="step2">
                        <h3 class="section-title">2. Select Date & Time</h3>
                        <p class="text-muted mb-4">Choose when you need the service.</p>

                        <!-- Selected Service Preview -->
                        <div class="alert alert-info alert-message" id="selectedServicePreview">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Selected: <span id="selectedServiceName"></span></h6>
                                    <small class="text-muted" id="selectedServiceType"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-theme" onclick="goToStep(1)">
                                    <i class="fas fa-edit me-1"></i> Change
                                </button>
                            </div>
                        </div>

                        <!-- Date Selection -->
                        <div class="time-slot-picker mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Select Date
                            </h5>
                            <div class="floating-label">
                                <input type="text" id="booking_date" class="form-control" placeholder="Select date"
                                    readonly>
                                <label for="booking_date">Booking Date</label>
                                <i class="fas fa-calendar calendar-icon"></i>
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div class="time-slot-picker mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-clock me-2"></i>Select Time Slot
                            </h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        <input type="time" id="start_time" class="form-control" min="07:00" max="23:00"
                                            required>
                                        <label for="start_time">Start Time</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        <input type="time" id="end_time" class="form-control" min="08:00" max="01:00"
                                            required>
                                        <label for="end_time">End Time</label>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light border">
                                <div class="d-flex">
                                    <i class="fas fa-info-circle text-primary me-3 mt-1"></i>
                                    <div>
                                        <h6 class="text-primary mb-2">Service Hours</h6>
                                        <p class="mb-1"><strong>Weekdays:</strong> 7:30 AM - 12:00 AM</p>
                                        <p class="mb-1"><strong>Saturday:</strong> 10:00 AM - 1:00 AM</p>
                                        <p class="mb-0"><strong>Sunday:</strong> 10:00 AM - 12:00 AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Occasion & Guests -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <select id="occasion" class="form-select" required>
                                        <option value="">Select an occasion</option>
                                        <?php foreach ($occasions as $occasion): ?>
                                            <option value="<?php echo $occasion['pro_id']; ?>">
                                                <?php echo htmlspecialchars($occasion['pro_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="other">Other</option>
                                    </select>
                                    <label for="occasion">Occasion/Event Type</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">
                                    <input type="number" id="guest_count" class="form-control" min="1" max="500"
                                        placeholder=" " required>
                                    <label for="guest_count">Number of Guests</label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between mt-5">
                            <button class="btn btn-outline-theme" onclick="goToStep(1)">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button class="btn btn-theme" id="nextStep2">
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Confirm & Pay -->
                    <div class="booking-step" id="step3">
                        <h3 class="section-title">3. Confirm Your Booking</h3>
                        <p class="text-muted mb-4">Review your booking details and submit.</p>

                        <!-- Booking Summary -->
                        <div class="booking-summary">
                            <h5 class="fw-bold mb-4">Booking Summary</h5>

                            <div class="summary-item">
                                <span class="summary-label">Service Provider</span>
                                <span class="summary-value" id="summaryServiceName"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Service Type</span>
                                <span class="summary-value" id="summaryServiceType"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Date</span>
                                <span class="summary-value" id="summaryDate"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Time</span>
                                <span class="summary-value" id="summaryTime"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Duration</span>
                                <span class="summary-value" id="summaryDuration"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Number of Guests</span>
                                <span class="summary-value" id="summaryGuests"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Occasion</span>
                                <span class="summary-value" id="summaryOccasion"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Hourly Rate</span>
                                <span class="summary-value" id="summaryHourlyRate"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Hours</span>
                                <span class="summary-value" id="summaryTotalHours"></span>
                            </div>

                            <div class="total-amount">
                                <div class="total-label">Total Amount</div>
                                <div class="total-value" id="summaryTotalAmount"></div>
                                <small class="text-muted">Inclusive of all taxes</small>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="mt-4">
                            <div class="floating-label">
                                <textarea id="special_requests" class="form-control" rows="4"
                                    placeholder=" "></textarea>
                                <label for="special_requests">Special Requests / Dietary Requirements</label>
                            </div>
                            <small class="text-muted">Any special requirements, menu preferences, allergies,
                                etc.</small>
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <div class="floating-label">
                                <textarea id="address" class="form-control" rows="3" placeholder=" "
                                    required></textarea>
                                <label for="address">Event Address</label>
                            </div>
                            <small class="text-muted">Full event address including city and zip code</small>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="terms-and-conditions.php" target="_blank">Terms & Conditions</a>
                                and <a href="privacy-policy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between mt-5">
                            <button class="btn btn-outline-theme" onclick="goToStep(2)">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button class="btn btn-theme btn-lg" id="confirmBooking">
                                <i class="fas fa-check-circle me-2"></i> Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let currentStep = 1;
        let selectedService = null;
        let serviceDetails = {};
        let bookingData = {};

        // Initialize flatpickr for date picker
        flatpickr("#booking_date", {
            minDate: "today",
            maxDate: new Date().fp_incr(90), // 3 months
            dateFormat: "Y-m-d",
            disableMobile: true,
            onChange: function (selectedDates, dateStr) {
                bookingData.date = dateStr;
                updateSummary();
            }
        });

        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('booking_date').value = tomorrow.toISOString().split('T')[0];
        bookingData.date = tomorrow.toISOString().split('T')[0];

        // Service selection
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function () {
                // Remove selection from all cards
                document.querySelectorAll('.service-card').forEach(c => {
                    c.classList.remove('selected');
                });

                // Select current card
                this.classList.add('selected');

                // Get service details
                const serviceId = this.dataset.serviceId;
                selectedService = serviceId;

                // Get service name and type
                const serviceName = this.querySelector('.service-name').textContent;
                const serviceType = this.querySelector('.service-type').textContent;
                const hourlyRate = this.querySelector('.service-details span:last-child').textContent.match(/\d+/)[0];

                serviceDetails = {
                    id: serviceId,
                    name: serviceName,
                    type: serviceType,
                    hourlyRate: parseInt(hourlyRate)
                };

                // Enable next button
                document.getElementById('nextStep1').disabled = false;

                // Update preview
                document.getElementById('selectedServiceName').textContent = serviceName;
                document.getElementById('selectedServiceType').textContent = serviceType + ' Service';

                // Store in booking data
                bookingData.serviceId = serviceId;
                bookingData.serviceName = serviceName;
                bookingData.serviceType = serviceType;
                bookingData.hourlyRate = parseInt(hourlyRate);

                showAlert('Service selected: ' + serviceName, 'success');
            });
        });

        // Service type filter
        document.querySelectorAll('#serviceTypeFilter button').forEach(btn => {
            btn.addEventListener('click', function () {
                // Update active button
                document.querySelectorAll('#serviceTypeFilter button').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                const filterType = this.dataset.type;
                filterServices(filterType);
            });
        });

        function filterServices(type) {
            const services = document.querySelectorAll('#servicesGrid .col-md-6');
            let visibleCount = 0;

            services.forEach(service => {
                if (type === 'all' || service.dataset.type === type) {
                    service.style.display = 'block';
                    visibleCount++;
                } else {
                    service.style.display = 'none';
                }
            });

            // Show/hide no services message
            document.getElementById('noServices').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Time calculation
        function calculateHours() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime) {
                let start = new Date('1970-01-01T' + startTime + ':00');
                let end = new Date('1970-01-01T' + endTime + ':00');

                // Handle overnight (end time next day)
                if (end < start) {
                    end.setDate(end.getDate() + 1);
                }

                const diffMs = end - start;
                const hours = diffMs / (1000 * 60 * 60);

                bookingData.startTime = startTime;
                bookingData.endTime = endTime;
                bookingData.totalHours = hours.toFixed(1);

                // Calculate total amount
                if (bookingData.hourlyRate) {
                    bookingData.totalAmount = (bookingData.hourlyRate * hours).toFixed(2);
                }

                updateSummary();
            }
        }

        document.getElementById('start_time').addEventListener('change', calculateHours);
        document.getElementById('end_time').addEventListener('change', calculateHours);

        // Update other fields
        document.getElementById('guest_count').addEventListener('input', function () {
            bookingData.guests = this.value;
            updateSummary();
        });

        document.getElementById('occasion').addEventListener('change', function () {
            const occasionText = this.options[this.selectedIndex].text;
            bookingData.occasion = occasionText;
            updateSummary();
        });

        // Step navigation
        window.goToStep = function (step) {
            // Validate current step before moving
            if (step > currentStep) {
                if (!validateStep(currentStep)) {
                    return;
                }
            }

            // Update progress bar
            document.getElementById('progressFill').style.width = ((step - 1) * 33.33) + '%';

            // Update step indicators
            document.querySelectorAll('.step').forEach(s => {
                s.classList.remove('active');
                if (parseInt(s.dataset.step) < step) {
                    s.classList.add('completed');
                }
            });
            document.querySelector(`.step[data-step="${step}"]`).classList.add('active');

            // Show/hide steps
            document.querySelectorAll('.booking-step').forEach(s => {
                s.classList.remove('active');
            });
            document.getElementById(`step${step}`).classList.add('active');

            currentStep = step;

            // Update summary when reaching step 3
            if (step === 3) {
                updateSummary();
            }
        }

        document.getElementById('nextStep1').addEventListener('click', function () {
            if (selectedService) {
                goToStep(2);
            }
        });

        document.getElementById('nextStep2').addEventListener('click', function () {
            goToStep(3);
        });

        // Update booking summary
        function updateSummary() {
            if (currentStep === 3) {
                document.getElementById('summaryServiceName').textContent = bookingData.serviceName || 'Not selected';
                document.getElementById('summaryServiceType').textContent = bookingData.serviceType || 'Not selected';
                document.getElementById('summaryDate').textContent = formatDate(bookingData.date) || 'Not selected';
                document.getElementById('summaryTime').textContent = (bookingData.startTime || '--') + ' to ' + (bookingData.endTime || '--');
                document.getElementById('summaryDuration').textContent = (bookingData.totalHours || '0') + ' hours';
                document.getElementById('summaryGuests').textContent = bookingData.guests || 'Not specified';
                document.getElementById('summaryOccasion').textContent = bookingData.occasion || 'Not specified';
                document.getElementById('summaryHourlyRate').textContent = '₹' + (bookingData.hourlyRate || '0') + '/hour';
                document.getElementById('summaryTotalHours').textContent = bookingData.totalHours || '0';
                document.getElementById('summaryTotalAmount').textContent = '₹' + (bookingData.totalAmount || '0');
            }
        }

        // Validate step
        function validateStep(step) {
            switch (step) {
                case 1:
                    if (!selectedService) {
                        showAlert('Please select a service provider', 'danger');
                        return false;
                    }
                    return true;

                case 2:
                    const date = document.getElementById('booking_date').value;
                    const startTime = document.getElementById('start_time').value;
                    const endTime = document.getElementById('end_time').value;
                    const guests = document.getElementById('guest_count').value;
                    const occasion = document.getElementById('occasion').value;

                    if (!date) {
                        showAlert('Please select a date', 'danger');
                        return false;
                    }
                    if (!startTime || !endTime) {
                        showAlert('Please select start and end time', 'danger');
                        return false;
                    }
                    if (!guests || guests < 1) {
                        showAlert('Please enter number of guests', 'danger');
                        return false;
                    }
                    if (!occasion) {
                        showAlert('Please select an occasion', 'danger');
                        return false;
                    }

                    // Validate time range
                    const today = new Date();
                    const selectedDate = new Date(date);

                    if (selectedDate < today.setHours(0, 0, 0, 0)) {
                        showAlert('Booking date cannot be in the past', 'danger');
                        return false;
                    }

                    return true;

                default:
                    return true;
            }
        }

        // Confirm booking
        document.getElementById('confirmBooking').addEventListener('click', function () {
            if (!validateStep(3)) return;

            const address = document.getElementById('address').value;
            const specialRequests = document.getElementById('special_requests').value;
            const terms = document.getElementById('terms').checked;

            if (!address.trim()) {
                showAlert('Please enter event address', 'danger');
                return;
            }
            if (!terms) {
                showAlert('Please agree to terms and conditions', 'danger');
                return;
            }

            // Prepare booking data
            const formData = new FormData();
            formData.append('action', 'create_booking');
            formData.append('customer_id', '<?php echo $user_id; ?>');
            formData.append('partner_id', bookingData.serviceId);
            formData.append('partner_type', bookingData.serviceType);
            formData.append('service_id', bookingData.serviceId);
            formData.append('guest_count', bookingData.guests);
            formData.append('service_type', 'booking');
            formData.append('booking_date', bookingData.date);
            formData.append('start_time', bookingData.startTime);
            formData.append('end_time', bookingData.endTime);
            formData.append('total_hours', bookingData.totalHours);
            formData.append('total_amount', bookingData.totalAmount);
            formData.append('address', address);
            formData.append('special_requests', specialRequests);
            formData.append('occasion', bookingData.occasion);

            // Show loading
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            // Submit booking
            fetch('ajax/process_booking.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Booking confirmed successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'bookings.php?success=true&booking_id=' + data.booking_id;
                        }, 2000);
                    } else {
                        showAlert('Error: ' + data.message, 'danger');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // Helper functions
        function formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show alert-message`;
            alert.id = alertId;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;

            alertContainer.appendChild(alert);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                const alertEl = document.getElementById(alertId);
                if (alertEl) {
                    alertEl.classList.remove('show');
                    setTimeout(() => alertEl.remove(), 300);
                }
            }, 5000);
        }

        // Set default time to evening (6 PM - 10 PM)
        document.getElementById('start_time').value = '18:00';
        document.getElementById('end_time').value = '22:00';
        calculateHours();

        // Initialize summary
        updateSummary();
    </script>
</body>

</html>
<?php
include_once('../config/connect.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get customer details
$customer_stmt = $conn->prepare("
    SELECT c.*, u.email, u.phone 
    FROM customers c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.user_id = ?
");
$customer_stmt->bind_param("i", $user_id);
$customer_stmt->execute();
$customer = $customer_stmt->get_result()->fetch_assoc();

// Get recent bookings
$bookings_stmt = $conn->prepare("
    SELECT b.*, p.partner_type, u.full_name as partner_name 
    FROM bookings b
    JOIN partners p ON b.partner_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE b.customer_id = ?
    ORDER BY b.created_at DESC
    LIMIT 5
");
$bookings_stmt->bind_param("i", $customer['id']);
$bookings_stmt->execute();
$recent_bookings = $bookings_stmt->get_result();

// Get available partners
$partners_stmt = $conn->prepare("
    SELECT p.*, u.full_name, u.email, u.phone, 
           (SELECT AVG(rating) FROM reviews WHERE partner_id = p.id) as avg_rating
    FROM partners p
    JOIN users u ON p.user_id = u.id
    WHERE p.is_verified = 1 AND p.availability = 'available'
    AND u.status = 'active'
");
$partners_stmt->execute();
$available_partners = $partners_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <style>
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }

        .stats-card.bookings {
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
        }

        .stats-card.pending {
            background: linear-gradient(45deg, #3498db, #2980b9);
        }

        .stats-card.completed {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
        }

        .partner-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }

        .partner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include_once '../includes/header.php' ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="text-center mb-4">
                        <div class="mb-3" style="font-size: 50px;">👤</div>
                        <h4>
                            <?php echo $_SESSION['full_name']; ?>
                        </h4>
                        <p class="text-muted">Customer</p>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bookings.php">
                                <i class="fas fa-calendar-check"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="book-partner.php">
                                <i class="fas fa-plus-circle"></i> Book Partner
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card bookings">
                            <h3>
                                <?php echo $customer['total_bookings']; ?>
                            </h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card pending">
                            <h3>
                                <?php
                                // Count pending bookings
                                $pending_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status = 'pending'");
                                $pending_stmt->bind_param("i", $customer['id']);
                                $pending_stmt->execute();
                                echo $pending_stmt->get_result()->fetch_row()[0];
                                ?>
                            </h3>
                            <p>Pending Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card completed">
                            <h3>
                                <?php
                                // Count completed bookings
                                $completed_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status = 'completed'");
                                $completed_stmt->bind_param("i", $customer['id']);
                                $completed_stmt->execute();
                                echo $completed_stmt->get_result()->fetch_row()[0];
                                ?>
                            </h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Available Partners -->
                <div class="dashboard-card">
                    <h4>Available Partners</h4>
                    <div class="row">
                        <?php while ($partner = $available_partners->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="partner-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5>
                                                <?php echo $partner['full_name']; ?>
                                            </h5>
                                            <p class="text-muted">
                                                <i class="fas fa-utensils"></i>
                                                <?php echo ucfirst($partner['partner_type']); ?>
                                            </p>
                                        </div>
                                        <span class="badge bg-success">Available</span>
                                    </div>

                                    <div class="mb-2">
                                        <span class="text-warning">
                                            <?php
                                            $rating = $partner['avg_rating'] ? round($partner['avg_rating'], 1) : 'No ratings';
                                            echo $rating . ' ⭐';
                                            ?>
                                        </span>
                                        <span class="ms-3">
                                            <i class="fas fa-briefcase"></i>
                                            <?php echo $partner['experience_years']; ?> years
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>₹
                                                <?php echo $partner['hourly_rate']; ?>
                                            </strong>/hour
                                        </div>
                                        <a href="book-partner.php?partner_id=<?php echo $partner['id']; ?>"
                                            class="btn btn-sm btn-theme">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="dashboard-card">
                    <h4>Recent Bookings</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Partner</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo $booking['booking_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $booking['partner_name']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br>
                                            <small>
                                                <?php echo $booking['start_time'] . ' - ' . $booking['end_time']; ?>
                                            </small>
                                        </td>
                                        <td>₹
                                            <?php echo $booking['total_amount']; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php
                                            switch ($booking['status']) {
                                                case 'pending':
                                                    echo 'warning';
                                                    break;
                                                case 'confirmed':
                                                    echo 'info';
                                                    break;
                                                case 'in_progress':
                                                    echo 'primary';
                                                    break;
                                                case 'completed':
                                                    echo 'success';
                                                    break;
                                                case 'cancelled':
                                                    echo 'danger';
                                                    break;
                                            }
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view-booking.php?id=<?php echo $booking['id']; ?>"
                                                class="btn btn-sm btn-outline-theme">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php' ?>
</body>

</html>
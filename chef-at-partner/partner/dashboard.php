<?php
include_once('../config/connect.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'partner') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get partner details
$partner_stmt = $conn->prepare("
    SELECT p.*, u.email, u.phone, u.status as user_status 
    FROM partners p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = ?
");
$partner_stmt->bind_param("i", $user_id);
$partner_stmt->execute();
$partner = $partner_stmt->get_result()->fetch_assoc();

// Get partner statistics
$stats_stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
        AVG(r.rating) as avg_rating
    FROM bookings b
    LEFT JOIN reviews r ON b.id = r.booking_id
    WHERE b.partner_id = ?
");
$stats_stmt->bind_param("i", $partner['id']);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Get upcoming bookings
$upcoming_stmt = $conn->prepare("
    SELECT b.*, u.full_name as customer_name, u.phone as customer_phone
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN users u ON c.user_id = u.id
    WHERE b.partner_id = ? AND b.status IN ('confirmed', 'pending')
    AND b.booking_date >= CURDATE()
    ORDER BY b.booking_date, b.start_time
    LIMIT 5
");
$upcoming_stmt->bind_param("i", $partner['id']);
$upcoming_stmt->execute();
$upcoming_bookings = $upcoming_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Dashboard - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
</head>

<body>
    <?php include_once '../includes/header.php' ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="text-center mb-4">
                        <div class="mb-3" style="font-size: 50px;">
                            <?php
                            $icons = ['chef' => '👨‍🍳', 'bartender' => '🍸', 'helper' => '👨‍🍼', 'cleaner' => '🧹'];
                            echo $icons[$partner['partner_type']] ?? '👤';
                            ?>
                        </div>
                        <h4>
                            <?php echo $_SESSION['full_name']; ?>
                        </h4>
                        <p class="text-muted">
                            <?php echo ucfirst($partner['partner_type']); ?>
                        </p>

                        <?php if ($partner['is_verified']): ?>
                            <span class="badge bg-success mb-2">Verified</span>
                        <?php else: ?>
                            <span class="badge bg-warning mb-2">Pending Verification</span>
                        <?php endif; ?>

                        <div class="mt-3">
                            <strong>Rating:</strong>
                            <div class="text-warning">
                                <?php
                                $rating = $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'No ratings';
                                echo $rating . ' ⭐';
                                ?>
                            </div>
                        </div>
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
                            <a class="nav-link" href="availability.php">
                                <i class="fas fa-clock"></i> Availability
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="earnings.php">
                                <i class="fas fa-money-bill-wave"></i> Earnings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> Profile
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
                    <div class="col-md-3">
                        <div class="stats-card bookings">
                            <h3>
                                <?php echo $stats['total_bookings']; ?>
                            </h3>
                            <p>Total Jobs</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card pending">
                            <h3>
                                <?php echo $stats['pending_bookings']; ?>
                            </h3>
                            <p>Pending Jobs</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card completed">
                            <h3>
                                <?php echo $stats['completed_bookings']; ?>
                            </h3>
                            <p>Completed</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
                            <h3>₹
                                <?php
                                // Calculate total earnings
                                $earnings_stmt = $conn->prepare("SELECT SUM(total_amount) FROM bookings WHERE partner_id = ? AND status = 'completed'");
                                $earnings_stmt->bind_param("i", $partner['id']);
                                $earnings_stmt->execute();
                                $earnings = $earnings_stmt->get_result()->fetch_row()[0];
                                echo $earnings ? $earnings : '0';
                                ?>
                            </h3>
                            <p>Total Earnings</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <?php if ($partner['is_verified']): ?>
                    <div class="dashboard-card mb-4">
                        <h4>Quick Actions</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="availability.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-clock"></i><br>
                                    Update Availability
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="bookings.php" class="btn btn-outline-success w-100 mb-2">
                                    <i class="fas fa-calendar"></i><br>
                                    View Bookings
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="profile.php" class="btn btn-outline-info w-100 mb-2">
                                    <i class="fas fa-edit"></i><br>
                                    Edit Profile
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="earnings.php" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="fas fa-chart-line"></i><br>
                                    View Earnings
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Upcoming Bookings -->
                <div class="dashboard-card">
                    <h4>Upcoming Bookings</h4>
                    <?php if ($upcoming_bookings->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Date & Time</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($booking = $upcoming_bookings->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong>
                                                    <?php echo $booking['customer_name']; ?>
                                                </strong><br>
                                                <small>
                                                    <?php echo $booking['customer_phone']; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br>
                                                <small>
                                                    <?php echo $booking['start_time'] . ' - ' . $booking['end_time']; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo $booking['total_hours']; ?> hours
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
                                                        echo 'success';
                                                        break;
                                                    default:
                                                        echo 'secondary';
                                                }
                                                ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($booking['status'] == 'pending'): ?>
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')">
                                                        Accept
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')">
                                                        Reject
                                                    </button>
                                                <?php else: ?>
                                                    <a href="view-booking.php?id=<?php echo $booking['id']; ?>"
                                                        class="btn btn-sm btn-outline-theme">
                                                        View
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No upcoming bookings found.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Verification Status -->
                <?php if (!$partner['is_verified']): ?>
                    <div class="dashboard-card">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Account Pending Verification</h5>
                            <p>Your account is under verification by our admin team. You'll be able to accept bookings once
                                verified.</p>
                            <p>Status: <strong>
                                    <?php echo ucfirst($partner['user_status']); ?>
                                </strong></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function updateBookingStatus(bookingId, status) {
            if (confirm('Are you sure?')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "update-booking-status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            alert('Booking status updated!');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                };
                xhr.send(`booking_id=${bookingId}&status=${status}`);
            }
        }
    </script>

    <?php include_once '../includes/footer.php' ?>
</body>

</html>
<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query
$query = "
    SELECT b.*, p.partner_type, u.full_name as partner_name, u.profile_image,
           (SELECT COUNT(*) FROM reviews WHERE booking_id = b.id) as has_review
    FROM bookings b
    JOIN partners p ON b.partner_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE b.customer_id = ?
";

$params = [$user_id];
$types = "i";

if (!empty($status)) {
    $query .= " AND b.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($date_from)) {
    $query .= " AND DATE(b.booking_date) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $query .= " AND DATE(b.booking_date) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result();

// Get status counts for filter
$status_counts_stmt = $conn->prepare("
    SELECT status, COUNT(*) as count 
    FROM bookings 
    WHERE customer_id = ? 
    GROUP BY status
");
$status_counts_stmt->bind_param("i", $user_id);
$status_counts_stmt->execute();
$status_counts = $status_counts_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <style>
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-tab {
            padding: 8px 20px;
            border-radius: 20px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .filter-tab:hover {
            background: #e9ecef;
            color: #495057;
        }

        .filter-tab.active {
            background: #c7a07d;
            border-color: #c7a07d;
            color: white;
        }

        .date-filter {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .empty-state-icon {
            font-size: 80px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .booking-detail-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
        }

        .booking-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            width: 20px;
            color: #c7a07d;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <?php include_once '../includes/header.php' ?>

    <div class="container-fluid mt-5 pt-4">
        <div class="row" style="margin-top: 80px;">
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-2">
                <div class="sidebar">
                    <!-- Same sidebar as dashboard.php -->
                    <?php include 'includes/sidebar.php'; ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <div class="mb-4">
                    <h1 class="h3 mb-2">My Bookings</h1>
                    <p class="text-muted">Manage and track all your bookings in one place</p>
                </div>

                <!-- Filter Card -->
                <div class="filter-card">
                    <h5 class="mb-3">Filter Bookings</h5>

                    <!-- Status Tabs -->
                    <div class="filter-tabs">
                        <a href="bookings.php" class="filter-tab <?php echo empty($status) ? 'active' : ''; ?>">
                            All <span class="badge bg-secondary ms-1">
                                <?php echo array_sum(array_column($status_counts, 'count')); ?>
                            </span>
                        </a>
                        <?php foreach ($status_counts as $status_item): ?>
                            <a href="bookings.php?status=<?php echo $status_item['status']; ?>"
                                class="filter-tab <?php echo $status == $status_item['status'] ? 'active' : ''; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $status_item['status'])); ?>
                                <span class="badge bg-secondary ms-1">
                                    <?php echo $status_item['count']; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" class="date-filter">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="<?php echo $date_from; ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-theme w-100">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bookings List -->
                <?php if ($bookings->num_rows > 0): ?>
                    <?php while ($booking = $bookings->fetch_assoc()): ?>
                        <div class="booking-detail-card">
                            <div class="booking-header">
                                <div>
                                    <h5 class="mb-1">Booking #
                                        <?php echo $booking['booking_id']; ?>
                                    </h5>
                                    <p class="text-muted mb-0">Booked on
                                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?php echo $booking['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                </span>
                            </div>

                            <div class="booking-info-grid">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <div>
                                        <div class="small text-muted">Partner</div>
                                        <div class="fw-semibold">
                                            <?php echo $booking['partner_name']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-utensils"></i>
                                    <div>
                                        <div class="small text-muted">Service Type</div>
                                        <div class="fw-semibold">
                                            <?php echo ucfirst($booking['partner_type']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <div>
                                        <div class="small text-muted">Date</div>
                                        <div class="fw-semibold">
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <div class="small text-muted">Time</div>
                                        <div class="fw-semibold">
                                            <?php echo $booking['start_time'] . ' - ' . $booking['end_time']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <div>
                                        <div class="small text-muted">Guests</div>
                                        <div class="fw-semibold">
                                            <?php echo $booking['number_of_guests']; ?> Persons
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <div>
                                        <div class="small text-muted">Amount</div>
                                        <div class="fw-semibold">₹
                                            <?php echo $booking['total_amount']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($booking['status'] == 'completed' && !$booking['has_review']): ?>
                                        <a href="add-review.php?booking_id=<?php echo $booking['id']; ?>"
                                            class="btn btn-sm btn-outline-theme">
                                            <i class="fas fa-star me-1"></i> Add Review
                                        </a>
                                    <?php elseif ($booking['status'] == 'completed' && $booking['has_review']): ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i> Reviewed
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="action-buttons">
                                    <a href="view-booking.php?id=<?php echo $booking['id']; ?>"
                                        class="btn btn-sm btn-outline-theme">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>

                                    <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                        <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="fas fa-times me-1"></i> Cancel Booking
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($booking['status'] == 'confirmed' || $booking['status'] == 'in_progress'): ?>
                                        <a href="messages.php?partner_id=<?php echo $booking['partner_id']; ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-comment me-1"></i> Message
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="far fa-calendar-times"></i>
                        </div>
                        <h3 class="mb-3">No Bookings Found</h3>
                        <p class="text-muted mb-4">You haven't made any bookings yet. Start by booking a partner!</p>
                        <a href="book-partner.php" class="btn btn-theme">
                            <i class="fas fa-plus-circle me-1"></i> Book a Partner
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php' ?>
</body>

</html>
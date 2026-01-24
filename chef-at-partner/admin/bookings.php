<?php
include "db-conn.php";
// include "auth-check.php"; 

// Check if user is admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) == true) {
    header("Location: login.php");
    exit();
}

// Get filter values
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$sql = "SELECT b.*, 
               u.full_name as customer_name, 
               u.email as customer_email, 
               u.phone as customer_phone,
               c.address as customer_address,
               c.preferred_location,
            --    p.name as partner_name,
            --    p.email as partner_email,
            --    p.mobile as partner_phone,
               s.service_name,
               pro.pro_name as occasion_name
        FROM bookings b
        LEFT JOIN customers c ON b.customer_id = c.id
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN partners p ON b.partner_id = p.id
        LEFT JOIN services s ON b.service_id = s.id
        LEFT JOIN products pro ON b.service_id = pro.pro_id
        WHERE 1=1";

if (!empty($filter_status) && $filter_status !== 'all') {
    $sql .= " AND b.status = '$filter_status'";
}

if (!empty($filter_date)) {
    $sql .= " AND DATE(b.booking_date) = '$filter_date'";
}

if (!empty($search)) {
    $sql .= " AND (b.booking_id LIKE '%$search%' 
                   OR u.name LIKE '%$search%' 
                   OR u.email LIKE '%$search%' 
                   OR b.address LIKE '%$search%'
                   OR u.mobile LIKE '%$search%')";
}

$sql .= " ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $sql);

// Get statistics
$stats_sql = "SELECT 
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) as assigned,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
    COUNT(*) as total,
    SUM(total_amount) as total_revenue
    FROM bookings";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Bookings Management | Chef At Partner</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-assigned {
            background-color: #cce5ff;
            color: #004085;
        }

        .action-dropdown {
            min-width: 120px;
        }

        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body class="crm_body_bg">

    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">

        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner ">
            <div class="container-fluid p-0 sm_padding_15px">

                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Bookings Management</h4>
                            <div class="page-title-right">
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#assignPartnerModal">
                                    <i class="fas fa-user-plus me-2"></i>Assign Partner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['total']; ?></div>
                            <div class="stat-label">Total Bookings</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['pending']; ?></div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['confirmed']; ?></div>
                            <div class="stat-label">Confirmed</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['completed']; ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
                            <div class="stat-label">Cancelled</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="filter-card">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all">All Status</option>
                                        <option value="pending" <?php echo ($filter_status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo ($filter_status == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="assigned" <?php echo ($filter_status == 'assigned') ? 'selected' : ''; ?>>Assigned</option>
                                        <option value="completed" <?php echo ($filter_status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($filter_status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Booking Date</label>
                                    <input type="date" name="date" class="form-control"
                                        value="<?php echo $filter_date; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by ID, Name, Email or Address"
                                        value="<?php echo $search; ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Booking ID</th>
                                                <th>Customer</th>
                                                <th>Service</th>
                                                <th>Date & Time</th>
                                                <th>Address</th>
                                                <th>Amount</th>
                                                <th>Partner</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $counter = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Format date and time
                                                $booking_date = date('d M Y', strtotime($row['booking_date']));
                                                $start_time = date('h:i A', strtotime($row['start_time']));
                                                $end_time = date('h:i A', strtotime($row['end_time']));

                                                // Get status badge class
                                                $status_class = 'status-' . $row['status'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $row['booking_id']; ?></strong><br>
                                                        <small class="text-muted">Created:
                                                            <?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $row['customer_name']; ?></strong><br>
                                                        <small><?php echo $row['customer_email']; ?></small><br>
                                                        <small><?php echo $row['customer_phone']; ?></small>
                                                    </td>
                                                    <td><?php echo $row['service_type']; ?></td>
                                                    <td>
                                                        <strong><?php echo $booking_date; ?></strong><br>
                                                        <?php echo $start_time; ?> - <?php echo $end_time; ?><br>
                                                        <small>Hours: <?php echo $row['total_hours']; ?></small>
                                                    </td>
                                                    <td><?php echo substr($row['address'], 0, 30) . '...'; ?></td>
                                                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                                    <td>
                                                        <?php if ($row['full_name'] ?? ''): ?>
                                                            <span
                                                                class="badge bg-info"><?php echo $row['full_name'] ?? 'N/A'; ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Not Assigned</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge <?php echo $status_class; ?>">
                                                            <?php echo ucfirst($row['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" data-bs-toggle="dropdown">
                                                                Actions
                                                            </button>
                                                            <ul class="dropdown-menu action-dropdown">
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#viewBookingModal<?php echo $row['id']; ?>">
                                                                        <i class="fas fa-eye me-2"></i>View Details
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#updateStatusModal<?php echo $row['id']; ?>">
                                                                        <i class="fas fa-sync me-2"></i>Update Status
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#assignPartnerModalSingle<?php echo $row['id']; ?>">
                                                                        <i class="fas fa-user-plus me-2"></i>Assign Partner
                                                                    </a>
                                                                </li>
                                                                <?php if (!empty($row['special_requests'])): ?>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#viewRequestsModal<?php echo $row['id']; ?>">
                                                                            <i class="fas fa-sticky-note me-2"></i>View Requests
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger"
                                                                        href="booking_delete.php?id=<?php echo $row['id']; ?>"
                                                                        onclick="return confirm('Are you sure you want to delete this booking?');">
                                                                        <i class="fas fa-trash me-2"></i>Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- View Booking Modal -->
                                                <div class="modal fade" id="viewBookingModal<?php echo $row['id']; ?>"
                                                    tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Booking Details -
                                                                    <?php echo $row['booking_id']; ?>
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h6>Customer Information</h6>
                                                                        <p><strong>Name:</strong>
                                                                            <?php echo $row['customer_name']; ?></p>
                                                                        <p><strong>Email:</strong>
                                                                            <?php echo $row['customer_email']; ?></p>
                                                                        <p><strong>Phone:</strong>
                                                                            <?php echo $row['customer_phone']; ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6>Booking Information</h6>
                                                                        <p><strong>Service Type:</strong>
                                                                            <?php echo $row['service_type']; ?></p>
                                                                        <p><strong>Date:</strong>
                                                                            <?php echo $booking_date; ?></p>
                                                                        <p><strong>Time:</strong> <?php echo $start_time; ?>
                                                                            to <?php echo $end_time; ?></p>
                                                                        <p><strong>Total Hours:</strong>
                                                                            <?php echo $row['total_hours']; ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-3">
                                                                    <div class="col-12">
                                                                        <h6>Address</h6>
                                                                        <p><?php echo $row['address']; ?></p>
                                                                    </div>
                                                                </div>
                                                                <?php if (!empty($row['special_requests'])): ?>
                                                                    <div class="row mt-3">
                                                                        <div class="col-12">
                                                                            <h6>Special Requests</h6>
                                                                            <p><?php echo $row['special_requests']; ?></p>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Update Status Modal -->
                                                <div class="modal fade" id="updateStatusModal<?php echo $row['id']; ?>"
                                                    tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Booking Status</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="update_booking_status.php" method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="booking_id"
                                                                        value="<?php echo $row['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Current Status</label>
                                                                        <input type="text" class="form-control"
                                                                            value="<?php echo ucfirst($row['status']); ?>"
                                                                            readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">New Status</label>
                                                                        <select name="new_status" class="form-select"
                                                                            required>
                                                                            <option value="pending">Pending</option>
                                                                            <option value="confirmed">Confirmed</option>
                                                                            <option value="assigned">Assigned</option>
                                                                            <option value="completed">Completed</option>
                                                                            <option value="cancelled">Cancelled</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Notes (Optional)</label>
                                                                        <textarea name="notes" class="form-control" rows="3"
                                                                            placeholder="Add notes about status change..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Update
                                                                        Status</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Assign Partner Modal (Single) -->
                                                <div class="modal fade"
                                                    id="assignPartnerModalSingle<?php echo $row['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Assign Partner</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="assign_partner.php" method="POST">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="booking_id"
                                                                        value="<?php echo $row['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Select Partner</label>
                                                                        <select name="partner_id" class="form-select"
                                                                            required>
                                                                            <option value="">Select a partner</option>
                                                                            <?php
                                                                            $partners_sql = "SELECT * FROM partners WHERE status = 'active'";
                                                                            $partners_result = mysqli_query($conn, $partners_sql);
                                                                            while ($partner = mysqli_fetch_assoc($partners_result)) {
                                                                                echo '<option value="' . $partner['partner_id'] . '">' . $partner['name'] . ' (' . $partner['email'] . ')</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Assign
                                                                        Partner</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php
                                                $counter++;
                                            }

                                            if ($counter == 1) {
                                                echo '<tr><td colspan="9" class="text-center py-4">No bookings found.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <!-- Bulk Assign Partner Modal -->
        <div class="modal fade" id="assignPartnerModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Assign Partner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="bulk_assign_partner.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Partner</label>
                                <select name="partner_id" class="form-select" required>
                                    <option value="">Select a partner</option>
                                    <?php
                                    $partners_sql = "SELECT * FROM partners WHERE status = 'active'";
                                    $partners_result = mysqli_query($conn, $partners_sql);
                                    while ($partner = mysqli_fetch_assoc($partners_result)) {
                                        echo '<option value="' . $partner['partner_id'] . '">' . $partner['name'] . ' (' . $partner['email'] . ')</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Bookings (Ctrl+Click for multiple)</label>
                                <select name="booking_ids[]" class="form-select" multiple size="5" required>
                                    <?php
                                    // Reset result pointer
                                    mysqli_data_seek($result, 0);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        if ($row['partner_id'] == NULL || $row['partner_id'] == '') {
                                            echo '<option value="' . $row['id'] . '">' . $row['booking_id'] . ' - ' . $row['customer_name'] . ' - ' . $row['booking_date'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Hold Ctrl to select multiple bookings</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign to Selected Bookings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <?php include "footer.php"; ?>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Export to Excel
        function exportToExcel() {
            // Get table data
            var table = document.querySelector('table');
            var html = table.outerHTML;

            // Create a Blob with the HTML content
            var blob = new Blob([html], { type: 'application/vnd.ms-excel' });

            // Create a link element
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'bookings_<?php echo date('Y-m-d'); ?>.xls';

            // Append link to body, click it, and remove it
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>

</html>
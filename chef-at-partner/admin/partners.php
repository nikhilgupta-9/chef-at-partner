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
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_verification = isset($_GET['verification']) ? $_GET['verification'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $partner_id = $_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'verify':
            $verify_sql = "UPDATE partners SET is_verified = 1, verified_by = 'admin', verified_at = NOW() WHERE id = '$partner_id'";
            mysqli_query($conn, $verify_sql);
            $_SESSION['message'] = "Partner verified successfully!";
            $_SESSION['message_type'] = "success";
            break;

        case 'unverify':
            $unverify_sql = "UPDATE partners SET is_verified = 0, verified_by = NULL, verified_at = NULL WHERE id = '$partner_id'";
            mysqli_query($conn, $unverify_sql);
            $_SESSION['message'] = "Partner verification removed!";
            $_SESSION['message_type'] = "warning";
            break;

        case 'activate':
            $activate_sql = "UPDATE partners p 
                           JOIN users u ON p.user_id = u.id 
                           SET u.status = 'active' 
                           WHERE p.id = '$partner_id'";
            mysqli_query($conn, $activate_sql);
            $_SESSION['message'] = "Partner activated successfully!";
            $_SESSION['message_type'] = "success";
            break;

        case 'deactivate':
            $deactivate_sql = "UPDATE partners p 
                             JOIN users u ON p.user_id = u.id 
                             SET u.status = 'inactive' 
                             WHERE p.id = '$partner_id'";
            mysqli_query($conn, $deactivate_sql);
            $_SESSION['message'] = "Partner deactivated!";
            $_SESSION['message_type'] = "warning";
            break;

        case 'delete':
            // First get user_id to delete from users table
            $get_user_sql = "SELECT user_id FROM partners WHERE id = '$partner_id'";
            $user_result = mysqli_query($conn, $get_user_sql);
            $user_data = mysqli_fetch_assoc($user_result);
            $user_id = $user_data['user_id'];

            // Delete from partners
            $delete_partner_sql = "DELETE FROM partners WHERE id = '$partner_id'";
            mysqli_query($conn, $delete_partner_sql);

            // Delete from users
            $delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
            mysqli_query($conn, $delete_user_sql);

            $_SESSION['message'] = "Partner deleted successfully!";
            $_SESSION['message_type'] = "danger";
            break;
    }

    header("Location: partners.php");
    exit();
}

// Build query
$sql = "SELECT p.*, 
               u.full_name, 
               u.email, 
               u.phone, 
               u.profile_image,
               u.status as user_status,
               u.created_at as user_created
        FROM partners p
        JOIN users u ON p.user_id = u.id
        WHERE 1=1";

// Add filters
if (!empty($filter_status) && $filter_status !== 'all') {
    $sql .= " AND u.status = '$filter_status'";
}

if (!empty($filter_type) && $filter_type !== 'all') {
    $sql .= " AND p.partner_type = '$filter_type'";
}

if (!empty($filter_verification) && $filter_verification !== 'all') {
    if ($filter_verification === 'verified') {
        $sql .= " AND p.is_verified = 1";
    } elseif ($filter_verification === 'pending') {
        $sql .= " AND p.is_verified = 0";
    }
}

if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE '%$search%' 
                   OR u.email LIKE '%$search%' 
                   OR u.phone LIKE '%$search%'
                   OR p.aadhar_number LIKE '%$search%')";
}

$sql .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_partners,
    SUM(CASE WHEN p.is_verified = 1 THEN 1 ELSE 0 END) as verified,
    SUM(CASE WHEN p.is_verified = 0 THEN 1 ELSE 0 END) as pending_verification,
    SUM(CASE WHEN u.status = 'active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN u.status = 'inactive' THEN 1 ELSE 0 END) as inactive,
    AVG(p.rating) as avg_rating,
    SUM(p.total_jobs) as total_jobs_completed,
    SUM(p.hourly_rate) as total_hourly_rate
    FROM partners p
    JOIN users u ON p.user_id = u.id";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

// Get partner types for filter
$types_sql = "SELECT DISTINCT partner_type FROM partners WHERE partner_type IS NOT NULL";
$types_result = mysqli_query($conn, $types_sql);
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Partners Management | Chef At Partner</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .verified-badge {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .pending-badge {
            background-color: #fff3cd;
            color: #856404;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 14px;
        }

        .partner-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .skills-tag {
            display: inline-block;
            background: #e9ecef;
            padding: 2px 6px;
            margin: 1px;
            border-radius: 12px;
            font-size: 11px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
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

        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .action-dropdown {
            min-width: 150px;
        }

        .table-actions {
            white-space: nowrap;
        }

        .document-icon {
            cursor: pointer;
            color: #007bff;
            font-size: 18px;
        }

        .table-avatar {
            display: flex;
            align-items: center;
        }

        .table-skills {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

                <!-- Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show"
                                role="alert">
                                <?php echo $_SESSION['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Partners Management</h4>
                            <div class="page-title-right">
                                <a href="export_partners.php" class="btn btn-secondary">
                                    <i class="fas fa-download me-2"></i>Export CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['total_partners'] ?? 0; ?></div>
                            <div class="stat-label">Total Partners</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['verified'] ?? 0; ?></div>
                            <div class="stat-label">Verified</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['pending_verification'] ?? 0; ?></div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?> <i
                                    class="fas fa-star text-warning"></i></div>
                            <div class="stat-label">Avg Rating</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['total_jobs_completed'] ?? 0; ?></div>
                            <div class="stat-label">Jobs Completed</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-number">₹<?php echo number_format($stats['total_hourly_rate'] ?? 0, 0); ?>
                            </div>
                            <div class="stat-label">Total Hourly Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="filter-card">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">User Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all">All Status</option>
                                        <option value="active" <?php echo ($filter_status == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($filter_status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Partner Type</label>
                                    <select name="type" class="form-select">
                                        <option value="all">All Types</option>
                                        <?php while ($type = mysqli_fetch_assoc($types_result)): ?>
                                            <option value="<?php echo $type['partner_type']; ?>" <?php echo ($filter_type == $type['partner_type']) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($type['partner_type']); ?>
                                            </option>
                                        <?php endwhile;
                                        mysqli_data_seek($types_result, 0); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Verification</label>
                                    <select name="verification" class="form-select">
                                        <option value="all">All</option>
                                        <option value="verified" <?php echo ($filter_verification == 'verified') ? 'selected' : ''; ?>>Verified</option>
                                        <option value="pending" <?php echo ($filter_verification == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by name, email, phone or Aadhar"
                                        value="<?php echo $search; ?>">
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="partners.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Partners Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Partner</th>
                                                <th>Contact</th>
                                                <th>Type</th>
                                                <th>Experience</th>
                                                <th>Hourly Rate</th>
                                                <th>Rating</th>
                                                <th>Jobs</th>
                                                <th>Documents</th>
                                                <th>Skills</th>
                                                <th>Status</th>
                                                <th>Verification</th>
                                                <th>Joined Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result && mysqli_num_rows($result) > 0) {
                                                $counter = 1;
                                                while ($partner = mysqli_fetch_assoc($result)) {
                                                    // Parse skills JSON
                                                    $skills = json_decode($partner['skills'], true);
                                                    if (!is_array($skills)) {
                                                        $skills = [];
                                                    }

                                                    // Calculate rating stars
                                                    $rating = $partner['rating'] ?? 0;
                                                    $full_stars = floor($rating);
                                                    $half_star = ($rating - $full_stars) >= 0.5;
                                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td>
                                                            <div class="table-avatar">
                                                                <?php if (!empty($partner['profile_image'])) { ?>
                                                                    <img src="<?php echo $partner['profile_image']; ?>"
                                                                        class="partner-avatar me-2"
                                                                        alt="<?php echo htmlspecialchars($partner['full_name']); ?>">
                                                                <?php } else { ?>
                                                                    <i class="fas fa-user me-2"></i>
                                                                <?php } ?>

                                                                <div>
                                                                    <strong><?php echo $partner['full_name']; ?></strong><br>
                                                                    <small class="text-muted">Aadhar:
                                                                        <?php echo $partner['aadhar_number']; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small><?php echo $partner['email']; ?></small><br>
                                                            <small><?php echo $partner['phone']; ?></small>
                                                        </td>
                                                        <td><?php echo ucfirst($partner['partner_type']); ?></td>
                                                        <td><?php echo $partner['experience_years']; ?> years</td>
                                                        <td>₹<?php echo number_format($partner['hourly_rate'], 2); ?></td>
                                                        <td>
                                                            <div class="rating-stars">
                                                                <?php for ($i = 0; $i < $full_stars; $i++): ?>
                                                                    <i class="fas fa-star"></i>
                                                                <?php endfor; ?>
                                                                <?php if ($half_star): ?>
                                                                    <i class="fas fa-star-half-alt"></i>
                                                                <?php endif; ?>
                                                                <?php for ($i = 0; $i < $empty_stars; $i++): ?>
                                                                    <i class="far fa-star"></i>
                                                                <?php endfor; ?>
                                                                <br>
                                                                <small>(<?php echo number_format($rating, 1); ?>)</small>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $partner['total_jobs']; ?></td>
                                                        <td>
                                                            <?php if (!empty($partner['aadhar_front'])): ?>
                                                                <i class="fas fa-id-card document-icon me-2"
                                                                    title="View Aadhar Front"
                                                                    onclick="window.open('<?php echo $partner['aadhar_front']; ?>')"></i>
                                                            <?php endif; ?>
                                                            <?php if (!empty($partner['aadhar_back'])): ?>
                                                                <i class="fas fa-id-card document-icon" title="View Aadhar Back"
                                                                    onclick="window.open('<?php echo $partner['aadhar_back']; ?>')"></i>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="table-skills"
                                                            title="<?php echo implode(', ', array_slice($skills, 0, 5)); ?>">
                                                            <?php if (!empty($skills)): ?>
                                                                <?php foreach (array_slice($skills, 0, 2) as $skill): ?>
                                                                    <span class="skills-tag"><?php echo $skill; ?></span>
                                                                <?php endforeach; ?>
                                                                <?php if (count($skills) > 2): ?>
                                                                    <span class="skills-tag">+<?php echo count($skills) - 2; ?></span>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($partner['user_status'] == 'active'): ?>
                                                                <span class="status-badge status-active">Active</span>
                                                            <?php else: ?>
                                                                <span class="status-badge status-inactive">Inactive</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($partner['is_verified']): ?>
                                                                <span class="verified-badge status-badge">
                                                                    <i class="fas fa-check-circle me-1"></i> Verified
                                                                </span>
                                                                <?php if (!empty($partner['verified_at'])): ?>
                                                                    <br>
                                                                    <small
                                                                        class="text-muted"><?php echo date('d M Y', strtotime($partner['verified_at'])); ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="pending-badge status-badge">
                                                                    <i class="fas fa-clock me-1"></i> Pending
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo date('d M Y', strtotime($partner['user_created'])); ?>
                                                        </td>
                                                        <td class="table-actions">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-light dropdown-toggle"
                                                                    type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu action-dropdown">
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#viewPartnerModal<?php echo $partner['id']; ?>">
                                                                            <i class="fas fa-eye me-2"></i>View Details
                                                                        </a>
                                                                    </li>
                                                                    <?php if (!$partner['is_verified']): ?>
                                                                        <li>
                                                                            <a class="dropdown-item text-success"
                                                                                href="partners.php?action=verify&id=<?php echo $partner['id']; ?>"
                                                                                onclick="return confirm('Verify this partner?')">
                                                                                <i class="fas fa-check-circle me-2"></i>Verify
                                                                                Partner
                                                                            </a>
                                                                        </li>
                                                                    <?php else: ?>
                                                                        <li>
                                                                            <a class="dropdown-item text-warning"
                                                                                href="partners.php?action=unverify&id=<?php echo $partner['id']; ?>"
                                                                                onclick="return confirm('Remove verification?')">
                                                                                <i class="fas fa-times-circle me-2"></i>Unverify
                                                                            </a>
                                                                        </li>
                                                                    <?php endif; ?>

                                                                    <?php if ($partner['user_status'] == 'active'): ?>
                                                                        <li>
                                                                            <a class="dropdown-item text-warning"
                                                                                href="partners.php?action=deactivate&id=<?php echo $partner['id']; ?>"
                                                                                onclick="return confirm('Deactivate this partner?')">
                                                                                <i class="fas fa-user-slash me-2"></i>Deactivate
                                                                            </a>
                                                                        </li>
                                                                    <?php else: ?>
                                                                        <li>
                                                                            <a class="dropdown-item text-success"
                                                                                href="partners.php?action=activate&id=<?php echo $partner['id']; ?>"
                                                                                onclick="return confirm('Activate this partner?')">
                                                                                <i class="fas fa-user-check me-2"></i>Activate
                                                                            </a>
                                                                        </li>
                                                                    <?php endif; ?>

                                                                    <li>
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#editPartnerModal<?php echo $partner['id']; ?>">
                                                                            <i class="fas fa-edit me-2"></i>Edit
                                                                        </a>
                                                                    </li>

                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item text-danger"
                                                                            href="partners.php?action=delete&id=<?php echo $partner['id']; ?>"
                                                                            onclick="return confirm('Are you sure you want to delete this partner? This action cannot be undone.')">
                                                                            <i class="fas fa-trash me-2"></i>Delete
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <!-- View Partner Modal -->
                                                    <div class="modal " id="viewPartnerModal<?php echo $partner['id']; ?>"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Partner Details -
                                                                        <?php echo $partner['full_name']; ?>
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-4 text-center">
                                                                            <img src="<?php echo !empty($partner['profile_image']) ? $partner['profile_image'] : 'assets/img/default-avatar.png'; ?>"
                                                                                class="img-fluid rounded-circle mb-3"
                                                                                style="width: 150px; height: 150px; object-fit: cover;"
                                                                                alt="<?php echo $partner['full_name']; ?>">
                                                                            <h5><?php echo $partner['full_name']; ?></h5>
                                                                            <p class="text-muted">
                                                                                <?php echo ucfirst($partner['partner_type']); ?>
                                                                                Partner
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <p><strong>Email:</strong><br><?php echo $partner['email']; ?>
                                                                                    </p>
                                                                                    <p><strong>Phone:</strong><br><?php echo $partner['phone']; ?>
                                                                                    </p>
                                                                                    <p><strong>Aadhar
                                                                                            Number:</strong><br><?php echo $partner['aadhar_number']; ?>
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p><strong>Experience:</strong><br><?php echo $partner['experience_years']; ?>
                                                                                        years</p>
                                                                                    <p><strong>Hourly
                                                                                            Rate:</strong><br>₹<?php echo number_format($partner['hourly_rate'], 2); ?>
                                                                                    </p>
                                                                                    <p><strong>Rating:</strong><br>
                                                                                        <span class="rating-stars">
                                                                                            <?php echo $rating; ?>
                                                                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                                                                <?php if ($i < floor($rating)): ?>
                                                                                                    <i class="fas fa-star"></i>
                                                                                                <?php elseif ($i == floor($rating) && ($rating - floor($rating)) >= 0.5): ?>
                                                                                                    <i class="fas fa-star-half-alt"></i>
                                                                                                <?php else: ?>
                                                                                                    <i class="far fa-star"></i>
                                                                                                <?php endif; ?>
                                                                                            <?php endfor; ?>
                                                                                        </span>
                                                                                    </p>
                                                                                </div>
                                                                            </div>

                                                                            <?php if (!empty($skills)): ?>
                                                                                <div class="row mt-3">
                                                                                    <div class="col-12">
                                                                                        <strong>Skills:</strong><br>
                                                                                        <?php foreach ($skills as $skill): ?>
                                                                                            <span
                                                                                                class="badge bg-secondary me-1 mb-1"><?php echo $skill; ?></span>
                                                                                        <?php endforeach; ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>

                                                                            <!-- Document Preview -->
                                                                            <?php if (!empty($partner['aadhar_front']) || !empty($partner['aadhar_back'])): ?>
                                                                                <div class="row mt-3">
                                                                                    <div class="col-12">
                                                                                        <strong>Documents:</strong><br>
                                                                                        <div class="d-flex">
                                                                                            <?php if (!empty($partner['aadhar_front'])): ?>
                                                                                                <div class="me-3">
                                                                                                    <p class="mb-1"><small>Aadhar
                                                                                                            Front</small></p>
                                                                                                    <img src="<?php echo $partner['aadhar_front']; ?>"
                                                                                                        class="img-thumbnail"
                                                                                                        style="max-width: 200px; cursor: pointer;"
                                                                                                        onclick="window.open('<?php echo $partner['aadhar_front']; ?>')">
                                                                                                </div>
                                                                                            <?php endif; ?>

                                                                                            <?php if (!empty($partner['aadhar_back'])): ?>
                                                                                                <div>
                                                                                                    <p class="mb-1"><small>Aadhar
                                                                                                            Back</small></p>
                                                                                                    <img src="<?php echo $partner['aadhar_back']; ?>"
                                                                                                        class="img-thumbnail"
                                                                                                        style="max-width: 200px; cursor: pointer;"
                                                                                                        onclick="window.open('<?php echo $partner['aadhar_back']; ?>')">
                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>

                                                                            <!-- Verification Info -->
                                                                            <?php if ($partner['is_verified']): ?>
                                                                                <div class="row mt-3">
                                                                                    <div class="col-12">
                                                                                        <div class="alert alert-success">
                                                                                            <strong><i
                                                                                                    class="fas fa-check-circle me-1"></i>
                                                                                                Verified</strong><br>
                                                                                            <small>
                                                                                                Verified by:
                                                                                                <?php echo $partner['verified_by'] ?? 'Admin'; ?><br>
                                                                                                Verified on:
                                                                                                <?php echo date('d M Y H:i', strtotime($partner['verified_at'])); ?>
                                                                                            </small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Edit Partner Modal -->
                                                    <div class="modal fade" id="editPartnerModal<?php echo $partner['id']; ?>"
                                                        tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Partner</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form action="update_partner.php" method="POST">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="partner_id"
                                                                            value="<?php echo $partner['id']; ?>">

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Hourly Rate (₹)</label>
                                                                            <input type="number" name="hourly_rate"
                                                                                class="form-control"
                                                                                value="<?php echo $partner['hourly_rate']; ?>"
                                                                                step="0.01" min="0" required>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Experience (Years)</label>
                                                                            <input type="number" name="experience_years"
                                                                                class="form-control"
                                                                                value="<?php echo $partner['experience_years']; ?>"
                                                                                min="0" max="50" required>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select name="status" class="form-select">
                                                                                <option value="active" <?php echo ($partner['user_status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                                                <option value="inactive" <?php echo ($partner['user_status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Verification
                                                                                Status</label>
                                                                            <select name="is_verified" class="form-select">
                                                                                <option value="0" <?php echo (!$partner['is_verified']) ? 'selected' : ''; ?>>Not Verified</option>
                                                                                <option value="1" <?php echo ($partner['is_verified']) ? 'selected' : ''; ?>>Verified</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="14" class="text-center py-4">No partners found.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <small class="text-muted">Showing <?php echo mysqli_num_rows($result); ?>
                                                partners</small>
                                        </div>
                                        <div>
                                            <nav>
                                                <ul class="pagination pagination-sm">
                                                    <li class="page-item disabled"><a class="page-link"
                                                            href="#">Previous</a></li>
                                                    <li class="page-item active"><a class="page-link" href="#">1</a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
        <?php include "footer.php"; ?>
    </section>

    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Add hover effect for skills column
        document.querySelectorAll('.table-skills').forEach(function (cell) {
            cell.addEventListener('mouseenter', function () {
                if (this.scrollWidth > this.clientWidth) {
                    this.setAttribute('title', this.textContent);
                }
            });
        });
    </script>
</body>

</html>
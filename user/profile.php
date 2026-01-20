<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Get user details
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Handle profile image upload
    $profile_image = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . '_' . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        // Check file size (max 2MB)
        if ($_FILES["profile_image"]["size"] > 2000000) {
            $error_msg = "File is too large. Maximum size is 2MB.";
        } else {
            // Allow certain file formats
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    // Delete old profile image if exists
                    if (!empty($user['profile_image']) && file_exists($target_dir . $user['profile_image'])) {
                        unlink($target_dir . $user['profile_image']);
                    }
                    $profile_image = $file_name;
                } else {
                    $error_msg = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error_msg = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }
    }

    if (empty($error_msg)) {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, address = ?, profile_image = ?
            WHERE id = ?
        ");
        $update_stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $profile_image, $user_id);

        if ($update_stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            $success_msg = "Profile updated successfully!";

            // Refresh user data
            $user_stmt->execute();
            $user = $user_stmt->get_result()->fetch_assoc();
        } else {
            $error_msg = "Error updating profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <style>
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #c7a07d 0%, #a07a5d 100%);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
        }

        .profile-image-container {
            position: relative;
            display: inline-block;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }

        .change-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            color: #c7a07d;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .change-photo-btn:hover {
            background: #f8f9fa;
            transform: scale(1.1);
        }

        .profile-body {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c7a07d;
            box-shadow: 0 0 0 0.2rem rgba(199, 160, 125, 0.25);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
            background: rgba(199, 160, 125, 0.1);
            color: #c7a07d;
        }

        .tab-nav {
            display: flex;
            border-bottom: 2px solid #f8f9fa;
            margin-bottom: 30px;
        }

        .tab-link {
            padding: 12px 25px;
            text-decoration: none;
            color: #6c757d;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-link:hover {
            color: #c7a07d;
        }

        .tab-link.active {
            color: #c7a07d;
            border-bottom-color: #c7a07d;
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
                    <?php include 'includes/sidebar.php'; ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <!-- Success/Error Messages -->
                <?php if ($success_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h1 class="h3 mb-2">My Profile</h1>
                    <p class="text-muted">Manage your personal information and preferences</p>
                </div>

                <!-- Profile Stats -->
                <div class="row mb-5">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h3 class="stat-number">
                                <?php
                                $total_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ?");
                                $total_stmt->bind_param("i", $user_id);
                                $total_stmt->execute();
                                echo $total_stmt->get_result()->fetch_row()[0];
                                ?>
                            </h3>
                            <p class="stat-label">Total Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="stat-number">
                                <?php
                                $rating_stmt = $conn->prepare("
                                    SELECT AVG(r.rating) 
                                    FROM reviews r
                                    JOIN bookings b ON r.booking_id = b.id
                                    WHERE b.customer_id = ?
                                ");
                                $rating_stmt->bind_param("i", $user_id);
                                $rating_stmt->execute();
                                $avg_rating = $rating_stmt->get_result()->fetch_row()[0];
                                echo $avg_rating ? round($avg_rating, 1) : '0.0';
                                ?>
                            </h3>
                            <p class="stat-label">Avg Rating</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <h3 class="stat-number">
                                <?php
                                $partners_stmt = $conn->prepare("
                                    SELECT COUNT(DISTINCT partner_id) 
                                    FROM bookings 
                                    WHERE customer_id = ?
                                ");
                                $partners_stmt->bind_param("i", $user_id);
                                $partners_stmt->execute();
                                echo $partners_stmt->get_result()->fetch_row()[0];
                                ?>
                            </h3>
                            <p class="stat-label">Partners Used</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <h3 class="stat-number">Member</h3>
                            <p class="stat-label">Since
                                <?php echo date('M Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="profile-header">
                            <div class="profile-image-container">
                                <img src="<?php echo !empty($user['profile_image']) ? '../uploads/' . $user['profile_image'] : 'https://via.placeholder.com/150'; ?>"
                                    class="profile-img" id="profileImagePreview" alt="Profile Image">
                                <button type="button" class="change-photo-btn"
                                    onclick="document.getElementById('profileImageInput').click()">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" name="profile_image" id="profileImageInput" style="display: none;"
                                    accept="image/*" onchange="previewImage(this)">
                            </div>
                            <h3 class="mt-3">
                                <?php echo $user['full_name']; ?>
                            </h3>
                            <p class="mb-0 opacity-75">Customer</p>
                        </div>

                        <div class="profile-body">
                            <!-- Tab Navigation -->
                            <div class="tab-nav">
                                <a href="#personal-info" class="tab-link active" data-bs-toggle="tab">Personal Info</a>
                                <a href="#security" class="tab-link" data-bs-toggle="tab">Security</a>
                                <a href="#preferences" class="tab-link" data-bs-toggle="tab">Preferences</a>
                            </div>

                            <div class="tab-content">
                                <!-- Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-info">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="full_name" class="form-control"
                                                value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email" class="form-control"
                                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" name="phone" class="form-control"
                                                value="<?php echo htmlspecialchars($user['phone']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" name="dob" class="form-control"
                                                value="<?php echo $user['dob']; ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" class="form-control"
                                                rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Tab -->
                                <div class="tab-pane fade" id="security">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" name="current_password" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="new_password" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" name="confirm_password" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Leave password fields blank if you don't want to change it.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preferences Tab -->
                                <div class="tab-pane fade" id="preferences">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Preferred Cuisine</label>
                                            <select name="preferred_cuisine" class="form-select">
                                                <option value="">Select Cuisine</option>
                                                <option value="indian" <?php echo ($user['preferred_cuisine'] ?? '') == 'indian' ? 'selected' : ''; ?>>Indian</option>
                                                <option value="italian" <?php echo ($user['preferred_cuisine'] ?? '') == 'italian' ? 'selected' : ''; ?>>Italian</option>
                                                <option value="chinese" <?php echo ($user['preferred_cuisine'] ?? '') == 'chinese' ? 'selected' : ''; ?>>Chinese</option>
                                                <option value="mexican" <?php echo ($user['preferred_cuisine'] ?? '') == 'mexican' ? 'selected' : ''; ?>>Mexican</option>
                                                <option value="continental" <?php echo ($user['preferred_cuisine'] ?? '') == 'continental' ? 'selected' : ''; ?>>Continental</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Notification Preferences</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="email_notifications" id="emailNotifications" <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="emailNotifications">
                                                    Email Notifications
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sms_notifications"
                                                    id="smsNotifications" <?php echo ($user['sms_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="smsNotifications">
                                                    SMS Notifications
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-theme">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php' ?>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('profileImagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Tab functionality
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
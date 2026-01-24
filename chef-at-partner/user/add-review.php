<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 0;

// Validate booking and check if review is allowed
if ($booking_id > 0) {
    $booking_stmt = $conn->prepare("
        SELECT b.*, p.id as partner_id, u.full_name as partner_name, u.profile_image as partner_image
        FROM bookings b
        JOIN partners p ON b.partner_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE b.id = ? AND b.customer_id = ? AND b.status = 'completed'
        AND NOT EXISTS (SELECT 1 FROM reviews WHERE booking_id = b.id)
    ");
    $booking_stmt->bind_param("ii", $booking_id, $customer_id);
    $booking_stmt->execute();
    $booking = $booking_stmt->get_result()->fetch_assoc();

    if (!$booking) {
        header("Location: bookings.php?error=invalid_booking");
        exit();
    }

    $partner_id = $booking['partner_id'];
} elseif ($partner_id > 0) {
    // Direct review (without booking)
    $partner_stmt = $conn->prepare("
        SELECT p.*, u.full_name, u.profile_image
        FROM partners p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ? AND p.is_verified = 1
    ");
    $partner_stmt->bind_param("i", $partner_id);
    $partner_stmt->execute();
    $partner = $partner_stmt->get_result()->fetch_assoc();

    if (!$partner) {
        header("Location: book-partner.php?error=invalid_partner");
        exit();
    }
} else {
    header("Location: bookings.php");
    exit();
}

// Handle review submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = floatval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $food_quality = intval($_POST['food_quality']);
    $professionalism = intval($_POST['professionalism']);
    $punctuality = intval($_POST['punctuality']);
    $communication = intval($_POST['communication']);
    $value_for_money = intval($_POST['value_for_money']);

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $error_msg = "Rating must be between 1 and 5 stars.";
    } elseif (strlen($comment) < 10) {
        $error_msg = "Please write a more detailed review (minimum 10 characters).";
    } else {
        // Handle image uploads
        $uploaded_images = [];
        if (!empty($_FILES['review_images']['name'][0])) {
            foreach ($_FILES['review_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['review_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . basename($_FILES['review_images']['name'][$key]);
                    $target_file = "../uploads/reviews/" . $file_name;

                    // Check file type and size
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = mime_content_type($tmp_name);
                    $file_size = $_FILES['review_images']['size'][$key];

                    if (in_array($file_type, $allowed_types) && $file_size <= 5 * 1024 * 1024) {
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $uploaded_images[] = $file_name;
                        }
                    }
                }
            }
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert review
            $review_stmt = $conn->prepare("
                INSERT INTO reviews (
                    booking_id, partner_id, customer_id, rating, comment,
                    food_quality, professionalism, punctuality, communication, value_for_money,
                    images, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', NOW())
            ");

            $images_json = !empty($uploaded_images) ? json_encode($uploaded_images) : NULL;
            $review_stmt->bind_param(
                "iiisddddddss",
                $booking_id,
                $partner_id,
                $customer_id,
                $rating,
                $comment,
                $food_quality,
                $professionalism,
                $punctuality,
                $communication,
                $value_for_money,
                $images_json
            );

            if (!$review_stmt->execute()) {
                throw new Exception("Failed to save review.");
            }

            $review_id = $conn->insert_id;

            // Insert individual review images
            if (!empty($uploaded_images)) {
                $image_stmt = $conn->prepare("
                    INSERT INTO review_images (review_id, image_url, sort_order, created_at)
                    VALUES (?, ?, ?, NOW())
                ");

                foreach ($uploaded_images as $index => $image_url) {
                    $image_stmt->bind_param("isi", $review_id, $image_url, $index);
                    $image_stmt->execute();
                }
            }

            // Update partner's average rating
            $update_partner_stmt = $conn->prepare("
                UPDATE partners 
                SET 
                    avg_rating = (
                        SELECT AVG(rating) 
                        FROM reviews 
                        WHERE partner_id = ? AND status = 'approved'
                    ),
                    total_reviews = (
                        SELECT COUNT(*) 
                        FROM reviews 
                        WHERE partner_id = ? AND status = 'approved'
                    )
                WHERE id = ?
            ");
            $update_partner_stmt->bind_param("iii", $partner_id, $partner_id, $partner_id);
            $update_partner_stmt->execute();

            // Commit transaction
            $conn->commit();

            $success_msg = "Thank you for your review! Your feedback helps other customers.";

            // Redirect after 3 seconds
            header("refresh:3;url=reviews.php");

        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Error submitting review: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/star-rating-svg@3.5.0/src/css/star-rating-svg.css">
    <style>
        .review-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .partner-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .partner-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .partner-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #c7a07d;
        }

        .booking-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .rating-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .overall-rating {
            text-align: center;
            margin-bottom: 30px;
        }

        .rating-number {
            font-size: 48px;
            font-weight: 700;
            color: #c7a07d;
            line-height: 1;
        }

        .rating-stars {
            font-size: 32px;
            color: #ffc107;
            margin: 10px 0;
        }

        .rating-label {
            color: #6c757d;
            font-size: 14px;
        }

        .aspect-rating {
            margin-bottom: 20px;
        }

        .aspect-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .aspect-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .aspect-rating-stars {
            color: #ffc107;
            font-size: 18px;
            cursor: pointer;
        }

        .aspect-rating-stars i {
            margin: 0 2px;
            transition: all 0.2s ease;
        }

        .aspect-rating-stars i:hover {
            transform: scale(1.2);
        }

        .comment-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .comment-box {
            min-height: 150px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .comment-box:focus {
            border-color: #c7a07d;
            box-shadow: 0 0 0 0.2rem rgba(199, 160, 125, 0.25);
        }

        .image-upload-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #c7a07d;
            background: rgba(199, 160, 125, 0.05);
        }

        .upload-area.dragover {
            border-color: #c7a07d;
            background: rgba(199, 160, 125, 0.1);
        }

        .upload-icon {
            font-size: 48px;
            color: #c7a07d;
            margin-bottom: 15px;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .submit-section {
            text-align: center;
            padding: 30px;
        }

        .tip-box {
            background: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .tip-box i {
            color: #3498db;
            margin-right: 10px;
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
                <div class="review-container">
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

                    <!-- Partner Header -->
                    <div class="partner-header">
                        <div class="partner-info">
                            <img src="<?php echo !empty($booking['partner_image']) ? '../uploads/' . $booking['partner_image'] : 'https://via.placeholder.com/80'; ?>"
                                class="partner-avatar" alt="<?php echo $booking['partner_name']; ?>">
                            <div>
                                <h1 class="h4 mb-1">Review Your Experience</h1>
                                <p class="text-muted mb-2">Share your feedback about</p>
                                <h3 class="h5 mb-0">
                                    <?php echo $booking['partner_name']; ?>
                                </h3>
                                <p class="text-muted mb-0">
                                    <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?> •
                                    <?php echo $booking['number_of_guests']; ?> guests
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="booking-details">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Booking ID:</strong> #
                                    <?php echo $booking['booking_id']; ?>
                                </p>
                                <p class="mb-1"><strong>Date:</strong>
                                    <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?>
                                </p>
                                <p class="mb-1"><strong>Time:</strong>
                                    <?php echo $booking['start_time']; ?> -
                                    <?php echo $booking['end_time']; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Service Type:</strong>
                                    <?php echo ucfirst($booking['partner_type']); ?>
                                </p>
                                <p class="mb-1"><strong>Number of Guests:</strong>
                                    <?php echo $booking['number_of_guests']; ?>
                                </p>
                                <p class="mb-0"><strong>Amount Paid:</strong> ₹
                                    <?php echo $booking['total_amount']; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" enctype="multipart/form-data" id="reviewForm">
                        <!-- Overall Rating -->
                        <div class="rating-section">
                            <h3 class="h5 mb-4">Overall Rating</h3>
                            <div class="overall-rating">
                                <div class="rating-number" id="overallRating">0.0</div>
                                <div class="rating-stars" id="overallStars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="far fa-star" data-rating="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="rating-label">Tap to rate</p>
                                <input type="hidden" name="rating" id="ratingInput" value="0" required>
                            </div>

                            <!-- Aspect Ratings -->
                            <div class="aspect-ratings">
                                <h4 class="h6 mb-3">Rate specific aspects (optional)</h4>

                                <div class="aspect-rating">
                                    <div class="aspect-label">
                                        <span class="aspect-name">Food Quality</span>
                                        <span class="aspect-value" id="foodQualityValue">-</span>
                                    </div>
                                    <div class="aspect-rating-stars" id="foodQualityStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star" data-aspect="food_quality"
                                                data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="food_quality" id="foodQualityInput" value="0">
                                </div>

                                <div class="aspect-rating">
                                    <div class="aspect-label">
                                        <span class="aspect-name">Professionalism</span>
                                        <span class="aspect-value" id="professionalismValue">-</span>
                                    </div>
                                    <div class="aspect-rating-stars" id="professionalismStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star" data-aspect="professionalism"
                                                data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="professionalism" id="professionalismInput" value="0">
                                </div>

                                <div class="aspect-rating">
                                    <div class="aspect-label">
                                        <span class="aspect-name">Punctuality</span>
                                        <span class="aspect-value" id="punctualityValue">-</span>
                                    </div>
                                    <div class="aspect-rating-stars" id="punctualityStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star" data-aspect="punctuality"
                                                data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="punctuality" id="punctualityInput" value="0">
                                </div>

                                <div class="aspect-rating">
                                    <div class="aspect-label">
                                        <span class="aspect-name">Communication</span>
                                        <span class="aspect-value" id="communicationValue">-</span>
                                    </div>
                                    <div class="aspect-rating-stars" id="communicationStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star" data-aspect="communication"
                                                data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="communication" id="communicationInput" value="0">
                                </div>

                                <div class="aspect-rating">
                                    <div class="aspect-label">
                                        <span class="aspect-name">Value for Money</span>
                                        <span class="aspect-value" id="valueForMoneyValue">-</span>
                                    </div>
                                    <div class="aspect-rating-stars" id="valueForMoneyStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star" data-aspect="value_for_money"
                                                data-rating="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="value_for_money" id="valueForMoneyInput" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-section">
                            <h3 class="h5 mb-4">Share Your Experience</h3>
                            <div class="tip-box">
                                <i class="fas fa-lightbulb"></i>
                                <strong>Writing a great review:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Describe your overall experience</li>
                                    <li>Mention what you liked most</li>
                                    <li>Share if anything could be improved</li>
                                    <li>Be specific and honest</li>
                                </ul>
                            </div>
                            <textarea name="comment" id="comment" class="comment-box"
                                placeholder="How was your experience? What did you like? What could be improved? (Minimum 10 characters)"
                                required></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted" id="charCount">0 characters</small>
                                <small class="text-muted">Minimum 10 characters</small>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="image-upload-section">
                            <h3 class="h5 mb-4">Add Photos (Optional)</h3>
                            <p class="text-muted mb-4">Upload photos of the food, setup, or your experience. Maximum 5
                                images, 5MB each.</p>

                            <div class="upload-area" id="uploadArea">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h5>Drag & Drop Images Here</h5>
                                <p class="text-muted mb-3">or click to browse</p>
                                <input type="file" name="review_images[]" id="reviewImages" class="d-none" multiple
                                    accept="image/*">
                                <button type="button" class="btn btn-outline-theme"
                                    onclick="document.getElementById('reviewImages').click()">
                                    <i class="fas fa-images me-2"></i> Choose Images
                                </button>
                            </div>

                            <div class="image-preview" id="imagePreview"></div>
                        </div>

                        <!-- Submit Section -->
                        <div class="submit-section">
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I confirm that this review is based on my genuine experience and complies with the
                                    <a href="#" class="text-decoration-none">Community Guidelines</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-theme btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i> Submit Review
                            </button>

                            <a href="bookings.php" class="btn btn-outline-secondary ms-3">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php' ?>

    <script>
        // Overall Rating
        const overallStars = document.querySelectorAll('#overallStars i');
        const ratingInput = document.getElementById('ratingInput');
        const overallRating = document.getElementById('overallRating');

        overallStars.forEach(star => {
            star.addEventListener('mouseover', function () {
                const rating = this.dataset.rating;
                highlightStars(overallStars, rating, 'overall');
            });

            star.addEventListener('click', function () {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                overallRating.textContent = rating + '.0';
                highlightStars(overallStars, rating, 'overall');
                updateStars(overallStars, rating);
            });
        });

        // Aspect Ratings
        const aspects = ['food_quality', 'professionalism', 'punctuality', 'communication', 'value_for_money'];

        aspects.forEach(aspect => {
            const stars = document.querySelectorAll(`#${aspect}Stars i`);
            const input = document.getElementById(`${aspect}Input`);
            const valueSpan = document.getElementById(`${aspect}Value`);

            stars.forEach(star => {
                star.addEventListener('mouseover', function () {
                    const rating = this.dataset.rating;
                    highlightStars(stars, rating, aspect);
                });

                star.addEventListener('mouseout', function () {
                    const currentRating = input.value;
                    highlightStars(stars, currentRating, aspect);
                });

                star.addEventListener('click', function () {
                    const rating = this.dataset.rating;
                    input.value = rating;
                    valueSpan.textContent = rating + '/5';
                    updateStars(stars, rating);
                });
            });
        });

        // Star highlighting functions
        function highlightStars(starElements, rating, type) {
            starElements.forEach(star => {
                if (star.dataset.rating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');

                    // Color coding for overall rating
                    if (type === 'overall') {
                        if (rating >= 4) star.style.color = '#28a745';
                        else if (rating >= 3) star.style.color = '#ffc107';
                        else star.style.color = '#dc3545';
                    } else {
                        star.style.color = '#ffc107';
                    }
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                    star.style.color = '#dee2e6';
                }
            });
        }

        function updateStars(starElements, rating) {
            starElements.forEach(star => {
                if (star.dataset.rating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }

        // Character count for comment
        const commentTextarea = document.getElementById('comment');
        const charCount = document.getElementById('charCount');

        commentTextarea.addEventListener('input', function () {
            const length = this.value.length;
            charCount.textContent = length + ' characters';

            if (length < 10) {
                charCount.style.color = '#dc3545';
            } else if (length < 50) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#28a745';
            }
        });

        // Image upload and preview
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('reviewImages');
        const imagePreview = document.getElementById('imagePreview');
        const maxFiles = 5;
        let uploadedFiles = [];

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('dragover');
        }

        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        fileInput.addEventListener('change', function () {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            const remainingSlots = maxFiles - uploadedFiles.length;
            const filesToAdd = Array.from(files).slice(0, remainingSlots);

            filesToAdd.forEach(file => {
                if (validateImage(file)) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        addImagePreview(file.name, e.target.result);
                    };
                    reader.readAsDataURL(file);
                    uploadedFiles.push(file);
                }
            });

            // Update file input
            const dataTransfer = new DataTransfer();
            uploadedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        function validateImage(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!validTypes.includes(file.type)) {
                alert('Please upload only images (JPEG, PNG, GIF, WebP)');
                return false;
            }

            if (file.size > maxSize) {
                alert('Image size should be less than 5MB');
                return false;
            }

            if (uploadedFiles.length >= maxFiles) {
                alert(`Maximum ${maxFiles} images allowed`);
                return false;
            }

            return true;
        }

        function addImagePreview(fileName, imageUrl) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';

            previewItem.innerHTML = `
                <img src="${imageUrl}" class="preview-image" alt="${fileName}">
                <div class="remove-image" onclick="removeImage(this, '${fileName}')">
                    <i class="fas fa-times"></i>
                </div>
            `;

            imagePreview.appendChild(previewItem);
        }

        function removeImage(element, fileName) {
            const previewItem = element.parentElement;
            previewItem.remove();

            // Remove from uploadedFiles array
            uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);

            // Update file input
            const dataTransfer = new DataTransfer();
            uploadedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        // Form validation
        document.getElementById('reviewForm').addEventListener('submit', function (e) {
            const rating = parseFloat(ratingInput.value);
            const comment = commentTextarea.value.trim();
            const agreeTerms = document.getElementById('agreeTerms').checked;

            if (rating < 1 || rating > 5) {
                e.preventDefault();
                alert('Please provide an overall rating');
                return;
            }

            if (comment.length < 10) {
                e.preventDefault();
                alert('Please write a more detailed review (minimum 10 characters)');
                return;
            }

            if (!agreeTerms) {
                e.preventDefault();
                alert('Please agree to the terms and community guidelines');
                return;
            }
        });
    </script>
</body>

</html>
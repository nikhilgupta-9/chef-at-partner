<?php
include_once('db-conn.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$message_type = '';

// Handle Service CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add_service') {
        // Add new service
        $category_id = (int) $_POST['category_id'];
        $service_name = trim($_POST['service_name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $service_name)));
        $description = trim($_POST['description']);
        $short_description = trim($_POST['short_description']);
        $base_price = (float) $_POST['base_price'];
        $hourly_rate = isset($_POST['hourly_rate']) ? 1 : 0;
        $min_hours = (int) $_POST['min_hours'];
        $max_hours = (int) $_POST['max_hours'];
        $is_popular = isset($_POST['is_popular']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'];
        $meta_title = trim($_POST['meta_title']);
        $meta_description = trim($_POST['meta_description']);
        $meta_keywords = trim($_POST['meta_keywords']);

        // Handle image upload
        $cover_image = '';
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            $upload_dir = '../uploads/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $file_name = 'service_' . time() . '_' . uniqid() . '.' . $file_ext;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
                    $cover_image = 'uploads/services/' . $file_name;
                }
            }
        }

        // Handle gallery images
        $gallery_images = [];
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $file_ext = strtolower(pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION));
                    if (in_array($file_ext, $allowed_ext)) {
                        $file_name = 'gallery_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $target_path = $upload_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $target_path)) {
                            $gallery_images[] = 'uploads/services/' . $file_name;
                        }
                    }
                }
            }
        }
        $gallery_json = !empty($gallery_images) ? json_encode($gallery_images) : NULL;

        // Insert service
        $stmt = $conn->prepare("INSERT INTO services (
            category_id, service_name, slug, description, short_description, 
            base_price, hourly_rate, min_hours, max_hours, is_popular, 
            is_featured, cover_image, gallery_images, status, meta_title, 
            meta_description, meta_keywords
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "issssdiiiiissssss",
            $category_id,
            $service_name,
            $slug,
            $description,
            $short_description,
            $base_price,
            $hourly_rate,
            $min_hours,
            $max_hours,
            $is_popular,
            $is_featured,
            $cover_image,
            $gallery_json,
            $status,
            $meta_title,
            $meta_description,
            $meta_keywords
        );

        if ($stmt->execute()) {
            $service_id = $conn->insert_id;

            // Handle features
            if (!empty($_POST['feature_name'])) {
                foreach ($_POST['feature_name'] as $feature) {
                    $feature = trim($feature);
                    if (!empty($feature)) {
                        $feature_stmt = $conn->prepare("INSERT INTO service_features (service_id, feature_name) VALUES (?, ?)");
                        $feature_stmt->bind_param("is", $service_id, $feature);
                        $feature_stmt->execute();
                    }
                }
            }

            // Handle FAQs
            if (!empty($_POST['faq_question'])) {
                for ($i = 0; $i < count($_POST['faq_question']); $i++) {
                    $question = trim($_POST['faq_question'][$i]);
                    $answer = trim($_POST['faq_answer'][$i]);

                    if (!empty($question) && !empty($answer)) {
                        $faq_stmt = $conn->prepare("INSERT INTO service_faqs (service_id, question, answer) VALUES (?, ?, ?)");
                        $faq_stmt->bind_param("iss", $service_id, $question, $answer);
                        $faq_stmt->execute();
                    }
                }
            }

            $message = "Service added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding service: " . $stmt->error;
            $message_type = "danger";
        }
    } elseif ($action == 'update_service') {
        // Update existing service
        $service_id = (int) $_POST['service_id'];
        $category_id = (int) $_POST['category_id'];
        $service_name = trim($_POST['service_name']);
        $description = trim($_POST['description']);
        $short_description = trim($_POST['short_description']);
        $base_price = (float) $_POST['base_price'];
        $hourly_rate = isset($_POST['hourly_rate']) ? 1 : 0;
        $min_hours = (int) $_POST['min_hours'];
        $max_hours = (int) $_POST['max_hours'];
        $is_popular = isset($_POST['is_popular']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'];
        $meta_title = trim($_POST['meta_title']);
        $meta_description = trim($_POST['meta_description']);
        $meta_keywords = trim($_POST['meta_keywords']);

        // Get current service data
        $current_stmt = $conn->prepare("SELECT cover_image FROM services WHERE id = ?");
        $current_stmt->bind_param("i", $service_id);
        $current_stmt->execute();
        $current_stmt->bind_result($current_image);
        $current_stmt->fetch();
        $current_stmt->close();

        // Handle image upload
        $cover_image = $current_image;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            $upload_dir = '../uploads/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                // Delete old image if exists
                if ($current_image && file_exists('../' . $current_image)) {
                    unlink('../' . $current_image);
                }

                $file_name = 'service_' . time() . '_' . uniqid() . '.' . $file_ext;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
                    $cover_image = 'uploads/services/' . $file_name;
                }
            }
        }

        // Update service
        $stmt = $conn->prepare("UPDATE services SET 
            category_id = ?, service_name = ?, description = ?, short_description = ?,
            base_price = ?, hourly_rate = ?, min_hours = ?, max_hours = ?, is_popular = ?,
            is_featured = ?, cover_image = ?, status = ?, meta_title = ?,
            meta_description = ?, meta_keywords = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        $stmt->bind_param(
            "isssdiiiiisssssi",
            $category_id,
            $service_name,
            $description,
            $short_description,
            $base_price,
            $hourly_rate,
            $min_hours,
            $max_hours,
            $is_popular,
            $is_featured,
            $cover_image,
            $status,
            $meta_title,
            $meta_description,
            $meta_keywords,
            $service_id
        );

        if ($stmt->execute()) {
            // Update features
            if (isset($_POST['feature_name'])) {
                // Delete old features
                $conn->query("DELETE FROM service_features WHERE service_id = $service_id");

                // Add new features
                foreach ($_POST['feature_name'] as $feature) {
                    $feature = trim($feature);
                    if (!empty($feature)) {
                        $feature_stmt = $conn->prepare("INSERT INTO service_features (service_id, feature_name) VALUES (?, ?)");
                        $feature_stmt->bind_param("is", $service_id, $feature);
                        $feature_stmt->execute();
                    }
                }
            }

            // Update FAQs
            if (isset($_POST['faq_question'])) {
                // Delete old FAQs
                $conn->query("DELETE FROM service_faqs WHERE service_id = $service_id");

                // Add new FAQs
                for ($i = 0; $i < count($_POST['faq_question']); $i++) {
                    $question = trim($_POST['faq_question'][$i]);
                    $answer = trim($_POST['faq_answer'][$i]);

                    if (!empty($question) && !empty($answer)) {
                        $faq_stmt = $conn->prepare("INSERT INTO service_faqs (service_id, question, answer) VALUES (?, ?, ?)");
                        $faq_stmt->bind_param("iss", $service_id, $question, $answer);
                        $faq_stmt->execute();
                    }
                }
            }

            $message = "Service updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating service: " . $stmt->error;
            $message_type = "danger";
        }
    }
}

// Handle delete service
if (isset($_GET['delete'])) {
    $service_id = (int) $_GET['delete'];

    // Get service images
    $img_stmt = $conn->prepare("SELECT cover_image, gallery_images FROM services WHERE id = ?");
    $img_stmt->bind_param("i", $service_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $service = $img_result->fetch_assoc();

    // Delete cover image
    if ($service['cover_image'] && file_exists('../' . $service['cover_image'])) {
        unlink('../' . $service['cover_image']);
    }

    // Delete gallery images
    if ($service['gallery_images']) {
        $gallery_images = json_decode($service['gallery_images'], true);
        foreach ($gallery_images as $image) {
            if (file_exists('../' . $image)) {
                unlink('../' . $image);
            }
        }
    }

    // Delete service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        $message = "Service deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting service: " . $stmt->error;
        $message_type = "danger";
    }
}

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY name");

// Fetch services
$services_query = "SELECT s.*, c.name as category_name 
                   FROM services s 
                   LEFT JOIN service_categories c ON s.category_id = c.id 
                   ORDER BY s.created_at DESC";
$services = $conn->query($services_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - CHEF AT PARTNER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <?php include "links.php"; ?>
    <style>
        .service-card {
            transition: transform 0.3s;
            border: 1px solid #e0e0e0;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .service-image {
            height: 200px;
            object-fit: cover;
        }

        .badge-service {
            font-size: 0.75rem;
        }

        .feature-list {
            list-style: none;
            padding-left: 0;
        }

        .feature-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }

        .gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .gallery-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .dropzone {
            border: 2px dashed #007bff !important;
            border-radius: 5px;
            padding: 20px;
        }

        .feature-item,
        .faq-item {
            margin-bottom: 10px;
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
        <div class="main_content_iner">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="text-center">Manage Services</h2>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <?php if ($message): ?>
                                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show"
                                        role="alert">
                                        <?php echo $message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <!-- Add/Edit Service Form -->
                                <form action="" method="post" enctype="multipart/form-data" class="mb-5"
                                    id="serviceForm">
                                    <input type="hidden" name="action" id="formAction" value="add_service">
                                    <input type="hidden" name="service_id" id="service_id">

                                    <div class="row">
                                        <!-- Basic Information -->
                                        <div class="col-md-8">
                                            <div class="card mb-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0">Basic Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Service Name *</label>
                                                            <input type="text" name="service_name" id="service_name"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Category *</label>
                                                            <select name="category_id" id="category_id"
                                                                class="form-control" required>
                                                                <option value="">Select Category</option>
                                                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                                                    <option value="<?php echo $cat['id']; ?>">
                                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                                    </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Short Description</label>
                                                            <textarea name="short_description" id="short_description"
                                                                class="form-control" rows="2"></textarea>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Full Description *</label>
                                                            <textarea name="description" id="description"
                                                                class="form-control" rows="5" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pricing Information -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-info text-white">
                                                    <h5 class="mb-0">Pricing & Duration</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Base Price (₹) *</label>
                                                            <input type="number" name="base_price" id="base_price"
                                                                class="form-control" min="0" step="0.01" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Minimum Hours</label>
                                                            <input type="number" name="min_hours" id="min_hours"
                                                                class="form-control" min="1" value="1">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Maximum Hours</label>
                                                            <input type="number" name="max_hours" id="max_hours"
                                                                class="form-control" min="1" value="8">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="hourly_rate" id="hourly_rate" value="1"
                                                                    checked>
                                                                <label class="form-check-label" for="hourly_rate">
                                                                    This is an hourly rate service
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Features -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-success text-white">
                                                    <h5 class="mb-0">What's Included (Features)</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="featuresContainer">
                                                        <div class="feature-item input-group mb-2">
                                                            <input type="text" name="feature_name[]"
                                                                class="form-control" placeholder="Add a feature">
                                                            <button type="button" class="btn btn-danger remove-feature">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="addFeature()">
                                                        <i class="bi bi-plus"></i> Add Feature
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- FAQ -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-warning text-white">
                                                    <h5 class="mb-0">Frequently Asked Questions</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="faqContainer">
                                                        <div class="faq-item border p-3 mb-3">
                                                            <div class="mb-2">
                                                                <label>Question</label>
                                                                <input type="text" name="faq_question[]"
                                                                    class="form-control" placeholder="Enter question">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label>Answer</label>
                                                                <textarea name="faq_answer[]" class="form-control"
                                                                    rows="2" placeholder="Enter answer"></textarea>
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-faq">
                                                                Remove FAQ
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                        onclick="addFaq()">
                                                        <i class="bi bi-plus"></i> Add FAQ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sidebar Options -->
                                        <div class="col-md-4">
                                            <!-- Image Upload -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-secondary text-white">
                                                    <h5 class="mb-0">Service Images</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Cover Image *</label>
                                                        <input type="file" name="cover_image" id="cover_image"
                                                            class="form-control" accept="image/*">
                                                        <small class="text-muted">Recommended: 800x600px, max
                                                            2MB</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Gallery Images</label>
                                                        <input type="file" name="gallery_images[]" class="form-control"
                                                            multiple accept="image/*">
                                                        <small class="text-muted">Multiple images allowed</small>
                                                    </div>
                                                    <div id="imagePreview" class="gallery-preview"></div>
                                                </div>
                                            </div>

                                            <!-- Status & Options -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-dark text-white">
                                                    <h5 class="mb-0">Status & Options</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="active">Active</option>
                                                            <option value="inactive">Inactive</option>
                                                            <option value="pending">Pending</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_popular" id="is_popular" value="1">
                                                            <label class="form-check-label" for="is_popular">
                                                                Mark as Popular Service
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_featured" id="is_featured" value="1">
                                                            <label class="form-check-label" for="is_featured">
                                                                Mark as Featured Service
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SEO Settings -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-dark text-white">
                                                    <h5 class="mb-0">SEO Settings</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Meta Title</label>
                                                        <input type="text" name="meta_title" id="meta_title"
                                                            class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Meta Description</label>
                                                        <textarea name="meta_description" id="meta_description"
                                                            class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Meta Keywords</label>
                                                        <input type="text" name="meta_keywords" id="meta_keywords"
                                                            class="form-control">
                                                        <small class="text-muted">Comma separated keywords</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                                        <i class="bi bi-check-circle"></i> Save Service
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm w-100 mt-2"
                                                        onclick="resetForm()">
                                                        <i class="bi bi-arrow-clockwise"></i> Reset Form
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Services List -->
                                <div class="mt-5">
                                    <h3 class="text-center mb-4">Current Services</h3>

                                    <!-- Filters -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <select class="form-control" id="categoryFilter"
                                                onchange="filterServices()">
                                                <option value="">All Categories</option>
                                                <?php
                                                $categories_filter = $conn->query("SELECT * FROM service_categories ORDER BY name");
                                                while ($cat = $categories_filter->fetch_assoc()): ?>
                                                    <option value="<?php echo $cat['id']; ?>">
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="statusFilter" onchange="filterServices()">
                                                <option value="">All Status</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="searchService"
                                                placeholder="Search services..." onkeyup="filterServices()">
                                        </div>
                                    </div>

                                    <!-- Services Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Image</th>
                                                    <th>Service Name</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Popular</th>
                                                    <th>Featured</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="servicesTable">
                                                <?php $counter = 1; ?>
                                                <?php while ($service = $services->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td>
                                                            <?php if ($service['cover_image']): ?>
                                                                <img src="../<?php echo $service['cover_image']; ?>"
                                                                    alt="Service Image"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            <?php else: ?>
                                                                <div
                                                                    style="width: 50px; height: 50px; background: #eee; 
                                                                   display: flex; align-items: center; justify-content: center;">
                                                                    <i class="bi bi-image"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($service['service_name']); ?></strong><br>
                                                            <small
                                                                class="text-muted"><?php echo substr($service['short_description'], 0, 50); ?>...</small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($service['category_name']); ?></td>
                                                        <td>₹<?php echo $service['base_price']; ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php
                                                            switch ($service['status']) {
                                                                case 'active':
                                                                    echo 'success';
                                                                    break;
                                                                case 'inactive':
                                                                    echo 'secondary';
                                                                    break;
                                                                case 'pending':
                                                                    echo 'warning';
                                                                    break;
                                                            }
                                                            ?>">
                                                                <?php echo ucfirst($service['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($service['is_popular']): ?>
                                                                <span class="badge bg-success">Yes</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($service['is_featured']): ?>
                                                                <span class="badge bg-info">Yes</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($service['created_at'])); ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button class="btn btn-sm btn-warning"
                                                                    onclick="editService(<?php echo $service['id']; ?>)">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-info"
                                                                    onclick="viewService(<?php echo $service['id']; ?>)">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                                <a href="?delete=<?php echo $service['id']; ?>"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this service?')">
                                                                    <i class="bi bi-trash"></i>
                                                                </a>
                                                            </div>
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
                </div>
            </div>
        </div>
    </section>
    <section class="mb-0" style="position: fixed; bottom: 0; width: 100%;">
        <?php require "footer.php"; ?>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add feature field
        function addFeature() {
            const container = document.getElementById('featuresContainer');
            const div = document.createElement('div');
            div.className = 'feature-item input-group mb-2';
            div.innerHTML = `
                <input type="text" name="feature_name[]" class="form-control" placeholder="Add a feature">
                <button type="button" class="btn btn-danger remove-feature">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(div);

            // Add event listener to remove button
            div.querySelector('.remove-feature').addEventListener('click', function () {
                div.remove();
            });
        }

        // Add FAQ field
        function addFaq() {
            const container = document.getElementById('faqContainer');
            const div = document.createElement('div');
            div.className = 'faq-item border p-3 mb-3';
            div.innerHTML = `
                <div class="mb-2">
                    <label>Question</label>
                    <input type="text" name="faq_question[]" class="form-control" placeholder="Enter question">
                </div>
                <div class="mb-2">
                    <label>Answer</label>
                    <textarea name="faq_answer[]" class="form-control" rows="2" placeholder="Enter answer"></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-faq">Remove FAQ</button>
            `;
            container.appendChild(div);

            // Add event listener to remove button
            div.querySelector('.remove-faq').addEventListener('click', function () {
                div.remove();
            });
        }

        // Reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset the form?')) {
                document.getElementById('serviceForm').reset();
                document.getElementById('formAction').value = 'add_service';
                document.getElementById('service_id').value = '';
                document.getElementById('featuresContainer').innerHTML = `
                    <div class="feature-item input-group mb-2">
                        <input type="text" name="feature_name[]" class="form-control" placeholder="Add a feature">
                        <button type="button" class="btn btn-danger remove-feature">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                document.getElementById('faqContainer').innerHTML = `
                    <div class="faq-item border p-3 mb-3">
                        <div class="mb-2">
                            <label>Question</label>
                            <input type="text" name="faq_question[]" class="form-control" placeholder="Enter question">
                        </div>
                        <div class="mb-2">
                            <label>Answer</label>
                            <textarea name="faq_answer[]" class="form-control" rows="2" placeholder="Enter answer"></textarea>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-faq">Remove FAQ</button>
                    </div>
                `;
                document.getElementById('imagePreview').innerHTML = '';
            }
        }

        // Edit service
        function editService(serviceId) {
            // Fetch service data via AJAX
            fetch(`ajax/get-service.php?id=${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const service = data.service;

                        // Fill form with service data
                        document.getElementById('formAction').value = 'update_service';
                        document.getElementById('service_id').value = service.id;
                        document.getElementById('service_name').value = service.service_name;
                        document.getElementById('category_id').value = service.category_id;
                        document.getElementById('short_description').value = service.short_description || '';
                        document.getElementById('description').value = service.description;
                        document.getElementById('base_price').value = service.base_price;
                        document.getElementById('min_hours').value = service.min_hours;
                        document.getElementById('max_hours').value = service.max_hours;
                        document.getElementById('hourly_rate').checked = service.hourly_rate == 1;
                        document.getElementById('status').value = service.status;
                        document.getElementById('is_popular').checked = service.is_popular == 1;
                        document.getElementById('is_featured').checked = service.is_featured == 1;
                        document.getElementById('meta_title').value = service.meta_title || '';
                        document.getElementById('meta_description').value = service.meta_description || '';
                        document.getElementById('meta_keywords').value = service.meta_keywords || '';

                        // Fill features
                        const featuresContainer = document.getElementById('featuresContainer');
                        featuresContainer.innerHTML = '';
                        if (data.features && data.features.length > 0) {
                            data.features.forEach(feature => {
                                const div = document.createElement('div');
                                div.className = 'feature-item input-group mb-2';
                                div.innerHTML = `
                                    <input type="text" name="feature_name[]" class="form-control" 
                                           value="${feature.feature_name.replace(/"/g, '&quot;')}">
                                    <button type="button" class="btn btn-danger remove-feature">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                `;
                                featuresContainer.appendChild(div);
                            });
                        }

                        // Add empty feature field
                        const emptyFeatureDiv = document.createElement('div');
                        emptyFeatureDiv.className = 'feature-item input-group mb-2';
                        emptyFeatureDiv.innerHTML = `
                            <input type="text" name="feature_name[]" class="form-control" placeholder="Add a feature">
                            <button type="button" class="btn btn-danger remove-feature">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                        featuresContainer.appendChild(emptyFeatureDiv);

                        // Fill FAQs
                        const faqContainer = document.getElementById('faqContainer');
                        faqContainer.innerHTML = '';
                        if (data.faqs && data.faqs.length > 0) {
                            data.faqs.forEach(faq => {
                                const div = document.createElement('div');
                                div.className = 'faq-item border p-3 mb-3';
                                div.innerHTML = `
                                    <div class="mb-2">
                                        <label>Question</label>
                                        <input type="text" name="faq_question[]" class="form-control" 
                                               value="${faq.question.replace(/"/g, '&quot;')}">
                                    </div>
                                    <div class="mb-2">
                                        <label>Answer</label>
                                        <textarea name="faq_answer[]" class="form-control" rows="2">${faq.answer}</textarea>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-faq">Remove FAQ</button>
                                `;
                                faqContainer.appendChild(div);
                            });
                        }

                        // Add empty FAQ field
                        const emptyFaqDiv = document.createElement('div');
                        emptyFaqDiv.className = 'faq-item border p-3 mb-3';
                        emptyFaqDiv.innerHTML = `
                            <div class="mb-2">
                                <label>Question</label>
                                <input type="text" name="faq_question[]" class="form-control" placeholder="Enter question">
                            </div>
                            <div class="mb-2">
                                <label>Answer</label>
                                <textarea name="faq_answer[]" class="form-control" rows="2" placeholder="Enter answer"></textarea>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-faq">Remove FAQ</button>
                        `;
                        faqContainer.appendChild(emptyFaqDiv);

                        // Show image preview if exists
                        const imagePreview = document.getElementById('imagePreview');
                        imagePreview.innerHTML = '';
                        if (service.cover_image) {
                            const img = document.createElement('img');
                            img.src = '../' + service.cover_image;
                            img.alt = 'Current Image';
                            img.style.maxWidth = '100px';
                            imagePreview.appendChild(img);
                        }

                        // Scroll to form
                        document.getElementById('serviceForm').scrollIntoView({ behavior: 'smooth' });

                        // Add event listeners to remove buttons
                        document.querySelectorAll('.remove-feature').forEach(button => {
                            button.addEventListener('click', function () {
                                this.parentElement.remove();
                            });
                        });

                        document.querySelectorAll('.remove-faq').forEach(button => {
                            button.addEventListener('click', function () {
                                this.closest('.faq-item').remove();
                            });
                        });
                    } else {
                        alert('Error loading service data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading service data');
                });
        }

        // View service details
        function viewService(serviceId) {
            window.open(`../service-details.php?id=${serviceId}`, '_blank');
        }

        // Filter services
        function filterServices() {
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchService').value.toLowerCase();

            const rows = document.querySelectorAll('#servicesTable tr');

            rows.forEach(row => {
                const categoryCell = row.cells[3]?.textContent || '';
                const statusCell = row.cells[5]?.textContent.toLowerCase() || '';
                const nameCell = row.cells[2]?.textContent.toLowerCase() || '';

                let show = true;

                if (category && categoryCell.indexOf(category) === -1) {
                    show = false;
                }

                if (status && statusCell !== status.toLowerCase()) {
                    show = false;
                }

                if (search && !nameCell.includes(search)) {
                    show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        // Image preview
        document.getElementById('cover_image').addEventListener('change', function (e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Gallery images preview
        const galleryInput = document.querySelector('input[name="gallery_images[]"]');
        if (galleryInput) {
            galleryInput.addEventListener('change', function (e) {
                const preview = document.getElementById('imagePreview');

                for (let i = 0; i < this.files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '80px';
                        img.style.marginRight = '5px';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(this.files[i]);
                }
            });
        }

        // Initialize remove buttons
        document.addEventListener('DOMContentLoaded', function () {
            // Feature remove buttons
            document.querySelectorAll('.remove-feature').forEach(button => {
                button.addEventListener('click', function () {
                    this.parentElement.remove();
                });
            });

            // FAQ remove buttons
            document.querySelectorAll('.remove-faq').forEach(button => {
                button.addEventListener('click', function () {
                    this.closest('.faq-item').remove();
                });
            });
        });
    </script>
</body>

</html>
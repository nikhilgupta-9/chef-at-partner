<?php
include_once __DIR__ . '/config/connect.php';
include_once(__DIR__ . '/util/function.php');
// Fetch products for the current subcategory alias
$products = fetch_product_page();
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title><?= $_SESSION['meta_title'] ?? 'CHEF AT PARTNER' ?></title>
    <meta name="keywords" content="<?= $_SESSION['meta_key'] ?? '' ?>">
    <meta name="description" content="<?= $_SESSION['meta_desc'] ?? '' ?>">
    <?php include_once 'links.php' ?>

    <style>
        .service-card {
            height: 100%;
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .card-img-top {
            /* height: 200px; */
            object-fit: cover;
            margin-bottom: 10px;
        }

        .card-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .card-subtitle {
            font-size: 1rem;
            color: #2f3235;
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
            min-height: 25px;
        }

        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            color: #2e7d32;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .discount-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }

        .card-footer {
            background: white;
            border-top: 1px solid #eee;
            padding: 1rem;
        }

        .btn-book {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            border: none;
            padding: 6px 20px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .btn-book:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            color: white;
            transform: scale(1.05);
        }

        .no-products-card {
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
        }
    </style>
</head>

<body class="home page-template-default page page-id-3699 wpb-js-composer js-comp-ver-5.2.1 vc_responsive">

    <?php include_once 'includes/header.php' ?>

    <!-- Banner Section -->
    <div class="container-fluid p-0 mb-4">
        <div class="row">
            <img src="<?= $BASE_URL ?>assets/images/banner/bg-banner2.png" alt="Banner" class="img-fluid w-100">
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3"><?= $_SESSION['sub_cat_name'] ?? 'Our Services' ?></h1>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Professional home service providers, background-verified and rated by thousands of
                satisfied customers. Book your service in just a few clicks.
            </p>
        </div>

        <div class="row g-4">
            <?php if (!empty($products)): ?>
                <?php
                $hasProducts = false;
                foreach ($products as $product):
                    if (!empty($product['pro_name'])):
                        $hasProducts = true;
                        $discount = 0;
                        if (!empty($product['mrp']) && $product['mrp'] > $product['selling_price']) {
                            $discount = $product['mrp'] - $product['selling_price'];
                        }
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card service-card h-100">
                                <?php if ($discount > 0): ?>
                                    <div class="discount-badge">SAVE ₹<?= $discount ?></div>
                                <?php endif; ?>

                                <div class="price-badge">₹<?= $product['selling_price'] ?></div>

                                <img src="<?= $BASE_URL ?>admin/assets/img/uploads/<?= $product['pro_img'] ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($product['pro_name']) ?>">

                                <div class="card-body">
                                    <h4 class="card-title"><?= htmlspecialchars($product['pro_name']) ?></h4>
                                    <h5 class="card-subtitle mb-2 text-muted"><?= $_SESSION['sub_cat_name'] ?? 'Service' ?></h5>
                                    <p class="card-text">
                                        Expert service with professional standards. Book now for the best experience.
                                    </p>
                                </div>

                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <?php if ($discount > 0): ?>
                                        <small class="text-muted">
                                            <del>₹<?= $product['mrp'] ?></del>
                                        </small>
                                    <?php else: ?>
                                        <span></span>
                                    <?php endif; ?>
                                    <a href="<?= $BASE_URL ?>book-service/<?= $product['product_slug'] ?>" class="btn btn-book">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;

                if (!$hasProducts): ?>
                    <div class="col-12">
                        <div class="card no-products-card text-center py-5">
                            <div class="card-body">
                                <h3 class="text-muted mb-3">No services available yet</h3>
                                <p class="text-muted mb-4">We're working on adding services to this category. Please check back
                                    soon!</p>
                                <a href="<?= $BASE_URL ?>" class="btn btn-primary">Browse Other Services</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="col-12">
                    <div class="card no-products-card text-center py-5">
                        <div class="card-body">
                            <h3 class="text-muted mb-3">Service Not Found</h3>
                            <p class="text-muted mb-4">The service category you're looking for doesn't exist or has been
                                moved.</p>
                            <a href="<?= $BASE_URL ?>" class="btn btn-primary">Go to Homepage</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'includes/footer.php' ?>

</body>

</html>
<?php include_once('config/connect.php') ?>
<!DOCTYPE html>
<html lang="en-US">


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title>CHEF AT PARTNER</title>

    <!-- <link rel="stylesheet" href="css/pe-icon-7-stroke.css" type="text/css" media="all" /> -->
    <?php include_once 'links.php' ?>


</head>
<style>
    .service-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .service-img {
        height: 180px;
        overflow: hidden;
    }

    .service-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-body {
        padding: 16px;
    }

    .service-body h5 {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .service-sub {
        font-size: 13px;
        color: #888;
        margin-bottom: 8px;
    }

    .service-desc {
        font-size: 14px;
        color: #555;
        margin-bottom: 16px;
    }

    .service-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price {
        font-weight: 600;
        color: #2e7d32;
    }
</style>

<body class="home page-template-default page page-id-3699">

    <?php include_once 'includes/header.php' ?>

    <div class="content-block">
        <div class="container-bg with-bg container-fluid"
            data-style="background-image: url(assets/images/contact-us/contact-hero.png);">
            <div class="container-bg-overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-item-title">
                                <h1 class="text-center texttransform-none fw-bold">
                                    Our Services
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <main class="main-content">
            <!-- Services Section -->
            <section class="services-section py-5">
                <div class="container">
                    <div class="section-header text-center mb-5">
                        <h1 class="fw-bold display-5 mb-3">Our Services</h1>
                        <p class="lead text-muted mx-auto" style="max-width: 600px;">
                            Professional home service providers, background-verified and rated by thousands of
                            satisfied customers. Book your service in just a few clicks.
                        </p>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($sub_cat as $sub_c) { ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card service-card h-100 shadow-sm border-0">
                                    <div class="service-img p-1 mb-2">
                                        <a href="<?= $BASE_URL ?>">
                                            <img src="<?= $BASE_URL ?>admin/uploads/sub-category/<?= $sub_c['sub_cat_img'] ?>"
                                                alt="<?= $sub_c['categories'] ?>" class="img-fluid rounded">
                                        </a>
                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">
                                            <?= htmlspecialchars($sub_c['categories']) ?>
                                        </h5>
                                        <p class="card-subtitle text-muted small mb-2">Multi Cuisine Specialists</p>
                                        <p class="card-text">
                                            Expert chefs for traditional & international cuisines.
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <a href="<?= $site ?>book-a-chef/<?= $sub_c['slug_url'] ?>"
                                                class="btn btn-primary btn-sm px-3">
                                                Book Now
                                            </a>
                                            <span class="fw-bold text-primary">₹299</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <!-- Occasions Section -->
            <section class="occasion-section my-3">
                <div class="occasion-header">
                    <h2>Special Occasions</h2>
                    <p>We cater to all kinds of occasions</p>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="occasion-grid">

                            <?php
                            foreach ($ocassion as $oca) {
                                ?>
                                <div class="occasion-card" data-bs-toggle="modal" data-bs-target="#chooseServiceModal">
                                    <img src="<?= $BASE_URL ?>admin/assets/img/uploads/<?= $oca['pro_img'] ?>"
                                        alt="<?= $oca['pro_name'] ?>">
                                    <div class="occasion-overlay">
                                        <h3><?= $oca['pro_name'] ?></h3>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </section>

            <!-- Promo Section -->
            <section class="promo-section py-5">
                <div class="container-fluid">
                    <div class="vc_row wpb_row vc_row-fluid vc_custom_1496400967126">
                        <div class="wpb_column vc_column_container vc_col-sm-6">
                            <div class="vc_column-inner vc_custom_1501610531272">
                                <div class="wpb_wrapper">
                                    <div
                                        class="mgt-promo-block-container wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp">
                                        <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                            <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-15851137"
                                                data-style="background-color: #ffffff;background-image: url(upload/food-plate.jpg);background-repeat: no-repeat;height: 420px;">
                                                <div class="mgt-promo-block-content va-middle">
                                                    <div class="mgt-promo-block-content-inside vc_custom_1502117266071">
                                                        <h5 style="text-align: center;">Hungry?</h5>
                                                        <h2 style="text-align: center;">
                                                            Book A<br />
                                                            Chef
                                                        </h2>
                                                        <div
                                                            class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                            <a class="btn hvr-grow mgt-button-icon- mgt-button mgt-style-solid mgt-size-normal mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                href="reservation.html">
                                                                Eat with us
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wpb_column vc_column_container vc_col-sm-6">
                            <div class="vc_column-inner vc_custom_1501610544677">
                                <div class="wpb_wrapper">
                                    <div
                                        class="mgt-promo-block-container wpb_content_element wpb_animate_when_almost_visible wpb_fadeInRight fadeInRight">
                                        <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                            <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-15051295"
                                                data-style="background-color: #ffffff;background-image: url(assets/images/reservation.webp);background-repeat: no-repeat;height: 420px;">
                                                <div class="mgt-promo-block-content va-middle">
                                                    <div class="mgt-promo-block-content-inside vc_custom_1502119503691">
                                                        <h5 style="text-align: center;">Menus</h5>
                                                        <h2 style="text-align: center;">
                                                            Start your<br />
                                                            experience today
                                                        </h2>
                                                        <div
                                                            class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                            <a class="btn hvr-grow mgt-button-icon- mgt-button mgt-style-solid mgt-size-normal mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                href="the-menu.html">
                                                                Menus
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php include_once 'includes/footer.php' ?>

</body>

</html>
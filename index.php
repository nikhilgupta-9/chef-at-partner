<?php include_once('config/connect.php') ?>
<!DOCTYPE html>
<html lang="en-US">


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title>Home | CHEF AT PARTNER</title>

    <!-- <link rel="stylesheet" href="css/pe-icon-7-stroke.css" type="text/css" media="all" /> -->
    <?php include_once 'links.php' ?>


</head>




<body class="home page-template-default page page-id-3699 wpb-js-composer js-comp-ver-5.2.1 vc_responsive">

    <?php include_once 'includes/header.php' ?>

    <!-- LEFT SIDE FLOATING BUTTONS -->



    <div class="content-block stick-to-footer">
        <div class="page-container container">
            <div class="row">
                <div class="col-md-12 entry-content">
                    <article>
                        <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true"
                            class="vc_row wpb_row vc_row-fluid vc_row-no-padding">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <!-- Swiper -->
                                    <div class="swiper header-banner">
                                        <div class="swiper-wrapper">
                                            <?php
                                            foreach ($banner as $ban) {
                                                ?>
                                                <div class="swiper-slide">
                                                    <img src="<?= $BASE_URL . "admin/" . $ban['banner_path'] ?>" alt="">
                                                </div>
                                            <?php } ?>

                                        </div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1496400967126">
                            <div class="wpb_column vc_column_container vc_col-sm-6">
                                <div class="vc_column-inner vc_custom_1501610531272">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-promo-block-container wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp">
                                            <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                                <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-66293803"
                                                    data-style="background-color: #ffffff;background-image: url(assets/images/about/a1.png);background-repeat: no-repeat;height: 420px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div
                                                            class="mgt-promo-block-content-inside vc_custom_1502111254458">
                                                            <h5 style="text-align: center;">Hungry?</h5>
                                                            <h2 style="text-align: center;">
                                                                Book A<br />
                                                                Table
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
                                                <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-65740325"
                                                    data-style="background-color: #ffffff;background-image: url(assets/images/about/a2.png);background-repeat: no-repeat;height: 420px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div
                                                            class="mgt-promo-block-content-inside vc_custom_1502119337451">
                                                            <h5 style="text-align: center;">Menus</h5>
                                                            <h2 style="text-align: center;">
                                                                Start your<br />
                                                                experience today
                                                            </h2>
                                                            <div
                                                                class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                                <a class="btn hvr-grow mgt-button-icon- mgt-button mgt-style-solid mgt-size-normal mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                    href="about-us-restaurant.html">
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
                        <div class="container py-5">
                            <div class="section-header text-center mb-5">
                                <h1 class="fw-bold">Our Services</h1>
                                <p class="text-muted">
                                    Professional home service providers, background-verified and rated by thousands of
                                    satisfied customers. Book your service in just a few clicks.
                                </p>
                            </div>

                            <div class="row g-4">
                                <?php foreach ($sub_cat as $sub_c) { ?>
                                    <div class="col-lg-3 col-md-4 col-6">
                                        <div class="service-card h-100 p-2">

                                            <div class="service-img">
                                                <a href="<?= $BASE_URL ?>">
                                                    <img src="<?= $BASE_URL ?>admin/uploads/sub-category/<?= $sub_c['sub_cat_img'] ?>"
                                                        alt="<?= $sub_c['categories'] ?>">
                                                </a>
                                            </div>

                                            <div class="service-body">
                                                <h5 class="mt-0">

                                                    <?= $sub_c['categories'] ?>
                                                </h5>
                                                <p class="service-sub">Multi Cuisine Specialists</p>
                                                <p class="service-desc">
                                                    Expert chefs for traditional & international cuisines.
                                                </p>
                                                <div class="service-footer">
                                                    <button class="btn btn-outline-primary btn-sm">Book Now</button>
                                                    <span class="price">₹299</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>


                        <section class="occasion-section">
                            <div class="occasion-header">
                                <h2>Special Occasions</h2>
                                <p>We cater to all kinds of occasions</p>
                            </div>

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
                        </section>
                        <section class="professional-section">
                            <div class="professional-container">

                                <h2 class="professional-title">Top Rated Professionals</h2>
                                <p class="professional-subtitle">
                                    Trained, Verified and Background Checked
                                </p>

                                <div class="professional-slider">
                                    <div class="professional-track">

                                        <!-- Card 1 -->
                                        <div class="professional-card">
                                            <div class="professional-img">
                                                <img src="assets/images/proffesionals/1.png" alt="Ganesh">
                                            </div>
                                            <h3>Ganesh</h3>
                                            <p>Multi Cuisine Chef</p>
                                            <span>18 Years of Experience</span>
                                            <div class="professional-rating">5.0</div>
                                        </div>

                                        <!-- Card 2 -->
                                        <div class="professional-card">
                                            <div class="professional-img">
                                                <img src="assets/images/proffesionals/2.png" alt="Amit">
                                            </div>
                                            <h3>Amit</h3>
                                            <p>Multi Cuisine Chef</p>
                                            <span>10 Years of Experience</span>
                                            <div class="professional-rating">4.7</div>
                                        </div>

                                        <!-- Card 3 -->
                                        <div class="professional-card">
                                            <div class="professional-img">
                                                <img src="assets/images/proffesionals/3.png" alt="Bhupendra">
                                            </div>
                                            <h3>Bhupendra</h3>
                                            <p>Multi Cuisine Chef</p>
                                            <span>11 Years of Experience</span>
                                            <div class="professional-rating">4.7</div>
                                        </div>

                                        <!-- Card 4 -->
                                        <div class="professional-card">
                                            <div class="professional-img">
                                                <img src="assets/images/proffesionals/4.png" alt="Pan">
                                            </div>
                                            <h3>Pan</h3>
                                            <p>Multi Cuisine Chef</p>
                                            <span>18 Years of Experience</span>
                                            <div class="professional-rating">4.4</div>
                                        </div>

                                    </div>
                                </div>

                                <a href="#" class="professional-btn">View More</a>

                            </div>
                        </section>

                        <div data-vc-full-width="true" data-vc-full-width-init="false"
                            class="vc_row wpb_row vc_row-fluid vc_custom_1501762265855 vc_row-has-fill">
                            <div class="wpb_column vc_column_container vc_col-sm-5">
                                <div class="vc_column-inner vc_custom_1502119248785">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-header-block clearfix text-left text-black wpb_animate_when_almost_visible wpb_fadeInDown fadeInDown wpb_content_element mgt-header-block-style-2 mgt-header-block-fontsize-medium mgt-header-texttransform-none mgt-header-block-2224038">
                                            <p class="mgt-header-block-subtitle">About Us</p>
                                            <h2 class="mgt-header-block-title text-font-weight-default">Fresh and local
                                                perfect food for you</h2>
                                            <div class="mgt-header-line mgt-header-line-margin-large"></div>
                                        </div>
                                        <div
                                            class="wpb_text_column wpb_content_element wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft text-size-medium">
                                            <div class="wpb_wrapper">
                                                <p>
                                                    <span style="color: #999999;">
                                                        This kitchen is a brewery of life – whether it’s the kids baking
                                                        parties or their parents elaborate soirees, there’s always
                                                        something cooking in here.
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-button-wrapper mgt-button-wrapper-align-left mgt-button-wrapper-display-inline mgt-button-top-margin-false mgt-button-right-margin-false mgt-button-round-edges-full wpb_animate_when_almost_visible wpb_fadeInUpBig fadeInUpBig">
                                            <a class="btn hvr-push mgt-button-icon- mgt-button mgt-style-solid mgt-size-normal mgt-align-left mgt-display-inline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                href="about-us-restaurant.html">
                                                About us
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-5">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-promo-block-container wpb_content_element wpb_animate_when_almost_visible wpb_fadeInRight fadeInRight">
                                            <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                                <div class="mgt-promo-block animated white-text cover-image no-darken mgt-promo-block-69094683"
                                                    data-style="background-color: #ffffff;background-image: url(assets/images/about/1.png);background-repeat: no-repeat;height: 495px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div
                                                            class="mgt-promo-block-content-inside vc_custom_1502111459689 mgt-promo-block-content-inside-show-on-hover">
                                                            <h2 style="text-align: center;">Let’s build something
                                                                amazing. We’re ready when you are.</h2>
                                                            <p style="text-align: center;">We only hire great people who
                                                                strive to push their ideas into fruition by outmuscling
                                                                and outhustling the competition.</p>
                                                            <div
                                                                class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                                <a class="btn hvr-push mgt-button-icon-true mgt-button mgt-style-bordered mgt-size-normal mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-right text-font-weight-default mgt-text-transform-none"
                                                                    href="about-us-restaurant.html">
                                                                    Read more<i
                                                                        class="entypo-icon entypo-icon-right-open-mini"></i>
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

                            <div class="wpb_column vc_column_container vc_col-sm-2">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div
                                                class="mgt-promo-block-wrapper mgt-promo-block-shadow mgt-promo-block-hover">
                                                <div class="mgt-promo-block black-text cover-image no-darken mgt-promo-block-59326650"
                                                    data-style="background-color: #f5f5f5;background-image: url(upload/restaurant-table.jpg);background-repeat: no-repeat;height: 145px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vc_empty_space" style="height: 30px;"><span
                                                class="vc_empty_space_inner"></span></div>
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div
                                                class="mgt-promo-block-wrapper mgt-promo-block-shadow mgt-promo-block-hover">
                                                <div class="mgt-promo-block black-text cover-image no-darken mgt-promo-block-11490570"
                                                    data-style="background-color: #f5f5f5;background-image: url(upload/brooke-lark.jpg);background-repeat: no-repeat;height: 145px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vc_empty_space" style="height: 30px;"><span
                                                class="vc_empty_space_inner"></span></div>
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div
                                                class="mgt-promo-block-wrapper mgt-promo-block-shadow mgt-promo-block-hover">
                                                <div class="mgt-promo-block black-text cover-image no-darken mgt-promo-block-73119794"
                                                    data-style="background-color: #f5f5f5;background-image: url(assets/images/dishes/food-3.jpg);background-repeat: no-repeat;height: 145px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true"
                            data-vc-parallax="1.5" data-vc-parallax-image="assets/images/banner/page-bg.png"
                            class="vc_row wpb_row vc_row-fluid vc_custom_1501853508813 vc_row-has-fill vc_row-no-padding vc_general vc_parallax vc_parallax-content-moving">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <h2 style="font-size: 55px; color: white; line-height: 58px; text-align: center; font-family: Alex Brush; font-weight: 700; font-style: normal;"
                                            class="vc_custom_heading">
                                            When it satisfies your tastebuds
                                        </h2>
                                        <h2 style="font-size: 65px; color: #ffffff; line-height: 68px; text-align: center;"
                                            class="vc_custom_heading">Sunday Brunch</h2>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div data-vc-full-width="true" data-vc-full-width-init="false"
                            class="vc_row wpb_row vc_row-fluid vc_custom_1501767193871">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-header-block clearfix text-center text-black wpb_animate_when_almost_visible wpb_fadeInDown fadeInDown wpb_content_element mgt-header-block-style-2 mgt-header-block-fontsize-medium mgt-header-texttransform-none mgt-header-block-65407710">
                                            <p class=" text-black">Menus</p>
                                            <h2 class="mgt-header-block-title text-font-weight-default">This month
                                                specials</h2>
                                            <div class="mgt-header-line mgt-header-line-margin-large"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1501769916408">
                            <div class="wpb_column vc_column_container vc_col-sm-6">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img
                                                    src="upload/brooke-lark-1024x1024.jpg"
                                                    alt="HOUSE MADE BEEF BRISKET POUTINE" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4 class="">HOUSE MADE BEEF BRISKET POUTINE<sup>NEW</sup></h4>
                                                </div>
                                                <div class="mgt-item-price-value">$23</div>
                                                <p class="mgt-item-price-description">Horseradish gravy; pickles</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-orange clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img src="upload/salad-wine-1024x1024.jpg"
                                                    alt="CAESAR SALAD &amp; POPCORN CHICKEN" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>CAESAR SALAD &amp; POPCORN CHICKEN<sup>SPECIAL</sup></h4>
                                                </div>
                                                <div class="mgt-item-price-value">$24</div>
                                                <p class="mgt-item-price-description">Classic dressing, bacon, croutons,
                                                    egg yolk; parmesan</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img
                                                    src="upload/barmen-coctail-1024x1024.jpg" alt="SALMON TARTARE" />
                                            </div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>SALMON TARTARE</h4>
                                                </div>
                                                <div class="mgt-item-price-value">$26</div>
                                                <p class="mgt-item-price-description">Fennel, citrus &amp; herb yogourt.
                                                    Served with fries and/or salad</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img
                                                    src="upload/pumpkin-soup-1024x1024.jpg"
                                                    alt="SEARED HALLOUMI CHEESE" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>SEARED HALLOUMI CHEESE</h4>
                                                </div>
                                                <div class="mgt-item-price-value">$22</div>
                                                <p class="mgt-item-price-description">Celeriac, veggies, herb pesto,
                                                    black olive &amp; sunflower seeds</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-6">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUpBig fadeInUpBig mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img src="upload/salad-1024x1024.jpg"
                                                    alt="BEEF CHEESEBURGER" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>BEEF CHEESEBURGER</h4>
                                                </div>
                                                <div class="mgt-item-price-value">$22</div>
                                                <p class="mgt-item-price-description">Île-aux-Grues aged cheddar, smoked
                                                    mustard &amp; caramelized onions</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-black clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUpBig fadeInUpBig mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img
                                                    src="upload/restaurant-table-1024x1024.jpg"
                                                    alt="LIGHTLY SEARED TUNA SALAD" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>LIGHTLY SEARED TUNA SALAD<sup>season</sup></h4>
                                                </div>
                                                <div class="mgt-item-price-value">$27</div>
                                                <p class="mgt-item-price-description">Miso dressing, baby spinach,
                                                    quinoa, crisp vegetables &amp; bagel chips</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUpBig fadeInUpBig mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img
                                                    src="upload/glass-orange-coctail-1-1024x1024.jpg"
                                                    alt="DUCK CONFIT SHEPERD&#039;S PIE" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>DUCK CONFIT SHEPERD&#039;S PIE</h4>
                                                </div>
                                                <div class="mgt-item-price-value">$26</div>
                                                <p class="mgt-item-price-description">Braised cabbage &amp; corn with
                                                    house made fruit ketchup</p>
                                            </div>
                                        </div>
                                        <div
                                            class="mgt-item-price mgt-item-price-badge-color-red clearfix wpb_content_element wpb_animate_when_almost_visible wpb_fadeInUpBig fadeInUpBig mgt-item-price-with-image">
                                            <div class="mgt-item-price-image"><img src="upload/food-plate-1024x1024.jpg"
                                                    alt="NORDIC SHRIMP ROLL" /></div>
                                            <div class="mgt-item-price-details">
                                                <div class="mgt-item-price-line"></div>
                                                <div class="mgt-item-price-title-holder">
                                                    <h4>NORDIC SHRIMP ROLL</h4>
                                                </div>
                                                <div class="mgt-item-price-value">$25</div>
                                                <p class="mgt-item-price-description">Balsam fir &amp; sumac mayo,
                                                    celery &amp; baby kale. Served with fries &amp; coleslaw</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1501771059262">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp">
                                            <a class="btn hvr-push mgt-button-icon- mgt-button mgt-style-bordered mgt-size-normal mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none text-white"
                                                href="the-menu.html">
                                                View The Full Menu
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <section class="monday-special-section">
                            <div class="monday-special-wrap">

                                <div class="monday-special-grid">

                                    <!-- LEFT CONTENT -->
                                    <div class="monday-special-content">
                                        <span class="monday-tag">MONDAY SPECIAL</span>
                                        <h2>Make Mondays Special<br>with a Chef at Partner</h2>
                                        <p>
                                            Turn your Mondays into a delightful culinary experience.
                                            Book a professional chef at special partner prices and enjoy
                                            freshly prepared meals at your home or event.
                                        </p>

                                        <ul class="monday-points">
                                            <li>✔ Exclusive Monday-only offers</li>
                                            <li>✔ Verified professional chefs</li>
                                            <li>✔ Flexible time slots</li>
                                            <li>✔ Perfect for home & office dining</li>
                                        </ul>

                                        <a href="#" class="monday-btn">Book a Chef Now</a>
                                    </div>

                                    <!-- RIGHT CARD -->
                                    <div class="monday-special-card">

                                    </div>

                                </div>

                            </div>
                        </section>

                        <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true"
                            data-vc-parallax="1.5" data-vc-parallax-image="upload/bookchef.png"
                            class="vc_row wpb_row vc_row-fluid vc_custom_1501853930727 vc_row-has-fill vc_row-no-padding vc_general vc_parallax vc_parallax-content-moving">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <h2 style="font-size: 55px; color: white; line-height: 58px; text-align: center; font-family: Alex Brush; font-weight: 700; font-style: normal;"
                                            class="vc_custom_heading">
                                            Hire A Cook or Maid for monthly basis
                                        </h2>
                                        <h2 style="font-size: 65px; color: #ffffff; line-height: 68px; text-align: center;"
                                            class="vc_custom_heading">Trained, Verified, Assured Service</h2>
                                        <div
                                            class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full wpb_animate_when_almost_visible wpb_fadeInUp fadeInUp">
                                            <a class="btn hvr-push mgt-button-icon- mgt-button mgt-style-solid mgt-size-large mgt-align-center mgt-display-newline mgt-text-size-normal mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                href="contact-us-restaurant.html">
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>

                        <section class="lmr-pro-section">
                            <div class="lmr-pro-wrap">

                                <div class="lmr-pro-head">
                                    <h2>Last Minute Deals</h2>
                                    <p>Book a professional chef before the timer runs out</p>
                                </div>

                                <div class="lmr-pro-grid">

                                    <!-- CARD -->
                                    <div class="lmr-pro-card">
                                        <span class="lmr-pro-tag">🔥 Hot Deal</span>

                                        <h3>Private Chef – Dinner</h3>
                                        <p>Perfect for romantic dinners & family gatherings.</p>

                                        <!-- TIMER -->
                                        <div class="lmr-timer">
                                            <div>
                                                <span id="h1">02</span>
                                                <small>Hours</small>
                                            </div>
                                            <div>
                                                <span id="m1">45</span>
                                                <small>Min</small>
                                            </div>
                                            <div>
                                                <span id="s1">30</span>
                                                <small>Sec</small>
                                            </div>
                                        </div>

                                        <div class="lmr-price">
                                            <del>₹5,999</del>
                                            <strong>₹4,299</strong>
                                        </div>

                                        <a href="#" class="lmr-cta">Book Now</a>
                                    </div>

                                    <!-- CARD -->
                                    <div class="lmr-pro-card">
                                        <span class="lmr-pro-tag orange">⏳ Ending Soon</span>

                                        <h3>Birthday Party Chef</h3>
                                        <p>Live cooking experience for special celebrations.</p>

                                        <div class="lmr-timer">
                                            <div><span>05</span><small>Hours</small></div>
                                            <div><span>10</span><small>Min</small></div>
                                            <div><span>55</span><small>Sec</small></div>
                                        </div>

                                        <div class="lmr-price">
                                            <del>₹8,499</del>
                                            <strong>₹6,499</strong>
                                        </div>

                                        <a href="#" class="lmr-cta">Book Now</a>
                                    </div>

                                </div>

                            </div>
                        </section>



                        <section class="dishes-section ">
                            <img class="sticker-img" src="assets/images/banner/side-sticker.png" alt="">
                            <div class="dishes-header">
                                <h2>Worldwide Cuisines</h2>
                                <p>Choose from <span>15+ cuisines</span> and <span>500+ dishes</span></p>
                            </div>

                            <div class="dishes-grid">
                                <?php
                                foreach ($cuisines as $cus) {
                                    ?>
                                    <div class="dishes-card">
                                        <a href="<?= $BASE_URL ?>cuisines/<?= $cus['slug_url'] ?>">
                                            <img src="<?= $BASE_URL ?>admin/assets/img/uploads/<?= $cus['pro_img'] ?>"
                                                alt="North Indian">
                                            <h4><?= $cus['pro_name'] ?></h4>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>


                            </div>
                        </section>
                        <div class="vc_row-full-width vc_clearfix"></div>

                    </article>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php' ?>


</body>

</html>
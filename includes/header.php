<?php
include_once(__DIR__ . "/../config/connect.php");
include_once(__DIR__ . "/../util/function.php");

$contact = contact_us();
$header_logo = get_header_logo();
$dark_logo = get_footer_logo();
$banner = fetch_banner();
$sub_cat = get_sub_category();
$cuisines = get_cuisines();
$ocassion = get_occasions();
?>
<header class="cap-navbar">
    <div class="cap-nav-container">

        <!-- LOGO -->
        <div class="cap-logo">
            <a href="index.php">
                <img src="<?= $BASE_URL . $header_logo ?>" class="cap-logo-white" alt="Chef At Partner">
                <img src="<?= $BASE_URL . $dark_logo ?>" class="cap-logo-dark" alt="Chef At Partner">
            </a>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-5">
            <nav class="cap-nav-menu">
                <a href="<?= $BASE_URL ?>">Home</a>
                <a href="<?= $BASE_URL ?>about-us.php">About</a>
                <a href="<?= $BASE_URL ?>booking.php">Bookings</a>
                <a href="<?= $BASE_URL ?>services.php">Services</a>
                <a href="<?= $BASE_URL ?>contact-us.php">Contact Us</a>
            </nav>

            <!-- SEARCH -->
            <div class="cap-search py-0">
                <input class="py-0" style="max-height: 40px;" type="text" placeholder="Search here...">
                <button>
                    <i class="fa fa-search"></i>
                </button>
            </div>
            <div class="cap-cta">
                <a href="<?= $BASE_URL ?>signup.php">Sign Up</a>
            </div>
        </div>

        <!-- DESKTOP MENU -->
        <div class="mobile-main-menu-toggle" id="mobileMenuToggle">
            <i class="fa fa-bars "></i>
        </div>

        <div class="mobile-sidebar" id="mobileSidebar">
            <div class="mobile-sidebar-header">
                <img src="<?= $BASE_URL . $header_logo ?>" alt="Logo">
                <span id="closeSidebar">&times;</span>
            </div>

            <ul class="mobile-menu">
                <li><a href="<?= $BASE_URL ?>">Home</a></li>
                <li><a href="<?= $BASE_URL ?>about-us.php">About</a></li>
                <li><a href="<?= $BASE_URL ?>gallery.php">Gallery</a></li>
                <li><a href="<?= $BASE_URL ?>services.php">Services</a></li>
                <li><a href="<?= $BASE_URL ?>contact.php">Contact Us</a></li>
                <li class="highlight"><a href="reservation.html">Reservation</a></li>
            </ul>

            <!-- LOGIN BUTTON -->
            <div class="mobile-sidebar-login">
                <a href="<?= $BASE_URL ?>login.php" class="mobile-login-btn">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            </div>
        </div>



    </div>
</header>
<!-- RIGHT SIDE FLOATING BUTTONS -->
<!-- Floating Contact Buttons -->
<div>
    <div class="gfabx-grid-bg"></div>

    <div class="gfabx-container">
        <div class="gfabx-fab" id="gfabx-fab">
            <i class="fa fa-plus" aria-hidden="true"></i>

        </div>

        <div class="gfabx-menu" id="gfabx-menu">
            <div class="gfabx-item" data-game="galaxy">
                <a href="tel:9454168429">
                    <button class="gfabx-item-text">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        Call Us
                    </button>
                </a>
                <!-- <div class="gfabx-item-btn"></div> -->
            </div>
            <div class="gfabx-item" data-game="racer">
                <a href="https://wa.me/9454168429">
                    <button class="gfabx-item-text">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        Whatsapp
                    </button>
                </a>
                <!-- <div class="gfabx-item-btn"></div> -->
            </div>
            <div class="gfabx-item" data-game="quest">
                <a href="booking.php">
                    <button class="gfabx-item-text">
                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>Book a Chef</button>
                </a>

            </div>
            <div class="gfabx-item " data-game="arena">
                <a href="contact-us.php">
                    <button class="gfabx-item-text">
                        <i class="fas fa-envelope-open-text"></i>Enquiry Now</button>
                </a>
                <!-- <div class="gfabx-item-btn"></div> -->
            </div>
        </div>
    </div>

    <div class="gfabx-preview" id="gfabx-preview">
        <div class="gfabx-box">
            <div class="gfabx-close" id="gfabx-close">✕</div>
            <h2 class="gfabx-title" id="gfabx-title"></h2>
            <p class="gfabx-desc" id="gfabx-desc"></p>
        </div>
    </div>
</div>
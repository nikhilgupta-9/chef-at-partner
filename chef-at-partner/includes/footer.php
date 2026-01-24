<?php
include_once(__DIR__ . "/../config/connect.php");
include_once(__DIR__ . "/../util/function.php");

$contact = contact_us();

?>
<style>
    .one-row-menu {
        display: flex;
        flex-wrap: wrap;
        max-width: 100%;
        /* force single row */
        gap: 1px 18px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .one-row-menu li {
        flex-shrink: 0;
        margin-bottom: 5px;
        /* prevent breaking */
    }
</style>

<div class="container-fluid footer-wrapper">
    <div class="row">
        <div class="footer-sidebar-wrapper footer-sidebar-style-dark" data-style="">
            <div class="footer-sidebar sidebar container footer-sidebar-col-4">
                <ul id="footer-sidebar" class="clearfix">

                    <li id="nav_menu-2" class="widget widget_nav_menu">
                        <h2 class="widgettitle">Company</h2>
                        <div class="menu-footermenu-1-container">
                            <ul id="menu-footermenu-1" class="menu">
                                <li class="menu-item"><a href="<?php $BASE_URL ?>">Home</a></li>
                                <li class="menu-item"><a href="gallery.php">Gallery</a></li>
                                <li class="menu-item"><a href="services.php">Services</a></li>
                                <li class="menu-item"><a href="contact-us.php">Contact Us</a></li>
                                <li class="menu-item"><a href="booking.php">Bookings</a></li>
                            </ul>
                        </div>
                    </li>
                    <li id="nav_menu-2" class="widget widget_nav_menu">
                        <h2 class="widgettitle">For Customer</h2>
                        <div class="menu-footermenu-1-container">
                            <ul id="menu-footermenu-1" class="menu">
                                <li class="menu-item"><a href="signup.php">Sign up / Login</a></li>
                                <li class="menu-item"><a href="about-us.php">About Us</a></li>
                                <li class="menu-item"><a href="term-and-conditions.php">Term & Conditions</a></li>
                                <li class="menu-item"><a href="privacy-policy.php">Privacy Policy</a></li>
                                <li class="menu-item"><a href="cancellation-policy.php">Cancellation Policy</a></li>
                                <!-- <li class="menu-item"><a href="#">Gift Vouchers</a></li> -->
                            </ul>
                        </div>
                    </li>
                    <li id="barrel-recent-posts-3" class="widget widget_barrel_recent_entries">
                        <h2 class="widgettitle">For Partners</h2>
                        <ul>
                            <li class="menu-item"><a href="<?= $BASE_URL ?>partner/partner-register.php">Register as
                                    Partner</a></li>
                            <li class="menu-item"><a href="91<?= $contact['phone'] ?>"><?= $contact['phone'] ?></a></li>
                        </ul>
                    </li>
                    <li id="custom_html-2" class="widget_text widget widget_custom_html">
                        <h2 class="widgettitle">Reseravations</h2>
                        <div class="textwidget custom-html-widget">
                            <p>Our support available to help you 24 hours a day, seven days a week.</p>
                            <p>
                                Please Call: <?= $contact['phone'] ?><br />
                                For more information
                                <span class="text-color-theme">
                                    <a href="mailto:<?= $contact['email'] ?>" class="__cf_email__">
                                        <?= $contact['email'] ?></a>
                                </span>
                            </p>
                            <p><a href="#">Book A Table</a></p>
                            <div class="widget_barrel_social_icons shortcode_barrel_social_icons">
                                <div class="social-icons-wrapper">
                                    <ul class="social-links">
                                        <li>
                                            <a href="<?= $contact['facebook'] ?>" target="_blank" class="a-facebook">
                                                <i class="fa-brands fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= $contact['twitter'] ?>" target="_blank" class="a-twitter">
                                                <i class="fa-brands fa-x-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= $contact['instagram'] ?>" target="_blank" class="a-instagram">
                                                <i class="fa-brands fa-instagram"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://youtube.com/" target="_blank" class="a-dribbble">
                                                <i class="fa-brands fa-youtube"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= $contact['linkdin'] ?>" target="_blank" class="a-pinterest">
                                                <i class="fa-brands fa-linkedin"></i>
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </li>
                    <li id="nav_menu-2" class="widget widget_nav_menu" style="width: 100%;">
                        <h2 class="widgettitle">Services</h2>
                        <div class="menu-footermenu-1-container">
                            <ul class="one-row-menu">
                                <li>Cook and Chef Service</li>
                                <li>Catering Service</li>
                                <li>Bartender Service</li>
                                <li>Waiter Service</li>
                                <li>Cleaner Service</li>
                                <li>Occasions</li>
                                <li>Cuisines</li>
                                <li>Menu</li>
                                <li>Gallery</li>
                                <li>Customer Reviews</li>
                                <li>Top Rated Chefs</li>
                                <li>Top Rated Bartenders</li>
                                <li>Top Rated Waiters</li>
                                <li>Top Rated Cleaners</li>
                            </ul>

                        </div>
                    </li>
                    <li id="nav_menu-2" class="widget widget_nav_menu" style="width: 100%;">
                        <h2 class="widgettitle">Serving In</h2>
                        <div class="menu-footermenu-1-container">
                            <ul class="one-row-menu">
                                <li>Delhi</li>
                                <li>Noida</li>
                                <li>Gurugram</li>
                                <li>Faridabad</li>
                                <li>Ghaziabad</li>
                                <li>Greater Noida</li>
                                <li>Mumbai</li>
                                <li>Navi Mumbai</li>
                                <li>Thane</li>
                                <li>Pune</li>
                                <li>Bengaluru</li>
                                <li>Hyderabad</li>
                                <li>Ahmedabad</li>
                                <li>Jaipur</li>
                                <li>Kolkata</li>
                                <li>Chandigarh</li>
                                <li>Zirakpur</li>
                                <li>Panchkula</li>
                                <li>Lucknow</li>
                                <li>Vadodara</li>
                                <li>Surat</li>
                                <li>Chennai</li>
                                <li>Indore</li>
                                <li>Goa</li>
                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
        <!-- <div class="row">
            <div class="footer-sidebar-wrapper footer-sidebar-style-dark pt-0" data-style="">
                <div class="footer-sidebar sidebar container footer-sidebar-col-4">
                    <ul id="footer-sidebar" class="clearfix">

                        <li id="nav_menu-2" class="widget widget_nav_menu">
                            <h2 class="widgettitle">Company</h2>
                            <div class="menu-footermenu-1-container">
                                <ul id="menu-footermenu-1" class="menu">
                                    <li class="menu-item"><a href="<?php $BASE_URL ?>">Home</a></li>
                                    <li class="menu-item"><a href="gallery.php">Gallery</a></li>
                                    <li class="menu-item"><a href="services.php">Services</a></li>
                                    <li class="menu-item"><a href="contact-us.php">Contact Us</a></li>
                                    <li class="menu-item"><a href="booking.php">Bookings</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div> -->
        <footer class="footer-style-dark footer-col-2">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 footer-copyright">Powered by <a href="" target="_blank"
                            rel="noopener noreferrer">@ Chef At Partner - All right Reserved</a></div>
                    <div class="col-md-6 footer-menu">
                        <div class="menu-footermenu-simple-container">
                            <ul id="menu-footermenu-simple" class="footer-menu">
                                <li class="menu-item"><a href="#">PERMISSIONS AND COPYRIGHT</a></li>
                                <li class="menu-item"><a href="contact-us.php">CONTACT THE TEAM</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <a class="scroll-to-top" href="#top"></a>
        </footer>
    </div>
</div>

<!-- MOBILE SIDEBAR -->



<div class="modal fade" id="chooseServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <button type="button" class="btn btn-link p-0 me-2" data-bs-dismiss="modal">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <h5 class="modal-title mx-auto">Choose Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-4 text-center">

                    <!-- Service Item -->
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/chef.svg" alt="">
                            <p>Cooks & Chefs</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/bartender.svg" alt="">
                            <p>Bartenders</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/waiter.svg" alt="">
                            <p>Waiters</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/cleaner.svg" alt="">
                            <p>Cleaners</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/ingredients.svg" alt="">
                            <p>Ingredients Delivery</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/appliance.svg" alt="">
                            <p>Appliances on Rent</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/crockery.svg" alt="">
                            <p>Crockery on Rent</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="service-option">
                            <img src="icons/decor.svg" alt="">
                            <p>Party Decorator</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<style>
    .service-option {
        background: #f6f6f6;
        border-radius: 14px;
        padding: 30px 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .service-option:hover {
        background: #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .service-option img {
        height: 60px;
        margin-bottom: 12px;
    }

    .service-option p {
        font-size: 14px;
        font-weight: 500;
        margin: 0;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= $BASE_URL ?>js/jquery.js"></script>
<script src="<?= $BASE_URL ?>js/jquery-migrate.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/responsive-lightbox/assets/nivo/nivo-lightbox.min.js"></script>
<script>
    /* <![CDATA[ */
    var rlArgs = {
        script: "nivo",
        selector: "lightbox",
        customEvents: "",
        activeGalleries: "1",
        effect: "fade",
        clickOverlayToClose: "1",
        keyboardNav: "1",
        errorMessage: "The requested content cannot be loaded. Please try again later.",
    };
    /* ]]> */
</script>
<script src="<?= $BASE_URL ?>js/plugins/responsive-lightbox/js/frontd.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>
<script
    src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/extensions/revolution.extension.slideanims.min.js"></script>
<script
    src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script
    src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/extensions/revolution.extension.navigation.min.js"></script>
<script
    src="<?= $BASE_URL ?>js/plugins/revslider/public/assets/js/extensions/revolution.extension.actions.min.js"></script>

<script>
    (function ($) {
        $(document).ready(function () {
            $("body").addClass("transparent-header");
        });
    })(jQuery);
</script>

<script src="js/plugins/js-skin.js"></script>
<script>
    const navbar = document.querySelector(".cap-navbar");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 80) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
</script>

<script>
    function setREVStartSize(e) {
        try {
            var i = jQuery(window).width(),
                t = 9999,
                r = 0,
                n = 0,
                l = 0,
                f = 0,
                s = 0,
                h = 0;
            if (
                (e.responsiveLevels &&
                    (jQuery.each(e.responsiveLevels, function (e, f) {
                        f > i && ((t = r = f), (l = e)), i > f && f > r && ((r = f), (n = e));
                    }),
                        t > r && (l = n)),
                    (f = e.gridheight[l] || e.gridheight[0] || e.gridheight),
                    (s = e.gridwidth[l] || e.gridwidth[0] || e.gridwidth),
                    (h = i / s),
                    (h = h > 1 ? 1 : h),
                    (f = Math.round(h * f)),
                    "fullscreen" == e.sliderLayout)
            ) {
                var u = (e.c.width(), jQuery(window).height());
                if (void 0 != e.fullScreenOffsetContainer) {
                    var c = e.fullScreenOffsetContainer.split(",");
                    if (c)
                        jQuery.each(c, function (e, i) {
                            u = jQuery(i).length > 0 ? u - jQuery(i).outerHeight(!0) : u;
                        }),
                            e.fullScreenOffset.split("%").length > 1 && void 0 != e.fullScreenOffset && e.fullScreenOffset.length > 0
                                ? (u -= (jQuery(window).height() * parseInt(e.fullScreenOffset, 0)) / 100)
                                : void 0 != e.fullScreenOffset && e.fullScreenOffset.length > 0 && (u -= parseInt(e.fullScreenOffset, 0));
                }
                f = u;
            } else void 0 != e.minHeight && f < e.minHeight && (f = e.minHeight);
            e.c.closest(".rev_slider_wrapper").css({ height: f });
        } catch (d) {
            console.log("Failure at Presize of Slider:" + d);
        }
    }
</script>

<script>
    function revslider_showDoubleJqueryError(sliderID) {
        var errorMessage = "Revolution Slider Error: You have some jquery.js library include that comes after the revolution files js include.";
        errorMessage += "<br> This includes make eliminates the revolution slider libraries, and make it not work.";
        errorMessage += "<br><br> To fix it you can:<br>&nbsp;&nbsp;&nbsp; 1. In the Slider Settings -> Troubleshooting set option:  <strong><b>Put JS Includes To Body</b></strong> option to true.";
        errorMessage += "<br>&nbsp;&nbsp;&nbsp; 2. Find the double jquery.js include and remove it.";
        errorMessage = "<span style='font-size:16px;color:#BC0C06;'>" + errorMessage + "</span>";
        jQuery(sliderID).show().html(errorMessage);
    }
</script>

<script>
    /* <![CDATA[ */
    var thickboxL10n = {
        next: "Next >",
        prev: "< Prev",
        image: "Image",
        of: "of",
        close: "Close",
        noiframes: "This feature requires inline frames. You have iframes disabled or your browser does not support them.",
        loadingAnimation: "http:\/\/wp.magnium-themes.com\/barrel\/barrel-1\/wp-includes\/js\/thickbox\/loadingAnimation.gif",
    };
    /* ]]> */
</script>
<script src="<?= $BASE_URL ?>js/plugins/thickbox/thickbox.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/bootstrap.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/easing.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/select2/select2.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/owl-carousel/owl.carousel.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/jquery.nanoscroller.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/jquery.mixitup.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/TweenMax.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/template.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/js/dist/js_composer_front.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/lib/waypoints/waypoints.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/jquery.appear.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/jquery.countTo.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/lib/bower/skrollr/dist/skrollr.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/lib/vc_accordion/vc-accordion.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/lib/vc-tta-autoplay/vc-tta-autoplay.min.js"></script>
<script src="<?= $BASE_URL ?>js/plugins/js_composer/assets/lib/vc_tabs/vc-tabs.min.html"></script>
<script src="<?= $BASE_URL ?>js/plugins/slick.min.html"></script>



<script>
    var htmlDiv = document.getElementById("rs-plugin-settings-inline-css");
    var htmlDivCss = "";
    if (htmlDiv) {
        htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
    } else {
        var htmlDiv = document.createElement("div");
        htmlDiv.innerHTML = "<style>" + htmlDivCss + "</style>";
        document.getElementsByTagName("head")[0].appendChild(htmlDiv.childNodes[0]);
    }
</script>
<script>
    setREVStartSize({
        c: jQuery("#rev_slider_2_1"),
        responsiveLevels: [1240, 1240, 1240, 480],
        gridwidth: [1240, 1240, 1240, 480],
        gridheight: [868, 868, 868, 720],
        sliderLayout: "fullscreen",
        fullScreenAutoWidth: "on",
        fullScreenAlignForce: "off",
        fullScreenOffsetContainer: "",
        fullScreenOffset: "",
    });

    var revapi2,
        tpj = jQuery;

    tpj(document).ready(function () {
        if (tpj("#rev_slider_2_1").revolution == undefined) {
            revslider_showDoubleJqueryError("#rev_slider_2_1");
        } else {
            revapi2 = tpj("#rev_slider_2_1")
                .show()
                .revolution({
                    sliderType: "standard",
                    jsFileLocation: "js/plugins/revslider/public/assets/js/",
                    sliderLayout: "fullscreen",
                    dottedOverlay: "none",
                    delay: 9000,
                    navigation: {
                        keyboardNavigation: "off",
                        keyboard_direction: "horizontal",
                        mouseScrollNavigation: "off",
                        mouseScrollReverse: "default",
                        onHoverStop: "off",
                        touch: {
                            touchenabled: "on",
                            touchOnDesktop: "off",
                            swipe_threshold: 75,
                            swipe_min_touches: 1,
                            swipe_direction: "horizontal",
                            drag_block_vertical: false,
                        },
                        bullets: {
                            enable: true,
                            hide_onmobile: true,
                            hide_under: 750,
                            style: "uranus",
                            hide_onleave: false,
                            direction: "horizontal",
                            h_align: "right",
                            v_align: "bottom",
                            h_offset: 50,
                            v_offset: 50,
                            space: 10,
                            tmp: '<span class="tp-bullet-inner"></span>',
                        },
                    },
                    responsiveLevels: [1240, 1240, 1240, 480],
                    visibilityLevels: [1240, 1240, 1240, 480],
                    gridwidth: [1240, 1240, 1240, 480],
                    gridheight: [868, 868, 868, 720],
                    lazyType: "none",
                    shadow: 0,
                    spinner: "spinner0",
                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,
                    shuffle: "off",
                    autoHeight: "off",
                    fullScreenAutoWidth: "on",
                    fullScreenAlignForce: "off",
                    fullScreenOffsetContainer: "",
                    fullScreenOffset: "",
                    disableProgressBar: "on",
                    hideThumbsOnMobile: "off",
                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    debugMode: false,
                    fallbacks: {
                        simplifyAll: "off",
                        nextSlideOnWindowFocus: "off",
                        disableFocusListener: false,
                    },
                });
        }
    }); /*ready*/
</script>
<script>
    var htmlDivCss = unescape(
        "%23rev_slider_2_1%20.uranus%20.tp-bullet%7B%0A%20%20border-radius%3A%2050%25%3B%0A%20%20box-shadow%3A%200%200%200%202px%20rgba%28255%2C%20255%2C%20255%2C%200%29%3B%0A%20%20-webkit-transition%3A%20box-shadow%200.3s%20ease%3B%0A%20%20transition%3A%20box-shadow%200.3s%20ease%3B%0A%20%20background%3Atransparent%3B%0A%20%20width%3A15px%3B%0A%20%20height%3A15px%3B%0A%7D%0A%23rev_slider_2_1%20.uranus%20.tp-bullet.selected%2C%0A%23rev_slider_2_1%20.uranus%20.tp-bullet%3Ahover%20%7B%0A%20%20box-shadow%3A%200%200%200%202px%20rgba%28255%2C%20255%2C%20255%2C1%29%3B%0A%20%20border%3Anone%3B%0A%20%20border-radius%3A%2050%25%3B%0A%20%20background%3Atransparent%3B%0A%7D%0A%0A%23rev_slider_2_1%20.uranus%20.tp-bullet-inner%20%7B%0A%20%20-webkit-transition%3A%20background-color%200.3s%20ease%2C%20-webkit-transform%200.3s%20ease%3B%0A%20%20transition%3A%20background-color%200.3s%20ease%2C%20transform%200.3s%20ease%3B%0A%20%20top%3A%200%3B%0A%20%20left%3A%200%3B%0A%20%20width%3A%20100%25%3B%0A%20%20height%3A%20100%25%3B%0A%20%20outline%3A%20none%3B%0A%20%20border-radius%3A%2050%25%3B%0A%20%20background-color%3A%20rgb%28255%2C%20255%2C%20255%29%3B%0A%20%20background-color%3A%20rgba%28255%2C%20255%2C%20255%2C%200.3%29%3B%0A%20%20text-indent%3A%20-999em%3B%0A%20%20cursor%3A%20pointer%3B%0A%20%20position%3A%20absolute%3B%0A%7D%0A%0A%23rev_slider_2_1%20.uranus%20.tp-bullet.selected%20.tp-bullet-inner%2C%0A%23rev_slider_2_1%20.uranus%20.tp-bullet%3Ahover%20.tp-bullet-inner%7B%0A%20transform%3A%20scale%280.4%29%3B%0A%20-webkit-transform%3A%20scale%280.4%29%3B%0A%20background-color%3Argb%28255%2C%20255%2C%20255%29%3B%0A%7D%0A"
    );
    var htmlDiv = document.getElementById("rs-plugin-settings-inline-css");
    if (htmlDiv) {
        htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
    } else {
        var htmlDiv = document.createElement("div");
        htmlDiv.innerHTML = "<style>" + htmlDivCss + "</style>";
        document.getElementsByTagName("head")[0].appendChild(htmlDiv.childNodes[0]);
    }
</script>
<script>
    (function ($) {
        $(document).ready(function () {
            function initPortfolioCarousel() {
                $("#portfolio-list-76440791").owlCarousel({
                    items: 1,
                    slideSpeed: 200,
                    itemsDesktop: [1199, 1],
                    itemsDesktopSmall: [979, 1],
                    itemsTablet: [768, 1],
                    itemsMobile: [479, 1],
                    autoPlay: true,
                    navigation: true,
                    navigationText: false,
                    pagination: false,
                    afterInit: function (elem) {
                        $(this).css("display", "block");
                    },
                });
            }

            setTimeout(initPortfolioCarousel, 1000);
        });
    })(jQuery);
</script>
<script>
    // Add interactive hover effects
    document.querySelectorAll('.book-btn').forEach(button => {
        button.addEventListener('click', function () {
            const serviceTitle = this.closest('.service-card').querySelector('.service-title').textContent;
            alert(`Thank you for your interest in our ${serviceTitle} service! A booking representative will contact you shortly.`);

            // Animation effect
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });

    // Add subtle animation on page load
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.service-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 150);
        });
    });
</script>
<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper(".header-banner", {
        pagination: {
            el: ".swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        loop: true,
        slidesPerView: 1,


        autoplay: {
            delay: 3000,        // 3 seconds
            disableOnInteraction: false,
        },
    });
</script>
<script>
    let totalSeconds = 9930;

    const hEl = document.getElementById("h1");
    const mEl = document.getElementById("m1");
    const sEl = document.getElementById("s1");

    if (hEl && mEl && sEl) {
        setInterval(() => {
            if (totalSeconds <= 0) return;

            totalSeconds--;

            let h = Math.floor(totalSeconds / 3600);
            let m = Math.floor((totalSeconds % 3600) / 60);
            let s = totalSeconds % 60;

            hEl.innerText = String(h).padStart(2, '0');
            mEl.innerText = String(m).padStart(2, '0');
            sEl.innerText = String(s).padStart(2, '0');
        }, 1000);
    }

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const track = document.querySelector('.professional-track');
        const cards = document.querySelectorAll('.professional-card');

        if (!track || cards.length === 0) {
            return; // ✅ now legal
        }

        const cardWidth = cards[0].offsetWidth + 30;
        let index = 0;

        cards.forEach(card => {
            const clone = card.cloneNode(true);
            track.appendChild(clone);
        });

        const totalCards = track.children.length;

        function autoSlide() {
            index++;
            track.style.transition = "transform 0.6s ease-in-out";
            track.style.transform = `translateX(-${index * cardWidth}px)`;

            if (index >= totalCards / 2) {
                setTimeout(() => {
                    track.style.transition = "none";
                    index = 0;
                    track.style.transform = "translateX(0)";
                }, 700);
            }
        }

        setInterval(autoSlide, 2500);

    });


    function navSearch() {
        const value = document.getElementById("navSearchInput").value.trim();
        if (value !== "") {
            alert("Searching for: " + value);
            // yahan redirect ya filter logic laga sakte ho
            // window.location.href = "search.html?q=" + value;
        }
    }
</script>
<script>
    const fab = document.getElementById('gfabx-fab');
    const menu = document.getElementById('gfabx-menu');
    const items = document.querySelectorAll('.gfabx-item');
    const preview = document.getElementById('gfabx-preview');
    const title = document.getElementById('gfabx-title');
    const desc = document.getElementById('gfabx-desc');
    const close = document.getElementById('gfabx-close');

    const data = {
        galaxy: { title: 'Galaxy Defenders', desc: 'Interstellar tournament action.' },
        racer: { title: 'Pixel Racer', desc: 'Fast neon multiplayer racing.' },
        quest: { title: 'Crypto Quest', desc: 'Dungeon raids & blockchain loot.' },
        arena: { title: 'Neon Arena', desc: '5v5 cyberpunk brawler.' }
    };

    fab.onclick = () => {
        fab.classList.toggle('active');
        menu.classList.toggle('active');
        items.forEach((i, idx) => setTimeout(() => i.classList.toggle('show'), idx * 60));
    };


    close.onclick = () => preview.classList.remove('show');
</script>
<script>
    const openBtn = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileOverlay');
    const closeBtn = document.getElementById('closeSidebar');

    openBtn.addEventListener('click', () => {
        sidebar.classList.add('active');
        overlay.classList.add('active');
    });

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');

        // FORCE hamburger back
        openBtn.style.display = 'block';
    }

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>
<script>
    document.addEventListener("click", function (e) {
        const toggle = document.getElementById("mobileMenuToggle");
        if (!toggle) return;

        // Remove injected close icon
        const closeIcon = toggle.querySelector(".pe-7s-close");
        if (closeIcon) {
            closeIcon.remove();
        }

        // Ensure hamburger icon exists
        if (!toggle.querySelector(".fa-bars")) {
            toggle.innerHTML = '<i class="fa fa-bars fixed-ham"></i>';
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const navbar = document.querySelector(".cap-navbar");

        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });

    });
</script>
<?php
include_once(__DIR__ . '/config/connect.php');
include_once(__DIR__ . '/util/function.php');

$gallery = get_gallery();
?>
<!DOCTYPE html>
<html lang="en-US">


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title>Gallery | CHEF AT PARTNER</title>
    <?php include_once 'links.php' ?>


</head>

<body class="home page-template-default page page-id-3699 wpb-js-composer js-comp-ver-5.2.1 vc_responsive">

    <?php include_once 'includes/header.php' ?>

    <!-- content block -->
    <div class="content-block">
        <div class="container-bg with-bg container-fluid" data-style="background-image: url(upload/pumpkin-soup.jpg);">
            <div class="container-bg-overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-item-title">
                                <h1 class="text-center texttransform-none">Gallery</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-container container">
            <div class="row">
                <div class="col-md-12 entry-content">
                    <article>
                        <div
                            class="vc_row wpb_row vc_row-fluid vc_custom_1502041734192 vc_row-o-equal-height vc_row-flex">
                            <div class="wpb_column vc_column_container vc_col-sm-3">
                                <div class="vc_column-inner vc_custom_1487239752272">
                                    <div class="wpb_wrapper"></div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-6">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-header-block clearfix text-center text-black wpb_animate_when_almost_visible wpb_fadeInDown fadeInDown wpb_content_element mgt-header-block-style-2 mgt-header-block-fontsize-medium mgt-header-texttransform-none mgt-header-block-43612531">
                                            <p class="mgt-header-block-subtitle">Gallery</p>
                                            <h2 class="mgt-header-block-title text-font-weight-default">Moments We
                                                Create</h2>
                                            <div class="mgt-header-line mgt-header-line-margin-large"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-3">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper"></div>
                                </div>
                            </div>
                        </div>

                        <div class="gallery-grid" id="chefGallery">

                            <?php
                            foreach ($gallery as $g) {
                                ?>
                                <div class="gallery-item">
                                    <img src="<?= $site . $g ?>" alt="Gallery Image">
                                    <span class="view-icon">
                                        <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                                    </span>
                                </div>
                            <?php } ?>



                        </div>


                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1502044403439">
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                                <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-82901659"
                                                    data-style="background-color: #f5f5f5;background-image: url(assets/images/contact-us/contact-img-1.jpg);background-repeat: no-repeat;height: 300px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside">
                                                            <h5 style="text-align: center;">Hungry?</h5>
                                                            <h2 style="text-align: center;">
                                                                Book A<br />
                                                                Chef
                                                            </h2>
                                                            <div
                                                                class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                                <a class="btn hvr-push mgt-button-icon- mgt-button mgt-style-solid mgt-size-large mgt-align-center mgt-display-newline mgt-text-size-small mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                    href="reservation.html">
                                                                    Book A chef
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
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                                <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-66330768"
                                                    data-style="background-color: #f5f5f5;background-image: url(assets/images/contact-us/contact-img-2.webp);background-repeat: no-repeat;height: 300px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside">
                                                            <h5 style="text-align: center;">History</h5>
                                                            <h2 style="text-align: center;">
                                                                The finest traditions from the heart of India
                                                            </h2>
                                                            <div
                                                                class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                                <a class="btn hvr-grow mgt-button-icon- mgt-button mgt-style-solid mgt-size-large mgt-align-center mgt-display-newline mgt-text-size-small mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                    href="about-us-restaurant.html">
                                                                    About us
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
                            <div class="wpb_column vc_column_container vc_col-sm-4">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div class="mgt-promo-block-container wpb_content_element">
                                            <div class="mgt-promo-block-wrapper mgt-promo-block-hover">
                                                <div class="mgt-promo-block animated white-text cover-image darken mgt-promo-block-75835486"
                                                    data-style="background-color: #f5f5f5;background-image: url(upload/barmen-coctail.jpg);background-repeat: no-repeat;height: 300px;">
                                                    <div class="mgt-promo-block-content va-middle">
                                                        <div class="mgt-promo-block-content-inside">
                                                            <h5 style="text-align: center;">Menus</h5>
                                                            <h2 style="text-align: center;">
                                                                Start your<br />
                                                                experience today
                                                            </h2>
                                                            <div
                                                                class="mgt-button-wrapper mgt-button-wrapper-align-center mgt-button-wrapper-display-newline mgt-button-top-margin-true mgt-button-right-margin-false mgt-button-round-edges-full">
                                                                <a class="btn hvr-grow mgt-button-icon- mgt-button mgt-style-solid mgt-size-large mgt-align-center mgt-display-newline mgt-text-size-small mgt-button-icon-separator- mgt-button-icon-position-left text-font-weight-default mgt-text-transform-none"
                                                                    href="the-menu.html">
                                                                    Our Menu
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

                    </article>
                </div>
            </div>
        </div>
    </div>
    <div class="lightbox" id="lightbox">
        <span class="lightbox-close">&times;</span>
        <span class="lightbox-btn lightbox-prev">&#10094;</span>
        <img src="" alt="Full Image">
        <span class="lightbox-btn lightbox-next">&#10095;</span>
    </div>



    <?php include_once 'includes/footer.php' ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const galleryItems = document.querySelectorAll("#chefGallery .gallery-item");
            const lightbox = document.getElementById("lightbox");
            const lightboxImg = lightbox.querySelector("img");

            let currentIndex = 0;

            galleryItems.forEach((item, index) => {
                item.addEventListener("click", function () {
                    const img = item.querySelector("img");
                    currentIndex = index;
                    openLightbox(img.src);
                });
            });

            function openLightbox(src) {
                lightboxImg.src = src;
                lightbox.classList.add("active");
            }

            function changeImage(step) {
                currentIndex += step;

                if (currentIndex < 0) currentIndex = galleryItems.length - 1;
                if (currentIndex >= galleryItems.length) currentIndex = 0;

                lightboxImg.src =
                    galleryItems[currentIndex].querySelector("img").src;
            }

            document.querySelector(".lightbox-prev").onclick = () => changeImage(-1);
            document.querySelector(".lightbox-next").onclick = () => changeImage(1);
            document.querySelector(".lightbox-close").onclick = () =>
                lightbox.classList.remove("active");

            lightbox.addEventListener("click", function (e) {
                if (e.target === lightbox) {
                    lightbox.classList.remove("active");
                }
            });

        });
    </script>




</body>


</html>
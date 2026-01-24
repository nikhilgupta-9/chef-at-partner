<?php include_once('conn/config.php')?>
<!DOCTYPE html>
<html lang="en-US">


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title>CHEF AT PARTNER</title>

    <!-- <link rel="stylesheet" href="css/pe-icon-7-stroke.css" type="text/css" media="all" /> -->
    <?php include_once 'links.php' ?>


</head>




<body class="home page-template-default page page-id-3699 wpb-js-composer js-comp-ver-5.2.1 vc_responsive">
    
    <?php include_once 'includes/header.php' ?>

    <!-- main content -->

    <section class="auth-section container">
    <div class="row auth-card w-100 ">

        <div class="col-md-6 d-none d-md-block auth-image"
             style="background-image:url('assets/images/partner-login.jpeg');">
        </div>

        <div class="col-md-6 auth-form">
            <h2>Partner Login</h2>
            <p>Work with Chef At Partner</p>

            <form>
                <input type="email" class="form-control mb-3 bg-white" placeholder="Email">
                <input type="password" class="form-control mb-3" placeholder="Password">

                <button class="btn btn-theme w-100 ">Login</button>

                <p class="mt-3 text-center">
                    New partner? <a href="partner-signup.php" class="auth-link">Join us</a>
                </p>
            </form>
        </div>

    </div>
</section>



    <?php include_once 'includes/footer.php' ?>


</body>

<!-- Mirrored from max-themes.net/demos/b-rest/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Dec 2025 06:33:28 GMT -->

</html>
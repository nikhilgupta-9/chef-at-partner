<?php include_once('conn/config.php') ?>
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
        <div class="row auth-card w-100">

            <div class="col-md-6 auth-form">
                <h2>Join as Service Partner</h2>
                <p>Select your profession & start earning</p>

                <form>
                    <input type="text" class="form-control mb-3" placeholder="Full Name">
                    <input type="email" class="form-control mb-3" placeholder="Email">
                    <input type="tel" class="form-control mb-3" placeholder="Phone">

                    <select class="form-control mb-3 no-select2" style="height: auto; border: 2px solid rgb(199, 160, 125); padding: 0; border-radius: 25px;">
                        <option style="border-radius: 25px;">Select Profession</option>
                        <option>Chef</option>
                        <option>Waiter</option>
                        <option>Bartender</option>
                        <option>Party Staff</option>
                    </select>

                    <button class="btn btn-theme w-100">Apply Now</button>
                </form>
            </div>

            <div class="col-md-6 d-none d-md-block auth-image"
                style="background-image:url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb');">
            </div>

        </div>
    </section>




    <?php include_once 'includes/footer.php' ?>

<script>
    $('select').not('.no-select2').select2();

</script>
</body>

<!-- Mirrored from max-themes.net/demos/b-rest/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Dec 2025 06:33:28 GMT -->

</html>
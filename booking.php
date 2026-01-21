<?php
include_once('config/connect.php');
include_once('util/function.php');

$occasions = get_occasions();
$date = date('Y-m-d');
$min_date = date('Y-m-d');
$max_date = date('Y-m-d', strtotime('+3 months'));
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <title>Booking | CHEF AT PARTNER</title>
    <?php include_once 'links.php' ?>
    <style>
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }

        .required {
            color: red;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input1 {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea.input1 {
            min-height: 120px;
            resize: vertical;
        }
    </style>
</head>

<body class="home page-template-default page page-id-3699 wpb-js-composer js-comp-ver-5.2.1 vc_responsive">

    <?php include_once 'includes/header.php' ?>

    <!-- main content -->

    <div class="content-block">
        <div class="container-bg with-bg container-fluid"
            data-style="background-image: url(assets/images/banner/reservation-hero.png);">
            <div class="container-bg-overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-item-title">
                                <h1 class="text-center texttransform-none">&nbsp;</h1>
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
                        <div data-vc-full-width="true" data-vc-full-width-init="false"
                            class="vc_row wpb_row vc_row-fluid">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="mgt-header-block clearfix text-center text-black wpb_animate_when_almost_visible wpb_fadeInDown fadeInDown wpb_content_element mgt-header-block-style-2 mgt-header-block-fontsize-medium mgt-header-texttransform-none mgt-header-block-38963443">
                                            <p class="mgt-header-block-subtitle">Book our services today</p>
                                            <h2 class="mgt-header-block-title text-font-weight-default">Make a Booking
                                            </h2>
                                            <div class="mgt-header-line mgt-header-line-margin-large"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>

                        <?php
                        // Display success/error messages
                        if (isset($_GET['success'])) {
                            echo '<div class="alert alert-success">Booking submitted successfully!</div>';
                        }
                        if (isset($_GET['error'])) {
                            echo '<div class="alert alert-danger">Error submitting booking. Please try again.</div>';
                        }
                        ?>

                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1501856468228">
                            <div class="wpb_column vc_column_container vc_col-sm-6">
                                <div class="vc_column-inner vc_custom_1502107374699">
                                    <div class="wpb_wrapper">
                                        <div
                                            class="wpb_text_column wpb_content_element wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft text-size-medium">
                                            <div class="wpb_wrapper">
                                                <p><span class="text-color">To book our chef services please use the
                                                        form
                                                        below or call us to secure your booking.</span></p>
                                            </div>
                                        </div>
                                        <div
                                            class="vc_separator wpb_content_element vc_separator_align_center vc_sep_width_100 vc_sep_pos_align_center vc_separator_no_text vc_sep_color_grey wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft">
                                            <span class="vc_sep_holder vc_sep_holder_l"><span
                                                    class="vc_sep_line"></span></span><span
                                                class="vc_sep_holder vc_sep_holder_r"><span
                                                    class="vc_sep_line"></span></span>
                                        </div>
                                        <div
                                            class="wpb_text_column wpb_content_element wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft text-size-medium">
                                            <div class="wpb_wrapper">
                                                <p><strong>Chef Services Available 7 days a week</strong></p>
                                                <p><strong>Service Hours</strong></p>
                                                <p>
                                                    Monday &#8211; Friday: 7.30am &#8211; 12am<br />
                                                    Saturday: 10am &#8211; 1am<br />
                                                    Sunday: 10am &#8211; 12am
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="vc_separator wpb_content_element vc_separator_align_center vc_sep_width_100 vc_sep_pos_align_center vc_separator_no_text vc_sep_color_grey wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft">
                                            <span class="vc_sep_holder vc_sep_holder_l"><span
                                                    class="vc_sep_line"></span></span><span
                                                class="vc_sep_holder vc_sep_holder_r"><span
                                                    class="vc_sep_line"></span></span>
                                        </div>
                                        <div
                                            class="wpb_text_column wpb_content_element wpb_animate_when_almost_visible wpb_fadeInLeft fadeInLeft text-size-medium">
                                            <div class="wpb_wrapper">
                                                <p><span class="text-color">We take bookings for lunch and dinner
                                                        events.
                                                        To make a booking, please call us at (4224) 235 134
                                                        between 10am-6pm, Monday to Friday, or use the form.</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wpb_column vc_column_container vc_col-sm-6 vc_col-has-fill">
                                <div class="vc_column-inner vc_custom_1502107458750">
                                    <div class="wpb_wrapper">
                                        <div class="wpb_text_column wpb_content_element">
                                            <div class="wpb_wrapper">
                                                <h3>Book Your Celebrations</h3>
                                                <p>
                                                    Please fill in all required fields marked with <span
                                                        class="required">*</span><br />
                                                    We will confirm your booking within 24 hours.
                                                </p>
                                            </div>
                                        </div>

                                        <form class="quform" action="<?= $BASE_URL ?>ajax/process_booking.php"
                                            method="post" id="bookingForm">
                                            <div class="quform-elements">

                                                <!-- Occasion Selection -->
                                                <div class="quform-element form-group">
                                                    <label for="occasion">Occasion/Event Type <span
                                                            class="required">*</span></label>
                                                    <select name="occasion" id="occasion" class="input1" required>
                                                        <option value="">Select an occasion</option>
                                                        <?php foreach ($occasions as $occasion): ?>
                                                            <option value="<?php echo $occasion['pro_id']; ?>">
                                                                <?php echo htmlspecialchars($occasion['pro_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                        <option value="other">Other (Specify in Special Requests)
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Service Type -->
                                                <div class="quform-element form-group">
                                                    <label for="service_type">Service Type <span
                                                            class="required">*</span></label>
                                                    <select name="service_type" id="service_type" class="input1"
                                                        required>
                                                        <option value="">Select service type</option>
                                                        <option value="lunch">Lunch</option>
                                                        <option value="dinner">Dinner</option>
                                                        <option value="full_day">Full Day</option>
                                                        <option value="multiple_days">Multiple Days</option>
                                                    </select>
                                                </div>

                                                <!-- Booking Date -->
                                                <div class="quform-element form-group">
                                                    <label for="booking_date">Booking Date <span
                                                            class="required">*</span></label>
                                                    <input type="date" id="booking_date" name="booking_date"
                                                        class="input1" required min="<?php echo $min_date; ?>"
                                                        max="<?php echo $max_date; ?>" value="<?php echo $date; ?>">
                                                </div>

                                                <!-- Time -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="quform-element form-group">
                                                            <label for="start_time">Start Time <span
                                                                    class="required">*</span></label>
                                                            <input type="time" id="start_time" name="start_time"
                                                                class="input1" required min="07:00" max="23:00">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="quform-element form-group">
                                                            <label for="end_time">End Time <span
                                                                    class="required">*</span></label>
                                                            <input type="time" id="end_time" name="end_time"
                                                                class="input1" required min="08:00" max="01:00">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Total Hours (Auto-calculated) -->
                                                <div class="quform-element form-group">
                                                    <label for="total_hours">Estimated Total Hours</label>
                                                    <input type="text" id="total_hours" name="total_hours"
                                                        class="input1" readonly>
                                                </div>

                                                <!-- Guest Information -->
                                                <div class="quform-element form-group">
                                                    <label for="name">Full Name <span class="required">*</span></label>
                                                    <input type="text" id="name" name="name" class="input1" required
                                                        placeholder="Your full name*">
                                                </div>

                                                <div class="quform-element form-group">
                                                    <label for="email">Email Address <span
                                                            class="required">*</span></label>
                                                    <input type="email" id="email" name="email" class="input1" required
                                                        placeholder="Your email address*">
                                                </div>

                                                <div class="quform-element form-group">
                                                    <label for="phone">Phone Number <span
                                                            class="required">*</span></label>
                                                    <input type="tel" id="phone" name="phone" class="input1" required
                                                        placeholder="Your phone number*">
                                                </div>

                                                <!-- Address -->
                                                <div class="quform-element form-group">
                                                    <label for="address">Event Address <span
                                                            class="required">*</span></label>
                                                    <textarea id="address" name="address" class="input1" required
                                                        placeholder="Full event address including city and zip code*"></textarea>
                                                </div>

                                                <!-- Special Requests -->
                                                <div class="quform-element form-group">
                                                    <label for="special_requests">Special Requests / Dietary
                                                        Requirements</label>
                                                    <textarea id="special_requests" name="special_requests"
                                                        class="input1"
                                                        placeholder="Any special requirements, menu preferences, allergies, etc."></textarea>
                                                </div>

                                                <!-- Estimated Guests -->
                                                <div class="quform-element form-group">
                                                    <label for="guest_count">Number of Guests <span
                                                            class="required">*</span></label>
                                                    <input type="number" id="guest_count" name="guest_count"
                                                        class="input1" required min="1" max="500"
                                                        placeholder="Estimated number of guests">
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="quform-submit">
                                                    <div class="quform-submit-inner">
                                                        <button type="submit" class="submit-button">
                                                            <span>Submit Booking Request</span>
                                                        </button>
                                                    </div>
                                                    <div class="quform-loading-wrap">
                                                        <span class="quform-loading"></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php' ?>

    <script>
        // Calculate total hours when times change
        document.getElementById('start_time').addEventListener('change', calculateHours);
        document.getElementById('end_time').addEventListener('change', calculateHours);

        function calculateHours() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime) {
                const start = new Date('1970-01-01T' + startTime + 'Z');
                const end = new Date('1970-01-01T' + endTime + 'Z');

                // Handle overnight (end time next day)
                let diff = end - start;
                if (diff < 0) {
                    diff += 24 * 60 * 60 * 1000; // Add 24 hours
                }

                const hours = diff / (1000 * 60 * 60);
                document.getElementById('total_hours').value = hours.toFixed(1) + ' hours';
            }
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            const bookingDate = new Date(document.getElementById('booking_date').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (bookingDate < today) {
                e.preventDefault();
                alert('Booking date cannot be in the past.');
                return false;
            }

            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime >= endTime && endTime !== '00:00') {
                e.preventDefault();
                alert('End time must be after start time.');
                return false;
            }

            // Show loading indicator
            document.querySelector('.quform-loading-wrap').style.display = 'block';
        });

        // Initialize date field with tomorrow as default
        document.addEventListener('DOMContentLoaded', function () {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            document.getElementById('booking_date').value = tomorrowStr;
        });
    </script>
</body>

</html>
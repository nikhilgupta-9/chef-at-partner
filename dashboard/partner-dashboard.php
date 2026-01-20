<?php
include_once('../conn/config.php')
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Partner Dashboard</title>
    <?php include_once '../links.php'?>
</head>

<body>
    <!-- Login Section -->
    <section class="login-section" id="loginSection">
        <div class="login-card-container">
            <div class="login-header-section">
                <div class="login-logo-container">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="login-title-container">Restaurant Partner</h1>
                <p class="login-subtitle-container">Login to access your dashboard</p>
            </div>

            <form class="login-form-container" id="loginForm">
                <div class="login-input-group">
                    <label class="login-input-label" for="username">Username</label>
                    <input type="text" id="username" class="login-input-field" placeholder="Enter your username"
                        required>
                </div>

                <div class="login-input-group">
                    <label class="login-input-label" for="password">Password</label>
                    <input type="password" id="password" class="login-input-field" placeholder="Enter your password"
                        required>
                </div>

                <div class="login-input-group">
                    <label class="login-input-label">Select Role</label>
                    <div class="login-role-selector">
                        <div class="role-option-button" data-role="waiter">
                            <i class="fas fa-concierge-bell"></i> Waiter
                        </div>
                        <div class="role-option-button" data-role="chef">
                            <i class="fas fa-utensil-spoon"></i> Chef
                        </div>
                        <div class="role-option-button " data-role="bartender">
                            <i class="fas fa-cocktail"></i> Bartender
                        </div>
                    </div>
                </div>

                <button type="submit" class="login-submit-button">Login to Dashboard</button>
            </form>
        </div>
    </section>

    <!-- Dashboard Section -->
    <section class="dashboard-container hidden" id="dashboardSection">
        <!-- Mobile Header -->


        <!-- Overlay for mobile menu -->
        <div class="overlay" id="overlay"></div>

        <!-- Sidebar -->
        <nav class="sidebar-navigation" id="sidebar">
            <div class="sidebar-header-section">
                <div class="sidebar-logo-container">
                    <i class="fas fa-utensils"></i>
                </div>
                <h2 class="sidebar-title-container">PartnerDash</h2>
            </div>

            <div class="user-profile-section">
                <div class="user-avatar-container" id="userAvatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info-container">
                    <div class="user-name-container" id="userName">John Doe</div>
                    <div class="user-role-container" id="userRole">Waiter</div>
                </div>
            </div>

            <ul class="nav-menu-container">
                <li class="nav-item-container">
                    <a href="#dashboard" class="nav-link-item active" data-page="dashboard">
                        <div class="nav-icon-container">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item-container">
                    <a href="#work-report" class="nav-link-item" data-page="workReport">
                        <div class="nav-icon-container">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span>Work Report</span>
                    </a>
                </li>
                <li class="nav-item-container">
                    <a href="#payments" class="nav-link-item" data-page="payments">
                        <div class="nav-icon-container">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item-container">
                    <a href="#account" class="nav-link-item" data-page="account">
                        <div class="nav-icon-container">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span>Account Details</span>
                    </a>
                </li>
                <li class="nav-item-container">
                    <a href="#shifts" class="nav-link-item" data-page="shifts">
                        <div class="nav-icon-container">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span>Upcoming Shifts</span>
                    </a>
                </li>
                <li class="nav-item-container">
                    <a href="#support" class="nav-link-item" data-page="support">
                        <div class="nav-icon-container">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span>Support</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer-section">
                <a href="#" class="logout-button-container" id="logoutButton">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content-section">
            <div class="header-section d-flex">
                <div class="mobile-header">
                    <button class="mobile-menu-button" id="mobileMenuButton">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div>
                    <h2 class="page-title-container" id="pageTitle">Dashboard</h2>
                    <div class="date-display-container" id="currentDate">Loading...</div>
                </div>

            </div>

            <!-- Dashboard Stats -->
            <div class="page-content" id="dashboardPage">
                <div class="stats-cards-container">
                    <div class="stat-card-container">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Hours This Week</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="hoursWeek">38.5</div>
                        <div class="stat-card-change">+2.5 from last week</div>
                    </div>

                    <div class="stat-card-container">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Total Earnings</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-inr"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="totalEarnings">₹1,245.50</div>
                        <div class="stat-card-change">+₹125 from last week</div>
                    </div>

                    <div class="stat-card-container">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Tips Received</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="tipsReceived">₹342.25</div>
                        <div class="stat-card-change">+₹42 from last week</div>
                    </div>

                    <div class="stat-card-container">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Next Payment</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="nextPayment">₹845.75</div>
                        <div class="stat-card-change">Due in 3 days</div>
                    </div>
                </div>

                <div class="dashboard-content-container">
                    <div class="content-card-container">
                        <div class="content-card-header">
                            <h3 class="content-card-title">Recent Work Report</h3>
                            <a href="#work-report" class="content-card-action" id="viewAllWork">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="work-report-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Shift</th>
                                        <th>Hours</th>
                                        <th>Tips</th>
                                    </tr>
                                </thead>
                                <tbody id="recentWorkTable">
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="content-card-container">
                        <div class="content-card-header">
                            <h3 class="content-card-title">Upcoming Shifts</h3>
                            <a href="#shifts" class="content-card-action">View All</a>
                        </div>
                        <div id="upcomingShifts">
                            <!-- Filled by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Report Page -->
            <div class="page-content hidden" id="workReportPage">
                <div class="content-card-container">
                    <div class="content-card-header">
                        <h3 class="content-card-title">Work Report History</h3>
                        <div class="content-card-action">Last 30 Days</div>
                    </div>
                    <div class="table-responsive">
                        <table class="work-report-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Hours</th>
                                    <th>Base Pay</th>
                                    <th>Tips</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="fullWorkTable">
                                <!-- Filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payments Page -->
            <div class="page-content hidden" id="paymentsPage">
                <div class="content-card-container">
                    <div class="content-card-header">
                        <h3 class="content-card-title">Payment History</h3>
                        <div class="content-card-action">Current Month</div>
                    </div>
                    <div class="table-responsive">
                        <table class="work-report-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="paymentHistoryTable">
                                <!-- Filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Account Details Page -->
            <div class="page-content hidden" id="accountPage">
                <div class="content-card-container">
                    <div class="content-card-header">
                        <h3 class="content-card-title">Account Details</h3>
                        <a href="#" class="content-card-action">Edit</a>
                    </div>
                    <div id="accountDetails">
                        <!-- Filled by JavaScript -->
                    </div>
                </div>

                <div class="content-card-container">
                    <div class="content-card-header">
                        <h3 class="content-card-title">Payment Method</h3>
                        <a href="#" class="content-card-action">Change</a>
                    </div>
                    <div class="account-detail-item">
                        <div class="account-detail-label">Bank Account</div>
                        <div class="account-detail-value">**** **** **** 4321</div>
                    </div>
                    <div class="account-detail-item">
                        <div class="account-detail-label">Routing Number</div>
                        <div class="account-detail-value">*****4321</div>
                    </div>
                    <div class="account-detail-item">
                        <div class="account-detail-label">Next Payout</div>
                        <div class="account-detail-value">June 15, 2023</div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Shifts Page -->
            <div class="page-content hidden" id="shiftsPage">
                <div class="content-card-container">
                    <div class="content-card-header">
                        <h3 class="content-card-title">Upcoming Shifts</h3>
                        <a href="#" class="content-card-action">Request Change</a>
                    </div>
                    <div id="fullShiftsList">
                        <!-- Filled by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="page-content hidden" id="supportPage">
                <div class="support-header-container">
                    <h1 class="support-title-container">Help & Support Center</h1>
                    <p class="support-subtitle-container">Get help with your dashboard, payments, shifts, and more</p>
                </div>

                <div class="support-quick-actions-container">
                    <div class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3 class="quick-action-title">FAQ</h3>
                        <p class="quick-action-desc">Find answers to common questions</p>
                        <a href="#faq" class="quick-action-link">Browse FAQ</a>
                    </div>

                    <div class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="quick-action-title">Contact</h3>
                        <p class="quick-action-desc">Send us a message</p>
                        <a href="#contact" class="quick-action-link">Contact Support</a>
                    </div>

                    <div class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3 class="quick-action-title">Call Us</h3>
                        <p class="quick-action-desc">24/7 Support Hotline</p>
                        <div class="quick-action-phone">+1 (800) 123-4567</div>
                    </div>

                    <div class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="quick-action-title">Live Chat</h3>
                        <p class="quick-action-desc">Chat with support agent</p>
                        <button class="quick-action-button">Start Chat</button>
                    </div>
                </div>

                <div class="support-content-container">
                    <!-- FAQ Section -->
                    <div class="content-card-container">
                        <div class="content-card-header">
                            <h3 class="content-card-title">Frequently Asked Questions</h3>
                            <div class="content-card-action">All FAQs</div>
                        </div>

                        <div class="faq-category-container">
                            <h4 class="faq-category-title">Payments & Earnings</h4>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>When will I receive my payment?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Payments are processed every Friday for the previous week's work. It takes 1-3
                                        business days to reflect in your account depending on your bank.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>How are tips calculated and distributed?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Tips are calculated daily based on your shift performance and are added to your
                                        weekly earnings. You can view tip breakdowns in your Work Report section.</p>
                                </div>
                            </div>
                        </div>

                        <div class="faq-category-container">
                            <h4 class="faq-category-title">Shifts & Scheduling</h4>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>How do I request a shift change?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Go to Upcoming Shifts section, click "Request Change" on the shift you want to
                                        modify, and submit your request at least 48 hours in advance.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>What happens if I miss a shift?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Please notify your manager immediately. Multiple unexcused absences may affect
                                        your schedule priority and earnings.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="content-card-container">
                        <div class="content-card-header">
                            <h3 class="content-card-title">Contact Support</h3>
                            <div class="content-card-action">Response within 24 hours</div>
                        </div>

                        <form class="support-form-container" id="supportForm">
                            <div class="support-form-group">
                                <label class="support-form-label">Issue Type</label>
                                <select class="support-form-select" id="issueType">
                                    <option value="">Select issue type</option>
                                    <option value="payment">Payment Issue</option>
                                    <option value="schedule">Schedule Issue</option>
                                    <option value="technical">Technical Problem</option>
                                    <option value="account">Account Issue</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="support-form-group">
                                <label class="support-form-label">Subject</label>
                                <input type="text" class="support-form-input" id="supportSubject"
                                    placeholder="Brief description of your issue">
                            </div>

                            <div class="support-form-group">
                                <label class="support-form-label">Message</label>
                                <textarea class="support-form-textarea" id="supportMessage"
                                    placeholder="Please describe your issue in detail..." rows="5"></textarea>
                            </div>

                            <div class="support-form-group">
                                <label class="support-form-label">Attachments (Optional)</label>
                                <div class="file-upload-container">
                                    <input type="file" id="supportAttachment" class="file-upload-input"
                                        accept=".jpg,.jpeg,.png,.pdf">
                                    <label for="supportAttachment" class="file-upload-label">
                                        <i class="fas fa-paperclip"></i> Choose File
                                    </label>
                                    <span class="file-upload-text" id="fileName">No file chosen</span>
                                </div>
                            </div>

                            <div class="support-form-actions">
                                <button type="submit" class="support-submit-button">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                                <button type="button" class="support-cancel-button">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Support Resources -->
                    <div class="content-card-container">
                        <div class="content-card-header">
                            <h3 class="content-card-title">Support Resources</h3>
                            <div class="content-card-action">Helpful Links</div>
                        </div>

                        <div class="resources-container">
                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="resource-info">
                                    <h4 class="resource-title">User Guide</h4>
                                    <p class="resource-desc">Complete guide to using the partner dashboard</p>
                                </div>
                                <a href="#" class="resource-link">View Guide</a>
                            </div>

                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="resource-info">
                                    <h4 class="resource-title">Video Tutorials</h4>
                                    <p class="resource-desc">Step-by-step video guides</p>
                                </div>
                                <a href="#" class="resource-link">Watch Now</a>
                            </div>

                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="resource-info">
                                    <h4 class="resource-title">Policy Documents</h4>
                                    <p class="resource-desc">Terms, conditions and policies</p>
                                </div>
                                <a href="#" class="resource-link">View Documents</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script>
        // Current user data
        let currentUser = {
            name: "John Doe",
            role: "waiter",
            username: ""
        };

        // Data for different roles
        const roleData = {
            waiter: {
                name: "Alex Johnson",
                avatarIcon: "fa-concierge-bell",
                hoursWeek: "38.5",
                totalEarnings: "₹1,245.50",
                tipsReceived: "₹342.25",
                nextPayment: "₹845.75"
            },
            chef: {
                name: "Maria Rodriguez",
                avatarIcon: "fa-utensil-spoon",
                hoursWeek: "42.0",
                totalEarnings: "₹2,150.00",
                tipsReceived: "₹0.00",
                nextPayment: "₹1,850.00"
            },
            bartender: {
                name: "James Wilson",
                avatarIcon: "fa-cocktail",
                hoursWeek: "36.0",
                totalEarnings: "₹1,580.25",
                tipsReceived: "₹425.50",
                nextPayment: "₹1,120.75"
            }
        };

        // Work report data
        const workReportData = [
            { date: "Jun 10, 2023", shift: "Evening (4PM-12AM)", hours: 8, basePay: 120, tips: 45.50 },
            { date: "Jun 9, 2023", shift: "Evening (4PM-12AM)", hours: 8, basePay: 120, tips: 38.25 },
            { date: "Jun 8, 2023", shift: "Day (8AM-4PM)", hours: 8, basePay: 120, tips: 32.75 },
            { date: "Jun 7, 2023", shift: "Evening (4PM-12AM)", hours: 8, basePay: 120, tips: 52.00 },
            { date: "Jun 6, 2023", shift: "Day (8AM-4PM)", hours: 6.5, basePay: 97.5, tips: 28.50 },
            { date: "Jun 5, 2023", shift: "Evening (4PM-12AM)", hours: 8, basePay: 120, tips: 41.25 },
            { date: "Jun 3, 2023", shift: "Day (8AM-4PM)", hours: 8, basePay: 120, tips: 35.00 },
            { date: "Jun 2, 2023", shift: "Evening (4PM-12AM)", hours: 8, basePay: 120, tips: 48.75 },
            { date: "Jun 1, 2023", shift: "Day (8AM-4PM)", hours: 8, basePay: 120, tips: 30.25 }
        ];

        // Payment history data
        const paymentHistoryData = [
            { date: "Jun 5, 2023", description: "Weekly Payroll", amount: "₹845.75", status: "paid" },
            { date: "May 29, 2023", description: "Weekly Payroll", amount: "₹812.50", status: "paid" },
            { date: "May 22, 2023", description: "Weekly Payroll", amount: "₹798.25", status: "paid" },
            { date: "May 15, 2023", description: "Weekly Payroll", amount: "₹775.00", status: "paid" },
            { date: "May 8, 2023", description: "Weekly Payroll + Bonus", amount: "₹925.50", status: "paid" },
            { date: "Jun 12, 2023", description: "Weekly Payroll", amount: "₹845.75", status: "pending" }
        ];

        // Upcoming shifts data
        const upcomingShiftsData = [
            { date: "Jun 13, 2023", time: "4:00 PM - 12:00 AM", role: "Evening Shift" },
            { date: "Jun 14, 2023", time: "4:00 PM - 12:00 AM", role: "Evening Shift" },
            { date: "Jun 15, 2023", time: "8:00 AM - 4:00 PM", role: "Day Shift" },
            { date: "Jun 16, 2023", time: "4:00 PM - 12:00 AM", role: "Evening Shift" },
            { date: "Jun 17, 2023", time: "12:00 PM - 8:00 PM", role: "Weekend Shift" },
            { date: "Jun 19, 2023", time: "8:00 AM - 4:00 PM", role: "Day Shift" }
        ];

        // DOM Elements
        const loginSection = document.getElementById('loginSection');
        const dashboardSection = document.getElementById('dashboardSection');
        const loginForm = document.getElementById('loginForm');
        const roleButtons = document.querySelectorAll('.role-option-button');
        const logoutButton = document.getElementById('logoutButton');
        const navLinks = document.querySelectorAll('.nav-link-item');
        const pageContents = document.querySelectorAll('.page-content');
        const userName = document.getElementById('userName');
        const userRole = document.getElementById('userRole');
        const userAvatar = document.getElementById('userAvatar');
        const pageTitle = document.getElementById('pageTitle');
        const currentDateElement = document.getElementById('currentDate');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mobileUserName = document.getElementById('mobileUserName');
        const mobileUserAvatar = document.getElementById('mobileUserAvatar');
        const viewAllWork = document.getElementById('viewAllWork');

        // Initialize the application
        function initApp() {
            // Set current date
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            currentDateElement.textContent = now.toLocaleDateString('en-US', options);

            // Set up role selection
            roleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    roleButtons.forEach(btn => btn.classList.remove('selected'));
                    this.classList.add('selected');
                    currentUser.role = this.getAttribute('data-role');
                });
            });

            // Set default role
            document.querySelector('.role-option-button[data-role="waiter"]').classList.add('selected');
            currentUser.role = 'waiter';

            // Login form submission
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const username = document.getElementById('username').value;
                if (!username) {
                    alert('Please enter a username');
                    return;
                }

                currentUser.username = username;
                loginUser();
            });

            // Logout button
            logoutButton.addEventListener('click', function (e) {
                e.preventDefault();
                logoutUser();
            });

            // Mobile menu toggle
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', closeMobileMenu);

            // Navigation
            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Update active nav link
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');

                    // Show corresponding page
                    const pageId = this.getAttribute('data-page');
                    showPage(pageId);

                    // Close mobile menu on navigation
                    closeMobileMenu();
                });
            });

            // View all work link
            viewAllWork.addEventListener('click', function (e) {
                e.preventDefault();
                navLinks.forEach(nav => nav.classList.remove('active'));
                document.querySelector('.nav-link-item[data-page="workReport"]').classList.add('active');
                showPage('workReport');
                closeMobileMenu();
            });

            // Initialize dashboard with data
            updateDashboardForRole();
            populateRecentWorkTable();
            populateFullWorkTable();
            populatePaymentHistoryTable();
            populateAccountDetails();
            populateUpcomingShifts();
            populateFullShiftsList();
        }

        // Toggle mobile menu
        function toggleMobileMenu() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            // Change menu icon
            const menuIcon = mobileMenuButton.querySelector('i');
            if (sidebar.classList.contains('mobile-open')) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
            } else {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        }

        // Close mobile menu
        function closeMobileMenu() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');

            // Reset menu icon
            const menuIcon = mobileMenuButton.querySelector('i');
            menuIcon.classList.remove('fa-times');
            menuIcon.classList.add('fa-bars');
        }

        // Login function
        function loginUser() {
            loginSection.classList.add('hidden');
            dashboardSection.classList.remove('hidden');

            // Update user info based on role
            updateDashboardForRole();

            // Show dashboard page by default
            showPage('dashboard');
        }

        // Logout function
        function logoutUser() {
            dashboardSection.classList.add('hidden');
            loginSection.classList.remove('hidden');

            // Reset form
            loginForm.reset();
            document.getElementById('username').focus();

            // Close mobile menu if open
            closeMobileMenu();
        }

        // Show specific page
        function showPage(pageId) {
            // Hide all pages
            pageContents.forEach(page => page.classList.add('hidden'));

            // Show requested page
            const pageElement = document.getElementById(`${pageId}Page`);
            if (pageElement) {
                pageElement.classList.remove('hidden');

                // Update page title
                const pageTitles = {
                    dashboard: 'Dashboard',
                    workReport: 'Work Report',
                    payments: 'Payments',
                    account: 'Account Details',
                    shifts: 'Upcoming Shifts'
                };

                pageTitle.textContent = pageTitles[pageId] || 'Dashboard';
            }
        }

        // Update dashboard for selected role
        function updateDashboardForRole() {
            const roleInfo = roleData[currentUser.role];

            // Update user info
            userName.textContent = roleInfo.name;
            userRole.textContent = currentUser.role.charAt(0).toUpperCase() + currentUser.role.slice(1);
            mobileUserName.textContent = roleInfo.name;

            // Update avatar icon
            const avatarIcon = `<i class="fas ${roleInfo.avatarIcon}"></i>`;
            userAvatar.innerHTML = avatarIcon;
            mobileUserAvatar.innerHTML = avatarIcon;

            // Update stats
            document.getElementById('hoursWeek').textContent = roleInfo.hoursWeek;
            document.getElementById('totalEarnings').textContent = roleInfo.totalEarnings;
            document.getElementById('tipsReceived').textContent = roleInfo.tipsReceived;
            document.getElementById('nextPayment').textContent = roleInfo.nextPayment;

            // Update username if provided
            if (currentUser.username) {
                userName.textContent = currentUser.username;
                mobileUserName.textContent = currentUser.username;
            }
        }

        // Populate recent work table
        function populateRecentWorkTable() {
            const tableBody = document.getElementById('recentWorkTable');
            tableBody.innerHTML = '';

            // Show only recent 5 entries
            const recentWork = workReportData.slice(0, 5);

            recentWork.forEach(work => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${work.date}</td>
                    <td>${work.shift}</td>
                    <td>${work.hours}</td>
                    <td>₹${work.tips.toFixed(2)}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Populate full work table
        function populateFullWorkTable() {
            const tableBody = document.getElementById('fullWorkTable');
            tableBody.innerHTML = '';

            workReportData.forEach(work => {
                const total = work.basePay + work.tips;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${work.date}</td>
                    <td>${work.shift}</td>
                    <td>${work.hours}</td>
                    <td>₹${work.basePay.toFixed(2)}</td>
                    <td>₹${work.tips.toFixed(2)}</td>
                    <td>₹${total.toFixed(2)}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Populate payment history table
        function populatePaymentHistoryTable() {
            const tableBody = document.getElementById('paymentHistoryTable');
            tableBody.innerHTML = '';

            paymentHistoryData.forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${payment.date}</td>
                    <td>${payment.description}</td>
                    <td>${payment.amount}</td>
                    <td><span class="payment-status status-${payment.status}">${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}</span></td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Populate account details
        function populateAccountDetails() {
            const accountDetails = document.getElementById('accountDetails');

            const details = [
                { label: "Full Name", value: roleData[currentUser.role].name },
                { label: "Role", value: currentUser.role.charAt(0).toUpperCase() + currentUser.role.slice(1) },
                { label: "Employee ID", value: "EMP-2023-" + (currentUser.role === 'waiter' ? '001' : currentUser.role === 'chef' ? '002' : '003') },
                { label: "Email", value: currentUser.username ? `${currentUser.username}@restaurant.com` : "user@restaurant.com" },
                { label: "Phone", value: "(555) 123-4567" },
                { label: "Join Date", value: "January 15, 2022" }
            ];

            let html = '';
            details.forEach(detail => {
                html += `
                    <div class="account-detail-item">
                        <div class="account-detail-label">${detail.label}</div>
                        <div class="account-detail-value">${detail.value}</div>
                    </div>
                `;
            });

            accountDetails.innerHTML = html;
        }

        // Populate upcoming shifts in dashboard
        function populateUpcomingShifts() {
            const shiftsContainer = document.getElementById('upcomingShifts');

            // Show only next 3 shifts
            const nextShifts = upcomingShiftsData.slice(0, 3);

            let html = '';
            nextShifts.forEach(shift => {
                html += `
                    <div class="upcoming-shift-item">
                        <div>
                            <div class="shift-time-container">${shift.date}</div>
                            <div class="shift-role-container">${shift.time}</div>
                        </div>
                        <div class="shift-role-container">${shift.role}</div>
                    </div>
                `;
            });

            shiftsContainer.innerHTML = html;
        }

        // Populate full shifts list
        function populateFullShiftsList() {
            const shiftsContainer = document.getElementById('fullShiftsList');

            let html = '';
            upcomingShiftsData.forEach(shift => {
                html += `
                    <div class="upcoming-shift-item">
                        <div>
                            <div class="shift-time-container">${shift.date}</div>
                            <div class="shift-role-container">${shift.time}</div>
                        </div>
                        <div class="shift-role-container">${shift.role}</div>
                    </div>
                `;
            });

            shiftsContainer.innerHTML = html;
        }

        // Initialize the app when DOM is loaded
        document.addEventListener('DOMContentLoaded', initApp);
    </script>
    <script>
        // Add to roleData object for support page titles
const pageTitles = {
    dashboard: 'Dashboard',
    workReport: 'Work Report',
    payments: 'Payments',
    account: 'Account Details',
    shifts: 'Upcoming Shifts',
    support: 'Support'  // Add this line
};

// Add this function for FAQ toggle functionality
function initFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            faqItem.classList.toggle('active');
        });
    });
}

// Add this function for support form handling
function initSupportForm() {
    const supportForm = document.getElementById('supportForm');
    const fileInput = document.getElementById('supportAttachment');
    const fileName = document.getElementById('fileName');
    
    // File input display
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'No file chosen';
            }
        });
    }
    
    // Form submission
    if (supportForm) {
        supportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const issueType = document.getElementById('issueType').value;
            const subject = document.getElementById('supportSubject').value;
            const message = document.getElementById('supportMessage').value;
            
            if (!issueType || !subject || !message) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Show success message (in a real app, this would send to server)
            alert('Thank you! Your support request has been submitted. We will respond within 24 hours.');
            supportForm.reset();
            fileName.textContent = 'No file chosen';
        });
    }
    
    // Cancel button
    const cancelButton = document.querySelector('.support-cancel-button');
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            supportForm.reset();
            fileName.textContent = 'No file chosen';
        });
    }
}

// Add these function calls to your initApp() function:
function initApp() {
    // Your existing code...
    
    // Initialize support section features
    initFAQ();
    initSupportForm();
    
    // Your existing code...
}

// Add support for Live Chat button
document.addEventListener('DOMContentLoaded', function() {
    const liveChatButton = document.querySelector('.quick-action-button');
    if (liveChatButton) {
        liveChatButton.addEventListener('click', function() {
            alert('Live chat feature would open here. In a real implementation, this would connect to a live chat service.');
        });
    }
});
    </script>
</body>

</html>
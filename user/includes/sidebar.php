<div class="sidebar">
    <div class="user-profile">
        <?php
        $user_id = $_SESSION['user_id'];
        $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();
        ?>
        <img src="<?php echo !empty($user['profile_image']) ? '../uploads/' . $user['profile_image'] : 'https://via.placeholder.com/100'; ?>"
            class="profile-img" alt="<?php echo $_SESSION['full_name']; ?>">
        <h5 class="mb-1">
            <?php echo $_SESSION['full_name']; ?>
        </h5>
        <p class="mb-0 opacity-75">Customer</p>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>"
                    href="bookings.php">
                    <i class="fas fa-calendar-check"></i> My Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>"
                    href="messages.php">
                    <i class="fas fa-comments"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>"
                    href="reviews.php">
                    <i class="fas fa-star"></i> Reviews
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"
                    href="profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'book-partner.php' ? 'active' : ''; ?>"
                    href="book-partner.php">
                    <i class="fas fa-plus-circle"></i> Book Partner
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
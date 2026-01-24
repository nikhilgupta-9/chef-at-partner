<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// if ($_SESSION['user_role'] !== 'admin') {
//     header("Location: unauthorized.php");
//     exit();
// }
?>
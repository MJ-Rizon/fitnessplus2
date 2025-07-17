<?php
session_start();
include 'config.php';
include '../db/connection.php';

// Restrict access based on allowed IPs
//$allowed_ips = ['127.0.0.1', '::1', '192.168.1.100'];
//$user_ip = $_SERVER['REMOTE_ADDR'];

//if (!in_array($user_ip, $allowed_ips)) {
//    header('HTTP/1.0 403 Forbidden');
//    echo 'Access denied: Your IP address is not allowed.';
//    exit;
//}

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: Login/admin_login.php");
    exit;
}
?>
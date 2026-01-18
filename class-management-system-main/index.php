<?php
// index.php

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy session
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'schedule';

// Allowed public pages
$publicPages = ['schedule', 'courses', 'instructors', 'rooms', 'about'];

// Allowed admin pages
$adminPages = ['admin_login', 'admin_register', 'admin_dashboard', 'admin_courses', 'admin_instructors', 'admin_rooms', 'admin_schedule', 'admin_conflicts', 'admin_profile'];

// Check if admin pages require auth (except login and register)
if (in_array($page, $adminPages) && $page !== 'admin_login' && $page !== 'admin_register') {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
        header('Location: index.php?page=admin_login');
        exit;
    }
}

// Route to appropriate page
if (in_array($page, $publicPages)) {
    include 'pages/' . $page . '.php';
} elseif (in_array($page, $adminPages)) {
    $pageName = str_replace('admin_', '', $page);
    include 'pages/admin/' . $pageName . '.php';
} else {
    include 'pages/schedule.php';
}
?>

<?php
// includes/admin_shell.php

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
// Only redirect if we're NOT on the login or register page
$currentPage = $_GET['page'] ?? '';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    // Allow access to login and register pages without authentication
    if ($currentPage !== 'admin_login' && $currentPage !== 'admin_register') {
        header('Location: index.php?page=admin_login');
        exit;
    }
}

// Get admin info from session (if logged in)
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
$adminFullName = $_SESSION['admin_full_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Class Schedule Management System</title>
    <link rel="stylesheet" href="public/output.css">
</head>
<body class="bg-[#0b0b0b] text-gray-100 min-h-screen flex flex-col">
    <!-- Admin navbar -->
    <header class="bg-black border-b border-[#262626]">
        <nav class="max-w-7xl mx-auto flex items-center justify-between py-3 px-4">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-semibold tracking-wide uppercase text-gray-300">
                    🔐 Admin Panel
                </span>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <span class="text-xs text-gray-500">|</span>
                    <span class="text-xs text-gray-400">
                        Welcome, <strong><?php echo htmlspecialchars($adminFullName); ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4 text-xs md:text-sm">
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="index.php?page=admin_dashboard" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Dashboard
                    </a>
                    <a href="index.php?page=admin_courses" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Courses
                    </a>
                    <a href="index.php?page=admin_instructors" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Instructors
                    </a>
                    <a href="index.php?page=admin_rooms" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Rooms
                    </a>
                    <a href="index.php?page=admin_schedule" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Schedule
                    </a>
                    <a href="index.php?page=admin_conflicts" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Conflicts
                    </a>
                    <div class="h-4 w-px bg-[#262626]"></div>
                    <a href="index.php?page=admin_profile" class="px-2 py-1 rounded text-gray-300 hover:text-white hover:bg-[#181818]">
                        Profile
                    </a>
                    <a href="index.php?action=logout" class="px-3 py-1 rounded border border-gray-500 text-gray-200 text-xs hover:bg-gray-100 hover:text-black">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="index.php?page=admin_login" class="px-3 py-1 rounded border border-gray-500 text-gray-200 text-xs hover:bg-gray-100 hover:text-black">
                        Back to Login
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 py-6">

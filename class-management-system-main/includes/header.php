<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule Management System</title>
    <link rel="stylesheet" href="public/output.css">
</head>
<body class="bg-[#f7f7f5] text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <header class="bg-[#1e90ff] text-white shadow">
            <nav class="max-w-6xl mx-auto flex items-center justify-between py-4 px-4">
                <a href="index.php" class="text-lg font-bold tracking-wide">
                    Class Schedule MS
                </a>
                <div class="space-x-3 text-sm md:text-base flex items-center">
                    <a href="index.php" class="hover:underline">Schedule</a>
                    <a href="index.php?page=courses" class="hover:underline">Courses</a>
                    <a href="index.php?page=instructors" class="hover:underline">Instructors</a>
                    <a href="index.php?page=rooms" class="hover:underline">Rooms</a>
                    <a href="index.php?page=about" class="hover:underline">About</a>
                    
                    <div class="h-4 w-px bg-white/30"></div>
                    
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <!-- Admin is logged in -->
                        <a href="index.php?page=admin_dashboard" class="hover:underline font-semibold">
                            👤 <?php echo htmlspecialchars($_SESSION['admin_full_name'] ?? 'Admin'); ?>
                        </a>
                        <a href="index.php?action=logout" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm">
                            Logout
                        </a>
                    <?php else: ?>
                        <!-- Admin is not logged in -->
                        <a href="index.php?page=admin_login" class="hover:underline font-semibold">
                            🔐 Admin
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <!-- Main content starts here (closed in footer) -->
        <main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6">

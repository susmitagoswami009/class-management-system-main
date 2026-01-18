<?php
// pages/admin/login.php

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';

$error = '';
$success = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
    header('Location: index.php?page=admin_dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = loginAdmin($username, $password);
        
        if ($result['success']) {
            // Set session variables
            $_SESSION['admin_id'] = $result['admin_id'];
            $_SESSION['admin_username'] = $result['username'];
            $_SESSION['admin_email'] = $result['email'];
            $_SESSION['admin_full_name'] = $result['full_name'];
            $_SESSION['isAdmin'] = true;
            
            // Redirect to dashboard
            header('Location: index.php?page=admin_dashboard');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Class Schedule Management System</title>
    <link rel="stylesheet" href="public/output.css">
</head>
<body class="bg-[#0b0b0b] text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border border-[#262626] rounded-xl shadow-2xl p-8 mb-4">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold mb-2 text-gray-100">🔐 Admin Login</h1>
                <p class="text-sm text-gray-400">
                    Sign in to manage the scheduling system.
                </p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 text-sm text-red-300 bg-[#3a1a1a] border border-red-600 rounded px-4 py-3">
                    <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all"
                        placeholder="Enter your username"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all"
                        placeholder="Enter your password"
                        required
                    />
                </div>

                <button
                    type="submit"
                    name="login"
                    class="w-full rounded-lg bg-black text-gray-100 py-3 text-sm font-semibold border border-gray-600 hover:bg-[#050505] transition-all"
                >
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-[#262626]">
                <p class="text-sm text-gray-400 text-center">
                    Don't have an account? 
                    <a href="index.php?page=admin_register" class="text-gray-200 font-semibold hover:text-white hover:underline">
                        Register here
                    </a>
                </p>
            </div>
        </div>

        <!-- Demo credentials box -->
        <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-4 mb-4">
            <p class="text-xs font-semibold text-gray-400 mb-3">📋 Demo Credentials:</p>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Username:</span>
                    de class="text-xs text-gray-200 bg-[#0c0c0c] px-2 py-1 rounded font-mono">admin</code>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Password:</span>
                    de class="text-xs text-gray-200 bg-[#0c0c0c] px-2 py-1 rounded font-mono">admin123</code>
                </div>
            </div>
        </div>

        <!-- Links -->
        <div class="text-center text-xs text-gray-500">
            <a href="index.php" class="hover:text-gray-300">← Back to Student Portal</a>
        </div>
    </div>
</body>
</html>

<?php
// pages/admin/register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php?page=admin_dashboard');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        
        // Validate passwords match
        if ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $result = registerAdmin($username, $email, $password, $fullName);
            
            if ($result['success']) {
                $success = $result['message'];
                // Clear form
                $username = $email = $password = $confirmPassword = $fullName = '';
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Class Schedule Management System</title>
    <link rel="stylesheet" href="public/output.css">
</head>
<body class="bg-[#0b0b0b] text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border border-[#262626] rounded-xl shadow-lg p-8 mb-4">
            <h1 class="text-3xl font-bold mb-2 text-gray-100">Create Admin Account</h1>
            <p class="text-sm text-gray-400 mb-6">
                Register a new administrator account for the scheduling system.
            </p>

            <?php if ($error): ?>
                <div class="mb-4 text-sm text-red-300 bg-[#3a1a1a] border border-red-600 rounded px-4 py-3">
                    <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-4 text-sm text-green-300 bg-[#1a3a1a] border border-green-600 rounded px-4 py-3">
                    <strong>✓ Success:</strong> <?php echo htmlspecialchars($success); ?>
                    <p class="mt-2 text-xs text-gray-400">
                        <a href="index.php?page=admin_login" class="text-green-300 hover:underline">
                            Proceed to login →
                        </a>
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Full Name
                    </label>
                    <input
                        type="text"
                        name="full_name"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        placeholder="e.g., John Doe"
                        value="<?php echo htmlspecialchars($fullName ?? ''); ?>"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        placeholder="Enter username (no spaces)"
                        value="<?php echo htmlspecialchars($username ?? ''); ?>"
                        pattern="[a-zA-Z0-9_]+"
                        required
                    />
                    <p class="text-xs text-gray-500 mt-1">Letters, numbers, and underscore only</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        placeholder="e.g., admin@example.com"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
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
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        placeholder="At least 6 characters"
                        minlength="6"
                        required
                    />
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-200">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        name="confirm_password"
                        class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        placeholder="Re-enter your password"
                        minlength="6"
                        required
                    />
                </div>

                <button
                    type="submit"
                    name="register"
                    class="w-full rounded-lg bg-black text-gray-100 py-3 text-sm font-semibold border border-gray-600 hover:bg-[#050505] transition-colors"
                >
                    Create Account
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-[#262626]">
                <p class="text-sm text-gray-400 text-center">
                    Already have an account? 
                    <a href="index.php?page=admin_login" class="text-gray-200 font-semibold hover:underline">
                        Login here
                    </a>
                </p>
            </div>
        </div>

        <!-- Requirements box -->
        <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-4">
            <p class="text-xs text-gray-500 mb-2"><strong>Account Requirements:</strong></p>
            <ul class="text-xs text-gray-400 space-y-1">
                <li>✓ Unique username (letters, numbers, underscore)</li>
                <li>✓ Valid email address</li>
                <li>✓ Password: minimum 6 characters</li>
                <li>✓ Passwords must match</li>
            </ul>
        </div>
    </div>
</body>
</html>

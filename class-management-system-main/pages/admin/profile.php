<?php
// pages/admin/profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/admin_shell.php';
include 'includes/data.php';

require_once __DIR__ . '/../../config/db.php';

$adminId = $_SESSION['admin_id'];
$admin = getAdminById($adminId);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Get current password hash
        $result = $conn->query("SELECT password_hash FROM admins WHERE id = $adminId");
        $adminData = $result->fetch_assoc();
        
        // Verify current password
        if (!password_verify($currentPassword, $adminData['password_hash'])) {
            $error = 'Current password is incorrect.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must be at least 6 characters.';
        } else {
            // Update password
            $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $sql = "UPDATE admins SET password_hash = '$newPasswordHash' WHERE id = $adminId";
            
            if ($conn->query($sql) === TRUE) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Error updating password. Please try again.';
            }
        }
    }
}
?>

<h1 class="text-3xl font-bold mb-2 text-gray-100">Admin Profile</h1>
<p class="text-sm text-gray-400 mb-6">
    Manage your administrator account settings and security.
</p>

<?php if ($error): ?>
    <div class="mb-4 text-sm text-red-300 bg-[#3a1a1a] border border-red-600 rounded px-4 py-3">
        <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="mb-4 text-sm text-green-300 bg-[#1a3a1a] border border-green-600 rounded px-4 py-3">
        <strong>✓ Success:</strong> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div class="grid gap-6 md:grid-cols-2">
    <!-- Account Information -->
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-100">Account Information</h2>
        
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Full Name</label>
                <p class="text-gray-100 font-medium"><?php echo htmlspecialchars($admin['full_name'] ?? 'N/A'); ?></p>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Username</label>
                <p class="text-gray-100 font-medium"><?php echo htmlspecialchars($admin['username']); ?></p>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Email</label>
                <p class="text-gray-100 font-medium"><?php echo htmlspecialchars($admin['email']); ?></p>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Member Since</label>
                <p class="text-gray-100 font-medium">
                    <?php echo date('F d, Y', strtotime($admin['created_at'])); ?>
                </p>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Account Status</label>
                <span class="inline-block px-2 py-1 rounded text-xs font-semibold <?php echo $admin['is_active'] ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'; ?>">
                    <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-100">Change Password</h2>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-200">Current Password</label>
                <input
                    type="password"
                    name="current_password"
                    class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    placeholder="Enter your current password"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-200">New Password</label>
                <input
                    type="password"
                    name="new_password"
                    class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    placeholder="At least 6 characters"
                    minlength="6"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-200">Confirm New Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    class="w-full rounded-lg bg-[#0c0c0c] border border-[#333333] px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    placeholder="Re-enter new password"
                    minlength="6"
                    required
                />
            </div>

            <button
                type="submit"
                name="change_password"
                class="w-full rounded-lg bg-black text-gray-100 py-3 text-sm font-semibold border border-gray-600 hover:bg-[#050505] transition-colors"
            >
                Update Password
            </button>
        </form>

        <div class="mt-4 p-3 bg-[#0c0c0c] border border-[#333333] rounded text-xs text-gray-400">
            <p><strong>Password Requirements:</strong></p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Minimum 6 characters</li>
                <li>Must be different from current password</li>
                <li>Both new passwords must match</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>


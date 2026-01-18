<?php
// pages/admin/instructors.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/admin_shell.php';
include 'includes/data.php';

$instructors = getInstructors();
$scheduleEntries = getScheduleEntries();
$timeslots = getTimeslots();
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_instructor') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';

        if (empty($id) || empty($name)) {
            $errorMessage = 'Please fill all fields.';
        } else {
            if (addInstructor($id, $name)) {
                $successMessage = 'Instructor added successfully!';
                $instructors = getInstructors();
            } else {
                $errorMessage = 'Error adding instructor. ID may already exist.';
            }
        }
    } elseif ($_POST['action'] === 'delete_instructor') {
        $id = $_POST['instructor_id'] ?? '';
        if (deleteInstructor($id)) {
            $successMessage = 'Instructor deleted successfully!';
            $instructors = getInstructors();
        } else {
            $errorMessage = 'Error deleting instructor.';
        }
    }
}
?>

<h1 class="text-2xl font-bold mb-2 text-gray-100">
    Manage Instructors
</h1>
<p class="text-sm text-gray-400 mb-4">
    Add instructors and later assign them to courses and timeslots.
</p>

<?php if ($successMessage): ?>
    <div class="mb-4 text-xs text-green-200 bg-[#1a3a1a] border border-green-600 rounded px-3 py-2">
        ✓ <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="mb-4 text-xs text-red-200 bg-[#3a1a1a] border border-red-600 rounded px-3 py-2">
        ✗ <?php echo htmlspecialchars($errorMessage); ?>
    </div>
<?php endif; ?>

<div class="grid gap-4 md:grid-cols-2">
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">
            Existing Instructors (<?php echo count($instructors); ?>)
        </h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($instructors as $instructor): ?>
                <?php
                $instructorSchedules = array_filter($scheduleEntries, function($e) use ($instructor) {
                    return $e['instructor_id'] === $instructor['id'];
                });
                ?>
                <li class="flex items-center justify-between bg-[#151515] border border-[#2a2a2a] rounded-md px-3 py-2">
                    <div>
                        <span class="text-gray-100 font-semibold"><?php echo htmlspecialchars($instructor['name']); ?></span>
                        <div class="text-xs text-gray-400"><?php echo count($instructorSchedules); ?> class(es) assigned</div>
                    </div>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_instructor">
                        <input type="hidden" name="instructor_id" value="<?php echo htmlspecialchars($instructor['id']); ?>">
                        <button type="submit" class="text-xs px-2 py-1 rounded border border-red-600 text-red-300 hover:bg-red-900">
                            Delete
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">
            Add New Instructor
        </h2>
        <form method="POST" class="space-y-3 text-sm">
            <div>
                <label class="block mb-1 text-gray-200">Instructor ID</label>
                <input
                    type="text"
                    name="id"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., I4"
                    required
                />
            </div>
            <div>
                <label class="block mb-1 text-gray-200">Full Name</label>
                <input
                    type="text"
                    name="name"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., Dr. Karim"
                    required
                />
            </div>
            <button
                type="submit"
                name="action"
                value="add_instructor"
                class="mt-2 rounded bg-black text-gray-100 px-4 py-2 text-xs font-semibold border border-gray-600 hover:bg-[#050505] w-full"
            >
                Add Instructor
            </button>
        </form>
    </div>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>


<?php
// pages/admin/courses.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/admin_shell.php';
include 'includes/data.php';

$courses = getCourses();
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_course') {
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';

        if (empty($code) || empty($name) || empty($type)) {
            $errorMessage = 'Please fill all fields.';
        } else {
            if (addCourse($code, $name, $type)) {
                $successMessage = 'Course added successfully!';
                $courses = getCourses();
            } else {
                $errorMessage = 'Error adding course. Code may already exist.';
            }
        }
    } elseif ($_POST['action'] === 'delete_course') {
        $code = $_POST['course_code'] ?? '';
        if (deleteCourse($code)) {
            $successMessage = 'Course deleted successfully!';
            $courses = getCourses();
        } else {
            $errorMessage = 'Error deleting course.';
        }
    }
}
?>

<h1 class="text-2xl font-bold mb-2 text-gray-100">
    Manage Courses
</h1>
<p class="text-sm text-gray-400 mb-4">
    Add or review courses and specify whether they are THEORY or LAB.
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
    <!-- Existing courses list -->
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">Existing Courses (<?php echo count($courses); ?>)</h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($courses as $course): ?>
                <li class="flex items-center justify-between bg-[#151515] border border-[#2a2a2a] rounded-md px-3 py-2">
                    <div>
                        <div class="font-semibold text-gray-100">
                            <?php echo htmlspecialchars($course['code']); ?> – <?php echo htmlspecialchars($course['name']); ?>
                        </div>
                        <div class="text-xs text-gray-400">Type: <?php echo htmlspecialchars($course['type']); ?></div>
                    </div>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_course">
                        <input type="hidden" name="course_code" value="<?php echo htmlspecialchars($course['code']); ?>">
                        <button type="submit" class="text-xs px-2 py-1 rounded border border-red-600 text-red-300 hover:bg-red-900">
                            Delete
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Add course form -->
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">Add New Course</h2>
        <form method="POST" class="space-y-3 text-sm">
            <div>
                <label class="block mb-1 text-gray-200">Course Code</label>
                <input
                    type="text"
                    name="code"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., CSE301"
                    required
                />
            </div>
            <div>
                <label class="block mb-1 text-gray-200">Course Name</label>
                <input
                    type="text"
                    name="name"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., Database Systems"
                    required
                />
            </div>
            <div>
                <label class="block mb-1 text-gray-200">Course Type</label>
                <select
                    name="type"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    required
                >
                    <option value="">Select type</option>
                    <option value="THEORY">THEORY</option>
                    <option value="LAB">LAB</option>
                </select>
            </div>
            <button
                type="submit"
                name="action"
                value="add_course"
                class="mt-2 rounded bg-black text-gray-100 px-4 py-2 text-xs font-semibold border border-gray-600 hover:bg-[#050505] w-full"
            >
                Add Course
            </button>
        </form>
    </div>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>


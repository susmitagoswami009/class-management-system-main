<?php
// pages/admin/schedule.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/admin_shell.php';
include 'includes/data.php';

$currentConflicts = [];
$successMessage = '';

// Get data from database
$timeslots = getTimeslots();
$courses = getCourses();
$instructors = getInstructors();
$rooms = getRooms();
$confirmed = getScheduleEntries();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $courseCode = $_POST['courseCode'] ?? '';
    $section = $_POST['section'] ?? '';
    $instructorId = $_POST['instructorId'] ?? '';
    $timeslotId = $_POST['timeslotId'] ?? '';
    $roomId = $_POST['roomId'] ?? '';

    if ($_POST['action'] === 'check_add') {
        if (empty($courseCode) || empty($section) || empty($instructorId) || empty($timeslotId) || empty($roomId)) {
            $currentConflicts = ['Please fill all fields before saving.'];
        } else {
            $conflicts = detectConflicts($courseCode, $instructorId, $timeslotId, $roomId);
            $currentConflicts = $conflicts;

            if (empty($conflicts)) {
                // Add to database
                if (addScheduleEntry($courseCode, $section, $instructorId, $roomId, $timeslotId)) {
                    $successMessage = 'Schedule entry added successfully!';
                    $courseCode = $section = $instructorId = $timeslotId = $roomId = '';
                    // Refresh confirmed entries from database
                    $confirmed = getScheduleEntries();
                } else {
                    $currentConflicts = ['Error adding schedule entry to database.'];
                }
            }
        }
    } elseif ($_POST['action'] === 'save_draft') {
        if (!empty($courseCode) && !empty($section) && !empty($instructorId) && !empty($timeslotId) && !empty($roomId)) {
            $conflicts = detectConflicts($courseCode, $instructorId, $timeslotId, $roomId);
            if (!empty($conflicts)) {
                if (addConflictDraft($courseCode, $section, $instructorId, $roomId, $timeslotId, $conflicts)) {
                    $successMessage = 'Draft saved. You can resolve it in the Conflicts page.';
                    $courseCode = $section = $instructorId = $timeslotId = $roomId = '';
                    $currentConflicts = [];
                } else {
                    $currentConflicts = ['Error saving draft.'];
                }
            }
        }
    }
}
?>

<h1 class="text-2xl font-bold mb-2 text-gray-100">
    Manage Schedule
</h1>
<p class="text-sm text-gray-400 mb-4">
    Create schedule entries. Conflicts are detected automatically. You can
    save conflicted entries as drafts and resolve them later.
</p>

<?php if ($successMessage): ?>
    <div class="mb-4 text-xs text-green-200 bg-[#1a3a1a] border border-green-600 rounded px-3 py-2">
        ✓ <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    <!-- Left: schedule form -->
    <div class="md:col-span-2 bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">
            New Schedule Entry
        </h2>
        <form method="POST" class="space-y-3 text-sm">
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block mb-1 text-gray-200">Course</label>
                    <select
                        name="courseCode"
                        class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        required
                    >
                        <option value="">Select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['code']); ?>">
                                <?php echo htmlspecialchars($course['code']); ?> – <?php echo htmlspecialchars($course['name']); ?> (<?php echo htmlspecialchars($course['type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-gray-200">Section</label>
                    <input
                        type="text"
                        name="section"
                        class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        placeholder="e.g., A"
                        required
                    />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block mb-1 text-gray-200">Instructor</label>
                    <select
                        name="instructorId"
                        class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        required
                    >
                        <option value="">Select instructor</option>
                        <?php foreach ($instructors as $instructor): ?>
                            <option value="<?php echo htmlspecialchars($instructor['id']); ?>">
                                <?php echo htmlspecialchars($instructor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-gray-200">Timeslot</label>
                    <select
                        name="timeslotId"
                        class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        required
                    >
                        <option value="">Select timeslot</option>
                        <?php foreach ($timeslots as $ts): ?>
                            <option value="<?php echo htmlspecialchars($ts['id']); ?>">
                                <?php echo htmlspecialchars($ts['label']); ?> – <?php echo htmlspecialchars($ts['days']); ?> (<?php echo htmlspecialchars($ts['start_time']); ?>–<?php echo htmlspecialchars($ts['end_time']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-1 text-gray-200">Room</label>
                <select
                    name="roomId"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    required
                >
                    <option value="">Select room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo htmlspecialchars($room['id']); ?>">
                            <?php echo htmlspecialchars($room['name']); ?> (<?php echo htmlspecialchars($room['type']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button
                    type="submit"
                    name="action"
                    value="check_add"
                    class="rounded bg-black text-gray-100 px-4 py-2 text-xs font-semibold border border-gray-600 hover:bg-[#050505]"
                >
                    Check &amp; Add
                </button>
                <button
                    type="submit"
                    name="action"
                    value="save_draft"
                    class="rounded px-4 py-2 text-xs font-semibold border border-gray-400 text-gray-100 bg-[#181818] hover:bg-gray-100 hover:text-black"
                >
                    Save Draft (on conflict)
                </button>
            </div>
        </form>
    </div>

    <!-- Right: conflict panel -->
    <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-100">
            Conflict Status
        </h2>
        <?php if (empty($currentConflicts)): ?>
            <p class="text-sm text-gray-500">
                No conflicts detected yet. Fill the form and click
                <span class="font-semibold text-gray-200">Check &amp; Add</span>
                to validate.
            </p>
        <?php else: ?>
            <ul class="list-disc pl-5 text-sm text-red-300 space-y-1">
                <?php foreach ($currentConflicts as $conflict): ?>
                    <li><?php echo htmlspecialchars($conflict); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p class="text-xs text-gray-500 mt-4">
            If conflicts exist, you can save the entry as a draft using
            <span class="font-semibold">Save Draft</span>. Drafts can be
            reviewed and fixed on the
            <span class="font-semibold">Conflicts</span> page.
        </p>
    </div>
</div>

<!-- Confirmed schedule list -->
<div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
    <h2 class="text-lg font-semibold mb-2 text-gray-100">
        Confirmed Schedule Entries (<?php echo count($confirmed); ?> total)
    </h2>
    <?php if (empty($confirmed)): ?>
        <p class="text-sm text-gray-500">
            No confirmed schedule entries yet. Add a schedule with no conflicts
            to see it here.
        </p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#262626] text-gray-300">
                        <th class="py-2 text-left">ID</th>
                        <th class="py-2 text-left">Course</th>
                        <th class="py-2 text-left">Section</th>
                        <th class="py-2 text-left">Instructor</th>
                        <th class="py-2 text-left">Timeslot</th>
                        <th class="py-2 text-left">Room</th>
                        <th class="py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($confirmed as $e): ?>
                        <?php
                        $course = getCourse($e['course_code']);
                        $instructor = getInstructor($e['instructor_id']);
                        $room = getRoom($e['room_id']);
                        $ts = getTimeslot($e['timeslot_id']);
                        ?>
                        <tr class="border-t border-[#262626]">
                            <td class="py-2 text-gray-400">#<?php echo htmlspecialchars($e['id']); ?></td>
                            <td class="py-2">
                                <?php echo htmlspecialchars($e['course_code']); ?> – <?php echo htmlspecialchars($course['name'] ?? ''); ?>
                            </td>
                            <td class="py-2"><?php echo htmlspecialchars($e['section']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($instructor['name'] ?? ''); ?></td>
                            <td class="py-2">
                                <?php echo htmlspecialchars($ts['label'] ?? ''); ?> (<?php echo htmlspecialchars($ts['start_time'] ?? ''); ?>–<?php echo htmlspecialchars($ts['end_time'] ?? ''); ?>)
                            </td>
                            <td class="py-2">
                                <?php echo htmlspecialchars($room['name'] ?? ''); ?> (<?php echo htmlspecialchars($room['type'] ?? ''); ?>)
                            </td>
                            <td class="py-2">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($e['id']); ?>">
                                    <button type="submit" name="action" value="delete" class="text-xs text-red-300 hover:text-red-100">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>


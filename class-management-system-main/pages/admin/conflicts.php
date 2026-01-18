<?php
// pages/admin/conflicts.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/admin_shell.php';
include 'includes/data.php';

$draftConflicts = getConflictDrafts();
$confirmed = getScheduleEntries();
$lastMessage = '';

// Handle re-check and add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['draft_id'])) {
    $draftId = (int)$_POST['draft_id'];
    $courseCode = $_POST['courseCode'] ?? '';
    $section = $_POST['section'] ?? '';
    $instructorId = $_POST['instructorId'] ?? '';
    $timeslotId = $_POST['timeslotId'] ?? '';
    $roomId = $_POST['roomId'] ?? '';

    // Re-check conflicts
    $updatedConflicts = detectConflicts($courseCode, $instructorId, $timeslotId, $roomId);

    if (empty($updatedConflicts)) {
        // No conflicts, add to confirmed schedule in database
        if (addScheduleEntry($courseCode, $section, $instructorId, $roomId, $timeslotId)) {
            // Delete draft from database
            deleteConflictDraft($draftId);
            
            $lastMessage = 'Draft ' . htmlspecialchars($courseCode) . ' (' . htmlspecialchars($section) . ') has no conflicts and has been added to the main schedule.';
            
            // Refresh drafts and confirmed
            $draftConflicts = getConflictDrafts();
            $confirmed = getScheduleEntries();
        } else {
            $lastMessage = 'Error adding schedule entry to database.';
        }
    } else {
        $lastMessage = 'Draft ' . htmlspecialchars($courseCode) . ' (' . htmlspecialchars($section) . ') still has conflicts. Please resolve them.';
    }
}
?>

<h1 class="text-2xl font-bold mb-2 text-gray-100">
    Conflict Drafts
</h1>
<p class="text-sm text-gray-400 mb-4">
    All schedule entries that conflicted during creation are listed here.
    Edit them to resolve conflicts and add them to the main schedule.
</p>

<?php if ($lastMessage): ?>
    <div class="mb-4 text-xs text-gray-200 bg-[#1a1a1a] border border-[#333333] rounded px-3 py-2">
        <?php echo htmlspecialchars($lastMessage); ?>
    </div>
<?php endif; ?>

<?php if (empty($draftConflicts)): ?>
    <p class="text-sm text-gray-500">
        No conflict drafts at the moment. Conflicted entries saved from the
        Schedule page will appear here.
    </p>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($draftConflicts as $d): ?>
            <?php
            $course = getCourse($d['course_code']);
            $instructor = getInstructor($d['instructor_id']);
            $room = getRoom($d['room_id']);
            $ts = getTimeslot($d['timeslot_id']);
            ?>
            <div class="bg-[#111111] border border-[#262626] rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2 text-gray-100">
                    Draft #<?php echo htmlspecialchars($d['id']); ?> – <?php echo htmlspecialchars($d['course_code']); ?> (<?php echo htmlspecialchars($d['section']); ?>)
                </h2>

                <form method="POST" class="space-y-3 text-sm">
                    <input type="hidden" name="draft_id" value="<?php echo htmlspecialchars($d['id']); ?>" />

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="block mb-1 text-gray-200">Course</label>
                            <select
                                name="courseCode"
                                class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                required
                            >
                                <option value="">Select course</option>
                                <?php 
                                $courses = getCourses();
                                foreach ($courses as $course): ?>
                                    <option value="<?php echo htmlspecialchars($course['code']); ?>" <?php echo ($course['code'] === $d['course_code']) ? 'selected' : ''; ?>>
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
                                value="<?php echo htmlspecialchars($d['section']); ?>"
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
                                <?php 
                                $instructors = getInstructors();
                                foreach ($instructors as $instructor): ?>
                                    <option value="<?php echo htmlspecialchars($instructor['id']); ?>" <?php echo ($instructor['id'] === $d['instructor_id']) ? 'selected' : ''; ?>>
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
                                <?php 
                                $timeslots = getTimeslots();
                                foreach ($timeslots as $ts): ?>
                                    <option value="<?php echo htmlspecialchars($ts['id']); ?>" <?php echo ($ts['id'] === $d['timeslot_id']) ? 'selected' : ''; ?>>
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
                            <?php 
                            $rooms = getRooms();
                            foreach ($rooms as $room): ?>
                                <option value="<?php echo htmlspecialchars($room['id']); ?>" <?php echo ($room['id'] === $d['room_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($room['name']); ?> (<?php echo htmlspecialchars($room['type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!empty($d['conflicts'])): ?>
                        <div class="bg-[#0c0c0c] border border-red-900 rounded p-3">
                            <p class="text-xs text-red-400 font-semibold mb-2">Current Conflicts:</p>
                            <ul class="list-disc pl-5 text-xs text-red-300 space-y-1">
                                <?php foreach ($d['conflicts'] as $conflict): ?>
                                    <li><?php echo htmlspecialchars($conflict); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <button
                            type="submit"
                            class="rounded bg-black text-gray-100 px-4 py-2 text-xs font-semibold border border-gray-600 hover:bg-[#050505]"
                        >
                            Re-check &amp; Add to Schedule
                        </button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_draft_id" value="<?php echo htmlspecialchars($d['id']); ?>">
                            <button type="submit" name="action" value="delete_draft" class="rounded text-red-300 px-4 py-2 text-xs font-semibold border border-red-600 hover:bg-red-900">
                                Delete Draft
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/admin_shell_footer.php'; ?>


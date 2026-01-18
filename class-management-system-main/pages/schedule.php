<?php
// pages/schedule.php
include 'includes/header.php';
include 'includes/data.php';

$timeslots = getTimeslots();
$scheduleEntries = getScheduleEntries();
?>

<h1 class="text-2xl font-bold mb-1 text-[#1e90ff]">
    Class Schedule
</h1>
<p class="text-sm text-gray-600 mb-6">
    5 fixed timeslots starting from 8:00 AM, with 10-minute breaks and no clashes.
</p>

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($timeslots as $timeslot): ?>
        <div class="bg-white border border-[#d8d8d0] rounded-lg shadow-sm p-4 flex flex-col">
            <div class="mb-3 border-b border-[#e4e4dd] pb-2">
                <h2 class="font-semibold text-lg text-gray-900">
                    <?php echo htmlspecialchars($timeslot['label']); ?> • <?php echo htmlspecialchars($timeslot['days']); ?>
                </h2>
                <p class="text-sm text-gray-500">
                    <?php echo htmlspecialchars($timeslot['start_time']); ?> – <?php echo htmlspecialchars($timeslot['end_time']); ?>
                </p>
            </div>

            <?php
            $entries = array_filter($scheduleEntries, function($e) use ($timeslot) {
                return $e['timeslot_id'] === $timeslot['id'];
            });

            if (empty($entries)):
            ?>
                <p class="text-sm text-gray-400 italic">No classes scheduled.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($entries as $e): ?>
                        <?php
                        $course = getCourse($e['course_code']);
                        $instructor = getInstructor($e['instructor_id']);
                        $room = getRoom($e['room_id']);
                        ?>
                        <li class="border border-[#d0e3ff] rounded-md p-2 text-sm bg-[#f3f8ff]">
                            <div class="font-semibold text-gray-900">
                                <?php echo htmlspecialchars($e['course_code']); ?> (<?php echo htmlspecialchars($e['section']); ?>) – <?php echo htmlspecialchars($course['name'] ?? ''); ?>
                            </div>
                            <div class="text-gray-700">
                                Instructor: <span class="text-[#1e90ff] font-medium"><?php echo htmlspecialchars($instructor['name'] ?? ''); ?></span>
                            </div>
                            <div class="text-gray-700">
                                Room: <span class="font-medium"><?php echo htmlspecialchars($room['name'] ?? ''); ?> (<?php echo htmlspecialchars($room['type'] ?? ''); ?>)</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>

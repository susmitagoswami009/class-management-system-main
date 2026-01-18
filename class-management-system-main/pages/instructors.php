<?php
// pages/instructors.php
include 'includes/header.php';
include 'includes/data.php';

$instructors = getInstructors();
$scheduleEntries = getScheduleEntries();
$timeslots = getTimeslots();
?>

<h1 class="text-2xl font-bold mb-1 text-[#1e90ff]">Instructors</h1>
<p class="text-sm text-gray-600 mb-4">
    Each instructor's assigned classes in the fixed timeslots.
</p>

<div class="space-y-4">
    <?php foreach ($instructors as $instructor): ?>
        <?php
        $entries = array_filter($scheduleEntries, function($e) use ($instructor) {
            return $e['instructor_id'] === $instructor['id'];
        });
        ?>
        <div class="bg-white border border-[#d8d8d0] rounded-lg shadow-sm p-4">
            <h2 class="font-semibold text-lg mb-2 text-gray-900"><?php echo htmlspecialchars($instructor['name']); ?></h2>
            <?php if (empty($entries)): ?>
                <p class="text-sm text-gray-400 italic">No assigned classes.</p>
            <?php else: ?>
                <ul class="space-y-1 text-sm">
                    <?php foreach ($entries as $e): ?>
                        <?php
                        $course = getCourse($e['course_code']);
                        $ts = getTimeslot($e['timeslot_id']);
                        ?>
                        <li class="flex justify-between">
                            <span class="text-gray-800">
                                <?php echo htmlspecialchars($e['course_code']); ?> (<?php echo htmlspecialchars($e['section']); ?>) – <?php echo htmlspecialchars($course['name'] ?? ''); ?>
                            </span>
                            <span class="text-gray-500">
                                <?php echo htmlspecialchars($ts['label'] ?? ''); ?> (<?php echo htmlspecialchars($ts['start_time'] ?? ''); ?>–<?php echo htmlspecialchars($ts['end_time'] ?? ''); ?>)
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>

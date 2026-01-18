<?php
// pages/rooms.php
include 'includes/header.php';
include 'includes/data.php';

$rooms = getRooms();
$scheduleEntries = getScheduleEntries();
$timeslots = getTimeslots();
?>

<h1 class="text-2xl font-bold mb-1 text-[#1e90ff]">Rooms</h1>
<p class="text-sm text-gray-600 mb-4">
    Theory and lab rooms with their scheduled classes. Lab courses appear only in LAB rooms.
</p>

<div class="space-y-4">
    <?php foreach ($rooms as $room): ?>
        <?php
        $entries = array_filter($scheduleEntries, function($e) use ($room) {
            return $e['room_id'] === $room['id'];
        });
        ?>
        <div class="bg-white border border-[#d8d8d0] rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($room['name']); ?></h2>
                <span class="text-xs px-2 py-1 rounded font-semibold <?php echo ($room['type'] === 'LAB') ? 'bg-[#ffe4e4] text-[#c53030]' : 'bg-[#e4ffe9] text-[#067a46]'; ?>">
                    <?php echo htmlspecialchars($room['type']); ?>
                </span>
            </div>

            <?php if (empty($entries)): ?>
                <p class="text-sm text-gray-400 italic">No classes scheduled.</p>
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

<?php
// pages/admin/rooms.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/admin_shell.php';
include 'includes/data.php';

$rooms = getRooms();
$scheduleEntries = getScheduleEntries();
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_room') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';

        if (empty($id) || empty($name) || empty($type)) {
            $errorMessage = 'Please fill all fields.';
        } else {
            if (addRoom($id, $name, $type)) {
                $successMessage = 'Room added successfully!';
                $rooms = getRooms();
            } else {
                $errorMessage = 'Error adding room. ID may already exist.';
            }
        }
    } elseif ($_POST['action'] === 'delete_room') {
        $id = $_POST['room_id'] ?? '';
        if (deleteRoom($id)) {
            $successMessage = 'Room deleted successfully!';
            $rooms = getRooms();
        } else {
            $errorMessage = 'Error deleting room.';
        }
    }
}
?>

<h1 class="text-2xl font-bold mb-2 text-gray-100">
    Manage Rooms
</h1>
<p class="text-sm text-gray-400 mb-4">
    Configure rooms with type (THEORY / LAB) and basic identifiers.
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
            Existing Rooms (<?php echo count($rooms); ?>)
        </h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($rooms as $room): ?>
                <?php
                $roomSchedules = array_filter($scheduleEntries, function($e) use ($room) {
                    return $e['room_id'] === $room['id'];
                });
                ?>
                <li class="flex items-center justify-between bg-[#151515] border border-[#2a2a2a] rounded-md px-3 py-2">
                    <div>
                        <div class="text-gray-100 font-semibold"><?php echo htmlspecialchars($room['name']); ?></div>
                        <div class="text-xs text-gray-400">
                            Type: <?php echo htmlspecialchars($room['type']); ?> | 
                            <?php echo count($roomSchedules); ?> class(es) scheduled | 
                            Capacity: <?php echo htmlspecialchars($room['capacity']); ?>
                        </div>
                    </div>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_room">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
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
            Add New Room
        </h2>
        <form method="POST" class="space-y-3 text-sm">
            <div>
                <label class="block mb-1 text-gray-200">Room ID</label>
                <input
                    type="text"
                    name="id"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., R201"
                    required
                />
            </div>
            <div>
                <label class="block mb-1 text-gray-200">Room Name</label>
                <input
                    type="text"
                    name="name"
                    class="w-full rounded bg-[#0b0b0b] border border-[#333333] px-3 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="e.g., Lab 301"
                    required
                />
            </div>
            <div>
                <label class="block mb-1 text-gray-200">Room Type</label>
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
                value="add_room"
                class="mt-2 rounded bg-black text-gray-100 px-4 py-2 text-xs font-semibold border border-gray-600 hover:bg-[#050505] w-full"
            >
                Add Room
            </button>
        </form>
    </div>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>

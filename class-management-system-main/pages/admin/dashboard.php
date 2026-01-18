<?php
// pages/admin/dashboard.php
include 'includes/admin_shell.php';
include 'includes/data.php';

$stats = getDashboardStats();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-100">Dashboard</h1>
    <p class="mt-1 text-sm text-gray-500">
        Overview of the Class Schedule Management System
    </p>
</div>

<div class="grid gap-4 md:grid-cols-3 lg:grid-cols-5">
    <!-- Total Courses Card -->
    <div class="flex items-center gap-4 rounded-xl bg-[#111111] border border-[#262626] px-5 py-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-black text-gray-100 border border-gray-600">
            <span class="text-lg font-semibold">📚</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                Total Courses
            </span>
            <span class="text-2xl font-semibold text-gray-100"><?php echo $stats['totalCourses']; ?></span>
        </div>
    </div>

    <!-- Total Schedules Card -->
    <div class="flex items-center gap-4 rounded-xl bg-[#111111] border border-[#262626] px-5 py-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-black text-gray-100 border border-gray-600">
            <span class="text-lg font-semibold">📅</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                Total Schedules
            </span>
            <span class="text-2xl font-semibold text-gray-100"><?php echo $stats['totalSchedules']; ?></span>
        </div>
    </div>

    <!-- Total Instructors Card -->
    <div class="flex items-center gap-4 rounded-xl bg-[#111111] border border-[#262626] px-5 py-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-black text-gray-100 border border-gray-600">
            <span class="text-lg font-semibold">👨‍🏫</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                Total Instructors
            </span>
            <span class="text-2xl font-semibold text-gray-100"><?php echo $stats['totalInstructors']; ?></span>
        </div>
    </div>

    <!-- Total Rooms Card -->
    <div class="flex items-center gap-4 rounded-xl bg-[#111111] border border-[#262626] px-5 py-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-black text-gray-100 border border-gray-600">
            <span class="text-lg font-semibold">🏛️</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                Total Rooms
            </span>
            <span class="text-2xl font-semibold text-gray-100"><?php echo $stats['totalRooms']; ?></span>
        </div>
    </div>

    <!-- Conflicts Card -->
    <div class="flex items-center gap-4 rounded-xl bg-[#111111] border border-[#262626] px-5 py-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-black text-gray-100 border border-gray-600">
            <span class="text-lg font-semibold">⚠️</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                Conflicts
            </span>
            <span class="text-2xl font-semibold text-gray-100"><?php echo $stats['totalConflicts']; ?></span>
        </div>
    </div>
</div>

<?php include 'includes/admin_shell_footer.php'; ?>

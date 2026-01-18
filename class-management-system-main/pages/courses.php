<?php
// pages/courses.php
include 'includes/header.php';
include 'includes/data.php';

$courses = getCourses();
?>

<h1 class="text-2xl font-bold mb-1 text-[#1e90ff]">Courses</h1>
<p class="text-sm text-gray-600 mb-4">
    List of theory and lab courses managed by the scheduling system.
</p>

<div class="bg-white border border-[#d8d8d0] rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-[#eaf3ff]">
            <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Code</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Course Name</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Type</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <?php foreach ($courses as $course): ?>
                <tr class="border-t border-[#ecece4]">
                    <td class="px-4 py-2 font-mono text-gray-800"><?php echo htmlspecialchars($course['code']); ?></td>
                    <td class="px-4 py-2 text-gray-800"><?php echo htmlspecialchars($course['name']); ?></td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold <?php echo ($course['type'] === 'LAB') ? 'bg-[#ffe4e4] text-[#c53030]' : 'bg-[#e4ffe9] text-[#067a46]'; ?>">
                            <?php echo htmlspecialchars($course['type']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

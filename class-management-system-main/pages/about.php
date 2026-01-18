<?php
// pages/about.php
include 'includes/header.php';
?>

<div class="max-w-2xl">
    <h1 class="text-2xl font-bold mb-2 text-[#1e90ff]">About This System</h1>
    <p class="text-sm text-gray-700 mb-4">
        This frontend prototype models a Class Schedule Management System focused on 
        fixed timeslots, instructor and room availability, and lab vs theory constraints.
    </p>

    <ul class="list-disc pl-5 text-sm space-y-1 mb-4 text-gray-800">
        <li>5 non-overlapping timeslots starting from 8:00 AM with 10-minute breaks.</li>
        <li>Rooms have types: THEORY / LAB.</li>
        <li>Courses have types: THEORY / LAB; lab courses appear only in LAB rooms.</li>
        <li>No instructor or room is double-booked in the same timeslot.</li>
    </ul>

    <p class="text-sm text-gray-700">
        This UI can be extended with admin forms to add, edit, and review schedule 
        entries, and connected to a backend service or database for persistent storage.
    </p>
</div>

<?php include 'includes/footer.php'; ?>

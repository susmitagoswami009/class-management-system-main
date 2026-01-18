<?php
// includes/data.php

require_once __DIR__ . '/../config/db.php';

// Get all timeslots from database
function getTimeslots() {
    global $conn;
    $result = $conn->query("SELECT * FROM timeslots ORDER BY start_time ASC");
    $timeslots = [];
    while ($row = $result->fetch_assoc()) {
        $timeslots[] = $row;
    }
    return $timeslots;
}

// Get all courses from database
function getCourses() {
    global $conn;
    $result = $conn->query("SELECT * FROM courses ORDER BY code ASC");
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

// Get single course by code
function getCourse($code) {
    global $conn;
    $code = $conn->real_escape_string($code);
    $result = $conn->query("SELECT * FROM courses WHERE code = '$code'");
    return $result->fetch_assoc();
}

// Get all instructors from database
function getInstructors() {
    global $conn;
    $result = $conn->query("SELECT * FROM instructors ORDER BY name ASC");
    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
    return $instructors;
}

// Get single instructor by ID
function getInstructor($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $result = $conn->query("SELECT * FROM instructors WHERE id = '$id'");
    return $result->fetch_assoc();
}

// Get all rooms from database
function getRooms() {
    global $conn;
    $result = $conn->query("SELECT * FROM rooms ORDER BY name ASC");
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    return $rooms;
}

// Get single room by ID
function getRoom($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $result = $conn->query("SELECT * FROM rooms WHERE id = '$id'");
    return $result->fetch_assoc();
}

// Get single timeslot by ID
function getTimeslot($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $result = $conn->query("SELECT * FROM timeslots WHERE id = '$id'");
    return $result->fetch_assoc();
}

// Get all schedule entries
function getScheduleEntries() {
    global $conn;
    $result = $conn->query("SELECT * FROM schedule_entries ORDER BY timeslot_id ASC");
    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    return $entries;
}

// Get schedule entries by timeslot
function getScheduleByTimeslot($timeslotId) {
    global $conn;
    $timeslotId = $conn->real_escape_string($timeslotId);
    $result = $conn->query("SELECT * FROM schedule_entries WHERE timeslot_id = '$timeslotId'");
    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    return $entries;
}

// Get schedule entries by instructor
function getScheduleByInstructor($instructorId) {
    global $conn;
    $instructorId = $conn->real_escape_string($instructorId);
    $result = $conn->query("SELECT * FROM schedule_entries WHERE instructor_id = '$instructorId' ORDER BY timeslot_id ASC");
    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    return $entries;
}

// Get schedule entries by room
function getScheduleByRoom($roomId) {
    global $conn;
    $roomId = $conn->real_escape_string($roomId);
    $result = $conn->query("SELECT * FROM schedule_entries WHERE room_id = '$roomId' ORDER BY timeslot_id ASC");
    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    return $entries;
}

// Detect conflicts for a new schedule entry
function detectConflicts($courseCode, $instructorId, $timeslotId, $roomId) {
    global $conn;
    $conflicts = [];

    $courseCode = $conn->real_escape_string($courseCode);
    $instructorId = $conn->real_escape_string($instructorId);
    $timeslotId = $conn->real_escape_string($timeslotId);
    $roomId = $conn->real_escape_string($roomId);

    // Get course and room details
    $course = getCourse($courseCode);
    $room = getRoom($roomId);

    // Check instructor double-booking
    $result = $conn->query("SELECT id FROM schedule_entries WHERE instructor_id = '$instructorId' AND timeslot_id = '$timeslotId'");
    if ($result->num_rows > 0) {
        $conflicts[] = 'Instructor already has a class in this timeslot.';
    }

    // Check room double-booking
    $result = $conn->query("SELECT id FROM schedule_entries WHERE room_id = '$roomId' AND timeslot_id = '$timeslotId'");
    if ($result->num_rows > 0) {
        $conflicts[] = 'Room is already booked in this timeslot.';
    }

    // Check lab vs room type
    if ($course && $room) {
        if ($course['type'] === 'LAB' && $room['type'] !== 'LAB') {
            $conflicts[] = 'Lab course must be scheduled in a LAB room.';
        }
        if ($course['type'] === 'THEORY' && $room['type'] === 'LAB') {
            $conflicts[] = 'Theory course should not be in a LAB room.';
        }
    }

    return $conflicts;
}

// Add schedule entry
function addScheduleEntry($courseCode, $section, $instructorId, $roomId, $timeslotId) {
    global $conn;

    $courseCode = $conn->real_escape_string($courseCode);
    $section = $conn->real_escape_string($section);
    $instructorId = $conn->real_escape_string($instructorId);
    $roomId = $conn->real_escape_string($roomId);
    $timeslotId = $conn->real_escape_string($timeslotId);

    $sql = "INSERT INTO schedule_entries (course_code, section, instructor_id, room_id, timeslot_id) 
            VALUES ('$courseCode', '$section', '$instructorId', '$roomId', '$timeslotId')";

    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Add course
function addCourse($code, $name, $type) {
    global $conn;

    $code = $conn->real_escape_string($code);
    $name = $conn->real_escape_string($name);
    $type = $conn->real_escape_string($type);

    $sql = "INSERT INTO courses (code, name, type) VALUES ('$code', '$name', '$type')";
    return $conn->query($sql) === TRUE;
}

// Add instructor
function addInstructor($id, $name) {
    global $conn;

    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);

    $sql = "INSERT INTO instructors (id, name) VALUES ('$id', '$name')";
    return $conn->query($sql) === TRUE;
}

// Add room
function addRoom($id, $name, $type) {
    global $conn;

    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);
    $type = $conn->real_escape_string($type);

    $sql = "INSERT INTO rooms (id, name, type) VALUES ('$id', '$name', '$type')";
    return $conn->query($sql) === TRUE;
}

// Delete schedule entry
function deleteScheduleEntry($id) {
    global $conn;

    $id = (int)$id;
    $sql = "DELETE FROM schedule_entries WHERE id = $id";
    return $conn->query($sql) === TRUE;
}

// Delete course
function deleteCourse($code) {
    global $conn;

    $code = $conn->real_escape_string($code);
    $sql = "DELETE FROM courses WHERE code = '$code'";
    return $conn->query($sql) === TRUE;
}

// Delete instructor
function deleteInstructor($id) {
    global $conn;

    $id = $conn->real_escape_string($id);
    $sql = "DELETE FROM instructors WHERE id = '$id'";
    return $conn->query($sql) === TRUE;
}

// Delete room
function deleteRoom($id) {
    global $conn;

    $id = $conn->real_escape_string($id);
    $sql = "DELETE FROM rooms WHERE id = '$id'";
    return $conn->query($sql) === TRUE;
}

// Add conflict draft
function addConflictDraft($courseCode, $section, $instructorId, $roomId, $timeslotId, $conflicts) {
    global $conn;

    $courseCode = $conn->real_escape_string($courseCode);
    $section = $conn->real_escape_string($section);
    $instructorId = $conn->real_escape_string($instructorId);
    $roomId = $conn->real_escape_string($roomId);
    $timeslotId = $conn->real_escape_string($timeslotId);
    $conflictsJson = json_encode($conflicts);
    $conflictsJson = $conn->real_escape_string($conflictsJson);

    $sql = "INSERT INTO conflict_drafts (course_code, section, instructor_id, room_id, timeslot_id, conflicts) 
            VALUES ('$courseCode', '$section', '$instructorId', '$roomId', '$timeslotId', '$conflictsJson')";

    if ($conn->query($sql) === TRUE) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

// Get all conflict drafts
function getConflictDrafts() {
    global $conn;
    $result = $conn->query("SELECT * FROM conflict_drafts ORDER BY created_at DESC");
    $drafts = [];
    while ($row = $result->fetch_assoc()) {
        $row['conflicts'] = json_decode($row['conflicts'], true);
        $drafts[] = $row;
    }
    return $drafts;
}

// Get single conflict draft
function getConflictDraft($id) {
    global $conn;
    $id = (int)$id;
    $result = $conn->query("SELECT * FROM conflict_drafts WHERE id = $id");
    $draft = $result->fetch_assoc();
    if ($draft) {
        $draft['conflicts'] = json_decode($draft['conflicts'], true);
    }
    return $draft;
}

// Delete conflict draft
function deleteConflictDraft($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM conflict_drafts WHERE id = $id";
    return $conn->query($sql) === TRUE;
}

// Get total counts for dashboard
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total courses
    $result = $conn->query("SELECT COUNT(*) as count FROM courses");
    $stats['totalCourses'] = $result->fetch_assoc()['count'];
    
    // Total instructors
    $result = $conn->query("SELECT COUNT(*) as count FROM instructors");
    $stats['totalInstructors'] = $result->fetch_assoc()['count'];
    
    // Total rooms
    $result = $conn->query("SELECT COUNT(*) as count FROM rooms");
    $stats['totalRooms'] = $result->fetch_assoc()['count'];
    
    // Total schedule entries
    $result = $conn->query("SELECT COUNT(*) as count FROM schedule_entries");
    $stats['totalSchedules'] = $result->fetch_assoc()['count'];
    
    // Total conflicts
    $result = $conn->query("SELECT COUNT(*) as count FROM conflict_drafts");
    $stats['totalConflicts'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

?>

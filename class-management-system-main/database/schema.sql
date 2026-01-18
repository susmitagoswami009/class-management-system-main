-- database/schema.sql

-- Courses Table
CREATE TABLE courses (
  code VARCHAR(20) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type ENUM('THEORY', 'LAB') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Instructors Table
CREATE TABLE instructors (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rooms Table
CREATE TABLE rooms (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type ENUM('THEORY', 'LAB') NOT NULL,
  capacity INT DEFAULT 40,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Timeslots Table
CREATE TABLE timeslots (
  id VARCHAR(20) PRIMARY KEY,
  label VARCHAR(20) NOT NULL,
  days VARCHAR(50) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedule Entries Table
CREATE TABLE schedule_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_code VARCHAR(20) NOT NULL,
  section VARCHAR(10) NOT NULL,
  instructor_id VARCHAR(20) NOT NULL,
  room_id VARCHAR(20) NOT NULL,
  timeslot_id VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (course_code) REFERENCES courses(code) ON DELETE CASCADE,
  FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
  FOREIGN KEY (timeslot_id) REFERENCES timeslots(id) ON DELETE CASCADE,
  UNIQUE KEY unique_instructor_timeslot (instructor_id, timeslot_id),
  UNIQUE KEY unique_room_timeslot (room_id, timeslot_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conflict Drafts Table (for saving conflicted schedules)
CREATE TABLE conflict_drafts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_code VARCHAR(20) NOT NULL,
  section VARCHAR(10) NOT NULL,
  instructor_id VARCHAR(20) NOT NULL,
  room_id VARCHAR(20) NOT NULL,
  timeslot_id VARCHAR(20) NOT NULL,
  conflicts JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_code) REFERENCES courses(code) ON DELETE CASCADE,
  FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
  FOREIGN KEY (timeslot_id) REFERENCES timeslots(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins Table (for user authentication)
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a default admin (username: admin, password: admin123)
INSERT INTO admins (username, email, password_hash, full_name) VALUES
('admin', 'admin@example.com', '$2y$10$YourHashedPasswordHere', 'Administrator');



-- Insert Sample Data

-- Insert Timeslots
INSERT INTO timeslots (id, label, days, start_time, end_time) VALUES
('TS1', 'ST1', 'Sun & Tue', '08:00:00', '09:30:00'),
('TS2', 'ST2', 'Sun & Tue', '09:40:00', '11:10:00'),
('TS3', 'MW1', 'Mon & Wed', '11:20:00', '12:50:00'),
('TS4', 'MW2', 'Mon & Wed', '13:00:00', '14:30:00'),
('TS5', 'RA1', 'Thu & Sat', '14:40:00', '16:10:00');

-- Insert Courses
INSERT INTO courses (code, name, type) VALUES
('CSE101', 'Intro to Programming', 'THEORY'),
('CSE102', 'Data Structures Lab', 'LAB'),
('CSE201', 'Algorithms', 'THEORY'),
('EEE120', 'Circuits Lab', 'LAB');

-- Insert Instructors
INSERT INTO instructors (id, name) VALUES
('I1', 'Dr. Rahman'),
('I2', 'Dr. Alam'),
('I3', 'Ms. Ahmed');

-- Insert Rooms
INSERT INTO rooms (id, name, type, capacity) VALUES
('R101', 'Room 101', 'THEORY', 50),
('R102', 'Room 102', 'THEORY', 45),
('L201', 'Lab 201', 'LAB', 30);

-- Insert Sample Schedule Entries
INSERT INTO schedule_entries (course_code, section, instructor_id, room_id, timeslot_id) VALUES
('CSE101', 'A', 'I1', 'R101', 'TS1'),
('CSE201', 'B', 'I2', 'R102', 'TS2'),
('CSE102', 'A', 'I3', 'L201', 'TS3'),
('EEE120', 'A', 'I2', 'L201', 'TS4'),
('CSE101', 'C', 'I1', 'R101', 'TS5');

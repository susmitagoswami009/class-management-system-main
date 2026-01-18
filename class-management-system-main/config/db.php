<?php
// config/db.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'class_scheduler');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset('utf8mb4');

// Authentication Functions

/**
 * Register a new admin user
 */
function registerAdmin($username, $email, $password, $fullName) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'All fields are required.'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email format.'];
    }
    
    // Escape input
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $fullName = $conn->real_escape_string($fullName);
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Check if username already exists
    $result = $conn->query("SELECT id FROM admins WHERE username = '$username'");
    if ($result->num_rows > 0) {
        return ['success' => false, 'error' => 'Username already exists.'];
    }
    
    // Check if email already exists
    $result = $conn->query("SELECT id FROM admins WHERE email = '$email'");
    if ($result->num_rows > 0) {
        return ['success' => false, 'error' => 'Email already registered.'];
    }
    
    // Insert new admin
    $sql = "INSERT INTO admins (username, email, password_hash, full_name) 
            VALUES ('$username', '$email', '$passwordHash', '$fullName')";
    
    if ($conn->query($sql) === TRUE) {
        return ['success' => true, 'message' => 'Registration successful! You can now login.'];
    } else {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login admin user
 */
function loginAdmin($username, $password) {
    global $conn;
    
    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Username and password are required.'];
    }
    
    $username = $conn->real_escape_string($username);
    
    // Get admin from database
    $result = $conn->query("SELECT id, username, email, password_hash, full_name, is_active FROM admins WHERE username = '$username'");
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'error' => 'Username or password is incorrect.'];
    }
    
    $admin = $result->fetch_assoc();
    
    // Check if admin is active
    if (!$admin['is_active']) {
        return ['success' => false, 'error' => 'Your account has been deactivated.'];
    }
    
    // Verify password
    if (!password_verify($password, $admin['password_hash'])) {
        return ['success' => false, 'error' => 'Username or password is incorrect.'];
    }
    
    // Login successful - return admin data
    return [
        'success' => true,
        'admin_id' => $admin['id'],
        'username' => $admin['username'],
        'email' => $admin['email'],
        'full_name' => $admin['full_name']
    ];
}

/**
 * Get admin by ID
 */
function getAdminById($id) {
    global $conn;
    
    $id = (int)$id;
    $result = $conn->query("SELECT id, username, email, full_name, is_active, created_at FROM admins WHERE id = $id");
    return $result->fetch_assoc();
}

?>

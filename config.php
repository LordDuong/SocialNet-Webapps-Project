<?php
// Suppress errors from being displayed
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database Configuration, fill in '...'
define('DB_HOST', 'localhost');
define('DB_USER', '...'); //Enter your database username into '...'
define('DB_PASS', '...'); //Enter your database password
define('DB_NAME', '...'); //Enter your database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>

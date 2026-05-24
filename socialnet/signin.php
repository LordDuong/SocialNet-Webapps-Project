<?php
header('Content-Type: application/json');
session_start();

// Include database config
require_once '../config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validation
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

// Query database for user
$query = "SELECT id, username, fullname, password FROM account WHERE username = '$username'";
$result = $conn->query($query);

// Check if user exists
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Username or password is incorrect']);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username or password is incorrect']);
    exit;
}

// Password is correct - create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['fullname'] = $user['fullname'];

echo json_encode(['success' => true, 'message' => 'Sign in successful']);

$conn->close();
?>

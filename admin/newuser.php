<?php
header('Content-Type: application/json');

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
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Validation
$errors = [];

if (empty($username)) {
    $errors[] = 'Username is required';
} elseif (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
} elseif (!preg_match('/^[a-z0-9_]+$/', $username)) {
    $errors[] = 'Username can only contain lowercase letters, numbers, and underscores';
}

if (empty($fullname)) {
    $errors[] = 'Full name is required';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

// If validation fails
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Check if username already exists
$checkQuery = "SELECT id FROM account WHERE username = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    $stmt->close();
    exit;
}
$stmt->close();

// Hash password with bcrypt
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert new user
$insertQuery = "INSERT INTO account (username, fullname, password, description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ssss", $username, $fullname, $hashedPassword, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>

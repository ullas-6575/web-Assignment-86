<?php
// login_handler.php — Authenticates user against users.json

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Read form data (accepts username OR email in the "identifier" field)
$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

// Basic validation
if (empty($identifier) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username/Email and Password are required.']);
    exit;
}

// Load users
$usersFile = __DIR__ . '/users.json';

if (!file_exists($usersFile)) {
    echo json_encode(['success' => false, 'message' => 'No registered users yet. Please sign up first.']);
    exit;
}

$data = file_get_contents($usersFile);
$users = json_decode($data, true);

if (!is_array($users)) {
    echo json_encode(['success' => false, 'message' => 'Error reading user data.']);
    exit;
}

// Find matching user by username or email
$matchedUser = null;
foreach ($users as $user) {
    if (strtolower($user['username']) === strtolower($identifier) ||
        strtolower($user['email']) === strtolower($identifier)) {
        $matchedUser = $user;
        break;
    }
}

if ($matchedUser === null) {
    echo json_encode(['success' => false, 'message' => 'User not found. Check your username or email.']);
    exit;
}

// Verify password
if (password_verify($password, $matchedUser['password'])) {
    echo json_encode([
        'success'  => true,
        'message'  => 'Login successful! Welcome back, ' . $matchedUser['fullname'] . '!',
        'fullname' => $matchedUser['fullname'],
        'username' => $matchedUser['username']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
}

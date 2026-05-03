<?php
// signup.php — Stores new user info in users.json

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Read form data
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 4 characters.']);
    exit;
}

// Load existing users
$usersFile = __DIR__ . '/users.json';
$users = [];

if (file_exists($usersFile)) {
    $data = file_get_contents($usersFile);
    $users = json_decode($data, true);
    if (!is_array($users)) {
        $users = [];
    }
}

// Check if username or email already exists
foreach ($users as $user) {
    if (strtolower($user['username']) === strtolower($username)) {
        echo json_encode(['success' => false, 'message' => 'Username already taken.']);
        exit;
    }
    if (strtolower($user['email']) === strtolower($email)) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }
}

// Create new user with hashed password
$newUser = [
    'fullname' => $fullname,
    'username' => $username,
    'email'    => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'created'  => date('Y-m-d H:i:s')
];

$users[] = $newUser;

// Save back to file
if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Server error: could not save user data.']);
}

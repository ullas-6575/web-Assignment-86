<?php
// register_process.php — Inserts new user into MySQL via db.php
// FIX: replaced die() calls with proper redirects back to login.php so
//      errors are shown inline using the restored signup-message box.

include 'db.php';

if (!isset($_POST['submit'])) {
    header("location: login.php?action=signup");
    exit();
}

$fullname = trim($_POST['fullname']);
$username = trim($_POST['username']);
$email    = trim($_POST['email']);
$password = $_POST['password'];

// ── Validation ──────────────────────────────────────────────────────────────
if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
    header("location: login.php?action=signup&signup_error=All+fields+are+required.");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("location: login.php?action=signup&signup_error=Invalid+email+address.");
    exit();
}

if (strlen($password) < 8) {
    header("location: login.php?action=signup&signup_error=Password+must+be+at+least+8+characters.");
    exit();
}

// ── Escape inputs (prevents SQL injection) ──────────────────────────────────
$fn  = mysqli_real_escape_string($conn, $fullname);
$un  = mysqli_real_escape_string($conn, $username);
$em  = mysqli_real_escape_string($conn, $email);

// ── Check duplicates ─────────────────────────────────────────────────────────
$check = mysqli_query($conn, "SELECT id FROM users WHERE username='$un' OR email='$em'");
if (mysqli_num_rows($check) > 0) {
    header("location: login.php?action=signup&signup_error=Username+or+email+already+taken.");
    exit();
}

// ── Insert ───────────────────────────────────────────────────────────────────
$hashed = password_hash($password, PASSWORD_DEFAULT);
$q = "INSERT INTO users (fullname, username, email, password)
      VALUES ('$fn', '$un', '$em', '$hashed')";

if (mysqli_query($conn, $q)) {
    mysqli_close($conn);
    header("location: login.php?registered=1");
    exit();
} else {
    mysqli_close($conn);
    header("location: login.php?action=signup&signup_error=Server+error.+Please+try+again.");
    exit();
}
?>

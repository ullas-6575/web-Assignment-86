<?php
// login_process.php — Authenticates user against MySQL database
// FIX: added mysqli_real_escape_string() to prevent SQL injection

session_start();
include 'db.php';

if (!isset($_POST['submit'])) {
    header("location: login.php");
    exit();
}

$identifier = mysqli_real_escape_string($conn, trim($_POST['identifier']));
$password   = $_POST['password'];

// Fetch user by username OR email
$q   = "SELECT * FROM users WHERE username='$identifier' OR email='$identifier'";
$res = mysqli_query($conn, $q);

if (mysqli_num_rows($res) == 1) {
    $user = mysqli_fetch_assoc($res);

    if (password_verify($password, $user['password'])) {

        // ── SESSION ──
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];

        // ── COOKIE ("remember me") ──
        if (isset($_POST['remember'])) {
            setcookie('username', $user['username'], time() + (7 * 24 * 3600), '/');
        }

        mysqli_close($conn);
        header("location: index.php");
        exit();

    } else {
        mysqli_close($conn);
        header("location: login.php?error=invalid");
        exit();
    }
} else {
    mysqli_close($conn);
    header("location: login.php?error=notfound");
    exit();
}
?>
